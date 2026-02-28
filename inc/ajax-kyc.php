<?php
/**
 * ThessNest — AJAX KYC (Know Your Customer) System
 *
 * Handles secure document uploads for Landlord verification.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handle KYC Document Upload
 */
function thessnest_submit_kyc() {
	check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Authentication required.', 'thessnest' ) ) );
	}

	$user_id = get_current_user_id();

	// Check if user is a landlord
	$user = get_userdata( $user_id );
	if ( ! in_array( 'landlord', (array) $user->roles ) && ! current_user_can( 'administrator' ) ) {
		wp_send_json_error( array( 'message' => __( 'Only landlords can submit KYC documents.', 'thessnest' ) ) );
	}

	// Check if already verified or pending
	$current_status = get_user_meta( $user_id, '_kyc_status', true );
	if ( $current_status === 'approved' ) {
		wp_send_json_error( array( 'message' => __( 'Your account is already verified.', 'thessnest' ) ) );
	}
	if ( $current_status === 'pending' ) {
		wp_send_json_error( array( 'message' => __( 'Your verification is already in progress.', 'thessnest' ) ) );
	}

	// Handle File Upload
	if ( empty( $_FILES['kyc_document'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Please upload a valid ID document.', 'thessnest' ) ) );
	}

	$file = $_FILES['kyc_document'];
	
	// Server-side validation
	$allowed_mimes = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'pdf'          => 'application/pdf'
	);
	
	// Validate MIME type properly using WordPress function
	$file_type = wp_check_filetype( $file['name'], $allowed_mimes );
	if ( ! $file_type['ext'] ) {
		wp_send_json_error( array( 'message' => __( 'Invalid file type. Only JPG, PNG, and PDF are allowed.', 'thessnest' ) ) );
	}

	// Max 5MB
	if ( $file['size'] > 5 * 1024 * 1024 ) {
		wp_send_json_error( array( 'message' => __( 'File size must be under 5MB.', 'thessnest' ) ) );
	}

	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );

	// Override upload behavior so the file isn't attached to a specific post ID
	$upload_overrides = array(
		'test_form' => false,
		'mimes'     => $allowed_mimes,
	);

	$attachment_id = media_handle_sideload( $file, 0, null, $upload_overrides );

	if ( is_wp_error( $attachment_id ) ) {
		wp_send_json_error( array( 'message' => $attachment_id->get_error_message() ) );
	}

	// Update User Meta
	update_user_meta( $user_id, '_kyc_document_id', $attachment_id );
	update_user_meta( $user_id, '_kyc_status', 'pending' );

	// Optional: Send email to Admin
	$admin_email = get_option( 'admin_email' );
	$subject     = sprintf( __( 'New KYC Verification Request: %s', 'thessnest' ), $user->display_name );
	$message     = sprintf( __( "User %s has submitted an ID document for verification.\nPlease check the Users section in the WP Admin panel.", 'thessnest' ), $user->display_name );
	wp_mail( $admin_email, $subject, $message );

	wp_send_json_success( array( 
		'message' => __( 'Document uploaded successfully. Your account is now pending review.', 'thessnest' ),
		'new_status' => 'pending'
	) );
}
add_action( 'wp_ajax_thessnest_submit_kyc', 'thessnest_submit_kyc' );

/**
 * Add KYC columns to the WP Admin Users Table
 */
function thessnest_kyc_user_columns( $columns ) {
	$columns['kyc_status'] = __( 'KYC Status', 'thessnest' );
	$columns['kyc_doc']    = __( 'KYC Document', 'thessnest' );
	return $columns;
}
add_filter( 'manage_users_columns', 'thessnest_kyc_user_columns' );

function thessnest_kyc_user_column_content( $value, $column_name, $user_id ) {
	if ( 'kyc_status' === $column_name ) {
		$status = get_user_meta( $user_id, '_kyc_status', true );
		if ( ! $status ) $status = 'unverified';
		
		$colors = array(
			'approved'   => '#38a169', // Green
			'pending'    => '#d97706', // Orange
			'rejected'   => '#e53e3e', // Red
			'unverified' => '#718096'  // Gray
		);
		$color = isset($colors[$status]) ? $colors[$status] : '#718096';
		
		return '<strong style="color:'.$color.'; text-transform:uppercase;">' . esc_html( $status ) . '</strong>';
	}
	
	if ( 'kyc_doc' === $column_name ) {
		$doc_id = get_user_meta( $user_id, '_kyc_document_id', true );
		if ( $doc_id ) {
			$url = wp_get_attachment_url( $doc_id );
			return '<a href="' . esc_url( $url ) . '" target="_blank" class="button button-small">' . __( 'View ID', 'thessnest' ) . '</a>';
		}
		return 'N/A';
	}
	
	return $value;
}
add_filter( 'manage_users_custom_column', 'thessnest_kyc_user_column_content', 10, 3 );
