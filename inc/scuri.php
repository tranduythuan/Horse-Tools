<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;

/* -------------------------------------------------------------------------
 * Kept from the original: genuinely useful, low-risk hardening.
 * ---------------------------------------------------------------------- */

# Require authentication for the REST API (blocks anonymous REST).
# KEPT AT FULL STRENGTH by request. The UI carries a red warning about what
# this breaks (WooCommerce Store API, REST form plugins, block-theme comments,
# oEmbed) so the choice is informed.
if ( isset( $horsetools_options['scuri-off1'] ) ) {
	add_filter( 'rest_authentication_errors', function ( $result ) {
		if ( true === $result || is_wp_error( $result ) ) {
			return $result;
		}
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_not_logged_in', __( 'You are not logged in', 'horse-tools' ), array( 'status' => 401 ) );
		}
		return $result;
	} );
}

# Disable XML-RPC. The highest-value item here: xmlrpc.php is the top
# brute-force and pingback-amplification target and almost nothing but Jetpack
# uses it in 2026. (The old pre_option_enable_xmlrpc line was dead — no such
# core option since 3.5 — and has been dropped.)
if ( isset( $horsetools_options['scuri-off2'] ) ) {
	add_filter( 'wp_xmlrpc_server_class', '__return_false' );
	add_filter( 'xmlrpc_enabled', '__return_false' );
}

# Remove wp-embed.js.
if ( isset( $horsetools_options['scuri-off3'] ) ) {
	function horsetools_deregister_scripts() {
		wp_deregister_script( 'wp-embed' );
	}
	add_action( 'wp_footer', 'horsetools_deregister_scripts' );
}

# Remove the X-Pingback header.
if ( isset( $horsetools_options['scuri-off4'] ) ) {
	function horsetools_adminify_remove_pingback_head( $headers ) {
		if ( isset( $headers['X-Pingback'] ) ) {
			unset( $headers['X-Pingback'] );
		}
		return $headers;
	}
	add_filter( 'wp_headers', 'horsetools_adminify_remove_pingback_head' );
}

# Remove generator / RSD / WLW header clutter. (start_post_rel_link,
# index_rel_link and parent_post_rel_link were removed from core in 3.3, so
# those three remove_action calls have been dropped as dead code.)
if ( isset( $horsetools_options['scuri-off5'] ) ) {
	function horsetools_remove_header_info() {
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	}
	add_action( 'init', 'horsetools_remove_header_info' );
}

# Disable feeds (returns 410 Gone; see the note on the function).
if ( isset( $horsetools_options['scuri-off6'] ) ) {
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
	add_action( 'do_feed_rss2', 'horsetools_disable_feed', 1 );
	add_action( 'do_feed_rdf', 'horsetools_disable_feed', 1 );
	add_action( 'do_feed_rss', 'horsetools_disable_feed', 1 );
	add_action( 'do_feed_atom', 'horsetools_disable_feed', 1 );
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
}

# Remove the WordPress version from the generator tag. Harmless tidy-up.
if ( isset( $horsetools_options['scuri-verof2'] ) ) {
	add_filter( 'the_generator', '__return_empty_string' );
}

/*
 * REMOVED in 1.1.0, and not coming back:
 *
 *   scuri-up1  "block non-image uploads" — ran on wp_handle_upload_prefilter,
 *              so it blocked plugin/theme ZIP installs and the WXR importer and
 *              broke the plugin's own SVG upload, while protecting nothing (the
 *              real risk is PHP execution in uploads, a server-config matter).
 *
 *   scuri-verof1 "remove ?ver= from CSS/JS" — ?ver is the cache-buster; stripping
 *              it serves stale assets after every update. Conceals nothing.
 *
 *   scuri-sql1 "prevent SQL injection/XSS" — ran only for logged-in non-admins,
 *              matched four fixed strings in the URI, and its 255-char cap broke
 *              long legitimate admin URLs. Real SQLi defence is $wpdb->prepare().
 *
 * Their replacements are the four features below.
 */

