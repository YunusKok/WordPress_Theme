<?php
/**
 * ThessNest — WooCommerce Integration Engine
 *
 * Hooks into the booking management process. When a landlord accepts a booking,
 * we generate a hidden WooCommerce product representing the Deposit/Platform Fee,
 * and redirect the tenant to the checkout page securely.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_WooCommerce_Integration {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Hook into the booking accept action to generate a Woo Order link
		add_action( 'wp_ajax_thessnest_generate_checkout_link', [ $this, 'generate_checkout_link' ] );
		
		// Hide the custom "Booking Fee" products from the shop loop
		add_action( 'pre_get_posts', [ $this, 'hide_booking_products_from_shop' ] );
	}

	/**
	 * Creates a hidden WooCommerce product for the specific booking 
	 * and returns the add-to-cart checkout link.
	 */
	public static function generate_checkout_link_internal( $booking_id ) {
		$property_id = get_post_meta( $booking_id, '_booking_property_id', true );
		$deposit     = (float) get_post_meta( $property_id, '_thessnest_deposit', true );
		$tenant_id   = get_post_field( 'post_author', $booking_id );
		
		// If there is no deposit/platform fee, no need for checkout
		if ( $deposit <= 0 ) {
			return false;
		}

		global $thessnest_opt;
		$engine = isset($thessnest_opt['payment_engine']) ? $thessnest_opt['payment_engine'] : 'native';

		$payment_link = '';

		if ( 'woocommerce' === $engine ) {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return new WP_Error( 'no_woo', esc_html__( 'WooCommerce is not active.', 'thessnest' ) );
			}

			// Check if a payment product already exists for this booking
			$existing_product_id = get_post_meta( $booking_id, '_woo_payment_product_id', true );

			if ( ! $existing_product_id ) {
				// Create a Hidden "Deposit" Product
				$product = new WC_Product_Simple();
				$product->set_name( sprintf( esc_html__( 'Booking Deposit: %s', 'thessnest' ), get_the_title( $property_id ) ) );
				$product->set_status( 'publish' );
				$product->set_catalog_visibility( 'hidden' ); // Keep out of shop
				$product->set_price( $deposit );
				$product->set_regular_price( $deposit );
				$product->set_virtual( true ); // No shipping
				$product->set_sold_individually( true );
				
				// Save the product
				$product_id = $product->save();
				
				// Link product to booking
				update_post_meta( $product_id, '_thessnest_related_booking', $booking_id );
				update_post_meta( $booking_id, '_woo_payment_product_id', $product_id );
			} else {
				$product_id = $existing_product_id;
			}

			// Generate the Add To Cart URL which redirects immediately to checkout
			$checkout_url = wc_get_checkout_url();
			$payment_link = add_query_arg( [ 'add-to-cart' => $product_id ], $checkout_url );

		} else {
			// NATIVE PAYMENT ENGINE (Stripe / PayPal)
			$checkout_page_cache = get_option( 'thessnest_native_checkout_page_id' );
			$checkout_page = get_post( $checkout_page_cache );
			
			if ( ! $checkout_page || $checkout_page->post_status !== 'publish' ) {
				// Create the page dynamically if it doesn't exist
				$page_id = wp_insert_post( array(
					'post_title'     => 'Secure Checkout',
					'post_name'      => 'secure-checkout',
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'page_template'  => 'template-checkout.php'
				) );
				update_option( 'thessnest_native_checkout_page_id', $page_id );
				$payment_link = get_permalink( $page_id );
			} else {
				$payment_link = get_permalink( $checkout_page->ID );
			}

			$payment_link = add_query_arg( 'booking_id', $booking_id, $payment_link );
		}

		// Update booking status to "Awaiting Payment"
		update_post_meta( $booking_id, '_booking_status', 'awaiting_payment' );

		// Send email to Tenant with the payment link
		$tenant    = get_userdata( $tenant_id );
		$prop_name = get_the_title( $property_id );
		$subject   = sprintf( esc_html__( 'Your booking for %s was accepted! Complete Payment', 'thessnest' ), $prop_name );
		
		$message  = '<h2>' . esc_html__( 'Great news!', 'thessnest' ) . '</h2>';
		$message .= '<p>' . sprintf( esc_html__( 'The landlord has accepted your booking request for <strong>%s</strong>.', 'thessnest' ), $prop_name ) . '</p>';
		$message .= '<p>' . esc_html__( 'To finalize the reservation, please complete the secure deposit payment using the link below:', 'thessnest' ) . '</p>';
		$message .= '<a href="' . esc_url( $payment_link ) . '" class="btn" style="display:inline-block; padding:12px 24px; background:#2563eb; color:#fff; text-decoration:none; border-radius:6px; margin:20px 0;">' . esc_html__( 'Complete Payment Now', 'thessnest' ) . '</a>';

		wp_mail( $tenant->user_email, $subject, $message );

		return $payment_link;
	}

	/**
	 * Keep these auto-generated deposit products completely hidden from the frontend shop loop.
	 */
	public function hide_booking_products_from_shop( $q ) {
		if ( ! is_admin() && $q->is_main_query() && $q->is_post_type_archive( 'product' ) ) {
			$meta_query = $q->get( 'meta_query' );
			if ( ! is_array( $meta_query ) ) {
				$meta_query = [];
			}
			$meta_query[] = [
				'key'     => '_thessnest_related_booking',
				'compare' => 'NOT EXISTS'
			];
			$q->set( 'meta_query', $meta_query );
		}
	}
}

// Init
new ThessNest_WooCommerce_Integration();
