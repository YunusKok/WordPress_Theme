<?php
/**
 * ThessNest — AJAX Booking Engine
 *
 * Handles availability fetching, booking submissions, and status management.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Endpoint: Fetch Blocked Dates for a Property
 */
function thessnest_fetch_booked_dates() {
	$property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;

	if ( ! $property_id ) {
		wp_send_json_error( array( 'message' => __( 'Missing property ID.', 'thessnest' ) ) );
	}

	// Query all approved/pending bookings for this property
	$args = array(
		'post_type'      => 'thessnest_booking',
		'posts_per_page' => -1,
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'   => '_booking_property_id',
				'value' => $property_id,
			),
			array(
				'key'     => '_booking_status',
				'value'   => array( 'confirmed', 'pending' ),
				'compare' => 'IN',
			)
		)
	);

	$query = new WP_Query( $args );
	$blocked_dates = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$checkin  = get_post_meta( get_the_ID(), '_booking_checkin', true ); // Format: Y-m-d
			$checkout = get_post_meta( get_the_ID(), '_booking_checkout', true ); // Format: Y-m-d

			if ( $checkin && $checkout ) {
				// Convert to an object format compatible with Flatpickr's 'disable' array
				$blocked_dates[] = array(
					'from' => $checkin,
					'to'   => $checkout
				);
			}
		}
		wp_reset_postdata();
	}

	wp_send_json_success( array( 'blocked_dates' => $blocked_dates ) );
}
add_action( 'wp_ajax_thessnest_fetch_booked_dates', 'thessnest_fetch_booked_dates' );
add_action( 'wp_ajax_nopriv_thessnest_fetch_booked_dates', 'thessnest_fetch_booked_dates' );

/**
 * Endpoint: Submit a new Booking Request
 */
