<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}
delete_option('horsetools_settings');
delete_option('horsetools_code_settings');
delete_option('horsetools_extend_settings');
delete_option('horsetools_fontset_settings');
delete_option('horsetools_redirects_settings');
delete_option('horsetools_gindex_settings');
delete_option('horsetools_toc_settings');
delete_option('horsetools_ads_settings');
delete_option('horsetools_notify_settings');
delete_option('horsetools_shortcode_settings');
delete_option('horsetools_search_settings');
delete_option('horsetools_debug_settings');
delete_option('horsetools_font_settings');
delete_option('horsetools_clean_settings');
// Internal bookkeeping, not user settings.
delete_option('horsetools_debug_applied');
delete_option('horsetools_adstxt_flushed');
delete_option('horsetools_clean_cron_last');
delete_option('horsetools_config_backup');

// Drop the scheduled cleanup event too, in case the plugin is removed without
// being deactivated first.
wp_clear_scheduled_hook('horsetools_scheduled_clean');

// NOTE: the debug feature writes WP_DEBUG / WP_DEBUG_LOG / WP_DEBUG_DISPLAY
// into wp-config.php. Those constants are deliberately NOT touched here —
// editing wp-config.php from an uninstall routine is riskier than leaving it.
// If you enabled WP_DEBUG_DISPLAY, turn it off in wp-config.php by hand after
// uninstalling, or the site keeps printing PHP warnings to visitors. A copy of
// the file as it was before the plugin first edited it is at
// wp-config.php.horsetools.bak.
