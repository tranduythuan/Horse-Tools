<?php
/**
 * Write the brand files out from the one definition of the mark.
 *
 * The mark is defined once, in horsetools_brand_mark_svg(), and printed inline
 * so it inherits the surrounding colour and costs no request. But anything
 * outside PHP — a README, a listing icon, a banner, a favicon — needs a file,
 * and a file kept by hand is a second drawing that drifts from the first.
 *
 * So every file here is generated from that function rather than drawn again:
 *
 *     php tools/export-brand-mark.php          # write them
 *     php tools/export-brand-mark.php --check  # exit 1 if any is stale
 *
 * The release workflow runs --check, which is what makes a mark edited in one
 * place and not the others impossible to ship.
 *
 * @package Horse Tools
 */

$root = dirname( __DIR__ );
$src  = file_get_contents( $root . '/inc/horsetools.php' );

if ( ! preg_match( '~function horsetools_brand_mark_svg.*?\n\}~s', $src, $m ) ) {
	fwrite( STDERR, "Could not find horsetools_brand_mark_svg() in inc/horsetools.php\n" );
	exit( 1 );
}

// The function's only dependency is esc_attr(), so it runs without WordPress.
if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}
eval( $m[0] );

const HT_INK  = '#3d2a00';
const HT_GOLD = '#f4b400';

/** The mark without its own <svg> wrapper, so it can be placed inside one. */
function ht_mark_inner( $fill ) {
	return preg_replace( '~^<svg[^>]*>|</svg>$~', '', horsetools_brand_mark_svg( $fill ) );
}

/**
 * Every brand file, keyed by path relative to the plugin root.
 *
 * All of them embed the shapes the function produces; none redraws them.
 *
 * @return array<string,string>
 */
function ht_brand_files() {
	$inner = ht_mark_inner( HT_INK );

	// Standalone files carry the brand ink and a fixed size: they have no
	// surrounding colour to inherit and no container to size them.
	$mark = str_replace(
		'width="100%" height="100%"',
		'width="100" height="100"',
		horsetools_brand_mark_svg( HT_INK )
	);

	$icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" width="256" height="256" role="img" aria-label="Horse Tools">'
		. '<rect width="256" height="256" rx="56" fill="' . HT_GOLD . '"/>'
		. '<g transform="translate(52 52) scale(1.52)">' . $inner . '</g>'
		. '</svg>';

	$banner = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1544 500" width="1544" height="500" role="img" aria-label="Horse Tools">'
		. '<rect width="1544" height="500" fill="' . HT_GOLD . '"/>'
		. '<g transform="translate(150 130) scale(2.4)">' . $inner . '</g>'
		. '<text x="440" y="252" font-family="Georgia, serif" font-size="112" fill="' . HT_INK . '">Horse Tools</text>'
		. '<text x="444" y="322" font-family="Georgia, serif" font-size="40" fill="' . HT_INK . '" opacity="0.72">All-in-one WordPress toolkit</text>'
		. '</svg>';

	$head = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

	return array(
		'img/horse-tools-mark.svg' => $head . $mark . "\n",
		'img/brand/icon.svg'       => $head . $icon . "\n",
		'img/brand/banner.svg'     => $head . $banner . "\n",
	);
}

$files = ht_brand_files();

if ( in_array( '--check', $argv, true ) ) {
	$stale = array();
	foreach ( $files as $path => $body ) {
		$full = $root . '/' . $path;
		if ( ! is_readable( $full ) || file_get_contents( $full ) !== $body ) {
			$stale[] = $path;
		}
	}
	if ( ! $stale ) {
		printf( "%d brand files match horsetools_brand_mark_svg().\n", count( $files ) );
		exit( 0 );
	}
	fwrite( STDERR, "Out of date: " . implode( ', ', $stale ) . "\nRun: php tools/export-brand-mark.php\n" );
	exit( 1 );
}

foreach ( $files as $path => $body ) {
	$full = $root . '/' . $path;
	if ( ! is_dir( dirname( $full ) ) ) {
		mkdir( dirname( $full ), 0755, true );
	}
	file_put_contents( $full, $body );
	printf( "Wrote %s (%d bytes).\n", $path, strlen( $body ) );
}
