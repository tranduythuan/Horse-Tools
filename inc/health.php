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

	$sec  = admin_url( 'admin.php?page=horsetools-options' );
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
		$sec, __( 'Security tab → Limit login attempts', 'horse-tools' )
	);
	$add(
		'fileedit', $sec_cat, __( 'The theme/plugin file editor is disabled', 'horse-tools' ),
		( $sec_on && $on( $o, 'scuri-fileedit1' ) ) || ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) ? 'pass' : 'warn', 2,
		$sec, __( 'Security tab → Lock down the admin', 'horse-tools' )
	);
	$add(
		'enum', $sec_cat, __( 'User enumeration is blocked', 'horse-tools' ),
		( $sec_on && $on( $o, 'scuri-enum1' ) ) ? 'pass' : 'warn', 2,
		$sec, __( 'Security tab → Block user enumeration', 'horse-tools' )
	);
	$add(
		'xmlrpc', $sec_cat, __( 'XML-RPC is disabled', 'horse-tools' ),
		( $sec_on && $on( $o, 'scuri-off2' ) ) ? 'pass' : 'warn', 1,
		$sec, __( 'Security tab → Disable unused endpoints', 'horse-tools' )
	);
	$add(
		'headers', $sec_cat, __( 'Security response headers are sent', 'horse-tools' ),
		( $sec_on && $on( $o, 'scuri-head1' ) ) ? 'pass' : 'warn', 2,
		$sec, __( 'Security tab → Security response headers', 'horse-tools' )
	);

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
	$add( 'gfonts', $priv_cat, __( 'No Google Fonts leaking to Google', 'horse-tools' ), $gstatus, 2, $sec, $gfix );

	// ---- Performance ----------------------------------------------------
	$add(
		'speed', $perf_cat, __( 'The optimisation module is on', 'horse-tools' ),
		$on( $o, 'speed' ) ? 'pass' : 'warn', 2,
		$sec, __( 'Optimize tab', 'horse-tools' )
	);
	$add(
		'lazy', $perf_cat, __( 'Images are lazy-loaded', 'horse-tools' ),
		( $on( $o, 'speed' ) && $on( $o, 'speed-lazy1' ) ) ? 'pass' : 'warn', 1,
		$sec, __( 'Optimize tab → image lazy loading', 'horse-tools' )
	);
	$add(
		'media', $perf_cat, __( 'Uploaded images are compressed', 'horse-tools' ),
		( $on( $o, 'media' ) && $on( $o, 'media-zip1' ) ) ? 'pass' : 'warn', 1,
		$sec, __( 'Media tab → JPG compression', 'horse-tools' )
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
		isset( $ex['debug'] ) ? admin_url( 'admin.php?page=horsetools-debug-options' ) : '',
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
