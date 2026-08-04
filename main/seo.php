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
	) );
}

function horsetools_seo_menu() {
	horsetools_group_menu( 'seo-options', __( 'SEO', 'horse-tools' ), 'ti-chart-arrows-vertical', 'horsetools_seo_page', 3 );
}
add_action( 'admin_menu', 'horsetools_seo_menu', 20 );
