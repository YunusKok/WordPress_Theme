<?php
/**
 * ThessNest — Header Template
 *
 * Renders the site header: Logo, primary navigation, and action buttons.
 * Includes a mobile off-canvas drawer for smaller viewports.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="theme-color" content="#1B2A4A">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<?php
$header_style = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'header_style' ) : 'modern';
$header_classes = 'site-header header-style-' . esc_attr( $header_style );
?>
<!-- ===== SITE HEADER ===== -->
<header class="<?php echo esc_attr( $header_classes ); ?>" id="site-header" role="banner">
	<?php if ( function_exists( 'thessnest_output_topbar' ) ) { thessnest_output_topbar(); } ?>
	<div class="header-inner">

		<!-- Logo -->
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo" aria-label="<?php esc_attr_e( 'ThessNest Home', 'thessnest' ); ?>">
			<?php
			$logo_url = thessnest_get_logo_url();
			if ( $logo_url ) : ?>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" style="height:36px; width:auto;" />
			<?php else : ?>
				<!-- Inline SVG icon – house + location pin -->
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
					<polyline points="9 22 9 12 15 12 15 22"/>
				</svg>
				<span>Thess<span class="logo-accent">Nest</span></span>
			<?php endif; ?>
		</a>

		<!-- Primary Navigation (Desktop) -->
		<nav class="primary-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'thessnest' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location'  => 'primary',
				'container'       => false,
				'menu_class'      => 'primary-menu-list',
				'items_wrap'      => '<ul id="%1$s" class="%2$s" style="display:flex;gap:var(--space-6);list-style:none;margin:0;padding:0;align-items:center;">%3$s</ul>',
				'fallback_cb'     => 'thessnest_fallback_menu',
			) );
			?>
		</nav>

		<!-- Header Actions -->
		<div class="header-actions">

			<!-- Add Listing Button (outline) -->
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( home_url( '/add-listing/' ) ); ?>" class="btn btn-outline btn-add-listing">
			<?php else : ?>
				<a href="#" class="btn btn-outline btn-add-listing" data-modal-open="modal-register">
			<?php endif; ?>
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
				</svg>
				<span class="btn-text"><?php esc_html_e( 'Add Listing', 'thessnest' ); ?></span>
			</a>

			<!-- Sign In / Dashboard -->
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="btn-signin">
					<?php esc_html_e( 'Dashboard', 'thessnest' ); ?>
				</a>
			<?php else : ?>
				<a href="#" class="btn-signin" data-modal-open="modal-login">
					<?php esc_html_e( 'Sign In', 'thessnest' ); ?>
				</a>
			<?php endif; ?>

			<!-- Mobile Menu Toggle -->
			<button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="<?php esc_attr_e( 'Toggle Menu', 'thessnest' ); ?>" aria-expanded="false" aria-controls="mobile-nav-drawer">
				<span class="hamburger-icon">
					<span></span>
					<span></span>
					<span></span>
				</span>
			</button>
		</div>
	</div>
</header>

<!-- ===== MOBILE NAV DRAWER ===== -->
<div class="mobile-nav-overlay" id="mobile-nav-overlay" aria-hidden="true"></div>
<aside class="mobile-nav-drawer" id="mobile-nav-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'thessnest' ); ?>">
	<button class="mobile-nav-close" id="mobile-nav-close" aria-label="<?php esc_attr_e( 'Close Menu', 'thessnest' ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
		</svg>
	</button>

	<?php
	wp_nav_menu( array(
		'theme_location' => 'primary',
		'container'      => false,
		'depth'          => 1,
		'fallback_cb'    => 'thessnest_fallback_menu_mobile',
	) );
	?>

	<div class="mobile-nav-actions">
		<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( home_url( '/add-listing/' ) ); ?>" class="btn btn-outline"><?php esc_html_e( 'Add Listing', 'thessnest' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'Dashboard', 'thessnest' ); ?></a>
		<?php else : ?>
			<a href="#" class="btn btn-outline" data-modal-open="modal-register"><?php esc_html_e( 'Add Listing', 'thessnest' ); ?></a>
			<a href="#" class="btn btn-primary" data-modal-open="modal-login"><?php esc_html_e( 'Sign In', 'thessnest' ); ?></a>
		<?php endif; ?>
	</div>
</aside>
