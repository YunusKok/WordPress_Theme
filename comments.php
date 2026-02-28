<?php
/**
 * ThessNest — Custom Comments Template (Reviews)
 *
 * Modified to support 5-star ratings for the "property" post type.
 *
 * @package ThessNest
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="property-reviews-section" style="margin-top:var(--space-12); padding-top:var(--space-8); border-top:1px solid var(--color-border);">

	<?php if ( have_comments() ) : ?>
		<div style="display:flex; align-items:center; gap:var(--space-4); margin-bottom:var(--space-8);">
			<h2 style="font-size:var(--font-size-2xl); color:var(--color-primary); margin:0;">
				<?php
				$review_count = get_comments_number();
				printf(
					/* translators: 1: number of comments, 2: post title */
					esc_html( _nx( '%1$s Review', '%1$s Reviews', $review_count, 'comments title', 'thessnest' ) ),
					number_format_i18n( $review_count )
				);
				?>
			</h2>
			<?php
			$average_rating = get_post_meta( get_the_ID(), '_thessnest_average_rating', true );
			if ( $average_rating ) {
				echo '<div style="display:flex; align-items:center; gap:var(--space-2); background:var(--glass-bg); padding:var(--space-2) var(--space-4); border-radius:var(--radius-full); border:1px solid var(--color-border); font-weight:600;">';
				echo '<span style="color:var(--color-accent); font-size:18px;">★</span> ' . esc_html( $average_rating );
				echo '</div>';
			}
			?>
		</div>

		<ol class="comment-list" style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:var(--space-6);">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 48,
				'callback'    => 'thessnest_custom_comment_callback' // Custom callback to output stars
			) );
			?>
		</ol>

		<?php
		the_comments_navigation( array(
			'prev_text' => esc_html__( 'Older Reviews', 'thessnest' ),
			'next_text' => esc_html__( 'Newer Reviews', 'thessnest' ),
		) );
		?>

	<?php endif; // Check for have_comments(). ?>

	<?php
	// If comments are closed and there are comments, let's leave a little note.
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
		<p class="no-comments" style="margin-top:var(--space-6); color:var(--color-text-muted);"><?php esc_html_e( 'Reviews are closed.', 'thessnest' ); ?></p>
	<?php endif; ?>

	<?php
	// Load the custom comment form with Star Rating
	if ( comments_open() ) :

		$commenter = wp_get_current_commenter();

		/**
		 * Custom 5-Star HTML to inject before the comment textarea.
		 */
		$star_rating_html = '
		<div class="rating-input-container" style="margin-bottom:var(--space-4);">
			<label style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">' . esc_html__( 'Your Rating *', 'thessnest' ) . '</label>
			<div class="star-rating-radio" style="display:flex; flex-direction:row-reverse; justify-content:flex-end; gap:var(--space-2); font-size:24px;">
				<input type="radio" id="star5" name="thessnest_rating" value="5" required style="display:none;" />
				<label for="star5" title="5 stars" style="cursor:pointer; color:var(--color-border); transition:color 0.2s;">★</label>
				
				<input type="radio" id="star4" name="thessnest_rating" value="4" style="display:none;" />
				<label for="star4" title="4 stars" style="cursor:pointer; color:var(--color-border); transition:color 0.2s;">★</label>
				
				<input type="radio" id="star3" name="thessnest_rating" value="3" style="display:none;" />
				<label for="star3" title="3 stars" style="cursor:pointer; color:var(--color-border); transition:color 0.2s;">★</label>
				
				<input type="radio" id="star2" name="thessnest_rating" value="2" style="display:none;" />
				<label for="star2" title="2 stars" style="cursor:pointer; color:var(--color-border); transition:color 0.2s;">★</label>
				
				<input type="radio" id="star1" name="thessnest_rating" value="1" style="display:none;" />
				<label for="star1" title="1 star" style="cursor:pointer; color:var(--color-border); transition:color 0.2s;">★</label>
			</div>
			<style>
				/* CSS to light up stars on hover and when checked */
				.star-rating-radio label:hover,
				.star-rating-radio label:hover ~ label,
				.star-rating-radio input:checked ~ label {
					color: var(--color-accent) !important;
				}
			</style>
		</div>';

		// Form configuration
		$args = array(
			'title_reply'          => esc_html__( 'Leave a Review', 'thessnest' ),
			'title_reply_to'       => esc_html__( 'Leave a Reply to %s', 'thessnest' ),
			'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title" style="font-size:var(--font-size-xl); margin-bottom:var(--space-4); color:var(--color-primary);">',
			'title_reply_after'    => '</h3>',
			'cancel_reply_before'  => ' <small>',
			'cancel_reply_after'   => '</small>',
			'cancel_reply_link'    => esc_html__( 'Cancel reply', 'thessnest' ),
			'label_submit'         => esc_html__( 'Submit Review', 'thessnest' ),
			'class_submit'         => 'btn btn-primary',
			'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s" style="padding:var(--space-3) var(--space-6);">%4$s</button>',
			'comment_notes_before' => '',
			'comment_field'        => $star_rating_html . '<div class="comment-form-comment" style="margin-bottom:var(--space-4);"><label for="comment" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">' . _x( 'Review', 'noun', 'thessnest' ) . '</label><textarea id="comment" name="comment" cols="45" rows="5" required style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);"></textarea></div>',
			'fields'               => array(
				'author' => '<div style="display:flex; gap:var(--space-4); margin-bottom:var(--space-4);"><div class="comment-form-author" style="flex:1;"><label for="author" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">' . esc_html__( 'Name', 'thessnest' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" ' . ( $req ? 'required' : '' ) . ' style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);" /></div>',
				'email'  => '<div class="comment-form-email" style="flex:1;"><label for="email" style="display:block; margin-bottom:var(--space-2); font-weight:600; color:var(--color-text);">' . esc_html__( 'Email', 'thessnest' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label><input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" ' . ( $req ? 'required' : '' ) . ' style="width:100%; padding:var(--space-3); border:1px solid var(--color-border); border-radius:var(--radius-md); background:var(--color-surface); color:var(--color-text);" /></div></div>',
				'url'    => '', // Remove website field to keep it clean
			),
		);

		// Wrapper specifically for the form
		echo '<div class="review-form-wrapper" style="background:var(--color-background); padding:var(--space-6); border-radius:var(--radius-xl); border:1px solid var(--color-border); box-shadow:var(--shadow-sm); margin-top:var(--space-8);">';
		comment_form( $args );
		echo '</div>';

	endif;
	?>

</div><!-- #comments -->
