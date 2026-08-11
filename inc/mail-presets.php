<?php
/**
 * Horse Tools — the settings an email service needs, so nobody has to know them.
 *
 * The SMTP form inherited from Foxtool asks eight questions: sender name, sender
 * address, account, password, server, port, protocol, authentication. Seven of
 * the eight have exactly one correct answer once you know which service you are
 * using, and the eighth is a password. Asking all eight is asking somebody to
 * know `smtp.gmail.com`, `587` and `STARTTLS` in order to be allowed to send
 * their own email, and that is where most people stop.
 *
 * So the knowledge lives here instead. Pick the service; the constants fill
 * themselves in; what is left is the credential, which nothing can guess.
 *
 * Two things beyond host and port earn their place in the table. Several
 * services have a *fixed* username — Resend wants the literal word "resend",
 * SendGrid wants "apikey" — which people fill with their email address and then
 * cannot work out why authentication fails. And every service has a rule about
 * which address you are allowed to send *as*; getting that wrong is the single
 * most common reason a correctly configured SMTP connection still produces mail
 * nobody receives. Both are recorded, and the second is checked.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * The services, and what each one needs.
 *
 * - host/port/enc: the constants, filled in on choosing.
 * - user: a fixed username when the service has one, '' when it is the address.
 * - from: 'user' when the sender must equal the account, 'verified' when the
 *   service requires the domain or address to be verified on their side, '' when
 *   there is no rule worth stating.
 *
 * @return array<string,array>
 */
function horsetools_mail_presets() {
	return array(
		'gmail'     => array(
			'label' => 'Gmail / Google Workspace',
			'host'  => 'smtp.gmail.com',
			'port'  => '587',
			'enc'   => 'tls',
			'user'  => '',
			'from'  => 'user',
			'secret' => __( 'App password', 'horse-tools' ),
			'where' => 'https://myaccount.google.com/apppasswords',
			'note'  => __( 'Not your normal password — Google requires an app password, and the page that makes them only appears once two-step verification is on.', 'horse-tools' ),
		),
		'brevo'     => array(
			'label' => 'Brevo',
			'host'  => 'smtp-relay.brevo.com',
			'port'  => '587',
			'enc'   => 'tls',
			'user'  => '',
			'from'  => 'verified',
			'secret' => __( 'SMTP key', 'horse-tools' ),
			'where' => 'https://app.brevo.com/settings/keys/smtp',
			'note'  => __( 'Free for 300 messages a day with no card. The account login is the username; the SMTP key is not the same thing as the API key.', 'horse-tools' ),
		),
		'resend'    => array(
			'label' => 'Resend',
			'host'  => 'smtp.resend.com',
			'port'  => '587',
			'enc'   => 'tls',
			'user'  => 'resend',
			'from'  => 'verified',
			'secret' => __( 'API key', 'horse-tools' ),
			'where' => 'https://resend.com/api-keys',
			'note'  => __( 'The username is the literal word “resend”, not your address. You must add and verify your domain before it will send as you.', 'horse-tools' ),
		),
		'sendgrid'  => array(
			'label' => 'SendGrid',
			'host'  => 'smtp.sendgrid.net',
			'port'  => '587',
			'enc'   => 'tls',
			'user'  => 'apikey',
			'from'  => 'verified',
			'secret' => __( 'API key', 'horse-tools' ),
			'where' => 'https://app.sendgrid.com/settings/api_keys',
			'note'  => __( 'The username is the literal word “apikey”, not your address.', 'horse-tools' ),
		),
		'mailgun'   => array(
			'label' => 'Mailgun',
			'host'  => 'smtp.mailgun.org',
			'port'  => '587',
			'enc'   => 'tls',
			'user'  => '',
			'from'  => 'verified',
			'secret' => __( 'SMTP password', 'horse-tools' ),
			'where' => 'https://app.mailgun.com/mg/sending/domains',
			'note'  => __( 'The username is the postmaster address Mailgun shows on your sending domain, not your login.', 'horse-tools' ),
		),
		'zoho'      => array(
			'label' => 'Zoho Mail',
			'host'  => 'smtp.zoho.com',
			'port'  => '587',
			'enc'   => 'tls',
			'user'  => '',
			'from'  => 'user',
			'secret' => __( 'App password', 'horse-tools' ),
			'where' => 'https://accounts.zoho.com/home#security/app_password',
			'note'  => '',
		),
		'microsoft' => array(
			'label' => 'Microsoft 365 / Outlook',
			'host'  => 'smtp.office365.com',
			'port'  => '587',
			'enc'   => 'tls',
			'user'  => '',
			'from'  => 'user',
			'secret' => __( 'Password or app password', 'horse-tools' ),
			'where' => '',
			'note'  => __( 'Microsoft has been switching this off for new tenants; if it refuses to authenticate, that is why, and another service is the quicker road.', 'horse-tools' ),
		),
		'yandex'    => array(
			'label' => 'Yandex Mail',
			'host'  => 'smtp.yandex.com',
			'port'  => '465',
			'enc'   => 'ssl',
			'user'  => '',
			'from'  => 'user',
			'secret' => __( 'App password', 'horse-tools' ),
			'where' => '',
			'note'  => '',
		),
	);
}

