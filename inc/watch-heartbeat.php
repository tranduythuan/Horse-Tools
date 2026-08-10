<?php
/**
 * Horse Tools — a numbered heartbeat, so that silence becomes a signal.
 *
 * Everything else here reports trouble. None of it can report the one failure
 * that matters most: itself stopping. Switch the plugin off, delete the options,
 * let the site die, and every watcher goes quiet — and quiet is exactly what a
 * site with nothing wrong also looks like. Two years of casino links looked like
 * quiet too.
 *
 * The fix is not another alarm. It is a message that arrives when nothing is
 * wrong, on a schedule, carrying a number. Then three different failures each
 * leave their own mark:
 *
 *   - Numbers skip (…#47, #50…)  → beats were sent and did not reach you. The
 *     channel is broken: a blocked bot, a spam filter.
 *   - A number arrives late      → the beat was not attempted on time. The site
 *     was idle, cron is dead, or the site was down.
 *   - Nothing arrives at all     → the worst case, and the only one you can
 *     notice from outside the site. Which is why the message always says when
 *     the next one is due: an expectation is what makes an absence visible.
 *
 * The counter therefore advances on every *attempt*, not on every success. If it
 * only advanced on success there would be no gap to see, and a channel that has
 * been failing for a month would look identical to a month with no beats due.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

const HORSETOOLS_HB = 'horsetools_heartbeat';

/** @return bool */
function horsetools_hb_enabled() {
	global $horsetools_options;
	return ! empty( $horsetools_options['watch-hb'] );
}

/**
 * How long between beats, in seconds.
 *
 * Weekly by default. Daily turns the one message that is supposed to mean
 * something into background noise within a fortnight; monthly means a channel
 * can be broken for four weeks before anybody wonders.
 *
 * @return int
 */
function horsetools_hb_interval() {
	global $horsetools_options;
	$days = isset( $horsetools_options['watch-hb-days'] ) ? (int) $horsetools_options['watch-hb-days'] : 7;
	if ( ! in_array( $days, array( 1, 7, 14, 30 ), true ) ) {
		$days = 7;
	}
	return $days * DAY_IN_SECONDS;
}

/**
 * @return array{seq:int,sent:int,due:int,ok:bool,error:string,ever:bool}
 *         sent: unix time of the last attempt. due: when the next one is owed.
 */
function horsetools_hb_state() {
	$s = get_option( HORSETOOLS_HB, array() );
	$s = is_array( $s ) ? $s : array();
	$sent = isset( $s['sent'] ) ? (int) $s['sent'] : 0;
	return array(
		'seq'   => isset( $s['seq'] ) ? (int) $s['seq'] : 0,
		'sent'  => $sent,
		'due'   => $sent ? $sent + horsetools_hb_interval() : 0,
		'ok'    => ! empty( $s['ok'] ),
		'error' => isset( $s['error'] ) ? (string) $s['error'] : '',
		'ever'  => $sent > 0,
	);
}

/** @return bool */
function horsetools_hb_due() {
	if ( ! horsetools_hb_enabled() ) {
		return false;
	}
	$s = horsetools_hb_state();
	// Never sent one: the first beat goes out as soon as the feature is on, so
	// the owner sees within minutes whether the channel works at all rather than
	// finding out in a week that it never did.
	return ! $s['ever'] || time() >= $s['due'];
}

/**
 * The message.
 *
 * It carries the state, not just "alive". A heartbeat that says nothing but
 * "still here" is one the owner stops opening, and then the number in it — the
 * only part that makes silence readable — stops being read too.
 *
 * @param int $seq
 * @return string
 */
