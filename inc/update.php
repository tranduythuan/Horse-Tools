<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Self-update from GitHub Releases.
 *
 * Horse Tools is distributed outside the wordpress.org repository, so WordPress
 * cannot update it by itself — and installing by hand meant pushing the ZIP up
 * from the site owner's own connection through wp-admin's uploader. On slow or
 * lossy uplinks that multi-megabyte browser upload stalls and dies (HTTP/3
 * stream aborts, HTTP/2 408 body timeouts) even though the site itself runs
 * fine, because small requests survive a bad path while a long sustained upload
 * does not.
 *
 * This module removes the browser upload from the picture entirely: the plugin
 * reports its updates to WordPress' own update system, pointing at the ZIP
 * asset of the latest GitHub Release. When the site owner clicks Update (or
 * enables auto-updates), the SERVER downloads the package straight from GitHub
 * — datacenter-to-datacenter, the same path wordpress.org plugins use — and the
 * standard upgrader installs it. No file ever travels through the admin's own
 * connection again.
 *
 * Safety: only the `horse-tools-*.zip` asset of this repository is ever
 * accepted as a package, everything is fetched over HTTPS, and the result is
 * cached (12–16 h on success, 1 h on failure) so a GitHub outage cannot slow
 * wp-admin down.
 *
 * Politeness: the version check reads a small manifest from the release CDN
 * rather than calling the GitHub API, which is limited to 60 unauthenticated
 * requests per hour per IP address — a limit shared hosting can put many sites
 * behind. The cache carries random jitter so installations do not drift into
 * checking in unison. Both keep the traffic shaped like a few thousand sites
 * minding their own business rather than automated bulk activity.
 */

function horsetools_update_repo() {
	return 'tranduythuan/Horse-Tools';
}

/**
 * Only ever trust a package that is an asset of THIS repository's releases.
 */
function horsetools_update_package_ok( $url ) {
	$expected = 'https://github.com/' . horsetools_update_repo() . '/releases/download/';
	return is_string( $url ) && 0 === strpos( $url, $expected ) && preg_match( '~/horse-tools-[^/]+\.zip$~', $url );
}

/**
 * The small manifest each release carries, fetched from the release CDN.
 *
 * This is the normal path. It exists because the GitHub API allows only 60
 * unauthenticated requests per hour PER IP — fine for one site, but shared
 * hosting can put many sites behind one address, and a conditional request
 * does not help (tested: a 304 still counts). Release assets are served from
 * the download CDN, which that limit does not apply to, and a version manifest
 * is release metadata for our own software — the thing releases are for.
 *
 * @return array|null Same shape as the API path, or null to fall back.
 */
function horsetools_update_from_manifest() {
	$res = wp_remote_get(
		'https://github.com/' . horsetools_update_repo() . '/releases/latest/download/update.json',
		array(
			'timeout'     => 10,
			'redirection' => 5, // the fixed URL 302s to the CDN
			'headers'     => array( 'User-Agent' => 'horse-tools-updater' ),
		)
	);
	if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
		return null;
	}
	$body = wp_remote_retrieve_body( $res );
	if ( strlen( $body ) > 20000 ) {
		return null;
	}
	$data = json_decode( $body, true );
	if ( ! is_array( $data ) || empty( $data['version'] ) || empty( $data['package'] ) ) {
		return null;
	}
	if ( ! horsetools_update_package_ok( $data['package'] ) ) {
		return null;
	}
	return array(
		'version' => ltrim( (string) $data['version'], 'vV' ),
		'package' => (string) $data['package'],
		'url'     => ! empty( $data['url'] ) ? (string) $data['url'] : 'https://github.com/' . horsetools_update_repo() . '/releases',
		'body'    => isset( $data['notes'] ) ? (string) $data['notes'] : '',
	);
}

