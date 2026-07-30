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
			<div class="ht-card ht-snip" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_snip' ) ); ?>" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
			  <h3><i class="ti ti-file-code"></i> <?php _e('Custom shortcodes (snippets)', 'horse-tools') ?></h3>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Create a named block of HTML/CSS/JS and drop it anywhere with [ht-snippet name="your-name"]. Placeholders {{param}} or %%param%% are filled from the shortcode’s attributes, plus built-ins: currentyear, currentdate, postid, posttitle, sitename, siteurl.', 'horse-tools'); ?></p>

				<p><input type="text" id="ht-snip-name" class="ht-input-big" placeholder="<?php esc_attr_e( 'Name — letters, numbers, dashes (e.g. call-to-action)', 'horse-tools' ); ?>" /></p>
				<p><input type="text" id="ht-snip-title" class="ht-input-big" placeholder="<?php esc_attr_e( 'Display name (shown in the list)', 'horse-tools' ); ?>" /></p>
				<p><input type="text" id="ht-snip-desc" class="ht-input-big" placeholder="<?php esc_attr_e( 'Description (for your reference)', 'horse-tools' ); ?>" /></p>
				<p><textarea id="ht-snip-content" class="ht-code-textarea" style="height:170px" placeholder="<?php esc_attr_e( 'HTML, CSS or JS…  e.g. <a href=&quot;{{url}}&quot;>{{label}}</a>', 'horse-tools' ); ?>"></textarea></p>

				<details class="ht-snip-adv">
					<summary><i class="ti ti-adjustments"></i> <?php _e( 'Advanced options — device, visitors, schedule, tags (optional)', 'horse-tools' ); ?></summary>
				<div class="ht-snip-opts">
					<label><span><?php _e( 'Enabled', 'horse-tools' ); ?></span>
						<select id="ht-snip-on"><option value="1"><?php _e( 'Yes', 'horse-tools' ); ?></option><option value="0"><?php _e( 'No (temporarily off)', 'horse-tools' ); ?></option></select></label>
					<label><span><?php _e( 'Hide from administrators', 'horse-tools' ); ?></span>
						<select id="ht-snip-noadmin"><option value="0"><?php _e( 'No', 'horse-tools' ); ?></option><option value="1"><?php _e( 'Yes — preview as a visitor', 'horse-tools' ); ?></option></select></label>
					<label><span><?php _e( 'Show on devices', 'horse-tools' ); ?></span>
						<select id="ht-snip-device"><option value=""><?php _e( 'All devices', 'horse-tools' ); ?></option><option value="desktop"><?php _e( 'Desktop only', 'horse-tools' ); ?></option><option value="mobile"><?php _e( 'Mobile only', 'horse-tools' ); ?></option></select></label>
					<label><span><?php _e( 'Show to', 'horse-tools' ); ?></span>
						<select id="ht-snip-login"><option value=""><?php _e( 'Everyone', 'horse-tools' ); ?></option><option value="in"><?php _e( 'Logged-in users', 'horse-tools' ); ?></option><option value="out"><?php _e( 'Logged-out visitors', 'horse-tools' ); ?></option></select></label>
					<label><span><?php _e( 'Minimum role', 'horse-tools' ); ?></span>
						<select id="ht-snip-role"><option value=""><?php _e( 'Any', 'horse-tools' ); ?></option><option value="subscriber">Subscriber+</option><option value="contributor">Contributor+</option><option value="author">Author+</option><option value="editor">Editor+</option><option value="administrator">Administrator</option></select></label>
					<label><span><?php _e( 'Tags (comma separated)', 'horse-tools' ); ?></span>
						<input type="text" id="ht-snip-tags" placeholder="<?php esc_attr_e( 'e.g. contact, promo', 'horse-tools' ); ?>" /></label>
					<label><span><?php _e( 'Show from', 'horse-tools' ); ?></span>
						<input type="date" id="ht-snip-from" /></label>
					<label><span><?php _e( 'Show until', 'horse-tools' ); ?></span>
						<input type="date" id="ht-snip-to" /></label>
				</div>
				</details>

				<p class="ht-snip-actions">
					<button type="button" class="ht-priv-btn" id="ht-snip-save"><i class="ti ti-device-floppy"></i> <?php _e( 'Save snippet', 'horse-tools' ); ?></button>
					<button type="button" class="ht-priv-btn" id="ht-snip-clear"><i class="ti ti-eraser"></i> <?php _e( 'New / clear', 'horse-tools' ); ?></button>
					<button type="button" class="ht-priv-btn" id="ht-snip-import"><i class="ti ti-download"></i> <?php _e( 'Import from Shortcoder', 'horse-tools' ); ?></button>
				</p>
				<div id="ht-snip-msg"></div>

				<div class="ht-snip-filter">
					<label class="ht-snip-search-l"><i class="ti ti-search"></i>
						<input type="search" id="ht-snip-search" placeholder="<?php esc_attr_e( 'Search snippets by name, description or tag…', 'horse-tools' ); ?>" autocomplete="off" /></label>
					<label><i class="ti ti-tags"></i> <?php _e( 'Tag', 'horse-tools' ); ?>
						<select id="ht-snip-tagfilter"><option value=""><?php _e( 'All snippets', 'horse-tools' ); ?></option></select></label>
					<span id="ht-snip-countlbl" class="ht-snip-countlbl"></span>
				</div>
				<div id="ht-snip-list"></div>
			</div>

			<div class="ht-card">
			  <h3><i class="ti ti-arrows-shuffle"></i> <?php _e('Where shortcodes run', 'horse-tools') ?></h3>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('WordPress runs shortcodes in post content by default. Turn these on to run them elsewhere too. (Saved with the SAVE button at the bottom.)', 'horse-tools'); ?></p>
				<?php horsetools_toggle( 'shortcode-inwidget', __( 'Run shortcodes in widgets', 'horse-tools' ), array( 'module' => 'shortcode', 'tab' => 'SNIPPETS', 'section' => 'Where shortcodes run' ) ); ?>
				<?php horsetools_toggle( 'shortcode-inexcerpt', __( 'Run shortcodes in excerpts', 'horse-tools' ), array( 'module' => 'shortcode', 'tab' => 'SNIPPETS', 'section' => 'Where shortcodes run' ) ); ?>
				<?php horsetools_toggle( 'shortcode-inmenu', __( 'Run shortcodes in menu items', 'horse-tools' ), array( 'module' => 'shortcode', 'tab' => 'SNIPPETS', 'section' => 'Where shortcodes run' ) ); ?>
				<?php horsetools_toggle( 'shortcode-interm', __( 'Run shortcodes in category / tag descriptions', 'horse-tools' ), array( 'module' => 'shortcode', 'tab' => 'SNIPPETS', 'section' => 'Where shortcodes run' ) ); ?>
			</div>

			<div class="ht-card ht-snip2" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_snip' ) ); ?>" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
			  <h3><i class="ti ti-search"></i> <?php _e('Find where a shortcode is used', 'horse-tools') ?></h3>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Type any shortcode name (without brackets) to list the posts and pages that contain it — handy before you change or remove one.', 'horse-tools'); ?></p>
				<p class="ht-snip-actions">
					<input type="text" id="ht-snip-usage-tag" class="ht-input-big" style="max-width:280px" placeholder="<?php esc_attr_e( 'e.g. sc, ht-snippet, vip', 'horse-tools' ); ?>" />
					<button type="button" class="ht-priv-btn" id="ht-snip-usage-btn"><i class="ti ti-search"></i> <?php _e( 'Find usage', 'horse-tools' ); ?></button>
				</p>
				<div id="ht-snip-usage-out"></div>

			  <h3 style="margin-top:22px"><i class="ti ti-replace"></i> <?php _e('Convert Shortcoder tags', 'horse-tools') ?></h3>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Rewrite every [sc …] tag in your content to [ht-snippet …], keeping all attributes. Preview first; applying edits post content, so take a backup. Tip: you don’t have to convert — imported snippets already answer [sc …] once Shortcoder is deactivated.', 'horse-tools'); ?></p>
				<p class="ht-snip-actions">
					<button type="button" class="ht-priv-btn" id="ht-snip-rep-preview"><i class="ti ti-eye"></i> <?php _e( 'Preview', 'horse-tools' ); ?></button>
					<button type="button" class="ht-priv-btn" id="ht-snip-rep-apply" disabled><i class="ti ti-replace"></i> <?php _e( 'Convert now', 'horse-tools' ); ?></button>
				</p>
				<div id="ht-snip-rep-out"></div>
			</div>
			<style>
			.ht-snip .ht-priv-btn,.ht-snip2 .ht-priv-btn{display:inline-flex;align-items:center;gap:6px;background:#fff;border:1px solid #e0a800;color:#8a5a00;padding:8px 14px;border-radius:8px;cursor:pointer;font-size:13px;transition:background .12s}
			.ht-snip .ht-priv-btn:hover,.ht-snip2 .ht-priv-btn:hover{background:#fff9e6}
			.ht-snip .ht-priv-btn[disabled],.ht-snip2 .ht-priv-btn[disabled]{opacity:.5;cursor:default}
			.ht-snip-actions{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin:6px 0}
			.ht-snip-opts{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:10px 16px;margin:8px 0 4px}
			.ht-snip-opts label{display:flex;flex-direction:column;gap:3px;font-size:12px;color:#666}
			.ht-snip-opts select,.ht-snip-opts input{padding:6px 8px;border:1px solid #ddd;border-radius:7px;font-size:13px}
			.ht-snip-adv{margin:8px 0 4px;border:1px solid #eee;border-radius:9px;background:#fafafa}
			.ht-snip-adv>summary{cursor:pointer;padding:9px 12px;font-size:13px;font-weight:600;color:#8a5a00;list-style:none;display:flex;align-items:center;gap:7px;user-select:none}
			.ht-snip-adv>summary::-webkit-details-marker{display:none}
			.ht-snip-adv>summary::after{content:"▾";margin-left:auto;color:#b8942f;transition:transform .15s}
			.ht-snip-adv[open]>summary::after{transform:rotate(180deg)}
			.ht-snip-adv[open]>summary{border-bottom:1px solid #eee}
			.ht-snip-adv .ht-snip-opts{padding:12px}
			.ht-snip-filter{display:flex;flex-wrap:wrap;align-items:center;gap:14px;margin:14px 0 6px;font-size:13px}
			.ht-snip-filter label{display:inline-flex;align-items:center;gap:6px;color:#666}
			.ht-snip-filter select{padding:5px 8px;border:1px solid #ddd;border-radius:7px}
			.ht-snip-search-l{flex:1;min-width:200px}
			.ht-snip-filter #ht-snip-search{flex:1;padding:7px 10px;border:1px solid #ddd;border-radius:7px;font-size:13px;min-width:160px}
			.ht-snip-countlbl{color:#999;font-size:12px;margin-left:auto}
			.ht-snip .CodeMirror{border:1px solid #ddd;border-radius:8px;height:220px;font-size:13px}
			.ht-snip .CodeMirror-focused{border-color:#e0a800;box-shadow:0 0 0 1px #f0d98a}
			#ht-snip-list{margin-top:6px}
			.ht-snip-row{display:flex;align-items:center;gap:10px;padding:9px 10px;border:1px solid #ececec;border-radius:9px;margin-bottom:7px;background:#fff;flex-wrap:wrap}
			.ht-snip-row.off{opacity:.55}
			.ht-snip-row b{font-size:13px}
			.ht-snip-code{font-family:monospace;font-size:11.5px;background:#fff8e1;border:1px solid #f0d98a;color:#8a5a00;border-radius:5px;padding:2px 7px;cursor:pointer;white-space:nowrap}
			.ht-snip-row .grow{flex:1;min-width:120px;overflow:hidden}
			.ht-snip-row .grow small{color:#999;display:block;font-size:11px}
			.ht-snip-row a.op{cursor:pointer;color:#8a5a00;font-size:12px;text-decoration:underline}
			.ht-snip-row a.del{color:#c0392b}
			.ht-snip-badge{display:inline-block;font-size:10px;background:#eef3f8;color:#3a6ea5;border-radius:20px;padding:1px 8px;margin-right:4px}
			.ht-snip-tag{display:inline-block;font-size:10px;background:#fff3d6;color:#8a5a00;border:1px solid #f0d98a;border-radius:20px;padding:1px 8px;margin-right:4px}
			.ht-snip-msg{padding:8px 12px;border-radius:8px;margin:6px 0;font-size:13px;background:#f4f6f8}
			.ht-snip-msg.err{background:#fdecea;color:#8a1c12}
			.ht-snip-msg.good{background:#eafaf0;color:#1e6b3f}
			.ht-snip-table{border-collapse:collapse;width:100%;max-width:680px;font-size:13px;margin-top:6px}
			.ht-snip-table th,.ht-snip-table td{border:1px solid #ececec;padding:6px 9px;text-align:left}
			.ht-snip-table th{background:#fafafa}
			</style>
			<script>
			(function(){
				var root = document.querySelector('.ht-snip');
				if (!root || root.dataset.ready) { return; }
				root.dataset.ready = '1';
				var AJAX = root.dataset.ajax, NONCE = root.dataset.nonce;
				var $ = function(id){ return document.getElementById(id); };
				var F = { name:$('ht-snip-name'), title:$('ht-snip-title'), desc:$('ht-snip-desc'), content:$('ht-snip-content'),
					on:$('ht-snip-on'), noadmin:$('ht-snip-noadmin'), device:$('ht-snip-device'), login:$('ht-snip-login'),
					role:$('ht-snip-role'), tags:$('ht-snip-tags'), from:$('ht-snip-from'), to:$('ht-snip-to') };
				var msg=$('ht-snip-msg'), list=$('ht-snip-list'), tagFilter=$('ht-snip-tagfilter');
					var search=$('ht-snip-search'), countlbl=$('ht-snip-countlbl');
					var adv=document.querySelector('.ht-snip-adv');
					var cm=null;
					function initCM(){
						if (cm || !window.wp || !window.wp.codeEditor || !window.htSnipCM || !F.content) { return; }
						try { cm = wp.codeEditor.initialize(F.content, window.htSnipCM).codemirror; } catch(e){ cm = null; }
						if (cm){ cm.on('change', function(){ cm.save(); }); }
					}
					if (document.readyState === 'complete') { initCM(); } else { window.addEventListener('load', initCM); }
				var I18N = {
					saved: <?php echo wp_json_encode( __( 'Snippet saved.', 'horse-tools' ) ); ?>,
					deleted: <?php echo wp_json_encode( __( 'Snippet deleted.', 'horse-tools' ) ); ?>,
					none: <?php echo wp_json_encode( __( 'No snippets yet. Create one above, or import from Shortcoder.', 'horse-tools' ) ); ?>,
					copied: <?php echo wp_json_encode( __( 'Copied', 'horse-tools' ) ); ?>,
					confirmDel: <?php echo wp_json_encode( __( 'Delete this snippet? Content that uses it will stop rendering.', 'horse-tools' ) ); ?>,
					imported: <?php echo wp_json_encode( __( 'Imported %1$d snippet(s); %2$d already existed.', 'horse-tools' ) ); ?>,
					usageNone: <?php echo wp_json_encode( __( 'Not found in any post or page.', 'horse-tools' ) ); ?>,
					usageHead: <?php echo wp_json_encode( __( 'Found in %d item(s):', 'horse-tools' ) ); ?>,
					repNone: <?php echo wp_json_encode( __( 'No [sc] tags found in your content.', 'horse-tools' ) ); ?>,
					repPrev: <?php echo wp_json_encode( __( '%d post(s) contain [sc] tags. Click “Convert now” to rewrite them.', 'horse-tools' ) ); ?>,
					repDone: <?php echo wp_json_encode( __( 'Converted %d post(s).', 'horse-tools' ) ); ?>,
					fail: <?php echo wp_json_encode( __( 'Something went wrong.', 'horse-tools' ) ); ?>,
					edit: <?php echo wp_json_encode( __( 'Edit', 'horse-tools' ) ); ?>,
					del: <?php echo wp_json_encode( __( 'Delete', 'horse-tools' ) ); ?>,
					view: <?php echo wp_json_encode( __( 'View', 'horse-tools' ) ); ?>,
					off: <?php echo wp_json_encode( __( 'off', 'horse-tools' ) ); ?>
				};
				var COND = {
					device: { desktop: <?php echo wp_json_encode( __( 'desktop', 'horse-tools' ) ); ?>, mobile: <?php echo wp_json_encode( __( 'mobile', 'horse-tools' ) ); ?> },
					login:  { in: <?php echo wp_json_encode( __( 'logged-in', 'horse-tools' ) ); ?>, out: <?php echo wp_json_encode( __( 'logged-out', 'horse-tools' ) ); ?> }
				};
				var snippets = <?php echo wp_json_encode( horsetools_snip_list_payload() ); ?>;

				function esc(s){ return String(s).replace(/[&<>"]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];}); }
				function say(el,t,cls){ el.innerHTML = t ? '<div class="ht-snip-msg '+(cls||'')+'">'+esc(t)+'</div>' : ''; }
				function post(data, done){
					data.nonce = NONCE;
					var body = Object.keys(data).map(function(k){ return encodeURIComponent(k)+'='+encodeURIComponent(data[k]); }).join('&');
					fetch(AJAX,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body})
						.then(function(r){return r.json();}).then(done).catch(function(){ say(msg,I18N.fail,'err'); });
				}
				function badges(s){
					var b='';
					if(!s.on) b+='<span class="ht-snip-badge" style="background:#fdecea;color:#c0392b">'+esc(I18N.off)+'</span>';
					if(s.device&&COND.device[s.device]) b+='<span class="ht-snip-badge">'+esc(COND.device[s.device])+'</span>';
					if(s.login&&COND.login[s.login]) b+='<span class="ht-snip-badge">'+esc(COND.login[s.login])+'</span>';
					if(s.role) b+='<span class="ht-snip-badge">'+esc(s.role)+'+</span>';
					if(s.no_admin) b+='<span class="ht-snip-badge">no-admin</span>';
					if(s.date_from||s.date_to) b+='<span class="ht-snip-badge">'+esc((s.date_from||'…')+'→'+(s.date_to||'…'))+'</span>';
					(s.tags||[]).forEach(function(t){ b+='<span class="ht-snip-tag">'+esc(t)+'</span>'; });
					return b;
				}
				function refreshTagFilter(){
					var all={}; snippets.forEach(function(s){ (s.tags||[]).forEach(function(t){ all[t.toLowerCase()]=t; }); });
					var cur=tagFilter.value;
					var html='<option value="">'+tagFilter.options[0].text+'</option>';
					Object.keys(all).sort().forEach(function(k){ html+='<option value="'+esc(k)+'">'+esc(all[k])+'</option>'; });
					tagFilter.innerHTML=html; tagFilter.value=cur;
				}
				function render(){
					refreshTagFilter();
					var filt=tagFilter.value.toLowerCase();
					var q=(search&&search.value||'').trim().toLowerCase();
					var rows=snippets.filter(function(s){
						if (filt && !(s.tags||[]).some(function(t){return t.toLowerCase()===filt;})) { return false; }
						if (q){
							var hay=((s.slug||'')+' '+(s.title||'')+' '+(s.desc||'')+' '+((s.tags||[]).join(' '))).toLowerCase();
							if (hay.indexOf(q)===-1) { return false; }
						}
						return true;
					});
					if (countlbl){ countlbl.textContent = snippets.length ? (rows.length+'/'+snippets.length) : ''; }
					if (!rows.length){ list.innerHTML = '<div class="ht-snip-msg">'+esc(snippets.length?<?php echo wp_json_encode( __( 'No snippet matches your search.', 'horse-tools' ) ); ?>:I18N.none)+'</div>'; return; }
					var html='';
					rows.forEach(function(s){
						var code='[ht-snippet name="'+s.slug+'"]';
						html += '<div class="ht-snip-row'+(s.on?'':' off')+'">'
							+ '<span class="grow"><b>'+esc(s.title||s.slug)+'</b>'
							+ (s.desc?'<small>'+esc(s.desc)+'</small>':'')
							+ '<div style="margin-top:4px">'+badges(s)+'</div></span>'
							+ '<span class="ht-snip-code" data-copy="'+esc(code)+'" title="'+esc(code)+'">'+esc(code)+'</span>'
							+ '<a class="op" data-edit="'+esc(s.slug)+'">'+esc(I18N.edit)+'</a>'
							+ '<a class="op del" data-del="'+esc(s.slug)+'">'+esc(I18N.del)+'</a>'
							+ '</div>';
					});
					list.innerHTML = html;
				}
				tagFilter.addEventListener('change', render);
					if (search){ search.addEventListener('input', render); }

				list.addEventListener('click', function(e){
					var c=e.target.closest('[data-copy]'), ed=e.target.closest('[data-edit]'), dl=e.target.closest('[data-del]');
					if (c){ navigator.clipboard && navigator.clipboard.writeText(c.dataset.copy); c.textContent=I18N.copied; setTimeout(function(){ c.textContent=c.dataset.copy; },1000); return; }
					if (ed){ var s=snippets.find(function(x){return x.slug===ed.dataset.edit;}); if(s){
						F.name.value=s.slug; F.title.value=s.title||''; F.desc.value=s.desc||''; F.content.value=s.content||''; if(cm){ cm.setValue(s.content||''); }
						F.on.value=s.on?'1':'0'; F.noadmin.value=s.no_admin?'1':'0'; F.device.value=s.device||''; F.login.value=s.login||'';
						F.role.value=s.role||''; F.tags.value=(s.tags||[]).join(', '); F.from.value=s.date_from||''; F.to.value=s.date_to||'';
						if (adv && (s.device||s.login||s.role||s.no_admin||s.date_from||s.date_to||(s.tags&&s.tags.length)||!s.on)) { adv.open=true; }
						F.name.focus(); window.scrollTo({top:0,behavior:'smooth'}); } return; }
					if (dl){ if(!confirm(I18N.confirmDel))return; post({action:'horsetools_snip_delete',slug:dl.dataset.del}, function(res){ if(res&&res.success){ snippets=res.data.snippets; render(); say(msg,I18N.deleted,'good'); } else { say(msg,(res&&res.data&&res.data.msg)||I18N.fail,'err'); } }); return; }
				});

				$('ht-snip-save').addEventListener('click', function(){
					if (cm){ cm.save(); }
					post({action:'horsetools_snip_save', slug:F.name.value, title:F.title.value, desc:F.desc.value, content:F.content.value,
						on:F.on.value, no_admin:F.noadmin.value, device:F.device.value, login:F.login.value, role:F.role.value,
						tags:F.tags.value, date_from:F.from.value, date_to:F.to.value}, function(res){
						if (res&&res.success){ snippets=res.data.snippets; render(); say(msg,I18N.saved,'good'); }
						else { say(msg,(res&&res.data&&res.data.msg)||I18N.fail,'err'); }
					});
				});
				$('ht-snip-clear').addEventListener('click', function(){
					F.name.value=F.title.value=F.desc.value=F.content.value=F.tags.value=F.from.value=F.to.value='';
					if(cm){ cm.setValue(''); }
					F.on.value='1'; F.noadmin.value='0'; F.device.value=''; F.login.value=''; F.role.value=''; say(msg,''); F.name.focus();
				});
				$('ht-snip-import').addEventListener('click', function(){
					post({action:'horsetools_snip_import_sc'}, function(res){
						if (res&&res.success){ snippets=res.data.snippets; render(); say(msg,I18N.imported.replace('%1$d',res.data.imported).replace('%2$d',res.data.skipped),'good'); }
						else { say(msg,(res&&res.data&&res.data.msg)||I18N.fail,'err'); }
					});
				});

				var uOut=$('ht-snip-usage-out'), rOut=$('ht-snip-rep-out');
				$('ht-snip-usage-btn').addEventListener('click', function(){
					post({action:'horsetools_snip_usage', tag:$('ht-snip-usage-tag').value}, function(res){
						if (!res||!res.success){ say(uOut,(res&&res.data&&res.data.msg)||I18N.fail,'err'); return; }
						var d=res.data;
						if (!d.count){ say(uOut,I18N.usageNone); return; }
						var html='<div class="ht-snip-msg good">'+esc(I18N.usageHead.replace('%d',d.count))+'</div><table class="ht-snip-table"><tbody>';
						d.rows.forEach(function(r){
							html+='<tr><td>'+esc(r.title)+'</td><td>'+esc(r.type)+'</td><td>'
								+(r.edit?'<a href="'+esc(r.edit)+'" target="_blank" rel="noopener">'+esc(I18N.edit)+'</a> ':'')
								+(r.view?'<a href="'+esc(r.view)+'" target="_blank" rel="noopener">'+esc(I18N.view)+'</a>':'')+'</td></tr>';
						});
						uOut.innerHTML=html+'</tbody></table>';
					});
				});
				var repApply=$('ht-snip-rep-apply');
				$('ht-snip-rep-preview').addEventListener('click', function(){
					post({action:'horsetools_snip_replace', apply:'0'}, function(res){
						if (!res||!res.success){ say(rOut,I18N.fail,'err'); return; }
						if (!res.data.affected){ say(rOut,I18N.repNone); repApply.disabled=true; return; }
						say(rOut,I18N.repPrev.replace('%d',res.data.affected)); repApply.disabled=false;
					});
				});
				repApply.addEventListener('click', function(){
					repApply.disabled=true;
					post({action:'horsetools_snip_replace', apply:'1'}, function(res){
						if (!res||!res.success){ say(rOut,I18N.fail,'err'); return; }
						say(rOut,I18N.repDone.replace('%d',res.data.changed),'good');
					});
				});

				render();
			})();
			</script>
			<div class="ht-card ht-snip3" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_snip' ) ); ?>" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
			  <h3><i class="ti ti-transfer"></i> <?php _e('Move snippets between sites', 'horse-tools') ?></h3>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Export downloads all your snippets as a .json file. Import merges a file from another site — existing snippets of the same name are kept, not overwritten.', 'horse-tools'); ?></p>
				<p class="ht-snip-actions">
					<button type="button" class="ht-priv-btn" id="ht-snip-export"><i class="ti ti-file-download"></i> <?php _e( 'Export snippets (.json)', 'horse-tools' ); ?></button>
					<button type="button" class="ht-priv-btn" id="ht-snip-import-btn"><i class="ti ti-file-upload"></i> <?php _e( 'Import snippets', 'horse-tools' ); ?></button>
					<input type="file" id="ht-snip-import-file" accept=".json,application/json" style="display:none" />
				</p>
				<div id="ht-snip-io-out"></div>
			</div>

			<div class="ht-card">
			  <h3><i class="ti ti-list-search"></i> <?php _e('Shortcode reference', 'horse-tools') ?></h3>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Every Horse Tools shortcode. Click any example to copy it.', 'horse-tools'); ?></p>
				<?php
				$horsetools_sc_ref = array(
					__( 'Conditional & content', 'horse-tools' ) => array(
						'[ht-if device="mobile"] … [ht-else] … [/ht-if]',
						'[ht-snippet name="my-block"]',
						'[ht-raw] … [/ht-raw]',
						'[vip] … [/vip]',
					),
					__( 'Layout & interactive', 'horse-tools' ) => array(
						'[ht-alert type="warning"] … [/ht-alert]',
						'[ht-accordion faq="1"][ht-item title="Q?"]A[/ht-item][/ht-accordion]',
						'[ht-tabs][ht-tab title="One"] … [/ht-tab][/ht-tabs]',
						'[ht-toggle title="More"] … [/ht-toggle]',
						'[ht-reveal title="Answer"] … [/ht-reveal]',
						'[ht-copy]SAVE20[/ht-copy]',
						'[ht-progress value="80" label="PHP"]',
						'[ht-countdown date="2026-09-01 00:00"]',
						'[ht-button url="#" icon="cart"]Buy now[/ht-button]',
						'[ht-email subject="Hi"]you@site.com[/ht-email]',
					),
					__( 'Data & media', 'horse-tools' ) => array(
						'[ht-loop type="post" count="5" cat="news" template="cards"]',
						'[ht-field key="title"]',
						'[ht-post id="123" field="content"]',
						'[ht-readingtime] min',
						'[ht-count type="posts"]',
						'[ht-qr size="160"]',
						'[ht-video id="VIDEO_ID" type="youtube"]',
						'[ht-icon name="heart" size="24"]',
					),
				);
				echo '<div class="ht-ref">';
				foreach ( $horsetools_sc_ref as $group => $codes ) {
					echo '<div class="ht-ref-group"><h4>' . esc_html( $group ) . '</h4>';
					foreach ( $codes as $code ) {
						echo '<code class="ht-ref-code" data-copy="' . esc_attr( $code ) . '">' . esc_html( $code ) . '</code>';
					}
					echo '</div>';
				}
				echo '</div>';
				?>
			</div>
			<style>
			.ht-snip3 .ht-priv-btn,.ht-snip3 .ht-priv-btn{display:inline-flex;align-items:center;gap:6px;background:#fff;border:1px solid #e0a800;color:#8a5a00;padding:8px 14px;border-radius:8px;cursor:pointer;font-size:13px}
			.ht-snip3 .ht-priv-btn:hover{background:#fff9e6}
			.ht-ref-group{margin-bottom:14px}
			.ht-ref-group h4{margin:0 0 6px;font-size:13px;color:#8a5a00}
			.ht-ref-code{display:block;font-family:monospace;font-size:12px;background:#fff8e1;border:1px solid #f0d98a;color:#8a5a00;border-radius:6px;padding:5px 9px;margin-bottom:5px;cursor:pointer;max-width:640px;overflow-x:auto;white-space:nowrap}
			.ht-ref-code:hover{background:#fff2cc}
			.ht-ref-code.copied{background:#eafaf0;border-color:#bfe6cd;color:#1e6b3f}
			</style>
			<script>
			(function(){
				var io = document.querySelector('.ht-snip3');
				if (!io || io.dataset.ready) { return; }
				io.dataset.ready = '1';
				var AJAX = io.dataset.ajax, NONCE = io.dataset.nonce;
				var out = document.getElementById('ht-snip-io-out');
				var IO = {
					exported: <?php echo wp_json_encode( __( 'Exported.', 'horse-tools' ) ); ?>,
					imported: <?php echo wp_json_encode( __( 'Imported %1$d snippet(s); %2$d skipped. Reloading…', 'horse-tools' ) ); ?>,
					badfile: <?php echo wp_json_encode( __( 'That file could not be read as JSON.', 'horse-tools' ) ); ?>,
					fail: <?php echo wp_json_encode( __( 'Something went wrong.', 'horse-tools' ) ); ?>
				};
				function say(t,cls){ out.innerHTML = '<div class="ht-snip-msg '+(cls||'')+'">'+String(t).replace(/[&<>]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;'}[c];})+'</div>'; }
				function post(data, done){
					data.nonce = NONCE;
					var body = Object.keys(data).map(function(k){ return encodeURIComponent(k)+'='+encodeURIComponent(data[k]); }).join('&');
					fetch(AJAX,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body}).then(function(r){return r.json();}).then(done).catch(function(){ say(IO.fail,'err'); });
				}
				document.getElementById('ht-snip-export').addEventListener('click', function(){
					post({action:'horsetools_snip_export'}, function(res){
						if(!res||!res.success){ say(IO.fail,'err'); return; }
						var blob = new Blob([res.data.json],{type:'application/json'});
						var a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'horse-tools-snippets.json';
						document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(a.href);
						say(IO.exported,'good');
					});
				});
				var file = document.getElementById('ht-snip-import-file');
				document.getElementById('ht-snip-import-btn').addEventListener('click', function(){ file.click(); });
				file.addEventListener('change', function(e){
					var f = e.target.files[0]; if(!f){ return; }
					var reader = new FileReader();
					reader.onload = function(ev){
						post({action:'horsetools_snip_import_json', data:ev.target.result}, function(res){
							if(!res||!res.success){ say((res&&res.data&&res.data.msg)||IO.badfile,'err'); return; }
							say(IO.imported.replace('%1$d',res.data.added).replace('%2$d',res.data.skipped),'good');
							setTimeout(function(){ location.reload(); }, 1200);
						});
					};
					reader.readAsText(f); file.value='';
				});
				// reference: click to copy
				document.querySelectorAll('.ht-ref-code').forEach(function(c){
					c.addEventListener('click', function(){
						var t = c.getAttribute('data-copy');
						if(navigator.clipboard){ navigator.clipboard.writeText(t); }
						c.classList.add('copied'); setTimeout(function(){ c.classList.remove('copied'); }, 900);
					});
				});
			})();
			</script>
			<div class="ht-card ht-snip4" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_snip' ) ); ?>" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
			  <h3><i class="ti ti-plug-connected-x"></i> <?php _e('Leave another shortcode plugin', 'horse-tools') ?></h3>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Most snippet plugins store their snippets as a custom post type. Enter that post type to import them all as Horse Tools snippets — the other plugin is only read, never changed. After importing you can deactivate it and (for Shortcoder) your [sc …] tags keep working, or use “Convert” above.', 'horse-tools'); ?></p>
				<p class="ht-snip-actions">
					<input type="text" id="ht-cpt-input" class="ht-input-big" style="max-width:260px" placeholder="<?php esc_attr_e( 'post type, e.g. shortcoder', 'horse-tools' ); ?>" value="shortcoder" />
					<button type="button" class="ht-priv-btn" id="ht-cpt-import"><i class="ti ti-download"></i> <?php _e( 'Import', 'horse-tools' ); ?></button>
				</p>
				<p class="ht-note"><?php _e( 'Known post types:', 'horse-tools' ); ?>
					<a class="ht-cpt-preset" data-cpt="shortcoder">Shortcoder</a> ·
					<a class="ht-cpt-preset" data-cpt="content_block">Content Blocks</a> ·
					<a class="ht-cpt-preset" data-cpt="woody_snippet">Woody snippets</a>
				</p>
				<div id="ht-cpt-out"></div>
			</div>

			<div class="ht-card ht-scman" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_snip' ) ); ?>" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
			  <h3><i class="ti ti-toggle-left"></i> <?php _e('Turn Horse Tools shortcodes on or off', 'horse-tools') ?></h3>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Switch off any shortcode you do not use. A disabled shortcode simply stops rendering. This only affects Horse Tools shortcodes — never those from other plugins.', 'horse-tools'); ?></p>
				<?php
				$horsetools_sc_off = (array) get_option( 'horsetools_sc_disabled', array() );
				foreach ( horsetools_sc_manageable() as $group => $tags ) {
					echo '<div class="ht-scman-group"><h4>' . esc_html( $group ) . '</h4>';
					foreach ( $tags as $tag ) {
						$on = ! in_array( $tag, $horsetools_sc_off, true );
						echo '<label class="ht-scman-item"><input type="checkbox" class="ht-scman-cb" value="' . esc_attr( $tag ) . '"' . checked( $on, true, false ) . ' /> <code>[' . esc_html( $tag ) . ']</code></label>';
					}
					echo '</div>';
				}
				?>
				<p class="ht-snip-actions">
					<button type="button" class="ht-priv-btn" id="ht-scman-save"><i class="ti ti-device-floppy"></i> <?php _e( 'Save shortcode status', 'horse-tools' ); ?></button>
				</p>
				<div id="ht-scman-out"></div>
			</div>
			<style>
			.ht-snip4 .ht-priv-btn,.ht-scman .ht-priv-btn{display:inline-flex;align-items:center;gap:6px;background:#fff;border:1px solid #e0a800;color:#8a5a00;padding:8px 14px;border-radius:8px;cursor:pointer;font-size:13px}
			.ht-snip4 .ht-priv-btn:hover,.ht-scman .ht-priv-btn:hover{background:#fff9e6}
			.ht-cpt-preset{cursor:pointer;color:#8a5a00;text-decoration:underline}
			.ht-scman-group{margin-bottom:12px}
			.ht-scman-group h4{margin:0 0 6px;font-size:13px;color:#8a5a00}
			.ht-scman-item{display:inline-flex;align-items:center;gap:5px;margin:0 14px 8px 0;font-size:13px}
			.ht-scman-item code{background:#fff8e1;border:1px solid #f0d98a;color:#8a5a00;border-radius:5px;padding:1px 6px}
			</style>
			<script>
			(function(){
				var im = document.querySelector('.ht-snip4');
				if (!im || im.dataset.ready) { return; }
				im.dataset.ready = '1';
				var AJAX = im.dataset.ajax, NONCE = im.dataset.nonce;
				function esc(s){ return String(s).replace(/[&<>]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;'}[c];}); }
				function post(data, done){
					data.nonce = NONCE;
					var body = Object.keys(data).map(function(k){ return encodeURIComponent(k)+'='+encodeURIComponent(data[k]); }).join('&');
					fetch(AJAX,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body}).then(function(r){return r.json();}).then(done).catch(function(){ done(null); });
				}
				var I18N = {
					imported: <?php echo wp_json_encode( __( 'Imported %1$d snippet(s); %2$d already existed. Reloading…', 'horse-tools' ) ); ?>,
					saved: <?php echo wp_json_encode( __( 'Saved.', 'horse-tools' ) ); ?>,
					fail: <?php echo wp_json_encode( __( 'Something went wrong.', 'horse-tools' ) ); ?>
				};
				var out = document.getElementById('ht-cpt-out');
				function say(el,t,cls){ el.innerHTML = '<div class="ht-snip-msg '+(cls||'')+'">'+esc(t)+'</div>'; }
				document.querySelectorAll('.ht-cpt-preset').forEach(function(a){
					a.addEventListener('click', function(){ document.getElementById('ht-cpt-input').value = a.getAttribute('data-cpt'); });
				});
				document.getElementById('ht-cpt-import').addEventListener('click', function(){
					var pt = document.getElementById('ht-cpt-input').value.trim();
					post({action:'horsetools_snip_import_sc', post_type:pt}, function(res){
						if(!res||!res.success){ say(out,(res&&res.data&&res.data.msg)||I18N.fail,'err'); return; }
						say(out,I18N.imported.replace('%1$d',res.data.imported).replace('%2$d',res.data.skipped),'good');
						setTimeout(function(){ location.reload(); }, 1300);
					});
				});

				// on/off manager
				var man = document.querySelector('.ht-scman');
				var mout = document.getElementById('ht-scman-out');
				var scForm = man.closest('form') || document.querySelector('.ht-wrap form[action$="options.php"]');
					if (scForm) {
						scForm.addEventListener('submit', function(e){
							if (scForm.dataset.htScmanDone === '1') { return; }
							e.preventDefault();
							var dis = [];
							man.querySelectorAll('.ht-scman-cb').forEach(function(cb){ if(!cb.checked){ dis.push(cb.value); } });
							var b = 'action=horsetools_sc_disable_save&nonce='+encodeURIComponent(man.dataset.nonce)+dis.map(function(t){ return '&disabled[]='+encodeURIComponent(t); }).join('');
							fetch(man.dataset.ajax,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:b}).catch(function(){}).then(function(){ scForm.dataset.htScmanDone='1'; scForm.submit(); });
						});
					}
					document.getElementById('ht-scman-save').addEventListener('click', function(){
					var disabled = [];
					man.querySelectorAll('.ht-scman-cb').forEach(function(cb){ if(!cb.checked){ disabled.push(cb.value); } });
					var body = 'action=horsetools_sc_disable_save&nonce='+encodeURIComponent(man.dataset.nonce)
						+ disabled.map(function(t){ return '&disabled[]='+encodeURIComponent(t); }).join('');
					fetch(man.dataset.ajax,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body})
						.then(function(r){return r.json();})
						.then(function(res){ say(mout, res&&res.success?I18N.saved:I18N.fail, res&&res.success?'good':'err'); })
						.catch(function(){ say(mout,I18N.fail,'err'); });
				});
			})();
			</script>
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


/* -------------------------------------------------------------------------
 * Snippets — admin AJAX: create / edit / delete, import from Shortcoder,
 * find usage across posts, and convert [sc] tags to [ht-snippet].
 *
 * All handlers require manage_options and share the horsetools_snip nonce.
 * Snippet content is stored raw (admin-only, unfiltered_html trust model);
 * the slug is always sanitised to a shortcode-safe key.
 * ---------------------------------------------------------------------- */

/** Normalise and persist the snippet store. */
function horsetools_snip_store( array $snips ) {
	update_option( 'horsetools_snippets', $snips, false );
}

/** Shared guard for every snippet AJAX endpoint. */
function horsetools_snip_guard() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'msg' => __( 'Permission denied.', 'horse-tools' ) ) );
	}
	check_ajax_referer( 'horsetools_snip', 'nonce' );
}

/** Return the store as a list for the UI (slug included, content trimmed for display size is left to JS). */
function horsetools_snip_list_payload() {
	$out = array();
	foreach ( horsetools_snippets_get() as $slug => $s ) {
		$out[] = array(
			'slug'      => $slug,
			'title'     => isset( $s['title'] ) ? $s['title'] : $slug,
			'desc'      => isset( $s['desc'] ) ? $s['desc'] : '',
			'content'   => isset( $s['content'] ) ? $s['content'] : '',
			'on'        => ! empty( $s['on'] ),
			'no_admin'  => ! empty( $s['no_admin'] ),
			'device'    => isset( $s['device'] ) ? $s['device'] : '',
			'login'     => isset( $s['login'] ) ? $s['login'] : '',
			'role'      => isset( $s['role'] ) ? $s['role'] : '',
			'date_from' => isset( $s['date_from'] ) ? $s['date_from'] : '',
			'date_to'   => isset( $s['date_to'] ) ? $s['date_to'] : '',
			'tags'      => isset( $s['tags'] ) && is_array( $s['tags'] ) ? $s['tags'] : array(),
		);
	}
	return $out;
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

	$snips          = horsetools_snippets_get();
	$snips[ $slug ] = array(
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
	horsetools_snip_store( $snips );

	wp_send_json_success( array( 'snippets' => horsetools_snip_list_payload(), 'saved' => $slug ) );
}

add_action( 'wp_ajax_horsetools_snip_delete', 'horsetools_snip_delete_ajax' );
function horsetools_snip_delete_ajax() {
	horsetools_snip_guard();
	$slug  = isset( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';
	$snips = horsetools_snippets_get();
	if ( isset( $snips[ $slug ] ) ) {
		unset( $snips[ $slug ] );
		horsetools_snip_store( $snips );
	}
	wp_send_json_success( array( 'snippets' => horsetools_snip_list_payload() ) );
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

	$snips    = horsetools_snippets_get();
	$imported = 0;
	$skipped  = 0;
	foreach ( $rows as $row ) {
		$slug = sanitize_key( $row->post_name );
		if ( '' === $slug ) {
			continue;
		}
		if ( isset( $snips[ $slug ] ) ) {
			$skipped++;
			continue; // never clobber an existing snippet
		}
		$snips[ $slug ] = array(
			'title'   => $row->post_title ? sanitize_text_field( $row->post_title ) : $slug,
			'content' => (string) $row->post_content, // raw
			'on'      => 1,
		);
		$imported++;
	}
	if ( $imported ) {
		horsetools_snip_store( $snips );
	}
	wp_send_json_success( array(
		'snippets' => horsetools_snip_list_payload(),
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
	wp_send_json_success( array( 'json' => wp_json_encode( horsetools_snip_list_payload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) );
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
	$snips    = horsetools_snippets_get();
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
		if ( isset( $snips[ $slug ] ) ) {
			$skipped++;
			continue; // never clobber an existing snippet
		}
		$tags = isset( $entry['tags'] ) ? $entry['tags'] : '';
		$snips[ $slug ] = array(
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
		$added++;
	}
	if ( $added ) {
		horsetools_snip_store( $snips );
	}
	wp_send_json_success( array( 'snippets' => horsetools_snip_list_payload(), 'added' => $added, 'skipped' => $skipped ) );
}

/* -------------------------------------------------------------------------
 * Insert-shortcode button for BOTH editors.
 *
 * Gutenberg gets a small "Horse Tools" block (htsc-block.js) with a picker;
 * the Classic editor gets a Quicktags button (htsc-qt.js). Both are fed the
 * same list of shortcode templates + the site's own snippets.
 * ---------------------------------------------------------------------- */

/** Shortcode templates + snippets offered by the editor picker. */
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
	);
	foreach ( horsetools_snippets_get() as $slug => $s ) {
		$title    = ! empty( $s['title'] ) ? $s['title'] : $slug;
		$items[] = array(
			'label'  => $title . ' — [ht-snippet]',
			'insert' => '[ht-snippet name="' . $slug . '"]',
		);
	}
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
