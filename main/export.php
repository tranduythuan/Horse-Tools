<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Option names that make sense to move between sites.
 *
 * Everything the plugin owns, minus horsetools_font_settings: that is the
 * registry of uploaded font FILES, and the .ttf/.woff2 files live on this
 * site's disk. Carrying the registry to another site would only leave dangling
 * references, so it is deliberately not exported.
 *
 * @return string[]
 */
function horsetools_export_option_names() {
	return array_values( array_diff( horsetools_option_names(), array( 'horsetools_font_settings' ) ) );
}

/**
 * Build the export payload: a readable envelope, not base64.
 *
 * The old exporter only included a module if it happened to be enabled on the
 * Extend screen, so turning a module off to troubleshoot and then exporting
 * "for backup" silently dropped that module's configuration. This exports every
 * exportable option unconditionally.
 *
 * @return array
 */
function horsetools_export_payload() {
	$settings = array();
	foreach ( horsetools_export_option_names() as $option ) {
		$value = get_option( $option, null );
		if ( is_array( $value ) ) {
			$settings[ $option ] = $value;
		}
	}
	return array(
		'_meta'    => array(
			'plugin'    => 'horse-tools',
			'version'   => HORSETOOLS_VERSION,
			'generated' => gmdate( 'c' ),
			'site'      => home_url( '/' ),
		),
		'settings' => $settings,
	);
}

/**
 * Parse pasted/uploaded import data into a whitelisted option_name => array map.
 *
 * Accepts both the current readable envelope and the old base64(json) format.
 * Only option names the plugin actually owns are kept — an import file is
 * untrusted input, so it must never be allowed to write an arbitrary option
 * (e.g. siteurl, or a role's capabilities).
 *
 * @param string $raw Raw textarea contents.
 * @return array|null option_name => array, or null when unparseable.
 */
function horsetools_import_parse( $raw ) {
	$raw = is_string( $raw ) ? trim( $raw ) : '';
	if ( '' === $raw ) {
		return null;
	}
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		$decoded = base64_decode( $raw, true );
		if ( false !== $decoded ) {
			$data = json_decode( $decoded, true );
		}
	}
	if ( ! is_array( $data ) ) {
		return null;
	}

	// New envelope: settings keyed by option name. Old format: flat, keyed by
	// the short module names the previous exporter used.
	if ( isset( $data['settings'] ) && is_array( $data['settings'] ) ) {
		$incoming = $data['settings'];
	} else {
		$legacy = array(
			'tool'      => 'horsetools_settings',
			'code'      => 'horsetools_code_settings',
			'clean'     => 'horsetools_extend_settings',
			'font'      => 'horsetools_fontset_settings',
			'redirect'  => 'horsetools_redirects_settings',
			'index'     => 'horsetools_gindex_settings',
			'toc'       => 'horsetools_toc_settings',
			'ads'       => 'horsetools_ads_settings',
			'notify'    => 'horsetools_notify_settings',
			'shortcode' => 'horsetools_shortcode_settings',
			'search'    => 'horsetools_search_settings',
			'debug'     => 'horsetools_debug_settings',
		);
		$incoming = array();
		foreach ( $legacy as $short => $option ) {
			if ( isset( $data[ $short ] ) ) {
				$incoming[ $option ] = $data[ $short ];
			}
		}
	}

	$allowed = horsetools_export_option_names();
	$clean   = array();
	foreach ( $incoming as $option => $value ) {
		if ( in_array( $option, $allowed, true ) && is_array( $value ) ) {
			$clean[ $option ] = $value;
		}
	}
	return $clean;
}

/**
 * Per-option diff between an incoming import and what is stored now.
 *
 * @param array $incoming option_name => array
 * @return array option_name => array( added, changed, removed )
 */
function horsetools_import_diff( array $incoming ) {
	$diff = array();
	foreach ( $incoming as $option => $value ) {
		$current = get_option( $option, array() );
		$current = is_array( $current ) ? $current : array();
		$added   = 0;
		$changed = 0;
		foreach ( $value as $k => $v ) {
			if ( ! array_key_exists( $k, $current ) ) {
				$added++;
			} elseif ( maybe_serialize( $current[ $k ] ) !== maybe_serialize( $v ) ) {
				$changed++;
			}
		}
		$removed = count( array_diff_key( $current, $value ) );
		$diff[ $option ] = array( 'added' => $added, 'changed' => $changed, 'removed' => $removed );
	}
	return $diff;
}

