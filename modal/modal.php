<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
# them Global
$horsetools_options = get_option('horsetools_settings');
$horsetools_code_options = get_option('horsetools_code_settings');
$horsetools_extend_options = get_option('horsetools_extend_settings');
$horsetools_fontset_options = get_option('horsetools_fontset_settings');
$horsetools_redirects_options = get_option('horsetools_redirects_settings');
$horsetools_gindex_options = get_option('horsetools_gindex_settings');
$horsetools_toc_options = get_option('horsetools_toc_settings');
$horsetools_ads_options = get_option('horsetools_ads_settings');
$horsetools_notify_options = get_option('horsetools_notify_settings');
$horsetools_shortcode_options = get_option('horsetools_shortcode_settings');
$horsetools_search_options = get_option('horsetools_search_settings');
$horsetools_debug_options = get_option('horsetools_debug_settings');
# main
/**
 * Load the admin screens.
 *
 * Every hook registered by main/*.php is admin-only — admin_menu, admin_init
 * and update_option_* — but the files were being included on every request,
 * so roughly 162 KB of PHP was parsed and executed on each front-end page view
 * for no effect at all. Gate the whole thing on is_admin().
 */
function horsetools_load_admin_files() {
    if ( ! is_admin() ) {
        return;
    }
    global $horsetools_options, $horsetools_extend_options;
    $optional_files = array(
        'code'      => 'main/code.php',
        'clean'     => 'main/clean.php',
        'font'      => 'main/font.php',
        'redirect'  => 'main/redirects.php',
        'index'     => 'main/gindex.php',
        'toc'       => 'main/toc.php',
        'ads'       => 'main/ads.php',
        'notify'    => 'main/notify.php',
        'shortcode' => 'main/shortcode.php',
        'search'    => 'main/search.php',
        'debug'     => 'main/debug.php',
        'export'    => 'main/export.php',
    );
    $files_to_include = array(
        'main/admin.php',
        'main/extend.php',
        // Always present: SEO is not an optional module, it is a home for
        // settings that already existed and were simply hard to find.
        'main/group.php',
        'main/seo.php',
        'main/security.php',
        'main/speed.php',
        'main/content.php',
        'main/display.php',
        'main/customers.php',
        'main/accounts.php',
        'main/tools.php',
    );
    foreach ($optional_files as $key => $file) {
        if (isset($horsetools_extend_options[$key])) {
            $files_to_include[] = $file;
        }
    }
    $files_to_include[] = 'main/about.php';
    if (isset($horsetools_options['horsetools2']) && !empty($horsetools_options['horsetools21'])) {
        $admin_id = get_current_user_id();
        $allowed_id = $horsetools_options['horsetools21'];
        if (is_admin() && current_user_can('manage_options') && $admin_id == $allowed_id) {
            horsetools_include_files($files_to_include);
        }
    } else {
        horsetools_include_files($files_to_include);
    }
}
function horsetools_include_files($files) {
    foreach ($files as $file) {
        include(HORSETOOLS_DIR . $file);
    }
}
add_action('init', 'horsetools_load_admin_files');
# nhieu tinh nang
$horsetools_extend_files = array(
    'clean'     => 'inc/clean.php',
    'font'      => 'inc/font.php',
    'redirect'  => 'inc/redirects.php',
    'ads'       => 'inc/ads.php',
    'notify'    => 'inc/notify.php',
    'shortcode' => 'inc/shortcode.php',
    'search'    => 'inc/search.php',
    'debug'     => 'inc/debug.php',
    'index'     => 'inc/gindex.php',
    'toc'       => 'inc/toc.php',
);
$horsetools_option_files = array(
    'speed'   => 'inc/speed.php',
    'scuri'   => 'inc/scuri.php',
    'tool'    => 'inc/tool.php',
    'main'    => 'inc/main.php',
    'media'   => 'inc/media.php',
    'post'    => 'inc/post.php',
    'mail'    => 'inc/mail.php',
    'woo'     => 'inc/woo.php',
    'user'    => 'inc/user.php',
    'custom'  => 'inc/custom.php',
    'goo'     => 'inc/goo.php',
    'chat'    => 'inc/chat.php',
    // Keyed on the feature's own switch, not a tab: measuring contact clicks
    // covers links inside posts too, so it must not depend on the chat module.
    'track-contact1' => 'inc/chat-track.php',
);
// These modules register nothing but admin_*, load-* and wp_ajax_* hooks, so
// there is no reason to parse them on a front-end page view. admin-ajax.php
// counts as admin, so their AJAX handlers still register.
$horsetools_admin_only_modules = array( 'clean', 'debug', 'index' );

if (isset($horsetools_extend_options) && is_array($horsetools_extend_options)) {
    foreach ($horsetools_extend_files as $option_key => $file_path) {
        // Not is_admin(): inc/clean.php registers the handler for the daily
        // cleanup, and WP-Cron is not an admin request, so skipping the file
        // there left the scheduled event firing into nothing — WordPress then
        // marks it done and reschedules, so it failed silently rather than
        // erroring. horsetools_is_backend() covers cron and WP-CLI too.
        if ( in_array( $option_key, $horsetools_admin_only_modules, true ) && ! horsetools_is_backend() ) {
            continue;
        }
        if (isset($horsetools_extend_options[$option_key])) {
            include(HORSETOOLS_DIR . $file_path);
        }
    }
}
/**
 * Features that moved to their own screen but whose code still lives in a file
 * gated by the tab they came from.
 *
 * The tab master switches predate the reorganisation: 'post' turns on the whole
 * CONTENT tab, and inc/post.php — which also holds the URL, image-alt,
 * external-link and FAQ schema features — loads only when it is on. Now that
 * those settings live on the SEO screen, someone can switch one on there while
 * CONTENT is off, and it would quietly do nothing. Each feature inside the file
 * already checks its own key, so loading the file when any of these is set
 * turns on exactly that feature and nothing else.
 */
$horsetools_orphan_keys = array(
    'post'   => array( 'post-link1', 'post-link2', 'post-html1', 'post-alt1', 'post-out1', 'faq-schema1' ),
    // Moving or restyling the login page came from the CUSTOM tab, and the
    // reCAPTCHA check from GOOGLE. Both now live on the Security screen.
    'custom' => array( 'custom-ad1', 'custom-main1' ),
    'goo'    => array( 'goo-cap1' ),
);

if (isset($horsetools_options) && is_array($horsetools_options)) {
    foreach ($horsetools_option_files as $option_key => $file_path) {
        $wanted = isset($horsetools_options[$option_key]);
        if ( ! $wanted && isset( $horsetools_orphan_keys[ $option_key ] ) ) {
            foreach ( $horsetools_orphan_keys[ $option_key ] as $key ) {
                if ( ! empty( $horsetools_options[ $key ] ) ) {
                    $wanted = true;
                    break;
                }
            }
        }
        if ($wanted) {
            include(HORSETOOLS_DIR . $file_path);
        }
    }
}
