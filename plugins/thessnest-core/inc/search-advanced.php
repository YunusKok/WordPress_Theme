<?php
/**
 * ThessNest — Advanced Search & Filtering
 *
 * Handles:
 * - AJAX live filtering without page reload
 * - Date range availability filtering
 * - Radius-based location search
 * - Saved searches for logged-in users
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Advanced_Search {

	public function __construct() {
		add_action( 'wp_ajax_thessnest_live_search', array( $this, 'live_search' ) );
		add_action( 'wp_ajax_nopriv_thessnest_live_search', array( $this, 'live_search' ) );

		add_action( 'wp_ajax_thessnest_save_search', array( $this, 'save_search' ) );
		add_action( 'wp_ajax_thessnest_get_saved_searches', array( $this, 'get_saved_searches' ) );
		add_action( 'wp_ajax_thessnest_delete_saved_search', array( $this, 'delete_saved_search' ) );
	}

	/**
	 * AJAX Live Search — returns filtered property results.
	 */
	public function live_search() {
		$args = array(
			'post_type'      => 'property',
			'post_status'    => 'publish',
			'posts_per_page' => intval( isset( $_POST['per_page'] ) ? $_POST['per_page'] : 12 ),
			'paged'          => intval( isset( $_POST['page'] ) ? $_POST['page'] : 1 ),
		);

		$meta_query = array( 'relation' => 'AND' );
		$tax_query  = array( 'relation' => 'AND' );

		// ── Price Range ──
		$price_min = isset( $_POST['price_min'] ) ? intval( $_POST['price_min'] ) : 0;
		$price_max = isset( $_POST['price_max'] ) ? intval( $_POST['price_max'] ) : 0;

		if ( $price_min > 0 ) {
			$meta_query[] = array(
				'key'     => '_thessnest_rent',
				'value'   => $price_min,
				'type'    => 'NUMERIC',
				'compare' => '>=',
			);
		}
		if ( $price_max > 0 ) {
			$meta_query[] = array(
				'key'     => '_thessnest_rent',
				'value'   => $price_max,
				'type'    => 'NUMERIC',
				'compare' => '<=',
			);
		}

		// ── Neighborhood ──
		$neighborhood = isset( $_POST['neighborhood'] ) ? intval( $_POST['neighborhood'] ) : 0;
		if ( $neighborhood > 0 ) {
			$tax_query[] = array(
				'taxonomy' => 'neighborhood',
				'terms'    => $neighborhood,
			);
		}

		// ── Amenities ──
		if ( ! empty( $_POST['amenities'] ) && is_array( $_POST['amenities'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'amenity',
				'terms'    => array_map( 'intval', $_POST['amenities'] ),
				'operator' => 'AND',
			);
		}

		// ── Target Group ──
		if ( ! empty( $_POST['target_group'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'target_group',
				'terms'    => intval( $_POST['target_group'] ),
			);
		}

		// ── Max Tenants ──
		$guests = isset( $_POST['guests'] ) ? intval( $_POST['guests'] ) : 0;
		if ( $guests > 0 ) {
			$meta_query[] = array(
				'key'     => '_thessnest_max_tenants',
				'value'   => $guests,
				'type'    => 'NUMERIC',
				'compare' => '>=',
			);
		}

		// ── Instant Book ──
		if ( ! empty( $_POST['instant_book'] ) ) {
			$meta_query[] = array(
				'key'   => '_thessnest_instant_book',
				'value' => '1',
			);
		}

		// ── WiFi Speed Min ──
		$wifi = isset( $_POST['wifi_min'] ) ? intval( $_POST['wifi_min'] ) : 0;
		if ( $wifi > 0 ) {
			$meta_query[] = array(
				'key'     => '_thessnest_wifi_speed',
				'value'   => $wifi,
				'type'    => 'NUMERIC',
				'compare' => '>=',
			);
		}

		// ── Date Availability ──
		$checkin  = isset( $_POST['checkin'] ) ? sanitize_text_field( $_POST['checkin'] ) : '';
		$checkout = isset( $_POST['checkout'] ) ? sanitize_text_field( $_POST['checkout'] ) : '';

		// ── Location Radius Search ──
		$lat    = isset( $_POST['lat'] ) ? floatval( $_POST['lat'] ) : 0;
		$lng    = isset( $_POST['lng'] ) ? floatval( $_POST['lng'] ) : 0;
		$radius = isset( $_POST['radius'] ) ? intval( $_POST['radius'] ) : 0;

		// Apply meta/tax queries
		if ( count( $meta_query ) > 1 ) {
			$args['meta_query'] = $meta_query;
		}
		if ( count( $tax_query ) > 1 ) {
			$args['tax_query'] = $tax_query;
		}

		// ── Sorting ──
		$sort = isset( $_POST['sort'] ) ? sanitize_text_field( $_POST['sort'] ) : 'newest';
		switch ( $sort ) {
			case 'price_low':
				$args['meta_key'] = '_thessnest_rent';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;
			case 'price_high':
				$args['meta_key'] = '_thessnest_rent';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'rating':
				$args['meta_key'] = '_thessnest_avg_rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			default: // newest
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
		}

		$query   = new WP_Query( $args );
		$results = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$pid = get_the_ID();

				// If date filtering, check availability
				if ( $checkin && $checkout ) {
					if ( self::has_booking_overlap( $pid, $checkin, $checkout ) ) {
						continue;
					}
				}

				// If radius search, check distance
				if ( $lat && $lng && $radius ) {
					$prop_lat = floatval( get_post_meta( $pid, '_thessnest_latitude', true ) );
					$prop_lng = floatval( get_post_meta( $pid, '_thessnest_longitude', true ) );
					$dist     = self::haversine_distance( $lat, $lng, $prop_lat, $prop_lng );
					if ( $dist > $radius ) continue;
				}

				$results[] = array(
					'id'        => $pid,
					'title'     => get_the_title(),
					'url'       => get_permalink(),
					'thumbnail' => get_the_post_thumbnail_url( $pid, 'medium_large' ),
					'rent'      => floatval( get_post_meta( $pid, '_thessnest_rent', true ) ),
					'utilities' => floatval( get_post_meta( $pid, '_thessnest_utilities', true ) ),
					'deposit'   => floatval( get_post_meta( $pid, '_thessnest_deposit', true ) ),
					'wifi'      => intval( get_post_meta( $pid, '_thessnest_wifi_speed', true ) ),
					'tenants'   => intval( get_post_meta( $pid, '_thessnest_max_tenants', true ) ),
					'instant'   => get_post_meta( $pid, '_thessnest_instant_book', true ) === '1',
					'rating'    => floatval( get_post_meta( $pid, '_thessnest_avg_rating', true ) ),
					'reviews'   => intval( get_post_meta( $pid, '_thessnest_review_count', true ) ),
				);
			}
			wp_reset_postdata();
		}

		wp_send_json_success( array(
			'results'     => $results,
			'total'       => $query->found_posts,
			'total_pages' => $query->max_num_pages,
			'current_page'=> intval( $args['paged'] ),
		) );
	}

	/**
	 * Check if property has booking overlap.
	 */
	private static function has_booking_overlap( $property_id, $checkin, $checkout ) {
		global $wpdb;
		return (bool) $wpdb->get_var( $wpdb->prepare( "
			SELECT p.ID FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm_prop ON p.ID = pm_prop.post_id AND pm_prop.meta_key = '_booking_property_id'
			INNER JOIN {$wpdb->postmeta} pm_status ON p.ID = pm_status.post_id AND pm_status.meta_key = '_booking_status'
			INNER JOIN {$wpdb->postmeta} pm_cin ON p.ID = pm_cin.post_id AND pm_cin.meta_key = '_booking_checkin'
			INNER JOIN {$wpdb->postmeta} pm_cout ON p.ID = pm_cout.post_id AND pm_cout.meta_key = '_booking_checkout'
			WHERE p.post_type = 'thessnest_booking'
			AND pm_prop.meta_value = %d
			AND pm_status.meta_value IN ('confirmed', 'pending')
			AND (pm_cin.meta_value < %s AND pm_cout.meta_value > %s)
			LIMIT 1
		", $property_id, $checkout, $checkin ) );
	}

	/**
	 * Haversine formula to calculate distance between two lat/lng points.
	 */
	private static function haversine_distance( $lat1, $lng1, $lat2, $lng2 ) {
		$R   = 6371; // km
		$dLat = deg2rad( $lat2 - $lat1 );
		$dLng = deg2rad( $lng2 - $lng1 );
		$a    = sin( $dLat / 2 ) * sin( $dLat / 2 ) +
				cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) *
				sin( $dLng / 2 ) * sin( $dLng / 2 );
		$c    = 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );
		return $R * $c;
	}

	/**
	 * Save search criteria for a user.
	 */
	public function save_search() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Login required.', 'thessnest' ) ) );
		}

		$label   = isset( $_POST['label'] ) ? sanitize_text_field( $_POST['label'] ) : __( 'My Search', 'thessnest' );
		$filters = isset( $_POST['filters'] ) ? $_POST['filters'] : array();

		$saved = get_user_meta( get_current_user_id(), '_thessnest_saved_searches', true );
		if ( ! is_array( $saved ) ) $saved = array();

		if ( count( $saved ) >= 10 ) {
			wp_send_json_error( array( 'message' => __( 'Maximum 10 saved searches.', 'thessnest' ) ) );
		}

		$saved[] = array(
			'id'      => uniqid(),
			'label'   => $label,
			'filters' => $filters,
			'created' => current_time( 'mysql' ),
		);

		update_user_meta( get_current_user_id(), '_thessnest_saved_searches', $saved );
		wp_send_json_success( array( 'message' => __( 'Search saved!', 'thessnest' ) ) );
	}

	/**
	 * Get saved searches.
	 */
	public function get_saved_searches() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );
		$saved = get_user_meta( get_current_user_id(), '_thessnest_saved_searches', true );
		wp_send_json_success( array( 'searches' => is_array( $saved ) ? $saved : array() ) );
	}

	/**
	 * Delete a saved search.
	 */
	public function delete_saved_search() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );
		$search_id = isset( $_POST['search_id'] ) ? sanitize_text_field( $_POST['search_id'] ) : '';

		$saved = get_user_meta( get_current_user_id(), '_thessnest_saved_searches', true );
		if ( is_array( $saved ) ) {
			$saved = array_filter( $saved, function( $s ) use ( $search_id ) {
				return $s['id'] !== $search_id;
			} );
			update_user_meta( get_current_user_id(), '_thessnest_saved_searches', array_values( $saved ) );
		}

		wp_send_json_success( array( 'message' => __( 'Search deleted.', 'thessnest' ) ) );
	}
}

new ThessNest_Advanced_Search();
