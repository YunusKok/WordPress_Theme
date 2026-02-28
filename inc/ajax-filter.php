<?php
/**
 * ThessNest — AJAX Search Filter
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

function thessnest_filter_properties() {
	check_ajax_referer( 'thessnest-nonce', 'security' );

	$args = array(
		'post_type'      => 'property',
		'posts_per_page' => -1, // typically you want pagination, but simplifying for MVP map
		'post_status'    => 'publish',
	);

	$tax_query = array();
	$meta_query = array();

	// Neighborhood
	if ( ! empty( $_POST['neighborhood'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'neighborhood',
			'field'    => 'slug',
			'terms'    => sanitize_text_field( $_POST['neighborhood'] ),
		);
	}

	// Target Group
	if ( ! empty( $_POST['target_group'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'target_group',
			'field'    => 'slug',
			'terms'    => sanitize_text_field( $_POST['target_group'] ),
		);
	}

	// Amenities
	if ( ! empty( $_POST['amenity'] ) && is_array( $_POST['amenity'] ) ) {
		$amenities = array_map( 'sanitize_text_field', wp_unslash( $_POST['amenity'] ) );
		$tax_query[] = array(
			'taxonomy' => 'amenity',
			'field'    => 'slug',
			'terms'    => $amenities,
			'operator' => 'AND',
		);
	}

	if ( count( $tax_query ) > 0 ) {
		$tax_query['relation'] = 'AND';
		$args['tax_query'] = $tax_query;
	}

	// Price
	$price_min = ! empty( $_POST['price_min'] ) ? intval( $_POST['price_min'] ) : 0;
	$price_max = ! empty( $_POST['price_max'] ) ? intval( $_POST['price_max'] ) : 0;

	if ( $price_min > 0 || $price_max > 0 ) {
		$price_meta = array(
			'key'     => '_thessnest_rent',
			'type'    => 'NUMERIC',
			'compare' => 'BETWEEN'
		);
		if ( $price_min > 0 && $price_max > 0 ) {
			$price_meta['value'] = array( $price_min, $price_max );
		} elseif ( $price_min > 0 ) {
			$price_meta['compare'] = '>=';
			$price_meta['value']   = $price_min;
		} else {
			$price_meta['compare'] = '<=';
			$price_meta['value']   = $price_max;
		}
		$meta_query[] = $price_meta;
	}

	if ( count( $meta_query ) > 0 ) {
		$meta_query['relation'] = 'AND';
		$args['meta_query'] = $meta_query;
	}

	$query = new WP_Query( $args );

	$html = '';
	$markers = array();

	if ( $query->have_posts() ) {
		ob_start();
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/property-card' );

			// Marker logic
			$prop_id = get_the_ID();
			$lat = get_post_meta( $prop_id, '_thessnest_latitude', true );
			$lng = get_post_meta( $prop_id, '_thessnest_longitude', true );

			if ( ! $lat || ! $lng ) {
				$lat = 40.6401 + ( ( rand(0, 100) - 50 ) / 3000 );
				$lng = 22.9444 + ( ( rand(0, 100) - 50 ) / 3000 );
			}

			$img_url = has_post_thumbnail() ? get_the_post_thumbnail_url( $prop_id, 'medium' ) : '';
			$price   = thessnest_format_price( thessnest_get_meta( 'rent', $prop_id ) );

			$markers[] = array(
				'id'    => $prop_id,
				'title' => html_entity_decode( get_the_title() ),
				'url'   => get_the_permalink(),
				'lat'   => (float) $lat,
				'lng'   => (float) $lng,
				'img'   => $img_url,
				'price' => $price,
			);
		}
		$html = ob_get_clean();
	}

	wp_reset_postdata();

	wp_send_json_success( array(
		'html'    => $html,
		'markers' => $markers,
		'count'   => $query->found_posts,
	) );
}
add_action( 'wp_ajax_thessnest_filter_properties', 'thessnest_filter_properties' );
add_action( 'wp_ajax_nopriv_thessnest_filter_properties', 'thessnest_filter_properties' );
