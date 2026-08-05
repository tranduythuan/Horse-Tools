<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_shortcode_options; ?>
			<div class="ht-card ht-snip" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_snip' ) ); ?>" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
			  <h3><i class="ti ti-file-code"></i> <?php _e('Custom shortcodes (snippets)', 'horse-tools') ?></h3>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Create a named block of HTML/CSS/JS and drop it anywhere with [ht-snippet name="your-name"]. Placeholders {{param}} or %%param%% are filled from the shortcode’s attributes, plus built-ins: currentyear, currentdate, postid, posttitle, sitename, siteurl.', 'horse-tools'); ?></p>

				<p><input type="text" id="ht-snip-name" class="ht-input-big" placeholder="<?php esc_attr_e( 'Name — letters, numbers, dashes (e.g. call-to-action)', 'horse-tools' ); ?>" /></p>
				<p><input type="text" id="ht-snip-title" class="ht-input-big" placeholder="<?php esc_attr_e( 'Display name (shown in the list)', 'horse-tools' ); ?>" /></p>
				<p><input type="text" id="ht-snip-desc" class="ht-input-big" placeholder="<?php esc_attr_e( 'Description (for your reference)', 'horse-tools' ); ?>" /></p>
				<div class="ht-howto"><i class="ti ti-info-circle"></i><span><?php _e( 'Write your block just like a post: type text and use the toolbar to insert links, images and formatting. Need raw HTML/CSS/JS? Switch to the “Text” tab. Placeholders like {{url}} still work in both.', 'horse-tools' ); ?></span></div>
				<div class="ht-snip-editor">
				<?php
				wp_editor( '', 'htsnippetbody', array(
					'media_buttons' => true,
					'textarea_rows' => 9,
					'teeny'         => false,
					'quicktags'     => true,
					'tinymce'       => array(
						'toolbar1' => 'formatselect,bold,italic,bullist,numlist,link,unlink,alignleft,aligncenter,alignright,forecolor,removeformat,undo,redo',
					),
				) );
				?>
				</div>

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

				<?php
				// PHP snippets: shown only where they could actually be used, and
				// gated behind the account's own two-factor code.
				$ht_php_why = function_exists( 'horsetools_php_user_blocked' ) ? horsetools_php_user_blocked() : 'off';
				if ( function_exists( 'horsetools_php_user_blocked' ) ) :
					?>
					<div class="ht-snip-php">
						<?php if ( '' === $ht_php_why ) : ?>
							<?php if ( 'file_edit' === horsetools_php_notice_reason() ) : ?>
								<p class="ht-note"><i class="ti ti-info-circle"></i>
									<?php _e( 'This site has WordPress\'s built-in file editor switched off (DISALLOW_FILE_EDIT) — good. PHP snippets still work, because they carry their own protections: two-factor unlock, a syntax check before saving, auto-disable on a crash, and code signing. To switch them off as well, add HORSETOOLS_NO_PHP to wp-config.php.', 'horse-tools' ); ?></p>
							<?php endif; ?>
							<label class="ht-php-toggle">
								<input type="checkbox" id="ht-snip-php" />
								<b><?php _e( 'Run this snippet as PHP', 'horse-tools' ); ?></b>
							</label>
							<div id="ht-snip-php-box" hidden>
								<div id="ht-snip-php-lock" <?php echo horsetools_php_unlocked() ? 'hidden' : ''; ?>>
									<p class="ht-note"><i class="ti ti-lock"></i>
										<?php _e( 'PHP runs with full access to your site, so it stays locked. Enter a current code from your authenticator to unlock it for 15 minutes.', 'horse-tools' ); ?></p>
									<p>
										<input type="text" id="ht-snip-php-code" inputmode="numeric" autocomplete="off"
											placeholder="<?php esc_attr_e( '6-digit code', 'horse-tools' ); ?>" style="width:130px" />
										<button type="button" class="ht-priv-btn" id="ht-snip-php-unlock"><i class="ti ti-lock-open"></i> <?php _e( 'Unlock', 'horse-tools' ); ?></button>
									</p>
								</div>
								<div id="ht-snip-php-fields" <?php echo horsetools_php_unlocked() ? '' : 'hidden'; ?>>
									<p class="ht-note"><i class="ti ti-bulb"></i>
										<?php _e( 'Write plain PHP — no opening tag needed. The code is checked for syntax errors before it is saved, and if it ever crashes a page Horse Tools switches it off automatically.', 'horse-tools' ); ?></p>
									<label><span><?php _e( 'Where it runs', 'horse-tools' ); ?></span>
										<select id="ht-snip-php-hook">
											<?php foreach ( horsetools_php_hooks_allowed() as $k => $label ) : ?>
												<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></option>
											<?php endforeach; ?>
										</select></label>
									<label><span><?php _e( 'Side of the site', 'horse-tools' ); ?></span>
										<select id="ht-snip-php-scope">
											<?php foreach ( horsetools_php_scopes_allowed() as $k => $label ) : ?>
												<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $label ); ?></option>
											<?php endforeach; ?>
										</select></label>
								</div>
							</div>
						<?php else : ?>
							<p class="ht-note"><i class="ti ti-shield-lock"></i>
								<?php
								$ht_php_msgs = array(
									'constant'     => __( 'PHP snippets are switched off by HORSETOOLS_NO_PHP in wp-config.php.', 'horse-tools' ),
									'file_mods'    => __( 'PHP snippets are unavailable because this site sets DISALLOW_FILE_MODS — the platform does not allow code changes at all.', 'horse-tools' ),
									'cap'          => __( 'Only a full administrator may use PHP snippets.', 'horse-tools' ),
									'no2fa_module' => __( 'To use PHP snippets, switch on two-factor authentication first (Horse Tools → Overview → Security).', 'horse-tools' ),
									'no2fa_user'   => __( 'To use PHP snippets, switch on two-factor authentication for your own account first (Users → Profile).', 'horse-tools' ),
								);
								echo esc_html( isset( $ht_php_msgs[ $ht_php_why ] ) ? $ht_php_msgs[ $ht_php_why ] : __( 'PHP snippets are not available.', 'horse-tools' ) );
								?>
							</p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

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
			.ht-snip-editor{margin:4px 0 6px}
			.ht-snip-editor .wp-editor-wrap{border-radius:8px}
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
				var F = { name:$('ht-snip-name'), title:$('ht-snip-title'), desc:$('ht-snip-desc'), content:$('htsnippetbody'),
					on:$('ht-snip-on'), noadmin:$('ht-snip-noadmin'), device:$('ht-snip-device'), login:$('ht-snip-login'),
					role:$('ht-snip-role'), tags:$('ht-snip-tags'), from:$('ht-snip-from'), to:$('ht-snip-to'),
					php:$('ht-snip-php'), phpHook:$('ht-snip-php-hook'), phpScope:$('ht-snip-php-scope') };
				var msg=$('ht-snip-msg'), list=$('ht-snip-list'), tagFilter=$('ht-snip-tagfilter');
					var search=$('ht-snip-search'), countlbl=$('ht-snip-countlbl');
					var adv=document.querySelector('.ht-snip-adv');
					// The content box is the WordPress editor (wp_editor id "htsnippetbody").
					// These helpers sync it with the JS-driven create/edit/clear flow,
					// working in both the Visual (TinyMCE) and Text (plain) tabs.
					function htEd(){ return window.tinymce ? window.tinymce.get('htsnippetbody') : null; }
					function htSetEditor(html){
						html = html || '';
						if (F.content){ F.content.value = html; }
						var ed = htEd();
						if (ed){ ed.setContent(html); }
					}
					function htFlushEditor(){ if (window.tinymce){ window.tinymce.triggerSave(); } }
				var PHPNONCE = <?php echo wp_json_encode( wp_create_nonce( 'horsetools_php' ) ); ?>;
				var I18N = {
					phpUnlocked: <?php echo wp_json_encode( __( 'PHP editing unlocked for %d minutes.', 'horse-tools' ) ); ?>,
					phpBad: <?php echo wp_json_encode( __( 'This snippet’s code no longer matches its signature — it was changed outside this screen and will not run. Review it and save again to re-sign it.', 'horse-tools' ) ); ?>,
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
						F.name.value=s.slug; F.title.value=s.title||''; F.desc.value=s.desc||''; htSetEditor(s.content||'');
						F.on.value=s.on?'1':'0'; F.noadmin.value=s.no_admin?'1':'0'; F.device.value=s.device||''; F.login.value=s.login||'';
						F.role.value=s.role||''; F.tags.value=(s.tags||[]).join(', '); F.from.value=s.date_from||''; F.to.value=s.date_to||'';
						if (F.php){ F.php.checked=!!s.php; F.php.dispatchEvent(new Event('change')); }
						if (F.phpHook){ F.phpHook.value=s.php_hook||''; }
						if (F.phpScope){ F.phpScope.value=s.php_scope||'front'; }
						if (s.php_bad){ say(msg, I18N.phpBad, 'err'); }
						if (adv && (s.device||s.login||s.role||s.no_admin||s.date_from||s.date_to||(s.tags&&s.tags.length)||!s.on)) { adv.open=true; }
						F.name.focus(); window.scrollTo({top:0,behavior:'smooth'}); } return; }
					if (dl){ if(!confirm(I18N.confirmDel))return; post({action:'horsetools_snip_delete',slug:dl.dataset.del}, function(res){ if(res&&res.success){ snippets=res.data.snippets; render(); say(msg,I18N.deleted,'good'); } else { say(msg,(res&&res.data&&res.data.msg)||I18N.fail,'err'); } }); return; }
				});

				// ---- PHP snippets: reveal the box, unlock with a 2FA code ----
				var phpBox=$('ht-snip-php-box'), phpLock=$('ht-snip-php-lock'), phpFields=$('ht-snip-php-fields');
				if (F.php && phpBox){
					var syncPhp=function(){ phpBox.hidden = !F.php.checked; };
					F.php.addEventListener('change', syncPhp);
					syncPhp();
				}
				if ($('ht-snip-php-unlock')){
					$('ht-snip-php-unlock').addEventListener('click', function(){
						var code=$('ht-snip-php-code');
						post({action:'horsetools_php_unlock', php_nonce:PHPNONCE, code:code.value}, function(res){
							if (res&&res.success){
								phpLock.hidden=true; phpFields.hidden=false; code.value='';
								say(msg, I18N.phpUnlocked.replace('%d', res.data.minutes), 'good');
							} else { say(msg,(res&&res.data&&res.data.msg)||I18N.fail,'err'); }
						});
					});
				}

				$('ht-snip-save').addEventListener('click', function(){
					htFlushEditor();
					post({action:'horsetools_snip_save', slug:F.name.value, title:F.title.value, desc:F.desc.value, content:F.content.value,
						on:F.on.value, no_admin:F.noadmin.value, device:F.device.value, login:F.login.value, role:F.role.value,
						tags:F.tags.value, date_from:F.from.value, date_to:F.to.value,
						php:(F.php&&F.php.checked)?'1':'0',
						php_hook:F.phpHook?F.phpHook.value:'', php_scope:F.phpScope?F.phpScope.value:'front'}, function(res){
						if (res&&res.success){ snippets=res.data.snippets; render(); say(msg,I18N.saved,'good'); }
						else { say(msg,(res&&res.data&&res.data.msg)||I18N.fail,'err'); }
					});
				});
				$('ht-snip-clear').addEventListener('click', function(){
					F.name.value=F.title.value=F.desc.value=F.content.value=F.tags.value=F.from.value=F.to.value='';
					htSetEditor('');
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
