<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } 
global $horsetools_options;
# thay doi nut mua hang ở trang xem sản phẩm
if(!empty($horsetools_options['woo-text1'])){
function horsetools_sing_to_cart_text(){
	global $horsetools_options;
	$name = !empty($horsetools_options['woo-text1']) ? esc_html($horsetools_options['woo-text1']) : null;
    return $name;	
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'horsetools_sing_to_cart_text' ); 
}
# thay doi nut mua hang ở trang xem sản phẩm
if(!empty($horsetools_options['woo-text2'])){
function horsetools_pro_to_cart_text(){
	global $horsetools_options;
	$name = !empty($horsetools_options['woo-text2']) ? esc_html($horsetools_options['woo-text2']) : null;
    return $name;
}
add_filter( 'woocommerce_product_add_to_cart_text', 'horsetools_pro_to_cart_text' ); 
}
# liên hệ thay cho 0đ
if(!empty($horsetools_options['woo-text3'])){
function horsetools_product_zero( $price, $product ) {
	global $horsetools_options;
    if ( $product->get_price() == 0 ) {
        if ( $product->is_on_sale() && $product->get_regular_price() ) {
            $regular_price = wc_get_price_to_display( $product, array( 'qty' => 1, 'price' => $product->get_regular_price() ) );
            $price = wc_format_price_range( $regular_price, __( 'Free!', 'woocommerce' ) );
        } else {
			$name = !empty($horsetools_options['woo-text3']) ? esc_html($horsetools_options['woo-text3']) : null;
            $price = '<span class="woozero">' . $name . '</span>';
        }
    }
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'horsetools_product_zero', 10, 2 );
}
# liên hệ thay cho hết hàng
if(!empty($horsetools_options['woo-text4'])){
function horsetools_product_end( $price, $product ) {
	global $horsetools_options;
    if ( !is_admin() && !$product->is_in_stock()) {
	   $name = !empty($horsetools_options['woo-text4']) ? esc_html($horsetools_options['woo-text4']) : null;
       $price = '<span class="woozero">' . $name . '</span>';
    }
    return $price;
}
add_filter( 'woocommerce_get_price_html', 'horsetools_product_end', 99, 2 );
}
# chuyen don vi tien te
function horsetools_currency_symbol( $currency_symbol, $currency ) {
    global $horsetools_options;
    if ( isset( $horsetools_options['woo-cy1'] ) && !empty( $horsetools_options['woo-cy1'] ) ) {
        $current_currency = get_option('woocommerce_currency');
        if ( $currency === $current_currency ) {
            $currency_symbol = esc_html($horsetools_options['woo-cy1']);
        }
    }
    return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'horsetools_currency_symbol', 10, 2);
# thông báo telegram khi có đơn hàng
if (isset($horsetools_options['woo-tele1'])){
function horsetools_woo_telegram($order_id) {
    global $horsetools_options;
    if (!$order_id) return;
    $site = get_site_url();
    $order = wc_get_order($order_id);
    $order_data = $order->get_data();
    $first_name = $order_data['billing']['first_name'];
    $last_name = $order_data['billing']['last_name'];
    $phone = $order_data['billing']['phone'];
    $current_time = current_time('d/m/Y H:i:s');
    $msg = __('From', 'horse-tools') . ": $site\n" .
           __('Code orders', 'horse-tools') . ": $order_id\n" .
           __('Buyer', 'horse-tools') . ": $last_name $first_name\n" .
           __('Contact', 'horse-tools') . ": $phone\n" .
           __('Time', 'horse-tools') . ": $current_time";

    $token = !empty($horsetools_options['woo-tele11']) ? $horsetools_options['woo-tele11'] : null;
    $chatID = !empty($horsetools_options['woo-tele12']) ? $horsetools_options['woo-tele12'] : null;
    if ( empty( $token ) || empty( $chatID ) ) {
        return;
    }
    // Was file_get_contents() on a remote URL with no timeout: on a host with
    // allow_url_fopen off (a common default) it silently returned false and the
    // notification was simply lost, and a slow Telegram response blocked the
    // customer's checkout for up to default_socket_timeout. 'blocking' => false
    // means the shopper never waits on Telegram at all.
    $response = wp_safe_remote_post(
        'https://api.telegram.org/bot' . rawurlencode( $token ) . '/sendMessage',
        array(
            'timeout'  => 10,
            'blocking' => false,
            'body'     => array(
                'chat_id'    => $chatID,
                'parse_mode' => 'html',
                'text'       => $msg,
            ),
        )
    );
    if ( is_wp_error( $response ) ) {
        error_log( 'horse-tools: Telegram order notification failed: ' . $response->get_error_message() );
    }
}
add_action('woocommerce_checkout_order_processed', 'horsetools_woo_telegram');
}











