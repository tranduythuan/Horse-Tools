<?php
/**
 * Write the brand mark out as a standalone .svg file.
 *
 * The mark is defined once, in horsetools_brand_mark_svg(), and printed inline
 * so it inherits the surrounding colour and costs no request. But anything
 * outside PHP — the README, a wordpress.org listing icon, a favicon — needs a
 * file, and a file kept by hand is a second drawing that drifts from the first.
 *
 * So the file is generated from the function rather than drawn again:
 *
 *     php tools/export-brand-mark.php          # writes img/horse-tools-mark.svg
 *     php tools/export-brand-mark.php --check  # exits 1 if the file is stale
 *
 * The --check form is what makes drift impossible to ship unnoticed.
 *
 * @package Horse Tools
 */

$root = dirname( __DIR__ );
$src  = file_get_contents( $root . '/inc/horsetools.php' );

if ( ! preg_match( '~function horsetools_brand_mark_svg.*?\n\}~s', $src, $m ) ) {
	fwrite( STDERR, "Could not find horsetools_brand_mark_svg() in inc/horsetools.php\n" );
	exit( 1 );
}

// The function's only dependency is esc_attr(); everything else is string
// building, so it can be evaluated on its own without loading WordPress.
if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}
eval( $m[0] );

// A file has no surrounding colour to inherit, so it carries the brand ink.
// Anything that wants it in another colour uses the PHP function instead.
$svg = horsetools_brand_mark_svg( '#3d2a00' );

// Standalone files need a width and height a browser can use; the inline copy
// is sized by its container.
$svg = str_replace( 'width="100%" height="100%"', 'width="100" height="100"', $svg );
$svg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" . $svg . "\n";

$out = $root . '/img/horse-tools-mark.svg';

if ( in_array( '--check', $argv, true ) ) {
	$have = is_readable( $out ) ? file_get_contents( $out ) : '';
	if ( $have === $svg ) {
		echo "img/horse-tools-mark.svg matches horsetools_brand_mark_svg().\n";
		exit( 0 );
	}
	fwrite( STDERR, "img/horse-tools-mark.svg is out of date. Run: php tools/export-brand-mark.php\n" );
	exit( 1 );
}

file_put_contents( $out, $svg );
printf( "Wrote %s (%d bytes).\n", 'img/horse-tools-mark.svg', strlen( $svg ) );
