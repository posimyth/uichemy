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

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$admin_page_url = admin_url('admin.php?page=uichemy-welcome');

echo '<div class="uich-container uich-main-bg" >';
	echo '<div class="uich-logo">';
		echo '<a href="' . esc_url($admin_page_url) . '">';
			echo '<img src="' . esc_url( UICH_URL ) . 'assets/svg/uichemy-logo.svg" />';
		echo '</a>';
	echo '</div>';
	echo '<div class="uich-header-btn-group">';
		echo '<div class="uich-with-notification-btn" >';
			echo '<a target="_blank" rel="noopener noreferrer" href="https://roadmap.uichemy.com/updates" class="uich-header-btn uich-transparent-btn">What’s New?</a>';
			echo '<span class="uich-notify-circle">1</span>';
		echo '</div>';
		echo '<button type="button" class="uich-header-btn filled-btn">';
			echo esc_html__( 'Version ', 'uichemy' ) . esc_html( UICH_VERSION );
		echo '</button>';
	echo '</div>';
echo '</div>';
