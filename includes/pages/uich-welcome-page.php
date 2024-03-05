<?php
/**
 * The file add design for welcome page
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Uichemy
 */

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$flexbox_setting     = apply_filters( 'uich_recommended_settings', 'flexbox_container' );
$flexbox_setting_val = ! empty( $flexbox_setting['data'] ) ? $flexbox_setting['data'] : '';

$elementor_install         = apply_filters( 'uich_recommended_settings', 'elementor_install' );
$elementor_install_success = ! empty( $elementor_install['success'] ) ? (bool) $elementor_install['success'] : false;

$file_uploads     = apply_filters( 'uich_recommended_settings', 'enable_unfiltered_file_uploads' );
$file_uploads_val = ! empty( $file_uploads['data'] ) ? $file_uploads['data'] : '';

$user = wp_get_current_user();

echo '<div class="uich-container-main">';

require_once UICH_PATH . 'includes/pages/design-header.php';

echo '<div class="uich-welcome-section container">';

	echo '<div class="uich-left-text">';

		echo '<h1 class="uich-heading">' . esc_html__( 'Welcome, ', 'uichemy' ) . esc_html( $user->display_name ) . '</h1>';
		echo '<p class="uich-paragraph">' . esc_html__( 'Convert your Figma Designs to 100% Editable Elementor Templates in Seconds.', 'uichemy' ) . '</p>';

		echo '<div class="uich-btn-group">';
			echo '<a target="_blank" rel="noopener noreferrer" href="https://www.figma.com/community/plugin/1265873702834050352" class="uich-yellow-btn">' . esc_html__( 'Elementor Figma Plugin', 'uichemy' ) . '</a>';
			echo '<a target="_blank" rel="noopener noreferrer" href="https://www.figma.com/community/plugin/1344313361212431142" class="uich-yellow-btn">' . esc_html__( 'Bricks Figma Plugin', 'uichemy' ) . '</a>';
			// echo '<a class="uich-quick-link" href="https://uichemy.com/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Learn More', 'uichemy' ) . '</a>';
		echo '</div>';

	echo '</div>';

echo '<div class="uich-right-img">';
	echo '<img src="' . esc_url( UICH_URL ) . 'assets/images/fig-elementor-updated.png" draggable="false"/>';
echo '</div>';

echo '</div>';

echo '<div class="uich-info-boxes-section-cover">';

echo '<div class="uich-how-does-work-section uich-container">';
echo '<div class="uich-heading-strip">';
	echo '<h2 class="uich-heading">' . esc_html__( 'How does it work?', 'uichemy' ) . '</h2><a class="uich-icon-btn" href="https://www.youtube.com/watch?v=vm8Ak5Oy9AU" target="_blank" rel="noopener noreferrer"><svg width="18" height="18" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.0225 1.66043C17.3021 1.94001 17.5035 2.28793 17.6068 2.66957C18.2014 5.06317 18.0641 8.84361 17.6183 11.3296C17.515 11.7113 17.3136 12.0592 17.034 12.3388C16.7544 12.6183 16.4065 12.8198 16.0248 12.9231C14.6277 13.3041 9.00453 13.3041 9.00453 13.3041C9.00453 13.3041 3.38125 13.3041 1.98408 12.9231C1.60245 12.8198 1.25453 12.6183 0.974969 12.3388C0.69541 12.0592 0.493965 11.7113 0.390695 11.3296C-0.207459 8.94643 -0.0435205 5.16368 0.37909 2.68118C0.48235 2.29953 0.68379 1.95158 0.96335 1.672C1.24291 1.39241 1.59084 1.19094 1.97248 1.08765C3.36965 0.706602 8.99307 0.695068 8.99307 0.695068C8.99307 0.695068 14.6162 0.695068 16.0134 1.07611C16.395 1.1794 16.7429 1.38086 17.0225 1.66043ZM11.868 6.99978L7.20312 9.7017V4.29785L11.868 6.99978Z" fill="#FF0000"/></svg>' . esc_html__( 'Watch Video', 'uichemy' ) . '</a>';
echo '</div>';

