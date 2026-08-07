<?php
/**
 * Horse Tools — watch the site's own contact details for changes.
 *
 * Every other detector in this area is about links: who the site points at, and
 * whether somebody added a link nobody meant to add. For a shop that is the
 * wrong thing to guard hardest. Changing the hotline in the floating chat
 * button — one value, in one option, on every page — takes the customers
 * directly, needs no SEO, leaves no link to find, and is worth far more to
 * whoever does it than a paid casino link ever was.
 *
 * So: take the set of contact identifiers the site currently publishes, and say
 * something when that set changes. Deliberately change-based, not
 * classification-based. Trying to decide which of the numbers on a page "is the
 * hotline" means guessing, and guessing wrong on prices and product codes. Not
 * guessing means the first scan records whatever is there, the owner confirms
 * it once, and after that any difference is worth a sentence.
 *
 * This first pass watches the plugin's own settings — the chat buttons, the
 * navbar, the services panel. They are the highest-value target because they
 * appear on every page, and the cheapest to check because they are a handful of
 * options rather than eight hundred posts. Post content comes next.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

const HORSETOOLS_CONTACT_BASELINE = 'horsetools_contact_baseline';

/**
 * A Vietnamese phone number reduced to one canonical form, or '' if the string
 * is not one.
 *
 * `0988.34.34.12`, `0988 34 34 12`, `+84988343412` and `84988343412` are the
 * same number and must compare equal, or every reformatting of the same
 * number reads as a change.
 *
 * The length test is what keeps prices and order numbers out: `1.790.000`
 * reduces to seven digits and is rejected, a 13-digit reference is rejected,
 * and what is left has to start with a zero the way every Vietnamese number
 * written for a Vietnamese reader does.
 *
 * @param string $raw
 * @return string Canonical `0XXXXXXXXX`, or ''.
 */
function horsetools_contact_phone( $raw ) {
	$s = trim( (string) $raw );

	// The string has to look like somebody wrote a phone number — digits and
	// the punctuation people put between them, nothing else.
	//
	// Without this the digits get scraped out of anything at all. A custom chat
	// icon is stored as inline SVG, and `viewBox="0 0 24 24" … stroke-width="1.8"
	// … d="M4 5h16v10H8l-4 4z"` reduces to 00242418451610844: seventeen digits
	// beginning 00, which is exactly the shape of an international number. It
	// was reported as the shop's phone number, with the whole SVG printed as the
	// value. Scraping digits out of arbitrary text is the wrong instinct; a
	// phone number is a short string that contains nothing but a phone number.
	if ( ! preg_match( '/^[+0-9()\[\]. \t-]{8,24}$/', $s ) ) {
		return '';
	}

	$compact = preg_replace( '/[^\d+]/', '', $s );
	if ( '' === $compact ) {
		return '';
	}

	// A leading + or 00 is somebody stating that this is a phone number, the
	// same way tel: does. Without one, only the 0… form a Vietnamese reader
	// would write counts — that is what keeps prices and order references out.
	//
	// Foreign numbers have to be recognised, not discarded. Discarding them
	// meant that replacing the hotline with an overseas number was noticed only
	// because the old one had gone, and that *adding* one was not noticed at all.
	$intl = false;
	if ( '+' === $compact[0] ) {
		$intl = true;
		$d    = substr( $compact, 1 );
	} elseif ( 0 === strpos( $compact, '00' ) ) {
		$intl = true;
		$d    = substr( $compact, 2 );
	} else {
		$d = $compact;
	}
	$d = preg_replace( '/\D+/', '', $d );
	if ( '' === $d ) {
		return '';
	}

	// +84…, 0084… and a bare 84… are the same number as the 0… form, and must
	// collapse to it or the hotline reads as two different numbers.
	if ( 0 === strpos( $d, '84' ) && ( $intl || 11 === strlen( $d ) || 12 === strlen( $d ) ) ) {
		$d    = '0' . substr( $d, 2 );
		$intl = false;
	}

	if ( $intl ) {
		$len = strlen( $d );
		return ( $len >= 8 && $len <= 15 ) ? '+' . $d : '';
	}

	if ( 0 !== strpos( $d, '0' ) ) {
		return '';
	}
	$len = strlen( $d );
	return ( $len >= 10 && $len <= 11 ) ? $d : '';
}

