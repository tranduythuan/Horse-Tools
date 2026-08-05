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
		),		'tl-tab3' => array(
			'label'  => __( 'Custom CSS', 'horse-tools' ),
			'icon'   => 'ti-brush',
			'module' => 'code',
			'files'  => array( 'main/section/tl-code1.php' ),
		),
		'tl-tab4' => array(
			'label'  => __( 'Code in head', 'horse-tools' ),
			'icon'   => 'ti-code',
			'module' => 'code',
			'files'  => array( 'main/section/tl-code2.php' ),
		),
		'tl-tab5' => array(
			'label'  => __( 'Code in body', 'horse-tools' ),
			'icon'   => 'ti-code-plus',
			'module' => 'code',
			'files'  => array( 'main/section/tl-code3.php' ),
		),
		'tl-tab6' => array(
			'label'  => __( 'Code in footer', 'horse-tools' ),
			'icon'   => 'ti-code-minus',
			'module' => 'code',
			'files'  => array( 'main/section/tl-code4.php' ),
		),
		'tl-tab7' => array(
			'label'  => __( 'Code on login', 'horse-tools' ),
			'icon'   => 'ti-login',
			'module' => 'code',
			'files'  => array( 'main/section/tl-code5.php' ),
		),
		'tl-tab8' => array(
			'label'  => __( 'Debug', 'horse-tools' ),
			'icon'   => 'ti-bug',
			'module' => 'debug',
			'files'  => array( 'main/section/tl-debug1.php' ),
		),
		'tl-tab9' => array(
			'label'  => __( 'Backup', 'horse-tools' ),
			'icon'   => 'ti-file-export',
			'module' => 'export',
			'raw'    => true,
			'files'  => array( 'main/section/tl-backup.php' ),
		),
	) );
}

function horsetools_tools_menu() {
	horsetools_group_menu( 'tools-options', __( 'Tools', 'horse-tools' ), 'ti-tools', 'horsetools_tools_page', 9 );
}
add_action( 'admin_menu', 'horsetools_tools_menu', 20 );
