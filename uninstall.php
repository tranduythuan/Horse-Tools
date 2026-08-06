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
delete_option('horsetools_snippets_legacy');
delete_option('horsetools_snip_index');
delete_option('horsetools_snip_migrated');
delete_option('horsetools_sc_disabled');
delete_option('horsetools_services');

// Snippets are one record each, with their tags in their own taxonomy. None of
// that is registered during an uninstall — the plugin's files are not loaded —
// so wp_delete_post() and wp_delete_term() would refuse to act on a post type
// and a taxonomy WordPress does not currently know about. These go out by hand.
global $wpdb;
$horsetools_snip_ids = $wpdb->get_col(
	$wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s", 'ht_snippet' )
);
foreach ( (array) $horsetools_snip_ids as $horsetools_snip_id ) {
	$horsetools_snip_id = (int) $horsetools_snip_id;
	$wpdb->delete( $wpdb->term_relationships, array( 'object_id' => $horsetools_snip_id ) );
	$wpdb->delete( $wpdb->postmeta, array( 'post_id' => $horsetools_snip_id ) );
	$wpdb->delete( $wpdb->posts, array( 'ID' => $horsetools_snip_id ) );
}
// The tag terms themselves, which nothing points at any more.
$horsetools_snip_tt = $wpdb->get_results(
	$wpdb->prepare( "SELECT term_id, term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE taxonomy = %s", 'ht_snippet_tag' )
);
foreach ( (array) $horsetools_snip_tt as $horsetools_snip_row ) {
	$wpdb->delete( $wpdb->term_relationships, array( 'term_taxonomy_id' => (int) $horsetools_snip_row->term_taxonomy_id ) );
	$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => (int) $horsetools_snip_row->term_taxonomy_id ) );
	$wpdb->delete( $wpdb->terms, array( 'term_id' => (int) $horsetools_snip_row->term_id ) );
}

// Drop the scheduled cleanup event too, in case the plugin is removed without
// being deactivated first.
wp_clear_scheduled_hook('horsetools_scheduled_clean');

// Drop the 404 log table.
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
