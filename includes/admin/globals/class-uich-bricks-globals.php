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
                if($key === Uich_Bricks_Globals::UICHEMY_THEME_NAME){
                    $width = sanitize_text_field($style['settings']['container']['width'] ?? Uich_Bricks_Globals::DEFAULT_CONTAINER_WIDTH);

                    // Reindex: Remove the current theme and append it to the end
                    $uichemy_theme_style = $style;
                    unset($theme_styles_array[$key]);
                    $theme_styles_array[$key] = $uichemy_theme_style;

                    update_option('bricks_theme_styles', $theme_styles_array);

                    return (object)[
                        'width' => $width,
                        'themeID' => $key,
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
                        'themeID' => $key ?? '',
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
                'themeID' => Uich_Bricks_Globals::UICHEMY_THEME_NAME,
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
            $theme_id = Uich_Bricks_Globals::initContainerWidth()->themeID;
            $theme_styles_array = Uich_Bricks_Globals::getOptionAsArray('bricks_theme_styles');

            // For initialize and retrieve the Uichemy container width.
            Uich_Bricks_Globals::get_global_container_width();

            if(empty($theme_styles_array)){
                return false;
            }

            $theme_styles_array[$theme_id]['settings']['container']['width'] = $container_width;
            return update_option('bricks_theme_styles', $theme_styles_array);
        }

        // Sync the Uichemy color palette with updates.
        public static function sync_uich_color_palette( $color_updates ){
            $color_palettes = Uich_Bricks_Globals::getOptionAsArray('bricks_color_palette');
            $uichemy_palette = Uich_Bricks_Globals::get_uich_color_palette();
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
                    unset($color);

                    if(!$found){
                        $color = [
                            'id'   => $id,
                            'hex'  => $hex,
                            'name' => $name,
                        ];
                        if(!empty($rgb)){
                            $color['rgb'] = $rgb;
                        }
                        $color_palettes[$palette_key]['colors'][] = $color;
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

                    // Convert objects to arrays if they exist
                    $desktop = is_object($desktop) ? get_object_vars($desktop) : $desktop;
                    $tablet = is_object($tablet) ? get_object_vars($tablet) : $tablet;
                    $mobile = is_object($mobile) ? get_object_vars($mobile) : $mobile;
                    $mobile_landscape = is_object($mobile_landscape) ? get_object_vars($mobile_landscape) : $mobile_landscape;

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

                    // Convert objects to arrays if they exist
                    $desktop = is_object($desktop) ? get_object_vars($desktop) : $desktop;
                    $tablet = is_object($tablet) ? get_object_vars($tablet) : $tablet;
                    $mobile = is_object($mobile) ? get_object_vars($mobile) : $mobile;
                    $mobile_landscape = is_object($mobile_landscape) ? get_object_vars($mobile_landscape) : $mobile_landscape;

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