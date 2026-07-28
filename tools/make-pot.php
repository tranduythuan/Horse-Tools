<?php
/**
 * Generate lang/horse-tools.pot from the plugin source.
 *
 * WP-CLI's i18n command is the usual way to do this, but it is not always
 * available. This does the same job with token_get_all(), which parses PHP
 * properly instead of guessing with regexes — so a translator string that
 * contains a bracket, a quote or a newline is extracted correctly.
 *
 * Usage:  php tools/make-pot.php [plugin-root]
 *
 * It also reports msgids present in the existing .po files that no longer
 * appear in the source, which is how you find translations left stale by a
 * rename.
 */

$root = rtrim($argv[1] ?? dirname(__DIR__), '/\\');
$domain = 'horse-tools';

/** Functions to extract from, mapped to the argument index holding the msgid. */
$single = [
    '__' => 0, '_e' => 0, 'esc_html__' => 0, 'esc_html_e' => 0,
    'esc_attr__' => 0, 'esc_attr_e' => 0, '_x' => 0, 'esc_html_x' => 0,
    'esc_attr_x' => 0, '_ex' => 0,
];
$plural = ['_n' => [0, 1], '_nx' => [0, 1]];

$skip = '~[\\\\/](link[\\\\/](google-api|svg-sanitize)|node_modules|tools)[\\\\/]~';

$files = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    if ($f->isFile() && strtolower($f->getExtension()) === 'php' && !preg_match($skip, $f->getPathname())) {
        $files[] = $f->getPathname();
    }
}
sort($files);

$entries = []; // msgid => ['plural'=>?, 'ctx'=>?, 'refs'=>[]]

foreach ($files as $file) {
    $rel = str_replace('\\', '/', ltrim(str_replace($root, '', $file), '/\\'));
    $tokens = token_get_all(file_get_contents($file));
    $count = count($tokens);

    for ($i = 0; $i < $count; $i++) {
        $t = $tokens[$i];
        if (!is_array($t) || $t[0] !== T_STRING) {
            continue;
        }
        $fn = $t[1];
        if (!isset($single[$fn]) && !isset($plural[$fn])) {
            continue;
        }
        // Skip method calls and namespaced lookalikes.
        for ($b = $i - 1; $b >= 0 && is_array($tokens[$b]) && $tokens[$b][0] === T_WHITESPACE; $b--);
        if ($b >= 0 && is_array($tokens[$b]) && in_array($tokens[$b][0], [T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION], true)) {
            continue;
        }
        // Next non-whitespace token must be "(".
        for ($j = $i + 1; $j < $count && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE; $j++);
        if ($j >= $count || $tokens[$j] !== '(') {
            continue;
        }

        // Collect the top-level argument list as literal strings (null where
        // the argument is not a plain quoted string).
        $args = [];
        $depth = 0;
        $current = null;
        $currentIsPlain = true;
        for ($k = $j; $k < $count; $k++) {
            $tk = $tokens[$k];
            if ($tk === '(') { $depth++; if ($depth === 1) continue; }
            if ($tk === ')') { $depth--; if ($depth === 0) { $args[] = $currentIsPlain ? $current : null; break; } }
            if ($depth === 1 && $tk === ',') { $args[] = $currentIsPlain ? $current : null; $current = null; $currentIsPlain = true; continue; }
            if ($depth >= 1) {
                if (is_array($tk) && $tk[0] === T_CONSTANT_ENCAPSED_STRING) {
                    $lit = $tk[1];
                    $q = $lit[0];
                    $body = substr($lit, 1, -1);
                    $body = $q === "'"
                        ? str_replace(["\\'", '\\\\'], ["'", '\\'], $body)
                        : stripcslashes($body);
                    $current = ($current === null) ? $body : $current . $body;
                } elseif (is_array($tk) && in_array($tk[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                    // ignore
                } elseif ($tk === '.') {
                    // string concatenation of literals is fine
                } else {
                    $currentIsPlain = false;
                }
            }
        }

        $line = $t[2];
        if (isset($plural[$fn])) {
            [$si, $pi] = $plural[$fn];
            if (!isset($args[$si], $args[$pi]) || $args[$si] === null) continue;
            $key = $args[$si];
            $entries[$key]['plural'] = $args[$pi];
        } else {
            $idx = $single[$fn];
            if (!isset($args[$idx]) || $args[$idx] === null) continue;
            $key = $args[$idx];
        }
        // Context-bearing variants put the context after the msgid(s).
        if (in_array($fn, ['_x', 'esc_html_x', 'esc_attr_x', '_ex'], true) && isset($args[1])) {
            $entries[$key]['ctx'] = $args[1];
        }
        $entries[$key]['refs'][] = $rel . ':' . $line;
    }
}

ksort($entries);

$esc = function ($s) {
    return str_replace(
        ["\\", "\"", "\n", "\t", "\r"],
        ["\\\\", "\\\"", "\\n", "\\t", ""],
        $s
    );
};

$out = [];
$out[] = '# Copyright (C) ' . date('Y') . ' Trần Duy Thuận';
$out[] = '# This file is distributed under the GPLv2 or later.';
$out[] = 'msgid ""';
$out[] = 'msgstr ""';
$out[] = '"Project-Id-Version: Horse Tools\\n"';
$out[] = '"Report-Msgid-Bugs-To: https://github.com/tranduythuan/Horse-Tools/issues\\n"';
$out[] = '"MIME-Version: 1.0\\n"';
$out[] = '"Content-Type: text/plain; charset=UTF-8\\n"';
$out[] = '"Content-Transfer-Encoding: 8bit\\n"';
$out[] = '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"';
$out[] = '"X-Domain: ' . $domain . '\\n"';
$out[] = '';

foreach ($entries as $msgid => $e) {
    foreach (array_chunk(array_unique($e['refs']), 4) as $chunk) {
        $out[] = '#: ' . implode(' ', $chunk);
    }
    if (!empty($e['ctx'])) {
        $out[] = 'msgctxt "' . $esc($e['ctx']) . '"';
    }
    $out[] = 'msgid "' . $esc($msgid) . '"';
    if (!empty($e['plural'])) {
        $out[] = 'msgid_plural "' . $esc($e['plural']) . '"';
        $out[] = 'msgstr[0] ""';
        $out[] = 'msgstr[1] ""';
    } else {
        $out[] = 'msgstr ""';
    }
    $out[] = '';
}

$potPath = $root . '/lang/' . $domain . '.pot';
file_put_contents($potPath, implode("\n", $out));

printf("Wrote %s\n  %d unique strings from %d files\n", $potPath, count($entries), count($files));

// Report msgids in the shipped translations that no longer exist in source.
foreach (glob($root . '/lang/*.po') as $po) {
    preg_match_all('~^msgid "((?:[^"\\\\]|\\\\.)*)"~m', file_get_contents($po), $m);
    $stale = 0;
    foreach ($m[1] as $raw) {
        if ($raw === '') continue;
        $id = stripcslashes($raw);
        if (!isset($entries[$id])) $stale++;
    }
    printf("  %-28s %d of %d msgids no longer in source\n", basename($po), $stale, count($m[1]) - 1);
}