/**
 * Every contact identifier in a piece of text.
 *
 * Anchored on the things that can only be contact details — `tel:`, `mailto:`,
 * and the messenger link shapes — plus bare phone numbers, which are where the
 * false positives live and are therefore reported with their surrounding text
 * so a human can dismiss them in one look.
 *
 * @param string $text
 * @return array<string,array> Keyed by identity, so the same number found five
 *                             times collapses to one entry with count 5.
 */
function horsetools_contact_extract( $text ) {
	$text = (string) $text;
	$out  = array();

	$put = function ( $type, $value, $raw ) use ( &$out ) {
		if ( '' === $value ) {
			return;
		}
		$id = $type . ':' . $value;
		if ( isset( $out[ $id ] ) ) {
			$out[ $id ]['count']++;
			return;
		}
		$out[ $id ] = array(
			'type'  => $type,
			'value' => $value,
			'raw'   => $raw,
			'count' => 1,
		);
	};

	// tel: and mailto: are unambiguous — someone wrote them to be dialled.
	if ( preg_match_all( '~tel:([+0-9().\s-]{8,20})~i', $text, $m ) ) {
		foreach ( $m[1] as $hit ) {
			$put( 'phone', horsetools_contact_phone( $hit ), trim( $hit ) );
		}
	}
	if ( preg_match_all( '~mailto:([^\s"\'<>?]+@[^\s"\'<>?]+)~i', $text, $m ) ) {
		foreach ( $m[1] as $hit ) {
			$put( 'email', strtolower( rtrim( $hit, '.' ) ), $hit );
		}
	}

	// Messenger links. The id may be a username or a number; either way it is
	// the thing that decides whose inbox the customer lands in.
	$links = array(
		'zalo'      => '~zalo\.me/([A-Za-z0-9._-]{3,40})~i',
		'messenger' => '~(?:m\.me|messenger\.com/t)/([A-Za-z0-9._-]{3,40})~i',
		'whatsapp'  => '~wa\.me/(\d{8,15})~i',
		'telegram'  => '~t\.me/([A-Za-z0-9_]{3,40})~i',
	);
	foreach ( $links as $type => $re ) {
		if ( preg_match_all( $re, $text, $m ) ) {
			foreach ( $m[1] as $hit ) {
				$value = ( 'zalo' === $type || 'whatsapp' === $type )
					? ( horsetools_contact_phone( $hit ) ?: strtolower( $hit ) )
					: strtolower( $hit );
				$put( $type, $value, $hit );
			}
		}
	}

	// Email written as text rather than as a link.
	if ( preg_match_all( '~[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}~', $text, $m ) ) {
		foreach ( $m[0] as $hit ) {
			$put( 'email', strtolower( rtrim( $hit, '.' ) ), $hit );
		}
	}

	// A bare number written for a Vietnamese reader: 0988.34.34.12,
	// 0988 34 34 12, 0988343412. Bounded so it cannot start mid-way through a
	// longer run of digits.
	//
	// Run against the text with the link forms above taken out. Otherwise
	// `tel:0988.34.34.12` is found twice — once as a link and once as the plain
	// number inside it — and the same number appears to be used twice as often
	// as it is.
	$bare = preg_replace(
		array(
			'~tel:[+0-9().\s-]{8,20}~i',
			'~mailto:[^\s"\'<>?]+~i',
			'~(?:zalo\.me|m\.me|messenger\.com/t|wa\.me|t\.me)/[A-Za-z0-9._-]+~i',
		),
		' ',
		$text
	);
	if ( preg_match_all( '~(?<![0-9])(0[0-9][0-9. ]{7,16}[0-9])(?![0-9])~', $bare, $m ) ) {
		foreach ( $m[1] as $hit ) {
			$put( 'phone', horsetools_contact_phone( $hit ), trim( $hit ) );
		}
	}

	// The same, written for someone abroad. The + is what makes this safe to
	// look for: a run of digits with a plus in front of it is a phone number
	// and almost nothing else. Anything too short to be one — a price written
	// "+1.790.000" — is dropped by the length check in the normaliser.
	if ( preg_match_all( '~(?<![0-9+])(\+\d[\d. -]{6,18}\d)(?![0-9])~', $bare, $m ) ) {
		foreach ( $m[1] as $hit ) {
			$put( 'phone', horsetools_contact_phone( $hit ), trim( $hit ) );
		}
	}

	return $out;
}

