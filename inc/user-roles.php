<?php
/**
 * ThessNest — User Roles & Capabilities
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register custom user roles (Landlord and Tenant).
 */
function thessnest_add_roles() {
	// Add Landlord Role
	add_role( 'landlord', __( 'Landlord', 'thessnest' ), array(
		'read'         => true,
		'edit_posts'   => false,
		'delete_posts' => false,
	) );

	// Add Tenant Role
	add_role( 'tenant', __( 'Tenant', 'thessnest' ), array(
		'read'         => true,
		'edit_posts'   => false,
		'delete_posts' => false,
	) );
}
add_action( 'init', 'thessnest_add_roles' );
