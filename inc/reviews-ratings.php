<?php
/**
 * ThessNest — Property Reviews & Ratings
 *
 * Extends the native WordPress commenting system to support
 * 5-star ratings for the "property" post type.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1. Validate and Sanitize the 5-star rating before saving the comment.
 */
function thessnest_preprocess_review( $commentdata ) {
	// Only apply to the 'property' post type
	if ( isset( $commentdata['comment_post_ID'] ) && 'property' === get_post_type( $commentdata['comment_post_ID'] ) ) {
		// If a rating is submitted, ensure it's between 1 and 5.
		if ( isset( $_POST['thessnest_rating'] ) ) {
			$rating = intval( $_POST['thessnest_rating'] );
			if ( $rating < 1 || $rating > 5 ) {
				wp_die( __( 'Please provide a valid rating between 1 and 5 stars.', 'thessnest' ), 400 );
			}
		} else {
			// Optional: Make rating mandatory
			// wp_die( __( 'A star rating is required to submit a review.', 'thessnest' ), 400 );
		}
	}
	return $commentdata;
}
add_filter( 'preprocess_comment', 'thessnest_preprocess_review' );

/**
 * 2. Save the rating as comment metadata when a comment is posted.
 */
function thessnest_save_review_rating( $comment_id ) {
	if ( isset( $_POST['thessnest_rating'] ) && 'property' === get_post_type( get_comment( $comment_id )->comment_post_ID ) ) {
		$rating = intval( $_POST['thessnest_rating'] );
		add_comment_meta( $comment_id, 'thessnest_rating', $rating, true );
	}
}
add_action( 'comment_post', 'thessnest_save_review_rating' );

/**
 * 3. Calculate and cache the average property rating.
 *
 * Hooked to run whenever a comment is approved, trashed, or deleted.
 */
function thessnest_update_property_average_rating( $post_id ) {
	if ( 'property' !== get_post_type( $post_id ) ) {
		return;
	}

	$comments = get_comments( array(
		'post_id' => $post_id,
		'status'  => 'approve',
	) );

	$total_rating = 0;
	$count = 0;

	foreach ( $comments as $comment ) {
		$rating = get_comment_meta( $comment->comment_ID, 'thessnest_rating', true );
		if ( $rating ) {
			$total_rating += intval( $rating );
			$count++;
		}
	}

	if ( $count > 0 ) {
		$average = round( $total_rating / $count, 1 );
		update_post_meta( $post_id, '_thessnest_average_rating', $average );
		update_post_meta( $post_id, '_thessnest_review_count', $count );
	} else {
		delete_post_meta( $post_id, '_thessnest_average_rating' );
		delete_post_meta( $post_id, '_thessnest_review_count' );
	}
}

// Hooks to trigger average recalculation
add_action( 'wp_set_comment_status', function( $id, $status ) {
	$comment = get_comment( $id );
	if ( $comment ) {
		thessnest_update_property_average_rating( $comment->comment_post_ID );
	}
}, 10, 2 );

add_action( 'wp_insert_comment', function( $id, $comment ) {
	if ( 1 === (int) $comment->comment_approved ) { // Only calculate on approved comments
		thessnest_update_property_average_rating( $comment->comment_post_ID );
	}
}, 10, 2 );

add_action( 'deleted_comment', function( $id ) {
	global $wpdb;
	$comment = $wpdb->get_row( $wpdb->prepare( "SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = %d", $id ) );
	if ( $comment ) {
		$post_id = $comment->comment_post_ID;
		thessnest_update_property_average_rating( $post_id );
	}
} );
add_action( 'trashed_comment', function( $id ) {
	$comment = get_comment( $id );
	if ( $comment ) {
		thessnest_update_property_average_rating( $comment->comment_post_ID );
	}
} );

/**
 * 4. Helper Function to Display Stars
 */
function thessnest_display_stars( $rating ) {
	$html = '<div class="star-rating" style="display:inline-flex; gap:2px; color:var(--color-accent); font-size:14px;">';
	$full_stars = floor( $rating );
	$half_star  = ( $rating - $full_stars >= 0.5 );
	$empty_stars = 5 - $full_stars - ( $half_star ? 1 : 0 );

	for ( $i = 0; $i < $full_stars; $i++ ) {
		$html .= '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
	}
	if ( $half_star ) {
		$html .= '<svg width="14" height="14" viewBox="0 0 24 24" fill="url(#halfGrad)"><defs><linearGradient id="halfGrad" x1="0" x2="1" y1="0" y2="0"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="var(--color-border)"/></linearGradient></defs><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
	}
	for ( $i = 0; $i < $empty_stars; $i++ ) {
		$html .= '<svg width="14" height="14" viewBox="0 0 24 24" fill="var(--color-border)"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
	}

	$html .= '</div>';
	return $html;
}

/**
 * 5. Custom Comment HTML Output Callback
 */
function thessnest_custom_comment_callback( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	$rating = get_comment_meta( $comment->comment_ID, 'thessnest_rating', true );
	?>
	<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? 'property-review-item' : 'parent property-review-item' ); ?> style="background:var(--color-surface); padding:var(--space-6); border:1px solid var(--color-border); border-radius:var(--radius-lg); margin-bottom:var(--space-4);">
		
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<footer class="comment-meta" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:var(--space-3);">
				<div class="comment-author vcard" style="display:flex; align-items:center; gap:var(--space-3);">
					<?php if ( 0 != $args['avatar_size'] ) { echo get_avatar( $comment, $args['avatar_size'], '', '', array( 'class' => 'avatar', 'style' => 'border-radius:50%;' ) ); } ?>
					<div>
						<b class="fn" style="color:var(--color-primary); font-size:var(--font-size-base);"><?php echo get_comment_author_link( $comment ); ?></b>
						<br>
						<span class="comment-metadata" style="font-size:var(--font-size-sm); color:var(--color-text-muted);">
							<?php
							printf(
								/* translators: 1: date, 2: time */
								esc_html__( '%1$s at %2$s', 'thessnest' ),
								get_comment_date( '', $comment ),
								get_comment_time()
							);
							?>
						</span>
					</div>
				</div><!-- .comment-author -->

				<?php if ( $rating ) : ?>
					<div class="review-stars-display" style="background:var(--color-background); padding:4px 8px; border-radius:var(--radius-md); border:1px solid var(--color-border);">
						<?php echo thessnest_display_stars( $rating ); ?>
					</div>
				<?php endif; ?>
			</footer><!-- .comment-meta -->

			<div class="comment-content" style="color:var(--color-text); line-height:1.6;">
				<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation" style="color:#d97706; font-style:italic;"><?php esc_html_e( 'Your review is awaiting moderation.', 'thessnest' ); ?></p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div><!-- .comment-content -->

			<?php
			comment_reply_link( array_merge( $args, array(
				'add_below' => 'div-comment',
				'depth'     => $depth,
				'max_depth' => $args['max_depth'],
				'before'    => '<div class="reply" style="margin-top:var(--space-3); font-size:var(--font-size-sm); font-weight:500;">',
				'after'     => '</div>'
			) ) );
			?>
		</article><!-- .comment-body -->
	<?php
	// WP doesn't need closing </li>, it manages it automatically.
}
