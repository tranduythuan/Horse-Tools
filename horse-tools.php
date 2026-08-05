<?php
/**
 * Plugin Name: Horse Tools
 * Plugin URI: https://github.com/tranduythuan/Horse-Tools
 * Description: All-in-one WordPress toolkit: contact chat button, custom login, media optimisation, SEO index, cleanup and more.
 * Version: 1.3.3
 * Author: Trần Duy Thuận
 * Author URI: https://tranduythuan.com/
 * Text Domain: horse-tools
 * Domain Path: /lang
 * Requires PHP: 8.1
 * Requires at least: 6.0
 * Tested up to: 6.9
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Horse Tools is a fork of Foxtool by Fox Theme (GPLv2), which is no longer
 * maintained by its original author. The original work is gratefully
 * acknowledged; this version has been rebranded, security-hardened and is
 * developed further by Trần Duy Thuận.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'HORSETOOLS_VERSION', '1.3.3' );
define( 'HORSETOOLS_URL', plugin_dir_url( __FILE__ ) );
define( 'HORSETOOLS_DIR', plugin_dir_path( __FILE__ ) );
define( 'HORSETOOLS_BASE', plugin_basename( __FILE__ ) );

/**
 * Whether this request can reach the plugin's own machinery.
 *
 * is_admin() alone is the wrong test and has already cost us: WP-Cron runs
 * through wp-cron.php with no admin constant, so a file skipped for "not admin"
 * is also skipped for every scheduled task it registers — the task then fires
 * into nothing and WordPress reschedules it, silently, for ever. WP-CLI is here
 * for the same reason: activation and updates run there with no admin either.
 */
function horsetools_is_backend() {
	return is_admin()
		|| ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() )
		|| ( defined( 'DOING_CRON' ) && DOING_CRON )
		|| ( defined( 'WP_CLI' ) && WP_CLI );
}

include( HORSETOOLS_DIR . 'inc/registry.php' );
include( HORSETOOLS_DIR . 'inc/http.php' );
// Validators for values printed into inline <style> blocks. Front-end code
// uses these, so they must never be behind the backend-only gate below.
include( HORSETOOLS_DIR . 'inc/css.php' );
// Sanitizing is what happens when settings are written, which only the admin
// screens, the activation migration and WP-CLI ever do. A page view never
// needs it.
if ( horsetools_is_backend() ) {
	include( HORSETOOLS_DIR . 'inc/sanitize.php' );
}
// Saving a screen that spans more than one option group. Needs the sanitizer,
// so it is loaded straight after it, and only where settings can be written.
if ( horsetools_is_backend() ) {
	include( HORSETOOLS_DIR . 'inc/save.php' );
}
// Self-update from GitHub Releases. Backend rather than admin-only: the update
// check also runs from WP-Cron, and the injected package URL is what lets the
// SERVER download new versions directly from GitHub instead of the owner
// uploading the ZIP through the browser. A visitor's page view is the one case
// that never needs any of it.
if ( horsetools_is_backend() ) {
	include( HORSETOOLS_DIR . 'inc/update.php' );
}
if ( is_admin() ) {
	include( HORSETOOLS_DIR . 'inc/ui.php' );
	include( HORSETOOLS_DIR . 'inc/health.php' );
}
include( HORSETOOLS_DIR . 'inc/horsetools.php' );
include( HORSETOOLS_DIR . 'inc/code.php' );
include( HORSETOOLS_DIR . 'modal/modal.php' );

/**
 * Admin pages that belong to this plugin. Used to scope asset loading.
 */
function horsetools_current_admin_page() {
	if ( ! isset( $_GET['page'] ) ) {
		return '';
	}
	return sanitize_key( wp_unslash( $_GET['page'] ) );
}

function horsetools_is_plugin_screen() {
	return strpos( horsetools_current_admin_page(), 'horsetools-' ) === 0;
}

