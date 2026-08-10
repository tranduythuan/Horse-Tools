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
	$orders = isset( $horsetools_options['woo-tele12'] ) ? trim( (string) $horsetools_options['woo-tele12'] ) : '';
	if ( '' !== $orders ) {
		return $orders;
	}
	// The chat this plugin already talks to.
	//
	// Asking for a chat ID was asking a question the plugin had already answered.
	// Two-factor recovery pairs an administrator's Telegram and stores the chat
	// on their account, and a site set up that way has a working bot, a known
	// chat, and a proven route — while this function reported "no Telegram" and
	// sent security mail into a spam folder instead, because it only knew to look
	// at the WooCommerce order chat. Found on a live site whose owner said the bot
	// had been filled in for ages. They were right.
	return horsetools_alert_paired_chat();
}

/**
 * The Telegram chat of the lowest-numbered administrator who has paired one.
 *
 * Lowest-numbered so the answer is the same every time, including from cron
 * where there is no current user. Administrators only: a paired subscriber is
 * not somebody to send the site's security state to.
 *
 * @return string
 */
function horsetools_alert_paired_chat() {
	// No static. It would save four user queries on the one admin page that
	// renders this and a handful on a weekly cron run, and it would freeze the
	// answer for the rest of the request — which has already made two functions
	// in this plugin untestable today, and one of them wrong.
	if ( ! function_exists( 'get_users' ) ) {
		return '';
	}
	$users = get_users(
		array(
			'role'       => 'administrator',
			'orderby'    => 'ID',
			'order'      => 'ASC',
			'number'     => 5,
			'fields'     => 'ID',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'     => '_horsetools_2fa_tg_chat',
					'value'   => '',
					'compare' => '!=',
				),
			),
		)
	);
	foreach ( (array) $users as $id ) {
		$chat = trim( (string) get_user_meta( (int) $id, '_horsetools_2fa_tg_chat', true ) );
		if ( '' !== $chat ) {
			return $chat;
		}
	}
	return '';
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
		$chat = horsetools_alert_chat();
		// Where the chat came from matters as much as what it is. A site that
		// never typed a chat ID anywhere is about to start receiving Telegram
		// messages, and being told why is the difference between a feature and a
		// surprise.
		if ( $chat === horsetools_alert_paired_chat() ) {
			/* translators: %s: Telegram chat ID. */
			return sprintf( __( 'Telegram (chat %s — the one paired for two-factor recovery)', 'horse-tools' ), $chat );
		}
		/* translators: %s: Telegram chat ID. */
		return sprintf( __( 'Telegram (chat %s)', 'horse-tools' ), $chat );
	}
	/* translators: %s: email address. */
	return sprintf( __( 'Email to %s', 'horse-tools' ), get_option( 'admin_email' ) );
}

/**
 * Which channels this site could use, best first.
 *
 * @return string[]
 */
function horsetools_alert_channels() {
	$out = array();
	if ( '' !== horsetools_alert_token() && '' !== horsetools_alert_chat() ) {
		$out[] = 'telegram';
	}
	if ( is_email( get_option( 'admin_email' ) ) ) {
		$out[] = 'email';
	}
	return $out;
}

/**
 * Human name for a channel.
 *
 * @param string $channel
 * @return string
 */
function horsetools_alert_channel_name( $channel ) {
	return ( 'telegram' === $channel ) ? 'Telegram' : __( 'email', 'horse-tools' );
}

/**
 * Send one message, falling back to the other channel if the first will not go.
 *
 * The fallback is deliberately loud. A quiet one is the classic mistake: Telegram
 * breaks, email carries everything without comment, and a year later the owner
 * believes they have two channels when they have had one since March — and finds
 * out only when the second one goes too. So a message that arrives by the second
 * route says, at the top, that it did and why. The failure is the news; the
 * delivery is just how the news got there.
 *
 * @param string $text    Plain text. Telegram gets it as-is; email gets it as the body.
 * @param string $subject Email subject. Ignored by Telegram.
 * @return array{ok:bool,channel:string,fell_back:bool,tried:array<string,string>}
 *               tried: channel => error message, for the ones that did not go.
 */
function horsetools_alert_send( $text, $subject = '' ) {
	$text     = (string) $text;
	$channels = horsetools_alert_channels();
	$tried    = array();

	if ( '' === trim( $text ) ) {
		return array( 'ok' => false, 'channel' => '', 'fell_back' => false, 'tried' => array( '' => __( 'Nothing to send.', 'horse-tools' ) ) );
	}
	if ( ! $channels ) {
		return array( 'ok' => false, 'channel' => '', 'fell_back' => false, 'tried' => array( '' => __( 'This site has no way to reach you: no Telegram bot, and no valid admin email address.', 'horse-tools' ) ) );
	}

	foreach ( $channels as $i => $channel ) {
		$body = $text;
		if ( $i > 0 ) {
			// Name the channel that failed and repeat what it said. "Delivered by
			// the backup" is not the useful part; "your main channel is broken,
			// and here is the reason it gave" is.
			$first = array_key_first( $tried );
			$body  = sprintf(
				/* translators: 1: this channel, 2: the channel that failed, 3: the reason it gave. */
				__( '[!] This went by %1$s because %2$s would not send: %3$s', 'horse-tools' ),
				horsetools_alert_channel_name( $channel ),
				horsetools_alert_channel_name( $first ),
				$tried[ $first ]
			) . "\n\n" . $text;
		}

		$r = horsetools_alert_send_via( $channel, $body, $subject );
		if ( true === $r ) {
			return array( 'ok' => true, 'channel' => $channel, 'fell_back' => ( $i > 0 ), 'tried' => $tried );
		}
		$tried[ $channel ] = is_wp_error( $r ) ? $r->get_error_message() : __( 'unknown error', 'horse-tools' );
	}

	return array( 'ok' => false, 'channel' => '', 'fell_back' => false, 'tried' => $tried );
}

/**
 * One attempt down one channel.
 *
 * Blocking on purpose, unlike the order notifier. That one runs during a
 * shopper's checkout and must never make them wait; this one runs from cron and
 * its whole value is knowing whether it worked. A fire-and-forget security alert
 * is a security alert that can fail every time for a year without anyone finding
 * out.
 *
 * @param string $channel 'telegram' | 'email'
 * @param string $text
 * @param string $subject
 * @return true|WP_Error
 */
function horsetools_alert_send_via( $channel, $text, $subject = '' ) {
	if ( 'telegram' === $channel ) {
		$r = wp_safe_remote_post(
			'https://api.telegram.org/bot' . rawurlencode( horsetools_alert_token() ) . '/sendMessage',
			array(
				'timeout' => 10,
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
