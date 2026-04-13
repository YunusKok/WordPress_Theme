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

		<header class="page-header" style="margin-bottom:var(--space-12);text-align:center;">
			<?php if ( is_home() && ! is_front_page() ) : ?>
				<h1 class="page-title" style="font-size:var(--font-size-4xl);color:var(--color-primary);"><?php single_post_title(); ?></h1>
			<?php elseif ( is_category() || is_tag() || is_archive() ) : ?>
				<?php the_archive_title( '<h1 class="page-title" style="font-size:var(--font-size-4xl);color:var(--color-primary);">', '</h1>' ); ?>
				<?php the_archive_description( '<div class="archive-description" style="color:var(--color-text-muted);margin-top:var(--space-4);">', '</div>' ); ?>
			<?php else : ?>
				<h1 class="page-title" style="font-size:var(--font-size-4xl);color:var(--color-primary);"><?php esc_html_e( 'Blog', 'thessnest' ); ?></h1>
			<?php endif; ?>
		</header>

		<div class="blog-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: var(--space-8);">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="background:var(--color-surface);border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-card);transition:transform 0.2s ease;">
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" class="post-thumbnail-link" style="display:block;height:220px;overflow:hidden;">
							<?php the_post_thumbnail( 'card-thumb', array( 'style' => 'width:100%;height:100%;object-fit:cover;' ) ); ?>
						</a>
					<?php endif; ?>
					
					<div class="post-content-wrap" style="padding: var(--space-6);">
						<header class="entry-header">
							<div class="entry-meta" style="font-size:var(--font-size-xs);color:var(--color-text-muted);margin-bottom:var(--space-2);text-transform:uppercase;letter-spacing:0.05em;">
								<?php echo get_the_date(); ?> • <?php the_category( ', ' ); ?>
							</div>
							<h2 class="card-title" style="margin-top:0;font-size:var(--font-size-lg);line-height:1.4;">
								<a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;"><?php the_title(); ?></a>
							</h2>
						</header>
						
						<div class="entry-summary" style="margin-top: var(--space-3); color: var(--color-text-muted); font-size: var(--font-size-sm); line-height:1.6;">
							<?php the_excerpt(); ?>
						</div>
						
						<div style="margin-top: var(--space-4);">
							<a href="<?php the_permalink(); ?>" style="color:var(--color-primary);font-weight:600;font-size:var(--font-size-sm);text-decoration:underline;"><?php esc_html_e( 'Read More', 'thessnest' ); ?></a>
						</div>
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
