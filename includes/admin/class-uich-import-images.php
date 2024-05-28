<?php
/**
 * This file is used when importing media files.
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Wdesignkit
 */

/**Exit if accessed directly.*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uichemy_Import_Images' ) ) {

	/**
	 * Template Import Images Here
	 *
	 * @since 1.0.0
	 */
	class Uichemy_Import_Images {

		/**
		 * Replaced images IDs.
		 *
		 * The old attachment ID and the new attachment ID generated after the import.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @var array
		 */
		private static $new_image_ids = array();

		/**
		 * Get attachment url image hash sha1.
		 *
		 * Retrieve the sha1 hash of the image URL.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param string $attachment_url The attachment URL.
		 */
		private static function get_attachment_url_hash_image( $attachment_url ) {
			return sha1( $attachment_url );
		}

		/**
		 * Media Import image.
		 *
		 * Import a single image from a remote server, upload the image WordPress
		 * uploads folder, create a new attachment in the database and updates the
		 * attachment metadata.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param array $attachment The attachment.
		 */
		public static function wdkit_Import_media( $attachment ) {
			$stored_image = self::get_store_image_saved( $attachment );

			if ( $stored_image ) {
				return $stored_image;
			}

			// Extract the file name and extension from the url.
			$file_name = basename( $attachment['url'] );

			$file_content = wp_remote_retrieve_body( wp_safe_remote_get( $attachment['url'] ) );

			if ( empty( $file_content ) ) {
				return false;
			}

			$upload_data = wp_upload_bits( $file_name, null, $file_content );

			$post_image = array(
				'post_title' => $file_name,
				'guid'       => $upload_data['url'],
			);

			$info = wp_check_filetype( $upload_data['file'] );
			if ( ! empty( $info ) ) {
				$post_image['post_mime_type'] = $info['type'];
			} else {
				return $attachment;
			}

			$post_id = wp_insert_attachment( $post_image, $upload_data['file'] );

			// On REST requests.
			if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
				require_once ABSPATH . '/wp-admin/includes/image.php';
			}

			if ( ! function_exists( 'wp_read_video_metadata' ) ) {
				require_once ABSPATH . '/wp-admin/includes/media.php';
			}

			wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload_data['file'] ) );
			update_post_meta( $post_id, 'tpgb_source_image_key', self::get_attachment_url_hash_image( $attachment['url'] ) );

			$new_attachment_img = array(
				'id'  => $post_id,
				'url' => $upload_data['url'],
			);

			if ( isset( $attachment['id'] ) ) {
				self::$new_image_ids[ $attachment['id'] ] = $new_attachment_img;
			}

			return $new_attachment_img;
		}

		/**
		 * Get store saved image.
		 *
		 * Retrieve new image ID, if the image has a new ID after the import.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @param array $attachment The attachment.
		 */
		private static function get_store_image_saved( $attachment ) {
			global $wpdb;

			if ( isset( $attachment['id'] ) && isset( self::$new_image_ids[ $attachment['id'] ] ) ) {
				return self::$new_image_ids[ $attachment['id'] ];
			}

			$post_id = $wpdb->get_var(
				$wpdb->prepare( 'SELECT `post_id` FROM `' . $wpdb->postmeta . '` WHERE `meta_key` = \'tpgb_source_image_key\' AND `meta_value` = %s;', self::get_attachment_url_hash_image( $attachment['url'] ) )
			);

			if ( ! empty( $post_id ) ) {
				$new_attachment_img = array(
					'id'  => $post_id,
					'url' => wp_get_attachment_url( $post_id ),
				);

				if ( isset( $attachment['id'] ) ) {
					self::$new_image_ids[ $attachment['id'] ] = $new_attachment_img;
				}

				return $new_attachment_img;
			}

			return false;
		}

		function check_svg_filetype_and_ext($data, $file, $filename, $mimes) {
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			if ($ext === 'svg') {
				$data['ext'] = 'svg';
				$data['type'] = 'image/svg+xml';
			}

			return $data;
		}

		/**
		 * Import images Constructor.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public function __construct() {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			add_filter('wp_check_filetype_and_ext', array( $this, 'check_svg_filetype_and_ext', 10, 4));

			WP_Filesystem();
		}
	}

}
