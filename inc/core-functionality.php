<?php
/**
 * ThessNest — Core Functionality
 *
 * Registers Custom Post Types and Taxonomies.
 * Ideally, in a deployed enterprise environment, this file
 * should be moved to an independent Must-Use (mu-plugin) or standard plugin.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/* ==========================================================================
   1. CUSTOM POST TYPE — Properties
   ========================================================================== */

/**
 * Register the "property" Custom Post Type.
 *
 * - Publicly queryable, has archive, REST-enabled.
 * - Slug: /properties/
 * - Dashboard icon: dashicons-building.
 */
function thessnest_register_property_cpt() {

	$labels = array(
		'name'                  => _x( 'Properties', 'Post type general name',   'thessnest' ),
		'singular_name'         => _x( 'Property',   'Post type singular name',  'thessnest' ),
		'menu_name'             => _x( 'Properties', 'Admin Menu text',          'thessnest' ),
		'add_new'               => __( 'Add New Property',  'thessnest' ),
		'add_new_item'          => __( 'Add New Property',  'thessnest' ),
		'edit_item'             => __( 'Edit Property',     'thessnest' ),
		'new_item'              => __( 'New Property',      'thessnest' ),
		'view_item'             => __( 'View Property',     'thessnest' ),
		'view_items'            => __( 'View Properties',   'thessnest' ),
		'search_items'          => __( 'Search Properties', 'thessnest' ),
		'not_found'             => __( 'No properties found.', 'thessnest' ),
		'not_found_in_trash'    => __( 'No properties found in Trash.', 'thessnest' ),
		'all_items'             => __( 'All Properties',    'thessnest' ),
		'archives'              => __( 'Property Archives', 'thessnest' ),
		'featured_image'        => __( 'Property Image',    'thessnest' ),
		'set_featured_image'    => __( 'Set property image','thessnest' ),
		'remove_featured_image' => __( 'Remove property image', 'thessnest' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,     // Gutenberg + REST API support
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'properties', 'with_front' => false ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-building',
		'supports'           => array(
			'title',
			'editor',
			'thumbnail',
			'excerpt',
			'custom-fields',
			'revisions',
		),
	);

	register_post_type( 'property', $args );
}
add_action( 'init', 'thessnest_register_property_cpt' );


/* ==========================================================================
   2. CUSTOM TAXONOMIES
   ========================================================================== */

/**
 * Register hierarchical "Neighborhood" taxonomy.
 *
 * Used to categorise properties by area in Thessaloniki
 * (e.g., Ladadika, Ano Poli, Kalamaria).
 */
function thessnest_register_neighborhood_taxonomy() {

	$labels = array(
		'name'              => _x( 'Neighborhoods', 'taxonomy general name', 'thessnest' ),
		'singular_name'     => _x( 'Neighborhood',  'taxonomy singular name', 'thessnest' ),
		'search_items'      => __( 'Search Neighborhoods',  'thessnest' ),
		'all_items'         => __( 'All Neighborhoods',     'thessnest' ),
		'parent_item'       => __( 'Parent Neighborhood',   'thessnest' ),
		'parent_item_colon' => __( 'Parent Neighborhood:',  'thessnest' ),
		'edit_item'         => __( 'Edit Neighborhood',     'thessnest' ),
		'update_item'       => __( 'Update Neighborhood',   'thessnest' ),
		'add_new_item'      => __( 'Add New Neighborhood',  'thessnest' ),
		'new_item_name'     => __( 'New Neighborhood Name', 'thessnest' ),
		'menu_name'         => __( 'Neighborhoods',         'thessnest' ),
	);

	register_taxonomy( 'neighborhood', array( 'property' ), array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'neighborhood' ),
	) );
}
add_action( 'init', 'thessnest_register_neighborhood_taxonomy' );


/**
 * Register non-hierarchical "Amenity" taxonomy (tag-style).
 *
 * Examples: Fast Wi-Fi, Dedicated Workspace, Washing Machine, Balcony.
 */
