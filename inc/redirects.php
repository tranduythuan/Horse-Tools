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

/* =========================================================================
 * 404 log
 *
 * Feeds the redirect manager: instead of typing redirect rules blind, the
 * admin sees which dead URLs are actually being hit and turns the busy ones
 * into redirects. Off by default; records only anonymous GET page requests,
 * deduplicated by URL, capped so a scanner cannot fill the table.
 * ====================================================================== */

/**
 * @return string Fully-qualified log table name.
 */
function horsetools_404_table() {
	global $wpdb;
	return $wpdb->prefix . 'horsetools_404';
}

/**
 * Create the table once, via dbDelta. Guarded by a stored schema version so it
 * is a no-op on every admin load after the first.
 */
function horsetools_404_install() {
	if ( '1' === get_option( 'horsetools_404_db' ) ) {
		return;
	}
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table   = horsetools_404_table();
	$charset = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE {$table} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		url_hash char(32) NOT NULL,
		url varchar(255) NOT NULL,
		referrer varchar(255) NOT NULL DEFAULT '',
		hits bigint(20) unsigned NOT NULL DEFAULT 1,
		last_seen datetime NOT NULL,
		status varchar(20) NOT NULL DEFAULT 'new',
		PRIMARY KEY  (id),
		UNIQUE KEY url_hash (url_hash),
		KEY status (status),
		KEY hits (hits)
	) {$charset};";
	dbDelta( $sql );
	update_option( 'horsetools_404_db', '1', false );
}
add_action( 'admin_init', 'horsetools_404_install' );

/**
 * Is 404 logging switched on?
 */
function horsetools_404_logging_on() {
	global $horsetools_redirects_options;
	return ! empty( $horsetools_redirects_options['redi-404log'] );
}

/**
 * Record a 404, if it is the kind worth recording.
 *
 * Deliberately narrow: anonymous GET requests for real pages only. Logged-in
 * users, POSTs, obvious crawlers, and asset-looking paths (.css/.js/images/
 * fonts) are skipped so the log stays about content URLs a redirect could fix.
 */
function horsetools_404_record() {
	if ( ! is_404() || ! horsetools_404_logging_on() ) {
		return;
	}
	if ( '1' !== get_option( 'horsetools_404_db' ) ) {
		return; // table not created yet — next admin load builds it
	}
	if ( is_user_logged_in() ) {
		return;
	}
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'GET' !== strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) ) {
		return;
	}
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
	if ( '' === $ua || preg_match( '/bot|crawl|spider|slurp|bing|google|yandex|baidu|facebookexternalhit|preview|monitor|curl|wget/i', $ua ) ) {
		return;
	}

	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? rawurldecode( (string) wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = wp_parse_url( $uri, PHP_URL_PATH );
	if ( ! is_string( $path ) || '' === $path ) {
		return;
	}
	if ( preg_match( '/\.(css|js|png|jpe?g|gif|webp|svg|ico|woff2?|ttf|eot|map|bmp|mp4|webm|pdf)$/i', $path ) ) {
		return;
	}
	$path = substr( $path, 0, 255 );
	$ref  = isset( $_SERVER['HTTP_REFERER'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ), 0, 255 ) : '';

	global $wpdb;
	$table = horsetools_404_table();
	$hash  = md5( $path );

	$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE url_hash = %s", $hash ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name
	if ( $id ) {
		$wpdb->query( $wpdb->prepare( "UPDATE {$table} SET hits = hits + 1, last_seen = %s WHERE id = %d", current_time( 'mysql' ), $id ) ); // phpcs:ignore WordPress.DB
		return;
	}

	// Hard cap on distinct URLs: bump existing rows forever, but stop inserting
	// new ones once the table is large, so a URL-fuzzing scanner cannot grow it
	// without bound.
	$rows = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB
	if ( $rows >= 2000 ) {
		return;
	}
	$wpdb->insert(
		$table,
		array(
			'url_hash'  => $hash,
			'url'       => $path,
			'referrer'  => $ref,
			'hits'      => 1,
			'last_seen' => current_time( 'mysql' ),
			'status'    => 'new',
		),
		array( '%s', '%s', '%s', '%d', '%s', '%s' )
	);
}
add_action( 'template_redirect', 'horsetools_404_record', 1 );

/**
 * The most-hit unresolved 404s, for the admin table.
 *
 * @param int $limit
 * @return array
 */
function horsetools_404_recent( $limit = 100 ) {
	if ( '1' !== get_option( 'horsetools_404_db' ) ) {
		return array();
	}
	global $wpdb;
	$table = horsetools_404_table();
	return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE status = 'new' ORDER BY hits DESC, last_seen DESC LIMIT %d", $limit ) ); // phpcs:ignore WordPress.DB
}

/**
 * Ignore / mark-redirected / delete / clear, from the log table.
 */
function horsetools_404_action_ajax() {
	check_ajax_referer( 'horsetools_404_action', 'security' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'forbidden', 403 );
	}
	global $wpdb;
	$table = horsetools_404_table();
	$do    = isset( $_POST['do'] ) ? sanitize_key( wp_unslash( $_POST['do'] ) ) : '';
	$id    = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

	switch ( $do ) {
		case 'ignore':
			$wpdb->update( $table, array( 'status' => 'ignored' ), array( 'id' => $id ) );
			break;
		case 'redirected':
			$wpdb->update( $table, array( 'status' => 'redirected' ), array( 'id' => $id ) );
			break;
		case 'delete':
			$wpdb->delete( $table, array( 'id' => $id ) );
			break;
		case 'clear':
			$wpdb->query( "DELETE FROM {$table}" ); // phpcs:ignore WordPress.DB
			break;
		default:
			wp_send_json_error( 'bad action', 400 );
	}
	wp_send_json_success();
}
add_action( 'wp_ajax_horsetools_404_log_action', 'horsetools_404_action_ajax' );







