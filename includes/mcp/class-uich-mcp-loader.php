<?php
/**
 * MCP Server Bootstrap for UiChemy
 *
 * Include this file from your main UiChemy plugin file to enable MCP support.
 * Example: require_once plugin_dir_path( __FILE__ ) . 'mcp/class-uich-mcp-loader.php';
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Uichemy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load MCP server class
require_once plugin_dir_path( __FILE__ ) . 'class-uich-mcp-server.php';

// Initialize the MCP REST endpoint (runs on every request)
Uich_MCP_Server::init();