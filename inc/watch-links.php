<?php
/**
 * Horse Tools — a directory of everywhere the site's content points.
 *
 * This is the one that would have caught it. An administrator account nobody
 * recognised edited three old posts and left casino links in them, and the site
 * ran that way for over two years. Nothing was broken, nothing looked wrong, and
 * no amount of reading the dashboard would ever have shown it, because the
 * dashboard has never had a list of who the site links to.
 *
 * The unit is the host, not the link. A page with forty links to eight hosts is
 * eight decisions, not forty, and a person will make eight. It also survives the
 * obvious evasion: changing the path, the anchor text or the tracking parameters
 * every week does not change the host, and the host is the thing being paid for.
 *
 * Nothing here classifies anything. There is no list of bad domains in this
 * plugin and there is not going to be one — a keyword list is out of date the
 * week it ships, and a plugin that comes preloaded with gambling vocabulary is a
 * plugin that gets flagged for containing gambling vocabulary. What this does
 * instead is what the site owner cannot do by hand: hold the complete set, so
 * that "one that was not there yesterday" becomes a question a computer can ask.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

const HORSETOOLS_LINK_HOSTS = 'horsetools_link_hosts';
const HORSETOOLS_LINK_OK    = 'horsetools_link_approved';

/** Stop growing here. Past this it is not an inventory, it is a scrape. */
const HORSETOOLS_LINK_MAX = 400;

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
 * Every outbound host in a piece of markup.
 *
 * @param string   $html
 * @param string[] $self Hosts belonging to this site.
 * @return array<string,array> Keyed by host.
 */
function horsetools_link_extract( $html, array $self = array() ) {
	$html = (string) $html;
	$out  = array();
	$self = array_flip( $self );

	$add = function ( $url, $kind, $anchor = '', $dofollow = false ) use ( &$out, $self ) {
		$host = horsetools_link_host( $url );
		if ( '' === $host || isset( $self[ $host ] ) ) {
			return;
		}
		if ( ! isset( $out[ $host ] ) ) {
			$out[ $host ] = array(
				'host'     => $host,
				'count'    => 0,
				'anchor'   => '',
				'link'     => 0,
				'embed'    => 0,
				'dofollow' => 0,
			);
		}
		$out[ $host ]['count']++;
		$out[ $host ][ $kind ]++;
		if ( $dofollow ) {
			$out[ $host ]['dofollow']++;
		}
		// Keep the first non-empty anchor text. It is the single most useful thing
		// on the review screen: "read more" tells you nothing, and the phrase a
		// paid link was bought for tells you everything in one glance.
		if ( '' === $out[ $host ]['anchor'] && '' !== $anchor ) {
			$out[ $host ]['anchor'] = $anchor;
		}
	};

	$attr = function ( $attrs, $name ) {
		if ( preg_match( '~\b' . $name . '\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))~i', $attrs, $m ) ) {
			return html_entity_decode( '' !== $m[1] ? $m[1] : ( isset( $m[2] ) && '' !== $m[2] ? $m[2] : ( isset( $m[3] ) ? $m[3] : '' ) ), ENT_QUOTES );
		}
		return '';
	};

	// Anchor text, collected first and separately. Pairing `<a>` with `</a>` is
	// the only way to read it, and that pairing fails on markup with an
	// unclosed anchor — which is exactly the markup a careless injection leaves
	// behind. So the text is a lookup that may come up empty, and counting the
	// links below never depends on it.
	$texts = array();
	if ( preg_match_all( '~<a\b([^>]*)>(.*?)</a>~is', $html, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $hit ) {
			$host = horsetools_link_host( $attr( $hit[1], 'href' ) );
			if ( '' === $host || isset( $texts[ $host ] ) ) {
				continue;
			}
			$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $hit[2] ) ) );
			if ( '' !== $text ) {
				$texts[ $host ] = function_exists( 'mb_substr' ) ? mb_substr( $text, 0, 80 ) : substr( $text, 0, 80 );
			}
		}
	}

	// Every opening anchor tag, closed or not.
	if ( preg_match_all( '~<a\b([^>]*)>~i', $html, $m ) ) {
		foreach ( $m[1] as $attrs ) {
			$href = $attr( $attrs, 'href' );
			if ( '' === $href ) {
				continue;
			}
			$rel      = strtolower( $attr( $attrs, 'rel' ) );
			$dofollow = ( false === strpos( $rel, 'nofollow' ) && false === strpos( $rel, 'ugc' ) && false === strpos( $rel, 'sponsored' ) );
			$host     = horsetools_link_host( $href );
			$add( $href, 'link', isset( $texts[ $host ] ) ? $texts[ $host ] : '', $dofollow );
		}
	}

	// Things the browser fetches and runs. A foreign <script src> in a post body
	// is a different and worse finding than a link, and it only gets there if
	// whoever wrote it had unfiltered_html — which is to say, was an
	// administrator.
	if ( preg_match_all( '~<(?:script|iframe|embed|source|object)\b([^>]*)>~i', $html, $m ) ) {
		foreach ( $m[1] as $attrs ) {
			foreach ( array( 'src', 'data' ) as $name ) {
				$url = $attr( $attrs, $name );
				if ( '' !== $url ) {
					$add( $url, 'embed' );
				}
			}
		}
	}
	if ( preg_match_all( '~<link\b([^>]*)>~i', $html, $m ) ) {
		foreach ( $m[1] as $attrs ) {
			$url = $attr( $attrs, 'href' );
			if ( '' !== $url ) {
				$add( $url, 'embed' );
			}
		}
	}

	// A bare URL on its own line, which WordPress turns into a link or an embed
	// on the way out. Run against the text with the markup taken away, so the
	// href values already counted above are not counted a second time.
	$text = wp_strip_all_tags( $html );
	if ( preg_match_all( '~\bhttps?://[^\s<>"\'\)\]]+~i', $text, $m ) ) {
		foreach ( $m[0] as $url ) {
			$add( rtrim( $url, '.,;:' ), 'link' );
		}
	}

	return $out;
}

