<?php
/**
 * ThessNest — Native SEO Tags
 *
 * Injects dynamic Meta Title, Description, and Keywords into <head>.
 * Built specifically for Erasmus / Digital Nomad keywords in Thessaloniki.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Generate and output SEO Meta Tags in wp_head.
 */
function thessnest_native_seo_tags() {
	// Base Keyword Clusters
	$base_keywords = 'Erasmus housing Thessaloniki, digital nomad apartments Thessaloniki, student accommodation no agency fee, verified landlord student apartments Thessaloniki, short term rental fast wifi Thessaloniki';

	$description = '';
	$keywords    = $base_keywords;

	if ( is_front_page() || is_home() ) {
		$description = 'Verified mid-term rentals & student accommodation with no agency fee. Find laptop-friendly workspace digital nomad apartments, safe student rooms near Aristotle University, and flexible checkout mid-term rent in Thessaloniki.';
		$keywords    = $base_keywords . ', accommodation proof for D-type student visa, Erasmus internship housing Thessaloniki, student flat with utilities included Thessaloniki, flatmate search Erasmus Thessaloniki, student rental contract requirements Greece';
	} elseif ( is_singular( 'property' ) ) {
		global $post;
		$title = get_the_title( $post->ID );
		
		// Fallback description based on title + trust keywords
		$description = sprintf( 'Rent %s in Thessaloniki. Safe student rooms & digital nomad flat with high-speed internet monthly rental. Verified landlord student apartments.', esc_attr( $title ) );
		$keywords    = $title . ', rent apartment abroad without viewing, how to rent a flat in Greece as a foreigner, student apartment with washing machine Thessaloniki, ' . $base_keywords;
	} elseif ( is_post_type_archive( 'property' ) || is_tax( array( 'neighborhood', 'amenity', 'target_group' ) ) ) {
		$description = 'Browse verified student apartments and digital nomad housing in Thessaloniki. Filter by safest neighborhoods for international students, deposit-free nomad housing, and flats with dishwasher and oven mid-term.';
		$keywords    = 'student housing near cheap supermarkets Thessaloniki, Erasmus flat near grocery stores Thessaloniki, coworking friendly apartments Thessaloniki, ' . $base_keywords;
	} else {
		// Generic fallback
		$description = 'ThessNest: Premium midterm and short term rental fast wifi in Thessaloniki. Accommodation proof for D-type student visa.';
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
	echo "<!-- /ThessNest Native SEO -->\n";
}
add_action( 'wp_head', 'thessnest_native_seo_tags', 2 ); // Priority 2 to load early in head
