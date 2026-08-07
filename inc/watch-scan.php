<?php
/**
 * Horse Tools — one walk over the site's content, shared by every watcher.
 *
 * The contact watch reads every published post. So does the outbound-link
 * inventory. So will whatever comes next. Giving each of them its own batch
 * cursor would mean reading eight hundred rows of `post_content` two and three
 * times over, on a shared host, to answer questions that could all be answered
 * from the same row.
 *
 * So the walk lives here and the watchers register as collectors. Each collector
 * is handed a whole batch at a time rather than a row at a time, so it reads and
 * writes its own option once per batch instead of once per post.
 *
 * The cursor carries a signature of the registered collectors. Add one — or
 * change how an existing one reads a post — and the signature no longer matches,
 * the pass starts again from the beginning, and every collector is asked to
 * throw away what it had. That is the alternative to a "remember to bump the
 * version" comment that nobody remembers to act on.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

const HORSETOOLS_SCAN_STATE = 'horsetools_content_scan';

/**
 * Bumped by hand when a collector's *reading* changes without its name changing.
 *
 * Fixing a phone-number rule or teaching the link reader a new tag means the
 * stored results were produced by code that no longer exists. Re-reading is the
 * only way to make what is on screen match what the current code would say.
 */
const HORSETOOLS_SCAN_SCHEMA = '2';

/**
 * The registered collectors.
 *
 * A collector is `array( 'batch' => callable(array $rows), 'reset' => callable )`
 * keyed by a short stable name. The name is part of the cursor signature, so
 * renaming one forces a re-read — which is the right thing to do, since a rename
 * generally means the thing it collects has changed too.
 *
 * @return array<string,array>
 */
function horsetools_scan_collectors() {
	$c = (array) apply_filters( 'horsetools_scan_collectors', array() );
	ksort( $c );
	return $c;
}

/**
 * What the stored results were produced by.
 *
 * @return string
 */
function horsetools_scan_signature() {
	return substr( md5( HORSETOOLS_SCAN_SCHEMA . '|' . implode( ',', array_keys( horsetools_scan_collectors() ) ) ), 0, 12 );
}

/**
 * Post types carrying text a visitor can read.
 *
 * @return string[]
 */
function horsetools_scan_post_types() {
	$types = get_post_types( array( 'public' => true ), 'names' );
	unset( $types['attachment'] );
	// Snippets are not public themselves, but their bodies are printed into
	// pages that are.
	if ( post_type_exists( 'ht_snippet' ) ) {
		$types['ht_snippet'] = 'ht_snippet';
	}
	return array_values( $types );
}

/**
 * @return array{sig:string,done:bool,offset:int,since:string,total:int}
 */
function horsetools_scan_state() {
	$s = get_option( HORSETOOLS_SCAN_STATE, array() );
	$s = is_array( $s ) ? $s : array();
	return array(
		'sig'    => isset( $s['sig'] ) ? (string) $s['sig'] : '',
		'done'   => ! empty( $s['done'] ),
		'offset' => isset( $s['offset'] ) ? (int) $s['offset'] : 0,
		'since'  => isset( $s['since'] ) ? (string) $s['since'] : '',
		'total'  => isset( $s['total'] ) ? (int) $s['total'] : 0,
	);
}

/**
 * Throw away every collector's results and put the cursor back to the start.
 *
 * Called when the signature no longer matches. It has to clear the collectors
 * too: their counts accumulate, so replaying the same eight hundred posts over
 * a set that was never emptied would report every number on the site as being
 * used twice as often as it is.
 */
function horsetools_scan_reset() {
	foreach ( horsetools_scan_collectors() as $collector ) {
		if ( isset( $collector['reset'] ) && is_callable( $collector['reset'] ) ) {
			call_user_func( $collector['reset'] );
		}
	}
	update_option(
		HORSETOOLS_SCAN_STATE,
		array( 'sig' => horsetools_scan_signature(), 'done' => false, 'offset' => 0, 'since' => '', 'total' => 0 ),
		false
	);
}

/**
 * Read one batch and hand it to every collector.
 *
 * @param int $size Posts per batch.
 * @return array{done:bool,offset:int,total:int,scanned:int}
 */
