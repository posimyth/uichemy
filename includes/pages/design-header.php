<?php
/**
 * The file that defines the core plugin class
 *
 * It is Header File. 
 *
 * @link       https://posimyth.com/
 * @since      1.0.0
 *
 * @package    Wdesignkit
 * @subpackage Wdesignkit/pages/header
 */

$output             .= '<div class="uich-container uich-main-bg" >';
	$output         .= '<div class="uich-logo">';
		$output     .= '<img src="' . esc_url( UICH_URL ) . 'assets/svg/uichemy-logo.svg" />';
	$output         .= '</div>';
	$output         .= '<div class="uich-header-btn-group">';
		$output     .= '<div class="uich-with-notification-btn" >';
			$output .= '<button type="button" class="uich-header-btn uich-transparent-btn">What’s New?</button>';
			$output .= '<span class="uich-notify-circle">1</span>';
		$output     .= '</div>';
		$output     .= '<button type="button" class="uich-header-btn filled-btn">';
			$output .= esc_html__( 'Version ', 'uichemy' ) . esc_html( UICH_VERSION );
		$output     .= '</button>';
	$output         .= '</div>';
$output             .= '</div>';

return $output;
