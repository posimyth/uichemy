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

namespace Uich\token;

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uich_Token_Manager' ) ) {

	/**
	 * Main classs call for token
	 *
	 * @link       https://posimyth.com/
	 * @since      1.0.0
	 */
	class Uich_Token_Manager {

		/**
		 * Define the core functionality of the plugin.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'uich_manage_token', array( $this, 'uich_create_default' ), 10, 1 );
		}

		/**
		 * Create Default setting data in db
		 *
		 * @since 1.0.0
		 *
		 * @param string $type use for check page type.
		 */
		public function uich_create_default( $type ) {

			if ( 'get_token' === $type ) {
				return $this->get_token();
			} elseif ( 'create_token' === $type ) {
				return $this->create_token();
			} elseif ( 'delete_token' === $type ) {
				return $this->delete_token();
			} elseif ( 'reset_token' === $type ) {
				return $this->reset_token();
			} elseif ( 'is_token_set' === $type ) {
				return $this->is_token_set();
			}
		}

		/**
		 * Generate random token
		 *
		 * @since 1.0.0
		 */
		public static function gen_random_token() {
			$hash = strtoupper( MD5( random_bytes( 512 ) ) );
			$split_hash = str_split($hash, 4);
			return join("-", $split_hash);
		}

		/**
		 * Create Token
		 *
		 * @since 1.0.0
		 */
		public static function create_token() {
			return add_option( UICH_TOKEN_OPTION, self::gen_random_token() );
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
			return get_option( UICH_TOKEN_OPTION ) !== false;
		}

		/**
		 * Delete Token
		 *
		 * @since 1.0.0
		 */
		public static function delete_token() {
			return delete_option( UICH_TOKEN_OPTION );
		}

		/**
		 * Set Token
		 *
		 * @since 1.0.0
		 *
		 * @param string $value use dor databash update.
		 */
		public static function set_token( $value = '' ) {
			return update_option( UICH_TOKEN_OPTION, $value );
		}

		/**
		 * Get Token
		 *
		 * @since 1.0.0
		 */
		public static function get_token() {
			$token = get_option( UICH_TOKEN_OPTION );

			if ( false === $token ) {
				self::create_token();
			}

			return get_option( UICH_TOKEN_OPTION );
		}
	}

	new Uich_Token_Manager();
}
