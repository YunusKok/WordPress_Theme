<?php
/**
 * ThessNest — Admin Front Page Settings
 *
 * Hides the default block editor on the front page and displays
 * a notice directing the user to the Theme Customizer.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check if the currently edited post is the front page.
 *
 * @param int $post_id The post ID.
 * @return bool True if it's the front page, false otherwise.
 */
function thessnest_is_front_page_edit( $post_id ) {
	if ( ! $post_id ) {
		return false;
	}

	// Option 1: Is it set as the static front page?
	$front_page_id = (int) get_option( 'page_on_front' );
	if ( $front_page_id === (int) $post_id ) {
		return true;
	}

	// Option 2: Does it use the front-page.php template?
	$template = get_page_template_slug( $post_id );
	if ( 'front-page.php' === $template ) {
		return true;
	}

	return false;
}

/**
 * Hide the editor on the Front Page.
 */
function thessnest_hide_editor_front_page() {
	if ( isset( $_GET['post'] ) ) {
		$post_id = intval( $_GET['post'] );
	} elseif ( isset( $_POST['post_ID'] ) ) {
		$post_id = intval( $_POST['post_ID'] );
	} else {
		return;
	}

	if ( thessnest_is_front_page_edit( $post_id ) ) {
		// Remove standard WordPress text editor
		remove_post_type_support( 'page', 'editor' );

		// Also remove Gutenberg Block Editor for this specific page
		add_filter( 'use_block_editor_for_post', '__return_false', 10 );
	}
}
add_action( 'admin_init', 'thessnest_hide_editor_front_page' );

/**
 * Show a custom notice on the Front Page edit screen
 * to direct users to the Theme Customizer.
 *
 * @param WP_Post $post Current post object.
 */
function thessnest_front_page_customizer_notice( $post ) {
	if ( ! thessnest_is_front_page_edit( $post->ID ) ) {
		return;
	}

	$customizer_url = add_query_arg(
		array(
			'autofocus[panel]' => 'thessnest_homepage_panel',
			'url'              => home_url( '/' ),
		),
		admin_url( 'customize.php' )
	);
	?>
	<div class="notice notice-info inline" style="margin-top:20px; padding: 20px; border-left-color: #0073aa; background: #fff;">
		<h2><?php esc_html_e( 'Front Page Content', 'thessnest' ); ?></h2>
		<p style="font-size: 14px;">
			<?php esc_html_e( 'The content and design of the homepage is managed dynamically. To change texts, images, and other settings, please use the Theme Customizer.', 'thessnest' ); ?>
		</p>
		<p>
			<a href="<?php echo esc_url( $customizer_url ); ?>" class="button button-primary button-large">
				<?php esc_html_e( 'Edit Homepage Settings (Customizer)', 'thessnest' ); ?>
			</a>
		</p>
	</div>
	<?php
}
add_action( 'edit_form_after_title', 'thessnest_front_page_customizer_notice' );
