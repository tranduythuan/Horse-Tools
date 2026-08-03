<?php
/**
 * On-demand loader for the bundled Google API client.
 *
 * The client is ~480 files but is only needed by two optional features —
 * Google Login and the SEO Indexing / Search Console integration. Shipping it
 * loose made every plugin install unpack ~760 files, which is slow on hosts
 * without the PHP `zip` extension (WordPress falls back to a pure-PHP unzip that
 * processes one file at a time). So the distributed plugin ships the client as a
 * single `link/google-api.zip`; this unpacks it into `link/google-api/` on first
 * use and reuses it thereafter. A normal install now unpacks ~280 files.
 *
 * Returns the autoload path, or false if the library can't be made available
 * (the callers already degrade gracefully in that case). In the dev checkout the
 * loose folder exists and no archive is present, so this returns immediately.
 */
function horsetools_google_autoload_path() {
	$dir      = HORSETOOLS_DIR . 'link/google-api';
	$autoload = $dir . '/vendor/autoload.php';
	if ( file_exists( $autoload ) ) {
		return $autoload;
	}
	$zip = HORSETOOLS_DIR . 'link/google-api.zip';
	if ( ! file_exists( $zip ) ) {
		return false;
	}
	// Prefer the native extension: it writes files directly and never prompts
	// for FTP credentials the way WP_Filesystem can.
	if ( class_exists( 'ZipArchive' ) ) {
		$za = new ZipArchive();
		if ( true === $za->open( $zip ) ) {
			@$za->extractTo( $dir );
			$za->close();
		}
	}
	// Fallback to WordPress' own unzipper (PclZip) when ZipArchive is absent.
	if ( ! file_exists( $autoload ) ) {
		if ( ! function_exists( 'unzip_file' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		if ( function_exists( 'WP_Filesystem' ) && WP_Filesystem() ) {
			unzip_file( $zip, $dir );
		}
	}
	return file_exists( $autoload ) ? $autoload : false;
}

/**
 * Horse Tools — outbound request guard.
 *
 * WordPress's own wp_http_validate_url() is a useful first gate but it is not
 * an SSRF boundary:
 *
 *   - it blocks IPv4 loopback and RFC1918 only. 169.254.0.0/16 — the AWS, GCP
 *     and Azure instance-metadata address — is NOT blocked;
 *   - it reasons about IPv4 only, so http://[::1]/ and ULA/link-local IPv6
 *     hosts pass straight through;
 *   - it resolves the hostname separately from the connection that follows,
 *     so a short-TTL attacker domain can answer with a public address during
 *     validation and an internal one at connect time (DNS rebinding).
 *
 * These helpers add the missing range checks on every address a host resolves
 * to. Redirects are refused outright rather than re-validated per hop, which
 * removes that whole bypass class — image fetching has no legitimate need to
 * follow a redirect chain.
 *
 * Residual risk, stated honestly: without pinning the connection to the exact
 * IP that was vetted, a determined DNS-rebinding attacker retains a narrow
 * race window. Closing it fully requires bypassing the WP HTTP API, which
 * costs more than it buys here. What remains is blind — the callers all
 * verify the response really is an image before using it.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Is this IP address inside a range we refuse to talk to?
 *
 * @param string $ip IPv4 or IPv6 address.
 * @return bool True when the address must not be contacted.
 */
function horsetools_ip_is_blocked( $ip ) {
	if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
		return true;
	}

	// Anything not a normal, globally routable unicast address.
	$public = filter_var(
		$ip,
		FILTER_VALIDATE_IP,
		FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
	);
	if ( false === $public ) {
		return true;
	}

	// FILTER_FLAG_NO_RES_RANGE misses a few ranges that matter for SSRF.
	if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
		$long   = ip2long( $ip );
		$ranges = array(
			array( '169.254.0.0', 16 ), // link-local — cloud instance metadata
			array( '100.64.0.0', 10 ),  // carrier-grade NAT
			array( '192.0.0.0', 24 ),   // IETF protocol assignments
			array( '198.18.0.0', 15 ),  // benchmarking
			array( '224.0.0.0', 4 ),    // multicast
			array( '240.0.0.0', 4 ),    // reserved
			array( '0.0.0.0', 8 ),      // "this network"
		);
		foreach ( $ranges as $range ) {
			$mask = -1 << ( 32 - $range[1] );
			if ( ( $long & $mask ) === ( ip2long( $range[0] ) & $mask ) ) {
				return true;
			}
		}
		return false;
	}

	// IPv6: reject loopback, unspecified, unique-local, link-local, and any
	// IPv4-mapped address whose embedded IPv4 we would have refused.
	$packed = @inet_pton( $ip );
	if ( false === $packed || strlen( $packed ) !== 16 ) {
		return true;
	}
	$first = ord( $packed[0] );
	if ( $packed === str_repeat( "\0", 15 ) . "\1" ) {
		return true; // ::1
	}
	if ( $packed === str_repeat( "\0", 16 ) ) {
		return true; // ::
	}
	if ( ( $first & 0xFE ) === 0xFC ) {
		return true; // fc00::/7 unique local
	}
	if ( 0xFE === $first && ( ord( $packed[1] ) & 0xC0 ) === 0x80 ) {
		return true; // fe80::/10 link local
	}
	if ( 0xFF === $first ) {
		return true; // ff00::/8 multicast
	}
	if ( substr( $packed, 0, 12 ) === str_repeat( "\0", 10 ) . "\xFF\xFF" ) {
		return horsetools_ip_is_blocked( inet_ntop( substr( $packed, 12, 4 ) ) );
	}

	return false;
}

