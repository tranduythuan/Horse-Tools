<?php
/**
 * Horse Tools — which domains this site links to on purpose.
 *
 * Split out of inc/watch-links.php so the front end can ask the question without
 * carrying the answer's machinery. The inventory, the batched collector and the
 * review screen are all admin work and stay there; what is left here is the
 * short list a page view needs: normalise a URL to a host, say which hosts are
 * this site, and read back what the owner approved.
 *
 * That matters because inc/link-guard.php runs on every rendered post. Loading
 * six hundred lines of admin screen on a shop's product page to reach two option
 * reads would be paying for the whole feature on the one request that must not
 * pay for anything.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

const HORSETOOLS_LINK_OK = 'horsetools_link_approved';

/**
 * A URL reduced to the host that would be paid for it, or '' if there is none.
 *
 * `www.` comes off because nobody approving `example.com` means to withhold
 * approval from `www.example.com`. A subdomain stays on: `promo.example.com` is
 * frequently not the same people as `example.com`, and on a compromised site it
 * is frequently not the same people on purpose.
 *
 * @param string $url
 * @return string
 */
function horsetools_link_host( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	// `//evil.com/x` is a real link that parse_url reads as a path unless it is
	// given a scheme. Injected markup uses this form precisely because it is easy
	// to skim past.
	if ( 0 === strpos( $url, '//' ) ) {
		$url = 'http:' . $url;
	}
	// mailto:, tel:, #anchor, javascript:, data: — none of them point anywhere,
	// and contact details are somebody else's job (see watch-contact.php).
	if ( ! preg_match( '~^https?://~i', $url ) ) {
		return '';
	}

	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( ! is_string( $host ) || '' === $host ) {
		return '';
	}
	$host = strtolower( rtrim( trim( $host ), '.' ) );
	if ( 0 === strpos( $host, 'www.' ) ) {
		$host = substr( $host, 4 );
	}

	// Has to look like a host and not like the wreckage of a broken tag: at least
	// one dot, and none of the characters that only appear when a regex has run
	// off the end of an attribute.
	if ( ! preg_match( '~^[^\s"\'<>/\\\\?#@]+\.[^\s"\'<>/\\\\?#@]{2,}$~u', $host ) ) {
		return '';
	}
	return $host;
}

/**
 * A host as it arrives from the review form, normalised the same way the scan
 * normalised it.
 *
 * The form posts back bare hosts, but it must not matter: a value that has
 * already been through the scan and a value somebody typed have to land on the
 * same key, or approving a domain silently fails to approve it.
 *
 * @param string $s
 * @return string
 */
function horsetools_link_host_input( $s ) {
	$s = trim( (string) $s );
	if ( '' === $s ) {
		return '';
	}
	if ( ! preg_match( '~^(?:https?:)?//~i', $s ) ) {
		$s = 'http://' . $s;
	}
	return horsetools_link_host( $s );
}

/** The hosts that are this site. Links to these are not outbound. */
function horsetools_link_self_hosts() {
	$hosts = array();
	foreach ( array( home_url(), site_url() ) as $url ) {
		$h = horsetools_link_host( $url );
		if ( '' !== $h ) {
			$hosts[ $h ] = true;
		}
	}
	/**
	 * Multisite, a separate media domain, a staging alias — anything the owner
	 * considers "us" and does not want listed as somewhere else.
	 */
	return (array) apply_filters( 'horsetools_link_self_hosts', array_keys( $hosts ) );
}

/**
 * host => unix time it was approved.
 *
 * The timestamp is not used yet. It is stored because the question it answers —
 * "you agreed to this domain three years ago; is it still the same people?" —
 * cannot be asked retroactively, and a domain changing hands is the one way an
 * approved link goes bad without anything on this site changing at all.
 *
 * @return array<string,int>
 */
function horsetools_link_approved() {
	$a = get_option( HORSETOOLS_LINK_OK, null );
	return is_array( $a ) ? $a : array();
}

/** Has the owner been through the list once? */
function horsetools_link_reviewed() {
	return is_array( get_option( HORSETOOLS_LINK_OK, null ) );
}
