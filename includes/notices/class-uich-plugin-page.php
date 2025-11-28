<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    uichemy
 * @subpackage uichemy/includes
 */

/**Exit if accessed directly.*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uich_Plugin_Page' ) ) {

	/**
	 * Uich_Plugin_Page
	 *
	 * @since 1.0.0
	 */
	class Uich_Plugin_Page {

		/**
		 * Singleton instance variable.
		 *
		 * @var instance|null The single instance of the class.
		 */
		private static $instance;

		/**
		 * Singleton instance getter method.
		 *
		 * @since 1.0.0
		 *
		 * @return self The single instance of the class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Initializes the core functionalities of the plugin for admin users with 'manage_options' capability.
		 *
		 * This constructor method checks if the current user is in the WordPress admin dashboard
		 * and has the capability to manage options. If these conditions are met, it adds specific
		 * filters to enhance the plugin's functionality in the admin area.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public function __construct() {

			if ( is_admin() && current_user_can( 'manage_options' ) ) {
				// Add a filter to include a settings link for the plugin in the WordPress plugins page.
				add_filter( 'plugin_action_links_' . UICH_PBNAME, array( $this, 'uich_settings_pro_link' ) );

				// Add a filter to include additional links/meta for the plugin on the WordPress plugins page.
				add_filter( 'plugin_row_meta', array( $this, 'uich_extra_links_plugin_row_meta' ), 10, 2 );
			}
		}

		/**
		 * Generates additional links for the plugin on the plugins page.
		 *
		 * This function modifies the plugin's action links by adding custom links for 'Settings' and 'Need Help?'
		 * to the existing list of links displayed on the WordPress plugins page.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string[] $links An array containing the existing links for the plugin.
		 * @return string[] An updated array with additional custom links.
		 */
		public function uich_settings_pro_link( $links ) {

			$settings  = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=uichemy-welcome' ), __( 'Settings', 'uichemy' ) );
			$need_help = sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_url( 'https://uichemy.com/' ), __( 'Need Help?', 'uichemy' ) );

			$links   = (array) $links;
			$links[] = $settings;
			$links[] = $need_help;

			return $links;
		}

		/**
		 * Adds extra links/meta to the plugin's row on the WordPress plugins page.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param string $plugin_meta return full array.
		 * @param string $plugin_file check path.
		 * @return array An updated array containing additional custom links/meta.
		 */
		public function uich_extra_links_plugin_row_meta( $plugin_meta = array(), $plugin_file = '' ) {

			if ( strpos( $plugin_file, UICH_PBNAME ) !== false ) {
				$new_links = array(
					'video-tutorials' => '<a href="' . esc_url( 'https://youtu.be/8_6DymM-5KQ?si=YdyU6U-ASiWMzHkb' ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Video Tutorials', 'uichemy' ) . '</a>',
					'join-community'  => '<a href="' . esc_url( 'https://www.facebook.com/groups/uichemy' ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Join Community', 'uichemy' ) . '</a>',
					'whats-new'       => '<a href="' . esc_url( 'https://roadmap.uichemy.com/updates' ) . '" target="_blank" rel="noopener noreferrer" style="color: orange;">' . esc_html__( 'What\'s New?', 'uichemy' ) . '</a>',
					'req-feature'     => '<a href="' . esc_url( 'https://roadmap.uichemy.com/boards/feature-requests' ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Request Feature', 'uichemy' ) . '</a>',
				);

				$plugin_meta = array_merge( $plugin_meta, $new_links );
			}

			return $plugin_meta;
		}
	}

	Uich_Plugin_Page::get_instance();
}
