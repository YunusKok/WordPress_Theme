<?php
/**
 * ThessNest — AJAX Favorites System
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handle AJAX request to toggle favorite property.
 */
function thessnest_toggle_favorite() {
	check_ajax_referer( 'thessnest-nonce', 'security' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'You must be logged in to save properties.', 'thessnest' ) ) );
	}

	$property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;

	if ( ! $property_id || get_post_type( $property_id ) !== 'property' ) {
		wp_send_json_error( array( 'message' => __( 'Invalid property ID.', 'thessnest' ) ) );
	}

	$user_id = get_current_user_id();
	$favorites = get_user_meta( $user_id, 'thessnest_favorites', true );

	if ( ! is_array( $favorites ) ) {
		$favorites = array();
	}

	$is_saved = false;

	if ( in_array( $property_id, $favorites ) ) {
		// Remove from favorites
		$favorites = array_diff( $favorites, array( $property_id ) );
		$is_saved = false;
	} else {
		// Add to favorites
		$favorites[] = $property_id;
		$favorites = array_unique( $favorites );
		$is_saved = true;
	}

	update_user_meta( $user_id, 'thessnest_favorites', $favorites );

	wp_send_json_success( array(
		'is_saved' => $is_saved,
		'message'  => $is_saved ? __( 'Property saved.', 'thessnest' ) : __( 'Property removed.', 'thessnest' )
	) );
}
add_action( 'wp_ajax_thessnest_toggle_favorite', 'thessnest_toggle_favorite' );
add_action( 'wp_ajax_nopriv_thessnest_toggle_favorite', 'thessnest_toggle_favorite' );
