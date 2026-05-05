<?php
/**
 * ThessNest — Theme Functions & Definitions
 *
 * Registers Custom Post Types, Taxonomies, Enqueues,
 * Theme Supports, and helper functions for a mid-term
 * housing directory targeting Erasmus students & Digital Nomads.
 *
 * @package ThessNest
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ==========================================================================
   0. CONSTANTS
   ========================================================================== */

define( 'THESSNEST_VERSION', '1.0.0' );
define( 'THESSNEST_DIR',     get_template_directory() );
define( 'THESSNEST_URI',     get_template_directory_uri() );


/* ==========================================================================
   1. THEME SETUP — After-Setup Hook
   ========================================================================== */

if ( ! function_exists( 'thessnest_setup' ) ) :
	/**
	 * Configure core WordPress theme supports.
	 */
	function thessnest_setup() {

		// Load theme textdomain for i18n
		load_theme_textdomain( 'thessnest', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the <title> tag.
		add_theme_support( 'title-tag' );

		// Enable post thumbnails on posts and the property CPT.
		add_theme_support( 'post-thumbnails' );

		// Enqueue editor styles to fix core block UI differences (e.g. button blocks)
		add_theme_support( 'editor-styles' );
		add_editor_style( 'editor-style.css' );

		// Enable excerpts for pages (used for subtitles on the frontend).
		add_post_type_support( 'page', 'excerpt' );

		// Custom image sizes for property cards and heroes.
		add_image_size( 'card-thumb',  640,  480, true );  // 4:3 ratio for cards
		add_image_size( 'hero-bg',    1920, 1080, true );  // Full-width hero

		// HTML5 markup for core components.
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		) );

		// Register navigation menus.
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary Menu', 'thessnest' ),
			'footer'  => esc_html__( 'Footer Menu', 'thessnest' ),
		) );

		// Disable core block patterns (we build our own).
		remove_theme_support( 'core-block-patterns' );

		// Set content width for embeds.
		if ( ! isset( $GLOBALS['content_width'] ) ) {
			$GLOBALS['content_width'] = 1280;
		}
	}
endif;
add_action( 'after_setup_theme', 'thessnest_setup' );


/* ==========================================================================
   2. CORE FUNCTIONALITY (CPTs, Taxonomies & Meta Boxes)
   ========================================================================== */

require_once THESSNEST_DIR . '/inc/admin-front-page.php';
require_once THESSNEST_DIR . '/inc/seo-tags.php';
// Theme Settings & Admin Integrations
// NOTE: Plugin specific files have been extracted to `thessnest-core`.

// Theme Settings & Admin Integrations
require_once THESSNEST_DIR . '/inc/admin-menu.php';
require_once THESSNEST_DIR . '/inc/redux-helpers.php';
require_once THESSNEST_DIR . '/inc/redux-config.php';
require_once THESSNEST_DIR . '/inc/demo-setup.php'; // Local Demo Setup Script
require_once THESSNEST_DIR . '/inc/tgm-config.php'; // TGM Plugin Activation
require_once THESSNEST_DIR . '/inc/ocdi-config.php'; // One Click Demo Import Config

// -------------------------------------------------------------------------
// NOTE: Core functionality (CPTs, Elementor Widgets, Payments, 
// Booking Engine, Advanced Reviews) has been strictly extracted to 
// the `thessnest-core` plugin to comply with Envato ThemeForest guidelines.
// -------------------------------------------------------------------------




/* ==========================================================================
   4. ENQUEUE STYLES & SCRIPTS
   ========================================================================== */

/**
 * Enqueue front-end CSS and JavaScript assets.
 *
 * - Google Fonts (Inter) via preconnect + stylesheet
 * - Swiper.js v11 from CDN (carousel)
 * - Theme stylesheet (style.css)
 * - Custom JS (thessnest.js)
 */
