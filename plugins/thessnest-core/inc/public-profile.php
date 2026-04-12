<?php
/**
 * ThessNest — Public Host Profile & Superhost System
 *
 * Handles:
 * - Public profile page for hosts
 * - Superhost badge logic (auto-awarded)
 * - Profile verification badges
 * - Response time tracking
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Public_Profile {

	public function __construct() {
		// Rewrite rule for /host/{username}
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_filter( 'template_include', array( $this, 'load_profile_template' ) );

		// Track response time on message reply
		add_action( 'wp_ajax_thessnest_send_message', array( $this, 'track_response_time' ), 5 );

		// AJAX: Get public profile data
		add_action( 'wp_ajax_thessnest_get_host_profile', array( $this, 'get_profile_data' ) );
		add_action( 'wp_ajax_nopriv_thessnest_get_host_profile', array( $this, 'get_profile_data' ) );

		// Cron: Recalculate superhost status monthly
		if ( ! wp_next_scheduled( 'thessnest_check_superhost' ) ) {
			wp_schedule_event( time(), 'monthly', 'thessnest_check_superhost' );
		}
		add_action( 'thessnest_check_superhost', array( $this, 'recalculate_superhost' ) );
	}

	/**
	 * URL rewrite: /host/{username}
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule( '^host/([^/]+)/?$', 'index.php?thessnest_host_profile=$matches[1]', 'top' );
	}

	public function add_query_vars( $vars ) {
		$vars[] = 'thessnest_host_profile';
		return $vars;
	}

	public function load_profile_template( $template ) {
		$host_slug = get_query_var( 'thessnest_host_profile' );
		if ( ! $host_slug ) {
			return $template;
		}

		$custom = locate_template( 'template-host-profile.php' );
		return $custom ? $custom : $template;
	}

	/**
	 * Get full profile data for a host.
	 */
	public static function get_data( $user_id ) {
		$user = get_userdata( $user_id );
		if ( ! $user ) return null;

		$listings_count = count_user_posts( $user_id, 'property' );
		$member_since   = date_i18n( get_option( 'date_format' ), strtotime( $user->user_registered ) );
		$avg_rating     = floatval( get_user_meta( $user_id, '_thessnest_avg_rating', true ) );
		$total_reviews  = intval( get_user_meta( $user_id, '_thessnest_total_reviews', true ) );
		$response_time  = get_user_meta( $user_id, '_thessnest_avg_response_mins', true );
		$is_superhost   = (bool) get_user_meta( $user_id, '_thessnest_superhost', true );
		$total_guests   = intval( get_user_meta( $user_id, '_thessnest_total_guests', true ) );

		// Verification badges
		$badges = array();
		if ( get_user_meta( $user_id, '_kyc_status', true ) === 'approved' ) {
			$badges[] = array( 'icon' => '🪪', 'label' => __( 'ID Verified', 'thessnest' ) );
		}
		if ( $user->user_email ) {
			$badges[] = array( 'icon' => '✉️', 'label' => __( 'Email Verified', 'thessnest' ) );
		}
		$phone = get_user_meta( $user_id, '_thessnest_phone', true );
		if ( ! empty( $phone ) ) {
			$badges[] = array( 'icon' => '📱', 'label' => __( 'Phone Verified', 'thessnest' ) );
		}
		if ( $is_superhost ) {
			$badges[] = array( 'icon' => '⭐', 'label' => __( 'Superhost', 'thessnest' ) );
		}

		// Response time label
		$response_label = __( 'Not available', 'thessnest' );
		if ( $response_time ) {
			$mins = intval( $response_time );
			if ( $mins < 60 ) {
				$response_label = sprintf( __( 'Within %d minutes', 'thessnest' ), $mins );
			} elseif ( $mins < 1440 ) {
				$response_label = sprintf( __( 'Within %d hours', 'thessnest' ), round( $mins / 60 ) );
			} else {
				$response_label = sprintf( __( 'Within %d days', 'thessnest' ), round( $mins / 1440 ) );
			}
		}

		return array(
			'id'              => $user_id,
			'name'            => $user->display_name,
			'avatar'          => get_avatar_url( $user_id, array( 'size' => 200 ) ),
			'bio'             => get_user_meta( $user_id, 'description', true ),
			'member_since'    => $member_since,
			'listings_count'  => $listings_count,
			'avg_rating'      => $avg_rating,
			'total_reviews'   => $total_reviews,
			'total_guests'    => $total_guests,
			'response_time'   => $response_label,
			'is_superhost'    => $is_superhost,
			'badges'          => $badges,
			'languages'       => get_user_meta( $user_id, '_thessnest_languages', true ),
		);
	}

	/**
	 * AJAX endpoint for public profile.
	 */
	public function get_profile_data() {
		$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
		if ( ! $user_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid user.', 'thessnest' ) ) );
		}

		$data = self::get_data( $user_id );
		if ( ! $data ) {
			wp_send_json_error( array( 'message' => __( 'Host not found.', 'thessnest' ) ) );
		}

		wp_send_json_success( $data );
	}

	/**
	 * Track host response time.
	 */
	public function track_response_time() {
		if ( ! is_user_logged_in() ) return;

		$user_id = get_current_user_id();
		$user    = get_userdata( $user_id );

		if ( ! in_array( 'landlord', (array) $user->roles ) ) return;

		// Simple rolling average: store last response timestamp
		$last_msg_time = get_user_meta( $user_id, '_thessnest_last_received_msg_time', true );
		if ( $last_msg_time ) {
			$diff_mins = ( time() - intval( $last_msg_time ) ) / 60;
			$current_avg = floatval( get_user_meta( $user_id, '_thessnest_avg_response_mins', true ) );
			$count       = intval( get_user_meta( $user_id, '_thessnest_response_count', true ) );

			$new_avg = ( $current_avg * $count + $diff_mins ) / ( $count + 1 );
			update_user_meta( $user_id, '_thessnest_avg_response_mins', round( $new_avg ) );
			update_user_meta( $user_id, '_thessnest_response_count', $count + 1 );
		}
	}

	/**
	 * Recalculate Superhost status for all landlords.
	 * Criteria: 4.8+ avg rating, 5+ reviews, 10+ total guests, < 24hr response
	 */
	public function recalculate_superhost() {
		$landlords = get_users( array( 'role' => 'landlord' ) );

		foreach ( $landlords as $landlord ) {
			$avg_rating    = floatval( get_user_meta( $landlord->ID, '_thessnest_avg_rating', true ) );
			$total_reviews = intval( get_user_meta( $landlord->ID, '_thessnest_total_reviews', true ) );
			$total_guests  = intval( get_user_meta( $landlord->ID, '_thessnest_total_guests', true ) );
			$response_mins = intval( get_user_meta( $landlord->ID, '_thessnest_avg_response_mins', true ) );

			$min_rating   = function_exists( 'thessnest_opt' ) ? floatval( thessnest_opt( 'superhost_min_rating', 4.8 ) ) : 4.8;
			$min_reviews  = function_exists( 'thessnest_opt' ) ? intval( thessnest_opt( 'superhost_min_reviews', 5 ) ) : 5;

			$is_superhost = (
				$avg_rating >= $min_rating &&
				$total_reviews >= $min_reviews &&
				$total_guests >= 10 &&
				( $response_mins > 0 && $response_mins < 1440 )
			);

			update_user_meta( $landlord->ID, '_thessnest_superhost', $is_superhost ? 1 : 0 );
		}
	}
}

new ThessNest_Public_Profile();
