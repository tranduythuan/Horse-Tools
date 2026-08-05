<?php
/**
 * Horse Tools — values that end up inside a <style> block.
 *
 * A CSS context cannot be HTML-escaped, so anything concatenated into inline
 * styles is validated by type instead. Eight front-end files do this — chat,
 * custom login, dark mode, popups, search, shortcodes and the contents list —
 * which is why these live in their own always-loaded file rather than beside
 * the settings sanitisers. They were in inc/sanitize.php until 1.2.86, and
 * when that file stopped loading on the front end in 1.2.67 every one of
 * those features fatally errored the moment a colour was set.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Sanitize a CSS colour value.
 *
 * The colour pickers in this plugin are Coloris instances configured with
 * `alpha: true` and `formatToggle: true` (see link/color/coloris.js), so a
 * stored value may legitimately be #rgb, #rrggbb, #rrggbbaa, rgb()/rgba() or
 * hsl()/hsla(). sanitize_hex_color() alone would silently blank every
 * translucent colour an admin has already saved, so it is tried first and a
 * strict functional-notation check is used as the fallback.
 *
 * Anything else becomes '' (the output sites treat '' as "not set").
 *
 * @param mixed $value Raw value.
 * @return string Safe CSS colour, or ''.
 */
function horsetools_sanitize_color( $value ) {
	if ( ! is_scalar( $value ) ) {
		return '';
	}
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	// Plain 3/6 digit hex.
	$hex = sanitize_hex_color( $value );
	if ( ! empty( $hex ) ) {
		return $hex;
	}

	// 4/8 digit hex with alpha.
	if ( preg_match( '/^#([A-Fa-f0-9]{4}|[A-Fa-f0-9]{8})$/', $value ) ) {
		return $value;
	}

	// rgb()/rgba()/hsl()/hsla() - digits, dots, commas, %, spaces and / only.
	if ( preg_match( '/^(rgb|rgba|hsl|hsla)\(\s*[0-9.,%\s\/deg-]+\s*\)$/i', $value ) ) {
		return $value;
	}

	// A bare CSS keyword such as "transparent" or "red".
	if ( preg_match( '/^[a-z]{3,20}$/i', $value ) ) {
		return strtolower( $value );
	}

	return '';
}

/**
 * Sanitize a non-negative number used in a CSS length / count / delay.
 *
 * Kept deliberately permissive: the value is clamped rather than rejected, so
 * an out-of-range entry degrades to the nearest sane value instead of being
 * blanked.
 *
 * @param mixed $value Raw value.
 * @param int   $min   Lower bound.
 * @param int   $max   Upper bound.
 * @return string Numeric string, or '' when nothing was entered.
 */
function horsetools_sanitize_number( $value, $min = 0, $max = 100000 ) {
	if ( is_array( $value ) || is_object( $value ) ) {
		return '';
	}
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( ! is_numeric( $value ) ) {
		return '';
	}
	$num = (float) $value;
	if ( $num < $min ) {
		$num = $min;
	}
	if ( $num > $max ) {
		$num = $max;
	}
	// Preserve integers as integers so they still render as "10px", not "10.0px".
	return ( floor( $num ) == $num ) ? (string) (int) $num : (string) $num;
}

/**
 * Return a safe CSS colour, or a fallback.
 *
 * @param mixed  $value    Stored value.
 * @param string $fallback Returned when the value is not a valid colour.
 * @return string
 */
function horsetools_css_color( $value, $fallback = '' ) {
	$color = horsetools_sanitize_color( $value );
	return ( '' === $color ) ? $fallback : $color;
}

/**
 * Return a safe CSS number, or a fallback.
 *
 * @param mixed  $value    Stored value.
 * @param string $fallback Returned when the value is not numeric.
 * @return string
 */
function horsetools_css_number( $value, $fallback = '0' ) {
	if ( is_array( $value ) || is_object( $value ) || ! is_numeric( trim( (string) $value ) ) ) {
		return $fallback;
	}
	return horsetools_sanitize_number( $value, -100000, 100000 );
}

