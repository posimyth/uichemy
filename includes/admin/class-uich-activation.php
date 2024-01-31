<?php
/**
 * This file Call Only One time when click and active Plugin
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Uichemy
 */

/**
 * Exit if accessed directly.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uich_Activation' ) ) {

	/**
	 * Here Enqueue all js and css script
	 */
	class Uich_Activation {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since   1.0.0
		 */
		public function __construct() {
			self::uich_create_user_with_edit_posts_capability();
		}

		/**
		 * Here Create New user for uich.
		 *
		 * @since   1.0.0
		 */
		public static function uich_create_user_with_edit_posts_capability() {
			$username = UICH_USERNAME;

			if ( ! username_exists( $username ) ) {

				$password = wp_generate_password( 12 ); // Generates a random password with 12 characters.

				$userdata = array(
					'user_login' => $username,
					'user_pass'  => $password,
				);

				$user_id = wp_insert_user( $userdata );

				$user = new WP_User( $user_id );
				$user->add_cap( 'edit_posts' );
			}
		}
	}

	new Uich_Activation();
}
