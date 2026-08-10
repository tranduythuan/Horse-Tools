<?php
/**
 * Horse Tools — say whether this site is allowed to send mail as its own domain.
 *
 * WordPress hands a message to the operating system and reports success. That
 * success means the message left the building. It says nothing about whether it
 * arrived, and on a large share of sites it does not: the domain publishes an
 * SPF record naming a mail provider, the web server is not that provider, and
 * every notification the site sends is forged as far as the receiving end is
 * concerned.
 *
 * Nothing in WordPress tells the owner this. They find out when a customer says
 * "I never got the email", months later, if ever.
 *
 * All of it is readable from DNS, which costs nothing: no account, no API key,
 * no external service. The whole of this file is `dns_get_record()` and the
 * patience to evaluate what comes back honestly.
 *
 * The last word matters more than the rest. A checker that guesses "probably
 * broken" is a checker people learn to ignore, so this one distinguishes three
 * answers and never blurs them: yes, no, and *I cannot tell from here* — the
 * third being the correct answer far more often than tools like this admit.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** How long a DNS answer is held. Records change rarely; asking on every load is rude. */
const HORSETOOLS_MAIL_DNS_TTL = 12 * HOUR_IN_SECONDS;

/**
 * The domain this site sends as.
 *
 * @return string
 */
function horsetools_mail_domain() {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	$host = is_string( $host ) ? strtolower( $host ) : '';
	return ( 0 === strpos( $host, 'www.' ) ) ? substr( $host, 4 ) : $host;
}

/**
 * Can this installation ask DNS at all?
 *
 * Shared hosts disable dns_get_record often enough that "no findings" would
 * otherwise read as "nothing wrong".
 *
 * @return bool
 */
function horsetools_mail_dns_available() {
	return function_exists( 'dns_get_record' ) && ! in_array( 'dns_get_record', array_map( 'trim', explode( ',', (string) ini_get( 'disable_functions' ) ) ), true );
}

/**
 * One cached DNS lookup.
 *
 * @param string $name
 * @param int    $type DNS_TXT, DNS_MX, DNS_A…
 * @return array|null Null when the lookup could not be made at all.
 */
function horsetools_mail_dns( $name, $type = DNS_TXT ) {
	if ( ! horsetools_mail_dns_available() ) {
		return null;
	}
	$key = 'ht_dns_' . md5( $name . '|' . $type );
	$hit = get_transient( $key );
	if ( is_array( $hit ) ) {
		return $hit;
	}
	// The @ is not laziness: a domain with no record of this type makes
	// dns_get_record emit a warning on some resolvers and return false on
	// others, and neither is an error worth showing anybody.
	$rows = @dns_get_record( $name, $type ); // phpcs:ignore
	$rows = is_array( $rows ) ? $rows : array();
	set_transient( $key, $rows, HORSETOOLS_MAIL_DNS_TTL );
	return $rows;
}

/** Forget every cached answer — used when the owner asks to check again. */
function horsetools_mail_dns_flush() {
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_ht_dns_%' OR option_name LIKE '_transient_timeout_ht_dns_%'" ); // phpcs:ignore WordPress.DB
	wp_cache_flush();
}

/**
 * The TXT strings for a name, each joined from its chunks.
 *
 * A TXT record longer than 255 characters arrives in pieces, and an SPF record
 * with several includes routinely is. PHP hands the pieces back in `entries`;
 * reading only `txt` silently truncates the record, and a truncated SPF record
 * evaluates to something other than what the domain published.
 *
 * @param string $name
 * @return string[]
 */
function horsetools_mail_txt( $name ) {
	$out = array();
	foreach ( (array) horsetools_mail_dns( $name, DNS_TXT ) as $row ) {
		if ( isset( $row['entries'] ) && is_array( $row['entries'] ) ) {
			$out[] = implode( '', $row['entries'] );
		} elseif ( isset( $row['txt'] ) ) {
			$out[] = (string) $row['txt'];
		}
	}
	return $out;
}

