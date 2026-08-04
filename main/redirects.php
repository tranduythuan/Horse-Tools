<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_redirects_options_page() {
	global $horsetools_redirects_options;
	ob_start(); 
	?>
	<div class="wrap ht-wrap">
	<div class="ht-wrap-top">
	</div>
	<div class="ht-wrap2">
	  <div class="ht-box">
		<div class="ht-menu">
			<div class="ht-logo ht-logoquay">
			<a class="ht-logoquaya" href="https://tranduythuan.com/" target="_blank">
			<span><?php horsetools_logo(); ?></span>
			</a>
			</div>
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="ti ti-compass"></i> <?php _e('Redirects (301)', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="ti ti-ban"></i> <?php _e('Broken links (404)', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab3')"><i class="ti ti-bug"></i> <?php _e('Maintenance (503)', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_redirects_settings_group'); ?> 
			<!-- 301 -->
			<div class="sotab-box htbox" id="tab1">
			<h2><?php _e('Redirects (301)', 'horse-tools'); ?></h2>
			<div class="ht-howto"><i class="ti ti-info-circle"></i><span><?php _e( 'When a URL changes or an old link should point somewhere new, this sends visitors (and Google) straight to the right page instead of a “not found” error — so you don’t lose traffic.', 'horse-tools' ); ?></span></div>
			<div class="ht-card">
			  <h3><i class="ti ti-compass"></i> <?php _e('Redirect 301 whole page', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi11', __( 'Enable site-wide 301 redirects', 'horse-tools' ), array(
					'module'  => 'redirect',
					'tab'     => '301',
					'section' => 'Redirect 301 whole page',
				) ); ?>
				<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[redi12]" value="<?php if(!empty($horsetools_redirects_options['redi12'])){echo sanitize_text_field($horsetools_redirects_options['redi12']);} ?>" />
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('This function will redirect all of your website pages to the destination page of your choice', 'horse-tools'); ?></p>
			  <h3><i class="ti ti-compass"></i> <?php _e('Redirect 301 to a custom page', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi1', __( 'Enable 301 redirection', 'horse-tools' ), array(
					'module'      => 'redirect',
					'tab'         => '301',
					'section'     => 'Redirect 301 to a custom page',
					'description' => __( 'This function allows you to redirect 301 links to the target page', 'horse-tools' ),
				) ); ?>

				<div id="sortable-list">
				<div data-id="1" class="ui-state-default ht-button-grid">
				<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[rechan11]" value="<?php if(!empty($horsetools_redirects_options['rechan11'])){echo sanitize_text_field($horsetools_redirects_options['rechan11']);} ?>" />
				<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[rechan21]" value="<?php if(!empty($horsetools_redirects_options['rechan21'])){echo sanitize_text_field($horsetools_redirects_options['rechan21']);} ?>" />
				</div>
				<?php
				if (is_array($horsetools_redirects_options) || is_object($horsetools_redirects_options)) {
					foreach ($horsetools_redirects_options as $key => $value) {
						if (preg_match('/^rechan1(\d+)$/', $key, $matches) && $matches[1] != 1) {
							$n = $matches[1];
							echo '<div data-id="' . $n . '" class="ui-state-default ht-button-grid">';
							echo '<input class="ht-input-big" placeholder="'. __('Enter the link', 'horse-tools') .'" type="text" name="horsetools_redirects_settings[rechan1' . $n . ']" value="' . sanitize_text_field($horsetools_redirects_options['rechan1' . $n]) . '" />';
							echo '<input class="ht-input-big" placeholder="'. __('Enter the link', 'horse-tools') .'" type="text" name="horsetools_redirects_settings[rechan2' . $n . ']" value="' . sanitize_text_field($horsetools_redirects_options['rechan2' . $n]) . '" />';
							echo '<span id="ht-chatx">&#x2715</span>';
							echo '</div>';
						}
					}
				}
				?>
				</div>
				<span id="ht-chatmore"><i class="ti ti-plus"></i> <?php _e('Add link', 'horse-tools'); ?></span>
			</div>
			<div class="ht-card">
			  <h3><i class="ti ti-wand"></i> <?php _e('Automatic redirects', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi-autoslug', __( 'Create a 301 automatically when a post permalink changes', 'horse-tools' ), array(
					'module'      => 'redirect',
					'tab'         => '301',
					'section'     => 'Automatic redirects',
					'description' => __( 'When you change a published post or page URL — its slug, its parent, or the whole path — the old address is redirected to the new one. WordPress already does this for a simple slug change; this also covers moves that core misses, and only ever acts on a URL that would otherwise 404.', 'horse-tools' ),
				) ); ?>
				<?php
				$autoslug = function_exists( 'horsetools_autoslug_list' ) ? horsetools_autoslug_list() : array();
				if ( ! empty( $autoslug ) ) :
				?>
				<table class="ht-404-table" data-autoslug-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_autoslug_action' ) ); ?>">
					<thead><tr>
						<th><?php _e( 'From', 'horse-tools' ); ?></th>
						<th><?php _e( 'To', 'horse-tools' ); ?></th>
						<th><?php _e( 'Actions', 'horse-tools' ); ?></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $autoslug as $key => $e ) : ?>
						<tr data-key="<?php echo esc_attr( $key ); ?>">
							<td class="ht-404-url"><?php echo esc_html( $e['from'] ); ?></td>
							<td class="ht-404-url"><?php echo esc_html( $e['to'] ); ?></td>
							<td class="ht-404-actions"><a href="javascript:void(0)" class="ht-autoslug-delete">&times;</a></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<p><a href="javascript:void(0)" class="ht-autoslug-clear"><?php _e( 'Clear all automatic redirects', 'horse-tools' ); ?></a></p>
				<?php elseif ( isset( $horsetools_redirects_options['redi-autoslug'] ) ) : ?>
					<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e( 'No automatic redirects yet. Change a published URL and one will appear here.', 'horse-tools' ); ?></p>
				<?php endif; ?>
			</div>
			</div>
			<!-- 404 -->
			<div class="sotab-box htbox" id="tab2" style="display:none">
			<h2><?php _e('Broken links (404)', 'horse-tools'); ?></h2>
			<div class="ht-howto"><i class="ti ti-info-circle"></i><span><?php _e( 'Handle dead links: send visitors who hit a “404 – not found” page to a page you choose, and keep a log of the broken URLs so you know what to fix.', 'horse-tools' ); ?></span></div>
			<div class="ht-card">
			  <h3><i class="ti ti-ban"></i> <?php _e('404 redirects', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi2', __( 'Enable 404 redirection', 'horse-tools' ), array(
					'module'  => 'redirect',
					'tab'     => '404',
					'section' => '404 redirects',
				) ); ?>
				<select id="horsetools-toc-page-select">
					<option value=""><?php _e('Select redirect page', 'horse-tools'); ?></option>
					<?php
					$pages = get_pages();
					foreach ($pages as $page) {
						echo '<option value="' . esc_attr($page->post_name) . '">' . esc_html($page->post_title) . '</option>';
					}
					?>
				</select>
				<div id="horsetools-toc-tags">
					<?php 
					if (!empty($horsetools_redirects_options['redi21'])) {
						$page_slug = $horsetools_redirects_options['redi21'];
						if (!empty($page_slug)) {
							echo '<span class="horsetools-toc-tag">' . esc_html($page_slug) . ' <span class="remove-tag" data-slug="' . esc_attr($page_slug) . '">&times;</span></span>';
						}
					} 
					?>
				</div>
				<input id="horsetools-hi-input" class="ht-input-big" type="text" style="display:none;" name="horsetools_redirects_settings[redi21]" value="<?php if(!empty($horsetools_redirects_options['redi21'])){echo sanitize_text_field($horsetools_redirects_options['redi21']);} ?>" />
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Redirect the 404 page to the homepage or a custom page of your choice, leave the field blank if you want to redirect to the homepage', 'horse-tools'); ?></p>
			</div>
			<div class="ht-card">
			  <h3><i class="ti ti-clipboard-list"></i> <?php _e('404 log', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi-404log', __( 'Record 404 hits', 'horse-tools' ), array(
					'module'      => 'redirect',
					'tab'         => '404',
					'section'     => '404 log',
					'description' => __( 'Log the dead URLs anonymous visitors actually hit, so you can turn the busy ones into redirects. Logged-in users, bots and asset requests are not recorded, and nothing leaves your site.', 'horse-tools' ),
				) ); ?>
				<?php
				$log_rows = function_exists( 'horsetools_404_recent' ) ? horsetools_404_recent( 100 ) : array();
				if ( ! empty( $log_rows ) ) :
				?>
				<table class="ht-404-table" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_404_action' ) ); ?>">
					<thead><tr>
						<th><?php _e( 'Requested URL', 'horse-tools' ); ?></th>
						<th><?php _e( 'Hits', 'horse-tools' ); ?></th>
						<th><?php _e( 'Last seen', 'horse-tools' ); ?></th>
						<th><?php _e( 'Actions', 'horse-tools' ); ?></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $log_rows as $row ) : ?>
						<tr data-id="<?php echo (int) $row->id; ?>" data-url="<?php echo esc_attr( $row->url ); ?>">
							<td class="ht-404-url"><?php echo esc_html( $row->url ); ?></td>
							<td><?php echo (int) $row->hits; ?></td>
							<td><?php echo esc_html( wp_date( 'Y-m-d H:i', strtotime( $row->last_seen ) ) ); ?></td>
							<td class="ht-404-actions">
								<a href="javascript:void(0)" class="ht-404-redirect" title="<?php esc_attr_e( 'Create a 301 redirect from this URL', 'horse-tools' ); ?>"><i class="ti ti-compass"></i> <?php _e( 'Redirect', 'horse-tools' ); ?></a>
								<a href="javascript:void(0)" class="ht-404-ignore" title="<?php esc_attr_e( 'Hide this URL from the log', 'horse-tools' ); ?>"><?php _e( 'Ignore', 'horse-tools' ); ?></a>
								<a href="javascript:void(0)" class="ht-404-delete" title="<?php esc_attr_e( 'Delete this log entry', 'horse-tools' ); ?>">&times;</a>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<p><a href="javascript:void(0)" class="ht-404-clear"><?php _e( 'Clear the whole log', 'horse-tools' ); ?></a></p>
				<?php elseif ( horsetools_404_logging_on() ) : ?>
					<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e( 'No 404s recorded yet.', 'horse-tools' ); ?></p>
				<?php endif; ?>
			</div>
			</div>
			<!-- 503 -->
			<div class="sotab-box htbox" id="tab3" style="display:none">
			<h2><?php _e('Maintenance (503)', 'horse-tools'); ?></h2>
			<div class="ht-howto"><i class="ti ti-info-circle"></i><span><?php _e( 'Temporarily close the site with a “under maintenance” notice while you make changes. You (and other logged-in admins) still see the site normally, so you can work in peace.', 'horse-tools' ); ?></span></div>
			<div class="ht-card">
			  <h3><i class="ti ti-bug"></i> <?php _e('Maintenance mode for developers (503)', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi3', __( 'Enable 503 maintenance mode', 'horse-tools' ), array(
					'module'      => 'redirect',
					'tab'         => '503',
					'section'     => 'Maintenance mode for developers (503)',
					'description' => __( 'All links on your website will redirect to the maintenance page, and only logged-in admin accounts can view the content', 'horse-tools' ),
				) ); ?>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter title', 'horse-tools') ?>" name="horsetools_redirects_settings[redi31]" type="text" value="<?php if(!empty($horsetools_redirects_options['redi31'])){echo sanitize_text_field($horsetools_redirects_options['redi31']);} ?>"/>
				</p>
				<p>
				<textarea style="height:150px;" class="ht-code-textarea" name="horsetools_redirects_settings[redi32]" placeholder="<?php _e('Enter content here', 'horse-tools'); ?>"><?php if(!empty($horsetools_redirects_options['redi32'])){echo esc_textarea($horsetools_redirects_options['redi32']);} ?></textarea>
				</p>
			</div>
			</div>
			<div class="ht-submit">
				<button type="submit"><i class="ti ti-device-floppy"></i> <?php _e('SAVE CONTENT', 'horse-tools'); ?></button>
			</div>
				<button id="ht-save-fast" type="submit"><i class="ti ti-device-floppy"></i></button>
			</form>
		</div>
	  </div>
      <div class="ht-sidebar">
	  </div>
	</div>	
	</div>
	<script>
        jQuery(document).ready(function($) {
			// them link
			var count = 0;
			$('#ht-chatmore').click(function() {
				var count = $('#sortable-list .ui-state-default:last').data('id') + 1;
				var newDiv = $('<div data-id="' + count + '" class="ui-state-default ht-button-grid">' +
					'<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[rechan1' + count + ']" />' +
					'<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[rechan2' + count + ']" />' +
					'<span id="ht-chatx">&#x2715</span>' +
					'</div>');
				$('#sortable-list').append(newDiv);
			});
			$('#sortable-list').on('click', '#ht-chatx', function() {
				$(this).parent('.ui-state-default').remove();
				count--;
			});
			// chon trang can chuyen
			function updateNoPageMessage() {
				if ($('#horsetools-toc-tags .horsetools-toc-tag').length === 0) {
					$('#horsetools-toc-tags').append('<span class="htno-page"><?php _e("No pages selected", "horse-tools"); ?></span>');
				} else {
					$('#horsetools-toc-tags .htno-page').remove();
				}
			}
			$('#horsetools-toc-page-select').change(function() {
				var selectedPage = $(this).val();
				if (selectedPage) {
					var formattedPage = selectedPage; // Prepend slash
					$('#horsetools-toc-tags').html('<span class="horsetools-toc-tag">' + formattedPage + ' <span class="remove-tag" data-slug="' + formattedPage + '">&times;</span></span>');
					$('#horsetools-hi-input').val(formattedPage); // Set the input value with slash
					updateNoPageMessage();
				}
				$(this).val('');
			});
			$(document).on('click', '.remove-tag', function() {
				$(this).parent('.horsetools-toc-tag').remove();
				$('#horsetools-hi-input').val('');
				updateNoPageMessage();
			});
			updateNoPageMessage();

			// ---- 404 log ----------------------------------------------------
			var log404Nonce = $('.ht-404-table').data('nonce');

			function log404Action(id, act, done) {
				$.post('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
					action: 'horsetools_404_log_action',
					security: log404Nonce,
					id: id,
					'do': act
				}).done(function () { if (done) { done(); } });
			}

			// Turn a logged 404 into a new 301 rule: add a row on the 301 tab with
			// the dead URL prefilled as the source, jump there, focus the target.
			$(document).on('click', '.ht-404-redirect', function () {
				var $row = $(this).closest('tr');
				var url = $row.data('url');
				var n = ($('#sortable-list .ui-state-default:last').data('id') || 0) + 1;
				var html = '<div data-id="' + n + '" class="ui-state-default ht-button-grid">' +
					'<input class="ht-input-big" type="text" name="horsetools_redirects_settings[rechan1' + n + ']" />' +
					'<input class="ht-input-big" type="text" name="horsetools_redirects_settings[rechan2' + n + ']" />' +
					'<span id="ht-chatx">&#x2715</span></div>';
				var $new = $(html);
				$('#sortable-list').append($new);
				$new.find('input').eq(0).val(url);
				// Make sure the 301 feature toggle is on, or the rule would not fire.
				$('#ht-redirect-redi1').prop('checked', true).trigger('change');
				var tabBtn = document.querySelector('[onclick*="tab1"]');
				if (tabBtn) { tabBtn.click(); }
				$new.find('input').eq(1).trigger('focus');
				log404Action($row.data('id'), 'redirected', function () { $row.remove(); });
			});

			$(document).on('click', '.ht-404-ignore', function () {
				var $row = $(this).closest('tr');
				log404Action($row.data('id'), 'ignore', function () { $row.remove(); });
			});
			$(document).on('click', '.ht-404-delete', function () {
				var $row = $(this).closest('tr');
				log404Action($row.data('id'), 'delete', function () { $row.remove(); });
			});
			$(document).on('click', '.ht-404-clear', function () {
				log404Action(0, 'clear', function () { $('.ht-404-table tbody').empty(); });
			});

			// ---- Automatic (slug-change) redirects --------------------------
			var autoslugNonce = $('[data-autoslug-nonce]').data('autoslug-nonce');
			function autoslugAction(data, done) {
				$.post('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
					$.extend({ action: 'horsetools_autoslug_action', security: autoslugNonce }, data)
				).done(function () { if (done) { done(); } });
			}
			$(document).on('click', '.ht-autoslug-delete', function () {
				var $row = $(this).closest('tr');
				autoslugAction({ 'do': 'delete', key: $row.data('key') }, function () { $row.remove(); });
			});
			$(document).on('click', '.ht-autoslug-clear', function () {
				autoslugAction({ 'do': 'clear' }, function () { $('[data-autoslug-nonce] tbody').empty(); });
			});
		});
	</script>
	<?php
	// style horsetools
	require_once( HORSETOOLS_DIR . 'main/style.php');
	echo ob_get_clean();
}
function horsetools_redirects_options_link() {
	add_submenu_page ('horsetools-options', 'Redirects', '<i class="ti ti-compass" style="width:20px;"></i> '. __('Redirects', 'horse-tools'), 'manage_options', 'horsetools-redirects-options', 'horsetools_redirects_options_page');
}
// Menu removed in 1.2.73: these settings are tabs on the SEO screen now.
// add_action('admin_menu', 'horsetools_redirects_options_link');
function horsetools_redirects_register_settings() {
	register_setting( 'horsetools_redirects_settings_group', 'horsetools_redirects_settings', array( 'sanitize_callback' => 'horsetools_sanitize_redirects' ) );
}
add_action('admin_init', 'horsetools_redirects_register_settings');
// clear cache
function horsetools_redirects_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_redirects_settings', 'options');
}
add_action('update_option_horsetools_redirects_settings', 'horsetools_redirects_settings_cache', 10, 2);

