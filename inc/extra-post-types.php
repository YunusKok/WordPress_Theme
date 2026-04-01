<?php
/**
 * ThessNest — Extensibility Post Types (Experiences & Events)
 *
 * Implements the missing components found in competitive themes
 * for booking local tours, classes, and tickets.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Extra_Post_Types {

	public function __construct() {
		add_action( 'init', [ $this, 'register_experience_cpt' ] );
		add_action( 'init', [ $this, 'register_event_cpt' ] );
	}

	/**
	 * Register the "Experience" Custom Post Type for local tours and classes.
	 */
	public function register_experience_cpt() {
		$labels = array(
			'name'                  => _x( 'Experiences', 'Post Type General Name', 'thessnest' ),
			'singular_name'         => _x( 'Experience', 'Post Type Singular Name', 'thessnest' ),
			'menu_name'             => __( 'Experiences', 'thessnest' ),
			'name_admin_bar'        => __( 'Experience', 'thessnest' ),
			'add_new'               => __( 'Add New', 'thessnest' ),
			'add_new_item'          => __( 'Add New Experience', 'thessnest' ),
			'new_item'              => __( 'New Experience', 'thessnest' ),
			'edit_item'             => __( 'Edit Experience', 'thessnest' ),
			'view_item'             => __( 'View Experience', 'thessnest' ),
			'all_items'             => __( 'All Experiences', 'thessnest' ),
			'search_items'          => __( 'Search Experiences', 'thessnest' ),
			'not_found'             => __( 'No experiences found.', 'thessnest' ),
		);
		$args = array(
			'label'                 => __( 'Experience', 'thessnest' ),
			'description'           => __( 'Local experiences, tours, and classes.', 'thessnest' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 28,
			'menu_icon'             => 'dashicons-camera',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
			'show_in_rest'          => true,
		);
		register_post_type( 'experience', $args );

		// Taxonomy for Experience Types
		register_taxonomy( 'experience_type', array( 'experience' ), array(
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => _x( 'Experience Types', 'taxonomy general name', 'thessnest' ),
				'singular_name'     => _x( 'Experience Type', 'taxonomy singular name', 'thessnest' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'experience-type' ),
		) );
	}

	/**
	 * Register the "Event" Custom Post Type for event ticketing.
	 */
	public function register_event_cpt() {
		$labels = array(
			'name'                  => _x( 'Events', 'Post Type General Name', 'thessnest' ),
			'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'thessnest' ),
			'menu_name'             => __( 'Events', 'thessnest' ),
			'name_admin_bar'        => __( 'Event', 'thessnest' ),
			'add_new_item'          => __( 'Add New Event', 'thessnest' ),
			'edit_item'             => __( 'Edit Event', 'thessnest' ),
			'all_items'             => __( 'All Events', 'thessnest' ),
		);
		$args = array(
			'label'                 => __( 'Event', 'thessnest' ),
			'description'           => __( 'Student parties, meetups, and local events.', 'thessnest' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'public'                => true,
			'show_ui'               => true,
			'menu_position'         => 29,
			'menu_icon'             => 'dashicons-tickets-alt',
			'has_archive'           => true,
			'capability_type'       => 'post',
			'show_in_rest'          => true,
			'rewrite'               => array( 'slug' => 'events' ),
		);
		register_post_type( 'event', $args );
	}
}

new ThessNest_Extra_Post_Types();
