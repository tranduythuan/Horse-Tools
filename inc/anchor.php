<?php
/**
 * Horse Tools — hold the owner's decisions somewhere the database cannot reach.
 *
 * Every baseline in this plugin lives in wp_options: the contact details that
 * were confirmed, the domains that were approved. That is fine against somebody
 * editing posts, and useless against somebody who can write to the database
 * directly — an injection in another plugin, a leaked database password, a
 * backup tool with its own hole. They do not need to hide their link. They add
 * their own domain to the approved list, and from then on the watcher reports
 * "all clear" about it, for ever. A watch that vouches for the attacker is worse
 * than no watch at all.
 *
 * So a fingerprint of those decisions is kept in a file. Confirming through the
 * screen writes both; a hand at the database can only reach one. When the two
 * disagree, something changed what the owner agreed to without going through
 * the screen, and that is worth saying out loud.
 *
 * What this does not defend, stated plainly rather than left for someone to
 * discover:
 *
 *   - An attacker with a genuine administrator account. They can press the same
 *     confirm button the owner does, and both copies update. Only the check-in
 *     message reaching a human catches that one.
 *   - An attacker who can write files. They rewrite the anchor as easily as the
 *     database. Nothing in a PHP plugin defends against write access to its own
 *     filesystem.
 *
 * It defends exactly the middle case, which is also the common one.
 *
 * Only *decisions* are fingerprinted, never observations. What the scanner found
 * changes whenever a post is edited and is supposed to; what a person agreed to
 * changes only when a person agrees to something.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** The options whose contents are a human decision. */
function horsetools_anchor_options() {
	return array(
		'horsetools_contact_baseline',
		'horsetools_contact_baseline_content',
		'horsetools_link_approved',
	);
}

/**
 * Where the anchor lives.
 *
 * wp-content, not the plugin folder: an update replaces the plugin folder, and
 * an anchor that disappears every release is an alarm that cries wolf every
 * release. Same shape as the debug log directory — an index.php and an .htaccess
 * in both spellings — and the file itself opens with an exit, so a server that
 * ignores all of that and hands the file to PHP still returns nothing.
 *
 * @return string
 */
function horsetools_anchor_dir() {
	return WP_CONTENT_DIR . '/horsetools-anchor';
}

/**
 * Create the directory and its guards. Returns false if the filesystem will not
 * have it, which is a fact the owner needs rather than one to paper over.
 *
 * @return bool
 */
function horsetools_anchor_prepare() {
	$dir = horsetools_anchor_dir();
	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
	}
	if ( ! is_dir( $dir ) || ! wp_is_writable( $dir ) ) {
		return false;
	}
	horsetools_guard_directory( $dir );
	return true;
}

/**
 * The anchor file, found by looking rather than by asking the database.
 *
 * The name carries a random part so the URL cannot be guessed, but the plugin
 * finds it with a glob and not from an option. Storing the path in wp_options
 * would hand the same attacker a way to make the anchor invisible: delete one
 * row and the plugin decides there never was one. Found on disk, deleting that
 * row achieves nothing, and deleting the file itself is loud.
 *
 * @return string Path, or '' if there is none yet.
 */
function horsetools_anchor_file() {
	$found = glob( horsetools_anchor_dir() . '/ht-anchor-*.php' );
	if ( ! $found ) {
		return '';
	}
	sort( $found );
	return $found[0];
}

/**
 * A stable fingerprint of one decision.
 *
 * The keys only — `phone:0988343412`, `nhacai.xyz` — sorted. The rows also carry
 * how many times each was seen, and that moves whenever a post is edited. A
 * count is an observation; including it would mean the anchor disagreed with the
 * database for reasons that have nothing to do with anybody deciding anything.
 *
 * @param mixed $value
 * @return string
 */
function horsetools_anchor_fingerprint( $value ) {
	if ( ! is_array( $value ) ) {
		return '';
	}
	$keys = array_map( 'strval', array_keys( $value ) );
	sort( $keys, SORT_STRING );
	return hash( 'sha256', implode( "\n", $keys ) );
}

/**
 * Write the anchor to match what is in the database right now.
 *
 * Called from every path where a person agrees to something. Nowhere else —
 * an anchor that re-writes itself on a schedule would faithfully record whatever
 * the attacker put there.
 *
 * @return bool
 */
