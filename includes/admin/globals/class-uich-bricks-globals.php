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

if ( ! class_exists( 'Uich_Bricks_Globals' ) ) {

    /**
	 * For handling Global Colors/Typography Operations for bricks-builder
	 */
    class Uich_Bricks_Globals {

	    const TYPO_CLASS_CATEGORY_ID = 'UICH_TYPO';
	    const PADDING_CLASS_CATEGORY_ID = 'UICH_PADDING';
        const DEFAULT_CONTAINER_WIDTH = '1100px';
        const DEFAULT_CONTAINER_TABLET_WIDTH = '85%';
        const DEFAULT_CONTAINER_MOBILE_WIDTH = '90%';
        const UICH_THEME_ID = 'uichemy_theme';
        const UICH_PALETTE_NAME = 'UiChemy Palette';

        // Helper method to safely retrieve and ensure an option is an array.
        private static function get_option_as_array($optionName){
            $option = get_option($optionName, []);
            return is_array($option) ? $option : [];
        }

        public static function object_to_array($obj){
            if(is_object($obj) || is_array($obj)){
                $ret = (array)$obj;
                foreach ($ret as &$item) {
                    $item = Uich_Bricks_Globals::object_to_array($item);
                }
                return $ret;
            }
            return $obj;
        }

        // Check if a category exists in the class categories.
        private static function category_exists($categories,  $categoryId){
            foreach($categories as $category){
                if(($category['id'] ?? '') === $categoryId){
                    return true;
                }
            }
            return false;
        }

        // Initialize and retrieve the Uichemy container width.
        public static function get_or_create_container_width(){
            $theme_styles_array = Uich_Bricks_Globals::get_option_as_array('bricks_theme_styles');

            // Check for existing Uichemy Theme
            foreach($theme_styles_array as $key => $style){
                if($key === Uich_Bricks_Globals::UICH_THEME_ID){
                    $desktop = sanitize_text_field($style['settings']['container']['width'] ?? Uich_Bricks_Globals::DEFAULT_CONTAINER_WIDTH);
                    $tablet_portrait = isset($style['settings']['container']['width:tablet_portrait']) ? sanitize_text_field($style['settings']['container']['width:tablet_portrait']) : null;
                    $mobile_landscape = isset($style['settings']['container']['width:mobile_landscape']) ? sanitize_text_field($style['settings']['container']['width:mobile_landscape']) : null;
                    $mobile_portrait = isset($style['settings']['container']['width:mobile_portrait']) ? sanitize_text_field($style['settings']['container']['width:mobile_portrait']) : null;

                    // Reindex: Remove the current theme and append it to the end
                    $uichemy_theme_style = $style;
                    unset($theme_styles_array[$key]);
                    $theme_styles_array[$key] = $uichemy_theme_style;

                    update_option('bricks_theme_styles', $theme_styles_array);

                    $width_data = ['desktop' => $desktop];

                    if($tablet_portrait !== null){
                        $width_data['tablet_portrait'] = $tablet_portrait;
                    }
                    if($mobile_landscape !== null){
                        $width_data['mobile_landscape'] = $mobile_landscape;
                    }
                    if($mobile_portrait !== null){
                        $width_data['mobile_portrait'] = $mobile_portrait;
                    }
            

                    return (object)[
                        'width' => (object)$width_data,
                        'themeID' => $key,
                    ];
                }
            }

            // Check for theme with 'any' condition
            foreach(array_reverse($theme_styles_array, true) as $key => $style){
                if(isset($style['settings'])
                    && isset($style['settings']['conditions'])
                    && isset($style['settings']['conditions']['conditions'])
                    && isset($style['settings']['conditions']['conditions'][0])
                    && isset($style['settings']['conditions']['conditions'][0]['main'])
                    && $style['settings']['conditions']['conditions'][0]['main'] === 'any'
                ){
                    $desktop = sanitize_text_field($style['settings']['container']['width'] ?? Uich_Bricks_Globals::DEFAULT_CONTAINER_WIDTH);
                    $tablet_portrait = isset($style['settings']['container']['width:tablet_portrait']) ? sanitize_text_field($style['settings']['container']['width:tablet_portrait']) : null;
                    $mobile_landscape = isset($style['settings']['container']['width:mobile_landscape']) ? sanitize_text_field($style['settings']['container']['width:mobile_landscape']) : null;
                    $mobile_portrait = isset($style['settings']['container']['width:mobile_portrait']) ? sanitize_text_field($style['settings']['container']['width:mobile_portrait']) : null;

                    $width_data = ['desktop' => $desktop];

                    if($tablet_portrait !== null){
                        $width_data['tablet_portrait'] = $tablet_portrait;
                    }
                    if($mobile_landscape !== null){
                        $width_data['mobile_landscape'] = $mobile_landscape;
                    }
                    if($mobile_portrait !== null){
                        $width_data['mobile_portrait'] = $mobile_portrait;
                    }

                    return (object)[
                        'width' => (object)$width_data,
                        'themeID' => $key ?? '',
                    ];
                }
            }

            // Create new Uichemy Theme if none found
            $themeStyles[Uich_Bricks_Globals::UICH_THEME_ID] = [
                'label' => 'UICHEMY THEME',
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
                        'width:tablet_portrait' => Uich_Bricks_Globals::DEFAULT_CONTAINER_TABLET_WIDTH,
                        'width:mobile_portrait' => Uich_Bricks_Globals::DEFAULT_CONTAINER_MOBILE_WIDTH,
                    ],
                ],
            ];

            update_option('bricks_theme_styles', $themeStyles);

            return (object)[
                'width' => [
                    'desktop' => Uich_Bricks_Globals::DEFAULT_CONTAINER_WIDTH,
                    'tablet_portrait' => Uich_Bricks_Globals::DEFAULT_CONTAINER_TABLET_WIDTH,
                    'mobile_portrait' => Uich_Bricks_Globals::DEFAULT_CONTAINER_MOBILE_WIDTH,
                ],
                'themeID' => Uich_Bricks_Globals::UICH_THEME_ID,
            ];
        }

        // Retrieve or create the Uichemy color palette.
        public static function get_or_create_uich_color_palette(){
            $color_palettes = self::get_option_as_array('bricks_color_palette');

            // Search for the Uichemy palette (case-insensitive for robustness)
            foreach($color_palettes as $palette){
                if(isset( $palette['name']) && isset($palette['colors']) && strcasecmp($palette['name'], Uich_Bricks_Globals::UICH_PALETTE_NAME ) === 0){
                    return $palette['colors'];
                }
            }

            // Create new Uichemy palette
            $uichemy_palette = [
                'id'     => 'uichemy_palette_' . uniqid(), // Unique ID to avoid conflicts
                'name'   => Uich_Bricks_Globals::UICH_PALETTE_NAME,
                'colors' => [],
            ];

            $color_palettes[] = $uichemy_palette;
            update_option('bricks_color_palette', $color_palettes);

            return $uichemy_palette['colors'];
        }

        // Retrieve Uichemy typography classes.
        public static function get_uich_typography_classes(){
            $uichemy_category_id = Uich_Bricks_Globals::TYPO_CLASS_CATEGORY_ID;
            $global_classes = Uich_Bricks_Globals::get_option_as_array('bricks_global_classes');
            $class_categories = Uich_Bricks_Globals::get_option_as_array('bricks_global_classes_categories');

            // Ensure Uichemy typography category exists
            if(!Uich_Bricks_Globals::category_exists($class_categories, $uichemy_category_id)){
                $class_categories[] = [
                    'id' => $uichemy_category_id,
                    'name' => 'Uichemy Typography',
                ];
                update_option('bricks_global_classes_categories', $class_categories);
            }

            $typography_classes = [];
            foreach($global_classes as $class){
                if(isset($class['category']) && $class['category'] === $uichemy_category_id && isset($class['settings'])){
                    $value = [];

                    if(isset($class['settings']['_typography'])){
                        $value['_typography'] = $class['settings']['_typography'];
                    }
                    if(isset($class['settings']['_typography:tablet_portrait'])){
                        $value['_typography:tablet_portrait'] = $class['settings']['_typography:tablet_portrait'];
                    }
                    if(isset($class['settings']['_typography:mobile_portrait'])){
                        $value['_typography:mobile_portrait'] = $class['settings']['_typography:mobile_portrait'];
                    }

                    $typography_classes[] = [
                        'id'   => sanitize_text_field($class['id'] ?? ''),
                        'name' => sanitize_text_field($class['name'] ?? ''),
                        'value' => $value,
                    ];
                }
            }

            return $typography_classes;
        }

        // Retrieve Uichemy padding classes.
        public static function get_uich_padding_classes(){
            $uichemy_category_id = Uich_Bricks_Globals::PADDING_CLASS_CATEGORY_ID;
            $global_classes = Uich_Bricks_Globals::get_option_as_array('bricks_global_classes');
            $class_categories = Uich_Bricks_Globals::get_option_as_array('bricks_global_classes_categories');

            // Ensure Uichemy padding category exists
            if(!Uich_Bricks_Globals::category_exists($class_categories, $uichemy_category_id)){
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
                    $value = [];

                    if(isset($class['settings']['_padding'])){
                        $value['_padding'] = $class['settings']['_padding'];
                    }
                    if(isset($class['settings']['_padding:tablet_portrait'])){
                        $value['_padding:tablet_portrait'] = $class['settings']['_padding:tablet_portrait'];
                    }
                    if(isset($class['settings']['_padding:mobile_portrait'])){
                        $value['_padding:mobile_portrait'] = $class['settings']['_padding:mobile_portrait'];
                    }

                    $padding_classes[] = [
                        'id'   => sanitize_text_field($class['id'] ?? ''),
                        'name' => sanitize_text_field($class['name'] ?? ''),
                        'value' => $value,
                    ];
                }
            }

            return $padding_classes;
        }

        // Set the Uichemy container width.
        public static function set_uich_container_width( $container_width ){
            $theme_id = Uich_Bricks_Globals::get_or_create_container_width()->themeID;
            $theme_styles_array = Uich_Bricks_Globals::get_option_as_array('bricks_theme_styles');

            if(empty($theme_styles_array)){
                return false;
            }

            // Set desktop width (required)
            $theme_styles_array[$theme_id]['settings']['container']['width'] = sanitize_text_field($container_width->desktop);

            // Handle tablet_portrait
            if(isset($container_width->tablet_portrait) && !empty($container_width->tablet_portrait)){
                $theme_styles_array[$theme_id]['settings']['container']['width:tablet_portrait'] = sanitize_text_field($container_width->tablet_portrait);
            } else {
                unset($theme_styles_array[$theme_id]['settings']['container']['width:tablet_portrait']);
            }

            // Handle mobile_landscape
            if(isset($container_width->mobile_landscape) && !empty($container_width->mobile_landscape)){
                $theme_styles_array[$theme_id]['settings']['container']['width:mobile_landscape'] = sanitize_text_field($container_width->mobile_landscape);
            } else {
                unset($theme_styles_array[$theme_id]['settings']['container']['width:mobile_landscape']);
            }

            // Handle mobile_portrait
            if(isset($container_width->mobile_portrait) && !empty($container_width->mobile_portrait)){
                $theme_styles_array[$theme_id]['settings']['container']['width:mobile_portrait'] = sanitize_text_field($container_width->mobile_portrait);
            } else {
                unset($theme_styles_array[$theme_id]['settings']['container']['width:mobile_portrait']);
            }

            return update_option('bricks_theme_styles', $theme_styles_array);
        }

        // Sync the Uichemy color palette with updates.
        public static function sync_uich_color_palette( $color_updates ){
            $uichemy_palette = Uich_Bricks_Globals::get_or_create_uich_color_palette();
            $color_palettes = Uich_Bricks_Globals::get_option_as_array('bricks_color_palette');
            $palette_key = null;

            function extractHexCode($hex) {
                // Remove any whitespace
                $hex = trim($hex);

                // Check if hex code is 9 characters (# + 8 digits for RGBA)
                if(strlen($hex) === 9){
                    // Return only first 7 characters (# + 6 digits)
                    return sanitize_hex_color(substr($hex, 0, 7));
                }

                // Return original hex if not RGBA
                return sanitize_hex_color($hex);
            }

            foreach($color_palettes as $key => $palette){
                if(isset($palette['name']) && strcasecmp($palette['name'], Uich_Bricks_Globals::UICH_PALETTE_NAME) === 0){

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
                    ));
                } else if($action === 'SET' || $action === 'ADD'){

                    if(!isset($update->hex)){
                        continue;
                    }

                    $hex = extractHexCode($update->hex);
                    $name = isset($update->name) ? sanitize_text_field($update->name) : '';
                    $rgb = isset($update->rgb) ? $update->rgb : '';

                    $found = false;
                    foreach($color_palettes[$palette_key]['colors'] as &$color){
                        if(isset($color['id']) && $color['id'] === $id){
                            $color['hex'] = $hex;
                            $color['name'] = $name;
                            if(!empty($rgb)){
                                $color['rgb'] = $rgb;
                            }
                            $found = true;
                            break;
                        }
                    }

                    if(!$found){
                        $new_color = [
                            'id'   => $id,
                            'hex'  => $hex,
                            'name' => $name,
                        ];
                        if(!empty($rgb)){
                            $new_color['rgb'] = $rgb;
                        }
                        $color_palettes[$palette_key]['colors'][] = $new_color;
                    }
                }
            }

            return update_option('bricks_color_palette', $color_palettes);

        }

        // Sync Uichemy typography classes with updates.
        public static function sync_uich_typography_classes($typography_updates){
            Uich_Bricks_Globals::get_uich_typography_classes();
            $uichemy_category_id = Uich_Bricks_Globals::TYPO_CLASS_CATEGORY_ID;
            $global_classes = Uich_Bricks_Globals::get_option_as_array('bricks_global_classes');

            $convertIntoArray = Uich_Bricks_Globals::object_to_array($typography_updates);

            foreach($convertIntoArray as $update){

                if(!isset($update['action'], $update['value']['id'])){
                    continue;
                }

                $action = sanitize_text_field($update['action']);
                $id = sanitize_text_field($update['value']['id']);

                if($action === 'DEL'){
                    $global_classes = array_values(array_filter(
                        $global_classes,
                        fn($class) => !isset($class['id'], $class['category']) || $class['id'] !== $id || $class['category'] !== $uichemy_category_id
                    ));
                } else if($action === 'SET' || $action === 'ADD'){
                    if(!isset($update['value']['value'])){
                        continue;
                    }

                    // Sanitize name and typography settings
                    $typography = $update['value']['value'];
                    $name = sanitize_text_field($update['value']['name'] ?? '');

                    $found = false;
                    foreach($global_classes as &$class){
                        if(isset($class['id'], $class['category']) && $class['id'] === $id && $class['category'] === $uichemy_category_id){
                            // Update existing class
                            $class['settings'] = $typography;
                            $class['name'] = $name;
                            $found = true;
                            break;
                        }
                    }

                    if(!$found){
                        $global_classes[] = [
                            'id'       => $id,
                            'category' => $uichemy_category_id,
                            'settings' => $typography,
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
            $global_classes = Uich_Bricks_Globals::get_option_as_array('bricks_global_classes');

            $convertIntoArray = Uich_Bricks_Globals::object_to_array($padding_updates);

            foreach($convertIntoArray as $update){
                if(!isset($update['action'], $update['value']['id'])){
                    continue;
                }

                $action = sanitize_text_field($update['action']);
                $id = sanitize_text_field($update['value']['id']);

                if($action === 'DEL'){
                    $global_classes = array_values(array_filter(
                        $global_classes,
                        fn($class) => !isset($class['id'], $class['category']) || $class['id'] !== $id || $class['category'] !== $uichemy_category_id
                    ));
                } else if($action === 'SET' || $action === 'ADD'){
                    if(!isset($update['value']['value'])){
                        continue;
                    }

                    // Sanitize name and typography settings
                    $padding = $update['value']['value'];
                    $name = sanitize_text_field($update['value']['name'] ?? '');

                    $found = false;
                    foreach($global_classes as &$class){
                        if(isset($class['id'], $class['category']) && $class['id'] === $id && $class['category'] === $uichemy_category_id){
                            // Update existing class
                            $class['settings'] = $padding;
                            $class['name'] = $name;
                            $found = true;
                            break;
                        }
                    }

                    if(!$found){
                        $global_classes[] = [
                            'id'       => $id,
                            'category' => $uichemy_category_id,
                            'settings' => $padding,
                            'name'     => $name,
                        ];
                    }
                }
            }

            return update_option('bricks_global_classes', $global_classes);
        }

        // Get current active globals
        public static function get_uich_bricks_globals(){
            return array(
                'width' => Uich_Bricks_Globals::get_or_create_container_width()->width,
                'colors' => Uich_Bricks_Globals::get_or_create_uich_color_palette(),
                'typography' => Uich_Bricks_Globals::get_uich_typography_classes(),
                'padding' => Uich_Bricks_Globals::get_uich_padding_classes()
            );
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

            return Uich_Bricks_Globals::get_uich_bricks_globals();
        }
    }
}