function thessnest_register_amenity_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Amenities', 'taxonomy general name', 'thessnest' ),
		'singular_name'              => _x( 'Amenity',   'taxonomy singular name', 'thessnest' ),
		'search_items'               => __( 'Search Amenities',   'thessnest' ),
		'popular_items'              => __( 'Popular Amenities',  'thessnest' ),
		'all_items'                   => __( 'All Amenities',     'thessnest' ),
		'edit_item'                  => __( 'Edit Amenity',       'thessnest' ),
		'update_item'                => __( 'Update Amenity',     'thessnest' ),
		'add_new_item'               => __( 'Add New Amenity',    'thessnest' ),
		'new_item_name'              => __( 'New Amenity Name',   'thessnest' ),
		'separate_items_with_commas' => __( 'Separate amenities with commas', 'thessnest' ),
		'add_or_remove_items'        => __( 'Add or remove amenities',        'thessnest' ),
		'choose_from_most_used'      => __( 'Choose from the most used amenities', 'thessnest' ),
		'menu_name'                  => __( 'Amenities', 'thessnest' ),
	);

	register_taxonomy( 'amenity', array( 'property' ), array(
		'labels'            => $labels,
		'hierarchical'      => false,   // Tag-style
		'public'            => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'amenity' ),
	) );
}
add_action( 'init', 'thessnest_register_amenity_taxonomy' );


/**
 * Register hierarchical "Target Group" taxonomy.
 *
 * Values: Student, Digital Nomad, Expat.
 */
function thessnest_register_target_group_taxonomy() {

	$labels = array(
		'name'              => _x( 'Target Groups', 'taxonomy general name', 'thessnest' ),
		'singular_name'     => _x( 'Target Group',  'taxonomy singular name', 'thessnest' ),
		'search_items'      => __( 'Search Target Groups',  'thessnest' ),
		'all_items'         => __( 'All Target Groups',     'thessnest' ),
		'parent_item'       => __( 'Parent Target Group',   'thessnest' ),
		'parent_item_colon' => __( 'Parent Target Group:',  'thessnest' ),
		'edit_item'         => __( 'Edit Target Group',     'thessnest' ),
		'update_item'       => __( 'Update Target Group',   'thessnest' ),
		'add_new_item'      => __( 'Add New Target Group',  'thessnest' ),
		'new_item_name'     => __( 'New Target Group Name', 'thessnest' ),
		'menu_name'         => __( 'Target Groups',         'thessnest' ),
	);

	register_taxonomy( 'target_group', array( 'property' ), array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'target-group' ),
	) );
}
add_action( 'init', 'thessnest_register_target_group_taxonomy' );


/* ==========================================================================
   3. ARCHIVE FILTERS (WP_Query Modification)
   ========================================================================== */

/**
 * Modify the main query for property archives based on $_GET parameters.
 *
 * @param WP_Query $query The current query object.
 */
function thessnest_filter_properties( $query ) {

	// Only modify the main query on the frontend for property archives
	if ( is_admin() || ! $query->is_main_query() || ! is_post_type_archive( 'property' ) && ! is_tax( array( 'neighborhood', 'amenity', 'target_group' ) ) ) {
		return;
	}

	// 1. Meta Query for Price Range (requires ACF/Custom Field 'rent')
	$meta_query = $query->get( 'meta_query' );
	if ( ! is_array( $meta_query ) ) {
		$meta_query = array( 'relation' => 'AND' );
	}

	$has_price_filter = false;
	$price_meta_query = array(
		'key'     => 'rent',
		'type'    => 'NUMERIC',
	);

	if ( isset( $_GET['price_min'] ) && is_numeric( $_GET['price_min'] ) && $_GET['price_min'] > 0 ) {
		$price_meta_query['value']   = (int) $_GET['price_min'];
		$price_meta_query['compare'] = '>=';
		$has_price_filter            = true;
	}

	if ( isset( $_GET['price_max'] ) && is_numeric( $_GET['price_max'] ) && $_GET['price_max'] > 0 ) {
		if ( $has_price_filter ) {
			// If we have both min and max, create a BETWEEN query
			$price_meta_query['value']   = array( (int) $_GET['price_min'], (int) $_GET['price_max'] );
			$price_meta_query['compare'] = 'BETWEEN';
		} else {
			$price_meta_query['value']   = (int) $_GET['price_max'];
			$price_meta_query['compare'] = '<=';
			$has_price_filter            = true;
		}
	}

	if ( $has_price_filter ) {
		$meta_query[] = $price_meta_query;
		$query->set( 'meta_query', $meta_query );
	}


	// 2. Tax Query for Amenities (Array of slugs)
	if ( isset( $_GET['amenity'] ) && is_array( $_GET['amenity'] ) ) {
		$tax_query = $query->get( 'tax_query' );
		if ( ! is_array( $tax_query ) ) {
			$tax_query = array( 'relation' => 'AND' );
		}

		$amenities = array_map( 'sanitize_text_field', wp_unslash( $_GET['amenity'] ) );

		$tax_query[] = array(
			'taxonomy' => 'amenity',
			'field'    => 'slug',
			'terms'    => $amenities,
			'operator' => 'IN',
		);

		$query->set( 'tax_query', $tax_query );
	}
}
add_action( 'pre_get_posts', 'thessnest_filter_properties' );


