<?php
/**
 * Plugin Name:       UiChemy — Figma Converter for Elementor, Gutenberg and Bricks
 * Plugin URI:        https://uichemy.com
 * Description:       Convert Figma Design to 100% Editable WordPress websites in Elementor Website Builder and Gutenberg aka WordPress Block Editor.
 * Version:           4.4.3
 * Author:            POSIMYTH
 * Author URI:        https://posimyth.com
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       uichemy
 * Requires at least: 6.6
 * Tested up to:      6.9
 * Requires PHP:      7.4
 *
 * @link              https://posimyth.com
 * @package           Uichemy
 */

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'UICH_VERSION', '4.4.3' );
define( 'UICH_FILE', __FILE__ );
define( 'UICH_PATH', plugin_dir_path( __FILE__ ) );
define( 'UICH_URL', plugins_url( '/', __FILE__ ) );
define( 'UICH_BDNAME', basename( __DIR__ ) );
define( 'UICH_PBNAME', plugin_basename( __FILE__ ) );
define( 'UICH_TOKEN_OPTION', 'uichemy_token' );
define( 'UICH_USER_OPTION', 'uichemy_user' );
define( 'UICH_ADMIN_NOTICE_FALG', 1 );

require UICH_PATH . 'includes/class-uich-uichemy.php';
