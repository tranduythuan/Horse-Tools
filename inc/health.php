<?php
/**
 * Horse Tools — site health audit.
 *
 * A single read-only pass over the site's configuration and environment that
 * produces a 0–100 score and a list of actionable checks. It reads options
 * fresh (not the request-time globals) so the score is always current, and it
 * never writes anything. Rendered as a card in the dashboard sidebar.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Run the audit.
 *
 * @return array {
 *     @type int    $score  0–100.
 *     @type string $grade  A–E.
 *     @type array  $counts pass/warn/fail totals.
 *     @type array  $checks List of check rows, worst first.
 * }
 */
function horsetools_health_report() {
	$o  = (array) get_option( 'horsetools_settings', array() );
	$ex = (array) get_option( 'horsetools_extend_settings', array() );

	$sec = admin_url( 'admin.php?page=horsetools-options' );

	// Which screen each checked setting now lives on. This was one page until
	// the settings were grouped by subject in 1.2.71; left pointing at the old
	// page, every "fix this" link landed on the Overview screen, which holds no
	// settings at all — the link would look like it worked and do nothing.
	// Listed by prefix rather than guessed, because there are only nine of them
	// and a wrong guess fails silently.
	$screens = array(
		'scuri' => 'horsetools-security-options',
		'speed' => 'horsetools-speed-options',
		'media' => 'horsetools-speed-options', // image settings sit under Speed
	);

	// A fix link that jumps straight to the relevant control (opens its tab and
	// scrolls to the exact field) instead of just reloading the page. The
	// htadmin.js sidebar handler picks up the #ht-jump=<field-id> fragment.
	$jump = function ( $key, $raw_id = '' ) use ( $sec, $screens ) {
		$prefix = strtok( $key, '-' );
		$url    = isset( $screens[ $prefix ] )
			? admin_url( 'admin.php?page=' . $screens[ $prefix ] )
			: $sec;
		$id = '' !== $raw_id
			? $raw_id
			: ( function_exists( 'horsetools_field_id' ) ? horsetools_field_id( 'main', $key ) : '' );
		return '' !== $id ? $url . '#ht-jump=' . $id : $url;
	};
	$checks = array();

	/**
	 * @param string $id
	 * @param string $cat     Category label.
	 * @param string $label   What is being checked (already translated).
	 * @param string $status  pass | warn | fail.
	 * @param int    $weight  Contribution to the score. 0 = shown but not scored.
	 * @param string $fix_url Where to go to fix it.
	 * @param string $fix     Short fix instruction (translated).
	 */
	$add = function ( $id, $cat, $label, $status, $weight, $fix_url = '', $fix = '' ) use ( &$checks ) {
		$checks[] = array(
			'id'     => $id,
			'cat'    => $cat,
			'label'  => $label,
			'status' => $status,
			'weight' => $weight,
			'fix_url' => $fix_url,
			'fix'    => $fix,
		);
	};

	$on = function ( $arr, $k ) {
		return ! empty( $arr[ $k ] );
	};

	$sec_cat  = __( 'Security', 'horse-tools' );
	$priv_cat = __( 'Privacy', 'horse-tools' );
	$perf_cat = __( 'Performance', 'horse-tools' );
	$env_cat  = __( 'Environment', 'horse-tools' );

	// ---- Security -------------------------------------------------------
	$sec_on = $on( $o, 'scuri' );
	$add(
		'login', $sec_cat, __( 'Login attempts are rate-limited', 'horse-tools' ),
		( $sec_on && $on( $o, 'scuri-login1' ) ) ? 'pass' : 'fail', 3,
		$jump( 'scuri-login1' ), __( 'Security tab → Limit login attempts', 'horse-tools' )
	);
	$add(
		'fileedit', $sec_cat, __( 'The theme/plugin file editor is disabled', 'horse-tools' ),
		( $sec_on && $on( $o, 'scuri-fileedit1' ) ) || ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) ? 'pass' : 'warn', 2,
		$jump( 'scuri-fileedit1' ), __( 'Security tab → Lock down the admin', 'horse-tools' )
	);
	$add(
		'enum', $sec_cat, __( 'User enumeration is blocked', 'horse-tools' ),
		( $sec_on && $on( $o, 'scuri-enum1' ) ) ? 'pass' : 'warn', 2,
		$jump( 'scuri-enum1' ), __( 'Security tab → Block user enumeration', 'horse-tools' )
	);
	$add(
		'xmlrpc', $sec_cat, __( 'XML-RPC is disabled', 'horse-tools' ),
		( $sec_on && $on( $o, 'scuri-off2' ) ) ? 'pass' : 'warn', 1,
		$jump( 'scuri-off2' ), __( 'Security tab → Disable unused endpoints', 'horse-tools' )
	);
	$add(
		'headers', $sec_cat, __( 'Security response headers are sent', 'horse-tools' ),
		( $sec_on && $on( $o, 'scuri-head1' ) ) ? 'pass' : 'warn', 2,
		$jump( 'scuri-head1' ), __( 'Security tab → Security response headers', 'horse-tools' )
	);
	// Everything this plugin signs — the PHP snippet signature, the trusted
	// device cookie — is keyed on wp_salt(). WordPress falls back to keys kept
	// in the options table when wp-config.php does not define them, and says
	// nothing about it. A signature whose key sits in the database cannot keep
	// out somebody who is already in the database.
	if ( function_exists( 'horsetools_salt_location' ) ) {
		$salt_where = horsetools_salt_location();
		$salt_map   = array(
			'file'    => array( 'pass', '' ),
			'partial' => array( 'pass', __( 'One half is in the database — still safe, but tidy it up', 'horse-tools' ) ),
			'db'      => array( 'fail', __( 'Paste the eight keys into wp-config.php — see the notice at the top of any Horse Tools screen', 'horse-tools' ) ),
			'unknown' => array( 'warn', __( 'Could not be determined', 'horse-tools' ) ),
		);
		$salt_row = isset( $salt_map[ $salt_where ] ) ? $salt_map[ $salt_where ] : $salt_map['unknown'];
		$add(
			'salt', $sec_cat, __( 'Signing keys are in wp-config.php, not the database', 'horse-tools' ),
			$salt_row[0], 3, '', $salt_row[1]
		);
	}
	// The hotline, the Zalo, the email. Swapping one of those takes the
	// customers directly and leaves no link behind to find.
	if ( function_exists( 'horsetools_contact_status' ) ) {
		$c_state = horsetools_contact_status();
		$c_map   = array(
			'clean'   => array( 'pass', '' ),
			'unset'   => array( 'warn', __( 'Confirm them once, from the notice on any Horse Tools screen', 'horse-tools' ) ),
			'changed' => array( 'fail', __( 'Something changed — see the notice on any Horse Tools screen', 'horse-tools' ) ),
		);
		$c_row = isset( $c_map[ $c_state['state'] ] ) ? $c_map[ $c_state['state'] ] : $c_map['unset'];
		$add(
			'contact', $sec_cat, __( 'Contact details are the ones you confirmed', 'horse-tools' ),
			$c_row[0], 3, '', $c_row[1]
		);
	}
	// The same watch over post content. Separate row because it takes a while to
	// finish the first time, and "still reading" is not the same answer as "all
	// agreed" — reporting it as a pass would be claiming a guard that is not up
	// yet.
	if ( function_exists( 'horsetools_contact_content_status' ) ) {
		$cc = horsetools_contact_content_status();
		if ( 'scanning' === $cc['state'] ) {
			$read  = isset( $cc['progress']['read'] ) ? (int) $cc['progress']['read'] : 0;
			$total = isset( $cc['progress']['total'] ) ? (int) $cc['progress']['total'] : 0;
			$note  = $total
				/* translators: 1: posts read so far, 2: posts in total. */
				? sprintf( __( 'Reading your content: %1$d of %2$d', 'horse-tools' ), $read, $total )
				: __( 'Reading your content…', 'horse-tools' );
			$add( 'contact_content', $sec_cat, __( 'Contact details in your posts are watched', 'horse-tools' ), 'warn', 2, '', $note );
		} else {
			$cc_map = array(
				'clean'   => array( 'pass', '' ),
				'unset'   => array( 'warn', __( 'Confirm them once, from the banner on any Horse Tools screen', 'horse-tools' ) ),
				'changed' => array( 'fail', __( 'A new one appeared in a post — see the banner', 'horse-tools' ) ),
			);
			$cc_row = isset( $cc_map[ $cc['state'] ] ) ? $cc_map[ $cc['state'] ] : $cc_map['unset'];
			$add( 'contact_content', $sec_cat, __( 'Contact details in your posts are watched', 'horse-tools' ), $cc_row[0], 2, '', $cc_row[1] );
		}
	}
	// Who the content links to. The row that would have caught three old posts
	// quietly carrying casino links for two years.
	if ( function_exists( 'horsetools_link_status' ) ) {
		$ls = horsetools_link_status();
		if ( 'scanning' === $ls['state'] ) {
			$add(
				'links', $sec_cat, __( 'Outbound links are watched', 'horse-tools' ), 'warn', 3, '',
				__( 'Still reading your content', 'horse-tools' )
			);
		} else {
			$l_map = array(
				'clean'   => array( 'pass', '' ),
				'unset'   => array( 'warn', __( 'Go through the list once and approve it', 'horse-tools' ) ),
				'changed' => array( 'fail', __( 'A domain you have not approved is linked from your content', 'horse-tools' ) ),
			);
			$l_row = isset( $l_map[ $ls['state'] ] ) ? $l_map[ $ls['state'] ] : $l_map['unset'];
			$add(
				'links', $sec_cat, __( 'Outbound links are watched', 'horse-tools' ),
				$l_row[0], 3, horsetools_link_screen_url(), $l_row[1]
			);
		}
	}
	// Everything above is only ever read by somebody who logs in and looks. This
	// row is about whether anyone would find out otherwise — and it is the only
	// row that can be wrong about *itself*, since a plugin that has been switched
	// off prints no rows at all.
	if ( function_exists( 'horsetools_hb_enabled' ) ) {
		$hb = horsetools_hb_state();
		if ( ! horsetools_hb_enabled() ) {
			$hb_row = array( 'warn', __( 'Security tab → Check-in → turn on the regular message', 'horse-tools' ) );
		} elseif ( ! $hb['ever'] ) {
			$hb_row = array( 'warn', __( 'Turned on but never sent — send a test message', 'horse-tools' ) );
		} elseif ( ! $hb['ok'] ) {
			$hb_row = array( 'fail', __( 'The last one could not be sent — Security tab → Check-in', 'horse-tools' ) );
		} elseif ( time() > $hb['due'] + 2 * DAY_IN_SECONDS ) {
			$hb_row = array( 'warn', __( 'Overdue — nothing has been running the schedule', 'horse-tools' ) );
		} else {
			$hb_row = array( 'pass', '' );
		}
		$add(
			'heartbeat', $sec_cat, __( 'You would be told if this site went quiet', 'horse-tools' ),
			$hb_row[0], 3, admin_url( 'admin.php?page=horsetools-security-options' ), $hb_row[1]
		);
	}
	// A stray wp-config copy or database dump in the web root is worse than any
	// of the settings above being off.
	if ( function_exists( 'horsetools_exposure_status' ) ) {
		$exp   = horsetools_exposure_status();
		$e_num = count( $exp['found'] );
		$add(
			'exposure', $sec_cat, __( 'No downloadable secrets in the site folder', 'horse-tools' ),
			$e_num ? 'fail' : 'pass', 4, '',
			$e_num ? __( 'See the banner at the top of any Horse Tools screen', 'horse-tools' ) : ''
		);
	}

	// ---- Privacy --------------------------------------------------------
	$seen  = (array) get_option( 'horsetools_gfont_seen', array() );
	$local = (array) get_option( 'horsetools_gfont_local', array() );
	if ( false === get_option( 'horsetools_gfont_seen', false ) ) {
		$gstatus = 'warn';
		$gfix    = __( 'Security tab → Privacy → Scan external requests', 'horse-tools' );
	} elseif ( empty( $seen ) ) {
		$gstatus = 'pass';
		$gfix    = '';
	} else {
		$covered = ! empty( $local ) && $on( $o, 'scuri-gfont1' );
		$gstatus = $covered ? 'pass' : 'fail';
		$gfix    = $covered ? '' : __( 'Security tab → Privacy → self-host Google Fonts', 'horse-tools' );
	}
	$add( 'gfonts', $priv_cat, __( 'No Google Fonts leaking to Google', 'horse-tools' ), $gstatus, 2, $jump( 'scuri-gfont1' ), $gfix );

	// ---- Performance ----------------------------------------------------
	$add(
		'speed', $perf_cat, __( 'The optimisation module is on', 'horse-tools' ),
		$on( $o, 'speed' ) ? 'pass' : 'warn', 2,
		$jump( 'speed', 'check1' ), __( 'Optimize tab', 'horse-tools' )
	);
	$add(
		'lazy', $perf_cat, __( 'Images are lazy-loaded', 'horse-tools' ),
		( $on( $o, 'speed' ) && $on( $o, 'speed-lazy1' ) ) ? 'pass' : 'warn', 1,
		$jump( 'speed-lazy1' ), __( 'Optimize tab → image lazy loading', 'horse-tools' )
	);
	$add(
		'media', $perf_cat, __( 'Uploaded images are compressed', 'horse-tools' ),
		( $on( $o, 'media' ) && $on( $o, 'media-zip1' ) ) ? 'pass' : 'warn', 1,
		$jump( 'media-zip1' ), __( 'Media tab → JPG compression', 'horse-tools' )
	);

	// ---- Environment ----------------------------------------------------
	if ( PHP_VERSION_ID >= 80100 ) {
		$php_status = 'pass';
	} elseif ( PHP_VERSION_ID >= 70400 ) {
		$php_status = 'warn';
	} else {
		$php_status = 'fail';
	}
	$add(
		'php', $env_cat,
		/* translators: %s: PHP version. */
		sprintf( __( 'PHP is a supported version (%s)', 'horse-tools' ), PHP_VERSION ),
		$php_status, 2, '', __( 'Ask your host to update PHP to 8.1 or newer.', 'horse-tools' )
	);

	$https = ( 0 === strpos( strtolower( (string) home_url() ), 'https' ) );
	$add(
		'https', $env_cat, __( 'The site is served over HTTPS', 'horse-tools' ),
		$https ? 'pass' : 'fail', 2,
		admin_url( 'options-general.php' ), __( 'Move the site address to https://', 'horse-tools' )
	);

	$public = ( '0' !== (string) get_option( 'blog_public', '1' ) );
	$add(
		'visibility', $env_cat, __( 'The site is visible to search engines', 'horse-tools' ),
		$public ? 'pass' : 'warn', 3,
		admin_url( 'options-reading.php' ), __( 'Reading settings → uncheck “Discourage search engines”', 'horse-tools' )
	);

	$debug_leaks = ( defined( 'WP_DEBUG' ) && WP_DEBUG && ( ! defined( 'WP_DEBUG_DISPLAY' ) || WP_DEBUG_DISPLAY ) );
	$add(
		'debug', $env_cat, __( 'PHP errors are not shown to visitors', 'horse-tools' ),
		$debug_leaks ? 'fail' : 'pass', 2,
		isset( $ex['debug'] ) ? admin_url( 'admin.php?page=horsetools-tools-options' ) : '',
		__( 'Turn off WP_DEBUG_DISPLAY on a live site.', 'horse-tools' )
	);

	if ( function_exists( 'wp_get_update_data' ) ) {
		$upd     = wp_get_update_data();
		$pending = isset( $upd['counts']['total'] ) ? (int) $upd['counts']['total'] : 0;
		$add(
			'updates', $env_cat, __( 'Core, plugins and themes are up to date', 'horse-tools' ),
			$pending > 0 ? 'warn' : 'pass', 2,
			admin_url( 'update-core.php' ), __( 'Install the pending updates.', 'horse-tools' )
		);
	}

	// ---- Score ----------------------------------------------------------
	$got   = 0.0;
	$total = 0;
	$counts = array( 'pass' => 0, 'warn' => 0, 'fail' => 0 );
	foreach ( $checks as $c ) {
		$counts[ $c['status'] ]++;
		if ( $c['weight'] <= 0 ) {
			continue;
		}
		$total += $c['weight'];
		$got   += $c['weight'] * ( 'pass' === $c['status'] ? 1 : ( 'warn' === $c['status'] ? 0.5 : 0 ) );
	}
	$score = $total > 0 ? (int) round( $got / $total * 100 ) : 100;

	if ( $score >= 90 ) {
		$grade = 'A';
	} elseif ( $score >= 75 ) {
		$grade = 'B';
	} elseif ( $score >= 60 ) {
		$grade = 'C';
	} elseif ( $score >= 40 ) {
		$grade = 'D';
	} else {
		$grade = 'E';
	}

	// Worst first: fail, then warn, then pass; keep insertion order within.
	$rank = array( 'fail' => 0, 'warn' => 1, 'pass' => 2 );
	usort( $checks, function ( $a, $b ) use ( $rank ) {
		return $rank[ $a['status'] ] <=> $rank[ $b['status'] ];
	} );

	return array(
		'score'  => $score,
		'grade'  => $grade,
		'counts' => $counts,
		'checks' => $checks,
	);
}

