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
	<?php
	$title   = thessnest_opt( '404_title', __( 'Page Not Found', 'thessnest' ) );
	$message = thessnest_opt( '404_message', __( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'thessnest' ) );
	$image   = thessnest_opt( '404_image', '' );
	?>
	<div style="max-width:600px;margin:0 auto;background:var(--color-surface);padding:var(--space-12) var(--space-8);border-radius:var(--radius-2xl);box-shadow:var(--shadow-md);border:1px solid var(--color-border);">
		<?php if ( is_array( $image ) && ! empty( $image['url'] ) ) : ?>
			<img src="<?php echo esc_url( $image['url'] ); ?>" alt="404" style="max-width:100%;height:auto;margin-bottom:var(--space-6);border-radius:var(--radius-lg);">
		<?php else : ?>
			<h1 style="font-size:var(--font-size-hero);color:var(--color-primary);margin-bottom:var(--space-4);">404</h1>
		<?php endif; ?>
		
		<h2 style="font-size:var(--font-size-2xl);color:var(--color-text);margin-bottom:var(--space-6);">
			<?php echo esc_html( $title ); ?>
		</h2>
		<p style="color:var(--color-text-muted);font-size:var(--font-size-lg);margin-bottom:var(--space-8);line-height:1.7;">
			<?php echo esc_html( $message ); ?>
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
