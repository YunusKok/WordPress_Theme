<?php
/**
 * ThessNest — Roommate Matching System
 *
 * Unique differentiator: No other rental theme has this.
 * Users can create "looking for roommate" profiles and get
 * matched based on preferences (budget, location, lifestyle).
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Roommate_Matching {

	public function __construct() {
		add_action( 'wp_ajax_thessnest_save_roommate_profile', array( $this, 'save_profile' ) );
		add_action( 'wp_ajax_thessnest_get_roommate_matches', array( $this, 'get_matches' ) );
		add_action( 'wp_ajax_thessnest_toggle_roommate_search', array( $this, 'toggle_search' ) );
	}

	/**
	 * Save roommate preference profile.
	 */
	public function save_profile() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Login required.', 'thessnest' ) ) );
		}

		$user_id = get_current_user_id();
		$prefs   = array(
			'budget_min'     => isset( $_POST['budget_min'] ) ? intval( $_POST['budget_min'] ) : 0,
			'budget_max'     => isset( $_POST['budget_max'] ) ? intval( $_POST['budget_max'] ) : 0,
			'neighborhood'   => isset( $_POST['neighborhood'] ) ? sanitize_text_field( $_POST['neighborhood'] ) : '',
			'move_in_date'   => isset( $_POST['move_in_date'] ) ? sanitize_text_field( $_POST['move_in_date'] ) : '',
			'stay_duration'  => isset( $_POST['stay_duration'] ) ? sanitize_text_field( $_POST['stay_duration'] ) : '', // 1-3m, 3-6m, 6m+
			'age_range'      => isset( $_POST['age_range'] ) ? sanitize_text_field( $_POST['age_range'] ) : '',       // 18-25, 25-35, 35+
			'gender_pref'    => isset( $_POST['gender_pref'] ) ? sanitize_text_field( $_POST['gender_pref'] ) : 'any', // male, female, any
			'smoker'         => isset( $_POST['smoker'] ) ? sanitize_text_field( $_POST['smoker'] ) : 'no',
			'pets'           => isset( $_POST['pets'] ) ? sanitize_text_field( $_POST['pets'] ) : 'no',
			'student'        => isset( $_POST['student'] ) ? (bool) $_POST['student'] : false,
			'quiet_hours'    => isset( $_POST['quiet_hours'] ) ? (bool) $_POST['quiet_hours'] : false,
			'languages'      => isset( $_POST['languages'] ) ? sanitize_text_field( $_POST['languages'] ) : '',
			'bio'            => isset( $_POST['bio'] ) ? sanitize_textarea_field( $_POST['bio'] ) : '',
		);

		update_user_meta( $user_id, '_thessnest_roommate_prefs', $prefs );
		update_user_meta( $user_id, '_thessnest_roommate_active', 1 );

		wp_send_json_success( array( 'message' => __( 'Roommate profile saved!', 'thessnest' ) ) );
	}

	/**
	 * Toggle roommate search active/inactive.
	 */
	public function toggle_search() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );
		$user_id = get_current_user_id();
		$current = get_user_meta( $user_id, '_thessnest_roommate_active', true );
		update_user_meta( $user_id, '_thessnest_roommate_active', $current ? 0 : 1 );
		wp_send_json_success( array( 'active' => ! $current ) );
	}

	/**
	 * Get matched roommate profiles.
	 */
	public function get_matches() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Login required.', 'thessnest' ) ) );
		}

		$user_id  = get_current_user_id();
		$my_prefs = get_user_meta( $user_id, '_thessnest_roommate_prefs', true );

		if ( ! is_array( $my_prefs ) ) {
			wp_send_json_error( array( 'message' => __( 'Please create your roommate profile first.', 'thessnest' ) ) );
		}

		// Get all active roommate seekers except current user
		$users = get_users( array(
			'exclude'    => array( $user_id ),
			'meta_query' => array(
				array( 'key' => '_thessnest_roommate_active', 'value' => '1' ),
			),
		) );

		$matches = array();
		foreach ( $users as $user ) {
			$prefs = get_user_meta( $user->ID, '_thessnest_roommate_prefs', true );
			if ( ! is_array( $prefs ) ) continue;

			$score = self::calculate_match_score( $my_prefs, $prefs );

			$matches[] = array(
				'user_id'      => $user->ID,
				'name'         => $user->display_name,
				'avatar'       => get_avatar_url( $user->ID, array( 'size' => 100 ) ),
				'match_score'  => $score,
				'budget_range' => sprintf( '€%d – €%d', $prefs['budget_min'], $prefs['budget_max'] ),
				'neighborhood' => $prefs['neighborhood'],
				'move_in'      => $prefs['move_in_date'],
				'duration'     => $prefs['stay_duration'],
				'student'      => $prefs['student'],
				'bio'          => wp_trim_words( $prefs['bio'], 20 ),
			);
		}

		// Sort by match score descending
		usort( $matches, function( $a, $b ) {
			return $b['match_score'] - $a['match_score'];
		} );

		wp_send_json_success( array( 'matches' => array_slice( $matches, 0, 20 ) ) );
	}

	/**
	 * Calculate match score between two preference sets (0-100).
	 */
	private static function calculate_match_score( $a, $b ) {
		$score  = 0;
		$total  = 0;

		// Budget overlap (30 points)
		$total += 30;
		if ( $a['budget_max'] >= $b['budget_min'] && $b['budget_max'] >= $a['budget_min'] ) {
			$overlap = min( $a['budget_max'], $b['budget_max'] ) - max( $a['budget_min'], $b['budget_min'] );
			$range   = max( $a['budget_max'] - $a['budget_min'], $b['budget_max'] - $b['budget_min'], 1 );
			$score  += min( 30, round( ( $overlap / $range ) * 30 ) );
		}

		// Neighborhood (20 points)
		$total += 20;
		if ( $a['neighborhood'] === $b['neighborhood'] && ! empty( $a['neighborhood'] ) ) {
			$score += 20;
		}

		// Stay duration (15 points)
		$total += 15;
		if ( $a['stay_duration'] === $b['stay_duration'] ) {
			$score += 15;
		}

		// Smoking preference (10 points)
		$total += 10;
		if ( $a['smoker'] === $b['smoker'] ) {
			$score += 10;
		}

		// Pets (10 points)
		$total += 10;
		if ( $a['pets'] === $b['pets'] ) {
			$score += 10;
		}

		// Quiet hours (10 points)
		$total += 10;
		if ( $a['quiet_hours'] === $b['quiet_hours'] ) {
			$score += 10;
		}

		// Age range (5 points)
		$total += 5;
		if ( $a['age_range'] === $b['age_range'] ) {
			$score += 5;
		}

		return $total > 0 ? round( ( $score / $total ) * 100 ) : 0;
	}
}

new ThessNest_Roommate_Matching();
