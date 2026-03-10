<?php
/**
 * ThessNest — Admin Menu & Options Panel
 *
 * Creates a professional admin experience similar to Homey theme:
 * - "ThessNest" top-level menu with dashboard stats
 * - Sub-menus grouping all CPTs and taxonomies
 * - Tabbed "ThessNest Options" settings page
 * - Admin bar shortcut
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;


/* ==========================================================================
   1. REGISTER TOP-LEVEL MENU & SUB-MENUS
   ========================================================================== */

/**
 * Register admin menu pages.
 */
function thessnest_admin_menu() {

	// ── 1. Top-level menu: ThessNest Dashboard ──
	add_menu_page(
		__( 'ThessNest', 'thessnest' ),       // Page title
		__( 'ThessNest', 'thessnest' ),        // Menu title
		'manage_options',                       // Capability
		'thessnest-dashboard',                  // Menu slug
		'thessnest_dashboard_page',             // Callback
		'dashicons-admin-home',                 // Icon
		2                                       // Position (very top)
	);

	// ── 2. Sub-menu: ThessNest Options ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'ThessNest Options', 'thessnest' ),
		__( 'ThessNest Options', 'thessnest' ),
		'manage_options',
		'thessnest-options',
		'thessnest_options_page'
	);

	// ── 3. Sub-menu: Properties ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'All Properties', 'thessnest' ),
		__( 'Properties', 'thessnest' ),
		'edit_posts',
		'edit.php?post_type=property'
	);

	// ── 4. Sub-menu: Add New Property ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'Add New Property', 'thessnest' ),
		__( 'Add New Property', 'thessnest' ),
		'edit_posts',
		'post-new.php?post_type=property'
	);

	// ── 5. Sub-menu: Bookings ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'Bookings', 'thessnest' ),
		__( 'Bookings', 'thessnest' ),
		'edit_posts',
		'edit.php?post_type=thessnest_booking'
	);

	// ── 6. Sub-menu: Messages ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'Messages', 'thessnest' ),
		__( 'Messages', 'thessnest' ),
		'edit_posts',
		'edit.php?post_type=thessnest_message'
	);

	// ── 7. Sub-menu: Reviews (Comments) ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'Reviews', 'thessnest' ),
		__( 'Reviews', 'thessnest' ),
		'moderate_comments',
		'edit-comments.php'
	);

	// ── 8. Taxonomy sub-menus ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'Neighborhoods', 'thessnest' ),
		__( 'Neighborhoods', 'thessnest' ),
		'manage_categories',
		'edit-tags.php?taxonomy=neighborhood&post_type=property'
	);

	add_submenu_page(
		'thessnest-dashboard',
		__( 'Amenities', 'thessnest' ),
		__( 'Amenities', 'thessnest' ),
		'manage_categories',
		'edit-tags.php?taxonomy=amenity&post_type=property'
	);

	add_submenu_page(
		'thessnest-dashboard',
		__( 'Target Groups', 'thessnest' ),
		__( 'Target Groups', 'thessnest' ),
		'manage_categories',
		'edit-tags.php?taxonomy=target_group&post_type=property'
	);
}
add_action( 'admin_menu', 'thessnest_admin_menu' );


/**
 * Fix the parent file for taxonomy pages so ThessNest menu stays highlighted.
 */
function thessnest_fix_taxonomy_menu_highlight( $parent_file ) {
	global $current_screen;

	if ( ! empty( $current_screen->taxonomy ) ) {
		$tax_in_thessnest = array( 'neighborhood', 'amenity', 'target_group' );
		if ( in_array( $current_screen->taxonomy, $tax_in_thessnest, true ) ) {
			return 'thessnest-dashboard';
		}
	}

	// Keep ThessNest highlighted when editing properties, bookings, messages
	if ( ! empty( $current_screen->post_type ) ) {
		$cpt_in_thessnest = array( 'property', 'thessnest_booking', 'thessnest_message' );
		if ( in_array( $current_screen->post_type, $cpt_in_thessnest, true ) ) {
			return 'thessnest-dashboard';
		}
	}

	return $parent_file;
}
add_filter( 'parent_file', 'thessnest_fix_taxonomy_menu_highlight' );