function horsetools_customize_enqueue() {
	// Only on this plugin's own screens. Without the guard every logged-in
	// user — including subscribers on profile.php — downloaded the admin CSS,
	// the icon font and the colour picker, and learned the plugin's exact
	// version from the ?ver= query arg.
	if ( ! horsetools_is_plugin_screen() ) {
		return;
	}
	wp_enqueue_style( 'horsetools-icon', HORSETOOLS_URL . 'link/tabler/tabler-icons.css', array(), HORSETOOLS_VERSION );
	wp_enqueue_style( 'horsetools-css', HORSETOOLS_URL . 'link/htadmin.css', array(), HORSETOOLS_VERSION );
	// Load the admin script and colour picker in the HEAD, not the footer. On
	// some hosts a heavy admin page drops the plugin's footer <script> tags (the
	// tab handler, colour picker, sidebar search and dependent toggles all went
	// missing on one site while the CSS in the head loaded fine). The head is
	// printed reliably there, and htadmin.js only touches the DOM on
	// DOMContentLoaded, so head loading is safe. jQuery is declared as a
	// dependency so it is always present first.
	wp_enqueue_script( 'horsetools-js', HORSETOOLS_URL . 'link/htadmin.js', array( 'jquery' ), HORSETOOLS_VERSION, false );
	wp_enqueue_style( 'coloris-css', HORSETOOLS_URL . 'link/color/coloris.css', array(), HORSETOOLS_VERSION );
	wp_enqueue_script( 'coloris-js', HORSETOOLS_URL . 'link/color/coloris.js', array(), HORSETOOLS_VERSION, false );
}
add_action( 'admin_enqueue_scripts', 'horsetools_customize_enqueue' );

/**
 * Tab switching must never depend on the external admin script loading. Some
 * hosts run cache/optimise/security plugins (LiteSpeed, WP Rocket, etc.) that
 * defer, combine or drop plugin footer scripts — when that happens to
 * link/htadmin.js the inline onclick="httab(...)" throws "httab is not defined"
 * and every tab is stuck. Printing a self-contained httab() in the head (not
 * enqueued, so optimisers leave it alone) guarantees the tabs always work; when
 * htadmin.js does load it simply redefines httab with the same behaviour.
 */
function horsetools_inline_tab_fallback() {
	if ( ! horsetools_is_plugin_screen() ) {
		return;
	}
	?>
<script id="horsetools-tab-fallback">
window.httab = window.httab || function (evt, tabname) {
	var x = document.getElementsByClassName('htbox'), i;
	for (i = 0; i < x.length; i++) { x[i].style.display = 'none'; }
	var s = document.getElementsByClassName('sotab');
	for (i = 0; i < s.length; i++) { s[i].className = s[i].className.replace(' sotab-select', ''); }
	var pane = document.getElementById(tabname);
	if (pane) { pane.style.display = 'block'; }
	if (evt && evt.currentTarget) { evt.currentTarget.className += ' sotab-select'; }
	try { localStorage.setItem('htranksel', tabname); } catch (e) {}
	if (window.jQuery) { jQuery('.ht-dev').each(function () { var ed = jQuery(this).data('CodeMirrorInstance'); if (ed) { ed.refresh(); } }); }
};
</script>
	<?php
}
add_action( 'admin_head', 'horsetools_inline_tab_fallback' );

/**
 * Which admin screens need a given heavy asset.
 *
 * These lists used to name the per-module screens directly. Those screens
 * became tabs during the regrouping, so the names stopped matching anything
 * and the assets silently stopped loading — the code boxes lose their editor
 * and the image pickers their media library, with no error to notice.
 *
 * @param string $asset media | codemirror | select2
 * @return string[]
 */
function horsetools_screens_needing( $asset ) {
	$map = array(
		'media'      => array( 'horsetools-options', 'horsetools-customers-options', 'horsetools-content-options', 'horsetools-display-options', 'horsetools-security-options', 'horsetools-speed-options' ),
		'codemirror' => array( 'horsetools-tools-options', 'horsetools-customers-options' ),
		'select2'    => array( 'horsetools-font-options', 'horsetools-display-options' ),
	);
	return isset( $map[ $asset ] ) ? $map[ $asset ] : array();
}

