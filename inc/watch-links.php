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

/**
 * Stop growing here.
 *
 * Was 400, which a real site went straight through: a blog of 866 posts that
 * cites its sources reached the ceiling and the inventory stopped recording
 * anything new — silently, reporting "all clear" about domains it had never
 * looked at. The cap is meant to stop an option growing without bound on a site
 * that has been scraped into; it is not meant to be reachable by writing.
 *
 * Two thousand hosts is roughly 300 KB in an option that does not autoload, and
 * a site legitimately linking to more than that has bigger questions than this
 * one. When it *is* reached it now says so — see horsetools_link_truncated().
 */
const HORSETOOLS_LINK_MAX = 2000;

/** Set when the inventory could not record everything it found. */
const HORSETOOLS_LINK_FULL = 'horsetools_link_full';

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
					// Say so rather than quietly stop. An inventory that is missing
					// entries still answers "is this domain new?" — with "no" — for
					// every domain it never managed to record, which is the one
					// answer it must never give wrongly.
					update_option( HORSETOOLS_LINK_FULL, 1, false );
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
	delete_option( HORSETOOLS_LINK_FULL );
}

/**
 * Did the inventory run out of room?
 *
 * If it did, "this domain is not new" is a claim it cannot make, and every
 * screen that would otherwise say the content is clean has to say this instead.
 *
 * @return bool
 */
