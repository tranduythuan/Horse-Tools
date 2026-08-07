<?php
/**
 * Horse Tools — files in the web root that should not be there.
 *
 * Every WordPress site on the internet is probed continuously for the same
 * short list of mistakes: a copy of wp-config left behind, a database dump, a
 * .git directory, a forgotten phpinfo. Nothing about being probed is worth
 * reporting — it happens to everybody, every day, and an alert about it is one
 * the owner learns to ignore within a week.
 *
 * What is worth reporting is when one of those probes would have found
 * something. So this checks the site rather than the traffic: does the file
 * actually exist, here, in the web root, right now.
 *
 * The 404 log the redirects module already keeps then adds the second half of
 * the sentence — not "you are being scanned", which is noise, but "the thing
 * somebody went looking for on this site is here". That is the only reading of
 * a 404 log that earns an alarm.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Paths that should never sit in a web root, and why.
 *
 * Deliberately short. Every entry has to be something that is dangerous when
 * present and that has no legitimate reason to be there — a list padded with
 * maybes is a list nobody reads to the end of.
 *
 * @return array<string,array{why:string,grave:bool}> Path relative to ABSPATH.
 */
function horsetools_exposure_paths() {
	$secret = __( 'Contains database credentials or signing keys.', 'horse-tools' );
	$dump   = __( 'A database dump: everything on the site, downloadable.', 'horse-tools' );

	return array(
		'.env'                          => array( 'why' => $secret, 'grave' => true ),
		'wp-config.php.bak'             => array( 'why' => $secret, 'grave' => true ),
		'wp-config.php~'                => array( 'why' => $secret, 'grave' => true ),
		'wp-config.php.save'            => array( 'why' => $secret, 'grave' => true ),
		'wp-config.php.old'             => array( 'why' => $secret, 'grave' => true ),
		'wp-config.txt'                 => array( 'why' => $secret, 'grave' => true ),
		'wp-config.php.horsetools.bak'  => array( 'why' => $secret, 'grave' => true ),
		'.git/config'                   => array( 'why' => __( 'A .git directory exposes the whole source history, and often credentials with it.', 'horse-tools' ), 'grave' => true ),
		'.svn/entries'                  => array( 'why' => __( 'Version-control metadata exposes the source.', 'horse-tools' ), 'grave' => true ),
		'.ssh/id_rsa'                   => array( 'why' => __( 'A private key.', 'horse-tools' ), 'grave' => true ),
		'backup.sql'                    => array( 'why' => $dump, 'grave' => true ),
		'database.sql'                  => array( 'why' => $dump, 'grave' => true ),
		'dump.sql'                      => array( 'why' => $dump, 'grave' => true ),
		'backup.zip'                    => array( 'why' => __( 'A site archive, downloadable by anyone who guesses the name.', 'horse-tools' ), 'grave' => true ),
		'phpinfo.php'                   => array( 'why' => __( 'Publishes the server configuration, paths and loaded modules.', 'horse-tools' ), 'grave' => false ),
		'info.php'                      => array( 'why' => __( 'Usually a phpinfo page.', 'horse-tools' ), 'grave' => false ),
		'adminer.php'                   => array( 'why' => __( 'A database client. Anyone who finds it is one password from the database.', 'horse-tools' ), 'grave' => true ),
		'wp-content/debug.log'          => array( 'why' => __( 'PHP errors: server paths, fragments of queries, sometimes the contents of a failed request.', 'horse-tools' ), 'grave' => false ),
		'error_log'                     => array( 'why' => __( 'Server errors, including absolute paths.', 'horse-tools' ), 'grave' => false ),
	);
}

/**
 * Which of them are actually here.
 *
 * Checked on the filesystem rather than over HTTP. A loopback request is slower,
 * and the hosts where it fails outright are the ones this most needs to work on.
 * Everything in the list is inside the web root by construction, so "it exists"
 * and "it can be fetched" are the same statement on a default server.
 *
 * @return array<string,array> Path => row, worst first.
 */
function horsetools_exposure_scan() {
	$found = array();
	foreach ( horsetools_exposure_paths() as $rel => $row ) {
		$full = ABSPATH . $rel;
		if ( ! file_exists( $full ) ) {
			continue;
		}
		$row['path'] = $rel;
		$row['size'] = (int) @filesize( $full );
		$found[]     = $row;
	}
	usort( $found, function ( $a, $b ) {
		return ( $b['grave'] ? 1 : 0 ) <=> ( $a['grave'] ? 1 : 0 );
	} );
	return $found;
}

