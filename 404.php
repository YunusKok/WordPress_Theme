<?php
/**
 * ThessNest — 404 Not Found Template
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main-content" role="main" class="container" style="padding:var(--space-20) var(--space-4);text-align:center;">
	<div style="max-width:600px;margin:0 auto;background:var(--color-surface);padding:var(--space-12) var(--space-8);border-radius:var(--radius-2xl);box-shadow:var(--shadow-md);border:1px solid var(--color-border);">
		<h1 style="font-size:var(--font-size-hero);color:var(--color-primary);margin-bottom:var(--space-4);">
			<?php esc_html_e( '404', 'thessnest' ); ?>
		</h1>
		<h2 style="font-size:var(--font-size-2xl);color:var(--color-text);margin-bottom:var(--space-6);">
			<?php esc_html_e( 'Oops! Page Not Found', 'thessnest' ); ?>
		</h2>
		<p style="color:var(--color-text-muted);font-size:var(--font-size-lg);margin-bottom:var(--space-8);line-height:1.7;">
			<?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'thessnest' ); ?>
		</p>
		
		<div style="margin-bottom:var(--space-8);">
			<?php get_search_form(); ?>
		</div>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary" style="padding:var(--space-4) var(--space-8);border-radius:var(--radius-full);">
			<?php esc_html_e( 'Back to Home', 'thessnest' ); ?>
		</a>
	</div>
</main>

<?php
get_footer();