/**
 * Apply an import: snapshot current config first, then write each option
 * through the sanitizer.
 *
 * The backup lets the user undo in one click. Sanitising here (rather than
 * trusting the file) matters because the register_setting sanitize callbacks
 * only exist for modules whose admin screen loaded this request.
 *
 * @param array $incoming option_name => array
 * @return array Applied option names.
 */
function horsetools_import_apply( array $incoming ) {
	// Snapshot everything exportable so Undo can restore it exactly.
	$backup = array();
	foreach ( horsetools_export_option_names() as $option ) {
		$backup[ $option ] = get_option( $option, null );
	}
	update_option( 'horsetools_config_backup', $backup, false );

	$applied = array();
	foreach ( $incoming as $option => $value ) {
		if ( 'horsetools_clean_settings' === $option && function_exists( 'horsetools_sanitize_clean' ) ) {
			$value = horsetools_sanitize_clean( $value );
		} elseif ( function_exists( 'horsetools_sanitize_settings_array' ) ) {
			$value = horsetools_sanitize_settings_array( $value );
		}
		update_option( $option, $value );
		$applied[] = $option;
	}
	return $applied;
}

/**
 * Restore the snapshot taken by the last import.
 *
 * @return bool True if a backup existed and was restored.
 */
function horsetools_import_undo() {
	$backup = get_option( 'horsetools_config_backup', null );
	if ( ! is_array( $backup ) ) {
		return false;
	}
	foreach ( $backup as $option => $value ) {
		if ( ! in_array( $option, horsetools_export_option_names(), true ) ) {
			continue;
		}
		if ( null === $value ) {
			delete_option( $option );
		} else {
			update_option( $option, $value );
		}
	}
	delete_option( 'horsetools_config_backup' );
	return true;
}

/**
 * Built-in configuration presets.
 *
 * A preset is a small, curated set of recommended settings for one kind of
 * site. Presets are deliberately ADDITIVE: applying one turns recommended
 * options on and tunes a few values, but never switches off anything the user
 * has already enabled (see horsetools_preset_merged). That keeps them safe to
 * try — and a one-click Undo is kept regardless.
 *
 * Keys map onto real option groups: the master switches (speed/scuri/media/
 * post) live in horsetools_settings and both load the module and enable it;
 * the table of contents additionally needs its module turned on in
 * horsetools_extend_settings.
 *
 * Nothing here enables anything that can silently break a site — the full REST
 * API block (scuri-off1), HSTS and CSP are intentionally left out, since those
 * carry the warnings on the Security screen and are choices, not defaults.
 *
 * @return array id => array( label, icon, desc, items[], settings[] )
 */
