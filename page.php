<?php
/**
 * ThessNest — Page Template
 *
 * Renders standard WordPress pages using the theme's native
 * iOS Liquid Glass design system (CSS Custom Properties).
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main-content" role="main">
	<?php while ( have_posts() ) : the_post(); ?>

	<!-- Page Hero -->
	<section style="background:var(--color-surface); padding:var(--space-16) var(--space-4); border-bottom:1px solid var(--color-border); text-align:center;">
		<div class="container">
			<h1 style="font-size:var(--font-size-3xl); font-weight:800; color:var(--color-primary); margin-bottom:var(--space-3);">
				<?php the_title(); ?>
			</h1>
			<?php if ( has_excerpt() ) : ?>
			<p style="font-size:var(--font-size-lg); color:var(--color-text-muted); max-width:600px; margin:0 auto; line-height:1.7;">
				<?php echo esc_html( get_the_excerpt() ); ?>
			</p>
			<?php endif; ?>
		</div>
	</section>

	<!-- Main Content Area -->
	<section style="padding:var(--space-12) var(--space-4); background:var(--color-background);">
		<div class="container" style="max-width:800px;">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="background:var(--color-surface); padding:var(--space-8) var(--space-6); border-radius:var(--radius-2xl); box-shadow:var(--shadow-card); border:1px solid var(--color-border);">
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages( array(
						'before' => '<div class="page-links" style="margin-top:var(--space-6); padding-top:var(--space-4); border-top:1px solid var(--color-border);">' . esc_html__( 'Pages:', 'thessnest' ),
						'after'  => '</div>',
					) );
					?>
				</div>
			</article>
		</div>
	</section>

	<?php endwhile; ?>
</main>

<?php get_footer(); ?>
