<?php
/**
 * Minimal gettext helpers shared by the tools/ scripts.
 *
 * Enough .po reading/writing and .mo compiling to keep the shipped
 * translations in sync without needing gettext or WP-CLI installed.
 */

/**
 * Read a .po (or .pot) file into msgid => msgstr.
 *
 * Plural entries are keyed by their singular msgid and carry msgstr[0].
 * The header entry (empty msgid) is dropped.
 *
 * @param string $path
 * @return array<string,string>
 */
function horsetools_read_po( $path ) {
    $out    = array();
    $msgid  = '';
    $msgstr = '';
    $target = null;
    $have   = false;

    $flush = function () use ( &$out, &$msgid, &$msgstr, &$have ) {
        if ( $have && '' !== $msgid ) {
            $out[ $msgid ] = $msgstr;
        }
        $msgid  = '';
        $msgstr = '';
        $have   = false;
    };

    foreach ( file( $path, FILE_IGNORE_NEW_LINES ) as $line ) {
        $line = trim( $line );

        if ( '' === $line || '#' === $line[0] ) {
            $flush();
            $target = null;
            continue;
        }
        if ( 0 === strpos( $line, 'msgctxt ' ) ) {
            $flush();
            $target = null;
            continue;
        }
        if ( 0 === strpos( $line, 'msgid_plural ' ) ) {
            $target = null;
            continue;
        }
        if ( 0 === strpos( $line, 'msgid ' ) ) {
            $flush();
            $msgid  = stripcslashes( substr( $line, 7, -1 ) );
            $target = 'msgid';
            continue;
        }
        if ( 0 === strpos( $line, 'msgstr[0] ' ) ) {
            $msgstr = stripcslashes( substr( $line, 11, -1 ) );
            $target = 'msgstr';
            $have   = true;
            continue;
        }
        if ( 0 === strpos( $line, 'msgstr[' ) ) {
            $target = null;
            continue;
        }
        if ( 0 === strpos( $line, 'msgstr ' ) ) {
            $msgstr = stripcslashes( substr( $line, 8, -1 ) );
            $target = 'msgstr';
            $have   = true;
            continue;
        }
        if ( '"' === $line[0] && null !== $target ) {
            $chunk = stripcslashes( substr( $line, 1, -1 ) );
            if ( 'msgid' === $target ) {
                $msgid .= $chunk;
            } else {
                $msgstr .= $chunk;
            }
        }
    }
    $flush();

    return $out;
}

/**
 * Escape a string for a .po literal.
 */
function horsetools_po_escape( $s ) {
    return str_replace(
        array( "\\", '"', "\n", "\t", "\r" ),
        array( "\\\\", '\\"', '\\n', '\\t', '' ),
        $s
    );
}

/**
 * Append entries to a .po file.
 *
 * @param string                $path
 * @param array<string,string>  $entries msgid => msgstr
 * @return int Number appended.
 */
function horsetools_append_po( $path, array $entries ) {
    if ( ! $entries ) {
        return 0;
    }
    $existing = horsetools_read_po( $path );
    $lines    = array( '' );
    $added    = 0;
    foreach ( $entries as $msgid => $msgstr ) {
        if ( isset( $existing[ $msgid ] ) && '' !== $existing[ $msgid ] ) {
            continue;
        }
        $lines[] = 'msgid "' . horsetools_po_escape( $msgid ) . '"';
        $lines[] = 'msgstr "' . horsetools_po_escape( $msgstr ) . '"';
        $lines[] = '';
        $added++;
    }
    if ( $added ) {
        file_put_contents( $path, rtrim( file_get_contents( $path ), "\n" ) . "\n" . implode( "\n", $lines ), LOCK_EX );
    }
    return $added;
}

/**
 * Compile msgid => msgstr into a binary .mo file.
 *
 * Implements the standard little-endian MO layout: magic, revision, count,
 * offsets of the original and translation string tables, then the tables and
 * the string data. The hash table is omitted (size 0), which gettext and
 * WordPress both accept.
 *
 * @param string               $path
 * @param array<string,string> $entries
 * @param string               $header msgstr for the empty msgid.
 * @return int Number of entries written.
 */
function horsetools_write_mo( $path, array $entries, $header = '' ) {
    $entries = array_filter(
        $entries,
        static function ( $v ) {
            return '' !== $v;
        }
    );
    ksort( $entries );

    // The header lives at the empty msgid and must sort first.
    $all = array( '' => $header ) + $entries;
    ksort( $all );

    $count       = count( $all );
    $originals   = '';
    $translations = '';
    $origTable   = '';
    $transTable  = '';

    // 7 uint32 header, then two tables of 2 uint32 per entry.
    $headerSize    = 7 * 4;
    $tableSize     = $count * 8;
    $origDataStart = $headerSize + 2 * $tableSize;

    $offset = $origDataStart;
    foreach ( $all as $msgid => $msgstr ) {
        $len       = strlen( $msgid );
        $origTable .= pack( 'VV', $len, $offset );
        $originals .= $msgid . "\0";
        $offset    += $len + 1;
    }
    foreach ( $all as $msgid => $msgstr ) {
        $len          = strlen( $msgstr );
        $transTable  .= pack( 'VV', $len, $offset );
        $translations .= $msgstr . "\0";
        $offset      += $len + 1;
    }

    $mo  = pack( 'V', 0x950412de );        // magic, little endian
    $mo .= pack( 'V', 0 );                 // revision
    $mo .= pack( 'V', $count );            // string count
    $mo .= pack( 'V', $headerSize );       // offset of original table
    $mo .= pack( 'V', $headerSize + $tableSize ); // offset of translation table
    $mo .= pack( 'V', 0 );                 // hash table size
    $mo .= pack( 'V', $headerSize + 2 * $tableSize ); // hash table offset
    $mo .= $origTable . $transTable . $originals . $translations;

    file_put_contents( $path, $mo, LOCK_EX );

    return $count;
}