/**
 * @param string $domain
 * @return string The v=spf1 record, or '' if the domain publishes none.
 */
function horsetools_mail_spf( $domain ) {
	foreach ( horsetools_mail_txt( $domain ) as $txt ) {
		if ( 0 === stripos( trim( $txt ), 'v=spf1' ) ) {
			return trim( $txt );
		}
	}
	return '';
}

/**
 * @param string $domain
 * @return string The DMARC record, or ''.
 */
function horsetools_mail_dmarc( $domain ) {
	foreach ( horsetools_mail_txt( '_dmarc.' . $domain ) as $txt ) {
		if ( 0 === stripos( trim( $txt ), 'v=DMARC1' ) ) {
			return trim( $txt );
		}
	}
	return '';
}

/**
 * @param string $domain
 * @return string[] Mail exchangers, lowest preference first.
 */
function horsetools_mail_mx( $domain ) {
	$rows = (array) horsetools_mail_dns( $domain, DNS_MX );
	usort( $rows, function ( $a, $b ) {
		return ( isset( $a['pri'] ) ? $a['pri'] : 0 ) <=> ( isset( $b['pri'] ) ? $b['pri'] : 0 );
	} );
	$out = array();
	foreach ( $rows as $row ) {
		if ( ! empty( $row['target'] ) ) {
			$out[] = strtolower( rtrim( (string) $row['target'], '.' ) );
		}
	}
	return $out;
}

/* -------------------------------------------------------------------------
 * Evaluating SPF against one address
 * ---------------------------------------------------------------------- */

/**
 * Is an IPv4 address inside a CIDR block?
 *
 * @param string $ip
 * @param string $cidr e.g. 1.2.3.0/24, or a bare address.
 * @return bool
 */
function horsetools_mail_in_cidr( $ip, $cidr ) {
	if ( false === strpos( $cidr, '/' ) ) {
		return $ip === $cidr;
	}
	list( $net, $bits ) = explode( '/', $cidr, 2 );
	$bits = (int) $bits;
	$a    = ip2long( $ip );
	$b    = ip2long( $net );
	if ( false === $a || false === $b || $bits < 0 || $bits > 32 ) {
		return false;
	}
	if ( 0 === $bits ) {
		return true;
	}
	$mask = -1 << ( 32 - $bits );
	return ( $a & $mask ) === ( $b & $mask );
}

/**
 * Evaluate a domain's SPF record for one sending address.
 *
 * A deliberately partial implementation of RFC 7208, and honest about it. `ip4`,
 * `ip6`, `a`, `mx`, `include`, `redirect` and `all` are handled; `ptr` and
 * `exists` are not, and meeting one abandons the evaluation rather than guessing
 * past it. The ten-lookup limit is the RFC's and is enforced, because a record
 * that exceeds it is one receivers will reject anyway.
 *
 * @param string $ip     The sending address.
 * @param string $domain
 * @param int    $budget Remaining DNS lookups, by reference.
 * @param int    $depth  Guards against a record that includes itself.
 * @return string 'pass' | 'fail' | 'softfail' | 'neutral' | 'none' | 'unknown'
 */