function horsetools_update_get_release() {
	$cached = get_site_transient( 'horsetools_github_release' );
	if ( is_array( $cached ) && isset( $cached['version'] ) ) {
		return $cached;
	}

	$release = array( 'version' => '', 'package' => '', 'url' => '', 'body' => '' );

	// Spread the checks out. Without the jitter every site that updated on the
	// same day would come back at the same moment for ever after, which is the
	// shape of traffic that looks like automated bulk activity rather than a
	// few thousand sites minding their own business.
	$ttl = 12 * HOUR_IN_SECONDS + wp_rand( 0, 4 * HOUR_IN_SECONDS );

	$manifest = horsetools_update_from_manifest();
	if ( is_array( $manifest ) ) {
		set_site_transient( 'horsetools_github_release', $manifest, $ttl );
		return $manifest;
	}

	// Fallback only: an older release with no manifest, or the CDN unreachable.
	$res = wp_remote_get(
		'https://api.github.com/repos/' . horsetools_update_repo() . '/releases/latest',
		array(
			'timeout' => 10,
			'headers' => array(
				'Accept'     => 'application/vnd.github+json',
				'User-Agent' => 'horse-tools-updater',
			),
		)
	);

	if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
		// Network/API failure: remember for an hour so wp-admin stays fast.
		set_site_transient( 'horsetools_github_release', $release, HOUR_IN_SECONDS );
		return $release;
	}

	$data = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( is_array( $data ) && ! empty( $data['tag_name'] ) ) {
		$version = ltrim( (string) $data['tag_name'], 'vV' );
		$package = '';
		if ( ! empty( $data['assets'] ) && is_array( $data['assets'] ) ) {
			foreach ( $data['assets'] as $asset ) {
				$name = isset( $asset['name'] ) ? (string) $asset['name'] : '';
				$url  = isset( $asset['browser_download_url'] ) ? (string) $asset['browser_download_url'] : '';
				if ( preg_match( '/^horse-tools-.+\.zip$/', $name ) && '' !== $url ) {
					$package = $url;
					break;
				}
			}
		}
		// Never accept a package that is not the official asset of THIS repo.
		if ( '' !== $version && horsetools_update_package_ok( $package ) ) {
			$release = array(
				'version' => $version,
				'package' => $package,
				'url'     => ! empty( $data['html_url'] ) ? (string) $data['html_url'] : 'https://github.com/' . horsetools_update_repo() . '/releases',
				'body'    => isset( $data['body'] ) ? (string) $data['body'] : '',
			);
		}
	}

	set_site_transient( 'horsetools_github_release', $release, $ttl );
	return $release;
}

/**
 * Tell WordPress' update system about the GitHub release. Runs both when the
 * update transient is being rebuilt (cron / Dashboard→Updates) and when it is
 * read, so the Plugins page reflects our cached knowledge immediately.
 */
function horsetools_update_inject( $transient ) {
	if ( ! is_object( $transient ) ) {
		return $transient;
	}
	$release = horsetools_update_get_release();
	if ( '' === $release['version'] || '' === $release['package'] ) {
		return $transient;
	}

	$item = (object) array(
		'id'            => 'github.com/' . horsetools_update_repo(),
		'slug'          => 'horse-tools',
		'plugin'        => HORSETOOLS_BASE,
		'new_version'   => $release['version'],
		'url'           => $release['url'],
		'package'       => $release['package'],
		'icons'         => array(),
		'banners'       => array(),
		'requires'      => '6.0',
		'requires_php'  => '8.1',
		'compatibility' => new stdClass(),
	);

	if ( version_compare( $release['version'], HORSETOOLS_VERSION, '>' ) ) {
		if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
			$transient->response = array();
		}
		$transient->response[ HORSETOOLS_BASE ] = $item;
		if ( isset( $transient->no_update[ HORSETOOLS_BASE ] ) ) {
			unset( $transient->no_update[ HORSETOOLS_BASE ] );
		}
	} else {
		// Registering in no_update keeps the "enable auto-updates" UI working.
		if ( ! isset( $transient->no_update ) || ! is_array( $transient->no_update ) ) {
			$transient->no_update = array();
		}
		$transient->no_update[ HORSETOOLS_BASE ] = $item;
		if ( isset( $transient->response[ HORSETOOLS_BASE ] ) ) {
			unset( $transient->response[ HORSETOOLS_BASE ] );
		}
	}
	return $transient;
}
add_filter( 'pre_set_site_transient_update_plugins', 'horsetools_update_inject' );
add_filter( 'site_transient_update_plugins', 'horsetools_update_inject' );

