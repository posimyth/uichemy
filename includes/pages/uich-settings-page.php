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

$current_token = apply_filters( 'uich_manage_token', 'get_token' );
$capable_users = apply_filters( 'uich_manage_usermanager', 'get_userlist' );
$selected_user = apply_filters( 'uich_manage_usermanager', 'get_user' );

$output = '';

$output .= '<div class="uich-container-main uich-settings-pages">';
	require_once UICH_PATH . 'includes/pages/design-header.php';

	$output .= '<form class="uich-main-form">';

	$output .= '<div class="uich-feilds">';

		$output     .= '<label for="SiteURL">';
			$output .= esc_html__( 'Site URL', 'uichemy' );
		$output     .= '</label>';

		$output .= '<input readonly id="uichemy-site-url-input" name="SiteURL" type="url" value="' . esc_url( UICH_URL ) . '"/>';

		$output     .= '<button id="uichemy-url-copy-btn" class="uich-token-copy-url">';
			$output .= '<img class="copy-icon" src="' . esc_url( UICH_URL ) . 'assets/svg//copy-action.svg" />';
			$output .= '<img class="hidden done-icon" src="' . esc_url( UICH_URL ) . 'assets/svg//done-status.svg" />';
		$output     .= '</button>';

	$output .= '</div>';

	$output .= '<div class="uich-feilds">';

		$output     .= '<label for="Security">';
			$output .= esc_html__( 'Security Token', 'uichemy' );
		$output     .= '</label>';

		$output .= '<input readonly id="uichemy-token-input" name="Security" type="text" value="' . esc_attr( $current_token ) . '"/>';

		$output     .= '<button id="uichemy-token-copy-btn" class="uich-token-copy-url">';
			$output .= '<img class="copy-icon" src="' . esc_url( UICH_URL ) . 'assets/svg/copy-action.svg" />';
			$output .= '<img class="hidden done-icon" src="' . esc_url( UICH_URL ) . 'assets/svg/done-status.svg" />';
		$output     .= '</button>';

	$output .= '</div>';

	$output     .= '<button type="button" id="uichemy-regenerate-btn" class="uich-border-btn">';
		$output .= '<span>' . esc_html__( 'Regenerate Token', 'uichemy' ) . '</span>';
		$output .= '<span><div class="uich-loader"></div></span>';
	$output     .= '</button>';

	$output .= '<div class="uich-feilds uich-dropdown-cover">';

		$output     .= '<label for="IMPuser" class="uich-dropdown">';
			$output .= esc_html__( 'Import as User', 'uichemy' );
		$output     .= '</label>';

		$output .= '<select value="' . esc_attr( $selected_user ) . '" id="uichemy-user-select">';

foreach ( $capable_users as $user ) {
	$username = ! empty( $user->user_login ) ? $user->user_login : '';
	$selected = ( $username === $selected_user ) ? 'selected="selected"' : '';
	$output  .= "<option value='" . esc_attr( $username ) . "' {$selected}>" . esc_html( ucfirst ( $username ) ) . '</option>';
}

		$output .= '</select>';
	$output     .= '</div>';

$output .= '</form>';
$output .= '</div>';

echo $output;
