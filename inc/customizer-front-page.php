<?php
/**
 * ThessNest — Front Page Customizer Settings
 *
 * Full Customizer panel for managing the homepage content.
 * No hardcoded brand references — everything is configurable.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register front page settings.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function thessnest_front_page_customize_register( $wp_customize ) {

	// 1. Panel: Homepage Settings
	$wp_customize->add_panel( 'thessnest_homepage_panel', array(
		'priority'       => 10,
		'capability'     => 'edit_theme_options',
		'title'          => __( 'Homepage Settings', 'thessnest' ),
		'description'    => __( 'Manage the content, titles, images, and sections on the front page.', 'thessnest' ),
	) );

	// =========================================================
	// SECTION: HERO
	// =========================================================
	$wp_customize->add_section( 'thessnest_hero_section', array(
		'title'       => __( 'Hero Section', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 10,
	) );

	// Hero Background Image
	$wp_customize->add_setting( 'hero_bg_image', array(
		'default'           => get_theme_file_uri( 'assets/images/hero-bg-default.png' ),
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hero_bg_image', array(
		'label'    => __( 'Hero Background Image', 'thessnest' ),
		'section'  => 'thessnest_hero_section',
		'settings' => 'hero_bg_image',
	) ) );

	// Hero Title
	$wp_customize->add_setting( 'hero_title', array(
		'default'           => __( 'Find Your Perfect Home', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'hero_title', array(
		'label'       => __( 'Hero Title', 'thessnest' ),
		'section'     => 'thessnest_hero_section',
		'type'        => 'text',
	) );

	// Hero Subtitle
	$wp_customize->add_setting( 'hero_subtitle', array(
		'default'           => __( 'Browse verified listings with transparent pricing. No hidden fees, instant booking.', 'thessnest' ),
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( 'hero_subtitle', array(
		'label'       => __( 'Hero Subtitle', 'thessnest' ),
		'section'     => 'thessnest_hero_section',
		'type'        => 'textarea',
	) );


	// =========================================================
	// SECTION: TRUST BAR (Partner Logos)
	// =========================================================
	$wp_customize->add_section( 'thessnest_trustbar_section', array(
		'title'       => __( 'Trust Bar (Partner Logos)', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 15,
		'description' => __( 'Upload partner/certification logos. Leave empty to hide this section entirely.', 'thessnest' ),
	) );

	// Trust Bar Label
	$wp_customize->add_setting( 'trustbar_label', array(
		'default'           => __( 'Trusted by leading organizations', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'trustbar_label', array(
		'label'       => __( 'Section Label', 'thessnest' ),
		'section'     => 'thessnest_trustbar_section',
		'type'        => 'text',
	) );

	// 6 Logo Slots
	for ( $i = 1; $i <= 6; $i++ ) {
		$wp_customize->add_setting( 'trustbar_logo_' . $i, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'trustbar_logo_' . $i, array(
			'label'    => sprintf( __( 'Partner Logo %d', 'thessnest' ), $i ),
			'section'  => 'thessnest_trustbar_section',
		) ) );

		$wp_customize->add_setting( 'trustbar_logo_' . $i . '_alt', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'trustbar_logo_' . $i . '_alt', array(
			'label'       => sprintf( __( 'Logo %d Alt Text', 'thessnest' ), $i ),
			'section'     => 'thessnest_trustbar_section',
			'type'        => 'text',
		) );
	}


	// =========================================================
	// SECTION: FEATURED PROPERTIES
	// =========================================================
	$wp_customize->add_section( 'thessnest_featured_section', array(
		'title'       => __( 'Featured Properties', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 20,
	) );

	$wp_customize->add_setting( 'featured_title', array(
		'default'           => __( 'Featured Properties', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'featured_title', array(
		'label'       => __( 'Section Title', 'thessnest' ),
		'section'     => 'thessnest_featured_section',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'featured_subtitle', array(
		'default'           => __( 'Hand-picked properties with verified landlords, ready for move-in.', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'featured_subtitle', array(
		'label'       => __( 'Section Subtitle', 'thessnest' ),
		'section'     => 'thessnest_featured_section',
		'type'        => 'textarea',
	) );


	// =========================================================
	// SECTION: POPULAR DESTINATIONS
	// =========================================================
	$wp_customize->add_section( 'thessnest_destinations_section', array(
		'title'       => __( 'Popular Destinations', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 25,
	) );

	$wp_customize->add_setting( 'destinations_show', array(
		'default'           => true,
		'sanitize_callback' => 'thessnest_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'destinations_show', array(
		'label'       => __( 'Show Popular Destinations', 'thessnest' ),
		'section'     => 'thessnest_destinations_section',
		'type'        => 'checkbox',
	) );

	$wp_customize->add_setting( 'destinations_title', array(
		'default'           => __( 'Popular Destinations', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'destinations_title', array(
		'label'       => __( 'Section Title', 'thessnest' ),
		'section'     => 'thessnest_destinations_section',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'destinations_subtitle', array(
		'default'           => __( 'Explore the most sought-after neighborhoods and areas.', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'destinations_subtitle', array(
		'label'       => __( 'Section Subtitle', 'thessnest' ),
		'section'     => 'thessnest_destinations_section',
		'type'        => 'textarea',
	) );

	$wp_customize->add_setting( 'destinations_count', array(
		'default'           => 6,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'destinations_count', array(
		'label'       => __( 'Number of Destinations to Show', 'thessnest' ),
		'section'     => 'thessnest_destinations_section',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 2, 'max' => 12, 'step' => 1 ),
	) );


	// =========================================================
	// SECTION: STATS COUNTER
	// =========================================================
	$wp_customize->add_section( 'thessnest_stats_section', array(
		'title'       => __( 'Stats Counter Bar', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 30,
	) );

	$wp_customize->add_setting( 'stats_show', array(
		'default'           => true,
		'sanitize_callback' => 'thessnest_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'stats_show', array(
		'label'       => __( 'Show Stats Counter', 'thessnest' ),
		'section'     => 'thessnest_stats_section',
		'type'        => 'checkbox',
	) );

	$stat_defaults = array(
		1 => array( 'number' => '500',  'suffix' => '+', 'label' => __( 'Properties Listed', 'thessnest' ) ),
		2 => array( 'number' => '1200', 'suffix' => '+', 'label' => __( 'Happy Tenants', 'thessnest' ) ),
		3 => array( 'number' => '50',   'suffix' => '+', 'label' => __( 'Neighborhoods', 'thessnest' ) ),
		4 => array( 'number' => '98',   'suffix' => '%', 'label' => __( 'Satisfaction Rate', 'thessnest' ) ),
	);

	for ( $i = 1; $i <= 4; $i++ ) {
		$wp_customize->add_setting( 'stat_' . $i . '_number', array(
			'default'           => $stat_defaults[ $i ]['number'],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'stat_' . $i . '_number', array(
			'label'       => sprintf( __( 'Stat %d Number', 'thessnest' ), $i ),
			'section'     => 'thessnest_stats_section',
			'type'        => 'text',
		) );

		$wp_customize->add_setting( 'stat_' . $i . '_suffix', array(
			'default'           => $stat_defaults[ $i ]['suffix'],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'stat_' . $i . '_suffix', array(
			'label'       => sprintf( __( 'Stat %d Suffix (+, %%, etc.)', 'thessnest' ), $i ),
			'section'     => 'thessnest_stats_section',
			'type'        => 'text',
		) );

		$wp_customize->add_setting( 'stat_' . $i . '_label', array(
			'default'           => $stat_defaults[ $i ]['label'],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'stat_' . $i . '_label', array(
			'label'       => sprintf( __( 'Stat %d Label', 'thessnest' ), $i ),
			'section'     => 'thessnest_stats_section',
			'type'        => 'text',
		) );
	}


	// =========================================================
	// SECTION: HOW IT WORKS
	// =========================================================
	$wp_customize->add_section( 'thessnest_hiw_section', array(
		'title'       => __( 'How It Works', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 35,
	) );

	$wp_customize->add_setting( 'hiw_title', array(
		'default'           => __( 'How It Works', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'hiw_title', array(
		'label'       => __( 'Main Title', 'thessnest' ),
		'section'     => 'thessnest_hiw_section',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'hiw_subtitle', array(
		'default'           => __( 'Secure your home in three simple steps.', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'hiw_subtitle', array(
		'label'       => __( 'Main Subtitle', 'thessnest' ),
		'section'     => 'thessnest_hiw_section',
		'type'        => 'textarea',
	) );

	$default_titles = array(
		1 => __( 'Search & Discover', 'thessnest' ),
		2 => __( 'Book Securely', 'thessnest' ),
		3 => __( 'Move In', 'thessnest' ),
	);
	$default_descs = array(
		1 => __( 'Browse verified listings filtered by location, budget, amenities, and more.', 'thessnest' ),
		2 => __( 'Reserve your place online with transparent pricing. No hidden fees, no surprises.', 'thessnest' ),
		3 => __( 'Arrive at your destination and settle into your new home. Welcome!', 'thessnest' ),
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		$wp_customize->add_setting( 'hiw_step_' . $i . '_title', array(
			'default'           => $default_titles[ $i ],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'hiw_step_' . $i . '_title', array(
			'label'       => sprintf( __( 'Step %d Title', 'thessnest' ), $i ),
			'section'     => 'thessnest_hiw_section',
			'type'        => 'text',
		) );

		$wp_customize->add_setting( 'hiw_step_' . $i . '_desc', array(
			'default'           => $default_descs[ $i ],
			'sanitize_callback' => 'wp_kses_post',
		) );
		$wp_customize->add_control( 'hiw_step_' . $i . '_desc', array(
			'label'       => sprintf( __( 'Step %d Description', 'thessnest' ), $i ),
			'section'     => 'thessnest_hiw_section',
			'type'        => 'textarea',
		) );
	}


	// =========================================================
	// SECTION: TESTIMONIALS
	// =========================================================
	$wp_customize->add_section( 'thessnest_testimonials_section', array(
		'title'       => __( 'Testimonials', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 40,
	) );

	$wp_customize->add_setting( 'testimonials_show', array(
		'default'           => true,
		'sanitize_callback' => 'thessnest_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'testimonials_show', array(
		'label'       => __( 'Show Testimonials', 'thessnest' ),
		'section'     => 'thessnest_testimonials_section',
		'type'        => 'checkbox',
	) );

	$wp_customize->add_setting( 'testimonials_title', array(
		'default'           => __( 'What Our Users Say', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'testimonials_title', array(
		'label'       => __( 'Section Title', 'thessnest' ),
		'section'     => 'thessnest_testimonials_section',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'testimonials_subtitle', array(
		'default'           => __( 'Real experiences from real people who found their home through our platform.', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'testimonials_subtitle', array(
		'label'       => __( 'Section Subtitle', 'thessnest' ),
		'section'     => 'thessnest_testimonials_section',
		'type'        => 'textarea',
	) );

	for ( $i = 1; $i <= 3; $i++ ) {
		$wp_customize->add_setting( 'testimonial_' . $i . '_text', array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
		) );
		$wp_customize->add_control( 'testimonial_' . $i . '_text', array(
			'label'       => sprintf( __( 'Testimonial %d Text', 'thessnest' ), $i ),
			'section'     => 'thessnest_testimonials_section',
			'type'        => 'textarea',
		) );

		$wp_customize->add_setting( 'testimonial_' . $i . '_name', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'testimonial_' . $i . '_name', array(
			'label'       => sprintf( __( 'Testimonial %d Name', 'thessnest' ), $i ),
			'section'     => 'thessnest_testimonials_section',
			'type'        => 'text',
		) );

		$wp_customize->add_setting( 'testimonial_' . $i . '_role', array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'testimonial_' . $i . '_role', array(
			'label'       => sprintf( __( 'Testimonial %d Role / Title', 'thessnest' ), $i ),
			'section'     => 'thessnest_testimonials_section',
			'type'        => 'text',
		) );

		$wp_customize->add_setting( 'testimonial_' . $i . '_image', array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'testimonial_' . $i . '_image', array(
			'label'    => sprintf( __( 'Testimonial %d Photo', 'thessnest' ), $i ),
			'section'  => 'thessnest_testimonials_section',
		) ) );

		$wp_customize->add_setting( 'testimonial_' . $i . '_rating', array(
			'default'           => 5,
			'sanitize_callback' => 'absint',
		) );
		$wp_customize->add_control( 'testimonial_' . $i . '_rating', array(
			'label'       => sprintf( __( 'Testimonial %d Rating (1-5)', 'thessnest' ), $i ),
			'section'     => 'thessnest_testimonials_section',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 1, 'max' => 5, 'step' => 1 ),
		) );
	}


	// =========================================================
	// SECTION: WHY CHOOSE US
	// =========================================================
	$wp_customize->add_section( 'thessnest_whychoose_section', array(
		'title'       => __( 'Why Choose Us', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 45,
	) );

	$wp_customize->add_setting( 'whychoose_title', array(
		'default'           => __( 'Why Choose Us', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'whychoose_title', array(
		'label'       => __( 'Section Title', 'thessnest' ),
		'section'     => 'thessnest_whychoose_section',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'whychoose_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'whychoose_image', array(
		'label'    => __( 'Section Image', 'thessnest' ),
		'section'  => 'thessnest_whychoose_section',
	) ) );

	$feature_defaults = array(
		1 => array(
			'title' => __( 'Verified Landlords', 'thessnest' ),
			'desc'  => __( 'Every landlord undergoes identity and property ownership verification before listing.', 'thessnest' ),
		),
		2 => array(
			'title' => __( 'No Hidden Fees', 'thessnest' ),
			'desc'  => __( 'Transparent pricing — rent, utilities, and deposit displayed upfront.', 'thessnest' ),
		),
		3 => array(
			'title' => __( 'Global Community', 'thessnest' ),
			'desc'  => __( 'Multilingual support, instant booking, and map navigation for international tenants.', 'thessnest' ),
		),
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		$wp_customize->add_setting( 'whychoose_feature_' . $i . '_title', array(
			'default'           => $feature_defaults[ $i ]['title'],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'whychoose_feature_' . $i . '_title', array(
			'label'       => sprintf( __( 'Feature %d Title', 'thessnest' ), $i ),
			'section'     => 'thessnest_whychoose_section',
			'type'        => 'text',
		) );

		$wp_customize->add_setting( 'whychoose_feature_' . $i . '_desc', array(
			'default'           => $feature_defaults[ $i ]['desc'],
			'sanitize_callback' => 'wp_kses_post',
		) );
		$wp_customize->add_control( 'whychoose_feature_' . $i . '_desc', array(
			'label'       => sprintf( __( 'Feature %d Description', 'thessnest' ), $i ),
			'section'     => 'thessnest_whychoose_section',
			'type'        => 'textarea',
		) );
	}


	// =========================================================
	// SECTION: CTA BANNER
	// =========================================================
	$wp_customize->add_section( 'thessnest_cta_section', array(
		'title'       => __( 'CTA Banner', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 50,
	) );

	$wp_customize->add_setting( 'cta_title', array(
		'default'           => __( 'Own a Property?', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'cta_title', array(
		'label'       => __( 'CTA Title', 'thessnest' ),
		'section'     => 'thessnest_cta_section',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'cta_subtitle', array(
		'default'           => __( 'List your space and reach thousands of tenants looking for their next home.', 'thessnest' ),
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( 'cta_subtitle', array(
		'label'       => __( 'CTA Subtitle', 'thessnest' ),
		'section'     => 'thessnest_cta_section',
		'type'        => 'textarea',
	) );

	$wp_customize->add_setting( 'cta_btn_text', array(
		'default'           => __( 'List Your Property', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'cta_btn_text', array(
		'label'       => __( 'Button Text', 'thessnest' ),
		'section'     => 'thessnest_cta_section',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'cta_btn_link', array(
		'default'           => '#',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'cta_btn_link', array(
		'label'       => __( 'Button Link', 'thessnest' ),
		'section'     => 'thessnest_cta_section',
		'type'        => 'url',
	) );

}
add_action( 'customize_register', 'thessnest_front_page_customize_register' );

/**
 * Sanitize checkbox.
 */
if ( ! function_exists( 'thessnest_sanitize_checkbox' ) ) {
	function thessnest_sanitize_checkbox( $checked ) {
		return ( ( isset( $checked ) && true == $checked ) ? true : false );
	}
}
