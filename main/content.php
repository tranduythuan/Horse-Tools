<?php
/**
 * Horse Tools — the Content screen.
 *
 * What used to be the CONTENT tab, minus the SEO settings that moved to their own screen in 1.2.69.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function horsetools_content_page() {
	horsetools_group_render( array(
		'ct-tab1' => array(
			'label' => __( 'Posts', 'horse-tools' ),
			'icon'  => 'ti-notes',
			'files' => array( 'main/page/6post.php' ),
		),		'ct-tab2' => array(
			'label'  => __( 'Lock content', 'horse-tools' ),
			'icon'   => 'ti-lock',
			'module' => 'shortcode',
			'files'  => array( 'main/section/ct-sc1.php' ),
		),
		'ct-tab3' => array(
			'label'  => __( 'Signature', 'horse-tools' ),
			'icon'   => 'ti-signature',
			'module' => 'shortcode',
			'files'  => array( 'main/section/ct-sc2.php' ),
		),
		'ct-tab4' => array(
			'label'  => __( 'Date shortcodes', 'horse-tools' ),
			'icon'   => 'ti-calendar',
			'module' => 'shortcode',
			'files'  => array( 'main/section/ct-sc3.php' ),
		),
		'ct-tab5' => array(
			'label'  => __( 'Google fetch', 'horse-tools' ),
			'icon'   => 'ti-brand-google',
			'module' => 'shortcode',
			'files'  => array( 'main/section/ct-sc4.php' ),
		),
		'ct-tab6' => array(
			'label'  => __( 'Icons', 'horse-tools' ),
			'icon'   => 'ti-icons',
			'module' => 'shortcode',
			'files'  => array( 'main/section/ct-sc5.php' ),
		),
		'ct-tab7' => array(
			'label'  => __( 'Snippets', 'horse-tools' ),
			'icon'   => 'ti-code',
			'module' => 'shortcode',
			'files'  => array( 'main/section/ct-sc6.php' ),
		),
		'ct-tab8' => array(
			'label'  => __( 'Tables', 'horse-tools' ),
			'icon'   => 'ti-table',
			'module' => 'shortcode',
			'raw'    => true,
			'files'  => array( 'main/section/ct-tables.php' ),
		),
	) );
}

function horsetools_content_menu() {
	horsetools_group_menu( 'content-options', __( 'Content', 'horse-tools' ), 'ti-notes', 'horsetools_content_page', 5 );
}
add_action( 'admin_menu', 'horsetools_content_menu', 20 );
