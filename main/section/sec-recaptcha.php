<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
  <h3><i class="ti ti-login-2"></i> <?php _e('Block login spam with Google reCAPTCHA', 'horse-tools') ?></h3>
	<p>
	<?php $styles = array('None', 'V2', 'V3'); ?>
	<select name="horsetools_settings[goo-cap1]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($horsetools_options['goo-cap1']) && $horsetools_options['goo-cap1'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ht-right-text"><?php _e('Off / select', 'horse-tools'); ?></label>
	</p>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Site key', 'horse-tools'); ?>" name="horsetools_settings[goo-cap11]" type="text" value="<?php if(!empty($horsetools_options['goo-cap11'])){echo sanitize_text_field($horsetools_options['goo-cap11']);} ?>"/>
	</p>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Secret key', 'horse-tools'); ?>" name="horsetools_settings[goo-cap12]" type="text" value="<?php if(!empty($horsetools_options['goo-cap12'])){echo sanitize_text_field($horsetools_options['goo-cap12']);} ?>"/>
	</p>
	<?php horsetools_input( 'goo-cap13', __( 'v3 score threshold (0 – 1)', 'horse-tools' ), array(
		'tab'         => 'GOOGLE',
		'section'     => 'Block login spam with Google reCAPTCHA',
		'type'        => 'number',
		'class'       => 'ht-input-small',
		'placeholder' => '0.5',
		'min'         => '0',
		'max'         => '1',
		'step'        => '0.05',
	) ); ?>
	<p>
	<button type="button" class="button" id="ht-cap-check" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_cap_check' ) ); ?>">
		<?php esc_html_e( 'Test these keys against Google', 'horse-tools' ); ?>
	</button>
	<span id="ht-cap-out" style="margin-left:10px"></span>
	</p>
	<p class="ht-note"><i class="ti ti-alert-triangle"></i>
	<?php esc_html_e( 'A v2 key and a v3 key look identical and are not interchangeable. Put a v2 key in the box while the dropdown says V3 and Google refuses to load the widget, so the token is never produced and every login is rejected — which reaches whoever is trying to sign in as “wrong password”. Press the button above after changing a key; it asks Google directly.', 'horse-tools' ); ?>
	</p>

	<script>
	document.addEventListener('DOMContentLoaded', function () {
		var b = document.getElementById('ht-cap-check');
		var o = document.getElementById('ht-cap-out');
		if (!b || !o) { return; }
		b.addEventListener('click', function () {
			var f = function (n) { var e = document.querySelector('[name="horsetools_settings[' + n + ']"]'); return e ? e.value : ''; };
			b.disabled = true;
			o.textContent = <?php echo wp_json_encode( __( 'Asking Google…', 'horse-tools' ) ); ?>;
			o.style.color = '';
			// The values in the boxes, not the saved ones — so a key can be checked
			// before it is committed to a live login screen.
			fetch(ajaxurl, {
				method: 'POST', credentials: 'same-origin',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				body: 'action=horsetools_cap_check&nonce=' + encodeURIComponent(b.dataset.nonce)
					+ '&mode=' + encodeURIComponent(f('goo-cap1'))
					+ '&site=' + encodeURIComponent(f('goo-cap11'))
					+ '&secret=' + encodeURIComponent(f('goo-cap12'))
			})
			.then(function (r) { return r.json(); })
			.then(function (j) {
				o.textContent = (j && j.data && j.data.message) ? j.data.message : '';
				o.style.color = (j && j.success) ? '#2e9e5b' : '#c0392b';
				b.disabled = false;
			})
			.catch(function () {
				o.textContent = <?php echo wp_json_encode( __( 'The request itself failed.', 'horse-tools' ) ); ?>;
				o.style.color = '#c0392b';
				b.disabled = false;
			});
		});
	});
	</script>

	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Retrieve the Site Key and Secret Key from your Google reCAPTCHA project and add them to the two fields above', 'horse-tools'); ?><br>
	<?php _e('The score threshold applies to reCAPTCHA v3 only. Google returns 1.0 for traffic it is confident is human and 0.0 for traffic it is confident is a bot; 0.5 is the recommended starting point. Raise it to block more aggressively, lower it if real visitors are being turned away.', 'horse-tools'); ?><br>
	<?php _e('If the Secret key is empty the check is skipped entirely rather than rejecting every login.', 'horse-tools'); ?><br>
	<a target="_blank" href="https://www.google.com/recaptcha">Google reCAPTCHA</a>
	</p>