function horsetools_hb_compose( $seq ) {
	$lines = array();

	$lines[] = sprintf(
		/* translators: 1: sequence number, 2: site name. */
		__( 'Horse Tools beat #%1$d — %2$s', 'horse-tools' ),
		$seq,
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
	);
	$lines[] = home_url( '/' );
	$lines[] = '';

	$say = function ( $label, $good, $detail = '' ) use ( &$lines ) {
		$lines[] = ( $good ? '[OK] ' : '[!] ' ) . $label . ( '' !== $detail ? ' — ' . $detail : '' );
	};

	if ( function_exists( 'horsetools_contact_status' ) ) {
		$c = horsetools_contact_status();
		$say(
			__( 'Contact details in your settings', 'horse-tools' ),
			'clean' === $c['state'],
			'clean' === $c['state'] ? '' : ( 'unset' === $c['state']
				? __( 'not confirmed yet', 'horse-tools' )
				: __( 'CHANGED', 'horse-tools' ) )
		);
	}
	if ( function_exists( 'horsetools_contact_content_status' ) ) {
		$cc = horsetools_contact_content_status();
		$say(
			__( 'Contact details in your posts', 'horse-tools' ),
			'clean' === $cc['state'],
			'clean' === $cc['state'] ? '' : ( 'scanning' === $cc['state']
				? __( 'still reading', 'horse-tools' )
				: ( 'unset' === $cc['state']
					? __( 'not confirmed yet', 'horse-tools' )
					/* translators: %d: how many new contact details appeared. */
					: sprintf( __( '%d NEW', 'horse-tools' ), count( $cc['added'] ) ) ) )
		);
	}
	if ( function_exists( 'horsetools_link_status' ) ) {
		$l = horsetools_link_status();
		$say(
			__( 'Where your content links to', 'horse-tools' ),
			'clean' === $l['state'],
			'clean' === $l['state']
				/* translators: %d: number of approved domains. */
				? sprintf( __( '%d domains, all approved', 'horse-tools' ), $l['total'] )
				: ( 'scanning' === $l['state']
					? __( 'still reading', 'horse-tools' )
					: ( 'unset' === $l['state']
						? __( 'not reviewed yet', 'horse-tools' )
						/* translators: %s: the domains that are not approved. */
						: sprintf( __( 'NOT APPROVED: %s', 'horse-tools' ), implode( ', ', array_slice( array_keys( $l['new'] ), 0, 5 ) ) ) ) )
		);
	}
	if ( function_exists( 'horsetools_exposure_status' ) ) {
		$e = horsetools_exposure_status();
		$n = count( $e['found'] );
		$say(
			__( 'Downloadable secrets in the site folder', 'horse-tools' ),
			0 === $n,
			/* translators: %d: number of exposed files. */
			$n ? sprintf( __( '%d FOUND', 'horse-tools' ), $n ) : __( 'none', 'horse-tools' )
		);
	}

	if ( function_exists( 'horsetools_anchor_status' ) ) {
		$a = horsetools_anchor_status();
		if ( 'ok' !== $a['state'] ) {
			$say(
				__( 'Your approvals match their copy on disk', 'horse-tools' ),
				false,
				'mismatch' === $a['state']
					? __( 'THEY DO NOT — something changed them without going through the screens', 'horse-tools' )
					: __( 'no copy on disk yet', 'horse-tools' )
			);
		}
	}

	$lines[] = '';
	$lines[] = sprintf(
		/* translators: %s: date the next message is due. */
		__( 'Next beat due: %s', 'horse-tools' ),
		date_i18n( get_option( 'date_format' ), time() + horsetools_hb_interval() )
	);
	$lines[] = __( 'If it does not arrive, something stopped it — check the site.', 'horse-tools' );

	return implode( "\n", $lines );
}

/**
 * Send one beat.
 *
 * The counter and the timestamp are written *before* the send and kept whatever
 * the result. Writing them only on success would mean a channel that fails every
 * time is also a channel that is due every time, so the site would retry on
 * every admin page load for ever — and the gap in numbering that tells the owner
 * their channel is broken would never appear.
 *
 * @param bool $force Ignore the schedule (the test button).
 * @return true|WP_Error
 */
function horsetools_hb_fire( $force = false ) {
	if ( ! $force && ! horsetools_hb_due() ) {
		return new WP_Error( 'horsetools_hb_early', __( 'Not due yet.', 'horse-tools' ) );
	}

	$state = horsetools_hb_state();
	$seq   = $state['seq'] + 1;

	$result = horsetools_alert_send(
		horsetools_hb_compose( $seq ),
		/* translators: 1: sequence number, 2: site name. */
		sprintf( __( 'Horse Tools beat #%1$d — %2$s', 'horse-tools' ), $seq, wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) )
	);

	update_option(
		HORSETOOLS_HB,
		array(
			'seq'   => $seq,
			'sent'  => time(),
			'ok'    => true === $result,
			'error' => is_wp_error( $result ) ? $result->get_error_message() : '',
		),
		false
	);

	return $result;
}

/**
 * Fire when due.
 *
 * Both from cron and from an admin page load, because neither alone is enough:
 * WP-Cron does not run on a site with no visitors, and an admin hook does not
 * run on a site whose owner does not log in. Either one is allowed to be the one
 * that fires; the stored timestamp is what stops them doing it twice.
 */
function horsetools_hb_tick( $grace = 0 ) {
	if ( ! horsetools_hb_due() ) {
		return;
	}
	// Let cron go first.
	//
	// Sending is blocking on purpose — the value of this message is knowing
	// whether it went — but that means whoever triggers it waits, and an admin
	// page load waiting fifteen seconds on a mail server is a bad way to learn
	// that the plugin cares about you. Cron requests are nobody's page load, so
	// they fire the moment a beat is due and the admin hook only steps in once
	// the beat is properly late — which is also the only case where cron has
	// evidently stopped running.
	$s = horsetools_hb_state();
	if ( $grace > 0 && $s['ever'] && time() < $s['due'] + $grace ) {
		return;
	}
	// A short lock, because two page loads a second apart would otherwise both
	// see the same "due" and send two beats with two different numbers.
	if ( get_transient( 'horsetools_hb_lock' ) ) {
		return;
	}
	set_transient( 'horsetools_hb_lock', 1, 5 * MINUTE_IN_SECONDS );
	horsetools_hb_fire();
}
add_action( 'admin_init', 'horsetools_hb_tick_admin', 30 );
function horsetools_hb_tick_admin() { horsetools_hb_tick( DAY_IN_SECONDS ); }

