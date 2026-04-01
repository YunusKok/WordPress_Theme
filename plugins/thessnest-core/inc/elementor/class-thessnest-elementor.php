<?php
/**
 * ThessNest — Elementor Extension Base
 *
 * Initializes the Elementor custom widgets and categories for the theme.
 * Ensures Elementor is active before executing.
 *
 * @package ThessNest
 */

defined( 'ABSPATH' ) || exit;

class ThessNest_Elementor_Extension {

	/**
	 * Instance
	 */
	private static $_instance = null;

	/**
	 * Get Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Load Textdomain
	 */
	public function i18n() {
		load_plugin_textdomain( 'thessnest' );
	}

	/**
	 * Initialize the extension
	 */
	public function init() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		// Register Widget Category
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );

		// Register Widgets
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	/**
	 * Register Category
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'thessnest-elements',
			[
				'title' => esc_html__( 'ThessNest Elements', 'thessnest' ),
				'icon'  => 'eicon-home',
			]
		);
	}

	/**
	 * Register Widgets
	 */
	public function register_widgets( $widgets_manager ) {
		// Requires will go here once we build the specific widget files.
		require_once( __DIR__ . '/widgets/widget-hero-search.php' );
		require_once( __DIR__ . '/widgets/widget-property-grid.php' );
		require_once( __DIR__ . '/widgets/widget-how-it-works.php' );

		$widgets_manager->register( new \ThessNest_Hero_Search_Widget() );
		$widgets_manager->register( new \ThessNest_Property_Grid_Widget() );
		$widgets_manager->register( new \ThessNest_How_It_Works_Widget() );
	}
}

// Bootstrap
ThessNest_Elementor_Extension::instance();