function horsetools_presets() {
	return array(
		'blog' => array(
			'label' => __( 'Blog / News site', 'horse-tools' ),
			'icon'  => 'article',
			'desc'  => __( 'Sensible defaults for a content site: a table of contents on long posts, lighter pages and the baseline security hardening.', 'horse-tools' ),
			'items' => array(
				__( 'Table of contents on posts', 'horse-tools' ),
				__( 'Delay analytics/ads/chat until interaction, and defer JavaScript', 'horse-tools' ),
				__( 'Lazy-load images; drop Emoji and jQuery Migrate', 'horse-tools' ),
				__( 'nofollow + new tab on external links', 'horse-tools' ),
				__( 'Use titles as image alt text', 'horse-tools' ),
				__( 'Security: block user enumeration, limit logins, disable XML-RPC, hide the version, disable the file editor', 'horse-tools' ),
			),
			'settings' => array(
				'horsetools_settings'        => array(
					'speed' => '1', 'speed-off1' => '1', 'speed-off4' => '1', 'speed-lazy1' => '1',
					'speed-defer1' => '1', 'speed-dash1' => '1', 'speed-hb1' => '1', 'speed-hb2' => 'slow',
					'speed-delay1' => '1', 'speed-delay-mode' => 'listed', 'speed-delay-timeout' => '5',
					'post' => '1', 'post-out1' => '1', 'post-alt1' => '1',
					'scuri' => '1', 'scuri-off2' => '1', 'scuri-off4' => '1', 'scuri-off5' => '1',
					'scuri-verof2' => '1', 'scuri-enum1' => '1', 'scuri-login1' => '1', 'scuri-fileedit1' => '1',
				),
				'horsetools_extend_settings' => array( 'toc' => '1' ),
				'horsetools_toc_settings'    => array( 'toc1' => '1' ),
			),
		),
		'woocommerce' => array(
			'label' => __( 'WooCommerce / Store', 'horse-tools' ),
			'icon'  => 'shopping-cart',
			'desc'  => __( 'Security and speed for a shop, chosen not to interfere with the store. The REST API is left fully on — WooCommerce cart and checkout need it — and permalinks are left untouched.', 'horse-tools' ),
			'items' => array(
				__( 'Delay analytics/ads/chat until interaction, and defer JavaScript (cart & checkout untouched)', 'horse-tools' ),
				__( 'Lazy-load images; drop Emoji', 'horse-tools' ),
				__( 'Compress uploaded JPGs', 'horse-tools' ),
				__( 'Security headers (frame, nosniff, referrer)', 'horse-tools' ),
				__( 'Limit logins, block user enumeration, disable XML-RPC and the file editor', 'horse-tools' ),
				__( 'REST API left ON so the store keeps working', 'horse-tools' ),
			),
			'settings' => array(
				'horsetools_settings' => array(
					'speed' => '1', 'speed-off4' => '1', 'speed-lazy1' => '1',
					'speed-defer1' => '1', 'speed-dash1' => '1', 'speed-hb1' => '1', 'speed-hb2' => 'slow',
					'speed-delay1' => '1', 'speed-delay-mode' => 'listed', 'speed-delay-timeout' => '5',
					'media' => '1', 'media-zip1' => '1',
					'scuri' => '1', 'scuri-off2' => '1', 'scuri-off4' => '1', 'scuri-verof2' => '1',
					'scuri-enum1' => '1', 'scuri-login1' => '1', 'scuri-fileedit1' => '1',
					'scuri-head1' => '1', 'scuri-head-xfo' => '1', 'scuri-head-nosniff' => '1', 'scuri-head-ref' => '1',
				),
			),
		),
		'performance' => array(
			'label' => __( 'Performance', 'horse-tools' ),
			'icon'  => 'rocket',
			'desc'  => __( 'One click for the safe, high-impact speed features — including delaying third-party scripts and deferring JavaScript. It deliberately leaves out the advanced, riskier options (async CSS, the aggressive “delay all” mode), which you can still turn on yourself later. Gutenberg and Classic CSS are left alone, since removing those can change how a theme looks.', 'horse-tools' ),
			'items' => array(
				__( 'Delay third-party scripts (analytics, ads, chat) until the visitor interacts — safe built-in list, with a 5-second fall-back', 'horse-tools' ),
				__( 'Defer JavaScript so it no longer blocks the page', 'horse-tools' ),
				__( 'Drop Emoji, jQuery Migrate and admin icons; calm the Heartbeat', 'horse-tools' ),
				__( 'Instant-page prefetch and image lazy-loading', 'horse-tools' ),
				__( 'HTML compression; compress JPGs and serve WebP', 'horse-tools' ),
			),
			'settings' => array(
				'horsetools_settings' => array(
					'speed' => '1', 'speed-off1' => '1', 'speed-off4' => '1', 'speed-link1' => '1', 'speed-lazy1' => '1',
					'speed-dash1' => '1', 'speed-hb1' => '1', 'speed-hb2' => 'slow',
					'speed-defer1' => '1',
					'speed-delay1' => '1', 'speed-delay-mode' => 'listed', 'speed-delay-timeout' => '5',
					'speed-zip1' => '1', 'speed-zip11' => '1', 'speed-zip12' => '1',
					'media' => '1', 'media-zip1' => '1', 'media-webp1' => '1',
				),
			),
		),
		'security' => array(
			'label' => __( 'Security hardening', 'horse-tools' ),
			'icon'  => 'shield-lock',
			'desc'  => __( 'The full recommended hardening set. The one thing left out is the full REST API block, which can break forms and guests — enable that yourself on the Security screen if your site truly needs none of it.', 'horse-tools' ),
			'items' => array(
				__( 'Limit failed logins and email you on lockout', 'horse-tools' ),
				__( 'Block user enumeration (?author=N, users REST, oEmbed)', 'horse-tools' ),
				__( 'All safe security headers (frame, nosniff, referrer, permissions)', 'horse-tools' ),
				__( 'Disable XML-RPC, X-Pingback, extra header tags and the version tag', 'horse-tools' ),
				__( 'Disable the theme & plugin file editor', 'horse-tools' ),
			),
			'settings' => array(
				'horsetools_settings' => array(
					'scuri' => '1', 'scuri-off2' => '1', 'scuri-off4' => '1', 'scuri-off5' => '1', 'scuri-verof2' => '1',
					'scuri-enum1' => '1', 'scuri-login1' => '1', 'scuri-login-mail' => '1', 'scuri-fileedit1' => '1',
					'scuri-head1' => '1', 'scuri-head-xfo' => '1', 'scuri-head-nosniff' => '1',
					'scuri-head-ref' => '1', 'scuri-head-perm' => '1',
				),
			),
		),
	);
}

