<?php
/**
 * ThessNest — Availability Calendar UI
 *
 * Integrates Flatpickr to visually block booked dates on the single property page.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1. Enqueue Flatpickr Assets & Inject Disabled Dates
 */
function thessnest_enqueue_availability_calendar() {
	if ( ! is_singular( 'property' ) ) {
		return;
	}

	global $post;
	$property_id = $post->ID;

	// Load Flatpickr
	wp_enqueue_style( 'flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.13' );
	wp_enqueue_style( 'flatpickr-airbnb', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css', array('flatpickr'), '4.6.13' ); // Premium theme
	wp_enqueue_script( 'flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.13', true );

	// Fetch Confirmed Bookings for this property
	$disabled_dates = array();
	
	$bookings = new WP_Query( array(
		'post_type'      => 'thessnest_booking',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_booking_property_id',
				'value'   => $property_id,
				'compare' => '='
			),
			array(
				'key'     => '_booking_status',
				'value'   => array( 'confirmed', 'ical_imported' ),
				'compare' => 'IN',
			)
		)
	) );

	if ( $bookings->have_posts() ) {
		while ( $bookings->have_posts() ) {
			$bookings->the_post();
			$checkin  = get_post_meta( get_the_ID(), '_booking_checkin', true );
			$checkout = get_post_meta( get_the_ID(), '_booking_checkout', true );
			if ( $checkin && $checkout ) {
				$disabled_dates[] = array(
					'from' => $checkin,
					'to'   => $checkout
				);
			}
		}
		wp_reset_postdata();
	}

	// Prepare data for JS
	wp_localize_script( 'flatpickr', 'thessnestCalendarData', array(
		'disabledDates' => $disabled_dates,
		'minDate'       => date('Y-m-d')
	) );

	// Inline Initialization Script
	wp_add_inline_script( 'flatpickr', "
	document.addEventListener('DOMContentLoaded', function() {
		if (typeof flatpickr !== 'undefined') {
			var checkinInput = document.getElementById('booking_checkin');
			var checkoutInput = document.getElementById('booking_checkout');
			
			if (checkinInput && checkoutInput) {
				var disabledList = thessnestCalendarData.disabledDates;
				
				var checkinPicker = flatpickr(checkinInput, {
					minDate: thessnestCalendarData.minDate,
					disable: disabledList,
					dateFormat: 'Y-m-d',
					onChange: function(selectedDates, dateStr, instance) {
						checkoutPicker.set('minDate', dateStr);
						checkoutInput.removeAttribute('disabled');
						checkoutPicker.open();
					}
				});

				var checkoutPicker = flatpickr(checkoutInput, {
					minDate: thessnestCalendarData.minDate,
					disable: disabledList,
					dateFormat: 'Y-m-d',
					onChange: function(selectedDates, dateStr, instance) {
						// Optionally trigger price calculation
						if(typeof calculateBookingPrice === 'function') calculateBookingPrice();
					}
				});
			}
		}
	});
	" );
}
add_action( 'wp_enqueue_scripts', 'thessnest_enqueue_availability_calendar', 20 );