/**
 * "View details" popup for the update row — name, version and the release
 * notes straight from GitHub, so the owner can see what changed before updating.
 */
function horsetools_update_details( $result, $action, $args ) {
	if ( 'plugin_information' !== $action || empty( $args->slug ) || 'horse-tools' !== $args->slug ) {
		return $result;
	}
	$release = horsetools_update_get_release();
	if ( '' === $release['version'] ) {
		return $result;
	}
	$changelog = nl2br( esc_html( $release['body'] ) );
	return (object) array(
		'name'          => 'Horse Tools',
		'slug'          => 'horse-tools',
		'version'       => $release['version'],
		'author'        => '<a href="https://tranduythuan.com/">Trần Duy Thuận</a>',
		'homepage'      => 'https://github.com/' . horsetools_update_repo(),
		'requires'      => '6.0',
		'requires_php'  => '8.1',
		'download_link' => $release['package'],
		'sections'      => array(
			'description' => esc_html__( 'All-in-one WordPress toolkit: contact chat, shortcodes, security & privacy, media optimisation, SEO, cleanup and more — in one plugin.', 'horse-tools' ),
			'changelog'   => '' !== $changelog ? $changelog : esc_html__( 'See the GitHub release page for details.', 'horse-tools' ),
		),
	);
}
add_filter( 'plugins_api', 'horsetools_update_details', 10, 3 );

/**
 * A "Check for updates" link on the Plugins row: clears the cache, re-asks
 * GitHub right away and reports what it found. Without this the owner would
 * have to wait out the cache to see a release published minutes ago.
 */
function horsetools_update_action_link( $links ) {
	if ( current_user_can( 'update_plugins' ) ) {
		$url     = wp_nonce_url( admin_url( 'plugins.php?horsetools-check-update=1' ), 'horsetools-check-update' );
		$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Check for updates', 'horse-tools' ) . '</a>';
	}
	return $links;
}
add_filter( 'plugin_action_links_' . HORSETOOLS_BASE, 'horsetools_update_action_link' );

function horsetools_update_manual_check() {
	if ( ! isset( $_GET['horsetools-check-update'] ) ) {
		return;
	}
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}
	check_admin_referer( 'horsetools-check-update' );
	delete_site_transient( 'horsetools_github_release' );
	// Rebuild the core update transient now; our inject filter fetches fresh data.
	if ( function_exists( 'wp_clean_plugins_cache' ) ) {
		wp_clean_plugins_cache( true );
	}
	wp_update_plugins();
	wp_safe_redirect( admin_url( 'plugins.php?horsetools-update-checked=1' ) );
	exit;
}
add_action( 'admin_init', 'horsetools_update_manual_check' );

function horsetools_update_checked_notice() {
	if ( ! isset( $_GET['horsetools-update-checked'] ) || ! current_user_can( 'update_plugins' ) ) {
		return;
	}
	$release = horsetools_update_get_release();
	if ( '' === $release['version'] ) {
		$class = 'notice-warning';
		$msg   = __( 'Horse Tools could not reach GitHub to check for updates. Please try again later.', 'horse-tools' );
	} elseif ( version_compare( $release['version'], HORSETOOLS_VERSION, '>' ) ) {
		$class = 'notice-info';
		/* translators: %s: new version number */
		$msg = sprintf( __( 'Horse Tools %s is available — an update link now appears under the plugin below. WordPress will download it directly from GitHub.', 'horse-tools' ), $release['version'] );
	} else {
		$class = 'notice-success';
		/* translators: %s: current version number */
		$msg = sprintf( __( 'Horse Tools is up to date (version %s).', 'horse-tools' ), HORSETOOLS_VERSION );
	}
	echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p>' . esc_html( $msg ) . '</p></div>';
}
add_action( 'admin_notices', 'horsetools_update_checked_notice' );
