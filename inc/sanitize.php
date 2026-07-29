<?php
/**
 * Horse Tools - shared settings sanitizer.
 *
 * Every register_setting() call in main/*.php stores an associative array that
 * mixes types (colours, numbers, URLs, plain text, and a few fields that hold
 * raw HTML/JS/SVG by design). This file provides one reusable walker plus a
 * thin per-group wrapper for each option, so that nothing is ever written to
 * the database exactly as it was POSTed.
 *
 * Design rules (deliberate):
 *  - Keys are classified by EXPLICIT name, not by loose pattern matching. The
 *    same suffix means different things in different groups (e.g.
 *    'main-c1' is a colour in the TOC group while 'main-search-c1' is a
 *    number), so pattern guessing would silently corrupt settings.
 *  - Unknown keys are NEVER dropped. They fall through to sanitize_text_field()
 *    (or a recursive walk for arrays), which is lossless for plain strings.
 *  - Non-scalar values (checkbox/post-type arrays) are walked recursively.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* -------------------------------------------------------------------------
 * 1. Intentionally raw keys
 * ---------------------------------------------------------------------- */

/**
 * Keys whose value MUST be stored (and echoed) unfiltered.
 *
 * These are the plugin's "custom code" features: the header/footer/login
 * script boxes, the custom CSS boxes, the AdSense / ads.txt payloads, the
 * custom SVG icon fields, the admin-footer HTML, and a couple of stored
 * credentials (an SMTP app password, Google service-account JSON) that must
 * survive byte-for-byte.
 *
 * Filtering any of these breaks the feature outright - a custom <script> or an
 * inline SVG cannot survive sanitize_text_field() or wp_kses_post().
 *
 * SECURITY NOTE: these options are only writable through the plugin's own
 * settings screens, which are gated on the 'manage_options' capability, and
 * WordPress already treats a user who has 'manage_options' (and
 * 'unfiltered_html') as able to inject arbitrary front-end markup. This
 * allow-list is therefore explicit and closed: nothing outside it is exempt.
 *
 * @return string[] Exact key names that are stored raw.
 */
function horsetools_sanitize_raw_keys() {
	return array(
		// main/code.php - custom CSS (code1/11/12) and custom JS/HTML blocks.
		'code1', 'code11', 'code12', 'code2', 'code3', 'code4', 'code5',
		// main/ads.php - AdSense / ad network code and the ads.txt body.
		'ads-sense11', 'ads-sense-p1', 'ads-sense-p2', 'ads-sense-c3', 'ads-adstxt2',
		// main/admin.php - HTML shown in the wp-admin footer.
		'custom-foo11',
		// main/admin.php - SMTP application password (must not be trimmed/altered).
		'mail-gsmtp14',
		// main/gindex.php - Google service-account JSON credentials.
		'json1',
	);
}

/**
 * Dynamic (numbered) raw keys.
 *
 * The chat button / navigation bar repeaters let the admin paste a custom
 * inline <svg> per item, and the Google Index screen stores one JSON
 * credential blob per row. Both are raw by design; see the note above.
 *
 * @return string[] Regular expressions matched against the key name.
 */
function horsetools_sanitize_raw_key_patterns() {
	return array(
		'/^chat-nut4\d+$/',   // custom SVG icon, chat button repeater.
		'/^chat-nav1\d+$/',   // custom SVG icon, navigation bar repeater.
		'/^chat-nav01$/',     // custom SVG icon, navigation bar centre button.
		'/^json\d+$/',        // Google service-account JSON credential rows.
	);
}

/**
 * Is this key on the intentionally-raw allow-list?
 *
 * @param string $key Option key.
 * @return bool
 */
function horsetools_sanitize_is_raw( $key ) {
	if ( in_array( $key, horsetools_sanitize_raw_keys(), true ) ) {
		return true;
	}
	foreach ( horsetools_sanitize_raw_key_patterns() as $pattern ) {
		if ( preg_match( $pattern, $key ) ) {
			return true;
		}
	}
	return false;
}