function horsetools_mail_spf_eval( $ip, $domain, &$budget = 10, $depth = 0 ) {
	if ( $depth > 5 || $budget < 0 ) {
		return 'unknown';
	}
	$record = horsetools_mail_spf( $domain );
	if ( '' === $record ) {
		return 'none';
	}
	$v4 = (bool) filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );

	foreach ( preg_split( '/\s+/', $record ) as $term ) {
		$term = trim( $term );
		if ( '' === $term || 0 === stripos( $term, 'v=spf1' ) ) {
			continue;
		}

		// redirect= is not a mechanism and has no qualifier; it replaces the
		// whole result when nothing above it matched.
		if ( 0 === stripos( $term, 'redirect=' ) ) {
			$budget--;
			return horsetools_mail_spf_eval( $ip, substr( $term, 9 ), $budget, $depth + 1 );
		}
		if ( 0 === stripos( $term, 'exp=' ) ) {
			continue;
		}

		$q = '+';
		if ( in_array( $term[0], array( '+', '-', '~', '?' ), true ) ) {
			$q    = $term[0];
			$term = substr( $term, 1 );
		}
		$result = array( '+' => 'pass', '-' => 'fail', '~' => 'softfail', '?' => 'neutral' );
		$verdict = $result[ $q ];

		$lower = strtolower( $term );

		if ( 'all' === $lower ) {
			return $verdict;
		}
		if ( 0 === strpos( $lower, 'ip4:' ) ) {
			if ( $v4 && horsetools_mail_in_cidr( $ip, substr( $term, 4 ) ) ) {
				return $verdict;
			}
			continue;
		}
		if ( 0 === strpos( $lower, 'ip6:' ) ) {
			// No prefix arithmetic for v6 here; an exact match is still a match,
			// and anything else falls through rather than being called a miss.
			if ( ! $v4 && 0 === strcasecmp( trim( substr( $term, 4 ) ), $ip ) ) {
				return $verdict;
			}
			continue;
		}
		if ( 0 === strpos( $lower, 'include:' ) ) {
			$budget--;
			if ( $budget < 0 ) {
				return 'unknown';
			}
			$inner = horsetools_mail_spf_eval( $ip, substr( $term, 8 ), $budget, $depth + 1 );
			if ( 'pass' === $inner ) {
				return $verdict;
			}
			if ( 'unknown' === $inner ) {
				return 'unknown';
			}
			continue;
		}
		if ( 'a' === $lower || 0 === strpos( $lower, 'a:' ) || 0 === strpos( $lower, 'a/' ) ) {
			$budget--;
			$target = ( 'a' === $lower ) ? $domain : trim( substr( $term, 2 ) );
			$target = strtok( $target, '/' );
			foreach ( (array) horsetools_mail_dns( $target, DNS_A ) as $row ) {
				if ( ! empty( $row['ip'] ) && $row['ip'] === $ip ) {
					return $verdict;
				}
			}
			continue;
		}
		if ( 'mx' === $lower || 0 === strpos( $lower, 'mx:' ) ) {
			$budget--;
			$target = ( 'mx' === $lower ) ? $domain : trim( substr( $term, 3 ) );
			foreach ( horsetools_mail_mx( $target ) as $host ) {
				$budget--;
				foreach ( (array) horsetools_mail_dns( $host, DNS_A ) as $row ) {
					if ( ! empty( $row['ip'] ) && $row['ip'] === $ip ) {
						return $verdict;
					}
				}
			}
			continue;
		}
		if ( 0 === strpos( $lower, 'ptr' ) || 0 === strpos( $lower, 'exists:' ) ) {
			// Not implemented, and guessing past it would mean reporting a fail on
			// a record that in fact authorises this server.
			return 'unknown';
		}
	}

	// A record with no `all` at the end is neutral by default.
	return 'neutral';
}

/**
 * The address this site would be sending from, as best it can be known.
 *
 * SERVER_ADDR is the address the web server answered on. Outbound mail usually
 * leaves by the same address on a small VPS and frequently does not on shared
 * hosting, behind a load balancer, or anywhere with a separate mail relay. So
 * this is a starting point for the check, never the basis for an accusation —
 * see how horsetools_mail_findings() qualifies what it says.
 *
 * @return string '' when it cannot be determined.
 */
function horsetools_mail_server_ip() {
	$ip = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';
	if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
		return '';
	}
	// A private or loopback address is not the address anything leaves by. Sites
	// behind a proxy, a load balancer or a container runtime see 127.0.0.1 or a
	// 10.x here, and judging either the sender policy or the reverse name against
	// it produces an answer about a machine that does not exist on the internet.
	//
	// Returning '' says "cannot tell", which every caller already handles, and
	// which is the truthful answer. It matters more than it sounds: without this,
	// the sender-policy check could evaluate a 10.x against a record ending in
	// "reject everything else" and print a confident accusation that happened to
	// be right for the wrong reason — and would be wrong the first time it met a
	// host whose real address was authorised.
	if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
		return '';
	}
	return $ip;
}