function horsetools_anchor_write() {
	if ( ! horsetools_anchor_prepare() ) {
		return false;
	}

	$data = array(
		'v'       => 1,
		'written' => time(),
		'site'    => home_url( '/' ),
		'marks'   => array(),
	);
	foreach ( horsetools_anchor_options() as $name ) {
		$stored = get_option( $name, null );
		// A null means "never confirmed", which is a real state and not the same
		// as "confirmed nothing". Recorded as such so the check can tell them
		// apart instead of reading an empty approval as a missing one.
		$data['marks'][ $name ] = is_array( $stored ) ? horsetools_anchor_fingerprint( $stored ) : null;
	}

	$path = horsetools_anchor_file();
	if ( '' === $path ) {
		$path = horsetools_anchor_dir() . '/ht-anchor-' . bin2hex( random_bytes( 8 ) ) . '.php';
	}

	$body = horsetools_php_guard() . wp_json_encode( $data );
	$tmp  = horsetools_anchor_dir() . '/.ht-anchor-' . bin2hex( random_bytes( 6 ) ) . '.tmp';
	if ( false === @file_put_contents( $tmp, $body, LOCK_EX ) ) { // phpcs:ignore
		return false;
	}
	@chmod( $tmp, 0600 ); // phpcs:ignore
	if ( ! @rename( $tmp, $path ) ) { // phpcs:ignore
		@unlink( $tmp ); // phpcs:ignore
		return false;
	}
	return true;
}

/**
 * Re-anchor after a decision.
 *
 * A named call rather than horsetools_anchor_write() at each site, so that
 * "somebody just decided something" and "put the fingerprint on disk" stay
 * separate ideas — and so the watchers have one symbol to depend on. This file
 * is loaded wherever they are, including the cron path that pulls them in.
 */
function horsetools_anchor_touch() {
	horsetools_anchor_write();
}

/**
 * @return array|null The stored anchor, or null if there is none or it is unreadable.
 */
function horsetools_anchor_read() {
	$path = horsetools_anchor_file();
	if ( '' === $path || ! is_readable( $path ) ) {
		return null;
	}
	$raw = (string) @file_get_contents( $path ); // phpcs:ignore
	$at  = strpos( $raw, "\n" );
	if ( false === $at ) {
		return null;
	}
	$data = json_decode( substr( $raw, $at + 1 ), true );
	return ( is_array( $data ) && isset( $data['marks'] ) && is_array( $data['marks'] ) ) ? $data : null;
}

/**
 * Which decisions no longer match the anchor.
 *
 * @return string[] Option names.
 */
function horsetools_anchor_mismatches() {
	$anchor = horsetools_anchor_read();
	if ( null === $anchor ) {
		return array();
	}
	$off = array();
	foreach ( horsetools_anchor_options() as $name ) {
		if ( ! array_key_exists( $name, $anchor['marks'] ) ) {
			// Written by a version that did not watch this one yet. Not a change
			// anybody made; the next confirmation records it.
			continue;
		}
		$stored = get_option( $name, null );
		$now    = is_array( $stored ) ? horsetools_anchor_fingerprint( $stored ) : null;
		if ( $now !== $anchor['marks'][ $name ] ) {
			$off[] = $name;
		}
	}
	return $off;
}

/**
 * @return array{state:string,off:array,written:int}
 *         state: 'unwritable' | 'none' | 'ok' | 'mismatch'
 */
function horsetools_anchor_status() {
	$anchor = horsetools_anchor_read();
	if ( null === $anchor ) {
		// Asked by trying, not by reasoning about permissions. Reading the mode
		// bits of wp-content answers a different question from "can this process
		// create that directory and put a file in it" — open_basedir, an immutable
		// flag, a full disk and a filesystem that ignores chmod all disagree with
		// them, and each one would have been reported as "nothing anchored yet"
		// rather than "there is nowhere to keep it".
		return array(
			'state'   => horsetools_anchor_prepare() ? 'none' : 'unwritable',
			'off'     => array(),
			'written' => 0,
		);
	}
	$off = horsetools_anchor_mismatches();
	return array(
		'state'   => $off ? 'mismatch' : 'ok',
		'off'     => $off,
		'written' => isset( $anchor['written'] ) ? (int) $anchor['written'] : 0,
	);
}

