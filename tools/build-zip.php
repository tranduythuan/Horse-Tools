<?php
/**
 * Build the distributable plugin ZIP, honouring .distignore.
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
foreach ( $iter as $item ) {
	if ( $item->isDir() ) {
		continue;
	}
	$rel = str_replace( '\\', '/', substr( $item->getPathname(), strlen( $root ) + 1 ) );
	$zip->addFile( $item->getPathname(), 'horse-tools/' . $rel );
	$n++;
}
$zip->close();

printf( "%s\n%d files, %.2f MB\n", $file, $n, filesize( $file ) / 1048576 );