/**
 * Has anybody gone looking for these on this site?
 *
 * Reads the 404 log the redirects module keeps. On its own this is background
 * noise; alongside a file that exists it is the difference between "you have a
 * stray backup" and "you have a stray backup and somebody has been asking for
 * exactly that".
 *
 * @return array{total:int,last:string,paths:array<string,int>}
 */
function horsetools_exposure_probes() {
	global $wpdb;
	$empty = array( 'total' => 0, 'last' => '', 'paths' => array() );

	$table = $wpdb->prefix . 'horsetools_404';
	// The log belongs to the redirects module, which may not be switched on.
	$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ); // phpcs:ignore WordPress.DB
	if ( $exists !== $table ) {
		return $empty;
	}

	// Match on the file name, so /.env and /wp/.env and /old/backup.sql all
	// count. Bounded to the list above rather than guessing at what looks
	// suspicious, which is how these turn into noise.
	$likes = array();
	$args  = array();
	foreach ( array_keys( horsetools_exposure_paths() ) as $rel ) {
		$likes[] = 'url LIKE %s';
		$args[]  = '%' . $wpdb->esc_like( basename( $rel ) ) . '%';
	}
	if ( ! $likes ) {
		return $empty;
	}

	$sql  = "SELECT url, hits, last_seen FROM {$table} WHERE " . implode( ' OR ', $likes ) . ' ORDER BY hits DESC LIMIT 40';
	$rows = $wpdb->get_results( $wpdb->prepare( $sql, $args ) ); // phpcs:ignore WordPress.DB
	if ( ! $rows ) {
		return $empty;
	}

	$out = array( 'total' => 0, 'last' => '', 'paths' => array() );
	foreach ( $rows as $r ) {
		$out['total']            += (int) $r->hits;
		$out['paths'][ $r->url ]  = (int) $r->hits;
		if ( $r->last_seen > $out['last'] ) {
			$out['last'] = $r->last_seen;
		}
	}
	return $out;
}

/**
 * The whole picture, computed once a day.
 *
 * Nineteen stat() calls and one query is not much, but it is not nothing on
 * every admin page load, and the answer does not change between two of them.
 *
 * @return array{found:array,probes:array}
 */
function horsetools_exposure_status() {
	static $memo = null;
	if ( null !== $memo ) {
		return $memo;
	}
	$cached = get_transient( 'horsetools_exposure' );
	if ( is_array( $cached ) ) {
		$memo = $cached;
		return $memo;
	}
	$memo = array(
		'found'  => horsetools_exposure_scan(),
		'probes' => horsetools_exposure_probes(),
	);
	set_transient( 'horsetools_exposure', $memo, DAY_IN_SECONDS );
	return $memo;
}

/**
 * Say so, once, at the top of the plugin's own screens.
 */
function horsetools_exposure_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! horsetools_is_plugin_screen() ) {
		return;
	}
	$s = horsetools_exposure_status();
	if ( ! $s['found'] ) {
		return;
	}

	ob_start();
	echo '<p><strong>' . esc_html__( 'There are files in your site’s folder that anyone can download.', 'horse-tools' ) . '</strong></p>';

	foreach ( $s['found'] as $row ) {
		$probed = 0;
		foreach ( $s['probes']['paths'] as $url => $hits ) {
			if ( false !== stripos( $url, basename( $row['path'] ) ) ) {
				$probed += $hits;
			}
		}
		echo '<p style="margin:2px 0"><code>' . esc_html( $row['path'] ) . '</code> — ' . esc_html( $row['why'] );
		if ( $probed > 0 ) {
			echo ' <strong>'
				. sprintf(
					/* translators: %d: number of requests for this exact path. */
					esc_html__( 'Somebody has asked for this path %d times.', 'horse-tools' ),
					(int) $probed
				)
				. '</strong>';
		}
		echo '</p>';
	}

	echo '<p>' . esc_html__( 'Delete them, or move them somewhere outside the folder your site is served from. If one of them is a copy of wp-config.php, change your database password afterwards — assume it has been read.', 'horse-tools' ) . '</p>';
	horsetools_admin_banner( 'bad', ob_get_clean() );
}
add_action( 'admin_notices', 'horsetools_exposure_notice' );