/* -------------------------------------------------------------------------
 * 2. Type helpers
 * ---------------------------------------------------------------------- */

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
 * Numeric keys and their clamp ranges.
 *
 * @return array<string,array{0:int,1:int}>
 */
function horsetools_sanitize_number_keys() {
	return array(
		// main/admin.php - dark mode / scrollbar / chat button geometry.
		'main-mode12'      => array( 0, 5000 ),   // offset from bottom, px
		'main-mode13'      => array( 0, 5000 ),   // offset from side, px
		'main-scroll13'    => array( 0, 200 ),    // scrollbar radius, px
		'main-scroll14'    => array( 0, 200 ),    // scrollbar width, px
		'custom-main18'    => array( 0, 200 ),    // login form border radius, px
		'chat-nut-bot'     => array( 0, 5000 ),   // chat button offset, px
		'chat-nut-lr'      => array( 0, 5000 ),   // chat button offset, px
		'chat-nut-rus'     => array( 0, 200 ),    // chat icon radius, px
		'chat-nut-op'      => array( 0, 1 ),      // chat icon opacity, 0-1
		// main/admin.php - media / uploads.
		'media-up21'       => array( 0, 100000 ), // max upload size, MB
		'media-zip11'      => array( 0, 100 ),    // JPEG/PNG quality
		'media-zip21'      => array( 0, 20000 ),  // max width, px
		'media-zip22'      => array( 0, 20000 ),  // max height, px
		'media-webp11'     => array( 0, 100 ),    // WebP quality
		'media-avif21'     => array( 0, 100 ),    // AVIF quality
		'media-cutop14'    => array( 0, 100 ),    // frame opacity, %
		'media-logo13'     => array( 0, 100 ),    // watermark scale, %
		'media-logo14'     => array( 0, 100 ),    // watermark opacity, %
		// main/admin.php - SMTP port.
		'mail-gsmtp16'     => array( 0, 65535 ),
		// main/ads.php
		'ads-click-c2'     => array( 0, 8760 ),   // cooldown, hours
		'ads-sense-c2'     => array( 1, 1000 ),   // insert every N tags
		// main/notify.php
		'notify-popup-c1'  => array( 0, 86400 ),  // popup delay, seconds
		'notify-popup-m1'  => array( 0, 200 ),    // popup radius, px
		'notify-popup-m2'  => array( 0, 5000 ),   // popup max width, px
		// main/page/2scuri.php — login-attempt limiter
		'scuri-login-max'  => array( 1, 50 ),     // failed attempts before lockout
		'scuri-login-mins' => array( 1, 1440 ),   // lockout length, minutes
		// main/page/11google.php
		'goo-cap13'        => array( 0, 1 ),      // reCAPTCHA v3 score threshold
		// main/search.php
		'main-search-c1'   => array( 1, 500 ),    // result limit
		// main/shortcode.php
		'shortcode-s41'    => array( 0, 86400 ),  // countdown, seconds
		'shortcode-s44'    => array( 0, 100 ),    // underline thickness, px
		'shortcode-s45'    => array( 0, 200 ),    // border radius, px
		// main/toc.php
		'main-si1'         => array( 1, 100 ),    // font size, px
		'main-r1'          => array( 0, 200 ),    // radius, px
		'main-r2'          => array( 0, 200 ),    // radius, px
		'main-her2'        => array( 0, 100 ),    // vertical position, %
		'main-her3'        => array( 0, 500 ),    // horizontal offset, px
		'tag2'             => array( 1, 1000 ),   // insert before Nth heading
	);
}

/**
 * Keys holding a CSS colour.
 *
 * @return string[]
 */
function horsetools_sanitize_color_keys() {
	return array(
		// main/admin.php
		'chat-ico-color', 'chat-nut-color',
		'chat-nav-c1', 'chat-nav-c3', 'chat-nav-c31', 'chat-nav-c4', 'chat-nav-c5', 'chat-nav-c6',
		'custom-bg11',
		'custom-main11', 'custom-main12', 'custom-main13', 'custom-main14',
		'custom-main15', 'custom-main16', 'custom-main17',
		'main-mode-c1', 'main-scroll11', 'main-scroll12',
		'media-cutop-c1', 'media-logo10-c1',
		// main/notify.php
		'notify-block-c1', 'notify-block-c2', 'notify-cookie-c1', 'notify-notis-c1',
		// main/search.php
		'main-search-c2', 'main-search-code2',
		// main/shortcode.php
		'shortcode-s42', 'shortcode-s43',
		// main/toc.php
		'main-b1', 'main-b2', 'main-b3',
		'main-c1', 'main-c2', 'main-c4', 'main-c5', 'main-c6', 'main-c7',
		'main-t1', 'main-t2',
	);
}

