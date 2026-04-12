<?php
/**
 * Plugin Name: ThessNest Core
 * Plugin URI:  https://thessnest.com
 * Description: Essential core functionality for the ThessNest Directory Theme (Custom Post Types, Elementor Widgets, Payments, and Booking Engines).
 * Version:     1.0.0
 * Author:      ThessNest Team
 * Author URI:  https://thessnest.com
 * Text Domain: thessnest-core
 *
 * @package ThessNest_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define Plugin Constants
define( 'THESSNEST_CORE_VERSION', '1.0.0' );
define( 'THESSNEST_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'THESSNEST_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * The main ThessNest Core class.
 */
final class ThessNest_Core {

	/**
	 * Instance.
	 *
	 * @access private
	 * @var \ThessNest_Core
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @access public
	 * @return \ThessNest_Core
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Init.
	 *
	 * @access private
	 */
	private function init() {
		load_plugin_textdomain( 'thessnest', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		$this->includes();
	}

	/**
	 * Includes all functionality files.
	 */
	private function includes() {
		// Ajax Handlers
		require_once THESSNEST_CORE_DIR . 'inc/ajax-add-listing.php';
		require_once THESSNEST_CORE_DIR . 'inc/ajax-booking.php';
		require_once THESSNEST_CORE_DIR . 'inc/ajax-dashboard.php';
		require_once THESSNEST_CORE_DIR . 'inc/ajax-favorites.php';
		require_once THESSNEST_CORE_DIR . 'inc/ajax-filter.php';
		require_once THESSNEST_CORE_DIR . 'inc/ajax-inquiry.php';
		require_once THESSNEST_CORE_DIR . 'inc/ajax-kyc.php';
		require_once THESSNEST_CORE_DIR . 'inc/ajax-messaging.php';

		// CPTs, Taxonomies & Meta Boxes
		require_once THESSNEST_CORE_DIR . 'inc/core-functionality.php'; // Property & Neighborhoods
		require_once THESSNEST_CORE_DIR . 'inc/extra-post-types.php';   // Experience & Events
		require_once THESSNEST_CORE_DIR . 'inc/admin-map-meta.php';
		require_once THESSNEST_CORE_DIR . 'inc/admin-meta-boxes.php';

		// Features & Logic
		require_once THESSNEST_CORE_DIR . 'inc/accommodation-proof.php';
		require_once THESSNEST_CORE_DIR . 'inc/advanced-reviews.php';
		require_once THESSNEST_CORE_DIR . 'inc/availability-calendar.php';
		require_once THESSNEST_CORE_DIR . 'inc/dashboard-analytics.php';
		require_once THESSNEST_CORE_DIR . 'inc/digital-lease.php';
		require_once THESSNEST_CORE_DIR . 'inc/email-templates.php';
		require_once THESSNEST_CORE_DIR . 'inc/host-payouts.php';
		require_once THESSNEST_CORE_DIR . 'inc/neighborhood-guides.php';
		require_once THESSNEST_CORE_DIR . 'inc/pricing-engine.php';
		require_once THESSNEST_CORE_DIR . 'inc/public-profile.php';
		require_once THESSNEST_CORE_DIR . 'inc/recaptcha.php';
		require_once THESSNEST_CORE_DIR . 'inc/reviews-ratings.php';
		require_once THESSNEST_CORE_DIR . 'inc/roommate-matching.php';
		require_once THESSNEST_CORE_DIR . 'inc/search-advanced.php';
		require_once THESSNEST_CORE_DIR . 'inc/social-login.php';
		require_once THESSNEST_CORE_DIR . 'inc/user-roles.php';
		require_once THESSNEST_CORE_DIR . 'inc/wishlist-compare.php';

		// Payments & Monetization
		require_once THESSNEST_CORE_DIR . 'inc/automated-invoicing.php';
		require_once THESSNEST_CORE_DIR . 'inc/ical-sync.php';
		require_once THESSNEST_CORE_DIR . 'inc/monetization.php';
		require_once THESSNEST_CORE_DIR . 'inc/payments-native.php';
		require_once THESSNEST_CORE_DIR . 'inc/woo-integration.php';

		// Elementor Extension (Loaded explicitly on plugins_loaded or elementor/init)
		require_once THESSNEST_CORE_DIR . 'inc/elementor/class-thessnest-elementor.php';
	}
}

// Initialize the plugin.
function thessnest_core_init() {
	ThessNest_Core::instance();
}
add_action( 'plugins_loaded', 'thessnest_core_init' );
