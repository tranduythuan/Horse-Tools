<?php
/**
 * Build the distributable plugin ZIP locally, the same way CI does.
 *
 * **CI publishes releases, not this.** `.github/workflows/release.yml` is what
 * runs on a version tag, and it checks the brand mark and the translations
 * before it builds anything. This is for looking at what a package would
 * contain without waiting for a tag.
 *
 * It must therefore produce the same layout CI does, or looking at it proves
 * nothing — which is exactly what went wrong: five releases were published from
 * an earlier version of this script that did not pack link/google-api into a
 * single archive, so they shipped ~800 files instead of ~300 and installed
 * slowly on hosts without the PHP zip extension.
 *
 * Not PowerShell's Compress-Archive: on Windows PowerShell 5.1 that writes
 * backslashes into the entry names, which is not what the ZIP format says and
 * which some unpackers read as one long filename rather than a path.
 *
 * Usage:  php tools/build-zip.php [output-dir]
 */

$root = dirname( __DIR__ );
$out  = rtrim( $argv[1] ?? dirname( $root ), '/\\' );

// Version straight from the plugin header, so the file can never be misnamed.
preg_match( "/define\(\s*'HORSETOOLS_VERSION',\s*'([^']+)'/", file_get_contents( $root . '/horse-tools.php' ), $m );
$version = $m[1] ?? '0.0.0';

$rules = array();
foreach ( file( $root . '/.distignore', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line ) {
	$line = trim( $line );
	if ( '' !== $line && '#' !== $line[0] ) {
		$rules[] = $line;
	}
}

/** Does this plugin-relative path match a .distignore rule? */
function ht_excluded( $rel, array $rules ) {
	$name = basename( $rel );
	foreach ( $rules as $rule ) {
		if ( '/' === $rule[0] ) {
			$anchored = ltrim( $rule, '/' );
			if ( $rel === $anchored || 0 === strpos( $rel, $anchored . '/' ) || fnmatch( $anchored, $rel ) ) {
				return true;
			}
			continue;
		}
		if ( fnmatch( $rule, $name ) ) {
			return true;
		}
	}
	return false;
}

$file = $out . '/horse-tools-' . $version . '.zip';
if ( file_exists( $file ) ) {
	unlink( $file );
}
$zip = new ZipArchive();
if ( true !== $zip->open( $file, ZipArchive::CREATE ) ) {
	fwrite( STDERR, "cannot create $file\n" );
	exit( 1 );
}

$n    = 0;
$iter = new RecursiveIteratorIterator(
	new RecursiveCallbackFilterIterator(
		new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ),
		function ( $item ) use ( $root, $rules ) {
			$rel = str_replace( '\\', '/', substr( $item->getPathname(), strlen( $root ) + 1 ) );
			return ! ht_excluded( $rel, $rules );
		}
	)
);
// The Google API client is only used by the optional Google Login and SEO
// Indexing features, and it is roughly two thirds of the plugin's file count.
// It ships as one archive that horsetools_google_autoload_path() unpacks on
// first use, so a normal install writes a few hundred files instead of eight
// hundred — which on a host without the PHP zip extension is the difference
// between a quick install and a stalled one.
$api_dir = $root . '/link/google-api';
$api_zip = null;
if ( is_dir( $api_dir ) ) {
	$api_zip = $out . '/google-api.zip';
	if ( file_exists( $api_zip ) ) {
		unlink( $api_zip );
	}
	$az = new ZipArchive();
	$az->open( $api_zip, ZipArchive::CREATE );
	$ai = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $api_dir, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $ai as $f ) {
		if ( $f->isDir() ) {
			continue;
		}
		$az->addFile( $f->getPathname(), str_replace( '\\', '/', substr( $f->getPathname(), strlen( $api_dir ) + 1 ) ) );
	}
	$az->close();
}

foreach ( $iter as $item ) {
	if ( $item->isDir() ) {
		continue;
	}
	$rel = str_replace( '\\', '/', substr( $item->getPathname(), strlen( $root ) + 1 ) );
	if ( 0 === strpos( $rel, 'link/google-api/' ) ) {
		continue; // goes in as the single archive below
	}
	$zip->addFile( $item->getPathname(), 'horse-tools/' . $rel );
	$n++;
}
if ( $api_zip ) {
	$zip->addFile( $api_zip, 'horse-tools/link/google-api.zip' );
	$n++;
}
$zip->close();
if ( $api_zip && file_exists( $api_zip ) ) {
	unlink( $api_zip ); // it lives inside the package now
}

printf( "%s\n%d files, %.2f MB\n", $file, $n, filesize( $file ) / 1048576 );
