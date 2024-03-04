<?php
/**
 * It is Main File to load all Notice, Upgrade Menu and all
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
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

if ( ! class_exists( 'Uich_Toast_Popup' ) ) {

	/**
	 * This class used for only load All Notice Files
	 *
	 * @since 1.0.0
	 */
	class Uich_Toast_Popup {

		/**
		 * Instance
		 *
		 * @since 1.0.0
		 * @access private
		 * @static
		 * @var instance of the class.
		 */
		private static $instance = null;


		/**
		 * Singleton Instance Creation Method.
		 *
		 * This public static method ensures that only one instance of the class is loaded or can be loaded.
		 * It follows the Singleton design pattern to create or return the existing instance of the class.
		 *
		 * @since 1.0.0
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
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->uich_toast_popup_feedback();
		}

		/**
		 * Check if the Current Screen is Related to Plugin Management.
		 *
		 * @since 1.0.0
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
		 * @since 1.0.0
		 */
		public function uich_toast_popup_feedback() {
			add_action(
				'current_screen',
				function () {

					if ( ! $this->uich__plugins_screen() ) {
						return;
					}

					add_action( 'admin_footer', array( $this, 'uich_toast_popup' ) );
				}
			);
		}

		/**
		 * Perform some compatibility checks to make sure basic requirements are meet.
		 *
		 * @since 1.0.0
		 */
		public function uich_toast_popup() {
			echo '<div class="uich_toast">';

				echo '<div class="uich_toast-content">';

					echo '<svg style="display:none" class="uich-success-svg" width="39" height="38" viewBox="0 0 39 38" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M37.3731 22.4881L37.3614 22.4939C35.9903 23.5454 35.4094 25.3347 35.8974 26.9905L35.9032 27.0021C36.6816 29.6338 34.753 32.2887 32.0108 32.3585H31.9992C30.268 32.4049 28.7459 33.5087 28.1707 35.1412V35.147C27.2527 37.738 24.1273 38.7546 21.8674 37.1918C20.4641 36.2336 18.6091 36.1836 17.1327 37.1918H17.1269C14.867 38.7488 11.7415 37.7379 10.8294 35.1411C10.2491 33.5046 8.72922 32.4048 7.00094 32.3584H6.9893C4.24727 32.2886 2.31844 29.6338 3.09695 27.0021L3.10273 26.9904C3.59063 25.3346 3.00969 23.5453 1.63875 22.4938L1.62711 22.4881C-0.551484 20.8149 -0.551484 17.5384 1.62711 15.8653L1.63875 15.8595C3.00969 14.808 3.59063 13.0186 3.09695 11.3629V11.3513C2.31258 8.71963 4.24719 6.06463 6.9893 5.99494H7.00094C8.72633 5.94846 10.2542 4.84463 10.8294 3.21221V3.20642C11.7414 0.615409 14.867 -0.401232 17.1269 1.16158H17.1327C18.5559 2.14338 20.4382 2.14338 21.8674 1.16158C24.1501 -0.414747 27.2581 0.630956 28.1707 3.20642V3.21221C28.7459 4.83885 30.2679 5.94853 31.9992 5.99494H32.0108C34.7529 6.06463 36.6816 8.71963 35.9032 11.3513L35.8974 11.3629C35.4094 13.0186 35.9903 14.808 37.3614 15.8595L37.3731 15.8653C39.5516 17.5384 39.5516 20.8149 37.3731 22.4881Z" fill="#3EB655"/><path d="M19.5004 29.9987C25.477 29.9987 30.322 25.1537 30.322 19.1771C30.322 13.2005 25.477 8.35547 19.5004 8.35547C13.5237 8.35547 8.67871 13.2005 8.67871 19.1771C8.67871 25.1537 13.5237 29.9987 19.5004 29.9987Z" fill="#8BD399"/><path opacity="0.1" d="M27.8093 12.2486C25.9375 10.7087 23.5428 9.7832 20.9321 9.7832C14.9556 9.7832 10.1079 14.6309 10.1079 20.6074C10.1079 23.218 11.0335 25.6128 12.5732 27.4846C10.195 25.5008 8.68018 22.5166 8.68018 19.1755C8.68018 13.1989 13.5237 8.35547 19.5003 8.35547C22.8413 8.35547 25.8255 9.87031 27.8093 12.2486Z" fill="black"/><path d="M16.9257 23.4117L14.5327 20.8658C13.906 20.1989 13.9383 19.1504 14.6049 18.5237C15.2716 17.8962 16.3206 17.9298 16.9467 18.5962L18.0891 19.8111L22.9448 14.2612C23.5467 13.5725 24.5935 13.5026 25.2828 14.1054C25.9716 14.7081 26.0411 15.7547 25.4387 16.4434L19.3797 23.368C18.7342 24.105 17.5951 24.1243 16.9257 23.4117Z" fill="white"/></svg>';
					echo '<svg style="display:none" class="uich-error-svg" width="39" height="38" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.96273 4.14568L3.14055 14.2293C3.03538 14.4114 2.98 14.6179 2.97998 14.8282C2.97996 15.0385 3.0353 15.2451 3.14044 15.4272C3.24557 15.6093 3.3968 15.7605 3.57891 15.8657C3.76102 15.9708 3.96761 16.0262 4.17789 16.0261H15.8213C16.0316 16.0262 16.2382 15.9708 16.4203 15.8657C16.6024 15.7605 16.7536 15.6093 16.8588 15.4272C16.9639 15.2451 17.0193 15.0385 17.0192 14.8282C17.0192 14.6179 16.9638 14.4114 16.8587 14.2293L11.0371 4.14568C10.932 3.96362 10.7808 3.81244 10.5987 3.70732C10.4167 3.60221 10.2102 3.54688 9.99992 3.54688C9.78969 3.54688 9.58317 3.60221 9.40111 3.70732C9.21904 3.81244 9.06785 3.96362 8.96273 4.14568Z" fill="#EE404C"/><path d="M10.076 7.25391H9.9241C9.55019 7.25391 9.24707 7.55702 9.24707 7.93094V11.167C9.24707 11.5409 9.55019 11.8441 9.9241 11.8441H10.076C10.4499 11.8441 10.753 11.5409 10.753 11.167V7.93094C10.753 7.55702 10.4499 7.25391 10.076 7.25391Z" fill="#FFF7ED"/><path d="M10 14.4122C10.4159 14.4122 10.753 14.0751 10.753 13.6592C10.753 13.2434 10.4159 12.9062 10 12.9062C9.58419 12.9062 9.24707 13.2434 9.24707 13.6592C9.24707 14.0751 9.58419 14.4122 10 14.4122Z" fill="#FFF7ED"/></svg>';

					echo '<div class="uich_message">';
						echo '<span class="uich_text uich_text-1">Success</span>';
						echo '<span class="uich_text uich_text-2">Your changes has been saved</span>';
					echo '</div>';

				echo '</div>';

				echo '<span class="uich_close"><svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.40189 13.8824C1.6628 14.131 2.01579 14.2707 2.38371 14.2707C2.75164 14.2707 3.10464 14.131 3.36554 13.8824L8.29597 9.08935L13.2264 13.8824C13.5894 14.2307 14.1189 14.3665 14.6151 14.2392C15.1113 14.1117 15.4991 13.7401 15.6319 13.2645C15.7648 12.7888 15.6231 12.2812 15.2598 11.9332L10.2597 7.20698L15.2598 2.48073C15.5669 2.1372 15.6713 1.66762 15.5375 1.2338C15.4037 0.799975 15.0495 0.460448 14.5969 0.332167C14.1441 0.203875 13.6545 0.304005 13.2959 0.598359L8.29596 5.32461L3.36553 0.598359C3.00716 0.304005 2.51729 0.203875 2.06473 0.332167C1.61217 0.46046 1.25797 0.799963 1.12415 1.2338C0.990316 1.66762 1.09477 2.1372 1.40184 2.48073L6.33227 7.20698L1.40184 11.9332C1.12065 12.1856 0.960938 12.5385 0.960938 12.9077C0.960938 13.2772 1.12065 13.6298 1.40184 13.8824L1.40189 13.8824Z" fill="#19191B"/></svg></span>';

				echo '<div class="uich_progress"></div>';

			echo '</div>';

			?>
				<script>
					var closeIcon = document.querySelector(".uich_close");

					var timer1, timer2;

					function Toast_message( type='', title='Success', description='' ) {
						var toast = document.querySelector(".uich_toast"),
							progress = document.querySelector(".uich_progress");
						
							if( type ){
								toast.querySelector('.uich_text-1').innerHTML = title;

								toast.querySelector('.uich-success-svg').style.display = 'flex';
								toast.querySelector('.uich-error-svg').style.display = 'none';
							}else{
								toast.querySelector('.uich_text-1').innerHTML = 'Oops';

								toast.querySelector('.uich-success-svg').style.display = 'none';
								toast.querySelector('.uich-error-svg').style.display = 'flex';
							}

							if( toast.querySelectorAll('.uich_text-2').length > 0 ){
								toast.querySelector('.uich_text-2').innerHTML = description;
							}

							toast.classList.add("active");
							progress.classList.add("active");

							timer1 = setTimeout(() => {
								toast.classList.remove("active");
							}, 5000);

							timer2 = setTimeout(() => {
								progress.classList.remove("active");
							}, 5300);    
					}

					closeIcon.addEventListener("click", () => {
						var toast = document.querySelector(".uich_toast");
							progress = document.querySelector(".uich_progress");
							
							toast.classList.remove("active");

							setTimeout(() => {
								progress.classList.remove("active");
							}, 300);

							clearTimeout(timer1);
							clearTimeout(timer2);
					});
				</script>
			<?php
		}
	}

	Uich_Toast_Popup::instance();
}
