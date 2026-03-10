<?php
/**
 * ThessNest — One-Click Setup & Demo Data
 *
 * Automates the creation of essential pages, menus, taxonomies, and dummy properties.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handle the Setup Request
 */
function thessnest_handle_demo_setup() {
	if ( ! isset( $_POST['thessnest_run_setup'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	check_admin_referer( 'thessnest_demo_setup', 'thessnest_demo_nonce' );

	// 1. Update Permalinks
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	update_option( 'rewrite_rules', false );
	$wp_rewrite->flush_rules();

	// 2. Create Required Pages
	$pages = array(
		'dashboard'   => array( 'title' => 'Dashboard', 'content' => '', 'template' => 'template-dashboard.php' ),
		'add-listing' => array( 'title' => 'Add Listing', 'content' => '', 'template' => 'template-add-listing.php' ),
		'about-us'    => array( 'title' => 'About Us', 'content' => 'Welcome to ThessNest. A modern way to find your next home.', 'template' => 'template-about.php' ),
		'contact'     => array( 'title' => 'Contact', 'content' => 'Please use the form below to contact us. [contact-form-7]', 'template' => '' ),
	);

	$page_ids = array();
	foreach ( $pages as $slug => $data ) {
		$page_check = get_page_by_path( $slug );
		if ( ! isset( $page_check->ID ) ) {
			$page_id = wp_insert_post( array(
				'post_title'     => $data['title'],
				'post_name'      => $slug,
				'post_content'   => $data['content'],
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'comment_status' => 'closed',
			) );

			if ( ! is_wp_error( $page_id ) && ! empty( $data['template'] ) ) {
				update_post_meta( $page_id, '_wp_page_template', $data['template'] );
			}
			$page_ids[ $slug ] = $page_id;
		} else {
			$page_ids[ $slug ] = $page_check->ID;
		}
	}

	// Create Home Page if not exists
	$home_check = get_page_by_title( 'Home' );
	$home_id    = 0;
	if ( ! isset( $home_check->ID ) ) {
		$home_id = wp_insert_post( array(
			'post_title'     => 'Home',
			'post_content'   => '',
			'post_status'    => 'publish',
			'post_type'      => 'page',
		) );
	} else {
		$home_id = $home_check->ID;
	}

	// Set Static Front Page
	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}

	// 3. Setup Navigation Menu
	$menu_name = 'Main Menu';
	$menu_exists = wp_get_nav_menu_object( $menu_name );

	if ( ! $menu_exists ) {
		$menu_id = wp_create_nav_menu( $menu_name );

		if ( ! is_wp_error( $menu_id ) ) {
			// Add Home
			wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'     => 'Home',
				'menu-item-object-id' => $home_id,
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			) );

			// Add About Us
			if ( isset( $page_ids['about-us'] ) ) {
				wp_update_nav_menu_item( $menu_id, 0, array(
					'menu-item-title'     => 'About Us',
					'menu-item-object-id' => $page_ids['about-us'],
					'menu-item-object'    => 'page',
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				) );
			}

			// Add Custom Link to Properties
			wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-title'  => 'Our Solutions',
				'menu-item-url'    => home_url( '/properties/' ),
				'menu-item-status' => 'publish',
				'menu-item-type'   => 'custom',
			) );

			// Add Contact
			if ( isset( $page_ids['contact'] ) ) {
				wp_update_nav_menu_item( $menu_id, 0, array(
					'menu-item-title'     => 'Contact',
					'menu-item-object-id' => $page_ids['contact'],
					'menu-item-object'    => 'page',
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				) );
			}

			// Set to primary location
			$locations = get_theme_mod( 'nav_menu_locations' );
			$locations['primary'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	// 4. Create Taxonomies if empty
	$terms = array(
		'neighborhood' => array( 'Kalamaria', 'Toumba', 'Center', 'Pylaia', 'Panorama' ),
		'amenity'      => array( 'Wi-Fi', 'Balcony', 'Washing Machine', 'Oven', 'Dishwasher', 'AC' ),
		'target_group' => array( 'Students', 'Erasmus', 'Digital Nomads', 'Professionals' )
	);

	$inserted_terms = array();
	foreach ( $terms as $tax => $items ) {
		$inserted_terms[ $tax ] = array();
		foreach ( $items as $item ) {
			if ( ! term_exists( $item, $tax ) ) {
				$t = wp_insert_term( $item, $tax );
				if ( ! is_wp_error( $t ) ) {
					$inserted_terms[ $tax ][] = (int) $t['term_id'];
				}
			} else {
				$t_obj = get_term_by( 'name', $item, $tax );
				$inserted_terms[ $tax ][] = (int) $t_obj->term_id;
			}
		}
	}

	// 5. Create Dummy Properties
	$dummy_count = wp_count_posts( 'property' )->publish;
	if ( $dummy_count < 4 ) {
		$dummy_props = array(
			array( 'title' => 'Modern Studio in City Center', 'rent' => 450, 'utils' => 50, 'dep' => 450, 'lat' => 40.6300, 'lng' => 22.9500 ),
			array( 'title' => 'Sea-view Apartment Kalamaria', 'rent' => 600, 'utils' => 80, 'dep' => 600, 'lat' => 40.5841, 'lng' => 22.9547 ),
			array( 'title' => 'Cozy Toumba Flat for Erasmus', 'rent' => 380, 'utils' => 40, 'dep' => 380, 'lat' => 40.6133, 'lng' => 22.9736 ),
			array( 'title' => 'Luxury Nomad Nest Panorama', 'rent' => 950, 'utils' => 120, 'dep' => 950, 'lat' => 40.5878, 'lng' => 23.0305 ),
		);

		foreach ( $dummy_props as $idx => $p ) {
			// Insert post
			$prop_id = wp_insert_post( array(
				'post_title'   => $p['title'],
				'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fully furnished and ready to move in.',
				'post_status'  => 'publish',
				'post_type'    => 'property',
			) );

			if ( ! is_wp_error( $prop_id ) ) {
				// Meta
				update_post_meta( $prop_id, '_thessnest_rent', $p['rent'] );
				update_post_meta( $prop_id, '_thessnest_utilities', $p['utils'] );
				update_post_meta( $prop_id, '_thessnest_deposit', $p['dep'] );
				update_post_meta( $prop_id, '_thessnest_wifi_speed', rand(50, 300) );
				update_post_meta( $prop_id, '_thessnest_max_tenants', rand(1, 3) );
				update_post_meta( $prop_id, '_thessnest_latitude', $p['lat'] );
				update_post_meta( $prop_id, '_thessnest_longitude', $p['lng'] );

				// Terms
				if ( ! empty( $inserted_terms['neighborhood'] ) ) {
					wp_set_object_terms( $prop_id, array( $inserted_terms['neighborhood'][ $idx % count( $inserted_terms['neighborhood'] ) ] ), 'neighborhood' );
				}
				if ( ! empty( $inserted_terms['target_group'] ) ) {
					wp_set_object_terms( $prop_id, array( $inserted_terms['target_group'][ rand( 0, count( $inserted_terms['target_group'] ) - 1 ) ] ), 'target_group' );
				}
				if ( ! empty( $inserted_terms['amenity'] ) ) {
					$random_amenities = array_rand( array_flip( $inserted_terms['amenity'] ), 3 );
					wp_set_object_terms( $prop_id, (array) $random_amenities, 'amenity' );
				}
			}
		}
	}

	// Set Customizer Image if Hero is empty
	if ( ! get_theme_mod( 'hero_background_image' ) ) {
		set_theme_mod( 'hero_background_image', 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1920&h=1080&fit=crop&q=80' );
	}

	// Redirect with success message
	wp_safe_redirect( add_query_arg( 'thessnest_setup', 'success', wp_get_referer() ) );
	exit;
}
add_action( 'admin_init', 'thessnest_handle_demo_setup' );
