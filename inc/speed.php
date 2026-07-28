<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;
# Remove jquery-migrate
if(isset($horsetools_options['speed-off1'])){
function horsetools_remove_jquery_migrate( $scripts ) {
   if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $script = $scripts->registered['jquery'];
   if ( $script->deps ) { 
        $script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
 }
 }
 }
add_action( 'wp_default_scripts', 'horsetools_remove_jquery_migrate' );
}
# tắt Gutenberg CSS o home
if(isset($horsetools_options['speed-off2'])){
function horsetools_remove_wp_block_library_css() {
    if ( is_front_page() ) {
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );
        wp_dequeue_style( 'wc-blocks-style' );
    }
}
add_action( 'wp_enqueue_scripts', 'horsetools_remove_wp_block_library_css', 100 );
}
# tắt Classic CSS o home
if(isset($horsetools_options['speed-off3'])){
function horsetools_classic_styles_off() {
	if ( is_front_page()) {
    wp_dequeue_style( 'classic-theme-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'horsetools_classic_styles_off', 20 );
}
# tắt emoji 
if(isset($horsetools_options['speed-off4'])){
function horsetools_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	// WordPress 6.4 deprecated print_emoji_styles() and moved the CSS to
	// wp_enqueue_emoji_styles(), so the two removals above have matched nothing
	// since then and the emoji stylesheet kept printing on every page.
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'admin_print_styles', 'wp_enqueue_emoji_styles' );
	remove_action( 'embed_head', 'print_emoji_detection_script' );
	add_filter( 'tiny_mce_plugins', function ( $plugins ) {
		return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : $plugins;
	} );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );		
}
add_action( 'init', 'horsetools_disable_emojis' );
}
# gioi han so ban ghi trong csdl 
if(isset($horsetools_options['speed-data1'])){
function horsetools_limit_post_revisions($num, $post) {
	global $horsetools_options;
	if(!empty($horsetools_options['speed-data11'])){
    $limit = $horsetools_options['speed-data11'];
	} else {
	$limit = 3;	
	}
    return $limit;
}
add_filter('wp_revisions_to_keep', 'horsetools_limit_post_revisions', 10, 2);	
}
# gioi han thoi gian luu bai viet tu dong pút
if(isset($horsetools_options['speed-data2'])){
	if (!defined('AUTOSAVE_INTERVAL')) {
		$secon = !empty($horsetools_options['speed-data21']) ? $horsetools_options['speed-data21'] : 1;
		define('AUTOSAVE_INTERVAL', $secon * MINUTE_IN_SECONDS);
	}
}
# thu vien instant-page.js tai truoc link khi di chuot
if(isset($horsetools_options['speed-link1'])){
function horsetools_instantpage_scripts() {
  wp_enqueue_script( 'instantpage', HORSETOOLS_URL . 'link/instantpage.js', array(), '5.7.0', true );
}
add_action( 'wp_enqueue_scripts', 'horsetools_instantpage_scripts' );
function horsetools_instantpage_loader_tag( $tag, $handle ) {
  if ( 'instantpage' === $handle ) {
    if ( strpos( $tag, 'text/javascript' ) !== false ) {
      $tag = str_replace( 'text/javascript', 'module', $tag );
    }
    else {
      $tag = str_replace( '<script ', "<script type='module' ", $tag );
    }
  }
  return $tag;
}
add_filter( 'script_loader_tag', 'horsetools_instantpage_loader_tag', 10, 2 );
}
# cuon trang muot ma
if(isset($horsetools_options['speed-link2'])){
function horsetools_smooth_scripts() {
	// `true` was being passed as $ver, not $in_footer — so this loaded
	// render-blocking in <head> with ?ver=1, inside the Optimize feature set.
	wp_enqueue_script( 'smooth-scroll', HORSETOOLS_URL . 'link/smooth-scroll.min.js', array(), HORSETOOLS_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'horsetools_smooth_scripts' );
}
# lazyload hinh anh
if(isset($horsetools_options['speed-lazy1'])){
function horsetools_lazyload_to_images_with_jquery() {
    if (!is_admin()) {
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                $("img").addClass("lazyload").each(function() {
                    var dataSrc = $(this).attr("src");
                    $(this).attr("data-src", dataSrc).removeAttr("src");
                });
            });
        ');
		wp_enqueue_script( 'lazyload', HORSETOOLS_URL . 'link/lazysizes.min.js', array('jquery'), '5.3.2', true);
    }
}
add_action('wp_enqueue_scripts', 'horsetools_lazyload_to_images_with_jquery');
}
# tuy chon nen html
function horsetools_minify_html_output($buffer) {
	global $horsetools_options;
	if ( substr( ltrim( $buffer ), 0, 5) == '<?xml' )
		return ( $buffer );
	if ( isset($horsetools_options['speed-zip16']) && mb_detect_encoding($buffer, 'UTF-8', true) )
		$mod = '/u';
	else
		$mod = '/s';
	$buffer = str_replace(array (chr(13) . chr(10), chr(9)), array (chr(10), ''), $buffer);
	$buffer = str_ireplace(array ('<script', '/script>', '<pre', '/pre>', '<textarea', '/textarea>', '<style', '/style>'), array ('M1N1FY-ST4RT<script', '/script>M1N1FY-3ND', 'M1N1FY-ST4RT<pre', '/pre>M1N1FY-3ND', 'M1N1FY-ST4RT<textarea', '/textarea>M1N1FY-3ND', 'M1N1FY-ST4RT<style', '/style>M1N1FY-3ND'), $buffer);
	$split = explode('M1N1FY-3ND', $buffer);
	$buffer = ''; 
	for ( $i=0; $i<count($split); $i++ ) {
		$ii = strpos($split[$i], 'M1N1FY-ST4RT');
		if ( $ii !== false ) {
			$process = substr($split[$i], 0, $ii);
			$asis = substr($split[$i], $ii + 12);
			if ( substr($asis, 0, 7) == '<script' ) {
				$split2 = explode(chr(10), $asis);
				$asis = '';
				for ( $iii = 0; $iii < count($split2); $iii ++ ) {
					if ( $split2[$iii] )
						$asis .= trim($split2[$iii]) . chr(10);
					if ( isset($horsetools_options['speed-zip11']) ) {
						$last = substr(trim($split2[$iii]), -1);
						if ( strpos($split2[$iii], '//') !== false && ($last == ';' || $last == '>' || $last == '{' || $last == '}' || $last == ',') )
							$asis .= chr(10);
					}
				}
				if ( $asis )
					$asis = substr($asis, 0, -1);
				if ( isset($horsetools_options['speed-zip12']) )
					$asis = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis);
				if ( isset($horsetools_options['speed-zip11']) )
					$asis = str_replace(array (';' . chr(10), '>' . chr(10), '{' . chr(10), '}' . chr(10), ',' . chr(10)), array(';', '>', '{', '}', ','), $asis);
			} else if ( substr($asis, 0, 6) == '<style' ) {
				$asis = preg_replace(array ('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod), array('>', '<', '\\1'), $asis);
				if ( isset($horsetools_options['speed-zip12']) )
					$asis = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis);
				$asis = str_replace(array (chr(10), ' {', '{ ', ' }', '} ', '( ', ' )', ' :', ': ', ' ;', '; ', ' ,', ', ', ';}'), array('', '{', '{', '}', '}', '(', ')', ':', ':', ';', ';', ',', ',', '}'), $asis);
			}
		} else {
			$process = $split[$i];
			$asis = '';
		}
		$process = preg_replace(array ('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod), array('>', '<', '\\1'), $process);
		if ( isset($horsetools_options['speed-zip12']) )
			$process = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->' . $mod, '', $process);
		$buffer .= $process.$asis;
	}
	$buffer = str_replace(array (chr(10) . '<script', chr(10) . '<style', '*/' . chr(10), 'M1N1FY-ST4RT'), array('<script', '<style', '*/', ''), $buffer);
	if ( isset($horsetools_options['speed-zip13']) && strtolower( substr( ltrim( $buffer ), 0, 15 ) ) == '<!doctype html>' )
		$buffer = str_replace( ' />', '>', $buffer );
	if ( isset($horsetools_options['speed-zip14']) ) {
		// Use the configured site host, never HTTP_HOST — that header is supplied
		// by the client, so a request with Host: fonts.googleapis.com would rewrite
		// every reference to that host into a same-origin path. Behind a cache that
		// keys on path alone, that is cache poisoning for every later visitor.
		$site_host = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( $site_host ) {
			$buffer = str_replace(
				array( 'https://' . $site_host . '/', 'http://' . $site_host . '/', '//' . $site_host . '/' ),
				array( '/', '/', '/' ),
				$buffer
			);
		}
	}
	if (isset($horsetools_options['speed-zip15']))
		$buffer = str_replace( array( 'http://', 'https://' ), '//', $buffer );
	return ( $buffer );
}
function horsetools_init_minify_html(){
	global $horsetools_options;
	// Only buffer real page views. The minifier collapses whitespace runs,
	// which silently corrupts whitespace inside JSON string values — so it must
	// never see an AJAX, REST, cron or feed response.
	if ( wp_doing_ajax() || wp_doing_cron() || is_feed()
		|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
		|| ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}
	if(isset($horsetools_options['speed-zip1']) && !is_user_logged_in()){
		ob_start('horsetools_minify_html_output');
	}
}
add_action( 'init', 'horsetools_init_minify_html', 1 );