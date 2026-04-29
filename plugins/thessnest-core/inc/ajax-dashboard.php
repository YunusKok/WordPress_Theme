<?php
/**
 * ThessNest — Dashboard Data Handlers
 *
 * Handles AJAX requests for the frontend User Dashboard:
 * 1. Profile information updates
 * 2. Property deletion (Trash)
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Update User Profile Information
 */
function thessnest_update_profile() {
	check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'You must be logged in to perform this action.', 'thessnest' ) ) );
	}

	$user_id = get_current_user_id();

	$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( trim( wp_unslash( $_POST['first_name'] ) ) ) : '';
	$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( trim( wp_unslash( $_POST['last_name'] ) ) ) : '';
	$email      = isset( $_POST['user_email'] ) ? sanitize_email( trim( wp_unslash( $_POST['user_email'] ) ) ) : '';

	// Basic validation
	if ( empty( $first_name ) || empty( $last_name ) || empty( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'All fields are required.', 'thessnest' ) ) );
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'thessnest' ) ) );
	}

	// Check if email exists and belongs to someone else
	if ( email_exists( $email ) && email_exists( $email ) != $user_id ) {
		wp_send_json_error( array( 'message' => __( 'This email address is already in use by another account.', 'thessnest' ) ) );
	}

	// Update user data
	$userdata = array(
		'ID'           => $user_id,
		'user_email'   => $email,
		'first_name'   => $first_name,
		'last_name'    => $last_name,
		// Also update display_name based on first and last name for better UX
		'display_name' => $first_name . ' ' . $last_name,
	);

	$result = wp_update_user( $userdata );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}

	/**
	 * Hook for extended profile meta (student fields, host fields, passport, etc.)
	 * @see thessnest_save_extended_profile_meta() in inc/user-roles.php
	 */
	do_action( 'thessnest_after_profile_save', $user_id );

	wp_send_json_success( array( 'message' => __( 'Profile updated successfully.', 'thessnest' ) ) );
}
add_action( 'wp_ajax_thessnest_update_profile', 'thessnest_update_profile' );
// No nopriv, action requires login

/**
 * Delete (Trash) a Property
 */
function thessnest_delete_property() {
	check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'You must be logged in to perform this action.', 'thessnest' ) ) );
	}

	$property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;

	if ( ! $property_id ) {
		wp_send_json_error( array( 'message' => __( 'Invalid property ID.', 'thessnest' ) ) );
	}

	$post = get_post( $property_id );

	if ( ! $post || $post->post_type !== 'property' ) {
		wp_send_json_error( array( 'message' => __( 'Property not found.', 'thessnest' ) ) );
	}

	// Security: Ensure the current user is the author of this property (or an admin)
	if ( (int) $post->post_author !== get_current_user_id() && ! current_user_can( 'administrator' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to delete this property.', 'thessnest' ) ) );
	}

	// Move to trash instead of forced delete to allow recovery from WP Admin
	$trashed = wp_trash_post( $property_id );

	if ( $trashed ) {
		wp_send_json_success( array( 
			'message' => __( 'Property successfully moved to trash.', 'thessnest' ),
			'property_id' => $property_id 
		) );
	} else {
		wp_send_json_error( array( 'message' => __( 'Failed to delete the property. Please try again later.', 'thessnest' ) ) );
	}
}
add_action( 'wp_ajax_thessnest_delete_property', 'thessnest_delete_property' );
// No nopriv, action requires login

/**
 * Save iCal Feeds from Frontend Dashboard
 */
function thessnest_save_ical_feeds_frontend() {
	check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'You must be logged in.', 'thessnest' ) ) );
	}

	$property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;
	if ( ! $property_id || 'property' !== get_post_type( $property_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid property.', 'thessnest' ) ) );
	}

	// Ownership check
	$post = get_post( $property_id );
	if ( (int) $post->post_author !== get_current_user_id() && ! current_user_can( 'administrator' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'thessnest' ) ) );
	}

	$feeds = array();
	if ( isset( $_POST['feeds'] ) && is_array( $_POST['feeds'] ) ) {
		foreach ( $_POST['feeds'] as $feed ) {
			$url  = isset( $feed['url'] ) ? esc_url_raw( $feed['url'] ) : '';
			$name = isset( $feed['name'] ) ? sanitize_text_field( $feed['name'] ) : '';
			if ( ! empty( $url ) ) {
				$feeds[] = array( 'name' => $name, 'url' => $url );
			}
		}
	}

	update_post_meta( $property_id, '_thessnest_ical_feeds', $feeds );

	// Clean up legacy meta
	if ( ! empty( $feeds ) ) {
		delete_post_meta( $property_id, '_thessnest_ical_import_url' );
	}

	wp_send_json_success( array(
		'message'    => sprintf( __( '%d feed(s) saved successfully.', 'thessnest' ), count( $feeds ) ),
		'feed_count' => count( $feeds ),
	) );
}
add_action( 'wp_ajax_thessnest_save_ical_feeds_frontend', 'thessnest_save_ical_feeds_frontend' );

