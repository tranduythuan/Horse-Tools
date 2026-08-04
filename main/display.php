<?php
/**
 * Horse Tools — the Appearance screen.
 *
 * How the site looks to visitors, and how the admin area looks to you. The admin-side settings are what remained of the CUSTOM tab after the login page moved to Security.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function horsetools_display_page() {
	horsetools_group_render( array(
		'dp-tab1' => array(
			'label' => __( 'Site', 'horse-tools' ),
			'icon'  => 'ti-device-desktop',
			'files' => array( 'main/page/4main.php' ),
		),
		'dp-tab2' => array(
			'label' => __( 'Admin area', 'horse-tools' ),
			'icon'  => 'ti-brand-wordpress',
			'files' => array( 'main/page/10custom.php' ),
		),	) );
}

function horsetools_display_menu() {
	horsetools_group_menu( 'display-options', __( 'Appearance', 'horse-tools' ), 'ti-device-desktop', 'horsetools_display_page', 6 );
}
add_action( 'admin_menu', 'horsetools_display_menu', 20 );
