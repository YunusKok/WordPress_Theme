<?php
/**
 * ThessNest — AJAX Add Listing Handler
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handle frontend property submission.
 */
function thessnest_submit_listing() {
	check_ajax_referer( 'thessnest_add_listing_nonce', 'security' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'You must be logged in to submit a listing.', 'thessnest' ) ) );
	}

	// Block unverified hosts from submitting listings
	if ( function_exists( 'thessnest_host_can_list' ) && ! thessnest_host_can_list() ) {
		wp_send_json_error( array( 'message' => __( 'Your account is not yet verified. Please complete the ID verification in your Dashboard before listing a property.', 'thessnest' ) ) );
	}

	// 1. Sanitize Basic Inputs
	$title       = isset( $_POST['listing_title'] ) ? sanitize_text_field( wp_unslash( $_POST['listing_title'] ) ) : '';
	$description = isset( $_POST['listing_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['listing_description'] ) ) : '';
	$rent        = isset( $_POST['listing_rent'] ) ? intval( $_POST['listing_rent'] ) : 0;
	$utilities   = isset( $_POST['listing_utilities'] ) ? intval( $_POST['listing_utilities'] ) : 0;
	$deposit     = isset( $_POST['listing_deposit'] ) ? intval( $_POST['listing_deposit'] ) : 0;
	$wifi_speed  = isset( $_POST['listing_wifi_speed'] ) ? intval( $_POST['listing_wifi_speed'] ) : 0;
	$max_tenants = isset( $_POST['listing_max_tenants'] ) ? max( 1, intval( $_POST['listing_max_tenants'] ) ) : 1;
	
	if ( empty( $title ) || empty( $description ) || $rent <= 0 ) {
		wp_send_json_error( array( 'message' => __( 'Title, Description, and Rent are required fields.', 'thessnest' ) ) );
	}

	// 2. Create the Post (Pending Review)
	$post_data = array(
		'post_title'   => $title,
		'post_content' => $description,
		'post_status'  => 'pending', // Requires admin approval
		'post_author'  => get_current_user_id(),
		'post_type'    => 'property',
	);

	$post_id = wp_insert_post( $post_data, true );

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Error saving the listing. Please try again.', 'thessnest' ) ) );
	}

	// 3. Save Meta Data
	update_post_meta( $post_id, '_thessnest_rent', $rent );
	update_post_meta( $post_id, '_thessnest_utilities', $utilities );
	update_post_meta( $post_id, '_thessnest_deposit', $deposit );
	if ( $wifi_speed > 0 ) {
		update_post_meta( $post_id, '_thessnest_wifi_speed', $wifi_speed );
	}
	update_post_meta( $post_id, '_thessnest_max_tenants', $max_tenants );
	$instant_book = isset( $_POST['listing_instant_book'] ) ? '1' : '0';
	update_post_meta( $post_id, '_thessnest_instant_book', $instant_book );

	$ical_url = isset( $_POST['listing_ical_url'] ) ? esc_url_raw( $_POST['listing_ical_url'] ) : '';
	if ( ! empty( $ical_url ) ) {
		update_post_meta( $post_id, '_thessnest_ical_import_url', $ical_url );
	}

	// 4. Assign Taxonomies
	$neighborhood_id = isset( $_POST['listing_neighborhood'] ) ? intval( $_POST['listing_neighborhood'] ) : 0;
	if ( $neighborhood_id > 0 ) {
		wp_set_object_terms( $post_id, array( $neighborhood_id ), 'neighborhood', false );
	}

	if ( isset( $_POST['listing_amenities'] ) && is_array( $_POST['listing_amenities'] ) ) {
		$amenity_ids = array_map( 'intval', $_POST['listing_amenities'] );
		wp_set_object_terms( $post_id, $amenity_ids, 'amenity', false );
	}

	if ( isset( $_POST['listing_target_groups'] ) && is_array( $_POST['listing_target_groups'] ) ) {
		$group_ids = array_map( 'intval', $_POST['listing_target_groups'] );
		wp_set_object_terms( $post_id, $group_ids, 'target_group', false );
	}

	// 5. Handle Image Uploads
	$gallery_ids = array();
	$featured_image_set = false;

	if ( ! empty( $_FILES['listing_images']['name'][0] ) ) {
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$files = $_FILES['listing_images'];

		foreach ( $files['name'] as $key => $value ) {
			if ( $files['name'][ $key ] ) {
				$file = array(
					'name'     => $files['name'][ $key ],
					'type'     => $files['type'][ $key ],
					'tmp_name' => $files['tmp_name'][ $key ],
					'error'    => $files['error'][ $key ],
					'size'     => $files['size'][ $key ]
				);

				$attachment_id = media_handle_sideload( $file, $post_id );

				if ( ! is_wp_error( $attachment_id ) ) {
					// First valid image becomes the featured image
					if ( ! $featured_image_set ) {
						set_post_thumbnail( $post_id, $attachment_id );
						$featured_image_set = true;
					} else {
						// Rest go to the gallery meta
						$gallery_ids[] = $attachment_id;
					}
				}
			}
		}

		if ( ! empty( $gallery_ids ) ) {
			// Save the gallery IDs as comma-separated string (or array depending on theme structure. We use string for core-functionality helper)
			// Wait, thessnest_get_gallery in core-functionality.php uses explode(',', ...), so we need string.
			update_post_meta( $post_id, '_thessnest_gallery', implode( ',', $gallery_ids ) );
		}
	}

	wp_send_json_success( array( 
		'message' => __( 'Your property has been submitted and is Pending Review. An admin will contact you regarding the listing fee before it goes live.', 'thessnest' ),
		'redirect' => home_url( '/dashboard/?tab=properties' )
	) );
}
add_action( 'wp_ajax_thessnest_submit_listing', 'thessnest_submit_listing' );
// We do not allow nopriv (not logged in) submissions for this feature.
