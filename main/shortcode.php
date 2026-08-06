<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_shortcode_options_page() {
	global $horsetools_shortcode_options;
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="ti ti-lock"></i> <?php _e('LOCKVIP', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="ti ti-signature"></i> <?php _e('SIGN', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab3')"><i class="ti ti-calendar"></i> <?php _e('DATE', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab4')"><i class="ti ti-download"></i> <?php _e('GGET', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab5')"><i class="ti ti-mood-smile"></i> <?php _e('ICON', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab6')"><i class="ti ti-file-code"></i> <?php _e('SNIPPETS', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_shortcode_settings_group'); ?> 
			<!-- LOCKVIP -->
			<div class="sotab-box htbox" id="tab1" >
			<h2><?php _e('LOCKVIP', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-lock"></i> <?php _e('Shortcode content visible only to group of users', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'shortcode-s1', __( 'Enable shortcode lock', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'LOCKVIP',
					'section' => 'Shortcode content visible only to group of users',
				) ); ?>
				<?php
				$roles = get_editable_roles();
				echo '<select name="horsetools_shortcode_settings[shortcode-s11]">';
				echo '<option value="">Default</option>';
				foreach ($roles as $role_name => $role_info) {
					if ( true ) { // all roles selectable; horsetools_user_meets_role() ranks them
					if(isset($horsetools_shortcode_options['shortcode-s11']) && $horsetools_shortcode_options['shortcode-s11'] == $role_name) { $selected = 'selected="selected"'; } else { $selected = NULL; }
					echo '<option value="'. $role_name .'" '. $selected .'>'. $role_name .'</option>';
					}
				}
				echo '</select>';
				?>
				<label class="ht-right-text"><?php _e('Role', 'horse-tools'); ?></label>
				<p>
				<textarea style="height:100px;" class="ht-code-textarea" name="horsetools_shortcode_settings[shortcode-s12]" placeholder="<?php _e('Enter note content', 'horse-tools'); ?>"><?php if(!empty($horsetools_shortcode_options['shortcode-s12'])){echo esc_textarea($horsetools_shortcode_options['shortcode-s12']);} ?></textarea>
				</p>
				<input class="ht-input-big ht-view-in" type="text" value="[vip] <?php _e('Content to be hidden', 'horse-tools'); ?> [/vip]"/>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('This shortcode allows you to lock any content, and only the selected group of logged-in users can view it', 'horse-tools'); ?></p>     
			</div>
			</div>
			<!-- SIGN -->
			<div class="sotab-box htbox" id="tab2" style="display:none">
			<h2><?php _e('SIGN', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-signature"></i> <?php _e('Signature shortcode', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'shortcode-s2', __( 'Enable signature shortcode', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'SIGN',
					'section' => 'Signature shortcode',
				) ); ?>
				<div class="ht-classic">
				<?php
				$shortcode_s21 = !empty($horsetools_shortcode_options['shortcode-s21']) ? wp_kses_post($horsetools_shortcode_options['shortcode-s21']) : '';
				ob_start();
				wp_editor(
					$shortcode_s21,
					'userpostcontent',
					array(
						'textarea_name' => 'horsetools_shortcode_settings[shortcode-s21]',
						'media_buttons' => false,
					)
				);
				$editor_contents = ob_get_clean();
				echo $editor_contents;
				?>
				</div>
				<p>
				<input class="ht-input-big ht-view-in" type="text" value="[sign]"/>
				</p>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('If you want to display your signature anywhere, you can create content above and then use the generated shortcode at your desired location', 'horse-tools'); ?></p>   				
			</div>
			</div>
			<!-- DATE -->
			<div class="sotab-box htbox" id="tab3" style="display:none">
			<h2><?php _e('DATE', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-calendar"></i> <?php _e('Shortcode to display date', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'shortcode-s3', __( 'Enable date shortcode', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'DATE',
					'section' => 'Shortcode to display date',
				) ); ?>
				<p>
				<?php $styles = array('VI', 'EN'); ?>
				<select name="horsetools_shortcode_settings[shortcode-s31]"> 
				<?php foreach($styles as $style) { ?> 
				<?php if(isset($horsetools_shortcode_options['shortcode-s31']) && $horsetools_shortcode_options['shortcode-s31'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
				<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
				<?php } ?> 
				</select>
				<label class="ht-right-text"><?php _e('Display type', 'horse-tools'); ?></label>
				</p>
				<p><input class="ht-input-big ht-view-in" type="text" value="[titday]"/></p>
				<p><input class="ht-input-big ht-view-in" type="text" value="[titmonth]"/></p>
				<p><input class="ht-input-big ht-view-in" type="text" value="[tityear]"/></p>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('This shortcode is used to display the date in the post title. Please note that you need to enable the shortcode usage in the post title under the POST, PAGE section', 'horse-tools'); ?></p>   				
			</div>
			</div>
			<!-- GGET -->
			<div class="sotab-box htbox" id="tab4" style="display:none">
			<h2><?php _e('GGET', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-download"></i> <?php _e('Download button GGET shortcode', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'shortcode-s4', __( 'Enable GGET shortcode', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'GGET',
					'section' => 'Download button GGET shortcode',
				) ); ?>
				<?php horsetools_toggle( 'shortcode-s4a', __( 'Display link when seconds expire', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'GGET',
					'section' => 'Download button GGET shortcode',
				) ); ?>
				<?php horsetools_toggle( 'shortcode-s4b', __( 'Center-align button on page', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'GGET',
					'section' => 'Download button GGET shortcode',
				) ); ?>
				<p>
				<input class="ht-input-small" placeholder="10" name="horsetools_shortcode_settings[shortcode-s41]" type="number" value="<?php if(!empty($horsetools_shortcode_options['shortcode-s41'])){echo $horsetools_shortcode_options['shortcode-s41'];} ?>"/>
				<label class="ht-label-right"><?php _e('Enter waiting time', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_shortcode_settings[shortcode-s42]" type="text" data-coloris value="<?php if(!empty($horsetools_shortcode_options['shortcode-s42'])){echo $horsetools_shortcode_options['shortcode-s42'];} ?>"/>
				<label class="ht-right-text"><?php _e('Select button color', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_shortcode_settings[shortcode-s43]" type="text" data-coloris value="<?php if(!empty($horsetools_shortcode_options['shortcode-s43'])){echo $horsetools_shortcode_options['shortcode-s43'];} ?>"/>
				<label class="ht-right-text"><?php _e('Select button border color', 'horse-tools'); ?></label>
				</p>
				<p class="ht-keo">
				<input type="range" name="horsetools_shortcode_settings[shortcode-s44]" min="1" max="7" value="<?php if(!empty($horsetools_shortcode_options['shortcode-s44'])){echo sanitize_text_field($horsetools_shortcode_options['shortcode-s44']);} else { echo '2';} ?>" class="htslide" data-index="7">
				<span><?php _e('Border size', 'horse-tools'); ?> <span id="demo7"></span> PX</span>
				</p>
				<p class="ht-keo">
				<input type="range" name="horsetools_shortcode_settings[shortcode-s45]" min="1" max="50" value="<?php if(!empty($horsetools_shortcode_options['shortcode-s45'])){echo sanitize_text_field($horsetools_shortcode_options['shortcode-s45']);} else { echo '10';} ?>" class="htslide" data-index="8">
				<span><?php _e('Border radius', 'horse-tools'); ?> <span id="demo8"></span> PX</span>
				</p>
				
				<p><input class='ht-input-big ht-view-in' type='text' value='[gget url="<?php _e('Download link', 'horse-tools'); ?>"/]'/></p>
				<p><input class='ht-input-big ht-view-in' type='text' value='[gget url="<?php _e('Download link', 'horse-tools'); ?>"] <?php _e('Button name', 'horse-tools'); ?> [/gget]' /></p>
				<p><input class='ht-input-big ht-view-in' type='text' value='[gget aff="<?php _e('Other links', 'horse-tools'); ?>" url="<?php _e('Download link', 'horse-tools'); ?>"] <?php _e('Button name', 'horse-tools'); ?> [/gget]' /></p>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('This shortcode is used to create a download button with a timeout', 'horse-tools'); ?></p>  
			</div>
			</div>
			<!-- ICON -->
			<div class="sotab-box htbox" id="tab5" style="display:none">
			<h2><?php _e('ICON', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-mood-smile"></i> <?php _e('Insert icons anywhere with a shortcode', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'shortcode-s5', __( 'Enable the [ht-icon] shortcode', 'horse-tools' ), array(
					'module'      => 'shortcode',
					'tab'         => 'ICON',
					'section'     => 'Insert icons anywhere with a shortcode',
					'description' => __( 'Lets you drop any of the bundled Tabler icons (over 5,800, MIT licence) into posts, pages and anywhere shortcodes run. The icon font only loads on pages that actually use one, so it costs nothing elsewhere.', 'horse-tools' ),
				) ); ?>

				<div class="ht-icon-guide">
					<p><b><?php _e('How to use', 'horse-tools'); ?></b></p>
					<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Search below, click any icon to copy its ready-made shortcode, then paste it where you want the icon to appear.', 'horse-tools'); ?></p>
					<table class="ht-icon-usage">
						<tr>
							<td><code>[ht-icon name="heart"]</code></td>
							<td><?php _e('The icon on its own.', 'horse-tools'); ?></td>
						</tr>
						<tr>
							<td><code>[ht-icon name="heart" size="32"]</code></td>
							<td><?php _e('Set the size in pixels.', 'horse-tools'); ?></td>
						</tr>
						<tr>
							<td><code>[ht-icon name="heart" color="#e11"]</code></td>
							<td><?php _e('Set the colour (hex, rgb or a CSS name).', 'horse-tools'); ?></td>
						</tr>
						<tr>
							<td><code>[ht-icon name="loader" spin="1"]</code></td>
							<td><?php _e('Spin it — handy for loaders.', 'horse-tools'); ?></td>
						</tr>
						<tr>
							<td><code>[ht-icon name="phone" label="Call us"]</code></td>
							<td><?php _e('Add a label so screen readers announce it.', 'horse-tools'); ?></td>
						</tr>
					</table>
				</div>

				<div class="ht-iconbrowser" data-src="<?php echo esc_url( HORSETOOLS_URL . 'link/tabler/icons.json' ); ?>">
					<div class="ht-iconbrowser-bar">
						<input type="search" id="ht-icon-search" class="ht-input-big" placeholder="<?php esc_attr_e( 'Search icons — e.g. cart, arrow, brand-facebook…', 'horse-tools' ); ?>" autocomplete="off" />
						<span id="ht-icon-count" class="ht-icon-count"></span>
					</div>
					<div id="ht-icon-grid" class="ht-icon-grid" role="listbox" aria-label="<?php esc_attr_e( 'Icon picker', 'horse-tools' ); ?>">
						<p class="ht-note ht-icon-loading"><i class="ti ti-loader-2 ht-icon-spin"></i> <?php _e('Loading icons…', 'horse-tools'); ?></p>
					</div>
					<div id="ht-icon-copied" class="ht-icon-copied" aria-live="polite"></div>
				</div>

				<style>
				.ht-icon-usage{border-collapse:collapse;margin:8px 0 4px}
				.ht-icon-usage td{padding:4px 12px 4px 0;vertical-align:top;font-size:13px}
				.ht-icon-usage code{background:#fff8e1;border:1px solid #f0d98a;border-radius:5px;padding:2px 7px;color:#8a5a00;white-space:nowrap}
				.ht-iconbrowser{margin-top:14px}
				.ht-iconbrowser-bar{display:flex;align-items:center;gap:12px;margin-bottom:10px}
				.ht-iconbrowser-bar input{max-width:420px}
				.ht-icon-count{color:#888;font-size:12px;white-space:nowrap}
				.ht-icon-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(88px,1fr));gap:8px;max-height:470px;overflow:auto;padding:4px;border:1px solid #eee;border-radius:10px;background:#fafafa}
				.ht-icon-spin{display:inline-block;animation:ht-icon-spin 1s linear infinite}
				@keyframes ht-icon-spin{to{transform:rotate(360deg)}}
				.ht-icon-cell{display:flex;flex-direction:column;align-items:center;gap:6px;padding:11px 4px;border:1px solid #ececec;border-radius:9px;background:#fff;cursor:pointer;transition:border-color .12s,background .12s;text-align:center}
				.ht-icon-cell:hover,.ht-icon-cell:focus{border-color:#e0a800;background:#fff9e6;outline:none}
				.ht-icon-cell i{font-size:26px;color:#444}
				.ht-icon-cell span{font-size:10px;line-height:1.2;color:#999;max-width:80px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
				.ht-icon-copied{position:fixed;left:50%;bottom:28px;transform:translateX(-50%) translateY(20px);background:#222;color:#fff;padding:9px 18px;border-radius:24px;font-size:13px;opacity:0;pointer-events:none;transition:opacity .2s,transform .2s;z-index:99999}
				.ht-icon-copied.show{opacity:1;transform:translateX(-50%) translateY(0)}
				</style>
				<script>
				(function(){
					var wrap = document.querySelector('.ht-iconbrowser');
					if (!wrap || wrap.dataset.ready) { return; }
					wrap.dataset.ready = '1';
					var grid   = document.getElementById('ht-icon-grid');
					var search = document.getElementById('ht-icon-search');
					var count  = document.getElementById('ht-icon-count');
					var toast  = document.getElementById('ht-icon-copied');
					var CAP    = 300; // render at most this many — 5,800 nodes at once is needless
					var all    = [];
					var toastTimer;

					function esc(s){ return s.replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }

					function showToast(msg){
						toast.textContent = msg;
						toast.classList.add('show');
						clearTimeout(toastTimer);
						toastTimer = setTimeout(function(){ toast.classList.remove('show'); }, 1600);
					}

					function copy(text){
						if (navigator.clipboard && navigator.clipboard.writeText) {
							navigator.clipboard.writeText(text).then(function(){ showToast('<?php echo esc_js( __( 'Copied: ', 'horse-tools' ) ); ?>' + text); });
						} else {
							var ta = document.createElement('textarea');
							ta.value = text; document.body.appendChild(ta); ta.select();
							try { document.execCommand('copy'); showToast('<?php echo esc_js( __( 'Copied: ', 'horse-tools' ) ); ?>' + text); } catch(e){}
							document.body.removeChild(ta);
						}
					}

					function render(list){
						var shown = list.slice(0, CAP);
						var html = '';
						for (var i = 0; i < shown.length; i++) {
							var n = shown[i];
							html += '<div class="ht-icon-cell" role="option" tabindex="0" data-name="' + esc(n) + '" title="[ht-icon name=&quot;' + esc(n) + '&quot;]"><i class="ti ti-' + esc(n) + '"></i><span>' + esc(n) + '</span></div>';
						}
						grid.innerHTML = html || '<p class="ht-note"><?php echo esc_js( __( 'No icon matches that.', 'horse-tools' ) ); ?></p>';
						var suffix = list.length > CAP ? ' (<?php echo esc_js( __( 'showing first', 'horse-tools' ) ); ?> ' + CAP + ')' : '';
						count.textContent = list.length + ' <?php echo esc_js( __( 'icons', 'horse-tools' ) ); ?>' + suffix;
					}

					function filter(){
						var q = search.value.trim().toLowerCase().replace(/\s+/g,'-');
						if (!q) { render(all); return; }
						render(all.filter(function(n){ return n.indexOf(q) !== -1; }));
					}

					grid.addEventListener('click', function(e){
						var cell = e.target.closest('.ht-icon-cell');
						if (cell) { copy('[ht-icon name="' + cell.dataset.name + '"]'); }
					});
					grid.addEventListener('keydown', function(e){
						if (e.key !== 'Enter' && e.key !== ' ') { return; }
						var cell = e.target.closest('.ht-icon-cell');
						if (cell) { e.preventDefault(); copy('[ht-icon name="' + cell.dataset.name + '"]'); }
					});
					search.addEventListener('input', filter);

					fetch(wrap.dataset.src)
						.then(function(r){ return r.json(); })
						.then(function(data){ all = Array.isArray(data) ? data : []; render(all); })
						.catch(function(){ grid.innerHTML = '<p class="ht-note ht-note-red"><?php echo esc_js( __( 'Could not load the icon list.', 'horse-tools' ) ); ?></p>'; });
				})();
				</script>
			</div>
			</div>
			<!-- SNIPPETS -->
			<div class="sotab-box htbox" id="tab6" style="display:none">
			<h2><?php _e('SNIPPETS', 'horse-tools'); ?></h2>
			<?php require HORSETOOLS_DIR . 'main/section/snippets-screen.php'; ?>
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
	<?php
	// style horsetools
	require_once( HORSETOOLS_DIR . 'main/style.php');
	echo ob_get_clean();
}
function horsetools_shortcode_options_link() {
	add_submenu_page ('horsetools-options', 'Shortcode', '<i class="ti ti-brackets" style="width:20px;"></i> '. __('Shortcode', 'horse-tools'), 'manage_options', 'horsetools-shortcode-options', 'horsetools_shortcode_options_page');
}
// Menu removed in 1.2.79: now a tab on a grouped screen.
// add_action('admin_menu', 'horsetools_shortcode_options_link');
function horsetools_shortcode_register_settings() {
	register_setting( 'horsetools_shortcode_settings_group', 'horsetools_shortcode_settings', array( 'sanitize_callback' => 'horsetools_sanitize_shortcode' ) );
}
add_action('admin_init', 'horsetools_shortcode_register_settings');
// clear cache
function horsetools_shortcode_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_shortcode_settings', 'options');
}
add_action('update_option_horsetools_shortcode_settings', 'horsetools_shortcode_settings_cache', 10, 2);


/* -------------------------------------------------------------------------
 * Snippets — admin AJAX: create / edit / delete, import from Shortcoder,
 * find usage across posts, and convert [sc] tags to [ht-snippet].
 *
 * All handlers require manage_options and share the horsetools_snip nonce.
 * Snippet content is stored raw (admin-only, unfiltered_html trust model);
 * the slug is always sanitised to a shortcode-safe key.
 * ---------------------------------------------------------------------- */

/** Shared guard for every snippet AJAX endpoint. */
function horsetools_snip_guard() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'msg' => __( 'Permission denied.', 'horse-tools' ) ) );
	}
	check_ajax_referer( 'horsetools_snip', 'nonce' );
}

/**
 * One page of the list, for the screen.
 *
 * Bodies are not in here. The screen used to be handed every snippet with all
 * of its content inline in the page source, which is invisible at three
 * snippets and a multi-megabyte admin page at a thousand. It asks for a page at
 * a time now, and for a body only when the user opens one to edit.
 *
 * @param array $args search, tag, page.
 * @return array
 */
function horsetools_snip_list_payload( $args = array() ) {
	$args = wp_parse_args( $args, array( 'search' => '', 'tag' => '', 'page' => 1 ) );
	$found = horsetools_snip_list(
		array(
			'search'   => $args['search'],
			'tag'      => $args['tag'],
			'page'     => $args['page'],
			'per_page' => HORSETOOLS_SNIP_PER_PAGE,
		)
	);
	return array(
		'items' => $found['items'],
		'total' => $found['total'],
		'pages' => $found['pages'],
		'page'  => max( 1, (int) $args['page'] ),
		'tags'  => horsetools_snip_tags(),
	);
}

/** The screen's page size. One number, so the PHP and the JS cannot disagree. */
const HORSETOOLS_SNIP_PER_PAGE = 25;

/** Read the list arguments the screen posted. */
function horsetools_snip_req_args() {
	// phpcs:disable WordPress.Security.NonceVerification -- nonce checked in horsetools_snip_guard()
	return array(
		'search' => isset( $_POST['s'] ) ? sanitize_text_field( wp_unslash( $_POST['s'] ) ) : '',
		'tag'    => isset( $_POST['tag'] ) ? sanitize_title( wp_unslash( $_POST['tag'] ) ) : '',
		'page'   => isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1,
	);
	// phpcs:enable WordPress.Security.NonceVerification
}

/** Page through the list. */
add_action( 'wp_ajax_horsetools_snip_page', 'horsetools_snip_page_ajax' );
function horsetools_snip_page_ajax() {
	horsetools_snip_guard();
	wp_send_json_success( horsetools_snip_list_payload( horsetools_snip_req_args() ) );
}

/** One snippet in full, including its body — asked for only when editing. */
add_action( 'wp_ajax_horsetools_snip_get', 'horsetools_snip_get_ajax' );
function horsetools_snip_get_ajax() {
	horsetools_snip_guard();
	$slug = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
	$snip = horsetools_snip_read( $slug );
	if ( ! $snip ) {
		wp_send_json_error( array( 'msg' => __( 'That snippet no longer exists.', 'horse-tools' ) ) );
	}
	$snip['slug']    = $slug;
	$snip['php_bad'] = ! empty( $snip['php'] ) && function_exists( 'horsetools_php_signature_ok' )
		&& ! horsetools_php_signature_ok( $snip );
	wp_send_json_success( $snip );
}

/** Sanitise a comma/space separated tag string into a slug list. */
function horsetools_snip_parse_tags( $raw ) {
	$raw  = (string) $raw;
	$bits = preg_split( '/[,\n]+/', $raw );
	$tags = array();
	foreach ( (array) $bits as $b ) {
		$t = sanitize_text_field( trim( $b ) );
		if ( '' !== $t ) {
			$tags[ strtolower( $t ) ] = $t; // de-dupe case-insensitively
		}
	}
	return array_values( $tags );
}

add_action( 'wp_ajax_horsetools_snip_save', 'horsetools_snip_save_ajax' );
function horsetools_snip_save_ajax() {
	horsetools_snip_guard();
	$slug = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
	if ( '' === $slug ) {
		wp_send_json_error( array( 'msg' => __( 'Give the snippet a name (letters, numbers and dashes).', 'horse-tools' ) ) );
	}
	// Do not let a snippet shadow a built-in Horse Tools shortcode.
	$reserved = array( 'vip', 'sign', 'titday', 'titmonth', 'tityear', 'gget', 'ht-icon', 'ht-snippet' );
	if ( in_array( $slug, $reserved, true ) ) {
		wp_send_json_error( array( 'msg' => __( 'That name is reserved by another Horse Tools shortcode. Pick a different one.', 'horse-tools' ) ) );
	}
	$title   = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : $slug;
	$content = isset( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : ''; // raw by design
	$on      = isset( $_POST['on'] ) && '1' === (string) $_POST['on'];

	$in_list = function ( $key, $allowed ) {
		$v = isset( $_POST[ $key ] ) ? sanitize_key( wp_unslash( $_POST[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- nonce checked in horsetools_snip_guard()
		return in_array( $v, $allowed, true ) ? $v : '';
	};
	$valid_date = function ( $key ) {
		$v = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- nonce checked in horsetools_snip_guard()
		return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $v ) ? $v : '';
	};

	$prev = (array) horsetools_snip_read( $slug );
	$snip = array(
		'title'     => $title,
		'desc'      => isset( $_POST['desc'] ) ? sanitize_text_field( wp_unslash( $_POST['desc'] ) ) : '',
		'content'   => $content,
		'on'        => $on ? 1 : 0,
		'no_admin'  => ( isset( $_POST['no_admin'] ) && '1' === (string) $_POST['no_admin'] ) ? 1 : 0,
		'device'    => $in_list( 'device', array( 'desktop', 'mobile' ) ),
		'login'     => $in_list( 'login', array( 'in', 'out' ) ),
		'role'      => $in_list( 'role', array( 'subscriber', 'contributor', 'author', 'editor', 'administrator' ) ),
		'date_from' => $valid_date( 'date_from' ),
		'date_to'   => $valid_date( 'date_to' ),
		'tags'      => horsetools_snip_parse_tags( isset( $_POST['tags'] ) ? wp_unslash( $_POST['tags'] ) : '' ),
	);

	// PHP snippets: validate, sign and record. Nothing is stored if the code
	// does not parse or the author has not cleared every gate.
	if ( function_exists( 'horsetools_php_prepare_save' ) ) {
		$err = horsetools_php_prepare_save( $snip, $prev, $slug );
		if ( '' !== $err ) {
			wp_send_json_error( array( 'msg' => $err ) );
		}
	}

	horsetools_snip_write( $slug, $snip );

	wp_send_json_success( array( 'list' => horsetools_snip_list_payload( horsetools_snip_req_args() ), 'saved' => $slug ) );
}

add_action( 'wp_ajax_horsetools_snip_delete', 'horsetools_snip_delete_ajax' );
function horsetools_snip_delete_ajax() {
	horsetools_snip_guard();
	$slug  = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
	horsetools_snip_remove( $slug );
	wp_send_json_success( array( 'list' => horsetools_snip_list_payload( horsetools_snip_req_args() ) ) );
}

/**
 * Import snippets from Shortcoder.
 *
 * Reads the shortcoder custom post type directly from the database, so it works
 * whether or not that plugin is still active. Each Shortcoder snippet keys on
 * its post slug — the exact name used in [sc name="slug"] — so imported
 * snippets answer the same shortcode. Existing snippets are never overwritten.
 */
add_action( 'wp_ajax_horsetools_snip_import_sc', 'horsetools_snip_import_sc_ajax' );
function horsetools_snip_import_sc_ajax() {
	horsetools_snip_guard();
	// Any snippet plugin that stores its snippets as a custom post type can be
	// imported by naming that post type. Defaults to Shortcoder. This never
	// touches the other plugin — it only reads its rows out of the database, so
	// it works whether or not that plugin is still active.
	$post_type = isset( $_POST['post_type'] ) ? sanitize_key( wp_unslash( $_POST['post_type'] ) ) : 'shortcoder'; // phpcs:ignore WordPress.Security.NonceVerification -- nonce checked in horsetools_snip_guard()
	if ( '' === $post_type ) {
		$post_type = 'shortcoder';
	}
	global $wpdb;
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT post_title, post_name, post_content FROM {$wpdb->posts} WHERE post_type = %s AND post_status != %s",
			$post_type,
			'trash'
		)
	);
	if ( empty( $rows ) ) {
		wp_send_json_error( array(
			/* translators: %s: post type name. */
			'msg' => sprintf( __( 'No snippets were found for the post type "%s".', 'horse-tools' ), $post_type ),
		) );
	}

	$imported = 0;
	$skipped  = 0;
	foreach ( $rows as $row ) {
		$slug = sanitize_key( $row->post_name );
		if ( '' === $slug ) {
			continue;
		}
		if ( horsetools_snip_post( $slug ) ) {
			$skipped++;
			continue; // never clobber an existing snippet
		}
		horsetools_snip_write(
			$slug,
			array(
				'title'   => $row->post_title ? sanitize_text_field( $row->post_title ) : $slug,
				'content' => (string) $row->post_content, // raw
				'on'      => 1,
			)
		);
		$imported++;
	}
	wp_send_json_success( array(
		'list'     => horsetools_snip_list_payload( horsetools_snip_req_args() ),
		'imported' => $imported,
		'skipped'  => $skipped,
	) );
}

/**
 * Find posts/pages whose content contains a given shortcode tag.
 */
add_action( 'wp_ajax_horsetools_snip_usage', 'horsetools_snip_usage_ajax' );
function horsetools_snip_usage_ajax() {
	horsetools_snip_guard();
	$tag = isset( $_POST['tag'] ) ? preg_replace( '/[^a-z0-9_\-]/i', '', wp_unslash( $_POST['tag'] ) ) : '';
	if ( '' === $tag ) {
		wp_send_json_error( array( 'msg' => __( 'Enter a shortcode name to search for.', 'horse-tools' ) ) );
	}
	global $wpdb;
	$like = '%[' . $wpdb->esc_like( $tag ) . '%'; // matches [tag  and [tag]
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_title, post_type FROM {$wpdb->posts}
			 WHERE post_status IN ('publish','draft','pending','private','future')
			 AND post_type NOT IN ('revision','nav_menu_item')
			 AND post_content LIKE %s
			 ORDER BY post_modified DESC LIMIT 200",
			$like
		)
	);
	$out = array();
	foreach ( (array) $rows as $r ) {
		$out[] = array(
			'id'    => (int) $r->ID,
			'title' => $r->post_title ? $r->post_title : __( '(no title)', 'horse-tools' ),
			'type'  => $r->post_type,
			'edit'  => get_edit_post_link( $r->ID, '' ),
			'view'  => get_permalink( $r->ID ),
		);
	}
	wp_send_json_success( array( 'tag' => $tag, 'count' => count( $out ), 'rows' => $out ) );
}

/**
 * Convert [sc ...] tags to [ht-snippet ...] across post content.
 *
 * Preview mode only counts affected posts; apply mode rewrites them and returns
 * how many changed. Only the tag token is swapped, so attributes are preserved.
 */
add_action( 'wp_ajax_horsetools_snip_replace', 'horsetools_snip_replace_ajax' );
function horsetools_snip_replace_ajax() {
	horsetools_snip_guard();
	$apply = isset( $_POST['apply'] ) && '1' === (string) $_POST['apply'];
	global $wpdb;
	$rows = $wpdb->get_results(
		"SELECT ID, post_content FROM {$wpdb->posts}
		 WHERE post_status IN ('publish','draft','pending','private','future')
		 AND post_type NOT IN ('revision','nav_menu_item')
		 AND ( post_content LIKE '%[sc %' OR post_content LIKE '%[sc]%' )"
	);
	$affected = 0;
	$changed  = 0;
	foreach ( (array) $rows as $r ) {
		$new = preg_replace(
			array( '/\[sc(\s)/', '/\[sc\]/', '/\[\/sc\]/' ),
			array( '[ht-snippet$1', '[ht-snippet]', '[/ht-snippet]' ),
			$r->post_content
		);
		if ( $new === $r->post_content ) {
			continue;
		}
		$affected++;
		if ( $apply ) {
			$wpdb->update( $wpdb->posts, array( 'post_content' => $new ), array( 'ID' => $r->ID ) );
			clean_post_cache( $r->ID );
			$changed++;
		}
	}
	wp_send_json_success( array( 'apply' => $apply, 'affected' => $affected, 'changed' => $changed ) );
}

/* ---- Snippets: export / import as JSON --------------------------------- */
add_action( 'wp_ajax_horsetools_snip_export', 'horsetools_snip_export_ajax' );
function horsetools_snip_export_ajax() {
	horsetools_snip_guard();
	// An export genuinely does want every snippet and every body, but it does
	// not have to hold them all at once: read a page, append it, move on.
	$all  = array();
	$page = 1;
	do {
		$batch = horsetools_snip_list( array( 'page' => $page, 'per_page' => 50 ) );
		foreach ( $batch['items'] as $row ) {
			$snip = horsetools_snip_read( $row['slug'] );
			if ( $snip ) {
				$snip['slug'] = $row['slug'];
				$all[]        = $snip;
			}
		}
		$page++;
	} while ( $page <= $batch['pages'] );

	wp_send_json_success( array( 'json' => wp_json_encode( $all, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) );
}

add_action( 'wp_ajax_horsetools_snip_import_json', 'horsetools_snip_import_json_ajax' );
function horsetools_snip_import_json_ajax() {
	horsetools_snip_guard();
	$raw  = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '';
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		wp_send_json_error( array( 'msg' => __( 'That is not a valid snippet export file.', 'horse-tools' ) ) );
	}
	$reserved = array( 'vip', 'sign', 'titday', 'titmonth', 'tityear', 'gget', 'ht-icon', 'ht-snippet' );
	$added    = 0;
	$skipped  = 0;
	foreach ( $data as $entry ) {
		if ( ! is_array( $entry ) ) {
			continue;
		}
		$slug = sanitize_key( isset( $entry['slug'] ) ? $entry['slug'] : '' );
		if ( '' === $slug || in_array( $slug, $reserved, true ) ) {
			continue;
		}
		if ( horsetools_snip_post( $slug ) ) {
			$skipped++;
			continue; // never clobber an existing snippet
		}
		$tags = isset( $entry['tags'] ) ? $entry['tags'] : '';
		$snip = array(
			'title'     => sanitize_text_field( isset( $entry['title'] ) ? $entry['title'] : $slug ),
			'desc'      => sanitize_text_field( isset( $entry['desc'] ) ? $entry['desc'] : '' ),
			'content'   => (string) ( isset( $entry['content'] ) ? $entry['content'] : '' ),
			'on'        => ! empty( $entry['on'] ) ? 1 : 0,
			'no_admin'  => ! empty( $entry['no_admin'] ) ? 1 : 0,
			'device'    => in_array( isset( $entry['device'] ) ? $entry['device'] : '', array( 'desktop', 'mobile' ), true ) ? $entry['device'] : '',
			'login'     => in_array( isset( $entry['login'] ) ? $entry['login'] : '', array( 'in', 'out' ), true ) ? $entry['login'] : '',
			'role'      => in_array( isset( $entry['role'] ) ? $entry['role'] : '', array( 'subscriber', 'contributor', 'author', 'editor', 'administrator' ), true ) ? $entry['role'] : '',
			'date_from' => isset( $entry['date_from'] ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $entry['date_from'] ) ? $entry['date_from'] : '',
			'date_to'   => isset( $entry['date_to'] ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $entry['date_to'] ) ? $entry['date_to'] : '',
			'tags'      => horsetools_snip_parse_tags( is_array( $tags ) ? implode( ',', $tags ) : (string) $tags ),
		);
		horsetools_snip_write( $slug, $snip );
		$added++;
	}
	wp_send_json_success( array( 'list' => horsetools_snip_list_payload( horsetools_snip_req_args() ), 'added' => $added, 'skipped' => $skipped ) );
}

/* -------------------------------------------------------------------------
 * Insert-shortcode button for BOTH editors.
 *
 * Gutenberg gets a small "Horse Tools" block (htsc-block.js) with a picker;
 * the Classic editor gets a Quicktags button (htsc-qt.js). Both are fed the
 * same fixed list of shortcode templates. Snippets are not in it — they are
 * chosen by name through the searchable picker, which does not grow the page.
 * ---------------------------------------------------------------------- */

/** Shortcode templates offered by the editor picker. */
function horsetools_sc_editor_items() {
	$items = array(
		array( 'label' => '[ht-icon]',      'insert' => '[ht-icon name="heart"]' ),
		array( 'label' => '[ht-button]',    'insert' => '[ht-button url="#" icon="cart"]Buy now[/ht-button]' ),
		array( 'label' => '[ht-alert]',     'insert' => '[ht-alert type="info"]Your message[/ht-alert]' ),
		array( 'label' => '[ht-accordion]', 'insert' => "[ht-accordion faq=\"1\"][ht-item title=\"Question?\"]Answer[/ht-item][/ht-accordion]" ),
		array( 'label' => '[ht-tabs]',      'insert' => "[ht-tabs][ht-tab title=\"One\"]First[/ht-tab][ht-tab title=\"Two\"]Second[/ht-tab][/ht-tabs]" ),
		array( 'label' => '[ht-toggle]',    'insert' => '[ht-toggle title="Show more"]Hidden content[/ht-toggle]' ),
		array( 'label' => '[ht-copy]',      'insert' => '[ht-copy]SAVE20[/ht-copy]' ),
		array( 'label' => '[ht-progress]',  'insert' => '[ht-progress value="80" label="PHP"]' ),
		array( 'label' => '[ht-countdown]', 'insert' => '[ht-countdown date="2026-12-31 23:59"]' ),
		array( 'label' => '[ht-if]',        'insert' => '[ht-if device="mobile"]Mobile only[ht-else]Desktop[/ht-if]' ),
		array( 'label' => '[ht-loop]',      'insert' => '[ht-loop type="post" count="5" template="cards"]' ),
		array( 'label' => '[ht-video]',     'insert' => '[ht-video id="VIDEO_ID" type="youtube"]' ),
		array( 'label' => '[ht-qr]',        'insert' => '[ht-qr size="160"]' ),
		array( 'label' => '[ht-readingtime]','insert' => '[ht-readingtime]' ),
		array( 'label' => '[ht-count]',     'insert' => '[ht-count type="posts"]' ),
		array( 'label' => '[ht-email]',     'insert' => '[ht-email]you@site.com[/ht-email]' ),
		array( 'label' => '[ht-snippet]',   'insert' => '[ht-snippet name=""]' ),
	);
	// The site's own snippets are deliberately not appended here. This is a
	// fixed menu of shortcode templates, and a menu is the wrong shape for a
	// list that grows: at a few dozen snippets it is already unusable, and it
	// would put every snippet name into the source of every editor page.
	// Choosing a snippet by name is what the searchable picker is for — the
	// "Shortcode" button in the Classic editor, the "Horse Tools snippet" block
	// in the block editor — so this menu offers the empty tag and gets out of
	// the way.
	return $items;
}

function horsetools_sc_editor_payload() {
	return array(
		'items' => horsetools_sc_editor_items(),
		'i18n'  => array(
			'title'     => __( 'Horse Tools', 'horse-tools' ),
			'choose'    => __( '— Insert a shortcode —', 'horse-tools' ),
			'shortcode' => __( 'Shortcode', 'horse-tools' ),
			'pick'      => __( 'Type the number of the shortcode to insert:', 'horse-tools' ),
			'button'    => __( 'Horse Tools shortcode', 'horse-tools' ),
		),
	);
}

add_action( 'enqueue_block_editor_assets', 'horsetools_sc_block_enqueue' );
function horsetools_sc_block_enqueue() {
	wp_enqueue_script(
		'horsetools-sc-block',
		HORSETOOLS_URL . 'link/shortcode/htsc-block.js',
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor' ),
		HORSETOOLS_VERSION,
		true
	);
	wp_localize_script( 'horsetools-sc-block', 'horsetoolsSC', horsetools_sc_editor_payload() );
}

add_action( 'admin_enqueue_scripts', 'horsetools_sc_qt_enqueue' );
function horsetools_sc_qt_enqueue( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	// The block editor has its own button; only add Quicktags for the Classic editor.
	if ( $screen && method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
		return;
	}
	wp_enqueue_script( 'horsetools-sc-qt', HORSETOOLS_URL . 'link/shortcode/htsc-qt.js', array( 'quicktags' ), HORSETOOLS_VERSION, true );
	wp_localize_script( 'horsetools-sc-qt', 'horsetoolsSC', horsetools_sc_editor_payload() );
}

/* -------------------------------------------------------------------------
 * Shortcode on/off manager (Option A: Horse Tools' own tags + snippets only).
 *
 * The manageable list is a fixed allow-list of tags this plugin registers, so
 * the manager can never disable a shortcode owned by another plugin.
 * ---------------------------------------------------------------------- */

/** Grouped allow-list of Horse Tools shortcodes the manager may toggle. */
function horsetools_sc_manageable() {
	return array(
		__( 'Content', 'horse-tools' )              => array( 'ht-snippet', 'sc', 'ht-if', 'ht-raw', 'vip', 'sign' ),
		__( 'Layout & interactive', 'horse-tools' ) => array( 'ht-alert', 'ht-accordion', 'ht-tabs', 'ht-toggle', 'ht-reveal', 'ht-copy', 'ht-progress', 'ht-countdown', 'ht-button', 'ht-email' ),
		__( 'Data & media', 'horse-tools' )         => array( 'ht-loop', 'ht-field', 'ht-post', 'ht-readingtime', 'ht-count', 'ht-qr', 'ht-video', 'ht-icon', 'gget' ),
		__( 'Date', 'horse-tools' )                 => array( 'titday', 'titmonth', 'tityear' ),
	);
}

/** Flat list of every manageable tag. */
function horsetools_sc_manageable_flat() {
	$flat = array();
	foreach ( horsetools_sc_manageable() as $tags ) {
		$flat = array_merge( $flat, $tags );
	}
	return $flat;
}

add_action( 'wp_ajax_horsetools_sc_disable_save', 'horsetools_sc_disable_save_ajax' );
function horsetools_sc_disable_save_ajax() {
	horsetools_snip_guard();
	$raw     = isset( $_POST['disabled'] ) ? (array) wp_unslash( $_POST['disabled'] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification -- nonce checked in horsetools_snip_guard()
	$allowed = horsetools_sc_manageable_flat();
	$clean   = array();
	foreach ( $raw as $tag ) {
		$tag = sanitize_key( $tag );
		// Only ever store a tag that belongs to Horse Tools.
		if ( in_array( $tag, $allowed, true ) ) {
			$clean[] = $tag;
		}
	}
	update_option( 'horsetools_sc_disabled', array_values( array_unique( $clean ) ), false );
	wp_send_json_success( array( 'disabled' => $clean ) );
}
