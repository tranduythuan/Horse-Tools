<?php
/**
 * Two-factor authentication (TOTP) — opt-in, per user.
 *
 * Loaded from inc/scuri.php only when the feature is switched on. Every piece
 * uses standard WordPress hooks and user-meta, never core files, so WordPress
 * updates never affect it — and disabling the plugin instantly restores the
 * normal login.
 *
 * Flow: username + password are checked by WordPress as usual; if that user has
 * 2FA enrolled we interrupt at `wp_login` (clear the just-set cookie), show an
 * interim screen asking for the 6-digit code, and only set the auth cookie once
 * the code (or a backup / e-mailed / Telegram code) is verified.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;

/* ---------------------------------------------------------------------------
 * TOTP core (RFC 6238, 30s step, SHA-1, 6 digits — what every authenticator app
 * uses).
 * ------------------------------------------------------------------------- */
function horsetools_2fa_base32_decode( $b32 ) {
	$map  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
	$b32  = strtoupper( (string) $b32 );
	$bits = '';
	for ( $i = 0, $n = strlen( $b32 ); $i < $n; $i++ ) {
		$v = strpos( $map, $b32[ $i ] );
		if ( false === $v ) { continue; }
		$bits .= str_pad( decbin( $v ), 5, '0', STR_PAD_LEFT );
	}
	$out = '';
	for ( $i = 0, $n = strlen( $bits ); $i + 8 <= $n; $i += 8 ) {
		$out .= chr( bindec( substr( $bits, $i, 8 ) ) );
	}
	return $out;
}
function horsetools_2fa_base32_encode( $bin ) {
	$map  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
	$bits = '';
	for ( $i = 0, $n = strlen( $bin ); $i < $n; $i++ ) {
		$bits .= str_pad( decbin( ord( $bin[ $i ] ) ), 8, '0', STR_PAD_LEFT );
	}
	$out = '';
	for ( $i = 0, $n = strlen( $bits ); $i < $n; $i += 5 ) {
		$out .= $map[ bindec( str_pad( substr( $bits, $i, 5 ), 5, '0' ) ) ];
	}
	return $out;
}
function horsetools_2fa_code( $secret, $slice = null ) {
	$key = horsetools_2fa_base32_decode( $secret );
	if ( null === $slice ) { $slice = (int) floor( time() / 30 ); }
	$bin  = pack( 'N', 0 ) . pack( 'N', (int) $slice );          // 8-byte big-endian counter
	$hash = hash_hmac( 'sha1', $bin, $key, true );
	$off  = ord( $hash[19] ) & 0x0f;
	$num  = ( ( ord( $hash[ $off ] ) & 0x7f ) << 24 )
		| ( ( ord( $hash[ $off + 1 ] ) & 0xff ) << 16 )
		| ( ( ord( $hash[ $off + 2 ] ) & 0xff ) << 8 )
		| ( ord( $hash[ $off + 3 ] ) & 0xff );
	return str_pad( (string) ( $num % 1000000 ), 6, '0', STR_PAD_LEFT );
}
function horsetools_2fa_verify( $secret, $code, $window = 1 ) {
	$code = preg_replace( '/\D/', '', (string) $code );
	if ( 6 !== strlen( $code ) || '' === (string) $secret ) { return false; }
	$slice = (int) floor( time() / 30 );
	for ( $i = -$window; $i <= $window; $i++ ) {
		if ( hash_equals( horsetools_2fa_code( $secret, $slice + $i ), $code ) ) { return true; }
	}
	return false;
}

/* ---------------------------------------------------------------------------
 * Per-user state
 * ------------------------------------------------------------------------- */
function horsetools_2fa_enabled( $user_id ) {
	return (bool) get_user_meta( $user_id, '_horsetools_2fa_enabled', true );
}
function horsetools_2fa_secret( $user_id ) {
	return (string) get_user_meta( $user_id, '_horsetools_2fa_secret', true );
}
function horsetools_2fa_new_secret() {
	return horsetools_2fa_base32_encode( random_bytes( 20 ) );
}
function horsetools_2fa_make_backup() {
	$codes = array();
	for ( $i = 0; $i < 10; $i++ ) { $codes[] = bin2hex( random_bytes( 4 ) ); } // 8 hex chars
	return $codes;
}
function horsetools_2fa_store_backup( $user_id, array $codes ) {
	update_user_meta( $user_id, '_horsetools_2fa_backup', array_map( 'wp_hash_password', $codes ) );
}
function horsetools_2fa_use_backup( $user_id, $code ) {
	$code = strtolower( trim( (string) $code ) );
	if ( '' === $code ) { return false; }
	$hashes = get_user_meta( $user_id, '_horsetools_2fa_backup', true );
	if ( ! is_array( $hashes ) ) { return false; }
	foreach ( $hashes as $i => $hash ) {
		if ( wp_check_password( $code, $hash ) ) {
			unset( $hashes[ $i ] );
			update_user_meta( $user_id, '_horsetools_2fa_backup', array_values( $hashes ) );
			return true;
		}
	}
	return false;
}

