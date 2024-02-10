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

echo '<div class="uich-container-main">';

	require_once UICH_PATH . 'includes/pages/design-header.php';

	echo '<div class="uich-welcome-section container">';
		echo '<div class="uich-left-text">';
			echo '<h1 class="uich-heading">Welcome, Benjamin Lang!</h1>';
			echo '<p class="uich-paragraph">Convert your Figma Designs to 100% Editable Elementor Templates in Seconds.</p>';
			echo '<div class="uich-btn-group">';
				echo '<button type="button" class="uich-yellow-btn">Install Figma Plugin</button>';
				echo '<a class="uich-quick-link" href="#" target="_blank" rel="noopener noreferrer">Learn More</a>';
			echo '</div>';
		echo '</div>';
		echo '<div class="uich-right-img">';
			echo '<img src="' . esc_url( UICH_URL ) . 'assets/images/fig-elementor-updated.png"/>';
		echo '</div>';
	echo '</div>';

	echo '<div class="uich-how-does-work-section uich-container">';
		echo '<div class="uich-points-section">';
			echo '<div class="uich-heading-strip">';
				echo '<h2 class="uich-heading">How does it Work?</h2>';
				echo '<a class="uich-icon-btn" href="https://www.youtube.com/watch?v=vm8Ak5Oy9AU" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-youtube"></i>Watch Video</a>';
			echo '</div>';
			echo '<ol>';
				echo '<li>Install UiChemy Figma Plugin</li>';
				echo '<li>Export Template from Figma</li>';
				echo '<li>Import in Elementor</li>';
			echo '</ol>';
		echo '</div>';

		echo '<div class="uich-activate-section uich-api-deactive">';
			echo '<div class="uich-cover">';
				echo '<div class="uich-icon-box">';
					echo '<svg width="51" height="50" viewBox="0 0 51 50" fill="none" xmlns="http://www.w3.org/2000/svg"> x<rect x="0.5" width="50" height="50" rx="25" fill="#E6F6E8"/><rect x="10.5" y="10" width="30" height="30" rx="15" fill="#00A31B"/><path d="M32 20L23.0625 29L19 24.9091" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
				echo '</div>';
				echo '<p class="uich-text">Security Token Generated</p>';
				echo '<div class="uich-active-deactive">';
					$url = menu_page_url( 'uichemy-welcome', false );
					echo '<a href="' . esc_url( $url ) . '" class="uich-btn" type="button">Get Token</a>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="uich-quick-link-cards container">';
		echo '<div class="uich-card-main">';
			echo '<h4 class="uich-heading">Documentation</h4>';
			echo '<button type="button" class="uich-border-btn">Read Now</button>';
		echo '</div>';

		echo '<div class="uich-card-main">';
			echo '<h4 class="uich-heading">Need Help?</h4>';
			echo '<button type="button" class="uich-border-btn">Raise Ticket</button>';
		echo '</div>';

		echo '<div class="uich-card-main">';
			echo '<h4 class="uich-heading">Join Our Community</h4>';
			echo '<button type="button" class="uich-border-btn">Join Now</button>';
		echo '</div>';

		echo '<div class="uich-card-main">';
			echo '<h4 class="uich-heading">YouTube Tutorials</h4>';
			echo '<button type="button" class="uich-border-btn">Watch Now</button>';
		echo '</div>';
	echo '</div>';

	echo '<div class="uich-welcome-section uich-templates-section container">';
		echo '<div class="uich-left-text">';
			echo '<h1 class="uich-heading">UiChemy Templates</h1>';
			echo '<p class="uich-paragraph">We`re offering a Lifetime Deal for a limited time only. Afterward, we will switch to only offering Yearly Plans. </p>';
			echo '<div class="uich-btn-group">';
				echo '<button type="button" class="uich-yellow-btn">Template Library</button>';
			echo '</div>';
		echo '</div>';
		echo '<div class="uich-right-img">';
			echo '<img src="' . esc_url( UICH_URL ) . 'assets/images/templates-right-img-updated.png"/>';
		echo '</div>';
	echo '</div>';

	echo '<div class="uich-faqs-section container">';
		echo '<div class="uich-left-section">';
			echo '<div class="uich-heading-para">';
				echo '<h2 class="uich-heading">Frequently Asked Questions.</h2>';
				echo '<p class="uich-text"> For any further help, reach us at <a href="#">Helpdesk</a> or connect via livechat on website.</p>';
			echo '</div>';

			echo '<div class="uich-bottom-section">';
				echo '<div class="uich-social-icons">';
					echo '<a href="https://www.facebook.com/uichemy/" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-facebook"></i></a>';
					echo '<a href="https://twitter.com/i/flow/login?redirect_after_login=%2Fuichemy" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-x-twitter"></i></a>';
					echo '<a href="https://www.instagram.com/uichemyhq/" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-instagram"></i></a>';
					echo '<a href="https://www.pinterest.com/posimyth/" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-pinterest"></i></a>';
				echo '</div>';
				echo '<p class="uich-text">© POSIMYTH Innovations</p>';
			echo '</div>';
		echo '</div>';

		echo '<div class="uich-right-section">';
			echo '<div class="uich-container">';
				echo '<div class="uich-accordion-area">';
					echo '<div class="uich-accordion-box">';
						echo '<h5 class="uich-acc-trigger">How does UiChemy work? <i class="fa-solid fa-minus" aria-hidden="true"></i></h5>';
						echo '<div class="uich-acc-container">';
							echo '<p>UiChemy works by integrating directly with Figma and Elementor. Once you have designed your website layout in Figma, you can export a template JSON or push the file directly using our UiChemy WordPress plugin. It’s a simple 3-step process just follow <a href="https://uichemy.com/help/design-guidelines/" target="_blank" rel="noopener noreferrer">our design guidelines in the docs</a>. To get started, <a href="https://uichemy.com/help/getting-started/" target="_blank" rel="noopener noreferrer">please check our documentation</a>.</p>';
						echo '</div>';
					echo '</div>';

					echo '<div class="uich-accordion-box">';
						echo '<h5 class="uich-acc-trigger">Is UiChemy a standalone software or a plugin? <i class="fa-solid fa-plus" aria-hidden="true"></i></h5>';
						echo '<div class="uich-acc-container">';
							echo '<p>Uichemy is a <a href="https://www.figma.com/community/plugin/1265873702834050352" target="_blank" rel="noopener noreferrer">Figma Plugin</a> that you need to install in Figma. From there, you can export the design in a template JSON or install';
							echo 'the UiChemy WordPress plugin to directly push the design from Figma to your WordPress site.</p>';
						echo '</div>';
					echo '</div>';

					echo '<div class="uich-accordion-box">';
						echo '<h5 class="uich-acc-trigger">Does UiChemy require any coding knowledge? <i class="fa-solid fa-plus" aria-hidden="true"></i></h5>';
						echo '<div class="uich-acc-container">';
							echo '<p>';
								echo 'Not at all. UiChemy does not require you to know any lines of code. It works seamlessly between both platforms all you have to do is plug in and export your designs.';
							echo '</p>';
						echo '</div>';
					echo '</div>';

					echo '<div class="uich-accordion-box">';
						echo '<h5 class="uich-acc-trigger">How can I get support or assistance with UiChemy? <i class="fa-solid fa-plus" aria-hidden="true"></i></h5>';
						echo '<div class="uich-acc-container">';
							echo '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';

echo '</div>';

echo '<div></div>';
