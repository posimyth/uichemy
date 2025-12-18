<?php
/**
 * This file specifically check which user role
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Uichemy
 */

namespace Uich\User;

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uich_UserManager' ) ) {

	/**
	 * Main classs call for Userrole
	 *
	 * @link       https://posimyth.com/
	 * @since      1.0.0
	 */
	class Uich_UserManager {

		/**
		 * Define the core functionality of the plugin.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'uich_manage_usermanager', array( $this, 'uich_usermanage' ), 10, 2 );
		}

		/**
		 * Create Default setting data in db
		 *
		 * @since 1.0.0
		 *
		 * @param string $type use for check page type.
		 * @param string $data use for check page data.
		 */
		public function uich_usermanage( $type, $data = '' ) {

			if ( 'get_userlist' === $type ) {
				$capable_users = self::get_all_capable_users();

				return $capable_users;
			} elseif ( 'get_user' === $type ) {
				return self::get_selected_user();
			} elseif ( 'set_user' === $type ) {
				return self::set_user( $data );
			} elseif ( 'delete_user' === $type ) {
				return self::delete_user_option();
			}
		}

		/**
		 * Get_all_capable_users
		 *
		 * @since 1.0.0
		 */
		public static function get_all_capable_users() {
			$all_users     = get_users();
			$capable_users = array();

			foreach ( $all_users as $user ) {
				if ( $user->has_cap( 'import' ) ) {
					$capable_users[] = $user->user_login;
				}
			}

			return $capable_users;
		}

		/**
		 * Init_selected_user
		 *
		 * @since 1.0.0
		 */
		public static function init_selected_user() {

			$capable_users = self::get_all_capable_users();

			if ( empty( $capable_users ) ) {
				return;
			}

			$selected_username = $capable_users[0];

			return add_option( UICH_USER_OPTION, $selected_username );
		}

		/**
		 * Init_selected_user
		 *
		 * @since 1.0.0
		 */
		public static function get_selected_user() {
			if( false === get_option( UICH_USER_OPTION  ) ) {
				self::init_selected_user();
			}

			return get_option( UICH_USER_OPTION );
		}

		/**
		 * Is_user_set
		 *
		 * @since 1.0.0
		 */
		public static function is_user_set() {
			return get_option( UICH_USER_OPTION ) !== false;
		}

		/**
		 * Set_user
		 *
		 * @since 1.0.0
		 * @param string $username use for username.
		 */
		public static function set_user( $username = '' ) {
			if ( apply_filters( 'uich_manage_token', 'is_token_set' ) ) {
				return update_option( UICH_USER_OPTION, $username );
			}

			return add_option( UICH_USER_OPTION, $username );
		}

		/**
		 * Delete_user_option
		 *
		 * @since 1.0.0
		 */
		public static function delete_user_option() {
			return delete_option( UICH_USER_OPTION );
		}
	}

	new Uich_UserManager();

}