/* -------------------------------------------------------------------------
 * D. Disable the theme / plugin file editor
 *
 * DISALLOW_FILE_EDIT closes the built-in code editor under Appearance and
 * Plugins — a classic post-compromise escalation path. Defined at plugin-load
 * time, which is before the capability is ever evaluated.
 * ---------------------------------------------------------------------- */
if ( isset( $horsetools_options['scuri-fileedit1'] ) && ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/* -------------------------------------------------------------------------
 * B. User-enumeration protection
 *
 * Done properly, not by hiding one endpoint: block ?author=N scans, drop the
 * users REST route for anonymous requests, strip the author URL from oEmbed,
 * and make login errors generic so they do not reveal whether the username or
 * the password was wrong.
 * ---------------------------------------------------------------------- */
if ( isset( $horsetools_options['scuri-enum1'] ) ) {
	function horsetools_block_author_scan() {
		if ( is_admin() || is_user_logged_in() ) {
			return;
		}
		if ( isset( $_GET['author'] ) && preg_match( '/^\d+$/', (string) wp_unslash( $_GET['author'] ) ) ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}
	add_action( 'template_redirect', 'horsetools_block_author_scan' );

	function horsetools_hide_users_rest( $endpoints ) {
		if ( is_user_logged_in() ) {
			return $endpoints;
		}
		foreach ( array( '/wp/v2/users', '/wp/v2/users/(?P<id>[\d]+)' ) as $route ) {
			if ( isset( $endpoints[ $route ] ) ) {
				unset( $endpoints[ $route ] );
			}
		}
		return $endpoints;
	}
	add_filter( 'rest_endpoints', 'horsetools_hide_users_rest' );

	add_filter( 'oembed_response_data', function ( $data ) {
		unset( $data['author_url'], $data['author_name'] );
		return $data;
	} );

	add_filter( 'login_errors', function () {
		return esc_html__( 'Login failed. Check your details and try again.', 'horse-tools' );
	} );
}

/* -------------------------------------------------------------------------
 * C. HTTP security headers
 *
 * Sent on front-end responses only. HSTS is gated on HTTPS; CSP is a free-text
 * advanced field (sanitize_text_field strips the CR/LF that would allow header
 * injection).
 * ---------------------------------------------------------------------- */
if ( isset( $horsetools_options['scuri-head1'] ) ) {
	function horsetools_security_headers() {
		if ( is_admin() ) {
			return;
		}
		global $horsetools_options;
		if ( ! empty( $horsetools_options['scuri-head-xfo'] ) ) {
			header( 'X-Frame-Options: SAMEORIGIN' );
		}
		if ( ! empty( $horsetools_options['scuri-head-nosniff'] ) ) {
			header( 'X-Content-Type-Options: nosniff' );
		}
		if ( ! empty( $horsetools_options['scuri-head-ref'] ) ) {
			header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		}
		if ( ! empty( $horsetools_options['scuri-head-perm'] ) ) {
			header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
		}
		if ( ! empty( $horsetools_options['scuri-head-hsts'] ) && is_ssl() ) {
			header( 'Strict-Transport-Security: max-age=15552000; includeSubDomains' );
		}
		if ( ! empty( $horsetools_options['scuri-head-csp'] ) ) {
			$csp = sanitize_text_field( $horsetools_options['scuri-head-csp'] );
			if ( '' !== $csp ) {
				header( 'Content-Security-Policy: ' . $csp );
			}
		}
	}
	add_action( 'send_headers', 'horsetools_security_headers' );
}

/* -------------------------------------------------------------------------
 * A. Limit failed login attempts
 *
 * Throttle by IP: after N failures the IP is locked out for M minutes. This is
 * the real brute-force defence that replaces the removed "SQL protection"
 * theatre. State lives in transients, so it self-expires and needs no table.
 * ---------------------------------------------------------------------- */
if ( isset( $horsetools_options['scuri-login1'] ) ) {

	function horsetools_login_ip() {
		global $horsetools_options;
		// By default we use REMOTE_ADDR — the real TCP peer address, which the
		// client CANNOT forge. Proxy headers (X-Forwarded-For, CF-Connecting-IP…)
		// ARE forgeable by anyone who can reach the server directly, so trusting
		// them is OFF by default. If we trusted them on a directly-reachable site,
		// an attacker could send a different fake IP on every try (never getting
		// locked out) or spoof an innocent visitor's IP to lock THEM out.
		//
		// Only enable "site is behind Cloudflare / a proxy" (scuri-login-proxy)
		// when the site is reachable ONLY through that proxy — then these headers
		// are set by the proxy itself and are trustworthy. We take the FIRST valid
		// IP (the original client; the rest of the chain are the proxies).
		if ( ! empty( $horsetools_options['scuri-login-proxy'] ) ) {
			foreach ( array( 'HTTP_CF_CONNECTING_IP', 'HTTP_TRUE_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP' ) as $header ) {
				if ( empty( $_SERVER[ $header ] ) ) {
					continue;
				}
				$raw   = sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) );
				$first = trim( explode( ',', $raw )[0] );
				if ( filter_var( $first, FILTER_VALIDATE_IP ) ) {
					return $first;
				}
			}
		}
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return ( '' !== $ip && filter_var( $ip, FILTER_VALIDATE_IP ) ) ? $ip : 'unknown';
	}
	function horsetools_login_key( $ip ) {
		return 'horsetools_lla_' . md5( $ip );
	}
	function horsetools_login_max() {
		global $horsetools_options;
		$n = isset( $horsetools_options['scuri-login-max'] ) ? (int) $horsetools_options['scuri-login-max'] : 5;
		return $n > 0 ? $n : 5;
	}
	// Lock-out length in seconds, from the number + its unit (minutes/hours/days).
	// The stored value stays in 'scuri-login-mins' for backward compatibility; the
	// unit 'scuri-login-unit' defaults to minutes so existing sites are unchanged.
	function horsetools_login_lock_seconds() {
		global $horsetools_options;
		$n = isset( $horsetools_options['scuri-login-mins'] ) ? (int) $horsetools_options['scuri-login-mins'] : 15;
		if ( $n <= 0 ) {
			$n = 15;
		}
		$unit = isset( $horsetools_options['scuri-login-unit'] ) ? $horsetools_options['scuri-login-unit'] : 'minutes';
		if ( 'days' === $unit ) {
			return $n * DAY_IN_SECONDS;
		}
		if ( 'hours' === $unit ) {
			return $n * HOUR_IN_SECONDS;
		}
		return $n * MINUTE_IN_SECONDS;
	}

	// Reject the attempt up front while the IP is locked out.
	function horsetools_login_gate( $user, $username = '' ) {
		if ( '' === $username ) {
			return $user; // login page load, not an attempt
		}
		$data = get_transient( horsetools_login_key( horsetools_login_ip() ) );
		if ( is_array( $data ) && ! empty( $data['locked'] ) && $data['locked'] > time() ) {
			return new WP_Error(
				'horsetools_locked',
				sprintf(
					/* translators: %s: human-readable time remaining, e.g. "15 minutes" or "1 day". */
					esc_html__( 'Too many failed attempts. Try again in about %s.', 'horse-tools' ),
					human_time_diff( time(), $data['locked'] )
				)
			);
		}
		return $user;
	}
	add_filter( 'authenticate', 'horsetools_login_gate', 30, 2 );

	function horsetools_login_record_fail() {
		$key    = horsetools_login_key( horsetools_login_ip() );
		$window = horsetools_login_lock_seconds();
		$data   = get_transient( $key );
		if ( ! is_array( $data ) ) {
			$data = array( 'count' => 0, 'locked' => 0 );
		}
		$data['count']++;
		if ( $data['count'] >= horsetools_login_max() ) {
			$data['locked'] = time() + $window;
			$data['count']  = 0;
			// Track locked IPs in an option so the admin "Reset lockouts" button
			// can clear them all — transients live in the object cache (Redis) on
			// some hosts, where they can't be enumerated, so we keep our own list.
			$idx = get_option( 'horsetools_lla_index', array() );
			if ( ! is_array( $idx ) ) {
				$idx = array();
			}
			$idx[ horsetools_login_ip() ] = $data['locked'];
			update_option( 'horsetools_lla_index', $idx, false );
			if ( ! empty( $GLOBALS['horsetools_options']['scuri-login-mail'] ) ) {
				wp_mail(
					get_option( 'admin_email' ),
					esc_html__( 'A login was locked out on your site', 'horse-tools' ),
					sprintf(
						/* translators: 1: IP address, 2: site URL. */
						esc_html__( 'The address %1$s was locked out after repeated failed logins on %2$s.', 'horse-tools' ),
						horsetools_login_ip(),
						home_url( '/' )
					)
				);
			}
		}
		set_transient( $key, $data, $window );
	}
	add_action( 'wp_login_failed', 'horsetools_login_record_fail' );

	function horsetools_login_clear() {
		$ip = horsetools_login_ip();
		delete_transient( horsetools_login_key( $ip ) );
		$idx = get_option( 'horsetools_lla_index', array() );
		if ( is_array( $idx ) && isset( $idx[ $ip ] ) ) {
			unset( $idx[ $ip ] );
			update_option( 'horsetools_lla_index', $idx, false );
		}
	}
	add_action( 'wp_login', 'horsetools_login_clear' );

	// Admin "Reset all login lockouts" button. Clears every current lockout so a
	// mistakenly locked-out address (including your own) gets straight back in —
	// important now that the lockout can be set in days.
	add_action( 'wp_ajax_horsetools_lla_reset', 'horsetools_lla_reset_ajax' );
	function horsetools_lla_reset_ajax() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}
		check_ajax_referer( 'horsetools_lla', 'nonce' );
		$idx = get_option( 'horsetools_lla_index', array() );
		if ( is_array( $idx ) ) {
			foreach ( array_keys( $idx ) as $ip ) {
				delete_transient( horsetools_login_key( (string) $ip ) );
			}
		}
		delete_option( 'horsetools_lla_index' );
		// Belt-and-suspenders for transients stored in the DB (no object cache).
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\\_transient\\_horsetools\\_lla\\_%' OR option_name LIKE '\\_transient\\_timeout\\_horsetools\\_lla\\_%'" );
		wp_send_json_success();
	}
}

