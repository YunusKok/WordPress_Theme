<?php
/**
 * ThessNest — User Roles & Capabilities
 *
 * Roles are only added on theme activation to avoid calling
 * add_role() on every page load (which is a DB write if the
 * role doesn't exist yet).
 *
 * Also handles:
 * - Assigning the correct role on registration (based on Redux toggle)
 * - Setting KYC verification status for new hosts
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register custom user roles (Landlord and Tenant).
 * Hooked to after_switch_theme so it runs only once on activation.
 */
function thessnest_add_roles() {
	// Only add if not already registered
	if ( ! get_role( 'landlord' ) ) {
		add_role( 'landlord', __( 'Landlord', 'thessnest' ), array(
			'read'           => true,
			'upload_files'   => true,  // Required for KYC document uploads
			'edit_posts'     => false,
			'delete_posts'   => false,
		) );
	}

	if ( ! get_role( 'tenant' ) ) {
		add_role( 'tenant', __( 'Tenant', 'thessnest' ), array(
			'read'         => true,
			'edit_posts'   => false,
			'delete_posts' => false,
		) );
	}
}
add_action( 'after_switch_theme', 'thessnest_add_roles' );


/* ==========================================================================
   ROLE ASSIGNMENT ON REGISTRATION
   ========================================================================== */

/**
 * Assign selected role to a newly registered user.
 *
 * When "User roles on the register form" is enabled in Redux,
 * reads the thessnest_user_role POST value (landlord or tenant).
 * Otherwise falls back to the Redux default_user_role setting.
 *
 * Also sets KYC status to 'unverified' for new hosts if
 * Host Verification toggle is active.
 */
function thessnest_assign_role_on_register( $user_id ) {
	$role_selection_enabled = function_exists( 'thessnest_opt' )
		? thessnest_opt( 'enable_role_selection', '0' )
		: '0';

	$allowed_roles = array( 'landlord', 'tenant' );

	if ( $role_selection_enabled === '1' && ! empty( $_POST['thessnest_user_role'] ) ) {
		// Read from form submission
		$selected_role = sanitize_text_field( $_POST['thessnest_user_role'] );
	} else {
		// Fall back to Redux default
		$selected_role = function_exists( 'thessnest_opt' )
			? thessnest_opt( 'default_user_role', 'tenant' )
			: 'tenant';
	}

	// Security: only allow valid roles
	if ( ! in_array( $selected_role, $allowed_roles, true ) ) {
		$selected_role = 'tenant';
	}

	// Set the role
	$user = new WP_User( $user_id );
	$user->set_role( $selected_role );

	// If host verification is enabled and user registered as landlord,
	// mark them as unverified so they cannot list until admin approves.
	if ( $selected_role === 'landlord' ) {
		$verification_enabled = function_exists( 'thessnest_opt' )
			? thessnest_opt( 'enable_host_verification', false )
			: false;

		if ( $verification_enabled ) {
			update_user_meta( $user_id, '_kyc_status', 'unverified' );
		}
	}
}
add_action( 'user_register', 'thessnest_assign_role_on_register' );


/* ==========================================================================
   HOST CAN LIST HELPER
   ========================================================================== */

/**
 * Check if the current host is allowed to create listings.
 *
 * Returns true if:
 * - Host verification is disabled in Redux, OR
 * - The landlord's KYC status is 'approved'
 *
 * @param int|null $user_id User ID. Defaults to current user.
 * @return bool
 */
function thessnest_host_can_list( $user_id = null ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	// If host verification is disabled globally, everyone can list
	$verification_enabled = function_exists( 'thessnest_opt' )
		? thessnest_opt( 'enable_host_verification', false )
		: false;

	if ( ! $verification_enabled ) {
		return true;
	}

	// Admins can always list
	if ( user_can( $user_id, 'manage_options' ) ) {
		return true;
	}

	$status = get_user_meta( $user_id, '_kyc_status', true );
	return ( $status === 'approved' );
}

