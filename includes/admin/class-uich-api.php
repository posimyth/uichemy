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
		 * Member Variable
		 *
		 * @var staring $flexbox_container_db
		 */
		public $flexbox_container_db = 'elementor_experiment-container';

		/**
		 * Member Variable
		 *
		 * @var staring $flexbox_container_db
		 */
		public $file_uploads_db = 'elementor_unfiltered_files_upload';

		/**
		 * Member Variable
		 *
		 * @var staring $flexbox_container_db
		 */
		public $elementor_pluginpath = 'elementor/elementor.php';

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

			add_filter( 'uich_recommended_settings', array( $this, 'uich_recommended_settings' ), 10, 1 );
			add_action( 'wp_ajax_uich_uichemy', array( $this, 'uich_api_call' ) );

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

					register_rest_route(
						'uichemy/v1',
						'/bricks/getnonce',
						array(
							'methods'             => array( 'GET' ),
							'callback'            => array( $this, 'uich_handle_bricks_get_nonce' ),
							'permission_callback' => '__return_true',
						)
					);

					register_rest_route(
						'uichemy/v1',
						'/bricks/import',
						array(
							'methods'             => array( 'POST' ),
							'callback'            => array( $this, 'uich_handle_bricks_import' ),
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

			// Match Security Token.
			$this->uich_check_token( $request );

			$json = $request->get_body();

			// Check if JSON data is present.
			if ( ! empty( $json ) && class_exists( '\Elementor\Plugin' ) ) {

				$file_name = 'export.json';

				// Using the elementor tmp_file creator.
				$temp_filename = \Elementor\Plugin::$instance->uploads_manager->create_temp_file( $json, $file_name );
				$file_data     = array(
					'fileName' => $file_name,
            		'fileData' => base64_encode($json),
				);

				$selected_user = apply_filters( 'uich_manage_usermanager', 'get_user' );

				// Set current user as admin.
				wp_set_current_user( null, $selected_user );

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

			$this->uich_check_token( $request );

			return array(
				'success' => true,
				'message' => esc_html__( 'All Good.', 'uichemy' ),
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
			$requesturi = ! empty( $_SERVER['REQUEST_URI'] ) ? esc_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';

			if ( ! empty( $origin ) && preg_match( '/\/wp-json\/uichemy\/v1/', wp_parse_url( $requesturi, PHP_URL_PATH ) ) === 1 ) {
				// Allow security token header.
				header( 'Access-Control-Allow-Headers: UiChemy-Security-Token' );
			}

			return $value;
		}

		/**
		 * Check token matching
		 *
		 * @since   1.0.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request object.
		 */
		public function uich_check_token( WP_REST_Request $request ) {

			// In case of shutdown.
			header( 'Access-Control-Allow-Origin: *' );

			$token         = $request->get_header( 'UiChemy-Security-Token' );
			$current_token = apply_filters( 'uich_manage_token', 'get_token' );

			if ( is_null( $token ) || empty( $current_token ) || $current_token !== $token ) {
				wp_send_json(
					array(
						'success' => false,
						'message' => esc_html__( 'Invalid Security Token', 'uichemy' ),
					)
				);

				wp_die();
			}
		}

		/**
		 * Check http_request time
		 *
		 * @since   1.0.0
		 */
		public function uich_modify_http_request_default_timeout() {
			return 30;
		}

		/**
		 * Send nonce for Bricks Import
		 *
		 * @since   1.0.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request object.
		 */
		public function uich_handle_bricks_get_nonce( WP_REST_Request $request ) {

			// Match Security Token.
			$this->uich_check_token( $request );

			// Get the user to use
			$selected_user = apply_filters( 'uich_manage_usermanager', 'get_user' );

			// Set current user as admin.
			wp_set_current_user( null, $selected_user );

			// Create Nonce
			$nonce = wp_create_nonce("bricks-nonce");

			return array(
		        'success' => true,
		        'nonce' => $nonce,
		    );
		}
		
		/**
		 * Handle import for Bricks
		 *
		 * @since   1.0.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request object.
		 */
		public function uich_handle_bricks_import( WP_REST_Request $request ) {

			// Match Security Token.
			$this->uich_check_token( $request );

			if( !class_exists( '\Bricks\Theme' ) || !class_exists(( '\Bricks\Templates' )) ) {
				return array(
					'success' => false,
					'message' => 'Please Install & Activate Bricks First.',
				);
			}

			if( empty($_FILES) || empty($_FILES['files']) ) {
				return array(
					'success' => false,
					'message' => 'No file provided',
				);
			}

			// Get the user to use & Set current user
			$selected_user = apply_filters( 'uich_manage_usermanager', 'get_user' );
			wp_set_current_user( null, $selected_user );

			// Load WP_WP_Filesystem for temp file URL access
			global $wp_filesystem;

			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';

				WP_Filesystem();
			}

			// Get the template Elements
			$template = json_decode( $wp_filesystem->get_contents( $_FILES['files']['tmp_name'][0] ), true );
			$elements = $template['content'];

			\Bricks\Templates::$template_images = [];

			foreach ( $elements as $index => $element ) {
				if ( !empty( $element['settings'] ) ) {
					\Bricks\Theme::instance()->templates->import_images( $element['settings'], true );
				}
			}

			// STEP: Replace remote image data with imported/existing image data.
			if ( count( \Bricks\Templates::$template_images ) ) {
				$elements_encoded = wp_json_encode( $elements );

				foreach ( \Bricks\Templates::$template_images as $template_image ) {
					$elements_encoded = str_replace(
						wp_json_encode( $template_image['old'] ),
						wp_json_encode( $template_image['new'] ),
						$elements_encoded
					);
				}

				$elements = json_decode( $elements_encoded, true );

				$template['content'] = $elements;

				// Replace Uploaded File Contents for Importing.
				$wp_filesystem->put_contents($_FILES['files']['tmp_name'][0], json_encode( $template ));
			}

			// Import & Return the value
			\Bricks\Theme::instance()->templates->import_template();
		}
		
		/**
		 * Get uichemy Api Call Ajax.
		 */
		public function uich_api_call() {

			check_ajax_referer( 'uichemy-ajax-nonce', 'nonce' );

			if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'content' => __( 'Insufficient permissions.', 'uichemy' ) ) );
			}

			$type = isset( $_POST['type'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['type'] ) ) ) : false;
			if ( ! $type ) {
				$this->uich_error_msg( __( 'Something went wrong.', 'uichemy' ) );
			}

			switch ( $type ) {
				case 'install_elementor':
					$data = $this->uich_install_elementor();
				break;
				case 'flexbox_container':
					$data = $this->uich_flexbox_container();
				break;
				case 'elementor_file_uploads':
					$data = $this->uich_elementor_file_uploads();
				break;
			}

			wp_send_json( $data );
			wp_die();
		}

		/**
		 * Error JSON message
		 *
		 * @param array  $data give array.
		 * @param string $status api code number.
		 * */
		public function uich_error_msg( $data = null, $status = null ) {
			wp_send_json_error( $data );
			wp_die();
		}

		/**
		 * Response Message
		 *
		 * @param array  $success give array.
		 * @param message $status api code number.
		 * @param message $description api code number.
		 * @param message $data give array or string.
		 * */
		public function uich_response( $message = '', $description = '', $success = false, $data = '' ){

			return array( 
				'message' => $message, 
				'description' => $description, 
				'success' => $success,
				'data' => $data, 
			);
		}

		/**
		 * Install Elementor
		 * 
		 * @since 1.0.0
		 * */
		public function uich_install_elementor() {
			include_once ABSPATH . 'wp-admin/includes/file.php';
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';

			$response = wp_remote_post('http://api.wordpress.org/plugins/info/1.0/',
				[
					'body' => [
						'action' => 'plugin_information',
						'request' => serialize((object) [
							'slug' => 'elementor',
							'fields' => [
								'version' => false,
							],
						]),
					],
				]
			);

			$elementor_plugin = unserialize(wp_remote_retrieve_body($response));
			if ( is_wp_error($elementor_plugin) ) {
				return $this->uich_response( 'Something Went Wrong', 'get body', false, $elementor_plugin );
			}

			$upgrad = new \Plugin_Upgrader(new \Automatic_Upgrader_Skin());

			/**Install Plugin*/
			$elementor_Install = $upgrad->install($elementor_plugin->download_link);
			if ( is_wp_error($elementor_Install) ) {
				return $this->uich_response( 'Something Went Wrong', 'Install Plugin', false, $elementor_Install );
			}

			/**Activate Plugin*/
			if ( true === $elementor_Install ) {
				$elementor_active = activate_plugin( $upgrad->plugin_info(), '', false, true );

				if ( is_wp_error($elementor_active) ) {
					return $this->uich_response( 'Something Went Wrong', 'Activate Plugin', false, $elementor_active );
				}

				$success = null === $elementor_active;

				return $this->uich_response( 'Successfully Activated!', 'Elementor Installed and Activated Successfully.', $success, '' );
			}else{
				return $this->uich_response( 'Something Went Wrong', 'Not Activate Plugin', false, $elementor_active );
			}
		}

		/**
		 * Flexbox Container
		 *
		 * @since 1.0.0
		 * */
		public function uich_flexbox_container() {
			
			if ( 'active' !== get_option( $this->flexbox_container_db ) ) {
				update_option( $this->flexbox_container_db, 'active' );

				return $this->uich_response( 'Successfully Enabled!', 'Flexbox Container activated Successfully.', true, '' );
			}else {
				return $this->uich_response( 'Something Went Wrong', 'Flexbox Container Alredy Activated', true, '' );
			}
		}

		/**
		 * Flexbox Container
		 * 
		 * @since 1.0.0
		 * */
		public function uich_elementor_file_uploads() {
			$fileupload = get_option( $this->file_uploads_db );

			if ( empty( $fileupload ) && $fileupload !== 1 ) {
				update_option( $this->file_uploads_db, 1 );

				return $this->uich_response( 'Successfully Enabled!', 'Unfiltered File Uploads activated Successfully.', true, '' );
			}else {
				return $this->uich_response( 'Something Went Wrong', 'File Uploads Alredy Activated', true, '' );
			}
		}

		/**
		 * Create Default setting data in db
		 *
		 * @since 1.0.0
		 *
		 * @param string $type use for check page type.
		 */
		public function uich_recommended_settings( $type ) {

			if ( 'elementor_install' === $type ) {
				if ( is_plugin_active( $this->elementor_pluginpath ) ) {
					return $this->uich_response( 'Elementor Activated', 'Elementor Activated', true, '' );
				}else{
					return $this->uich_response( 'Elementor Not Activated', 'Elementor Not Activated', false, '' );
				}
			}else if ( 'flexbox_container' === $type ) {
				$get_default = get_option( $this->flexbox_container_db );

				return $this->uich_response( '', '', true, $get_default );
			}else if( 'enable_unfiltered_file_uploads' === $type ){
				$get_default = get_option( $this->file_uploads_db );

				return $this->uich_response( '', '', true, $get_default );
			}
		}

	}

	new Uich_Api();
}
