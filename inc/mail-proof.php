<?php
/**
 * Horse Tools — know that mail arrives, instead of assuming it.
 *
 * `wp_mail()` returns true when the message was accepted for delivery. Every
 * "test email sent successfully" in every WordPress plugin means exactly that
 * and no more, and it is the reason so many sites are quietly not sending: the
 * screen said it worked, so nobody looked in the inbox.
 *
 * There is no way for PHP to learn whether a message arrived. What it can do is
 * stop pretending, and ask the one participant who *can* see: send the test,
 * then ask the owner what actually turned up. Three answers, one click, and the
 * answer is recorded with the date so the screen afterwards reports something
 * that was observed rather than something that was configured.
 *
 * The record is tied to the settings that produced it. Change the sender, the
 * password or the service and the proof stops counting, because it is no longer
 * about the thing that is running.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

const HORSETOOLS_MAIL_PROOF = 'horsetools_mail_proof';

/** How long a confirmed delivery keeps counting. Ninety days. */
const HORSETOOLS_MAIL_PROOF_TTL = 90 * DAY_IN_SECONDS;

/**
 * A fingerprint of everything that decides how mail leaves this site.
 *
 * Not the password itself — its presence and length are enough to notice a
 * change, and the value has no business being hashed into an option that is
 * read on admin screens.
 *
 * @return string
 */
function horsetools_mail_config_mark() {
	global $horsetools_options;
	$o    = is_array( $horsetools_options ) ? $horsetools_options : array();
	$grab = function ( $key ) use ( $o ) {
		return isset( $o[ $key ] ) ? (string) $o[ $key ] : '';
	};
	$parts = array(
		'on'   => empty( $o['mail-gsmtp1'] ) ? '0' : '1',
		'name' => $grab( 'mail-gsmtp11' ),
		'from' => $grab( 'mail-gsmtp12' ),
		'user' => $grab( 'mail-gsmtp13' ),
		'pass' => (string) strlen( $grab( 'mail-gsmtp14' ) ),
		'host' => $grab( 'mail-gsmtp15' ),
		'port' => $grab( 'mail-gsmtp16' ),
		'enc'  => $grab( 'mail-gsmtp17' ),
		'auth' => empty( $o['mail-gsmtp18'] ) ? '0' : '1',
	);
	return substr( hash( 'sha256', wp_json_encode( $parts ) ), 0, 16 );
}

/**
 * @return array{state:string,when:int,to:string,mark:string,stale:bool}
 *         state: 'none' | 'sent' | 'inbox' | 'spam' | 'missing'
 */
function horsetools_mail_proof() {
	$p = get_option( HORSETOOLS_MAIL_PROOF, array() );
	$p = is_array( $p ) ? $p : array();
	$state = isset( $p['state'] ) ? (string) $p['state'] : 'none';
	$mark  = isset( $p['mark'] ) ? (string) $p['mark'] : '';
	return array(
		'state' => in_array( $state, array( 'sent', 'inbox', 'spam', 'missing' ), true ) ? $state : 'none',
		'when'  => isset( $p['when'] ) ? (int) $p['when'] : 0,
		'to'    => isset( $p['to'] ) ? (string) $p['to'] : '',
		'mark'  => $mark,
		// A proof about settings that have since changed is not a proof about
		// what is running now.
		'stale' => ( '' !== $mark && $mark !== horsetools_mail_config_mark() ),
	);
}

/**
 * Send the test and record that it went, but claim nothing about arrival.
 *
 * @param string $to
 * @return true|WP_Error
 */
function horsetools_mail_proof_send( $to ) {
	$to = sanitize_email( $to );
	if ( ! is_email( $to ) ) {
		return new WP_Error( 'horsetools_mail_addr', __( 'That is not an email address.', 'horse-tools' ) );
	}

	$site = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$body = implode(
		"\n",
		array(
			/* translators: %s: site name. */
			sprintf( __( 'This is a test message from %s.', 'horse-tools' ), $site ),
			home_url( '/' ),
			'',
			__( 'If you are reading it, mail from your site reaches this address. Go back to the Email screen and say where it turned up — the inbox or the spam folder — because those are two different answers and only you can see which one it was.', 'horse-tools' ),
			'',
			/* translators: %s: date and time. */
			sprintf( __( 'Sent: %s', 'horse-tools' ), date_i18n( get_option( 'date_format' ) . ' H:i' ) ),
		)
	);

	$sent = wp_mail(
		$to,
		/* translators: %s: site name. */
		sprintf( __( 'Test message from %s', 'horse-tools' ), $site ),
		$body
	);

	if ( ! $sent ) {
		return new WP_Error( 'horsetools_mail_reject', __( 'WordPress could not hand the message to the mail server at all, so it never left the site.', 'horse-tools' ) );
	}

	update_option(
		HORSETOOLS_MAIL_PROOF,
		array(
			'state' => 'sent',
			'when'  => time(),
			'to'    => $to,
			'mark'  => horsetools_mail_config_mark(),
		),
		false
	);
	return true;
}

