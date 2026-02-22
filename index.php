<?php
/**
 * ThessNest — Index Template (Fallback)
 *
 * Required by WordPress. Falls back to a simple loop
 * or redirects to the front page.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main-content" class="container" role="main" style="padding:var(--space-16) var(--space-4);">

	<?php if ( have_posts() ) : ?>

		<div class="property-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'property-card' ); ?> style="padding: var(--space-6);">
					<h2 class="card-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h2>
					<div style="margin-top: var(--space-3); color: var(--color-text-muted); font-size: var(--font-size-sm);">
						<?php the_excerpt(); ?>
					</div>
				</article>
				<?php
			endwhile;
			?>
		</div>

		<div class="pagination">
			<?php
			the_posts_pagination( array(
				'mid_size'  => 2,
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
			) );
			?>
		</div>

	<?php else : ?>

		<div class="text-center">
			<p class="text-muted"><?php esc_html_e( 'No content found.', 'thessnest' ); ?></p>
		</div>

	<?php endif; ?>

</main>

<?php get_footer(); ?>
