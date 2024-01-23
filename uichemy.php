<?php
/**
 * Plugin Name:       UiChemy
 * Plugin URI:        https://uichemy.com
 * Description:       Convert Figma Design to 100% Editable WordPress websites in Elementor Website Builder and Gutenberg aka WordPress Block Editor.
 * Version:           1.0.0
 * Author:            POSIMYTH
 * Author URI:        https://posimyth.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       uichemy
 * Requires at least: 5.7.0
 * Tested up to:      6.4
 * Requires PHP:      7.1
 *
 * @link              https://posimyth.com
 * @since             1.0.0
 * @package           Uichemy
 */

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'UICH_VERSION', '1.0.0' );
define( 'UICH_URL', plugins_url( '/', __FILE__ ) );
define( 'UICH_PATH', plugin_dir_path( __FILE__ ) );
define( 'UICH_USERNAME', 'uichemy' );

/**
 * Load Text Domain.
 * Text Domain : uichemy
 *
 * @since   1.0.0
 */
function uich_plugins_loaded() {
	load_plugin_textdomain( 'uichemy', false, UICH_PATH . '/languages' );
}
add_action( 'plugins_loaded', 'uich_plugins_loaded' );

/**
 * Added admin Menu for dashbord
 *
 * @since   1.0.0
 */
function uich_admin_menu() {
	add_menu_page(
		esc_html__( 'UiChemy', 'uichemy' ),
		esc_html__( 'UiChemy', 'uichemy' ),
		'manage_options',
		'uichemy-settings',
		'uich_menu_page_template',
		UICH_URL . 'assets/svg/bw-logo.svg'
	);
}
add_action( 'admin_menu', 'uich_admin_menu' );

/**
 * Dashbord Menu funciton call for backend design
 *
 * @since   1.0.0
 */
function uich_menu_page_template() {
	include UICH_PATH . 'admin/admin.php';
}

/**
 * Enqueue admin script
 *
 * @since   1.0.0
 *
 * @param string $page get current page slug.
 */
function uich_enqueue_admin_scripts( $page ) {

	if ( 'toplevel_page_uichemy-settings' !== $page ) {
		return;
	}

	wp_enqueue_style( 'uichemy-wp-menu-image-style', UICH_URL . 'assets/css/wp-menu-image.css', array(), UICH_VERSION, 'all' );
	wp_enqueue_style( 'uichemy-style', UICH_URL . 'assets/css/out.css', array(), UICH_VERSION, 'all' );

	wp_enqueue_script( 'uichemy-script', UICH_URL . 'assets/js/uichemy-script.js', array( 'jquery' ), UICH_VERSION, true );
	wp_enqueue_script( 'uichemy-script', UICH_URL . 'admin/uichemy-script.js', array( 'jquery' ), UICH_VERSION, true );
	wp_localize_script(
		'uichemy-script',
		'uiche_ajax_object',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'uichemy-ajax-nonce' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'uich_enqueue_admin_scripts' );

/**
 * Check regenerate token
 *
 * @since   1.0.0
 */
function uiche_regenerate_token() {
	$nonce = ( isset( $_POST['nonce'] ) ) ? sanitize_key( wp_unslash( $_POST['nonce'] ) ) : '';

	if ( ! isset( $nonce ) || empty( $nonce ) || ! wp_verify_nonce( $nonce, 'uichemy-ajax-nonce' ) ) {
		wp_send_json_error( null, 400 );
	}

	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( null, 400 );
	}

	Uich_Token_Manager::reset_token();

	// Prepare the response with the new token.
	$response = array(
		'message' => esc_html__( 'Reset Successful', 'uichemy' ),
		'token'   => Uich_Token_Manager::get_token(),
	);

	// Send the JSON response back to the client-side JavaScript.
	wp_send_json_success( $response );
}

add_action( 'wp_ajax_uichemy_regenerate_token', 'uiche_regenerate_token' );

/**
 * Activation Hook.
 *
 * @since   1.0.0
 */