/** Is WordPress sending through a configured SMTP service rather than the local machine? */
function horsetools_mail_via_smtp() {
	global $horsetools_options;
	return ! empty( $horsetools_options['mail-gsmtp1'] );
}

/**
 * Which provider the domain's MX suggests, so the setup can propose one instead
 * of asking the owner to know.
 *
 * @return array{key:string,name:string} Empty key when nothing is recognised.
 */
function horsetools_mail_guess_provider() {
	$mx = horsetools_mail_mx( horsetools_mail_domain() );
	$known = array(
		'google'      => array( 'Google Workspace / Gmail', array( 'google.com', 'googlemail.com' ) ),
		'larksuite'   => array( 'Lark Suite', array( 'larksuite.com' ) ),
		'zoho'        => array( 'Zoho Mail', array( 'zoho.com', 'zoho.eu' ) ),
		'microsoft'   => array( 'Microsoft 365 / Outlook', array( 'outlook.com', 'protection.outlook.com' ) ),
		'yandex'      => array( 'Yandex Mail', array( 'yandex.net', 'yandex.ru' ) ),
		'cpanel'      => array( 'Hosting của bạn (cPanel)', array( 'cpanel' ) ),
	);
	foreach ( $mx as $host ) {
		foreach ( $known as $key => $row ) {
			foreach ( $row[1] as $needle ) {
				if ( false !== strpos( $host, $needle ) ) {
					return array( 'key' => $key, 'name' => $row[0] );
				}
			}
		}
	}
	return array( 'key' => '', 'name' => '' );
}

/* -------------------------------------------------------------------------
 * What to tell the owner
 * ---------------------------------------------------------------------- */

/**
 * Can this server open a connection to another mail server at all?
 *
 * The question nobody asks, and the answer that explains most vanished mail.
 * PHP's mail() hands the message to a local mail agent, which accepts it — so
 * wp_mail() returns true and every screen says "sent" — and then tries to reach
 * the outside world on port 25. On a great many providers, DigitalOcean and
 * Google Cloud among them, outbound 25 is closed by default to stop the machines
 * being used for spam. The message sits in a local queue for ever. There is no
 * bounce, because the bounce would have to go out the same door.
 *
 * That is exactly the shape this was diagnosed from: two sites on one server with
 * opposite DNS — one publishing a hard-fail SPF, the other publishing none at all
 * — both reporting mail sent and neither delivering anything. Different DNS,
 * identical outcome, so the DNS was not the cause.
 *
 * The check is a bare TCP connect to a well-known mail exchanger. Nothing is sent
 * and no message is involved; it asks only whether the door opens. Held for half a
 * day, because whether a host blocks a port is not something that changes hourly.
 *
 * @return string 'open' | 'blocked' | 'unknown'
 */
function horsetools_mail_port25() {
	$hit = get_transient( 'horsetools_mail_port25' );
	if ( is_string( $hit ) && '' !== $hit ) {
		return $hit;
	}
	if ( ! function_exists( 'fsockopen' ) ) {
		return 'unknown';
	}

	$errno  = 0;
	$errstr = '';
	// A five second ceiling: a blocked port usually fails instantly with
	// "connection refused" and sometimes hangs until it times out, and an admin
	// screen must not wait on the second case.
	$sock = @fsockopen( 'aspmx.l.google.com', 25, $errno, $errstr, 5 ); // phpcs:ignore
	if ( $sock ) {
		fclose( $sock );
		$result = 'open';
	} else {
		$result = 'blocked';
	}
	set_transient( 'horsetools_mail_port25', $result, HORSETOOLS_MAIL_DNS_TTL );
	return $result;
}

