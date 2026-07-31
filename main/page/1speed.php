<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('OPTIMIZE', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check1" data-target="play1" type="checkbox" name="horsetools_settings[speed]" value="1" <?php if ( isset($horsetools_options['speed']) && 1 == $horsetools_options['speed'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play1" class="toggle-div ht-card">
  <h3><i class="ti ti-square-minus"></i> <?php _e('Disable unnecessary items', 'horse-tools') ?></h3>
	<!-- tôi ưu 1 -->
	<?php horsetools_toggle( 'speed-off1', __( 'Disable jQuery Migrate', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Disable unnecessary items',
		'description' => __( 'jQuery Migrate is a library used to maintain the operation of certain themes, plugins that rely on older code. If your website no longer relies on this library, you can disable it', 'horse-tools' ),
	) ); ?>
	<!-- tôi ưu 2 -->
	<?php horsetools_toggle( 'speed-off2', __( 'Disable Gutenberg CSS', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Disable unnecessary items',
		'description' => __( 'If you not using it, you can disable Gutenberg CSS on the homepage', 'horse-tools' ),
	) ); ?>
	<!-- tôi ưu 3 -->
	<?php horsetools_toggle( 'speed-off3', __( 'Disable Classic CSS', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Disable unnecessary items',
		'description' => __( 'If you not using it, you can disable Classic CSS on the homepage', 'horse-tools' ),
	) ); ?>
	<!-- tôi ưu 4 -->
	<?php horsetools_toggle( 'speed-off4', __( 'Disable Emoji', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Disable unnecessary items',
		'description' => __( 'If you not using it, you can disable Emoji', 'horse-tools' ),
	) ); ?>
		<!-- dashicons -->
		<?php horsetools_toggle( 'speed-dash1', __( 'Disable Dashicons for visitors', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'Disable unnecessary items',
			'description' => __( 'Removes the admin icon font (Dashicons) on the front-end for logged-out visitors, who never see it. It is kept for logged-in users because the admin bar uses it.', 'horse-tools' ),
		) ); ?>
		<!-- heartbeat -->
		<?php horsetools_toggle( 'speed-hb1', __( 'Control the Heartbeat API', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'Disable unnecessary items',
			'description' => __( 'WordPress “Heartbeat” pings the server every 15–60 seconds (autosave, post-lock, dashboard). Slowing or limiting it cuts admin-ajax.php load, especially with several admin tabs open.', 'horse-tools' ),
		) ); ?>
		<p class="ht-field" data-ht-parent="ht-main-speed-hb1">
		<label class="ht-field-label"><?php _e('Heartbeat mode', 'horse-tools'); ?></label>
		<select name="horsetools_settings[speed-hb2]">
			<?php
			$ht_hb = ! empty( $horsetools_options['speed-hb2'] ) ? $horsetools_options['speed-hb2'] : 'slow';
			$ht_hb_opts = array(
				'slow'     => __( 'Slow down to 60 seconds (safe)', 'horse-tools' ),
				'frontend' => __( 'Disable on the front-end, slow in admin', 'horse-tools' ),
				'minimal'  => __( 'Only in the post editor (autosave/locking)', 'horse-tools' ),
			);
			foreach ( $ht_hb_opts as $ht_v => $ht_l ) {
				echo '<option value="' . esc_attr( $ht_v ) . '"' . selected( $ht_hb, $ht_v, false ) . '>' . esc_html( $ht_l ) . '</option>';
			}
			?>
		</select>
		</p>

  <h3><i class="ti ti-brand-javascript"></i> <?php _e('Optimization Library', 'horse-tools') ?></h3>
	<!-- thư vien js 1 -->
	<?php horsetools_toggle( 'speed-link1', __( 'Enable Instant-page', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Optimization Library',
		'description' => __( 'Instant-page is a library that allows you to preload the content of a linked page into the browser memory simply by hovering over the link. When you click on the link, it provides a remarkably fast loading experience', 'horse-tools' ),
	) ); ?>
	<!-- thư vien js 2 -->
	<?php horsetools_toggle( 'speed-link2', __( 'Enable Smooth-scroll', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Optimization Library',
		'description' => __( 'Smooth-scroll is a library that enables you to create a smooth scrolling effect, providing users with a perception of faster page navigation', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-bolt"></i> <?php _e('JavaScript &amp; connections', 'horse-tools') ?></h3>
		<!-- defer js -->
		<?php horsetools_toggle( 'speed-defer1', __( 'Defer JavaScript', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'JavaScript & connections',
			'description' => __( 'Add “defer” to front-end scripts so they no longer block the page from rendering; they run in order once the HTML is parsed. jQuery is never deferred (inline snippets depend on it). Big Core Web Vitals win.', 'horse-tools' ),
			'warning'     => __( 'If a theme/plugin script misbehaves, add its handle or file name to the exclusion list below. Disable if you already use a full-page optimiser that defers scripts.', 'horse-tools' ),
		) ); ?>
		<p class="ht-field">
		<label class="ht-field-label"><?php _e('Scripts to exclude from defer (one per line — a script handle or part of its URL)', 'horse-tools'); ?></label>
		<textarea style="height:80px;" class="ht-code-textarea" name="horsetools_settings[speed-defer-exclude]" placeholder="jquery-migrate&#10;slider.min.js"><?php if(!empty($horsetools_options['speed-defer-exclude'])){echo esc_textarea($horsetools_options['speed-defer-exclude']);} ?></textarea>
		</p>

		<!-- preconnect -->
		<?php horsetools_toggle( 'speed-pre1', __( 'Preconnect to third-party hosts', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'JavaScript & connections',
			'description' => __( 'Tell the browser to start the DNS + TCP + TLS handshake to external hosts early (fonts, CDN, analytics), so their files arrive sooner. Adds preconnect and dns-prefetch hints to the page head.', 'horse-tools' ),
		) ); ?>
		<p class="ht-field">
		<label class="ht-field-label"><?php _e('Hosts to preconnect (one per line — host or full URL)', 'horse-tools'); ?></label>
		<textarea style="height:80px;" class="ht-code-textarea" name="horsetools_settings[speed-pre-hosts]" placeholder="fonts.googleapis.com&#10;fonts.gstatic.com"><?php if(!empty($horsetools_options['speed-pre-hosts'])){echo esc_textarea($horsetools_options['speed-pre-hosts']);} ?></textarea>
		</p>
		<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Only add hosts the page really uses. Preconnecting to a host you do not load from wastes a connection.', 'horse-tools'); ?></p>

		<!-- preload -->
		<?php horsetools_toggle( 'speed-preload1', __( 'Preload critical assets', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'JavaScript & connections',
			'description' => __( 'Start fetching a few important files immediately (the LCP image, a web font, the main CSS). The type is detected from the file extension. Do not preload many files — it competes with everything else for bandwidth.', 'horse-tools' ),
		) ); ?>
		<p class="ht-field">
		<label class="ht-field-label"><?php _e('Asset URLs to preload (one per line — .woff2/.css/.js/.jpg/.webp…)', 'horse-tools'); ?></label>
		<textarea style="height:80px;" class="ht-code-textarea" name="horsetools_settings[speed-preload-urls]" placeholder="https://example.com/wp-content/uploads/hero.webp&#10;https://example.com/fonts/main.woff2"><?php if(!empty($horsetools_options['speed-preload-urls'])){echo esc_textarea($horsetools_options['speed-preload-urls']);} ?></textarea>
		</p>

  <h3><i class="ti ti-hourglass-low"></i> <?php _e('Delay JavaScript until interaction', 'horse-tools') ?></h3>
		<!-- delay js -->
		<?php horsetools_toggle( 'speed-delay1', __( 'Delay JavaScript execution', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'Delay JavaScript until interaction',
			'description' => __( 'Hold heavy third-party scripts (analytics, tag managers, pixels, chat, ads) until the visitor first interacts — scroll, mouse move, tap, key or click — then run them in order. The single biggest win for Total Blocking Time and “Reduce unused JavaScript”. It re-fires the page-ready events afterwards, keeps script order, and never touches JSON-LD structured data or ES modules.', 'horse-tools' ),
			'warning'     => __( 'Test the site after turning this on. If something that must work before interaction breaks (a hero slider, a cookie bar), add its handle or file name to a list below. Do not use together with “Delay JS” in another optimiser.', 'horse-tools' ),
		) ); ?>
		<p class="ht-field" data-ht-parent="ht-main-speed-delay1">
		<label class="ht-field-label"><?php _e('Mode', 'horse-tools'); ?></label>
		<select name="horsetools_settings[speed-delay-mode]">
			<?php
			$ht_dm = ! empty( $horsetools_options['speed-delay-mode'] ) ? $horsetools_options['speed-delay-mode'] : 'listed';
			$ht_dm_opts = array(
				'listed' => __( 'Delay only the scripts I list (recommended, safe)', 'horse-tools' ),
				'all'    => __( 'Delay all scripts except the exclusions (most aggressive)', 'horse-tools' ),
			);
			foreach ( $ht_dm_opts as $ht_v => $ht_l ) {
				echo '<option value="' . esc_attr( $ht_v ) . '"' . selected( $ht_dm, $ht_v, false ) . '>' . esc_html( $ht_l ) . '</option>';
			}
			?>
		</select>
		</p>
		<p class="ht-field" data-ht-parent="ht-main-speed-delay1">
		<label class="ht-field-label"><?php _e('“Listed” mode — scripts to delay (one per line: a handle, file name or part of a URL). Leave empty to use the built-in list of common trackers.', 'horse-tools'); ?></label>
		<textarea style="height:90px;" class="ht-code-textarea" name="horsetools_settings[speed-delay-list]" placeholder="googletagmanager&#10;fbevents&#10;hotjar&#10;tawk.to"><?php if(!empty($horsetools_options['speed-delay-list'])){echo esc_textarea($horsetools_options['speed-delay-list']);} ?></textarea>
		</p>
		<p class="ht-field" data-ht-parent="ht-main-speed-delay1">
		<label class="ht-field-label"><?php _e('“All” mode — scripts to NEVER delay (one per line). A hero slider or a cookie-consent script usually belongs here.', 'horse-tools'); ?></label>
		<textarea style="height:80px;" class="ht-code-textarea" name="horsetools_settings[speed-delay-exclude]" placeholder="slider&#10;consent"><?php if(!empty($horsetools_options['speed-delay-exclude'])){echo esc_textarea($horsetools_options['speed-delay-exclude']);} ?></textarea>
		</p>
		<?php horsetools_input( 'speed-delay-timeout', __( 'Fall-back timer — run the delayed scripts after this many seconds even with no interaction (0 = only on interaction)', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'Delay JavaScript until interaction',
			'type'        => 'number',
			'class'       => 'ht-input-small',
			'placeholder' => '0',
			'parent'      => 'speed-delay1',
		) ); ?>
		<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Tip: to stop one specific script from ever being delayed, add the attribute data-ht-no-delay to its tag. Logged-in users are never affected.', 'horse-tools'); ?></p>
		<p class="ht-field" data-ht-parent="ht-main-speed-delay1">
		<button type="button" class="button" id="ht-scanjs" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_speed_scanjs' ) ); ?>"><i class="ti ti-radar-2"></i> <?php _e('Scan the scripts running on the home page', 'horse-tools'); ?></button>
		<span id="ht-scanjs-msg" class="description" style="margin-left:8px"></span>
		</p>
		<div id="ht-scanjs-list" data-ht-parent="ht-main-speed-delay1" style="margin:6px 0;overflow-x:auto"></div>
		<script>
		document.addEventListener('DOMContentLoaded',function(){
			var btn=document.getElementById('ht-scanjs'); if(!btn){return;}
			var box=document.getElementById('ht-scanjs-list'), msg=document.getElementById('ht-scanjs-msg');
			function ta(name){ return document.querySelector('textarea[name="horsetools_settings['+name+']"]'); }
			function addKw(name,kw){
				var t=ta(name); if(!t||!kw){return;}
				var lines=t.value.split(/\r?\n/).map(function(s){return s.trim();}).filter(Boolean);
				if(lines.indexOf(kw)===-1){ lines.push(kw); t.value=lines.join('\n'); t.dispatchEvent(new Event('change',{bubbles:true})); }
			}
			var I18N={
				scanning:<?php echo wp_json_encode( __( 'Scanning…', 'horse-tools' ) ); ?>,
				none:<?php echo wp_json_encode( __( 'No scripts found.', 'horse-tools' ) ); ?>,
				found:<?php echo wp_json_encode( __( '%d scripts found — “+ Delay” holds one back, “+ Exclude” keeps it running immediately:', 'horse-tools' ) ); ?>,
				delayed:<?php echo wp_json_encode( __( 'delayed', 'horse-tools' ) ); ?>,
				live:<?php echo wp_json_encode( __( 'runs now', 'horse-tools' ) ); ?>,
				delay:<?php echo wp_json_encode( __( '+ Delay', 'horse-tools' ) ); ?>,
				excl:<?php echo wp_json_encode( __( '+ Exclude', 'horse-tools' ) ); ?>,
				added:<?php echo wp_json_encode( __( 'added ✓', 'horse-tools' ) ); ?>,
				err:<?php echo wp_json_encode( __( 'Scan failed.', 'horse-tools' ) ); ?>
			};
			btn.addEventListener('click',function(){
				msg.textContent=I18N.scanning; btn.disabled=true; box.innerHTML='';
				fetch(ajaxurl,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({action:'horsetools_speed_scanjs',nonce:btn.dataset.nonce})})
				.then(function(r){return r.json();}).then(function(res){
					btn.disabled=false;
					if(!res.success){ msg.textContent=(res.data&&res.data.msg)||I18N.err; return; }
					var list=res.data.scripts||[];
					msg.textContent=I18N.found.replace('%d',res.data.total);
					if(!list.length){ box.innerHTML='<p class="description">'+I18N.none+'</p>'; return; }
					var html='<table class="widefat striped" style="max-width:840px"><tbody>';
					list.forEach(function(s){
						var badge=s.delayed?'<span style="color:#1d9e75;font-weight:600">● '+I18N.delayed+'</span>':'<span style="color:#8a8a8a">○ '+I18N.live+'</span>';
						var acts='';
						if(s.keyword){ acts='<a href="javascript:void(0)" class="ht-scan-add" data-t="speed-delay-list" data-k="'+encodeURIComponent(s.keyword)+'">'+I18N.delay+'</a> · <a href="javascript:void(0)" class="ht-scan-add" data-t="speed-delay-exclude" data-k="'+encodeURIComponent(s.keyword)+'">'+I18N.excl+'</a>'; }
						html+='<tr><td style="width:96px">'+badge+'</td><td><code style="word-break:break-all">'+String(s.label).replace(/&/g,'&amp;').replace(/</g,'&lt;')+'</code></td><td style="width:150px;text-align:right;white-space:nowrap">'+acts+'</td></tr>';
					});
					html+='</tbody></table>';
					box.innerHTML=html;
					box.querySelectorAll('.ht-scan-add').forEach(function(a){
						a.addEventListener('click',function(){ addKw(a.dataset.t, decodeURIComponent(a.dataset.k)); a.textContent=I18N.added; a.style.pointerEvents='none'; a.style.opacity='0.6'; });
					});
				}).catch(function(){ btn.disabled=false; msg.textContent=I18N.err; });
			});
		});
		</script>

  <h3><i class="ti ti-loader"></i> <?php _e('The function of lazy loading images', 'horse-tools') ?></h3>
	<!-- lazyload img 1 -->
	<?php horsetools_toggle( 'speed-lazy1', __( 'Native image lazy-load + async decode', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'The function of lazy loading images',
		'description' => __( 'Adds decoding="async" to images so the browser decodes them off the main thread, and relies on WordPress’ built-in native lazy-load (which correctly keeps the first/LCP image eager). Replaces the old script-based method that removed image src — that hurt SEO and broke images with JavaScript off.', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-brush"></i> <?php _e('Load CSS without blocking render', 'horse-tools') ?></h3>
		<?php horsetools_toggle( 'speed-acss1', __( 'Async CSS — remove render-blocking stylesheets', 'horse-tools' ), array(
			'tab'         => 'OPTIMIZE',
			'section'     => 'Load CSS without blocking render',
			'description' => __( 'Loads stylesheets without blocking the first paint (the media-toggle technique), so the page appears on screen sooner. A big win for First Contentful Paint and Lighthouse’s “Eliminate render-blocking resources”.', 'horse-tools' ),
			'warning'     => __( 'The most powerful but riskiest speed option. Without the Critical CSS below, the page can flash unstyled for a moment (FOUC). Paste your above-the-fold CSS, or keep the main theme stylesheet in the exclusion list. Test the front end carefully, and don’t combine it with another plugin that also optimises CSS delivery.', 'horse-tools' ),
		) ); ?>
		<p class="ht-field" data-ht-parent="ht-main-speed-acss1">
		<label class="ht-field-label"><?php _e('Critical CSS — the above-the-fold styles, inlined in the head to prevent the flash (optional, but strongly recommended)', 'horse-tools'); ?></label>
		<textarea style="height:120px;" class="ht-code-textarea" name="horsetools_settings[speed-acss-critical]" placeholder="body{margin:0}&#10;.header{…}"><?php if(!empty($horsetools_options['speed-acss-critical'])){echo esc_textarea($horsetools_options['speed-acss-critical']);} ?></textarea>
		</p>
		<p class="ht-field" data-ht-parent="ht-main-speed-acss1">
		<label class="ht-field-label"><?php _e('Stylesheets to keep render-blocking (one per line — a handle or part of the URL). Put your main theme CSS here if you don’t have critical CSS yet.', 'horse-tools'); ?></label>
		<textarea style="height:70px;" class="ht-code-textarea" name="horsetools_settings[speed-acss-exclude]" placeholder="style.css&#10;flatsome"><?php if(!empty($horsetools_options['speed-acss-exclude'])){echo esc_textarea($horsetools_options['speed-acss-exclude']);} ?></textarea>
		</p>
		<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Only affects enqueued stylesheets, and only for logged-out visitors. A fallback keeps every stylesheet working when JavaScript is turned off.', 'horse-tools'); ?></p>

  <h3><i class="ti ti-file-zip"></i> <?php _e('Compress HTML into a single line', 'horse-tools') ?></h3>
	<!-- nén 1 -->
	<?php horsetools_toggle( 'speed-zip1', __( 'Enable HTML compression', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Compress HTML into a single line',
		'description' => __( 'With this feature, HTML will be compressed into a single line, removing unnecessary characters and whitespace to speed up page loading', 'horse-tools' ),
		'warning'     => __( 'Do not enable if you are using optimization plugins with similar functionality (conflict)', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'speed-zip11', __( 'Minify Inline JavaScript', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>
	<?php horsetools_toggle( 'speed-zip12', __( 'Remove comments from HTML, JavaScript, and CSS', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>
	<?php horsetools_toggle( 'speed-zip13', __( 'Remove XHTML closing tags from empty elements in HTML5', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>
	<?php horsetools_toggle( 'speed-zip14', __( 'Remove relative domain from internal URLs', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>
	<?php horsetools_toggle( 'speed-zip15', __( 'Remove protocols (HTTP: and HTTPS:) from all URLs', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>
	<?php horsetools_toggle( 'speed-zip16', __( 'Support multi-byte UTF-8 encoding (if you see strange characters)', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Compress HTML into a single line',
		'parent'  => 'speed-zip1',
	) ); ?>


  <h3><i class="ti ti-database"></i> <?php _e('Optimize saving post content into the database', 'horse-tools') ?></h3>
	<!-- csdl 1 -->
	<?php horsetools_toggle( 'speed-data1', __( 'Enable revision limit', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Optimize saving post content into the database',
	) ); ?>

	<?php horsetools_input( 'speed-data11', __( 'Enter the number of revisions', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Optimize saving post content into the database',
		'type'        => 'number',
		'class'       => 'ht-input-small',
		'placeholder' => '3',
		'parent'      => 'speed-data1',
	) ); ?>

	<!-- csdl 2 -->
	<?php horsetools_toggle( 'speed-data2', __( 'Change save interval', 'horse-tools' ), array(
		'tab'     => 'OPTIMIZE',
		'section' => 'Optimize saving post content into the database',
	) ); ?>

	<?php horsetools_input( 'speed-data21', __( 'Save interval (minutes)', 'horse-tools' ), array(
		'tab'         => 'OPTIMIZE',
		'section'     => 'Optimize saving post content into the database',
		'type'        => 'number',
		'class'       => 'ht-input-small',
		'placeholder' => '1',
		'parent'      => 'speed-data2',
	) ); ?>

	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('If you enable this feature and set automatic revision limit and automatic save time for posts or pages, it will reduce the amount of data stored in the database', 'horse-tools'); ?></p>
</div>
