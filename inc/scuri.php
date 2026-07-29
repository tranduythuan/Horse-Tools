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
	function horsetools_login_lock_minutes() {
		global $horsetools_options;
		$m = isset( $horsetools_options['scuri-login-mins'] ) ? (int) $horsetools_options['scuri-login-mins'] : 15;
		return $m > 0 ? $m : 15;
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
					/* translators: %d: minutes remaining. */
					esc_html__( 'Too many failed attempts. Try again in about %d minute(s).', 'horse-tools' ),
					(int) ceil( ( $data['locked'] - time() ) / 60 )
				)
			);
		}
		return $user;
	}
	add_filter( 'authenticate', 'horsetools_login_gate', 30, 2 );

	function horsetools_login_record_fail() {
		$key    = horsetools_login_key( horsetools_login_ip() );
		$window = horsetools_login_lock_minutes() * MINUTE_IN_SECONDS;
		$data   = get_transient( $key );
		if ( ! is_array( $data ) ) {
			$data = array( 'count' => 0, 'locked' => 0 );
		}
		$data['count']++;
		if ( $data['count'] >= horsetools_login_max() ) {
			$data['locked'] = time() + $window;
			$data['count']  = 0;
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
		delete_transient( horsetools_login_key( horsetools_login_ip() ) );
	}
	add_action( 'wp_login', 'horsetools_login_clear' );
}