function thessnest_enqueue_assets() {

	/* === Google Fonts: Inter === */
	wp_enqueue_style(
		'thessnest-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
		array(),
		null  // null = no version, ensures clean URL for external resource
	);

	/* === Swiper.js CSS (CDN) === */
	wp_enqueue_style(
		'swiper',
		'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
		array(),
		'11.0.0'
	);

	/* === Theme Stylesheet === */
	wp_enqueue_style(
		'thessnest-style',
		get_stylesheet_uri(),
		array( 'swiper' ),
		filemtime( get_stylesheet_directory() . '/style.css' ) // Auto cache-busting
	);

	/* === Flatpickr CSS (CDN)  === */
	wp_enqueue_style(
		'flatpickr',
		'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
		array(),
		'4.6.13'
	);

	/* === Swiper.js JS (CDN) === */
	wp_enqueue_script(
		'swiper',
		'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
		array(),
		'11.0.0',
		true  // Load in footer
	);

	/* === Flatpickr JS (CDN) === */
	wp_enqueue_script(
		'flatpickr',
		'https://cdn.jsdelivr.net/npm/flatpickr',
		array(),
		'4.6.13',
		true
	);

	/* === Theme JavaScript === */
	wp_enqueue_script(
		'thessnest-js',
		THESSNEST_URI . '/js/thessnest.js',
		array( 'swiper', 'flatpickr' ),
		THESSNEST_VERSION,
		true  // Load in footer
	);

	wp_localize_script( 'thessnest-js', 'thessnestAjax', array(
		'ajaxurl'  => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'thessnest-nonce' ),
		'loggedIn' => is_user_logged_in() ? '1' : '0'
	) );

	/* === Wishlist AJAX === */
	wp_enqueue_script(
		'thessnest-wishlist',
		THESSNEST_URI . '/js/wishlist-ajax.js',
		array(),
		THESSNEST_VERSION,
		true
	);

	wp_localize_script( 'thessnest-wishlist', 'thessnestWishlist', array(
		'ajaxurl'  => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'thessnest_wishlist_nonce' ),
		'loggedIn' => is_user_logged_in() ? '1' : '0',
		'loginMsg' => esc_html__( 'Please sign in to save properties.', 'thessnest' ),
	) );

	/* === Leaflet CSS & JS for Property Archives (Map) === */
	if ( is_post_type_archive( 'property' ) || is_tax( 'neighborhood' ) || is_tax( 'amenity' ) || is_tax( 'target_group' ) || is_page_template( 'template-search.php' ) ) {
		wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
		wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );
	}

	/* === Advanced Search (noUiSlider + Controller) === */
	if ( is_post_type_archive( 'property' ) || is_page_template( 'template-search.php' ) || is_tax() ) {

		// noUiSlider CSS & JS (CDN)
		wp_enqueue_style( 'nouislider', 'https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css', array(), '15.7.1' );
		wp_enqueue_script( 'nouislider', 'https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js', array(), '15.7.1', true );

		// Advanced Search JS
		wp_enqueue_script(
			'thessnest-advanced-search',
			THESSNEST_URI . '/js/advanced-search.js',
			array( 'nouislider', 'flatpickr' ),
			THESSNEST_VERSION,
			true
		);

		// Price defaults from Redux
		$price_min  = function_exists( 'thessnest_opt' ) ? intval( thessnest_opt( 'search_price_min', 100 ) )  : 100;
		$price_max  = function_exists( 'thessnest_opt' ) ? intval( thessnest_opt( 'search_price_max', 2000 ) ) : 2000;
		$price_step = function_exists( 'thessnest_opt' ) ? intval( thessnest_opt( 'search_price_step', 50 ) )  : 50;
		$per_page   = function_exists( 'thessnest_opt' ) ? intval( thessnest_opt( 'listings_per_page', 12 ) )  : 12;
		$currency   = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'currency_symbol', '€' )            : '€';

		wp_localize_script( 'thessnest-advanced-search', 'thessnestSearch', array(
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'thessnest-nonce' ),
			'emptyMsg'  => esc_html__( 'No properties match your filters.', 'thessnest' ),
			'defaults'  => array(
				'price_min'  => $price_min,
				'price_max'  => $price_max,
				'price_step' => $price_step,
				'per_page'   => $per_page,
				'currency'   => $currency,
			),
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'thessnest_enqueue_assets' );

/**
 * Enqueue Admin Scripts for Custom Redux Styling auto-fill.
 */
