<?php
/**
 * Plugin Name: Horse Tools
 * Plugin URI: https://github.com/tranduythuan/Horse-Tools
 * Description: All-in-one WordPress toolkit: contact chat button, custom login, media optimisation, SEO index, cleanup and more.
 * Version: 1.1.0
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

define( 'HORSETOOLS_VERSION', '1.1.0' );
define( 'HORSETOOLS_URL', plugin_dir_url( __FILE__ ) );
define( 'HORSETOOLS_DIR', plugin_dir_path( __FILE__ ) );
define( 'HORSETOOLS_BASE', plugin_basename( __FILE__ ) );

include( HORSETOOLS_DIR . 'inc/registry.php' );
include( HORSETOOLS_DIR . 'inc/http.php' );
include( HORSETOOLS_DIR . 'inc/sanitize.php' );
if ( is_admin() ) {
	include( HORSETOOLS_DIR . 'inc/ui.php' );
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
	wp_enqueue_script( 'horsetools-js', HORSETOOLS_URL . 'link/htadmin.js', array(), HORSETOOLS_VERSION, true );
	wp_enqueue_style( 'coloris-css', HORSETOOLS_URL . 'link/color/coloris.css', array(), HORSETOOLS_VERSION );
	wp_enqueue_script( 'coloris-js', HORSETOOLS_URL . 'link/color/coloris.js', array(), HORSETOOLS_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'horsetools_customize_enqueue' );

function horsetools_enqueue_media_uploader() {
	$page = horsetools_current_admin_page();

	if ( function_exists( 'wp_enqueue_media' ) && in_array( $page, array( 'horsetools-options', 'horsetools-notify-options' ), true ) ) {
		wp_enqueue_media();
		wp_enqueue_editor();
	}

	if ( in_array( $page, array( 'horsetools-code-options', 'horsetools-ads-options' ), true ) ) {
		wp_enqueue_style( 'codemirror-horsetools', HORSETOOLS_URL . 'link/codeline/codemirror.css', array(), '6.65.7' );
		wp_enqueue_script( 'codemirror-horsetools', HORSETOOLS_URL . 'link/codeline/codemirror.js', array(), '6.65.7', true );
		wp_enqueue_script( 'perl-horsetools', HORSETOOLS_URL . 'link/codeline/perl.js', array( 'codemirror-horsetools' ), '6.65.7', true );
		wp_enqueue_style( 'abbott-horsetools', HORSETOOLS_URL . 'link/codeline/cobalt.css', array(), '6.65.7' );
		wp_enqueue_script( 'search-horsetools', HORSETOOLS_URL . 'link/codeline/search.js', array( 'codemirror-horsetools' ), '6.65.7', true );
		wp_enqueue_script( 'searchcursor-horsetools', HORSETOOLS_URL . 'link/codeline/searchcursor.js', array( 'codemirror-horsetools' ), '6.65.7', true );
		wp_enqueue_script( 'dialog-horsetools', HORSETOOLS_URL . 'link/codeline/dialog.js', array( 'codemirror-horsetools' ), '6.65.7', true );
		wp_enqueue_style( 'dialog-horsetools', HORSETOOLS_URL . 'link/codeline/dialog.css', array(), '6.65.7' );
	}

	if ( 'horsetools-font-options' === $page ) {
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
	// to every page — from the plugin whose first tab is "Optimize". The two
	// front-end features that genuinely need it (lazyload, fancybox) declare it
	// as a dependency, which is what pulls it in when they are switched on.
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
}
register_deactivation_hook( __FILE__, 'horsetools_deactivation' );
