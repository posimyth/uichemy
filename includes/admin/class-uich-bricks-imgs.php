<?php

add_action('wp_ajax_uich_bricks_import_media', array('Uich_Bricks_Import_Images', 'import_media'));

class Uich_Bricks_Import_Images {

    public static function import_media() {
        check_ajax_referer( 'uichemy-ajax-nonce', 'nonce' );

        if( ! is_user_logged_in() || ! current_user_can('edit_pages') || ! current_user_can('upload_files') ) {
            wp_send_json_error([ 'content' => __( 'Insufficient permissions.', 'uichemy' ) ], 401);
        }

        $decode_data = isset($_POST['inputData']) ? json_decode(wp_unslash($_POST['inputData']), true) : [];

        $post_content = isset($decode_data) && !empty($decode_data) && isset($decode_data['content']) ? $decode_data['content'] : [];

        $post_content = self::import_bricks_media($post_content);

        $decode_data['content'] = $post_content;

        $site_url = get_site_url();

        $decode_data['sourceUrl'] = $site_url;

        wp_send_json_success( $decode_data);
    }


    public static function import_bricks_media($content = []){

        if(!empty($content)){
            $elements = $content;
            \Bricks\Templates::$template_images = [];

            foreach ( $elements as $index => $element ) {
                if ( !empty( $element['settings'] ) ) {
                    \Bricks\Theme::instance()->templates->import_images( $element['settings'], true );
                }
            }

            // STEP: Replace remote image data with imported/existing image data.
            if ( count( \Bricks\Templates::$template_images ) ) {
                $elements_encoded = wp_json_encode( $elements );

                foreach ( \Bricks\Templates::$template_images as $template_image ) {
                    $elements_encoded = str_replace(
                        wp_json_encode( $template_image['old'] ),
                        wp_json_encode( $template_image['new'] ),
                        $elements_encoded
                    );
                }

                $content = json_decode( $elements_encoded, true );
            }
        }

        return $content;
    }
}
