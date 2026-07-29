<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Register the scheduling option. It lives in its own option — not in
 * horsetools_settings — because options.php replaces the whole option it is
 * given, so a partial form saving into horsetools_settings would wipe every
 * other key. See inc/sanitize.php for the callback.
 */
function horsetools_clean_register_settings() {
	register_setting(
		'horsetools_clean_settings_group',
		'horsetools_clean_settings',
		array( 'sanitize_callback' => 'horsetools_sanitize_clean' )
	);
}
add_action( 'admin_init', 'horsetools_clean_register_settings' );

/**
 * One delete button: a labelled link plus a live count and a hidden confirm
 * payload. The count is filled by the preview request on page load.
 */
function horsetools_clean_button( $id, $action, $nonce_action, $result_sel, $estimated = false ) {
	$targets = horsetools_clean_targets();
	$label   = isset( $targets[ $id ] ) ? $targets[ $id ]['label'] : $id;
	?>
	<a href="javascript:void(0)"
		class="ht-clean-btn"
		data-ht-clean="<?php echo esc_attr( $id ); ?>"
		data-ht-action="<?php echo esc_attr( $action ); ?>"
		data-ht-nonce="<?php echo esc_attr( wp_create_nonce( $nonce_action ) ); ?>"
		data-ht-result="<?php echo esc_attr( $result_sel ); ?>"
		data-ht-label="<?php echo esc_attr( $label ); ?>"
		data-ht-estimated="<?php echo $estimated ? '1' : '0'; ?>">
		<i class="ti ti-trash" aria-hidden="true"></i>
		<span class="ht-clean-btn-label"><?php echo esc_html( $label ); ?></span>
		<span class="ht-clean-count" data-ht-count="<?php echo esc_attr( $id ); ?>"></span>
	</a>
	<?php
}

