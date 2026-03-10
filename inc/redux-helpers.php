<?php
/**
 * ThessNest — Redux Options Bridge
 *
 * Helper functions that read from Redux options ($thessnest_opts)
 * and fall back to sensible defaults when Redux is not active.
 * All template files should call these helpers instead of
 * reading options directly.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;


/**
 * Get a single Redux option value with a fallback.
 *
 * @param string $key     The option key (e.g. 'accent_color').
 * @param mixed  $default Fallback value if Redux is not active or key is empty.
 * @return mixed
 */
function thessnest_opt( $key, $default = '' ) {
	global $thessnest_opts;

	if ( isset( $thessnest_opts[ $key ] ) && $thessnest_opts[ $key ] !== '' ) {
		return $thessnest_opts[ $key ];
	}

	return $default;
}


/* ==========================================================================
   1. STYLING — Accent colour, dark mode, border-radius as CSS variables
   ========================================================================== */

function thessnest_output_dynamic_css() {
	$accent  = thessnest_opt( 'accent_color', '#2563eb' );
	$dark    = thessnest_opt( 'dark_mode', false );
	$radius  = thessnest_opt( 'border_radius', 'medium' );

	// Map radius option to actual value
	$radius_map = array(
		'none'   => '0',
		'small'  => '4px',
		'medium' => '8px',
		'large'  => '12px',
	);
	$radius_val = isset( $radius_map[ $radius ] ) ? $radius_map[ $radius ] : '8px';

	// Convert hex accent to RGB for rgba() usage
	$r = hexdec( substr( $accent, 1, 2 ) );
	$g = hexdec( substr( $accent, 3, 2 ) );
	$b = hexdec( substr( $accent, 5, 2 ) );

	echo "<style id=\"thessnest-redux-dynamic\">\n";
	echo ":root {\n";
	echo "  --accent: {$accent};\n";
	echo "  --accent-rgb: {$r},{$g},{$b};\n";
	echo "  --card-radius: {$radius_val};\n";
	echo "}\n";

	if ( $dark ) {
		echo "body { background:#1a1a2e; color:#e0e0e0; }\n";
		echo ".site-header { background:#16213e; }\n";
		echo ".property-card, .stat-card { background:#1f2937; border-color:#374151; color:#e5e7eb; }\n";
	}

	echo "</style>\n";
}
add_action( 'wp_head', 'thessnest_output_dynamic_css', 99 );


/* ==========================================================================
   2. FAVICON — Output from Redux media upload
   ========================================================================== */

function thessnest_output_favicon() {
	$favicon = thessnest_opt( 'favicon', '' );
	if ( is_array( $favicon ) && ! empty( $favicon['url'] ) ) {
		echo '<link rel="icon" href="' . esc_url( $favicon['url'] ) . '" sizes="32x32" />' . "\n";
	}
}
add_action( 'wp_head', 'thessnest_output_favicon', 5 );


/* ==========================================================================
   3. CHATBOT — Read from Redux, fallback to Customizer
   ========================================================================== */

function thessnest_output_chatbot_redux() {
	$enabled = thessnest_opt( 'chatbot_enabled', false );
	$embed   = thessnest_opt( 'chatbot_embed', '' );

	if ( $enabled && ! empty( $embed ) ) {
		echo "\n<!-- ThessNest Chatbot (Redux) -->\n";
		echo $embed;
		echo "\n<!-- /ThessNest Chatbot -->\n";
	}
}
add_action( 'wp_footer', 'thessnest_output_chatbot_redux', 98 );


/* ==========================================================================
   4. HEADER — Sticky class, logo, CTA button
   ========================================================================== */

/**
 * Add Redux-driven body classes (dark mode, header style).
 */
function thessnest_redux_body_classes( $classes ) {
	if ( thessnest_opt( 'dark_mode', false ) ) {
		$classes[] = 'thessnest-dark';
	}
	if ( thessnest_opt( 'sticky_header', true ) ) {
		$classes[] = 'has-sticky-header';
	}
	$header_style = thessnest_opt( 'header_style', 'default' );
	if ( $header_style === 'transparent' ) {
		$classes[] = 'header-transparent';
	}
	return $classes;
}
add_filter( 'body_class', 'thessnest_redux_body_classes' );


/**
 * Get the header logo URL from Redux (returns image URL or empty).
 */
function thessnest_get_logo_url() {
	$logo = thessnest_opt( 'logo', '' );
	if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
		return $logo['url'];
	}
	return '';
}

/**
 * Get the dark logo URL.
 */
function thessnest_get_logo_dark_url() {
	$logo = thessnest_opt( 'logo_dark', '' );
	if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
		return $logo['url'];
	}
	return '';
}