/* -------------------------------------------------------------------------
 * E. Privacy — self-host Google Fonts + external-request scanner
 *
 * Loading fonts from fonts.googleapis.com sends every visitor's IP to Google,
 * which German courts have ruled a GDPR violation. The self-host option
 * downloads the font files here once and rewrites the stylesheet to serve them
 * from this domain, so no visitor request ever reaches Google. The scanner is
 * the diagnosis: it fetches this site's own home page and lists every third-
 * party host it pulls from, flagging the privacy-sensitive ones.
 *
 * Everything here is admin-triggered or gated behind an explicit opt-in; the
 * plugin never phones home on its own.
 * ---------------------------------------------------------------------- */

/** uploads/horsetools-gfonts path + URL. */
function horsetools_gfonts_dir() {
	$up = wp_upload_dir();
	return array(
		'path' => trailingslashit( $up['basedir'] ) . 'horsetools-gfonts',
		'url'  => trailingslashit( $up['baseurl'] ) . 'horsetools-gfonts',
	);
}

/** Canonical form of a Google Fonts stylesheet URL, for stable matching. */
function horsetools_gfont_normalise_url( $url ) {
	$url = trim( (string) $url );
	if ( 0 === strpos( $url, '//' ) ) {
		$url = 'https:' . $url;
	}
	$url = preg_replace( '#^http://#i', 'https://', $url );
	$p   = wp_parse_url( $url );
	if ( empty( $p['host'] ) ) {
		return $url;
	}
	$out = 'https://' . strtolower( $p['host'] ) . ( isset( $p['path'] ) ? $p['path'] : '' );
	if ( ! empty( $p['query'] ) ) {
		$out .= '?' . $p['query'];
	}
	return $out;
}

