<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check7" data-target="play7" type="checkbox" name="horsetools_settings[mail]" value="1" <?php if ( isset($horsetools_options['mail']) && 1 == $horsetools_options['mail'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>

<div id="play7" class="ht-card-mail toggle-div">
	<a class="ht-smtp-a" onclick="htnone(event, 'ht-smtp')"><i class="ti ti-share"></i> <?php _e('Configure SMTP mail', 'horse-tools') ?></a>
	<div id="ht-smtp" style="display:none">
	<h3><i class="ti ti-mail"></i> <?php _e('Configure SMTP mail', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'mail-gsmtp1', __( 'Enable SMTP mail', 'horse-tools' ), array(
		'tab'     => 'MAIL',
		'section' => 'Configure SMTP mail',
	) ); ?>
	<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e('Enable SMTP mail to allow SMTP mail to function and send emails when you perform a test', 'horse-tools'); ?>
	</p>
	<div class="ht-card-mail-set">
	<a title="Gmail" id="mail1"><img src="<?php echo esc_url(HORSETOOLS_URL .'img/gmail.png'); ?>" /></a>
	<a title="Larksuite" id="mail2"><img src="<?php echo esc_url(HORSETOOLS_URL .'img/larksuite.png'); ?>" /></a>
	<a title="Yandex" id="mail3"><img src="<?php echo esc_url(HORSETOOLS_URL .'img/yandex.png'); ?>" /></a>
	</div>
	
	<p style="font-weight:bold"><?php _e('Sender name', 'horse-tools'); ?><p>
	<p><input class="ht-input-big"  placeholder="<?php _e('Sender name', 'horse-tools'); ?>" name="horsetools_settings[mail-gsmtp11]" type="text" value="<?php if(!empty($horsetools_options['mail-gsmtp11'])){echo sanitize_text_field($horsetools_options['mail-gsmtp11']);} ?>"/></p>
	
	<p style="font-weight:bold"><?php _e('Sender email address', 'horse-tools'); ?><p>
	<p><input class="ht-input-big"  placeholder="name@mail.com" name="horsetools_settings[mail-gsmtp12]" type="text" value="<?php if(!empty($horsetools_options['mail-gsmtp12'])){echo sanitize_text_field($horsetools_options['mail-gsmtp12']);} ?>"/></p>
	
	<p style="font-weight:bold"><?php _e('Account', 'horse-tools'); ?><p>
	<p><input class="ht-input-big"  placeholder="<?php _e('Account', 'horse-tools'); ?>" name="horsetools_settings[mail-gsmtp13]" type="text" value="<?php if(!empty($horsetools_options['mail-gsmtp13'])){echo sanitize_text_field($horsetools_options['mail-gsmtp13']);} ?>"/></p>
	
	<p style="font-weight:bold"><?php _e('App password', 'horse-tools'); ?><p>
	<p><input class="ht-input-big"  placeholder="<?php _e('App password', 'horse-tools'); ?>" name="horsetools_settings[mail-gsmtp14]" type="password" value="<?php if(!empty($horsetools_options['mail-gsmtp14'])){echo sanitize_text_field($horsetools_options['mail-gsmtp14']);} ?>"/></p>
	
	<p style="font-weight:bold"><?php _e('SMTP server', 'horse-tools'); ?><p>
	<p><input id="mail-sever" class="ht-input-big" name="horsetools_settings[mail-gsmtp15]" type="text" value="<?php if(!empty($horsetools_options['mail-gsmtp15'])){echo sanitize_text_field($horsetools_options['mail-gsmtp15']);} else {echo sanitize_text_field('smtp.gmail.com');} ?>"/></p>
	
	<p style="font-weight:bold"><?php _e('SMTP port', 'horse-tools'); ?><p>
	<p><input id="mail-host" class="ht-input-big" style="width:90px;" name="horsetools_settings[mail-gsmtp16]" type="number" value="<?php if(!empty($horsetools_options['mail-gsmtp16'])){echo sanitize_text_field($horsetools_options['mail-gsmtp16']);} else {echo sanitize_text_field('587');} ?>"/></p>
	
	<p style="font-weight:bold"><?php _e('SMTP protocol', 'horse-tools'); ?><p>
	<p>
	<?php
	// PHPMailer::$SMTPSecure accepts only '', 'tls' (STARTTLS) and 'ssl' (SMTPS).
	// The list used to start with 'starttls', which matches neither — PHPMailer
	// then opened a plaintext socket, never issued STARTTLS, and authentication
	// failed. Because it was the first entry it was also the pre-selected value,
	// so any admin who did not touch this dropdown saved a setting that silently
	// stopped all outgoing mail.
	$styles = array( 'tls' => 'STARTTLS (tls)', 'ssl' => 'SMTPS (ssl)', '' => __( 'None', 'horse-tools' ) );
	$current = isset($horsetools_options['mail-gsmtp17']) ? $horsetools_options['mail-gsmtp17'] : 'tls';
	if ( 'starttls' === $current ) { $current = 'tls'; }
	?>
	<select id="mail-check" style="width:150px;" name="horsetools_settings[mail-gsmtp17]">
	<?php foreach($styles as $value => $label) { ?>
	<option value="<?php echo esc_attr($value); ?>" <?php selected($current, $value); ?>><?php echo esc_html($label); ?></option>
	<?php } ?>
	</select>
	</p>
	<p style="font-weight:bold"><?php _e('Authenticate outgoing mail', 'horse-tools'); ?><p>
	<p>
	<label class="ht-container"><?php _e('SMTP authentication', 'horse-tools'); ?>
	<input id="mail-scuti" type="checkbox" name="horsetools_settings[mail-gsmtp18]" value="1" <?php if (isset($horsetools_options['mail-gsmtp18']) && 1 == $horsetools_options['mail-gsmtp18']) echo 'checked="checked"'; ?> />
	<span class="ht-checkmark"></span></label>
	</p>
	
	
	<h3><i class="ti ti-send"></i> <?php _e('Send test email', 'horse-tools') ?></h3>
	<div style="display:flex;">
	<input class="ht-input-big" id="ht-mailtest" placeholder="<?php _e('Enter email', 'horse-tools'); ?>" type="text" value=""/>
	<a id="ht-send-email" href="javascript:void(0)" data-nonce="<?php echo wp_create_nonce('ht-send-email-nonce'); ?>"><?php _e('Send', 'horse-tools'); ?></a>
	</div>
	<div id="ht-sen-end"></div>
	<div class="edoi" style="display:none"><div class="ht-sload"></div>  <?php _e('Please wait for the email to be sent', 'horse-tools'); ?></div>
	<script>
	// cấu hình email
	function updateFields(buttonId) {
			var serverInput = document.getElementById("mail-sever");
			var hostInput = document.getElementById("mail-host");
			var checkSelect = document.getElementById("mail-check");
			var scutiCheckbox = document.getElementById("mail-scuti");
			switch (buttonId) {
				case "mail1":
					serverInput.value = "smtp.gmail.com";
					hostInput.value = 587;
					checkSelect.value = "tls";
					scutiCheckbox.checked = true;
					break;
				case "mail2":
					serverInput.value = "smtp.larksuite.com";
					hostInput.value = 465;
					checkSelect.value = "ssl";
					scutiCheckbox.checked = true;
					break;
				case "mail3":
					serverInput.value = "smtp.yandex.com";
					hostInput.value = 465;
					checkSelect.value = "ssl";
					scutiCheckbox.checked = true;
					break;
			}
		}
		document.getElementById("mail1").addEventListener("click", function () {
			updateFields("mail1");
		});
		document.getElementById("mail2").addEventListener("click", function () {
			updateFields("mail2");
		});
		document.getElementById("mail3").addEventListener("click", function () {
			updateFields("mail3");
	});
	// Gửi email
	jQuery(document).ready(function($) {
		$('#ht-send-email').click(function(event) {
			event.preventDefault();
			$('.edoi').show();
			var nonce = $('#ht-send-email').data('nonce');
			var email = $('#ht-mailtest').val();
			$.ajax({
				url: '<?php echo admin_url('admin-ajax.php'); ?>',
				type: 'POST',
				data: {
					action: 'horsetools_send_email_ajax',
					nonce: nonce,
					email: email,
				},
				success: function(response) {
					$('#ht-sen-end').html('<span>'+ response + '</span>');
					$('.edoi').hide();
				}
			});
		});
	});
	</script>
	</div>
  
  <h3><i class="ti ti-send"></i> <?php _e('Create welcome email for registered users', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'mail-new1', __( 'Enable email sending', 'horse-tools' ), array(
		'tab'     => 'MAIL',
		'section' => 'Create welcome email for registered users',
	) ); ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('You need to activate this feature and customize the content below so that new registered users can receive emails', 'horse-tools'); ?></p>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Email subject', 'horse-tools') ?>" name="horsetools_settings[mail-new11]" type="text" value="<?php if(!empty($horsetools_options['mail-new11'])){echo sanitize_text_field($horsetools_options['mail-new11']);} ?>"/>
	</p>
	<p>
	<textarea style="height:150px;" class="ht-code-textarea" name="horsetools_settings[mail-new12]" placeholder="<?php _e('Enter email content here', 'horse-tools'); ?>"><?php if(!empty($horsetools_options['mail-new12'])){echo esc_textarea($horsetools_options['mail-new12']);} ?></textarea>
	</p>				  
	
  <h3><i class="ti ti-message-2"></i> <?php _e('Comment notification', 'horse-tools') ?></h3>
	<!-- mail comment 1 -->
	<?php horsetools_toggle( 'mail-com1', __( 'Email notification when there a reply', 'horse-tools' ), array(
		'tab'         => 'MAIL',
		'section'     => 'Comment notification',
		'description' => __( 'If someone replies to a comment, there will be an email notification sent to the commenter', 'horse-tools' ),
	) ); ?>
	<!-- mail comment 2 -->
	<?php horsetools_toggle( 'mail-com2', __( 'Email notification when a post has a comment', 'horse-tools' ), array(
		'tab'         => 'MAIL',
		'section'     => 'Comment notification',
		'description' => __( 'Notify the post author when someone comments on the post', 'horse-tools' ),
	) ); ?>
</div>	