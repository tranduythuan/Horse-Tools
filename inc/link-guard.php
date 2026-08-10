<?php
/**
 * Horse Tools — take the value out of a link nobody approved, while it is still there.
 *
 * Everything else in this area reports. Reporting leaves a window: the link goes
 * in, the plugin says so on a screen, and the link keeps working until somebody
 * logs in and reads that screen. On the site this was all written for, that
 * window was two years wide.
 *
 * This closes it. A domain that is not on the approved list gets `rel="nofollow"`
 * added on the way out, or the link taken away entirely, at the moment the page
 * is rendered — before anyone has noticed anything. Whatever the link was worth
 * to whoever put it there stops being worth that within one page view.
 *
 * It is deliberately the smallest useful action. It does not delete anything, it
 * does not touch the database, and turning it off puts every link back exactly
 * as it was, because the post was never modified — only what was printed.
 *
 * Every gate here fails open. This runs on every page view of a live shop, and
 * the failure that matters is not "a bad link survived", it is "the shop's own
 * links broke". So: off unless switched on, silent until the content has been
 * read all the way through, silent until the owner has been through the list
 * once, and never applied to anything that is not plainly an outbound link to a
 * domain the owner was shown and did not approve.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

const HORSETOOLS_LINK_GUARD = 'horsetools_link_guard';

/**
 * @return string 'off' | 'nofollow' | 'strip'
 */
function horsetools_link_guard_mode() {
	$mode = get_option( HORSETOOLS_LINK_GUARD, 'off' );
	return in_array( $mode, array( 'nofollow', 'strip' ), true ) ? $mode : 'off';
}

/** @param string $mode */
function horsetools_link_guard_set( $mode ) {
	update_option( HORSETOOLS_LINK_GUARD, in_array( $mode, array( 'nofollow', 'strip' ), true ) ? $mode : 'off', false );
	// Switching this off is what an attacker with database access would do first,
	// so the anchor watches it. Changed here, through the screen, it is agreed to;
	// changed by SQL it is not.
	if ( function_exists( 'horsetools_anchor_touch' ) ) {
		horsetools_anchor_touch( array( '@switches' ) );
	}
}

/**
 * Is the guard allowed to act on this request at all?
 *
 * Switched on, and a review has happened. The second is the condition that
 * matters: without it, installing an update would quietly nofollow every
 * outbound link on the site — the partner, the courier, the payment gateway —
 * because nothing had been approved yet.
 *
 * It is also the only gate needed. "Has the owner reviewed" already implies "the
 * walk had finished", because the review screen shows nothing to approve until
 * it has. Asking horsetools_scan_finished() here as well would have been worse
 * than redundant: it compares a signature built from the registered collectors,
 * and the collectors register on admin screens only, so on the front end — the
 * only place this filter runs — it answers false on every request and the guard
 * would never once have acted.
 *
 * @return bool
 */
function horsetools_link_guard_active() {
	return 'off' !== horsetools_link_guard_mode() && horsetools_link_reviewed();
}

/**
 * The hosts that are allowed through, as a lookup.
 *
 * Approved domains plus this site's own. Built once per piece of content and
 * handed down, rather than held in a `static` for the request: the cost this
 * avoids is rebuilding it for every anchor tag, and a function-level static
 * would also freeze the answer across everything else in the request for no
 * further gain — and make the whole filter impossible to test without
 * reimplementing it, which is the same as not testing it.
 *
 * @return array<string,true>
 */
function horsetools_link_guard_allowed() {
	// The approved list is used as it is stored, not copied into a new array.
	// Copying it cost nothing worth measuring on a shop with six domains and
	// rebuilt six hundred and eighty-six entries per post on a blog that has
	// that many — twenty times over on an archive page, for an answer that is
	// identical every time. Only the site's own hosts, of which there are two,
	// are merged in.
	$allowed = horsetools_link_approved();
	foreach ( horsetools_link_self_hosts() as $host ) {
		$allowed[ $host ] = true;
	}
	return $allowed;
}

/**
 * Does this opening-tag attribute string point somewhere unapproved?
 *
 * @param string             $attrs
 * @param array<string,true> $allowed
 * @return bool
 */