echo '<div class="uich-process-steps-cover">';
		echo '<div class="uich-img-block-info">';
			echo '<h4 class="uich-info-sm-heading">' . esc_html__( 'Step 1', 'uichemy' ) . '</h4>';
			echo '<h3 class="uich-info-md-heading">' . esc_html__( 'Install UiChemy Figma Plugin', 'uichemy' ) . '</h3>';
			echo '<div class="uich-img-box">';
				echo '<img src="' . esc_url( UICH_URL . 'assets/images/welcome_page/step-1.png' ) . '" draggable="false">';
			echo '</div>';
		echo '</div>';

		echo '<div class="uich-img-block-info">';
			echo '<h4 class="uich-info-sm-heading">' . esc_html__( 'Step 2', 'uichemy' ) . '</h4>';
			echo '<h3 class="uich-info-md-heading">Connect to <a href="https://uichemy.com/docs/what-is-live-import/" class="uich-mid-color">Live Import</a> using Security Token</h3>';
			echo '<div class="uich-img-box">';
				echo '<img src="' . esc_url( UICH_URL . 'assets/images/welcome_page/step-2.png' ) . '" draggable="false">';
			echo '</div>';
		echo '</div>';

		echo '<div class="uich-img-block-info">';
			echo '<h4 class="uich-info-sm-heading">' . esc_html__( 'Step 3', 'uichemy' ) . '</h4>';
			echo '<h3 class="uich-info-md-heading">' . esc_html__( 'Import in Elementor', 'uichemy' ) . '</h3>';
			echo '<div class="uich-img-box">';
			echo '<img src="' . esc_url( UICH_URL . 'assets/images/welcome_page/step-3.png' ) . '" draggable="false">';
			echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';

		echo '<div class="uich-right-info-boxes">';
	echo '<div class="uich-recommended-settings">';
		echo '<h2 class="uich-heading">Recommended Settings</h2>';
		echo '<ul>';
			echo '<li class="uich-listing-strip">';
			
				echo '<div class="uich-item-name">';

					echo '<h4>' . esc_html__('Elementor Page Builder', 'uichemy') . ' </h4>';

					if ( ! empty( $elementor_install_success ) ) {
						echo '<div class="uich-sm-icon">';
							echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 0C7.76142 0 10 2.23858 10 5C10 7.76142 7.76142 10 5 10C2.23858 10 0 7.76142 0 5C0 2.23858 2.23858 0 5 0ZM7.52014 3.68435C7.71473 3.48841 7.71362 3.17183 7.51768 2.97725C7.32174 2.78267 7.00516 2.78377 6.81058 2.97971L4.1862 5.62245L3.18681 4.61608C2.99223 4.42014 2.67565 4.41904 2.47971 4.61362C2.28377 4.8082 2.28267 5.12478 2.47725 5.32072L3.83142 6.68435C3.92529 6.77887 4.05299 6.83203 4.1862 6.83203C4.31941 6.83203 4.44712 6.77887 4.54099 6.68435L7.52014 3.68435Z" fill="#00A31B"/></svg>';
							echo '<span class="uich-activation-setting-tootip">' . esc_html__('Active', 'uichemy') . '</span>';
						echo '</div>';
					}else{
						echo '<div class="uich-sm-icon">';
							echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 0C2.24 0 0 2.24 0 5C0 7.76 2.24 10 5 10C7.76 10 10 7.76 10 5C10 2.24 7.76 0 5 0ZM5 8C4.63333 8 4.33333 7.7 4.33333 7.33333C4.33333 6.96667 4.63333 6.66667 5 6.66667C5.36667 6.66667 5.66667 6.96667 5.66667 7.33333C5.66667 7.7 5.36667 8 5 8ZM5.76334 2.82999L5.54 5.50334C5.51667 5.78334 5.28333 6 5 6C4.71667 6 4.48333 5.78334 4.46 5.50334L4.23666 2.82999C4.2 2.38334 4.54999 2 5 2C5.42667 2 5.76667 2.34667 5.76667 2.76667C5.76667 2.78667 5.76667 2.80999 5.76334 2.82999Z" fill="#FF1E1E"/></svg>';
							echo '<span class="uich-activation-setting-tootip">' . esc_html__('Inactive', 'uichemy') . '</span>';
						echo '</div>';
					}

				echo '</div>';

				if ( ! empty( $elementor_install_success ) ) {
					echo '<button class="uich-border-btn" type="button">' . esc_html__( 'No Action Needed', 'uichemy' ) . '</button>';
				}else{
					echo '<button class="uich-border-btn uich-activation-btn uich-install-elementor" type="button">' . esc_html__( 'Install & Activate', 'uichemy' ) . '</button>';
				}
	
			echo '</li>';
			echo '<li class="uich-listing-strip">';
			
				echo '<div class="uich-item-name">';
				echo '<h4>' .esc_html__('Flexbox Container', 'uichemy').' </h4>';

				if ( ! empty( $flexbox_setting_val ) && 'active' === $flexbox_setting_val ) {
					echo '<div class="uich-sm-icon">';
						echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 0C7.76142 0 10 2.23858 10 5C10 7.76142 7.76142 10 5 10C2.23858 10 0 7.76142 0 5C0 2.23858 2.23858 0 5 0ZM7.52014 3.68435C7.71473 3.48841 7.71362 3.17183 7.51768 2.97725C7.32174 2.78267 7.00516 2.78377 6.81058 2.97971L4.1862 5.62245L3.18681 4.61608C2.99223 4.42014 2.67565 4.41904 2.47971 4.61362C2.28377 4.8082 2.28267 5.12478 2.47725 5.32072L3.83142 6.68435C3.92529 6.77887 4.05299 6.83203 4.1862 6.83203C4.31941 6.83203 4.44712 6.77887 4.54099 6.68435L7.52014 3.68435Z" fill="#00A31B"/></svg>';
						echo '<span class="uich-activation-setting-tootip">' . esc_html__('Active', 'uichemy') . '</span>';
					echo '</div>';
				}else{
					echo '<div class="uich-sm-icon">';
						echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 0C2.24 0 0 2.24 0 5C0 7.76 2.24 10 5 10C7.76 10 10 7.76 10 5C10 2.24 7.76 0 5 0ZM5 8C4.63333 8 4.33333 7.7 4.33333 7.33333C4.33333 6.96667 4.63333 6.66667 5 6.66667C5.36667 6.66667 5.66667 6.96667 5.66667 7.33333C5.66667 7.7 5.36667 8 5 8ZM5.76334 2.82999L5.54 5.50334C5.51667 5.78334 5.28333 6 5 6C4.71667 6 4.48333 5.78334 4.46 5.50334L4.23666 2.82999C4.2 2.38334 4.54999 2 5 2C5.42667 2 5.76667 2.34667 5.76667 2.76667C5.76667 2.78667 5.76667 2.80999 5.76334 2.82999Z" fill="#FF1E1E"/></svg>';
						echo '<span class="uich-activation-setting-tootip">' . esc_html__('Inactive', 'uichemy') . '</span>';
					echo '</div>';
				}
				
				echo'</div>';

				if ( ! empty( $flexbox_setting_val ) && 'active' === $flexbox_setting_val ) {
					echo '<button class="uich-border-btn" type="button">' . esc_html__( 'No Action Needed', 'uichemy' ) . '</button>';
				}else{
					echo '<button class="uich-border-btn uich-activation-btn uich-active-flexboxcontainer" type="button">' . esc_html__( 'Install & Activate', 'uichemy' ) . '</button>';
				}

			echo '</li>';
			echo '<li class="uich-listing-strip">';
			
				echo '<div class="uich-item-name">';
				echo '<h4>' .esc_html__('Enable Unfiltered File Uploads', 'uichemy').' </h4>';

				if ( ! empty( $file_uploads_val ) ) {
					echo '<div class="uich-sm-icon">';
						echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 0C7.76142 0 10 2.23858 10 5C10 7.76142 7.76142 10 5 10C2.23858 10 0 7.76142 0 5C0 2.23858 2.23858 0 5 0ZM7.52014 3.68435C7.71473 3.48841 7.71362 3.17183 7.51768 2.97725C7.32174 2.78267 7.00516 2.78377 6.81058 2.97971L4.1862 5.62245L3.18681 4.61608C2.99223 4.42014 2.67565 4.41904 2.47971 4.61362C2.28377 4.8082 2.28267 5.12478 2.47725 5.32072L3.83142 6.68435C3.92529 6.77887 4.05299 6.83203 4.1862 6.83203C4.31941 6.83203 4.44712 6.77887 4.54099 6.68435L7.52014 3.68435Z" fill="#00A31B"/></svg>';
						echo '<span class="uich-activation-setting-tootip">' . esc_html__('Active', 'uichemy') . '</span>';
					echo '</div>';
				}else{
					echo '<div class="uich-sm-icon">';
						echo '<svg width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 0C2.24 0 0 2.24 0 5C0 7.76 2.24 10 5 10C7.76 10 10 7.76 10 5C10 2.24 7.76 0 5 0ZM5 8C4.63333 8 4.33333 7.7 4.33333 7.33333C4.33333 6.96667 4.63333 6.66667 5 6.66667C5.36667 6.66667 5.66667 6.96667 5.66667 7.33333C5.66667 7.7 5.36667 8 5 8ZM5.76334 2.82999L5.54 5.50334C5.51667 5.78334 5.28333 6 5 6C4.71667 6 4.48333 5.78334 4.46 5.50334L4.23666 2.82999C4.2 2.38334 4.54999 2 5 2C5.42667 2 5.76667 2.34667 5.76667 2.76667C5.76667 2.78667 5.76667 2.80999 5.76334 2.82999Z" fill="#FF1E1E"/></svg>';
						echo '<span class="uich-activation-setting-tootip">' . esc_html__('Inactive', 'uichemy') . '</span>';
					echo '</div>';
				}
				
				echo'</div>';

				if ( ! empty( $file_uploads_val ) ) {
					echo '<button class="uich-border-btn" type="button">' . esc_html__( 'No Action Needed', 'uichemy' ) . '</button>';
				}else{
					echo '<button class="uich-border-btn uich-activation-btn uich-active-fileuploads" type="button">' . esc_html__( 'Install & Activate', 'uichemy' ) . '</button>';
				}

		echo '</ul>';
	echo '</div>';

	echo '<div class="uich-activate-section uich-api-deactive">';
	echo '<div class="uich-cover">';
	echo '<div class="uich-icon-box">';
		echo '<svg width="51" height="50" viewBox="0 0 51 50" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="0.5" width="50" height="50" rx="25" fill="#E6F6E8" /><rect x="10.5" y="10" width="30" height="30" rx="15" fill="#00A31B" /> <path d="M32 20L23.0625 29L19 24.9091" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>';
	echo '</div>';
	echo '<p class="uich-text">Security Token Generated</p>';
	echo '<div class="uich-active-deactive">';
		$url = menu_page_url( 'uichemy-settings', false );
		echo '<a target="_blank" rel="noopener noreferrer" href="' . esc_url( $url ) . '" class="uich-btn">Get Security Token</a>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';

