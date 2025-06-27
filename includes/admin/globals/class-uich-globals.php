<?php
/**
 * File for handling Globals Operations
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

if ( ! class_exists( 'Uich_Globals' ) ) {

	/**
	 * For handling Global Colors/Typography Operations
	 */
	class Uich_Globals {

        // Modification helpers
        private static function get_all_kit_settings() {
            // Kit
            $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();

            $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );

            $meta_key = $page_settings_manager::META_KEY;

            $document_settings = $kit->get_meta( $meta_key );

            $meta_key = $page_settings_manager::META_KEY;
            $document_settings = $kit->get_meta( $meta_key );

            if ( ! $document_settings ) {
                $document_settings = [];
            }

            $default_settings = [
                'custom_colors' => $kit->get_settings_for_display( 'custom_colors' ),
                'system_colors' => $kit->get_settings_for_display( 'system_colors' ),
                'custom_typography' => $kit->get_settings_for_display( 'custom_typography' ),
                'system_typography' => $kit->get_settings_for_display( 'system_typography' ),
            ];

            $required_keys = [
                'custom_colors',
                'system_colors',
                'custom_typography',
                'system_typography',
            ];

            foreach($required_keys as $key){
                if ( isset( $document_settings[ $key ] ) ) continue;

                $document_settings[ $key ] = !empty($default_settings[$key]) ? $default_settings[$key] : [];
            }

            return $document_settings;
        }

        private static function save_all_kit_settings( $document_settings ) {
            // Kit
            $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();

            $page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );

            // Save to DB
            $page_settings_manager->save_settings( $document_settings, $kit->get_id() );

            // Update all the auto saves as well
            $all_users = get_users([ "ID" ]);
            $autosaves = [];

            foreach($all_users as $user){
                $autosave = $kit->get_autosave($user->ID);
                if( $autosave ){
                    $autosaves[] = $autosave;

                    // Save to DB
                    $page_settings_manager->save_settings( $document_settings, $autosave->get_id() );

                    // Clear Cache & reset CSS
                    Uich_Globals::elementor_refresh_css_and_clear_cache( $autosave->get_id() );
                }
            }

            // error_log(print_r("Updated autosaves: " . count($autosaves), true));

            // Clear Cache & reset CSS
            Uich_Globals::elementor_refresh_css_and_clear_cache( $kit->get_id() );
        }

        public static function get_elementor_container_width(){
            if(!class_exists( '\Elementor\Plugin' )) return false;

            $all_settings = Uich_Globals::get_all_kit_settings();
            $container_width_from_all_settings = array_key_exists('container_width', $all_settings) ? $all_settings['container_width'] : null;

            $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();
            $container_width_kit = $kit->get_settings_for_display('container_width');

            $default_container_width = [
                'unit' => 'px',
                'size' => 1140,
                'sizes' => []
            ];

            if(isset($container_width_from_all_settings)) {
                return $container_width_from_all_settings;
            } else if(isset($container_width_kit)) {
                return $container_width_kit;
            } else {
                return $default_container_width;
            }

        }

        // Lists
        public static function get_typography() {

            if(!class_exists( '\Elementor\Plugin' )) return false;

            $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();

            $system_typography = $kit->get_settings_for_display( 'system_typography' );
            $custom_typography = $kit->get_settings_for_display( 'custom_typography' );

            if ( ! $system_typography ) {
                $system_typography = [];
            }

            if ( ! $custom_typography ) {
                $custom_typography = [];
            }

            $combined_typography = array_merge( $system_typography, $custom_typography );

            $typography_array = [];

            foreach( $combined_typography as $item ){
                $id = $item["_id"];
                $title = $item["title"];

                // $item To be set as `value`
                unset( $item["_id"], $item["title"] );

                // Convert the value to an object if it's an array
                if( is_array($item) ) {
                    $item = (object) $item;
                }

                $typography_array[] = [
                    "id" => $id,
                    "title" => $title,
                    "value" => $item,
                ];
            }

            return $typography_array;
        }

        public static function get_colors() {
            $result = [];
            $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();

            $system_items = $kit->get_settings_for_display( 'system_colors' );
            $custom_items = $kit->get_settings_for_display( 'custom_colors' );

            if ( ! $system_items ) {
                $system_items = [];
            }

            if ( ! $custom_items ) {
                $custom_items = [];
            }

            $items = array_merge( $system_items, $custom_items );

            foreach ( $items as $index => $item ) {
                $id = $item['_id'];
                $result[] = [
                    'id' => $id,
                    'title' => $item['title'],
                    'value' => $item['color'],
                ];
            }

            return $result;
        }

        public static function set_container_width($new_container_width){

            // Get the current container width
            $document_settings = Uich_Globals::get_all_kit_settings();

            // Convert object -> array
            if(is_object($new_container_width)){
                $new_container_width = (array) $new_container_width;
            }

            // Set boxed width
            $document_settings['container_width'] = $new_container_width;

            // Save the settings
            Uich_Globals::save_all_kit_settings($document_settings);

            return $document_settings;
        }

        // Modifiers
        public static function set_or_create_color( $id, $title, $val ) {

            // Random ID: Math.random().toString(16).slice(2, 9)

            $db_item = array(
                '_id' => $id,
                'title' => $title,
                'color' => $val,
            );

            // Get the current set of settings
            $document_settings = Uich_Globals::get_all_kit_settings();

            // Both colors
            $system_colors = &$document_settings['system_colors'];
            $custom_colors = &$document_settings['custom_colors'];

            $found_and_set = false;

            // Check system colors
            foreach($system_colors as &$val){
                if($val['_id'] !== $id) continue;

                $val = $db_item;

                // Set the color & break
                // error_log( 'found a pre-existing system color' );

                // Set the flag to disallow further modification
                $found_and_set = true;

                break;
            }

            // Check system colors
            foreach($custom_colors as &$val){
                if($val['_id'] !== $id) continue;

                $val = $db_item;

                // error_log( 'found a pre-existing custom color' );

                // Set the flag to disallow further modification
                $found_and_set = true;

                break;
            }

            // Create a new color if not already found
            if( !$found_and_set ){
                $custom_colors[] = $db_item;
            }

            // Save the settings
            Uich_Globals::save_all_kit_settings($document_settings);

            return $document_settings;
        }

        public static function set_or_create_typography( $id, $title, $value ) {

            $db_item = array(
                '_id' => $id,
                'title' => $title,
                'value' => $value,
            );

            $valueObject = $db_item['value'];
            unset( $db_item['value'] );

            foreach( $valueObject as $key => $value ){
                if ( is_object($value) ) {
                    $db_item[$key] = (array) $value;
                } else {
                    $db_item[$key] = $value;
                }
            }

            // Get the current set of settings
            $document_settings = Uich_Globals::get_all_kit_settings();

            // Both typography
            $system_typography = &$document_settings['system_typography'];
            $custom_typography = &$document_settings['custom_typography'];
            $found_and_set = false;

            // var_dump($system_typography);

            // Check system typography
            foreach( $system_typography as &$val ){
                if( $val['_id'] !== $id ) continue;
                
                $val = $db_item;

                // Set the typography & break
                // error_log( 'found a pre-existing system typography' );

                // Set the flag to disallow further modification
                $found_and_set = true;

                break;
            }

            // Check system typography
            foreach( $custom_typography as &$val ){
                if( $val['_id'] !== $id ) continue;

                $val = $db_item;

                // error_log( 'found a pre-existing custom typography' );

                // Set the flag to disallow further modification
                $found_and_set = true;

                break;
            }

            // Create a new typography if not already found
            if( !$found_and_set ){
                $custom_typography[] = $db_item;
            }

            // Save the settings
            Uich_Globals::save_all_kit_settings( $document_settings );

            return $document_settings;

        }

        public static function delete_global_color( $id ) {

            // Get the current set of settings
            $document_settings = Uich_Globals::get_all_kit_settings();

            // colors
            $custom_colors = &$document_settings['custom_colors'];

            $match = null;
            foreach($custom_colors as $key => $val) {
                if($val['_id'] !== $id) continue;

                $match = $key;
                break;
            }

            if( null === $match ) return null;

            // Remove
            array_splice($custom_colors, $match, 1);

            // Save
            Uich_Globals::save_all_kit_settings($document_settings);
        }

        public static function delete_global_typography( $id ) {

            // Get the current set of settings
            $document_settings = Uich_Globals::get_all_kit_settings();

            // colors
            $custom_typography = &$document_settings['custom_typography'];

            $match = null;
            foreach( $custom_typography as $key => $val ){
                if( $val['_id'] !== $id ) continue;

                $match = $key;
                break;
            }

            if( null === $match ) return null;

            // Remove
            array_splice($custom_typography, $match, 1);

            // Save
            return Uich_Globals::save_all_kit_settings($document_settings);
        }

        private static function elementor_refresh_css_and_clear_cache( $id ){
            // Remove Post CSS.
            $post_css = \Elementor\Core\Files\CSS\Post::create( $id );

            $post_css->delete();

            // Refresh Cache.
            \Elementor\Plugin::$instance->documents->get( $id, false );

            $post_css = \Elementor\Core\Files\CSS\Post::create( $id );

            $post_css->enqueue();
        }


        // End Points
        public static function get_globals() {
            return array(
                'success' => true,
                'typography' => Uich_Globals::get_typography(),
				'colors' => Uich_Globals::get_colors(),
                'container_width' => Uich_Globals::get_elementor_container_width(),
            );
        }

        public static function sync_globals( $sync_data ) {

            $sync_color = $sync_data->colors;
            $sync_typography = $sync_data->typography;
            $sync_container_width = $sync_data->container_width;

            // set container width
            if(isset($sync_container_width)){
                Uich_Globals::set_container_width($sync_container_width);
            }

            // apply color changes
            foreach( $sync_color as $color ){

                $action = $color->action;
                $color_data = $color->value;

                if( $action === "DEL" ){
                    Uich_Globals::delete_global_color( $color_data->id );
                }

                if( $action === "ADD" || $action === "SET" ){
                    Uich_Globals::set_or_create_color( $color_data->id, $color_data->title, $color_data->value );
                }
            }

            // apply typography changes
            foreach( $sync_typography as $typography ){              
                
                $action = $typography->action;
                $typography_data = $typography->value;

                if( $action === "DEL" ){
                    Uich_Globals::delete_global_typography( $typography_data->id );
                }
                
                if( $action === "ADD" || $action === "SET" ){
                    Uich_Globals::set_or_create_typography( $typography_data->id, $typography_data->title, $typography_data->value );
                }
            }

            // Return the saved -> updated data
            return Uich_Globals::get_globals();
        }

    }
}
