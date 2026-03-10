<?php
/**
 * ThessNest — Admin Menu & Dashboard
 *
 * Creates a professional admin experience:
 * - "ThessNest" top-level menu with dashboard stats
 * - Sub-menus grouping all CPTs and taxonomies
 * - Admin bar shortcut to ThessNest Options (Redux)
 *
 * The theme options panel itself is handled by Redux Framework
 * via inc/redux-config.php — this file only handles the menu
 * structure and dashboard page.
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
		__( 'ThessNest', 'thessnest' ),
		__( 'ThessNest', 'thessnest' ),
		'manage_options',
		'thessnest-dashboard',
		'thessnest_dashboard_page',
		'dashicons-admin-home',
		2
	);

	// ── 2. Sub-menu: Properties ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'All Properties', 'thessnest' ),
		__( 'Properties', 'thessnest' ),
		'edit_posts',
		'edit.php?post_type=property'
	);

	// ── 3. Sub-menu: Add New Property ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'Add New Property', 'thessnest' ),
		__( 'Add New Property', 'thessnest' ),
		'edit_posts',
		'post-new.php?post_type=property'
	);

	// ── 4. Sub-menu: Bookings ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'Bookings', 'thessnest' ),
		__( 'Bookings', 'thessnest' ),
		'edit_posts',
		'edit.php?post_type=thessnest_booking'
	);

	// ── 5. Sub-menu: Messages ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'Messages', 'thessnest' ),
		__( 'Messages', 'thessnest' ),
		'edit_posts',
		'edit.php?post_type=thessnest_message'
	);

	// ── 6. Sub-menu: Reviews (Comments) ──
	add_submenu_page(
		'thessnest-dashboard',
		__( 'Reviews', 'thessnest' ),
		__( 'Reviews', 'thessnest' ),
		'moderate_comments',
		'edit-comments.php'
	);

	// ── 7. Taxonomy sub-menus ──
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
 * Add "ThessNest Options" link to the admin bar.
 */
function thessnest_admin_bar_menu( $wp_admin_bar ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Point to Redux options page if Redux is active, else to Customizer
	$options_url = class_exists( 'Redux' )
		? admin_url( 'admin.php?page=thessnest-options' )
		: admin_url( 'customize.php' );

	$wp_admin_bar->add_node( array(
		'id'    => 'thessnest-options-bar',
		'title' => '<span class="ab-icon dashicons dashicons-admin-home" style="font-family:dashicons; font-size:20px; margin-right:4px; line-height:1.4;"></span>' . __( 'ThessNest Options', 'thessnest' ),
		'href'  => $options_url,
		'meta'  => array( 'title' => __( 'ThessNest Theme Settings', 'thessnest' ) ),
	) );
}
add_action( 'admin_bar_menu', 'thessnest_admin_bar_menu', 40 );


// Replaced with TGM Plugin Activation


/* ==========================================================================
   4. DASHBOARD PAGE — Stats Overview
   ========================================================================== */

/**
 * Render the ThessNest Dashboard page with statistics cards.
 */
