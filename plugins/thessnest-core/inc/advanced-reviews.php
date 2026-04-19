<?php
/**
 * ThessNest — Multi-Criteria Advanced Reviews
 *
 * Extends the WordPress comment system to capture 4 specific metrics:
 * 1. Cleanliness
 * 2. Communication
 * 3. Location
 * 4. Value
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Advanced_Reviews {

	public function __construct() {
		// Add the custom fields to the comment form
		add_filter( 'comment_form_logged_in_after', [ $this, 'render_rating_fields' ] );
		add_filter( 'comment_form_after_fields', [ $this, 'render_rating_fields' ] );

		// Save the rating meta when a comment is posted
		add_action( 'comment_post', [ $this, 'save_rating_meta' ] );

		// Update the average rating meta on the property whenever a comment is approved or deleted
		add_action( 'comment_unapproved_to_approved', [ $this, 'update_property_average_rating' ] );
		add_action( 'comment_approved_to_unapproved', [ $this, 'update_property_average_rating' ] );
		add_action( 'deleted_comment', [ $this, 'update_property_average_rating' ] );
	}

	/**
	 * Render the 4-criteria star rating fields
	 */
	public function render_rating_fields() {
		if ( get_post_type() !== 'property' ) {
			return;
		}

		$criteria = [
			'cleanliness'   => __( 'Cleanliness', 'thessnest' ),
			'communication' => __( 'Communication', 'thessnest' ),
			'location'      => __( 'Location', 'thessnest' ),
			'value'         => __( 'Value', 'thessnest' ),
			'checkin'       => __( 'Check-in', 'thessnest' ),
		];

		echo '<div class="thessnest-rating-fields" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:25px; padding:20px; background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--radius-lg);">';
		
		foreach ( $criteria as $key => $label ) {
			echo '<div class="rating-item">';
			echo '<label style="display:block; margin-bottom:8px; font-weight:600; font-size:14px;">' . esc_html( $label ) . '</label>';
			echo '<div class="star-rating" style="display:flex; gap:5px; color:#fbbf24; cursor:pointer;">';
			for ( $i = 1; $i <= 5; $i++ ) {
				echo '<svg class="star-icon" data-value="' . $i . '" data-key="' . esc_attr( $key ) . '" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition:fill 0.2s;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
			}
			echo '<input type="hidden" name="thessnest_rating_' . esc_attr( $key ) . '" value="5">';
			echo '</div>';
			echo '</div>';
		}

		echo '</div>';

		// Interactive script for stars
		?>
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			const containers = document.querySelectorAll('.star-rating');
			containers.forEach(container => {
				const stars = container.querySelectorAll('.star-icon');
				const input = container.querySelector('input');
				
				// Set default fill
				updateStars(stars, input.value);

				stars.forEach(star => {
					star.addEventListener('click', function() {
						const val = this.dataset.value;
						input.value = val;
						updateStars(stars, val);
					});
					star.addEventListener('mouseover', function() {
						updateStars(stars, this.dataset.value);
					});
					star.addEventListener('mouseout', function() {
						updateStars(stars, input.value);
					});
				});
			});

			function updateStars(stars, val) {
				stars.forEach(star => {
					if (parseInt(star.dataset.value) <= parseInt(val)) {
						star.style.fill = '#fbbf24';
					} else {
						star.style.fill = 'none';
					}
				});
			}
		});
		</script>
		<?php
	}

	/**
	 * Save Meta
	 */
	public function save_rating_meta( $comment_id ) {
		$keys = ['cleanliness', 'communication', 'location', 'value', 'checkin'];
		$total = 0;

		foreach ( $keys as $key ) {
			if ( isset( $_POST['thessnest_rating_' . $key] ) ) {
				$val = intval( $_POST['thessnest_rating_' . $key] );
				update_comment_meta( $comment_id, '_thessnest_rating_' . $key, $val );
				$total += $val;
			}
		}

		// Save average for this specific comment
		$avg = $total / count( $keys );
		update_comment_meta( $comment_id, '_thessnest_rating_avg', $avg );
		
		// Trigger property update
		$comment = get_comment( $comment_id );
		$this->update_property_average_rating( $comment->comment_post_ID );
	}

	/**
	 * Calculate Global Average for the Property
	 */
	public function update_property_average_rating( $comment_id_or_post_id ) {
		if ( is_numeric( $comment_id_or_post_id ) ) {
			// If it's a comment hook, get post ID
			$comment = get_comment( $comment_id_or_post_id );
			$post_id = $comment ? $comment->comment_post_ID : $comment_id_or_post_id;
		}

		// Fetch all approved comments for this property
		$comments = get_comments( [
			'post_id' => $post_id,
			'status'  => 'approve'
		] );

		if ( empty( $comments ) ) {
			update_post_meta( $post_id, '_thessnest_average_rating', 0 );
			return;
		}

		$sum = 0;
		$count = 0;

		foreach ( $comments as $comment ) {
			$rating = get_comment_meta( $comment->comment_ID, '_thessnest_rating_avg', true );
			if ( $rating ) {
				$sum += (float) $rating;
				$count++;
			}
		}

		$final_avg = ( $count > 0 ) ? round( $sum / $count, 1 ) : 0;
		update_post_meta( $post_id, '_thessnest_average_rating', $final_avg );
	}
}

new ThessNest_Advanced_Reviews();
