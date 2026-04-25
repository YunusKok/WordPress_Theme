<?php
/**
 * ThessNest — Archive Template
 *
 * Displays general archives (Blog Categories, Tags, Dates, etc.).
 * Property archives are handled by archive-property.php.
 *
 * @package ThessNest
 */

get_header(); ?>

<main id="main-content" class="site-main" style="padding:var(--space-12) 0; background:var(--color-surface); min-height:80vh;">
	<div class="container">
		
		<header class="archive-header" style="margin-bottom:var(--space-10); text-align:center;">
			<?php
			the_archive_title( '<h1 class="archive-title" style="font-size:var(--font-size-3xl); margin-bottom:var(--space-3);">', '</h1>' );
			the_archive_description( '<div class="archive-description" style="color:var(--color-text-muted); font-size:var(--font-size-lg); max-width:600px; margin:0 auto;">', '</div>' );
			?>
		</header>

		<div class="archive-layout" style="display:grid; grid-template-columns:1fr; gap:var(--space-8);">
			<?php if ( is_active_sidebar( 'blog-sidebar' ) ) : ?>
				<style>
					@media (min-width: 1024px) {
						.archive-layout { grid-template-columns: 1fr 300px; }
					}
				</style>
			<?php endif; ?>

			<!-- Main Content -->
			<div class="archive-main">
				<?php if ( have_posts() ) : ?>
					<div class="blog-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:var(--space-6);">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/content', get_post_format() );
						endwhile;
						?>
					</div>

					<!-- Pagination -->
					<div class="thessnest-pagination" style="margin-top:var(--space-8); display:flex; justify-content:center; gap:var(--space-2);">
						<?php
						echo paginate_links( array(
							'prev_text' => '&laquo;',
							'next_text' => '&raquo;',
							'type'      => 'list'
						) );
						?>
					</div>
					<style>
						.thessnest-pagination ul { display:flex; gap:var(--space-2); list-style:none; padding:0; margin:0; }
						.thessnest-pagination a, .thessnest-pagination span { display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:var(--radius-md); background:var(--color-background); border:1px solid var(--color-border); font-weight:600; transition:all var(--transition-fast); }
						.thessnest-pagination a:hover { border-color:var(--color-accent); color:var(--color-accent); }
						.thessnest-pagination .current { background:var(--color-accent); color:#fff; border-color:var(--color-accent); }
					</style>

				<?php else : ?>
					<div class="no-results" style="background:var(--color-background); padding:var(--space-12); text-align:center; border-radius:var(--radius-2xl); border:1px dashed var(--color-border);">
						<h3 style="font-size:var(--font-size-xl); margin-bottom:var(--space-2);"><?php esc_html_e( 'Nothing Found', 'thessnest' ); ?></h3>
						<p style="color:var(--color-text-muted); margin-bottom:var(--space-6);"><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'thessnest' ); ?></p>
						<?php get_search_form(); ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Sidebar -->
			<?php if ( is_active_sidebar( 'blog-sidebar' ) ) : ?>
				<aside class="archive-sidebar" style="background:var(--color-background); border-radius:var(--radius-xl); padding:var(--space-6); border:1px solid var(--color-border); height:max-content; position:sticky; top:100px;">
					<?php dynamic_sidebar( 'blog-sidebar' ); ?>
				</aside>
				<style>
					.archive-sidebar .widget { margin-bottom:var(--space-6); }
					.archive-sidebar .widget:last-child { margin-bottom:0; }
					.archive-sidebar .widget-title { font-size:var(--font-size-lg); margin-bottom:var(--space-4); border-bottom:2px solid var(--color-surface); padding-bottom:var(--space-2); }
					.archive-sidebar ul { list-style:none; padding:0; margin:0; }
					.archive-sidebar ul li { margin-bottom:var(--space-2); padding-bottom:var(--space-2); border-bottom:1px solid var(--color-surface); }
					.archive-sidebar ul li:last-child { border-bottom:none; padding-bottom:0; margin-bottom:0; }
					.archive-sidebar ul li a { color:var(--color-text-muted); transition:color var(--transition-fast); }
					.archive-sidebar ul li a:hover { color:var(--color-accent); }
				</style>
			<?php endif; ?>

		</div>
	</div>
</main>

<?php get_footer(); ?>