/**
 * Keys holding a single URL.
 *
 * @return string[]
 */
function horsetools_sanitize_url_keys() {
	return array(
		// main/admin.php
		'custom-logo1',      // login screen logo
		'custom-bg12',       // login screen background image
		'custom-logbar21',   // admin bar logo
		'media-cutop12',     // custom frame image
		'media-logo11',      // watermark image
		'chat-nav03',        // navigation bar centre button link
		// main/notify.php
		'notify-popup11',    // popup image
		'notify-popup14',    // popup link
		'notify-cookie-l1',  // cookie policy link
		// main/redirects.php
		'redi12',            // "site closed" redirect target
	);
}

/**
 * Dynamic URL keys (repeater rows).
 *
 * @return string[] Regular expressions.
 */
function horsetools_sanitize_url_key_patterns() {
	return array(
		'/^chat-nav3\d+$/',   // navigation bar item link
		'/^rechan2\d+$/',     // redirect destination
	);
}

/**
 * Keys holding a single e-mail address.
 *
 * @return string[]
 */
function horsetools_sanitize_email_keys() {
	return array(
		'mail-gsmtp12', // SMTP "from" address
	);
}

/**
 * Rich-text keys: HTML is expected but must be limited to post-safe markup.
 *
 * @return string[]
 */
function horsetools_sanitize_kses_keys() {
	return array(
		// main/admin.php
		'custom-wid12',      // dashboard welcome widget body
		'mail-new12',        // new-user e-mail body
		// main/notify.php
		'notify-block13',    // ad-blocker notice body
		'notify-cookie12',   // cookie notice body
		'notify-notis11',    // site notice body
		'notify-popup13',    // popup body
		// main/shortcode.php
		'shortcode-s12',     // "content locked" message
	);
}

/**
 * Multiline plain-text keys: one entry per line (URL lists, slug lists).
 *
 * @return string[]
 */
function horsetools_sanitize_textarea_keys() {
	return array(
		'chat-nav-hi',    // page slugs where the nav bar is hidden
		'toc-page-hi',    // page slugs where the TOC is hidden
		'ads-click11',    // list of ad click destinations
		'url',            // main/gindex.php - list of URLs to submit
		'chat-quick',     // chat live-chat quick-reply lines (label|url)
		'chat-hours',     // chat opening-hours lines
	);
}

/* -------------------------------------------------------------------------
 * 3. The walker
 * ---------------------------------------------------------------------- */

/**
 * Sanitize a single value according to its key.
 *
 * @param string $key   Option key.
 * @param mixed  $value Raw value.
 * @return mixed Sanitized value.
 */