function horsetools_link_guard_hit( $attrs, array $allowed ) {
	if ( ! preg_match( '~\bhref\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))~i', $attrs, $m ) ) {
		return false;
	}
	$href = html_entity_decode( '' !== $m[1] ? $m[1] : ( isset( $m[2] ) && '' !== $m[2] ? $m[2] : ( isset( $m[3] ) ? $m[3] : '' ) ), ENT_QUOTES );
	$host = horsetools_link_host( $href );
	// '' covers everything that is not an outbound web link at all: a relative
	// path, an anchor on the page, mailto:, tel:, javascript:. None of those are
	// this feature's business and each one is a way to break a site.
	return ( '' !== $host && ! isset( $allowed[ $host ] ) );
}

/**
 * Put nofollow on one opening tag, keeping whatever rel was already there.
 *
 * @param string $tag Full `<a …>`.
 * @return string
 */
function horsetools_link_guard_nofollow_tag( $tag ) {
	if ( preg_match( '~\brel\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))~i', $tag, $m ) ) {
		$rel = '' !== $m[1] ? $m[1] : ( isset( $m[2] ) && '' !== $m[2] ? $m[2] : ( isset( $m[3] ) ? $m[3] : '' ) );
		if ( preg_match( '~\bnofollow\b~i', $rel ) ) {
			return $tag;
		}
		$new = trim( $rel . ' nofollow' );
		return str_replace( $m[0], 'rel="' . esc_attr( $new ) . '"', $tag );
	}
	// No rel at all: add one just before the closing bracket, keeping any
	// self-closing slash where it was.
	return preg_replace( '~\s*(/?)>$~', ' rel="nofollow"$1>', $tag, 1 );
}

/**
 * Rewrite the links in one piece of rendered HTML.
 *
 * @param string $html
 * @return string
 */
function horsetools_link_guard_apply( $html ) {
	$html = (string) $html;
	// The cheapest possible way out, taken on the overwhelming majority of
	// requests: a page with no anchor tag in it has nothing here to do.
	if ( '' === $html || false === stripos( $html, '<a' ) ) {
		return $html;
	}

	$allowed = horsetools_link_guard_allowed();

	if ( 'strip' === horsetools_link_guard_mode() ) {
		// Replace the whole element with the words that were inside it. The link
		// stops existing; the sentence still reads.
		$html = preg_replace_callback(
			'~<a\b([^>]*)>(.*?)</a>~is',
			function ( $m ) use ( $allowed ) {
				return horsetools_link_guard_hit( $m[1], $allowed ) ? $m[2] : $m[0];
			},
			$html
		);
		// An anchor with no closing tag never matched above. Injected markup is
		// exactly where those turn up, so the weaker measure is applied to
		// whatever is left rather than letting it through untouched.
	}

	return preg_replace_callback(
		'~<a\b([^>]*)>~i',
		function ( $m ) use ( $allowed ) {
			return horsetools_link_guard_hit( $m[1], $allowed ) ? horsetools_link_guard_nofollow_tag( $m[0] ) : $m[0];
		},
		$html
	);
}

/**
 * @param string $html
 * @return string
 */
function horsetools_link_guard_filter( $html ) {
	if ( is_admin() || ! horsetools_link_guard_active() ) {
		return $html;
	}
	return horsetools_link_guard_apply( $html );
}

/**
 * Late on the_content, so what is seen is what the visitor would have got.
 *
 * Priority 99: wpautop runs at 10 and shortcodes at 11, and a link produced by a
 * shortcode — which on this plugin includes every snippet — is as much a part of
 * the page as one typed into the editor. Running earlier would judge the post
 * before it had finished being built.
 *
 * The feed is the same page with the same links in it, going somewhere the owner
 * will never look at.
 */
function horsetools_link_guard_hooks() {
	add_filter( 'the_content', 'horsetools_link_guard_filter', 99 );
	add_filter( 'the_excerpt', 'horsetools_link_guard_filter', 99 );
	add_filter( 'the_content_feed', 'horsetools_link_guard_filter', 99 );
	add_filter( 'the_excerpt_rss', 'horsetools_link_guard_filter', 99 );
}
add_action( 'init', 'horsetools_link_guard_hooks' );
