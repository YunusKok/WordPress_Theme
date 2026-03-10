<?php
/**
 * ThessNest — Monetization Engine
 *
 * Handles the "Pay to Publish" flow. Allows landlords to pay a one-time
 * platform fee via WooCommerce to change a property status from 'pending' to 'publish'.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Monetization {

	public function __construct() {
		add_action( 'wp_ajax_thessnest_pay_to_publish', [ $this, 'generate_publish_checkout_link' ] );
		
		// Hook into WC Order complete to auto-publish the listing
		add_action( 'woocommerce_payment_complete', [ $this, 'auto_publish_property_on_payment' ] );
		// For orders that skip processing (e.g. 100% virtual/downloadable)
		add_action( 'woocommerce_order_status_completed', [ $this, 'auto_publish_property_on_payment' ] );
	}

	/**
	 * Creates a WooCommerce product for the Listing Fee and redirects to Checkout
	 */
	public function generate_publish_checkout_link() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

		if ( ! is_user_logged_in() || ! class_exists( 'WooCommerce' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'WooCommerce is not active. Publishing is free by default.', 'thessnest' ) ] );
		}

		$property_id  = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;
		$landlord_id  = get_current_user_id();

		$property = get_post( $property_id );
		if ( ! $property || $property->post_author != $landlord_id || $property->post_type !== 'property' ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid property.', 'thessnest' ) ] );
		}

		if ( $property->post_status === 'publish' ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Property is already published.', 'thessnest' ) ] );
		}

		// Pricing: For MVP, static €10. You can map this to Theme Options (Redux) later.
		$listing_fee = 10; 
		
		if ( function_exists( 'thessnest_opt' ) ) {
			$opt_fee = thessnest_opt( 'monetization_listing_fee', 10 );
			if ( $opt_fee >= 0 ) {
				$listing_fee = (float) $opt_fee;
			}
		}

		if ( $listing_fee <= 0 ) {
			// Free to publish
			wp_update_post( [ 'ID' => $property_id, 'post_status' => 'publish' ] );
			wp_send_json_success( [
				'message' => esc_html__( 'Property published for free!', 'thessnest' ),
				'redirect' => home_url( '/dashboard/?tab=properties' )
			] );
		}

		// Check if a payment product already exists for this exact listing fee
		$product_id = get_post_meta( $property_id, '_woo_publish_fee_product_id', true );

		if ( ! $product_id ) {
			// Create a Hidden "Listing Fee" Product
			$product = new WC_Product_Simple();
			$product->set_name( sprintf( esc_html__( 'Publishing Fee: %s', 'thessnest' ), $property->post_title ) );
			$product->set_status( 'publish' );
			$product->set_catalog_visibility( 'hidden' );
			$product->set_price( $listing_fee );
			$product->set_regular_price( $listing_fee );
			$product->set_virtual( true );
			$product->set_sold_individually( true );
			
			$product_id = $product->save();
			
			update_post_meta( $product_id, '_thessnest_publishing_for_property', $property_id );
			update_post_meta( $property_id, '_woo_publish_fee_product_id', $product_id );
		}

		// Generate the Add To Cart URL
		$checkout_url = wc_get_checkout_url();
		$payment_link = add_query_arg( [ 'add-to-cart' => $product_id ], $checkout_url );

		wp_send_json_success( [
			'message'  => esc_html__( 'Redirecting to secure checkout...', 'thessnest' ),
			'redirect' => $payment_link
		] );
	}

	/**
	 * Auto-Publish the property once the WooCommerce order is paid
	 */
	public function auto_publish_property_on_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		// Loop through order items to find our hidden "Publishing Fee" product
		foreach ( $order->get_items() as $item_id => $item ) {
			$product_id = $item->get_product_id();
			$property_id = get_post_meta( $product_id, '_thessnest_publishing_for_property', true );

			if ( $property_id ) {
				// We found a publishing fee, unlock the property!
				wp_update_post( [
					'ID'          => $property_id,
					'post_status' => 'publish'
				] );
				
				// Optional: Send a congratulatory email to the landlord
			}
		}
	}
}

new ThessNest_Monetization();
