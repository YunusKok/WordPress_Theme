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
			// To be replaced with actual raw URLs when the theme is hosted:
			// 'local_import_file'            => trailingslashit( get_template_directory() ) . 'demo-data/demo-content.xml',
			// 'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'demo-data/widgets.json',
			// 'local_import_redux'           => array(
			// 	array(
			// 		'file_path'   => trailingslashit( get_template_directory() ) . 'demo-data/redux-options.json',
			// 		'option_name' => 'thessnest_opt',
			// 	),
			// ),
			'import_preview_image_url'     => get_template_directory_uri() . '/screenshot.png',
			'import_notice'                => esc_html__( 'After importing this demo, wait a few moments. All properties, menus, and Redux settings will be configured automatically.', 'thessnest' ),
			'preview_url'                  => 'https://example.com/thessnest-demo',
		),
	);
}
add_filter( 'ocdi/import_files', 'thessnest_ocdi_import_files' );

/**
 * Execute custom actions after demo import finishes.
 * Sets the front page and navigation menus.
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
	$front_page_id = get_page_by_title( 'Home' );

	if ( isset( $front_page_id->ID ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
	}

	// 3. Flush Permalinks
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	update_option( 'rewrite_rules', false );
	$wp_rewrite->flush_rules();
}
add_action( 'ocdi/after_import', 'thessnest_ocdi_after_import_setup' );

/**
 * Disable PT branding for a cleaner admin experience.
 */
add_filter( 'ocdi/disable_pt_branding', '__return_true' );