/* ==========================================================================
   2. ADMIN BAR — ThessNest Options Shortcut
   ========================================================================== */

/**
 * Add "ThessNest Options" link to the admin bar (like Homey Options).
 */
function thessnest_admin_bar_menu( $wp_admin_bar ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$wp_admin_bar->add_node( array(
		'id'    => 'thessnest-options-bar',
		'title' => '<span class="ab-icon dashicons dashicons-admin-home" style="font-family:dashicons; font-size:20px; margin-right:4px; line-height:1.4;"></span>' . __( 'ThessNest Options', 'thessnest' ),
		'href'  => admin_url( 'admin.php?page=thessnest-options' ),
		'meta'  => array( 'title' => __( 'ThessNest Theme Settings', 'thessnest' ) ),
	) );
}
add_action( 'admin_bar_menu', 'thessnest_admin_bar_menu', 40 );


/* ==========================================================================
   3. DASHBOARD PAGE — Stats Overview
   ========================================================================== */

/**
 * Render the ThessNest Dashboard page with statistics cards.
 */
function thessnest_dashboard_page() {
	// Gather stats
	$property_count = wp_count_posts( 'property' );
	$total_properties = isset( $property_count->publish ) ? $property_count->publish : 0;

	$booking_count = wp_count_posts( 'thessnest_booking' );
	$total_bookings = isset( $booking_count->publish ) ? $booking_count->publish : 0;
	$pending_bookings = isset( $booking_count->pending ) ? $booking_count->pending : 0;

	$message_count = wp_count_posts( 'thessnest_message' );
	$total_messages = isset( $message_count->publish ) ? $message_count->publish : 0;

	$review_count = wp_count_comments();
	$total_reviews = isset( $review_count->approved ) ? $review_count->approved : 0;
	$pending_reviews = isset( $review_count->moderated ) ? $review_count->moderated : 0;

	$total_users = count_users();
	?>
	<div class="wrap thessnest-dashboard-wrap">

		<h1 style="display:flex; align-items:center; gap:10px; margin-bottom:24px;">
			<span class="dashicons dashicons-admin-home" style="font-size:32px; color:#2271b1;"></span>
			<?php esc_html_e( 'ThessNest Dashboard', 'thessnest' ); ?>
		</h1>

		<p style="font-size:14px; color:#646970; margin-bottom:24px;">
			<?php esc_html_e( 'Welcome to your ThessNest control centre. Here is an overview of your platform.', 'thessnest' ); ?>
		</p>

		<!-- Stats Grid -->
		<div class="thessnest-stats-grid">

			<!-- Properties -->
			<div class="thessnest-stat-card thessnest-stat-properties">
				<div class="thessnest-stat-icon">
					<span class="dashicons dashicons-building"></span>
				</div>
				<div class="thessnest-stat-content">
					<span class="thessnest-stat-number"><?php echo esc_html( $total_properties ); ?></span>
					<span class="thessnest-stat-label"><?php esc_html_e( 'Properties', 'thessnest' ); ?></span>
				</div>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=property' ) ); ?>" class="thessnest-stat-link">
					<?php esc_html_e( 'View All →', 'thessnest' ); ?>
				</a>
			</div>

			<!-- Bookings -->
			<div class="thessnest-stat-card thessnest-stat-bookings">
				<div class="thessnest-stat-icon">
					<span class="dashicons dashicons-calendar-alt"></span>
				</div>
				<div class="thessnest-stat-content">
					<span class="thessnest-stat-number"><?php echo esc_html( $total_bookings ); ?></span>
					<span class="thessnest-stat-label"><?php esc_html_e( 'Bookings', 'thessnest' ); ?></span>
				</div>
				<?php if ( $pending_bookings > 0 ) : ?>
					<span class="thessnest-stat-badge"><?php printf( esc_html__( '%d Pending', 'thessnest' ), $pending_bookings ); ?></span>
				<?php endif; ?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=thessnest_booking' ) ); ?>" class="thessnest-stat-link">
					<?php esc_html_e( 'View All →', 'thessnest' ); ?>
				</a>
			</div>

			<!-- Messages -->
			<div class="thessnest-stat-card thessnest-stat-messages">
				<div class="thessnest-stat-icon">
					<span class="dashicons dashicons-email-alt"></span>
				</div>
				<div class="thessnest-stat-content">
					<span class="thessnest-stat-number"><?php echo esc_html( $total_messages ); ?></span>
					<span class="thessnest-stat-label"><?php esc_html_e( 'Messages', 'thessnest' ); ?></span>
				</div>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=thessnest_message' ) ); ?>" class="thessnest-stat-link">
					<?php esc_html_e( 'View All →', 'thessnest' ); ?>
				</a>
			</div>

			<!-- Reviews -->
			<div class="thessnest-stat-card thessnest-stat-reviews">
				<div class="thessnest-stat-icon">
					<span class="dashicons dashicons-star-filled"></span>
				</div>
				<div class="thessnest-stat-content">
					<span class="thessnest-stat-number"><?php echo esc_html( $total_reviews ); ?></span>
					<span class="thessnest-stat-label"><?php esc_html_e( 'Reviews', 'thessnest' ); ?></span>
				</div>
				<?php if ( $pending_reviews > 0 ) : ?>
					<span class="thessnest-stat-badge"><?php printf( esc_html__( '%d Pending', 'thessnest' ), $pending_reviews ); ?></span>
				<?php endif; ?>
				<a href="<?php echo esc_url( admin_url( 'edit-comments.php' ) ); ?>" class="thessnest-stat-link">
					<?php esc_html_e( 'View All →', 'thessnest' ); ?>
				</a>
			</div>

			<!-- Users -->
			<div class="thessnest-stat-card thessnest-stat-users">
				<div class="thessnest-stat-icon">
					<span class="dashicons dashicons-groups"></span>
				</div>
				<div class="thessnest-stat-content">
					<span class="thessnest-stat-number"><?php echo esc_html( $total_users['total_users'] ); ?></span>
					<span class="thessnest-stat-label"><?php esc_html_e( 'Registered Users', 'thessnest' ); ?></span>
				</div>
				<a href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>" class="thessnest-stat-link">
					<?php esc_html_e( 'View All →', 'thessnest' ); ?>
				</a>
			</div>

		</div><!-- .thessnest-stats-grid -->

		<!-- Quick Actions -->
		<div class="thessnest-quick-actions">
			<h2><?php esc_html_e( 'Quick Actions', 'thessnest' ); ?></h2>
			<div class="thessnest-quick-actions-grid">
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=property' ) ); ?>" class="button button-primary button-hero">
					<span class="dashicons dashicons-plus-alt" style="margin-right:6px;"></span>
					<?php esc_html_e( 'Add New Property', 'thessnest' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=thessnest-options' ) ); ?>" class="button button-secondary button-hero">
					<span class="dashicons dashicons-admin-generic" style="margin-right:6px;"></span>
					<?php esc_html_e( 'Theme Settings', 'thessnest' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-secondary button-hero">
					<span class="dashicons dashicons-admin-appearance" style="margin-right:6px;"></span>
					<?php esc_html_e( 'Customize', 'thessnest' ); ?>
				</a>
			</div>
		</div>

	</div><!-- .wrap -->
	<?php
}


/* ==========================================================================
   4. THESSNEST OPTIONS PAGE — Tabbed Settings
   ========================================================================== */

/**
 * Register settings for the ThessNest Options page.
 */
function thessnest_register_settings() {
	// General
	register_setting( 'thessnest_options_general', 'thessnest_currency_symbol', array( 'default' => '€' ) );
	register_setting( 'thessnest_options_general', 'thessnest_currency_position', array( 'default' => 'before' ) );
	register_setting( 'thessnest_options_general', 'thessnest_default_language', array( 'default' => 'en' ) );

	// Logos
	register_setting( 'thessnest_options_logos', 'thessnest_logo_url' );
	register_setting( 'thessnest_options_logos', 'thessnest_logo_dark_url' );
	register_setting( 'thessnest_options_logos', 'thessnest_favicon_url' );

	// Header
	register_setting( 'thessnest_options_header', 'thessnest_sticky_header', array( 'default' => '1' ) );
	register_setting( 'thessnest_options_header', 'thessnest_header_style', array( 'default' => 'default' ) );
	register_setting( 'thessnest_options_header', 'thessnest_header_cta_text' );
	register_setting( 'thessnest_options_header', 'thessnest_header_cta_url' );

	// Booking
	register_setting( 'thessnest_options_booking', 'thessnest_min_stay', array( 'default' => '30' ) );
	register_setting( 'thessnest_options_booking', 'thessnest_max_stay', array( 'default' => '365' ) );
	register_setting( 'thessnest_options_booking', 'thessnest_deposit_rate', array( 'default' => '20' ) );
	register_setting( 'thessnest_options_booking', 'thessnest_booking_approval', array( 'default' => 'manual' ) );

	// Styling
	register_setting( 'thessnest_options_styling', 'thessnest_accent_color', array( 'default' => '#2563eb' ) );
	register_setting( 'thessnest_options_styling', 'thessnest_dark_mode', array( 'default' => '0' ) );

	// Contact
	register_setting( 'thessnest_options_contact', 'thessnest_opt_phone', array( 'default' => '+30 123 456 789' ) );
	register_setting( 'thessnest_options_contact', 'thessnest_opt_email', array( 'default' => 'hello@thessnest.com' ) );
	register_setting( 'thessnest_options_contact', 'thessnest_opt_instagram' );
	register_setting( 'thessnest_options_contact', 'thessnest_opt_whatsapp' );
	register_setting( 'thessnest_options_contact', 'thessnest_opt_facebook' );
	register_setting( 'thessnest_options_contact', 'thessnest_opt_twitter' );
}
add_action( 'admin_init', 'thessnest_register_settings' );


/**
 * Render the tabbed ThessNest Options page.
 */
function thessnest_options_page() {
	$tabs = array(
		'general' => array( 'label' => __( 'General', 'thessnest' ),  'icon' => 'dashicons-admin-settings' ),
		'logos'   => array( 'label' => __( 'Logos & Favicon', 'thessnest' ), 'icon' => 'dashicons-format-image' ),
		'header'  => array( 'label' => __( 'Header & Nav', 'thessnest' ), 'icon' => 'dashicons-menu' ),
		'booking' => array( 'label' => __( 'Booking', 'thessnest' ),  'icon' => 'dashicons-calendar-alt' ),
		'styling' => array( 'label' => __( 'Styling', 'thessnest' ), 'icon' => 'dashicons-art' ),
		'contact' => array( 'label' => __( 'Contact', 'thessnest' ), 'icon' => 'dashicons-phone' ),
	);

	$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
	if ( ! array_key_exists( $active_tab, $tabs ) ) {
		$active_tab = 'general';
	}

	// Determine option group for form
	$option_group = 'thessnest_options_' . $active_tab;
	?>
	<div class="wrap thessnest-options-wrap">

		<h1 style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
			<span class="dashicons dashicons-admin-home" style="font-size:32px; color:#2271b1;"></span>
			<?php esc_html_e( 'ThessNest Options', 'thessnest' ); ?>
		</h1>
		<p style="color:#646970; margin-bottom:20px;"><?php esc_html_e( 'Configure your ThessNest theme settings below.', 'thessnest' ); ?></p>

		<!-- Tabs Navigation -->
		<nav class="nav-tab-wrapper thessnest-tabs">
			<?php foreach ( $tabs as $slug => $tab ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=thessnest-options&tab=' . $slug ) ); ?>"
				   class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>">
					<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>" style="font-size:16px; margin-right:4px; line-height:1.8;"></span>
					<?php echo esc_html( $tab['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<!-- Settings Form -->
		<form method="post" action="options.php" class="thessnest-options-form">
			<?php
			settings_fields( $option_group );

			switch ( $active_tab ) :
				case 'general':
					thessnest_render_tab_general();
					break;
				case 'logos':
					thessnest_render_tab_logos();
					break;
				case 'header':
					thessnest_render_tab_header();
					break;
				case 'booking':
					thessnest_render_tab_booking();
					break;
				case 'styling':
					thessnest_render_tab_styling();
					break;
				case 'contact':
					thessnest_render_tab_contact();
					break;
			endswitch;

			submit_button( __( 'Save Settings', 'thessnest' ) );
			?>
		</form>

	</div><!-- .wrap -->
	<?php
}


/* ==========================================================================
   5. TAB RENDERERS
   ========================================================================== */

/** General Tab */
function thessnest_render_tab_general() {
	?>
	<table class="form-table thessnest-form-table">
		<tr>
			<th scope="row"><label for="thessnest_currency_symbol"><?php esc_html_e( 'Currency Symbol', 'thessnest' ); ?></label></th>
			<td>
				<input type="text" id="thessnest_currency_symbol" name="thessnest_currency_symbol"
					   value="<?php echo esc_attr( get_option( 'thessnest_currency_symbol', '€' ) ); ?>" class="regular-text" />
				<p class="description"><?php esc_html_e( 'The currency symbol shown on listings (e.g. €, $, £).', 'thessnest' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_currency_position"><?php esc_html_e( 'Currency Position', 'thessnest' ); ?></label></th>
			<td>
				<select id="thessnest_currency_position" name="thessnest_currency_position">
					<option value="before" <?php selected( get_option( 'thessnest_currency_position', 'before' ), 'before' ); ?>><?php esc_html_e( 'Before price (€100)', 'thessnest' ); ?></option>
					<option value="after"  <?php selected( get_option( 'thessnest_currency_position', 'before' ), 'after' );  ?>><?php esc_html_e( 'After price (100€)', 'thessnest' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_default_language"><?php esc_html_e( 'Default Language', 'thessnest' ); ?></label></th>
			<td>
				<select id="thessnest_default_language" name="thessnest_default_language">
					<option value="en" <?php selected( get_option( 'thessnest_default_language', 'en' ), 'en' ); ?>>English</option>
					<option value="tr" <?php selected( get_option( 'thessnest_default_language', 'en' ), 'tr' ); ?>>Türkçe</option>
					<option value="el" <?php selected( get_option( 'thessnest_default_language', 'en' ), 'el' ); ?>>Ελληνικά</option>
				</select>
			</td>
		</tr>
	</table>
	<?php
}

/** Logos & Favicon Tab */
function thessnest_render_tab_logos() {
	?>
	<table class="form-table thessnest-form-table">
		<tr>
			<th scope="row"><label for="thessnest_logo_url"><?php esc_html_e( 'Logo (Light)', 'thessnest' ); ?></label></th>
			<td>
				<input type="url" id="thessnest_logo_url" name="thessnest_logo_url"
					   value="<?php echo esc_attr( get_option( 'thessnest_logo_url', '' ) ); ?>" class="large-text" />
				<p class="description"><?php esc_html_e( 'URL of the logo used on a light background.', 'thessnest' ); ?></p>
				<?php $logo_url = get_option( 'thessnest_logo_url', '' ); if ( $logo_url ) : ?>
					<img src="<?php echo esc_url( $logo_url ); ?>" style="max-height:60px; margin-top:10px; border:1px solid #ddd; padding:4px; border-radius:4px;" />
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_logo_dark_url"><?php esc_html_e( 'Logo (Dark)', 'thessnest' ); ?></label></th>
			<td>
				<input type="url" id="thessnest_logo_dark_url" name="thessnest_logo_dark_url"
					   value="<?php echo esc_attr( get_option( 'thessnest_logo_dark_url', '' ) ); ?>" class="large-text" />
				<p class="description"><?php esc_html_e( 'URL of the logo used on a dark background (optional).', 'thessnest' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_favicon_url"><?php esc_html_e( 'Favicon', 'thessnest' ); ?></label></th>
			<td>
				<input type="url" id="thessnest_favicon_url" name="thessnest_favicon_url"
					   value="<?php echo esc_attr( get_option( 'thessnest_favicon_url', '' ) ); ?>" class="large-text" />
				<p class="description"><?php esc_html_e( 'URL of the site favicon (16x16 or 32x32 PNG recommended).', 'thessnest' ); ?></p>
			</td>
		</tr>
	</table>
	<?php
}

/** Header & Nav Tab */
function thessnest_render_tab_header() {
	?>
	<table class="form-table thessnest-form-table">
		<tr>
			<th scope="row"><label for="thessnest_sticky_header"><?php esc_html_e( 'Sticky Header', 'thessnest' ); ?></label></th>
			<td>
				<label>
					<input type="checkbox" id="thessnest_sticky_header" name="thessnest_sticky_header" value="1"
						<?php checked( get_option( 'thessnest_sticky_header', '1' ), '1' ); ?> />
					<?php esc_html_e( 'Enable sticky (fixed) header on scroll', 'thessnest' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_header_style"><?php esc_html_e( 'Header Style', 'thessnest' ); ?></label></th>
			<td>
				<select id="thessnest_header_style" name="thessnest_header_style">
					<option value="default" <?php selected( get_option( 'thessnest_header_style', 'default' ), 'default' ); ?>><?php esc_html_e( 'Default', 'thessnest' ); ?></option>
					<option value="transparent" <?php selected( get_option( 'thessnest_header_style', 'default' ), 'transparent' ); ?>><?php esc_html_e( 'Transparent (Hero overlay)', 'thessnest' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_header_cta_text"><?php esc_html_e( 'Header CTA Button Text', 'thessnest' ); ?></label></th>
			<td>
				<input type="text" id="thessnest_header_cta_text" name="thessnest_header_cta_text"
					   value="<?php echo esc_attr( get_option( 'thessnest_header_cta_text', '' ) ); ?>" class="regular-text" />
				<p class="description"><?php esc_html_e( 'e.g. "List Your Property" — leave empty to hide.', 'thessnest' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_header_cta_url"><?php esc_html_e( 'Header CTA Button URL', 'thessnest' ); ?></label></th>
			<td>
				<input type="url" id="thessnest_header_cta_url" name="thessnest_header_cta_url"
					   value="<?php echo esc_attr( get_option( 'thessnest_header_cta_url', '' ) ); ?>" class="large-text" />
			</td>
		</tr>
	</table>
	<?php
}

/** Booking Tab */
function thessnest_render_tab_booking() {
	?>
	<table class="form-table thessnest-form-table">
		<tr>
			<th scope="row"><label for="thessnest_min_stay"><?php esc_html_e( 'Minimum Stay (days)', 'thessnest' ); ?></label></th>
			<td>
				<input type="number" id="thessnest_min_stay" name="thessnest_min_stay" min="1" max="365"
					   value="<?php echo esc_attr( get_option( 'thessnest_min_stay', '30' ) ); ?>" class="small-text" />
				<p class="description"><?php esc_html_e( 'Minimum rental period in days.', 'thessnest' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_max_stay"><?php esc_html_e( 'Maximum Stay (days)', 'thessnest' ); ?></label></th>
			<td>
				<input type="number" id="thessnest_max_stay" name="thessnest_max_stay" min="1" max="730"
					   value="<?php echo esc_attr( get_option( 'thessnest_max_stay', '365' ) ); ?>" class="small-text" />
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_deposit_rate"><?php esc_html_e( 'Deposit Rate (%)', 'thessnest' ); ?></label></th>
			<td>
				<input type="number" id="thessnest_deposit_rate" name="thessnest_deposit_rate" min="0" max="100"
					   value="<?php echo esc_attr( get_option( 'thessnest_deposit_rate', '20' ) ); ?>" class="small-text" />
				<p class="description"><?php esc_html_e( 'Default deposit percentage of total rent.', 'thessnest' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_booking_approval"><?php esc_html_e( 'Booking Approval', 'thessnest' ); ?></label></th>
			<td>
				<select id="thessnest_booking_approval" name="thessnest_booking_approval">
					<option value="manual" <?php selected( get_option( 'thessnest_booking_approval', 'manual' ), 'manual' ); ?>><?php esc_html_e( 'Manual Approval', 'thessnest' ); ?></option>
					<option value="auto"   <?php selected( get_option( 'thessnest_booking_approval', 'manual' ), 'auto' );   ?>><?php esc_html_e( 'Auto Approve', 'thessnest' ); ?></option>
				</select>
			</td>
		</tr>
	</table>
	<?php
}

/** Styling Tab */
function thessnest_render_tab_styling() {
	?>
	<table class="form-table thessnest-form-table">
		<tr>
			<th scope="row"><label for="thessnest_accent_color"><?php esc_html_e( 'Accent Color', 'thessnest' ); ?></label></th>
			<td>
				<input type="color" id="thessnest_accent_color" name="thessnest_accent_color"
					   value="<?php echo esc_attr( get_option( 'thessnest_accent_color', '#2563eb' ) ); ?>" />
				<span style="margin-left:8px; color:#646970;"><?php echo esc_html( get_option( 'thessnest_accent_color', '#2563eb' ) ); ?></span>
				<p class="description"><?php esc_html_e( 'Main accent colour used across the theme.', 'thessnest' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_dark_mode"><?php esc_html_e( 'Dark Mode', 'thessnest' ); ?></label></th>
			<td>
				<label>
					<input type="checkbox" id="thessnest_dark_mode" name="thessnest_dark_mode" value="1"
						<?php checked( get_option( 'thessnest_dark_mode', '0' ), '1' ); ?> />
					<?php esc_html_e( 'Enable dark mode by default', 'thessnest' ); ?>
				</label>
			</td>
		</tr>
	</table>
	<?php
}

/** Contact Tab */
function thessnest_render_tab_contact() {
	?>
	<table class="form-table thessnest-form-table">
		<tr>
			<th scope="row"><label for="thessnest_opt_phone"><?php esc_html_e( 'Phone Number', 'thessnest' ); ?></label></th>
			<td><input type="text" id="thessnest_opt_phone" name="thessnest_opt_phone"
					   value="<?php echo esc_attr( get_option( 'thessnest_opt_phone', '+30 123 456 789' ) ); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_opt_email"><?php esc_html_e( 'Email Address', 'thessnest' ); ?></label></th>
			<td><input type="email" id="thessnest_opt_email" name="thessnest_opt_email"
					   value="<?php echo esc_attr( get_option( 'thessnest_opt_email', 'hello@thessnest.com' ) ); ?>" class="regular-text" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_opt_instagram"><?php esc_html_e( 'Instagram URL', 'thessnest' ); ?></label></th>
			<td><input type="url" id="thessnest_opt_instagram" name="thessnest_opt_instagram"
					   value="<?php echo esc_attr( get_option( 'thessnest_opt_instagram', '' ) ); ?>" class="large-text" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_opt_whatsapp"><?php esc_html_e( 'WhatsApp URL', 'thessnest' ); ?></label></th>
			<td><input type="url" id="thessnest_opt_whatsapp" name="thessnest_opt_whatsapp"
					   value="<?php echo esc_attr( get_option( 'thessnest_opt_whatsapp', '' ) ); ?>" class="large-text" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_opt_facebook"><?php esc_html_e( 'Facebook URL', 'thessnest' ); ?></label></th>
			<td><input type="url" id="thessnest_opt_facebook" name="thessnest_opt_facebook"
					   value="<?php echo esc_attr( get_option( 'thessnest_opt_facebook', '' ) ); ?>" class="large-text" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="thessnest_opt_twitter"><?php esc_html_e( 'Twitter / X URL', 'thessnest' ); ?></label></th>
			<td><input type="url" id="thessnest_opt_twitter" name="thessnest_opt_twitter"
					   value="<?php echo esc_attr( get_option( 'thessnest_opt_twitter', '' ) ); ?>" class="large-text" /></td>
		</tr>
	</table>
	<?php
}


/* ==========================================================================
   6. ADMIN CSS — Dashboard & Options Styling
   ========================================================================== */

/**
 * Enqueue admin styles for ThessNest pages.
 */
function thessnest_admin_styles( $hook ) {
	// Only load on our pages
	$our_pages = array(
		'toplevel_page_thessnest-dashboard',
		'thessnest_page_thessnest-options',
	);

	// Also load on CPT pages for consistent branding
	global $current_screen;
	$is_thessnest_page = in_array( $hook, $our_pages, true );

	if ( ! $is_thessnest_page ) {
		// Check if it's a ThessNest CPT screen
		if ( isset( $current_screen->post_type ) ) {
			$cpts = array( 'property', 'thessnest_booking', 'thessnest_message' );
			$is_thessnest_page = in_array( $current_screen->post_type, $cpts, true );
		}
	}

	// Always load the minimal menu styling
	wp_add_inline_style( 'wp-admin', thessnest_get_admin_menu_css() );

	if ( $is_thessnest_page ) {
		wp_add_inline_style( 'wp-admin', thessnest_get_admin_page_css() );
	}
}
add_action( 'admin_enqueue_scripts', 'thessnest_admin_styles' );


/**
 * CSS for the admin menu branding.
 */
function thessnest_get_admin_menu_css() {
	return '
		/* ThessNest menu icon colour */
		#toplevel_page_thessnest-dashboard .wp-menu-image::before {
			color: #56c5f0 !important;
		}
		#toplevel_page_thessnest-dashboard.current .wp-menu-image::before,
		#toplevel_page_thessnest-dashboard:hover .wp-menu-image::before {
			color: #fff !important;
		}
		/* Subtle separator above ThessNest menu */
		#toplevel_page_thessnest-dashboard {
			border-top: 1px solid rgba(255,255,255,.08);
			margin-top: 0;
		}
	';
}


/**
 * CSS for dashboard and options pages.
 */
function thessnest_get_admin_page_css() {
	return '
		/* ── Stats Grid ── */
		.thessnest-stats-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
			gap: 16px;
			margin-bottom: 32px;
		}
		.thessnest-stat-card {
			background: #fff;
			border: 1px solid #dcdcde;
			border-radius: 8px;
			padding: 20px;
			position: relative;
			transition: box-shadow 0.2s, transform 0.2s;
		}
		.thessnest-stat-card:hover {
			box-shadow: 0 4px 12px rgba(0,0,0,.08);
			transform: translateY(-2px);
		}
		.thessnest-stat-icon {
			width: 48px;
			height: 48px;
			border-radius: 10px;
			display: flex;
			align-items: center;
			justify-content: center;
			margin-bottom: 12px;
		}
		.thessnest-stat-icon .dashicons {
			font-size: 24px;
			color: #fff;
			width: 24px;
			height: 24px;
		}
		.thessnest-stat-properties .thessnest-stat-icon { background: #2563eb; }
		.thessnest-stat-bookings  .thessnest-stat-icon { background: #059669; }
		.thessnest-stat-messages  .thessnest-stat-icon { background: #7c3aed; }
		.thessnest-stat-reviews   .thessnest-stat-icon { background: #d97706; }
		.thessnest-stat-users     .thessnest-stat-icon { background: #dc2626; }

		.thessnest-stat-content {
			display: flex;
			flex-direction: column;
		}
		.thessnest-stat-number {
			font-size: 28px;
			font-weight: 700;
			color: #1d2327;
			line-height: 1.2;
		}
		.thessnest-stat-label {
			font-size: 13px;
			color: #646970;
			margin-top: 2px;
		}
		.thessnest-stat-badge {
			position: absolute;
			top: 12px;
			right: 12px;
			background: #fcf0e3;
			color: #9a6700;
			font-size: 11px;
			font-weight: 600;
			padding: 2px 8px;
			border-radius: 10px;
		}
		.thessnest-stat-link {
			display: inline-block;
			margin-top: 12px;
			font-size: 13px;
			font-weight: 500;
			text-decoration: none;
			color: #2271b1;
		}
		.thessnest-stat-link:hover {
			color: #135e96;
		}

		/* ── Quick Actions ── */
		.thessnest-quick-actions {
			background: #fff;
			border: 1px solid #dcdcde;
			border-radius: 8px;
			padding: 20px 24px;
		}
		.thessnest-quick-actions h2 {
			font-size: 16px;
			margin: 0 0 16px;
		}
		.thessnest-quick-actions-grid {
			display: flex;
			gap: 12px;
			flex-wrap: wrap;
		}
		.thessnest-quick-actions-grid .button-hero {
			display: inline-flex !important;
			align-items: center;
			font-size: 14px !important;
			padding: 8px 20px !important;
			height: auto !important;
			line-height: 1.6 !important;
		}

		/* ── Options Tabs ── */
		.thessnest-tabs .nav-tab {
			display: inline-flex;
			align-items: center;
		}
		.thessnest-options-form {
			background: #fff;
			border: 1px solid #dcdcde;
			border-top: none;
			border-radius: 0 0 8px 8px;
			padding: 0 24px 24px;
		}
		.thessnest-form-table th {
			font-weight: 600;
		}
	';
}