/** Does a body start with a known font-file signature? */
function horsetools_is_font_binary( $body ) {
	$sig = substr( (string) $body, 0, 4 );
	return in_array( $sig, array( 'wOF2', 'wOFF', 'OTTO', 'true', 'ttcf' ), true ) || "\x00\x01\x00\x00" === $sig;
}

/**
 * Download one Google Fonts stylesheet and its font files into uploads, and
 * write a local copy of the CSS pointing at them.
 *
 * @param string $css_url A fonts.googleapis.com stylesheet URL.
 * @return array|WP_Error array( css => basename, fonts => int ) or error.
 */
function horsetools_gfont_localise_one( $css_url ) {
	$dir = horsetools_gfonts_dir();
	if ( ! wp_mkdir_p( $dir['path'] ) ) {
		return new WP_Error( 'mkdir', __( 'Could not create the fonts folder in uploads.', 'horse-tools' ) );
	}
	// A modern browser UA is required or Google serves old TTF instead of woff2.
	$ua  = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
	$res = horsetools_safe_fetch( $css_url, array( 'user-agent' => $ua, 'timeout' => 15 ) );
	if ( is_wp_error( $res ) ) {
		return $res;
	}
	if ( 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		return new WP_Error( 'http', __( 'Google did not return the stylesheet (HTTP error).', 'horse-tools' ) );
	}
	$css = wp_remote_retrieve_body( $res );
	if ( '' === trim( $css ) || false === stripos( $css, '@font-face' ) ) {
		return new WP_Error( 'nocss', __( 'That did not look like a Google Fonts stylesheet.', 'horse-tools' ) );
	}
	if ( ! preg_match_all( '#url\((https://fonts\.gstatic\.com/[^)]+)\)#i', $css, $m ) ) {
		return new WP_Error( 'nofiles', __( 'No font files were found in the stylesheet.', 'horse-tools' ) );
	}

	$map   = array();
	$count = 0;
	foreach ( array_unique( $m[1] ) as $furl ) {
		$ext = strtolower( (string) pathinfo( (string) wp_parse_url( $furl, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
		$ext = preg_replace( '/[^a-z0-9]/', '', $ext );
		if ( ! in_array( $ext, array( 'woff2', 'woff', 'ttf' ), true ) ) {
			continue;
		}
		$base = md5( $furl ) . '.' . $ext;
		$dest = trailingslashit( $dir['path'] ) . $base;
		if ( ! file_exists( $dest ) ) {
			$fr = horsetools_safe_fetch( $furl, array( 'user-agent' => $ua, 'timeout' => 20 ) );
			if ( is_wp_error( $fr ) || 200 !== (int) wp_remote_retrieve_response_code( $fr ) ) {
				continue;
			}
			$body = wp_remote_retrieve_body( $fr );
			if ( '' === $body || ! horsetools_is_font_binary( $body ) ) {
				continue;
			}
			file_put_contents( $dest, $body ); // phpcs:ignore WordPress.WP.AlternativeFunctions -- controlled name inside uploads
		}
		$map[ $furl ] = trailingslashit( $dir['url'] ) . $base;
		$count++;
	}
	if ( ! $count ) {
		return new WP_Error( 'nodl', __( 'The font files could not be downloaded.', 'horse-tools' ) );
	}

	$local_css = strtr( $css, $map );
	$css_base  = md5( horsetools_gfont_normalise_url( $css_url ) ) . '.css';
	file_put_contents( trailingslashit( $dir['path'] ) . $css_base, $local_css ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	return array( 'css' => $css_base, 'fonts' => $count );
}

# Front end: rewrite enqueued Google Fonts to the local copies.
if ( isset( $horsetools_options['scuri-gfont1'] ) ) {
	function horsetools_gfont_rewrite() {
		$map = get_option( 'horsetools_gfont_local', array() );
		if ( ! is_array( $map ) || empty( $map ) ) {
			return; // nothing localised yet — do not touch the page
		}
		global $wp_styles;
		if ( ! ( $wp_styles instanceof WP_Styles ) ) {
			return;
		}
		$dir = horsetools_gfonts_dir();
		$ver = (int) get_option( 'horsetools_gfont_ver', 1 );
		foreach ( $wp_styles->registered as $handle => $obj ) {
			if ( empty( $obj->src ) ) {
				continue;
			}
			if ( 'fonts.googleapis.com' !== wp_parse_url( $obj->src, PHP_URL_HOST ) ) {
				continue;
			}
			$norm = horsetools_gfont_normalise_url( $obj->src );
			if ( isset( $map[ $norm ] ) ) {
				$wp_styles->registered[ $handle ]->src  = trailingslashit( $dir['url'] ) . $map[ $norm ] . '?v=' . $ver;
				$wp_styles->registered[ $handle ]->deps = array();
			}
		}
	}
	add_action( 'wp_enqueue_scripts', 'horsetools_gfont_rewrite', 999 );

	// Drop preconnect / dns-prefetch hints to Google's font hosts.
	add_filter( 'wp_resource_hints', function ( $urls ) {
		return array_filter( $urls, function ( $u ) {
			$href = is_array( $u ) ? ( isset( $u['href'] ) ? $u['href'] : '' ) : $u;
			$host = wp_parse_url( $href, PHP_URL_HOST );
			return ! in_array( $host, array( 'fonts.googleapis.com', 'fonts.gstatic.com' ), true );
		} );
	}, 10, 1 );
}

/** Label and privacy flag for a third-party host. */
function horsetools_privacy_classify_host( $h ) {
	$h     = strtolower( $h );
	$rules = array(
		'fonts.googleapis.com'  => array( 'Google Fonts', true ),
		'fonts.gstatic.com'     => array( 'Google Fonts', true ),
		'google-analytics.com'  => array( 'Analytics', true ),
		'googletagmanager.com'  => array( 'Tag Manager / tracking', true ),
		'doubleclick.net'       => array( 'Ad tracking', true ),
		'connect.facebook.net'  => array( 'Facebook', true ),
		'facebook.com'          => array( 'Facebook', true ),
		'gravatar.com'          => array( 'Gravatar (email hashes)', true ),
		'youtube-nocookie.com'  => array( 'YouTube (no-cookie)', false ),
		'youtube.com'           => array( 'YouTube embed', false ),
		'vimeo.com'             => array( 'Vimeo embed', false ),
		'cdnjs.cloudflare.com'  => array( 'CDN', false ),
		'jsdelivr.net'          => array( 'CDN', false ),
		'unpkg.com'             => array( 'CDN', false ),
		'bootstrapcdn.com'      => array( 'CDN', false ),
		'gstatic.com'           => array( 'Google', false ),
		'googleapis.com'        => array( 'Google API', false ),
	);
	foreach ( $rules as $needle => $info ) {
		if ( $h === $needle || substr( $h, -strlen( '.' . $needle ) ) === '.' . $needle ) {
			return array( 'type' => $info[0], 'flag' => (bool) $info[1] );
		}
	}
	return array( 'type' => __( 'External', 'horse-tools' ), 'flag' => false );
}

# AJAX: scan the site's own home page for third-party requests.
add_action( 'wp_ajax_horsetools_privacy_scan', 'horsetools_privacy_scan_ajax' );
function horsetools_privacy_scan_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'msg' => __( 'Permission denied.', 'horse-tools' ) ) );
	}
	check_ajax_referer( 'horsetools_privacy', 'nonce' );

	$home = home_url( '/' );
	// Same-origin fetch of our own home page: use the plain client (safe_fetch
	// would refuse a private/localhost address, which is normal in development).
	$res = wp_remote_get( $home, array( 'timeout' => 15, 'sslverify' => false, 'redirection' => 2, 'user-agent' => 'HorseTools privacy scan' ) );
	if ( is_wp_error( $res ) ) {
		wp_send_json_error( array( 'msg' => $res->get_error_message() ) );
	}
	$html = wp_remote_retrieve_body( $res );
	if ( '' === trim( $html ) ) {
		wp_send_json_error( array( 'msg' => __( 'The home page returned no HTML to scan.', 'horse-tools' ) ) );
	}

	$site_host = preg_replace( '/^www\./i', '', (string) wp_parse_url( $home, PHP_URL_HOST ) );
	$found     = array();
	if ( preg_match_all( '#\b(?:src|href)\s*=\s*["\']([^"\']+)["\']#i', $html, $m1 ) ) {
		$found = array_merge( $found, $m1[1] );
	}
	if ( preg_match_all( '#url\(\s*["\']?([^"\')]+)["\']?\s*\)#i', $html, $m2 ) ) {
		$found = array_merge( $found, $m2[1] );
	}

	$hosts  = array();
	$gfonts = array();
	foreach ( $found as $u ) {
		$u = trim( html_entity_decode( $u, ENT_QUOTES ) );
		if ( '' === $u || '#' === $u[0] ) {
			continue;
		}
		if ( 0 === stripos( $u, 'data:' ) || 0 === stripos( $u, 'javascript:' ) || 0 === stripos( $u, 'mailto:' ) || 0 === stripos( $u, 'tel:' ) ) {
			continue;
		}
		if ( 0 === strpos( $u, '//' ) ) {
			$u = 'https:' . $u;
		}
		$host = wp_parse_url( $u, PHP_URL_HOST );
		if ( ! $host ) {
			continue;
		}
		if ( strcasecmp( preg_replace( '/^www\./i', '', $host ), $site_host ) === 0 ) {
			continue;
		}
		$hosts[ $host ] = isset( $hosts[ $host ] ) ? $hosts[ $host ] + 1 : 1;
		if ( 'fonts.googleapis.com' === strtolower( $host ) ) {
			$gfonts[ horsetools_gfont_normalise_url( $u ) ] = 1;
		}
	}

	update_option( 'horsetools_gfont_seen', array_keys( $gfonts ), false );

	arsort( $hosts );
	$rows = array();
	foreach ( $hosts as $host => $c ) {
		$cls    = horsetools_privacy_classify_host( $host );
		$rows[] = array( 'host' => $host, 'count' => (int) $c, 'type' => $cls['type'], 'flag' => $cls['flag'] );
	}
	wp_send_json_success( array( 'rows' => $rows, 'gfonts' => count( $gfonts ), 'total' => count( $hosts ) ) );
}