/**
 * Contact identifiers currently in the plugin's own settings.
 *
 * Walks the option groups rather than naming the keys. The chat buttons store
 * their value in `chat-nut3<n>`, the navbar and the services panel elsewhere,
 * and those names have moved before — a watcher that has to be told each key
 * is a watcher that goes quiet the next time one is renamed.
 *
 * @return array<string,array>
 */
function horsetools_contact_scan_settings() {
	$found = array();

	$walk = function ( $value ) use ( &$walk, &$found ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $v ) {
				$walk( $v );
			}
			return;
		}
		if ( ! is_string( $value ) || '' === $value ) {
			return;
		}

		$rows = horsetools_contact_extract( $value );

		// A settings value is usually the identifier itself rather than prose
		// containing one: chat-nut3<n> holds "0838216168" or "+84 838 216 168",
		// not a sentence and not a link. The text scan above looks for a number
		// starting with a zero, so the international form — which has no zero in
		// it at all — slips past and the number reads as having been removed.
		$whole = horsetools_contact_phone( trim( $value ) );
		if ( '' !== $whole && ! isset( $rows[ 'phone:' . $whole ] ) ) {
			$rows[ 'phone:' . $whole ] = array(
				'type'  => 'phone',
				'value' => $whole,
				'raw'   => trim( $value ),
				'count' => 1,
			);
		}

		foreach ( $rows as $id => $row ) {
			if ( isset( $found[ $id ] ) ) {
				$found[ $id ]['count'] += $row['count'];
			} else {
				$row['where']  = 'settings';
				$found[ $id ]  = $row;
			}
		}
	};

	foreach ( horsetools_option_names() as $name ) {
		$walk( get_option( $name, array() ) );
	}
	// The services panel keeps its own option outside that map.
	$walk( get_option( 'horsetools_services', array() ) );

	return $found;
}

/**
 * The confirmed set.
 *
 * Kept in an option for now. When the anchor file lands this becomes the one
 * place to change: a baseline a database-level attacker can edit is a baseline
 * they can teach to accept their own number.
 *
 * @return array<string,array>
 */
function horsetools_contact_baseline() {
	$b = get_option( HORSETOOLS_CONTACT_BASELINE, null );
	return is_array( $b ) ? $b : array();
}

/** @param array<string,array> $set */
function horsetools_contact_baseline_set( array $set ) {
	update_option( HORSETOOLS_CONTACT_BASELINE, $set, false );
}

/** Has the owner confirmed a set yet? */
function horsetools_contact_has_baseline() {
	return is_array( get_option( HORSETOOLS_CONTACT_BASELINE, null ) );
}

/**
 * What changed between the confirmed set and what is there now.
 *
 * @param array<string,array> $now
 * @param array<string,array> $base
 * @return array{added:array,removed:array}
 */
function horsetools_contact_diff( array $now, array $base ) {
	return array(
		'added'   => array_diff_key( $now, $base ),
		'removed' => array_diff_key( $base, $now ),
	);
}

/**
 * Human wording for a channel.
 *
 * @param string $type
 * @return string
 */
