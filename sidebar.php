<?php
/**
 * ThessNest — General Sidebar Template
 *
 * Used on single posts, pages, and archives.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
	return;
}
?>

<aside class="thessnest-sidebar" style="background:var(--color-background); border-radius:var(--radius-xl); padding:var(--space-6); border:1px solid var(--color-border); height:max-content; position:sticky; top:100px;">
	<?php dynamic_sidebar( 'blog-sidebar' ); ?>
</aside>
<style>
	.thessnest-sidebar .widget { margin-bottom:var(--space-6); }
	.thessnest-sidebar .widget:last-child { margin-bottom:0; }
	.thessnest-sidebar .widget-title { font-size:var(--font-size-lg); margin-bottom:var(--space-4); border-bottom:2px solid var(--color-surface); padding-bottom:var(--space-2); }
	.thessnest-sidebar ul { list-style:none; padding:0; margin:0; }
	.thessnest-sidebar ul li { margin-bottom:var(--space-2); padding-bottom:var(--space-2); border-bottom:1px solid var(--color-surface); }
	.thessnest-sidebar ul li:last-child { border-bottom:none; padding-bottom:0; margin-bottom:0; }
	.thessnest-sidebar ul li a { color:var(--color-text-muted); transition:color var(--transition-fast); }
	.thessnest-sidebar ul li a:hover { color:var(--color-accent); }
</style>
