<?php
/**
 * ThessNest — Footer Template
 *
 * Renders the 4-column footer with About, Explore links,
 * Support links, Newsletter, and a copyright bar.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- ===== SITE FOOTER ===== -->
<footer class="site-footer" role="contentinfo">
	<div class="footer-grid container">

		<!-- Column 1: About -->
		<div class="footer-col footer-about">
			<h4><?php esc_html_e( 'ThessNest', 'thessnest' ); ?></h4>
			<p>
				<?php esc_html_e( 'Your trusted platform for mid-term housing in Thessaloniki. Built for Erasmus students, digital nomads, and expats looking for verified, comfortable living spaces.', 'thessnest' ); ?>
			</p>
			<div class="footer-social">
				<a href="#" aria-label="Facebook">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
				</a>
				<a href="#" aria-label="Instagram">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
				</a>
				<a href="#" aria-label="Twitter / X">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
				</a>
			</div>
		</div>

		<!-- Column 2: Explore -->
		<div class="footer-col">
			<h4><?php esc_html_e( 'Explore', 'thessnest' ); ?></h4>
			<ul>
				<li><a href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ); ?>"><?php esc_html_e( 'All Properties', 'thessnest' ); ?></a></li>
				<li><a href="#"><?php esc_html_e( 'Neighborhoods', 'thessnest' ); ?></a></li>
				<li><a href="#"><?php esc_html_e( 'Student Housing', 'thessnest' ); ?></a></li>
				<li><a href="#"><?php esc_html_e( 'Nomad Stays', 'thessnest' ); ?></a></li>
				<li><a href="#"><?php esc_html_e( 'Instant Book', 'thessnest' ); ?></a></li>
			</ul>
		</div>

		<!-- Column 3: Support -->
		<div class="footer-col">
			<h4><?php esc_html_e( 'Support', 'thessnest' ); ?></h4>
			<ul>
				<li><a href="#"><?php esc_html_e( 'How It Works', 'thessnest' ); ?></a></li>
				<li><a href="#"><?php esc_html_e( 'FAQs', 'thessnest' ); ?></a></li>
				<li><a href="#"><?php esc_html_e( 'Contact Us', 'thessnest' ); ?></a></li>
				<li><a href="#"><?php esc_html_e( 'Privacy Policy', 'thessnest' ); ?></a></li>
				<li><a href="#"><?php esc_html_e( 'Terms of Service', 'thessnest' ); ?></a></li>
			</ul>
		</div>

		<!-- Column 4: Newsletter -->
		<div class="footer-col">
			<h4><?php esc_html_e( 'Stay Updated', 'thessnest' ); ?></h4>
			<p style="font-size:0.875rem;margin-bottom:1rem;">
				<?php esc_html_e( 'Get the latest listings and housing tips delivered to your inbox.', 'thessnest' ); ?>
			</p>
			<form class="newsletter-form" action="#" method="post">
				<?php wp_nonce_field( 'thessnest_newsletter', 'thessnest_newsletter_nonce' ); ?>
				<input type="email" name="newsletter_email" placeholder="<?php esc_attr_e( 'Your email', 'thessnest' ); ?>" required aria-label="<?php esc_attr_e( 'Email address', 'thessnest' ); ?>">
				<button type="submit"><?php esc_html_e( 'Subscribe', 'thessnest' ); ?></button>
			</form>
		</div>

	</div>

	<!-- Footer Bottom -->
	<div class="footer-bottom">
		<p>
			&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php esc_html_e( 'ThessNest. All rights reserved.', 'thessnest' ); ?>
		</p>
	</div>
</footer>

<!-- Scroll to Top Button -->
<button class="scroll-to-top" id="scroll-to-top" aria-label="<?php esc_attr_e( 'Scroll to top', 'thessnest' ); ?>">
	<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"></polyline></svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
