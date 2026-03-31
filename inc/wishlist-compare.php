<?php
/**
 * ThessNest — Wishlist Sharing & Property Comparison
 *
 * Extends the existing favorites system with:
 * - Wishlist collections (folders)
 * - Shareable wishlist links
 * - Side-by-side property comparison (up to 3)
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Wishlist_Compare {

	public function __construct() {
		add_action( 'wp_ajax_thessnest_create_collection', array( $this, 'create_collection' ) );
		add_action( 'wp_ajax_thessnest_add_to_collection', array( $this, 'add_to_collection' ) );
		add_action( 'wp_ajax_thessnest_share_collection', array( $this, 'share_collection' ) );
		add_action( 'wp_ajax_thessnest_compare_properties', array( $this, 'compare' ) );
		add_action( 'wp_ajax_nopriv_thessnest_compare_properties', array( $this, 'compare' ) );
		add_action( 'wp_ajax_nopriv_thessnest_view_shared_collection', array( $this, 'view_shared' ) );
		add_action( 'wp_ajax_thessnest_view_shared_collection', array( $this, 'view_shared' ) );
	}

	/**
	 * Create a named wishlist collection.
	 */
	public function create_collection() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );
		$user_id = get_current_user_id();
		$name    = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : __( 'My Collection', 'thessnest' );

		$collections = get_user_meta( $user_id, '_thessnest_collections', true );
		if ( ! is_array( $collections ) ) $collections = array();

		if ( count( $collections ) >= 20 ) {
			wp_send_json_error( array( 'message' => __( 'Maximum 20 collections.', 'thessnest' ) ) );
		}

		$id = uniqid( 'col_' );
		$share_key = wp_generate_password( 16, false );

		$collections[ $id ] = array(
			'name'      => $name,
			'items'     => array(),
			'share_key' => $share_key,
			'created'   => current_time( 'mysql' ),
		);

		update_user_meta( $user_id, '_thessnest_collections', $collections );
		wp_send_json_success( array( 'collection_id' => $id, 'share_key' => $share_key ) );
	}

	/**
	 * Add property to a collection.
	 */
	public function add_to_collection() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );
		$user_id       = get_current_user_id();
		$collection_id = sanitize_text_field( $_POST['collection_id'] ?? '' );
		$property_id   = intval( $_POST['property_id'] ?? 0 );

		$collections = get_user_meta( $user_id, '_thessnest_collections', true );
		if ( ! isset( $collections[ $collection_id ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Collection not found.', 'thessnest' ) ) );
		}

		if ( ! in_array( $property_id, $collections[ $collection_id ]['items'] ) ) {
			$collections[ $collection_id ]['items'][] = $property_id;
		}

		update_user_meta( $user_id, '_thessnest_collections', $collections );
		wp_send_json_success( array( 'message' => __( 'Added to collection!', 'thessnest' ) ) );
	}

	/**
	 * Get share URL for a collection.
	 */
	public function share_collection() {
		check_ajax_referer( 'thessnest_dashboard_nonce', 'security' );
		$user_id       = get_current_user_id();
		$collection_id = sanitize_text_field( $_POST['collection_id'] ?? '' );

		$collections = get_user_meta( $user_id, '_thessnest_collections', true );
		if ( ! isset( $collections[ $collection_id ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Collection not found.', 'thessnest' ) ) );
		}

		$share_url = add_query_arg( array(
			'thessnest_shared' => $collections[ $collection_id ]['share_key'],
			'uid'              => $user_id,
		), home_url( '/' ) );

		wp_send_json_success( array( 'share_url' => $share_url ) );
	}

	/**
	 * View a shared collection (public, no auth required).
	 */
	public function view_shared() {
		$share_key = sanitize_text_field( $_POST['share_key'] ?? '' );
		$uid       = intval( $_POST['uid'] ?? 0 );

		$collections = get_user_meta( $uid, '_thessnest_collections', true );
		if ( ! is_array( $collections ) ) {
			wp_send_json_error( array( 'message' => __( 'Collection not found.', 'thessnest' ) ) );
		}

		foreach ( $collections as $col ) {
			if ( $col['share_key'] === $share_key ) {
				$items = array();
				foreach ( $col['items'] as $pid ) {
					$items[] = array(
						'id'        => $pid,
						'title'     => get_the_title( $pid ),
						'url'       => get_permalink( $pid ),
						'thumbnail' => get_the_post_thumbnail_url( $pid, 'medium' ),
						'rent'      => floatval( get_post_meta( $pid, '_thessnest_rent', true ) ),
					);
				}
				wp_send_json_success( array( 'name' => $col['name'], 'items' => $items ) );
			}
		}

		wp_send_json_error( array( 'message' => __( 'Invalid share link.', 'thessnest' ) ) );
	}

	/**
	 * Compare up to 3 properties side by side.
	 */
	public function compare() {
		$ids = isset( $_POST['property_ids'] ) ? array_map( 'intval', (array) $_POST['property_ids'] ) : array();
		$ids = array_slice( $ids, 0, 3 );

		if ( empty( $ids ) ) {
			wp_send_json_error( array( 'message' => __( 'Select properties to compare.', 'thessnest' ) ) );
		}

		$properties = array();
		foreach ( $ids as $pid ) {
			$post = get_post( $pid );
			if ( ! $post || $post->post_type !== 'property' ) continue;

			$neighborhoods = wp_get_post_terms( $pid, 'neighborhood', array( 'fields' => 'names' ) );
			$amenities     = wp_get_post_terms( $pid, 'amenity', array( 'fields' => 'names' ) );

			$properties[] = array(
				'id'           => $pid,
				'title'        => get_the_title( $pid ),
				'url'          => get_permalink( $pid ),
				'thumbnail'    => get_the_post_thumbnail_url( $pid, 'medium_large' ),
				'rent'         => floatval( get_post_meta( $pid, '_thessnest_rent', true ) ),
				'utilities'    => floatval( get_post_meta( $pid, '_thessnest_utilities', true ) ),
				'deposit'      => floatval( get_post_meta( $pid, '_thessnest_deposit', true ) ),
				'total_cost'   => floatval( get_post_meta( $pid, '_thessnest_rent', true ) ) + floatval( get_post_meta( $pid, '_thessnest_utilities', true ) ),
				'wifi_speed'   => intval( get_post_meta( $pid, '_thessnest_wifi_speed', true ) ),
				'max_tenants'  => intval( get_post_meta( $pid, '_thessnest_max_tenants', true ) ),
				'instant_book' => get_post_meta( $pid, '_thessnest_instant_book', true ) === '1',
				'neighborhood' => ! empty( $neighborhoods ) ? implode( ', ', $neighborhoods ) : '—',
				'amenities'    => ! empty( $amenities ) ? $amenities : array(),
				'rating'       => floatval( get_post_meta( $pid, '_thessnest_avg_rating', true ) ),
				'reviews'      => intval( get_post_meta( $pid, '_thessnest_review_count', true ) ),
			);
		}

		wp_send_json_success( array( 'properties' => $properties ) );
	}
}

new ThessNest_Wishlist_Compare();
