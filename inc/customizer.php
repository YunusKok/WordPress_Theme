<?php
/**
 * ThessNest Theme Customizer
 * 
 * Bu dosya, WordPress'in kendi yerleşik "Özelleştir" (Customizer) panelini
 * kullanarak temaya özel basit bir ayar sayfası ekler.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register fields for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function thessnest_customize_register( $wp_customize ) {

	// Yeni bir Ayar Sekmesi oluşturalım: "ThessNest Ayarları"
	$wp_customize->add_section( 'thessnest_theme_options', array(
		'title'       => __( 'ThessNest Ayarları', 'thessnest' ),
		'description' => __( 'Temaya özel temel ayarlar (İletişim, Sosyal Medya, vb.).', 'thessnest' ),
		'priority'    => 130, // "Ek CSS" sekmesinden hemen önce gösterir
	) );

	// 1. İletişim: Telefon Numarası
	$wp_customize->add_setting( 'thessnest_contact_phone', array(
		'default'           => '+30 123 456 789',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'thessnest_contact_phone', array(
		'label'       => __( 'İletişim Telefon Numarası', 'thessnest' ),
		'section'     => 'thessnest_theme_options',
		'type'        => 'text',
	) );

	// 2. İletişim: E-Posta
	$wp_customize->add_setting( 'thessnest_contact_email', array(
		'default'           => 'hello@thessnest.com',
		'sanitize_callback' => 'sanitize_email',
	) );
	$wp_customize->add_control( 'thessnest_contact_email', array(
		'label'       => __( 'İletişim E-Posta Adresi', 'thessnest' ),
		'section'     => 'thessnest_theme_options',
		'type'        => 'email',
	) );

	// 3. Sosyal Medya: Instagram
	$wp_customize->add_setting( 'thessnest_social_instagram', array(
		'default'           => 'https://instagram.com/thessnest',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'thessnest_social_instagram', array(
		'label'       => __( 'Instagram Bağlantısı', 'thessnest' ),
		'section'     => 'thessnest_theme_options',
		'type'        => 'url',
	) );

	// 4. Sosyal Medya: WhatsApp
	$wp_customize->add_setting( 'thessnest_social_whatsapp', array(
		'default'           => 'https://wa.me/30123456789',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'thessnest_social_whatsapp', array(
		'label'       => __( 'WhatsApp Bağlantısı (Örn: https://wa.me/...)', 'thessnest' ),
		'section'     => 'thessnest_theme_options',
		'type'        => 'url',
	) );

	// 5. Alt Bilgi (Footer) Metni
	$wp_customize->add_setting( 'thessnest_footer_text', array(
		'default'           => '&copy; ' . date('Y') . ' ThessNest. Tüm hakları saklıdır.',
		'sanitize_callback' => 'wp_kses_post', // Basit HTML etiketlerine izin verir (örn. <a>, <strong>)
	) );
	$wp_customize->add_control( 'thessnest_footer_text', array(
		'label'       => __( 'Footer (Alt Bilgi) Telif Metni', 'thessnest' ),
		'section'     => 'thessnest_theme_options',
		'type'        => 'textarea',
	) );

	// 6. Chatbot / Live Chat Embed Code
	$wp_customize->add_setting( 'thessnest_chatbot_embed', array(
		'default'           => '',
		'sanitize_callback' => 'thessnest_sanitize_chatbot_embed',
	) );
	$wp_customize->add_control( 'thessnest_chatbot_embed', array(
		'label'       => __( 'Chatbot / Live Chat Embed Code', 'thessnest' ),
		'description' => __( 'Paste the embed code from Tidio, Tawk.to, or any chat widget. It will appear on all pages.', 'thessnest' ),
		'section'     => 'thessnest_theme_options',
		'type'        => 'textarea',
	) );

}
add_action( 'customize_register', 'thessnest_customize_register' );

/**
 * Sanitize chatbot embed code — allow script tags.
 */
function thessnest_sanitize_chatbot_embed( $input ) {
	return $input; // Allow raw script embed (admin-only setting)
}

/**
 * Output the chatbot embed code in the footer (Customizer fallback).
 * Skipped if Redux chatbot is already enabled.
 */
function thessnest_output_chatbot_embed() {
	// Skip if Redux handles the chatbot
	if ( function_exists( 'thessnest_opt' ) && thessnest_opt( 'chatbot_enabled', false ) ) {
		return;
	}
	$embed = get_theme_mod( 'thessnest_chatbot_embed', '' );
	if ( ! empty( $embed ) ) {
		echo "\n<!-- ThessNest Chatbot Widget -->\n";
		echo $embed;
		echo "\n<!-- /ThessNest Chatbot Widget -->\n";
	}
}
add_action( 'wp_footer', 'thessnest_output_chatbot_embed', 99 );

// Include homepage-specific Customizer settings
require_once THESSNEST_DIR . '/inc/customizer-front-page.php';