/**
 * @param string $key
 * @return array|null
 */
function horsetools_mail_preset( $key ) {
	$all = horsetools_mail_presets();
	return isset( $all[ $key ] ) ? $all[ $key ] : null;
}

/** Which service the owner picked, if any. */
function horsetools_mail_preset_key() {
	global $horsetools_options;
	$key = isset( $horsetools_options['mail-preset'] ) ? (string) $horsetools_options['mail-preset'] : '';
	return ( null !== horsetools_mail_preset( $key ) ) ? $key : '';
}

/**
 * Which service the settings actually look like, whether or not one was picked.
 *
 * A site that filled the eight fields in by hand years ago should not be told to
 * start again — if the host matches a known service, the help for that service
 * applies to them too.
 *
 * @return string
 */
function horsetools_mail_preset_detect() {
	global $horsetools_options;
	$host = isset( $horsetools_options['mail-gsmtp15'] ) ? strtolower( trim( (string) $horsetools_options['mail-gsmtp15'] ) ) : '';
	if ( '' === $host ) {
		return '';
	}
	foreach ( horsetools_mail_presets() as $key => $row ) {
		if ( $host === $row['host'] ) {
			return $key;
		}
	}
	return '';
}

/**
 * The service the domain already receives mail through, as something to pick.
 *
 * The diagnosis panel reads the MX records and names the provider. Naming it is
 * only half an answer: the owner is standing in front of a list and wants to know
 * which line of it is theirs. The MX keys and the preset keys are not the same
 * words, and two of the providers that turn up in MX records — Lark, cPanel
 * hosting — have no entry here at all, so this says nothing rather than pointing
 * at the nearest-looking one.
 *
 * @param string $mx_key Key from horsetools_mail_guess_provider().
 * @return string Preset key, or '' when there is nothing honest to suggest.
 */
function horsetools_mail_preset_for_mx( $mx_key ) {
	$map = array(
		'google'    => 'gmail',
		'zoho'      => 'zoho',
		'microsoft' => 'microsoft',
		'yandex'    => 'yandex',
	);
	return isset( $map[ $mx_key ] ) ? $map[ $mx_key ] : '';
}

/**
 * The mistake that produces a working connection and undelivered mail.
 *
 * Authenticating as one address and sending as another is the commonest SMTP
 * misconfiguration there is, and the symptom is not an error — the service
 * accepts the login, then rejects or silently drops the message, or the receiver
 * does. Gmail is strictest: the sender has to be the account or one of its
 * verified aliases.
 *
 * Only stated where the service actually has the rule, and only when both values
 * are present. Guessing at somebody's alias list is not this function's job.
 *
 * @return string '' when there is nothing to say.
 */
function horsetools_mail_from_warning() {
	global $horsetools_options;
	$key = horsetools_mail_preset_key();
	if ( '' === $key ) {
		$key = horsetools_mail_preset_detect();
	}
	$preset = horsetools_mail_preset( $key );
	if ( ! $preset || 'user' !== $preset['from'] ) {
		return '';
	}

	$user = isset( $horsetools_options['mail-gsmtp13'] ) ? strtolower( trim( (string) $horsetools_options['mail-gsmtp13'] ) ) : '';
	$from = isset( $horsetools_options['mail-gsmtp12'] ) ? strtolower( trim( (string) $horsetools_options['mail-gsmtp12'] ) ) : '';
	if ( '' === $user || '' === $from || $user === $from ) {
		return '';
	}

	return sprintf(
		/* translators: 1: service name, 2: the sender address, 3: the account address. */
		__( '%1$s will only send as the account you sign in with. You are signing in as %3$s but sending as %2$s, which it will refuse or quietly drop — and refusing looks from here exactly like the message vanishing.', 'horse-tools' ),
		$preset['label'],
		$from,
		$user
	);
}
