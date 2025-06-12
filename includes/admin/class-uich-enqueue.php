<?php
/**
 * This file specifically loads JavaScript and CSS dependencies.
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Uichemy
 */

namespace Uich\Uich_enqueue;

/**
 * Exit if accessed directly.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uich_Enqueue' ) ) {

	/**
	 * Here Enqueue all js and css script
	 */
	class Uich_Enqueue {

        public $uich_onbording_end = 'uich_onbording_end';

        public $onbording_api = 'https://api.posimyth.com/wp-json/uich/v2/uich_store_user_data';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since   1.0.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'uich_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'uich_admin_scripts' ), 10, 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_bricks_scripts' ));

			// Gutenberg editor load
			add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );

            // Dash Script Load
            add_action( 'admin_enqueue_scripts', array( $this, 'uich_dash_admin_scripts' ), 10, 1 );

            add_action( 'wp_ajax_uich_install_wdesign', array( $this,'uich_install_wdesign') );            

            add_action( 'wp_ajax_uich_boarding_store', array( $this, 'uich_boarding_store' ) );

            add_action('admin_head', function () {
                $screen = get_current_screen();
            
                // Check if we are on the desired admin page
                if (isset($_GET['page']) && $_GET['page'] === 'uichemy-welcome') {
                    // Remove all admin notices via CSS
                    echo '<style>
                        .notice, .update-nag, .updated, .error, .is-dismissible, .notice-success, .notice-error, .notice-warning {
                            display: none !important;
                        }
                    </style>';
                }
            });

            add_action( 'wp_ajax_activate_elementor_pro_plugin', array( $this, 'activate_elementor_pro_plugin' ) );

            add_action('wp_ajax_uich_update_notice_count', array($this, 'uich_update_notice_count'));
		}

		public function editor_assets() {
			global $pagenow;
			$scripts_dep = array( 'react', 'react-dom', 'wp-block-editor', 'wp-element', 'wp-blocks', 'wp-i18n','wp-plugins', 'wp-components','wp-api-fetch');
			if ( 'widgets.php' !== $pagenow && 'customize.php' !== $pagenow ) {
				wp_enqueue_style( 'uichemy-cp-style', UICH_URL . 'assets/css/uich-cp.css', array(), UICH_VERSION, 'all' );
				$scripts_dep = array_merge($scripts_dep, array('wp-editor', 'wp-edit-post'));
				wp_enqueue_script('uich-editor-js', UICH_URL . 'assets/js/build/uich-copy-button.js', $scripts_dep, '1.0.0', false);
			}
		}

        public function uich_dash_admin_scripts( $page ){
            $slug = array( 'toplevel_page_uichemy-welcome' );
            if ( ! in_array( $page, $slug, true ) ) {
                return;
            }
    
            $this->uich_dash_enqueue_style();
            $this->uich_dash_enqueue_scripts();
        }

        /*
        * Enqueue Styles admin area.
        * @since 2.0.0
        */
        public function uich_dash_enqueue_style() {
            wp_enqueue_style( 'uich-dash-style', UICH_URL . 'dashboard/build/index.css', array(), UICH_VERSION, 'all' );
        }

        /**
         * Check if the notice should be shown
         *
         * @since 3.2.3
         */
        public function uich_notice_should_show(){
            return ( get_option( 'uich_menu_notice_count' ) < UICH_ADMIN_NOTICE_FALG );
        }

        /**
         * Enqueue script admin area.
         *
         * @since 2.0.0
         */
        public function uich_dash_enqueue_scripts() {
            
            $user = wp_get_current_user();
            $plusAddons = false ;
            $elementorPro = false;
            // $gutenberg = false;
            $elementor_install         = apply_filters( 'uich_recommended_settings', 'elementor_install' );
            $elementor_install_success = ! empty( $elementor_install['success'] ) ? (bool) $elementor_install['success'] : false;

            $tpgb_install = apply_filters( 'uich_recommended_settings', 'tpgb_install' );
            $tpgb_install_success = ! empty( $tpgb_install['success'] ) ? (bool) $tpgb_install['success'] : false;

            $flexbox_setting     = apply_filters( 'uich_recommended_settings', 'flexbox_container' );
            $flexbox_setting_val = ! empty( $flexbox_setting['data'] ) ? $flexbox_setting['data'] : '';

            $file_uploads     = apply_filters( 'uich_recommended_settings', 'enable_unfiltered_file_uploads' );
            $file_uploads_val = ! empty( $file_uploads['data'] ) ? $file_uploads['data'] : '';

            $bricks_uploads_svg     = apply_filters( 'uich_recommended_settings', 'bricks_svg_uploads' );
            $bricks_uploads_val_svg = ! empty( $bricks_uploads_svg['data'] ) ? $bricks_uploads_svg['data'] : '';

            $bricks_permissions =[];
            if(!empty($bricks_uploads_val_svg)){
                 foreach ( $bricks_uploads_val_svg as $role => $details ) {
                    if ( isset($details['capabilities']['bricks_upload_svg']) ) {
                        $bricks_permissions = $details['capabilities']['bricks_upload_svg'];
                    }
                }
            }

            $current_theme = wp_get_theme();
            $active_theme_slug = get_stylesheet(); 
			$current_theme_name = $current_theme->get( 'Name' );
		    $pluginslist = get_plugins();

            $find_plugin = [];
            foreach ( $pluginslist as $slug => $plu_name ) {
                $find_plugin[] =$plu_name['Name'];
                
            }
           

            $current_theme = wp_get_theme();
            $active_theme_slug = get_stylesheet(); 
			$current_theme_name = $current_theme->get( 'Name' );
		    $pluginslist = get_plugins();

            $all_themes = wp_get_themes();
            $active_theme_slug = get_stylesheet(); 

            $find_theme = [];

            foreach ( $all_themes as $slug => $theme ) {
                $find_theme[] =$theme->get('Name');
                
            }


            if ( isset( $pluginslist[ 'the-plus-addons-for-elementor-page-builder/theplus_elementor_addon.php' ] ) && !empty( $pluginslist[ 'the-plus-addons-for-elementor-page-builder/theplus_elementor_addon.php' ] ) ) {
                if( is_plugin_active('the-plus-addons-for-elementor-page-builder/theplus_elementor_addon.php') ){
                    $plusAddons = true;
                }
            }

            if ( isset( $pluginslist[ 'elementor-pro/elementor-pro.php' ] ) && !empty( $pluginslist[ 'elementor-pro/elementor-pro.php' ] ) ) {
                if( is_plugin_active('elementor-pro/elementor-pro.php') ){
                    $elementorPro = true;
                }
            }

            // if ( isset( $pluginslist[ 'gutenberg/gutenberg.php' ] ) && !empty( $pluginslist[ 'gutenberg/gutenberg.php' ] ) ) {
            //     if( is_plugin_active('gutenberg/gutenberg.php') ){
            //         $gutenberg = true;
            //     }
            // }

            $admins = get_users( array( 'role' => 'Administrator' ) );
            $admin_username = [];
            foreach ( $admins as $admin ) {
                $admin_username[] = $admin->user_login;
            }

            if($user)
            {
                $dashData = [
                    'userData' => [
                        'userName' => esc_html($user->display_name),
                        'profileLink' => esc_url( get_avatar_url( $user->ID ) ),
                        'userEmail' => get_option('admin_email'),
                        'siteUrl' => get_option('siteurl'),
                    ],
                    'elementor' =>$elementor_install_success,
                    'nexterBlock'=>$tpgb_install_success,
                    // 'gutenberg' => $gutenberg,
                    'plusAddons'=>$plusAddons,
                    'elementorPro'=>$elementorPro,
                    'flexboxCon'=>$flexbox_setting_val,
                    'eleFileLoad'=>$file_uploads_val,
                    'bricksFileLoad'=>$current_theme_name,
                    'bricksSvgLoad'=>$bricks_permissions,
                    'activeTheme'=> $active_theme_slug,
                    'findTheme' => $find_theme,
                    'findPlugin' => $find_plugin,
                    'uich_onbording' => get_option( 'uich_onbording_end' ),
                    'version'=>UICH_VERSION,
                    'adminUsername' => $admin_username,
                    'pluginVersion'=> $this->uich_notice_should_show(),
                    'siteToken' => apply_filters( 'uich_manage_token', 'get_token' ),
                ];
            }
           
            

            wp_enqueue_script( 'uich-dashscript', UICH_URL . 'dashboard/build/index.js', array( 'react', 'react-dom', 'wp-dom-ready', 'wp-element' ), UICH_VERSION, true );
            wp_localize_script(
                'uich-dashscript',
                'uich_ajax_object',
                array(
                    'adminUrl' => admin_url(),
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => wp_create_nonce( 'uich-dash-ajax-nonce' ),
                    'uich_url' => UICH_URL.'dashboard/',
                    'dashData' => $dashData,

                )
            );
        }

        /**
         * Install WDesign
         *
         * @since 3.2.3
         */

        public function uich_install_wdesign(){
            check_ajax_referer('uich-dash-ajax-nonce', 'security');

            if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error( array( 'content' => __( 'Insufficient permissions.', 'uichemy' ) ) );
            }

            $plu_slug = ( isset( $_POST['slug'] ) && !empty( $_POST['slug'] ) ) ? $_POST['slug'] : '';

            if ( $plu_slug === 'bricks' || $plu_slug === 'nexter' ) {
                $all_themes = wp_get_themes();

                if ( isset( $all_themes[ $plu_slug ] ) ) {
                    switch_theme( $plu_slug );
                    wp_send_json_success( array( 'content' => __( 'Bricks theme has been activated.', 'uichemy' ) ) );
                } else {
                    wp_send_json_error( array( 'content' => __( 'Bricks theme is not installed.', 'uichemy' ) ) );
                }
            }
            
            $plugin_file_map = [
                'the-plus-addons-for-elementor-page-builder' => 'theplus_elementor_addon.php',
            ];
            
            $plugin_file = isset($plugin_file_map[$plu_slug]) ? $plugin_file_map[$plu_slug] : $plu_slug.'.php';
            
            $plugin_basename = $plu_slug.'/'.$plugin_file;

            $installed_plugins = get_plugins();

            include_once ABSPATH . 'wp-admin/includes/file.php';
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            include_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';
            include_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

            $result   = array();
            $response = wp_remote_post(
                'http://api.wordpress.org/plugins/info/1.0/',
                array(
                    'body' => array(
                        'action'  => 'plugin_information',
                        'request' => serialize(
                            (object) array(
                                'slug'   => $plu_slug,
                                'fields' => array(
                                    'version' => false,
                                ),
                            )
                        ),
                    ),
                )
            );

            $plugin_info = unserialize( wp_remote_retrieve_body( $response ) );

            if ( ! $plugin_info ) {
                wp_send_json_error( array( 'content' => __( 'Failed to retrieve plugin information.', 'uichemy' ) ) );
            }

            $skin     = new \Automatic_Upgrader_Skin();
            $upgrader = new \Plugin_Upgrader( $skin );
            
            if ( ! isset( $installed_plugins[ $plugin_basename ] ) && empty( $installed_plugins[ $plugin_basename ] ) ) {
                $installed = $upgrader->install( $plugin_info->download_link );

                $activation_result = activate_plugin( $plugin_basename );

                $success = null === $activation_result;
                wp_send_json(['Sucees' => true]);

            } elseif ( isset( $installed_plugins[ $plugin_basename ] ) ) {
                $activation_result = activate_plugin( $plugin_basename );

                $success = null === $activation_result;
                wp_send_json(['Sucees' => true]);
            }
        }

        /**
         * Activate Elementor Pro Plugin
         *
         * @since 3.2.3
         */
        public function activate_elementor_pro_plugin() {
            
            check_ajax_referer('uich-dash-ajax-nonce', 'security');
        
            $plugin_paths = [
                'elementor-pro/elementor-pro.php',
                'elementor-pro/plugin.php'
            ];
        
            $option = isset($_POST['pluginName']) ? sanitize_text_field($_POST['pluginName']) : '';
        
            $plugin_file = '';
            foreach ($plugin_paths as $path) {
                if (file_exists(WP_PLUGIN_DIR . '/' . $path)) {
                    $plugin_file = $path;
                    break;
                }
            }
        
            if (empty($plugin_file)) {
                $response = [
                    'success' => false,
                    'message' => 'Elementor Pro plugin files not found. Please install the plugin first.',
                    'data' => $option
                ];
                wp_send_json_error($response);
                return;
            }
        
            if (is_plugin_active($plugin_file)) {
                $response = [
                    'success' => true,
                    'message' => 'Elementor Pro is already active',
                    'data' => $option
                ];
                wp_send_json_success($response);
                return;
            }
        
            if (!function_exists('activate_plugin')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
        
            $activation_result = activate_plugin($plugin_file);
        
            if (is_wp_error($activation_result)) {
                $response = [
                    'success' => false,
                    'message' => 'Failed to activate Elementor Pro: ' . $activation_result->get_error_message(),
                    'data' => $option
                ];
                wp_send_json_error($response);
            } else {
                $response = [
                    'success' => true,
                    'message' => 'Elementor Pro activated successfully',
                    'data' => $option
                ];
                wp_send_json_success($response);
            }
        }

		/**
		 * Add Menu Page WdKit.
		 *
		 * @since   1.0.0
		 */
		public function uich_admin_menu() {
			$capability = 'manage_options';

			if ( current_user_can( $capability ) ) {
				add_menu_page( __( 'UiChemy', 'uichemy' ), __( 'UiChemy', 'uichemy' ), 'manage_options', 'uichemy-welcome', array( $this, 'uich_menu_page_template' ), UICH_URL . 'assets/svg/bw-logo.svg' );
			}
		}

		/**
		 * Load Uichemy uichemy-settings page content.
		 *
		 * @since   1.0.0
		 */
		public function uich_menu_page_template() {
            echo '<div id="uich-dash"></div>';
		}

		/**
		 * Enqueue Scripts admin area.
		 *
		 * @since   1.0.0
		 *
		 * @param string $page use for check page type.
		 */
		public function uich_admin_scripts( $page ) {

			$slug = array( 'uichemy_page_uichemy-settings', 'toplevel_page_uichemy-welcome' );
			if ( ! in_array( $page, $slug, true ) ) {
				return;
			}
            
			// $this->uich_enqueue_scripts();
            $this->uich_enqueue_scripts_regenerate_token();

		}

		/**
		 * Enqueue script admin area.
		 *
		 * @since   1.0.0
		 */
		public function uich_enqueue_scripts() {
			wp_enqueue_script( 'uichemy-script', UICH_URL . 'assets/js/uichemy-script.js', array( 'jquery' ), UICH_VERSION, true );
			wp_localize_script(
				'uichemy-script',
				'uichemy_ajax_object',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'uichemy-ajax-nonce' ),
				)
			);
		}

        /**
         * Enqueue script regenerate token admin area.
         *
         * @since 3.2.3
         */
        public function uich_enqueue_scripts_regenerate_token() {
			wp_enqueue_script( 'uich-regenerate-token', UICH_URL . 'assets/js/uich-regenerate-token.js', array( 'jquery' ), UICH_VERSION, true );
			wp_localize_script(
				'uich-regenerate-token',
				'uichemy_ajax_object',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'uichemy-ajax-nonce' ),
				)
			);
		}

        /**
         * Enqueue script bricks button admin area.
         *
         * @since 3.2.3
         */
        public function enqueue_bricks_scripts() {
			wp_register_script(
				'uich-bricks-button-js',
				UICH_URL . 'assets/js/uich-bricks-button.js',
				array('jquery'),
				UICH_VERSION,
				true,
			);


			if ( !empty( $_GET['bricks'] ) && $_GET['bricks'] === 'run') {
				wp_enqueue_script('uich-bricks-button-js');

				wp_localize_script(
					'uich-bricks-button-js',
					'uichemy_ajax_object',
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => wp_create_nonce( 'uichemy-ajax-nonce' ),
					)
				);
			}

		}

        /**
         * Store onboarding data.
         *
         * @since 3.2.3
         */
        public function uich_boarding_store() {
            check_ajax_referer( 'uich-dash-ajax-nonce', 'nonce' );

            $uionData = ( isset($_POST['boardingData']) && !empty($_POST['boardingData']) ) ? wp_unslash(json_decode(stripslashes($_POST['boardingData']), true)) : [];

            if( !empty($uionData) && isset($uionData['uich_onboarding']) && $uionData['uich_onboarding'] == true ) {
                
                $server_software = ! empty( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

                $web_server         = $server_software;
                $memory_limit       = ini_get( 'memory_limit' );
                $max_execution_time = ini_get( 'max_execution_time' );
                $php_version        = phpversion();
                $wp_version         = get_bloginfo( 'version' );
                $email              = get_option( 'admin_email' );
                $siteurl            = get_option( 'siteurl' );
                $language           = get_bloginfo( 'language' );

                $act_plugin = array();
                $actplu     = get_option( 'active_plugins' );
                if ( ! function_exists( 'get_plugins' ) ) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }

                $plugins = get_plugins();
                foreach ( $actplu as $p ) {
                    if ( isset( $plugins[ $p ] ) ) {
                        $act_plugin[] = $plugins[ $p ]['Name'];
                    }
                }

                $theme      = '';
                $acthemeobj = wp_get_theme();
                if ( $acthemeobj->get( 'Name' ) !== null && ! empty( $acthemeobj->get( 'Name' ) ) ) {
                    $theme = $acthemeobj->get( 'Name' );
                }

                $elementor_install         = apply_filters( 'uich_recommended_settings', 'elementor_install' );
                $elementor_install_success = ! empty( $elementor_install['success'] ) ? (bool) $elementor_install['success'] : false;

                $flexbox_setting     = apply_filters( 'uich_recommended_settings', 'flexbox_container' );
                $flexbox_setting_val = ! empty( $flexbox_setting['data'] ) ? $flexbox_setting['data'] : 0;

                $file_uploads     = apply_filters( 'uich_recommended_settings', 'enable_unfiltered_file_uploads' );
                $file_uploads_val = ! empty( $file_uploads['data'] ) ? (bool) $file_uploads['data'] : 0;

                $basic_requirements = array(
                    'elementor_install'       => $elementor_install_success,
                    'flexbox_container'       => $flexbox_setting_val,
                    'unfiltered_file_uploads' => $file_uploads_val,
                );

                $final = array(
                    'web_server'         => $web_server,
                    'memory_limit'       => $memory_limit,
                    'max_execution_time' => $max_execution_time,
                    'php_version'        => $php_version,
                    'wp_version'         => $wp_version,
                    'email'              => $email,
                    'site_url'           => $siteurl,
                    'site_language'      => $language,
                    'theme'              => $theme,
                    'plugins'            => $act_plugin,
                    'basic_requirements' => $basic_requirements,
                );

                $response = wp_remote_post(
                    $this->onbording_api,
                    array(
                        'method' => 'POST',
                        'body'   => wp_json_encode( $final ),
                        'headers' => array(
                            'Content-Type' => 'application/json',
                        ),
                    )
                );

                update_option( $this->uich_onbording_end, true );

                $result = array(
                    'success'     => true,
                    'message'     => 'Welcome to UiChemy!',
                    'description' => 'Your Onboarding finished successfully.',
                );

                wp_send_json( $result );
                
            } else {
                
                update_option( $this->uich_onbording_end, true );
                
                $result = array(
                    'success'     => true,
                    'message'     => 'Onboarding completed.',
                    'description' => 'Onboarding process finished.',
                );

                wp_send_json( $result );
            }

            wp_die();
        }

        /**
         * Update notice count
         *
         * @since 3.2.3
         */
        public function uich_update_notice_count() {
            check_ajax_referer('uich-dash-ajax-nonce', 'security');

            $get_option = get_option('uich_menu_notice_count');
            $updated = false;
            if ($get_option < UICH_ADMIN_NOTICE_FALG) {
                $updated = update_option('uich_menu_notice_count', UICH_ADMIN_NOTICE_FALG, false);
            }

            wp_send_json_success(['updated' => (bool)$updated]);
        }

	}

	new Uich_Enqueue();
}