function horsetools_contact_type_label( $type ) {
	// The four brand names are not translated — they are the same word in every
	// language, and putting them through the translation table only invites a
	// well-meaning translator to change one and break nothing but recognition.
	$map = array(
		'phone'     => __( 'Phone number', 'horse-tools' ),
		'email'     => __( 'Email address', 'horse-tools' ),
		'zalo'      => 'Zalo',
		'messenger' => 'Messenger',
		'whatsapp'  => 'WhatsApp',
		'telegram'  => 'Telegram',
	);
	return isset( $map[ $type ] ) ? $map[ $type ] : $type;
}

/**
 * The current state, computed at most once an hour.
 *
 * Scanning the option groups is cheap, but it is not free and nothing about the
 * answer changes between two page loads a second apart.
 *
 * @return array{state:string,added:array,removed:array,count:int}
 *         state: 'unset' | 'clean' | 'changed'
 */
function horsetools_contact_status() {
	static $memo = null;
	if ( null !== $memo ) {
		return $memo;
	}

	$now = get_transient( 'horsetools_contact_now' );
	if ( ! is_array( $now ) ) {
		$now = horsetools_contact_scan_settings();
		set_transient( 'horsetools_contact_now', $now, HOUR_IN_SECONDS );
	}

	if ( ! horsetools_contact_has_baseline() ) {
		$memo = array( 'state' => 'unset', 'added' => array(), 'removed' => array(), 'count' => count( $now ) );
		return $memo;
	}

	$diff  = horsetools_contact_diff( $now, horsetools_contact_baseline() );
	$state = ( $diff['added'] || $diff['removed'] ) ? 'changed' : 'clean';
	$memo  = array(
		'state'   => $state,
		'added'   => $diff['added'],
		'removed' => $diff['removed'],
		'count'   => count( $now ),
	);
	return $memo;
}

/** Confirm what is there now as the set to compare against. */
function horsetools_contact_confirm() {
	delete_transient( 'horsetools_contact_now' );
	horsetools_contact_baseline_set( horsetools_contact_scan_settings() );
}

add_action( 'wp_ajax_horsetools_contact_confirm', 'horsetools_contact_confirm_ajax' );
function horsetools_contact_confirm_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	check_ajax_referer( 'horsetools_contact', 'nonce' );
	horsetools_contact_confirm();
	// One button agrees to whatever is on the screen, and what is on the screen
	// may be either side. Confirming both is right in each case: the settings set
	// is re-read anyway, and the content set is empty until its first pass ends.
	horsetools_contact_content_confirm();
	wp_send_json_success();
}

/**
 * Say something when the site's own contact details are not what was confirmed.
 *
 * Deliberately loud and not dismissible-forever: a changed hotline is either
 * something the owner just did — in which case confirming takes one click — or
 * it is the most direct attack there is on a shop.
 */
