<?php
/**
 * This file specifically loads JavaScript and CSS dependencies.
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Uichemy
 */

namespace Uich\Uich_enqueue;

/**
 * Exit if accessed directly.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uich_Enqueue' ) ) {

	/**
	 * Here Enqueue all js and css script
	 */
	class Uich_Enqueue {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since   1.0.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'uich_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'uich_admin_scripts' ), 10, 1 );

			// Gutenberg editor load
			add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );
		}

		public function editor_assets() {
			global $pagenow;
			$scripts_dep = array( 'react', 'react-dom', 'wp-block-editor', 'wp-element', 'wp-blocks', 'wp-i18n','wp-plugins', 'wp-components','wp-api-fetch');
			if ( 'widgets.php' !== $pagenow && 'customize.php' !== $pagenow ) {
				$scripts_dep = array_merge($scripts_dep, array('wp-editor', 'wp-edit-post'));
				wp_enqueue_script('uich-editor-js', UICH_URL . 'assets/js/uich-copy-button.js', $scripts_dep, '1.0.0', false);
			}
		}

		/**
		 * Add Menu Page WdKit.
		 *
		 * @since   1.0.0
		 */
		public function uich_admin_menu() {
			$capability = 'manage_options';

			if ( current_user_can( $capability ) ) {
				add_menu_page( __( 'UiChemy', 'uichemy' ), __( 'UiChemy', 'uichemy' ), 'manage_options', 'uichemy-welcome', array( $this, 'uich_menu_page_template' ), UICH_URL . 'assets/svg/bw-logo.svg' );

				add_submenu_page( 'uichemy-welcome', __( 'Settings', 'uichemy' ), __( 'Settings', 'uichemy' ), 'manage_options', 'uichemy-settings', array( $this, 'uich_submenu_settings_page_template' ) );
			}
		}

		/**
		 * Load Uichemy uichemy-settings page content.
		 *
		 * @since   1.0.0
		 */
		public function uich_menu_page_template() {
			require_once UICH_PATH . 'includes/pages/uich-welcome-page.php';
		}

		/**
		 * Load Uichemy uichemy-settings page content.
		 *
		 * @since   1.0.0
		 */
		public function uich_submenu_settings_page_template() {
			require_once UICH_PATH . 'includes/pages/uich-settings-page.php';
		}

		/**
		 * Enqueue Scripts admin area.
		 *
		 * @since   1.0.0
		 *
		 * @param string $page use for check page type.
		 */
		public function uich_admin_scripts( $page ) {

			$slug = array( 'uichemy_page_uichemy-settings', 'toplevel_page_uichemy-welcome' );
			if ( ! in_array( $page, $slug, true ) ) {
				return;
			}

			$this->uich_enqueue_styles();
			$this->uich_enqueue_scripts();
		}

		/**
		 * Enqueue Styles admin area.
		 *
		 * @since   1.0.0
		 *
		 * @param string $page use for check page type.
		 */
		public function uich_enqueue_styles() {
			wp_enqueue_style( 'uichemy-welcome-style', UICH_URL . 'assets/css/welcome-page.css', array(), UICH_VERSION, 'all' );
		}

		/**
		 * Enqueue script admin area.
		 *
		 * @since   1.0.0
		 */
		public function uich_enqueue_scripts() {
			wp_enqueue_script( 'uichemy-script', UICH_URL . 'assets/js/uichemy-script.js', array( 'jquery' ), UICH_VERSION, true );
			wp_localize_script(
				'uichemy-script',
				'uichemy_ajax_object',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'uichemy-ajax-nonce' ),
				)
			);
		}
	}

	new Uich_Enqueue();
}
