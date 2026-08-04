<?php
/**
 * Horse Tools — the SEO screen.
 *
 * Everything here already existed, spread across the CONTENT tab under
 * headings nobody looking for SEO would think to open — which is how a site
 * owner concludes the plugin has no SEO features at all. Nothing about what
 * these settings do has changed, only where they are.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function horsetools_seo_page() {
	horsetools_group_render( array(
		'seo-tab1' => array(
			'label' => __( 'Links & URLs', 'horse-tools' ),
			'icon'  => 'ti-link',
			'files' => array( 'main/section/seo-url.php' ),
		),
		'seo-tab2' => array(
			'label' => __( 'Rich results', 'horse-tools' ),
			'icon'  => 'ti-help-circle',
			'files' => array( 'main/section/seo-faq.php' ),
		),
		'seo-tab3' => array(
			'label' => __( 'Redirects 301', 'horse-tools' ),
			'icon'  => 'ti-compass',
			'files' => array( 'main/section/seo-301.php' ),
		),
		'seo-tab4' => array(
			'label' => __( 'Broken links 404', 'horse-tools' ),
			'icon'  => 'ti-ban',
			'files' => array( 'main/section/seo-404.php' ),
		),
		'seo-tab5' => array(
			'label' => __( 'Index now', 'horse-tools' ),
			'icon'  => 'ti-hand-finger',
			'files' => array( 'main/section/seo-index.php', 'main/section/seo-index2.php' ),
		),
		'seo-tab6' => array(
			'label' => __( 'Table of contents', 'horse-tools' ),
			'icon'  => 'ti-list',
			'files' => array( 'main/section/seo-toc.php' ),
		),	) );
}

function horsetools_seo_menu() {
	horsetools_group_menu( 'seo-options', __( 'SEO', 'horse-tools' ), 'ti-chart-arrows-vertical', 'horsetools_seo_page', 3 );
}
add_action( 'admin_menu', 'horsetools_seo_menu', 20 );
