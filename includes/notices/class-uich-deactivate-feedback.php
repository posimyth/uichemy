<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    uichemy
 * @subpackage uichemy/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Uich_Deactivate_Feedback' ) ) {

	/**
	 * This class used for only load All Notice Files
	 *
	 * @since 1.0.0
	 */
	class Uich_Deactivate_Feedback {

		/**
		 * Member Variable
		 *
		 * @since 1.0.0
		 * @var MyType $instance This is a description.
		 */
		private static $instance;

		/**
		 * Member Variable
		 *
		 * @since 1.0.0
		 * @var string $btn_skip This is a description.
		 */
		private $btn_skip = 'https://api.posimyth.com/wp-json/uich/v2/uich_deactivate_user_count';

		/**
		 * Member Variable
		 *
		 * @since 1.0.0
		 * @var string $btn_deactivate This is a description.
		 */
		private $btn_deactivate = 'https://api.posimyth.com/wp-json/uich/v2/uich_deactivate_user_data';

		/**
		 *  Initiator
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'admin_footer', array( $this, 'uich_deactive_popup' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'uich_onboarding_assets' ) );

			add_action( 'wp_ajax_uich_deactive_plugin', array( $this, 'uich_deactive_plugin' ) );
			add_action( 'wp_ajax_uich_skip_deactivate', array( $this, 'uich_skip_deactivate' ) );
		}

		/**
		 * Popup Html Css Js
		 *
		 * @since 1.0.0
		 */
		public function uich_deactive_popup() {
			global $pagenow;

			if ( 'plugins.php' === $pagenow ) {
				$this->uich_deact_popup_html();
				$this->uich_deact_popup_js();
			}
		}

		/**
		 * Popup Html Code
		 *
		 * @since 1.0.0
		 */
		public function uich_deact_popup_html() {

			$site_url = home_url();
			$security = wp_create_nonce( 'uich-deactivate-feedback' );

			?>
			<div class="uich-modal" id="uich-deactive-modal">
				<div class="uich-modal-wrap">
				
					<div class="uich-modal-header">
						<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M40 0H0V40H40V0Z" fill="#4B22CC"/>
							<path d="M22.3001 26.1203C22.3001 26.1203 17.6796 26.3816 17.6796 22.8857V13.9714C17.6796 13.1215 17.5093 12.2799 17.1782 11.4947C16.8472 10.7094 16.362 9.99598 15.7503 9.39503C15.1386 8.79409 14.4125 8.31744 13.6133 7.99228C12.8142 7.66713 11.9576 7.49985 11.0927 7.5H7.5V25.2676C7.5 27.1766 8.27175 29.0074 9.64548 30.3573C11.0192 31.7071 12.8824 32.4655 14.8251 32.4655H14.8352C15.3322 32.5115 15.8325 32.5115 16.3296 32.4655H25.078C27.0464 32.4655 28.9343 31.6971 30.3262 30.3294C31.7181 28.9617 32.5 27.1066 32.5 25.1724V20.7518H22.3238L22.3001 26.1203ZM23.2016 21.6144H31.6142V25.1669C31.6116 26.8695 30.9221 28.5016 29.6968 29.7055C28.4716 30.9094 26.8107 31.5869 25.078 31.5896H20.9331C21.3748 31.2416 21.7615 30.8311 22.0803 30.3715C23.063 28.9452 23.1892 27.3495 23.1892 26.1214L23.2016 21.6144Z" fill="white"/>
							<path d="M28.2425 7.5C26.6714 7.5 25.1647 8.11317 24.0537 9.20465C22.9427 10.2961 22.3184 11.7766 22.3181 13.3203V16.8107H32.4978V7.5H28.2425Z" fill="white"/>
						</svg>				
						<span class="uich-feed-head-title">
							<?php echo esc_html__( 'Quick Feedback', 'uichemy' ); ?>
						</span>
					</div>

					<div class="uich-modal-body">
						<h3 class="uich-feed-caption">
							<?php echo esc_html__( "If you have a moment, please let us know why you're deactivating Uichemy :", 'uichemy' ); ?>
						</h3>

						<form class="uich-feedback-dialog-form" method="post">

							<input type="hidden" name="site_url" value="<?php echo esc_url( $site_url ); ?>" />
							<input type="hidden" name="nonce" value="<?php echo esc_attr( $security ); ?>" />

							<div class="uich-modal-input">
								<?php
									$reson_data = array(
										array(
											'reason' => __( 'This is a temporary deactivation.', 'uichemy' ),
										),
										array(
											'reason' => __( 'Facing technical issues/bugs with the plugin.', 'uichemy' ),
										),
										array(
											'reason' => __( 'Performance Issues.', 'uichemy' ),
										),
										array(
											'reason' => __( 'Found an alternative plugin.', 'uichemy' ),
										),
										array(
											'reason' => __( 'No more planning to use.', 'uichemy' ),
										),
										array(
											'reason' => __( 'Dont want to use any wordpress plugin.', 'uichemy' ),
										),
										array(
											'reason' => __( 'Its missing the feature i require.', 'uichemy' ),
										),
										array(
											'reason' => __( 'Other', 'uichemy' ),
										),
									);

									foreach ( $reson_data as $key => $value ) {
										?>
										<div>
											<label class="uich-relist">
												<input type="radio" class="uich-radion-input" <?php echo 0 === $key ? 'checked="checked"' : ''; ?> id="<?php echo 'details-' . esc_attr( $key ); ?>" name="uich-reason-txt" value="<?php echo esc_attr( $value['reason'] ); ?>">
												<div class="uich-reason-txt-text"><?php echo esc_html( $value['reason'] ); ?></div>
											</label>
										</div>
								<?php } ?>
							</div>

							<textarea name="uich-reason-txt-deails" placeholder="<?php echo esc_html__( 'Please share the reason', 'uichemy' ); ?>" class="uich-reason-txt-deails"></textarea>
						</form>
					</div>

					<div class="uich-modal-footer">
						<a class="uich-modal-submit uich-btn uich-btn-primary" href="#">
							<?php echo esc_html__( 'Submit & Deactivate', 'uichemy' ); ?>
						</a>
						<a class="uich-modal-deactive" href="#">
							<?php echo esc_html__( 'Skip & Deactivate', 'uichemy' ); ?>
						</a>
					</div>
						
					<div class="uich-help-link">
						<span>
							<?php echo esc_html__( 'If you require any help , ', 'uichemy' ); ?>

							<a href="<?php echo esc_url( 'https://wordpress.org/support/plugin/uichemy' ); ?>" target="_blank" rel="noopener noreferrer"> 
								<?php echo esc_html__( 'please add a ticket ', 'uichemy' ); ?> </a> . 
								<?php echo esc_html__( 'We reply within 24 working hours.', 'uichemy' ); ?></span>

								<span> <?php echo esc_html__( 'Read' ); ?> 

								<a href="<?php echo esc_url( 'https://uichemy.com/docs/?utm_source=wpbackend&utm_medium=admin&utm_campaign=links' ); ?>" target="_blank" rel="noopener noreferrer">

								<?php echo esc_html__( 'Documentation.', 'uichemy' ); ?>   
							</a> 
						</span> 
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Call Css File here.
		 *
		 * @since 1.0.0
		 * @param page $page api code number.
		 */
		public function uich_onboarding_assets( $page ) {
			if ( 'plugins.php' === $page ) {
				wp_enqueue_style( 'uich-deactive-style', UICH_URL . 'assets/css/uich-deactive.css', array(), UICH_VERSION, 'all' );
			}
		}

		/**
		 * Call Ajax and js code here.
		 *
		 * @since 1.0.0
		 */
		public function uich_deact_popup_js() {
			?>
			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
					'use strict';

					// Modal Radio Input Click Action
					$('.uich-modal-input input[type=radio]').on( 'change', function(e) {
						$('.uich-reason-txt-deails').removeClass('uich-active');
						$('.uich-modal-input').find( '.'+$(this).attr('id') ).addClass('uich-active');
					});

					// Modal Cancel Click Action
					$( document ).on( 'click', '#uich-deactive-modal', function(e) {
						if ( e.target === this ) {
							$(this).removeClass('modal-active');
						}
					});

					// Deactivate Button Click Action
					$( document ).on( 'click', '#deactivate-uichemy', function(e) {
						e.preventDefault();
						$( '#uich-deactive-modal' ).addClass( 'modal-active' );
						$( '.uich-modal-deactive' ).attr( 'href', $(this).attr('href') );
						$( '.uich-modal-submit' ).attr( 'href', $(this).attr('href') );
					});

					// Submit to Remote Server
					$( document ).on( 'click', '.uich-modal-submit', function(e) {
						e.preventDefault();
						const url = $(this).attr('href');
						
						$(this).text('').addClass('uich-loading');

						let formObj = $( '#uich-deactive-modal' ).find('form.uich-feedback-dialog-form'),
							queryString = formObj.serialize(),
							formData = new URLSearchParams(queryString);

						var ajaxData = {
							action: 'uich_deactive_plugin',
							deactreson : formData.get('uich-reason-txt'),
							nonce : formData.get('nonce'),
							site_url : formData.get('site_url'),
						}
						
						if( formData.get('uich-reason-txt-deails') && formData.get('uich-reason-txt-deails') != '' ){
							ajaxData.tprestxt = formData.get('uich-reason-txt-deails');
						}
							
						$.ajax({
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							type: 'POST',
							data: ajaxData,
							success: function (data) {
								if(data.deactivated){
									$( '#uich-deactive-modal' ).removeClass( 'modal-active' );
									window.location.href = url;
								}
							},
							error: function(xhr) {
								console.log( 'Error occured. Please try again' + xhr.statusText + xhr.responseText );
							},
						});

					});

					$( document ).on( 'click', '.uich-modal-deactive', function(e) {
						e.preventDefault();
						const url = $(this).attr('href');

						let formObj = $( '#uich-deactive-modal' ).find('form.uich-feedback-dialog-form'),
							queryString = formObj.serialize(),
							formData = new URLSearchParams(queryString);

							$.ajax({
								url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
								type: 'POST',
								data: {
									action: 'uich_skip_deactivate',
									nonce: formData.get('nonce'),
								},
								success: function (data) {
									window.location.href = url;
								},
								error: function(xhr) {
									console.log( 'Error occured. Please try again' + xhr.statusText + xhr.responseText );
								},
							});
					})
				});
			</script>
			<?php
		}

		/**
		 * Deactive Plugin API Call
		 *
		 * @since 1.0.0
		 */
		public function uich_deactive_plugin() {
			$nonce = ! empty( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

			if ( ! isset( $nonce ) || empty( $nonce ) || ! wp_verify_nonce( $nonce, 'uich-deactivate-feedback' ) ) {
				die( 'Security checked!' );
			}

			$site_url   = ! empty( $_POST['site_url'] ) ? sanitize_text_field( wp_unslash( $_POST['site_url'] ) ) : '';
			$deactreson = ! empty( $_POST['deactreson'] ) ? sanitize_text_field( wp_unslash( $_POST['deactreson'] ) ) : '';

			$tprestxt = isset( $_POST['tprestxt'] ) && ! empty( $_POST['tprestxt'] ) ? sanitize_text_field( wp_unslash( $_POST['tprestxt'] ) ) : '';

			$api_params = array(
				'site_url'    => $site_url,
				'reason_key'  => $deactreson,
				'reason_text' => $tprestxt,
				'version'     => UICH_VERSION,
			);

			$response = wp_remote_post(
				$this->btn_deactivate,
				array(
					'timeout'   => 30,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			if ( is_wp_error( $response ) ) {
				wp_send_json( array( 'deactivated' => false ) );
			} else {
				wp_send_json( array( 'deactivated' => true ) );
			}

			wp_die();
		}

		/**
		 * Deactive Plugin API Call
		 *
		 * @since 1.0.0
		 */
		public function uich_skip_deactivate() {

			check_ajax_referer( 'uich-deactivate-feedback', 'nonce' );

			$response = wp_remote_post(
				$this->btn_skip,
				array(
					'body'    => array(),
					'headers' => array(
						'Content-Type' => 'application/x-www-form-urlencoded',
					),
				)
			);

			wp_die();
		}
	}

	Uich_Deactivate_Feedback::get_instance();
}