/* ==========================================================================
   4. SEO & BREADCRUMBS
   ========================================================================== */

/**
 * Generate Schema.org compliant breadcrumbs.
 */
function thessnest_breadcrumbs() {
	if ( is_front_page() || is_home() ) {
		return;
	}

	echo '<nav class="thessnest-breadcrumbs" aria-label="Breadcrumb" style="font-size:var(--font-size-sm);color:var(--color-text-muted);margin-bottom:var(--space-4);">';
	echo '<ol itemscope itemtype="https://schema.org/BreadcrumbList" style="list-style:none;padding:0;margin:0;display:flex;gap:var(--space-2);flex-wrap:wrap;">';

	// Home
	echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
	echo '<a itemprop="item" href="' . esc_url( home_url( '/' ) ) . '" style="color:var(--color-accent);"><span itemprop="name">' . esc_html__( 'Home', 'thessnest' ) . '</span></a>';
	echo '<meta itemprop="position" content="1" />';
	echo '</li>';
	echo '<li>/</li>';

	if ( is_post_type_archive( 'property' ) || is_tax( array( 'neighborhood', 'amenity', 'target_group' ) ) ) {
		// Properties Archive
		echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
		if ( is_post_type_archive( 'property' ) ) {
			echo '<span itemprop="name" aria-current="page">' . esc_html__( 'Properties', 'thessnest' ) . '</span>';
		} else {
			echo '<a itemprop="item" href="' . esc_url( get_post_type_archive_link( 'property' ) ) . '" style="color:var(--color-accent);"><span itemprop="name">' . esc_html__( 'Properties', 'thessnest' ) . '</span></a>';
			echo '<meta itemprop="position" content="2" />';
			echo '</li>';
			echo '<li>/</li>';

			echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
			echo '<span itemprop="name" aria-current="page">' . single_term_title( '', false ) . '</span>';
			echo '<meta itemprop="position" content="3" />';
		}
		echo '</li>';
	} elseif ( is_singular( 'property' ) ) {
		// Single Property
		echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
		echo '<a itemprop="item" href="' . esc_url( get_post_type_archive_link( 'property' ) ) . '" style="color:var(--color-accent);"><span itemprop="name">' . esc_html__( 'Properties', 'thessnest' ) . '</span></a>';
		echo '<meta itemprop="position" content="2" />';
		echo '</li>';
		echo '<li>/</li>';

		echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
		echo '<span itemprop="name" aria-current="page">' . get_the_title() . '</span>';
		echo '<meta itemprop="position" content="3" />';
		echo '</li>';
	} elseif ( is_page() ) {
		echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
		echo '<span itemprop="name" aria-current="page">' . get_the_title() . '</span>';
		echo '<meta itemprop="position" content="2" />';
		echo '</li>';
	}

	echo '</ol>';
	echo '</nav>';
}