function thessnest_admin_scripts( $hook ) {
	wp_enqueue_script( 'thessnest-admin-styling', get_template_directory_uri() . '/js/admin-styling.js', array('jquery'), THESSNEST_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'thessnest_admin_scripts' );


/**
 * Add preconnect hints for Google Fonts & jsDelivr CDN.
 * Improves LCP/FCP by establishing connections early.
 */
function thessnest_preconnect_hints() {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	echo '<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>' . "\n";
	
	// Preload main font
	echo '<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">' . "\n";
}
add_action( 'wp_head', 'thessnest_preconnect_hints', 1 );


/* ==========================================================================
   4.5. SEO MULTILINGUAL HREFLANG
   ========================================================================== */
function thessnest_add_hreflang_tags() {
	// If WPML or Polylang is active, they handle this. We provide a basic fallback.
	if ( function_exists( 'pll_the_languages' ) || function_exists( 'icl_get_languages' ) ) {
		return;
	}

	$locale = get_locale(); // e.g. en_US, tr_TR, el
	$lang   = substr( $locale, 0, 2 ); // e.g. en, tr, el

	// Use WordPress's own permalink for the current page — safe and sanitized.
	$url = get_permalink();
	if ( ! $url ) {
		$url = home_url( '/' );
	}

	echo '<link rel="alternate" hreflang="' . esc_attr( $lang ) . '" href="' . esc_url( $url ) . '" />' . "\n";
	echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $url ) . '" />' . "\n";
}
add_action( 'wp_head', 'thessnest_add_hreflang_tags', 1 );

/* ==========================================================================
   5. HELPER FUNCTIONS — Secure Output & Data Retrieval
   ========================================================================== */

/**
 * Get a property custom field value (escaped for safe output).
 *
 * @param string   $key     Meta key (without underscore prefix).
 * @param int|null $post_id Post ID. Defaults to current post.
 * @return string  Escaped meta value.
 */
function thessnest_get_meta( $key, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$value = get_post_meta( $post_id, '_thessnest_' . $key, true );
	return esc_html( $value ? $value : '' );
}


/**
 * Get gallery image IDs for a property.
 *
 * Stored as a serialised array under `_thessnest_gallery`.
 *
 * @param int|null $post_id Post ID. Defaults to current post.
 * @return array   Array of attachment IDs.
 */
function thessnest_get_gallery( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$gallery = get_post_meta( $post_id, '_thessnest_gallery', true );

	// Handle both formats: array (from admin) and comma-separated string (from frontend AJAX)
	if ( is_array( $gallery ) ) {
		return array_filter( array_map( 'intval', $gallery ) );
	}
	if ( is_string( $gallery ) && ! empty( $gallery ) ) {
		return array_filter( array_map( 'intval', explode( ',', $gallery ) ) );
	}
	return array();
}


/**
 * Get the first taxonomy term name for a given property.
 *
 * @param string   $taxonomy Taxonomy slug.
 * @param int|null $post_id  Post ID.
 * @return string  Term name or empty string.
 */
function thessnest_get_first_term( $taxonomy, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$terms = get_the_terms( $post_id, $taxonomy );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		return esc_html( $terms[0]->name );
	}

	return '';
}


// thessnest_format_price() is now in inc/redux-helpers.php with dynamic currency support.


/* ==========================================================================
   6. WIDGETS — Register Sidebar
   ========================================================================== */

/**
 * Register widget areas: Footer columns + Sidebars.
 */
