<?php
/**
 * Horse Tools — the Customers screen.
 *
 * Everything aimed at the person visiting to buy or ask something.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function horsetools_customers_page() {
	horsetools_group_render( array(
		'cu-tab1' => array(
			'label' => __( 'Chat', 'horse-tools' ),
			'icon'  => 'ti-message',
			'files' => array( 'main/page/12chat.php' ),
		),
		'cu-tab2' => array(
			'label' => __( 'WooCommerce', 'horse-tools' ),
			'icon'  => 'ti-shopping-cart',
			'files' => array( 'main/page/8woo.php' ),
		),		'cu-tab3' => array(
			'label'  => __( 'Ad-block notice', 'horse-tools' ),
			'icon'   => 'ti-ban',
			'module' => 'notify',
			'files'  => array( 'main/section/cu-notify1.php' ),
		),
		'cu-tab4' => array(
			'label'  => __( 'Notification bar', 'horse-tools' ),
			'icon'   => 'ti-bell',
			'module' => 'notify',
			'files'  => array( 'main/section/cu-notify2.php' ),
		),
		'cu-tab5' => array(
			'label'  => __( 'Popup', 'horse-tools' ),
			'icon'   => 'ti-app-window',
			'module' => 'notify',
			'files'  => array( 'main/section/cu-notify3.php' ),
		),
		'cu-tab6' => array(
			'label'  => __( 'Cookie notice', 'horse-tools' ),
			'icon'   => 'ti-cookie',
			'module' => 'notify',
			'files'  => array( 'main/section/cu-notify4.php' ),
		),
		'cu-tab7' => array(
			'label'  => __( 'Ad clicks', 'horse-tools' ),
			'icon'   => 'ti-click',
			'module' => 'ads',
			'files'  => array( 'main/section/cu-ads1.php' ),
		),
		'cu-tab8' => array(
			'label'  => __( 'AdSense', 'horse-tools' ),
			'icon'   => 'ti-ad',
			'module' => 'ads',
			'files'  => array( 'main/section/cu-ads2.php' ),
		),
		'cu-tab9' => array(
			'label'  => __( 'ads.txt', 'horse-tools' ),
			'icon'   => 'ti-file-text',
			'module' => 'ads',
			'files'  => array( 'main/section/cu-ads3.php' ),
		),
	) );
}

function horsetools_customers_menu() {
	horsetools_group_menu( 'customers-options', __( 'Customers', 'horse-tools' ), 'ti-message', 'horsetools_customers_page', 7 );
}
add_action( 'admin_menu', 'horsetools_customers_menu', 20 );
