<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;
# Tắt API REST
if (isset($horsetools_options['scuri-off1'])){
add_filter( 'rest_authentication_errors', function( $result ) {
    if ( true === $result || is_wp_error( $result ) ) {
        return $result;
    }
    if ( ! is_user_logged_in() ) {
        return new WP_Error( 'rest_not_logged_in',  __('You are not logged in', 'horse-tools'), array( 'status' => 401 ) );
    }
    return $result;
});
}
# Tắt  XML RPC
if (isset($horsetools_options['scuri-off2'])){
add_filter( 'wp_xmlrpc_server_class', '__return_false' );
add_filter('xmlrpc_enabled', '__return_false');
add_filter('pre_update_option_enable_xmlrpc', '__return_false');
add_filter('pre_option_enable_xmlrpc', '__return_zero');
}
# Xóa Wp-Embed
if (isset($horsetools_options['scuri-off3'])){
function horsetools_deregister_scripts(){
	wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'horsetools_deregister_scripts' );
}
# Xóa xpingback header
if (isset($horsetools_options['scuri-off4'])){
function horsetools_adminify_remove_pingback_head($headers){
    if (isset($headers['X-Pingback'])) {
        unset($headers['X-Pingback']);
    }
    return $headers;
}
add_filter('wp_headers', 'horsetools_adminify_remove_pingback_head');
}
# xóa tiêu đề ko cần thiết
if (isset($horsetools_options['scuri-off5'])){
function horsetools_remove_header_info() {
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'start_post_rel_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head',10,0); 
}
add_action('init', 'horsetools_remove_header_info');
}
# xóa nguồn cấp dữ liệu khác
if (isset($horsetools_options['scuri-off6'])){
/**
 * Turn feeds off.
 *
 * The old hook list missed do_feed_rss2 — the default feed type, i.e. the one
 * /feed/ and /comments/feed/ actually use — so the site's main feed kept
 * serving normally while the toggle appeared to have worked. Three of the six
 * hooks it did register are not actions at all (`do_feed` is a function;
 * comment feeds route through do_feed_rss2), so they were dead lines.
 *
 * It also called wp_die() with no arguments, which returns HTTP 500. Feed
 * readers and Search Console then log a server error forever. 410 Gone is the
 * honest status for a feed that has been deliberately withdrawn.
 */
function horsetools_disable_feed() {
	wp_die(
		sprintf(
			/* translators: %s: link to the site home page. */
			esc_html__( 'Feeds are not available on this site. %s', 'horse-tools' ),
			'<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Go to the home page', 'horse-tools' ) . '</a>'
		),
		esc_html__( 'Feeds disabled', 'horse-tools' ),
		array( 'response' => 410 )
	);
}
add_action('do_feed_rss2', 'horsetools_disable_feed', 1);
add_action('do_feed_rdf', 'horsetools_disable_feed', 1);
add_action('do_feed_rss', 'horsetools_disable_feed', 1);
add_action('do_feed_atom', 'horsetools_disable_feed', 1);
// Also stop advertising feeds that now return 410.
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
}
# Bao mat file ngan chan tai len ko phai la file anh
if (isset($horsetools_options['scuri-up1'])){
function horsetools_wp_handle_upload_prefilter($file) {
    if ($file['type'] == 'application/octet-stream' && isset($file['tmp_name'])) {
        $file_size = getimagesize($file['tmp_name']);
        if (isset($file_size[2]) && $file_size[2] != IMAGETYPE_UNKNOWN) {
            $file['type'] = image_type_to_mime_type($file_size[2]);
        } else {
            $file['error'] = __('Unable to determine image format', 'horse-tools');
            return $file;
        }
    }
    list($category, $type) = explode('/', $file['type']);
    $allowed_types = array('jpg', 'jpeg', 'gif', 'png', 'webp');
    if ($category !== 'image' || !in_array($type, $allowed_types)) {
        $file['error'] = __('I am sorry, you can only upload image files in the formats .GIF, .JPG, .PNG, .WEBP', 'horse-tools');
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'horsetools_wp_handle_upload_prefilter');
}
# Xóa ver của css và js
if (isset($horsetools_options['scuri-verof1'])){
function horsetools_remove_css_js_version( $src ) {
	if( strpos( $src, '?ver=' ) )
	$src = remove_query_arg( 'ver', $src );
	return $src;
	}
add_filter( 'style_loader_src', 'horsetools_remove_css_js_version', 9999 );
add_filter( 'script_loader_src', 'horsetools_remove_css_js_version', 9999 );
}
# xóa ver wordpress
if (isset($horsetools_options['scuri-verof2'])){
function horsetools_remove_wpversion() {
	return '';
	}
add_filter('the_generator', 'horsetools_remove_wpversion');
}
# bảo mật dữ liệu truy cập
if (isset($horsetools_options['scuri-sql1'])){
function horsetools_security_check() {
    global $user_ID;
    if ($user_ID) {
        if (!current_user_can('manage_options')) {
            if (strlen($_SERVER['REQUEST_URI']) > 255 ||
                stripos($_SERVER['REQUEST_URI'], "eval(") ||
                stripos($_SERVER['REQUEST_URI'], "CONCAT") ||
                stripos($_SERVER['REQUEST_URI'], "UNION+SELECT") ||
                stripos($_SERVER['REQUEST_URI'], "base64")) {
                    @header("HTTP/1.1 414 Request-URI Too Long");
                    @header("Status: 414 Request-URI Too Long");
                    @header("Connection: Close");
                    @exit;
            }
        }
    }
}
add_action('init', 'horsetools_security_check');
}



