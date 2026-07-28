<?php
/**
 * Horse Tools — single source of truth for modules, option groups and settings.
 *
 * Three separate copies of the module-to-option-name map used to exist
 * (modal/modal.php and twice in main/export.php), with one of them mapping
 * 'clean' to an option name that matched neither of the others. Everything now
 * reads from horsetools_module_map().
 *
 * The settings registry is populated as the admin screens render their fields
 * (see inc/ui.php). That means it is always honest about what exists — it
 * cannot drift the way a hand-maintained list would — at the cost of only being
 * complete once the screens have run, which is exactly when the search panel
 * and the "what's enabled" summary need it.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Feature module => the option name holding its settings.
 *
 * Modules keyed here are the ones with their own settings screen. The many
 * features stored inside the main `horsetools_settings` blob share that option.
 *
 * @return array<string,string>
 */
function horsetools_module_map() {
	return array(
		'main'      => 'horsetools_settings',
		'code'      => 'horsetools_code_settings',
		'extend'    => 'horsetools_extend_settings',
		'font'      => 'horsetools_font_settings',
		'fontset'   => 'horsetools_fontset_settings',
		'redirect'  => 'horsetools_redirects_settings',
		'index'     => 'horsetools_gindex_settings',
		'toc'       => 'horsetools_toc_settings',
		'ads'       => 'horsetools_ads_settings',
		'notify'    => 'horsetools_notify_settings',
		'shortcode' => 'horsetools_shortcode_settings',
		'search'    => 'horsetools_search_settings',
		'debug'     => 'horsetools_debug_settings',
	);
}

/**
 * Every option name the plugin owns. Used by export, reset and uninstall.
 *
 * @return string[]
 */
function horsetools_option_names() {
	return array_values( horsetools_module_map() );
}

/**
 * Read a plugin option group, cached for the request.
 *
 * modal/modal.php still populates the $horsetools_*_options globals that the
 * feature modules read, because rewriting ~350 call sites at once would be a
 * bad trade. New code should use this instead.
 *
 * @param string $module  Key from horsetools_module_map().
 * @param string $key     Setting key, or '' for the whole group.
 * @param mixed  $default Returned when the key is absent.
 * @return mixed
 */
function horsetools_opt( $module, $key = '', $default = null ) {
	static $cache = array();

	$map = horsetools_module_map();
	if ( ! isset( $map[ $module ] ) ) {
		return $default;
	}
	if ( ! array_key_exists( $module, $cache ) ) {
		$value            = get_option( $map[ $module ], array() );
		$cache[ $module ] = is_array( $value ) ? $value : array();
	}
	if ( '' === $key ) {
		return $cache[ $module ];
	}
	return array_key_exists( $key, $cache[ $module ] ) ? $cache[ $module ][ $key ] : $default;
}

/* -------------------------------------------------------------------------
 * Settings registry
 * ---------------------------------------------------------------------- */

/**
 * Record a control so the search panel and the enabled-summary can find it.
 *
 * @param array $field {
 *     @type string $key     Setting key.
 *     @type string $module  Module key (defaults to 'main').
 *     @type string $label   Visible label.
 *     @type string $page    Admin page slug the control lives on.
 *     @type string $tab     Tab id within that page.
 *     @type string $section Section heading it sits under.
 *     @type string $type    'toggle' | 'text' | 'number' | 'color' | 'select' | 'textarea'.
 * }
 */
function horsetools_register_field( array $field ) {
	global $horsetools_field_registry;

	if ( ! is_array( $horsetools_field_registry ) ) {
		$horsetools_field_registry = array();
	}
	if ( empty( $field['key'] ) ) {
		return;
	}
	$field = wp_parse_args(
		$field,
		array(
			'module'  => 'main',
			'label'   => '',
			'page'    => horsetools_current_admin_page(),
			'tab'     => '',
			'section' => '',
			'type'    => 'toggle',
		)
	);

	$horsetools_field_registry[ $field['module'] . '|' . $field['key'] ] = $field;
}

/**
 * Everything registered so far this request.
 *
 * @return array<string,array>
 */
function horsetools_get_field_registry() {
	global $horsetools_field_registry;
	return is_array( $horsetools_field_registry ) ? $horsetools_field_registry : array();
}

/**
 * The subset of registered fields that currently hold a non-empty value.
 *
 * "Enabled" for a toggle means the box is ticked; for everything else it means
 * the user has typed something. That is what the sidebar summary reports.
 *
 * @return array<string,array> Same shape as the registry, plus 'value'.
 */
function horsetools_get_active_fields() {
	$active = array();
	foreach ( horsetools_get_field_registry() as $id => $field ) {
		$value = horsetools_opt( $field['module'], $field['key'], null );
		if ( null === $value || '' === $value || array() === $value ) {
			continue;
		}
		if ( 'toggle' === $field['type'] && ! $value ) {
			continue;
		}
		$field['value'] = $value;
		$active[ $id ]  = $field;
	}
	return $active;
}