/**
 * Can this URL safely be fetched from the server?
 *
 * Applies WordPress's own validation first, then resolves the host and
 * rejects if ANY address it answers with is in a blocked range.
 *
 * @param string $url URL to check.
 * @return bool
 */
function horsetools_url_is_safe_target( $url ) {
	if ( ! is_string( $url ) || '' === trim( $url ) ) {
		return false;
	}

	$parts = wp_parse_url( $url );
	if ( empty( $parts['host'] ) || empty( $parts['scheme'] ) ) {
		return false;
	}
	if ( ! in_array( strtolower( $parts['scheme'] ), array( 'http', 'https' ), true ) ) {
		return false;
	}
	if ( ! wp_http_validate_url( $url ) ) {
		return false;
	}

	$host = trim( $parts['host'], '[]' );

	// Literal address in the URL — check it directly, no lookup needed.
	if ( filter_var( $host, FILTER_VALIDATE_IP ) ) {
		return ! horsetools_ip_is_blocked( $host );
	}

	$addresses = array();

	$v4 = gethostbynamel( $host );
	if ( is_array( $v4 ) ) {
		$addresses = $v4;
	}

	if ( function_exists( 'dns_get_record' ) ) {
		$aaaa = @dns_get_record( $host, DNS_AAAA );
		if ( is_array( $aaaa ) ) {
			foreach ( $aaaa as $record ) {
				if ( ! empty( $record['ipv6'] ) ) {
					$addresses[] = $record['ipv6'];
				}
			}
		}
	}

	// A host that resolves to nothing cannot be fetched anyway; refuse rather
	// than hand it to cURL and hope.
	if ( empty( $addresses ) ) {
		return false;
	}

	foreach ( $addresses as $address ) {
		if ( horsetools_ip_is_blocked( $address ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Fetch a URL for the plugin, with the guard above applied.
 *
 * Redirects are refused (`redirection => 0`) so there is no second, unvetted
 * destination. Callers must still verify the body is what they expected — a
 * remote server controls its own Content-Type header and can claim anything.
 *
 * @param string $url  URL to fetch.
 * @param array  $args Extra args merged into the request.
 * @return array|WP_Error Response array, or WP_Error on refusal/failure.
 */
function horsetools_safe_fetch( $url, array $args = array() ) {
	if ( ! horsetools_url_is_safe_target( $url ) ) {
		return new WP_Error(
			'horsetools_blocked_url',
			__( 'That address cannot be fetched from this server.', 'horse-tools' )
		);
	}

	$defaults = array(
		'timeout'     => 10,
		'redirection' => 0,
		'sslverify'   => true,
		'user-agent'  => 'HorseTools/' . HORSETOOLS_VERSION . '; ' . home_url( '/' ),
	);

	return wp_safe_remote_get( $url, array_merge( $defaults, $args ) );
}
