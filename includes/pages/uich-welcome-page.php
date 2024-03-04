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
			echo '<a target="_blank" rel="noopener noreferrer" href="https://www.figma.com/community/plugin/1265873702834050352" class="uich-yellow-btn">' . esc_html__( 'Figma Plugin', 'uichemy' ) . '</a>';
			echo '<a class="uich-quick-link" href="https://uichemy.com/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Learn More', 'uichemy' ) . '</a>';
		echo '</div>';

	echo '</div>';

echo '<div class="uich-right-img">';
	echo '<img src="' . esc_url( UICH_URL ) . 'assets/images/fig-elementor-updated.png" draggable="false"/>';
echo '</div>';

echo '</div>';

echo '<div class="uich-info-boxes-section-cover">';

echo '<div class="uich-how-does-work-section uich-container">';
echo '<div class="uich-heading-strip">';
	echo '<h2 class="uich-heading">' . esc_html__( 'How does it Work?', 'uichemy' ) . '</h2><a class="uich-icon-btn" href="https://www.youtube.com/watch?v=vm8Ak5Oy9AU" target="_blank" rel="noopener noreferrer"><svg width="18" height="18" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.0225 1.66043C17.3021 1.94001 17.5035 2.28793 17.6068 2.66957C18.2014 5.06317 18.0641 8.84361 17.6183 11.3296C17.515 11.7113 17.3136 12.0592 17.034 12.3388C16.7544 12.6183 16.4065 12.8198 16.0248 12.9231C14.6277 13.3041 9.00453 13.3041 9.00453 13.3041C9.00453 13.3041 3.38125 13.3041 1.98408 12.9231C1.60245 12.8198 1.25453 12.6183 0.974969 12.3388C0.69541 12.0592 0.493965 11.7113 0.390695 11.3296C-0.207459 8.94643 -0.0435205 5.16368 0.37909 2.68118C0.48235 2.29953 0.68379 1.95158 0.96335 1.672C1.24291 1.39241 1.59084 1.19094 1.97248 1.08765C3.36965 0.706602 8.99307 0.695068 8.99307 0.695068C8.99307 0.695068 14.6162 0.695068 16.0134 1.07611C16.395 1.1794 16.7429 1.38086 17.0225 1.66043ZM11.868 6.99978L7.20312 9.7017V4.29785L11.868 6.99978Z" fill="#FF0000"/></svg>' . esc_html__( 'Watch Video', 'uichemy' ) . '</a>';
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
			echo '<h3 class="uich-info-md-heading">Connect to <span class="uich-mid-color">Live Preview</span> using Security Token</h3>';
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
					echo '<button class="uich-border-btn uich-activation-btn" type="button">' . esc_html__( 'Active Now', 'uichemy' ) . '</button>';
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
					echo '<button class="uich-border-btn uich-activation-btn" type="button">' . esc_html__( 'Active Now', 'uichemy' ) . '</button>';
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
					echo '<button class="uich-border-btn uich-activation-btn" type="button">' . esc_html__( 'Active Now', 'uichemy' ) . '</button>';
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
echo '<a target="_blank" rel="noopener noreferrer" href="https://uichemy.com/docs/" class="uich-border-btn">Read Now</a>';
echo '</div>';

echo '<div class="uich-card-main">';
echo '<h4 class="uich-heading">' . esc_html__( 'Design Guidelines', 'uichemy' ) . '</h4>';
echo '<a target="_blank" rel="noopener noreferrer" href="https://uichemy.com/help/design-guidelines/" class="uich-border-btn">Learn More</a>';
echo '</div>';

echo '<div class="uich-card-main">';
echo '<h4 class="uich-heading">' . esc_html__( 'Join Our Community?', 'uichemy' ) . '</h4>';
echo '<a target="_blank" rel="noopener noreferrer" href="https://www.facebook.com/uichemy/" class="uich-border-btn">Join Now</a>';
echo '</div>';

