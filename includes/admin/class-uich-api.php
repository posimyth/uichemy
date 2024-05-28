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
		 * @var staring $file_uploads_db
		 */
		public $file_uploads_db = 'elementor_unfiltered_files_upload';

		/**
		 * Member Variable
		 *
		 * @var staring $elementor_pluginpath
		 */
		public $elementor_pluginpath = 'elementor/elementor.php';

		/**
		 * Onbording Popup Close
		 *
		 * @since 1.2.2
		 * @var uich_onbording_end of the class.
		 */
		public $uich_onbording_end = 'uich_onbording_end';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since   1.0.0
		 */
		public function __construct() {
			add_filter('upload_mimes', array( $this, 'add_svg_to_upload_mimes' ));
			add_filter( 'rest_pre_serve_request', array( $this, 'uiche_rest_send_cors_headers' ) );
			add_filter( 'http_request_timeout', array( $this, 'uich_modify_http_request_default_timeout' ), 10 );

			add_action( 'wp_ajax_uichemy_regenerate_token', array( $this, 'uiche_regenerate_token' ) );
			add_action( 'wp_ajax_uichemy_select_user', array( $this, 'uichemy_select_user' ) );
			add_action( 'wp_ajax_uich_uichemy', array( $this, 'uich_api_call' ) );

			add_filter( 'uich_recommended_settings', array( $this, 'uich_recommended_settings' ), 10, 1 );

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
						'uichemy/v2',
						'/elementor/import',
						array(
							'methods'             => 'POST',
							'callback'            => array( $this, 'uich_handle_elementor_import_v2' ),
							'permission_callback' => '__return_true',
						)
					);

					register_rest_route(
						'uichemy/v2',
						'/elementor/get_posts',
						array(
							'methods'             => 'GET',
							'callback'            => array( $this, 'uich_get_elementor_posts' ),
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
		 * Upload mimes Type
		 */
		public function add_svg_to_upload_mimes($mimes) {
            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
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

		public function uich_get_elementor_posts( WP_REST_Request $request ){
			// Match Security Token.
			$this->uich_check_token( $request );


			// fetch all post Types
			$all_post_types = get_post_types( [
				'public' => true,
				'can_export' => true,
				'_builtin' => false,
			] );


			// Create a response
			$response = array(
				'success' => true,
			);

			// Establishing installed things
			$is_elementor_installed = class_exists( 'Elementor\Plugin' );
			$is_nexter_installed = array_key_exists('nxt_builder', $all_post_types);

			// To Check if required plugins have been installed
			$response['is_elementor_pro_installed'] = class_exists( 'ElementorPro\Plugin' );
			$response['is_elementor_installed'] = $is_elementor_installed;
			$response['is_nexter_installed'] = $is_nexter_installed;

			// Initiate empty array for gathering posts
			$response['posts'] = array(
				'elementor_library' => array(),
				'page' => array(),
				'nxt_builder' => array(),
			);

			// all post types to gather
			$post_types_to_query = array();

			// Add built-in post types
			// $post_types_to_query['post'] = 'post';
			$post_types_to_query['page'] = 'page';
			if($is_elementor_installed) $post_types_to_query['elementor_library'] = 'elementor_library';
			if($is_nexter_installed) $post_types_to_query['nxt_builder'] = 'nxt_builder';

			if ( !class_exists( '\Elementor\Plugin' ) ) {
				return $response;
			}
			
			// Fetch posts for each post type
			foreach ( $post_types_to_query as $post_type ) {
				$args = array(
					'post_type'      => $post_type,
					'posts_per_page' => -1,
					'post_status'	 => 'any',
				);

				$query = new WP_Query( $args );

				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						$document = Elementor\Plugin::$instance->documents->get( get_the_ID() );

						if($document->is_built_with_elementor()){

							array_push($response['posts'][$post_type], array(
								'id' => get_the_ID(),
								'title' => get_the_title(),
							));

						}
					}
					wp_reset_postdata();
				}
			}

			return $response;
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

				// Disable Error Displaying
				ini_set( 'display_errors', 0 );
				
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
		 * Handle import v2
		 *
		 * @param WP_REST_Request $request WP_REST_Request object.
		 */
		public function uich_handle_elementor_import_v2( WP_REST_Request $request ) {

			// Match Security Token.
			$this->uich_check_token( $request );

			// Get The Body
			$json = $request->get_body();

			// Get Parameters
			$new_import = (isset( $_GET['newImport'] ) && !empty( $_GET['newImport'] )) ? sanitize_text_field($_GET['newImport']) : "true";
			$post_type = (isset( $_GET['postType'] ) && !empty( $_GET['postType'] )) ? sanitize_text_field($_GET['postType']) : 'elementor';
			$elementor_post_sub_type = (isset( $_GET['elementorPostSubType'] ) && !empty( $_GET['elementorPostSubType'] )) ? sanitize_text_field( $_GET['elementorPostSubType'] ) : '';
			$nexter_post_sub_type = (isset( $_GET['nexterPostSubType'] ) && !empty( $_GET['nexterPostSubType'] )) ? sanitize_text_field( $_GET['elementorPostSubType'] ) : '';

			// postType: 'page' | 'nexter' | 'elementor'
			$post_type_map = array(
				'page' => 'page',
				'elementor' => 'elementor_library',
				'nexter' => 'nxt_builder',
			);
			$post_type = array_key_exists($post_type, $post_type_map) ? $post_type_map[$post_type] : 'elementor_library';


			// elementorPostSubType: "header" | "footer" | "single-post" | "single-page" | "archive-page" | "search-page" | "404-page" | null
			if('elementor_library' === $post_type){
				$el_type_map = array(
					'standard-template' => 'page',
					'header' => 'header',
					'footer' => 'footer',
					'single-post' => 'single-post',
					'single-page' => 'single-page',
					'archive-page' => 'archive',
					'search-page' => 'search-results',
					'404-page' => 'error-404',
				);

				$el_type = array_key_exists($elementor_post_sub_type, $el_type_map)
					? $el_type_map[$elementor_post_sub_type]
					: 'single-page';

			} elseif ('nxt_builder' === $post_type){
				$el_type_map = array(
					'standard-template' => 'none',
					'header' => 'header',
					'footer' => 'footer',
					'singular' => 'singular',
					'archive-page' => 'archives',
					'404-page' => 'page-404',
				);

				$el_type = array_key_exists($nexter_post_sub_type, $el_type_map)
					? $el_type_map[$nexter_post_sub_type]
					: 'none';
			}


			// Check if JSON data is present.
			if ( !empty( $json ) && class_exists( '\Elementor\Plugin' ) ) {

				if($new_import === "true"){

					$json = json_decode( $json, true );
					$selected_user = apply_filters( 'uich_manage_usermanager', 'get_user' );

					// Set current user as admin.
					wp_set_current_user( null, $selected_user );

					// Disable Error Displaying
					ini_set( 'display_errors', 0 );
					
					$post_title = !empty($json) && isset($json['title']) ? sanitize_text_field($json['title']) : 'Temp 1';
					
					// if(!empty($post_type) && $post_type=='elementor'){
					// 	$post_type = 'elementor_library';
					// }

					$post_attributes = array(
						'post_title'  => $post_title,
						'post_type'   => $post_type,
						'post_status' => 'publish',
					);

					if ( 'elementor_library' === $post_type ) {
						$ell_type = empty($el_type) ? 'page' : $el_type;
						$new_document = \Elementor\Plugin::$instance->documents->create(
							$ell_type,
							$post_attributes
						);
					} else {
						$new_document = \Elementor\Plugin::$instance->documents->create(
							$post_attributes['post_type'],
							$post_attributes
						);
					}

					if ( is_wp_error( $new_document ) ) {
						wp_send_json(
							array(
								'success' => false,
								'import_failed' => $new_document->get_error_message(),
								'code'          => $new_document->get_error_code(),
							)
						);
						wp_die();
					}

					$post_content = isset($json) && !empty($json) && isset($json['content']) ? $json['content'] : [];
					$post_settings = isset($json) && !empty($json) && isset($json['page_settings']) ? $json['page_settings'] : [];
					$post_content = $this->ele_media_import($post_content);

					$new_document->save(
						array(
							'elements' => $post_content,
							'settings' => $post_settings,
						)
					);

					$inserted_id = $new_document->get_main_id();

					if('nxt_builder'===$post_type && !empty($el_type)){
						if ( '' === get_post_meta( $inserted_id, 'nxt-hooks-layout', true ) ) {
							if($el_type=='header' || $el_type=='footer'){
								add_post_meta( $inserted_id, 'nxt-hooks-layout-sections', $el_type );
								add_post_meta( $inserted_id, 'nxt-hooks-layout', 'sections' );
							}
							if($el_type=='page-404' || $el_type=='singular' || $el_type=='archives'){
								add_post_meta( $inserted_id, 'nxt-hooks-layout-pages', $el_type );
								add_post_meta( $inserted_id, 'nxt-hooks-layout', 'pages' );
							}
						}
					}

					return array(
						'success' => true,
						'result'  => array(
							'title'     => get_the_title( $inserted_id ),
							'edit_link' => get_edit_post_link( $inserted_id, 'internal' ),
							'view'      => get_permalink( $inserted_id ),
						),
					);

				}else if($new_import === "false"){
					$exist_post_id = (isset($_GET['importToPost']) && !empty($_GET['importToPost'])) ? (int) sanitize_text_field($_GET['importToPost']) : '';
					$importByReplacing = (isset($_GET['importByReplacing']) && !empty($_GET['importByReplacing'])) ? sanitize_text_field($_GET['importByReplacing']) : 'false';

					if(empty($exist_post_id)){
						return array(
							'success' => false,
							'message' => esc_html__( 'Post ID not provided.', 'uichemy' ),
						);
					}

					if(!empty($exist_post_id)){
						$exits_post_type = get_post_type( $exist_post_id ); 
						if(defined('ELEMENTOR_PATH') && class_exists('Elementor\Plugin') && !empty($exits_post_type) && $exits_post_type == $post_type ) {
							$elementor_instance = Elementor\Plugin::$instance;
							
							$document = $elementor_instance->documents->get_doc_or_auto_save($exist_post_id);
							if ($document) {
								$selected_user = apply_filters( 'uich_manage_usermanager', 'get_user' );

								// Set current user as admin.
								wp_set_current_user( null, $selected_user );

								// Get the content and convert it to JSON
								$exits_content = $document->get_elements_data();
								
								$json_array = json_decode($json, true);
								$update_content = !empty($json_array) && isset($json_array['content']) ? $json_array['content'] : [];

								if(!empty($importByReplacing) && $importByReplacing=='false'){
									$merged_content = array_merge($exits_content, $update_content);
								}else if(!empty($importByReplacing) && $importByReplacing=='true'){
									$merged_content = $update_content;
								}

								// Save Images <-- Test this
								$merged_content = $this->ele_media_import($merged_content);

								$document->save(
									array(
										'elements' => $merged_content,
									)
								);

								return array(
									'success' => true,
									'result'  => array(
										'title'     => get_the_title( $exist_post_id ),
										'edit_link' => get_edit_post_link( $exist_post_id, 'internal' ),
										'view'      => get_permalink( $exist_post_id ),
									),
								);

							} else {
								return array(
									'success' => false,
									'message' => esc_html__( 'This post doesn\'t have Elementor data.', 'uichemy' ),
								);
							}
						}
					}
				}

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

			if ( ! empty( $origin ) && preg_match( '/\/wp-json\/uichemy\/v/', wp_parse_url( $requesturi, PHP_URL_PATH ) ) === 1 ) {
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
			$nonce = wp_create_nonce("bricks-nonce-admin");

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
		 * Create Default setting data in db
		 *
		 * @since 1.2.2
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
			}else if( 'bricks_svg_uploads' === $type ) {
				$uroles = get_editable_roles();
				$capability = 'bricks_upload_svg';
				$briRole = array();
				foreach ($uroles as $role_key => $role_data) {
					if (isset($role_data['capabilities'][$capability]) && $role_data['capabilities'][$capability] === true) {
						$briRole[$role_key] = $role_data;
					}
				}

				return $this->uich_response( '', '', true, $briRole );
			}
		}

		/**
		 * Get uichemy Api Call Ajax.
		 *
		 * @since 1.0.10
		 */
		public function uich_api_call() {

			check_ajax_referer( 'uichemy-ajax-nonce', 'nonce' );

			if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
				wp_send_json( $this->uich_response( 'User Roll Issue', 'is_user_logged_in', true, '' ) );
				wp_die();
			}

			$type = isset( $_POST['type'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['type'] ) ) ) : false;
			if ( empty( $type ) ) {
				wp_send_json( $this->uich_response( 'Something went wrong.', 'Type Not Found', true, '' ) );
				wp_die();
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
				case 'bricks_file_uploads':
					$data = $this->uich_bricks_file_uploads();
				break;
			}

			wp_send_json( $data );
			wp_die();
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
				activate_plugin( $this->elementor_pluginpath );

				if ( is_plugin_active( $this->elementor_pluginpath ) ) {
					return $this->uich_response( 'Successfully Activated!', 'Elementor Installed and Activated Successfully.', true, '' );
				} else {
					return $this->uich_response( 'Something Went Wrong', 'Not Activate Plugin', false, '' );
				}

			}
		}

		/**
		 * Flexbox Container
		 *
		 * @since 1.2.2
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
		 * @since 1.2.2
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
		 * Bricks File Uploads
		 *
		 * @since 1.2.2
		 */
		public function uich_bricks_file_uploads(){

			$roles = wp_roles()->get_names();
			foreach ( $roles as $role_key => $label ) {
				wp_roles()->add_cap( $role_key, 'bricks_upload_svg' );
			}

			return $this->uich_response( 'Successfully Enabled!', 'Unfiltered File Uploads activated Successfully.', true, '' );
		}

		public function ele_media_import( $content = array() ) {
			if ( ! class_exists( 'Uichemy_Import_Images' ) ) {
				require_once UICH_PATH . 'includes/admin/class-uich-import-images.php';
			}
			if ( ! empty( $content ) ) {
				$media_import = array( $content );
				$media_import = self::widgets_elements_id_change( $media_import );
				$media_import = self::widgets_import_media_copy_content( $media_import );
				$content      = $media_import[0];
			}
			return $content;
		}

		/**
		 * Widgets elements data
		 */
		protected static function widgets_elements_id_change( $media_import ) {
			if ( did_action( 'elementor/loaded' ) ) {
				return \Elementor\Plugin::instance()->db->iterate_data(
					$media_import,
					function ( $element ) {
						$element['id'] = \Elementor\Utils::generate_random_string();
						return $element;
					}
				);
			} else {
				return $media_import;
			}
		}

		/**
		 * Widgets Media import copy content.
		 *
		 */
		protected static function widgets_import_media_copy_content( $media_import ) {
			if ( did_action( 'elementor/loaded' ) ) {

				return \Elementor\Plugin::instance()->db->iterate_data(
					$media_import,
					function ( $element_data ) {
						$elements = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );

						if ( ! $elements ) {
							return null;
						}

						return self::widgets_element_import_start( $elements );
					}
				);
			} else {
				return $media_import;
			}
		}

		/**
		 * Start element copy content for media import.
		 *
		 */
		protected static function widgets_element_import_start( \Elementor\Controls_Stack $element ) {
			$get_element_instance = $element->get_data();
			$tp_mi_on_fun         = 'on_import';

			if ( method_exists( $element, $tp_mi_on_fun ) ) {
				$get_element_instance = $element->{$tp_mi_on_fun}( $get_element_instance );
			}

			foreach ( $element->get_controls() as $get_control ) {
				$control_type = \Elementor\Plugin::instance()->controls_manager->get_control( $get_control['type'] );
				$control_name = $get_control['name'];

				if ( ! $control_type ) {
					return $get_element_instance;
				}

				if ( method_exists( $control_type, $tp_mi_on_fun ) ) {
					$get_element_instance['settings'][ $control_name ] = $control_type->{$tp_mi_on_fun}( $element->get_settings( $control_name ), $get_control );
				}
			}

			return $get_element_instance;
		}

	}

	new Uich_Api();
}