/**
 * Get the header CTA button data.
 */
function thessnest_get_header_cta() {
	$text = thessnest_opt( 'header_cta_text', '' );
	$url  = thessnest_opt( 'header_cta_url', '' );
	if ( ! empty( $text ) && ! empty( $url ) ) {
		return array( 'text' => $text, 'url' => $url );
	}
	return false;
}


/* ==========================================================================
   5. PRICE & CURRENCY — Dynamic formatting
   ========================================================================== */

/**
 * Format a price with the configured currency symbol & position.
 *
 * @param  string|int|float $price Raw price value.
 * @return string  Formatted price (e.g., "€450" or "450€").
 */
function thessnest_format_price( $price ) {
	if ( empty( $price ) && $price !== 0 && $price !== '0' ) {
		return '';
	}

	$symbol   = thessnest_opt( 'currency_symbol', '€' );
	$position = thessnest_opt( 'currency_position', 'before' );
	$formatted = number_format( (float) $price, 0, ',', '.' );

	if ( $position === 'after' ) {
		return $formatted . $symbol;
	}
	return $symbol . $formatted;
}


/**
 * Get the price period label.
 *
 * @return string e.g. "/ month"
 */
function thessnest_price_period() {
	$per = thessnest_opt( 'price_per', 'month' );
	$map = array(
		'month' => __( '/ month', 'thessnest' ),
		'week'  => __( '/ week', 'thessnest' ),
		'night' => __( '/ night', 'thessnest' ),
	);
	return isset( $map[ $per ] ) ? $map[ $per ] : $map['month'];
}

/**
 * Check if "No Platform Fees" badge should be shown.
 */
function thessnest_show_no_fees_badge() {
	return (bool) thessnest_opt( 'show_no_fees_badge', true );
}


/* ==========================================================================
   6. BOOKING — Helpers
   ========================================================================== */

function thessnest_min_stay() {
	return (int) thessnest_opt( 'min_stay', 30 );
}

function thessnest_max_stay() {
	return (int) thessnest_opt( 'max_stay', 365 );
}

function thessnest_deposit_rate() {
	return (int) thessnest_opt( 'deposit_rate', 20 );
}

function thessnest_booking_is_auto_approve() {
	return thessnest_opt( 'booking_approval', 'manual' ) === 'auto';
}

function thessnest_instant_booking_enabled() {
	return (bool) thessnest_opt( 'instant_booking', false );
}


/* ==========================================================================
   7. CONTACT — Helpers
   ========================================================================== */

function thessnest_contact_phone() {
	return thessnest_opt( 'contact_phone', '+30 123 456 789' );
}

function thessnest_contact_email() {
	return thessnest_opt( 'contact_email', 'hello@thessnest.com' );
}

function thessnest_contact_address() {
	return thessnest_opt( 'contact_address', 'Thessaloniki, Greece' );
}


/* ==========================================================================
   8. GOOGLE MAPS API KEY — Enqueue script with key
   ========================================================================== */