echo '<div class="uich-card-main">';
echo '<h4 class="uich-heading">' . esc_html__( 'YouTube Tutorials', 'uichemy' ) . '</h4>';
echo '<a target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/watch?v=m2R0Qo0ax4Y&list=PLFRO-irWzXaJ00ay82qZZ2T4etPCPh7er&pp=iAQB" class="uich-border-btn">Watch Now</a>';
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

echo '<div class="uich-faqs-section">';
	echo '<div class="uich-left-section">';

		echo '<div class="uich-heading-para">';
			echo '<h2 class="uich-heading">' . esc_html__( 'Frequently Asked Questions.', 'uichemy' ) . '</h2>';
			echo '<p class="uich-text"> For any further help, reach us at <a href="https://store.posimyth.com/helpdesk/" target="_blank" rel="noopener noreferrer">Helpdesk</a> or connect via livechat on website.</p>';
		echo '</div>';

		echo '<div class="uich-bottom-section">';

			echo '<div class="uich-social-icons">';
				echo '<a class="uich-social-icons-link" href="https://www.facebook.com/uichemy/" target="_blank" rel="noopener noreferrer"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.3447 11.1068L14.863 7.69417H11.621V5.48058C11.621 4.54672 12.0731 3.63592 13.5251 3.63592H15V0.730583C15 0.730583 13.6621 0.5 12.3836 0.5C9.71233 0.5 7.96804 2.13483 7.96804 5.0932V7.69417H5V11.1068H7.96804V19.357C8.56393 19.4516 9.17352 19.5 9.79452 19.5C10.4155 19.5 11.0251 19.4516 11.621 19.357V11.1068H14.3447Z" fill="#4B22CC"/></svg></a>';
				echo '<a class="uich-social-icons-link" href="https://twitter.com/i/flow/login?redirect_after_login=%2Fuichemy" target="_blank" rel="noopener noreferrer"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.7508 0.959961H18.8175L12.1175 8.6183L20 19.0375H13.8283L8.995 12.7175L3.46333 19.0375H0.395L7.56167 10.8458L0 0.960794H6.32833L10.6975 6.73746L15.7508 0.959961ZM14.675 17.2025H16.3742L5.405 2.69913H3.58167L14.675 17.2025Z" fill="#4B22CC"/></svg></a>';
				echo '<a class="uich-social-icons-link" href="https://www.instagram.com/uichemyhq/" target="_blank" rel="noopener noreferrer"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 1.80078C12.6719 1.80078 12.9883 1.8125 14.0391 1.85937C15.0156 1.90234 15.543 2.06641 15.8945 2.20312C16.3594 2.38281 16.6953 2.60156 17.043 2.94922C17.3945 3.30078 17.6094 3.63281 17.7891 4.09766C17.9258 4.44922 18.0898 4.98047 18.1328 5.95312C18.1797 7.00781 18.1914 7.32422 18.1914 9.99219C18.1914 12.6641 18.1797 12.9805 18.1328 14.0312C18.0898 15.0078 17.9258 15.5352 17.7891 15.8867C17.6094 16.3516 17.3906 16.6875 17.043 17.0352C16.6914 17.3867 16.3594 17.6016 15.8945 17.7812C15.543 17.918 15.0117 18.082 14.0391 18.125C12.9844 18.1719 12.668 18.1836 10 18.1836C7.32812 18.1836 7.01172 18.1719 5.96094 18.125C4.98438 18.082 4.45703 17.918 4.10547 17.7812C3.64062 17.6016 3.30469 17.3828 2.95703 17.0352C2.60547 16.6836 2.39062 16.3516 2.21094 15.8867C2.07422 15.5352 1.91016 15.0039 1.86719 14.0312C1.82031 12.9766 1.80859 12.6602 1.80859 9.99219C1.80859 7.32031 1.82031 7.00391 1.86719 5.95312C1.91016 4.97656 2.07422 4.44922 2.21094 4.09766C2.39062 3.63281 2.60937 3.29687 2.95703 2.94922C3.30859 2.59766 3.64062 2.38281 4.10547 2.20312C4.45703 2.06641 4.98828 1.90234 5.96094 1.85937C7.01172 1.8125 7.32812 1.80078 10 1.80078ZM10 0C7.28516 0 6.94531 0.0117187 5.87891 0.0585937C4.81641 0.105469 4.08594 0.277344 3.45312 0.523437C2.79297 0.78125 2.23438 1.12109 1.67969 1.67969C1.12109 2.23437 0.78125 2.79297 0.523437 3.44922C0.277344 4.08594 0.105469 4.8125 0.0585938 5.875C0.0117188 6.94531 0 7.28516 0 10C0 12.7148 0.0117188 13.0547 0.0585938 14.1211C0.105469 15.1836 0.277344 15.9141 0.523437 16.5469C0.78125 17.207 1.12109 17.7656 1.67969 18.3203C2.23438 18.875 2.79297 19.2187 3.44922 19.4727C4.08594 19.7187 4.8125 19.8906 5.875 19.9375C6.94141 19.9844 7.28125 19.9961 9.99609 19.9961C12.7109 19.9961 13.0508 19.9844 14.1172 19.9375C15.1797 19.8906 15.9102 19.7187 16.543 19.4727C17.1992 19.2187 17.7578 18.875 18.3125 18.3203C18.8672 17.7656 19.2109 17.207 19.4648 16.5508C19.7109 15.9141 19.8828 15.1875 19.9297 14.125C19.9766 13.0586 19.9883 12.7187 19.9883 10.0039C19.9883 7.28906 19.9766 6.94922 19.9297 5.88281C19.8828 4.82031 19.7109 4.08984 19.4648 3.45703C19.2187 2.79297 18.8789 2.23437 18.3203 1.67969C17.7656 1.125 17.207 0.78125 16.5508 0.527344C15.9141 0.28125 15.1875 0.109375 14.125 0.0625C13.0547 0.0117188 12.7148 0 10 0Z" fill="#4B22CC"/><path d="M10 4.86328C7.16406 4.86328 4.86328 7.16406 4.86328 10C4.86328 12.8359 7.16406 15.1367 10 15.1367C12.8359 15.1367 15.1367 12.8359 15.1367 10C15.1367 7.16406 12.8359 4.86328 10 4.86328ZM10 13.332C8.16016 13.332 6.66797 11.8398 6.66797 10C6.66797 8.16016 8.16016 6.66797 10 6.66797C11.8398 6.66797 13.332 8.16016 13.332 10C13.332 11.8398 11.8398 13.332 10 13.332Z" fill="#4B22CC"/><path d="M16.5391 4.66016C16.5391 5.32422 16 5.85938 15.3398 5.85938C14.6758 5.85938 14.1406 5.32031 14.1406 4.66016C14.1406 3.99609 14.6797 3.46094 15.3398 3.46094C16 3.46094 16.5391 4 16.5391 4.66016Z" fill="#4B22CC"/></svg></a>';
				echo '<a class="uich-social-icons-link" href="https://www.pinterest.com/posimyth/" target="_blank" rel="noopener noreferrer"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 0C4.47656 0 0 4.47656 0 10C0 14.2383 2.63672 17.8555 6.35547 19.3125C6.26953 18.5195 6.1875 17.3086 6.39062 16.4453C6.57422 15.6641 7.5625 11.4766 7.5625 11.4766C7.5625 11.4766 7.26172 10.8789 7.26172 9.99219C7.26172 8.60156 8.06641 7.5625 9.07031 7.5625C9.92187 7.5625 10.3359 8.20312 10.3359 8.97266C10.3359 9.83203 9.78906 11.1133 9.50781 12.3008C9.27344 13.2969 10.0078 14.1094 10.9883 14.1094C12.7656 14.1094 14.1328 12.2344 14.1328 9.53125C14.1328 7.13672 12.4141 5.46094 9.95703 5.46094C7.11328 5.46094 5.44141 7.59375 5.44141 9.80078C5.44141 10.6602 5.77344 11.582 6.1875 12.082C6.26953 12.1797 6.28125 12.2695 6.25781 12.3672C6.18359 12.6836 6.01172 13.3633 5.98047 13.5C5.9375 13.6836 5.83594 13.7227 5.64453 13.6328C4.39453 13.0508 3.61328 11.2266 3.61328 9.75781C3.61328 6.60156 5.90625 3.70703 10.2188 3.70703C13.6875 3.70703 16.3828 6.17969 16.3828 9.48438C16.3828 12.9297 14.2109 15.7031 11.1953 15.7031C10.1836 15.7031 9.23047 15.1758 8.90234 14.5547C8.90234 14.5547 8.40234 16.4648 8.28125 16.9336C8.05469 17.8008 7.44531 18.8906 7.03906 19.5547C7.97656 19.8438 8.96875 20 10 20C15.5234 20 20 15.5234 20 10C20 4.47656 15.5234 0 10 0Z" fill="#4B22CC"/></svg></a>';
			echo '</div>';

			echo '<p class="uich-text">' . esc_html__( '© POSIMYTH Innovations', 'uichemy' ) . '</p>';
		echo '</div>';

	echo '</div>';

	echo '<div class="uich-right-section">';

	echo '<div class="uich-container">';

		echo '<div class="uich-accordion-area">';

			echo '<div class="uich-accordion-box">';

				echo '<h5 class="uich-acc-trigger">' . esc_html__( 'How does UiChemy work?', 'uichemy' );
				echo '<span>';
					echo '<svg class="uich-minus-icon" width="15" height="15" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19" stroke="#020202" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
				echo '</span>';
				echo '</h5>';

				echo '<div class="uich-acc-container">';
					echo '<p>UiChemy works by integrating directly with Figma and Elementor. Once you have designed your website layout in Figma, you can export a template JSON or push the file directly using our UiChemy WordPress plugin. It’s a simple 3-step process just follow <a href="https://uichemy.com/help/design-guidelines/" target="_blank" rel="noopener noreferrer">our design guidelines in the docs</a>. To get started, <a href="https://uichemy.com/help/getting-started/" target="_blank" rel="noopener noreferrer">please check our documentation</a>.</p>';
				echo '</div>';
				echo '</div>';

				echo '<div class="uich-accordion-box">';
				echo '<h5 class="uich-acc-trigger">' . esc_html__( 'Is UiChemy a standalone software or a plugin?', 'uichemy' );
				echo '<span>';
					echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5V19" stroke="#020202" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 12H19" stroke="#020202" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
				echo '</span>';
				echo '</h5>';

				echo '<div class="uich-acc-container">';
					echo '<p>Uichemy is a <a href="https://www.figma.com/community/plugin/1265873702834050352" target="_blank" rel="noopener noreferrer">Figma Plugin</a> that you need to install in Figma. From there, you can export the design in a template JSON or install the UiChemy WordPress plugin to directly push the design from Figma to your WordPress site.</p>';
				echo '</div>';
				echo '</div>';

				echo '<div class="uich-accordion-box">';
				echo '<h5 class="uich-acc-trigger">' . esc_html__( 'Does UiChemy require any coding knowledge?', 'uichemy' );
				echo '<span>';
					echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5V19" stroke="#020202" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 12H19" stroke="#020202" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
				echo '</span>';
				echo '</h5>';

				echo '<div class="uich-acc-container">';
					echo '<p> Not at all. UiChemy does not require you to know any lines of code. It works seamlessly between both platforms all you have to do is plug in and export your designs. </p>';
				echo '</div>';
				echo '</div>';

				echo '<div class="uich-accordion-box">';
				echo '<h5 class="uich-acc-trigger">' . esc_html__( 'How can I get support or assistance with UiChemy?', 'uichemy' );
				echo '<span>';
					echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5V19" stroke="#020202" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 12H19" stroke="#020202" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
				echo '</span>';
				echo '</h5>';

				echo '<div class="uich-acc-container">';
				echo '<p>You can reach us at the uichemy <a href="https://store.posimyth.com/helpdesk/" target="_blank" rel="noopener noreferrer">helpdesk</a>.</p>';
				echo '</div>';

			echo '</div>';

		echo '</div>';

	echo '</div>';

	echo '</div>';

	echo '</div>';

echo '</div>';

echo '<div></div>';