/**
 * The cron entry point.
 *
 * WP-Cron is not an admin request, so the watchers this message reports on are
 * not loaded in it — they are behind `is_admin()`, which is right, since a page
 * view has no use for them. Without this the beat would still go out on
 * schedule and would say nothing at all about the site: every block in it is
 * guarded by function_exists(), so it would degrade to "still here" and quietly
 * become the thing nobody opens.
 */
function horsetools_hb_cron() {
	if ( ! function_exists( 'horsetools_contact_status' ) ) {
		foreach ( array( 'anchor', 'watch-scan', 'watch-contact', 'watch-links', 'watch-exposure' ) as $file ) {
			include_once HORSETOOLS_DIR . 'inc/' . $file . '.php';
		}
	}
	horsetools_hb_tick();
}
add_action( 'horsetools_heartbeat_cron', 'horsetools_hb_cron' );

/**
 * Keep the daily cron registered.
 *
 * Daily, not weekly: the job only asks "is a beat owed", so a run that WP-Cron
 * misses costs at most a day of lateness instead of skipping a whole beat.
 */
function horsetools_hb_schedule() {
	if ( ! wp_next_scheduled( 'horsetools_heartbeat_cron' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'horsetools_heartbeat_cron' );
	}
}
add_action( 'admin_init', 'horsetools_hb_schedule' );

/* -------------------------------------------------------------------------
 * What is NOT protected
 * ---------------------------------------------------------------------- */

/**
 * The honest list of gaps, in plain language.
 *
 * A green tick that means "this feature is switched on" is not the same as
 * "you would find out". The difference is the whole subject of this file, and
 * saying it out loud is worth more than another passing check.
 *
 * @return array<int,array{key:string,text:string,fix:string}>
 */
function horsetools_watch_gaps() {
	$gaps = array();

	if ( ! horsetools_hb_enabled() ) {
		$gaps[] = array(
			'key'  => 'hb-off',
			'text' => __( 'If this site stops working, or Horse Tools is switched off, nobody is told. Every warning here is only seen by someone who logs in and looks.', 'horse-tools' ),
			'fix'  => __( 'Turn on the regular check-in message', 'horse-tools' ),
		);
	} else {
		$s = horsetools_hb_state();
		if ( ! $s['ever'] ) {
			$gaps[] = array(
				'key'  => 'hb-never',
				'text' => __( 'The check-in message is on but has never been sent. Until one arrives you do not know the channel works.', 'horse-tools' ),
				'fix'  => __( 'Send a test message', 'horse-tools' ),
			);
		} elseif ( ! $s['ok'] ) {
			$gaps[] = array(
				'key'  => 'hb-fail',
				/* translators: %s: the error the channel reported. */
				'text' => sprintf( __( 'The last check-in message could not be sent: %s', 'horse-tools' ), $s['error'] ),
				'fix'  => __( 'Fix the channel and send a test message', 'horse-tools' ),
			);
		} elseif ( $s['due'] > 0 && time() > $s['due'] + 2 * DAY_IN_SECONDS ) {
			$gaps[] = array(
				'key'  => 'hb-late',
				/* translators: %s: human-readable time, e.g. "3 weeks". */
				'text' => sprintf( __( 'The last check-in message went out %s ago, which is later than it should be. Nothing has been running the schedule.', 'horse-tools' ), human_time_diff( $s['sent'] ) ),
				'fix'  => __( 'Check that WP-Cron is running on this site', 'horse-tools' ),
			);
		}
	}

	if ( function_exists( 'horsetools_scan_finished' ) && ! horsetools_scan_finished() ) {
		$p = horsetools_scan_progress();
		$gaps[] = array(
			'key'  => 'scan',
			'text' => $p['total']
				/* translators: 1: posts read, 2: posts in total. */
				? sprintf( __( 'Your content has not been read all the way through yet (%1$d of %2$d), so nothing found in it can be trusted as complete.', 'horse-tools' ), $p['read'], $p['total'] )
				: __( 'Your content has not been read all the way through yet.', 'horse-tools' ),
			'fix'  => __( 'It continues on its own each time you open an admin page', 'horse-tools' ),
		);
	}

	return $gaps;
}

/* -------------------------------------------------------------------------
 * Test button
 * ---------------------------------------------------------------------- */

add_action( 'wp_ajax_horsetools_hb_test', 'horsetools_hb_test_ajax' );
function horsetools_hb_test_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'horse-tools' ) ) );
	}
	check_ajax_referer( 'horsetools_hb', 'nonce' );

	$result = horsetools_hb_fire( true );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}
	wp_send_json_success(
		array(
			/* translators: %s: where the message was sent, e.g. "Telegram (chat 123)". */
			'message' => sprintf( __( 'Sent. Check %s — if nothing arrives, the channel is not working even though the site thinks it is.', 'horse-tools' ), horsetools_alert_target() ),
		)
	);
}
