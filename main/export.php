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

function horsetools_export_options_page() {
	$payload  = horsetools_export_payload();
	$json     = wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	$notice   = '';        // rendered above the form: array( type, html )
	$preview  = null;      // diff to show, plus the raw data to re-post
	$did_apply = false;

	// Handle the three POST actions. All share one nonce.
	if ( isset( $_POST['horsetools_import_preview'] ) || isset( $_POST['horsetools_import_apply'] ) || isset( $_POST['horsetools_import_undo'] ) ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'horse-tools' ) );
		}
		check_admin_referer( 'horsetools_import', 'horsetools_import_nonce' );

		if ( isset( $_POST['horsetools_import_undo'] ) ) {
			$ok     = horsetools_import_undo();
			$notice = $ok
				? array( 'ok', esc_html__( 'The previous configuration has been restored.', 'horse-tools' ) )
				: array( 'err', esc_html__( 'There is no backup to restore.', 'horse-tools' ) );
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="fa-regular fa-right-left"></i> <?php _e( 'BACKUP', 'horse-tools' ); ?></button>
		</div>
		<div class="ht-main">
			<?php
			if ( $notice ) {
				$cls = ( 'ok' === $notice[0] ) ? 'ht-updated' : 'ht-updated ht-updated-err';
				echo '<div class="' . esc_attr( $cls ) . '">' . $notice[1] . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped at assignment
			}
			?>

			<div class="sotab-box htbox" id="tab1">
			<h2><?php _e( 'BACKUP', 'horse-tools' ); ?></h2>

			<div class="ht-card">
			  <h3><i class="fa-regular fa-download"></i> <?php _e( 'Export', 'horse-tools' ); ?></h3>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e( 'This includes every setting group, whether or not its module is currently enabled. Uploaded font files are not included — they live on this site.', 'horse-tools' ); ?></p>
				<p>
				<textarea style="height:250px" class="ht-code-textarea" id="horsetools-json" readonly><?php echo esc_textarea( $json ); ?></textarea>
				</p>
				<button type="button" id="horsetools-dow-json"><i class="fa-regular fa-download"></i> <?php _e( 'Download .json', 'horse-tools' ); ?></button>
			</div>

			<div class="ht-card">
			  <h3><i class="fa-regular fa-upload"></i> <?php _e( 'Import', 'horse-tools' ); ?></h3>
				<form method="post" action="<?php echo esc_url( menu_page_url( 'horsetools-export-options', false ) ); ?>">
				<?php wp_nonce_field( 'horsetools_import', 'horsetools_import_nonce' ); ?>
				<p>
				<textarea style="height:200px" class="ht-code-textarea" id="horsetools-import-json" name="horsetools_export_tool" placeholder="<?php esc_attr_e( 'Paste an export here, or upload a .json file', 'horse-tools' ); ?>"><?php echo isset( $preview['raw'] ) ? esc_textarea( $preview['raw'] ) : ''; ?></textarea>
				</p>
				<input type="file" id="horsetools-upload-json" accept=".json,application/json" style="display:none;" />
				<button type="button" id="horsetools-upload-button"><i class="fa-regular fa-file-arrow-up"></i> <?php _e( 'Choose a file', 'horse-tools' ); ?></button>

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
					<p class="ht-note ht-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e( 'Applying overwrites these groups with the imported values. A one-click backup is kept so you can undo it.', 'horse-tools' ); ?></p>
					<div class="ht-submit">
						<button type="submit" name="horsetools_import_apply"><i class="fa-regular fa-file-import"></i> <?php _e( 'Apply import', 'horse-tools' ); ?></button>
					</div>
				<?php else : ?>
					<div class="ht-submit">
						<button type="submit" name="horsetools_import_preview"><i class="fa-regular fa-eye"></i> <?php _e( 'Preview changes', 'horse-tools' ); ?></button>
					</div>
				<?php endif; ?>
				</form>
			</div>

			<?php if ( $has_backup ) : ?>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-rotate-left"></i> <?php _e( 'Undo the last import', 'horse-tools' ); ?></h3>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e( 'The configuration from before your most recent import is stored. Restoring reverts every group to that snapshot.', 'horse-tools' ); ?></p>
				<form method="post" action="<?php echo esc_url( menu_page_url( 'horsetools-export-options', false ) ); ?>">
					<?php wp_nonce_field( 'horsetools_import', 'horsetools_import_nonce' ); ?>
					<div class="ht-submit">
						<button type="submit" name="horsetools_import_undo"><i class="fa-regular fa-rotate-left"></i> <?php _e( 'Restore previous configuration', 'horse-tools' ); ?></button>
					</div>
				</form>
			</div>
			<?php endif; ?>

			</div>
		</div>
	  </div>
	  <div class="ht-sidebar"></div>
	</div>
	</div>

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
	add_submenu_page( 'horsetools-options', 'Backup', '<i class="fa-regular fa-file-export" style="width:20px;"></i> ' . __( 'Backup', 'horse-tools' ), 'manage_options', 'horsetools-export-options', 'horsetools_export_options_page' );
}
add_action( 'admin_menu', 'horsetools_export_options_link' );