function horsetools_contact_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! horsetools_is_plugin_screen() ) {
		return;
	}
	$s = horsetools_contact_status();
	$c = horsetools_contact_content_status();
	if ( 'clean' === $s['state'] && in_array( $c['state'], array( 'clean', 'scanning' ), true ) ) {
		return;
	}

	// Print a set of identifiers as one line each. Asking somebody to confirm
	// "8 contact details" without showing which eight is asking them to click
	// yes at nothing, which is how an approval step becomes a formality.
	$list = function ( array $rows, $prefix = '' ) {
		foreach ( $rows as $row ) {
			echo '<p style="margin:2px 0">'
				. ( '' !== $prefix ? '<code>' . esc_html( $prefix ) . '</code> ' : '' )
				. esc_html( horsetools_contact_type_label( $row['type'] ) ) . ': <strong>'
				. esc_html( isset( $row['raw'] ) && '' !== $row['raw'] ? $row['raw'] : $row['value'] )
				. '</strong></p>';
		}
	};

	$nonce  = wp_create_nonce( 'horsetools_contact' );
	$button = '<button type="button" class="button button-primary" id="ht-contact-confirm" data-nonce="' . esc_attr( $nonce ) . '">'
		. esc_html__( 'These are correct — remember them', 'horse-tools' ) . '</button>';

	// The settings side is already agreed and only the content side has
	// something to say: keep the two apart rather than folding a first reading
	// of eight hundred posts into a "your details changed" alarm.
	if ( 'clean' === $s['state'] ) {
		ob_start();
		if ( 'unset' === $c['state'] ) {
			echo '<p><strong>' . esc_html__( 'Finished reading your posts and pages.', 'horse-tools' ) . '</strong></p>';
			echo '<p>' . esc_html__( 'These contact details appear in your content. Confirm them and you will be told if a new one ever turns up in a post — which is what happens when somebody edits an old article to put their own number in it.', 'horse-tools' ) . '</p>';
		} else {
			echo '<p><strong>' . esc_html__( 'A contact detail that was not there before has appeared in your content.', 'horse-tools' ) . '</strong></p>';
		}
		$list( $c['added'] );
		echo '<p>' . $button . '</p>';
		horsetools_admin_banner( 'unset' === $c['state'] ? 'info' : 'bad', ob_get_clean() );
		return;
	}

	if ( 'unset' === $s['state'] ) {
		ob_start();
		echo '<p><strong>'
			. esc_html__( 'Horse Tools can watch your contact details for changes.', 'horse-tools' ) . '</strong></p><p>'
			. esc_html__( 'These are the phone numbers, Zalo, Messenger links and email addresses currently in your settings. Check them, then confirm — after that you will be told if any of them ever change. Changing the hotline on a shop is the most direct attack there is, and the quietest.', 'horse-tools' )
			. '</p>';
		$list( horsetools_contact_scan_settings() );
		echo '<p>' . $button . '</p>';
		horsetools_admin_banner( 'info', ob_get_clean() );
	} else {
		ob_start();
		echo '<p><strong>'
			. esc_html__( 'Your contact details have changed.', 'horse-tools' ) . '</strong></p>';
		$list( $s['added'], __( 'New', 'horse-tools' ) );
		$list( $s['removed'], __( 'Gone', 'horse-tools' ) );
		echo '<p>' . esc_html__( 'If you just changed these yourself, confirm them. If you did not, somebody else did — check who last edited your settings before changing anything else.', 'horse-tools' )
			. '</p><p>' . $button . '</p>';
		horsetools_admin_banner( 'bad', ob_get_clean() );
	}
	?>
	<script>
	document.addEventListener('DOMContentLoaded',function(){
		var b=document.getElementById('ht-contact-confirm'); if(!b){return;}
		b.addEventListener('click',function(){
			b.disabled=true;
			fetch(ajaxurl,{method:'POST',credentials:'same-origin',
				headers:{'Content-Type':'application/x-www-form-urlencoded'},
				body:'action=horsetools_contact_confirm&nonce='+encodeURIComponent(b.dataset.nonce)})
			.then(function(r){return r.json();})
			.then(function(){ location.reload(); })
			.catch(function(){ b.disabled=false; });
		});
	});
	</script>
	<?php
}
add_action( 'admin_notices', 'horsetools_contact_notice' );

/* -------------------------------------------------------------------------
 * The same question, asked of the content.
 *
 * The settings are a handful of options and can be read on every admin load.
 * Post content is eight hundred rows on one of these sites, so it is walked in
 * batches, resumes where it stopped, and afterwards reads only what changed.
 *
 * Its baseline is deliberately separate from the settings one. Sharing a
 * baseline would mean that the moment the first content pass finished, every
 * number in every post appeared as a change — on a site whose owner had just
 * confirmed everything — a false alarm caused by nothing but the plugin's own
 * progress.
 * ---------------------------------------------------------------------- */

const HORSETOOLS_CONTACT_CONTENT   = 'horsetools_contact_content';
const HORSETOOLS_CONTACT_CONTENT_B = 'horsetools_contact_baseline_content';
const HORSETOOLS_CONTACT_SCAN      = 'horsetools_contact_scan';

