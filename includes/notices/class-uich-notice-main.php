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
 * @package    Wdesignkit
 * @subpackage Wdesignkit/includes
 */

/**Exit if accessed directly.*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uich_Notice_Main' ) ) {

	/**
	 * Uich_Notice_Main
	 *
	 * @since 1.0.0
	 */
	class Uich_Notice_Main {

		/**
		 * Singleton instance variable.
		 *
		 * @var instance|null The single instance of the class.
		 */
		private static $instance;

		/**
		 * Singleton instance getter method.
		 *
		 * @since 1.0.0
		 *
		 * @return self The single instance of the class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor for the core functionality of the plugin.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public function __construct() {
			self::wdkit_notice_fileload();
		}

		/**
		 * Loads the file for setting plugin page notices.
		 *
		 * @since 1.0.0
		 */
		public function wdkit_notice_fileload() {
			require_once UICH_PATH . 'includes/notices/class-uich-plugin-page.php';
		}
	}

	Uich_Notice_Main::get_instance();
}
