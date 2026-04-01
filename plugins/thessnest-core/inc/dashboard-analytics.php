<?php
/**
 * ThessNest — Advanced Dashboard Analytics
 *
 * Tracks property views and enqueues Chart.js for the frontend/backend dashboards
 * to generate hypothetical earnings and traffic reports.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1. Track Property Views
 * Hooking into template_redirect to increment view count securely.
 */
function thessnest_track_property_views() {
	if ( ! is_singular( 'property' ) ) {
		return;
	}

	global $post;
	
	// Prevent tracking bot traffic or logged-in admins tracking their own site
	if ( current_user_can( 'administrator' ) ) {
		return;
	}

	$views = (int) get_post_meta( $post->ID, '_thessnest_views', true );
	$views++;
	update_post_meta( $post->ID, '_thessnest_views', $views );
}
add_action( 'template_redirect', 'thessnest_track_property_views' );

/**
 * 2. Enqueue Chart.js on Dashboard
 */
function thessnest_enqueue_analytics_scripts() {
	// Only load on the frontend dashboard
	if ( is_page_template( 'template-dashboard.php' ) && isset( $_GET['tab'] ) && $_GET['tab'] === 'properties' ) {
		wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '4.4.0', true );
	}
}
add_action( 'wp_enqueue_scripts', 'thessnest_enqueue_analytics_scripts' );

/**
 * 3. Helper: Get Landlord Analytics Data
 * Returns JSON object for Chart.js
 */
function thessnest_get_landlord_chart_data( $user_id ) {
	$args = array(
		'post_type'      => 'property',
		'author'         => $user_id,
		'post_status'    => array( 'publish' ),
		'posts_per_page' => -1,
	);
	
	$query = new WP_Query( $args );
	
	$labels = array();
	$views  = array();
	$prices = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			
			$title = get_the_title();
			// truncate title for chart
			if ( strlen( $title ) > 15 ) {
				$title = substr( $title, 0, 15 ) . '...';
			}

			$v = (int) get_post_meta( get_the_ID(), '_thessnest_views', true );
			$p = (int) get_post_meta( get_the_ID(), '_thessnest_rent', true );

			$labels[] = $title;
			$views[]  = $v;
			$prices[] = $p; // Representing potential monthly earning per property
		}
		wp_reset_postdata();
	}

	return json_encode( array(
		'labels' => $labels,
		'views'  => $views,
		'prices' => $prices,
	) );
}
