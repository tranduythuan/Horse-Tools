<?php
/**
 * Report translation coverage against lang/horse-tools.pot.
 *
 * Usage:  php tools/i18n-status.php [plugin-root]
 *
 * Run tools/make-pot.php first so the template reflects current source.
 */

require_once __DIR__ . '/po-lib.php';

$root = rtrim($argv[1] ?? dirname(__DIR__), '/\\');
$listAll = in_array('--all', $argv, true);

$pot = $root . '/lang/horse-tools.pot';
if (!file_exists($pot)) {
    fwrite(STDERR, "No .pot yet — run tools/make-pot.php first.\n");
    exit(1);
}
$template = array_keys(horsetools_read_po($pot));
printf("Template: %d strings\n\n", count($template));

foreach (glob($root . '/lang/*.po') as $po) {
    $translated = horsetools_read_po($po);
    $have = 0;
    $missing = [];
    foreach ($template as $id) {
        if (!empty($translated[$id])) { $have++; } else { $missing[] = $id; }
    }
    $pct = count($template) ? round($have / count($template) * 100) : 0;
    printf("%-28s %3d%%  (%d translated, %d missing)\n", basename($po), $pct, $have, count($missing));
    $show = $listAll ? $missing : array_slice($missing, 0, 12);
    foreach ($show as $m) {
        printf("      missing: %s\n", $listAll ? $m : mb_strimwidth($m, 0, 72, '…'));
    }
    if (!$listAll && count($missing) > 12) {
        printf("      … and %d more (pass --all to list them)\n", count($missing) - 12);
    }
    echo "\n";
}
