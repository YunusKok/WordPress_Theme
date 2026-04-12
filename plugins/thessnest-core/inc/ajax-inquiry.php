<?php
/**
 * ThessNest — AJAX Property Inquiry
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handle AJAX request to submit an inquiry for a property.
 */
function thessnest_submit_inquiry() {
	check_ajax_referer( 'thessnest-inquiry-nonce', 'security' );

	$property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;
	$name        = isset( $_POST['inquiry_name'] ) ? sanitize_text_field( wp_unslash( $_POST['inquiry_name'] ) ) : '';
	$email       = isset( $_POST['inquiry_email'] ) ? sanitize_email( wp_unslash( $_POST['inquiry_email'] ) ) : '';
	$message     = isset( $_POST['inquiry_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['inquiry_message'] ) ) : '';

	if ( ! $property_id || ! $name || ! is_email( $email ) || ! $message ) {
		wp_send_json_error( array( 'message' => __( 'Please fill in all required fields correctly.', 'thessnest' ) ) );
	}

	$property = get_post( $property_id );

	if ( ! $property || $property->post_type !== 'property' ) {
		wp_send_json_error( array( 'message' => __( 'Invalid property.', 'thessnest' ) ) );
	}

	$landlord_id = $property->post_author;
	$landlord    = get_userdata( $landlord_id );

	if ( ! $landlord ) {
		wp_send_json_error( array( 'message' => __( 'Could not find the landlord for this property.', 'thessnest' ) ) );
	}

	$landlord_email = $landlord->user_email;
	$subject        = sprintf( __( 'New Inquiry for: %s', 'thessnest' ), $property->post_title );

	$body  = sprintf( __( 'You have received a new inquiry for your property: %s', 'thessnest' ), $property->post_title ) . "\n\n";
	$body .= sprintf( __( 'Name: %s', 'thessnest' ), $name ) . "\n";
	$body .= sprintf( __( 'Email: %s', 'thessnest' ), $email ) . "\n\n";
	$body .= __( 'Message:', 'thessnest' ) . "\n" . $message . "\n";

	$headers = array(
		'Reply-To: ' . $name . ' <' . $email . '>'
	);

	$sent = wp_mail( $landlord_email, $subject, $body, $headers );

	// --- NEW: Internal Messaging System Integration ---
	if ( is_user_logged_in() ) {
		$sender_id = get_current_user_id();
		
		// Create the message post
		$message_id = wp_insert_post( array(
			'post_title'   => wp_strip_all_tags( $subject ),
			'post_content' => wp_kses_post( $message ),
			'post_status'  => 'publish',
			'post_author'  => $sender_id,
			'post_type'    => 'thessnest_message',
		) );

		if ( ! is_wp_error( $message_id ) ) {
			// Save relationship meta
			update_post_meta( $message_id, '_recipient_id', $landlord_id );
			update_post_meta( $message_id, '_property_id', $property_id );
			update_post_meta( $message_id, '_is_read', 0 );
		}
	}
	// ----------------------------------------------------

	if ( $sent ) {
		wp_send_json_success( array( 'message' => __( 'Your inquiry has been sent to the landlord.', 'thessnest' ) ) );
	} else {
		wp_send_json_error( array( 'message' => __( 'There was a problem sending your message. Please try again later.', 'thessnest' ) ) );
	}
}
add_action( 'wp_ajax_thessnest_submit_inquiry', 'thessnest_submit_inquiry' );
add_action( 'wp_ajax_nopriv_thessnest_submit_inquiry', 'thessnest_submit_inquiry' );
