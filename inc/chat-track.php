<?php
/**
 * Horse Tools — contact-click measurement.
 *
 * Answers the question a site owner cannot otherwise answer: does anyone
 * actually press the contact buttons, and which ones.
 *
 * Nothing is stored on the site. The click is handed to whatever analytics the
 * site already loads (GA4 via gtag, or Tag Manager) and that is the end of it,
 * so there is no new data to keep, no personal data involved, and nothing to
 * declare in a privacy policy.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Load the tracker on the front end.
 *
 * In the footer and without dependencies: it binds one delegated listener and
 * needs neither jQuery nor the analytics library to have loaded first — it
 * checks for gtag at click time, not at load time.
 */
function horsetools_track_enqueue() {
	if ( is_admin() ) {
		return;
	}
	wp_enqueue_script(
		'horsetools-track',
		HORSETOOLS_URL . 'link/chat/horsetrack.js',
		array(),
		HORSETOOLS_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'horsetools_track_enqueue' );

/**
 * Report whether an analytics tag is present, for the settings screen.
 *
 * Read from the front page rather than guessed from active plugins: what
 * matters is whether a tag actually reaches a visitor, and that depends on
 * caching, consent tools and optimisers as much as on which plugin is
 * installed. Cached for an hour so opening the screen is not a page fetch.
 *
 * @return array{found:bool, id:string, how:string}
 */
function horsetools_track_detect() {
	$cached = get_transient( 'horsetools_track_detect' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	// Ask the site's own settings first. Fetching the home page is the more
	// honest test — it shows what a visitor really receives — but a site
	// fetching itself is exactly the request many hosts block, and the first
	// version of this reported "no tag found" when the truth was "I could not
	// look". Site Kit and friends have already stored the ID; reading it needs
	// no network at all and cannot be wrong in that direction.
	$out = horsetools_track_detect_stored();

	// The page is read whether or not an ID was found in the options, because it
	// answers a second question the options cannot: which of the two routes a
	// click will take. See horsetools_track_route().
	$res = wp_remote_get(
		home_url( '/' ),
		array(
			'timeout'     => 8,
			'sslverify'   => false,
			'redirection' => 3,
			// A plain WordPress user agent is what optimisers and bot filters
			// serve a stripped page to, and a stripped page has no tag in it.
			'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0 Safari/537.36',
		)
	);
	$body = is_wp_error( $res ) ? '' : wp_remote_retrieve_body( $res );
	$ok   = ! is_wp_error( $res )
		&& 200 === (int) wp_remote_retrieve_response_code( $res )
		// A 200 is not proof we reached the site. On shared hosting a loopback
		// often lands on the default vhost, a parking page or a bot-check page,
		// all of which answer 200 with no tag — and reading that as "you have no
		// analytics" is the confident wrong answer this screen must never give.
		&& horsetools_track_is_own_page( $body );

	if ( ! $ok ) {
		$body = '';
		if ( ! $out['found'] ) {
			$out['how'] = 'unreachable';
		}
	} elseif ( ! $out['found'] ) {
		if ( preg_match( '~\b(G-[A-Z0-9]{6,})\b~', $body, $m ) ) {
			$out = array( 'found' => true, 'id' => $m[1], 'how' => 'page' );
		} elseif ( preg_match( '~\b(GTM-[A-Z0-9]{4,})\b~', $body, $m ) ) {
			$out = array( 'found' => true, 'id' => $m[1], 'how' => 'page' );
		} else {
			$out['how'] = 'absent';
		}
	}

	$out['route'] = horsetools_track_route( $body );

	set_transient( 'horsetools_track_detect', $out, HOUR_IN_SECONDS );
	return $out;
}

/**
 * Is this really our own front page, or something the host answered with?
 *
 * Loopback requests on shared hosting routinely land somewhere else and still
 * return 200. Two independent markers have to be absent before we give up on
 * the body: WordPress's own asset paths, and the site's own host name.
 *
 * @param string $body
 * @return bool
 */
function horsetools_track_is_own_page( $body ) {
	if ( strlen( $body ) < 512 ) {
		return false;
	}
	// The host name, not merely "this is some WordPress site". On shared hosting
	// a loopback commonly lands on the account's primary domain, which is a real
	// WordPress install and would pass a wp-content check — and if that other
	// site has no analytics, its page gets read as proof that this one has none.
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	return $host && false !== stripos( $body, (string) $host );
}

/**
 * Look for a measurement ID anywhere in the site's own options.
 *
 * Searched by value rather than by option name. The first version listed the
 * option names of the analytics plugins it knew about, which missed every other
 * ordinary way of installing GA4 — pasted into a header-and-footer plugin, into
 * a theme option, into a page builder's global scripts — and, because that name
 * list also matched dozens of unrelated rows, a LIMIT could push the one row
 * that mattered out of the result set entirely. A measurement ID has a shape
 * nothing else has, so looking for the shape needs no list to maintain and
 * cannot be defeated by a plugin renaming its options.
 *
 * Transients are skipped (they hold cached copies of pages, not settings) and
 * huge values are left alone, so this stays a cheap query on a bloated table.
 *
 * @return array{found:bool, id:string, how:string}
 */
function horsetools_track_detect_stored() {
	global $wpdb;

	$rows = $wpdb->get_col(
		"SELECT option_value FROM {$wpdb->options}
		 WHERE option_name NOT LIKE '\\_transient%'
		   AND option_name NOT LIKE '\\_site\\_transient%'
		   AND LENGTH( option_value ) BETWEEN 10 AND 200000
		   AND ( option_value LIKE '%G-%' OR option_value LIKE '%GTM-%' )
		 LIMIT 200"
	); // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- one read, cached in a transient by the caller.

	// A GA4 property ID is what the tracker actually needs; a container ID only
	// means Tag Manager is present and still has to be wired up. Collect both
	// and prefer the former, rather than returning whichever row came first.
	$gtm = '';
	foreach ( (array) $rows as $value ) {
		if ( ! is_string( $value ) || '' === $value ) {
			continue;
		}
		if ( preg_match( '~\b(G-[A-Z0-9]{6,})\b~', $value, $m ) ) {
			return array( 'found' => true, 'id' => $m[1], 'how' => 'stored' );
		}
		if ( '' === $gtm && preg_match( '~\b(GTM-[A-Z0-9]{4,})\b~', $value, $m ) ) {
			$gtm = $m[1];
		}
	}
	if ( '' !== $gtm ) {
		return array( 'found' => true, 'id' => $gtm, 'how' => 'stored' );
	}
	return array( 'found' => false, 'id' => '', 'how' => '' );
}

/**
 * Which of the two routes a click will actually take on this site.
 *
 * The tracker asks one question at the moment of the click: is there a gtag()
 * on the page? If there is, the event goes straight to GA4 and the owner has
 * nothing to set up. If there is not, it goes into the dataLayer and sits there
 * until a tag and trigger are built in Tag Manager.
 *
 * Getting this wrong in either direction wastes somebody's afternoon, and the
 * obvious signal is misleading: a site can run Tag Manager and still have
 * gtag(), because a GA4 tag inside a container loads gtag.js itself. Telling
 * every Tag Manager user to go and build a trigger would send most of them on
 * an errand they do not need.
 *
 * So the container is opened and read. It is a public script; fetching it from
 * the server avoids the browser's cross-origin rules, and a GA4 measurement ID
 * inside it means a GA4 tag is configured, which means gtag() will exist.
 *
 * @param string $body Home page HTML, or '' when it could not be read.
 * @return string 'gtag' | 'datalayer' | 'none' | 'unknown'
 */
function horsetools_track_route( $body ) {
	if ( '' === $body ) {
		return 'unknown';
	}
	// gtag.js on the page, or the stub the snippet defines: nothing to set up.
	if ( preg_match( '~gtag/js\?id=G-~i', $body ) || preg_match( '~function\s+gtag\s*\(~i', $body ) ) {
		return 'gtag';
	}
	if ( ! preg_match( '~\b(GTM-[A-Z0-9]{4,})\b~', $body, $m ) ) {
		return 'none';
	}

	$cached = get_transient( 'horsetools_track_route_' . $m[1] );
	if ( false !== $cached ) {
		return $cached;
	}
	$res  = wp_remote_get(
		'https://www.googletagmanager.com/gtm.js?id=' . rawurlencode( $m[1] ),
		array( 'timeout' => 8 )
	);
	$route = 'datalayer';
	if ( ! is_wp_error( $res ) && 200 === (int) wp_remote_retrieve_response_code( $res ) ) {
		// A GA4 measurement ID inside the container means a GA4 tag is
		// configured there, and that tag brings gtag() with it.
		if ( preg_match( '~\bG-[A-Z0-9]{6,}\b~', wp_remote_retrieve_body( $res ) ) ) {
			$route = 'gtag';
		}
	} else {
		// Could not read the container. Say so rather than guess: the Tag
		// Manager instructions are wasted effort for someone who does not need
		// them, and their absence is a silent failure for someone who does.
		$route = 'unknown';
	}
	set_transient( 'horsetools_track_route_' . $m[1], $route, DAY_IN_SECONDS );
	return $route;
}

/**
 * Is a Tag Manager container present, whatever the measurement check concluded?
 *
 * Worth its own question because the two answers matter separately: the check
 * above stops at the first ID it finds and will report a GA4 property on a site
 * that also runs Tag Manager. Somebody who built click tags in that container
 * needs warning before this setting doubles their numbers — the case that cost
 * a real site a day of confused reports.
 *
 * @return bool
 */
function horsetools_track_has_gtm() {
	$found = get_transient( 'horsetools_track_gtm' );
	if ( false !== $found ) {
		return '1' === $found;
	}

	global $wpdb;
	$rows = $wpdb->get_col(
		"SELECT option_value FROM {$wpdb->options}
		 WHERE option_name NOT LIKE '\\_transient%'
		   AND option_name NOT LIKE '\\_site\\_transient%'
		   AND LENGTH( option_value ) BETWEEN 10 AND 200000
		   AND option_value LIKE '%GTM-%'
		 LIMIT 100"
	); // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- one read, cached below.

	$found = '0';
	foreach ( (array) $rows as $value ) {
		if ( is_string( $value ) && preg_match( '~\bGTM-[A-Z0-9]{4,}\b~', $value ) ) {
			$found = '1';
			break;
		}
	}
	set_transient( 'horsetools_track_gtm', $found, DAY_IN_SECONDS );
	return '1' === $found;
}

/** Clear the cached detection when asked to look again. */
function horsetools_track_recheck() {
	if ( ! isset( $_GET['horsetools-track-recheck'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	check_admin_referer( 'horsetools_track_recheck' );
	delete_transient( 'horsetools_track_detect' );
	delete_transient( 'horsetools_track_gtm' );
	wp_safe_redirect( remove_query_arg( array( 'horsetools-track-recheck', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'horsetools_track_recheck' );
