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

	if ( class_exists( 'Redux' ) ) {
		global $thessnest_opt;
		if ( ! empty( $thessnest_opt['opt-media-logo']['url'] ) ) {
			$logo_url = $thessnest_opt['opt-media-logo']['url'];
		}
		if ( ! empty( $thessnest_opt['footer_copyright'] ) ) {
			$footer_text = $thessnest_opt['footer_copyright'];
		}
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
			body { margin: 0; padding: 0; background-color: #f7f9fc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
			.email-wrapper { width: 100%; table-layout: fixed; background-color: #f7f9fc; padding-top: 40px; padding-bottom: 40px; }
			.email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); }
			.email-header { padding: 32px 40px; text-align: center; border-bottom: 1px solid #e2e8f0; background-color: #ffffff; }
			.email-header img { max-height: 48px; width: auto; }
			.email-body { padding: 40px; color: #334155; font-size: 16px; line-height: 1.6; }
			.email-body h1, .email-body h2, .email-body h3 { color: #0f172a; margin-top: 0; margin-bottom: 20px; font-weight: 600; }
			.email-body p { margin-top: 0; margin-bottom: 16px; }
			.email-footer { background-color: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0; }
			.email-footer p { margin: 0; font-size: 13px; color: #64748b; line-height: 1.5; }
			.email-footer a { color: #2563eb; text-decoration: none; }
			.btn { display: inline-block; padding: 12px 24px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; margin-top: 16px; margin-bottom: 16px; }
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
