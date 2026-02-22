<?php
/**
 * ThessNest — Singular Template
 *
 * A robust fallback for rendering single posts and pages if specific templates are missing.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main-content" role="main" class="container" style="padding:var(--space-12) var(--space-4);">
	<div style="max-width:800px;margin:0 auto;background:var(--color-surface);padding:var(--space-8) var(--space-6);border-radius:var(--radius-2xl);box-shadow:var(--shadow-card);border:1px solid var(--color-border);">
		
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header style="margin-bottom:var(--space-8);text-align:center;">
					<?php the_title( '<h1 style="font-size:var(--font-size-3xl);color:var(--color-primary);margin-bottom:var(--space-2);">', '</h1>' ); ?>
				</header>
				
				<div class="entry-content" style="line-height:1.8;color:var(--color-text);">
					<?php
					the_content();
					wp_link_pages( array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'thessnest' ),
						'after'  => '</div>',
					) );
					?>
				</div>
			</article>
			
		<?php endwhile; endif; ?>
		
	</div>
</main>

<?php
get_footer();