function thessnest_submit_booking() {
	check_ajax_referer( 'thessnest-inquiry-nonce', 'security' ); // Re-using the single property form nonce

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'You must be logged in to request a booking. Please sign in or register.', 'thessnest' ), 'login_required' => true ) );
	}

	$tenant_id   = get_current_user_id();
	$property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;
	$checkin     = isset( $_POST['checkin'] ) ? sanitize_text_field( wp_unslash( $_POST['checkin'] ) ) : '';
	$checkout    = isset( $_POST['checkout'] ) ? sanitize_text_field( wp_unslash( $_POST['checkout'] ) ) : '';
	$guests      = isset( $_POST['guests'] ) ? intval( $_POST['guests'] ) : 1;
	$message     = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $property_id || ! $checkin || ! $checkout ) {
		wp_send_json_error( array( 'message' => __( 'Please select valid check-in and check-out dates.', 'thessnest' ) ) );
	}

	$property = get_post( $property_id );
	if ( ! $property || $property->post_type !== 'property' ) {
		wp_send_json_error( array( 'message' => __( 'Invalid property.', 'thessnest' ) ) );
	}

	// Calculate Pricing (Server-side validation)
	$date1 = new DateTime( $checkin );
	$date2 = new DateTime( $checkout );
	$interval = $date1->diff( $date2 );
	$nights = $interval->days;

	if ( $nights < 1 ) {
		wp_send_json_error( array( 'message' => __( 'Checkout date must be after check-in date.', 'thessnest' ) ) );
	}

	$price_per_night = floatval( get_post_meta( $property_id, '_thessnest_rent', true ) );
	$total_price = $nights * $price_per_night;

	$landlord_id = $property->post_author;
	if ( $tenant_id == $landlord_id ) {
		wp_send_json_error( array( 'message' => __( 'You cannot book your own property.', 'thessnest' ) ) );
	}

	// Check for overlap against existing confirmed/pending bookings
	// A simple check: If (new_checkin < existing_checkout) AND (new_checkout > existing_checkin), it overlaps.
	global $wpdb;
	$overlap = $wpdb->get_var( $wpdb->prepare( "
		SELECT p.ID FROM {$wpdb->posts} p
		INNER JOIN {$wpdb->postmeta} pm_prop ON p.ID = pm_prop.post_id AND pm_prop.meta_key = '_booking_property_id'
		INNER JOIN {$wpdb->postmeta} pm_status ON p.ID = pm_status.post_id AND pm_status.meta_key = '_booking_status'
		INNER JOIN {$wpdb->postmeta} pm_cin ON p.ID = pm_cin.post_id AND pm_cin.meta_key = '_booking_checkin'
		INNER JOIN {$wpdb->postmeta} pm_cout ON p.ID = pm_cout.post_id AND pm_cout.meta_key = '_booking_checkout'
		WHERE p.post_type = 'thessnest_booking'
		AND pm_prop.meta_value = %d
		AND pm_status.meta_value IN ('confirmed', 'pending')
		AND (pm_cin.meta_value < %s AND pm_cout.meta_value > %s)
		LIMIT 1
	", $property_id, $checkout, $checkin ) );

	if ( $overlap ) {
		wp_send_json_error( array( 'message' => __( 'Those dates are no longer available. Please select different dates.', 'thessnest' ) ) );
	}

	// Create the Booking Record
	$title = sprintf( __( 'Booking: %s (%s to %s)', 'thessnest' ), $property->post_title, $checkin, $checkout );

	$booking_id = wp_insert_post( array(
		'post_title'   => wp_strip_all_tags( $title ),
		'post_status'  => 'publish', // The internal post is published, but the logical booking state is pending
		'post_author'  => $tenant_id,
		'post_type'    => 'thessnest_booking',
	) );

	if ( is_wp_error( $booking_id ) ) {
		wp_send_json_error( array( 'message' => __( 'System error processing your booking. Please try again.', 'thessnest' ) ) );
	}

	// Save booking data
	update_post_meta( $booking_id, '_booking_property_id', $property_id );
	update_post_meta( $booking_id, '_booking_landlord_id', $landlord_id );
	update_post_meta( $booking_id, '_booking_checkin', $checkin );
	update_post_meta( $booking_id, '_booking_checkout', $checkout );
	update_post_meta( $booking_id, '_booking_guests', $guests );
	update_post_meta( $booking_id, '_booking_total_price', $total_price );
	update_post_meta( $booking_id, '_booking_status', 'pending' ); // Status can be 'pending', 'confirmed', 'rejected', 'canceled'

	// Optional: Fire an internal message to the landlord so they get it in their Inbox
	$inbox_subject = sprintf( __( 'Booking Request for %s', 'thessnest' ), $property->post_title );
	$inbox_body = sprintf( __( "New booking request details:\nCheck-in: %s\nCheck-out: %s\nTotal Price: %s\nGuests: %d\n\nMessage from guest: %s", 'thessnest' ), 
		$checkin, $checkout, thessnest_format_price( $total_price ), $guests, $message );
	
	$message_id = wp_insert_post( array(
		'post_title'   => wp_strip_all_tags( $inbox_subject ),
		'post_content' => wp_kses_post( $inbox_body ),
		'post_status'  => 'publish',
		'post_author'  => $tenant_id,
		'post_type'    => 'thessnest_message',
	) );

	if ( ! is_wp_error( $message_id ) ) {
		update_post_meta( $message_id, '_recipient_id', $landlord_id );
		update_post_meta( $message_id, '_property_id', $property_id );
		update_post_meta( $message_id, '_is_read', 0 );
	}

	wp_send_json_success( array( 'message' => __( 'Booking request sent successfully! The landlord will review your request.', 'thessnest' ) ) );
}
add_action( 'wp_ajax_thessnest_submit_booking', 'thessnest_submit_booking' );


/**
 * Endpoint: Manage Booking Status (Dashboard)
 */
function thessnest_manage_booking() {
	check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Authentication required.', 'thessnest' ) ) );
	}

	$action_type = isset( $_POST['booking_action'] ) ? sanitize_text_field( $_POST['booking_action'] ) : ''; // 'accept' or 'reject' or 'cancel'
	$booking_id  = isset( $_POST['booking_id'] ) ? intval( $_POST['booking_id'] ) : 0;
	$user_id     = get_current_user_id();

	if ( ! in_array( $action_type, array('accept', 'reject', 'cancel') ) || ! $booking_id ) {
		wp_send_json_error( array( 'message' => __( 'Invalid request parameters.', 'thessnest' ) ) );
	}

	$booking = get_post( $booking_id );
	if ( ! $booking || $booking->post_type !== 'thessnest_booking' ) {
		wp_send_json_error( array( 'message' => __( 'Booking not found.', 'thessnest' ) ) );
	}

	$landlord_id = (int) get_post_meta( $booking_id, '_booking_landlord_id', true );
	$tenant_id   = (int) $booking->post_author;
	$current_status = get_post_meta( $booking_id, '_booking_status', true );

	if ( $action_type === 'accept' || $action_type === 'reject' ) {
		if ( $user_id !== $landlord_id ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized. Only the landlord can accept or reject bookings.', 'thessnest' ) ) );
		}
		
		$new_status = ( $action_type === 'accept' ) ? 'confirmed' : 'rejected';
		update_post_meta( $booking_id, '_booking_status', $new_status );

		// Fire internal message to tenant
		$msg_subject = sprintf( __( 'Update on your booking for %s', 'thessnest' ), get_the_title( get_post_meta( $booking_id, '_booking_property_id', true ) ) );
		$msg_body    = ( $action_type === 'accept' ) ? __( 'Great news! The landlord accepted your booking request. Have a great trip!', 'thessnest' ) : __( 'The landlord unfortunately declined your booking request. Please check other properties.', 'thessnest' );
		
		$msg_id = wp_insert_post( array(
			'post_title'   => wp_strip_all_tags( $msg_subject ),
			'post_content' => wp_kses_post( $msg_body ),
			'post_status'  => 'publish',
			'post_author'  => $landlord_id,
			'post_type'    => 'thessnest_message',
		) );
		
		if ( ! is_wp_error( $msg_id ) ) {
			update_post_meta( $msg_id, '_recipient_id', $tenant_id );
			update_post_meta( $msg_id, '_property_id', get_post_meta( $booking_id, '_booking_property_id', true ) );
			update_post_meta( $msg_id, '_is_read', 0 );
		}

		wp_send_json_success( array( 'message' => sprintf( __( 'Booking marked as %s.', 'thessnest' ), $new_status ), 'new_status' => $new_status ) );

	} elseif ( $action_type === 'cancel' ) {
		if ( $user_id !== $tenant_id ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized. Only the guest can cancel this trip.', 'thessnest' ) ) );
		}
		
		update_post_meta( $booking_id, '_booking_status', 'canceled' );
		wp_send_json_success( array( 'message' => __( 'Trip canceled successfully.', 'thessnest' ), 'new_status' => 'canceled' ) );
	}
}
add_action( 'wp_ajax_thessnest_manage_booking', 'thessnest_manage_booking' );
