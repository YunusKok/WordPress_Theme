<?php
/**
 * ThessNest — Native SEO Tags
 *
 * Injects dynamic Meta Title, Description, and Keywords into <head>.
 * Built to adapt to the configured primary city and target audience.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Generate and output SEO Meta Tags in wp_head.
 */
function thessnest_native_seo_tags() {
	$primary_city    = function_exists('thessnest_opt') ? thessnest_opt('primary_city', 'your city') : 'your city';
	$primary_country = function_exists('thessnest_opt') ? thessnest_opt('primary_country', '') : '';
	$target_audience = function_exists('thessnest_opt') ? thessnest_opt('target_audience', 'Erasmus students, digital nomads') : 'Erasmus students, digital nomads';

	// Base Keyword Clusters
	$base_keywords = sprintf( '%s housing %s, digital nomad apartments %s, short term rental fast wifi %s, remote work apartment %s', $target_audience, $primary_city, $primary_city, $primary_city, $primary_city );

	$description = '';
	$keywords    = $base_keywords;

	if ( is_front_page() || is_home() ) {
		$description = sprintf( 'Verified mid-term rentals & student accommodation with no agency fee. Find laptop-friendly workspace digital nomad apartments and flexible checkout mid-term rent in %s.', $primary_city );
		$keywords    = $base_keywords . ', ' . sprintf( 'student flat with utilities included %s, instant book mid-term rentals %s', $primary_city, $primary_city );
	} elseif ( is_singular( 'property' ) ) {
		global $post;
		$title = get_the_title( $post->ID );
		
		// Fallback description based on title + trust keywords
		$description = sprintf( 'Rent %s in %s. Safe rooms & digital nomad flat with high-speed internet monthly rental. Verified landlord apartments.', esc_attr( $title ), $primary_city );
		$keywords    = $title . ', rent apartment abroad without viewing, student apartment with washing machine ' . $primary_city . ', fully equipped kitchen digital nomad housing ' . $primary_city . ', ' . $base_keywords;
	} elseif ( is_post_type_archive( 'property' ) || is_tax( array( 'neighborhood', 'amenity', 'target_group' ) ) ) {
		$description = sprintf( 'Browse verified student apartments and digital nomad housing in %s. Filter by safest neighborhoods, deposit-free nomad housing, and flexible mid-term rentals.', $primary_city );
		$keywords    = sprintf( 'student housing near supermarkets %s, coworking friendly apartments %s, safest neighborhoods for international students %s, deposit-free nomad housing %s, %s', $primary_city, $primary_city, $primary_city, $primary_city, $base_keywords );
	} else {
		// Generic fallback
		$description = sprintf( 'ThessNest: Premium midterm and short term rental fast wifi in %s.', $primary_city );
	}

	echo "\n<!-- ThessNest Native SEO -->\n";
	if ( ! empty( $description ) ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	if ( ! empty( $keywords ) ) {
		echo '<meta name="keywords" content="' . esc_attr( $keywords ) . '">' . "\n";
	}
	
	// Add canonical link fallback if WP core or a plugin doesn't add it
	if ( ! has_action( 'wp_head', 'rel_canonical' ) ) {
		global $wp;
		$current_url = home_url( add_query_arg( array(), $wp->request ) );
		echo '<link rel="canonical" href="' . esc_url( $current_url ) . '">' . "\n";
	}

	// JSON-LD Schema Markup for Single Properties
	if ( is_singular( 'property' ) ) {
		global $post;
		$prop_id    = $post->ID;
		$rent       = get_post_meta( $prop_id, '_thessnest_rent', true );
		$avg_rating = get_post_meta( $prop_id, '_thessnest_average_rating', true );
		$rev_count  = get_post_meta( $prop_id, '_thessnest_review_count', true );
		$lat        = get_post_meta( $prop_id, '_thessnest_latitude', true );
		$lng        = get_post_meta( $prop_id, '_thessnest_longitude', true );
		$nb         = wp_get_post_terms( $prop_id, 'neighborhood', array( 'fields' => 'names' ) );
		$neighborhood_name = ( ! is_wp_error( $nb ) && ! empty( $nb ) ) ? $nb[0] : $primary_city;
		$img_url    = get_the_post_thumbnail_url( $prop_id, 'large' );

		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Accommodation',
			'name'     => get_the_title( $prop_id ),
			'description' => wp_strip_all_tags( get_the_excerpt( $prop_id ) ?: wp_trim_words( $post->post_content, 30 ) ),
			'url'      => get_the_permalink( $prop_id ),
		);

		if ( $img_url ) {
			$schema['image'] = $img_url;
		}

		if ( $lat && $lng ) {
			$schema['geo'] = array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => (float) $lat,
				'longitude' => (float) $lng,
			);
		}

		$schema['address'] = array(
			'@type'           => 'PostalAddress',
			'addressLocality' => $primary_city,
			'addressRegion'   => $neighborhood_name,
			'addressCountry'  => $primary_country,
		);

		if ( $rent ) {
			$schema['offers'] = array(
				'@type'         => 'Offer',
				'price'         => (float) $rent,
				'priceCurrency' => function_exists('thessnest_opt') ? thessnest_opt('payment_currency', 'EUR') : 'EUR',
				'availability'  => 'https://schema.org/InStock',
				'priceValidUntil' => date( 'Y-12-31' ),
			);
		}

		if ( $avg_rating && $rev_count ) {
			$schema['aggregateRating'] = array(
				'@type'       => 'AggregateRating',
				'ratingValue' => (float) $avg_rating,
				'reviewCount' => (int) $rev_count,
				'bestRating'  => 5,
				'worstRating' => 1,
			);
		}

		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>' . "\n";
	}

	echo "<!-- /ThessNest Native SEO -->\n";
}
add_action( 'wp_head', 'thessnest_native_seo_tags', 2 ); // Priority 2 to load early in head
