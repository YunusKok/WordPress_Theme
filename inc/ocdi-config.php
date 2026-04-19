<?php
/**
 * ThessNest — One Click Demo Import (OCDI) Configuration
 *
 * This file integrates the popular OCDI plugin, allowing users to import
 * demo content, widgets, and Redux Framework options seamlessly.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Filter the OCDI demo files array.
 */
function thessnest_ocdi_import_files() {
	return array(
		array(
			'import_file_name'             => esc_html__( 'ThessNest Default Demo', 'thessnest' ),
			'local_import_file'            => trailingslashit( get_template_directory() ) . 'demo-data/demo-content.xml',
			'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'demo-data/widgets.json',
			'local_import_redux'           => array(
				array(
					'file_path'   => trailingslashit( get_template_directory() ) . 'demo-data/redux-options.json',
					'option_name' => 'thessnest_opt',
				),
			),
			'import_preview_image_url'     => get_template_directory_uri() . '/screenshot.png',
			'import_notice'                => esc_html__( 'After importing this demo, wait a few moments. All properties, menus, and Redux settings will be configured automatically.', 'thessnest' ),
			'preview_url'                  => 'https://thessnest.com/demo',
		),
	);
}
add_filter( 'ocdi/import_files', 'thessnest_ocdi_import_files' );

/**
 * Execute custom actions after demo import finishes.
 * Sets the front page, navigation menus, and imports demo images.
 */
function thessnest_ocdi_after_import_setup() {
	// 1. Assign Menus
	$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );

	if ( $main_menu ) {
		set_theme_mod( 'nav_menu_locations', array(
			'primary' => $main_menu->term_id,
		) );
	}

	// 2. Assign Front Page
	$front_page = get_page_by_path( 'home' );
	if ( $front_page ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page->ID );
	}

	// 3. Flush Permalinks
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	update_option( 'rewrite_rules', false );
	$wp_rewrite->flush_rules();

	// 4. Attach remote demo images to imported properties
	thessnest_ocdi_attach_demo_images();
}
add_action( 'ocdi/after_import', 'thessnest_ocdi_after_import_setup' );

/**
 * Download demo images from a remote CDN and attach them to demo properties
 * as featured images. No images are bundled in the theme package — they are
 * fetched on demand during demo import, keeping the theme ZIP lightweight.
 *
 * Images are sourced from Unsplash (free, no attribution required for use).
 * Each URL targets a fixed photo ID so the result is always consistent.
 */
function thessnest_ocdi_attach_demo_images() {
	// Require WordPress media sideload helpers.
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	/**
	 * Map: property post slug => remote image URL.
	 *
	 * Unsplash source URLs return a JPEG at the requested dimensions.
	 * Using fixed photo IDs guarantees a consistent, relevant image every time.
	 */
	$demo_images = array(
		// Modern Studio in City Center — bright urban interior
		'modern-studio-city-center' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200&h=800&fit=crop&q=80',
		// Sea-View Apartment Kalamaria — coastal apartment with sea view
		'sea-view-kalamaria'        => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&h=800&fit=crop&q=80',
		// Cozy Toumba Flat — cozy living room
		'cozy-toumba-flat'          => 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=1200&h=800&fit=crop&q=80',
		// Luxury Nomad Nest Panorama — luxury apartment with city view
		'luxury-nomad-panorama'     => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=1200&h=800&fit=crop&q=80',
	);

	foreach ( $demo_images as $slug => $image_url ) {
		$property = get_page_by_path( $slug, OBJECT, 'property' );

		if ( ! $property ) {
			continue;
		}

		// Skip if a featured image is already set (e.g. re-running import).
		if ( has_post_thumbnail( $property->ID ) ) {
			continue;
		}

		// Download the remote image into the WordPress media library.
		$attachment_id = media_sideload_image( $image_url, $property->ID, $property->post_title, 'id' );

		if ( is_wp_error( $attachment_id ) ) {
			continue;
		}

		// Set as the featured image for this property.
		set_post_thumbnail( $property->ID, $attachment_id );
	}

	// Also set a hero background image in the Customizer if not already set.
	if ( ! get_theme_mod( 'hero_background_image' ) ) {
		$hero_url = 'https://images.unsplash.com/photo-1555993539-1732b0258235?w=1920&h=1080&fit=crop&q=80';
		$hero_id  = media_sideload_image( $hero_url, 0, 'ThessNest Hero Background', 'id' );

		if ( ! is_wp_error( $hero_id ) ) {
			set_theme_mod( 'hero_background_image', wp_get_attachment_url( $hero_id ) );
		}
	}
}

/**
 * Disable PT branding for a cleaner admin experience.
 */
add_filter( 'ocdi/disable_pt_branding', '__return_true' );