function thessnest_widgets_init() {

	// ── Footer Widget Areas (1–4) ──
	$footer_cols = 4;
	if ( function_exists( 'thessnest_opt' ) ) {
		$footer_cols = (int) thessnest_opt( 'footer_columns', 4 );
	}

	for ( $i = 1; $i <= $footer_cols; $i++ ) {
		register_sidebar( array(
			/* translators: %d = column number */
			'name'          => sprintf( esc_html__( 'Footer Area %d', 'thessnest' ), $i ),
			'id'            => 'footer-' . $i,
			'description'   => sprintf( esc_html__( 'Footer column %d — drag widgets here.', 'thessnest' ), $i ),
			'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

	// ── Page Sidebar ──
	register_sidebar( array(
		'name'          => esc_html__( 'Page Sidebar', 'thessnest' ),
		'id'            => 'page-sidebar',
		'description'   => esc_html__( 'Widgets shown on standard pages.', 'thessnest' ),
		'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	// ── Listings Sidebar ──
	register_sidebar( array(
		'name'          => esc_html__( 'Listings Sidebar', 'thessnest' ),
		'id'            => 'listings-sidebar',
		'description'   => esc_html__( 'Widgets shown on property archive pages.', 'thessnest' ),
		'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	// ── Single Listing Sidebar ──
	register_sidebar( array(
		'name'          => esc_html__( 'Single Listing', 'thessnest' ),
		'id'            => 'single-listing',
		'description'   => esc_html__( 'Widgets shown on single property pages.', 'thessnest' ),
		'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	// ── Blog Sidebar ──
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Sidebar', 'thessnest' ),
		'id'            => 'blog-sidebar',
		'description'   => esc_html__( 'Widgets shown on blog posts and archives.', 'thessnest' ),
		'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'thessnest_widgets_init' );


/* ==========================================================================
   7. SECURITY — Basic Hardening
   ========================================================================== */

// Remove WordPress version from head (information disclosure).
remove_action( 'wp_head', 'wp_generator' );

// Remove RSD link (XML-RPC endpoint exposure).
remove_action( 'wp_head', 'rsd_link' );

// Remove wlwmanifest link (Windows Live Writer).
remove_action( 'wp_head', 'wlwmanifest_link' );

// Remove shortlink.
remove_action( 'wp_head', 'wp_shortlink_wp_head' );


/* ==========================================================================
   8. PERFORMANCE — Inline Critical Resource Hints
   ========================================================================== */

/**
 * Defer non-critical JS.
 * Add defer attribute to all front-end theme scripts.
 *
 * @param string $tag    The <script> HTML tag.
 * @param string $handle Script handle.
 * @return string Modified tag.
 */
function thessnest_defer_scripts( $tag, $handle ) {
	// Only defer our theme scripts and swiper.
	$defer_handles = array( 'thessnest-js', 'swiper' );

	if ( in_array( $handle, $defer_handles, true ) ) {
		return str_replace( ' src', ' defer src', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'thessnest_defer_scripts', 10, 2 );


/* ==========================================================================
   9. FLUSH REWRITE RULES ON ACTIVATION
   ========================================================================== */

/**
 * Flush rewrite rules when theme is activated so CPT archives work.
 *
 * CPT/taxonomy registration is handled by the thessnest-core plugin.
 * We simply flush here after the plugin has already registered them.
 */
function thessnest_rewrite_flush() {
	// CPTs and taxonomies are registered by the thessnest-core plugin.
	// do_action allows the plugin to hook in before we flush.
	do_action( 'thessnest_before_flush_rewrite_rules' );
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'thessnest_rewrite_flush' );


/* ==========================================================================
   9b. ADMIN NOTICE — Detect Old Theme Pages on Activation
   ========================================================================== */

/**
 * On theme activation, scan for pages that may have been created by a
 * previous theme or page builder and flag them for review.
 */
function thessnest_flag_old_pages() {
	// ThessNest-specific slugs (pages we expect to exist).
	$thessnest_slugs = array( 'home', 'ana-sayfa', 'dashboard', 'add-listing', 'about-us' );

	// ThessNest page templates.
	$thessnest_templates = array(
		'template-dashboard.php',
		'template-add-listing.php',
		'template-about.php',
		'front-page.php',
	);

	$all_pages = get_pages( array( 'post_status' => 'publish' ) );
	$suspicious = array();

	foreach ( $all_pages as $page ) {
		$template = get_page_template_slug( $page->ID );

		// Skip pages that use a ThessNest template.
		if ( in_array( $template, $thessnest_templates, true ) ) {
			continue;
		}

		// Skip pages with known ThessNest slugs (user just created them).
		if ( in_array( $page->post_name, $thessnest_slugs, true ) ) {
			continue;
		}

		// Flag pages that contain page-builder shortcodes or metadata.
		$content     = $page->post_content;
		$has_builder = (
			has_shortcode( $content, 'elementor-template' ) ||
			strpos( $content, 'wp:generateblocks' ) !== false ||
			strpos( $content, 'et_pb_' ) !== false ||
			get_post_meta( $page->ID, '_elementor_edit_mode', true ) ||
			get_post_meta( $page->ID, '_wpb_shortcodes_custom_css', true )
		);

		if ( $has_builder ) {
			$suspicious[] = $page;
		}
	}

	if ( ! empty( $suspicious ) ) {
		update_option( 'thessnest_old_pages_detected', array_map( function( $p ) {
			return array( 'id' => $p->ID, 'title' => $p->post_title, 'slug' => $p->post_name );
		}, $suspicious ) );
	}
}
add_action( 'after_switch_theme', 'thessnest_flag_old_pages' );

/**
 * Show an admin notice if old/incompatible pages were detected.
 * Dismissible — once dismissed, it won't show again.
 */
function thessnest_old_pages_admin_notice() {
	// Don't show if already dismissed.
	if ( get_option( 'thessnest_old_pages_notice_dismissed' ) ) {
		return;
	}

	$old_pages = get_option( 'thessnest_old_pages_detected', array() );

	if ( empty( $old_pages ) ) {
		return;
	}

	$pages_url = admin_url( 'edit.php?post_type=page' );
	?>
	<div class="notice notice-warning is-dismissible thessnest-old-pages-notice">
		<h3 style="margin-top:12px;">🏠 ThessNest — <?php esc_html_e( 'Legacy Theme Pages Detected', 'thessnest' ); ?></h3>
		<p>
			<?php esc_html_e( 'The following pages left over from your previous theme may be incompatible with ThessNest. They contain page builder content (Elementor, WPBakery, etc.) and may appear broken.', 'thessnest' ); ?>
		</p>
		<ul style="list-style:disc;margin-left:20px;">
			<?php foreach ( $old_pages as $p ) : ?>
				<li>
					<strong><?php echo esc_html( $p['title'] ); ?></strong>
					<code>/<?php echo esc_html( $p['slug'] ); ?>/</code>
					— <a href="<?php echo esc_url( get_edit_post_link( $p['id'] ) ); ?>"><?php esc_html_e( 'Edit', 'thessnest' ); ?></a>
					| <a href="<?php echo esc_url( get_delete_post_link( $p['id'] ) ); ?>" style="color:#d63638;"><?php esc_html_e( 'Move to Trash', 'thessnest' ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
		<p>
			<a href="<?php echo esc_url( $pages_url ); ?>" class="button button-primary"><?php esc_html_e( 'View All Pages', 'thessnest' ); ?></a>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=thessnest_dismiss_old_pages' ), 'thessnest_dismiss_notice' ) ); ?>" class="button" style="margin-left:8px;"><?php esc_html_e( 'Dismiss This Notice', 'thessnest' ); ?></a>
		</p>
		<p style="color:#666;font-size:12px;">
			<?php esc_html_e( 'Tip: Create all pages used with ThessNest from scratch as described in the Setup Guide (setup_instructions.md).', 'thessnest' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'thessnest_old_pages_admin_notice' );

/**
 * Handle the dismiss action for the old-pages notice.
 */
function thessnest_dismiss_old_pages_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Unauthorized action.', 'thessnest' ) );
	}
	check_admin_referer( 'thessnest_dismiss_notice' );
	update_option( 'thessnest_old_pages_notice_dismissed', true );
	delete_option( 'thessnest_old_pages_detected' );
	wp_safe_redirect( admin_url() );
	exit;
}
add_action( 'admin_post_thessnest_dismiss_old_pages', 'thessnest_dismiss_old_pages_notice' );


/* ==========================================================================
   10. FALLBACK MENUS — Used when no menu is assigned
   ========================================================================== */

if ( ! function_exists( 'thessnest_fallback_menu' ) ) {
	/**
	 * Fallback menu for desktop if no menu is assigned.
	 */
	function thessnest_fallback_menu() {
		echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'thessnest' ) . '</a>';
		echo '<a href="' . esc_url( get_post_type_archive_link( 'property' ) ) . '">' . esc_html__( 'Properties', 'thessnest' ) . '</a>';
		echo '<a href="#">' . esc_html__( 'Neighborhoods', 'thessnest' ) . '</a>';
		echo '<a href="#">' . esc_html__( 'About', 'thessnest' ) . '</a>';
		echo '<a href="#">' . esc_html__( 'Contact', 'thessnest' ) . '</a>';
	}
}

if ( ! function_exists( 'thessnest_fallback_menu_mobile' ) ) {
	/**
	 * Fallback menu for mobile if no menu is assigned.
	 */
	function thessnest_fallback_menu_mobile() {
		echo '<ul>';
		echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'thessnest' ) . '</a></li>';
		echo '<li><a href="' . esc_url( get_post_type_archive_link( 'property' ) ) . '">' . esc_html__( 'Properties', 'thessnest' ) . '</a></li>';
		echo '<li><a href="#">' . esc_html__( 'Neighborhoods', 'thessnest' ) . '</a></li>';
		echo '<li><a href="#">' . esc_html__( 'About', 'thessnest' ) . '</a></li>';
		echo '<li><a href="#">' . esc_html__( 'Contact', 'thessnest' ) . '</a></li>';
		echo '</ul>';
	}
}

/* ==========================================================================
   11. THEME CUSTOMIZER (TEMA AYAR PANELI)
   ========================================================================== */

require_once THESSNEST_DIR . '/inc/customizer.php';

/* ==========================================================================
   12. FEATURE MODULES (MOVED TO PLUGIN)
   ========================================================================== */

// NOTE: All feature modules (Digital Lease, Payouts, Roommate Matching, etc.) 
// have been moved to the `thessnest-core` plugin to comply with 
// Envato ThemeForest "Plugin Territory" requirements.


/* ==========================================================================
   13. ADMIN BAR & ACCESS CONTROL
   ========================================================================== */

/**
 * Hide the WordPress admin bar for non-administrator users.
 * Non-admin users only interact via the frontend dashboard.
 */
if ( ! current_user_can( 'administrator' ) && ! is_admin() ) {
	add_filter( 'show_admin_bar', '__return_false' );
}

/**
 * Block non-admin users from accessing /wp-admin/ directly.
 * Redirects them to the homepage instead.
 */
if ( ! function_exists( 'thessnest_block_admin_access' ) ) :
	function thessnest_block_admin_access() {
		if ( is_user_logged_in() && is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			wp_safe_redirect( home_url( '/' ) );
			exit;
		}
	}
	add_action( 'init', 'thessnest_block_admin_access' );
endif;


/* ==========================================================================
   14. NON-ADMIN LOGIN REDIRECT
   ========================================================================== */

/**
 * Redirect non-admin users to the frontend dashboard after login
 * instead of /wp-admin/.
 */
if ( ! function_exists( 'thessnest_login_redirect' ) ) :
	function thessnest_login_redirect( $redirect_to, $request, $user ) {
		if ( isset( $user->roles ) && ! in_array( 'administrator', (array) $user->roles, true ) ) {
			$dashboard_page = home_url( '/dashboard/' );
			return $dashboard_page;
		}
		return $redirect_to;
	}
	add_filter( 'login_redirect', 'thessnest_login_redirect', 10, 3 );
endif;


/* ==========================================================================
   15. CLASSIC WIDGETS — Disable Block Widget Editor
   ========================================================================== */

/**
 * Keep the classic widget editor for better theme compatibility.
 * The block widget editor can break custom sidebar layouts.
 */
add_filter( 'use_widgets_block_editor', '__return_false' );


/* ==========================================================================
   16. NON-LATIN USERNAME SUPPORT
   ========================================================================== */

/**
 * Allow non-Latin characters (Turkish, Greek, Arabic, Cyrillic etc.)
 * in WordPress usernames — critical for international students.
 */
if ( ! function_exists( 'thessnest_non_strict_login' ) ) :
	function thessnest_non_strict_login( $username, $raw_username, $strict ) {
		if ( ! $strict ) {
			return $username;
		}
		return sanitize_user( stripslashes( $raw_username ), false );
	}
	add_filter( 'sanitize_user', 'thessnest_non_strict_login', 10, 3 );
endif;


/* ==========================================================================
   17. SEARCH SCOPE — Limit to Blog Posts Only
   ========================================================================== */

/**
 * Ensure the default WordPress search only returns blog posts,
 * not property CPTs (which have their own archive/search UI).
 */
if ( ! function_exists( 'thessnest_limit_search_to_posts' ) ) :
	function thessnest_limit_search_to_posts( $query ) {
		if ( ! is_admin() && $query->is_search() && $query->is_main_query() ) {
			$query->set( 'post_type', 'post' );
		}
	}
	add_action( 'pre_get_posts', 'thessnest_limit_search_to_posts' );
endif;


/* ==========================================================================
   18. MODAL AJAX HANDLERS (Login, Register, Forgot Password)
   ========================================================================== */

/**
 * AJAX Login Handler
 */
add_action( 'wp_ajax_nopriv_thessnest_ajax_login', 'thessnest_ajax_login' );
function thessnest_ajax_login() {
	check_ajax_referer( 'thessnest-login-nonce', 'login_security' );

	$username = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
	$password = isset( $_POST['password'] ) ? $_POST['password'] : '';
	$remember = isset( $_POST['remember'] ) ? true : false;

	if ( empty( $username ) || empty( $password ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter both username and password.', 'thessnest' ) ) );
	}

	$creds = array(
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => $remember,
	);

	$user = wp_signon( $creds, is_ssl() );

	if ( is_wp_error( $user ) ) {
		wp_send_json_error( array( 'message' => $user->get_error_message() ) );
	}

	wp_send_json_success( array( 
		'message'  => __( 'Login successful! Redirecting...', 'thessnest' ),
		'redirect' => home_url( '/dashboard/' ) 
	) );
}

/**
 * AJAX Register Handler
 */
add_action( 'wp_ajax_nopriv_thessnest_ajax_register', 'thessnest_ajax_register' );
function thessnest_ajax_register() {
	check_ajax_referer( 'thessnest-register-nonce', 'register_security' );

	$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$username   = isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';
	$password   = isset( $_POST['password'] ) ? $_POST['password'] : '';
	$role       = isset( $_POST['role'] ) && in_array( $_POST['role'], array( 'tenant', 'landlord' ) ) ? sanitize_text_field( $_POST['role'] ) : 'tenant';
	
	if ( empty( $email ) || empty( $username ) || empty( $password ) || empty( $first_name ) || empty( $last_name ) ) {
		wp_send_json_error( array( 'message' => __( 'Please fill in all required fields.', 'thessnest' ) ) );
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid email address.', 'thessnest' ) ) );
	}

	if ( username_exists( $username ) ) {
		wp_send_json_error( array( 'message' => __( 'This username is already taken.', 'thessnest' ) ) );
	}

	if ( email_exists( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'This email is already registered.', 'thessnest' ) ) );
	}

	$user_id = wp_insert_user( array(
		'user_login' => $username,
		'user_email' => $email,
		'user_pass'  => $password,
		'first_name' => $first_name,
		'last_name  ' => $last_name,
		'role'       => $role,
	) );

	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array( 'message' => $user_id->get_error_message() ) );
	}

	// Auto-login after registration
	$creds = array(
		'user_login'    => $username,
		'user_password' => $password,
		'remember'      => true,
	);
	wp_signon( $creds, is_ssl() );

	wp_send_json_success( array( 
		'message'  => __( 'Registration successful! Redirecting...', 'thessnest' ),
		'redirect' => home_url( '/dashboard/' ) 
	) );
}

/**
 * AJAX Forgot Password Handler
 */
add_action( 'wp_ajax_nopriv_thessnest_ajax_forgot_password', 'thessnest_ajax_forgot_password' );
function thessnest_ajax_forgot_password() {
	check_ajax_referer( 'thessnest-forgot-nonce', 'forgot_security' );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( empty( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter your email address.', 'thessnest' ) ) );
	}

	$user = get_user_by( 'email', $email );

	if ( ! $user ) {
		// Generic message to prevent user enumeration
		wp_send_json_success( array( 'message' => __( 'If an account exists with this email, a reset link has been sent.', 'thessnest' ) ) );
	}

	$key = get_password_reset_key( $user );
	if ( is_wp_error( $key ) ) {
		wp_send_json_error( array( 'message' => __( 'Could not generate reset key. Please try again later.', 'thessnest' ) ) );
	}

	$reset_url = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' );
	
	$message = __( 'Someone requested a password reset for your account:', 'thessnest' ) . "\r\n\r\n";
	$message .= network_home_url( '/' ) . "\r\n\r\n";
	$message .= __( 'Username:', 'thessnest' ) . ' ' . $user->user_login . "\r\n\r\n";
	$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'thessnest' ) . "\r\n\r\n";
	$message .= __( 'To reset your password, visit the following address:', 'thessnest' ) . "\r\n\r\n";
	$message .= $reset_url . "\r\n";

	$subject = sprintf( __( '[%s] Password Reset', 'thessnest' ), wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) );

	if ( wp_mail( $email, $subject, $message ) ) {
		wp_send_json_success( array( 'message' => __( 'If an account exists with this email, a reset link has been sent.', 'thessnest' ) ) );
	} else {
		wp_send_json_error( array( 'message' => __( 'The email could not be sent. Please contact the site administrator.', 'thessnest' ) ) );
	}
}