/**
 * Does this server's address have a name?
 *
 * Every serious receiver — Gmail and Yahoo certainly — expects the address a
 * message arrives from to resolve back to a name, and Yahoo rejects outright
 * when it does not. It is the cheapest possible sign of a machine that was set
 * up to send mail rather than one that has been taken over, which is why it is
 * checked before anything in the message is even read.
 *
 * A shared web server on a plain VPS often has none, because nobody set one and
 * nothing else needs it. The site owner cannot see this, gets no bounce they
 * ever read, and has no way to connect "no reverse DNS" to "customers say the
 * order email never came".
 *
 * Only the unambiguous case is reported: no name at all. Judging whether an
 * existing name is a *good* one — whether it matches the HELO, whether it
 * resolves back — is a question with enough grey in it to produce false alarms,
 * and this file does not raise those.
 *
 * @return string 'named' | 'nameless' | 'unknown'
 */
function horsetools_mail_rdns() {
	$ip = horsetools_mail_server_ip();
	if ( '' === $ip || ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
		return 'unknown';
	}
	$key = 'horsetools_mail_rdns_' . md5( $ip );
	$hit = get_transient( $key );
	if ( is_string( $hit ) && '' !== $hit ) {
		return $hit;
	}
	// gethostbyaddr() returns the address unchanged when there is no PTR record,
	// which is the whole test.
	$name   = @gethostbyaddr( $ip ); // phpcs:ignore
	$result = ( is_string( $name ) && '' !== $name && $name !== $ip ) ? 'named' : 'nameless';
	set_transient( $key, $result, HORSETOOLS_MAIL_DNS_TTL );
	return $result;
}

/**
 * The findings, worst first, in words rather than record syntax.
 *
 * @return array<int,array{level:string,text:string,fix:string}> level: bad | warn | note | ok
 */
