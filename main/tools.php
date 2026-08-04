<?php
/**
 * Horse Tools — the Tools screen.
 *
 * Housekeeping for the admin area, and the plugin's own settings.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function horsetools_tools_page() {
	horsetools_group_render( array(
		'tl-tab1' => array(
			'label' => __( 'Admin tools', 'horse-tools' ),
			'icon'  => 'ti-tools',
			'files' => array( 'main/page/3tool.php' ),
		),
		'tl-tab2' => array(
			'label' => __( 'Plugin settings', 'horse-tools' ),
			'icon'  => 'ti-settings',
			'files' => array( 'main/page/ht-setting.php' ),
		),	) );
}

function horsetools_tools_menu() {
	horsetools_group_menu( 'tools-options', __( 'Tools', 'horse-tools' ), 'ti-tools', 'horsetools_tools_page', 9 );
}
add_action( 'admin_menu', 'horsetools_tools_menu', 20 );
