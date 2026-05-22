<?php
/**
 * MCP Server for UiChemy
 *
 * Implements a Streamable HTTP MCP server that exposes
 * UiChemy tools to Claude Desktop and other MCP clients.
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Uichemy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uich_Api' ) ) {
	require_once UICH_PATH . 'includes/admin/class-uich-api.php';
}

if ( ! class_exists( 'Uich_MCP_Server' ) ) {

	/**
	 * Handles MCP protocol communication over Streamable HTTP transport.
	 *
	 * Registers a REST API endpoint at /uichemy/v1/mcp that accepts
	 * JSON-RPC 2.0 messages from MCP clients like Claude Desktop.
	 */
	class Uich_MCP_Server {

		// ============================================================
		// CONSTANTS
		// ============================================================

		const MCP_PROTOCOL_VERSION = '2024-11-05';
		const SERVER_NAME          = 'uichemy-wordpress-mcp';
		const SERVER_VERSION       = '1.0.0';
		const SERVER_DESCRIPTION   = 'UiChemy WordPress MCP — manage Elementor globals (colors, typography, container widths) and import converted designs into WordPress.';
		const REST_NAMESPACE       = 'uichemy/v1';
		const REST_ROUTE           = '/mcp';
		const API_KEY_OPTION       = 'uichemy_token';
		const ENABLED_OPTION       = 'uich_mcp_enabled';

		// ============================================================
		// INITIALIZATION
		// ============================================================

		/**
		 * Register the MCP REST API endpoint.
		 */
		public static function init() {
			add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
		}

		/**
		 * Register REST routes for the MCP endpoint.
		 * POST handles JSON-RPC messages from MCP clients.
		 */
		public static function register_routes() {
			register_rest_route(
				self::REST_NAMESPACE,
				self::REST_ROUTE,
				array(
					array(
						'methods'             => 'POST',
						'callback'            => array( __CLASS__, 'handle_post' ),
						'permission_callback' => array( __CLASS__, 'check_api_key' ),
					),
				)
			);
		}

		// ============================================================
		// AUTHENTICATION
		// ============================================================

		/**
		 * Validate the UiChemy security token from the request header.
		 * Accepts: UiChemy-Security-Token: <token>
		 * Uses the same token as the main UiChemy plugin (stored in 'uichemy_token' option).
		 */
		public static function check_api_key( WP_REST_Request $request ) {
			$api = new Uich_Api();

			$api->uich_check_token( $request );

			return true;
		}

		// ============================================================
		// REQUEST HANDLER
		// ============================================================

		/**
		 * Handle incoming POST requests with JSON-RPC 2.0 messages.
		 * Routes each method to the appropriate handler.
		 */
		public static function handle_post( WP_REST_Request $request ) {
			$body = $request->get_json_params();

			// Validate JSON-RPC structure
			if ( empty( $body ) || ! isset( $body['jsonrpc'] ) || '2.0' !== $body['jsonrpc'] ) {
				return self::json_rpc_error( null, -32600, 'Invalid JSON-RPC 2.0 request.' );
			}

			$method = isset( $body['method'] ) ? $body['method'] : '';
			$params = isset( $body['params'] ) ? $body['params'] : array();
			$id     = isset( $body['id'] ) ? $body['id'] : null;

			// Route to the correct handler based on MCP method
			switch ( $method ) {
				case 'initialize':
					return self::handle_initialize( $id, $params );

				case 'tools/list':
					return self::handle_tools_list( $id );

				case 'tools/call':
					return self::handle_tools_call( $id, $params );

				case 'notifications/initialized':
				case 'notifications/cancelled':
					// Notifications have no id and expect no response
					return new WP_REST_Response( null, 204 );

				default:
					return self::json_rpc_error( $id, -32601, "Method not found: {$method}" );
			}
		}

		// ============================================================
		// MCP PROTOCOL HANDLERS
		// ============================================================

		/**
		 * Handle the initialize handshake.
		 * Returns server capabilities and protocol version.
		 */
		private static function handle_initialize( $id, $params ) {
			$result = array(
				'protocolVersion' => self::MCP_PROTOCOL_VERSION,
				'capabilities'    => array(
					'tools' => new stdClass(),
				),
				'serverInfo'      => array(
					'name'        => self::SERVER_NAME,
					'version'     => self::SERVER_VERSION,
					'description' => self::SERVER_DESCRIPTION,
				),
			);

			return self::json_rpc_success( $id, $result );
		}

		/**
		 * Handle tools/list: returns all available MCP tools.
		 */
		private static function handle_tools_list( $id ) {
			$tools = array(
				array(
					'name'        => 'check_config',
					'description' => 'Checks MCP and Elementor readiness before sync. Verifies UiChemy globals class, Elementor plugin status, active kit availability, API key setup, and returns diagnostic details.',
					'inputSchema' => array(
						'type'       => 'object',
						'properties' => new stdClass(),
						'required'   => array(),
					),
				),
				array(
					'name'        => 'get_globals',
					'description' => 'Fetch all global design tokens from the active Elementor kit: colors (system + custom), typography (system + custom), and container widths per breakpoint (desktop, tablet, mobile, etc.).',
					'inputSchema' => array(
						'type'       => 'object',
						'properties' => new stdClass(),
						'required'   => array(),
					),
				),
				array(
                    'name'        => 'sync_globals',
                    'description' => 'Sync colors and typography to WordPress Elementor globals. Applies changes immediately to the active Elementor kit. Use get_globals first to compare existing values, then build the sync payload with appropriate actions (ADD for new items, SET for updates, DEL for removals). For ADD actions, generate a random 7-character hex id (e.g. "a1b2c3d"). IMPORTANT for typography: every typography value object MUST include "typography_typography" set to "custom". Every size/unit object (typography_font_size, typography_line_height, typography_letter_spacing) MUST include a "sizes" key set to empty array [].',
                    'inputSchema' => array(
                        'type'       => 'object',
                        'properties' => array(
                            'colors' => array(
                                'type'        => 'array',
                                'description' => 'Array of color sync operations.',
                                'items'       => array(
                                    'type'       => 'object',
                                    'properties' => array(
                                        'action' => array(
                                            'type' => 'string',
                                            'enum' => array( 'ADD', 'SET', 'DEL' ),
                                            'description' => 'ADD for new colors, SET to update existing, DEL to remove.',
                                        ),
                                        'value'  => array(
                                            'type'       => 'object',
                                            'properties' => array(
                                                'id'    => array(
                                                    'type'        => 'string',
                                                    'description' => 'Color id. For ADD: generate a random 7-char hex string. For SET/DEL: use the existing id from get_globals.',
                                                ),
                                                'title' => array(
                                                    'type'        => 'string',
                                                    'description' => 'Human-readable color name.',
                                                ),
                                                'value' => array(
                                                    'type'        => 'string',
                                                    'description' => 'Hex color value including hash, e.g. "#FF5733". Required for ADD and SET. Optional for DEL.',
                                                ),
                                            ),
                                            'required' => array( 'id' ),
                                        ),
                                    ),
                                    'required' => array( 'action', 'value' ),
                                ),
                            ),
                            'typography' => array(
                                'type'        => 'array',
                                'description' => 'Array of typography sync operations.',
                                'items'       => array(
                                    'type'       => 'object',
                                    'properties' => array(
                                        'action' => array(
                                            'type' => 'string',
                                            'enum' => array( 'ADD', 'SET', 'DEL' ),
                                            'description' => 'ADD for new typography, SET to update existing, DEL to remove.',
                                        ),
                                        'value'  => array(
                                            'type'       => 'object',
                                            'properties' => array(
                                                'id'    => array(
                                                    'type'        => 'string',
                                                    'description' => 'Typography id. For ADD: generate a random 7-char hex string. For SET/DEL: use the existing id from get_globals.',
                                                ),
                                                'title' => array(
                                                    'type'        => 'string',
                                                    'description' => 'Human-readable typography name.',
                                                ),
                                                'value' => array(
                                                    'type'        => 'object',
                                                    'description' => 'Typography properties object. Required for ADD and SET.',
                                                    'properties'  => array(
                                                        'typography_typography' => array(
                                                            'type'        => 'string',
                                                            'enum'        => array( 'custom' ),
                                                            'description' => 'MUST always be "custom". This activates the typography in Elementor. Without this key, typography will not apply.',
                                                        ),
                                                        'typography_font_family' => array(
                                                            'type'        => 'string',
                                                            'description' => 'Font family name, e.g. "Inter", "Playfair Display".',
                                                        ),
                                                        'typography_font_weight' => array(
                                                            'type'        => 'string',
                                                            'description' => 'Font weight as string, e.g. "400", "500", "600", "700".',
                                                        ),
                                                        'typography_font_style' => array(
                                                            'type'        => 'string',
                                                            'description' => 'Font style. Use "normal" or "italic".',
                                                        ),
                                                        'typography_font_size' => array(
                                                            'type'        => 'object',
                                                            'description' => 'Font size with unit. MUST include "sizes" as empty array.',
                                                            'properties'  => array(
                                                                'size'  => array( 'type' => 'number' ),
                                                                'unit'  => array( 'type' => 'string', 'enum' => array( 'px', 'em', 'rem', '%', 'vw' ) ),
                                                                'sizes' => array( 'type' => 'array', 'items' => new stdClass(), 'description' => 'MUST be empty array [].' ),
                                                            ),
                                                            'required' => array( 'size', 'unit', 'sizes' ),
                                                        ),
                                                        'typography_line_height' => array(
                                                            'type'        => 'object',
                                                            'description' => 'Line height with unit. MUST include "sizes" as empty array.',
                                                            'properties'  => array(
                                                                'size'  => array( 'type' => 'number' ),
                                                                'unit'  => array( 'type' => 'string', 'enum' => array( 'px', 'em', 'rem', '%' ) ),
                                                                'sizes' => array( 'type' => 'array', 'items' => new stdClass(), 'description' => 'MUST be empty array [].' ),
                                                            ),
                                                            'required' => array( 'size', 'unit', 'sizes' ),
                                                        ),
                                                        'typography_letter_spacing' => array(
                                                            'type'        => 'object',
                                                            'description' => 'Letter spacing with unit. MUST include "sizes" as empty array.',
                                                            'properties'  => array(
                                                                'size'  => array( 'type' => 'number' ),
                                                                'unit'  => array( 'type' => 'string', 'enum' => array( 'px', 'em', 'rem' ) ),
                                                                'sizes' => array( 'type' => 'array', 'items' => new stdClass(), 'description' => 'MUST be empty array [].' ),
                                                            ),
                                                            'required' => array( 'size', 'unit', 'sizes' ),
                                                        ),
                                                    ),
                                                    'required' => array( 'typography_typography', 'typography_font_family', 'typography_font_weight', 'typography_font_size' ),
                                                ),
                                            ),
                                            'required' => array( 'id' ),
                                        ),
                                    ),
                                    'required' => array( 'action', 'value' ),
                                ),
                            ),
							'container_width' => array(
								'type'        => 'object',
								'description' => 'Container width per breakpoint. Only include breakpoints you want to change.',
								'properties'  => array(
									'desktop' => array(
										'type'       => 'object',
										'properties' => array(
											'unit'  => array( 'type' => 'string', 'enum' => array( 'px', '%' ) ),
											'size'  => array( 'type' => 'number' ),
											'sizes' => array( 'type' => 'array', 'items' => new stdClass() ),
										),
									),
								),
							),
                        ),
                    ),
                    'required' => array( 'colors', 'typography', 'container_width' ),
                ),
			);

			$tools[] = array(
				'name'        => 'import_elementor_page',
				'description' => 'Import a converted Elementor JSON (from uploadToServer or convertNode URL) into WordPress as a new page/template.',
				'inputSchema' => array(
					'type'       => 'object',
					'properties' => array(
						'url' => array(
							'type'        => 'string',
							'description' => 'The uploaded JSON URL from convertNode or uploadToServer',
						),
						'title' => array(
							'type'        => 'string',
							'description' => 'Page title (default: "UiChemy Import")',
						),
						'postType' => array(
							'type'        => 'string',
							'enum'        => array( 'page', 'elementor' ),
							'description' => 'Post type: "page" for WP page, "elementor" for Elementor template (default: page)',
						),
					),
					'required' => array( 'url' ),
				),
			);

			$result = array( 'tools' => $tools );

			return self::json_rpc_success( $id, $result );
		}

		/**
		 * Handle tools/call: execute a specific tool by name.
		 * Routes to the matching tool handler.
		 */
		private static function handle_tools_call( $id, $params ) {
			// Check if MCP is enabled before executing any tool.
			// Done here (not in permission_callback) so MCP protocol handshake always works.
			$enabled = get_option( self::ENABLED_OPTION, '1' );
			if ( $enabled !== '1' ) {
				return self::json_rpc_success( $id, array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => 'MCP server is currently disabled. Enable it from the UiChemy admin dashboard → MCP Server tab.',
					),
				),
				'isError' => true,
			) );
			}

			$tool_name = isset( $params['name'] ) ? $params['name'] : '';
			$arguments = isset( $params['arguments'] ) ? $params['arguments'] : array();

			switch ( $tool_name ) {
				case 'check_config':
					return self::execute_check_config( $id );

				case 'get_globals':
					return self::execute_get_globals( $id );

				case 'sync_globals':
					return self::execute_sync_globals( $id, $arguments );

				case 'import_elementor_page':
					return self::execute_import_elementor_page( $id, $arguments );

				default:
					return self::json_rpc_error( $id, -32602, "Unknown tool: {$tool_name}" );
			}
		}

		// ============================================================
		// TOOL EXECUTORS
		// ============================================================

		/**
		 * Execute the check_config tool.
		 * Returns a readiness report for MCP + Elementor integration.
		 */
		private static function execute_check_config( $id ) {
			$has_globals_class  = class_exists( 'Uich_Globals' );
			$has_elementor      = class_exists( '\Elementor\Plugin' );
			$kit_available      = false;
			$active_kit_id      = null;
			$api_key_configured = ! empty( get_option( self::API_KEY_OPTION ) );

			if ( $has_elementor ) {
				$kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();
				if ( $kit ) {
					$kit_available = true;
					$active_kit_id = $kit->get_id();
				}
			}

			$is_ready = $has_globals_class && $has_elementor && $kit_available && $api_key_configured;

			$report = array(
				'ready'      => $is_ready,
				'checks'     => array(
					'uichemy_globals_class' => $has_globals_class,
					'elementor_active'      => $has_elementor,
					'active_kit_found'      => $kit_available,
					'api_key_configured'    => $api_key_configured,
				),
				'diagnostics' => array(
					'active_kit_id' => $active_kit_id,
					'wp_version'    => get_bloginfo( 'version' ),
					'php_version'   => phpversion(),
					'site_url'      => get_site_url(),
					'server'        => self::SERVER_NAME,
					'server_version'=> self::SERVER_VERSION,
				),
				'message'    => $is_ready
					? 'Configuration looks good. Sync tools are ready.'
					: 'Configuration issue detected. Check failed flags in "checks" before running sync.',
			);

			$result = array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => wp_json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ),
					),
				),
			);

			return self::json_rpc_success( $id, $result );
		}

		/**
		 * Execute the get_globals tool.
		 * Calls Uich_Globals::get_globals() and returns the data as MCP content.
		 */
		private static function execute_get_globals( $id ) {
			// Check that the Uich_Globals class is available
			if ( ! class_exists( 'Uich_Globals' ) ) {
				return self::json_rpc_error( $id, -32603, 'Uich_Globals class not found. Ensure UiChemy plugin is active.' );
			}

			// Check that Elementor is active
			if ( ! class_exists( '\Elementor\Plugin' ) ) {
				return self::json_rpc_error( $id, -32603, 'Elementor is not active. Global design tokens require Elementor.' );
			}

			$globals = Uich_Globals::get_globals();

			$result = array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => wp_json_encode( $globals, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ),
					),
				),
			);

			return self::json_rpc_success( $id, $result );
		}

		/**
		 * Execute the sync_globals tool.
		 * Converts MCP arguments into the object format Uich_Globals::sync_globals() expects
		 * and applies changes immediately to the active Elementor kit.
		 */
		private static function execute_sync_globals( $id, $arguments ) {
			if ( ! class_exists( 'Uich_Globals' ) ) {
				return self::json_rpc_error( $id, -32603, 'Uich_Globals class not found. Ensure UiChemy plugin is active.' );
			}

			if ( ! class_exists( '\Elementor\Plugin' ) ) {
				return self::json_rpc_error( $id, -32603, 'Elementor is not active.' );
			}

			$colors          = isset( $arguments['colors'] ) ? $arguments['colors'] : array();
			$typography      = isset( $arguments['typography'] ) ? $arguments['typography'] : array();
			$container_width = isset( $arguments['container_width'] ) ? $arguments['container_width'] : null;

			if ( ! is_array( $colors ) ) { $colors = array(); }
			if ( ! is_array( $typography ) ) { $typography = array(); }

			// Use existing Uich_Globals::sync_globals() — convert array to object for compatibility
			$sync_data = json_decode( wp_json_encode( $arguments ) );
			$sync_result = Uich_Globals::sync_globals( $sync_data );

			// Set container width AFTER sync_globals (sync_globals overwrites kit settings)
			if ( isset( $arguments['container_width'] ) && class_exists( 'Uich_Globals' ) ) {
				$cw_data = json_decode( wp_json_encode( $arguments['container_width'] ) );
				Uich_Globals::set_container_breakpoints_width( $cw_data );
			}

			// Count operations for summary
			$color_adds = 0; $color_sets = 0; $color_dels = 0;
			foreach ( $colors as $c ) {
				$action = isset( $c['action'] ) ? $c['action'] : '';
				if ( 'ADD' === $action ) { $color_adds++; }
				if ( 'SET' === $action ) { $color_sets++; }
				if ( 'DEL' === $action ) { $color_dels++; }
			}

			$typo_adds = 0; $typo_sets = 0; $typo_dels = 0;
			foreach ( $typography as $t ) {
				$action = isset( $t['action'] ) ? $t['action'] : '';
				if ( 'ADD' === $action ) { $typo_adds++; }
				if ( 'SET' === $action ) { $typo_sets++; }
				if ( 'DEL' === $action ) { $typo_dels++; }
			}

			$container_msg = $container_width ? "\nContainer width: updated" : '';

			$summary = sprintf(
				"Sync applied successfully.\n\nColors: %d added, %d updated, %d deleted\nTypography: %d added, %d updated, %d deleted%s\n\nThe Elementor kit globals have been updated and CSS cache has been cleared.",
				$color_adds, $color_sets, $color_dels,
				$typo_adds, $typo_sets, $typo_dels,
				$container_msg
			);

			$updated_globals = Uich_Globals::get_globals();

			$result = array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => $summary,
					),
					array(
						'type' => 'text',
						'text' => wp_json_encode( $updated_globals, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ),
					),
				),
			);

			return self::json_rpc_success( $id, $result );
		}

		/**
		 * Execute the import_elementor_page tool.
		 * Fetches converted JSON from URL and calls the existing import handler.
		 */
		private static function execute_import_elementor_page( $id, $arguments ) {
			if ( ! class_exists( '\Elementor\Plugin' ) ) {
				return self::json_rpc_error( $id, -32603, 'Elementor is not active.' );
			}

			$url       = isset( $arguments['url'] ) ? $arguments['url'] : '';
			$title     = isset( $arguments['title'] ) ? sanitize_text_field( $arguments['title'] ) : 'UiChemy Import';
			$post_type = isset( $arguments['postType'] ) ? sanitize_text_field( $arguments['postType'] ) : 'page';

			if ( empty( $url ) ) {
				return self::json_rpc_error( $id, -32602, 'Missing required parameter: url' );
			}

			$parsed       = wp_parse_url( $url );
			$allowed_host = 'api.uichemy.com';
			if ( empty( $parsed['host'] ) || $parsed['host'] !== $allowed_host ) {
				return self::json_rpc_error( $id, -32602, 'URL must point to a UiChemy server.' );
			}

			// Fetch the JSON from the uploaded URL
			$response = wp_remote_get( $url, array( 'timeout' => 60 ) );
			if ( is_wp_error( $response ) ) {
				return self::json_rpc_error( $id, -32603, 'Failed to fetch URL: ' . $response->get_error_message() );
			}

			$body = wp_remote_retrieve_body( $response );
			$json = json_decode( $body, true );

			if ( empty( $json ) ) {
				return self::json_rpc_error( $id, -32603, 'No valid JSON data found at URL.' );
			}

			// Build import body — map "elements" to "content" for the existing import handler
			$import_body = array(
				'title'         => ! empty( $json['title'] ) ? $json['title'] : $title,
				'content'       => isset( $json['elements'] ) ? $json['elements'] : ( isset( $json['content'] ) ? $json['content'] : array() ),
				'page_settings' => isset( $json['page_settings'] ) ? $json['page_settings'] : array(),
			);

			// Create a mock WP_REST_Request and call the existing import handler
			$mock_request = new WP_REST_Request( 'POST' );
			$mock_request->set_body( wp_json_encode( $import_body ) );
			$mock_request->set_header( 'UiChemy-Security-Token', get_option( self::API_KEY_OPTION ) );

			// Set query params
			$_GET['newImport'] = 'true';
			$_GET['postType']  = $post_type;

			$api = new \Uich_Api();
			$import_result = $api->uich_handle_elementor_import_v2( $mock_request );

			$result = array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => wp_json_encode( $import_result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ),
					),
				),
			);

			return self::json_rpc_success( $id, $result );
		}

		// ============================================================
		// JSON-RPC RESPONSE BUILDERS
		// ============================================================

		/**
		 * Build a successful JSON-RPC 2.0 response.
		 */
		private static function json_rpc_success( $id, $result ) {
			$response = array(
				'jsonrpc' => '2.0',
				'id'      => $id,
				'result'  => $result,
			);

			return new WP_REST_Response( $response, 200 );
		}

		/**
		 * Build a JSON-RPC 2.0 error response.
		 */
		private static function json_rpc_error( $id, $code, $message ) {
			$response = array(
				'jsonrpc' => '2.0',
				'id'      => $id,
				'error'   => array(
					'code'    => $code,
					'message' => $message,
				),
			);

			return new WP_REST_Response( $response, 200 );
		}
	}
}