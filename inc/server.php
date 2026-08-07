<?php
/**
 * Horse Tools — which web server is actually in front of PHP.
 *
 * One question, asked because several things in this plugin used to assume the
 * answer: does this server read `.htaccess`?
 *
 * A plugin cannot write server configuration, so the usual way to hide a file it
 * has to keep inside wp-content is to drop an `.htaccess` next to it saying
 * "deny". On Apache that works. On nginx the file is inert — nginx has never
 * read `.htaccess` and never will — and the directory is served exactly as if
 * the deny were not there. Worse, the `.htaccess` itself is handed over on
 * request, so the one artefact that was supposed to be the protection is instead
 * a 134-byte confirmation that somebody thought protection was needed here.
 *
 * That was checked rather than assumed: on a live nginx host, requesting
 * `wp-content/horsetools-anchor/.htaccess` returned HTTP 200 with the whole file
 * as `application/octet-stream`.
 *
 * The answer is read from `$_SERVER['SERVER_SOFTWARE']` and nothing else. A
 * loopback HTTP request would be a stronger test and is deliberately not used:
 * it is slow on every page load, and the hosts that block loopback requests are
 * exactly the hosts where this most needs an answer. SERVER_SOFTWARE costs
 * nothing and is exact for the case that matters.
 *
 * What this cannot see: nginx sitting in front of Apache and serving the static
 * files itself. PHP is handed to Apache there, so SERVER_SOFTWARE says Apache
 * while the `.htaccess` is never consulted for a plain file. That under-reports,
 * which is the safe direction for a check whose output is a warning — but it is
 * also why nothing in this plugin is allowed to *depend* on the answer. The
 * files are protected by their extension; this is only used to say so honestly.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * The server banner, lowercased.
 *
 * WP-Cron run from a real crontab, and WP-CLI, have no SERVER_SOFTWARE at all —
 * there is no web server in that request. So the value is remembered the first
 * time a web request sees it, and the remembered value is what the cron-side
 * code reads. Without that, the check-in message composed by cron would report a
 * different answer from the one the admin screens report, which is worse than
 * reporting nothing.
 *
 * @return string '' when it has never been seen.
 */
function horsetools_server_software() {
	$live = horsetools_server_software_live();
	if ( '' !== $live ) {
		return $live;
	}
	$seen = get_option( 'horsetools_server_software', '' );
	return is_string( $seen ) ? $seen : '';
}

/**
 * The banner this request was actually served with, or '' when there is none.
 *
 * The server sets this, not the client, so it is not attacker input in any
 * ordinary sense — but it is still a `$_SERVER` string that ends up in an option
 * and on an admin screen, and treating it like one costs nothing.
 *
 * @return string
 */
function horsetools_server_software_live() {
	if ( ! isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
		return '';
	}
	return strtolower( trim( sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) ) );
}

/**
 * Remember it, so cron and WP-CLI can answer the same question.
 *
 * Written only when it changes, so the normal case is one read of an option that
 * is already in the alloptions cache and no write at all.
 */
function horsetools_server_software_record() {
	$live = horsetools_server_software_live();
	if ( '' === $live ) {
		return;
	}
	if ( get_option( 'horsetools_server_software', '' ) !== $live ) {
		update_option( 'horsetools_server_software', $live, false );
	}
}
add_action( 'admin_init', 'horsetools_server_software_record', 1 );

/**
 * Does this server read `.htaccess`?
 *
 * Three answers, not two. "Unknown" is a real state and it is kept separate on
 * purpose: a warning that fires because the banner was a string nobody here has
 * seen before is a warning that teaches people to close warnings.
 *
 * @return string 'honoured' | 'ignored' | 'unknown'
 */
function horsetools_htaccess_state() {
	$sw = horsetools_server_software();
	if ( '' === $sw ) {
		return 'unknown';
	}
	// Certain, both ways. Anything not on either list is left as unknown rather
	// than guessed at.
	foreach ( array( 'apache', 'litespeed', 'lsws' ) as $reads ) {
		if ( false !== strpos( $sw, $reads ) ) {
			return 'honoured';
		}
	}
	foreach ( array( 'nginx', 'caddy', 'lighttpd', 'microsoft-iis', 'openresty' ) as $ignores ) {
		if ( false !== strpos( $sw, $ignores ) ) {
			return 'ignored';
		}
	}
	return 'unknown';
}

/**
 * @return bool True only when we are sure the file is inert.
 */
function horsetools_htaccess_ignored() {
	return 'ignored' === horsetools_htaccess_state();
}

/**
 * The one line every directory this plugin creates inside wp-content starts its
 * files with.
 *
 * `<?php exit; ?>` at the top of a file whose name ends in `.php` is the only
 * protection that holds on every server, because it is not protection the server
 * has to be configured to apply — it is the file's own first instruction. The
 * request is handed to PHP because of the extension, PHP stops immediately, and
 * the response is zero bytes. Apache, nginx, Caddy, IIS: same answer.
 *
 * @return string
 */
function horsetools_php_guard() {
	return "<?php exit; ?>\n";
}

/**
 * Strip that first line back off when the contents are read for display.
 *
 * Matches on `<?php` rather than on the exact guard, so a file written by an
 * older version, or one the owner has put their own header on, still reads back
 * as its contents rather than as its contents with a stray tag on top.
 *
 * @param string $raw
 * @return string
 */
function horsetools_php_guard_strip( $raw ) {
	if ( 0 !== strpos( $raw, '<?php' ) ) {
		return $raw;
	}
	$nl = strpos( $raw, "\n" );
	return ( false === $nl ) ? '' : substr( $raw, $nl + 1 );
}

/**
 * The usual pair of doorstops for a directory this plugin owns.
 *
 * Kept even though neither is what the protection rests on. The index.php stops
 * a directory listing where one is enabled; the `.htaccess` is correct and free
 * on Apache. Writing them is not a claim that they are sufficient — the files
 * inside are named so that they do not need either.
 *
 * @param string $dir Absolute path to an existing directory.
 */
function horsetools_guard_directory( $dir ) {
	if ( ! is_dir( $dir ) ) {
		return;
	}
	if ( ! file_exists( $dir . '/index.php' ) ) {
		@file_put_contents( $dir . '/index.php', "<?php\n// Silence is golden.\n" ); // phpcs:ignore
	}
	if ( ! file_exists( $dir . '/.htaccess' ) ) {
		// Both spellings, each behind its own guard. An unguarded "Require" is a
		// 500 on Apache 2.2, and an unguarded "Deny" is deprecated on 2.4.
		@file_put_contents( // phpcs:ignore
			$dir . '/.htaccess',
			"<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n"
			. "<IfModule !mod_authz_core.c>\n\tOrder allow,deny\n\tDeny from all\n</IfModule>\n"
		);
	}
}
