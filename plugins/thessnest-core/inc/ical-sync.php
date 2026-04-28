<?php
/**
 * ThessNest — iCal Sync Engine
 *
 * Two-way iCal synchronization with external platforms:
 *   • EXPORT — Serves .ics endpoint at /property/{slug}/ical/
 *   • IMPORT — Fetches external .ics feeds and blocks dates on the calendar
 *   • CRON   — WP-Cron job auto-syncs all feeds at a configurable interval (default: 4 hours)
 *
 * Supports multiple feeds per property (Airbnb + Booking.com + HousingAnywhere, etc.)
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_iCal_Engine {

	/** @var string WP-Cron hook name */
	const CRON_HOOK = 'thessnest_ical_sync_event';

	/** @var string Custom cron schedule name */
	const CRON_SCHEDULE = 'thessnest_ical_interval';

	public function __construct() {
		// ── Export ──
		add_action( 'init', [ $this, 'add_ical_endpoint' ] );
		add_action( 'template_redirect', [ $this, 'serve_ical_export' ] );

		// ── Import on property save ──
		add_action( 'save_post_property', [ $this, 'parse_external_ical_on_save' ], 20, 3 );

		// ── WP-Cron ──
		add_filter( 'cron_schedules', [ $this, 'register_cron_interval' ] );
		add_action( 'init', [ $this, 'maybe_schedule_cron' ] );
		add_action( self::CRON_HOOK, [ $this, 'sync_all_properties' ] );

		// ── AJAX manual sync ──
		add_action( 'wp_ajax_thessnest_ical_sync_now', [ $this, 'ajax_sync_now' ] );
	}

	/* =========================================================================
	   1. EXPORT — /property/{slug}/ical/ endpoint
	   ========================================================================= */

	/**
	 * Register /ical endpoint on the property post type.
	 */
	public function add_ical_endpoint() {
		add_rewrite_endpoint( 'ical', EP_PERMALINK );
	}

	/**
	 * Serve the .ics file dynamically.
	 */
	public function serve_ical_export() {
		if ( ! is_singular( 'property' ) ) {
			return;
		}

		// Check if /ical endpoint is being requested
		$ical_qv = get_query_var( 'ical', null );
		if ( $ical_qv === null && false === stripos( $_SERVER['REQUEST_URI'], '/ical' ) ) {
			return;
		}

		$property_id   = get_the_ID();
		if ( ! $property_id ) {
			return;
		}

		$property_name = get_the_title( $property_id );

		// Fetch all confirmed/pending/ical_imported bookings
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
					'value'   => [ 'confirmed', 'pending', 'ical_imported' ],
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
		echo "X-WR-CALNAME:" . sanitize_text_field( $property_name ) . "\r\n";

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
					$uid     = get_the_ID() . '@' . sanitize_text_field( $_SERVER['HTTP_HOST'] );

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

	/* =========================================================================
	   2. IMPORT — Parse external .ics feeds
	   ========================================================================= */

	/**
	 * Hook: Import feeds when a property post is saved in wp-admin.
	 */
	public function parse_external_ical_on_save( $post_id, $post, $update ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		self::import_feeds_for_property( $post_id );
	}

	/**
	 * Core import logic — reusable by both save_post and WP-Cron.
	 *
	 * Reads all iCal feed URLs for the given property, fetches each,
	 * deletes old ical_imported bookings, and creates new blocked bookings.
	 *
	 * @param int $post_id Property post ID.
	 * @return array Summary [ 'imported' => int, 'errors' => array ]
	 */
	public static function import_feeds_for_property( $post_id ) {
		$result = [ 'imported' => 0, 'errors' => [] ];

		// Collect feed URLs from new format and legacy format
		$feeds = self::get_feeds_for_property( $post_id );

		if ( empty( $feeds ) ) {
			return $result;
		}

		// Delete old "ical_imported" bookings for this property to avoid duplicates
		self::delete_imported_bookings( $post_id );

		$property_author = get_post_field( 'post_author', $post_id );

		foreach ( $feeds as $feed ) {
			$url  = $feed['url'];
			$name = ! empty( $feed['name'] ) ? $feed['name'] : 'External Calendar';

			if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
				$result['errors'][] = sprintf( 'Invalid URL for feed "%s" on property #%d', $name, $post_id );
				continue;
			}

			$response = wp_remote_get( $url, [
				'timeout'    => 15,
				'user-agent' => 'ThessNest iCal Sync/1.0',
			] );

			if ( is_wp_error( $response ) ) {
				$error_msg = sprintf(
					'[ThessNest iCal] Failed to fetch feed "%s" for property #%d: %s',
					$name, $post_id, $response->get_error_message()
				);
				error_log( $error_msg );
				$result['errors'][] = $error_msg;
				continue;
			}

			$status_code = wp_remote_retrieve_response_code( $response );
			if ( $status_code !== 200 ) {
				$error_msg = sprintf(
					'[ThessNest iCal] HTTP %d fetching feed "%s" for property #%d',
					$status_code, $name, $post_id
				);
				error_log( $error_msg );
				$result['errors'][] = $error_msg;
				continue;
			}

			$ical_content = wp_remote_retrieve_body( $response );
			if ( empty( $ical_content ) ) {
				continue;
			}

			// Parse VEVENTs
			preg_match_all( '/BEGIN:VEVENT(.*?)END:VEVENT/s', $ical_content, $events );

			if ( ! empty( $events[1] ) ) {
				foreach ( $events[1] as $event ) {
					preg_match( '/DTSTART(?:;.*?)?:([0-9TZ]+)/i', $event, $start );
					preg_match( '/DTEND(?:;.*?)?:([0-9TZ]+)/i', $event, $end );
					preg_match( '/SUMMARY[;:]?(.*)/i', $event, $summary_match );

					if ( isset( $start[1] ) && isset( $end[1] ) ) {
						$checkin  = date( 'Y-m-d', strtotime( substr( $start[1], 0, 8 ) ) );
						$checkout = date( 'Y-m-d', strtotime( substr( $end[1], 0, 8 ) ) );

						// Skip past events
						if ( strtotime( $checkout ) < time() ) {
							continue;
						}

						$event_summary = ! empty( $summary_match[1] ) ? trim( $summary_match[1] ) : '';
						$booking_title = sprintf(
							'iCal Sync: %s (%s → %s) [%s]',
							$name, $checkin, $checkout, $event_summary
						);

						$new_booking_id = wp_insert_post( [
							'post_title'  => $booking_title,
							'post_type'   => 'thessnest_booking',
							'post_status' => 'publish',
							'post_author' => $property_author ? $property_author : 1,
						] );

						if ( $new_booking_id && ! is_wp_error( $new_booking_id ) ) {
							update_post_meta( $new_booking_id, '_booking_property_id', $post_id );
							update_post_meta( $new_booking_id, '_booking_checkin', $checkin );
							update_post_meta( $new_booking_id, '_booking_checkout', $checkout );
							update_post_meta( $new_booking_id, '_booking_status', 'ical_imported' );
							update_post_meta( $new_booking_id, '_booking_ical_source', $name );
							$result['imported']++;
						}
					}
				}
			}
		}

		// Update last sync timestamp
		update_post_meta( $post_id, '_thessnest_ical_last_sync', current_time( 'timestamp' ) );

		return $result;
	}

	/**
	 * Get all iCal feed URLs for a property.
	 * Supports both new array format and legacy single URL.
	 *
	 * @param int $post_id
	 * @return array [ [ 'name' => '', 'url' => '' ], ... ]
	 */
	public static function get_feeds_for_property( $post_id ) {
		$feeds = [];

		// New format: array of feeds
		$stored_feeds = get_post_meta( $post_id, '_thessnest_ical_feeds', true );
		if ( is_array( $stored_feeds ) && ! empty( $stored_feeds ) ) {
			foreach ( $stored_feeds as $feed ) {
				if ( ! empty( $feed['url'] ) ) {
					$feeds[] = [
						'name' => ! empty( $feed['name'] ) ? $feed['name'] : 'External Calendar',
						'url'  => $feed['url'],
					];
				}
			}
		}

		// Legacy format: single URL (backward compat)
		if ( empty( $feeds ) ) {
			$legacy_url = get_post_meta( $post_id, '_thessnest_ical_import_url', true );
			if ( ! empty( $legacy_url ) && filter_var( $legacy_url, FILTER_VALIDATE_URL ) ) {
				$feeds[] = [
					'name' => 'Legacy Import',
					'url'  => $legacy_url,
				];
			}
		}

		return $feeds;
	}

	/**
	 * Delete all previously imported (ical_imported) bookings for a property.
	 */
	private static function delete_imported_bookings( $post_id ) {
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
	}

	/* =========================================================================
	   3. WP-CRON — Scheduled auto-sync
	   ========================================================================= */

	/**
	 * Register custom cron interval based on Redux setting.
	 */
	public function register_cron_interval( $schedules ) {
		$hours = thessnest_get_ical_sync_hours();

		$schedules[ self::CRON_SCHEDULE ] = [
			'interval' => $hours * HOUR_IN_SECONDS,
			'display'  => sprintf(
				/* translators: %d = number of hours */
				__( 'Every %d hours (ThessNest iCal Sync)', 'thessnest' ),
				$hours
			),
		];

		return $schedules;
	}

	/**
	 * Ensure the cron event is scheduled.
	 * If the interval changed via Redux, reschedule.
	 */
	public function maybe_schedule_cron() {
		// Check if sync is enabled
		if ( ! thessnest_is_ical_sync_enabled() ) {
			// Sync disabled — unschedule if scheduled
			$timestamp = wp_next_scheduled( self::CRON_HOOK );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, self::CRON_HOOK );
			}
			return;
		}

		$timestamp = wp_next_scheduled( self::CRON_HOOK );

		if ( ! $timestamp ) {
			wp_schedule_event( time(), self::CRON_SCHEDULE, self::CRON_HOOK );
			return;
		}

		// Check if interval changed — if so, reschedule
		$current_hours = thessnest_get_ical_sync_hours();
		$stored_hours  = get_option( 'thessnest_ical_sync_hours_cached', 4 );

		if ( (int) $current_hours !== (int) $stored_hours ) {
			wp_unschedule_event( $timestamp, self::CRON_HOOK );
			wp_schedule_event( time(), self::CRON_SCHEDULE, self::CRON_HOOK );
			update_option( 'thessnest_ical_sync_hours_cached', $current_hours );
		}
	}

	/**
	 * Cron callback — sync all properties that have iCal feeds configured.
	 */
	public function sync_all_properties() {
		$start_time = microtime( true );

		// Find all properties with iCal feeds (new or legacy)
		$properties = get_posts( [
			'post_type'      => 'property',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => [
				'relation' => 'OR',
				[
					'key'     => '_thessnest_ical_feeds',
					'compare' => 'EXISTS',
				],
				[
					'key'     => '_thessnest_ical_import_url',
					'value'   => '',
					'compare' => '!=',
				],
			],
		] );

		$total_imported = 0;
		$total_errors   = 0;

		foreach ( $properties as $property_id ) {
			$result = self::import_feeds_for_property( $property_id );
			$total_imported += $result['imported'];
			$total_errors   += count( $result['errors'] );
		}

		$elapsed = round( microtime( true ) - $start_time, 2 );

		error_log( sprintf(
			'[ThessNest iCal Cron] Sync complete: %d properties processed, %d events imported, %d errors, %.2fs elapsed.',
			count( $properties ), $total_imported, $total_errors, $elapsed
		) );
	}

	/* =========================================================================
	   4. AJAX — Manual Sync from Admin
	   ========================================================================= */

	/**
	 * AJAX handler: Manually trigger sync for a single property.
	 */
	public function ajax_sync_now() {
		check_ajax_referer( 'thessnest_ical_sync_nonce', 'security' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'thessnest' ) ] );
		}

		$post_id = isset( $_POST['property_id'] ) ? intval( $_POST['property_id'] ) : 0;
		if ( ! $post_id || 'property' !== get_post_type( $post_id ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid property ID.', 'thessnest' ) ] );
		}

		$result = self::import_feeds_for_property( $post_id );

		$last_sync = get_post_meta( $post_id, '_thessnest_ical_last_sync', true );
		$formatted = $last_sync ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_sync ) : '—';

		wp_send_json_success( [
			'message'   => sprintf(
				/* translators: %d = number of events imported */
				__( 'Sync complete: %d events imported.', 'thessnest' ),
				$result['imported']
			),
			'errors'    => $result['errors'],
			'last_sync' => $formatted,
		] );
	}

	/* =========================================================================
	   5. ACTIVATION / DEACTIVATION
	   ========================================================================= */

	/**
	 * Schedule cron on plugin activation.
	 */
	public static function activate() {
		if ( thessnest_is_ical_sync_enabled() && ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time(), self::CRON_SCHEDULE, self::CRON_HOOK );
		}
	}

	/**
	 * Clear cron on plugin deactivation.
	 */
	public static function deactivate() {
		$timestamp = wp_next_scheduled( self::CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::CRON_HOOK );
		}
	}
}

