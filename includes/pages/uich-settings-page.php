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
function get_site_url(){
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

echo '<div class="uich-container-main uich-settings-pages">';
	require_once UICH_PATH . 'includes/pages/design-header.php';

	echo '<form class="uich-main-form">';

	echo '<div class="uich-feilds">';

		echo '<label for="SiteURL">';
			echo esc_html__( 'Site URL', 'uichemy' );
		echo '</label>';

		echo '<input readonly id="uichemy-site-url-input" name="SiteURL" type="url" value="' . esc_url( get_site_url() ) . '"/>';

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
echo '</div>';