function horsetools_scan_batch( $size = 25 ) {
	global $wpdb;

	$collectors = horsetools_scan_collectors();
	$types      = horsetools_scan_post_types();
	if ( ! $collectors || ! $types ) {
		return array( 'done' => true, 'offset' => 0, 'total' => 0, 'scanned' => 0 );
	}

	$state = horsetools_scan_state();
	if ( $state['sig'] !== horsetools_scan_signature() ) {
		horsetools_scan_reset();
		$state = horsetools_scan_state();
	}

	$in = implode( ',', array_fill( 0, count( $types ), '%s' ) );

	if ( $state['done'] ) {
		// Steady state: only what changed. On a site where nothing has been
		// edited this reads no post content at all.
		$sql  = "SELECT ID, post_title, post_excerpt, post_content FROM {$wpdb->posts}"
			. " WHERE post_status = 'publish' AND post_type IN ($in) AND post_modified > %s"
			. ' ORDER BY post_modified ASC LIMIT %d';
		$args = array_merge( $types, array( '' !== $state['since'] ? $state['since'] : '1970-01-01 00:00:00', (int) $size ) );
	} else {
		if ( ! $state['total'] ) {
			$state['total'] = (int) $wpdb->get_var(
				$wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ($in)", $types ) // phpcs:ignore WordPress.DB
			);
		}
		$sql  = "SELECT ID, post_title, post_excerpt, post_content FROM {$wpdb->posts}"
			. " WHERE post_status = 'publish' AND post_type IN ($in)"
			. ' ORDER BY ID ASC LIMIT %d OFFSET %d';
		$args = array_merge( $types, array( (int) $size, (int) $state['offset'] ) );
	}

	$rows = (array) $wpdb->get_results( $wpdb->prepare( $sql, $args ) ); // phpcs:ignore WordPress.DB

	if ( $rows ) {
		foreach ( $collectors as $collector ) {
			if ( isset( $collector['batch'] ) && is_callable( $collector['batch'] ) ) {
				call_user_func( $collector['batch'], $rows );
			}
		}
	}

	$n = count( $rows );

	if ( $state['done'] ) {
		if ( $n ) {
			$state['since'] = current_time( 'mysql' );
			update_option( HORSETOOLS_SCAN_STATE, $state, false );
		}
		return array( 'done' => true, 'offset' => $state['total'], 'total' => $state['total'], 'scanned' => $n );
	}

	$state['offset'] += $n;
	if ( $n < $size ) {
		$state['done']  = true;
		$state['since'] = current_time( 'mysql' );
	}
	update_option( HORSETOOLS_SCAN_STATE, $state, false );

	return array(
		'done'    => $state['done'],
		'offset'  => $state['offset'],
		'total'   => $state['total'],
		'scanned' => $n,
	);
}

/**
 * Walk the scan forward while somebody is using the admin.
 *
 * WP-Cron is not reliable enough to be the only driver — this plugin has already
 * shipped a scheduled task that never ran once — and the first pass has to finish
 * before any of the watches mean anything. One small batch per admin page load
 * gets there without a screen to sit and watch. Once it is done the cost is a
 * single indexed query every quarter of an hour that usually returns nothing.
 */
function horsetools_scan_tick() {
	if ( wp_doing_ajax() || wp_doing_cron() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( get_transient( 'horsetools_scan_tick' ) ) {
		return;
	}
	set_transient( 'horsetools_scan_tick', 1, 5 );

	$state = horsetools_scan_state();
	if ( $state['done'] && $state['sig'] === horsetools_scan_signature() ) {
		if ( get_transient( 'horsetools_scan_seen' ) ) {
			return;
		}
		set_transient( 'horsetools_scan_seen', 1, 15 * MINUTE_IN_SECONDS );
	}
	horsetools_scan_batch( 25 );
}
add_action( 'admin_init', 'horsetools_scan_tick', 20 );

/**
 * Has the first full pass finished under the current signature?
 *
 * Every watcher has to ask this before it says anything: a set assembled from
 * the first forty of eight hundred posts is not an answer, and reporting it as
 * one would mean announcing a "new" contact detail on every admin load until the
 * pass caught up.
 *
 * @return bool
 */
function horsetools_scan_finished() {
	$state = horsetools_scan_state();
	return $state['done'] && $state['sig'] === horsetools_scan_signature();
}

/**
 * @return array{read:int,total:int}
 */
function horsetools_scan_progress() {
	$state = horsetools_scan_state();
	return array( 'read' => $state['offset'], 'total' => $state['total'] );
}
