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

            $required_keys = [
                'custom_colors',
                'system_colors',
                'custom_typography',
                'system_typography',
            ];

            foreach($required_keys as $key){
                if ( isset( $document_settings[ $key ] ) ) continue;

                $document_settings[ $key ] = [];
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

    class Uich_Bricks_Globals {

	    const TYPO_CLASS_CATEGORY_ID = 'UICHEMY_TYPO';
	    const PADDING_CLASS_CATEGORY_ID = 'UICHEMY_PADDING';
        const DEFAULT_CONTAINER_WIDTH = '1100px';
        const UICHEMY_THEME_NAME = 'Uichemy Theme';
        const UICHEMY_PALETTE_NAME = 'Uichemy Palette';

        // Helper method to safely retrieve and ensure an option is an array.
        private static function getOptionAsArray($optionName){
            $option = get_option($optionName, []);
            return is_array($option) ? $option : [];
        }

        // Check if a category exists in the class categories.
        private static function categoryExists($categories,  $categoryId){
            foreach($categories as $category){
                if(($category['id'] ?? '') === $categoryId){
                    return true;
                }
            }
            return false;
        }

        // Initialize and retrieve the Uichemy container width.
        public static function initContainerWidth(){
            $theme_styles_array = Uich_Bricks_Globals::getOptionAsArray('bricks_theme_styles');

            // Check for existing Uichemy Theme
            foreach($theme_styles_array as $key => $style){
                if(isset($style['label']) && $style['label'] === Uich_Bricks_Globals::UICHEMY_THEME_NAME){
                    $width = sanitize_text_field($style['settings']['container']['width'] ?? Uich_Bricks_Globals::DEFAULT_CONTAINER_WIDTH);

                    // Reindex: Remove the current theme and append it to the end
                    $uichemy_theme_style = $style;
                    unset($theme_styles_array[$key]);
                    $theme_styles_array['Uichemy Theme'] = $uichemy_theme_style;
                    update_option('bricks_theme_styles', $theme_styles_array);

                    return (object)[
                        'width' => $width,
                        'theme' => 'Uichemy Theme',
                    ];
                }
            }

            // Check for theme with 'any' condition
            foreach(array_reverse($theme_styles_array, true) as $key => $style){
                if(isset($style['settings']['conditions']['conditions'][0]['main'])
                    && $style['settings']['conditions']['conditions'][0]['main'] === 'any'
                ){
                    $width = sanitize_text_field($style['settings']['container']['width'] ?? Uich_Bricks_Globals::DEFAULT_CONTAINER_WIDTH);

                    return (object)[
                        'width' => $width,
                        'theme' => $style['label'] ?? '',
                    ];
                }
            }

            // Create new Uichemy Theme if none found
            $themeStyles[Uich_Bricks_Globals::UICHEMY_THEME_NAME] = [
                'label' => Uich_Bricks_Globals::UICHEMY_THEME_NAME,
                'settings' => [
                    '_custom' => true,
                    'conditions' => [
                        'conditions' => [
                            [
                                'id' => 'uichem',
                                'main' => 'any',
                            ],
                        ],
                    ],
                    'container' => [
                        'width' => Uich_Bricks_Globals::DEFAULT_CONTAINER_WIDTH,
                    ],
                ],
            ];
            update_option('bricks_theme_styles', $themeStyles);

            return (object)[
                'width' => Uich_Bricks_Globals::DEFAULT_CONTAINER_WIDTH,
                'theme' => Uich_Bricks_Globals::UICHEMY_THEME_NAME,
            ];
        }

        // Retrieve or create the Uichemy color palette.
        public static function get_uich_color_palette(){
            $color_palettes = self::getOptionAsArray('bricks_color_palette');

            // Search for the Uichemy palette (case-insensitive for robustness)
            foreach($color_palettes as $palette){
                if(isset( $palette['name']) && isset($palette['colors']) && strcasecmp($palette['name'], Uich_Bricks_Globals::UICHEMY_PALETTE_NAME ) === 0){
                    return $palette['colors'];
                }
            }

            // Create new Uichemy palette
            $uichemy_palette = [
                'id'     => 'uichemy_palette_' . uniqid(), // Unique ID to avoid conflicts
                'name'   => Uich_Bricks_Globals::UICHEMY_PALETTE_NAME,
                'colors' => [],
            ];

            $color_palettes[] = $uichemy_palette;
            update_option('bricks_color_palette', $color_palettes);

            return $uichemy_palette['colors'];
        }

        // Get the global container width.
        public static function get_global_container_width(){
            return Uich_Bricks_Globals::initContainerWidth()->width;
        }

        // Retrieve Uichemy typography classes.
        public static function get_uich_typography_classes(){
            $uichemy_category_id = Uich_Bricks_Globals::TYPO_CLASS_CATEGORY_ID;
            $global_classes = Uich_Bricks_Globals::getOptionAsArray('bricks_global_classes');
            $class_categories = Uich_Bricks_Globals::getOptionAsArray('bricks_global_classes_categories');

            // Ensure Uichemy typography category exists
            if(!Uich_Bricks_Globals::categoryExists($class_categories, $uichemy_category_id)){
                $class_categories[] = [
                    'id' => $uichemy_category_id,
                    'name' => 'Uichemy Typography',
                ];
                update_option('bricks_global_classes_categories', $class_categories);
            }

            $typography_classes = [];
            foreach($global_classes as $class){
                if(isset($class['category']) && $class['category'] === $uichemy_category_id && isset($class['settings'])){
                    $typography_classes[] = [
                        'id'   => sanitize_text_field($class['id'] ?? ''),
                        'name' => sanitize_text_field($class['name'] ?? ''),
                        'typography' => [
                           'desktop' => $class['settings']['_typography'] ?? null,
                            'tablet' => $class['settings']['_typography:tablet_portrait'] ?? null,
                            'mobile_landscape' => $class['settings']['_typography:mobile_landscape'] ?? null,
                            'mobile' => $class['settings']['_typography:mobile_portrait'] ?? null,
                        ],
                    ];
                }
            }

            return $typography_classes;
        }

        // Retrieve Uichemy padding classes.
        public static function get_uich_padding_classes(){
            $uichemy_category_id = Uich_Bricks_Globals::PADDING_CLASS_CATEGORY_ID;
            $global_classes = Uich_Bricks_Globals::getOptionAsArray('bricks_global_classes');
            $class_categories = Uich_Bricks_Globals::getOptionAsArray('bricks_global_classes_categories');

            // Ensure Uichemy padding category exists
            if(!Uich_Bricks_Globals::categoryExists($class_categories, $uichemy_category_id)){
                $class_categories[] = [
                    'id' => $uichemy_category_id,
                    'name' => 'Uichemy Padding',
                ];
                update_option('bricks_global_classes_categories', $class_categories);
            }
       
            // Collect padding classes for the Uichemy category
            $padding_classes = [];
            foreach($global_classes as $class){
                if(isset($class['category']) && $class['category'] === $uichemy_category_id && isset($class['settings'])){
                    $padding_classes[] = [
                        'id'   => sanitize_text_field($class['id'] ?? ''),
                        'name' => sanitize_text_field($class['name'] ?? ''),
                        'padding' => [
                            'desktop' => $class['settings']['_padding'] ?? null,
                            'tablet' => $class['settings']['_padding:tablet_portrait'] ?? null,
                            'mobile_landscape' => $class['settings']['_padding:mobile_landscape'] ?? null,
                            'mobile' => $class['settings']['_padding:mobile_portrait'] ?? null,
                        ],
                    ];
                }
            }

            return $padding_classes;
        }

        // Set the Uichemy container width.
        public static function set_uich_container_width( $container_width ){
            $container_width = sanitize_text_field( $container_width );
            $theme_name = Uich_Bricks_Globals::initContainerWidth()->theme;
            $theme_styles_array = Uich_Bricks_Globals::getOptionAsArray('bricks_theme_styles');

            // For initialize and retrieve the Uichemy container width.
            Uich_Bricks_Globals::get_global_container_width();

            if(empty($theme_styles_array)){
                return false;
            }

            $theme_styles_array[$theme_name]['settings']['container']['width'] = $container_width;
            return update_option('bricks_theme_styles', $theme_styles_array);
        }

        // Sync the Uichemy color palette with updates.
        public static function sync_uich_color_palette( $color_updates ){
            $color_palettes = Uich_Bricks_Globals::getOptionAsArray('bricks_color_palette');
            $uichemy_palette = Uich_Bricks_Globals::get_uich_color_palette();
            $palette_key = null;

            foreach($color_palettes as $key => $palette){
                if(isset($palette['name']) && strcasecmp($palette['name'], Uich_Bricks_Globals::UICHEMY_PALETTE_NAME) === 0){

                    $palette_key = $key;
                    break;
                }
            }

            if($palette_key === null){
                $color_palettes[] = $uichemy_palette;
                $palette_key = array_key_last($color_palettes);
            }

            $color_palettes[$palette_key]['colors'] = $color_palettes[ $palette_key ]['colors'] ?? [];

            foreach($color_updates as $update){
                if(!is_object($update) || !isset($update->action, $update->id)){
                    continue;
                }

                $action = sanitize_text_field($update->action);
                $id = sanitize_text_field($update->id);

                if($action === 'DEL'){
                    $color_palettes[$palette_key]['colors'] = array_values(array_filter(
                        $color_palettes[$palette_key]['colors'],
                        fn($color) => ! isset($color['id']) || $color['id'] !== $id
                    ) );
                } else if($action === 'SET' || $action === 'ADD'){
                    if(!isset($update->hex) || !preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $update->hex)){
                        continue;
                    }

                    $hex = sanitize_hex_color($update->hex);
                    $name = isset($update->name) ? sanitize_text_field($update->name) : '';

                    $found = false;
                    foreach($color_palettes[$palette_key]['colors'] as &$color){
                        if(isset($color['id']) && $color['id'] === $id){
                            $color['hex'] = $hex;
                            $color['name'] = $name;
                            $found = true;
                            break;
                        }
                    }
                    unset($color);

                    if(!$found){
                        $color_palettes[$palette_key]['colors'][] = [
                            'id'   => $id,
                            'hex'  => $hex,
                            'name' => $name,
                        ];
                    }
                }
            }

            return update_option('bricks_color_palette', $color_palettes);

        }

        // Sync Uichemy typography classes with updates.
        public static function sync_uich_typography_classes($typography_updates){
            Uich_Bricks_Globals::get_uich_typography_classes();
            $uichemy_category_id = Uich_Bricks_Globals::TYPO_CLASS_CATEGORY_ID;
            $global_classes = Uich_Bricks_Globals::getOptionAsArray('bricks_global_classes');

            foreach($typography_updates as $update){

                if(!is_object($update) || !isset($update->action, $update->id)){
                    continue;
                }
                
                $action = sanitize_text_field($update->action);
                $id = sanitize_text_field($update->id);


                if($action === 'DEL'){
                    $global_classes = array_values(array_filter(
                        $global_classes,
                        fn($class) => !isset($class['id'], $class['category']) || $class['id'] !== $id || $class['category'] !== $uichemy_category_id
                    ) );
                } elseif($action === 'SET' || $action === 'ADD'){
                    if(!isset($update->typography) || !is_object($update->typography)){
                        continue;
                    }

                    // Sanitize name and typography settings
                    $name = sanitize_text_field($update->name ?? '');
                    $desktop = $update->typography->desktop ?? null;
                    $tablet = $update->typography->tablet ?? null;
                    $mobile = $update->typography->mobile ?? null;
                    $mobile_landscape = $update->typography->mobile_landscape ?? null;

                    $found = false;
                    foreach($global_classes as &$class){
                        if(isset($class['id'], $class['category']) && $class['id'] === $id && $class['category'] === $uichemy_category_id){
                            
                            // Update existing class
                            $class['settings'] = array_merge($class['settings'] ?? [], [
                                '_typography'                   => $desktop,
                                '_typography:tablet_portrait'   => $tablet,
                                '_typography:mobile_portrait'   => $mobile,
                                '_typography:mobile_landscape'  => $mobile_landscape,
                            ]);
                            $class['name'] = $name;
                            $found = true;
                            break;
                        }
                    }
                    unset($class);

                    if(!$found){
                        $global_classes[] = [
                            'id'       => $id,
                            'category' => $uichemy_category_id,
                            'settings' => [
                                '_typography'                   => $desktop,
                                '_typography:tablet_portrait'   => $tablet,
                                '_typography:mobile_portrait'   => $mobile,
                                '_typography:mobile_landscape'  => $mobile_landscape,
                            ],
                            'name'     => $name,
                        ];
                    }
                }
            }

            return update_option('bricks_global_classes', $global_classes);
        }

        public static function sync_uich_padding_classes($padding_updates){
            Uich_Bricks_Globals::get_uich_padding_classes();
            $uichemy_category_id = Uich_Bricks_Globals::PADDING_CLASS_CATEGORY_ID;
            $global_classes = Uich_Bricks_Globals::getOptionAsArray('bricks_global_classes');

            foreach($padding_updates as $update){
                if(!is_object($update) || !isset($update->action, $update->id)){
                    continue;
                }

                $action = sanitize_text_field($update->action);
                $id = sanitize_text_field($update->id);

                if($action === 'DEL'){
                    $global_classes = array_values(array_filter(
                        $global_classes,
                        fn($class) => !isset($class['id'], $class['category']) || $class['id'] !== $id || $class['category'] !== $uichemy_category_id
                    ) );
                } elseif($action === 'SET' || $action === 'ADD'){
                    if(!isset($update->padding) || !is_object($update->padding)){
                        continue;
                    }

                    // Sanitize name and padding settings
                    $name = sanitize_text_field($update->name ?? '');
                    $desktop = $update->padding->desktop ?? null;
                    $tablet = $update->padding->tablet ?? null;
                    $mobile = $update->padding->mobile ?? null;
                    $mobile_landscape = $update->padding->mobile_landscape ?? null;

                    $found = false;
                    foreach($global_classes as &$class){
                        if(isset($class['id'], $class['category']) && $class['id'] === $id && $class['category'] === $uichemy_category_id){
                            // Update existing class
                            $class['settings'] = array_merge($class['settings'] ?? [], [
                                '_padding'                   => $desktop,
                                '_padding:tablet_portrait'   => $tablet,
                                '_padding:mobile_portrait'   => $mobile,
                                '_padding:mobile_landscape'  => $mobile_landscape,
                            ]);
                            $class['name'] = $name;
                            $found = true;
                            break;
                        }
                    }
                    unset($class);

                    if(!$found){
                        $global_classes[] = [
                            'id'       => $id,
                            'category' => $uichemy_category_id,
                            'settings' => [
                                '_padding'                   => $desktop,
                                '_padding:tablet_portrait'   => $tablet,
                                '_padding:mobile_portrait'   => $mobile,
                                '_padding:mobile_landscape'  => $mobile_landscape,
                            ],
                            'name'     => $name,
                        ];
                    }
                }
            }

            return update_option('bricks_global_classes', $global_classes);
        }

        // Sync all Uichemy globals (width, colors, typography, padding).
        public static function sync_uich_globals($global_data){

            if(isset($global_data->width)){
                Uich_Bricks_Globals::set_uich_container_width($global_data->width);
            }
    
            if(!empty($global_data->colors) && is_array($global_data->colors)){
                Uich_Bricks_Globals::sync_uich_color_palette($global_data->colors);
            }
    
            if(!empty($global_data->typography) && is_array($global_data->typography)){
                Uich_Bricks_Globals::sync_uich_typography_classes($global_data->typography);
            }
    
            if(!empty($global_data->padding) && is_array($global_data->padding)){
                Uich_Bricks_Globals::sync_uich_padding_classes($global_data->padding);
            }

            return array(
                'width' => Uich_Bricks_Globals::initContainerWidth()->width,
                'colors' => Uich_Bricks_Globals::get_uich_color_palette(),
                'typography' => Uich_Bricks_Globals::get_uich_typography_classes(),
                'padding' => Uich_Bricks_Globals::get_uich_padding_classes()
            );
        }
    }
}
