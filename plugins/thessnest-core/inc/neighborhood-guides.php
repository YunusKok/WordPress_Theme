<?php
/**
 * ThessNest — Neighborhood Guides
 *
 * Enriches the 'neighborhood' taxonomy with detailed content:
 * - Average rent, transport score, safety rating
 * - User reviews/ratings per neighborhood
 * - Nearby POIs (universities, metro, cafés)
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Neighborhood_Guides {

	public function __construct() {
		// Add custom fields to neighborhood taxonomy
		add_action( 'neighborhood_add_form_fields', array( $this, 'add_fields' ) );
		add_action( 'neighborhood_edit_form_fields', array( $this, 'edit_fields' ), 10, 2 );
		add_action( 'created_neighborhood', array( $this, 'save_fields' ) );
		add_action( 'edited_neighborhood', array( $this, 'save_fields' ) );

		// AJAX: Get neighborhood guide data
		add_action( 'wp_ajax_thessnest_neighborhood_guide', array( $this, 'get_guide' ) );
		add_action( 'wp_ajax_nopriv_thessnest_neighborhood_guide', array( $this, 'get_guide' ) );

		// AJAX: Submit neighborhood review
		add_action( 'wp_ajax_thessnest_review_neighborhood', array( $this, 'submit_review' ) );
	}

	/**
	 * Add fields to "Add Neighborhood" form.
	 */
	public function add_fields() {
		?>
		<div class="form-field">
			<label><?php esc_html_e( 'Transport Score (1-10)', 'thessnest' ); ?></label>
			<input type="number" name="transport_score" min="1" max="10" step="0.1" value="7">
		</div>
		<div class="form-field">
			<label><?php esc_html_e( 'Safety Rating (1-10)', 'thessnest' ); ?></label>
			<input type="number" name="safety_rating" min="1" max="10" step="0.1" value="8">
		</div>
		<div class="form-field">
			<label><?php esc_html_e( 'Nightlife Score (1-10)', 'thessnest' ); ?></label>
			<input type="number" name="nightlife_score" min="1" max="10" step="0.1" value="5">
		</div>
		<div class="form-field">
			<label><?php esc_html_e( 'Guide Description', 'thessnest' ); ?></label>
			<textarea name="guide_description" rows="5"></textarea>
		</div>
		<div class="form-field">
			<label><?php esc_html_e( 'Nearby Universities (comma-separated)', 'thessnest' ); ?></label>
			<input type="text" name="nearby_universities">
		</div>
		<div class="form-field">
			<label><?php esc_html_e( 'Cover Image URL', 'thessnest' ); ?></label>
			<input type="url" name="cover_image">
		</div>
		<?php
	}

	/**
	 * Edit fields on "Edit Neighborhood" form.
	 */
	public function edit_fields( $term, $taxonomy ) {
		$meta = get_term_meta( $term->term_id );
		?>
		<tr class="form-field">
			<th><label><?php esc_html_e( 'Transport Score', 'thessnest' ); ?></label></th>
			<td><input type="number" name="transport_score" min="1" max="10" step="0.1" value="<?php echo esc_attr( $meta['transport_score'][0] ?? 7 ); ?>"></td>
		</tr>
		<tr class="form-field">
			<th><label><?php esc_html_e( 'Safety Rating', 'thessnest' ); ?></label></th>
			<td><input type="number" name="safety_rating" min="1" max="10" step="0.1" value="<?php echo esc_attr( $meta['safety_rating'][0] ?? 8 ); ?>"></td>
		</tr>
		<tr class="form-field">
			<th><label><?php esc_html_e( 'Nightlife Score', 'thessnest' ); ?></label></th>
			<td><input type="number" name="nightlife_score" min="1" max="10" step="0.1" value="<?php echo esc_attr( $meta['nightlife_score'][0] ?? 5 ); ?>"></td>
		</tr>
		<tr class="form-field">
			<th><label><?php esc_html_e( 'Guide Description', 'thessnest' ); ?></label></th>
			<td><textarea name="guide_description" rows="5"><?php echo esc_textarea( $meta['guide_description'][0] ?? '' ); ?></textarea></td>
		</tr>
		<tr class="form-field">
			<th><label><?php esc_html_e( 'Nearby Universities', 'thessnest' ); ?></label></th>
			<td><input type="text" name="nearby_universities" value="<?php echo esc_attr( $meta['nearby_universities'][0] ?? '' ); ?>"></td>
		</tr>
		<tr class="form-field">
			<th><label><?php esc_html_e( 'Cover Image URL', 'thessnest' ); ?></label></th>
			<td><input type="url" name="cover_image" value="<?php echo esc_url( $meta['cover_image'][0] ?? '' ); ?>"></td>
		</tr>
		<?php
	}

	/**
	 * Save custom fields.
	 */
	public function save_fields( $term_id ) {
		$fields = array( 'transport_score', 'safety_rating', 'nightlife_score', 'guide_description', 'nearby_universities', 'cover_image' );
		foreach ( $fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_term_meta( $term_id, $field, sanitize_text_field( $_POST[ $field ] ) );
			}
		}
	}

	/**
	 * AJAX: Get full neighborhood guide data.
	 */
	public function get_guide() {
		$term_id = isset( $_POST['term_id'] ) ? intval( $_POST['term_id'] ) : 0;
		$term    = get_term( $term_id, 'neighborhood' );

		if ( ! $term || is_wp_error( $term ) ) {
			wp_send_json_error( array( 'message' => __( 'Neighborhood not found.', 'thessnest' ) ) );
		}

		// Calculate average rent in this neighborhood
		$properties = get_posts( array(
			'post_type'      => 'property',
			'posts_per_page' => -1,
			'tax_query'      => array( array( 'taxonomy' => 'neighborhood', 'terms' => $term_id ) ),
		) );

		$total_rent = 0;
		$count      = count( $properties );
		foreach ( $properties as $p ) {
			$total_rent += floatval( get_post_meta( $p->ID, '_thessnest_rent', true ) );
		}
		$avg_rent = $count > 0 ? round( $total_rent / $count ) : 0;

		wp_send_json_success( array(
			'name'           => $term->name,
			'slug'           => $term->slug,
			'description'    => get_term_meta( $term_id, 'guide_description', true ),
			'cover_image'    => get_term_meta( $term_id, 'cover_image', true ),
			'transport'      => floatval( get_term_meta( $term_id, 'transport_score', true ) ),
			'safety'         => floatval( get_term_meta( $term_id, 'safety_rating', true ) ),
			'nightlife'      => floatval( get_term_meta( $term_id, 'nightlife_score', true ) ),
			'universities'   => get_term_meta( $term_id, 'nearby_universities', true ),
			'avg_rent'       => $avg_rent,
			'total_listings' => $count,
			'currency'       => function_exists( 'thessnest_opt' ) ? thessnest_opt( 'currency_symbol', '€' ) : '€',
		) );
	}

	/**
	 * AJAX: Submit neighborhood review.
	 */
	public function submit_review() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Login required.', 'thessnest' ) ) );
		}

		$term_id = intval( $_POST['term_id'] ?? 0 );
		$rating  = intval( $_POST['rating'] ?? 0 );
		$comment = sanitize_textarea_field( $_POST['comment'] ?? '' );

		if ( $rating < 1 || $rating > 5 || ! $term_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid rating.', 'thessnest' ) ) );
		}

		$review_id = wp_insert_comment( array(
			'comment_post_ID' => 0,
			'comment_author'  => wp_get_current_user()->display_name,
			'comment_content' => $comment,
			'user_id'         => get_current_user_id(),
			'comment_type'    => 'thessnest_neighborhood_review',
			'comment_meta'    => array(
				'neighborhood_id' => $term_id,
				'rating'          => $rating,
			),
		) );

		wp_send_json_success( array( 'message' => __( 'Review submitted!', 'thessnest' ) ) );
	}
}

new ThessNest_Neighborhood_Guides();
