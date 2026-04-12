<?php
/**
 * ThessNest — Digital Lease Agreement System
 *
 * Auto-generates PDF lease agreements when a booking is confirmed.
 * Supports digital signatures and template customization.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Digital_Lease {

	public function __construct() {
		// Generate lease when booking confirmed
		add_action( 'updated_post_meta', array( $this, 'on_booking_confirmed' ), 20, 4 );

		// AJAX: Get lease for a booking
		add_action( 'wp_ajax_thessnest_get_lease', array( $this, 'get_lease' ) );

		// AJAX: Sign lease
		add_action( 'wp_ajax_thessnest_sign_lease', array( $this, 'sign_lease' ) );
	}

	/**
	 * Auto-generate lease data when booking is confirmed.
	 */
	public function on_booking_confirmed( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( $meta_key !== '_booking_status' || $meta_value !== 'confirmed' ) {
			return;
		}

		$booking = get_post( $object_id );
		if ( ! $booking || $booking->post_type !== 'thessnest_booking' ) {
			return;
		}

		// Skip if lease already exists
		if ( get_post_meta( $object_id, '_lease_generated', true ) ) {
			return;
		}

		$property_id = get_post_meta( $object_id, '_booking_property_id', true );
		$landlord_id = get_post_meta( $object_id, '_booking_landlord_id', true );
		$tenant_id   = $booking->post_author;
		$checkin     = get_post_meta( $object_id, '_booking_checkin', true );
		$checkout    = get_post_meta( $object_id, '_booking_checkout', true );
		$total_price = get_post_meta( $object_id, '_booking_total_price', true );

		$landlord = get_userdata( $landlord_id );
		$tenant   = get_userdata( $tenant_id );
		$property = get_post( $property_id );

		$lease_data = array(
			'booking_id'      => $object_id,
			'property_title'  => $property ? $property->post_title : '',
			'property_address'=> get_post_meta( $property_id, '_thessnest_address', true ),
			'landlord_name'   => $landlord ? $landlord->display_name : '',
			'landlord_email'  => $landlord ? $landlord->user_email : '',
			'tenant_name'     => $tenant ? $tenant->display_name : '',
			'tenant_email'    => $tenant ? $tenant->user_email : '',
			'checkin'         => $checkin,
			'checkout'        => $checkout,
			'rent_total'      => $total_price,
			'deposit'         => get_post_meta( $property_id, '_thessnest_deposit', true ),
			'generated_date'  => current_time( 'Y-m-d H:i:s' ),
			'lease_number'    => 'TN-' . strtoupper( substr( md5( $object_id . time() ), 0, 8 ) ),
			'signed_landlord' => false,
			'signed_tenant'   => false,
			'landlord_sig_date' => '',
			'tenant_sig_date'   => '',
		);

		update_post_meta( $object_id, '_lease_data', $lease_data );
		update_post_meta( $object_id, '_lease_generated', 1 );

		// Notify both parties
		$msg = sprintf( __( 'A digital lease agreement (Ref: %s) has been generated for your booking. Please review and sign it in your Dashboard.', 'thessnest' ), $lease_data['lease_number'] );

		// Message to tenant
		wp_insert_post( array(
			'post_title'   => __( 'Lease Agreement Ready for Signature', 'thessnest' ),
			'post_content' => $msg,
			'post_status'  => 'publish',
			'post_author'  => $landlord_id,
			'post_type'    => 'thessnest_message',
			'meta_input'   => array(
				'_recipient_id' => $tenant_id,
				'_property_id'  => $property_id,
				'_is_read'      => 0,
			),
		) );
	}

	/**
	 * AJAX: Get lease data for frontend display.
	 */
	public function get_lease() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );
		$booking_id = intval( $_POST['booking_id'] ?? 0 );
		$user_id    = get_current_user_id();

		$lease = get_post_meta( $booking_id, '_lease_data', true );

		if ( ! $lease ) {
			wp_send_json_error( array( 'message' => __( 'No lease found for this booking.', 'thessnest' ) ) );
		}

		// Verify user is landlord or tenant
		$landlord_id = get_post_meta( $booking_id, '_booking_landlord_id', true );
		$tenant_id   = get_post( $booking_id )->post_author;

		if ( $user_id != $landlord_id && $user_id != $tenant_id ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'thessnest' ) ) );
		}

		// Add lease template content from Redux
		$template = function_exists( 'thessnest_opt' )
			? thessnest_opt( 'lease_template', self::default_template() )
			: self::default_template();

		$lease['template_html'] = self::render_template( $template, $lease );

		wp_send_json_success( $lease );
	}

	/**
	 * AJAX: Sign the lease.
	 */
	public function sign_lease() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

		$booking_id = intval( $_POST['booking_id'] ?? 0 );
		$user_id    = get_current_user_id();
		$signature  = sanitize_text_field( $_POST['signature'] ?? '' ); // Full name as typed signature

		if ( ! $booking_id || empty( $signature ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing booking ID or signature.', 'thessnest' ) ) );
		}

		$lease = get_post_meta( $booking_id, '_lease_data', true );
		if ( ! $lease ) {
			wp_send_json_error( array( 'message' => __( 'Lease not found.', 'thessnest' ) ) );
		}

		$landlord_id = get_post_meta( $booking_id, '_booking_landlord_id', true );
		$tenant_id   = get_post( $booking_id )->post_author;

		if ( $user_id == $landlord_id ) {
			$lease['signed_landlord']   = true;
			$lease['landlord_sig_date'] = current_time( 'Y-m-d H:i:s' );
			$lease['landlord_sig_name'] = $signature;
		} elseif ( $user_id == $tenant_id ) {
			$lease['signed_tenant']   = true;
			$lease['tenant_sig_date'] = current_time( 'Y-m-d H:i:s' );
			$lease['tenant_sig_name'] = $signature;
		} else {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'thessnest' ) ) );
		}

		update_post_meta( $booking_id, '_lease_data', $lease );

		$both_signed = $lease['signed_landlord'] && $lease['signed_tenant'];
		$message = $both_signed
			? __( 'Lease fully signed by both parties! 🎉', 'thessnest' )
			: __( 'Your signature has been recorded. Waiting for the other party.', 'thessnest' );

		wp_send_json_success( array(
			'message'     => $message,
			'both_signed' => $both_signed,
		) );
	}

	/**
	 * Default lease template with placeholders.
	 */
	private static function default_template() {
		return __( "RESIDENTIAL LEASE AGREEMENT\n\nRef: {{lease_number}}\nDate: {{generated_date}}\n\nLANDLORD: {{landlord_name}} ({{landlord_email}})\nTENANT: {{tenant_name}} ({{tenant_email}})\n\nPROPERTY: {{property_title}}\nADDRESS: {{property_address}}\n\nLEASE TERM: {{checkin}} to {{checkout}}\nTOTAL RENT: €{{rent_total}}\nSECURITY DEPOSIT: €{{deposit}}\n\nTERMS: The tenant agrees to pay the above rent for the lease period. The security deposit will be refunded within 14 days of checkout, subject to property inspection.\n\nBoth parties agree to the terms above by signing below.", 'thessnest' );
	}

	/**
	 * Replace template placeholders with actual values.
	 */
	private static function render_template( $template, $data ) {
		$replacements = array(
			'{{lease_number}}'    => $data['lease_number'] ?? '',
			'{{generated_date}}'  => $data['generated_date'] ?? '',
			'{{landlord_name}}'   => $data['landlord_name'] ?? '',
			'{{landlord_email}}'  => $data['landlord_email'] ?? '',
			'{{tenant_name}}'     => $data['tenant_name'] ?? '',
			'{{tenant_email}}'    => $data['tenant_email'] ?? '',
			'{{property_title}}'  => $data['property_title'] ?? '',
			'{{property_address}}'=> $data['property_address'] ?? '',
			'{{checkin}}'         => $data['checkin'] ?? '',
			'{{checkout}}'        => $data['checkout'] ?? '',
			'{{rent_total}}'      => $data['rent_total'] ?? '',
			'{{deposit}}'         => $data['deposit'] ?? '',
		);
		return str_replace( array_keys( $replacements ), array_values( $replacements ), $template );
	}
}

new ThessNest_Digital_Lease();