/* =========================================================================
   HELPER FUNCTIONS — Used by Redux config and the engine
   ========================================================================= */

/**
 * Get the iCal sync interval in hours from Redux options.
 *
 * @return int Hours between syncs (default: 4).
 */
function thessnest_get_ical_sync_hours() {
	global $thessnest_opts;
	return isset( $thessnest_opts['ical_sync_interval'] ) ? intval( $thessnest_opts['ical_sync_interval'] ) : 4;
}

/**
 * Check if iCal auto-sync is enabled via Redux options.
 *
 * @return bool
 */
function thessnest_is_ical_sync_enabled() {
	global $thessnest_opts;
	// Default to enabled (true) if the option hasn't been set yet
	if ( ! isset( $thessnest_opts['ical_sync_enabled'] ) ) {
		return true;
	}
	return (bool) $thessnest_opts['ical_sync_enabled'];
}

// ── Initialize ──
new ThessNest_iCal_Engine();

// ── Activation/Deactivation hooks (must be in the main plugin file, but we register them here for locality) ──
register_activation_hook( THESSNEST_CORE_DIR . 'thessnest-core.php', [ 'ThessNest_iCal_Engine', 'activate' ] );
register_deactivation_hook( THESSNEST_CORE_DIR . 'thessnest-core.php', [ 'ThessNest_iCal_Engine', 'deactivate' ] );
