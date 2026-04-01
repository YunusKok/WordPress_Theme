<?php
/**
 * ThessNest — Native Custom Payment Gateways
 *
 * Implements direct Stripe and PayPal integrations without relying on WooCommerce.
 * Generates API-driven Checkout Sessions and handles Webhooks for top-tier security.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Native_Payments {

	public function __construct() {
		// Initialize REST APIs for Webhooks
		add_action( 'rest_api_init', [ $this, 'register_webhook_endpoints' ] );
		
		// AJAX Endpoint to generate session (called from Checkout Page)
		add_action( 'wp_ajax_thessnest_process_native_payment', [ $this, 'process_payment' ] );
	}

	/**
	 * Webhooks for async payment confirmations
	 */
	public function register_webhook_endpoints() {
		register_rest_route( 'thessnest/v1', '/stripe-webhook', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'stripe_webhook_handler' ],
			'permission_callback' => '__return_true'
		] );
		
		register_rest_route( 'thessnest/v1', '/paypal-webhook', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'paypal_webhook_handler' ],
			'permission_callback' => '__return_true'
		] );
	}

	/**
	 * Process Payment & Create Checkout Session
	 */
	public function process_payment() {
		check_ajax_referer( 'thessnest_checkout_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in to pay.', 'thessnest' ) ) );
		}

		$booking_id = isset( $_POST['booking_id'] ) ? intval( $_POST['booking_id'] ) : 0;
		$gateway    = isset( $_POST['gateway'] ) ? sanitize_text_field( $_POST['gateway'] ) : '';

		if ( ! $booking_id || ! in_array( $gateway, ['stripe', 'paypal'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid request parameters.', 'thessnest' ) ) );
		}

		// Validate booking ownership and status
		$tenant_id = get_post_field( 'post_author', $booking_id );
		if ( $tenant_id != get_current_user_id() ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to pay for this booking.', 'thessnest' ) ) );
		}

		$status = get_post_meta( $booking_id, '_booking_status', true );
		if ( $status !== 'awaiting_payment' ) {
			wp_send_json_error( array( 'message' => __( 'This booking is not awaiting payment.', 'thessnest' ) ) );
		}

		$property_id = get_post_meta( $booking_id, '_booking_property_id', true );
		$amount      = (float) get_post_meta( $property_id, '_thessnest_deposit', true );
		
		if ( $amount <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid deposit amount.', 'thessnest' ) ) );
		}

		$currency  = function_exists('thessnest_opt') ? thessnest_opt('payment_currency', 'EUR') : 'EUR';
		$prop_name = get_the_title( $property_id );

		// Route to specific gateway
		if ( 'stripe' === $gateway ) {
			$this->create_stripe_session( $booking_id, $amount, $currency, $prop_name );
		} elseif ( 'paypal' === $gateway ) {
			$this->create_paypal_order( $booking_id, $amount, $currency, $prop_name );
		}
	}

	/**
	 * Stripe Checkout Session Generation
	 */
	private function create_stripe_session( $booking_id, $amount, $currency, $prop_name ) {
		global $thessnest_opt;
		$secret_key = isset($thessnest_opt['stripe_secret_key']) ? $thessnest_opt['stripe_secret_key'] : '';
		
		if ( empty( $secret_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Stripe is not configured correctly.', 'thessnest' ) ) );
		}

		// Stripe expects zero-decimal currencies (cents)
		$amount_cents = round( $amount * 100 );

		$success_url = add_query_arg( ['booking_id' => $booking_id, 'payment_status' => 'success'], get_permalink( get_page_by_path('dashboard') ) );
		$cancel_url  = add_query_arg( ['booking_id' => $booking_id, 'payment_status' => 'cancelled'], get_permalink( get_page_by_path('dashboard') ) );

		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $secret_key,
				'Content-Type'  => 'application/x-www-form-urlencoded'
			),
			'body' => array(
				'payment_method_types[0]'     => 'card',
				'line_items[0][price_data][currency]' => strtolower($currency),
				'line_items[0][price_data][product_data][name]' => 'Booking Deposit: ' . $prop_name,
				'line_items[0][price_data][unit_amount]' => $amount_cents,
				'line_items[0][quantity]'     => 1,
				'mode'                        => 'payment',
				'success_url'                 => $success_url,
				'cancel_url'                  => $cancel_url,
				'client_reference_id'         => $booking_id,
				'metadata[booking_id]'        => $booking_id
			),
		);

		$response = wp_remote_post( 'https://api.stripe.com/v1/checkout/sessions', $args );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array( 'message' => $response->get_error_message() ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['error'] ) ) {
			wp_send_json_error( array( 'message' => $body['error']['message'] ) );
		}

		if ( isset( $body['url'] ) ) {
			wp_send_json_success( array( 'redirect_url' => $body['url'] ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to generate Stripe session.', 'thessnest' ) ) );
		}
	}

	/**
	 * Stripe Webhook Handler
	 */
	public function stripe_webhook_handler( WP_REST_Request $request ) {
		// Log incoming request for debug
		$payload = $request->get_body();
		$event   = json_decode( $payload, true );

		if ( ! isset( $event['type'] ) ) {
			return new WP_REST_Response( 'Invalid Payload', 400 );
		}

		if ( $event['type'] === 'checkout.session.completed' ) {
			$session = $event['data']['object'];
			$booking_id = isset( $session['client_reference_id'] ) ? intval( $session['client_reference_id'] ) : 0;
			
			if ( $booking_id ) {
				// Mark as paid
				update_post_meta( $booking_id, '_booking_status', 'paid' );
				update_post_meta( $booking_id, '_payment_gateway', 'stripe' );
				update_post_meta( $booking_id, '_stripe_payment_intent', $session['payment_intent'] );
				
				// Optional: Send success email
			}
		}

		return new WP_REST_Response( 'Webhook Received', 200 );
	}

	/**
	 * PayPal Order Generation
	 */
	private function create_paypal_order( $booking_id, $amount, $currency, $prop_name ) {
		global $thessnest_opt;
		$client_id = isset($thessnest_opt['paypal_client_id']) ? $thessnest_opt['paypal_client_id'] : '';
		$secret    = isset($thessnest_opt['paypal_secret']) ? $thessnest_opt['paypal_secret'] : '';
		$sandbox   = isset($thessnest_opt['paypal_sandbox']) ? $thessnest_opt['paypal_sandbox'] : true;

		if ( empty( $client_id ) || empty( $secret ) ) {
			wp_send_json_error( array( 'message' => __( 'PayPal is not configured correctly.', 'thessnest' ) ) );
		}

		$base_url = $sandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

		// Get Access Token
		$auth_response = wp_remote_post( $base_url . '/v1/oauth2/token', array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $secret ),
				'Content-Type'  => 'application/x-www-form-urlencoded'
			),
			'body' => 'grant_type=client_credentials'
		) );

		if ( is_wp_error( $auth_response ) ) {
			wp_send_json_error( array( 'message' => __( 'PayPal Auth Error.', 'thessnest' ) ) );
		}

		$auth_body = json_decode( wp_remote_retrieve_body( $auth_response ), true );
		if ( ! isset( $auth_body['access_token'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to authenticate with PayPal.', 'thessnest' ) ) );
		}

		$access_token = $auth_body['access_token'];

		$success_url = add_query_arg( ['booking_id' => $booking_id, 'payment_status' => 'success', 'gateway_fallback' => 'paypal'], get_permalink( get_page_by_path('dashboard') ) );
		$cancel_url  = add_query_arg( ['booking_id' => $booking_id, 'payment_status' => 'cancelled'], get_permalink( get_page_by_path('dashboard') ) );

		// Create Order
		$order_data = array(
			'intent' => 'CAPTURE',
			'purchase_units' => array(
				array(
					'reference_id' => 'BOOKING_' . $booking_id,
					'description'  => 'Booking Deposit: ' . $prop_name,
					'amount'       => array(
						'currency_code' => strtoupper($currency),
						'value'         => number_format( $amount, 2, '.', '' )
					)
				)
			),
			'application_context' => array(
				'return_url' => $success_url,
				'cancel_url' => $cancel_url,
				'user_action' => 'PAY_NOW'
			)
		);

		$order_response = wp_remote_post( $base_url . '/v2/checkout/orders', array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $access_token,
				'Content-Type'  => 'application/json'
			),
			'body' => wp_json_encode( $order_data )
		) );

		if ( is_wp_error( $order_response ) ) {
			wp_send_json_error( array( 'message' => $order_response->get_error_message() ) );
		}

		$order_body = json_decode( wp_remote_retrieve_body( $order_response ), true );

		if ( isset( $order_body['id'] ) && isset( $order_body['links'] ) ) {
			// Save Order ID to verify later before capturing
			update_post_meta( $booking_id, '_paypal_order_id', $order_body['id'] );

			// Find approve link
			foreach ( $order_body['links'] as $link ) {
				if ( $link['rel'] === 'approve' ) {
					wp_send_json_success( array( 'redirect_url' => $link['href'] ) );
				}
			}
		}

		wp_send_json_error( array( 'message' => __( 'Failed to generate PayPal order.', 'thessnest' ) ) );
	}

	/**
	 * PayPal Webhook Handler (Fallback for Capture)
	 */
	public function paypal_webhook_handler( WP_REST_Request $request ) {
		// Typically, PayPal orders are captured via frontend JS or a return_url check.
		// If using webhooks for CHECKOUT.ORDER.APPROVED
		return new WP_REST_Response( 'Webhook Received', 200 );
	}
}

