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

require_once THESSNEST_DIR . '/inc/core-functionality.php';
require_once THESSNEST_DIR . '/inc/admin-map-meta.php';
require_once THESSNEST_DIR . '/inc/admin-front-page.php';
require_once THESSNEST_DIR . '/inc/seo-tags.php';
require_once THESSNEST_DIR . '/inc/ajax-favorites.php';
require_once THESSNEST_DIR . '/inc/ajax-inquiry.php';
require_once THESSNEST_DIR . '/inc/user-roles.php';
require_once THESSNEST_DIR . '/inc/ajax-filter.php';
require_once THESSNEST_DIR . '/inc/ajax-add-listing.php';
require_once THESSNEST_DIR . '/inc/reviews-ratings.php';
require_once THESSNEST_DIR . '/inc/ajax-dashboard.php';
require_once THESSNEST_DIR . '/inc/ajax-messaging.php';
require_once THESSNEST_DIR . '/inc/ajax-booking.php';
require_once THESSNEST_DIR . '/inc/ajax-kyc.php';
require_once THESSNEST_DIR . '/inc/accommodation-proof.php';




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

	/* === Leaflet CSS & JS for Property Archives (Map) === */
	if ( is_post_type_archive( 'property' ) || is_tax( 'neighborhood' ) || is_tax( 'amenity' ) || is_tax( 'target_group' ) ) {
		wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4' );
		wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true );
	}
}
add_action( 'wp_enqueue_scripts', 'thessnest_enqueue_assets' );


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
	if ( function_exists('pll_the_languages') || function_exists('icl_get_languages') ) {
		return;
	}
	
	$locale = get_locale(); // e.g. en_US, tr_TR, el
	$lang = substr($locale, 0, 2); // e.g. en, tr, el
	
	$url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	
	echo '<link rel="alternate" hreflang="' . esc_attr($lang) . '" href="' . esc_url($url) . '" />' . "\n";
	echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($url) . '" />' . "\n";
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


/**
 * Format a price with euro symbol.
 *
 * @param  string|int $price Raw price value.
 * @return string     Formatted price (e.g., "€450").
 */
function thessnest_format_price( $price ) {
	if ( empty( $price ) ) {
		return '';
	}

	return '€' . number_format( (float) $price, 0, ',', '.' );
}


/* ==========================================================================
   6. WIDGETS — Register Sidebar
   ========================================================================== */

/**
 * Register a footer widget area (optional).
 */
function thessnest_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widgets', 'thessnest' ),
		'id'            => 'footer-widgets',
		'description'   => esc_html__( 'Widgets displayed in the footer area.', 'thessnest' ),
		'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
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
 */
function thessnest_rewrite_flush() {
	thessnest_register_property_cpt();
	thessnest_register_neighborhood_taxonomy();
	thessnest_register_amenity_taxonomy();
	thessnest_register_target_group_taxonomy();
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
		<h3 style="margin-top:12px;">🏠 ThessNest — <?php esc_html_e( 'Eski Tema Sayfaları Tespit Edildi', 'thessnest' ); ?></h3>
		<p>
			<?php esc_html_e( 'Önceki temanızdan kalan aşağıdaki sayfalar ThessNest ile uyumsuz olabilir. Bu sayfalar page builder (Elementor, WPBakery vb.) içeriği barındırıyor ve bozuk görünebilir.', 'thessnest' ); ?>
		</p>
		<ul style="list-style:disc;margin-left:20px;">
			<?php foreach ( $old_pages as $p ) : ?>
				<li>
					<strong><?php echo esc_html( $p['title'] ); ?></strong>
					<code>/<?php echo esc_html( $p['slug'] ); ?>/</code>
					— <a href="<?php echo esc_url( get_edit_post_link( $p['id'] ) ); ?>"><?php esc_html_e( 'Düzenle', 'thessnest' ); ?></a>
					| <a href="<?php echo esc_url( get_delete_post_link( $p['id'] ) ); ?>" style="color:#d63638;"><?php esc_html_e( 'Çöpe Taşı', 'thessnest' ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
		<p>
			<a href="<?php echo esc_url( $pages_url ); ?>" class="button button-primary"><?php esc_html_e( 'Tüm Sayfaları Görüntüle', 'thessnest' ); ?></a>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=thessnest_dismiss_old_pages' ), 'thessnest_dismiss_notice' ) ); ?>" class="button" style="margin-left:8px;"><?php esc_html_e( 'Bu Uyarıyı Kapat', 'thessnest' ); ?></a>
		</p>
		<p style="color:#666;font-size:12px;">
			<?php esc_html_e( 'İpucu: ThessNest ile kullanacağınız sayfaları Kurulum Rehberine (setup_instructions.md) göre sıfırdan oluşturun.', 'thessnest' ); ?>
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
		wp_die( esc_html__( 'Yetkisiz işlem.', 'thessnest' ) );
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