function uich_on_activate() {
	uich_create_user_with_edit_posts_capability();
	uich_generate_and_store_token();
}
register_activation_hook( __FILE__, 'uich_on_activate' );

/**
 * Deactivation Hook
 *
 * @since   1.0.0
 */
function uich_on_deactivate() {
	uich_delete_token();
}
register_deactivation_hook( __FILE__, 'uich_on_deactivate' );

// Load Token related file.
require_once UICH_PATH . 'class-uich-token-manager.php';

/**
 * Create New Token for uich
 *
 * @since   1.0.0
 */
function uich_generate_and_store_token() {
	Uich_Token_Manager::create_token();
}

/**
 * Delete Token for uich
 *
 * @since   1.0.0
 */
function uich_delete_token() {
	Uich_Token_Manager::delete_token();
}

/**
 * Here Create New user for uich.
 *
 * @since   1.0.0
 */
function uich_create_user_with_edit_posts_capability() {
	$username = UICH_USERNAME;

	if ( ! username_exists( $username ) ) {
		// Generate a random password.
		$password = wp_generate_password( 12 ); // Generates a random password with 12 characters.

		$userdata = array(
			'user_login' => $username,
			'user_pass'  => $password,
		);
		$user_id  = wp_insert_user( $userdata );

		// Add the 'edit_posts' capability to the user.
		$user = new WP_User( $user_id );
		$user->add_cap( 'edit_posts' );
	}
}

/**
 * Check http_request time
 *
 * @since   1.0.0
 */
function uich_modify_http_request_default_timeout() {
	return 30;
}
add_filter( 'http_request_timeout', 'uich_modify_http_request_default_timeout', 1, 0 );

/**
 * Check token matching
 *
 * @since   1.0.0
 *
 * @param WP_REST_Request $request WP_REST_Request object.
 */
function uich_are_tokens_matching( WP_REST_Request $request ) {

	$token = $request->get_header( 'UiChemy-Security-Token' );

	if ( is_null( $token ) || Uich_Token_Manager::get_token() !== $token ) {
		return false;
	}

	return true;
}

/**
 * Check Handle import
 *
 * @since   1.0.0
 *
 * @param WP_REST_Request $request WP_REST_Request object.
 */
function uich_handle_import( WP_REST_Request $request ) {

	// In case of shutdown.
	header( 'Access-Control-Allow-Origin: *' );

	// Match Security Token.
	if ( ! uich_are_tokens_matching( $request ) ) {
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
		wp_set_current_user( null, PLUGIN_USERNAME );

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
function uich_handle_check( WP_REST_Request $request ) {

	if ( uich_are_tokens_matching( $request ) ) {
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
function uiche_rest_send_cors_headers( $value ) {
	$origin     = get_http_origin();
	$requesturi = ! empty( $_SERVER['REQUEST_URI'] ) ? wdkit_sanitizer_bypass( $_SERVER, 'REQUEST_URI' ) : '';

	if ( ! empty( $origin ) && preg_match( '/^\/wp-json\/uichemy\/v1/', wp_parse_url( $requesturi, PHP_URL_PATH ) ) === 1 ) {
		header( 'Access-Control-Allow-Headers: UiChemy-Security-Token' );
	}

	return $value;
}
add_filter( 'rest_pre_serve_request', 'uiche_rest_send_cors_headers', 0 );

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'uichemy/v1',
			'/import',
			array(
				'methods'             => 'POST',
				'callback'            => 'uich_handle_import',
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'uichemy/v1',
			'/check',
			array(
				'methods'             => array( 'GET', 'POST' ),
				'callback'            => 'uich_handle_check',
				'permission_callback' => '__return_true',
			)
		);
	}
);

/**
 * Parse args check senitizer
 *
 * @since 1.0.0
 *
 * @param string $data send all post data.
 * @param string $type store text data.
 */
function uiche_sanitizer_bypass( $data, $type ) {
	return wp_unslash( $data[ $type ] );
}