# AJAX: download and self-host the Google Fonts the scan discovered.
add_action( 'wp_ajax_horsetools_gfonts_localise', 'horsetools_gfonts_localise_ajax' );
function horsetools_gfonts_localise_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'msg' => __( 'Permission denied.', 'horse-tools' ) ) );
	}
	check_ajax_referer( 'horsetools_privacy', 'nonce' );

	$urls = get_option( 'horsetools_gfont_seen', array() );
	if ( ! is_array( $urls ) || empty( $urls ) ) {
		wp_send_json_error( array( 'msg' => __( 'Run the scan first so there is something to self-host.', 'horse-tools' ) ) );
	}

	$map   = get_option( 'horsetools_gfont_local', array() );
	$map   = is_array( $map ) ? $map : array();
	$fonts = 0;
	$errs  = array();
	foreach ( $urls as $u ) {
		$r = horsetools_gfont_localise_one( $u );
		if ( is_wp_error( $r ) ) {
			$errs[] = $r->get_error_message();
			continue;
		}
		$map[ horsetools_gfont_normalise_url( $u ) ] = $r['css'];
		$fonts += (int) $r['fonts'];
	}
	if ( empty( $map ) ) {
		wp_send_json_error( array( 'msg' => $errs ? implode( ' | ', array_unique( $errs ) ) : __( 'Nothing could be self-hosted.', 'horse-tools' ) ) );
	}

	update_option( 'horsetools_gfont_local', $map, false );
	update_option( 'horsetools_gfont_ver', (int) get_option( 'horsetools_gfont_ver', 0 ) + 1, false );
	wp_send_json_success( array(
		'families' => count( $map ),
		'fonts'    => $fonts,
		'errors'   => array_values( array_unique( $errs ) ),
	) );
}
