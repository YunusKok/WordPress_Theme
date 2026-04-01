<?php
/**
 * ThessNest — Global HTML Email Templates
 *
 * Catches all outgoing WordPress emails and wraps them in a 
 * premium, responsive HTML template branded with the theme settings.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Switch all email content types to HTML
 */
add_filter( 'wp_mail_content_type', function() {
	return 'text/html';
} );

/**
 * Wrap outgoing emails in the premium HTML layout
 */
function thessnest_wrap_email_in_html( $args ) {
	
	$message = $args['message'];
	$subject = isset( $args['subject'] ) ? $args['subject'] : '';

	// If the message already contains an HTML wrapper, skip to prevent double wrapping.
	if ( strpos( $message, '<html' ) !== false || strpos( $message, '<body' ) !== false ) {
		return $args;
	}

	// Fetch Theme Configuration
	$site_name   = get_bloginfo( 'name' );
	$site_url    = home_url();
	$logo_url    = get_theme_file_uri( 'assets/images/logo-dark.svg' );
	$footer_text = sprintf( esc_html__( '© %s %s. All rights reserved.', 'thessnest' ), date('Y'), $site_name );

	// Base Colors for Email
	$bg_color       = '#f7f9fc';
	$header_bg      = '#ffffff';
	$body_bg        = '#ffffff';
	$footer_bg      = '#f8fafc';
	$text_color     = '#334155';
	$btn_color      = '#2563eb';

	if ( class_exists( 'Redux' ) ) {
		global $thessnest_opts;
		
		// Map redundant variable occasionally used in theme
		$opt = ! empty( $thessnest_opts ) ? $thessnest_opts : ( isset($GLOBALS['thessnest_opt']) ? $GLOBALS['thessnest_opt'] : array() );
		
		if ( ! empty( $opt['email_logo']['url'] ) ) {
			$logo_url = $opt['email_logo']['url'];
		} elseif ( ! empty( $opt['logo']['url'] ) ) {
			$logo_url = $opt['logo']['url'];
		}

		if ( ! empty( $opt['email_footer_text'] ) ) {
			$footer_text = $opt['email_footer_text'];
		} elseif ( ! empty( $opt['footer_copyright'] ) ) {
			$footer_text = $opt['footer_copyright'];
		}

		if ( ! empty( $opt['email_bg_color'] ) ) $bg_color = $opt['email_bg_color'];
		if ( ! empty( $opt['email_header_bg'] ) ) $header_bg = $opt['email_header_bg'];
		if ( ! empty( $opt['email_body_bg'] ) ) $body_bg = $opt['email_body_bg'];
		if ( ! empty( $opt['email_footer_bg'] ) ) $footer_bg = $opt['email_footer_bg'];
		if ( ! empty( $opt['email_text_color'] ) ) $text_color = $opt['email_text_color'];
		if ( ! empty( $opt['email_btn_color'] ) ) $btn_color = $opt['email_btn_color'];
	}

	// Format line breaks for HTML
	$formatted_message = wpautop( $message );

	// Build the Premium HTML Wrapper
	$html = '
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>' . esc_html( $subject ) . '</title>
		<style>
			body { margin: 0; padding: 0; background-color: ' . esc_attr( $bg_color ) . '; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
			.email-wrapper { width: 100%; table-layout: fixed; background-color: ' . esc_attr( $bg_color ) . '; padding-top: 40px; padding-bottom: 40px; }
			.email-container { max-width: 600px; margin: 0 auto; background-color: ' . esc_attr( $body_bg ) . '; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); }
			.email-header { padding: 32px 40px; text-align: center; border-bottom: 1px solid #e2e8f0; background-color: ' . esc_attr( $header_bg ) . '; }
			.email-header img { max-height: 48px; width: auto; }
			.email-body { padding: 40px; color: ' . esc_attr( $text_color ) . '; font-size: 16px; line-height: 1.6; background-color: ' . esc_attr( $body_bg ) . '; }
			.email-body h1, .email-body h2, .email-body h3 { color: ' . esc_attr( $text_color ) . '; margin-top: 0; margin-bottom: 20px; font-weight: 600; }
			.email-body p { margin-top: 0; margin-bottom: 16px; }
			.email-footer { background-color: ' . esc_attr( $footer_bg ) . '; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
			.email-footer p { margin: 0; font-size: 13px; color: ' . esc_attr( $text_color ) . '; line-height: 1.5; opacity: 0.8; }
			.email-footer a { color: ' . esc_attr( $btn_color ) . '; text-decoration: none; }
			.btn { display: inline-block; padding: 12px 24px; background-color: ' . esc_attr( $btn_color ) . '; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 16px; margin-bottom: 16px; }
		</style>
	</head>
	<body>
		<div class="email-wrapper">
			<div class="email-container">
				
				<!-- Header -->
				<div class="email-header">
					<a href="' . esc_url( $site_url ) . '">
						<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $site_name ) . '">
					</a>
				</div>
				
				<!-- Body Content -->
				<div class="email-body">
					' . $formatted_message . '
				</div>

				<!-- Footer -->
				<div class="email-footer">
					<p>' . wp_kses_post( $footer_text ) . '</p>
					<p style="margin-top: 8px;">
						<a href="' . esc_url( $site_url ) . '">' . esc_html__( 'Visit Website', 'thessnest' ) . '</a>
					</p>
				</div>

			</div>
		</div>
	</body>
	</html>
	';

	// Replace the original message with the wrapped HTML
	$args['message'] = $html;

	return $args;
}
add_filter( 'wp_mail', 'thessnest_wrap_email_in_html' );
