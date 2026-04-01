<?php
/**
 * ThessNest — iCal Sync Engine
 *
 * Exports property bookings to an .ics endpoint for Airbnb/Booking.com.
 * Imports an external .ics feed and blocks dates on the ThessNest calendar.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_iCal_Engine {

	public function __construct() {
		// Register a custom rewrite endpoint for exporting iCal (e.g. /property/apartment-1/ical/)
		add_action( 'init', [ $this, 'add_ical_endpoint' ] );
		add_action( 'template_redirect', [ $this, 'serve_ical_export' ] );

		// Hook into property save to optionally parse external iCal immediately
		add_action( 'save_post_property', [ $this, 'parse_external_ical_on_save' ], 10, 3 );
	}

	/**
	 * 1. Register /ical endpoint on the property post type
	 */
	public function add_ical_endpoint() {
		add_rewrite_endpoint( 'ical', EP_PERMALINK );
	}

	/**
	 * 2. Serve the .ics file dynamically
	 */
	public function serve_ical_export() {
		if ( ! is_singular( 'property' ) || ! get_query_var( 'ical' ) === '' && ! isset( $_GET['ical'] ) ) {
			// fallback check to see if we are on the endpoint
			if ( false === stripos( $_SERVER['REQUEST_URI'], '/ical' ) ) {
				return;
			}
		}

		$property_id = get_the_ID();
		if ( ! $property_id ) {
			return;
		}

		$property_name = get_the_title( $property_id );

		// Fetch all confirmed/pending bookings
		$bookings = new WP_Query( [
			'post_type'      => 'thessnest_booking',
			'posts_per_page' => -1,
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'   => '_booking_property_id',
					'value' => $property_id,
				],
				[
					'key'     => '_booking_status',
					'value'   => [ 'confirmed', 'pending' ],
					'compare' => 'IN',
				]
			]
		] );

		header( 'Content-Type: text/calendar; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="thessnest-property-' . $property_id . '.ics"' );

		echo "BEGIN:VCALENDAR\r\n";
		echo "VERSION:2.0\r\n";
		echo "PRODID:-//ThessNest//NONSGML ThessNest Property Calendar//EN\r\n";
		echo "CALSCALE:GREGORIAN\r\n";

		if ( $bookings->have_posts() ) {
			while ( $bookings->have_posts() ) {
				$bookings->the_post();
				$checkin  = get_post_meta( get_the_ID(), '_booking_checkin', true );
				$checkout = get_post_meta( get_the_ID(), '_booking_checkout', true );
				$status   = get_post_meta( get_the_ID(), '_booking_status', true );

				if ( $checkin && $checkout ) {
					// iCal date format YYYYMMDD
					$dtstart = date( 'Ymd', strtotime( $checkin ) );
					$dtend   = date( 'Ymd', strtotime( $checkout ) );
					$now     = gmdate( 'Ymd\THis\Z' );
					$uid     = get_the_ID() . '@' . $_SERVER['HTTP_HOST'];

					echo "BEGIN:VEVENT\r\n";
					echo "UID:{$uid}\r\n";
					echo "DTSTAMP:{$now}\r\n";
					echo "DTSTART;VALUE=DATE:{$dtstart}\r\n";
					echo "DTEND;VALUE=DATE:{$dtend}\r\n";
					echo "SUMMARY:ThessNest Sync - " . ucfirst( $status ) . "\r\n";
					echo "DESCRIPTION:Booking ID " . get_the_ID() . "\r\n";
					echo "END:VEVENT\r\n";
				}
			}
			wp_reset_postdata();
		}

		echo "END:VCALENDAR\r\n";
		exit;
	}

	/**
	 * 3. Import: Parses an external .ics URL and creates 'blocked' bookings in ThessNest
	 */
	public function parse_external_ical_on_save( $post_id, $post, $update ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		// Look for an iCal import URL meta field (Assume it's set via Redux/MetaBox on the property)
		$ical_url = get_post_meta( $post_id, '_thessnest_ical_import_url', true );

		if ( ! empty( $ical_url ) && filter_var( $ical_url, FILTER_VALIDATE_URL ) ) {
			
			$response = wp_remote_get( $ical_url, [ 'timeout' => 10 ] );
			if ( is_wp_error( $response ) ) {
				return;
			}
			
			$ical_content = wp_remote_retrieve_body( $response );
			if ( empty( $ical_content ) ) {
				return;
			}

			// Delete old "ical_imported" bookings for this property first to avoid infinite duplicates
			global $wpdb;
			$imported_bookings = $wpdb->get_col( $wpdb->prepare( "
				SELECT p.ID FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} pm_prop ON p.ID = pm_prop.post_id AND pm_prop.meta_key = '_booking_property_id'
				INNER JOIN {$wpdb->postmeta} pm_status ON p.ID = pm_status.post_id AND pm_status.meta_key = '_booking_status'
				WHERE p.post_type = 'thessnest_booking'
				AND pm_prop.meta_value = %d
				AND pm_status.meta_value = 'ical_imported'
			", $post_id ) );

			foreach ( $imported_bookings as $booking_id ) {
				wp_delete_post( $booking_id, true );
			}

			// Simple Regex to extract VEVENTs
			preg_match_all( '/BEGIN:VEVENT(.*?)END:VEVENT/s', $ical_content, $events );

			if ( ! empty( $events[1] ) ) {
				foreach ( $events[1] as $event ) {
					preg_match( '/DTSTART(?:;.*?)?:([0-9TZ]+)/i', $event, $start );
					preg_match( '/DTEND(?:;.*?)?:([0-9TZ]+)/i', $event, $end );

					if ( isset( $start[1] ) && isset( $end[1] ) ) {
						// Format: YYYYMMDD
						$checkin  = date( 'Y-m-d', strtotime( substr( $start[1], 0, 8 ) ) );
						$checkout = date( 'Y-m-d', strtotime( substr( $end[1], 0, 8 ) ) );

						// Create new internal pseudo-booking to block availability calendar
						$booking_title = 'iCal Sync Block (' . $checkin . ' to ' . $checkout . ')';
						$new_booking_id = wp_insert_post( [
							'post_title'  => $booking_title,
							'post_type'   => 'thessnest_booking',
							'post_status' => 'publish',
							'post_author' => $post->post_author
						] );

						if ( $new_booking_id && ! is_wp_error( $new_booking_id ) ) {
							update_post_meta( $new_booking_id, '_booking_property_id', $post_id );
							update_post_meta( $new_booking_id, '_booking_checkin', $checkin );
							update_post_meta( $new_booking_id, '_booking_checkout', $checkout );
							update_post_meta( $new_booking_id, '_booking_status', 'ical_imported' );
						}
					}
				}
			}
		}
	}
}

new ThessNest_iCal_Engine();
