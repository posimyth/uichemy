<?php

class Uichemy_Gutenberg_Image_Import {

    /**
	 * Replaced images IDs.
	 *
	 * The old attachment ID and the new attachment ID generated after the import.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @var array
	 */
	private static $new_image_ids = [];

	/**
	 * Get attachment url image hash sha1.
	 *
	 * Retrieve the sha1 hash of the image URL.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @param string $attachment_url The attachment URL.
	 */
	private static function get_attachment_url_hash_image( $attachment_url ) {
		return sha1( $attachment_url );
	}

    private static $instance;

    public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

    public function __construct() {
        add_action('wp_ajax_uichemy_import_images', array($this, 'cross_copy_paste_media_import'));
    }


    /**
     * AJAX Handler for Copy-Paste Media Import
     */
    public static function cross_copy_paste_media_import() {
        check_ajax_referer('uichemy-ajax-nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Not a Valid', 'tpgb'), 403);
        }

        $media_import = isset($_POST['content']) ? wp_unslash($_POST['content']) : '';


        if (empty($media_import)) {
            wp_send_json_error(__('Empty Content.', 'tpgb'));
        }

        $media_import = json_decode($media_import, true);
        
        // Check if the decoded content is an array or object and process it recursively
        if (is_array($media_import) || is_object($media_import)) {
            // Process the media import recursively
            $updated_media = self::array_recursively_data($media_import, function($block_data) {
                // Process each block using the check_block_image_upload function
                return self::check_block_image_upload($block_data);
            });
        }

        // Send the updated media import as JSON response
        wp_send_json_success($updated_media);
    }

    public static function array_recursively_data($data, $callback, $args = []) {
    // If the data has a 'name' key, we are dealing with a block
    if (isset($data['name'])) {
        // Recursively process inner blocks if they exist
        if (!empty($data['innerBlocks'])) {
            $data['innerBlocks'] = self::array_recursively_data($data['innerBlocks'], $callback, $args);
        }

        // Call the callback function to process the block data
        return call_user_func($callback, $data, $args);
    }

    // If data is neither an array nor an object, return the data as is
    if (gettype($data) !== "array" && gettype($data) !== "object") {
        return $data;
    }

    // Iterate through the array or object and process each item recursively
    foreach ($data as $key => $value) {
        $processed_data = self::array_recursively_data($value, $callback, $args);
        if (null !== $processed_data) {
            $data[$key] = $processed_data;
        }
    }

    return $data;
}

public static function check_block_image_upload($block_data) {

    if (isset($block_data['name']) && isset($block_data['attributes'])) {

        // Spectra Image Block
        if ($block_data['name'] === 'uagb/image' || $block_data['name'] === 'core/image') {
            if(isset($block_data['attributes']['url']) && isset($block_data['attributes']['id']) && !empty($block_data['attributes']['url'])){
                $result = self::upload_media($block_data['attributes']['url']);
                $block_data['attributes']['url'] = $block_data['attributes']['urlTablet'] = $block_data['attributes']['urlMobile'] = $result['url'];
                $block_data['attributes']['id'] = $result['post_id'];
            }
        }

        // Core Image Block
        elseif ($block_data['name'] === 'core/image') {
            if(isset($block_data['attributes']['url']) && isset($block_data['attributes']['id']) && !empty($block_data['attributes']['url'])){
                $result = self::upload_media($block_data['attributes']['url']);
                $block_data['attributes']['url'] = $result['url'];
                $block_data['attributes']['id'] = $result['post_id'];
            }
        }

        // Spectra Container Image Block
		elseif ($block_data['name'] === 'uagb/container') {
			if (isset($block_data['attributes']['backgroundImageDesktop']['url']) && isset($block_data['attributes']['backgroundImageDesktop']['id']) && !empty($block_data['attributes']['backgroundImageDesktop']['url'])) {
				$result = self::upload_media($block_data['attributes']['backgroundImageDesktop']['url']);
				$block_data['attributes']['backgroundImageDesktop']['url'] = $result['url'];
				$block_data['attributes']['backgroundImageDesktop']['id'] = $result['post_id'];
			}
		}

        // Core Container Image Block
        elseif ($block_data['name'] === 'core/group') {
            if (isset($block_data['attributes']['style']['background']['backgroundImage']['url']) && isset($block_data['attributes']['style']['background']['backgroundImage']['id']) && !empty($block_data['attributes']['style']['background']['backgroundImage']['url'])) {
				$result = self::upload_media($block_data['attributes']['style']['background']['backgroundImage']['url']);
				$block_data['attributes']['style']['background']['backgroundImage']['url'] = $result['url'];
				$block_data['attributes']['style']['background']['backgroundImage']['id'] = $result['post_id'];
			}
        }

        // Kedence Image Block
        elseif($block_data['name'] === "kadence/image"){
            if(isset($block_data['attributes']['url'])){
                $new_url = self::upload_media($block_data['attributes']['url']);
                $block_data['attributes']['url'] = $new_url['url'];
                $block_data['attributes']['id'] = $new_url['post_id'];
                $updated = self::update_img_src_with_uploaded_url($block_data['originalContent'], $block_data['name']);
                $block_data['originalContent'] = $updated['data'];
            }
        }

        // Kedence Container Image Block
        elseif($block_data['name'] === "kadence/column"){ 
            if(isset($block_data['attributes']['backgroundImg']) && is_array($block_data['attributes']['backgroundImg'])) {
                foreach($block_data['attributes']['backgroundImg'] as $index => $bg_img) {
                    if (isset($bg_img['bgImg'])) {                        
                        $new_media_url = self::upload_media($bg_img['bgImg']);

                        // Update the bgImg URL
                        $block_data['attributes']['backgroundImg'][$index]['bgImg'] = $new_media_url['url'];

                        // Update the bgImgID
                        $block_data['attributes']['backgroundImg'][$index]['bgImgID'] = $new_media_url['post_id'];
                    }
                }
            }    
        }

        // GenerateBlocks Image Block
        elseif($block_data['name'] === "generateblocks/media"){
            if (isset($block_data['originalContent']) && strpos($block_data['originalContent'], 'img') !== false) {
                $new_updated = self::update_img_src_with_uploaded_url($block_data['originalContent'], $block_data['name']);
                $pattern = '/<img\s+[^>]*src=["\']([^"\']+)["\']/i';

                // Check if the pattern matches and extract the URL
                if (preg_match($pattern, $new_updated['data'], $matches)) {
                    // Extracted URL from src attribute
                    $original_url = $matches[1];
                    $block_data['attributes']['htmlAttributes']['src'] = $original_url;
                }

                $block_data['originalContent'] = $new_updated['data'];
            }
        }

        // GenerateBlocks Container Image Block
        elseif($block_data['name'] === "generateblocks/element"){

            if(isset($block_data['attributes']['styles']) && isset($block_data['attributes']['styles']['backgroundImage'])){
                $link = str_replace(['url(', ')'], '', $block_data['attributes']['styles']['backgroundImage']);
                $result = self::upload_media($link);

                if($result && is_array($result) && isset($result['url'])){
                    $block_data['attributes']['styles']['backgroundImage'] = 'url(' . $result['url'] . ')';
                }
            }
        }
    }

    return $block_data;
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
    
}
    
Uichemy_Gutenberg_Image_Import::get_instance();