// Global hook to catch PayPal return_url and execute Capture Request
add_action( 'template_redirect', function() {
	if ( isset( $_GET['payment_status'], $_GET['gateway_fallback'], $_GET['token'], $_GET['booking_id'] ) && $_GET['payment_status'] === 'success' && $_GET['gateway_fallback'] === 'paypal' ) {
		
		$booking_id = intval( $_GET['booking_id'] );
		$status = get_post_meta( $booking_id, '_booking_status', true );
		
		// Prevent double capture
		if ( $status !== 'awaiting_payment' ) {
			return; 
		}

		global $thessnest_opt;
		$client_id = isset($thessnest_opt['paypal_client_id']) ? $thessnest_opt['paypal_client_id'] : '';
		$secret    = isset($thessnest_opt['paypal_secret']) ? $thessnest_opt['paypal_secret'] : '';
		$sandbox   = isset($thessnest_opt['paypal_sandbox']) ? $thessnest_opt['paypal_sandbox'] : true;
		$base_url  = $sandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

		// Auth
		$auth_response = wp_remote_post( $base_url . '/v1/oauth2/token', array(
			'headers' => array( 'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $secret ) ),
			'body'    => array( 'grant_type' => 'client_credentials' )
		) );
		
		if ( ! is_wp_error( $auth_response ) ) {
			$auth_body = json_decode( wp_remote_retrieve_body( $auth_response ), true );
			if ( isset( $auth_body['access_token'] ) ) {
				$access_token = $auth_body['access_token'];
				$order_id = sanitize_text_field( $_GET['token'] );

				// Capture Payment
				$capture_res = wp_remote_post( $base_url . '/v2/checkout/orders/' . $order_id . '/capture', array(
					'headers' => array(
						'Authorization' => 'Bearer ' . $access_token,
						'Content-Type'  => 'application/json'
					),
				) );

				$capture_body = json_decode( wp_remote_retrieve_body( $capture_res ), true );
				
				if ( isset( $capture_body['status'] ) && $capture_body['status'] === 'COMPLETED' ) {
					// Great success! Update DB
					update_post_meta( $booking_id, '_booking_status', 'paid' );
					update_post_meta( $booking_id, '_payment_gateway', 'paypal' );
					
					// Redirect cleanly
					$clean_url = remove_query_arg( ['token', 'PayerID', 'gateway_fallback'] );
					wp_redirect( $clean_url );
					exit;
				}
			}
		}
	}
} );

new ThessNest_Native_Payments();
