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

if ( ! class_exists( 'Uich_Api' ) ) {

	/**
	 * Here Enqueue all js and css script
	 */
	class Uich_Api {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since   1.0.0
		 */
		public function __construct() {
			add_filter( 'rest_pre_serve_request', array( $this, 'uiche_rest_send_cors_headers' ) );
			add_filter( 'http_request_timeout', array( $this, 'uich_modify_http_request_default_timeout' ), 10 );

			add_action( 'wp_ajax_uichemy_regenerate_token', array( $this, 'uiche_regenerate_token' ) );
			add_action( 'wp_ajax_uichemy_select_user', array( $this, 'uichemy_select_user' ) );

			add_action(
				'rest_api_init',
				function () {
					register_rest_route(
						'uichemy/v1',
						'/import',
						array(
							'methods'             => 'POST',
							'callback'            => array( $this, 'uich_handle_import' ),
							'permission_callback' => '__return_true',
						)
					);

					register_rest_route(
						'uichemy/v1',
						'/check',
						array(
							'methods'             => array( 'GET', 'POST' ),
							'callback'            => array( $this, 'uich_handle_check' ),
							'permission_callback' => '__return_true',
						)
					);
				}
			);
		}

		/**
		 * Check regenerate token
		 *
		 * @since   1.0.0
		 */
		public function uiche_regenerate_token() {
			$nonce = ( isset( $_POST['nonce'] ) ) ? sanitize_key( wp_unslash( $_POST['nonce'] ) ) : '';

			if ( ! isset( $nonce ) || empty( $nonce ) || ! wp_verify_nonce( $nonce, 'uichemy-ajax-nonce' ) ) {
				wp_send_json_error( null, 400 );
			}

			if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( null, 400 );
			}

			apply_filters( 'uich_manage_token', 'reset_token' );

			// Prepare the response with the new token.
			$response = array(
				'message' => esc_html__( 'Reset Successful', 'uichemy' ),
				'token'   => apply_filters( 'uich_manage_token', 'get_token' ),
			);

			// Send the JSON response back to the client-side JavaScript.
			wp_send_json_success( $response );
		}

		/**
		 * Select User role
		 *
		 * @since   1.0.0
		 */
		public function uichemy_select_user() {

			check_ajax_referer( 'uichemy-ajax-nonce', 'nonce' );

			if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( null, 400 );
			}

			$newuser = ! empty( $_POST['new_user'] ) ? sanitize_text_field( wp_unslash( $_POST['new_user'] ) ) : '';

			apply_filters( 'uich_manage_usermanager', 'set_user', $newuser );

			$response = array(
				'message'  => 'Set Successful',
				'new_user' => apply_filters( 'uich_manage_usermanager', 'get_user' ),
			);

			wp_send_json_success( $response );
		}

		/**
		 * Check Handle import
		 *
		 * @since   1.0.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request object.
		 */
		public function uich_handle_import( WP_REST_Request $request ) {

			// In case of shutdown.
			header( 'Access-Control-Allow-Origin: *' );

			// Match Security Token.
			if ( ! $this->uich_are_tokens_matching( $request ) ) {
				return array(
					'success' => false,
					'message' => esc_html__( 'Invalid Security Token', 'uichemy' ),
				);
			}

			$json = $request->get_body();

			// Check if JSON data is present.
			if ( ! empty( $json ) && class_exists( '\Elementor\Plugin' ) ) {

				$file_name = 'export.json';

				// Using the elementor tmp_file creator.
				$temp_filename = \Elementor\Plugin::$instance->uploads_manager->create_temp_file( $json, $file_name );
				$file_data     = array(
					'name'     => $file_name,
					'tmp_name' => $temp_filename,
				);

				// Set current user as admin.
				wp_set_current_user( null, UICH_USERNAME );

				// Start the import.
				$import_result = \Elementor\Plugin::$instance->templates_manager->import_template( $file_data );

				return array(
					'success' => true,
					'result'  => $import_result,
				);
			} else {
				// Return an error response.
				return array(
					'success' => false,
					'message' => esc_html__( 'No JSON data found.', 'uichemy' ),
				);
			}

			// Remove added filter.
			remove_filter( 'http_request_timeout', 'uich_modify_http_request_default_timeout', 1 );
		}

		/**
		 * Check handle check
		 *
		 * @since   1.0.0
		 *
		 * @param WP_REST_Request $request get current page slug.
		 */
		public function uich_handle_check( WP_REST_Request $request ) {

			if ( $this->uich_are_tokens_matching( $request ) ) {
				return array(
					'success' => true,
					'message' => esc_html__( 'All Good.', 'uichemy' ),
				);
			}

			return array(
				'success' => false,
				'message' => esc_html__( 'Invalid Security Token', 'uichemy' ),
			);
		}

		/**
		 * Filter to allow "token" header in Requests.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed $value The value to check and return.
		 */
		public function uiche_rest_send_cors_headers( $value ) {
			$origin     = get_http_origin();
			$requesturi = ! empty( $_SERVER['REQUEST_URI'] ) ? $this->uiche_sanitizer_bypass( $_SERVER, 'REQUEST_URI' ) : '';

			if ( ! empty( $origin ) && preg_match( '/\/wp-json\/uichemy\/v1/', wp_parse_url( $requesturi, PHP_URL_PATH ) ) === 1 ) {
				header( 'Access-Control-Allow-Headers: UiChemy-Security-Token' );
			}

			return $value;
		}

		/**
		 * Parse args check senitizer
		 *
		 * @since 1.0.0
		 *
		 * @param string $data send all post data.
		 * @param string $type store text data.
		 */
		public function uiche_sanitizer_bypass( $data, $type ) {
			return wp_unslash( $data[ $type ] );
		}

		/**
		 * Check token matching
		 *
		 * @since   1.0.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request object.
		 */
		public function uich_are_tokens_matching( WP_REST_Request $request ) {

			$token         = $request->get_header( 'UiChemy-Security-Token' );
			$current_token = apply_filters( 'uich_manage_token', 'get_token' );

			if ( is_null( $token ) || empty( $current_token ) || $current_token !== $token ) {
				return false;
			}

			return true;
		}

		/**
		 * Check http_request time
		 *
		 * @since   1.0.0
		 */
		public function uich_modify_http_request_default_timeout() {
			return 30;
		}
	}

	new Uich_Api();
}
