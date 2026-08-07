<?php
/**
 * Horse Tools — one way out of the site.
 *
 * Every watcher in this plugin so far speaks only to the dashboard, which means
 * it only reaches somebody who is already looking. The whole class of
 * problem these watchers exist for — a link added to an old post, a hotline
 * swapped, the plugin switched off — is invisible precisely because nobody was
 * looking, sometimes for years.
 *
 * So there has to be a way to reach the owner where they are. Telegram if the
 * site has a bot, email otherwise. Email is the floor and not a good one: a
 * security message from a shop's own server routinely lands in spam, which is
 * exactly why the state of the channel is reported on screen and why there is a
 * test button. A channel nobody has ever seen work is not a channel.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * The Telegram bot token, if the site has one.
 *
 * Shared with the WooCommerce order notifier — it is the same bot, and asking
 * for the token twice would mean two places to get it wrong.
 *
 * @return string
 */
function horsetools_alert_token() {
	global $horsetools_options;
	return isset( $horsetools_options['woo-tele11'] ) ? trim( (string) $horsetools_options['woo-tele11'] ) : '';
}

/**
 * Where security messages go.
 *
 * A site may well want these somewhere other than the order notifications — the
 * orders go to whoever packs them, and this does not. So there is a field of its
 * own, and the order chat is only the fallback.
 *
 * @return string
 */
function horsetools_alert_chat() {
	global $horsetools_options;
	$own = isset( $horsetools_options['watch-tg'] ) ? trim( (string) $horsetools_options['watch-tg'] ) : '';
	if ( '' !== $own ) {
		return $own;
	}
	return isset( $horsetools_options['woo-tele12'] ) ? trim( (string) $horsetools_options['woo-tele12'] ) : '';
}

/**
 * @return string 'telegram' | 'email'
 */
function horsetools_alert_channel() {
	return ( '' !== horsetools_alert_token() && '' !== horsetools_alert_chat() ) ? 'telegram' : 'email';
}

/**
 * Where a message would land, in words, for the setup screen.
 *
 * @return string
 */
function horsetools_alert_target() {
	if ( 'telegram' === horsetools_alert_channel() ) {
		/* translators: %s: Telegram chat ID. */
		return sprintf( __( 'Telegram (chat %s)', 'horse-tools' ), horsetools_alert_chat() );
	}
	/* translators: %s: email address. */
	return sprintf( __( 'Email to %s', 'horse-tools' ), get_option( 'admin_email' ) );
}

/**
 * Send one message out of the site.
 *
 * Blocking on purpose, unlike the order notifier. That one runs during a
 * shopper's checkout and must never make them wait; this one runs from cron or
 * an admin page and its whole value is knowing whether it worked. A fire-and-
 * forget security alert is a security alert that can fail every time for a year
 * without anyone finding out.
 *
 * @param string $text    Plain text. Telegram gets it as-is; email gets it as the body.
 * @param string $subject Email subject. Ignored by Telegram.
 * @return true|WP_Error
 */
function horsetools_alert_send( $text, $subject = '' ) {
	$text = (string) $text;
	if ( '' === trim( $text ) ) {
		return new WP_Error( 'horsetools_alert_empty', __( 'Nothing to send.', 'horse-tools' ) );
	}

	if ( 'telegram' === horsetools_alert_channel() ) {
		$r = wp_safe_remote_post(
			'https://api.telegram.org/bot' . rawurlencode( horsetools_alert_token() ) . '/sendMessage',
			array(
				'timeout' => 15,
				'body'    => array(
					'chat_id'                  => horsetools_alert_chat(),
					'text'                     => $text,
					'disable_web_page_preview' => true,
				),
			)
		);
		if ( is_wp_error( $r ) ) {
			return $r;
		}
		$code = (int) wp_remote_retrieve_response_code( $r );
		$body = json_decode( (string) wp_remote_retrieve_body( $r ), true );
		if ( 200 === $code && ! empty( $body['ok'] ) ) {
			return true;
		}
		// Telegram says why in plain language — "chat not found", "bot was
		// blocked by the user". Passing it through is the difference between a
		// setup screen that helps and one that says "failed".
		$why = isset( $body['description'] ) ? (string) $body['description'] : ( 'HTTP ' . $code );
		return new WP_Error( 'horsetools_alert_telegram', $why );
	}

	$to = get_option( 'admin_email' );
	if ( ! is_email( $to ) ) {
		return new WP_Error( 'horsetools_alert_noaddr', __( 'This site has no valid admin email address.', 'horse-tools' ) );
	}
	if ( '' === $subject ) {
		/* translators: %s: site name. */
		$subject = sprintf( __( 'Horse Tools — %s', 'horse-tools' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	}
	$sent = wp_mail( $to, $subject, $text );
	if ( ! $sent ) {
		return new WP_Error( 'horsetools_alert_mail', __( 'WordPress could not hand the message to the mail server.', 'horse-tools' ) );
	}
	// wp_mail() returning true means the message was accepted for delivery, not
	// that it arrived, and mail from a shop's own server about security is
	// exactly the mail that gets filtered. Said plainly on the setup screen.
	return true;
}
