<?php
/**
 * ThessNest — Front Page Customizer Settings
 *
 * Adds a new Customizer Panel with sections for managing
 * the homepage's dynamic content without ACF.
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
		'priority'       => 10, // Right at the top
		'capability'     => 'edit_theme_options',
		'title'          => __( 'Homepage Settings', 'thessnest' ),
		'description'    => __( 'Manage the static content, titles, and images on the front page.', 'thessnest' ),
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
		'default'           => __( 'Find Your Home Anywhere', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'hero_title', array(
		'label'       => __( 'Hero Title', 'thessnest' ),
		'section'     => 'thessnest_hero_section',
		'type'        => 'text',
	) );

	// Hero Subtitle
	$wp_customize->add_setting( 'hero_subtitle', array(
		'default'           => __( 'Find student accommodation no agency fee and digital nomad apartments. Verified landlords, instant booking.', 'thessnest' ),
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( 'hero_subtitle', array(
		'label'       => __( 'Hero Subtitle', 'thessnest' ),
		'section'     => 'thessnest_hero_section',
		'type'        => 'textarea',
	) );


	// =========================================================
	// SECTION: FEATURED PROPERTIES
	// =========================================================
	$wp_customize->add_section( 'thessnest_featured_section', array(
		'title'       => __( 'Featured Properties', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 20,
	) );

	// Featured Title
	$wp_customize->add_setting( 'featured_title', array(
		'default'           => __( 'Featured Properties', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'featured_title', array(
		'label'       => __( 'Section Title', 'thessnest' ),
		'section'     => 'thessnest_featured_section',
		'type'        => 'text',
	) );

	// Featured Subtitle
	$wp_customize->add_setting( 'featured_subtitle', array(
		'default'           => __( 'Verified landlord student apartments & digital nomad flats ready for move-in.', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'featured_subtitle', array(
		'label'       => __( 'Section Subtitle', 'thessnest' ),
		'section'     => 'thessnest_featured_section',
		'type'        => 'textarea',
	) );


	// =========================================================
	// SECTION: HOW IT WORKS
	// =========================================================
	$wp_customize->add_section( 'thessnest_hiw_section', array(
		'title'       => __( 'How It Works', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 30,
	) );

	// HIW Title
	$wp_customize->add_setting( 'hiw_title', array(
		'default'           => __( 'How It Works', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'hiw_title', array(
		'label'       => __( 'Main Title', 'thessnest' ),
		'section'     => 'thessnest_hiw_section',
		'type'        => 'text',
	) );

	// HIW Subtitle
	$wp_customize->add_setting( 'hiw_subtitle', array(
		'default'           => __( 'Secure your home in three simple steps.', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'hiw_subtitle', array(
		'label'       => __( 'Main Subtitle', 'thessnest' ),
		'section'     => 'thessnest_hiw_section',
		'type'        => 'textarea',
	) );

	// Steps (1, 2, 3) Texts
	for ( $i = 1; $i <= 3; $i++ ) {
		// Title
		$default_titles = array(
			1 => __( 'Choose Safe Rooms', 'thessnest' ),
			2 => __( 'Book', 'thessnest' ),
			3 => __( 'Move In', 'thessnest' )
		);
		$wp_customize->add_setting( 'hiw_step_' . $i . '_title', array(
			'default'           => $default_titles[$i],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( 'hiw_step_' . $i . '_title', array(
			'label'       => sprintf( __( 'Step %d Title', 'thessnest' ), $i ),
			'section'     => 'thessnest_hiw_section',
			'type'        => 'text',
		) );

		// Desc
		$default_descs = array(
			1 => __( 'Browse verified listings near Aristotle University or grocery stores. Filter by high-speed Wi-Fi, budget, and amenities.', 'thessnest' ),
			2 => __( 'Reserve your place online with transparent pricing. No hidden fees, no surprises.', 'thessnest' ),
			3 => __( 'Arrive in your destination and settle into your new home. Welcome!', 'thessnest' )
		);
		$wp_customize->add_setting( 'hiw_step_' . $i . '_desc', array(
			'default'           => $default_descs[$i],
			'sanitize_callback' => 'wp_kses_post',
		) );
		$wp_customize->add_control( 'hiw_step_' . $i . '_desc', array(
			'label'       => sprintf( __( 'Step %d Description', 'thessnest' ), $i ),
			'section'     => 'thessnest_hiw_section',
			'type'        => 'textarea',
		) );
	}


	// =========================================================
	// SECTION: CTA BANNER
	// =========================================================
	$wp_customize->add_section( 'thessnest_cta_section', array(
		'title'       => __( 'CTA Banner', 'thessnest' ),
		'panel'       => 'thessnest_homepage_panel',
		'priority'    => 40,
	) );

	// CTA Title
	$wp_customize->add_setting( 'cta_title', array(
		'default'           => __( 'Own a Property Here?', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'cta_title', array(
		'label'       => __( 'CTA Title', 'thessnest' ),
		'section'     => 'thessnest_cta_section',
		'type'        => 'text',
	) );

	// CTA Subtitle
	$wp_customize->add_setting( 'cta_subtitle', array(
		'default'           => __( 'List your space and reach thousands of Erasmus students and digital nomads looking for their next home.', 'thessnest' ),
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( 'cta_subtitle', array(
		'label'       => __( 'CTA Subtitle', 'thessnest' ),
		'section'     => 'thessnest_cta_section',
		'type'        => 'textarea',
	) );

	// CTA Button Text
	$wp_customize->add_setting( 'cta_btn_text', array(
		'default'           => __( 'List Your Property', 'thessnest' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'cta_btn_text', array(
		'label'       => __( 'Button Text', 'thessnest' ),
		'section'     => 'thessnest_cta_section',
		'type'        => 'text',
	) );

	// CTA Button Link
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
