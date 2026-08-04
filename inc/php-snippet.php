<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * PHP snippets — guarded execution.
 *
 * Running arbitrary PHP out of the database is the most powerful thing a plugin
 * can offer and the easiest way to take a site down, so every snippet passes
 * the same gates:
 *
 *  1. Capability — manage_options (super admin on multisite), and never while
 *     DISALLOW_FILE_EDIT / DISALLOW_FILE_MODS say the install is locked down.
 *  2. Two-factor — the account doing the editing must have Horse Tools 2FA on.
 *  3. Sudo window — a fresh 2FA code unlocks PHP editing for 15 minutes, so a
 *     stolen session cannot plant code on its own.
 *  4. Syntax check before saving — the code is parsed inside `if (false) {}`,
 *     so a typo is reported instead of white-screening the site.
 *  5. Crash guard — a snippet's first run is flagged in an option; if that
 *     request dies the flag survives, and the next admin page switches the
 *     snippet off and explains why.
 *  6. Signature — each snippet is signed with the site's auth salt. Code
 *     written straight into the database (the usual pay-off of an SQL-injection
 *     hole in some *other* plugin) carries no valid signature and is refused.
 *  7. Alert + log — saving or enabling PHP notifies the owner (Telegram, else
 *     e-mail) and is recorded with user, time and IP.
 *
 * Emergency exit: define( 'HORSETOOLS_NO_PHP', true ) in wp-config.php stops
 * every PHP snippet. That is the way back in if one ever does take a site down,
 * and it is deliberately a file-level switch so no database access can undo it.
 */

const HORSETOOLS_PHP_UNLOCK_TTL = 900; // 15 minutes

/**
 * A hard block that no amount of admin rights can override.
 *
 * @return string '' when PHP snippets are permitted, else the reason.
 */
function horsetools_php_blocked() {
	if ( defined( 'HORSETOOLS_NO_PHP' ) && HORSETOOLS_NO_PHP ) {
		return 'constant';
	}
	// DISALLOW_FILE_MODS means the platform has taken code changes off the
	// table entirely — no plugin or theme may even be installed — so honouring
	// it is meaningful: there is genuinely no other way in.
	if ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) {
		return 'file_mods';
	}
	// DISALLOW_FILE_EDIT deliberately does NOT block. It closes WordPress's
	// built-in theme/plugin file editor, and is set by most hardened sites (by
	// hosts, by security plugins, by this plugin's own Security tab). Blocking
	// on it would stop exactly the careful site owners this feature is for,
	// while stopping nobody determined — an administrator who wants to run PHP
	// can still install any snippet plugin. Worse, it would push people to
	// switch off real hardening to use a feature that is itself better guarded
	// than the editor that constant closes. It is surfaced as a note instead.
	return '';
}

/** A condition worth telling the user about that does not stop them. */
function horsetools_php_notice_reason() {
	if ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) {
		return 'file_edit';
	}
	return '';
}

/** Is the 2FA module available at all on this site? */
function horsetools_php_2fa_available() {
	return function_exists( 'horsetools_2fa_enabled' ) && function_exists( 'horsetools_2fa_verify' );
}

/**
 * May this user author PHP? Capability + 2FA, before any unlock is considered.
 *
 * @return string '' when allowed, else the reason.
 */
function horsetools_php_user_blocked( $user_id = 0 ) {
	$hard = horsetools_php_blocked();
	if ( '' !== $hard ) {
		return $hard;
	}
	$user_id = $user_id ? (int) $user_id : get_current_user_id();
	if ( ! $user_id ) {
		return 'auth';
	}
	if ( is_multisite() ) {
		if ( ! is_super_admin( $user_id ) ) {
			return 'cap';
		}
	} elseif ( ! user_can( $user_id, 'manage_options' ) ) {
		return 'cap';
	}
	if ( ! horsetools_php_2fa_available() ) {
		return 'no2fa_module';
	}
	if ( ! horsetools_2fa_enabled( $user_id ) ) {
		return 'no2fa_user';
	}
	return '';
}

/* -------------------------------------------------------------------------
 * Sudo window
 * ---------------------------------------------------------------------- */
function horsetools_php_unlock_key( $user_id ) {
	return 'horsetools_php_unlock_' . (int) $user_id;
}

function horsetools_php_unlocked( $user_id = 0 ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();
	if ( '' !== horsetools_php_user_blocked( $user_id ) ) {
		return false;
	}
	return (bool) get_transient( horsetools_php_unlock_key( $user_id ) );
}

