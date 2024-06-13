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
			
			return self::array_recursively_data(
				$data_import,
				function( $block_data ) {
					
					$elements = self::block_data_instance( $block_data );
					
					return $elements;
				}
			);
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
					}else if(isset( $block_val['url'] ) && !empty( $block_val['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp)$/', $block_val['url'])) {
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
									}else if( isset( $val['url'] ) && !empty( $val['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp)$/', $val['url']) ) {
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
											}else if( isset( $sub_val['url'] ) && !empty( $sub_val['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp)$/', $sub_val['url'])) {
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
													}else if( isset( $sub_val1['url'] ) && !empty( $sub_val1['url'] ) && preg_match('/\.(jpg|png|jpeg|gif|svg|webp)$/', $sub_val1['url'])) {
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