echo '<div class="uich-quick-link-cards">';
echo '<div class="uich-card-main">';
echo '<h4 class="uich-heading">' . esc_html__( 'Documentation', 'uichemy' ) . '</h4>';
echo '<a target="_blank" rel="noopener noreferrer" href="https://uichemy.com/docs/" class="uich-card-btn">' . esc_html__( 'Read Now', 'uichemy' ) .'</a>';
echo '</div>';

echo '<div class="uich-card-main">';
echo '<h4 class="uich-heading">' . esc_html__( 'Design Guidelines', 'uichemy' ) . '</h4>';
echo '<a target="_blank" rel="noopener noreferrer" href="https://uichemy.com/help/design-guidelines/" class="uich-card-btn">' . esc_html__( 'Learn More', 'uichemy' ) .'</a>';
echo '</div>';

echo '<div class="uich-card-main">';
echo '<h4 class="uich-heading">' . esc_html__( 'Join Our Community?', 'uichemy' ) . '</h4>';
echo '<a target="_blank" rel="noopener noreferrer" href="https://www.facebook.com/uichemy/" class="uich-card-btn">' . esc_html__( 'Join Now', 'uichemy' ) .'</a>';
echo '</div>';

echo '<div class="uich-card-main">';
echo '<h4 class="uich-heading">' . esc_html__( 'YouTube Tutorials', 'uichemy' ) . '</h4>';
echo '<a target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/watch?v=m2R0Qo0ax4Y&list=PLFRO-irWzXaJ00ay82qZZ2T4etPCPh7er&pp=iAQB" class="uich-card-btn">' . esc_html__( 'Watch Now', 'uichemy' ) .'</a>';
echo '</div>';
echo '</div>';


