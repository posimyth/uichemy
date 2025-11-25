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

if ( ! class_exists( 'Uich_Import_Images' ) ) {

	/**
	 * Template Import Images Here
	 *
	 * @since 1.0.0
	 */
	class Uich_Import_Images {

		// for store the images
		private static $new_image_ids = [];

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		public static function gutenberg_import_media_copy_content( $data_import ){

			$block_name = $data_import[0]['blockName'];

			// for tpag
			if(substr($block_name, 0, 4) === "tpgb"){
				return self::array_recursively_data(
					$data_import,
					function( $block_data ) {
						$elements = self::block_data_instance( $block_data );
						return $elements;
					}
				);
			}

			// for Gutenberg, Kadence , Spectra , Genrate Block
			if(is_array($data_import) || is_object($data_import)){
				return self::array_recursively_data( $data_import,
					function( $block_data ) {
						$elements = self::check_block_image_upload( $block_data );
						return $elements;
					} 
				);
			}
		}

		public static function string_replace_content(  $content = '', $old_url = '', $new_url = ''){

			if (is_string($content)) {
				return str_replace($old_url, $new_url, $content);
			}
		
			if (is_array($content)) {
				$newArray = array();
				foreach ($content as $key => $value) {
					$newArray[$key] = self::string_replace_content( $value, $old_url, $new_url );
				}
				return $newArray;
			}
		
			return $content;
		}

		public static function upload_media($url) {
			// Check if the image already exists in the media library
			$existing_image = self::find_existing_image_attachment(['url' => $url]);
	
			if ($existing_image) {
				return ['url' => $existing_image['url'], 'post_id' => (int) $existing_image['id']];
			}
	
			// Extract the file name and download the file content
			$file_name = basename($url);
			$file_content = wp_remote_retrieve_body(wp_safe_remote_get($url));
	
			if (empty($file_content)) {
				return false;
			}
	
			// Upload the file content to WordPress
			$upload_data = wp_upload_bits($file_name, null, $file_content);
	
			// Prepare attachment post data
			$post_image = [
				'post_title' => $file_name,
				'guid' => isset($upload_data['url']) ? $upload_data['url'] : '',
			];
	
			// Set the MIME type if available
			$file_info = wp_check_filetype($upload_data['file']);
			if (!empty($file_info['type'])) {
				$post_image['post_mime_type'] = $file_info['type'];
			} else {
				return false;
			}
	
			// Insert the attachment into the media library
			$post_id = wp_insert_attachment($post_image, $upload_data['file']);
	
			// Load required functions for metadata generation
			if (!function_exists('wp_generate_attachment_metadata')) {
				require_once ABSPATH . '/wp-admin/includes/image.php';
			}

			// Generate and update attachment metadata
			wp_update_attachment_metadata(
				$post_id,
				wp_generate_attachment_metadata($post_id, $upload_data['file'])
			);
	
			// Store a unique meta key for future duplicate prevention
			update_post_meta($post_id, 'tpgb_source_image_key', self::get_attachment_url_hash_image($url));
	
			return ['url' => $upload_data['url'], 'post_id' => (int) $post_id];
		}

		private static function find_existing_image_attachment($attachment) {
			global $wpdb;
	
			if (isset($attachment['id']) && isset(self::$new_image_ids[$attachment['id']])) {
				return self::$new_image_ids[$attachment['id']];
			}
	
			// Query for an existing attachment by meta key and hash value
			$post_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT `post_id` FROM `{$wpdb->postmeta}` WHERE `meta_key` = 'tpgb_source_image_key' AND `meta_value` = %s",
					self::get_attachment_url_hash_image($attachment['url'])
				)
			);

			if (!empty($post_id)) {
				$new_attachment_img = [
					'id' => $post_id,
					'url' => wp_get_attachment_url($post_id),
				];
				if (isset($attachment['id'])) {
					self::$new_image_ids[$attachment['id']] = $new_attachment_img;
				}
				return $new_attachment_img;
			}
	
			return false;
		}

		private static function get_attachment_url_hash_image( $attachment_url ) {
			return sha1( $attachment_url );
		}

		public static function check_block_image_upload( $block_data ) {
			if (isset($block_data['blockName']) && isset($block_data['attrs'])) {

				// Spectra Image Block
				if ($block_data['blockName'] === 'uagb/image') {
					if (isset($block_data['attrs']['url']) && isset($block_data['attrs']['id']) && !empty($block_data['attrs']['url'])) {
						$result = self::upload_media($block_data['attrs']['url']);
						$block_data['attrs']['url'] = $block_data['attrs']['urlTablet'] = $block_data['attrs']['urlMobile'] = $result['url'];
						$block_data['attrs']['id'] = $result['post_id'];
					}
				}

				// Spectra Container Image Block
				elseif ($block_data['blockName'] === 'uagb/container') {
					if (isset($block_data['attrs']['backgroundImageDesktop']['url']) && isset($block_data['attrs']['backgroundImageDesktop']['id']) && !empty($block_data['attrs']['backgroundImageDesktop']['url'])) {
						$result = self::upload_media($block_data['attrs']['backgroundImageDesktop']['url']);
						$block_data['attrs']['backgroundImageDesktop']['url'] = $result['url'];
						$block_data['attrs']['backgroundImageDesktop']['id'] = $result['post_id'];
					}
				}

				// Core Image Block
				elseif ($block_data['blockName'] === 'core/image') {
					if (isset($block_data['innerHTML']) && isset($block_data['attrs']['id']) && !empty($block_data['innerHTML'])) {
						
						// Extract the URL from innerHTML using regex
						preg_match('/src="(https:\/\/api\.uichemy\.com\/[^"]+)"/', $block_data['innerHTML'], $matches);

						if (!empty($matches[1])) {
							// Extracted old URL
							$old_url = $matches[1];
							
							$result = self::upload_media($old_url);
							
							// Ensure the upload result contains the URL
							if (isset($result['url']) && isset($result['post_id'])) {
								$new_url = $result['url'];
								$new_post_id = $result['post_id'];
				
								// Replace the old URL with the new one in innerHTML
								$block_data['innerHTML'] = str_replace($old_url, $new_url, $block_data['innerHTML']);
								
								// Replace the wp-image-<id> with wp-image-<new_post_id> in innerHTML
								$block_data['innerHTML'] = preg_replace('/wp-image-\d+/', 'wp-image-' . $new_post_id, $block_data['innerHTML']);
								
								// Replace the old URL with the new one in innerContent
								if (is_array($block_data['innerContent'])) {
									foreach ($block_data['innerContent'] as &$content) {
										// Replace the old URL with the new URL in each content element
										$content = str_replace($old_url, $new_url, $content);
										
										// Replace wp-image-<id> with wp-image-<new_post_id> in innerContent
										$content = preg_replace('/wp-image-\d+/', 'wp-image-' . $new_post_id, $content);
									}
								}

								$block_data['attrs']['id'] = $new_post_id;
							}
						}
					}
				}

				// Core Container Image Block
				elseif ($block_data['blockName'] === 'core/group') {
					if (isset($block_data['attrs']['style']['background']['backgroundImage']['url']) && isset($block_data['attrs']['style']['background']['backgroundImage']['id']) && !empty($block_data['attrs']['style']['background']['backgroundImage']['url'])) {
						
						$result = self::upload_media($block_data['attrs']['style']['background']['backgroundImage']['url']);
						$block_data['attrs']['style']['background']['backgroundImage']['url'] = $result['url'];
						$block_data['attrs']['style']['background']['backgroundImage']['id'] = $result['post_id'];
					}
				}

				// Kadence Image Block
				elseif($block_data['blockName'] === "kadence/image"){
					if (isset($block_data['innerHTML']) && strpos($block_data['innerHTML'], 'img') !== false) {
						$new_updated = self::update_img_src_with_uploaded_url($block_data['innerHTML'], $block_data['blockName']);

						$block_data['innerHTML'] = $new_updated['data'];
						$block_data['attrs']['id'] = $new_updated['post_id'];
						if(isset($block_data['innerContent'])){
							$block_data['innerContent'][0] = $new_updated['data'];
						}
					}
				}	

				// Kadence Container Image Block
				elseif($block_data['blockName'] === "kadence/column"){
					if (isset($block_data['attrs']['backgroundImg']) && is_array($block_data['attrs']['backgroundImg'])) {
						foreach ($block_data['attrs']['backgroundImg'] as &$bgImg) {
							if (isset($bgImg['bgImg'])) {
								$new_media_url = self:: upload_media($bgImg['bgImg']);
								$bgImg['bgImg'] = $new_media_url['url'];
								$bgImg['bgImgID'] = $new_media_url['post_id'];
							}
						}
					}	
				}

				// GenerateBlocks Image Block
				elseif($block_data['blockName'] === "generateblocks/media"){

					if (isset($block_data['innerHTML']) && strpos($block_data['innerHTML'], 'img') !== false) {
						$new_updated = self::update_img_src_with_uploaded_url($block_data['innerHTML'], $block_data['blockName']);
						$pattern = '/<img\s+[^>]*src=["\']([^"\']+)["\']/i';

						// Check if the pattern matches and extract the URL
						if (preg_match($pattern, $new_updated['data'], $matches)) {
							// Extracted URL from src attribute
							$original_url = $matches[1];
							$block_data['attrs']['htmlAttributes']['src'] = $original_url;
						}

						$block_data['innerHTML'] = $new_updated['data'];
						$block_data['innerContent'][0] = $new_updated['data'];			
					
					}
				}

				// GenerateBlocks Container Image Block
				elseif($block_data['blockName'] === "generateblocks/element"){
					if(isset($block_data['attrs']['styles']) && isset($block_data['attrs']['styles']['backgroundImage'])){
						$link = str_replace(['url(', ')'], '', $block_data['attrs']['styles']['backgroundImage']);
						$result = self::upload_media($link);

						if($result && is_array($result) && isset($result['url'])){
							$block_data['attrs']['styles']['backgroundImage'] = 'url(' . $result['url'] . ')';
						}
					}
				}
			}

			return $block_data;
		}

		public static function update_img_src_with_uploaded_url($data, $block_name) {
			// Use regex to match the src attribute in the img tag
			$pattern = '/<img\s+[^>]*src=["\']([^"\']+)["\']/i';
			
			// Check if the pattern matches and extract the URL
			if (preg_match($pattern, $data, $matches)) {

				// Extracted URL from src attribute
				$original_url = $matches[1]; 
				
				// Upload the media and get the new URL
				$upload_result = self::upload_media($original_url);
				$post_id = $upload_result['post_id'];
				
				// If upload was successful, replace the URL in the original data
				if ($upload_result && isset($upload_result['url'])) {
					$new_url = $upload_result['url'];
					// Replace the original URL with the new URL in the data
					$data = str_replace($original_url, $new_url, $data);
				}
				
				if($block_name === "kadence/image"){
					// Kadence: img tag in class update 
					$data = preg_replace_callback('/<img\s+[^>]*>/i', function($matches) use ($post_id) {
						$img_tag = $matches[0]; // Full <img> tag
						
						// Check if the class attribute exists, otherwise add one
						if (strpos($img_tag, 'class="') !== false) {
							// Update the class to include wp-image-{post_id}
							$updated_img_tag = preg_replace('/(class=["\'][^"\']*)/', '$1 wp-image-' . $post_id, $img_tag);
						} else {
							// If no class exists, add the class attribute with the new class
							$updated_img_tag = preg_replace('/<img([^>]+)>/', '<img$1 class="kb-img wp-image-' . $post_id . '" />', $img_tag);
						}
						
						return $updated_img_tag;
					}, $data);
				}	
			}

			return  [
				'data' => $data,
				'post_id' => $post_id
			]; 
		}

		public static function block_data_instance( array $block_data, array $args = [], $block_args = null ){
			
			if ( $block_data['blockName'] && $block_data['attrs'] ) {
			
				foreach($block_data['attrs'] as $block_key => $block_val) {
					
					if( isset( $block_val['url'] ) && isset( $block_val['id'] ) && !empty( $block_val['url'] ) ){
						$new_media = Tpgb_Import_Images::media_import( $block_val );
						$block_data['attrs'][$block_key] = $new_media;
						if(isset($block_data['innerHTML']) && !empty($block_data['innerHTML'])){
							$block_data['innerHTML'] = self::string_replace_content( $block_data['innerHTML'], $block_val['url'], $new_media['url'] );
						}
						if(isset($block_data['innerContent']) && !empty($block_data['innerContent'])){
							$block_data['innerContent'] = self::string_replace_content( $block_data['innerContent'], $block_val['url'], $new_media['url'] );
						}
					}else if(isset( $block_val['url'] ) && !empty( $block_val['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp|avif)$/', $block_val['url'])) {
						$new_media = Tpgb_Import_Images::media_import( $block_val );
						$block_data['attrs'][$block_key] = $new_media;
						if(isset($block_data['innerHTML']) && !empty($block_data['innerHTML'])){
							$block_data['innerHTML'] = self::string_replace_content( $block_data['innerHTML'], $block_val['url'], $new_media['url'] );
						}
						if(isset($block_data['innerContent']) && !empty($block_data['innerContent'])){
							$block_data['innerContent'] = self::string_replace_content( $block_data['innerContent'], $block_val['url'], $new_media['url'] );
						}
					}else if(is_array($block_val) && !empty($block_val)){
						if( !array_key_exists("md",$block_val) && !array_key_exists("openTypography",$block_val) && !array_key_exists("openBorder",$block_val) && !array_key_exists("openShadow",$block_val) && !array_key_exists("openFilter",$block_val)  ){
							foreach($block_val as $key => $val) {
								if(is_array($val) && !empty($val)){

									if( isset( $val['url'] ) && ( isset( $val['Id'] ) || isset( $val['id'] ) ) && !empty( $val['url'] ) ){
										$new_media = Tpgb_Import_Images::media_import( $val );
										$block_data['attrs'][$block_key][$key] = $new_media;
										if(isset($block_data['innerHTML']) && !empty($block_data['innerHTML'])){
											$block_data['innerHTML'] = self::string_replace_content( $block_data['innerHTML'], $val['url'], $new_media['url'] );
										}
										if(isset($block_data['innerContent']) && !empty($block_data['innerContent'])){
											$block_data['innerContent'] = self::string_replace_content( $block_data['innerContent'], $val['url'], $new_media['url'] );
										}
									}else if( isset( $val['url'] ) && !empty( $val['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp|avif)$/', $val['url']) ) {
										$new_media = Tpgb_Import_Images::media_import( $val );
										$block_data['attrs'][$block_key][$key] = $new_media;
										if(isset($block_data['innerHTML']) && !empty($block_data['innerHTML'])){
											$block_data['innerHTML'] = self::string_replace_content( $block_data['innerHTML'], $val['url'], $new_media['url'] );
										}
										if(isset($block_data['innerContent']) && !empty($block_data['innerContent'])){
											$block_data['innerContent'] = self::string_replace_content( $block_data['innerContent'], $val['url'], $new_media['url'] );
										}
									}else{
										foreach($val as $sub_key => $sub_val) {
											if( isset( $sub_val['url'] ) && ( isset( $sub_val['Id'] ) || isset( $sub_val['id'] ) ) && !empty( $sub_val['url'] ) ){
												$new_media = Tpgb_Import_Images::media_import( $sub_val );
												$block_data['attrs'][$block_key][$key][$sub_key] = $new_media;
												if(isset($block_data['innerHTML']) && !empty($block_data['innerHTML'])){
													$block_data['innerHTML'] = self::string_replace_content( $block_data['innerHTML'], $sub_val['url'], $new_media['url'] );
												}
												if(isset($block_data['innerContent']) && !empty($block_data['innerContent'])){
													$block_data['innerContent'] = self::string_replace_content( $block_data['innerContent'], $sub_val['url'], $new_media['url'] );
												}
											}else if( isset( $sub_val['url'] ) && !empty( $sub_val['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp|avif)$/', $sub_val['url'])) {
												$new_media = Tpgb_Import_Images::media_import( $sub_val );
												$block_data['attrs'][$block_key][$key][$sub_key] = $new_media;
												if(isset($block_data['innerHTML']) && !empty($block_data['innerHTML'])){
													$block_data['innerHTML'] = self::string_replace_content( $block_data['innerHTML'], $sub_val['url'], $new_media['url'] );
												}
												if(isset($block_data['innerContent']) && !empty($block_data['innerContent'])){
													$block_data['innerContent'] = self::string_replace_content( $block_data['innerContent'], $sub_val['url'], $new_media['url'] );
												}
											}else if(is_array($sub_val) && !empty($sub_val)){
												foreach($sub_val as $sub_key1 => $sub_val1) {
													if( isset( $sub_val1['url'] ) && ( isset( $sub_val1['Id'] ) || isset( $sub_val1['id'] ) ) && !empty( $sub_val1['url'] ) ){
														$new_media = Tpgb_Import_Images::media_import( $sub_val1 );
														$block_data['attrs'][$block_key][$key][$sub_key][$sub_key1] = $new_media;
														if(isset($block_data['innerHTML']) && !empty($block_data['innerHTML'])){
															$block_data['innerHTML'] = self::string_replace_content( $block_data['innerHTML'], $sub_val1['url'], $new_media['url'] );
														}
														if(isset($block_data['innerContent']) && !empty($block_data['innerContent'])){
															$block_data['innerContent'] = self::string_replace_content( $block_data['innerContent'], $sub_val1['url'], $new_media['url'] );
														}
													}else if( isset( $sub_val1['url'] ) && !empty( $sub_val1['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp|avif)$/', $sub_val1['url'])) {
														$new_media = Tpgb_Import_Images::media_import( $sub_val1 );
														$block_data['attrs'][$block_key][$key][$sub_key][$sub_key1] = $new_media;
														if(isset($block_data['innerHTML']) && !empty($block_data['innerHTML'])){
															$block_data['innerHTML'] = self::string_replace_content( $block_data['innerHTML'], $sub_val1['url'], $new_media['url'] );
														}
														if(isset($block_data['innerContent']) && !empty($block_data['innerContent'])){
															$block_data['innerContent'] = self::string_replace_content( $block_data['innerContent'], $sub_val1['url'], $new_media['url'] );
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			
			return $block_data;
		}

		public static function array_recursively_data( $data, $callback, $args = [] ) {
			if ( isset( $data['blockName'] ) ) {
				if ( ! empty( $data['innerBlocks'] ) ) {
					$data['innerBlocks'] = self::array_recursively_data( $data['innerBlocks'], $callback, $args );
				}

				return call_user_func( $callback, $data, $args );
			}

			if(gettype($data) !== "array" && gettype($data) !== "object") {
				return $data;
			}

			foreach ( $data as $block_key => $block_value ) {
				$block_data = self::array_recursively_data( $data[ $block_key ], $callback, $args );

				if ( null === $block_data ) {
					continue;
				}

				$data[ $block_key ] = $block_data;
			}

			return $data;
		}

		/**
		 * Elementor Widgets elements data
		 */
		public static function widgets_elements_id_change( $media_import ) {
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
		public static function widgets_import_media_copy_content( $media_import ) {
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
		public static function widgets_element_import_start( \Elementor\Controls_Stack $element ) {
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

}