function thessnest_enqueue_google_maps() {
	$api_key = thessnest_opt( 'google_maps_api_key', '' );
	if ( ! empty( $api_key ) ) {
		wp_enqueue_script(
			'google-maps',
			'https://maps.googleapis.com/maps/api/js?key=' . urlencode( $api_key ) . '&libraries=places',
			array(),
			null,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'thessnest_enqueue_google_maps' );


/* ==========================================================================
   9. TOP BAR — Notification bar above header
   ========================================================================== */

function thessnest_output_topbar() {
	if ( ! thessnest_opt( 'topbar_enabled', false ) ) {
		return;
	}

	$text     = thessnest_opt( 'topbar_text', '' );
	$link     = thessnest_opt( 'topbar_link', '' );
	$bg       = thessnest_opt( 'topbar_bg_color', '#1B2A4A' );
	$color    = thessnest_opt( 'topbar_text_color', '#ffffff' );
	$dismiss  = thessnest_opt( 'topbar_dismissible', true );

	if ( empty( $text ) ) return;

	$tag_open  = ! empty( $link ) ? '<a href="' . esc_url( $link ) . '" style="color:inherit;text-decoration:none;">' : '';
	$tag_close = ! empty( $link ) ? '</a>' : '';
	?>
	<div class="thessnest-topbar" style="background:<?php echo esc_attr( $bg ); ?>;color:<?php echo esc_attr( $color ); ?>;padding:8px 16px;text-align:center;font-size:14px;position:relative;z-index:1000;">
		<?php echo $tag_open; ?>
			<?php echo esc_html( $text ); ?>
		<?php echo $tag_close; ?>
		<?php if ( $dismiss ) : ?>
			<button onclick="this.parentElement.remove()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:inherit;font-size:18px;cursor:pointer;line-height:1;" aria-label="<?php esc_attr_e( 'Close', 'thessnest' ); ?>">&times;</button>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'wp_body_open', 'thessnest_output_topbar', 1 );


/* ==========================================================================
   10. CUSTOM CODE — CSS & JS injection
   ========================================================================== */

function thessnest_output_custom_css() {
	$css = thessnest_opt( 'custom_css', '' );
	if ( ! empty( $css ) ) {
		echo "\n<style id=\"thessnest-custom-css\">\n" . $css . "\n</style>\n";
	}

	$header_js = thessnest_opt( 'custom_js_header', '' );
	if ( ! empty( $header_js ) ) {
		echo "\n" . $header_js . "\n";
	}
}
add_action( 'wp_head', 'thessnest_output_custom_css', 100 );

function thessnest_output_custom_js_footer() {
	$footer_js = thessnest_opt( 'custom_js_footer', '' );
	if ( ! empty( $footer_js ) ) {
		echo "\n" . $footer_js . "\n";
	}
}
add_action( 'wp_footer', 'thessnest_output_custom_js_footer', 100 );


/* ==========================================================================
   11. GDPR — Cookie consent banner
   ========================================================================== */

function thessnest_output_gdpr_banner() {
	if ( ! thessnest_opt( 'gdpr_enabled', true ) ) {
		return;
	}

	$message    = thessnest_opt( 'gdpr_message', 'We use cookies to improve your experience. By continuing to browse, you agree to our use of cookies.' );
	$btn_text   = thessnest_opt( 'gdpr_button_text', 'Accept' );
	$privacy_id = thessnest_opt( 'gdpr_privacy_page', '' );
	$privacy_url = ! empty( $privacy_id ) ? get_permalink( $privacy_id ) : '';
	?>
	<div id="thessnest-gdpr" style="display:none;position:fixed;bottom:0;left:0;right:0;background:#1B2A4A;color:#fff;padding:16px 24px;z-index:99999;font-size:14px;box-shadow:0 -2px 10px rgba(0,0,0,.2);align-items:center;justify-content:center;gap:16px;">
		<span><?php echo esc_html( $message ); ?>
			<?php if ( $privacy_url ) : ?>
				<a href="<?php echo esc_url( $privacy_url ); ?>" style="color:#7dd3fc;text-decoration:underline;margin-left:4px;"><?php esc_html_e( 'Privacy Policy', 'thessnest' ); ?></a>
			<?php endif; ?>
		</span>
		<button onclick="document.getElementById('thessnest-gdpr').style.display='none';document.cookie='thessnest_gdpr=1;path=/;max-age=31536000';" style="background:#2563eb;color:#fff;border:none;padding:8px 20px;border-radius:6px;cursor:pointer;font-weight:600;white-space:nowrap;"><?php echo esc_html( $btn_text ); ?></button>
	</div>
	<script>if(!document.cookie.includes('thessnest_gdpr=1')){document.getElementById('thessnest-gdpr').style.display='flex';}</script>
	<?php
}
add_action( 'wp_footer', 'thessnest_output_gdpr_banner', 50 );


/* ==========================================================================
   12. OPTIMIZATIONS — Disable emojis & embeds
   ========================================================================== */

function thessnest_apply_optimizations() {
	if ( thessnest_opt( 'disable_emojis', true ) ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
	}

	if ( thessnest_opt( 'disable_embeds', false ) ) {
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	}
}
add_action( 'init', 'thessnest_apply_optimizations' );


/* ==========================================================================
   13. EMAIL — Custom sender name & address
   ========================================================================== */

function thessnest_mail_from_name( $name ) {
	$custom = thessnest_opt( 'email_from_name', '' );
	return ! empty( $custom ) ? $custom : $name;
}
add_filter( 'wp_mail_from_name', 'thessnest_mail_from_name' );

function thessnest_mail_from_email( $email ) {
	$custom = thessnest_opt( 'email_from_address', '' );
	return ! empty( $custom ) ? $custom : $email;
}
add_filter( 'wp_mail_from', 'thessnest_mail_from_email' );


/* ==========================================================================
   14. LABELS — Helper to get customizable UI strings
   ========================================================================== */

/**
 * Get a customizable label from Redux.
 *
 * @param string $key     Label key (e.g. 'rent', 'deposit', 'book_now').
 * @param string $default Fallback text.
 * @return string
 */
function thessnest_label( $key, $default = '' ) {
	return thessnest_opt( 'label_' . $key, $default );
}

