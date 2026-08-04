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
		),	) );
}

function horsetools_content_menu() {
	horsetools_group_menu( 'content-options', __( 'Content', 'horse-tools' ), 'ti-notes', 'horsetools_content_page', 5 );
}
add_action( 'admin_menu', 'horsetools_content_menu', 20 );
