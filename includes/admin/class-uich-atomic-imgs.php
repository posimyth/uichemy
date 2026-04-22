<?php
/**Exit if accessed directly.*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action('wp_ajax_elementor_import_media', array('Uich_Elementor_Import_Images', 'import_media'));

if ( ! class_exists( 'Uich_Elementor_Import_Images' ) ) {
    class Uich_Elementor_Import_Images {

        // Recursive handler for element arrays
        public static function process_and_upload_images(&$element) {
            if (!is_array($element)) {
                return;
            }

            // Process children first if exist
            if (isset($element['elements']) && is_array($element['elements'])) {
                foreach ($element['elements'] as &$child) {
                    self::process_and_upload_images($child);
                }
            }

            // --- SETTINGS HANDLING ---
            if (isset($element['settings']) && is_array($element['settings'])) {
                // Handle normal image
                if (
                    isset($element['settings']['image']['value']['src']['value']['url']['value']) &&
                    !empty($element['settings']['image']['value']['src']['value']['url']['value'])
                ) {
                    $image_url = $element['settings']['image']['value']['src']['value']['url']['value'];
                    $attachment_id = self::download_and_attach($image_url);
                    if ($attachment_id) {
                        $element['settings']['image']['value']['src']['value']['url'] = null;
                        $element['settings']['image']['value']['src']['value']['id']['value'] = $attachment_id;
                    }
                }

                // Handle SVG
                if (
                    isset($element['settings']['svg']['value']['url']['value']) &&
                    !empty($element['settings']['svg']['value']['url']['value'])
                ) {
                    $svg_url = $element['settings']['svg']['value']['url']['value'];
                    $attachment_id = self::download_and_attach($svg_url);
                    if ($attachment_id) {
                        $element['settings']['svg']['value']['url'] = null;
                        $element['settings']['svg']['value']['id']['value'] = $attachment_id;
                    }
                }
            }

            // --- STYLES HANDLING ---
            if (!empty($element['styles']) && is_array($element['styles'])) {
                foreach ($element['styles'] as &$style) {
                    if (!empty($style['variants']) && is_array($style['variants'])) {
                        foreach ($style['variants'] as &$variant) {
                            if (
                                isset($variant['props']['background']['value']['background-overlay']['value']) &&
                                is_array($variant['props']['background']['value']['background-overlay']['value'])
                            ) {
                                $background_overlays_values = &$variant['props']['background']['value']['background-overlay']['value'];

                                foreach ($background_overlays_values as &$background_overlays_value) {
                                    if (
                                        isset($background_overlays_value['value']['image']['value']['src']['value']['url']['value']) &&
                                        !empty($background_overlays_value['value']['image']['value']['src']['value']['url']['value'])
                                    ) {
                                        $bg_overlay_image_url = $background_overlays_value['value']['image']['value']['src']['value']['url']['value'];
                                        $attachment_id = self::download_and_attach($bg_overlay_image_url);
                                        if ($attachment_id) {
                                            $background_overlays_value['value']['image']['value']['src']['value']['url'] = null;
                                            $background_overlays_value['value']['image']['value']['src']['value']['id']['value'] = $attachment_id;
                                        }
                                    }
                                }
                            }

                            // Handle custom_css — normalize to Elementor's {raw: base64} format
                            if ( isset( $variant['custom_css'] ) ) {
                                $variant['custom_css'] = self::normalize_custom_css( $variant['custom_css'] );
                            }
                        }
                    }
                }
            }
        }

        /**
         * Normalize uich_custom_css to Elementor's expected format: { raw: base64_string }
         *
         * Accepts:
         *   - Plain string:           "color: red;"
         *   - Object with plain raw:  { raw: "color: red;" }
         *   - Already encoded:        { raw: "Y29sb3I6IHJlZDs=" }  (passes through)
         *
         * @param mixed $uich_custom_css
         * @return array|null
         */
        private static function normalize_custom_css( $uich_custom_css ) {
            if ( empty( $uich_custom_css ) ) {
                return null;
            }

            // Plain string — encode it
            if ( is_string( $uich_custom_css ) ) {
                $css = sanitize_textarea_field( trim( $uich_custom_css ) );
                return empty( $css ) ? null : [ 'raw' => base64_encode( $css ) ];
            }

            // Object/array with raw key
            if ( is_array( $uich_custom_css ) && isset( $uich_custom_css['raw'] ) && is_string( $uich_custom_css['raw'] ) ) {
                $raw = trim( $uich_custom_css['raw'] );
                if ( empty( $raw ) ) {
                    return null;
                }

                // Check if already valid Base64 — decode and re-encode to verify
                $decoded = base64_decode( $raw, true );
                if ( $decoded !== false && base64_encode( $decoded ) === $raw ) {
                    // Already Base64 encoded — pass through
                    return $uich_custom_css;
                }

                // Plain CSS in raw field — encode it
                $css = sanitize_textarea_field( $raw );
                return empty( $css ) ? null : [ 'raw' => base64_encode( $css ) ];
            }

            return null;
        }

        // Download + attach helper
        private static function download_and_attach($url) {
            if (!function_exists('download_url')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }
            if (!function_exists('media_handle_sideload')) {
                require_once ABSPATH . 'wp-admin/includes/media.php';
            }
            if (!function_exists('wp_read_image_metadata')) {
                require_once ABSPATH . 'wp-admin/includes/image.php';
            }

            // Check if this URL already exists
            $existing = get_posts([
                'post_type'  => 'attachment',
                'meta_key'   => '_uichemy_original_url',
                'meta_value' => esc_url_raw($url),
                'posts_per_page' => 1,
                'fields' => 'ids',
            ]);

            if (!empty($existing)) {
                $attachment_id = $existing[0];
                // error_log("Reusing existing attachment: $attachment_id for $url");
                return $attachment_id;
            }

            // Otherwise download & sideload
            $tmp = download_url($url);
            if (is_wp_error($tmp)) {
                return false;
            }

            $file_array = [
                'name'     => basename(parse_url($url, PHP_URL_PATH)),
                'tmp_name' => $tmp
            ];

            $attachment_id = media_handle_sideload($file_array, 0);
            if (is_wp_error($attachment_id)) {
                // error_log("Upload failed for: " . $url);
                @unlink($file_array['tmp_name']);
                return false;
            }

            // Save original URL to prevent duplicates
            update_post_meta($attachment_id, '_uichemy_original_url', esc_url_raw($url));

            // error_log("Uploaded new Image Post ID: $attachment_id for $url");
            return $attachment_id;
        }

        // AJAX handler
        public static function import_media() {
            check_ajax_referer( 'uichemy-ajax-nonce', 'nonce' );

            $post_content = isset($_POST['inputData']) ? json_decode(stripslashes($_POST['inputData']), true) : [];

            // If root has "elements", process them as children
            if (isset($post_content['elements']) && is_array($post_content['elements'])) {
                foreach ($post_content['elements'] as &$child) {
                    self::process_and_upload_images($child);
                }
            } else {
                // Otherwise treat root as element
                self::process_and_upload_images($post_content);
            }

            wp_send_json_success($post_content);
        }
    }
}