function horsetools_clean_options_page() {
	$targets    = horsetools_clean_targets();
	$next_run   = wp_next_scheduled( 'horsetools_scheduled_clean' );
	$freq_opts  = array(
		'off'     => __( 'Off', 'horse-tools' ),
		'daily'   => __( 'Daily', 'horse-tools' ),
		'weekly'  => __( 'Weekly', 'horse-tools' ),
		'monthly' => __( 'Monthly', 'horse-tools' ),
	);
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="ti ti-pin"></i> <?php _e( 'CONTENT', 'horse-tools' ); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="ti ti-message-circle"></i> <?php _e( 'COMMENT', 'horse-tools' ); ?></button>
			<button class="sotab" onclick="httab(event, 'tab3')"><i class="ti ti-photo"></i> <?php _e( 'MEDIA', 'horse-tools' ); ?></button>
			<button class="sotab" onclick="httab(event, 'tab4')"><i class="ti ti-history"></i> <?php _e( 'SCHEDULE', 'horse-tools' ); ?></button>
		</div>
		<div class="ht-main">

			<!-- CONTENT -->
			<div class="sotab-box htbox" id="tab1" style="margin-bottom:-60px;">
			<h2><?php _e( 'CONTENT', 'horse-tools' ); ?></h2>
			<div class="ht-card">
			   <h3><i class="ti ti-trash"></i> <?php _e( 'Optimize deletion of content in the database', 'horse-tools' ); ?></h3>
				<div class="ht-del">
				<?php
				horsetools_clean_button( 'revisions', 'horsetools_delete_revisions', 'horsetools_post_revisions', '#del-result' );
				horsetools_clean_button( 'auto_drafts', 'horsetools_delete_auto_drafts', 'horsetools_post_drafts', '#del-result' );
				horsetools_clean_button( 'trashed_posts', 'horsetools_delete_all_trashed_posts', 'horsetools_post_trashed', '#del-result' );
				?>
				</div>
				<div class="edel" style="display:none"><div class="ht-sload"></div> <?php _e( 'Please wait', 'horse-tools' ); ?></div>
				<div id="del-result" class="ht-clean-result"></div>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Deleting is permanent. Revisions, autosaves and trashed content (posts, pages, products) are removed together with their metadata and attached files.', 'horse-tools' ); ?></p>
			</div>
			</div>

			<!-- COMMENT -->
			<div class="sotab-box htbox" id="tab2" style="display:none;margin-bottom:-60px;">
			<h2><?php _e( 'COMMENT', 'horse-tools' ); ?></h2>
			<div class="ht-card">
			   <h3><i class="ti ti-trash"></i> <?php _e( 'Delete comments', 'horse-tools' ); ?></h3>
				<div class="ht-del">
				<?php
				horsetools_clean_button( 'comments_pending', 'horsetools_del_comenpend', 'horsetools_del_comenpend_nonce', '#del-result2' );
				horsetools_clean_button( 'comments_spam', 'horsetools_del_comenspam', 'horsetools_del_comenspam_nonce', '#del-result2' );
				horsetools_clean_button( 'comments_trash', 'horsetools_del_comentrash', 'horsetools_del_comentrash_nonce', '#del-result2' );
				horsetools_clean_button( 'comments_links', 'horsetools_del_comenlink', 'horsetools_del_comenlink_nonce', '#del-result2', true );
				?>
				</div>
				<div class="edel2" style="display:none"><div class="ht-sload"></div> <?php _e( 'Please wait', 'horse-tools' ); ?></div>
				<div id="del-result2" class="ht-clean-result"></div>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( '"Comments containing links" matches links in the comment body only. The figure shown is the total comment count, not a match count, so confirm carefully.', 'horse-tools' ); ?></p>
			</div>
			</div>

			<!-- MEDIA -->
			<div class="sotab-box htbox" id="tab3" style="display:none;margin-bottom:-60px;">
			<h2><?php _e( 'MEDIA', 'horse-tools' ); ?></h2>
			<div class="ht-card">
			   <h3><i class="ti ti-photo-off"></i> <?php _e( 'Find and delete all 404 images in media', 'horse-tools' ); ?></h3>
				<div class="ht-del">
				<?php horsetools_clean_button( 'media_404', 'horsetools_delete_media', 'horsetools_media_del', '#del-media', true ); ?>
				</div>
				<div class="emed" style="display:none"><div class="ht-sload"></div> <?php _e( 'Please wait', 'horse-tools' ); ?></div>
				<div id="del-media" class="ht-clean-result"></div>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Removes attachments whose file is missing from disk. Every attachment is scanned, so the count is known only after it runs.', 'horse-tools' ); ?></p>
			   <h3><i class="ti ti-photo-off"></i> <?php _e( 'Find and delete all 404 thumbnail images in media', 'horse-tools' ); ?></h3>
				<div class="ht-del">
				<?php horsetools_clean_button( 'media_thumbs_404', 'horsetools_delete_media_thum', 'horsetools_media_thum_del', '#del-media-thum', true ); ?>
				</div>
				<div class="emed-thum" style="display:none"><div class="ht-sload"></div> <?php _e( 'Please wait', 'horse-tools' ); ?></div>
				<div id="del-media-thum" class="ht-clean-result"></div>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Removes metadata entries for thumbnail files that are missing from disk. It does not delete images.', 'horse-tools' ); ?></p>
			  <h3><i class="ti ti-photo-off"></i> <?php _e( 'Delete cropped image', 'horse-tools' ); ?></h3>
				<div class="ht-card-note ht-del-crop">
				<?php
				global $_wp_additional_image_sizes;
				$image_sizes = get_intermediate_image_sizes();
				if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) > 0 ) {
					$image_sizes = array_merge( $image_sizes, array_keys( $_wp_additional_image_sizes ) );
				}
				$image_sizes = array_unique( $image_sizes );
				foreach ( $image_sizes as $size ) {
					$width = isset( $_wp_additional_image_sizes[ $size ]['width'] ) ? $_wp_additional_image_sizes[ $size ]['width'] : get_option( $size . '_size_w' );
					?>
					<p>
						<a href="javascript:void(0)" class="ht-cropdel" data-size="<?php echo esc_attr( $size ); ?>"><i class="ti ti-trash"></i> <?php echo esc_html( $size . ' (W: ' . $width . ')' ); ?></a>
					</p>
				<?php } ?>
				</div>
				<div id="delete-size-end" class="ht-clean-result"></div>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Use with care: your theme may need several image sizes to display correctly.', 'horse-tools' ); ?></p>
			</div>
			</div>

			<!-- SCHEDULE -->
			<div class="sotab-box htbox" id="tab4" style="display:none;margin-bottom:-60px;">
			<h2><?php _e( 'SCHEDULE', 'horse-tools' ); ?></h2>
			<div class="ht-card">
			   <h3><i class="ti ti-history"></i> <?php _e( 'Automatic cleanup', 'horse-tools' ); ?></h3>
				<form method="post" action="options.php">
				<?php settings_fields( 'horsetools_clean_settings_group' ); ?>
				<?php
				foreach ( $targets as $id => $target ) {
					if ( empty( $target['schedulable'] ) ) {
						continue;
					}
					$field   = 'cron-' . $id;
					$current = horsetools_opt( 'clean', $field, 'off' );
					$field_id = 'ht-clean-' . sanitize_html_class( $id );
					?>
					<p class="ht-field">
						<label class="ht-field-label" for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $target['label'] ); ?></label>
						<select id="<?php echo esc_attr( $field_id ); ?>" name="horsetools_clean_settings[<?php echo esc_attr( $field ); ?>]">
							<?php foreach ( $freq_opts as $value => $text ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $text ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
				}
				?>
				<p class="ht-note"><i class="ti ti-bulb"></i>
					<?php _e( 'Cleanup runs on WordPress cron, which only fires when your site receives traffic. Weekly and monthly are measured from the last run.', 'horse-tools' ); ?>
					<?php if ( $next_run ) : ?>
						<br><?php printf( esc_html__( 'Next automatic check: %s', 'horse-tools' ), esc_html( wp_date( 'Y-m-d H:i', $next_run ) ) ); ?>
					<?php endif; ?>
				</p>
				<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Deleting comments by link pattern is intentionally excluded from automatic cleanup — it stays a manual action.', 'horse-tools' ); ?></p>
				<div class="ht-submit"><button type="submit"><i class="ti ti-device-floppy"></i> <?php _e( 'Save schedule', 'horse-tools' ); ?></button></div>
				</form>
			</div>
			</div>

		</div>
	  </div>
	  <div class="ht-sidebar"></div>
	</div>
	</div>

	<!-- Confirmation modal, reused by every delete button -->
	<div class="ht-updated-main ht-confirm" id="ht-clean-confirm" style="display:none" role="dialog" aria-modal="true" aria-labelledby="ht-confirm-title">
		<div class="ht-updated-card">
			<i class="ti ti-alert-triangle" aria-hidden="true"></i>
			<div class="ht-updated-card-tit" id="ht-confirm-title"><?php _e( 'Confirm deletion', 'horse-tools' ); ?></div>
			<p id="ht-confirm-body"></p>
			<label class="ht-confirm-ack">
				<input type="checkbox" id="ht-confirm-ack">
				<span><?php _e( 'I understand this permanently deletes data.', 'horse-tools' ); ?></span>
			</label>
			<div class="ht-confirm-actions">
				<button type="button" class="ht-confirm-cancel"><?php _e( 'Cancel', 'horse-tools' ); ?></button>
				<button type="button" class="ht-confirm-go" disabled><?php _e( 'Delete', 'horse-tools' ); ?></button>
			</div>
		</div>
	</div>

	<script>
	jQuery(function ($) {
		var ajaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
		var previewNonce = '<?php echo esc_js( wp_create_nonce( 'horsetools_clean_preview' ) ); ?>';
		var counts = {};

		var i18n = {
			estimated: <?php echo wp_json_encode( __( 'up to %s', 'horse-tools' ) ); ?>,
			deleted:   <?php echo wp_json_encode( __( 'Deleted: %s', 'horse-tools' ) ); ?>,
			scanned:   <?php echo wp_json_encode( __( 'Deleted %1$s of %2$s scanned', 'horse-tools' ) ); ?>,
			error:     <?php echo wp_json_encode( __( 'Error — nothing was deleted.', 'horse-tools' ) ); ?>,
			nothing:   <?php echo wp_json_encode( __( 'Nothing to delete.', 'horse-tools' ) ); ?>,
			confirmN:  <?php echo wp_json_encode( __( 'Permanently delete %1$s from “%2$s”? This cannot be undone.', 'horse-tools' ) ); ?>,
			confirmEst:<?php echo wp_json_encode( __( 'Run “%s” now? This permanently deletes matching items and cannot be undone.', 'horse-tools' ) ); ?>
		};

		function fmt(n) { return Number(n).toLocaleString(); }
		function bytes(b) {
			if (!b) { return ''; }
			var u = ['B', 'KB', 'MB', 'GB'], i = 0;
			while (b >= 1024 && i < u.length - 1) { b /= 1024; i++; }
			return (Math.round(b * 10) / 10) + ' ' + u[i];
		}

		// Fill the counts next to every button.
		function loadPreview() {
			$.post(ajaxUrl, { action: 'horsetools_clean_preview', security: previewNonce })
				.done(function (res) {
					if (!res || !res.success) { return; }
					counts = res.data;
					$('.ht-clean-count').each(function () {
						var id = $(this).data('ht-count');
						var c = counts[id];
						if (!c) { return; }
						var txt;
						if (c.estimated) {
							txt = ' (' + i18n.estimated.replace('%s', fmt(c.count)) + ')';
						} else {
							txt = ' (' + fmt(c.count);
							if (c.size) { txt += ' · ~' + bytes(c.size); }
							txt += ')';
						}
						$(this).text(txt);
						// Disable a known-empty target (but never the estimated ones,
						// whose real count is unknown until they run).
						if (!c.estimated && c.count === 0) {
							$(this).closest('.ht-clean-btn').addClass('ht-clean-empty');
						} else {
							$(this).closest('.ht-clean-btn').removeClass('ht-clean-empty');
						}
					});
				});
		}
		loadPreview();

		// Confirmation modal.
		var $modal = $('#ht-clean-confirm');
		var $ack = $('#ht-confirm-ack');
		var $go = $modal.find('.ht-confirm-go');
		var pending = null;

		function openConfirm(cfg, message) {
			pending = cfg;
			$('#ht-confirm-body').text(message);
			$ack.prop('checked', false);
			$go.prop('disabled', true);
			$modal.css('display', 'block');
			$ack.trigger('focus');
		}
		function closeConfirm() { $modal.hide(); pending = null; }

		$ack.on('change', function () { $go.prop('disabled', !this.checked); });
		$modal.find('.ht-confirm-cancel').on('click', closeConfirm);
		$modal.on('click', function (e) { if (e.target === this) { closeConfirm(); } });
		$(document).on('keyup', function (e) { if (e.key === 'Escape' && $modal.is(':visible')) { closeConfirm(); } });

		// One confirm handler. The crop endpoint returns a plain string rather
		// than the {deleted_count} shape, so it gets its own small runner.
		$go.on('click', function () {
			if (!pending) { return; }
			var cfg = pending;
			closeConfirm();
			if (cfg.extra && cfg.extra.size) {
				var $result = $(cfg.result);
				$.post(ajaxUrl, { action: cfg.action, security: cfg.nonce, size: cfg.extra.size })
					.done(function (res) {
						$result.html('<span class="ht-clean-ok">' + ((res && res.data) ? res.data : '') + '</span>');
						loadPreview();
					})
					.fail(function () { $result.html('<span class="ht-clean-err">' + i18n.error + '</span>'); });
			} else {
				runDelete(cfg);
			}
		});

		function runDelete(cfg) {
			var $result = $(cfg.result);
			var $spin = $result.closest('.ht-card').find('.edel, .edel2, .emed, .emed-thum').first();
			$spin.show();
			$.post(ajaxUrl, { action: cfg.action, security: cfg.nonce })
				.done(function (res) {
					$spin.hide();
					if (!res || !res.success) {
						$result.html('<span class="ht-clean-err">' + i18n.error + '</span>');
						return;
					}
					var d = res.data || {};
					var n = (typeof d.deleted_count !== 'undefined') ? d.deleted_count : 0;
					var msg;
					if (typeof d.scanned !== 'undefined') {
						msg = i18n.scanned.replace('%1$s', fmt(n)).replace('%2$s', fmt(d.scanned));
					} else if (n === 0) {
						msg = i18n.nothing;
					} else {
						msg = i18n.deleted.replace('%s', fmt(n));
					}
					$result.html('<span class="ht-clean-ok">' + msg + '</span>');
					loadPreview();
				})
				.fail(function () {
					$spin.hide();
					$result.html('<span class="ht-clean-err">' + i18n.error + '</span>');
				});
		}

		// Every delete button routes through the confirmation modal.
		$('.ht-clean-btn').on('click', function (e) {
			e.preventDefault();
			var $btn = $(this);
			if ($btn.hasClass('ht-clean-empty')) { return; }
			var id = $btn.data('ht-clean');
			var cfg = {
				action: $btn.data('ht-action'),
				nonce:  $btn.data('ht-nonce'),
				result: $btn.data('ht-result'),
			};
			var label = $btn.data('ht-label');
			var c = counts[id];
			var message;
			if (c && !c.estimated) {
				if (c.count === 0) { return; }
				message = i18n.confirmN.replace('%1$s', fmt(c.count)).replace('%2$s', label);
			} else {
				message = i18n.confirmEst.replace('%s', label);
			}
			openConfirm(cfg, message);
		});

		// Cropped-image deletion keeps its own endpoint but now uses the modal.
		$('.ht-cropdel').on('click', function (e) {
			e.preventDefault();
			var size = $(this).data('size');
			openConfirm({
				action: 'horsetools_delete_images_by_size',
				nonce: '<?php echo esc_js( wp_create_nonce( 'horsetools_delete_crop_nonce' ) ); ?>',
				result: '#delete-size-end',
				extra: { size: size }
			}, i18n.confirmEst.replace('%s', size));
		});
	});
	</script>
	<?php
	require_once( HORSETOOLS_DIR . 'main/style.php' );
	echo ob_get_clean();
}

function horsetools_clean_options_link() {
	add_submenu_page( 'horsetools-options', 'Clean', '<i class="ti ti-wash" style="width:20px;"></i> ' . __( 'Clean', 'horse-tools' ), 'manage_options', 'horsetools-clean-options', 'horsetools_clean_options_page' );
}
add_action( 'admin_menu', 'horsetools_clean_options_link' );
