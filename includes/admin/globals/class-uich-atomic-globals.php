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

if ( ! class_exists( 'Uich_Atomic_Globals' ) ) {

    /**
	 * For handling Atomic Globals
	 */
	class Uich_Atomic_Globals {
        // ============================================================
        // CORE UTILITIES ATOMIC GLOBALS
        // ============================================================

        const CONTEXT_FRONTEND = 'frontend';
        const CONTEXT_PREVIEW  = 'preview';

        public static function object_to_array($obj) {
            if (is_object($obj) || is_array($obj)) {
                $ret = (array)$obj;
                foreach ($ret as &$item) {
                    $item = Uich_Atomic_Globals::object_to_array($item);
                }
                return $ret;
            }
            return $obj;
        }

        /** Get active kit's global classes as array */
        private static function get_global_classes_array(): array {
            $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();
            return json_decode($kit->get_meta('_elementor_global_classes'), true) 
                ?? ['items' => [], 'order' => []];
        }

        /** Save global classes + fire Elementor hooks */
        private static function save_global_classes_array(array $new, array $old): void {
            $kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();
            do_action('elementor/global_classes/update', Uich_Atomic_Globals::CONTEXT_FRONTEND, $new, $old);
            do_action('elementor/global_classes/update', Uich_Atomic_Globals::CONTEXT_PREVIEW,  $new, $old);
            $kit->update_meta('_elementor_global_classes', json_encode($new));
        }

        /**
         * Generic getter — filters items by ID prefix, extracts breakpoint values via callback.
         * @param string   $prefix  e.g. 'g-up', 'g-ub'
         * @param callable $extract fn(array $variant): ?array
         */
        private static function get_classes_by_prefix(string $prefix, callable $extract): array {
            $global_classes = Uich_Atomic_Globals::get_global_classes_array();
            $result = [];

            foreach ($global_classes['items'] ?? [] as $class) {
                if (!isset($class['id']) || strpos($class['id'], $prefix) !== 0) continue;

                $breakpoint_values = [];
                foreach ($class['variants'] ?? [] as $variant) {
                    $data = $extract($variant);
                    if ($data !== null) {
                        $bp = $variant['meta']['breakpoint'] ?? 'desktop';
                        $breakpoint_values[$bp] = $data;
                    }
                }

                if (!empty($breakpoint_values)) {
                    $result[] = [
                        'id'    => $class['id'],
                        'type'  => $class['type']  ?? 'class',
                        'label' => $class['label'] ?? '',
                        'value' => $breakpoint_values,
                    ];
                }
            }

            return $result;
        }

        /**
         * Generic sync — applies ADD/SET/DEL CRUD for items matching a prefix.
         * @param array    $items          Normalized items array
         * @param string   $prefix         e.g. 'g-up'
         * @param callable $build_variants fn(array $bpValues, string $bp): array
         */
        private static function sync_classes(array $items, string $prefix, callable $build_variants): void {
            $global_classes = Uich_Atomic_Globals::get_global_classes_array();
            $old_value      = unserialize(serialize($global_classes));;
            $breakpoints    = ['widescreen', 'desktop', 'laptop', 'tablet_extra', 'tablet', 'mobile_extra', 'mobile'];

            foreach ($items as $item) {
                $action   = $item['action'] ?? 'none';
                $itemData = $item['value']  ?? $item;

                if (!isset($itemData['id']) || strpos($itemData['id'], $prefix) !== 0) continue;

                $id = $itemData['id'];

                // Build variants — only for breakpoints that actually have real data
                $variants = [];
                foreach ($breakpoints as $bp) {
                    if (!isset($itemData['value'][$bp]) || !is_array($itemData['value'][$bp])) continue;

                    $bpValues = $itemData['value'][$bp];

                    // Skip breakpoint if all values are null or empty string
                    $hasData = array_filter($bpValues, fn($v) => $v !== null && $v !== '');
                    if (empty($hasData)) continue;

                    $variants[] = $build_variants($bpValues, $bp);
                }

                // Preserve label and type
                $itemData['label']    = $itemData['label']    ?? $global_classes['items'][$id]['label'] ?? '';
                $itemData['type']     = $itemData['type']      ?? $global_classes['items'][$id]['type']  ?? 'class';
                $itemData['variants'] = $variants;

                // Clean temporary keys
                unset($itemData['value'], $itemData['action']);

                // CRUD operations
                if ($action === 'DEL') {
                    unset($global_classes['items'][$id]);
                    $global_classes['order'] = array_values(array_diff($global_classes['order'] ?? [], [$id]));
                } elseif ($action === 'SET' && isset($global_classes['items'][$id])) {
                    $global_classes['items'][$id] = $itemData;
                } elseif ($action === 'ADD' && !isset($global_classes['items'][$id])) {
                    $global_classes['items'][$id] = $itemData;
                    $global_classes['order'][]    = $id;
                }
            }

            Uich_Atomic_Globals::save_global_classes_array($global_classes, $old_value);
        }

        /** Normalize sync input: handle single item vs array */
        private static function normalize_sync_input($raw): array {
            $arr = Uich_Atomic_Globals::object_to_array($raw);
            return isset($arr['id']) ? [$arr] : $arr;
        }

        /** Build Elementor size structure */
        private static function size(float $size, string $unit = 'px'): array {
            return ['$$type' => 'size', 'value' => ['size' => $size, 'unit' => $unit]];
        }

        /** Build a standard variant meta wrapper */
        private static function variant_meta(string $breakpoint): array {
            return ['breakpoint' => $breakpoint, 'state' => null];
        }

        /** Convert "12px" string → ['size' => 12, 'unit' => 'px'] */
        private static function parse_size_string(string $value): array {
            preg_match('/(-?\d+(?:\.\d+)?)(px|em|rem|%|vh|vw)?/', $value, $m);
            return ['size' => (float)($m[1] ?? 0), 'unit' => $m[2] ?? 'px'];
        }

        /** Convert rgba() or any color string to hex */
        private static function rgbaToHex(string $color): string {
            if (preg_match('/^#([a-f0-9]{3,6})$/i', $color)) return $color;

            if (preg_match('/rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,?\s*([\d.]+)?\s*\)/i', $color, $m)) {
                [$r, $g, $b] = [
                    max(0, min(255, (int)$m[1])),
                    max(0, min(255, (int)$m[2])),
                    max(0, min(255, (int)$m[3])),
                ];
                $a = isset($m[4]) ? (float)$m[4] : 1.0;

                return $a >= 1
                    ? sprintf('#%02x%02x%02x', $r, $g, $b)
                    : sprintf('#%02x%02x%02x%s', $r, $g, $b, str_pad(dechex(round($a * 255)), 2, '0', STR_PAD_LEFT));
            }

            return $color;
        }


        // ============================================================
        // COLOR VARIABLES
        // ============================================================

        public static function get_uich_elementor_variables(): array {
            $kit  = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();
            $data = json_decode($kit->get_meta('_elementor_global_variables'), true);

            $result = [];
            foreach ($data['data'] ?? [] as $id => $variable) {
                if (($variable['type'] ?? '') !== 'global-color-variable') continue;
                $result[] = [
                    'id'    => $id,
                    'type'  => $variable['type'],
                    'label' => $variable['label'],
                    'value' => Uich_Atomic_Globals::rgbaToHex($variable['value']['value']),
                ];
            }
            return $result;
        }

        public static function sync_uich_elementor_variables($sync_data): array {
            if (empty($sync_data->data->color)) return Uich_Atomic_Globals::get_uich_elementor_variables();

            $kit      = \Elementor\Plugin::$instance->kits_manager->get_active_kit_for_frontend();
            $meta_key = '_elementor_global_variables';
            $vars     = Uich_Atomic_Globals::object_to_array(json_decode($kit->get_meta($meta_key)));

            // Ensure base structure exists
            if (!is_array($vars) || empty($vars)) {
                $vars = ['data' => [], 'watermark' => 0, 'version' => 1];
            }

            foreach (Uich_Atomic_Globals::object_to_array($sync_data->data->color) as $variable) {
                $required = ['action', 'type', 'label', 'id', 'value'];
                if (!empty(array_diff($required, array_keys($variable)))) continue;
                if ($variable['type'] !== 'global-color-variable') continue;

                $id     = $variable['id'];
                $action = $variable['action'];

                if ($action === 'ADD' || $action === 'SET') {
                    $vars['data'][$id] = [
                        'type'  => $variable['type'],
                        'label' => $variable['label'],
                        'value' => ['$$type' => 'color', 'value' => $variable['value']],
                    ];
                }

                if ($action === 'DEL') {
                    unset($vars['data'][$id]);
                }
            }

            $vars['watermark'] = ($vars['watermark'] ?? 0) + 1;
            $kit->update_meta($meta_key, json_encode($vars));

            // Clear CSS cache
            \Elementor\Plugin::$instance->files_manager->clear_cache();

            return Uich_Atomic_Globals::get_uich_elementor_variables();
        }


        // ============================================================
        // TYPOGRAPHY  (prefix: g-ut)
        // ============================================================

        // Props that use size+unit structure
        private const TYPO_SIZE_PROPS = ['font-size', 'line-height', 'letter-spacing'];

        // All typography props we read/write
        private const TYPO_PROPS = [
            'font-family', 'font-weight', 'font-size', 'font-style',
            'text-align', 'text-transform', 'line-height', 'letter-spacing', 'text-decoration'
        ];

        public static function get_elementor_typo_classes(): array {
            return Uich_Atomic_Globals::get_classes_by_prefix('g-ut', function (array $variant): ?array {
                if (empty($variant['props'])) return null;

                $typo_value = [];
                foreach (Uich_Atomic_Globals::TYPO_PROPS as $prop) {
                    if (!isset($variant['props'][$prop])) continue;
                    $propData = $variant['props'][$prop];

                    // Size props return "12px", string props return the value directly
                    if (isset($propData['value']['size']) && isset($propData['value']['unit'])) {
                        $typo_value[$prop] = $propData['value']['size'] . $propData['value']['unit'];
                    } else {
                        $typo_value[$prop] = $propData['value'] ?? $propData;
                    }
                }

                return !empty($typo_value) ? $typo_value : null;
            });
        }

        public static function sync_elementor_typo_classes($sync_data): array {
            if (!empty($sync_data->data->typography)) {
                Uich_Atomic_Globals::sync_classes(
                    Uich_Atomic_Globals::normalize_sync_input($sync_data->data->typography),
                    'g-ut',
                    function (array $bp, string $breakpoint): array {
                        $props = [];
                        foreach ($bp as $propName => $propValue) {
                            if (in_array($propName, Uich_Atomic_Globals::TYPO_SIZE_PROPS)
                                && preg_match('/(-?\d+(?:\.\d+)?)(px|em|rem|%|vh|vw)?/', $propValue, $m)
                            ) {
                                $props[$propName] = [
                                    '$$type' => 'size',
                                    'value'  => ['size' => (float)$m[1], 'unit' => $m[2] ?? 'px'],
                                ];
                            } else {
                                $props[$propName] = ['$$type' => 'string', 'value' => $propValue];
                            }
                        }
                        return ['meta' => Uich_Atomic_Globals::variant_meta($breakpoint), 'props' => $props];
                    }
                );
            }

            return Uich_Atomic_Globals::get_elementor_typo_classes();
        }


        // ============================================================
        // WIDTH  (prefix: g-uw — single persistent class)
        // ============================================================

        public static function get_global_width_class_id(): string {
            $option_key = 'uich_atomic_global_width_class_id';
            $id = get_option($option_key);

            if (!$id) {
                $id = 'g-uw' . strtolower(wp_generate_password(5, false, false)) . 'u';
                update_option($option_key, $id);
            }

            return $id;
        }

        public static function get_elementor_width_class(): array {
            $global_classes = Uich_Atomic_Globals::get_global_classes_array();
            $id             = Uich_Atomic_Globals::get_global_width_class_id();
            $widths         = [];

            foreach ($global_classes['items'][$id]['variants'] ?? [] as $variant) {
                if (!isset($variant['meta']['breakpoint'], $variant['props']['max-width']['value'])) continue;
                $val = $variant['props']['max-width']['value'];
                if (isset($val['size'])) {
                    $widths[$variant['meta']['breakpoint']] = $val['size'] . ($val['unit'] ?? 'px');
                }
            }

            // First-time defaults
            if (empty($widths)) {
                $widths = ['desktop' => '1440px', 'tablet' => '85%', 'mobile' => '90%'];
                $old_value = unserialize(serialize($global_classes));

                $global_classes['items'][$id] = [
                    'id'       => $id,
                    'type'     => 'class',
                    'label'    => 'boxed-width',
                    'variants' => [
                        ['meta' => Uich_Atomic_Globals::variant_meta('desktop'), 'props' => ['max-width' => Uich_Atomic_Globals::size(1440)]],
                        ['meta' => Uich_Atomic_Globals::variant_meta('tablet'),  'props' => ['max-width' => Uich_Atomic_Globals::size(85, '%')]],
                        ['meta' => Uich_Atomic_Globals::variant_meta('mobile'),  'props' => ['max-width' => Uich_Atomic_Globals::size(90, '%')]],
                    ],
                ];
                $global_classes['order'][] = $id;
                Uich_Atomic_Globals::save_global_classes_array($global_classes, $old_value);
            }

            $widths['id'] = $id;
            return $widths;
        }

        public static function sync_elementor_width_class($sync_data): array {
            $widths         = isset($sync_data->data->width) ? (array)$sync_data->data->width : [];
            $global_classes = Uich_Atomic_Globals::get_global_classes_array();
            $old_value      = unserialize(serialize($global_classes));
            $id             = Uich_Atomic_Globals::get_global_width_class_id();

            if (!isset($global_classes['items'][$id])) {
                $global_classes['items'][$id] = ['id' => $id, 'type' => 'class', 'label' => 'boxed-width', 'variants' => []];
                $global_classes['order'][]    = $id;
            }

            $variants = [];
            foreach ($widths as $breakpoint => $value) {
                if ($breakpoint === 'id' || empty($value)) continue;

                preg_match('/^(\d+(?:\.\d+)?)([a-z%]+)$/i', $value, $m);
                $variants[] = [
                    'meta'  => Uich_Atomic_Globals::variant_meta($breakpoint),
                    'props' => ['max-width' => Uich_Atomic_Globals::size((float)($m[1] ?? $value), $m[2] ?? 'px')],
                ];
            }

            $global_classes['items'][$id]['variants'] = $variants;

            Uich_Atomic_Globals::save_global_classes_array($global_classes, $old_value);

            return Uich_Atomic_Globals::get_elementor_width_class();
        }


        // ============================================================
        // PADDING  (prefix: g-up)
        // ============================================================

        public static function get_elementor_padding_classes(): array {
            return Uich_Atomic_Globals::get_classes_by_prefix('g-up', function (array $variant): ?array {
                if (!isset($variant['props']['padding']['value'])) return null;
                $v = $variant['props']['padding']['value'];
                return [
                    'top'    => isset($v['block-start']['value'])  ? $v['block-start']['value']['size']  : null,
                    'bottom' => isset($v['block-end']['value'])    ? $v['block-end']['value']['size']    : null,
                    'left'   => isset($v['inline-start']['value']) ? $v['inline-start']['value']['size'] : null,
                    'right'  => isset($v['inline-end']['value'])   ? $v['inline-end']['value']['size']   : null,
                ];
            });
        }

        public static function sync_elementor_padding_classes($sync_data): array {
            if (!empty($sync_data->data->padding)) {
                Uich_Atomic_Globals::sync_classes(
                    Uich_Atomic_Globals::normalize_sync_input($sync_data->data->padding),
                    'g-up',
                    function (array $bp, string $breakpoint): array {
                        return [
                            'meta'  => Uich_Atomic_Globals::variant_meta($breakpoint),
                            'props' => [
                                'padding' => [
                                    '$$type' => 'dimensions',
                                    'value'  => [
                                        'block-start'  => ['$$type' => 'size', 'value' => Uich_Atomic_Globals::parse_size_string($bp['top']    ?? '0px')],
                                        'block-end'    => ['$$type' => 'size', 'value' => Uich_Atomic_Globals::parse_size_string($bp['bottom'] ?? '0px')],
                                        'inline-start' => ['$$type' => 'size', 'value' => Uich_Atomic_Globals::parse_size_string($bp['left']   ?? '0px')],
                                        'inline-end'   => ['$$type' => 'size', 'value' => Uich_Atomic_Globals::parse_size_string($bp['right']  ?? '0px')],
                                    ],
                                ],
                            ],
                        ];
                    }
                );
            }

            return Uich_Atomic_Globals::get_elementor_padding_classes();
        }


        // ============================================================
        // BORDER  (prefix: g-ub)
        // ============================================================

        public static function get_elementor_border_classes(): array {
            $color_variable_map = [];
            foreach (Uich_Atomic_Globals::get_uich_elementor_variables() as $variable) {
                if (!empty($variable['id']) && !empty($variable['value'])) {
                    $color_variable_map[$variable['id']] = $variable['value'];
                }
            }


            return Uich_Atomic_Globals::get_classes_by_prefix('g-ub', function (array $variant) use ($color_variable_map): ?array {
                if (!isset($variant['props']['border-width']['value'])) return null;
                $v = $variant['props']['border-width']['value'];

                return [
                    'width' => [
                        'top'    => $v['block-start']['value']['size']  ?? null,
                        'bottom' => $v['block-end']['value']['size']    ?? null,
                        'left'   => $v['inline-start']['value']['size'] ?? null,
                        'right'  => $v['inline-end']['value']['size']   ?? null,
                    ],
                    'color' => $variant['props']['border-color']['$$type'] === 'color'
                        ? $variant['props']['border-color']['value']
                        : $color_variable_map[$variant['props']['border-color']['value']] ?? null,
                    'style' => $variant['props']['border-style']['value'] ?? null,
                ];
            });
        }

        public static function sync_elementor_border_classes($sync_data): void {
            if (empty($sync_data->data->border)) return;

            // Build hex => id map from the incoming color data
            $color_variable_map = [];
            foreach (Uich_Atomic_Globals::get_uich_elementor_variables() as $variable) {
                if (!empty($variable['id']) && !empty($variable['value'])) {
                    $color_variable_map[$variable['value']] = $variable['id'];
                }
            }

            Uich_Atomic_Globals::sync_classes(
                Uich_Atomic_Globals::normalize_sync_input($sync_data->data->border),
                'g-ub',
                function (array $bp, string $breakpoint) use ($color_variable_map): array {
                    return [
                        'meta'  => Uich_Atomic_Globals::variant_meta($breakpoint),
                        'props' => [
                            'border-width' => [
                                '$$type' => 'border-width',
                                'value'  => [
                                    'block-start'  => Uich_Atomic_Globals::size($bp['width']['top']    ?? 0),
                                    'block-end'    => Uich_Atomic_Globals::size($bp['width']['bottom'] ?? 0),
                                    'inline-start' => Uich_Atomic_Globals::size($bp['width']['left']   ?? 0),
                                    'inline-end'   => Uich_Atomic_Globals::size($bp['width']['right']  ?? 0),
                                ],
                            ],
                            'border-color' => $bp['color'] && isset($color_variable_map[$bp['color']])
                                ? ['$$type' => 'global-color-variable', 'value' => $color_variable_map[$bp['color']]]
                                : ['$$type' => 'color',  'value' => $bp['color']],
                            'border-style' => ['$$type' => 'string', 'value' => $bp['style']],
                        ],
                    ];
                }
            );
        }


        // ============================================================
        // BORDER RADIUS  (prefix: g-ur)
        // ============================================================

        public static function get_elementor_border_radius_classes(): array {
            return Uich_Atomic_Globals::get_classes_by_prefix('g-ur', function (array $variant): ?array {
                if (!isset($variant['props']['border-radius']['value'])) return null;
                $v = $variant['props']['border-radius']['value'];
                return [
                    'topLeft'     => $v['start-start']['value']['size'] ?? null,
                    'topRight'    => $v['start-end']['value']['size']   ?? null,
                    'bottomLeft'  => $v['end-start']['value']['size']   ?? null,
                    'bottomRight' => $v['end-end']['value']['size']     ?? null,
                ];
            });
        }

        public static function sync_elementor_border_radius_classes($sync_data): void {
            if (empty($sync_data->data->borderRadius)) return;

            Uich_Atomic_Globals::sync_classes(
                Uich_Atomic_Globals::normalize_sync_input($sync_data->data->borderRadius),
                'g-ur',
                function (array $bp, string $breakpoint): array {
                    return [
                        'meta'  => Uich_Atomic_Globals::variant_meta($breakpoint),
                        'props' => [
                            'border-radius' => [
                                '$$type' => 'border-radius',
                                'value'  => [
                                    'start-start' => Uich_Atomic_Globals::size($bp['topLeft']     ?? 0),
                                    'start-end'   => Uich_Atomic_Globals::size($bp['topRight']    ?? 0),
                                    'end-start'   => Uich_Atomic_Globals::size($bp['bottomLeft']  ?? 0),
                                    'end-end'     => Uich_Atomic_Globals::size($bp['bottomRight'] ?? 0),
                                ],
                            ],
                        ],
                    ];
                }
            );
        }


        // ============================================================
        // SHADOW  (prefix: g-us)
        // ============================================================

        public static function get_elementor_shadow_classes(): array {
            return Uich_Atomic_Globals::get_classes_by_prefix('g-us', function (array $variant): ?array {
                if (!isset($variant['props']['box-shadow']['value'])) return null;
                $s = $variant['props']['box-shadow']['value'][0]['value'];
                return [
                    'hOffset'  => $s['hOffset']['value']['size']  ?? null,
                    'vOffset'  => $s['vOffset']['value']['size']  ?? null,
                    'blur'     => $s['blur']['value']['size']      ?? null,
                    'spread'   => $s['spread']['value']['size']    ?? null,
                    'color'    => $s['color']['value']             ?? null,
                    'position' => $s['position']['value']          ?? 'outset',
                ];
            });
        }

        public static function sync_elementor_shadow_classes($sync_data): void {
            if (empty($sync_data->data->shadow)) return;

            Uich_Atomic_Globals::sync_classes(
                Uich_Atomic_Globals::normalize_sync_input($sync_data->data->shadow),
                'g-us',
                function (array $bp, string $breakpoint): array {
                    $shadowValue = [
                        'hOffset' => Uich_Atomic_Globals::size($bp['hOffset'] ?? 0),
                        'vOffset' => Uich_Atomic_Globals::size($bp['vOffset'] ?? 0),
                        'blur'    => Uich_Atomic_Globals::size($bp['blur']    ?? 0),
                        'spread'  => Uich_Atomic_Globals::size($bp['spread']  ?? 0),
                        'color'   => ['$$type' => 'color', 'value' => $bp['color']],
                    ];

                    // Only include position when not default "outset"
                    if (($bp['position'] ?? 'outset') !== 'outset') {
                        $shadowValue['position'] = ['$$type' => 'string', 'value' => $bp['position']];
                    }

                    return [
                        'meta'  => Uich_Atomic_Globals::variant_meta($breakpoint),
                        'props' => [
                            'box-shadow' => [
                                '$$type' => 'box-shadow',
                                'value'  => [['$$type' => 'shadow', 'value' => $shadowValue]],
                            ],
                        ],
                    ];
                }
            );
        }


        // ============================================================
        // GAP  (prefix: g-ug)
        // ============================================================

        public static function get_elementor_gap_classes(): array {
            return Uich_Atomic_Globals::get_classes_by_prefix('g-ug', function (array $variant): ?array {
                if (!isset($variant['props']['gap']['value'])) return null;
                $v = $variant['props']['gap']['value'];
                return [
                    'row'    => $v['row']['value']['size']    ?? null,
                    'column' => $v['column']['value']['size'] ?? null,
                ];
            });
        }

        public static function sync_elementor_gap_classes($sync_data): void {
            if (empty($sync_data->data->gap)) return;

            Uich_Atomic_Globals::sync_classes(
                Uich_Atomic_Globals::normalize_sync_input($sync_data->data->gap),
                'g-ug',
                function (array $bp, string $breakpoint): array {
                    return [
                        'meta'  => Uich_Atomic_Globals::variant_meta($breakpoint),
                        'props' => [
                            'gap' => [
                                '$$type' => 'layout-direction',
                                'value'  => [
                                    'row'    => Uich_Atomic_Globals::size($bp['row']    ?? 0),
                                    'column' => Uich_Atomic_Globals::size($bp['column'] ?? 0),
                                ],
                            ],
                        ],
                    ];
                }
            );
        }


        // ============================================================
        // AGGREGATE GET + SYNC
        // ============================================================

        public static function get_global_classes_and_variable(): array {
            return [
                'width'        => Uich_Atomic_Globals::get_elementor_width_class(),
                'color'        => Uich_Atomic_Globals::get_uich_elementor_variables(),
                'typography'   => Uich_Atomic_Globals::get_elementor_typo_classes(),
                'padding'      => Uich_Atomic_Globals::get_elementor_padding_classes(),
                'border'       => Uich_Atomic_Globals::get_elementor_border_classes(),
                'borderRadius' => Uich_Atomic_Globals::get_elementor_border_radius_classes(),
                'gap'          => Uich_Atomic_Globals::get_elementor_gap_classes(),
                'shadow'       => Uich_Atomic_Globals::get_elementor_shadow_classes(),
            ];
        }

        public static function sych_uich_elementor_classes_and_variables_sync($sync_data): array {
            Uich_Atomic_Globals::sync_elementor_width_class($sync_data);
            Uich_Atomic_Globals::sync_uich_elementor_variables($sync_data);
            Uich_Atomic_Globals::sync_elementor_typo_classes($sync_data);
            Uich_Atomic_Globals::sync_elementor_padding_classes($sync_data);
            Uich_Atomic_Globals::sync_elementor_border_classes($sync_data);
            Uich_Atomic_Globals::sync_elementor_border_radius_classes($sync_data);
            Uich_Atomic_Globals::sync_elementor_gap_classes($sync_data);
            Uich_Atomic_Globals::sync_elementor_shadow_classes($sync_data);

            return Uich_Atomic_Globals::get_global_classes_and_variable();
        }
    }
}