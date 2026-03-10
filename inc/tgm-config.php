<?php
/**
 * ThessNest — TGM Plugin Activation Setup
 *
 * Configures the required and recommended plugins for the ThessNest theme
 * using the official TGM Plugin Activation library standard for Premium themes.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'thessnest_register_required_plugins' );

function thessnest_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 */
	$plugins = array(

		// 1. Redux Framework (Required for Theme Options)
		array(
			'name'      => 'Redux Framework',
			'slug'      => 'redux-framework',
			'required'  => true,
		),

		// 2. WP Mail SMTP (Required for Booking/Message forms)
		array(
			'name'      => 'WP Mail SMTP by WPForms',
			'slug'      => 'wp-mail-smtp',
			'required'  => true,
		),

		// 3. Contact Form 7 (Recommended)
		array(
			'name'      => 'Contact Form 7',
			'slug'      => 'contact-form-7',
			'required'  => false,
		),

		// 4. Loco Translate (Recommended)
		array(
			'name'      => 'Loco Translate',
			'slug'      => 'loco-translate',
			'required'  => false,
		),
		
		// 5. One Click Demo Import (Recommended)
		array(
			'name'      => 'One Click Demo Import',
			'slug'      => 'one-click-demo-import',
			'required'  => false,
		),

		// 6. Elementor (Recommended for Customizing Pages)
		array(
			'name'      => 'Elementor Website Builder',
			'slug'      => 'elementor',
			'required'  => false,
		),

		// 7. WooCommerce (Required for Payments & Subscriptions)
		array(
			'name'      => 'WooCommerce',
			'slug'      => 'woocommerce',
			'required'  => true,
		),
	);

	/*
	 * Configuration settings for TGM Plugin Activation.
	 */
	$config = array(
		'id'           => 'thessnest',             // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                    // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}