/**
 * Record what the owner actually found.
 *
 * @param string $state 'inbox' | 'spam' | 'missing'
 * @return bool
 */
function horsetools_mail_proof_answer( $state ) {
	if ( ! in_array( $state, array( 'inbox', 'spam', 'missing' ), true ) ) {
		return false;
	}
	$p = horsetools_mail_proof();
	update_option(
		HORSETOOLS_MAIL_PROOF,
		array(
			'state' => $state,
			'when'  => time(),
			'to'    => $p['to'],
			// Against the configuration as it is now, not as it was when the test
			// was sent: the owner is answering about the mail they just received.
			'mark'  => horsetools_mail_config_mark(),
		),
		false
	);
	return true;
}

/**
 * One line for the health card, describing what is known rather than what is on.
 *
 * @return array{status:string,text:string}
 */
function horsetools_mail_proof_row() {
	$p = horsetools_mail_proof();

	if ( $p['stale'] ) {
		return array( 'status' => 'warn', 'text' => __( 'Your email settings changed after the last test, so what is running now has not been tried', 'horse-tools' ) );
	}
	switch ( $p['state'] ) {
		case 'inbox':
			// Evidence goes off. Deliverability is not a property of the settings,
			// it is a property of a relationship between two mail systems that
			// drifts: an IP picks up a reputation, a provider tightens its rules,
			// a domain loses its records. A green tick resting on a year-old test
			// is the same lie as a green tick resting on a switch.
			if ( $p['when'] > 0 && ( time() - $p['when'] ) > HORSETOOLS_MAIL_PROOF_TTL ) {
				return array(
					'status' => 'warn',
					/* translators: %s: how long ago, e.g. "4 months". */
					'text'   => sprintf( __( 'Last confirmed %s ago — worth testing again', 'horse-tools' ), human_time_diff( $p['when'] ) ),
				);
			}
			return array(
				'status' => 'pass',
				/* translators: %s: how long ago, e.g. "3 days". */
				'text'   => sprintf( __( 'Confirmed %s ago', 'horse-tools' ), human_time_diff( $p['when'] ) ),
			);
		case 'spam':
			return array( 'status' => 'fail', 'text' => __( 'Your last test landed in the spam folder', 'horse-tools' ) );
		case 'missing':
			return array( 'status' => 'fail', 'text' => __( 'Your last test never arrived', 'horse-tools' ) );
		case 'sent':
			return array( 'status' => 'warn', 'text' => __( 'A test was sent but you have not said whether it arrived', 'horse-tools' ) );
	}
	return array( 'status' => 'warn', 'text' => __( 'Nobody has ever checked that mail from this site arrives', 'horse-tools' ) );
}

/* -------------------------------------------------------------------------
 * The two buttons
 * ---------------------------------------------------------------------- */

add_action( 'wp_ajax_horsetools_mail_test', 'horsetools_mail_test_ajax' );
function horsetools_mail_test_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'horse-tools' ) ) );
	}
	check_ajax_referer( 'horsetools_mail', 'nonce' );

	$to = isset( $_POST['to'] ) ? sanitize_email( wp_unslash( $_POST['to'] ) ) : '';
	if ( '' === $to ) {
		$to = (string) get_option( 'admin_email' );
	}
	$r = horsetools_mail_proof_send( $to );
	if ( is_wp_error( $r ) ) {
		wp_send_json_error( array( 'message' => $r->get_error_message() ) );
	}
	wp_send_json_success(
		array(
			/* translators: %s: email address. */
			'message' => sprintf( __( 'Sent to %s. Now go and look — and do not close this page, because the useful part is what you find.', 'horse-tools' ), $to ),
		)
	);
}

add_action( 'wp_ajax_horsetools_mail_answer', 'horsetools_mail_answer_ajax' );
function horsetools_mail_answer_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	check_ajax_referer( 'horsetools_mail', 'nonce' );
	$state = isset( $_POST['state'] ) ? sanitize_key( wp_unslash( $_POST['state'] ) ) : '';
	if ( ! horsetools_mail_proof_answer( $state ) ) {
		wp_send_json_error();
	}
	wp_send_json_success();
}

add_action( 'wp_ajax_horsetools_mail_recheck', 'horsetools_mail_recheck_ajax' );
function horsetools_mail_recheck_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	check_ajax_referer( 'horsetools_mail', 'nonce' );
	horsetools_mail_dns_flush();
	wp_send_json_success();
}
