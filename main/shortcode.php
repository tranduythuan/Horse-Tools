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
add_action('admin_menu', 'horsetools_shortcode_options_link');
function horsetools_shortcode_register_settings() {
	register_setting( 'horsetools_shortcode_settings_group', 'horsetools_shortcode_settings', array( 'sanitize_callback' => 'horsetools_sanitize_shortcode' ) );
}
add_action('admin_init', 'horsetools_shortcode_register_settings');
// clear cache
function horsetools_shortcode_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_shortcode_settings', 'options');
}
add_action('update_option_horsetools_shortcode_settings', 'horsetools_shortcode_settings_cache', 10, 2);

