<?php
/**
 * It is Main File to load all Notice, Upgrade Menu and all
 *
 * @link       https://posimyth.com/
 * @since      1.2.2
 *
 * @package    Uichemy
 * @subpackage Uichemy/includes
 * */

/**
 * Exit if accessed directly.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Uich_Onbording' ) ) {

	/**
	 * This class used for only load All Notice Files
	 *
	 * @since 1.2.2
	 */
	class Uich_Onbording {

		/**
		 * Instance
		 *
		 * @since 1.2.2
		 * @access private
		 * @static
		 * @var instance of the class.
		 */
		private static $instance = null;

		/**
		 * Onbording APi
		 *
		 * @since 1.2.2
		 * @var onbording_api of the class.
		 */
		public $onbording_api = 'https://api.posimyth.com/wp-json/uich/v2/uich_store_user_data';

		/**
		 * Onbording Popup Close
		 *
		 * @since 1.2.2
		 * @var uich_onbording_end of the class.
		 */
		public $uich_onbording_end = 'uich_onbording_end';

		/**
		 * Singleton Instance Creation Method.
		 *
		 * This public static method ensures that only one instance of the class is loaded or can be loaded.
		 * It follows the Singleton design pattern to create or return the existing instance of the class.
		 *
		 * @since 1.2.2
		 * @access public
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Perform some compatibility checks to make sure basic requirements are meet.
		 *
		 * @since 1.2.2
		 */
		public function __construct() {
			add_action( 'wp_ajax_uich_boarding_store', array( $this, 'uich_boarding_store' ) );
 
			$this->uich_deactivate_feedback();
		}

		/**
		 * Check if the Current Screen is Related to Plugin Management.
		 *
		 * @since 1.2.2
		 *
		 * @return bool True if the current screen is for managing plugins, otherwise false.
		 */
		private function uich__plugins_screen() {
			$pages = array( 'toplevel_page_uichemy-welcome', 'uichemy_page_uichemy-settings' );

			return in_array( get_current_screen()->id, $pages, true );
		}

		/**
		 * Initialize Hooks for Deactivation Feedback Functionality.
		 *
		 * Fired by the `current_screen` action hook.
		 *
		 * @since 1.2.2
		 */
		public function uich_deactivate_feedback() {
			add_action(
				'current_screen',
				function () {

					if ( ! $this->uich__plugins_screen() ) {
						return;
					}

					add_action( 'admin_enqueue_scripts', array( $this, 'uich_onboarding_assets' ) );
					add_action( 'admin_footer', array( $this, 'uich_onboarding_content_func' ) );
				}
			);
		}

		/**
		 * Perform some compatibility checks to make sure basic requirements are meet.
		 *
		 * @since 1.2.2
		 */
		public function uich_onboarding_assets() {
			$nonce = wp_create_nonce( 'uich_onboarding_nonce' );

			wp_enqueue_style( 'uichemy_onbording_style', UICH_URL . 'assets/css/uich-onbording.css', array(), UICH_VERSION, 'all' );
			wp_enqueue_script( 'uich_onboarding_js', UICH_URL . 'assets/js/uich-onbording.js', array(), UICH_VERSION, false );

			wp_localize_script(
				'uich_onboarding_js',
				'uich_onboarding_ajax',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => $nonce,
				)
			);
		}

		/**
		 * Perform some compatibility checks to make sure basic requirements are meet.
		 *
		 * @since 1.2.2
		 */
		public function uich_onboarding_content_func() {

			echo '<div class="uich-main">';

				echo '<div class="uich-fix-popup">';

					echo '<div class="uich-popup-container uich-step-1">';
						$this->uich_step1();
					echo '</div>';

					echo '<div class="uich-popup-container uich-step-2">';
						$this->uich_step2();
					echo '</div>';

					echo '<div class="uich-popup-container uich-step-3">';
						$this->uich_step3();
					echo '</div>';

					echo '<div class="uich-popup-container uich-step-4">';
						$this->uich_step4();
					echo '</div>';

					echo '<div class="uich-popup-container uich-step-5">';
						$this->uich_step5();
					echo '</div>';

					echo '<div class="uich-popup-container uich-step-6">';
						$this->uich_step6();
					echo '</div>';

					echo '<div class="uichemy-btn uich-btn-step-1">';
						echo '<button id="skip" class="uich-btn-skip">Skip</button>';
						echo '<span class="uichemy-page-count"> <span class="uich-pagination-first">01</span>/06 </span>';
						echo '<div class="uichemy-btn-group">';
							echo '<button class="uich-btn-back">Back</button>';
							echo '<button id="finish" class="uich-btn-finish">Next</button>';
						echo '</div>';
					echo '</div>';

				echo '</div>';

			echo '</div>';
		}

		/**
		 * Onbording Step 1
		 *
		 * @since 1.2.2
		 */
		public function uich_step1() {

			echo '<img src="' . esc_url( UICH_URL . 'assets/images/onbording/welcome.png' ) . '" alt="welcome" draggable="false"/>';

			echo '<div class="uichemy-content">';
				echo '<h1>' . esc_html__( 'Welcome', 'uichemy' ) . '</h1>';
				echo '<p>' . esc_html__( 'UiChemy is the perfect tool to bring your Figma design live on WordPress. Lets start!', 'uichemy' ) . '</p>';
			echo '</div>';

			echo '<div class="uich-select-page-builder">';
				echo '<h3>' . esc_html__( 'Select your Page Builder', 'uichemy' ) . '</h3>';

				echo '<div class="uich-egb-builder">';

					echo '<div class="uich-latest-builder" id="uich-radio-select">';
						echo '<input type="radio" id="uich-elementor" name="uich-radio" value="elementor" checked />';
						echo '<label for="uich-elementor">';
							echo '<svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 0C5.20044 0 0.5 4.70044 0.5 10.5C0.5 16.2976 5.20044 21 11 21C16.7996 21 21.5 16.2996 21.5 10.5C21.4981 4.70044 16.7976 0 11 0ZM8.37547 14.8736H6.62643V6.12454H8.37547V14.8736ZM15.3736 14.8736H10.1245V13.1245H15.3736V14.8736ZM15.3736 11.3735H10.1245V9.62451H15.3736V11.3735ZM15.3736 7.87358H10.1245V6.12454H15.3736V7.87358Z" fill="#92003B"/></svg>';
							echo '<h4>' . esc_html__( 'Elementor', 'uichemy' ) . '</h4>';
						echo '</label>';
					echo '</div>';

					echo '<div class="uich-latest-builder">';
						echo '<input type="radio" id="uich-bricks" name="uich-radio" value="bricks" />';
						echo '<label for="uich-bricks">';
							echo '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.2348 10.5276C20.2348 4.93987 15.7051 0.410156 10.1174 0.410156C4.52971 0.410156 0 4.93987 0 10.5276C0 16.1152 4.52971 20.6449 10.1174 20.6449C15.7051 20.6449 20.2348 16.1152 20.2348 10.5276Z" fill="#FFD53E"/><path d="M8.2497 9.05296C8.47771 8.71502 8.79029 8.44048 9.18727 8.22935C9.59273 8.01817 10.0531 7.91258 10.5683 7.91258C11.1681 7.91258 11.7087 8.06041 12.1902 8.35608C12.6801 8.6517 13.0644 9.07404 13.3432 9.62311C13.6304 10.1637 13.774 10.793 13.774 11.511C13.774 12.229 13.6304 12.8667 13.3432 13.4242C13.0644 13.9733 12.6801 14.3998 12.1902 14.7039C11.7087 15.008 11.1681 15.1601 10.5683 15.1601C10.0446 15.1601 9.58433 15.0587 9.18727 14.856C8.79877 14.6448 8.48619 14.3745 8.2497 14.0451V15.046H6.47583V5.66992H8.2497V9.05296ZM11.9621 11.511C11.9621 11.0887 11.8734 10.7254 11.696 10.4213C11.5271 10.1088 11.2991 9.87228 11.0118 9.71179C10.7331 9.5513 10.429 9.47102 10.0996 9.47102C9.77858 9.47102 9.47448 9.5555 9.18727 9.72447C8.90853 9.88496 8.68044 10.1215 8.50307 10.434C8.33418 10.7465 8.2497 11.1139 8.2497 11.5363C8.2497 11.9586 8.33418 12.3261 8.50307 12.6386C8.68044 12.9512 8.90853 13.1919 9.18727 13.3609C9.47448 13.5214 9.77858 13.6016 10.0996 13.6016C10.429 13.6016 10.7331 13.5172 11.0118 13.3482C11.2991 13.1792 11.5271 12.9385 11.696 12.6259C11.8734 12.3135 11.9621 11.9417 11.9621 11.511Z" fill="black"/></svg>';
							echo '<h4>' . esc_html__( 'Bricks', 'uichemy' ) . '</h4>';
						echo '</label>';
					echo '</div>';

					echo '<div class="uich-latest-builder">';
						echo '<input type="radio" id="uich-gutenberg" name="uich-radio" value="gutenberg" />';
						echo '<label for="uich-gutenberg">';
							echo '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 21C16.299 21 21 16.299 21 10.5C21 4.70101 16.299 0 10.5 0C4.70101 0 0 4.70101 0 10.5C0 16.299 4.70101 21 10.5 21ZM6.2107 11.3716C6.09155 8.50625 6.70433 7.01558 8.30435 6.31994C9.98948 5.57461 12.3044 6.18743 12.9853 7.57872C13.2746 8.15843 13.3087 8.37374 13.1555 8.63875C12.8321 9.23502 12.4916 9.03626 12.1172 8.07561C11.6746 6.86652 9.7682 6.41932 8.54265 7.21434C7.53838 7.86029 7.28306 8.60562 7.28306 10.9741C7.28306 12.7629 7.33412 13.1273 7.64051 13.6242C8.15116 14.4855 8.96819 14.9492 9.95544 14.9492C11.5725 14.9492 12.3044 14.1708 12.3044 12.4151C12.3044 11.9182 12.2533 11.4876 12.2023 11.4213C11.998 11.2226 11.0959 11.7029 10.6193 12.2495C10.2788 12.647 10.0235 12.8126 9.85331 12.7464C9.44479 12.5807 9.5299 12.1998 10.1086 11.5538C10.6363 10.9741 10.9767 10.8416 12.8661 10.4772C13.53 10.3447 14.0406 10.1129 14.4151 9.79816C15.0789 9.23502 15.5385 9.16876 15.5385 9.63253C15.5385 10.03 14.4491 10.9244 13.8193 11.0569C13.3768 11.1563 13.3597 11.206 13.2917 12.5145C13.2406 13.3261 13.1044 14.088 12.9342 14.4027C12.5257 15.1645 11.6235 15.6449 10.364 15.7277C8.59372 15.8768 7.38519 15.2805 6.65326 13.8892C6.3639 13.3592 6.27879 12.8292 6.2107 11.3716Z" fill="#287CB2"/></svg>';
							echo '<h4>' . esc_html__( 'Gutenberg', 'uichemy' ) . '</h4>';
						echo '</label>';
					echo '</div>';

				echo '</div>';
			echo '</div>';
		}

		/**
		 * Onbording Step 2
		 *
		 * @since 1.2.2
		 */
		public function uich_step2() {

			echo '<img src="' . esc_url( UICH_URL . 'assets/images/onbording/register-img.png' ) . '" alt="register-img" draggable="false"/>';

			echo '<div class="uichemy-content">';
				echo '<h1>' . esc_html__( 'Register to UiChemy', 'uichemy' ) . '</h1>';
				echo '<p>' . esc_html__( 'Create your account to get your UiChemy license key.', 'uichemy' ) . '</p>';
			echo '</div>';

			echo '<div class="uich-process-main">';

				echo '<img src="' . esc_url( UICH_URL . 'assets/images/onbording/dotted-line.png' ) . '" alt="line" id="register-dotted-image" />';

				echo '<div class="uich-process-col-3" style=" width: auto">';
					echo '<span>' . esc_html__( '1', 'uichemy' ) . '</span>';
					echo '<h4>' . esc_html__( 'Select your ', 'uichemy' ) . '<a href="https://uichemy.com/#pricing" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Plan', 'uichemy' ) . '</a></h4>';
				echo '</div>';

				echo '<div class="uich-process-col-3" style=" width: auto">';
					echo '<span>' . esc_html__( '2', 'uichemy' ) . '</span>';
					echo '<h4>' . esc_html__( 'Register at POSIMYTH Store', 'uichemy' ) . '</h4>';
				echo '</div>';

				echo '<div class="uich-process-col-3" style=" width: auto">';
					echo '<span>' . esc_html__( '3', 'uichemy' ) . '</span>';
					echo '<h4>' . esc_html__( 'Get the License Key', 'uichemy' ) . '</h4>';
				echo '</div>';
			echo '</div>';
		}

		/**
		 * Onbording Step 3
		 *
		 * @since 1.2.2
		 */
		public function uich_step3() {
			echo '<img src="' . esc_url( UICH_URL . 'assets/images/onbording/plugin-install.png' ) . '" alt="install plugin" draggable="false"/>';
			echo '<div class="uichemy-content">';
				echo '<h1>' . esc_html__( 'Install UiChemy Figma Plugin', 'uichemy' ) . '</h1>';
				echo '<p>' . esc_html__( 'Install the UiChemy Figma Plugin on your design board.', 'uichemy' ) . '</p>';
			echo '</div>';
			echo '<div class="uich-process-main">';
			echo '<img src="' . esc_url( UICH_URL . 'assets/images/onbording/dotted-line.png' ) . '" alt="line" id="plugin-dotted-image"/>';
				echo '<div class="uich-process-col-3" style=" width: 33.33% ">';
					echo '<span>' . esc_html__( '1', 'uichemy' ) . '</span>';
					echo '<h4>' . esc_html__( 'Install', 'uichemy' ) . ' <a href="https://www.figma.com/community/plugin/1265873702834050352" target="_blank" rel="noopener noreferrer">' . esc_html__( 'UiChemy Figma Plugin', 'uichemy' ) . '</a> ' . esc_html__( 'on', 'uichemy' ) . '<br> ' . esc_html__( 'Figma.com', 'uichemy' ) . '</h4>';
				echo '</div>';
				echo '<div class="uich-process-col-3" style=" width: 33.33% ">';
					echo '<span>' . esc_html__( '2', 'uichemy' ) . '</span>';
					echo '<h4>' . esc_html__( 'Install & Run UiChemy', 'uichemy' ) . '<br>' . esc_html__( 'Figma Plugin', 'uichemy' ) . '</h4>';
				echo '</div>';
				echo '<div class="uich-process-col-3" style=" width: 33.33% ">';
					echo '<span>' . esc_html__( '3', 'uichemy' ) . '</span>';
					echo '<h4>' . esc_html__( 'Paste the Key', 'uichemy' ) . '<br>' . esc_html__( 'and Activate', 'uichemy' ) . '</h4>';
				echo '</div>';
			echo '</div>';
		}

		/**
		 * Onbording Step 4
		 *
		 * @since 1.2.2
		 */
		public function uich_step4() {
			echo '<img src="' . esc_url( UICH_URL . 'assets/images/onbording/figma-connect.png' ) . '" alt="Figma plugin" draggable="false"/>';

			echo '<div class="uichemy-content">';
				echo '<h1>' . esc_html__( 'Connect Figma Plugin with WordPress', 'uichemy' ) . '</h1>';
			echo '</div>';

			echo '<div class="uich-process-main">';

				echo '<img src="' . esc_url( UICH_URL . 'assets/images/onbording/dotted-line.png' ) . '" alt="line" id="background-image"/>';

				echo '<div class="uich-process-col-3">';
					echo '<span>' . esc_html__( '1', 'uichemy' ) . '</span>';
					echo '<h4>' . esc_html__( 'Select the Design for Export', 'uichemy' ) . '</h4>';
				echo '</div>';

				echo '<div class="uich-process-col-3">';
					echo '<span>' . esc_html__( '2', 'uichemy' ) . '</span>';
					echo '<h4>' . esc_html__( 'Click on Live Preview', 'uichemy' ) . '</h4>';
					echo '<div class="uich-drop-arrow">';
						echo '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M16.3638 10.5303C16.6567 10.2374 16.6567 9.76256 16.3638 9.46967C16.0709 9.17678 15.5961 9.17678 15.3032 9.46967L10.75 14.0228L10.75 4.16699C10.75 3.75278 10.4142 3.41699 10 3.41699C9.58579 3.41699 9.25 3.75278 9.25 4.16699L9.25 14.0225L4.69716 9.46967C4.40427 9.17678 3.92939 9.17678 3.6365 9.46967C3.34361 9.76256 3.34361 10.2374 3.6365 10.5303L9.45376 16.3476C9.59054 16.4929 9.78467 16.5837 10 16.5837C10.2118 16.5837 10.4032 16.4958 10.5396 16.3546L16.3638 10.5303Z" fill="black"/></svg>';
						echo '<h4>' . esc_html__( 'Settings', 'uichemy' ) . '</h4>';
					echo '</div>';
				echo '</div>';

				echo '<div class="uich-process-col-3">';
					echo '<span>' . esc_html__( '3', 'uichemy' ) . '</span>';
					echo '<h4>' . esc_html__( 'Copy Site URL & Security Token from UiChemy WordPress Plugin', 'uichemy' ) . '</h4>';
				echo '</div>';

				echo '<div class="uich-process-col-3">';
					echo '<span>' . esc_html__( '4', 'uichemy' ) . '</span>';
					echo '<h4>' . esc_html__( 'Paste Site URL & Token from UiChemy WordPress Plugin', 'uichemy' ) . '</h4>';
				echo '</div>';

			echo '</div>';
		}

		/**
		 * Onbording Step 4
		 *
		 * @since 1.2.2
		 */
		public function uich_step5() {
			$flexbox_setting     = apply_filters( 'uich_recommended_settings', 'flexbox_container' );
			$flexbox_setting_val = ! empty( $flexbox_setting['data'] ) ? $flexbox_setting['data'] : '';

			$elementor_install         = apply_filters( 'uich_recommended_settings', 'elementor_install' );
			$elementor_install_success = ! empty( $elementor_install['success'] ) ? (bool) $elementor_install['success'] : false;

			$file_uploads     = apply_filters( 'uich_recommended_settings', 'enable_unfiltered_file_uploads' );
			$file_uploads_val = ! empty( $file_uploads['data'] ) ? $file_uploads['data'] : '';

			$svg_uploads = apply_filters( 'uich_recommended_settings', 'bricks_svg_uploads' );
			$briRole = ! empty( $svg_uploads['data'] ) ? $svg_uploads['data'] : '';

			$tpgb_install = apply_filters( 'uich_recommended_settings', 'tpgb_install' );
			$tpgb_install_success = ! empty( $tpgb_install['success'] ) ? (bool) $tpgb_install['success'] : false;

			echo '<img src="' . esc_url( UICH_URL . 'assets/images/onbording/bricks-svg.png' ) . '" class="uich-cover-bricks" alt="Preview-img" draggable="false" style="display:none" />';
			echo '<img src="' . esc_url( UICH_URL . 'assets/images/onbording/basic-requirements.png' ) . '" class="uich-cover-elementor" alt="Preview-img" draggable="false" style="display:none" />';
			echo '<img src="' . esc_url( UICH_URL . 'assets/images/onbording/tpgb-img.png' ) . '" class="uich-cover-gutenberg" alt="Preview-img" draggable="false" style="display:none" />';
			
			echo '<div class="uichemy-content">';
				echo '<h1>' . esc_html__( 'Basic Requirements', 'uichemy' ) . '</h1>';
				echo '<p>' . esc_html__( 'Please ensure the following things are set for best design conversion.', 'uichemy' ) . '</p>';
			echo '</div>';

			echo '<div class="uichemy-info">';

				echo '<div class="uich-box uich-page-elementor">';
				
					if ( ! empty( $elementor_install_success ) ) {
						echo '<div class="uich-tooltip uich-ob-success">';
							echo '<span>';
								echo '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.33366 2.5L3.75033 7.08333L1.66699 5" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>';
							echo '</span>';
							echo '<span id="uich-tooltip-txt">' . esc_html__( 'Active', 'uichemy' ) . '</span>';
						echo '</div>';
					}else{
						echo '<div class="uich-tooltip uich-error">';
							echo '<span>';
								echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.00016 1.33301C2.97512 1.33301 1.3335 2.97463 1.3335 4.99967C1.3335 7.02472 2.97512 8.66634 5.00016 8.66634C7.02521 8.66634 8.66683 7.02472 8.66683 4.99967C8.66683 2.97463 7.02521 1.33301 5.00016 1.33301ZM0.333496 4.99967C0.333496 2.42235 2.42283 0.333008 5.00016 0.333008C7.57749 0.333008 9.66683 2.42235 9.66683 4.99967C9.66683 7.577 7.57749 9.66634 5.00016 9.66634C2.42283 9.66634 0.333496 7.577 0.333496 4.99967ZM5 2.83301C5.27614 2.83301 5.5 3.05687 5.5 3.33301V4.99967C5.5 5.27582 5.27614 5.49967 5 5.49967C4.72386 5.49967 4.5 5.27582 4.5 4.99967V3.33301C4.5 3.05687 4.72386 2.83301 5 2.83301ZM5 6.16699C4.72386 6.16699 4.5 6.39085 4.5 6.66699C4.5 6.94313 4.72386 7.16699 5 7.16699H5.00417C5.28031 7.16699 5.50417 6.94313 5.50417 6.66699C5.50417 6.39085 5.28031 6.16699 5.00417 6.16699H5Z" fill="white"/></svg>';
							echo '</span>';
							echo '<span id="uich-tooltip-txt">' . esc_html__( 'Inactive', 'uichemy' ) . '</span>';
						echo '</div>';
					}
					
						echo '<h3>' . esc_html__( 'Elementor Page Builder', 'uichemy' ) . '</h3>';

					if ( ! empty( $elementor_install_success ) ) {
						echo '<div class="uich-info-btn uich-ob-active">' . esc_html__( 'No Action Needed', 'uichemy' ) . '</div>';
					} else {
						echo '<div class="uich-info-btn uich-success uich-onbording-elementor">' . esc_html__( 'Install & Activate', 'uichemy' ) . '</div>';
					}

				echo '</div>';

				echo '<div class="uich-box uich-page-elementor">';

					if ( ! empty( $flexbox_setting_val ) && 'active' === $flexbox_setting_val ) {
						echo '<div class="uich-tooltip uich-ob-success">';
							echo '<span>';
								echo '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.33366 2.5L3.75033 7.08333L1.66699 5" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>';
							echo '</span>';
							echo '<span id="uich-tooltip-txt">' . esc_html__( 'Active', 'uichemy' ) . '</span>';
						echo '</div>';
					}else{
						echo '<div class="uich-tooltip uich-error">';
							echo '<span>';
								echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.00016 1.33301C2.97512 1.33301 1.3335 2.97463 1.3335 4.99967C1.3335 7.02472 2.97512 8.66634 5.00016 8.66634C7.02521 8.66634 8.66683 7.02472 8.66683 4.99967C8.66683 2.97463 7.02521 1.33301 5.00016 1.33301ZM0.333496 4.99967C0.333496 2.42235 2.42283 0.333008 5.00016 0.333008C7.57749 0.333008 9.66683 2.42235 9.66683 4.99967C9.66683 7.577 7.57749 9.66634 5.00016 9.66634C2.42283 9.66634 0.333496 7.577 0.333496 4.99967ZM5 2.83301C5.27614 2.83301 5.5 3.05687 5.5 3.33301V4.99967C5.5 5.27582 5.27614 5.49967 5 5.49967C4.72386 5.49967 4.5 5.27582 4.5 4.99967V3.33301C4.5 3.05687 4.72386 2.83301 5 2.83301ZM5 6.16699C4.72386 6.16699 4.5 6.39085 4.5 6.66699C4.5 6.94313 4.72386 7.16699 5 7.16699H5.00417C5.28031 7.16699 5.50417 6.94313 5.50417 6.66699C5.50417 6.39085 5.28031 6.16699 5.00417 6.16699H5Z" fill="white"/></svg>';
							echo '</span>';
							echo '<span id="uich-tooltip-txt">' . esc_html__( 'Inactive', 'uichemy' ) . '</span>';
						echo '</div>';
					}

					echo '<h3>' . esc_html__( 'Flexbox (Container)', 'uichemy' ) . '</h3>';

					if ( ! empty( $flexbox_setting_val ) && 'active' === $flexbox_setting_val ) {
						echo '<div class="uich-info-btn uich-ob-active">' . esc_html__( 'No Action Needed', 'uichemy' ) . '</div>';
					} else {
						echo '<div class="uich-info-btn uich-success uich-onbording-fc">' . esc_html__( 'Activate', 'uichemy' ) . '</div>';
					}

				echo '</div>';

				echo '<div class="uich-box uich-page-elementor">';
				
					if ( ! empty( $file_uploads_val ) ) {
						echo '<div class="uich-tooltip uich-ob-success">';
							echo '<span>';
								echo '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.33366 2.5L3.75033 7.08333L1.66699 5" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>';
							echo '</span>';
							echo '<span id="uich-tooltip-txt">' . esc_html__( 'Active', 'uichemy' ) . '</span>';
						echo '</div>';
					}else{
						echo '<div class="uich-tooltip uich-error">';
							echo '<span>';
								echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.00016 1.33301C2.97512 1.33301 1.3335 2.97463 1.3335 4.99967C1.3335 7.02472 2.97512 8.66634 5.00016 8.66634C7.02521 8.66634 8.66683 7.02472 8.66683 4.99967C8.66683 2.97463 7.02521 1.33301 5.00016 1.33301ZM0.333496 4.99967C0.333496 2.42235 2.42283 0.333008 5.00016 0.333008C7.57749 0.333008 9.66683 2.42235 9.66683 4.99967C9.66683 7.577 7.57749 9.66634 5.00016 9.66634C2.42283 9.66634 0.333496 7.577 0.333496 4.99967ZM5 2.83301C5.27614 2.83301 5.5 3.05687 5.5 3.33301V4.99967C5.5 5.27582 5.27614 5.49967 5 5.49967C4.72386 5.49967 4.5 5.27582 4.5 4.99967V3.33301C4.5 3.05687 4.72386 2.83301 5 2.83301ZM5 6.16699C4.72386 6.16699 4.5 6.39085 4.5 6.66699C4.5 6.94313 4.72386 7.16699 5 7.16699H5.00417C5.28031 7.16699 5.50417 6.94313 5.50417 6.66699C5.50417 6.39085 5.28031 6.16699 5.00417 6.16699H5Z" fill="white"/></svg>';
							echo '</span>';
							echo '<span id="uich-tooltip-txt">' . esc_html__( 'Inactive', 'uichemy' ) . '</span>';
						echo '</div>';
					}

						echo '<h3>' . esc_html__( 'Enabled Unfiltered Uploads for SVG', 'uichemy' ) . '</h3>';

					if ( ! empty( $file_uploads_val ) ) {
						echo '<div class="uich-info-btn uich-ob-active">' . esc_html__( 'No Action Needed', 'uichemy' ) . '</div>';
					} else {
						echo '<div class="uich-info-btn uich-success uich-onbording-fu">'. esc_html__( 'Activate', 'uichemy' ) . '</div>';
					}

				echo '</div>';

				/** Bricks settings */
				echo '<div class="uich-box uich-page-bricks">';
					$themes = wp_get_themes();
					$theme_names = array_keys($themes);
					if( !in_array('bricks', $theme_names) ){
						echo '<div class="uich-tooltip uich-error">';
							echo '<span>';
								echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.00016 1.33301C2.97512 1.33301 1.3335 2.97463 1.3335 4.99967C1.3335 7.02472 2.97512 8.66634 5.00016 8.66634C7.02521 8.66634 8.66683 7.02472 8.66683 4.99967C8.66683 2.97463 7.02521 1.33301 5.00016 1.33301ZM0.333496 4.99967C0.333496 2.42235 2.42283 0.333008 5.00016 0.333008C7.57749 0.333008 9.66683 2.42235 9.66683 4.99967C9.66683 7.577 7.57749 9.66634 5.00016 9.66634C2.42283 9.66634 0.333496 7.577 0.333496 4.99967ZM5 2.83301C5.27614 2.83301 5.5 3.05687 5.5 3.33301V4.99967C5.5 5.27582 5.27614 5.49967 5 5.49967C4.72386 5.49967 4.5 5.27582 4.5 4.99967V3.33301C4.5 3.05687 4.72386 2.83301 5 2.83301ZM5 6.16699C4.72386 6.16699 4.5 6.39085 4.5 6.66699C4.5 6.94313 4.72386 7.16699 5 7.16699H5.00417C5.28031 7.16699 5.50417 6.94313 5.50417 6.66699C5.50417 6.39085 5.28031 6.16699 5.00417 6.16699H5Z" fill="white"/></svg>';
							echo '</span>';
							echo '<span id="uich-tooltip-txt">' . esc_html__( 'Inactive', 'uichemy' ) . '</span>';
						echo '</div>';
						echo '<h3>' . esc_html__( 'Bricks Page Builder', 'uichemy' ) . '</h3>';
						echo '<a target="_blank" rel="noopener noreferrer"  href="'.esc_url('https://bricksbuilder.io/pricing/').'" class="uich-info-btn uich-success" type="button">' . esc_html__( 'Download', 'uichemy' ) . '</a>';
					}else{
						$current_theme = wp_get_theme();
						$current_theme_name = $current_theme->get( 'Name' );
						
							if ( $current_theme->get( 'Name' ) == 'Bricks' ) {
								echo '<div class="uich-tooltip uich-ob-success">';
									echo '<span>';
										echo '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.33366 2.5L3.75033 7.08333L1.66699 5" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
									echo '</span>';
									echo '<span id="uich-tooltip-txt">' . esc_html__( 'Activate', 'uichemy' ) . '</span>';
								echo '</div>';
							}else{
								echo '<div class="uich-tooltip uich-error">';
									echo '<span>';
										echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.00016 1.33301C2.97512 1.33301 1.3335 2.97463 1.3335 4.99967C1.3335 7.02472 2.97512 8.66634 5.00016 8.66634C7.02521 8.66634 8.66683 7.02472 8.66683 4.99967C8.66683 2.97463 7.02521 1.33301 5.00016 1.33301ZM0.333496 4.99967C0.333496 2.42235 2.42283 0.333008 5.00016 0.333008C7.57749 0.333008 9.66683 2.42235 9.66683 4.99967C9.66683 7.577 7.57749 9.66634 5.00016 9.66634C2.42283 9.66634 0.333496 7.577 0.333496 4.99967ZM5 2.83301C5.27614 2.83301 5.5 3.05687 5.5 3.33301V4.99967C5.5 5.27582 5.27614 5.49967 5 5.49967C4.72386 5.49967 4.5 5.27582 4.5 4.99967V3.33301C4.5 3.05687 4.72386 2.83301 5 2.83301ZM5 6.16699C4.72386 6.16699 4.5 6.39085 4.5 6.66699C4.5 6.94313 4.72386 7.16699 5 7.16699H5.00417C5.28031 7.16699 5.50417 6.94313 5.50417 6.66699C5.50417 6.39085 5.28031 6.16699 5.00417 6.16699H5Z" fill="white"/></svg>';
									echo '</span>';
									echo '<span id="uich-tooltip-txt">' . esc_html__( 'Inactive', 'uichemy' ) . '</span>';
								echo '</div>';
							}

						echo '<h3>' . esc_html__( 'Bricks Page Builder', 'uichemy' ) . '</h3>';
						
						if ( $current_theme->get( 'Name' ) == 'Bricks' ) {
							echo '<button class="uich-info-btn uich-success uich-ob-active" type="button">' . esc_html__( 'No Action Needed', 'uichemy' ) . '</button>';
						}else{
							echo '<a target="_blank" rel="noopener noreferrer"  href="'.esc_url( admin_url( 'themes.php' ) ).'" class="uich-border-btn uich-activation-btn uich-activate-bricks" type="button">' . esc_html__( 'Activate', 'uichemy' ) . '</a>';
						}
					}
				echo '</div>';
				echo '<div class="uich-box uich-page-bricks">';
					if( isset($briRole) && empty($briRole) ){
						echo '<div class="uich-tooltip uich-error">';
							echo '<span>';
								echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.00016 1.33301C2.97512 1.33301 1.3335 2.97463 1.3335 4.99967C1.3335 7.02472 2.97512 8.66634 5.00016 8.66634C7.02521 8.66634 8.66683 7.02472 8.66683 4.99967C8.66683 2.97463 7.02521 1.33301 5.00016 1.33301ZM0.333496 4.99967C0.333496 2.42235 2.42283 0.333008 5.00016 0.333008C7.57749 0.333008 9.66683 2.42235 9.66683 4.99967C9.66683 7.577 7.57749 9.66634 5.00016 9.66634C2.42283 9.66634 0.333496 7.577 0.333496 4.99967ZM5 2.83301C5.27614 2.83301 5.5 3.05687 5.5 3.33301V4.99967C5.5 5.27582 5.27614 5.49967 5 5.49967C4.72386 5.49967 4.5 5.27582 4.5 4.99967V3.33301C4.5 3.05687 4.72386 2.83301 5 2.83301ZM5 6.16699C4.72386 6.16699 4.5 6.39085 4.5 6.66699C4.5 6.94313 4.72386 7.16699 5 7.16699H5.00417C5.28031 7.16699 5.50417 6.94313 5.50417 6.66699C5.50417 6.39085 5.28031 6.16699 5.00417 6.16699H5Z" fill="white"/></svg>';
							echo '</span>';
							echo '<span id="uich-tooltip-txt">' . esc_html__( 'Inactive', 'uichemy' ) . '</span>';
						echo '</div>';
						echo '<h3>' . esc_html__( 'SVG Uploads', 'uichemy' ) . '</h3>';
						echo '<button class="uich-info-btn uich-success uich-onbording-bricks">' . esc_html__( 'Activate', 'uichemy' ) . '</button>';
					}else{
						echo '<div class="uich-tooltip uich-ob-success">';
							echo '<span>';
								echo '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.33366 2.5L3.75033 7.08333L1.66699 5" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path></svg>';
							echo '</span>';
							echo '<span id="uich-tooltip-txt">' . esc_html__( 'Activate', 'uichemy' ) . '</span>';
						echo '</div>';
						echo '<h3>' . esc_html__( 'SVG Uploads', 'uichemy' ) . '</h3>';
						echo '<button class="uich-info-btn uich-success uich-onbording-bricks uich-ob-active" disabled="disabled">' . esc_html__( 'No Action Needed', 'uichemy' ) . '</button>';
					}
				echo '</div>';
				
				/** Gutenberg settings */
				echo '<div class="uich-box uich-page-gutenberg">';
					if ( ! empty( $tpgb_install_success ) ) {
						echo '<div class="uich-tooltip uich-ob-success">';
							echo '<span>';
								echo '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.33366 2.5L3.75033 7.08333L1.66699 5" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>';
							echo '</span>';
							echo '<span id="uich-tooltip-txt">' . esc_html__( 'Active', 'uichemy' ) . '</span>';
						echo '</div>';
					}else{
						echo '<div class="uich-tooltip uich-error">';
							echo '<span>';
								echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.00016 1.33301C2.97512 1.33301 1.3335 2.97463 1.3335 4.99967C1.3335 7.02472 2.97512 8.66634 5.00016 8.66634C7.02521 8.66634 8.66683 7.02472 8.66683 4.99967C8.66683 2.97463 7.02521 1.33301 5.00016 1.33301ZM0.333496 4.99967C0.333496 2.42235 2.42283 0.333008 5.00016 0.333008C7.57749 0.333008 9.66683 2.42235 9.66683 4.99967C9.66683 7.577 7.57749 9.66634 5.00016 9.66634C2.42283 9.66634 0.333496 7.577 0.333496 4.99967ZM5 2.83301C5.27614 2.83301 5.5 3.05687 5.5 3.33301V4.99967C5.5 5.27582 5.27614 5.49967 5 5.49967C4.72386 5.49967 4.5 5.27582 4.5 4.99967V3.33301C4.5 3.05687 4.72386 2.83301 5 2.83301ZM5 6.16699C4.72386 6.16699 4.5 6.39085 4.5 6.66699C4.5 6.94313 4.72386 7.16699 5 7.16699H5.00417C5.28031 7.16699 5.50417 6.94313 5.50417 6.66699C5.50417 6.39085 5.28031 6.16699 5.00417 6.16699H5Z" fill="white"/></svg>';
							echo '</span>';
							echo '<span id="uich-tooltip-txt">' . esc_html__( 'Inactive', 'uichemy' ) . '</span>';
						echo '</div>';
					}
					
						echo '<h3>' . esc_html__( 'The Plus Blocks For Block Editor', 'uichemy' ) . '</h3>';

					if ( ! empty( $tpgb_install_success ) ) {
						echo '<div class="uich-info-btn uich-ob-active">' . esc_html__( 'No Action Needed', 'uichemy' ) . '</div>';
					} else {
						echo '<div class="uich-info-btn uich-success uich-onbording-gutenberg">' . esc_html__( 'Install & Activate', 'uichemy' ) . '</div>';
					}
				echo '</div>';

			echo '</div>';
		}

		/**
		 * Onbording Step 4
		 *
		 * @since 1.2.2
		 */
		public function uich_step6() {
			echo '<img src="' . esc_url( UICH_URL . 'assets/images/onbording/live-preview.png' ) . '" alt="Preview-img"  draggable="false"/>';

			echo '<div class="uichemy-content">';
				echo '<h1>' . esc_html__( 'Congratulation!', 'uichemy' ) . '<br>' . esc_html__( 'UiChemy is Ready for Magic.', 'uichemy' ) . '</h1>';
				echo '<span>' . esc_html__( 'Still in Doubt?', 'uichemy' ) . '</span>';
			echo '</div>';

			echo '<div class="uichemy-list-item">';
				echo '<ul>';
					echo '<li id="first-item">';
						echo '<a href="https://uichemy.com/docs/onboarding-guide-uichemy/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Read Step-By-Step Docs', 'uichemy' ) . '</a>';
					echo '</li>';
					echo '<li>';
						echo '<a href="https://youtu.be/vm8Ak5Oy9AU?si=UC3-Yr9Vw4RQoMRO" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Watch Video Tutorials', 'uichemy' ) . '</a>';
					echo '</li>';
					echo '<li>';
						echo '<a href="https://uichemy.com/docs/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Read Documention', 'uichemy' ) . '</a>';
					echo '</li>';
					echo '<li>';
						echo '<a href="https://www.figma.com/community/file/1329383275066935195 " target="_blank" rel="noopener noreferrer">' . esc_html__( 'Figma Community Guidelines', 'uichemy' ) . '</a>';
					echo '</li>';
				echo '</ul>';
			echo '</div>';
		}

		/**
		 * On Boarding Data
		 *
		 * @since 1.2.2
		 */
		public function uich_boarding_store() {

			check_ajax_referer( 'uich_onboarding_nonce', 'nonce' );

			$server_software = ! empty( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

			$web_server         = $server_software;
			$memory_limit       = ini_get( 'memory_limit' );
			$max_execution_time = ini_get( 'max_execution_time' );
			$php_version        = phpversion();
			$wp_version         = get_bloginfo( 'version' );
			$email              = get_option( 'admin_email' );
			$siteurl            = get_option( 'siteurl' );
			$language           = get_bloginfo( 'language' );

			// Active Plugin Name.
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

			$plugin = wp_json_encode( $act_plugin );

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
				)
			);

			$existing_value = get_option( $this->uich_onbording_end );
			if ( false === $existing_value ) {
				add_option( $this->uich_onbording_end, true );
			}

			if ( is_wp_error( $response ) ) {
				$result = array(
					'success'     => false,
					'messages'    => 'Oops',
					'description' => 'Description',
				);

				wp_send_json( $result );
			} else {
				$status_one = wp_remote_retrieve_response_code( $response );

				if ( 200 === $status_one ) {
					$get_data_one = wp_remote_retrieve_body( $response );
					$get_res      = json_decode( json_decode( $get_data_one, true ), true );

					$result = array(
						'success'     => ! empty( $get_res['success'] ) ? $get_res['success'] : false,
						'messages'    => ! empty( $get_res['messages'] ) ? $get_res['messages'] : 'Successfully Completed!',
						'description' => ! empty( $get_res['description'] ) ? $get_res['description'] : 'Welcome to UiChemy! Your Onboarding finished successfully.',
					);

					wp_send_json( $result );
				} else {

					$result = array(
						'success'     => false,
						'messages'    => 'Oops',
						'description' => 'description',
					);

					wp_send_json( $result );
				}
			}

			wp_die();
		}

	}

	Uich_Onbording::instance();
}
