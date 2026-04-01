<?php
/**
 * ThessNest — Advanced Pricing Engine
 *
 * Centralized pricing calculator that handles:
 * - Base monthly rent
 * - Seasonal pricing (date-based rate overrides)
 * - Long-stay discounts (weekly/monthly/quarterly)
 * - Minimum stay enforcement
 * - Early booking discount
 * - Service fees & cleaning fees
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Pricing_Engine {

	/**
	 * Calculate the total price for a stay.
	 *
	 * @param int    $property_id  Property post ID.
	 * @param string $checkin      Check-in date (Y-m-d).
	 * @param string $checkout     Check-out date (Y-m-d).
	 * @return array|WP_Error      Pricing breakdown or error.
	 */
	public static function calculate( $property_id, $checkin, $checkout ) {
		$date1    = new DateTime( $checkin );
		$date2    = new DateTime( $checkout );
		$nights   = $date1->diff( $date2 )->days;

		if ( $nights < 1 ) {
			return new WP_Error( 'invalid_dates', __( 'Checkout must be after check-in.', 'thessnest' ) );
		}

		// ── Minimum stay check ──
		$min_stay = (int) get_post_meta( $property_id, '_thessnest_min_stay', true );
		if ( $min_stay > 0 && $nights < $min_stay ) {
			return new WP_Error( 'min_stay', sprintf(
				__( 'Minimum stay is %d nights for this property.', 'thessnest' ),
				$min_stay
			) );
		}

		// ── Base nightly rate ──
		$base_rate = floatval( get_post_meta( $property_id, '_thessnest_rent', true ) );

		// ── Calculate nightly subtotal with seasonal overrides ──
		$seasonal_rates = get_post_meta( $property_id, '_thessnest_seasonal_rates', true );
		$seasonal_rates = is_array( $seasonal_rates ) ? $seasonal_rates : array();

		$nightly_breakdown = array();
		$subtotal          = 0;
		$current           = clone $date1;

		while ( $current < $date2 ) {
			$date_str = $current->format( 'Y-m-d' );
			$rate     = self::get_rate_for_date( $date_str, $base_rate, $seasonal_rates );

			$nightly_breakdown[] = array(
				'date' => $date_str,
				'rate' => $rate,
			);
			$subtotal += $rate;
			$current->modify( '+1 day' );
		}

		// ── Long-stay discount ──
		$discount_amount  = 0;
		$discount_label   = '';
		$discount_percent = 0;

		$weekly_discount    = floatval( get_post_meta( $property_id, '_thessnest_weekly_discount', true ) );
		$monthly_discount   = floatval( get_post_meta( $property_id, '_thessnest_monthly_discount', true ) );
		$quarterly_discount = floatval( get_post_meta( $property_id, '_thessnest_quarterly_discount', true ) );

		if ( $nights >= 90 && $quarterly_discount > 0 ) {
			$discount_percent = $quarterly_discount;
			$discount_label   = __( 'Quarterly discount', 'thessnest' );
		} elseif ( $nights >= 28 && $monthly_discount > 0 ) {
			$discount_percent = $monthly_discount;
			$discount_label   = __( 'Monthly discount', 'thessnest' );
		} elseif ( $nights >= 7 && $weekly_discount > 0 ) {
			$discount_percent = $weekly_discount;
			$discount_label   = __( 'Weekly discount', 'thessnest' );
		}

		if ( $discount_percent > 0 ) {
			$discount_amount = round( $subtotal * ( $discount_percent / 100 ), 2 );
		}

		// ── Early booking discount ──
		$early_discount_amount = 0;
		$early_days   = (int) get_post_meta( $property_id, '_thessnest_early_bird_days', true );
		$early_pct    = floatval( get_post_meta( $property_id, '_thessnest_early_bird_discount', true ) );

		if ( $early_days > 0 && $early_pct > 0 ) {
			$today          = new DateTime();
			$days_until     = $today->diff( $date1 )->days;
			$is_future      = $date1 > $today;

			if ( $is_future && $days_until >= $early_days ) {
				$after_long_stay   = $subtotal - $discount_amount;
				$early_discount_amount = round( $after_long_stay * ( $early_pct / 100 ), 2 );
			}
		}

		// ── Additional fees per listing ──
		$cleaning_fee_amount = floatval( get_post_meta( $property_id, '_thessnest_cleaning_fee', true ) );
		$cleaning_fee_type   = get_post_meta( $property_id, '_thessnest_cleaning_fee_type', true ); // single, monthly, on-demand
		$cleaning_fee        = 0;
		$cleaning_fee_label  = '';

		if ( $cleaning_fee_amount > 0 ) {
			if ( $cleaning_fee_type === 'monthly' ) {
				$months = max( 1, ceil( $nights / 30 ) );
				$cleaning_fee = $cleaning_fee_amount * $months;
				$cleaning_fee_label = sprintf( __( 'Cleaning Fee (×%d months)', 'thessnest' ), $months );
			} elseif ( $cleaning_fee_type === 'single' || empty( $cleaning_fee_type ) ) {
				$cleaning_fee = $cleaning_fee_amount;
				$cleaning_fee_label = __( 'Cleaning Fee (Single)', 'thessnest' );
			} elseif ( $cleaning_fee_type === 'on-demand' ) {
				// On-demand is not added to the initial total.
				$cleaning_fee_label = sprintf( __( 'Cleaning available on demand: %s/use (Not included in this total)', 'thessnest' ), (function_exists('thessnest_opt') ? thessnest_opt('currency_symbol', '€') : '€') . $cleaning_fee_amount );
			}
		}

		$service_fee  = floatval( get_post_meta( $property_id, '_thessnest_service_fee', true ) );
		$utilities    = floatval( get_post_meta( $property_id, '_thessnest_utilities', true ) ) * $nights;
		$deposit      = floatval( get_post_meta( $property_id, '_thessnest_deposit', true ) );

		// ── Platform commission (global, from Redux) ──
		$commission_pct = 0;
		if ( function_exists( 'thessnest_opt' ) ) {
			$commission_pct = floatval( thessnest_opt( 'platform_commission', 0 ) );
		}

		$price_before_fees = $subtotal - $discount_amount - $early_discount_amount;
		$commission_amount = ( $commission_pct > 0 ) ? round( $price_before_fees * ( $commission_pct / 100 ), 2 ) : 0;

		$total = $price_before_fees + $cleaning_fee + $service_fee + $utilities + $commission_amount;

		return array(
			'nights'              => $nights,
			'base_rate'           => $base_rate,
			'subtotal'            => $subtotal,
			'discount_label'      => $discount_label,
			'discount_percent'    => $discount_percent,
			'discount_amount'     => $discount_amount,
			'early_bird_discount' => $early_discount_amount,
			'cleaning_fee'        => $cleaning_fee,
			'cleaning_fee_label'  => $cleaning_fee_label,
			'cleaning_fee_type'   => $cleaning_fee_type,
			'service_fee'         => $service_fee,
			'utilities'           => $utilities,
			'deposit'             => $deposit,
			'commission_pct'      => $commission_pct,
			'commission_amount'   => $commission_amount,
			'total'               => $total,
			'currency'            => function_exists( 'thessnest_opt' ) ? thessnest_opt( 'currency_symbol', '€' ) : '€',
		);
	}

	/**
	 * Get the rate for a specific date, checking seasonal overrides.
	 *
	 * @param string $date_str       Date in Y-m-d format.
	 * @param float  $base_rate      Default nightly rate.
	 * @param array  $seasonal_rates Array of seasonal rate rules.
	 * @return float
	 */
	private static function get_rate_for_date( $date_str, $base_rate, $seasonal_rates ) {
		foreach ( $seasonal_rates as $season ) {
			if ( empty( $season['start'] ) || empty( $season['end'] ) || empty( $season['rate'] ) ) {
				continue;
			}
			// Normalize to month-day for yearly recurring comparison
			$date_md  = substr( $date_str, 5 ); // "MM-DD"
			$start_md = substr( $season['start'], 5 );
			$end_md   = substr( $season['end'], 5 );

			// Handle year-wrap (e.g., Nov-Feb)
			if ( $start_md <= $end_md ) {
				if ( $date_md >= $start_md && $date_md <= $end_md ) {
					return floatval( $season['rate'] );
				}
			} else {
				if ( $date_md >= $start_md || $date_md <= $end_md ) {
					return floatval( $season['rate'] );
				}
			}
		}
		return $base_rate;
	}

	/**
	 * AJAX endpoint: Get price breakdown for frontend display.
	 */
	public static function ajax_get_price() {
		$property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;
		$checkin     = isset( $_POST['checkin'] ) ? sanitize_text_field( $_POST['checkin'] ) : '';
		$checkout    = isset( $_POST['checkout'] ) ? sanitize_text_field( $_POST['checkout'] ) : '';

		if ( ! $property_id || ! $checkin || ! $checkout ) {
			wp_send_json_error( array( 'message' => __( 'Missing parameters.', 'thessnest' ) ) );
		}

		$result = self::calculate( $property_id, $checkin, $checkout );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( $result );
	/**
	 * AJAX endpoint: Save custom pricing from Landlord Dashboard.
	 */
	public static function ajax_save_custom_pricing() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Not logged in.', 'thessnest' ) ) );
		}

		$property_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;
		$start_date  = isset( $_POST['start_date'] ) ? sanitize_text_field( $_POST['start_date'] ) : '';
		$end_date    = isset( $_POST['end_date'] ) ? sanitize_text_field( $_POST['end_date'] ) : '';
		$rate        = isset( $_POST['rate'] ) ? floatval( $_POST['rate'] ) : 0;

		if ( ! $property_id || ! $start_date || ! $end_date || $rate <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid data.', 'thessnest' ) ) );
		}

		// Security: Check if current user is the author of the property
		if ( get_post_field( 'post_author', $property_id ) != get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'thessnest' ) ) );
		}

		// Retrieve existing seasonal rates
		$seasonal_rates = get_post_meta( $property_id, '_thessnest_seasonal_rates', true );
		if ( ! is_array( $seasonal_rates ) ) {
			$seasonal_rates = array();
		}

		// Check for overlapping dates
		foreach ( $seasonal_rates as $season ) {
			if ( $start_date <= $season['end'] && $end_date >= $season['start'] ) {
				wp_send_json_error( array( 'message' => __( 'Dates overlap with an existing custom price period.', 'thessnest' ) ) );
			}
		}

		// Add new rate
		$seasonal_rates[] = array(
			'start' => $start_date,
			'end'   => $end_date,
			'rate'  => $rate
		);

		update_post_meta( $property_id, '_thessnest_seasonal_rates', $seasonal_rates );

		wp_send_json_success( array( 'message' => __( 'Custom pricing period saved successfully.', 'thessnest' ) ) );
	}
}

// Register AJAX endpoints
add_action( 'wp_ajax_thessnest_get_price', array( 'ThessNest_Pricing_Engine', 'ajax_get_price' ) );
add_action( 'wp_ajax_nopriv_thessnest_get_price', array( 'ThessNest_Pricing_Engine', 'ajax_get_price' ) );
add_action( 'wp_ajax_thessnest_save_custom_pricing', array( 'ThessNest_Pricing_Engine', 'ajax_save_custom_pricing' ) );
