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

/** How old an approval has to be before it is worth a second look. */
const HORSETOOLS_LINK_STALE_DAYS = 365;

/**
 * Approvals old enough to be worth asking about again — and few enough to ask.
 *
 * A domain can go bad without anything on this site changing. It expires, it is
 * bought by somebody else, and the link in a post from 2019 now points at
 * whatever the new owner sells. Nothing on the site moved, so nothing here
 * noticed, and the approval given three years ago is still standing.
 *
 * The timestamp for this has been stored since the approvals were first written
 * and never read until now. Reading it naively would have produced the same wall
 * the review screen already had once: a site that approved 686 domains in one
 * click gets all 686 back a year later, which is not a review, it is a wall with
 * a date on it.
 *
 * So the same filter that has worked everywhere else in this feature applies —
 * how *thinly* linked the domain is. A domain reached from two hundred posts is
 * one whose going bad you would hear about from a customer within the week. A
 * domain reached once, from an article nobody has opened since 2019, is the one
 * that can change hands in silence. That is where a second look is worth asking
 * for, and it turns hundreds into a handful.
 *
 * @param array<string,array> $found The inventory, for the link counts.
 * @param int                 $now   Injectable for testing.
 * @return array<string,int> host => when it was approved, oldest first.
 */
function horsetools_link_stale( array $found, $now = 0 ) {
	$now   = $now ? (int) $now : time();
	$limit = $now - ( HORSETOOLS_LINK_STALE_DAYS * DAY_IN_SECONDS );
	$out   = array();

	foreach ( horsetools_link_approved() as $host => $when ) {
		$when = (int) $when;
		if ( $when <= 0 || $when > $limit ) {
			continue;
		}
		// Still linked from somewhere — a domain no longer in the content is not
		// worth anybody's attention, whatever its age.
		if ( ! isset( $found[ $host ] ) ) {
			continue;
		}
		$posts = isset( $found[ $host ]['posts'] ) ? count( $found[ $host ]['posts'] ) : 0;
		if ( $posts > 2 ) {
			continue;
		}
		$out[ $host ] = $when;
	}

	asort( $out );
	return $out;
}

/** Mark these as looked at again, without changing what is approved. */
function horsetools_link_refresh( array $hosts ) {
	$approved = horsetools_link_approved();
	$now      = time();
	$hit      = false;
	foreach ( $hosts as $host ) {
		$host = horsetools_link_host_input( $host );
		if ( '' !== $host && isset( $approved[ $host ] ) ) {
			$approved[ $host ] = $now;
			$hit               = true;
		}
	}
	if ( $hit ) {
		update_option( HORSETOOLS_LINK_OK, $approved, false );
	}
	return $hit;
}