/* -------------------------------------------------------------------------
 * Collecting
 * ---------------------------------------------------------------------- */

add_filter( 'horsetools_scan_collectors', 'horsetools_link_collector' );
function horsetools_link_collector( $c ) {
	$c['links'] = array(
		'batch' => 'horsetools_link_collect',
		'reset' => 'horsetools_link_collect_reset',
	);
	return $c;
}

/**
 * @param array $rows Rows with ID, post_title, post_excerpt, post_content.
 */
function horsetools_link_collect( array $rows ) {
	$found = get_option( HORSETOOLS_LINK_HOSTS, array() );
	$found = is_array( $found ) ? $found : array();
	$self  = horsetools_link_self_hosts();

	foreach ( $rows as $row ) {
		foreach ( horsetools_link_extract( (string) $row->post_content, $self ) as $host => $hit ) {
			if ( ! isset( $found[ $host ] ) ) {
				if ( count( $found ) >= HORSETOOLS_LINK_MAX ) {
					continue;
				}
				// An empty row, not a zeroed copy of $hit: zeroing $hit itself
				// would throw away the counts about to be added, so the first post
				// a domain appeared in contributed nothing to its total.
				$found[ $host ] = array(
					'host'     => $host,
					'count'    => 0,
					'anchor'   => '',
					'link'     => 0,
					'embed'    => 0,
					'dofollow' => 0,
					'posts'    => array(),
				);
			}
			foreach ( array( 'count', 'link', 'embed', 'dofollow' ) as $k ) {
				$found[ $host ][ $k ] += $hit[ $k ];
			}
			if ( '' === $found[ $host ]['anchor'] && '' !== $hit['anchor'] ) {
				$found[ $host ]['anchor'] = $hit['anchor'];
			}
			if ( count( $found[ $host ]['posts'] ) < 8 && ! in_array( (int) $row->ID, $found[ $host ]['posts'], true ) ) {
				$found[ $host ]['posts'][] = (int) $row->ID;
			}
		}
	}
	update_option( HORSETOOLS_LINK_HOSTS, $found, false );
}

function horsetools_link_collect_reset() {
	update_option( HORSETOOLS_LINK_HOSTS, array(), false );
}

/* -------------------------------------------------------------------------
 * Approving
 * ---------------------------------------------------------------------- */