/* Signed values (no server-side storage needed) for the pending login and for a
 * trusted device. Keyed on wp_salt() so they can't be forged. */
function horsetools_2fa_sign( $payload ) {
	return hash_hmac( 'sha256', $payload, wp_salt( 'auth' ) );
}
function horsetools_2fa_pending_token( $user_id ) {
	$exp = time() + 300; // 5 minutes to finish the second step
	return $user_id . '|' . $exp . '|' . horsetools_2fa_sign( '2fa-pending|' . $user_id . '|' . $exp );
}
function horsetools_2fa_read_pending( $token ) {
	$p = explode( '|', (string) $token );
	if ( 3 !== count( $p ) || (int) $p[1] < time() ) { return 0; }
	return hash_equals( horsetools_2fa_sign( '2fa-pending|' . $p[0] . '|' . $p[1] ), $p[2] ) ? (int) $p[0] : 0;
}
// Derived from the account's password hash, so changing the password (or an
// admin resetting it) instantly invalidates every "trusted device" cookie.
function horsetools_2fa_trust_secret( $user_id ) {
	$u = get_userdata( $user_id );
	return $u ? hash_hmac( 'sha256', 'trust|' . $u->user_pass, wp_salt( 'auth' ) ) : '';
}
function horsetools_2fa_trust_device( $user_id ) {
	$exp = time() + 30 * DAY_IN_SECONDS;
	$val = $user_id . '|' . $exp . '|' . horsetools_2fa_sign( '2fa-trust|' . $user_id . '|' . $exp . '|' . horsetools_2fa_trust_secret( $user_id ) );
	setcookie( 'horsetools_2fa_trust', $val, $exp, defined( 'COOKIEPATH' ) ? COOKIEPATH : '/', defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '', is_ssl(), true );
}
function horsetools_2fa_device_trusted( $user_id ) {
	if ( empty( $_COOKIE['horsetools_2fa_trust'] ) ) { return false; }
	$p = explode( '|', sanitize_text_field( wp_unslash( $_COOKIE['horsetools_2fa_trust'] ) ) );
	if ( 3 !== count( $p ) || (int) $p[0] !== (int) $user_id || (int) $p[1] < time() ) { return false; }
	return hash_equals( horsetools_2fa_sign( '2fa-trust|' . $p[0] . '|' . $p[1] . '|' . horsetools_2fa_trust_secret( $user_id ) ), $p[2] );
}

/* ---------------------------------------------------------------------------
 * Recovery codes by e-mail / Telegram (each toggled in settings)
 * ------------------------------------------------------------------------- */
function horsetools_2fa_recovery_allowed( $channel ) {
	global $horsetools_options;
	return 'email' === $channel ? ! empty( $horsetools_options['scuri-2fa-email'] ) : ! empty( $horsetools_options['scuri-2fa-tg'] );
}
// Telegram recovery is only offered to a user who has set their OWN chat ID
// (and the site has a bot token + the toggle on).
function horsetools_2fa_tg_available( $user_id ) {
	global $horsetools_options;
	return ! empty( $horsetools_options['scuri-2fa-tg'] )
		&& ! empty( $horsetools_options['woo-tele11'] )
		&& '' !== (string) get_user_meta( $user_id, '_horsetools_2fa_tg_chat', true );
}
function horsetools_2fa_send_recovery( $user, $channel ) {
	$code = str_pad( (string) random_int( 0, 999999 ), 6, '0', STR_PAD_LEFT );
	set_transient( 'horsetools_2fa_rec_' . $user->ID, wp_hash_password( $code ), 600 ); // 10 min
	$msg = sprintf( __( 'Your one-time login code for %1$s is: %2$s (valid 10 minutes).', 'horse-tools' ), home_url( '/' ), $code );
	if ( 'email' === $channel ) {
		return wp_mail( $user->user_email, __( 'Your login recovery code', 'horse-tools' ), $msg );
	}
	global $horsetools_options;
	// One shared bot for the whole site, but each user's OWN chat ID — so a
	// recovery code always goes to that user's Telegram, never pooled to the admin.
	$token  = ! empty( $horsetools_options['woo-tele11'] ) ? $horsetools_options['woo-tele11'] : '';
	$chatid = (string) get_user_meta( $user->ID, '_horsetools_2fa_tg_chat', true );
	if ( '' === $token || '' === $chatid ) { return false; }
	$r = wp_remote_post( 'https://api.telegram.org/bot' . rawurlencode( $token ) . '/sendMessage', array(
		'timeout' => 8,
		'body'    => array( 'chat_id' => $chatid, 'text' => $msg ),
	) );
	return ! is_wp_error( $r );
}
function horsetools_2fa_use_recovery( $user_id, $code ) {
	$hash = get_transient( 'horsetools_2fa_rec_' . $user_id );
	if ( ! $hash ) { return false; }
	if ( wp_check_password( trim( (string) $code ), $hash ) ) {
		delete_transient( 'horsetools_2fa_rec_' . $user_id );
		return true;
	}
	return false;
}

