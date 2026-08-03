<?php
/**
 * Rebuild the bundled Google API client's Composer class map.
 *
 * The apiclient-services package was pruned to just the services the plugin uses
 * (Indexing, Oauth2, SearchConsole) via Google\Task\Composer::cleanup, but the
 * generated class map was never regenerated afterward — so it still lists ~36k
 * classes pointing at source files that no longer exist. Those two files
 * (autoload_classmap.php + autoload_static.php) balloon to ~12 MB of dead
 * entries, which is the bulk of the plugin ZIP and makes installs time out on
 * weak hosts.
 *
 * This script drops every class-map entry whose target file is missing, keeping
 * the original __DIR__/$vendorDir relative-path form intact so the autoloader
 * still resolves correctly once the plugin is installed elsewhere. It only
 * touches the $classMap array — $files, PSR-4 prefixes and the loader closure
 * are left byte-for-byte unchanged.
 *
 * Usage:  php tools/trim-google-autoload.php
 */

$composerDir = dirname( __DIR__ ) . '/link/google-api/vendor/composer';
$vendorDir   = dirname( $composerDir );
$baseDir     = dirname( $vendorDir );

if ( ! is_dir( $composerDir ) ) {
	fwrite( STDERR, "composer dir not found: $composerDir\n" );
	exit( 1 );
}

/** Resolve a class-map value expression to an absolute path, or null if not one. */
function ht_resolve_path( $valueExpr, $composerDir, $vendorDir, $baseDir ) {
	// Grab every single-quoted string segment in the value part.
	if ( ! preg_match_all( "/'((?:[^'\\\\]|\\\\.)*)'/", $valueExpr, $m ) ) {
		return null;
	}
	$parts = array_map( function ( $s ) { return stripcslashes( $s ); }, $m[1] );
	$tail  = implode( '', $parts );
	if ( strpos( $valueExpr, '__DIR__' ) !== false ) {
		$base = $composerDir;
	} elseif ( strpos( $valueExpr, '$vendorDir' ) !== false ) {
		$base = $vendorDir;
	} elseif ( strpos( $valueExpr, '$baseDir' ) !== false ) {
		$base = $baseDir;
	} else {
		return null;
	}
	return $base . $tail;
}

/** True if a line is a class-map entry ("'Class' => <path>,"). */
function ht_entry_target( $line, $composerDir, $vendorDir, $baseDir ) {
	$pos = strpos( $line, '=>' );
	if ( $pos === false ) {
		return false;
	}
	$value = substr( $line, $pos + 2 );
	if ( strpos( $value, '__DIR__' ) === false && strpos( $value, '$vendorDir' ) === false && strpos( $value, '$baseDir' ) === false ) {
		return false;
	}
	return ht_resolve_path( $value, $composerDir, $vendorDir, $baseDir );
}

$totalDropped = 0;

/* ---- 1) autoload_classmap.php — the whole file is one returned class map. */
$cmFile = $composerDir . '/autoload_classmap.php';
$lines  = file( $cmFile, FILE_IGNORE_NEW_LINES );
$out    = array();
$kept   = 0;
$dropped = 0;
foreach ( $lines as $line ) {
	$target = ht_entry_target( $line, $composerDir, $vendorDir, $baseDir );
	if ( $target !== false && $target !== null ) {
		if ( is_file( $target ) ) { $out[] = $line; $kept++; }
		else { $dropped++; }
		continue;
	}
	$out[] = $line; // header / array open / close / blank
}
file_put_contents( $cmFile, implode( "\n", $out ) . "\n" );
$totalDropped += $dropped;
echo "autoload_classmap.php: kept $kept, dropped $dropped\n";

/* ---- 2) autoload_static.php — filter ONLY inside the $classMap array. */
$stFile = $composerDir . '/autoload_static.php';
$lines  = file( $stFile, FILE_IGNORE_NEW_LINES );
$out    = array();
$inClassMap = false;
$kept = 0;
$dropped = 0;
foreach ( $lines as $line ) {
	if ( ! $inClassMap ) {
		$out[] = $line;
		if ( strpos( $line, 'public static $classMap' ) !== false ) {
			$inClassMap = true;
		}
		continue;
	}
	// Inside the $classMap array. The array closes with a line that is just ");".
	if ( preg_match( '/^\s*\);\s*$/', $line ) ) {
		$inClassMap = false;
		$out[] = $line;
		continue;
	}
	$target = ht_entry_target( $line, $composerDir, $vendorDir, $baseDir );
	if ( $target !== false && $target !== null ) {
		if ( is_file( $target ) ) { $out[] = $line; $kept++; }
		else { $dropped++; }
		continue;
	}
	$out[] = $line; // e.g. the "array (" opener line
}
file_put_contents( $stFile, implode( "\n", $out ) . "\n" );
$totalDropped += $dropped;
echo "autoload_static.php:   kept $kept, dropped $dropped\n";

echo "Done. Total dead entries removed: $totalDropped\n";
