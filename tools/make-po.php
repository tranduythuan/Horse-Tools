<?php
/**
 * Build lang/horse-tools-<locale>.po (+ .mo) from a JSON map of translations.
 *
 * Merge semantics: an existing non-empty translation in the .po is kept; the
 * JSON only fills the blanks. So completing a partial language never clobbers
 * its existing strings, and a brand-new language is filled entirely from JSON.
 *
 * Usage:  php tools/make-po.php <locale> <translations.json>
 *   <translations.json> is { "English source string": "translation", ... }
 */

require __DIR__ . '/po-lib.php';

$root   = dirname( __DIR__ );
$locale = isset( $argv[1] ) ? $argv[1] : '';
$json   = isset( $argv[2] ) ? $argv[2] : '';
if ( '' === $locale || ! is_file( $json ) ) {
	fwrite( STDERR, "usage: make-po.php <locale> <translations.json>\n" );
	exit( 1 );
}

$plurals = array(
	'vi'    => 'nplurals=1; plural=0;',
	'th'    => 'nplurals=1; plural=0;',
	'zh_CN' => 'nplurals=1; plural=0;',
	'ja'    => 'nplurals=1; plural=0;',
	'fr_FR' => 'nplurals=2; plural=(n > 1);',
	'pt_BR' => 'nplurals=2; plural=(n > 1);',
	'ru_RU' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<12 || n%100>14) ? 1 : 2);',
	'ar'    => 'nplurals=6; plural=(n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5);',
	'hi_IN' => 'nplurals=2; plural=(n != 1);',
);
$pf = isset( $plurals[ $locale ] ) ? $plurals[ $locale ] : 'nplurals=2; plural=(n != 1);';

$pot = horsetools_read_po( $root . '/lang/horse-tools.pot' ); // msgid => ''
$po  = $root . '/lang/horse-tools-' . $locale . '.po';
$mo  = $root . '/lang/horse-tools-' . $locale . '.mo';

$existing = is_file( $po ) ? horsetools_read_po( $po ) : array();

$map = json_decode( file_get_contents( $json ), true );
if ( ! is_array( $map ) ) {
	fwrite( STDERR, "translations.json is not a valid JSON object\n" );
	exit( 1 );
}

$header = 'Project-Id-Version: Horse Tools' . "\n"
	. 'MIME-Version: 1.0' . "\n"
	. 'Content-Type: text/plain; charset=UTF-8' . "\n"
	. 'Content-Transfer-Encoding: 8bit' . "\n"
	. 'Language: ' . $locale . "\n"
	. 'Plural-Forms: ' . $pf . "\n"
	. 'X-Domain: horse-tools' . "\n";

$lines = array();
$lines[] = '# Horse Tools translation (' . $locale . ') — machine-generated, review recommended.';
$lines[] = 'msgid ""';
$lines[] = 'msgstr ""';
foreach ( explode( "\n", rtrim( $header, "\n" ) ) as $h ) {
	$lines[] = '"' . $h . '\n"';
}
$lines[] = '';

$entries    = array();
$translated = 0;
foreach ( $pot as $msgid => $_ ) {
	if ( '' === $msgid ) {
		continue;
	}
	$tr = ( isset( $existing[ $msgid ] ) && '' !== $existing[ $msgid ] )
		? $existing[ $msgid ]
		: ( isset( $map[ $msgid ] ) ? (string) $map[ $msgid ] : '' );
	$lines[] = 'msgid "' . horsetools_po_escape( $msgid ) . '"';
	$lines[] = 'msgstr "' . horsetools_po_escape( $tr ) . '"';
	$lines[] = '';
	if ( '' !== $tr ) {
		$entries[ $msgid ] = $tr;
		$translated++;
	}
}

file_put_contents( $po, implode( "\n", $lines ) );
horsetools_write_mo( $mo, $entries, $header );

printf( "%s: %d/%d translated\n", basename( $po ), $translated, count( $pot ) );