/** Post types carrying text a visitor can read. */
function horsetools_contact_post_types() {
	$types = get_post_types( array( 'public' => true ), 'names' );
	unset( $types['attachment'] );
	// Snippets are not public themselves, but their bodies are printed into
	// pages that are.
	if ( post_type_exists( 'ht_snippet' ) ) {
		$types['ht_snippet'] = 'ht_snippet';
	}
	return array_values( $types );
}

/**
 * @return array{done:bool,offset:int,since:string,total:int}
 */
function horsetools_contact_scan_state() {
	$s = get_option( HORSETOOLS_CONTACT_SCAN, array() );
	$s = is_array( $s ) ? $s : array();
	return array(
		'done'   => ! empty( $s['done'] ),
		'offset' => isset( $s['offset'] ) ? (int) $s['offset'] : 0,
		'since'  => isset( $s['since'] ) ? (string) $s['since'] : '',
		'total'  => isset( $s['total'] ) ? (int) $s['total'] : 0,
	);
}

/**
 * Read one batch and fold what it finds into the stored set.
 *
 * @param int $size Posts per batch.
 * @return array{done:bool,offset:int,total:int,scanned:int}
 */
function horsetools_contact_scan_batch( $size = 25 ) {
	global $wpdb;

	$state = horsetools_contact_scan_state();
	$types = horsetools_contact_post_types();
	if ( ! $types ) {
		return array( 'done' => true, 'offset' => 0, 'total' => 0, 'scanned' => 0 );
	}
	$in = implode( ',', array_fill( 0, count( $types ), '%s' ) );

	if ( $state['done'] ) {
		// Steady state: only what changed. On a site where nothing has been
		// edited this reads no post content at all.
		$sql  = "SELECT ID, post_title, post_excerpt, post_content FROM {$wpdb->posts}"
			. " WHERE post_status = 'publish' AND post_type IN ($in) AND post_modified > %s"
			. ' ORDER BY post_modified ASC LIMIT %d';
		$args = array_merge( $types, array( '' !== $state['since'] ? $state['since'] : '1970-01-01 00:00:00', (int) $size ) );
	} else {
		if ( ! $state['total'] ) {
			$state['total'] = (int) $wpdb->get_var(
				$wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ($in)", $types ) // phpcs:ignore WordPress.DB
			);
		}
		$sql  = "SELECT ID, post_title, post_excerpt, post_content FROM {$wpdb->posts}"
			. " WHERE post_status = 'publish' AND post_type IN ($in)"
			. ' ORDER BY ID ASC LIMIT %d OFFSET %d';
		$args = array_merge( $types, array( (int) $size, (int) $state['offset'] ) );
	}

	$rows  = $wpdb->get_results( $wpdb->prepare( $sql, $args ) ); // phpcs:ignore WordPress.DB
	$found = get_option( HORSETOOLS_CONTACT_CONTENT, array() );
	$found = is_array( $found ) ? $found : array();

	foreach ( (array) $rows as $row ) {
		$text = $row->post_title . "\n" . $row->post_excerpt . "\n" . $row->post_content;
		foreach ( horsetools_contact_extract( $text ) as $id => $hit ) {
			if ( ! isset( $found[ $id ] ) ) {
				if ( count( $found ) >= 200 ) {
					continue; // a set this size is not a contact list; stop growing.
				}
				$hit['posts'] = array();
				$hit['count'] = 0;
				$found[ $id ] = $hit;
			}
			$found[ $id ]['count'] += $hit['count'];
			if ( count( $found[ $id ]['posts'] ) < 8 && ! in_array( (int) $row->ID, $found[ $id ]['posts'], true ) ) {
				$found[ $id ]['posts'][] = (int) $row->ID;
			}
		}
	}
	update_option( HORSETOOLS_CONTACT_CONTENT, $found, false );

	$n = count( (array) $rows );

	if ( $state['done'] ) {
		if ( $n ) {
			$state['since'] = current_time( 'mysql' );
			update_option( HORSETOOLS_CONTACT_SCAN, $state, false );
		}
		return array( 'done' => true, 'offset' => $state['total'], 'total' => $state['total'], 'scanned' => $n );
	}

	$state['offset'] += $n;
	if ( $n < $size ) {
		$state['done']  = true;
		$state['since'] = current_time( 'mysql' );
	}
	update_option( HORSETOOLS_CONTACT_SCAN, $state, false );

	return array(
		'done'    => $state['done'],
		'offset'  => $state['offset'],
		'total'   => $state['total'],
		'scanned' => $n,
	);
}

