<?php
/**
 * ThessNest — Redux Framework Configuration
 *
 * Defines all theme options using Redux Framework.
 * Requires the Redux Framework plugin to be installed and active.
 *
 * @package ThessNest
 * @link    https://wordpress.org/plugins/redux-framework/
 */

defined( 'ABSPATH' ) || exit;

// Bail early if Redux is not loaded.
if ( ! class_exists( 'Redux' ) ) {
	return;
}

// ── Option name used to store all settings ──
$opt_name = 'thessnest_options';

// ═══════════════════════════════════════════════════
//  REDUX GLOBAL ARGUMENTS
// ═══════════════════════════════════════════════════

Redux::set_args( $opt_name, array(
	'opt_name'             => $opt_name,
	'display_name'         => 'ThessNest',
	'display_version'      => THESSNEST_VERSION,
	'menu_type'            => 'submenu',
	'allow_sub_menu'       => true,
	'page_parent'          => 'thessnest-dashboard',     // Attach under our custom menu
	'page_slug'            => 'thessnest-options',
	'menu_title'           => __( 'ThessNest Options', 'thessnest' ),
	'page_title'           => __( 'ThessNest Options', 'thessnest' ),
	'admin_bar'            => true,
	'admin_bar_icon'       => 'dashicons-admin-home',
	'admin_bar_priority'   => 40,
	'global_variable'      => 'thessnest_opts',
	'dev_mode'             => false,
	'update_notice'        => false,
	'customizer'           => false,
	'page_permissions'     => 'manage_options',
	'save_defaults'        => true,
	'show_import_export'   => true,
	'database'             => '',
	'transient_time'       => 60 * MINUTE_IN_SECONDS,
	'output'               => true,
	'output_tag'           => true,
	'footer_credit'        => __( 'ThessNest Theme Options — Powered by Redux Framework', 'thessnest' ),
	'hints'                => array(
		'icon'    => 'el el-question-sign',
		'icon_position' => 'right',
		'icon_size'     => 'normal',
		'tip_style'     => array( 'color' => 'light', 'shadow' => true, 'rounded' => false ),
		'tip_position'  => array( 'my' => 'top left', 'at' => 'bottom right' ),
		'tip_effect'    => array( 'show' => array( 'effect' => 'slide', 'duration' => '500' ), 'hide' => array( 'effect' => 'slide', 'duration' => '500' ) ),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 1: General
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'General', 'thessnest' ),
	'id'     => 'general',
	'icon'   => 'el el-home',
	'fields' => array(
		array(
			'id'       => 'site_description',
			'type'     => 'textarea',
			'title'    => __( 'Site Description', 'thessnest' ),
			'subtitle' => __( 'A short description of your platform.', 'thessnest' ),
			'default'  => 'Mid-term housing for Erasmus students & Digital Nomads in Thessaloniki.',
		),
		array(
			'id'       => 'default_language',
			'type'     => 'select',
			'title'    => __( 'Default Language', 'thessnest' ),
			'options'  => array(
				'en' => 'English',
				'tr' => 'Türkçe',
				'el' => 'Ελληνικά',
			),
			'default'  => 'en',
		),
		array(
			'id'       => 'google_maps_api_key',
			'type'     => 'text',
			'title'    => __( 'Google Maps API Key', 'thessnest' ),
			'subtitle' => __( 'Required for map features. Get one at console.cloud.google.com', 'thessnest' ),
			'default'  => '',
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 2: Logos & Favicon
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Logos & Favicon', 'thessnest' ),
	'id'     => 'logos',
	'icon'   => 'el el-picture',
	'fields' => array(
		array(
			'id'       => 'logo',
			'type'     => 'media',
			'title'    => __( 'Logo (Light Background)', 'thessnest' ),
			'subtitle' => __( 'Upload the main logo. Recommended: PNG with transparency.', 'thessnest' ),
		),
		array(
			'id'       => 'logo_dark',
			'type'     => 'media',
			'title'    => __( 'Logo (Dark Background)', 'thessnest' ),
			'subtitle' => __( 'Optional. Used when header is transparent over a dark image.', 'thessnest' ),
		),
		array(
			'id'       => 'favicon',
			'type'     => 'media',
			'title'    => __( 'Favicon', 'thessnest' ),
			'subtitle' => __( 'Upload a 32x32 or 64x64 PNG icon.', 'thessnest' ),
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 3: Header Nav
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Header Nav', 'thessnest' ),
	'id'     => 'header',
	'icon'   => 'el el-lines',
	'fields' => array(
		array(
			'id'       => 'sticky_header',
			'type'     => 'switch',
			'title'    => __( 'Sticky Header', 'thessnest' ),
			'subtitle' => __( 'Enable/disable the sticky header on scroll.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'header_style',
			'type'     => 'select',
			'title'    => __( 'Header Style', 'thessnest' ),
			'options'  => array(
				'default'     => __( 'Default (Solid)', 'thessnest' ),
				'transparent' => __( 'Transparent (Hero Overlay)', 'thessnest' ),
			),
			'default'  => 'default',
		),
		array(
			'id'       => 'header_cta_text',
			'type'     => 'text',
			'title'    => __( 'Header CTA Button Text', 'thessnest' ),
			'subtitle' => __( 'e.g. "List Your Property". Leave empty to hide.', 'thessnest' ),
			'default'  => '',
		),
		array(
			'id'       => 'header_cta_url',
			'type'     => 'text',
			'title'    => __( 'Header CTA Button URL', 'thessnest' ),
			'default'  => '',
		),
		array(
			'id'       => 'header_search',
			'type'     => 'switch',
			'title'    => __( 'Show Search in Header', 'thessnest' ),
			'subtitle' => __( 'Enable/disable the search bar in the navigation.', 'thessnest' ),
			'default'  => true,
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 4: Booking Settings
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Booking', 'thessnest' ),
	'id'     => 'booking',
	'icon'   => 'el el-calendar',
	'fields' => array(
		array(
			'id'       => 'min_stay',
			'type'     => 'spinner',
			'title'    => __( 'Minimum Stay (days)', 'thessnest' ),
			'subtitle' => __( 'Minimum rental period allowed.', 'thessnest' ),
			'default'  => 30,
			'min'      => 1,
			'max'      => 365,
			'step'     => 1,
		),
		array(
			'id'       => 'max_stay',
			'type'     => 'spinner',
			'title'    => __( 'Maximum Stay (days)', 'thessnest' ),
			'subtitle' => __( 'Maximum rental period allowed.', 'thessnest' ),
			'default'  => 365,
			'min'      => 1,
			'max'      => 730,
			'step'     => 1,
		),
		array(
			'id'       => 'deposit_rate',
			'type'     => 'spinner',
			'title'    => __( 'Default Deposit Rate (%)', 'thessnest' ),
			'subtitle' => __( 'Percentage of total rent required as deposit.', 'thessnest' ),
			'default'  => 20,
			'min'      => 0,
			'max'      => 100,
			'step'     => 5,
		),
		array(
			'id'       => 'booking_approval',
			'type'     => 'button_set',
			'title'    => __( 'Booking Approval', 'thessnest' ),
			'subtitle' => __( 'Choose how new bookings are handled.', 'thessnest' ),
			'options'  => array(
				'manual' => __( 'Manual Approval', 'thessnest' ),
				'auto'   => __( 'Auto Approve', 'thessnest' ),
			),
			'default'  => 'manual',
		),
		array(
			'id'       => 'instant_booking',
			'type'     => 'switch',
			'title'    => __( 'Instant Booking', 'thessnest' ),
			'subtitle' => __( 'Allow tenants to book immediately without landlord approval.', 'thessnest' ),
			'default'  => false,
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 5: Price & Currency
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Price & Currency', 'thessnest' ),
	'id'     => 'pricing',
	'icon'   => 'el el-usd',
	'fields' => array(
		array(
			'id'       => 'currency_symbol',
			'type'     => 'text',
			'title'    => __( 'Currency Symbol', 'thessnest' ),
			'subtitle' => __( 'e.g. €, $, £', 'thessnest' ),
			'default'  => '€',
		),
		array(
			'id'       => 'currency_position',
			'type'     => 'button_set',
			'title'    => __( 'Currency Position', 'thessnest' ),
			'options'  => array(
				'before' => __( 'Before price (€100)', 'thessnest' ),
				'after'  => __( 'After price (100€)', 'thessnest' ),
			),
			'default'  => 'before',
		),
		array(
			'id'       => 'price_per',
			'type'     => 'select',
			'title'    => __( 'Price Display', 'thessnest' ),
			'subtitle' => __( 'How prices are labelled on listings.', 'thessnest' ),
			'options'  => array(
				'month' => __( '/ month', 'thessnest' ),
				'week'  => __( '/ week', 'thessnest' ),
				'night' => __( '/ night', 'thessnest' ),
			),
			'default'  => 'month',
		),
		array(
			'id'       => 'show_no_fees_badge',
			'type'     => 'switch',
			'title'    => __( '"No Platform Fees" Badge', 'thessnest' ),
			'subtitle' => __( 'Show the WYSIWYP badge on property cards.', 'thessnest' ),
			'default'  => true,
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 6: Styling
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Styling', 'thessnest' ),
	'id'     => 'styling',
	'icon'   => 'el el-brush',
	'fields' => array(
		array(
			'id'       => 'accent_color',
			'type'     => 'color',
			'title'    => __( 'Accent Color', 'thessnest' ),
			'subtitle' => __( 'Main accent colour used across the theme.', 'thessnest' ),
			'default'  => '#2563eb',
			'transparent' => false,
		),
		array(
			'id'       => 'dark_mode',
			'type'     => 'switch',
			'title'    => __( 'Dark Mode', 'thessnest' ),
			'subtitle' => __( 'Enable/disable the dark colour scheme on the frontend.', 'thessnest' ),
			'default'  => false,
		),
		array(
			'id'       => 'border_radius',
			'type'     => 'select',
			'title'    => __( 'Card Border Radius', 'thessnest' ),
			'subtitle' => __( 'Corner roundness of property cards and UI elements.', 'thessnest' ),
			'options'  => array(
				'none'   => __( 'None (Sharp corners)', 'thessnest' ),
				'small'  => __( 'Small (4px)', 'thessnest' ),
				'medium' => __( 'Medium (8px)', 'thessnest' ),
				'large'  => __( 'Large (12px)', 'thessnest' ),
			),
			'default'  => 'medium',
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 7: Footer
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Footer', 'thessnest' ),
	'id'     => 'footer',
	'icon'   => 'el el-website',
	'fields' => array(
		array(
			'id'       => 'footer_copyright',
			'type'     => 'text',
			'title'    => __( 'Copyright Text', 'thessnest' ),
			'subtitle' => __( 'Enter the copyright text for the footer.', 'thessnest' ),
			'default'  => '© ' . date( 'Y' ) . ' ThessNest. All rights reserved.',
		),
		array(
			'id'       => 'footer_social_media',
			'type'     => 'switch',
			'title'    => __( 'Social Media on Footer', 'thessnest' ),
			'subtitle' => __( 'Enable/Disable the social media icons in the footer.', 'thessnest' ),
			'default'  => true,
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 8: Contact & Social Media
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Contact', 'thessnest' ),
	'id'     => 'contact',
	'icon'   => 'el el-phone-alt',
	'fields' => array(
		array(
			'id'       => 'contact_phone',
			'type'     => 'text',
			'title'    => __( 'Phone Number', 'thessnest' ),
			'default'  => '+30 123 456 789',
		),
		array(
			'id'       => 'contact_email',
			'type'     => 'text',
			'title'    => __( 'Email Address', 'thessnest' ),
			'default'  => 'hello@thessnest.com',
			'validate' => 'email',
		),
		array(
			'id'       => 'contact_address',
			'type'     => 'textarea',
			'title'    => __( 'Address', 'thessnest' ),
			'default'  => 'Thessaloniki, Greece',
		),
		array(
			'id'       => 'social_instagram',
			'type'     => 'text',
			'title'    => __( 'Instagram URL', 'thessnest' ),
			'default'  => '',
		),
		array(
			'id'       => 'social_whatsapp',
			'type'     => 'text',
			'title'    => __( 'WhatsApp URL', 'thessnest' ),
			'subtitle' => __( 'e.g. https://wa.me/30123456789', 'thessnest' ),
			'default'  => '',
		),
		array(
			'id'       => 'social_facebook',
			'type'     => 'text',
			'title'    => __( 'Facebook URL', 'thessnest' ),
			'default'  => '',
		),
		array(
			'id'       => 'social_twitter',
			'type'     => 'text',
			'title'    => __( 'Twitter / X URL', 'thessnest' ),
			'default'  => '',
		),
		array(
			'id'       => 'social_linkedin',
			'type'     => 'text',
			'title'    => __( 'LinkedIn URL', 'thessnest' ),
			'default'  => '',
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 9: Chatbot / Live Chat
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Live Chat', 'thessnest' ),
	'id'     => 'livechat',
	'icon'   => 'el el-comment',
	'fields' => array(
		array(
			'id'       => 'chatbot_enabled',
			'type'     => 'switch',
			'title'    => __( 'Enable Live Chat Widget', 'thessnest' ),
			'subtitle' => __( 'Show the chatbot widget on all pages.', 'thessnest' ),
			'default'  => false,
		),
		array(
			'id'       => 'chatbot_embed',
			'type'     => 'ace_editor',
			'title'    => __( 'Chatbot Embed Code', 'thessnest' ),
			'subtitle' => __( 'Paste the embed code from Tidio, Tawk.to, or any chat widget.', 'thessnest' ),
			'mode'     => 'html',
			'theme'    => 'monokai',
			'default'  => '',
		),
	),
) );
