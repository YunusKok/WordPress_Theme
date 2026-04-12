<?php
/**
 * ThessNest — Host Payout & Commission System
 *
 * Handles:
 * - Platform commission on bookings
 * - Host earnings tracking
 * - Payout request management
 * - Earnings dashboard data
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Host_Payouts {

	public function __construct() {
		// Record earnings when booking is confirmed
		add_action( 'updated_post_meta', array( $this, 'on_booking_status_change' ), 10, 4 );

		// AJAX: Request payout
		add_action( 'wp_ajax_thessnest_request_payout', array( $this, 'request_payout' ) );

		// AJAX: Get earnings data
		add_action( 'wp_ajax_thessnest_get_earnings', array( $this, 'get_earnings' ) );

		// Admin: Payout management columns
		add_filter( 'manage_users_columns', array( $this, 'add_earnings_column' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'earnings_column_content' ), 10, 3 );
	}

	/**
	 * When a booking status changes to 'confirmed', record the earning.
	 */
	public function on_booking_status_change( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( $meta_key !== '_booking_status' || $meta_value !== 'confirmed' ) {
			return;
		}

		$booking = get_post( $object_id );
		if ( ! $booking || $booking->post_type !== 'thessnest_booking' ) {
			return;
		}

		// Skip if already recorded
		if ( get_post_meta( $object_id, '_earning_recorded', true ) ) {
			return;
		}

		$total_price   = floatval( get_post_meta( $object_id, '_booking_total_price', true ) );
		$landlord_id   = get_post_meta( $object_id, '_booking_landlord_id', true );

		// Calculate commission
		$commission_pct = function_exists( 'thessnest_opt' )
			? floatval( thessnest_opt( 'platform_commission', 0 ) )
			: 0;

		$commission = round( $total_price * ( $commission_pct / 100 ), 2 );
		$host_earning = $total_price - $commission;

		// Save to booking
		update_post_meta( $object_id, '_booking_commission', $commission );
		update_post_meta( $object_id, '_booking_host_earning', $host_earning );
		update_post_meta( $object_id, '_earning_recorded', 1 );

		// Update user lifetime earnings
		$lifetime = floatval( get_user_meta( $landlord_id, '_thessnest_total_earnings', true ) );
		update_user_meta( $landlord_id, '_thessnest_total_earnings', $lifetime + $host_earning );

		$pending = floatval( get_user_meta( $landlord_id, '_thessnest_pending_balance', true ) );
		update_user_meta( $landlord_id, '_thessnest_pending_balance', $pending + $host_earning );
	}

	/**
	 * AJAX: Get earnings summary for dashboard.
	 */
	public function get_earnings() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Authentication required.', 'thessnest' ) ) );
		}

		$user_id = get_current_user_id();

		$total_earnings  = floatval( get_user_meta( $user_id, '_thessnest_total_earnings', true ) );
		$pending_balance = floatval( get_user_meta( $user_id, '_thessnest_pending_balance', true ) );
		$total_withdrawn = floatval( get_user_meta( $user_id, '_thessnest_total_withdrawn', true ) );

		// Recent bookings earnings
		$recent_bookings = get_posts( array(
			'post_type'      => 'thessnest_booking',
			'posts_per_page' => 10,
			'meta_query'     => array(
				array(
					'key'   => '_booking_landlord_id',
					'value' => $user_id,
				),
				array(
					'key'     => '_booking_status',
					'value'   => 'confirmed',
				),
			),
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		$transactions = array();
		foreach ( $recent_bookings as $booking ) {
			$transactions[] = array(
				'id'       => $booking->ID,
				'date'     => get_the_date( 'Y-m-d', $booking ),
				'property' => get_the_title( get_post_meta( $booking->ID, '_booking_property_id', true ) ),
				'total'    => floatval( get_post_meta( $booking->ID, '_booking_total_price', true ) ),
				'earning'  => floatval( get_post_meta( $booking->ID, '_booking_host_earning', true ) ),
			);
		}

		wp_send_json_success( array(
			'total_earnings'  => $total_earnings,
			'pending_balance' => $pending_balance,
			'total_withdrawn' => $total_withdrawn,
			'transactions'    => $transactions,
			'currency'        => function_exists( 'thessnest_opt' ) ? thessnest_opt( 'currency_symbol', '€' ) : '€',
		) );
	}

	/**
	 * AJAX: Request payout.
	 */
	public function request_payout() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Authentication required.', 'thessnest' ) ) );
		}

		$user_id = get_current_user_id();
		$pending = floatval( get_user_meta( $user_id, '_thessnest_pending_balance', true ) );

		$min_payout = function_exists( 'thessnest_opt' )
			? floatval( thessnest_opt( 'min_payout_amount', 50 ) )
			: 50;

		if ( $pending < $min_payout ) {
			wp_send_json_error( array( 'message' => sprintf(
				__( 'Minimum payout amount is %s. Your current balance is %s.', 'thessnest' ),
				thessnest_format_price( $min_payout ),
				thessnest_format_price( $pending )
			) ) );
		}

		$payout_method = get_user_meta( $user_id, '_thessnest_payout_method', true );
		if ( empty( $payout_method ) ) {
			wp_send_json_error( array( 'message' => __( 'Please set your payout method in your profile settings first.', 'thessnest' ) ) );
		}

		// Create payout request post
		$payout_id = wp_insert_post( array(
			'post_title'  => sprintf( 'Payout: %s — %s', get_userdata( $user_id )->display_name, thessnest_format_price( $pending ) ),
			'post_status' => 'publish',
			'post_type'   => 'thessnest_payout',
			'post_author' => $user_id,
		) );

		if ( ! is_wp_error( $payout_id ) ) {
			update_post_meta( $payout_id, '_payout_amount', $pending );
			update_post_meta( $payout_id, '_payout_method', $payout_method );
			update_post_meta( $payout_id, '_payout_status', 'pending' );

			// Reset pending balance, add to withdrawn
			$withdrawn = floatval( get_user_meta( $user_id, '_thessnest_total_withdrawn', true ) );
			update_user_meta( $user_id, '_thessnest_pending_balance', 0 );
			update_user_meta( $user_id, '_thessnest_total_withdrawn', $withdrawn + $pending );

			// Email admin
			$admin_email = get_option( 'admin_email' );
			$user        = get_userdata( $user_id );
			wp_mail(
				$admin_email,
				sprintf( __( '[%s] New Payout Request', 'thessnest' ), get_bloginfo( 'name' ) ),
				sprintf( __( "Host: %s\nAmount: %s\nMethod: %s\n\nPlease process this payout.", 'thessnest' ),
					$user->display_name, thessnest_format_price( $pending ), $payout_method )
			);
		}

		wp_send_json_success( array( 'message' => __( 'Payout request submitted. You will receive your earnings within 3-5 business days.', 'thessnest' ) ) );
	}

	/**
	 * Admin column: Host Earnings.
	 */
	public function add_earnings_column( $columns ) {
		$columns['host_earnings'] = __( 'Earnings', 'thessnest' );
		return $columns;
	}

	public function earnings_column_content( $value, $column_name, $user_id ) {
		if ( $column_name === 'host_earnings' ) {
			$total = floatval( get_user_meta( $user_id, '_thessnest_total_earnings', true ) );
			return $total > 0 ? thessnest_format_price( $total ) : '—';
		}
		return $value;
	}
}

new ThessNest_Host_Payouts();

/**
 * Register the payout CPT.
 */
function thessnest_register_payout_cpt() {
	register_post_type( 'thessnest_payout', array(
		'labels' => array(
			'name'          => __( 'Payouts', 'thessnest' ),
			'singular_name' => __( 'Payout', 'thessnest' ),
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => 'thessnest-dashboard',
		'supports'           => array( 'title' ),
		'capability_type'    => 'post',
	) );
}
add_action( 'init', 'thessnest_register_payout_cpt' );