echo '<div class="uich-cards-section">';

	echo '<div class="uich-sec">';
		echo '<div class="uich-sec-cover">';
			echo '<h2 class="uich-heading">' . esc_html__( 'UiChemy Templates', 'uichemy' ) . '</h2>';
			echo '<p class="uich-paragraph">' . esc_html__( 'We are offering a Lifetime Deal for a limited time only. Afterward, we will switch to only offering Yearly Plans. Take advantage of our offer to lock in your Lifetime Deal forever for unlimited conversions.', 'uichemy' ) . '</p>';
			echo '<a target="_blank" href="https://uichemy.com/templates-library/" class="uich-yellow-btn uich-white-btn">' . esc_html__( 'Template Library', 'uichemy' ) . '</a>';
		echo '</div>';
		echo '<img src="' . esc_url( UICH_URL . 'assets/images/welcome_page/uichemy-templates.png' ) . '" draggable="false">';
	echo '</div>';

	echo '<div class="uich-sec">';

	echo '<div class="uich-sec-cover">';
		echo '<h2 class="uich-heading">' . esc_html__( 'Case Study', 'uichemy' ) . '</h2>';
		echo '<p class="uich-paragraph">' . esc_html__( 'We are offering a Lifetime Deal for a limited time only. Afterward, we will switch to only offering Yearly Plans.', 'uichemy' ) . '</p>';
		echo '<a target="_blank" rel="noopener noreferrer" href="https://uichemy.com/case-study-templates/" class="uich-yellow-btn uich-white-btn">' . esc_html__( 'Browse Case Studies', 'uichemy' ) . '</a>';
	echo '</div>';

	echo '<img src="' . esc_url( UICH_URL . 'assets/images/welcome_page/case-study.png' ) . '" draggable="false">';

echo '</div>';

echo '</div>';

require_once UICH_PATH . 'includes/pages/uich-footer.php';


echo '<div></div>';
