<?php
/**
 * It is Main File to load all Notice, Upgrade Menu and all
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Uichemy
 * @subpackage Uichemy/includes
 * */

/**
 * Exit if accessed directly.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uich_User_Experience_Main' ) ) {

	/**
	 * This class used for only load All Notice Files
	 *
	 * @since 1.0.0
	 */
	class Uich_User_Experience_Main {

		/**
		 * Instance
		 *
		 * @since 1.0.0
		 * @var instance of the class.
		 */
		private static $instance = null;

		/**
		 * Singleton Instance Creation Method.
		 *
		 * This public static method ensures that only one instance of the class is loaded or can be loaded.
		 * It follows the Singleton design pattern to create or return the existing instance of the class.
		 *
		 * @since 1.0.0
		 * @return self Instance of the class.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Perform some compatibility checks to make sure basic requirements are meet.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public function __construct() {
			$this->uich_user_experience();
		}


		/**
		 * Initiate our hooks
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public function uich_user_experience() {
		
			include UICH_PATH . 'includes/user-experience/class-uich-toast-popup.php';

			$uich_onbording_end = get_option( 'uich_onbording_end' );
			if ( empty( $uich_onbording_end ) ) {
				include UICH_PATH . 'includes/user-experience/class-uich-onbording.php';
			}

		}
	}

	Uich_User_Experience_Main::instance();
}