<?php
/**
 * The file that defines the core plugin class
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Uichemy
 * @subpackage Uichemy/token
 */

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'UICHEMY_TOKEN_OPTION', 'uichemy_token' );

if ( ! class_exists( 'Uich_Token_Manager' ) ) {

	/**
	 * Main classs call for token
	 *
	 * @link       https://posimyth.com/
	 * @since      1.0.0
	 */
	class Uich_Token_Manager {

		/**
		 * Generate random token
		 *
		 * @since 1.0.0
		 */
		public static function gen_random_token() {
			return str_shuffle( MD5( microtime() ) );
		}

		/**
		 * Create Token
		 *
		 * @since 1.0.0
		 */
		public static function create_token() {
			return add_option( UICHEMY_TOKEN_OPTION, self::gen_random_token() );
		}

		/**
		 * Reset Token
		 *
		 * @since 1.0.0
		 */
		public static function reset_token() {
			return self::set_token( self::gen_random_token() );
		}

		/**
		 * Is Set Token
		 *
		 * @since 1.0.0
		 */
		public static function is_token_set() {
			return get_option( UICHEMY_TOKEN_OPTION ) !== false;
		}

		/**
		 * Delete Token
		 *
		 * @since 1.0.0
		 */
		public static function delete_token() {
			return delete_option( UICHEMY_TOKEN_OPTION );
		}

		/**
		 * Set Token
		 *
		 * @since 1.0.0
		 *
		 * @param string $value use dor databash update.
		 */
		public static function set_token( $value = '' ) {
			return update_option( UICHEMY_TOKEN_OPTION, $value );
		}

		/**
		 * Get Token
		 *
		 * @since 1.0.0
		 */
		public static function get_token() {
			return get_option( UICHEMY_TOKEN_OPTION );
		}
	}

}