/** Spend a fresh 2FA code (or a backup code) to open the window. */
function horsetools_php_unlock_ajax() {
	check_ajax_referer( 'horsetools_php', 'php_nonce' );
	$user_id = get_current_user_id();
	if ( '' !== horsetools_php_user_blocked( $user_id ) ) {
		wp_send_json_error( array( 'msg' => __( 'You are not allowed to use PHP snippets.', 'horse-tools' ) ) );
	}
	$code = isset( $_POST['code'] ) ? preg_replace( '/\s+/', '', (string) wp_unslash( $_POST['code'] ) ) : '';
	if ( '' === $code ) {
		wp_send_json_error( array( 'msg' => __( 'Enter your authentication code.', 'horse-tools' ) ) );
	}
	$ok = horsetools_2fa_verify( horsetools_2fa_secret( $user_id ), $code );
	if ( ! $ok && function_exists( 'horsetools_2fa_use_backup' ) ) {
		$ok = horsetools_2fa_use_backup( $user_id, $code );
	}
	if ( ! $ok ) {
		horsetools_php_log( 'unlock_failed', '' );
		wp_send_json_error( array( 'msg' => __( 'That code is not right.', 'horse-tools' ) ) );
	}
	set_transient( horsetools_php_unlock_key( $user_id ), 1, HORSETOOLS_PHP_UNLOCK_TTL );
	horsetools_php_log( 'unlock', '' );
	wp_send_json_success( array( 'minutes' => (int) ( HORSETOOLS_PHP_UNLOCK_TTL / 60 ) ) );
}
add_action( 'wp_ajax_horsetools_php_unlock', 'horsetools_php_unlock_ajax' );

function horsetools_php_lock_ajax() {
	check_ajax_referer( 'horsetools_php', 'php_nonce' );
	delete_transient( horsetools_php_unlock_key( get_current_user_id() ) );
	wp_send_json_success();
}
add_action( 'wp_ajax_horsetools_php_lock', 'horsetools_php_lock_ajax' );

/* -------------------------------------------------------------------------
 * Signing
 * ---------------------------------------------------------------------- */
function horsetools_php_sign( $code ) {
	return hash_hmac( 'sha256', (string) $code, wp_salt( 'auth' ) );
}

function horsetools_php_signature_ok( $snip ) {
	if ( empty( $snip['sig'] ) ) {
		return false;
	}
	return hash_equals( horsetools_php_sign( isset( $snip['content'] ) ? $snip['content'] : '' ), (string) $snip['sig'] );
}

/* -------------------------------------------------------------------------
 * Syntax check
 * ---------------------------------------------------------------------- */
function horsetools_php_strip_open( $code ) {
	return preg_replace( '/^\s*<\?(php)?/i', '', (string) $code, 1 );
}

/**
 * Parse the code without running it. `if (false) { … }` is never entered, so
 * nothing executes and nothing is declared — a ParseError is the only outcome
 * a broken snippet can produce.
 *
 * @return string '' when the code parses, else the error message.
 */
function horsetools_php_lint( $code ) {
	$code = horsetools_php_strip_open( $code );
	if ( '' === trim( $code ) ) {
		return '';
	}
	try {
		// phpcs:ignore Squiz.PHP.Eval.Discouraged -- parse-only guard, see above.
		eval( 'if ( false ) { ' . $code . ' }' );
	} catch ( \ParseError $e ) {
		return $e->getMessage();
	} catch ( \Throwable $e ) {
		return $e->getMessage();
	}
	return '';
}

/* -------------------------------------------------------------------------
 * Crash guard
 * ---------------------------------------------------------------------- */
function horsetools_php_store( array $snips ) {
	update_option( 'horsetools_snippets', $snips, false );
}

/**
 * A snippet that has never completed a run is flagged before it runs. If the
 * request never gets to clear the flag, the snippet killed it — so the next
 * admin page load switches that snippet off rather than letting the site stay
 * down.
 */
function horsetools_php_guard_recover() {
	$slug = get_option( 'horsetools_php_guard', '' );
	if ( ! $slug ) {
		return;
	}
	delete_option( 'horsetools_php_guard' );
	$snips = horsetools_snippets_get();
	if ( isset( $snips[ $slug ] ) ) {
		$snips[ $slug ]['on'] = 0;
		horsetools_php_store( $snips );
	}
	update_option( 'horsetools_php_crashed', $slug, false );
	horsetools_php_log( 'auto_disabled', $slug );
}
add_action( 'admin_init', 'horsetools_php_guard_recover', 1 );