/**
 * Plain names for the things being anchored.
 *
 * @param string $option
 * @return string
 */
function horsetools_anchor_label( $option ) {
	$map = array(
		'horsetools_contact_baseline'         => __( 'the contact details you confirmed in your settings', 'horse-tools' ),
		'horsetools_contact_baseline_content' => __( 'the contact details you confirmed in your posts', 'horse-tools' ),
		'horsetools_link_approved'            => __( 'the list of domains you approved', 'horse-tools' ),
	);
	return isset( $map[ $option ] ) ? $map[ $option ] : $option;
}

/* -------------------------------------------------------------------------
 * Saying so
 * ---------------------------------------------------------------------- */

/**
 * The one alarm in this plugin that means somebody reached past the screens.
 *
 * Worded to leave the innocent explanation available, because it is a real one:
 * restoring a database backup, or copying a database from staging, moves the
 * options without moving the files and lands here honestly. Screaming "you have
 * been hacked" at somebody who just restored a backup is how a serious alarm
 * gets a reputation for being wrong.
 */
function horsetools_anchor_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! horsetools_is_plugin_screen() ) {
		return;
	}
	$s = horsetools_anchor_status();
	if ( 'mismatch' !== $s['state'] ) {
		return;
	}

	ob_start();
	echo '<p><strong>' . esc_html__( 'Something changed what you approved, without going through this screen.', 'horse-tools' ) . '</strong></p>';
	echo '<p>' . esc_html__( 'Horse Tools keeps a copy of your decisions in a file as well as in the database, precisely so that a change made straight to the database can be seen. These no longer match:', 'horse-tools' ) . '</p>';
	foreach ( $s['off'] as $option ) {
		echo '<p style="margin:2px 0">• ' . esc_html( horsetools_anchor_label( $option ) ) . '</p>';
	}
	echo '<p>' . esc_html__( 'If you have just restored a database backup, or copied the database from another copy of this site, that is the explanation — the file stayed while the database went back in time. Press the button and it will match again.', 'horse-tools' ) . '</p>';
	echo '<p>' . esc_html__( 'If you have not done either of those things, then something can write to your database that should not be able to. Changing passwords will not fix that on its own; the way in has to be found.', 'horse-tools' ) . '</p>';
	echo '<p><button type="button" class="button button-primary" id="ht-anchor-ok" data-nonce="' . esc_attr( wp_create_nonce( 'horsetools_anchor' ) ) . '">'
		. esc_html__( 'That was me — line them up again', 'horse-tools' ) . '</button></p>';
	horsetools_admin_banner( 'bad', ob_get_clean() );
	?>
	<script>
	(function(){
		if (window.htAnchorBound) { return; }
		window.htAnchorBound = true;
		document.addEventListener('click', function (e) {
			var b = e.target && e.target.closest ? e.target.closest('#ht-anchor-ok') : null;
			if (!b || b.disabled) { return; }
			b.disabled = true;
			fetch(ajaxurl, {method:'POST', credentials:'same-origin',
				headers:{'Content-Type':'application/x-www-form-urlencoded'},
				body:'action=horsetools_anchor_ok&nonce=' + encodeURIComponent(b.dataset.nonce)})
			.then(function (r) { return r.json(); })
			.then(function () { location.reload(); })
			.catch(function () { b.disabled = false; });
		});
	})();
	</script>
	<?php
}
add_action( 'admin_notices', 'horsetools_anchor_notice' );

add_action( 'wp_ajax_horsetools_anchor_ok', 'horsetools_anchor_ok_ajax' );
function horsetools_anchor_ok_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	check_ajax_referer( 'horsetools_anchor', 'nonce' );
	horsetools_anchor_write();
	wp_send_json_success();
}

/**
 * Write the first anchor once the owner has actually decided something.
 *
 * Not on activation: anchoring an empty set of decisions records nothing worth
 * protecting, and would make the first real confirmation look like a mismatch.
 */
function horsetools_anchor_bootstrap() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( 'none' !== horsetools_anchor_status()['state'] ) {
		return;
	}
	foreach ( horsetools_anchor_options() as $name ) {
		if ( is_array( get_option( $name, null ) ) ) {
			horsetools_anchor_write();
			return;
		}
	}
}
add_action( 'admin_init', 'horsetools_anchor_bootstrap', 40 );