/**
 * Merge a preset's values over the current configuration.
 *
 * The result is a superset of what is stored now — existing keys are preserved,
 * the preset's keys are layered on top — so horsetools_import_diff() reports
 * removed=0 and horsetools_import_apply() writes a clean merge.
 *
 * @param array $preset One entry from horsetools_presets().
 * @return array option_name => merged array.
 */
function horsetools_preset_merged( array $preset ) {
	$allowed = horsetools_export_option_names();
	$merged  = array();
	foreach ( $preset['settings'] as $option => $values ) {
		if ( ! in_array( $option, $allowed, true ) ) {
			continue;
		}
		$current            = get_option( $option, array() );
		$current            = is_array( $current ) ? $current : array();
		$merged[ $option ]  = array_merge( $current, $values );
	}
	return $merged;
}

function horsetools_export_options_page( $embed = false ) {
	$payload  = horsetools_export_payload();
	$json     = wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	$notice   = '';        // rendered above the form: array( type, html )
	$preview  = null;      // diff to show, plus the raw data to re-post
	$did_apply = false;

	// Handle the three POST actions. All share one nonce.
	if ( isset( $_POST['horsetools_import_preview'] ) || isset( $_POST['horsetools_import_apply'] ) || isset( $_POST['horsetools_import_undo'] ) || isset( $_POST['horsetools_preset_apply'] ) ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'horse-tools' ) );
		}
		check_admin_referer( 'horsetools_import', 'horsetools_import_nonce' );

		if ( isset( $_POST['horsetools_import_undo'] ) ) {
			$ok     = horsetools_import_undo();
			$notice = $ok
				? array( 'ok', esc_html__( 'The previous configuration has been restored.', 'horse-tools' ) )
				: array( 'err', esc_html__( 'There is no backup to restore.', 'horse-tools' ) );
		} elseif ( isset( $_POST['horsetools_preset_apply'] ) ) {
			$id      = isset( $_POST['horsetools_preset'] ) ? sanitize_key( wp_unslash( $_POST['horsetools_preset'] ) ) : '';
			$presets = horsetools_presets();
			if ( isset( $presets[ $id ] ) ) {
				$merged    = horsetools_preset_merged( $presets[ $id ] );
				$applied   = horsetools_import_apply( $merged );
				$did_apply = true;
				$notice    = array(
					'ok',
					sprintf(
						/* translators: %s: preset name. */
						esc_html__( 'Applied the "%s" preset. Recommended settings are on — you can undo this below.', 'horse-tools' ),
						esc_html( $presets[ $id ]['label'] )
					),
				);
			} else {
				$notice = array( 'err', esc_html__( 'That preset does not exist.', 'horse-tools' ) );
			}
		} else {
			$raw      = isset( $_POST['horsetools_export_tool'] ) ? wp_unslash( $_POST['horsetools_export_tool'] ) : '';
			$incoming = horsetools_import_parse( $raw );

			if ( null === $incoming ) {
				$notice = array( 'err', esc_html__( 'That is not a valid Horse Tools export file.', 'horse-tools' ) );
			} elseif ( empty( $incoming ) ) {
				$notice = array( 'err', esc_html__( 'The file contained no Horse Tools settings to import.', 'horse-tools' ) );
			} elseif ( isset( $_POST['horsetools_import_apply'] ) ) {
				$applied   = horsetools_import_apply( $incoming );
				$did_apply = true;
				$notice    = array(
					'ok',
					sprintf(
						/* translators: %d: number of setting groups. Vietnamese does not inflect the noun by count, so a single form is used. */
						esc_html__( 'Imported %d setting group(s). You can undo this below.', 'horse-tools' ),
						count( $applied )
					),
				);
			} else {
				// Preview: show the diff and re-offer the same data for apply.
				$preview = array(
					'diff' => horsetools_import_diff( $incoming ),
					'raw'  => $raw,
				);
			}
		}
	}

	$module_map = array_flip( horsetools_module_map() ); // option name => module key, for labels
	$has_backup = ( false !== get_option( 'horsetools_config_backup', false ) );

	ob_start();
	?>
		<?php if ( ! $embed ) : ?>
	<div class="wrap ht-wrap">
	<div class="ht-wrap-top"></div>
	<div class="ht-wrap2">
	  <div class="ht-box">
		<div class="ht-menu">
			<div class="ht-logo ht-logoquay">
			<a class="ht-logoquaya" href="https://tranduythuan.com/" target="_blank" rel="noopener">
			<span><?php horsetools_logo(); ?></span>
			</a>
			</div>
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="ti ti-arrows-left-right"></i> <?php _e( 'BACKUP', 'horse-tools' ); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="ti ti-adjustments-check"></i> <?php _e( 'PRESETS', 'horse-tools' ); ?></button>
		</div>
		<div class="ht-main">
		<?php endif; ?>
			<?php
			if ( $notice ) {
				$cls = ( 'ok' === $notice[0] ) ? 'ht-updated' : 'ht-updated ht-updated-err';
				echo '<div class="' . esc_attr( $cls ) . '">' . $notice[1] . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped at assignment
			}
			?>

			<div class="sotab-box htbox" id="tab1">
			<h2><?php _e( 'BACKUP', 'horse-tools' ); ?></h2>

			<div class="ht-card">
			  <h3><i class="ti ti-download"></i> <?php _e( 'Export', 'horse-tools' ); ?></h3>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e( 'This includes every setting group, whether or not its module is currently enabled. Uploaded font files are not included — they live on this site.', 'horse-tools' ); ?></p>
				<p>
				<textarea style="height:250px" class="ht-code-textarea" id="horsetools-json" readonly><?php echo esc_textarea( $json ); ?></textarea>
				</p>
				<button type="button" id="horsetools-dow-json"><i class="ti ti-download"></i> <?php _e( 'Download .json', 'horse-tools' ); ?></button>
			</div>

			<div class="ht-card">
			  <h3><i class="ti ti-upload"></i> <?php _e( 'Import', 'horse-tools' ); ?></h3>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=horsetools-tools-options' ) ); ?>">
				<?php wp_nonce_field( 'horsetools_import', 'horsetools_import_nonce' ); ?>
				<p>
				<textarea style="height:200px" class="ht-code-textarea" id="horsetools-import-json" name="horsetools_export_tool" placeholder="<?php esc_attr_e( 'Paste an export here, or upload a .json file', 'horse-tools' ); ?>"><?php echo isset( $preview['raw'] ) ? esc_textarea( $preview['raw'] ) : ''; ?></textarea>
				</p>
				<input type="file" id="horsetools-upload-json" accept=".json,application/json" style="display:none;" />
				<button type="button" id="horsetools-upload-button"><i class="ti ti-file-upload"></i> <?php _e( 'Choose a file', 'horse-tools' ); ?></button>

				<?php if ( $preview && ! empty( $preview['diff'] ) ) : ?>
					<h4><?php _e( 'These groups will change', 'horse-tools' ); ?></h4>
					<table class="ht-diff-table">
						<thead><tr>
							<th><?php _e( 'Setting group', 'horse-tools' ); ?></th>
							<th><?php _e( 'Added', 'horse-tools' ); ?></th>
							<th><?php _e( 'Changed', 'horse-tools' ); ?></th>
							<th><?php _e( 'Removed', 'horse-tools' ); ?></th>
						</tr></thead>
						<tbody>
						<?php foreach ( $preview['diff'] as $option => $d ) :
							$label = isset( $module_map[ $option ] ) ? $module_map[ $option ] : $option; ?>
							<tr>
								<td><?php echo esc_html( $label ); ?></td>
								<td><?php echo (int) $d['added'] ? '<span class="ht-diff-add">+' . (int) $d['added'] . '</span>' : '—'; ?></td>
								<td><?php echo (int) $d['changed'] ? '<span class="ht-diff-chg">' . (int) $d['changed'] . '</span>' : '—'; ?></td>
								<td><?php echo (int) $d['removed'] ? '<span class="ht-diff-del">−' . (int) $d['removed'] . '</span>' : '—'; ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Applying overwrites these groups with the imported values. A one-click backup is kept so you can undo it.', 'horse-tools' ); ?></p>
					<div class="ht-submit">
						<button type="submit" name="horsetools_import_apply"><i class="ti ti-file-import"></i> <?php _e( 'Apply import', 'horse-tools' ); ?></button>
					</div>
				<?php else : ?>
					<div class="ht-submit">
						<button type="submit" name="horsetools_import_preview"><i class="ti ti-eye"></i> <?php _e( 'Preview changes', 'horse-tools' ); ?></button>
					</div>
				<?php endif; ?>
				</form>
			</div>

			<?php if ( $has_backup ) : ?>
			<div class="ht-card">
			  <h3><i class="ti ti-rotate"></i> <?php _e( 'Undo the last import', 'horse-tools' ); ?></h3>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e( 'The configuration from before your most recent import is stored. Restoring reverts every group to that snapshot.', 'horse-tools' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=horsetools-tools-options' ) ); ?>">
					<?php wp_nonce_field( 'horsetools_import', 'horsetools_import_nonce' ); ?>
					<div class="ht-submit">
						<button type="submit" name="horsetools_import_undo"><i class="ti ti-rotate"></i> <?php _e( 'Restore previous configuration', 'horse-tools' ); ?></button>
					</div>
				</form>
			</div>
			<?php endif; ?>

			</div>

			<div class="sotab-box htbox" id="tab2" style="display:none">
			<h2><?php _e( 'PRESETS', 'horse-tools' ); ?></h2>
			<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e( 'One click applies a set of recommended settings for your kind of site. Presets only turn options on and tune a few values — they never switch off anything you have already enabled, and a one-click Undo is always kept.', 'horse-tools' ); ?></p>
			<?php foreach ( horsetools_presets() as $preset_id => $preset ) : ?>
				<div class="ht-card ht-preset">
				  <h3><i class="ti ti-<?php echo esc_attr( $preset['icon'] ); ?>"></i> <?php echo esc_html( $preset['label'] ); ?></h3>
					<p class="ht-note"><i class="ti ti-bulb"></i> <?php echo esc_html( $preset['desc'] ); ?></p>
					<ul class="ht-preset-list">
						<?php foreach ( $preset['items'] as $item ) : ?>
							<li><i class="ti ti-circle-check"></i> <?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=horsetools-tools-options' ) ); ?>">
						<?php wp_nonce_field( 'horsetools_import', 'horsetools_import_nonce' ); ?>
						<input type="hidden" name="horsetools_preset" value="<?php echo esc_attr( $preset_id ); ?>" />
						<div class="ht-submit">
							<button type="submit" name="horsetools_preset_apply"><i class="ti ti-wand"></i> <?php _e( 'Apply this preset', 'horse-tools' ); ?></button>
						</div>
					</form>
				</div>
			<?php endforeach; ?>
			<style>
			.ht-preset-list{list-style:none;margin:8px 0 4px;padding:0}
			.ht-preset-list li{display:flex;align-items:flex-start;gap:8px;padding:4px 0;font-size:13px;color:#444}
			.ht-preset-list li i{color:#2e9e5b;font-size:17px;flex-shrink:0;margin-top:1px}
			</style>
			</div>
		<?php if ( ! $embed ) : ?>
		</div>
	  </div>
	  <div class="ht-sidebar"></div>
	</div>
	</div>
		<?php endif; ?>

	<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function () {
		var dl = document.getElementById('horsetools-dow-json');
		var out = document.getElementById('horsetools-json');
		dl.addEventListener('click', function () {
			var data = out.value;
			if (!data.trim()) { return; }
			var d = new Date();
			var stamp = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
			var blob = new Blob([data], { type: 'application/json' });
			var url = URL.createObjectURL(blob);
			var a = document.createElement('a');
			a.href = url;
			a.download = 'horse-tools-' + stamp + '.json';
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
			URL.revokeObjectURL(url);
		});

		var pick = document.getElementById('horsetools-upload-button');
		var input = document.getElementById('horsetools-upload-json');
		var box = document.getElementById('horsetools-import-json');
		pick.addEventListener('click', function () { input.click(); });
		input.addEventListener('change', function (e) {
			var file = e.target.files[0];
			if (!file) { return; }
			var reader = new FileReader();
			reader.onload = function (ev) { box.value = ev.target.result; };
			reader.readAsText(file);
			input.value = '';
		});
	});
	</script>
	<?php
	require_once( HORSETOOLS_DIR . 'main/style.php' );
	echo ob_get_clean();
}
function horsetools_export_options_link() {
	add_submenu_page( 'horsetools-options', 'Backup', '<i class="ti ti-file-export" style="width:20px;"></i> ' . __( 'Backup', 'horse-tools' ), 'manage_options', 'horsetools-export-options', 'horsetools_export_options_page' );
}

// Menu removed in 1.2.81: now a tab on a grouped screen.
// add_action( 'admin_menu', 'horsetools_export_options_link' );
