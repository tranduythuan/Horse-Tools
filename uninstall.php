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
delete_option('horsetools_404_db');
delete_option('horsetools_slug_redirects');
delete_option('horsetools_gfont_local');
delete_option('horsetools_gfont_ver');
delete_option('horsetools_gfont_seen');
delete_option('horsetools_snippets');
delete_option('horsetools_sc_disabled');

// Drop the scheduled cleanup event too, in case the plugin is removed without
// being deactivated first.
wp_clear_scheduled_hook('horsetools_scheduled_clean');

// Drop the 404 log table.
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}horsetools_404" );

// Remove the self-hosted Google Fonts downloaded into uploads.
$horsetools_up = wp_upload_dir();
if ( ! empty( $horsetools_up['basedir'] ) ) {
	$horsetools_gdir = rtrim( $horsetools_up['basedir'], '/\\' ) . '/horsetools-gfonts';
	if ( is_dir( $horsetools_gdir ) ) {
		foreach ( (array) glob( $horsetools_gdir . '/*' ) as $horsetools_gfile ) {
			if ( is_file( $horsetools_gfile ) ) {
				@unlink( $horsetools_gfile );
			}
		}
		@rmdir( $horsetools_gdir );
	}
}

// NOTE: the debug feature writes WP_DEBUG / WP_DEBUG_LOG / WP_DEBUG_DISPLAY
// into wp-config.php. Those constants are deliberately NOT touched here —
// editing wp-config.php from an uninstall routine is riskier than leaving it.
// If you enabled WP_DEBUG_DISPLAY, turn it off in wp-config.php by hand after
// uninstalling, or the site keeps printing PHP warnings to visitors. A copy of
// the file as it was before the plugin first edited it is at
// wp-config.php.horsetools.bak.