function horsetools_sanitize_field( $key, $value ) {
	// Arrays (post-type checkbox groups, image size lists...) - walk them.
	if ( is_array( $value ) ) {
		$clean = array();
		foreach ( $value as $sub_key => $sub_value ) {
			$safe_key           = is_int( $sub_key ) ? $sub_key : sanitize_text_field( (string) $sub_key );
			$clean[ $safe_key ] = horsetools_sanitize_field( $key, $sub_value );
		}
		return $clean;
	}

	if ( ! is_scalar( $value ) && null !== $value ) {
		// Objects should never appear here; keep the key but empty it.
		return '';
	}

	// Intentionally raw - see horsetools_sanitize_raw_keys().
	if ( horsetools_sanitize_is_raw( $key ) ) {
		return $value;
	}

	$numbers = horsetools_sanitize_number_keys();
	if ( isset( $numbers[ $key ] ) ) {
		return horsetools_sanitize_number( $value, $numbers[ $key ][0], $numbers[ $key ][1] );
	}

	if ( in_array( $key, horsetools_sanitize_color_keys(), true ) ) {
		return horsetools_sanitize_color( $value );
	}

	if ( in_array( $key, horsetools_sanitize_email_keys(), true ) ) {
		return sanitize_email( (string) $value );
	}

	if ( in_array( $key, horsetools_sanitize_url_keys(), true ) ) {
		return esc_url_raw( (string) $value );
	}
	foreach ( horsetools_sanitize_url_key_patterns() as $pattern ) {
		if ( preg_match( $pattern, $key ) ) {
			return esc_url_raw( (string) $value );
		}
	}

	if ( in_array( $key, horsetools_sanitize_kses_keys(), true ) ) {
		return wp_kses_post( (string) $value );
	}

	if ( in_array( $key, horsetools_sanitize_textarea_keys(), true ) ) {
		return sanitize_textarea_field( (string) $value );
	}

	// Default: lossless for plain strings, and safe for everything else.
	return sanitize_text_field( (string) $value );
}

/**
 * Sanitize a whole settings array.
 *
 * Every incoming key is preserved; nothing is added and nothing is removed.
 *
 * @param mixed $input Value posted for the option.
 * @return array Sanitized settings array.
 */
function horsetools_sanitize_settings_array( $input ) {
	if ( ! is_array( $input ) ) {
		return array();
	}
	$clean = array();
	foreach ( $input as $key => $value ) {
		$safe_key           = sanitize_text_field( (string) $key );
		$clean[ $safe_key ] = horsetools_sanitize_field( $safe_key, $value );
	}
	return $clean;
}

/* -------------------------------------------------------------------------
 * 4. Per-group wrappers used as sanitize_callback
 * ---------------------------------------------------------------------- */

function horsetools_sanitize_main( $input )      { return horsetools_sanitize_settings_array( $input ); }
function horsetools_sanitize_ads( $input )       { return horsetools_sanitize_settings_array( $input ); }

/**
 * The Clean scheduling option holds only cron-<target> => frequency slug. Each
 * value must be one of the known frequencies; anything else becomes 'off' so a
 * tampered form can never make a target run on an unexpected schedule.
 */
function horsetools_sanitize_clean( $input ) {
	if ( ! is_array( $input ) ) {
		return array();
	}
	$allowed = array( 'off', 'daily', 'weekly', 'monthly' );
	$clean   = array();
	foreach ( $input as $key => $value ) {
		$key = sanitize_key( $key );
		if ( 0 !== strpos( $key, 'cron-' ) ) {
			continue;
		}
		$clean[ $key ] = in_array( $value, $allowed, true ) ? $value : 'off';
	}
	return $clean;
}
function horsetools_sanitize_code( $input )      { return horsetools_sanitize_settings_array( $input ); }
function horsetools_sanitize_debug( $input )     { return horsetools_sanitize_settings_array( $input ); }
function horsetools_sanitize_extend( $input )    { return horsetools_sanitize_settings_array( $input ); }
function horsetools_sanitize_font( $input )      { return horsetools_sanitize_settings_array( $input ); }
function horsetools_sanitize_gindex( $input )    { return horsetools_sanitize_settings_array( $input ); }
function horsetools_sanitize_notify( $input )    { return horsetools_sanitize_settings_array( $input ); }
function horsetools_sanitize_redirects( $input ) { return horsetools_sanitize_settings_array( $input ); }
function horsetools_sanitize_search( $input )    { return horsetools_sanitize_settings_array( $input ); }
function horsetools_sanitize_shortcode( $input ) { return horsetools_sanitize_settings_array( $input ); }
function horsetools_sanitize_toc( $input )       { return horsetools_sanitize_settings_array( $input ); }

/* -------------------------------------------------------------------------
 * 5. Output helpers for CSS contexts
 * ---------------------------------------------------------------------- */

/**
 * A CSS context is not HTML-escapable, so values concatenated into inline
 * <style> blocks are validated by type instead of escaped. These two helpers
 * are the single place the front-end templates go through.
 */

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
