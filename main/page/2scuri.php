<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('SECURITY', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check2" data-target="play2" type="checkbox" name="horsetools_settings[scuri]" value="1" <?php if ( isset($horsetools_options['scuri']) && 1 == $horsetools_options['scuri'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play2" class="ht-card toggle-div">

  <h3><i class="ti ti-user-shield"></i> <?php _e('Limit login attempts', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-login1', __( 'Lock out repeated failed logins', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Limit login attempts',
		'description' => __( 'After too many failed logins from the same address, block further attempts for a while. This is the real defence against password guessing.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_input( 'scuri-login-max', __( 'Attempts before lockout', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Limit login attempts',
		'type'        => 'number',
		'class'       => 'ht-input-small',
		'placeholder' => '5',
		'min'         => '1',
		'max'         => '50',
		'parent'      => 'scuri-login1',
	) ); ?>
	<?php horsetools_input( 'scuri-login-mins', __( 'Lockout length', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Limit login attempts',
		'type'        => 'number',
		'class'       => 'ht-input-small',
		'placeholder' => '15',
		'min'         => '1',
		'max'         => '999',
		'parent'      => 'scuri-login1',
	) ); ?>
	<?php
	global $horsetools_options;
	$ht_lock_unit  = isset( $horsetools_options['scuri-login-unit'] ) ? $horsetools_options['scuri-login-unit'] : 'minutes';
	$ht_lock_units = array( 'minutes' => __( 'Minute(s)', 'horse-tools' ), 'hours' => __( 'Hour(s)', 'horse-tools' ), 'days' => __( 'Day(s)', 'horse-tools' ) );
	?>
	<p class="ht-field" data-ht-parent="ht-main-scuri-login1">
		<select name="horsetools_settings[scuri-login-unit]">
			<?php foreach ( $ht_lock_units as $k => $v ) { echo '<option value="' . esc_attr( $k ) . '"' . selected( $ht_lock_unit, $k, false ) . '>' . esc_html( $v ) . '</option>'; } ?>
		</select>
		<label class="ht-right-text"><?php _e( 'Lockout time unit', 'horse-tools' ); ?></label>
	</p>
	<?php horsetools_toggle( 'scuri-login-mail', __( 'Email me when an address is locked out', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Limit login attempts',
		'parent'  => 'scuri-login1',
	) ); ?>
	<?php horsetools_toggle( 'scuri-login-proxy', __( 'Site is behind Cloudflare or a proxy (use the real visitor IP)', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Limit login attempts',
		'parent'      => 'scuri-login1',
		'description' => __( 'By default the lockout counts the direct connection IP, which cannot be faked. Only turn this on if your site is reachable ONLY through Cloudflare or a proxy — it then reads the real visitor IP from the proxy header (CF-Connecting-IP / X-Forwarded-For). Do NOT enable it on a normally-hosted site: those headers can be forged, letting an attacker dodge the lockout or get an innocent visitor locked out.', 'horse-tools' ),
	) ); ?>

	<p class="ht-field" data-ht-parent="ht-main-scuri-login1">
		<button type="button" id="ht-lla-reset" class="ht-priv-btn" style="display:inline-flex;align-items:center;gap:6px;background:#fff;border:1px solid #e0a800;color:#8a5a00;padding:8px 14px;border-radius:8px;cursor:pointer;font-size:13px" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_lla' ) ); ?>" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"><i class="ti ti-lock-open"></i> <?php _e( 'Reset all login lockouts now', 'horse-tools' ); ?></button>
		<span id="ht-lla-reset-msg" style="margin-left:8px;font-size:13px"></span>
	</p>
	<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e( 'Locked yourself out? Log in from a different network (e.g. your phone on mobile data = a different IP), then click the button above to clear all lockouts — or just wait for the lockout time to pass. As a last resort, disable the plugin by renaming its folder via FTP.', 'horse-tools' ); ?></p>
	<script>
	(function(){
		var b=document.getElementById('ht-lla-reset'); if(!b||b.dataset.ready){return;} b.dataset.ready='1';
		var msg=document.getElementById('ht-lla-reset-msg');
		b.addEventListener('click', function(){
			b.disabled=true;
			var body='action=horsetools_lla_reset&nonce='+encodeURIComponent(b.dataset.nonce);
			fetch(b.dataset.ajax,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body})
				.then(function(r){return r.json();})
				.then(function(res){ b.disabled=false; if(msg){ msg.textContent = res&&res.success ? <?php echo wp_json_encode( __( 'Cleared all lockouts.', 'horse-tools' ) ); ?> : <?php echo wp_json_encode( __( 'Something went wrong.', 'horse-tools' ) ); ?>; } })
				.catch(function(){ b.disabled=false; if(msg){ msg.textContent=<?php echo wp_json_encode( __( 'Something went wrong.', 'horse-tools' ) ); ?>; } });
		});
	})();
	</script>

  <h3><i class="ti ti-user-question"></i> <?php _e('Block user enumeration', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-enum1', __( 'Hide usernames from scanners', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Block user enumeration',
		'description' => __( 'Blocks ?author=N scans, removes the users REST endpoint for anonymous requests, strips the author from oEmbed, and makes login errors generic so they do not reveal whether the username or the password was wrong.', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-shield-half"></i> <?php _e('Security response headers', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-head1', __( 'Send security headers', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Security response headers',
		'description' => __( 'Add hardening headers to front-end responses. Each one below is optional.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-head-xfo', __( 'X-Frame-Options: SAMEORIGIN (block clickjacking)', 'horse-tools' ), array(
		'tab' => 'SECURITY', 'section' => 'Security response headers', 'parent' => 'scuri-head1',
	) ); ?>
	<?php horsetools_toggle( 'scuri-head-nosniff', __( 'X-Content-Type-Options: nosniff', 'horse-tools' ), array(
		'tab' => 'SECURITY', 'section' => 'Security response headers', 'parent' => 'scuri-head1',
	) ); ?>
	<?php horsetools_toggle( 'scuri-head-ref', __( 'Referrer-Policy: strict-origin-when-cross-origin', 'horse-tools' ), array(
		'tab' => 'SECURITY', 'section' => 'Security response headers', 'parent' => 'scuri-head1',
	) ); ?>
	<?php horsetools_toggle( 'scuri-head-perm', __( 'Permissions-Policy: block geolocation, mic and camera', 'horse-tools' ), array(
		'tab' => 'SECURITY', 'section' => 'Security response headers', 'parent' => 'scuri-head1',
	) ); ?>
	<?php horsetools_toggle( 'scuri-head-hsts', __( 'HSTS (force HTTPS for 180 days)', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Security response headers',
		'parent'  => 'scuri-head1',
		'warning' => __( 'Only enable once HTTPS works everywhere. Browsers will refuse plain HTTP to your site for six months, and it cannot be undone quickly.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_input( 'scuri-head-csp', __( 'Content-Security-Policy (advanced, leave blank if unsure)', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Security response headers',
		'parent'      => 'scuri-head1',
		'placeholder' => "default-src 'self'",
		'description' => __( 'A wrong CSP silently breaks scripts, styles and images. Test with browser dev tools before relying on it.', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-shield-lock"></i> <?php _e('Lock down the admin', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-fileedit1', __( 'Disable the theme & plugin file editor', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Lock down the admin',
		'description' => __( 'Removes the built-in code editor under Appearance and Plugins. If an attacker gets into wp-admin, they cannot use it to edit PHP files. You edit files over SFTP instead.', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-rosette-discount-check"></i> <?php _e('Disable unused endpoints', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-off1', __( 'Disable REST API for anonymous visitors', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Disable unused endpoints',
		'warning' => __( 'This blocks the REST API for logged-out visitors. It WILL break: WooCommerce cart and checkout for guests, Contact Form 7 and other REST-based forms, comment submission on block themes, and oEmbed. Only enable it if your site uses none of these.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-off2', __( 'Disable XML RPC', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Disable unused endpoints',
		'description' => __( 'Recommended. xmlrpc.php is a common brute-force and pingback-amplification target and almost nothing uses it now (except Jetpack).', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-off3', __( 'Disable Wp-Embed', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Disable unused endpoints',
		'description' => __( 'Removes wp-embed.js if you do not embed other WordPress posts.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-off4', __( 'Disable X-Pingback', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Disable unused endpoints',
		'description' => __( 'Removes the X-Pingback header. Pairs with disabling XML-RPC.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-off6', __( 'Disable feeds (RSS/Atom)', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Disable unused endpoints',
		'description' => __( 'Turns off the RSS and Atom feeds if your site does not publish one.', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-wash"></i> <?php _e('Tidy up', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-off5', __( 'Remove unnecessary header tags', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Tidy up',
		'description' => __( 'Removes the RSD, WLW manifest and adjacent-post link tags from the page head.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_toggle( 'scuri-verof2', __( 'Remove the WordPress version tag', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Tidy up',
		'description' => __( 'Removes the generator meta tag. A small tidy-up — not a security measure on its own, since asset fingerprints reveal the version anyway.', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-shield-lock"></i> <?php _e('Privacy — Google Fonts & external requests', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'scuri-gfont1', __( 'Self-host Google Fonts', 'horse-tools' ), array(
		'tab'         => 'SECURITY',
		'section'     => 'Privacy',
		'description' => __( 'When your theme or a plugin loads fonts from Google, every visitor\'s IP is sent to Google — which GDPR treats as a data transfer. This serves downloaded copies from your own domain instead. Scan first, then click self-host; this works for fonts loaded the standard way (if a font still loads from Google afterwards, your theme hard-codes it — the scan will show you).', 'horse-tools' ),
	) ); ?>
	<div class="ht-privacy" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_privacy' ) ); ?>" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
		<p class="ht-priv-actions">
			<button type="button" class="ht-priv-btn" id="ht-priv-scan"><i class="ti ti-radar-2"></i> <?php _e( 'Scan external requests', 'horse-tools' ); ?></button>
			<button type="button" class="ht-priv-btn" id="ht-priv-host"><i class="ti ti-download"></i> <?php _e( 'Download & self-host Google Fonts', 'horse-tools' ); ?></button>
		</p>
		<div id="ht-priv-out" aria-live="polite"></div>
	</div>
	<style>
	.ht-priv-actions{display:flex;flex-wrap:wrap;gap:10px;margin:6px 0 4px}
	.ht-priv-btn{display:inline-flex;align-items:center;gap:6px;background:#fff;border:1px solid #e0a800;color:#8a5a00;padding:8px 14px;border-radius:8px;cursor:pointer;font-size:13px;transition:background .12s}
	.ht-priv-btn:hover{background:#fff9e6}
	.ht-priv-btn[disabled]{opacity:.5;cursor:default}
	#ht-priv-out{margin-top:8px}
	.ht-priv-table{border-collapse:collapse;width:100%;max-width:640px;font-size:13px;margin-top:6px}
	.ht-priv-table th,.ht-priv-table td{border:1px solid #ececec;padding:6px 9px;text-align:left}
	.ht-priv-table th{background:#fafafa;font-weight:600}
	.ht-priv-flag{color:#c0392b;font-weight:600}
	.ht-priv-flag i{color:#c0392b}
	.ht-priv-ok{color:#2e9e5b;font-weight:600}
	.ht-priv-msg{padding:8px 12px;border-radius:8px;background:#f4f6f8;margin-top:6px;font-size:13px}
	.ht-priv-msg.err{background:#fdecea;color:#8a1c12}
	.ht-priv-msg.good{background:#eafaf0;color:#1e6b3f}
	</style>
	<script>
	(function(){
		var wrap = document.querySelector('.ht-privacy');
		if (!wrap || wrap.dataset.ready) { return; }
		wrap.dataset.ready = '1';
		var out   = document.getElementById('ht-priv-out');
		var bScan = document.getElementById('ht-priv-scan');
		var bHost = document.getElementById('ht-priv-host');
		var AJAX  = wrap.dataset.ajax, NONCE = wrap.dataset.nonce;
		var I18N = {
			scanning: <?php echo wp_json_encode( __( 'Scanning your home page…', 'horse-tools' ) ); ?>,
			hosting:  <?php echo wp_json_encode( __( 'Downloading fonts…', 'horse-tools' ) ); ?>,
			none:     <?php echo wp_json_encode( __( 'No third-party requests found. Nothing leaks to other servers.', 'horse-tools' ) ); ?>,
			found:    <?php echo wp_json_encode( __( 'third-party host(s) found', 'horse-tools' ) ); ?>,
			gfound:   <?php echo wp_json_encode( __( 'Google Fonts detected — click “Download & self-host” to serve them locally.', 'horse-tools' ) ); ?>,
			host:     <?php echo wp_json_encode( __( 'Host', 'horse-tools' ) ); ?>,
			type:     <?php echo wp_json_encode( __( 'Type', 'horse-tools' ) ); ?>,
			hits:     <?php echo wp_json_encode( __( 'Requests', 'horse-tools' ) ); ?>,
			hosted:   <?php echo wp_json_encode( __( 'Self-hosted %1$d font file(s) across %2$d stylesheet(s). Turn on “Self-host Google Fonts” above and save.', 'horse-tools' ) ); ?>,
			fail:     <?php echo wp_json_encode( __( 'Something went wrong.', 'horse-tools' ) ); ?>
		};

		function esc(s){ return String(s).replace(/[&<>"]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];}); }
		function msg(text, cls){ out.innerHTML = '<div class="ht-priv-msg '+(cls||'')+'">'+esc(text)+'</div>'; }

		function post(action, done){
			var body = 'action='+encodeURIComponent(action)+'&nonce='+encodeURIComponent(NONCE);
			fetch(AJAX, {method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:body})
				.then(function(r){ return r.json(); })
				.then(done)
				.catch(function(){ msg(I18N.fail,'err'); bScan.disabled=false; bHost.disabled=false; });
		}

		bScan.addEventListener('click', function(){
			bScan.disabled = true; msg(I18N.scanning);
			post('horsetools_privacy_scan', function(res){
				bScan.disabled = false;
				if (!res || !res.success) { msg((res&&res.data&&res.data.msg)||I18N.fail,'err'); return; }
				var d = res.data, rows = d.rows||[];
				if (!rows.length) { msg(I18N.none,'good'); return; }
				var html = '<div class="ht-priv-msg">'+d.total+' '+esc(I18N.found)+'.</div>';
				if (d.gfonts>0) { html += '<div class="ht-priv-msg err">'+esc(I18N.gfound)+'</div>'; }
				html += '<table class="ht-priv-table"><thead><tr><th>'+esc(I18N.host)+'</th><th>'+esc(I18N.type)+'</th><th>'+esc(I18N.hits)+'</th></tr></thead><tbody>';
				rows.forEach(function(r){
					var t = r.flag ? '<span class="ht-priv-flag"><i class="ti ti-alert-triangle"></i> '+esc(r.type)+'</span>' : esc(r.type);
					html += '<tr><td>'+esc(r.host)+'</td><td>'+t+'</td><td>'+r.count+'</td></tr>';
				});
				html += '</tbody></table>';
				out.innerHTML = html;
			});
		});

		bHost.addEventListener('click', function(){
			bHost.disabled = true; msg(I18N.hosting);
			post('horsetools_gfonts_localise', function(res){
				bHost.disabled = false;
				if (!res || !res.success) { msg((res&&res.data&&res.data.msg)||I18N.fail,'err'); return; }
				var d = res.data;
				var t = I18N.hosted.replace('%1$d', d.fonts).replace('%2$d', d.families);
				var html = '<div class="ht-priv-msg good">'+esc(t)+'</div>';
				if (d.errors && d.errors.length) { html += '<div class="ht-priv-msg err">'+esc(d.errors.join(' | '))+'</div>'; }
				out.innerHTML = html;
			});
		});
	})();
	</script>
</div>
