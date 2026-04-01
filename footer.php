<?php
/**
 * ThessNest — Footer Template
 *
 * Renders the 4-column footer with About, Explore links,
 * Support links, Newsletter, and a copyright bar.
 * Reads dynamic values from Redux ThessNest Options.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

// ── Pull Redux options (with fallbacks) ──
$footer_show_widgets = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'footer_show_widgets', true )  : true;
$footer_columns      = function_exists( 'thessnest_opt' ) ? (int) thessnest_opt( 'footer_columns', 4 )   : 4;
$footer_style        = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'footer_style', 'dark' )      : 'dark';
$footer_copyright    = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'footer_copyright', '&copy; ' . date( 'Y' ) . ' ThessNest. All rights reserved.' ) : '&copy; ' . date( 'Y' ) . ' ThessNest. All rights reserved.';
$show_social         = function_exists( 'thessnest_opt' ) ? (bool) thessnest_opt( 'footer_social_media', true ) : true;
$footer_logo         = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'footer_logo', '' ) : '';

$social_facebook     = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'social_facebook', '' )  : '';
$social_instagram    = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'social_instagram', '' ) : '';
$social_twitter      = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'social_twitter', '' )   : '';
$social_whatsapp     = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'social_whatsapp', '' )  : '';
$social_linkedin     = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'social_linkedin', '' )  : '';
$site_description    = function_exists( 'thessnest_opt' ) ? thessnest_opt( 'site_description', __( 'Your trusted platform for mid-term housing in Thessaloniki.', 'thessnest' ) ) : __( 'Your trusted platform for mid-term housing in Thessaloniki.', 'thessnest' );

// ── Style map ──
$style_map = array(
	'default' => array( 'bg' => '#ffffff', 'color' => '#374151', 'heading' => '#1B2A4A', 'link' => '#64748b', 'link_hover' => '#2563eb', 'border' => '#e5e7eb', 'bottom_bg' => '#f9fafb' ),
	'dark'    => array( 'bg' => '#0f172a', 'color' => '#94a3b8', 'heading' => '#f1f5f9', 'link' => '#94a3b8', 'link_hover' => '#7dd3fc', 'border' => '#1e293b', 'bottom_bg' => '#020617' ),
	'minimal' => array( 'bg' => '#f8fafc', 'color' => '#64748b', 'heading' => '#334155', 'link' => '#64748b', 'link_hover' => '#2563eb', 'border' => '#e2e8f0', 'bottom_bg' => '#f1f5f9' ),
);
$s = isset( $style_map[ $footer_style ] ) ? $style_map[ $footer_style ] : $style_map['dark'];
?>

<!-- ===== SITE FOOTER ===== -->
<footer class="site-footer site-footer--<?php echo esc_attr( $footer_style ); ?>" role="contentinfo" style="background:<?php echo esc_attr( $s['bg'] ); ?>;color:<?php echo esc_attr( $s['color'] ); ?>;">

	<?php if ( $footer_show_widgets ) : ?>
	<div class="footer-grid container" style="display:grid;grid-template-columns:repeat(<?php echo esc_attr( $footer_columns ); ?>,1fr);gap:40px;padding:60px 24px 40px;">

		<?php
		// Check if ANY footer widget area has widgets
		$has_widgets = false;
		for ( $i = 1; $i <= $footer_columns; $i++ ) {
			if ( is_active_sidebar( 'footer-' . $i ) ) {
				$has_widgets = true;
				break;
			}
		}

		if ( $has_widgets ) :
			// ── Dynamic widget columns ──
			for ( $i = 1; $i <= $footer_columns; $i++ ) :
		?>
			<div class="footer-col">
				<?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
					<?php dynamic_sidebar( 'footer-' . $i ); ?>
				<?php endif; ?>
			</div>
		<?php
			endfor;
		else :
			// ── Fallback: hardcoded default footer when no widgets assigned ──
		?>
			<!-- Column 1: About -->
			<div class="footer-col footer-about">
				<?php
				$logo_url = '';
				if ( is_array( $footer_logo ) && ! empty( $footer_logo['url'] ) ) {
					$logo_url = $footer_logo['url'];
				}
				if ( $logo_url ) : ?>
					<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" style="max-height:40px;margin-bottom:16px;">
				<?php else : ?>
					<h4 style="color:<?php echo esc_attr( $s['heading'] ); ?>;"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h4>
				<?php endif; ?>
				<p><?php echo esc_html( $site_description ); ?></p>

				<?php if ( $show_social ) : ?>
				<div class="footer-social" style="display:flex;gap:12px;margin-top:16px;">
					<?php if ( $social_facebook ) : ?>
					<a href="<?php echo esc_url( $social_facebook ); ?>" aria-label="Facebook" target="_blank" rel="noopener" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
					<?php endif; ?>
					<?php if ( $social_instagram ) : ?>
					<a href="<?php echo esc_url( $social_instagram ); ?>" aria-label="Instagram" target="_blank" rel="noopener" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
					<?php endif; ?>
					<?php if ( $social_twitter ) : ?>
					<a href="<?php echo esc_url( $social_twitter ); ?>" aria-label="Twitter / X" target="_blank" rel="noopener" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
					<?php endif; ?>
					<?php if ( $social_whatsapp ) : ?>
					<a href="<?php echo esc_url( $social_whatsapp ); ?>" aria-label="WhatsApp" target="_blank" rel="noopener" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
					<?php endif; ?>
					<?php if ( $social_linkedin ) : ?>
					<a href="<?php echo esc_url( $social_linkedin ); ?>" aria-label="LinkedIn" target="_blank" rel="noopener" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>

			<?php if ( $footer_columns >= 2 ) : ?>
			<!-- Column 2: Explore -->
			<div class="footer-col">
				<h4 style="color:<?php echo esc_attr( $s['heading'] ); ?>;"><?php esc_html_e( 'Explore', 'thessnest' ); ?></h4>
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 1 ) ); ?>
				<?php else : ?>
				<ul>
					<li><a href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><?php esc_html_e( 'All Properties', 'thessnest' ); ?></a></li>
					<li><a href="#" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><?php esc_html_e( 'Neighborhoods', 'thessnest' ); ?></a></li>
					<li><a href="#" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><?php esc_html_e( 'Student Housing', 'thessnest' ); ?></a></li>
					<li><a href="#" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><?php esc_html_e( 'Nomad Stays', 'thessnest' ); ?></a></li>
				</ul>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<?php if ( $footer_columns >= 3 ) : ?>
			<!-- Column 3: Support -->
			<div class="footer-col">
				<h4 style="color:<?php echo esc_attr( $s['heading'] ); ?>;"><?php esc_html_e( 'Support', 'thessnest' ); ?></h4>
				<ul>
					<li><a href="#" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><?php esc_html_e( 'How It Works', 'thessnest' ); ?></a></li>
					<li><a href="#" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><?php esc_html_e( 'FAQs', 'thessnest' ); ?></a></li>
					<li><a href="#" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><?php esc_html_e( 'Contact Us', 'thessnest' ); ?></a></li>
					<li><a href="#" style="color:<?php echo esc_attr( $s['link'] ); ?>;"><?php esc_html_e( 'Privacy Policy', 'thessnest' ); ?></a></li>
				</ul>
			</div>
			<?php endif; ?>

			<?php if ( $footer_columns >= 4 ) : ?>
			<!-- Column 4: Newsletter -->
			<div class="footer-col">
				<h4 style="color:<?php echo esc_attr( $s['heading'] ); ?>;"><?php esc_html_e( 'Stay Updated', 'thessnest' ); ?></h4>
				<p style="font-size:0.875rem;margin-bottom:1rem;"><?php esc_html_e( 'Get the latest listings and housing tips delivered to your inbox.', 'thessnest' ); ?></p>
				<form class="newsletter-form" action="#" method="post">
					<?php wp_nonce_field( 'thessnest_newsletter', 'thessnest_newsletter_nonce' ); ?>
					<input type="email" name="newsletter_email" placeholder="<?php esc_attr_e( 'Your email', 'thessnest' ); ?>" required aria-label="<?php esc_attr_e( 'Email address', 'thessnest' ); ?>">
					<button type="submit"><?php esc_html_e( 'Subscribe', 'thessnest' ); ?></button>
				</form>
			</div>
			<?php endif; ?>

		<?php endif; // has_widgets ?>

	</div>
	<?php endif; // footer_show_widgets ?>

	<!-- Footer Bottom -->
	<div class="footer-bottom" style="background:<?php echo esc_attr( $s['bottom_bg'] ); ?>;border-top:1px solid <?php echo esc_attr( $s['border'] ); ?>;">
		<p><?php echo wp_kses_post( $footer_copyright ); ?></p>
	</div>
</footer>

<style>
.site-footer .footer-col h4 { color: <?php echo esc_attr( $s['heading'] ); ?>; }
.site-footer .footer-col a { color: <?php echo esc_attr( $s['link'] ); ?>; transition: color 0.2s; }
.site-footer .footer-col a:hover { color: <?php echo esc_attr( $s['link_hover'] ); ?>; }
.site-footer .footer-widget h4.widget-title { color: <?php echo esc_attr( $s['heading'] ); ?>; margin-bottom: 16px; }
@media (max-width: 768px) {
	.site-footer .footer-grid { grid-template-columns: 1fr !important; gap: 32px !important; }
}
@media (min-width: 769px) and (max-width: 1024px) {
	.site-footer .footer-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
</style>

<!-- Scroll to Top Button -->
<button class="scroll-to-top" id="scroll-to-top" aria-label="<?php esc_attr_e( 'Scroll to top', 'thessnest' ); ?>">
	<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"></polyline></svg>
</button>


<?php wp_footer(); ?>
</body>
</html>
