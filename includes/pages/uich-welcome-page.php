<?php

$output = '';

$output .= '<div class="uich-container-main">';

    require_once UICH_PATH . 'includes/pages/design-header.php';

    $output .= '<div class="uich-welcome-section container">';
        $output .= '<div class="uich-left-text">';
            $output .= '<h1 class="uich-heading">Welcome, Benjamin Lang!</h1>';
            $output .= '<p class="uich-paragraph">Convert your Figma Designs to 100% Editable Elementor Templates in Seconds.</p>';
            $output .= '<div class="uich-btn-group">';
                $output .= '<button type="button" class="uich-yellow-btn">Install Figma Plugin</button>';
                $output .= '<a class="uich-quick-link" href="#" target="_blank" rel="noopener noreferrer">Learn More</a>';
            $output .= '</div>';
        $output .= '</div>';
        $output .= '<div class="uich-right-img">';
            $output .= '<img src="'. esc_url(UICH_URL) .'assets/svg/fig-elementor-img.svg"/>';
        $output .= '</div>';
    $output .= '</div>';

    $output .= '<div class="uich-how-does-work-section uich-container">';
        $output .= '<div class="uich-points-section">';
            $output .= '<div class="uich-heading-strip">';
                $output .= '<h2 class="uich-heading">How does it Work?</h2>';
                $output .= '<a class="uich-icon-btn" href="https://www.youtube.com/watch?v=vm8Ak5Oy9AU" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-youtube"></i>Watch Video</a>';
            $output .= '</div>';
            $output .= '<ol>';
                $output .= '<li>Install UiChemy Figma Plugin</li>';
                $output .= '<li>Export Template from Figma</li>';
                $output .= '<li>Import in Elementor</li>';
            $output .= '</ol>';
        $output .= '</div>';

        $output .= '<div class="uich-activate-section uich-api-deactive">';
            $output .= '<div class="uich-cover">';
                $output .= '<div class="uich-icon-box">';
                    $output .= '<svg width="51" height="50" viewBox="0 0 51 50" fill="none" xmlns="http://www.w3.org/2000/svg"> x<rect x="0.5" width="50" height="50" rx="25" fill="#E6F6E8"/><rect x="10.5" y="10" width="30" height="30" rx="15" fill="#00A31B"/><path d="M32 20L23.0625 29L19 24.9091" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                $output .= '</div>';
                $output .= '<p class="uich-text">API Not Connected</p>';
                $output .= '<div class="uich-active-deactive">';
                    $output .= '<a class="uich-btn" type="button">Get API Key</a>';
                $output .= '</div>';
            $output .= '</div>';
        $output .= '</div>';
    $output .= '</div>';

    $output .= '<div class="uich-quick-link-cards container">';
        $output .= '<div class="uich-card-main">';
            $output .= '<h4 class="uich-heading">Documentation</h4>';
            $output .= '<button type="button" class="uich-border-btn">Read Now</button>';
        $output .= '</div>';

        $output .= '<div class="uich-card-main">';
            $output .= '<h4 class="uich-heading">Need Help?</h4>';
            $output .= '<button type="button" class="uich-border-btn">Raise Ticket</button>';
        $output .= '</div>';

        $output .= '<div class="uich-card-main">';
            $output .= '<h4 class="uich-heading">Join Our Community</h4>';
            $output .= '<button type="button" class="uich-border-btn">Join Now</button>';
        $output .= '</div>';

        $output .= '<div class="uich-card-main">';
            $output .= '<h4 class="uich-heading">YouTube Tutorials</h4>';
            $output .= '<button type="button" class="uich-border-btn">Watch Now</button>';
        $output .= '</div>';
    $output .= '</div>';

    $output .= '<div class="uich-welcome-section uich-templates-section container">';
        $output .= '<div class="uich-left-text">';
            $output .= '<h1 class="uich-heading">UiChemy Templates</h1>';
            $output .= '<p class="uich-paragraph">We`re offering a Lifetime Deal for a limited time only. Afterward, we will switch to only offering Yearly Plans. </p>';
            $output .= '<div class="uich-btn-group">';
                $output .= '<button type="button" class="uich-yellow-btn">Template Library</button>';
            $output .= '</div>';
        $output .= '</div>';
        $output .= '<div class="uich-right-img">';
            $output .= '<img src="'. esc_url(UICH_URL) .'assets/svg/templates-section-right-img.svg"/>';
        $output .= '</div>';
    $output .= '</div>';

    $output .= '<div class="uich-faqs-section container">';
        $output .= '<div class="uich-left-section">';
            $output .= '<div class="uich-heading-para">';
                $output .= '<h2 class="uich-heading">Frequently Asked Questions.</h2>';
                $output .= '<p class="uich-text"> For any further help, reach us at <a href="#">Helpdesk</a> or connect via livechat on website.</p>';
            $output .= '</div>';

            $output .= '<div class="uich-bottom-section">';
                $output .= '<div class="uich-social-icons">';
                    $output .= '<a href="https://www.facebook.com/uichemy/" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-facebook"></i></a>';
                    $output .= '<a href="https://twitter.com/i/flow/login?redirect_after_login=%2Fuichemy" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-x-twitter"></i></a>';
                    $output .= '<a href="https://www.instagram.com/uichemyhq/" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-instagram"></i></a>';
                    $output .= '<a href="https://www.pinterest.com/posimyth/" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-pinterest"></i></a>';
                $output .= '</div>';
                $output .= '<p class="uich-text">© POSIMYTH Innovations</p>';
            $output .= '</div>';
        $output .= '</div>';

        $output .= '<div class="uich-right-section">';
            $output .= '<div class="uich-container">';
                $output .= '<div class="uich-accordion-area">';
                    $output .= '<div class="uich-accordion-box">';
                        $output .= '<h5 class="uich-acc-trigger">How does UiChemy work? <i class="fa-solid fa-minus" aria-hidden="true"></i></h5>';
                        $output .= '<div class="uich-acc-container">';
                            $output .= '<p>UiChemy works by integrating directly with Figma and Elementor. Once you have designed your website layout in Figma, you can export a template JSON or push the file directly using our UiChemy WordPress plugin. It’s a simple 3-step process just follow <a href="https://uichemy.com/help/design-guidelines/" target="_blank" rel="noopener noreferrer">our design guidelines in the docs</a>. To get started, <a href="https://uichemy.com/help/getting-started/" target="_blank" rel="noopener noreferrer">please check our documentation</a>.</p>';
                        $output .= '</div>';
                    $output .= '</div>';

                    $output .= '<div class="uich-accordion-box">';
                        $output .= '<h5 class="uich-acc-trigger">Is UiChemy a standalone software or a plugin? <i class="fa-solid fa-plus" aria-hidden="true"></i></h5>';
                        $output .= '<div class="uich-acc-container">';
                            $output .= '<p>Uichemy is a <a href="https://www.figma.com/community/plugin/1265873702834050352" target="_blank" rel="noopener noreferrer">Figma Plugin</a> that you need to install in Figma. From there, you can export the design in a template JSON or install';
                            $output .= 'the UiChemy WordPress plugin to directly push the design from Figma to your WordPress site.</p>';
                        $output .= '</div>';
                    $output .= '</div>';

                    $output .= '<div class="uich-accordion-box">';
                        $output .= '<h5 class="uich-acc-trigger">Does UiChemy require any coding knowledge? <i class="fa-solid fa-plus" aria-hidden="true"></i></h5>';
                        $output .= '<div class="uich-acc-container">';
                            $output .= '<p>';
                                $output .= 'Not at all. UiChemy does not require you to know any lines of code. It works seamlessly between both platforms all you have to do is plug in and export your designs.';
                            $output .= '</p>';
                        $output .= '</div>';
                    $output .= '</div>';

                    $output .= '<div class="uich-accordion-box">';
                        $output .= '<h5 class="uich-acc-trigger">How can I get support or assistance with UiChemy? <i class="fa-solid fa-plus" aria-hidden="true"></i></h5>';
                        $output .= '<div class="uich-acc-container">';
                            $output .= '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>';
                        $output .= '</div>';
                    $output .= '</div>';
                $output .= '</div>';
            $output .= '</div>';
        $output .= '</div>';
    $output .= '</div>';

$output .= '</div>';
$output .= '<div></div>';

echo $output;