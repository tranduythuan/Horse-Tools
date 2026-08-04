<?php
/**
 * Horse Tools — the Speed screen.
 *
 * Two halves of the same job: what the browser has to fetch and run, and how heavy the images it fetches are. Image handling used to sit under MEDIA, where it read as a filing cabinet rather than as the biggest lever most sites have over page weight.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function horsetools_speed_page() {
	horsetools_group_render( array(
		'sp-tab1' => array(
			'label' => __( 'Optimisation', 'horse-tools' ),
			'icon'  => 'ti-gauge',
			'files' => array( 'main/page/1speed.php' ),
		),
		'sp-tab2' => array(
			'label' => __( 'Images', 'horse-tools' ),
			'icon'  => 'ti-photo',
			'files' => array( 'main/page/5media.php' ),
		),		'sp-tab3' => array(
			'label'  => __( 'Clean posts', 'horse-tools' ),
			'icon'   => 'ti-pin',
			'module' => 'clean',
			'files'  => array( 'main/section/sp-clean1.php' ),
		),
		'sp-tab4' => array(
			'label'  => __( 'Clean comments', 'horse-tools' ),
			'icon'   => 'ti-message-circle',
			'module' => 'clean',
			'files'  => array( 'main/section/sp-clean2.php' ),
		),
		'sp-tab5' => array(
			'label'  => __( 'Clean media', 'horse-tools' ),
			'icon'   => 'ti-photo',
			'module' => 'clean',
			'files'  => array( 'main/section/sp-clean3.php' ),
		),
		'sp-tab6' => array(
			'label'  => __( 'Cleanup schedule', 'horse-tools' ),
			'icon'   => 'ti-history',
			'module' => 'clean',
			'files'  => array( 'main/section/sp-clean4.php' ),
		),
	) );
}

function horsetools_speed_menu() {
	horsetools_group_menu( 'speed-options', __( 'Speed', 'horse-tools' ), 'ti-gauge', 'horsetools_speed_page', 2 );
}
add_action( 'admin_menu', 'horsetools_speed_menu', 20 );
