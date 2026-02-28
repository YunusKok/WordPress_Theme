<?php
/**
 * ThessNest — User Roles & Capabilities
 *
 * Roles are only added on theme activation to avoid calling
 * add_role() on every page load (which is a DB write if the
 * role doesn't exist yet).
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