function horsetools_enqueue_media_uploader() {
	$page = horsetools_current_admin_page();

	if ( function_exists( 'wp_enqueue_media' ) && in_array( $page, horsetools_screens_needing( 'media' ), true ) ) {
		wp_enqueue_media();
		wp_enqueue_editor();
	}

	if ( in_array( $page, horsetools_screens_needing( 'codemirror' ), true ) ) {
		wp_enqueue_style( 'codemirror-horsetools', HORSETOOLS_URL . 'link/codeline/codemirror.css', array(), '6.65.7' );
		wp_enqueue_script( 'codemirror-horsetools', HORSETOOLS_URL . 'link/codeline/codemirror.js', array(), '6.65.7', true );
		wp_enqueue_script( 'perl-horsetools', HORSETOOLS_URL . 'link/codeline/perl.js', array( 'codemirror-horsetools' ), '6.65.7', true );
		wp_enqueue_style( 'abbott-horsetools', HORSETOOLS_URL . 'link/codeline/cobalt.css', array(), '6.65.7' );
		wp_enqueue_script( 'search-horsetools', HORSETOOLS_URL . 'link/codeline/search.js', array( 'codemirror-horsetools' ), '6.65.7', true );
		wp_enqueue_script( 'searchcursor-horsetools', HORSETOOLS_URL . 'link/codeline/searchcursor.js', array( 'codemirror-horsetools' ), '6.65.7', true );
		wp_enqueue_script( 'dialog-horsetools', HORSETOOLS_URL . 'link/codeline/dialog.js', array( 'codemirror-horsetools' ), '6.65.7', true );
		wp_enqueue_style( 'dialog-horsetools', HORSETOOLS_URL . 'link/codeline/dialog.css', array(), '6.65.7' );
	}

	if ( in_array( $page, horsetools_screens_needing( 'select2' ), true ) ) {
		wp_enqueue_script( 'select2-horsetools', HORSETOOLS_URL . 'link/select2.js', array( 'jquery' ), '4.1.0', true );
		wp_enqueue_style( 'select2-horsetools', HORSETOOLS_URL . 'link/select2.css', array(), '4.1.0' );
	}
}
add_action( 'admin_enqueue_scripts', 'horsetools_enqueue_media_uploader' );

function horsetools_enqueue_home() {
	global $horsetools_notify_options, $horsetools_search_options;

	// jQuery used to be enqueued here unconditionally on every front-end page.
	// link/index.js contains no jQuery at all, and on a block theme that has no
	// jQuery dependency of its own this plugin was single-handedly adding ~30 KB
	// to every page — from the plugin whose first tab is "Optimize". The only
	// front-end feature that still needs it (the search / popup modal below)
	// declares it as a dependency, which is what pulls it in when switched on.
	// (Lazy-load is now native and the lightbox engines are vanilla JS.)
	wp_enqueue_script( 'horsetools-index', HORSETOOLS_URL . 'link/index.js', array(), HORSETOOLS_VERSION, true );

	if ( isset( $horsetools_search_options['main-search1'] ) || isset( $horsetools_notify_options['notify-popup1'] ) ) {
		wp_enqueue_script( 'jquery-modal', HORSETOOLS_URL . 'link/jquery-modal.js', array( 'jquery' ), HORSETOOLS_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'horsetools_enqueue_home' );

function horsetools_settings_about( $links ) {
	array_unshift(
		$links,
		'<a href="' . esc_url( admin_url( 'admin.php?page=horsetools-options' ) ) . '">' . esc_html__( 'Settings', 'horse-tools' ) . '</a>'
	);
	return $links;
}
add_filter( 'plugin_action_links_' . HORSETOOLS_BASE, 'horsetools_settings_about' );

/**
 * One-time import of settings from a previously installed Foxtool instance.
 * Only runs when the Horse Tools option does not exist yet, so it never
 * overwrites anything the site owner has already configured here.
 */
function horsetools_migrate_legacy_options() {
	$groups = array(
		'settings',
		'code_settings',
		'extend_settings',
		'fontset_settings',
		'font_settings',
		'redirects_settings',
		'gindex_settings',
		'toc_settings',
		'ads_settings',
		'notify_settings',
		'shortcode_settings',
		'search_settings',
		'debug_settings',
	);

	foreach ( $groups as $group ) {
		$legacy = get_option( 'foxtool_' . $group, null );
		if ( null === $legacy ) {
			continue;
		}
		if ( false !== get_option( 'horsetools_' . $group, false ) ) {
			continue;
		}
		// Run the imported blob through the sanitizer explicitly. add_option()
		// fires sanitize_option_{$option}, but that filter is installed by
		// register_setting() on admin_init — which has already run by the time
		// the activation hook fires — so an unsanitized Foxtool blob would
		// otherwise land in the database verbatim.
		if ( is_array( $legacy ) && function_exists( 'horsetools_sanitize_settings_array' ) ) {
			$legacy = horsetools_sanitize_settings_array( $legacy );
		} elseif ( ! is_array( $legacy ) ) {
			continue;
		}
		add_option( 'horsetools_' . $group, $legacy );
	}
}

function horsetools_activation() {
	horsetools_migrate_legacy_options();
}
register_activation_hook( __FILE__, 'horsetools_activation' );

/**
 * On deactivation, drop the scheduled cleanup event. wp_clear_scheduled_hook()
 * is always available, so this works whether or not the Clean module was
 * loaded this request.
 */
function horsetools_deactivation() {
	wp_clear_scheduled_hook( 'horsetools_scheduled_clean' );
	wp_clear_scheduled_hook( 'horsetools_tables_sync' );
}
register_deactivation_hook( __FILE__, 'horsetools_deactivation' );