/** Hosts found in content. Empty until the walk has read everything. */
function horsetools_link_found() {
	if ( ! horsetools_scan_finished() ) {
		return array();
	}
	$f = get_option( HORSETOOLS_LINK_HOSTS, array() );
	return is_array( $f ) ? $f : array();
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

function horsetools_link_reviewed() {
	return is_array( get_option( HORSETOOLS_LINK_OK, null ) );
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

/**
 * @param string[] $hosts
 */
function horsetools_link_approve( array $hosts ) {
	$approved = horsetools_link_approved();
	$now      = time();
	foreach ( $hosts as $host ) {
		$host = horsetools_link_host_input( $host );
		if ( '' !== $host && ! isset( $approved[ $host ] ) ) {
			$approved[ $host ] = $now;
		}
	}
	update_option( HORSETOOLS_LINK_OK, $approved, false );
}

/** @param string[] $hosts */
function horsetools_link_revoke( array $hosts ) {
	$approved = horsetools_link_approved();
	foreach ( $hosts as $host ) {
		$host = horsetools_link_host_input( $host );
		if ( '' !== $host ) {
			unset( $approved[ $host ] );
		}
	}
	update_option( HORSETOOLS_LINK_OK, $approved, false );
}

/**
 * @return array{state:string,new:array,total:int,progress:array}
 *         state: 'scanning' | 'unset' | 'clean' | 'changed'
 */
function horsetools_link_status() {
	// Not memoised. The settings watcher has to be, because answering means
	// walking every option group; this one is two option reads and an array_diff
	// over a few dozen keys, and a stale answer held across an approval is worse
	// than the work it saves.
	if ( ! horsetools_scan_finished() ) {
		return array( 'state' => 'scanning', 'new' => array(), 'total' => 0, 'progress' => horsetools_scan_progress() );
	}
	$found = horsetools_link_found();
	$new   = array_diff_key( $found, horsetools_link_approved() );
	return array(
		'state'    => horsetools_link_reviewed() ? ( $new ? 'changed' : 'clean' ) : 'unset',
		'new'      => $new,
		'total'    => count( $found ),
		'progress' => array(),
	);
}

/**
 * Least-linked first.
 *
 * The ordering is the whole review. A host reached from one post by one link is
 * the interesting one; a host reached from two hundred posts is the payment
 * gateway or the shipping company, and the owner already knows. Putting the
 * long tail at the top means the odd one out is the first thing on screen
 * instead of page four — and it needs no list of known-good domains to do it,
 * which is the point.
 *
 * @param array<string,array> $hosts
 * @return array<string,array>
 */
function horsetools_link_sort( array $hosts ) {
	uasort( $hosts, function ( $a, $b ) {
		$pa = isset( $a['posts'] ) ? count( $a['posts'] ) : 0;
		$pb = isset( $b['posts'] ) ? count( $b['posts'] ) : 0;
		if ( $pa !== $pb ) {
			return $pa <=> $pb;
		}
		if ( $a['count'] !== $b['count'] ) {
			return $a['count'] <=> $b['count'];
		}
		return strcmp( $a['host'], $b['host'] );
	} );
	return $hosts;
}

/* -------------------------------------------------------------------------
 * The review screen
 * ---------------------------------------------------------------------- */

/**
 * A visible entry in the menu, not a page you can only be sent to.
 *
 * It was hidden at first — registered under the plugin menu and then taken back
 * out with remove_submenu_page(), the usual trick for a screen you arrive at
 * from a warning. It does not work: WordPress decides whether you may open
 * admin.php?page=… by looking the slug up in $submenu and reading the capability
 * off the entry it finds. Take the entry away and the lookup fails, and every
 * visit — including the administrator's — is answered with "Sorry, you are not
 * allowed to access this page."
 *
 * Which is just as well. "Where does my content link to?" is a question that had
 * no answer anywhere in this dashboard, and the whole point of the feature is
 * that it now has one; burying the answer behind a warning that only appears
 * when something is already wrong would give most of it back.
 */
add_action( 'admin_menu', 'horsetools_link_menu', 100 );
function horsetools_link_menu() {
	if ( function_exists( 'horsetools_group_menu' ) ) {
		horsetools_group_menu( 'links', __( 'Outbound links', 'horse-tools' ), 'ti-external-link', 'horsetools_link_screen', 40 );
		return;
	}
	add_submenu_page(
		'horsetools-options',
		__( 'Outbound links', 'horse-tools' ),
		__( 'Outbound links', 'horse-tools' ),
		'manage_options',
		'horsetools-links',
		'horsetools_link_screen'
	);
}

function horsetools_link_screen_url() {
	return admin_url( 'admin.php?page=horsetools-links' );
}

function horsetools_link_screen() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$done = '';
	if ( isset( $_POST['ht_links_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ht_links_nonce'] ) ), 'horsetools_links' ) ) {
		$ticked = isset( $_POST['ok'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['ok'] ) ) : array();
		$listed = isset( $_POST['seen'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['seen'] ) ) : array();

		// Approve what was ticked and un-approve what was on the page and was not.
		// Only what was on the page: a host discovered after this form was rendered
		// must not be silently un-approved by a decision nobody made about it.
		// Approving writes the option even when nothing was ticked, which is what
		// records that a review happened at all — "I looked, and none of these
		// belong" is an answer, and the screen must stop asking for a first review
		// once it has been given one.
		horsetools_link_approve( $ticked );
		horsetools_link_revoke( array_values( array_diff( $listed, $ticked ) ) );
		$left = count( array_diff( $listed, $ticked ) );
		$done = $left
			/* translators: %d: number of domains left unapproved. */
			? sprintf( _n( 'Saved. %d domain is still not approved.', 'Saved. %d domains are still not approved.', $left, 'horse-tools' ), $left )
			: __( 'Saved. Everything on this page is approved.', 'horse-tools' );
	}

	$status   = horsetools_link_status();
	$found    = horsetools_link_sort( horsetools_link_found() );
	$approved = horsetools_link_approved();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Where your content links to', 'horse-tools' ); ?></h1>
		<hr class="wp-header-end">

		<?php if ( '' !== $done ) : ?>
			<div class="notice notice-success"><p><?php echo esc_html( $done ); ?></p></div>
		<?php endif; ?>

		<?php if ( 'scanning' === $status['state'] ) : ?>
			<p>
				<?php
				$p = $status['progress'];
				echo esc_html(
					$p['total']
						/* translators: 1: posts read so far, 2: posts in total. */
						? sprintf( __( 'Still reading your content: %1$d of %2$d. Come back in a minute.', 'horse-tools' ), $p['read'], $p['total'] )
						: __( 'Still reading your content. Come back in a minute.', 'horse-tools' )
				);
				?>
			</p>
		<?php elseif ( ! $found ) : ?>
			<p><?php esc_html_e( 'Your posts and pages do not link anywhere outside this site.', 'horse-tools' ); ?></p>
		<?php else : ?>
			<p style="max-width:46em">
				<?php esc_html_e( 'Every other website your posts and pages point at, one row per domain. Untick anything you do not recognise, then save — you will be told the moment a domain that is not on this list turns up in your content.', 'horse-tools' ); ?>
			</p>
			<p style="max-width:46em">
				<?php esc_html_e( 'Rarely-linked domains are listed first, because the one that was added without your knowing is almost never the one you link to from two hundred posts.', 'horse-tools' ); ?>
			</p>

			<form method="post">
				<?php wp_nonce_field( 'horsetools_links', 'ht_links_nonce' ); ?>
				<p>
					<button type="button" class="button" id="ht-links-all"><?php esc_html_e( 'Tick all', 'horse-tools' ); ?></button>
					<button type="button" class="button" id="ht-links-none"><?php esc_html_e( 'Untick all', 'horse-tools' ); ?></button>
				</p>
				<table class="wp-list-table widefat striped">
					<thead>
					<tr>
						<td class="check-column"></td>
						<th><?php esc_html_e( 'Domain', 'horse-tools' ); ?></th>
						<th><?php esc_html_e( 'Links', 'horse-tools' ); ?></th>
						<th><?php esc_html_e( 'In', 'horse-tools' ); ?></th>
						<th><?php esc_html_e( 'Link text', 'horse-tools' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $found as $host => $row ) : ?>
						<?php
						// Before the first review nothing is approved yet, so every box
						// would start empty and agreeing to a site's own forty domains
						// would mean forty clicks. That is a screen people close. The
						// first pass starts ticked and asks to have the odd one taken
						// out — which is the shape of the answer anyway, and the odd one
						// is already at the top. After that the stored decision rules,
						// so a domain that turned up later starts unticked and stays
						// visible until somebody says otherwise.
						$is_ok = horsetools_link_reviewed() ? isset( $approved[ $host ] ) : true;
						$posts = isset( $row['posts'] ) ? $row['posts'] : array();
						?>
						<tr<?php echo $is_ok ? '' : ' style="background:#fdecea"'; ?>>
							<th class="check-column">
								<input type="hidden" name="seen[]" value="<?php echo esc_attr( $host ); ?>">
								<input type="checkbox" name="ok[]" value="<?php echo esc_attr( $host ); ?>" <?php checked( $is_ok ); ?>>
							</th>
							<td>
								<strong><?php echo esc_html( $host ); ?></strong>
								<?php if ( ! empty( $row['embed'] ) ) : ?>
									<br><span style="color:#c0392b"><?php esc_html_e( 'Loads a script or an embedded frame from this domain', 'horse-tools' ); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<?php echo (int) $row['count']; ?>
								<?php if ( ! empty( $row['dofollow'] ) ) : ?>
									<br><small><?php esc_html_e( 'passes SEO value', 'horse-tools' ); ?></small>
								<?php endif; ?>
							</td>
							<td>
								<?php
								foreach ( $posts as $pid ) {
									$title = get_the_title( $pid );
									printf(
										'<a href="%s">%s</a><br>',
										esc_url( (string) get_edit_post_link( $pid ) ),
										esc_html( '' !== $title ? $title : '#' . $pid )
									);
								}
								?>
							</td>
							<td><?php echo esc_html( isset( $row['anchor'] ) ? $row['anchor'] : '' ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<p class="submit">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Save this list', 'horse-tools' ); ?></button>
				</p>
			</form>
			<script>
			document.addEventListener('DOMContentLoaded',function(){
				var set=function(v){
					var b=document.querySelectorAll('input[name="ok[]"]'),i;
					for(i=0;i<b.length;i++){ b[i].checked=v; }
				};
				var a=document.getElementById('ht-links-all');
				var n=document.getElementById('ht-links-none');
				if(a){ a.addEventListener('click',function(){set(true);}); }
				if(n){ n.addEventListener('click',function(){set(false);}); }
			});
			</script>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Say something on the plugin's own screens, and say it once.
 *
 * The first review is an invitation, not an alarm. A domain arriving afterwards
 * is an alarm, because by then the complete set has been agreed and something
 * added itself to it.
 */
function horsetools_link_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! horsetools_is_plugin_screen() ) {
		return;
	}
	if ( 'horsetools-links' === horsetools_current_admin_page() ) {
		return;
	}
	$s = horsetools_link_status();
	if ( 'clean' === $s['state'] || 'scanning' === $s['state'] ) {
		return;
	}

	$link = '<a href="' . esc_url( horsetools_link_screen_url() ) . '" class="button button-primary">'
		. esc_html__( 'Review the list', 'horse-tools' ) . '</a>';

	ob_start();
	if ( 'unset' === $s['state'] ) {
		echo '<p><strong>' . esc_html__( 'Horse Tools can tell you when your content starts linking somewhere new.', 'horse-tools' ) . '</strong></p>';
		echo '<p>' . sprintf(
			/* translators: %d: number of domains. */
			esc_html( _n( 'Your posts and pages link to %d other domain. Go through it once and confirm which ones belong there; after that, a domain that was not on the list is worth one sentence on your screen instead of two years of nobody noticing.', 'Your posts and pages link to %d other domains. Go through it once and confirm which ones belong there; after that, a domain that was not on the list is worth one sentence on your screen instead of two years of nobody noticing.', $s['total'], 'horse-tools' ) ),
			(int) $s['total']
		) . '</p>';
	} else {
		echo '<p><strong>' . sprintf(
			/* translators: %d: number of domains. */
			esc_html( _n( 'Your content links to %d domain you have not approved.', 'Your content links to %d domains you have not approved.', count( $s['new'] ), 'horse-tools' ) ),
			count( $s['new'] )
		) . '</strong></p>';
		$names = array_slice( array_keys( $s['new'] ), 0, 6 );
		echo '<p>' . esc_html( implode( ' · ', $names ) )
			. ( count( $s['new'] ) > count( $names ) ? ' …' : '' ) . '</p>';
	}
	echo '<p>' . $link . '</p>';
	horsetools_admin_banner( 'unset' === $s['state'] ? 'info' : 'bad', ob_get_clean() );
}
add_action( 'admin_notices', 'horsetools_link_notice' );
