<?php
/**
 * ThessNest — Search Form Template
 *
 * Replaces the default WordPress search form with a modern, theme-styled version.
 * Limits search scope to blog posts as defined in functions.php.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="search-form-inner" style="position:relative; display:flex; align-items:center;">
		<label class="screen-reader-text" for="s"><?php esc_html_e( 'Search for:', 'thessnest' ); ?></label>
		
		<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search blog posts...', 'thessnest' ); ?>" value="<?php echo get_search_query(); ?>" name="s" id="s" style="width:100%; padding:var(--space-3) var(--space-12) var(--space-3) var(--space-4); border:1px solid var(--color-border); border-radius:var(--radius-full); background:var(--color-surface); outline:none; transition:all var(--transition-fast);" onfocus="this.style.borderColor='var(--color-accent)'; this.style.background='var(--color-background)';" onblur="this.style.borderColor='var(--color-border)'; this.style.background='var(--color-surface)';">
		
		<button type="submit" class="search-submit" aria-label="<?php esc_attr_e( 'Submit Search', 'thessnest' ); ?>" style="position:absolute; right:var(--space-2); top:50%; transform:translateY(-50%); background:var(--color-accent); border:none; width:36px; height:36px; border-radius:var(--radius-full); display:flex; align-items:center; justify-content:center; color:#fff; cursor:pointer; transition:all var(--transition-fast);" onmouseover="this.style.filter='brightness(1.1)';" onmouseout="this.style.filter='none';">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
		</button>
	</div>
</form>
