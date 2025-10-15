<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Uichemy
 * @subpackage Uichemy/includes
 */

namespace Uich;

/**
 * Exit if accessed directly.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uich_Uichemy' ) ) {

	/**
	 * It is Uichemy Main Class
	 *
	 * @since 1.0.0
	 */
	class Uich_Uichemy {

		/**
		 * Member Variable
		 *
		 * @since 1.0.0
		 * @var instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Define the core functionality of the plugin.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			register_activation_hook( UICH_FILE, array( __CLASS__, 'uich_activation' ) );
			register_deactivation_hook( UICH_FILE, array( __CLASS__, 'uich_deactivation' ) );

			add_action( 'plugins_loaded', array( $this, 'uich_plugin_loaded' ) );
		}

		/**
		 * Plugin Activation.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public static function uich_activation() { }

		/**
		 * Plugin deactivation.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public static function uich_deactivation() {
			apply_filters( 'uich_manage_token', 'delete_token' );
			apply_filters( 'uich_manage_usermanager', 'delete_user' );
		}

		/**
		 * Files load plugin loaded.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function uich_plugin_loaded() {
			$this->uich_load_textdomain();
			$this->uich_load_dependencies();
		}

		/**
		 * Load Text Domain. Text Domain : wdkit
		 *
		 * @since 1.0.0
		 */
		public function uich_load_textdomain() {
			load_plugin_textdomain( 'uichemy', false, UICH_BDNAME . '/languages/' );
		}

		/**
		 * Load the required dependencies for this plugin.
		 *
		 * -  Defines all hooks for the admin area.
		 * -  Defines all hooks for the public side of the site.
		 *
		 * @since    1.0.0
		 */
		public function uich_load_dependencies() {
			require_once UICH_PATH . 'includes/notices/class-uich-notice-main.php';
			require_once UICH_PATH . 'includes/admin/class-uich-token-manager.php';
			require_once UICH_PATH . 'includes/admin/class-uich-usermanager.php';
			require_once UICH_PATH . 'includes/admin/class-uich-api.php';
			require_once UICH_PATH . 'includes/admin/class-uich-enqueue.php';
			require_once UICH_PATH . 'includes/admin/class-uich-bricks-imgs.php';
			require_once UICH_PATH . 'includes/admin/class-uich-elementor.php';
			require_once UICH_PATH . 'includes/admin/class-uich-copy-images.php';
		}
	}

	Uich_Uichemy::get_instance();
}