/* ---------------------------------------------------------------------------
 * Enrolment on the user's own profile screen
 * ------------------------------------------------------------------------- */
add_action( 'show_user_profile', 'horsetools_2fa_profile' );
add_action( 'edit_user_profile', 'horsetools_2fa_profile' );
function horsetools_2fa_profile( $user ) {
	global $horsetools_options;
	if ( get_current_user_id() !== (int) $user->ID ) {
		// Not your own account: an admin may only RESET this user's 2FA (never see
		// the secret or enrol on their behalf) — the escape hatch for a user who
		// lost their device.
		if ( current_user_can( 'edit_users' ) && horsetools_2fa_enabled( $user->ID ) ) {
			echo '<h2>' . esc_html__( 'Two-factor authentication', 'horse-tools' ) . '</h2>';
			echo '<table class="form-table" role="presentation"><tr><th>' . esc_html__( 'Status', 'horse-tools' ) . '</th><td>';
			wp_nonce_field( 'horsetools_2fa_admin_' . $user->ID, 'horsetools_2fa_admin_nonce' );
			echo '<p>' . esc_html__( 'This user has two-factor authentication ON.', 'horse-tools' ) . '</p>';
			echo '<label><input type="checkbox" name="horsetools_2fa_admin_reset" value="1" /> ' . esc_html__( 'Turn it off for this user (e.g. they lost their device)', 'horse-tools' ) . '</label>';
			echo '</td></tr></table>';
		}
		return;
	}
	wp_enqueue_script( 'horsetools-qr', HORSETOOLS_URL . 'link/shortcode/qrcode.min.js', array(), '1.0.0', true );
	$enabled = horsetools_2fa_enabled( $user->ID );
	echo '<h2>' . esc_html__( 'Two-factor authentication', 'horse-tools' ) . '</h2>';
	echo '<table class="form-table" role="presentation"><tr><th>' . esc_html__( 'Status', 'horse-tools' ) . '</th><td>';
	wp_nonce_field( 'horsetools_2fa_' . $user->ID, 'horsetools_2fa_nonce' );

	if ( $enabled ) {
		echo '<p style="color:#1d9e75;font-weight:600">' . esc_html__( 'Two-factor authentication is ON for your account.', 'horse-tools' ) . '</p>';
		echo '<label><input type="checkbox" name="horsetools_2fa_disable" value="1" /> ' . esc_html__( 'Turn it off (uncheck stays on)', 'horse-tools' ) . '</label>';
		// Show freshly generated backup codes once, if any are pending display.
		$fresh = get_transient( 'horsetools_2fa_show_' . $user->ID );
		if ( is_array( $fresh ) ) {
			delete_transient( 'horsetools_2fa_show_' . $user->ID );
			echo '<p class="description" style="margin-top:10px">' . esc_html__( 'Save these one-time backup codes somewhere safe — each works once if you lose your phone:', 'horse-tools' ) . '</p>';
			echo '<pre style="background:#f6f7f7;padding:10px;border:1px solid #dcdcde;display:inline-block">' . esc_html( implode( "\n", $fresh ) ) . '</pre>';
		}
	} else {
		// Create (or reuse) a pending secret for this enrolment attempt.
		$secret = (string) get_user_meta( $user->ID, '_horsetools_2fa_pending', true );
		if ( '' === $secret ) {
			$secret = horsetools_2fa_new_secret();
			update_user_meta( $user->ID, '_horsetools_2fa_pending', $secret );
		}
		$issuer = rawurlencode( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
		$label  = rawurlencode( $user->user_login . '@' . wp_parse_url( home_url(), PHP_URL_HOST ) );
		$otpauth = 'otpauth://totp/' . $label . '?secret=' . $secret . '&issuer=' . $issuer;
		echo '<p>' . esc_html__( 'Scan this with Google Authenticator, Authy or a similar app, then enter the 6-digit code it shows to turn 2FA on.', 'horse-tools' ) . '</p>';
		echo '<div id="ht-2fa-qr" data-otp="' . esc_attr( $otpauth ) . '" style="margin:10px 0"></div>';
		echo '<p class="description">' . esc_html__( 'Or enter this key manually:', 'horse-tools' ) . ' <code>' . esc_html( $secret ) . '</code></p>';
		echo '<p><input type="text" name="horsetools_2fa_confirm" inputmode="numeric" autocomplete="off" placeholder="' . esc_attr__( '6-digit code', 'horse-tools' ) . '" class="regular-text" style="max-width:160px" /> ' . esc_html__( '← enter code and press "Update profile" to enable', 'horse-tools' ) . '</p>';
		?>
		<script>document.addEventListener('DOMContentLoaded',function(){var el=document.getElementById('ht-2fa-qr');if(el&&window.QRCode){new QRCode(el,{text:el.getAttribute('data-otp'),width:180,height:180,correctLevel:QRCode.CorrectLevel.M});}});</script>
		<?php
	}
	// Per-user Telegram chat ID, so a Telegram recovery code reaches THIS user's
	// own Telegram (never pooled to the admin). One shared bot, per-user chat.
	if ( ! empty( $horsetools_options['scuri-2fa-tg'] ) ) {
		$tg = (string) get_user_meta( $user->ID, '_horsetools_2fa_tg_chat', true );
		echo '<p style="margin-top:14px"><label>' . esc_html__( 'Your Telegram chat ID (for recovery codes)', 'horse-tools' ) . '<br/>';
		echo '<input type="text" name="horsetools_2fa_tg_chat" value="' . esc_attr( $tg ) . '" class="regular-text" placeholder="123456789" /></label></p>';
		echo '<p class="description">' . esc_html__( 'Message the site’s Telegram bot once, then paste YOUR own chat ID here (get it from @userinfobot) so recovery codes go to your Telegram, not the admin.', 'horse-tools' ) . '</p>';
	}
	echo '</td></tr></table>';
}
add_action( 'personal_options_update', 'horsetools_2fa_profile_save' );
add_action( 'edit_user_profile_update', 'horsetools_2fa_profile_save' );
function horsetools_2fa_profile_save( $user_id ) {
	// An admin turning OFF another user's 2FA (the only cross-account action).
	if ( get_current_user_id() !== (int) $user_id ) {
		if ( current_user_can( 'edit_users' ) && ! empty( $_POST['horsetools_2fa_admin_reset'] )
			&& isset( $_POST['horsetools_2fa_admin_nonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['horsetools_2fa_admin_nonce'] ) ), 'horsetools_2fa_admin_' . $user_id ) ) {
			delete_user_meta( $user_id, '_horsetools_2fa_enabled' );
			delete_user_meta( $user_id, '_horsetools_2fa_secret' );
			delete_user_meta( $user_id, '_horsetools_2fa_backup' );
		}
		return;
	}
	if ( ! isset( $_POST['horsetools_2fa_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['horsetools_2fa_nonce'] ) ), 'horsetools_2fa_' . $user_id ) ) {
		return;
	}
	// Save the user's own Telegram chat ID (numeric, may be negative for groups).
	if ( isset( $_POST['horsetools_2fa_tg_chat'] ) ) {
		$tg = preg_replace( '/[^0-9-]/', '', sanitize_text_field( wp_unslash( $_POST['horsetools_2fa_tg_chat'] ) ) );
		if ( '' === $tg ) { delete_user_meta( $user_id, '_horsetools_2fa_tg_chat' ); }
		else { update_user_meta( $user_id, '_horsetools_2fa_tg_chat', $tg ); }
	}
	if ( horsetools_2fa_enabled( $user_id ) ) {
		if ( ! empty( $_POST['horsetools_2fa_disable'] ) ) {
			delete_user_meta( $user_id, '_horsetools_2fa_enabled' );
			delete_user_meta( $user_id, '_horsetools_2fa_secret' );
			delete_user_meta( $user_id, '_horsetools_2fa_backup' );
		}
		return;
	}
	// Enrolment: the confirmation code must match the pending secret.
	$confirm = isset( $_POST['horsetools_2fa_confirm'] ) ? sanitize_text_field( wp_unslash( $_POST['horsetools_2fa_confirm'] ) ) : '';
	if ( '' === $confirm ) { return; }
	$pending = (string) get_user_meta( $user_id, '_horsetools_2fa_pending', true );
	if ( '' !== $pending && horsetools_2fa_verify( $pending, $confirm ) ) {
		update_user_meta( $user_id, '_horsetools_2fa_secret', $pending );
		update_user_meta( $user_id, '_horsetools_2fa_enabled', 1 );
		delete_user_meta( $user_id, '_horsetools_2fa_pending' );
		$codes = horsetools_2fa_make_backup();
		horsetools_2fa_store_backup( $user_id, $codes );
		set_transient( 'horsetools_2fa_show_' . $user_id, $codes, 120 ); // shown once on next load
	}
}

/* ---------------------------------------------------------------------------
 * The second login step
 * ------------------------------------------------------------------------- */
// Password-based XML-RPC authenticates through wp_authenticate() and never fires
// wp_login, so it would slip past the second factor entirely. Refuse it for any
// account that has 2FA on (use an application password for automation instead).
add_filter( 'authenticate', 'horsetools_2fa_block_xmlrpc', 99, 1 );
function horsetools_2fa_block_xmlrpc( $user ) {
	if ( ! ( $user instanceof WP_User ) ) { return $user; }
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST && horsetools_2fa_enabled( $user->ID ) ) {
		return new WP_Error( 'horsetools_2fa_xmlrpc', esc_html__( 'This account uses two-factor authentication; XML-RPC password login is disabled.', 'horse-tools' ) );
	}
	return $user;
}
add_action( 'wp_login', 'horsetools_2fa_after_login', 10, 2 );
function horsetools_2fa_after_login( $user_login, $user ) {
	if ( ! ( $user instanceof WP_User ) || ! horsetools_2fa_enabled( $user->ID ) ) { return; }
	if ( horsetools_2fa_device_trusted( $user->ID ) ) { return; }
	// First factor is done and the cookie is set — undo it and demand the code.
	wp_clear_auth_cookie();
	$redirect = isset( $_REQUEST['redirect_to'] ) ? wp_unslash( $_REQUEST['redirect_to'] ) : admin_url();
	$remember = ! empty( $_POST['rememberme'] );
	horsetools_2fa_prompt( $user, $redirect, $remember );
	exit;
}
function horsetools_2fa_prompt( $user, $redirect, $remember, $error = '' ) {
	$token = horsetools_2fa_pending_token( $user->ID );
	$err   = $error ? new WP_Error( 'horsetools_2fa', $error ) : null;
	login_header( __( 'Two-factor authentication', 'horse-tools' ), '', $err );
	?>
	<form name="ht2fa" method="post" action="<?php echo esc_url( site_url( 'wp-login.php?action=horsetools_2fa', 'login_post' ) ); ?>">
		<p>
			<label for="ht2fa_code"><?php esc_html_e( 'Authentication code', 'horse-tools' ); ?></label>
			<input type="text" name="horsetools_2fa_code" id="ht2fa_code" class="input" inputmode="numeric" autocomplete="one-time-code" value="" size="20" autofocus />
		</p>
		<p class="description"><?php esc_html_e( 'Enter the 6-digit code from your app, or a backup code.', 'horse-tools' ); ?></p>
		<?php if ( horsetools_2fa_recovery_allowed( 'email' ) || horsetools_2fa_tg_available( $user->ID ) ) : ?>
		<p style="font-size:13px">
			<?php esc_html_e( 'Lost your device?', 'horse-tools' ); ?>
			<?php if ( horsetools_2fa_recovery_allowed( 'email' ) ) : ?>
				<button type="submit" formaction="<?php echo esc_url( site_url( 'wp-login.php?action=horsetools_2fa_email', 'login_post' ) ); ?>" class="button-link"><?php esc_html_e( 'Email me a code', 'horse-tools' ); ?></button>
			<?php endif; ?>
			<?php if ( horsetools_2fa_tg_available( $user->ID ) ) : ?>
				&middot; <button type="submit" formaction="<?php echo esc_url( site_url( 'wp-login.php?action=horsetools_2fa_tg', 'login_post' ) ); ?>" class="button-link"><?php esc_html_e( 'Send a Telegram code', 'horse-tools' ); ?></button>
			<?php endif; ?>
		</p>
		<?php endif; ?>
		<p><label><input type="checkbox" name="horsetools_2fa_trust" value="1" /> <?php esc_html_e( 'Trust this device for 30 days', 'horse-tools' ); ?></label></p>
		<input type="hidden" name="horsetools_2fa_pending" value="<?php echo esc_attr( $token ); ?>" />
		<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect ); ?>" />
		<input type="hidden" name="rememberme" value="<?php echo $remember ? '1' : ''; ?>" />
		<p class="submit"><input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Verify', 'horse-tools' ); ?>" /></p>
	</form>
	<?php
	login_footer();
}
// Verify step.
add_action( 'login_form_horsetools_2fa', 'horsetools_2fa_do_verify' );
function horsetools_2fa_do_verify() {
	$uid  = horsetools_2fa_read_pending( isset( $_POST['horsetools_2fa_pending'] ) ? wp_unslash( $_POST['horsetools_2fa_pending'] ) : '' );
	$user = $uid ? get_user_by( 'id', $uid ) : false;
	if ( ! $user ) { wp_safe_redirect( wp_login_url() ); exit; }

	$redirect = isset( $_POST['redirect_to'] ) ? wp_unslash( $_POST['redirect_to'] ) : admin_url();
	$remember = ! empty( $_POST['rememberme'] );
	$code     = isset( $_POST['horsetools_2fa_code'] ) ? trim( (string) wp_unslash( $_POST['horsetools_2fa_code'] ) ) : '';

	// Cap wrong codes per user so the second factor can't be brute-forced by
	// someone who already has the password. After 10 tries, force a fresh login.
	$fkey = 'horsetools_2fa_fail_' . $uid;
	if ( (int) get_transient( $fkey ) >= 10 ) {
		wp_safe_redirect( wp_login_url() );
		exit;
	}

	$ok = horsetools_2fa_verify( horsetools_2fa_secret( $uid ), $code )
		|| horsetools_2fa_use_backup( $uid, $code )
		|| horsetools_2fa_use_recovery( $uid, $code );

	if ( ! $ok ) {
		// Feed the IP attempt-limiter too, so repeated 2FA failures also lock the IP.
		do_action( 'wp_login_failed', $user->user_login );
		set_transient( $fkey, (int) get_transient( $fkey ) + 1, 15 * MINUTE_IN_SECONDS );
		horsetools_2fa_prompt( $user, $redirect, $remember, __( 'Invalid code. Please try again.', 'horse-tools' ) );
		exit;
	}
	delete_transient( $fkey );
	if ( ! empty( $_POST['horsetools_2fa_trust'] ) ) { horsetools_2fa_trust_device( $uid ); }
	wp_set_auth_cookie( $uid, $remember );
	wp_safe_redirect( $redirect ? $redirect : admin_url() );
	exit;
}
// Recovery-send steps (re-show the prompt after sending).
function horsetools_2fa_send_step( $channel ) {
	$uid  = horsetools_2fa_read_pending( isset( $_POST['horsetools_2fa_pending'] ) ? wp_unslash( $_POST['horsetools_2fa_pending'] ) : '' );
	$user = $uid ? get_user_by( 'id', $uid ) : false;
	$allowed = ( 'tg' === $channel ) ? horsetools_2fa_tg_available( $uid ) : horsetools_2fa_recovery_allowed( $channel );
	if ( ! $user || ! $allowed ) { wp_safe_redirect( wp_login_url() ); exit; }
	$sent = horsetools_2fa_send_recovery( $user, $channel );
	$redirect = isset( $_POST['redirect_to'] ) ? wp_unslash( $_POST['redirect_to'] ) : admin_url();
	horsetools_2fa_prompt( $user, $redirect, ! empty( $_POST['rememberme'] ),
		$sent ? __( 'A one-time code has been sent. Enter it above.', 'horse-tools' )
		      : __( 'Could not send the recovery code — check the channel is configured.', 'horse-tools' ) );
	exit;
}
add_action( 'login_form_horsetools_2fa_email', function () { horsetools_2fa_send_step( 'email' ); } );
add_action( 'login_form_horsetools_2fa_tg', function () { horsetools_2fa_send_step( 'tg' ); } );
