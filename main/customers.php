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
		),	) );
}

function horsetools_customers_menu() {
	horsetools_group_menu( 'customers-options', __( 'Customers', 'horse-tools' ), 'ti-message', 'horsetools_customers_page', 7 );
}
add_action( 'admin_menu', 'horsetools_customers_menu', 20 );
