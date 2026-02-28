<?php
/**
 * Template Name: About Page
 *
 * ThessNest — About Page Template
 * Uses the theme's native iOS Liquid Glass design system.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main-content" role="main">
	<?php while ( have_posts() ) : the_post(); ?>

	<!-- About Hero -->
	<section style="background:linear-gradient(135deg, #1B2A4A 0%, #152240 40%, #1E3358 100%); padding:var(--space-20) var(--space-4); text-align:center; position:relative; overflow:hidden;">
		<!-- Ambient Orb -->
		<div style="position:absolute; width:400px; height:400px; border-radius:50%; background:radial-gradient(circle, rgba(232,145,58,0.15) 0%, transparent 70%); top:-100px; right:-80px; filter:blur(60px); pointer-events:none;"></div>

		<div class="container" style="position:relative; z-index:2;">
			<h1 style="font-size:var(--font-size-3xl); font-weight:800; color:#fff; margin-bottom:var(--space-4);">
				<?php the_title(); ?>
			</h1>
			<?php if ( has_excerpt() ) : ?>
			<p style="font-size:var(--font-size-lg); color:rgba(255,255,255,0.7); max-width:600px; margin:0 auto; line-height:1.7;">
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
