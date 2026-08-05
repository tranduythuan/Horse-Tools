<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_shortcode_options; ?>
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
