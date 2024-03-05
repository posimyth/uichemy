<?php
/**
 * The file that defines the core plugin class
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

// In case of Single or Multisite setup.
function uich_get_site_url(){
    $url = site_url();

    if( is_multisite() ){
        $network = get_network();
		
        if( $network !== null ){
            $url = site_url( $network->path );
        }
    }

    return trailingslashit($url);
}

$current_token = apply_filters( 'uich_manage_token', 'get_token' );
$capable_users = apply_filters( 'uich_manage_usermanager', 'get_userlist' );
$selected_user = apply_filters( 'uich_manage_usermanager', 'get_user' );

$admin_page_url = admin_url('admin.php?page=uichemy-welcome');

echo '<div class="uich-container-main uich-settings-pages">';
	
	require_once UICH_PATH . 'includes/pages/design-header.php';
	
	echo '<div class="uich-main-welcome-back-btn-cover">';
	
		echo '<a href= "' . esc_url($admin_page_url) . '" class="uich-main-welcome-back">';
			echo '<svg width="16" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8337 10.5H4.16699" stroke="#4B22CC" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.0003 16.3334L4.16699 10.5001L10.0003 4.66675" stroke="#4B22CC" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			
			echo esc_html__( 'Back', 'uichemy' );
		echo '</a>';
	echo '</div>';

	echo '<div class="uich-security-img-cover">';
		echo '<img class= "uich-security-img" src="' . esc_url( UICH_URL . 'assets/images/Security-token.png' ) . '" alt="Security-token" draggable="false"/>';
	echo '</div>';
	
	echo '<form class="uich-main-form">';

	echo '<div class="uich-feilds">';

		echo '<label for="SiteURL">';
			echo esc_html__( 'Site URL', 'uichemy' );
		echo '</label>';

		echo '<input readonly id="uichemy-site-url-input" name="SiteURL" type="url" value="' . esc_url( uich_get_site_url() ) . '"/>';

		echo '<button id="uichemy-url-copy-btn" class="uich-token-copy-url">';
			echo '<img class="copy-icon" src="' . esc_url( UICH_URL ) . 'assets/svg//copy-action.svg" />';
			echo '<img class="hidden done-icon" src="' . esc_url( UICH_URL ) . 'assets/svg//done-status.svg" />';
		echo '</button>';

	echo '</div>';

	echo '<div class="uich-feilds">';

		echo '<label for="Security">';
			echo esc_html__( 'Security Token', 'uichemy' );
		echo '</label>';

		echo '<input readonly id="uichemy-token-input" name="Security" type="text" value="' . esc_attr( $current_token ) . '"/>';

		echo '<button id="uichemy-token-copy-btn" class="uich-token-copy-url">';
			echo '<img class="copy-icon" src="' . esc_url( UICH_URL ) . 'assets/svg/copy-action.svg" />';
			echo '<img class="hidden done-icon" src="' . esc_url( UICH_URL ) . 'assets/svg/done-status.svg" />';
		echo '</button>';

	echo '</div>';

	echo '<button type="button" id="uichemy-regenerate-btn" class="uich-border-btn">';
		echo '<span>' . esc_html__( 'Regenerate Token', 'uichemy' ) . '</span>';
		echo '<span><div class="uich-loader"></div></span>';
	echo '</button>';

	echo '<div class="uich-feilds uich-dropdown-cover">';

		echo '<label for="IMPuser" class="uich-dropdown">';
			echo esc_html__( 'Import as User', 'uichemy' );
		echo '</label>';

		echo '<select value="' . esc_attr( $selected_user ) . '" id="uichemy-user-select">';

			foreach ( $capable_users as $user ) {
				$username = ! empty( $user->user_login ) ? $user->user_login : '';
				$selected = ( $username === $selected_user ) ? 'selected="selected"' : '';
				echo "<option value='" . esc_attr( $username ) . "' " . selected( $username, $selected_user, false ) . '>' . esc_html( ucfirst( $username ) ) . '</option>';
			}

		echo '</select>';
	echo '</div>';

echo '</form>';

require_once UICH_PATH . 'includes/pages/uich-footer.php';

echo '</div>';
