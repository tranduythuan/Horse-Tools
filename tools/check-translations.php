<?php
/**
 * List translatable strings that have no Vietnamese translation yet.
 *
 * The admin reads half in Vietnamese and half in English the moment a batch of
 * new strings ships without translations — which is what happened when the
 * screens were regrouped and every new group and tab name arrived in English.
 * This makes that visible before release instead of after:
 *
 *     php tools/check-translations.php          # list what is missing
 *     php tools/check-translations.php --count  # just the number, for CI
 *
 * @package Horse Tools
 */

$root = dirname( __DIR__ );

$files = array_merge(
	glob( $root . '/main/*.php' ),
	glob( $root . '/main/section/*.php' ),
	glob( $root . '/main/page/*.php' ),
	glob( $root . '/inc/*.php' ),
	glob( $root . '/modal/*.php' ),
	array( $root . '/horse-tools.php' )
);

// __( 'text', 'horse-tools' ) and its esc_ / _e variants, single-quoted only —
// which is how every string in this plugin is written.
$pattern = "~\\b(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\\(\\s*'((?:[^'\\\\]|\\\\.)*)'\\s*,\\s*'horse-tools'~";

$strings = array();
foreach ( $files as $file ) {
	$code = file_get_contents( $file );
	if ( preg_match_all( $pattern, $code, $m ) ) {
		foreach ( $m[1] as $raw ) {
			$strings[ stripslashes( $raw ) ] = basename( $file );
		}
	}
}

// What the Vietnamese catalogue already answers for. Read with the shared .po
// parser rather than a regex: gettext wraps long msgids across several quoted
// lines, and a line-based match sees every wrapped entry as untranslated — which
// is how this script once reported 97 strings missing that were all present.
require_once __DIR__ . '/po-lib.php';

$have = array();
foreach ( horsetools_read_po( $root . '/lang/horse-tools-vi.po' ) as $msgid => $msgstr ) {
	if ( '' !== $msgstr ) {
		$have[ $msgid ] = true;
	}
}

// The .po is the source; the .mo is what WordPress actually reads. They drift
// the moment the compile step is interrupted — which is how a screen shipped in
// English while this script, reading only the .po, reported nothing missing.
$mo_stale = false;
$mo_path  = $root . '/lang/horse-tools-vi.mo';
if ( is_readable( $mo_path ) ) {
	// The MO header's third uint32 is the string count, including the header
	// entry itself; the .po contributes one entry per non-empty translation.
	$head     = unpack( 'Vmagic/Vrev/Vcount', file_get_contents( $mo_path, false, null, 0, 12 ) );
	$mo_stale = ( (int) $head['count'] !== count( $have ) + 1 );
} else {
	$mo_stale = true;
}

$missing = array();
foreach ( $strings as $text => $where ) {
	if ( ! isset( $have[ $text ] ) ) {
		$missing[ $text ] = $where;
	}
}

if ( in_array( '--count', $argv, true ) ) {
	echo count( $missing ), "\n";
	exit( ( $missing || $mo_stale ) ? 1 : 0 );
}

// Full text, untruncated, for writing the translations from.
if ( in_array( '--full', $argv, true ) ) {
	$n = 0;
	foreach ( array_keys( $missing ) as $text ) {
		printf( "%d\t%s\n", ++$n, $text );
	}
	exit( $missing ? 1 : 0 );
}

printf(
	"%d translatable strings, %d translated, %d missing.\n\n",
	count( $strings ),
	count( $strings ) - count( $missing ),
	count( $missing )
);

foreach ( $missing as $text => $where ) {
	printf( "  [%s] %s\n", $where, mb_substr( $text, 0, 90 ) );
}

if ( $mo_stale ) {
	echo "\n  horse-tools-vi.mo is out of date with the .po — run: php tools/sync-translations.php\n";
}

exit( ( $missing || $mo_stale ) ? 1 : 0 );