/**
 * Render the health card for the dashboard sidebar.
 */
function horsetools_health_card() {
	$r      = horsetools_health_report();
	$score  = (int) $r['score'];
	$grade  = $r['grade'];
	$colour = $score >= 75 ? '#2e9e5b' : ( $score >= 50 ? '#e0a800' : '#c0392b' );
	// Circular gauge geometry.
	$circ   = 2 * M_PI * 52;
	$dash   = $circ * $score / 100;

	$icons = array( 'pass' => 'circle-check', 'warn' => 'alert-triangle', 'fail' => 'circle-x' );
	?>
	<div class="ht-health">
		<h3 class="ht-health-title"><i class="ti ti-heartbeat"></i> <?php esc_html_e( 'Site health', 'horse-tools' ); ?></h3>
		<div class="ht-health-gauge">
			<svg viewBox="0 0 120 120" width="120" height="120" aria-hidden="true">
				<circle cx="60" cy="60" r="52" fill="none" stroke="#eee" stroke-width="12" />
				<circle cx="60" cy="60" r="52" fill="none" stroke="<?php echo esc_attr( $colour ); ?>" stroke-width="12"
					stroke-linecap="round" stroke-dasharray="<?php echo esc_attr( round( $dash, 1 ) . ' ' . round( $circ, 1 ) ); ?>"
					transform="rotate(-90 60 60)" />
				<text x="60" y="56" text-anchor="middle" font-size="30" font-weight="700" fill="<?php echo esc_attr( $colour ); ?>"><?php echo (int) $score; ?></text>
				<text x="60" y="78" text-anchor="middle" font-size="13" fill="#888"><?php echo esc_html( sprintf( /* translators: %s: letter grade. */ __( 'Grade %s', 'horse-tools' ), $grade ) ); ?></text>
			</svg>
			<div class="ht-health-legend">
				<span class="ht-h-pass"><i class="ti ti-circle-check"></i> <?php echo (int) $r['counts']['pass']; ?></span>
				<span class="ht-h-warn"><i class="ti ti-alert-triangle"></i> <?php echo (int) $r['counts']['warn']; ?></span>
				<span class="ht-h-fail"><i class="ti ti-circle-x"></i> <?php echo (int) $r['counts']['fail']; ?></span>
			</div>
		</div>
		<ul class="ht-health-list">
			<?php foreach ( $r['checks'] as $c ) : ?>
				<li class="ht-h-<?php echo esc_attr( $c['status'] ); ?>">
					<i class="ti ti-<?php echo esc_attr( $icons[ $c['status'] ] ); ?>" aria-hidden="true"></i>
					<span class="ht-h-label">
						<?php echo esc_html( $c['label'] ); ?>
						<?php if ( 'pass' !== $c['status'] && '' !== $c['fix'] ) : ?>
							<span class="ht-h-fix">
								<?php if ( '' !== $c['fix_url'] ) : ?>
									<a href="<?php echo esc_url( $c['fix_url'] ); ?>"><?php echo esc_html( $c['fix'] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $c['fix'] ); ?>
								<?php endif; ?>
							</span>
						<?php endif; ?>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<style>
	.ht-health{background:#fff;border:1px solid #eee;border-radius:12px;padding:16px;margin-bottom:16px}
	.ht-health-title{margin:0 0 10px;font-size:15px;display:flex;align-items:center;gap:7px}
	.ht-health-title i{color:#c0392b}
	.ht-health-gauge{display:flex;align-items:center;gap:14px;margin-bottom:12px}
	.ht-health-legend{display:flex;flex-direction:column;gap:5px;font-size:13px;font-weight:600}
	.ht-health-legend i{width:16px}
	.ht-h-pass{color:#2e9e5b}.ht-h-warn{color:#b8860b}.ht-h-fail{color:#c0392b}
	.ht-health-list{list-style:none;margin:0;padding:0}
	.ht-health-list li{display:flex;align-items:flex-start;gap:8px;padding:7px 0;border-top:1px solid #f2f2f2;font-size:12.5px;color:#444;line-height:1.35}
	.ht-health-list li i{font-size:16px;flex-shrink:0;margin-top:1px}
	.ht-health-list li.ht-h-pass i{color:#2e9e5b}
	.ht-health-list li.ht-h-warn i{color:#e0a800}
	.ht-health-list li.ht-h-fail i{color:#c0392b}
	.ht-h-fix{display:block;font-size:11.5px;margin-top:2px}
	.ht-h-fix a{color:#8a5a00;text-decoration:underline}
	</style>
	<?php
}