/** Identities found in content — empty while the first pass is still running. */
function horsetools_contact_content_found() {
	$state = horsetools_contact_scan_state();
	if ( ! $state['done'] ) {
		return array();
	}
	$f = get_option( HORSETOOLS_CONTACT_CONTENT, array() );
	return is_array( $f ) ? $f : array();
}

function horsetools_contact_content_baseline() {
	$b = get_option( HORSETOOLS_CONTACT_CONTENT_B, null );
	return is_array( $b ) ? $b : array();
}

function horsetools_contact_content_has_baseline() {
	return is_array( get_option( HORSETOOLS_CONTACT_CONTENT_B, null ) );
}

/**
 * Content is judged on what appeared, not on what left.
 *
 * A number vanishing from the content usually means a post was deleted or
 * rewritten, which is ordinary work and would be a weekly false alarm. A number
 * *appearing* is the swap: whoever edits one post to put their own number in
 * leaves the original standing in the other eight hundred, so nothing is ever
 * reported missing anyway.
 *
 * @return array{state:string,added:array,progress:array}
 */
function horsetools_contact_content_status() {
	static $memo = null;
	if ( null !== $memo ) {
		return $memo;
	}
	$scan = horsetools_contact_scan_state();
	if ( ! $scan['done'] ) {
		$memo = array(
			'state'    => 'scanning',
			'added'    => array(),
			'progress' => array( 'read' => $scan['offset'], 'total' => $scan['total'] ),
		);
		return $memo;
	}
	$now = horsetools_contact_content_found();
	if ( ! horsetools_contact_content_has_baseline() ) {
		$memo = array( 'state' => 'unset', 'added' => $now, 'progress' => array() );
		return $memo;
	}
	$added = array_diff_key( $now, horsetools_contact_content_baseline() );
	$memo  = array(
		'state'    => $added ? 'changed' : 'clean',
		'added'    => $added,
		'progress' => array(),
	);
	return $memo;
}

function horsetools_contact_content_confirm() {
	update_option( HORSETOOLS_CONTACT_CONTENT_B, horsetools_contact_content_found(), false );
}

/**
 * Walk the scan forward while somebody is using the admin.
 *
 * WP-Cron is not reliable enough to be the only driver — this plugin has
 * already shipped a scheduled task that never ran once — and the first pass has
 * to finish before the watch means anything. One small batch per admin page
 * load gets there without a screen to sit and watch. Once it is done the cost
 * is a single indexed query every quarter of an hour that usually returns
 * nothing.
 */
function horsetools_contact_scan_tick() {
	if ( wp_doing_ajax() || wp_doing_cron() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( get_transient( 'horsetools_contact_tick' ) ) {
		return;
	}
	set_transient( 'horsetools_contact_tick', 1, 5 );

	$state = horsetools_contact_scan_state();
	if ( $state['done'] ) {
		if ( get_transient( 'horsetools_contact_seen' ) ) {
			return;
		}
		set_transient( 'horsetools_contact_seen', 1, 15 * MINUTE_IN_SECONDS );
	}
	horsetools_contact_scan_batch( 25 );
}
add_action( 'admin_init', 'horsetools_contact_scan_tick', 20 );