function horsetools_mail_findings() {
	$domain = horsetools_mail_domain();
	$out    = array();

	if ( ! horsetools_mail_dns_available() ) {
		return array(
			array(
				'level' => 'warn',
				'text'  => __( 'This host does not let the plugin read DNS, so none of the checks below can be made. That is not the same as everything being in order.', 'horse-tools' ),
				'fix'   => '',
			),
		);
	}

	$spf   = horsetools_mail_spf( $domain );
	$dmarc = horsetools_mail_dmarc( $domain );
	$mx    = horsetools_mail_mx( $domain );
	$ip    = horsetools_mail_server_ip();

	// First, because it makes everything below beside the point. No DNS record
	// fixes a closed port, and a site in this state can spend weeks adjusting SPF
	// while every message goes on sitting in a queue.
	if ( ! horsetools_mail_via_smtp() && 'nameless' === horsetools_mail_rdns() ) {
		$out[] = array(
			'level' => 'bad',
			/* translators: %s: the server's IP address. */
			'text'  => sprintf( __( 'This server sends mail from an address with no name attached to it (%s). Gmail and Yahoo both take that as the mark of a machine nobody set up to send mail, and Yahoo refuses it outright — which looks from here exactly like the message vanishing, because the refusal happens at the far end and the bounce goes somewhere you never read.', 'horse-tools' ), horsetools_mail_server_ip() ),
			'fix'   => __( 'Your host can attach a name to the address — ask them for “reverse DNS” or a “PTR record”. Or sidestep it entirely by sending through an email service, whose own servers already have one.', 'horse-tools' ),
		);
	}
	if ( ! horsetools_mail_via_smtp() && 'blocked' === horsetools_mail_port25() ) {
		$out[] = array(
			'level' => 'bad',
			'text'  => __( 'This server cannot reach any other mail server, so nothing it sends can ever be delivered. WordPress hands each message to a local mail program, that program accepts it — which is why every screen says “sent” — and then finds the way out closed. The message waits in a queue nobody reads, and there is no bounce, because a bounce would have to leave by the same door.', 'horse-tools' ),
			'fix'   => __( 'Nothing in DNS can change this. Send through an email service instead — those use a different port, which is open.', 'horse-tools' ),
		);
	}

	// The one that stops mail dead, and only when the site is sending from this
	// machine. Through an SMTP service it is the service's address that is
	// checked, not ours, and saying otherwise would be a false alarm on a site
	// that is set up correctly.
	if ( '' !== $spf && ! horsetools_mail_via_smtp() ) {
		if ( '' === $ip ) {
			$out[] = array(
				'level' => 'warn',
				'text'  => __( 'Your domain says which servers may send mail for it, and this site is sending straight from the web server. Whether that server is on the list could not be determined here.', 'horse-tools' ),
				'fix'   => __( 'Send through an email service instead — then the question does not arise.', 'horse-tools' ),
			);
		} else {
			$budget  = 10;
			$verdict = horsetools_mail_spf_eval( $ip, $domain, $budget );
			if ( 'fail' === $verdict ) {
				$out[] = array(
					'level' => 'bad',
					/* translators: %s: the domain name. */
					'text'  => sprintf( __( 'Mail this site sends as %s is treated as forged. Your domain publishes a list of servers allowed to send for it, that list ends in “reject everything else”, and this web server is not on it.', 'horse-tools' ), $domain ),
					'fix'   => __( 'Send through an email service that is on the list, or add this server to it.', 'horse-tools' ),
				);
			} elseif ( 'softfail' === $verdict ) {
				$out[] = array(
					'level' => 'warn',
					/* translators: %s: the domain name. */
					'text'  => sprintf( __( 'This web server is not on your domain’s list of allowed senders. The list says “treat anything else as suspicious” rather than “reject it”, so mail as %s will usually arrive in the spam folder.', 'horse-tools' ), $domain ),
					'fix'   => __( 'Send through an email service that is on the list.', 'horse-tools' ),
				);
			} elseif ( 'pass' === $verdict ) {
				$out[] = array(
					'level' => 'ok',
					'text'  => __( 'This web server is on your domain’s list of allowed senders.', 'horse-tools' ),
					'fix'   => '',
				);
			} else {
				$out[] = array(
					'level' => 'note',
					'text'  => __( 'Your domain has a list of allowed senders, but it could not be worked out from here whether this server is on it.', 'horse-tools' ),
					'fix'   => '',
				);
			}
		}
	}

	if ( '' === $spf ) {
		$out[] = array(
			'level' => 'warn',
			'text'  => __( 'Your domain does not say which servers are allowed to send mail for it. Anyone can send mail claiming to be you, and mail you really did send has nothing to vouch for it — Gmail and Yahoo both treat that as a reason for suspicion.', 'horse-tools' ),
			'fix'   => __( 'Add an SPF record. Whichever email service you choose below will give you the exact line.', 'horse-tools' ),
		);
	}

	if ( '' === $dmarc ) {
		$out[] = array(
			'level' => 'note',
			'text'  => __( 'Your domain has no DMARC record, so you are never told when somebody sends mail pretending to be you, and receivers have no instruction about what to do with it.', 'horse-tools' ),
			/* translators: %s: a DNS record value. */
			'fix'   => sprintf( __( 'A safe first one collects reports and changes nothing: a TXT record at _dmarc.%1$s with the value %2$s', 'horse-tools' ), $domain, 'v=DMARC1; p=none;' ),
		);
	}

	if ( ! $mx ) {
		$out[] = array(
			'level' => 'warn',
			/* translators: %s: the domain name. */
			'text'  => sprintf( __( 'Nothing accepts mail for %s, so any address at your own domain cannot receive replies. If you have stopped paying for a mailbox provider, its records may still be pointing there.', 'horse-tools' ), $domain ),
			'fix'   => __( 'Either set up a mailbox for the domain, or make sure the address you send notifications to is one you actually read.', 'horse-tools' ),
		);
	}

	// Worst first.
	$rank = array( 'bad' => 0, 'warn' => 1, 'note' => 2, 'ok' => 3 );
	usort( $out, function ( $a, $b ) use ( $rank ) {
		return $rank[ $a['level'] ] <=> $rank[ $b['level'] ];
	} );
	return $out;
}
