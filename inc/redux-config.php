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


// ═══════════════════════════════════════════════════
//  SECTION 10: Labels
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Labels', 'thessnest' ),
	'id'     => 'labels',
	'icon'   => 'el el-tag',
	'fields' => array(
		array(
			'id'       => 'label_rent',
			'type'     => 'text',
			'title'    => __( 'Rent Label', 'thessnest' ),
			'subtitle' => __( 'Text shown next to the price on cards.', 'thessnest' ),
			'default'  => 'Monthly Rent',
		),
		array(
			'id'       => 'label_utilities',
			'type'     => 'text',
			'title'    => __( 'Utilities Label', 'thessnest' ),
			'default'  => 'Utilities',
		),
		array(
			'id'       => 'label_deposit',
			'type'     => 'text',
			'title'    => __( 'Deposit Label', 'thessnest' ),
			'default'  => 'Security Deposit',
		),
		array(
			'id'       => 'label_book_now',
			'type'     => 'text',
			'title'    => __( 'Book Now Button', 'thessnest' ),
			'default'  => 'Book Now',
		),
		array(
			'id'       => 'label_contact_landlord',
			'type'     => 'text',
			'title'    => __( 'Contact Landlord Button', 'thessnest' ),
			'default'  => 'Contact Landlord',
		),
		array(
			'id'       => 'label_verified',
			'type'     => 'text',
			'title'    => __( 'Verified Badge', 'thessnest' ),
			'default'  => 'Verified',
		),
		array(
			'id'       => 'label_available',
			'type'     => 'text',
			'title'    => __( 'Available Status', 'thessnest' ),
			'default'  => 'Available Now',
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 11: Login & Register
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Login & Register', 'thessnest' ),
	'id'     => 'login_register',
	'icon'   => 'el el-lock',
	'fields' => array(
		array(
			'id'       => 'enable_registration',
			'type'     => 'switch',
			'title'    => __( 'Allow Registration', 'thessnest' ),
			'subtitle' => __( 'Enable/disable user registration on the frontend.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'default_user_role',
			'type'     => 'button_set',
			'title'    => __( 'Default Registration Role', 'thessnest' ),
			'options'  => array(
				'tenant'   => __( 'Tenant', 'thessnest' ),
				'landlord' => __( 'Landlord', 'thessnest' ),
			),
			'default'  => 'tenant',
		),
		array(
			'id'       => 'login_redirect',
			'type'     => 'text',
			'title'    => __( 'After Login Redirect URL', 'thessnest' ),
			'subtitle' => __( 'Where users go after logging in. Leave empty for dashboard.', 'thessnest' ),
			'default'  => '/dashboard/',
		),
		array(
			'id'       => 'require_terms',
			'type'     => 'switch',
			'title'    => __( 'Require Terms & Conditions', 'thessnest' ),
			'subtitle' => __( 'Users must accept T&C before registering.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'terms_page',
			'type'     => 'select',
			'title'    => __( 'Terms & Conditions Page', 'thessnest' ),
			'data'     => 'pages',
			'default'  => '',
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 12: Top Bar
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Top Bar', 'thessnest' ),
	'id'     => 'topbar',
	'icon'   => 'el el-minus',
	'fields' => array(
		array(
			'id'       => 'topbar_enabled',
			'type'     => 'switch',
			'title'    => __( 'Show Top Bar', 'thessnest' ),
			'subtitle' => __( 'Thin notification bar above the header.', 'thessnest' ),
			'default'  => false,
		),
		array(
			'id'       => 'topbar_text',
			'type'     => 'text',
			'title'    => __( 'Top Bar Text', 'thessnest' ),
			'default'  => '🎓 Early bird discount: 10% off for bookings before September!',
		),
		array(
			'id'       => 'topbar_link',
			'type'     => 'text',
			'title'    => __( 'Top Bar Link URL', 'thessnest' ),
			'subtitle' => __( 'Optional. Makes the bar clickable.', 'thessnest' ),
			'default'  => '',
		),
		array(
			'id'       => 'topbar_bg_color',
			'type'     => 'color',
			'title'    => __( 'Top Bar Background', 'thessnest' ),
			'default'  => '#1B2A4A',
			'transparent' => false,
		),
		array(
			'id'       => 'topbar_text_color',
			'type'     => 'color',
			'title'    => __( 'Top Bar Text Color', 'thessnest' ),
			'default'  => '#ffffff',
			'transparent' => false,
		),
		array(
			'id'       => 'topbar_dismissible',
			'type'     => 'switch',
			'title'    => __( 'Dismissible', 'thessnest' ),
			'subtitle' => __( 'Allow users to close the top bar.', 'thessnest' ),
			'default'  => true,
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 13: Typography
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Typography', 'thessnest' ),
	'id'     => 'typography',
	'icon'   => 'el el-font',
	'fields' => array(
		array(
			'id'       => 'body_font',
			'type'     => 'typography',
			'title'    => __( 'Body Font', 'thessnest' ),
			'subtitle' => __( 'Font for paragraphs and general text.', 'thessnest' ),
			'google'   => true,
			'default'  => array(
				'font-family' => 'Inter',
				'font-weight' => '400',
				'font-size'   => '16px',
				'color'       => '#374151',
			),
			'output'   => array( 'body, p, span, li, td' ),
		),
		array(
			'id'       => 'heading_font',
			'type'     => 'typography',
			'title'    => __( 'Heading Font', 'thessnest' ),
			'subtitle' => __( 'Font for all headings (h1–h6).', 'thessnest' ),
			'google'   => true,
			'default'  => array(
				'font-family' => 'Inter',
				'font-weight' => '700',
				'color'       => '#1B2A4A',
			),
			'output'   => array( 'h1, h2, h3, h4, h5, h6' ),
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 14: Add New Listing
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Add New Listing', 'thessnest' ),
	'id'     => 'add_listing',
	'icon'   => 'el el-plus-sign',
	'fields' => array(
		array(
			'id'       => 'max_images',
			'type'     => 'spinner',
			'title'    => __( 'Max Images per Listing', 'thessnest' ),
			'default'  => 10,
			'min'      => 1,
			'max'      => 30,
			'step'     => 1,
		),
		array(
			'id'       => 'listing_approval',
			'type'     => 'button_set',
			'title'    => __( 'New Listing Approval', 'thessnest' ),
			'options'  => array(
				'auto'   => __( 'Auto Publish', 'thessnest' ),
				'manual' => __( 'Require Admin Approval', 'thessnest' ),
			),
			'default'  => 'manual',
		),
		array(
			'id'       => 'require_kyc',
			'type'     => 'switch',
			'title'    => __( 'Require KYC Verification', 'thessnest' ),
			'subtitle' => __( 'Landlords must verify identity before listing.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'show_map_on_form',
			'type'     => 'switch',
			'title'    => __( 'Show Map Picker on Form', 'thessnest' ),
			'subtitle' => __( 'Enable the interactive map for location selection.', 'thessnest' ),
			'default'  => true,
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 15: Listing Detail Page
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Listing Detail Page', 'thessnest' ),
	'id'     => 'listing_detail',
	'icon'   => 'el el-file',
	'fields' => array(
		array(
			'id'       => 'show_gallery_lightbox',
			'type'     => 'switch',
			'title'    => __( 'Gallery Lightbox', 'thessnest' ),
			'subtitle' => __( 'Open images in fullscreen lightbox on click.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'show_share_buttons',
			'type'     => 'switch',
			'title'    => __( 'Share Buttons', 'thessnest' ),
			'subtitle' => __( 'Show social sharing buttons on listing pages.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'show_similar_listings',
			'type'     => 'switch',
			'title'    => __( 'Similar Listings', 'thessnest' ),
			'subtitle' => __( 'Show related properties below the listing.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'similar_listings_count',
			'type'     => 'spinner',
			'title'    => __( 'Number of Similar Listings', 'thessnest' ),
			'default'  => 3,
			'min'      => 1,
			'max'      => 8,
			'step'     => 1,
		),
		array(
			'id'       => 'show_map_on_detail',
			'type'     => 'switch',
			'title'    => __( 'Show Map on Detail Page', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'show_reviews',
			'type'     => 'switch',
			'title'    => __( 'Show Reviews Section', 'thessnest' ),
			'default'  => true,
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 16: Listings (Archive)
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Listings', 'thessnest' ),
	'id'     => 'listings',
	'icon'   => 'el el-th-large',
	'fields' => array(
		array(
			'id'       => 'listings_per_page',
			'type'     => 'spinner',
			'title'    => __( 'Listings Per Page', 'thessnest' ),
			'default'  => 12,
			'min'      => 4,
			'max'      => 48,
			'step'     => 4,
		),
		array(
			'id'       => 'listings_layout',
			'type'     => 'button_set',
			'title'    => __( 'Default Layout', 'thessnest' ),
			'options'  => array(
				'grid' => __( 'Grid', 'thessnest' ),
				'list' => __( 'List', 'thessnest' ),
			),
			'default'  => 'grid',
		),
		array(
			'id'       => 'listings_columns',
			'type'     => 'button_set',
			'title'    => __( 'Grid Columns', 'thessnest' ),
			'options'  => array(
				'2' => __( '2 Columns', 'thessnest' ),
				'3' => __( '3 Columns', 'thessnest' ),
				'4' => __( '4 Columns', 'thessnest' ),
			),
			'default'  => '3',
		),
		array(
			'id'       => 'listings_default_sort',
			'type'     => 'select',
			'title'    => __( 'Default Sort Order', 'thessnest' ),
			'options'  => array(
				'newest'     => __( 'Newest First', 'thessnest' ),
				'price_low'  => __( 'Price: Low to High', 'thessnest' ),
				'price_high' => __( 'Price: High to Low', 'thessnest' ),
			),
			'default'  => 'newest',
		),
		array(
			'id'       => 'show_sidebar_filters',
			'type'     => 'switch',
			'title'    => __( 'Show Sidebar Filters', 'thessnest' ),
			'default'  => true,
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 17: Search Listings
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Search Listings', 'thessnest' ),
	'id'     => 'search_listings',
	'icon'   => 'el el-search',
	'fields' => array(
		array(
			'id'       => 'search_show_neighborhood',
			'type'     => 'switch',
			'title'    => __( 'Neighborhood Filter', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'search_show_price_range',
			'type'     => 'switch',
			'title'    => __( 'Price Range Slider', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'search_price_min',
			'type'     => 'spinner',
			'title'    => __( 'Min Price Filter', 'thessnest' ),
			'default'  => 100,
			'min'      => 0,
			'max'      => 5000,
			'step'     => 50,
		),
		array(
			'id'       => 'search_price_max',
			'type'     => 'spinner',
			'title'    => __( 'Max Price Filter', 'thessnest' ),
			'default'  => 2000,
			'min'      => 100,
			'max'      => 10000,
			'step'     => 100,
		),
		array(
			'id'       => 'search_show_amenities',
			'type'     => 'switch',
			'title'    => __( 'Amenities Filter', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'search_show_target_group',
			'type'     => 'switch',
			'title'    => __( 'Target Group Filter', 'thessnest' ),
			'default'  => true,
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 18: Map Settings
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Map Settings', 'thessnest' ),
	'id'     => 'map_settings',
	'icon'   => 'el el-map-marker',
	'fields' => array(
		array(
			'id'       => 'map_default_lat',
			'type'     => 'text',
			'title'    => __( 'Default Latitude', 'thessnest' ),
			'default'  => '40.6401',
		),
		array(
			'id'       => 'map_default_lng',
			'type'     => 'text',
			'title'    => __( 'Default Longitude', 'thessnest' ),
			'default'  => '22.9444',
		),
		array(
			'id'       => 'map_default_zoom',
			'type'     => 'spinner',
			'title'    => __( 'Default Zoom Level', 'thessnest' ),
			'default'  => 13,
			'min'      => 1,
			'max'      => 20,
			'step'     => 1,
		),
		array(
			'id'       => 'map_style',
			'type'     => 'select',
			'title'    => __( 'Map Style', 'thessnest' ),
			'options'  => array(
				'default' => __( 'Default', 'thessnest' ),
				'silver'  => __( 'Silver', 'thessnest' ),
				'dark'    => __( 'Dark', 'thessnest' ),
				'retro'   => __( 'Retro', 'thessnest' ),
			),
			'default'  => 'default',
		),
		array(
			'id'       => 'map_marker_icon',
			'type'     => 'media',
			'title'    => __( 'Custom Map Marker', 'thessnest' ),
			'subtitle' => __( 'Upload a custom map pin icon (PNG, 32x32).', 'thessnest' ),
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 19: Google reCaptcha
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Google reCaptcha', 'thessnest' ),
	'id'     => 'recaptcha',
	'icon'   => 'el el-shield',
	'fields' => array(
		array(
			'id'       => 'recaptcha_enabled',
			'type'     => 'switch',
			'title'    => __( 'Enable reCaptcha', 'thessnest' ),
			'subtitle' => __( 'Protect login, registration, and contact forms.', 'thessnest' ),
			'default'  => false,
		),
		array(
			'id'       => 'recaptcha_site_key',
			'type'     => 'text',
			'title'    => __( 'Site Key', 'thessnest' ),
			'subtitle' => __( 'Get keys at google.com/recaptcha', 'thessnest' ),
			'default'  => '',
		),
		array(
			'id'       => 'recaptcha_secret_key',
			'type'     => 'text',
			'title'    => __( 'Secret Key', 'thessnest' ),
			'default'  => '',
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 20: Email Management
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Email Management', 'thessnest' ),
	'id'     => 'emails',
	'icon'   => 'el el-envelope',
	'fields' => array(
		array(
			'id'       => 'email_from_name',
			'type'     => 'text',
			'title'    => __( 'From Name', 'thessnest' ),
			'subtitle' => __( 'Sender name for all outgoing emails.', 'thessnest' ),
			'default'  => 'ThessNest',
		),
		array(
			'id'       => 'email_from_address',
			'type'     => 'text',
			'title'    => __( 'From Email', 'thessnest' ),
			'default'  => 'noreply@thessnest.com',
			'validate' => 'email',
		),
		array(
			'id'       => 'email_booking_notify',
			'type'     => 'switch',
			'title'    => __( 'Booking Notification', 'thessnest' ),
			'subtitle' => __( 'Send email to landlord on new booking.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'email_message_notify',
			'type'     => 'switch',
			'title'    => __( 'Message Notification', 'thessnest' ),
			'subtitle' => __( 'Send email when a new message is received.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'email_admin_new_listing',
			'type'     => 'switch',
			'title'    => __( 'New Listing Alert', 'thessnest' ),
			'subtitle' => __( 'Notify admin when a new listing is submitted.', 'thessnest' ),
			'default'  => true,
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 21: Page 404
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Page 404', 'thessnest' ),
	'id'     => 'page_404',
	'icon'   => 'el el-error-alt',
	'fields' => array(
		array(
			'id'       => '404_title',
			'type'     => 'text',
			'title'    => __( '404 Page Title', 'thessnest' ),
			'default'  => 'Page Not Found',
		),
		array(
			'id'       => '404_message',
			'type'     => 'textarea',
			'title'    => __( '404 Message', 'thessnest' ),
			'default'  => 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.',
		),
		array(
			'id'       => '404_image',
			'type'     => 'media',
			'title'    => __( '404 Image', 'thessnest' ),
			'subtitle' => __( 'Custom illustration for the 404 page.', 'thessnest' ),
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 22: Optimizations
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Optimizations', 'thessnest' ),
	'id'     => 'optimizations',
	'icon'   => 'el el-dashboard',
	'fields' => array(
		array(
			'id'       => 'lazy_load_images',
			'type'     => 'switch',
			'title'    => __( 'Lazy Load Images', 'thessnest' ),
			'subtitle' => __( 'Defer loading of off-screen images.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'minify_css',
			'type'     => 'switch',
			'title'    => __( 'Minify CSS', 'thessnest' ),
			'subtitle' => __( 'Remove whitespace from CSS output.', 'thessnest' ),
			'default'  => false,
		),
		array(
			'id'       => 'disable_emojis',
			'type'     => 'switch',
			'title'    => __( 'Disable WP Emoji Scripts', 'thessnest' ),
			'subtitle' => __( 'Remove the emoji scripts for faster loading.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'disable_embeds',
			'type'     => 'switch',
			'title'    => __( 'Disable oEmbed', 'thessnest' ),
			'subtitle' => __( 'Remove the embed scripts if you do not use embeds.', 'thessnest' ),
			'default'  => false,
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 23: Custom Code
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'Custom Code', 'thessnest' ),
	'id'     => 'custom_code',
	'icon'   => 'el el-css',
	'fields' => array(
		array(
			'id'       => 'custom_css',
			'type'     => 'ace_editor',
			'title'    => __( 'Custom CSS', 'thessnest' ),
			'subtitle' => __( 'Add your own CSS rules. No &lt;style&gt; tags needed.', 'thessnest' ),
			'mode'     => 'css',
			'theme'    => 'monokai',
			'default'  => '',
		),
		array(
			'id'       => 'custom_js_header',
			'type'     => 'ace_editor',
			'title'    => __( 'Header JS', 'thessnest' ),
			'subtitle' => __( 'JavaScript injected into &lt;head&gt;. Include &lt;script&gt; tags.', 'thessnest' ),
			'mode'     => 'javascript',
			'theme'    => 'monokai',
			'default'  => '',
		),
		array(
			'id'       => 'custom_js_footer',
			'type'     => 'ace_editor',
			'title'    => __( 'Footer JS', 'thessnest' ),
			'subtitle' => __( 'JavaScript injected before &lt;/body&gt;. Include &lt;script&gt; tags.', 'thessnest' ),
			'mode'     => 'javascript',
			'theme'    => 'monokai',
			'default'  => '',
		),
	),
) );


// ═══════════════════════════════════════════════════
//  SECTION 24: GDPR Agreement
// ═══════════════════════════════════════════════════

Redux::set_section( $opt_name, array(
	'title'  => __( 'GDPR Agreement', 'thessnest' ),
	'id'     => 'gdpr',
	'icon'   => 'el el-eye-open',
	'fields' => array(
		array(
			'id'       => 'gdpr_enabled',
			'type'     => 'switch',
			'title'    => __( 'Cookie Consent Banner', 'thessnest' ),
			'subtitle' => __( 'Show a GDPR cookie consent popup for EU visitors.', 'thessnest' ),
			'default'  => true,
		),
		array(
			'id'       => 'gdpr_message',
			'type'     => 'textarea',
			'title'    => __( 'Consent Message', 'thessnest' ),
			'default'  => 'We use cookies to improve your experience. By continuing to browse, you agree to our use of cookies.',
		),
		array(
			'id'       => 'gdpr_button_text',
			'type'     => 'text',
			'title'    => __( 'Accept Button Text', 'thessnest' ),
			'default'  => 'Accept',
		),
		array(
			'id'       => 'gdpr_privacy_page',
			'type'     => 'select',
			'title'    => __( 'Privacy Policy Page', 'thessnest' ),
			'subtitle' => __( 'Link to your Privacy Policy.', 'thessnest' ),
			'data'     => 'pages',
			'default'  => '',
		),
	),
) );