function thessnest_dashboard_page() {
	$property_count   = wp_count_posts( 'property' );
	$total_properties = isset( $property_count->publish ) ? $property_count->publish : 0;

	$booking_count    = wp_count_posts( 'thessnest_booking' );
	$total_bookings   = isset( $booking_count->publish ) ? $booking_count->publish : 0;
	$pending_bookings = isset( $booking_count->pending ) ? $booking_count->pending : 0;

	$message_count    = wp_count_posts( 'thessnest_message' );
	$total_messages   = isset( $message_count->publish ) ? $message_count->publish : 0;

	$review_count     = wp_count_comments();
	$total_reviews    = isset( $review_count->approved ) ? $review_count->approved : 0;
	$pending_reviews  = isset( $review_count->moderated ) ? $review_count->moderated : 0;

	$total_users      = count_users();
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

			<div class="thessnest-stat-card thessnest-stat-properties">
				<div class="thessnest-stat-icon"><span class="dashicons dashicons-building"></span></div>
				<div class="thessnest-stat-content">
					<span class="thessnest-stat-number"><?php echo esc_html( $total_properties ); ?></span>
					<span class="thessnest-stat-label"><?php esc_html_e( 'Properties', 'thessnest' ); ?></span>
				</div>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=property' ) ); ?>" class="thessnest-stat-link"><?php esc_html_e( 'View All →', 'thessnest' ); ?></a>
			</div>

			<div class="thessnest-stat-card thessnest-stat-bookings">
				<div class="thessnest-stat-icon"><span class="dashicons dashicons-calendar-alt"></span></div>
				<div class="thessnest-stat-content">
					<span class="thessnest-stat-number"><?php echo esc_html( $total_bookings ); ?></span>
					<span class="thessnest-stat-label"><?php esc_html_e( 'Bookings', 'thessnest' ); ?></span>
				</div>
				<?php if ( $pending_bookings > 0 ) : ?>
					<span class="thessnest-stat-badge"><?php printf( esc_html__( '%d Pending', 'thessnest' ), $pending_bookings ); ?></span>
				<?php endif; ?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=thessnest_booking' ) ); ?>" class="thessnest-stat-link"><?php esc_html_e( 'View All →', 'thessnest' ); ?></a>
			</div>

			<div class="thessnest-stat-card thessnest-stat-messages">
				<div class="thessnest-stat-icon"><span class="dashicons dashicons-email-alt"></span></div>
				<div class="thessnest-stat-content">
					<span class="thessnest-stat-number"><?php echo esc_html( $total_messages ); ?></span>
					<span class="thessnest-stat-label"><?php esc_html_e( 'Messages', 'thessnest' ); ?></span>
				</div>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=thessnest_message' ) ); ?>" class="thessnest-stat-link"><?php esc_html_e( 'View All →', 'thessnest' ); ?></a>
			</div>

			<div class="thessnest-stat-card thessnest-stat-reviews">
				<div class="thessnest-stat-icon"><span class="dashicons dashicons-star-filled"></span></div>
				<div class="thessnest-stat-content">
					<span class="thessnest-stat-number"><?php echo esc_html( $total_reviews ); ?></span>
					<span class="thessnest-stat-label"><?php esc_html_e( 'Reviews', 'thessnest' ); ?></span>
				</div>
				<?php if ( $pending_reviews > 0 ) : ?>
					<span class="thessnest-stat-badge"><?php printf( esc_html__( '%d Pending', 'thessnest' ), $pending_reviews ); ?></span>
				<?php endif; ?>
				<a href="<?php echo esc_url( admin_url( 'edit-comments.php' ) ); ?>" class="thessnest-stat-link"><?php esc_html_e( 'View All →', 'thessnest' ); ?></a>
			</div>

			<div class="thessnest-stat-card thessnest-stat-users">
				<div class="thessnest-stat-icon"><span class="dashicons dashicons-groups"></span></div>
				<div class="thessnest-stat-content">
					<span class="thessnest-stat-number"><?php echo esc_html( $total_users['total_users'] ); ?></span>
					<span class="thessnest-stat-label"><?php esc_html_e( 'Registered Users', 'thessnest' ); ?></span>
				</div>
				<a href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>" class="thessnest-stat-link"><?php esc_html_e( 'View All →', 'thessnest' ); ?></a>
			</div>

		</div>

		<!-- Quick Actions -->
		<div class="thessnest-quick-actions">
			<h2><?php esc_html_e( 'Quick Actions', 'thessnest' ); ?></h2>
			<div class="thessnest-quick-actions-grid">
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=property' ) ); ?>" class="button button-primary button-hero">
					<span class="dashicons dashicons-plus-alt" style="margin-right:6px;"></span>
					<?php esc_html_e( 'Add New Property', 'thessnest' ); ?>
				</a>
				<?php if ( class_exists( 'Redux' ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=thessnest-options' ) ); ?>" class="button button-secondary button-hero">
						<span class="dashicons dashicons-admin-generic" style="margin-right:6px;"></span>
						<?php esc_html_e( 'Theme Settings', 'thessnest' ); ?>
					</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-secondary button-hero">
					<span class="dashicons dashicons-admin-appearance" style="margin-right:6px;"></span>
					<?php esc_html_e( 'Customize', 'thessnest' ); ?>
				</a>
			</div>
			
			<div style="margin-top:24px; padding:20px; background:#fff; border-left:4px solid #46b450; box-shadow:0 1px 1px rgba(0,0,0,.04);">
				<h3 style="margin-top:0;"><?php esc_html_e( '🚀 One-Click Theme Setup (Optional)', 'thessnest' ); ?></h3>
				<p style="color:#50575e;">
					<?php esc_html_e( 'If you are installing this theme on a fresh WordPress install, click the button below to automatically generate the required pages (Home, Dashboard, Add Listing), create the main menu, set up taxonomies, and import sample properties.', 'thessnest' ); ?>
				</p>
				<form method="post" action="" style="margin-top:15px;">
					<?php wp_nonce_field( 'thessnest_demo_setup', 'thessnest_demo_nonce' ); ?>
					<input type="hidden" name="thessnest_run_setup" value="1">
					<button type="submit" class="button button-primary button-hero" onclick="return confirm('<?php esc_attr_e( 'This will create pages, menus, and dummy properties. Proceed?', 'thessnest' ); ?>');">
						<?php esc_html_e( 'Run Auto-Setup', 'thessnest' ); ?>
					</button>
				</form>
				<?php if ( isset( $_GET['thessnest_setup'] ) && $_GET['thessnest_setup'] === 'success' ) : ?>
					<div class="notice notice-success is-dismissible" style="margin:15px 0 0 0;">
						<p><strong><?php esc_html_e( 'Success!', 'thessnest' ); ?></strong> <?php esc_html_e( 'Essential pages, navigation menu, taxonomies, and dummy properties have been successfully created. Your theme is now ready to use.', 'thessnest' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>

	</div>
	<?php
}


/* ==========================================================================
   5. ADMIN CSS — Dashboard Styling
   ========================================================================== */

function thessnest_admin_styles( $hook ) {
	// Always load menu branding
	wp_add_inline_style( 'wp-admin', '
		#toplevel_page_thessnest-dashboard .wp-menu-image::before { color: #56c5f0 !important; }
		#toplevel_page_thessnest-dashboard.current .wp-menu-image::before,
		#toplevel_page_thessnest-dashboard:hover .wp-menu-image::before { color: #fff !important; }
		#toplevel_page_thessnest-dashboard { border-top: 1px solid rgba(255,255,255,.08); }
	' );

	// Dashboard page styles
	if ( $hook === 'toplevel_page_thessnest-dashboard' ) {
		wp_add_inline_style( 'wp-admin', '
			.thessnest-stats-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px; margin-bottom:32px; }
			.thessnest-stat-card { background:#fff; border:1px solid #dcdcde; border-radius:8px; padding:20px; position:relative; transition:box-shadow .2s,transform .2s; }
			.thessnest-stat-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.08); transform:translateY(-2px); }
			.thessnest-stat-icon { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:12px; }
			.thessnest-stat-icon .dashicons { font-size:24px; color:#fff; width:24px; height:24px; }
			.thessnest-stat-properties .thessnest-stat-icon { background:#2563eb; }
			.thessnest-stat-bookings  .thessnest-stat-icon { background:#059669; }
			.thessnest-stat-messages  .thessnest-stat-icon { background:#7c3aed; }
			.thessnest-stat-reviews   .thessnest-stat-icon { background:#d97706; }
			.thessnest-stat-users     .thessnest-stat-icon { background:#dc2626; }
			.thessnest-stat-content { display:flex; flex-direction:column; }
			.thessnest-stat-number { font-size:28px; font-weight:700; color:#1d2327; line-height:1.2; }
			.thessnest-stat-label { font-size:13px; color:#646970; margin-top:2px; }
			.thessnest-stat-badge { position:absolute; top:12px; right:12px; background:#fcf0e3; color:#9a6700; font-size:11px; font-weight:600; padding:2px 8px; border-radius:10px; }
			.thessnest-stat-link { display:inline-block; margin-top:12px; font-size:13px; font-weight:500; text-decoration:none; color:#2271b1; }
			.thessnest-stat-link:hover { color:#135e96; }
			.thessnest-quick-actions { background:#fff; border:1px solid #dcdcde; border-radius:8px; padding:20px 24px; }
			.thessnest-quick-actions h2 { font-size:16px; margin:0 0 16px; }
			.thessnest-quick-actions-grid { display:flex; gap:12px; flex-wrap:wrap; }
			.thessnest-quick-actions-grid .button-hero { display:inline-flex!important; align-items:center; font-size:14px!important; padding:8px 20px!important; height:auto!important; line-height:1.6!important; }
		' );
	}
}
add_action( 'admin_enqueue_scripts', 'thessnest_admin_styles' );