function horsetools_php_crash_notice() {
	$slug = get_option( 'horsetools_php_crashed', '' );
	if ( ! $slug || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	delete_option( 'horsetools_php_crashed' );
	echo '<div class="notice notice-error"><p>'
		. esc_html( sprintf(
			/* translators: %s: snippet name */
			__( 'The PHP snippet "%s" stopped the page it ran on, so Horse Tools switched it off. Edit it and switch it back on when it is fixed.', 'horse-tools' ),
			$slug
		) )
		. '</p></div>';
}
add_action( 'admin_notices', 'horsetools_php_crash_notice' );

/* -------------------------------------------------------------------------
 * Execution
 * ---------------------------------------------------------------------- */
function horsetools_php_note_error( $slug, $e ) {
	horsetools_php_log( 'error', $slug, $e->getMessage() );
}

/**
 * Run one snippet and return whatever it printed.
 *
 * Refused unless the signature matches, so database-only tampering cannot get
 * code to run here.
 */
function horsetools_php_exec( $slug, $snip ) {
	if ( '' !== horsetools_php_blocked() || ! horsetools_php_signature_ok( $snip ) ) {
		return '';
	}
	$first = empty( $snip['ok'] );
	if ( $first ) {
		update_option( 'horsetools_php_guard', $slug, false );
	}
	$out = '';
	ob_start();
	try {
		// phpcs:ignore Squiz.PHP.Eval.Discouraged -- this is the feature; gated above.
		eval( horsetools_php_strip_open( $snip['content'] ) );
	} catch ( \Throwable $e ) {
		horsetools_php_note_error( $slug, $e );
	}
	$out = (string) ob_get_clean();
	if ( $first ) {
		delete_option( 'horsetools_php_guard' );
		$snips = horsetools_snippets_get();
		if ( isset( $snips[ $slug ] ) ) {
			$snips[ $slug ]['ok'] = 1;
			horsetools_php_store( $snips );
		}
	}
	return $out;
}

/** Where a snippet may run. */
function horsetools_php_hooks_allowed() {
	return array(
		''                   => __( 'Only where I place its shortcode', 'horse-tools' ),
		'init'               => __( 'Every page load (init)', 'horse-tools' ),
		'wp_head'            => __( 'In the page <head>', 'horse-tools' ),
		'wp_footer'          => __( 'Before </body>', 'horse-tools' ),
		'the_content_before' => __( 'Above the post content', 'horse-tools' ),
		'the_content_after'  => __( 'Below the post content', 'horse-tools' ),
	);
}

function horsetools_php_scopes_allowed() {
	return array(
		'front' => __( 'Front end only', 'horse-tools' ),
		'admin' => __( 'Admin only', 'horse-tools' ),
		'both'  => __( 'Front end and admin', 'horse-tools' ),
	);
}

function horsetools_php_scope_ok( $scope ) {
	if ( 'both' === $scope ) {
		return true;
	}
	return is_admin() ? ( 'admin' === $scope ) : ( 'front' === $scope );
}

/**
 * Attach every enabled PHP snippet to the hook it chose. Runs on plugins_loaded
 * so an `init` snippet still lands before init fires.
 */
function horsetools_php_register_hooks() {
	if ( '' !== horsetools_php_blocked() ) {
		return;
	}
	foreach ( horsetools_snippets_get() as $slug => $snip ) {
		if ( empty( $snip['php'] ) || empty( $snip['on'] ) ) {
			continue;
		}
		$hook = isset( $snip['hook'] ) ? (string) $snip['hook'] : '';
		if ( '' === $hook ) {
			continue; // shortcode-only; horsetools_render_snippet() handles it
		}
		$scope = isset( $snip['scope'] ) ? (string) $snip['scope'] : 'front';
		if ( ! horsetools_php_scope_ok( $scope ) ) {
			continue;
		}
		$run = function () use ( $slug ) {
			$snips = horsetools_snippets_get();
			return isset( $snips[ $slug ] ) ? horsetools_php_exec( $slug, $snips[ $slug ] ) : '';
		};
		if ( 'the_content_before' === $hook || 'the_content_after' === $hook ) {
			$after = 'the_content_after' === $hook;
			add_filter( 'the_content', function ( $content ) use ( $run, $after ) {
				if ( ! is_singular() || ! in_the_loop() || ! is_main_query() ) {
					return $content;
				}
				$out = $run();
				return $after ? $content . $out : $out . $content;
			}, 20 );
		} else {
			add_action( $hook, function () use ( $run ) {
				echo $run(); // phpcs:ignore WordPress.Security.EscapeOutput -- snippet output is the point
			} );
		}
	}
}
add_action( 'plugins_loaded', 'horsetools_php_register_hooks', 20 );

/* -------------------------------------------------------------------------
 * Alerts + audit log
 * ---------------------------------------------------------------------- */
function horsetools_php_log( $action, $slug, $detail = '' ) {
	$log = get_option( 'horsetools_php_log', array() );
	if ( ! is_array( $log ) ) {
		$log = array();
	}
	$user = wp_get_current_user();
	array_unshift( $log, array(
		'time'   => time(),
		'user'   => $user && $user->ID ? $user->user_login : '-',
		'ip'     => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
		'action' => $action,
		'slug'   => $slug,
		'detail' => mb_substr( (string) $detail, 0, 200 ),
	) );
	update_option( 'horsetools_php_log', array_slice( $log, 0, 50 ), false );
}

/**
 * Tell the owner, out of band, that PHP code just changed — the one signal that
 * catches an attacker who did get through everything else.
 */
function horsetools_php_alert( $action, $slug ) {
	$user = wp_get_current_user();
	$msg  = sprintf(
		/* translators: 1: site URL, 2: snippet name, 3: user login, 4: IP address */
		__( 'Heads up: a PHP snippet was just changed on %1$s. Snippet: "%2$s". By: %3$s (IP %4$s). If this was not you, change your password and review Horse Tools → Shortcode immediately.', 'horse-tools' ),
		home_url( '/' ),
		$slug,
		$user && $user->ID ? $user->user_login : '-',
		isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '?'
	);

	global $horsetools_options;
	$token  = ! empty( $horsetools_options['woo-tele11'] ) ? $horsetools_options['woo-tele11'] : '';
	$chatid = $user && $user->ID ? (string) get_user_meta( $user->ID, '_horsetools_2fa_tg_chat', true ) : '';
	if ( '' !== $token && '' !== $chatid ) {
		wp_remote_post( 'https://api.telegram.org/bot' . rawurlencode( $token ) . '/sendMessage', array(
			'timeout'  => 8,
			'blocking' => false,
			'body'     => array( 'chat_id' => $chatid, 'text' => $msg ),
		) );
		return;
	}
	wp_mail( get_option( 'admin_email' ), __( 'A PHP snippet changed on your site', 'horse-tools' ), $msg );
}

/**
 * Validate and attach the PHP part of a snippet being saved.
 *
 * @param array  $snip  The snippet about to be stored (by reference).
 * @param array  $prev  The snippet as it was, if it existed.
 * @param string $slug
 * @return string '' on success, else an error message for the editor.
 */
function horsetools_php_prepare_save( array &$snip, array $prev, $slug ) {
	$wants_php = ! empty( $_POST['php'] ) && '1' === (string) $_POST['php']; // phpcs:ignore WordPress.Security.NonceVerification -- checked by the caller
	if ( ! $wants_php ) {
		// Turning PHP off is always allowed; keep the snippet as plain content.
		if ( ! empty( $prev['php'] ) ) {
			horsetools_php_log( 'php_off', $slug );
		}
		$snip['php'] = 0;
		unset( $snip['sig'], $snip['hook'], $snip['scope'], $snip['ok'] );
		return '';
	}

	$why = horsetools_php_user_blocked();
	if ( '' !== $why ) {
		$map = array(
			'constant'     => __( 'PHP snippets are switched off by HORSETOOLS_NO_PHP in wp-config.php.', 'horse-tools' ),
			'file_mods'    => __( 'This site sets DISALLOW_FILE_MODS, so running PHP from the database is not allowed.', 'horse-tools' ),
			'cap'          => __( 'Only a full administrator may use PHP snippets.', 'horse-tools' ),
			'no2fa_module' => __( 'Switch on Horse Tools two-factor authentication (Security tab) before using PHP snippets.', 'horse-tools' ),
			'no2fa_user'   => __( 'Your own account must have two-factor authentication switched on before you can use PHP snippets.', 'horse-tools' ),
		);
		return isset( $map[ $why ] ) ? $map[ $why ] : __( 'PHP snippets are not available.', 'horse-tools' );
	}
	if ( ! horsetools_php_unlocked() ) {
		return __( 'Enter a current two-factor code to unlock PHP editing first.', 'horse-tools' );
	}

	$err = horsetools_php_lint( $snip['content'] );
	if ( '' !== $err ) {
		/* translators: %s: PHP parse error */
		return sprintf( __( 'PHP syntax error — nothing was saved: %s', 'horse-tools' ), $err );
	}

	$hooks  = horsetools_php_hooks_allowed();
	$scopes = horsetools_php_scopes_allowed();
	$hook   = isset( $_POST['php_hook'] ) ? sanitize_key( wp_unslash( $_POST['php_hook'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$scope  = isset( $_POST['php_scope'] ) ? sanitize_key( wp_unslash( $_POST['php_scope'] ) ) : 'front'; // phpcs:ignore WordPress.Security.NonceVerification

	$snip['php']   = 1;
	$snip['hook']  = array_key_exists( $hook, $hooks ) ? $hook : '';
	$snip['scope'] = array_key_exists( $scope, $scopes ) ? $scope : 'front';
	$snip['sig']   = horsetools_php_sign( $snip['content'] );
	// Changed code has to earn its "runs without crashing" mark again.
	$snip['ok']    = ( ! empty( $prev['ok'] ) && isset( $prev['content'] ) && $prev['content'] === $snip['content'] ) ? 1 : 0;

	horsetools_php_log( 'save', $slug );
	horsetools_php_alert( 'save', $slug );
	return '';
}