function horsetools_link_truncated() {
	return (bool) get_option( HORSETOOLS_LINK_FULL, false );
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
	horsetools_anchor_touch();
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
	horsetools_anchor_touch();
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
		'state'     => horsetools_link_reviewed() ? ( $new ? 'changed' : 'clean' ) : 'unset',
		'new'       => $new,
		'total'     => count( $found ),
		'truncated' => horsetools_link_truncated(),
		'progress'  => array(),
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
		if ( isset( $_POST['guard'] ) ) {
			horsetools_link_guard_set( sanitize_key( wp_unslash( $_POST['guard'] ) ) );
		}

		// Only ever the exceptions are posted, never the whole list.
		//
		// It used to post one hidden field and one checkbox per domain. On a site
		// with 686 of them that is 1372 fields, and PHP's max_input_vars stops at
		// 1000 by default and drops the rest without a word — so the owner would
		// tick everything, press save, and several hundred domains would quietly
		// stay unapproved with nothing on screen to explain it. Anything that
		// scales with the number of rows is wrong here.
		$action = isset( $_POST['do'] ) ? sanitize_key( wp_unslash( $_POST['do'] ) ) : '';
		$picked = isset( $_POST['pick'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['pick'] ) ) : array();

		if ( 'approve_all' === $action ) {
			horsetools_link_approve( array_keys( horsetools_link_found() ) );
			$done = __( 'Saved. Everything found so far is approved.', 'horse-tools' );
		} elseif ( 'approve' === $action ) {
			horsetools_link_approve( $picked );
			$done = $picked
				/* translators: %d: number of domains approved. */
				? sprintf( _n( 'Approved %d domain.', 'Approved %d domains.', count( $picked ), 'horse-tools' ), count( $picked ) )
				: __( 'Nothing was ticked, so nothing changed.', 'horse-tools' );
		} elseif ( 'revoke' === $action ) {
			horsetools_link_revoke( $picked );
			$done = $picked
				/* translators: %d: number of domains no longer approved. */
				? sprintf( _n( '%d domain is no longer approved.', '%d domains are no longer approved.', count( $picked ), 'horse-tools' ), count( $picked ) )
				: __( 'Nothing was ticked, so nothing changed.', 'horse-tools' );
		} else {
			// The guard setting on its own, and deliberately nothing else. Writing
			// the approved list here would record that a review had happened while
			// the list was still empty — and "reviewed, nothing approved" is exactly
			// the state in which the guard defuses every outbound link on the site.
			// Choosing what should happen to unapproved domains is not the same act
			// as saying which domains are approved.
			$done = __( 'Saved.', 'horse-tools' );
		}
	}

	$status   = horsetools_link_status();
	$found    = horsetools_link_found();
	$approved = horsetools_link_approved();

	// Two lists, because they are two different jobs. The short one is a decision
	// waiting to be made; the long one is a reference you occasionally look
	// something up in. Showing them as one table of 686 rows made the second bury
	// the first, which is the wrong way round — the whole feature is about the
	// handful that is not agreed yet.
	$waiting = horsetools_link_sort( array_diff_key( $found, $approved ) );
	$settled = array_intersect_key( $found, $approved );
	ksort( $settled );
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
			<?php return; ?>
		<?php endif; ?>

		<?php if ( ! $found ) : ?>
			<p><?php esc_html_e( 'Your posts and pages do not link anywhere outside this site.', 'horse-tools' ); ?></p>
			<?php return; ?>
		<?php endif; ?>

		<?php if ( horsetools_link_truncated() ) : ?>
			<div class="notice notice-error" style="max-width:46em">
				<p><strong><?php esc_html_e( 'This list is incomplete.', 'horse-tools' ); ?></strong></p>
				<p><?php
				printf(
					/* translators: %d: the maximum number of domains that can be held. */
					esc_html__( 'Your content links to more than %d domains, which is as many as can be held. Domains beyond that were not recorded, so this cannot tell you when one of them is new — it would say it was already there.', 'horse-tools' ),
					(int) HORSETOOLS_LINK_MAX
				);
				?></p>
			</div>
		<?php endif; ?>

		<p style="max-width:48em">
			<?php
			printf(
				/* translators: 1: number waiting, 2: number already approved. */
				esc_html__( 'Your content links to %1$s domains you have not agreed to, and %2$s you have. You will be told the moment one arrives that is on neither list.', 'horse-tools' ),
				'<strong>' . number_format_i18n( count( $waiting ) ) . '</strong>',
				'<strong>' . number_format_i18n( count( $settled ) ) . '</strong>'
			);
			?>
		</p>

		<?php /* ---------- waiting ---------- */ ?>
		<h2><?php esc_html_e( 'Waiting for you', 'horse-tools' ); ?></h2>

		<?php if ( ! $waiting ) : ?>
			<p><?php esc_html_e( 'Nothing. Every domain your content points at is one you have agreed to.', 'horse-tools' ); ?></p>
		<?php else : ?>
			<?php
			// One page at a time, worst first. Rendering all of them was the other
			// half of the problem: each row prints up to eight post titles, and at
			// 686 rows that is five thousand get_the_title() calls on one screen.
			$per   = 50;
			$pages = (int) ceil( count( $waiting ) / $per );
			$page  = isset( $_GET['ht_page'] ) ? max( 1, min( $pages, (int) $_GET['ht_page'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification
			$slice = array_slice( $waiting, ( $page - 1 ) * $per, $per, true );

			// Prime every post this page will name in one query instead of one each.
			$ids = array();
			foreach ( $slice as $row ) {
				foreach ( ( isset( $row['posts'] ) ? $row['posts'] : array() ) as $pid ) {
					$ids[] = (int) $pid;
				}
			}
			if ( $ids ) {
				_prime_post_caches( array_unique( $ids ), false, false );
			}
			?>
			<p style="max-width:48em">
				<?php esc_html_e( 'Least-linked first, because the one added without your knowing is almost never the one you link to from two hundred posts. Tick what belongs and approve it; leave the rest here.', 'horse-tools' ); ?>
			</p>

			<?php if ( count( $waiting ) > $per ) : ?>
				<p style="max-width:48em">
					<strong><?php esc_html_e( 'A long list on a first pass is normal and does not have to be read row by row.', 'horse-tools' ); ?></strong>
					<?php esc_html_e( 'Look down this page, deal with anything that makes you stop, and use “Approve everything” below for the rest — what matters is having a baseline, so that tomorrow’s arrival stands out against it.', 'horse-tools' ); ?>
				</p>
			<?php endif; ?>

			<form method="post">
				<?php wp_nonce_field( 'horsetools_links', 'ht_links_nonce' ); ?>
				<p>
					<button type="button" class="button" id="ht-links-all"><?php esc_html_e( 'Tick all on this page', 'horse-tools' ); ?></button>
					<button type="button" class="button" id="ht-links-none"><?php esc_html_e( 'Untick all', 'horse-tools' ); ?></button>
					<?php if ( $pages > 1 ) : ?>
						<span style="margin-left:12px">
						<?php
						printf(
							/* translators: 1: current page, 2: total pages. */
							esc_html__( 'Page %1$d of %2$d', 'horse-tools' ),
							(int) $page,
							(int) $pages
						);
						?>
						</span>
						<?php if ( $page > 1 ) : ?>
							<a class="button" href="<?php echo esc_url( add_query_arg( 'ht_page', $page - 1, horsetools_link_screen_url() ) ); ?>">&laquo;</a>
						<?php endif; ?>
						<?php if ( $page < $pages ) : ?>
							<a class="button" href="<?php echo esc_url( add_query_arg( 'ht_page', $page + 1, horsetools_link_screen_url() ) ); ?>">&raquo;</a>
						<?php endif; ?>
					<?php endif; ?>
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
					<?php foreach ( $slice as $host => $row ) : ?>
						<tr>
							<th class="check-column">
								<input type="checkbox" name="pick[]" value="<?php echo esc_attr( $host ); ?>" checked>
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
								foreach ( ( isset( $row['posts'] ) ? $row['posts'] : array() ) as $pid ) {
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
					<button type="submit" name="do" value="approve" class="button button-primary"><?php esc_html_e( 'Approve the ticked ones', 'horse-tools' ); ?></button>
					<button type="submit" name="do" value="approve_all" class="button"
						onclick="return confirm(<?php echo esc_attr( wp_json_encode( sprintf( /* translators: %d: number of domains. */ __( 'Approve all %d domains your content currently links to?', 'horse-tools' ), count( $found ) ) ) ); ?>);">
						<?php
						printf(
							/* translators: %d: number of domains. */
							esc_html__( 'Approve everything (%d)', 'horse-tools' ),
							count( $found )
						);
						?>
					</button>
				</p>
			</form>
		<?php endif; ?>

		<?php /* ---------- settled ---------- */ ?>
		<?php if ( $settled ) : ?>
			<h2><?php esc_html_e( 'Already approved', 'horse-tools' ); ?></h2>
			<form method="post">
				<?php wp_nonce_field( 'horsetools_links', 'ht_links_nonce' ); ?>
				<p>
					<label for="ht-links-find"><?php esc_html_e( 'Find a domain', 'horse-tools' ); ?></label>
					<input type="search" id="ht-links-find" class="regular-text" placeholder="<?php esc_attr_e( 'type part of a domain…', 'horse-tools' ); ?>">
					<span id="ht-links-count"></span>
				</p>
				<p class="ht-note">
					<i class="ti ti-bulb"></i>
					<?php esc_html_e( 'No post titles here on purpose — this list is long, and looking up a title for every row is what made this screen slow. Tick anything you want to take back and press the button; it moves up to “Waiting for you” with its posts listed.', 'horse-tools' ); ?>
				</p>
				<div style="max-height:420px;overflow:auto;border:1px solid #dcdcde;background:#fff">
					<table class="wp-list-table widefat striped" id="ht-links-settled">
						<tbody>
						<?php foreach ( $settled as $host => $row ) : ?>
							<tr data-host="<?php echo esc_attr( $host ); ?>">
								<th class="check-column" style="width:2.2em">
									<input type="checkbox" name="pick[]" value="<?php echo esc_attr( $host ); ?>">
								</th>
								<td><?php echo esc_html( $host ); ?></td>
								<td style="width:8em">
									<?php
									printf(
										/* translators: %d: number of links. */
										esc_html( _n( '%d link', '%d links', (int) $row['count'], 'horse-tools' ) ),
										(int) $row['count']
									);
									?>
								</td>
								<td style="width:16em">
									<?php if ( ! empty( $row['embed'] ) ) : ?>
										<span style="color:#c0392b"><?php esc_html_e( 'script or embedded frame', 'horse-tools' ); ?></span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<p class="submit">
					<button type="submit" name="do" value="revoke" class="button"><?php esc_html_e( 'Take back the ticked ones', 'horse-tools' ); ?></button>
				</p>
			</form>
		<?php endif; ?>

		<?php /* ---------- guard ---------- */ ?>
		<form method="post">
			<?php wp_nonce_field( 'horsetools_links', 'ht_links_nonce' ); ?>
			<h2 style="margin-top:26px"><?php esc_html_e( 'And what should happen to a domain that is not on the list?', 'horse-tools' ); ?></h2>
			<p style="max-width:46em">
				<?php esc_html_e( 'Everything above only tells you. Telling you leaves a gap: the link goes in, the warning appears on a screen, and the link keeps working until you log in and read it. That gap is how three old posts carried casino links for two years.', 'horse-tools' ); ?>
			</p>
			<?php $guard = horsetools_link_guard_mode(); ?>
			<p class="ht-field">
				<label style="display:block;margin:6px 0">
					<input type="radio" name="guard" value="off" <?php checked( 'off', $guard ); ?>>
					<strong><?php esc_html_e( 'Nothing — just tell me', 'horse-tools' ); ?></strong><br>
					<span class="description" style="margin-left:24px"><?php esc_html_e( 'Your pages go out exactly as written.', 'horse-tools' ); ?></span>
				</label>
				<label style="display:block;margin:6px 0">
					<input type="radio" name="guard" value="nofollow" <?php checked( 'nofollow', $guard ); ?>>
					<strong><?php esc_html_e( 'Add nofollow to it (recommended)', 'horse-tools' ); ?></strong><br>
					<span class="description" style="margin-left:24px"><?php esc_html_e( 'The link still works for a reader and stops passing any SEO value — which is the whole reason a link like that is worth paying for. Nothing in your posts is changed; only what gets printed.', 'horse-tools' ); ?></span>
				</label>
				<label style="display:block;margin:6px 0">
					<input type="radio" name="guard" value="strip" <?php checked( 'strip', $guard ); ?>>
					<strong><?php esc_html_e( 'Take the link away, keep the words', 'horse-tools' ); ?></strong><br>
					<span class="description" style="margin-left:24px"><?php esc_html_e( 'Nobody can click through. Stronger, and more likely to get in your way — a domain you meant to link to but forgot to tick stops being a link until you tick it.', 'horse-tools' ); ?></span>
				</label>
			</p>
			<p class="ht-note">
				<i class="ti ti-bulb"></i>
				<?php esc_html_e( 'This never touches what is stored. Switch it off and every link is back as it was on the next page load.', 'horse-tools' ); ?>
			</p>
			<p class="submit">
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Save this choice', 'horse-tools' ); ?></button>
			</p>
		</form>
	</div>
	<script>
	document.addEventListener('DOMContentLoaded', function () {
		var page = document.querySelectorAll('form input[name="pick[]"]');
		var set = function (v) {
			var rows = document.querySelectorAll('#ht-links-settled');
			var boxes = document.querySelectorAll('input[name="pick[]"]'), i;
			for (i = 0; i < boxes.length; i++) {
				if (rows.length && rows[0].contains(boxes[i])) { continue; }
				boxes[i].checked = v;
			}
		};
		var a = document.getElementById('ht-links-all');
		var n = document.getElementById('ht-links-none');
		if (a) { a.addEventListener('click', function () { set(true); }); }
		if (n) { n.addEventListener('click', function () { set(false); }); }

		var find = document.getElementById('ht-links-find');
		var out  = document.getElementById('ht-links-count');
		var tbl  = document.getElementById('ht-links-settled');
		if (find && tbl) {
			var rows = tbl.querySelectorAll('tbody tr');
			find.addEventListener('input', function () {
				var q = find.value.toLowerCase().trim(), shown = 0, i;
				for (i = 0; i < rows.length; i++) {
					var hit = !q || rows[i].getAttribute('data-host').indexOf(q) > -1;
					rows[i].style.display = hit ? '' : 'none';
					if (hit) { shown++; }
				}
				if (out) { out.textContent = q ? (shown + '/' + rows.length) : ''; }
			});
		}
	});
	</script>
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
