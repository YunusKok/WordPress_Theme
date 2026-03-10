<?php
/**
 * ThessNest — Search Results Template
 *
 * Displays search results using the theme's native design system.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main-content" role="main">

	<!-- Search Header -->
	<section style="background:var(--color-surface); padding:var(--space-12) var(--space-4); border-bottom:1px solid var(--color-border); text-align:center;">
		<div class="container">
			<h1 style="font-size:var(--font-size-2xl); font-weight:800; color:var(--color-primary); margin-bottom:var(--space-3);">
				<?php
				/* translators: %s: search query. */
				printf( esc_html__( 'Search Results for: "%s"', 'thessnest' ), '<span style="color:var(--color-accent);">' . esc_html( get_search_query() ) . '</span>' );
				?>
			</h1>
			<p style="color:var(--color-text-muted); font-size:var(--font-size-base);">
				<?php
				global $wp_query;
				printf(
					/* translators: %d: number of results. */
					esc_html( _n( '%d result found', '%d results found', $wp_query->found_posts, 'thessnest' ) ),
					intval( $wp_query->found_posts )
				);
				?>
			</p>
		</div>
	</section>

	<!-- Results -->
	<section class="container" style="padding:var(--space-12) var(--space-4);">

		<?php if ( have_posts() ) : ?>

			<?php
			$layout = thessnest_opt( 'listings_layout', 'grid' );
			$cols   = thessnest_opt( 'listings_columns', '3' );
			?>
			<div class="property-grid <?php echo $layout === 'list' ? 'property-list' : ''; ?>" data-columns="<?php echo esc_attr( $cols ); ?>">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					// Use property card for properties, fallback for other post types
					if ( 'property' === get_post_type() ) :
						get_template_part( 'template-parts/property-card' );
					else :
					?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'property-card' ); ?> style="padding:var(--space-6);">
							<h2 class="card-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<div style="margin-top:var(--space-2); margin-bottom:var(--space-3);">
								<span style="font-size:var(--font-size-xs); color:var(--color-accent); font-weight:600; text-transform:uppercase;">
									<?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?>
								</span>
							</div>
							<div style="color:var(--color-text-muted); font-size:var(--font-size-sm);">
								<?php the_excerpt(); ?>
							</div>
						</article>
					<?php endif; ?>
				<?php endwhile; ?>
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

			<div style="text-align:center; padding:var(--space-16) 0;">
				<div style="max-width:500px; margin:0 auto; background:var(--color-surface); padding:var(--space-10) var(--space-8); border-radius:var(--radius-2xl); box-shadow:var(--shadow-md); border:1px solid var(--color-border);">
					<h2 style="font-size:var(--font-size-xl); color:var(--color-primary); margin-bottom:var(--space-4);">
						<?php esc_html_e( 'No Results Found', 'thessnest' ); ?>
					</h2>
					<p style="color:var(--color-text-muted); margin-bottom:var(--space-6); line-height:1.7;">
						<?php esc_html_e( 'Sorry, no results were found for your search. Please try again with different keywords.', 'thessnest' ); ?>
					</p>
					<?php get_search_form(); ?>
				</div>
			</div>

		<?php endif; ?>

	</section>

</main>

<?php get_footer(); ?>
