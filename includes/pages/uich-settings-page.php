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
		$output .= '<span>'.esc_html__( 'Regenerate Token', 'uichemy' ).'</span>';
		$output .= '<span><div class="uich-loader"></div></span>';
	$output     .= '</button>';

	// $output .= '<div class="uich-feilds dropdown-cover">';

	// 	$output     .= '<label for="IMPuser" class="uich-dropdown">';
	// 		$output .= esc_html__( 'Import as User', 'uichemy' );
	// 	$output     .= '</label>';

	// 	$output     .= '<select id="IMPuser" name="IMPuser">';
	// 		$output .= '<option value="volvo">Volvo</option>';
	// 		$output .= '<option value="saab">Saab</option>';
	// 		$output .= '<option value="fiat">Fiat</option>';
	// 		$output .= '<option value="audi">Audi</option>';
	// 	$output     .= '</select>';
	// $output         .= '</div>';

$output .= '</form>';
$output .= '</div>';

echo $output;
