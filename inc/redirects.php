<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_redirects_options;
# redirect 301 all
/**
 * Should this request be exempt from the site-wide redirect / maintenance mode?
 *
 * The old test compared $_SERVER['REQUEST_URI'] with !== against '/wp-admin/'
 * and '/wp-login.php', so any query string ('/wp-admin/?x=1') or trailing
 * segment defeated the exemption and locked the administrator out of their own
 * site while maintenance mode was on.
 */
function horsetools_redirect_is_exempt() {
	global $horsetools_options;

	if ( current_user_can('manage_options') ) {
		return true;
	}
	if ( is_admin() || ( isset( $GLOBALS['pagenow'] ) && 'wp-login.php' === $GLOBALS['pagenow'] ) ) {
		return true;
	}
	if ( wp_doing_ajax() || wp_doing_cron() || ( defined('WP_CLI') && WP_CLI ) ) {
		return true;
	}

	$uri = isset($_SERVER['REQUEST_URI']) ? rawurldecode( (string) $_SERVER['REQUEST_URI'] ) : '';
	if ( strpos( $uri, '/wp-login.php' ) !== false || strpos( $uri, '/wp-admin' ) !== false ) {
		return true;
	}
	$linklogin = !empty($horsetools_options['custom-chan11']) ? '/' . $horsetools_options['custom-chan11'] : '';
	if ( '' !== $linklogin && strpos( $uri, $linklogin ) !== false ) {
		return true;
	}
	return false;
}

function horsetools_redirect_to_301() {
    global $horsetools_redirects_options, $horsetools_options;
	// 301 full site
	if (isset($horsetools_redirects_options['redi11']) && !empty($horsetools_redirects_options['redi12']) && ! horsetools_redirect_is_exempt()){
		$linkout = $horsetools_redirects_options['redi12'];
		ob_clean();
		nocache_headers();
		// wp_redirect() applies wp_sanitize_redirect() and the wp_redirect
		// filters; a raw header("Location: …") applies neither.
		wp_redirect( $linkout, 301 );
		exit();
	}
	// 301 link line
	if (isset($horsetools_redirects_options['redi1']) && !isset($horsetools_redirects_options['redi11'])){
		if (is_array($horsetools_redirects_options) || is_object($horsetools_redirects_options)) {
			$redirects = array();
			foreach ($horsetools_redirects_options as $key => $value) {
				if (preg_match('/^rechan1(\d+)$/', $key, $matches)) {
					$n = $matches[1];
					$redirects[$horsetools_redirects_options['rechan1' . $n]] = $horsetools_redirects_options['rechan2' . $n];
				}
			}
			foreach ($redirects as $uri => $new_location) {
				$request_uri_trimmed = rtrim($_SERVER['REQUEST_URI'], '/');
				$uri_trimmed = rtrim(parse_url($uri, PHP_URL_PATH), '/'); 
				if ($request_uri_trimmed === $uri_trimmed) {
					ob_clean();
					nocache_headers();
					wp_redirect( empty($new_location) ? home_url() : $new_location, 301 );
					exit();
					}
				}
			}
		}
	// code chuyen den 503 khi bao tri
	if (isset($horsetools_redirects_options['redi3'])){
		if ( ! horsetools_redirect_is_exempt() ) {
			ob_clean();
			header("Cache-Control: no-cache, no-store, must-revalidate");
			header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
			header("HTTP/1.1 503 Service Temporarily Unavailable");
			header("Status: 503 Service Temporarily Unavailable");
			header("Retry-After: 3600"); 
			include(HORSETOOLS_DIR . 'page/503.php');
			exit();
		}
	}
}
add_action('init', 'horsetools_redirect_to_301');
// chuyển link 404 về trang chủ
if (isset($horsetools_redirects_options['redi2'])){
function horsetools_redirect_404_to_home() {
	global $horsetools_redirects_options;
	// ltrim the stored value before prefixing the slash: a value of "/evil.com"
	// would otherwise build "//evil.com", a protocol-relative absolute URL that
	// wp_redirect() honours. wp_safe_redirect() then confines the destination
	// to this host regardless.
	$target = !empty($horsetools_redirects_options['redi21'])
		? '/' . ltrim( (string) $horsetools_redirects_options['redi21'], '/' )
		: home_url();
    if (is_404()) {
        wp_safe_redirect($target);
        exit();
    }
}
add_action('template_redirect', 'horsetools_redirect_404_to_home');
}







