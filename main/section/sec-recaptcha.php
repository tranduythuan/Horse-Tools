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
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Retrieve the Site Key and Secret Key from your Google reCAPTCHA project and add them to the two fields above', 'horse-tools'); ?><br>
	<?php _e('The score threshold applies to reCAPTCHA v3 only. Google returns 1.0 for traffic it is confident is human and 0.0 for traffic it is confident is a bot; 0.5 is the recommended starting point. Raise it to block more aggressively, lower it if real visitors are being turned away.', 'horse-tools'); ?><br>
	<?php _e('If the Secret key is empty the check is skipped entirely rather than rejecting every login.', 'horse-tools'); ?><br>
	<a target="_blank" href="https://www.google.com/recaptcha">Google reCAPTCHA</a>
	</p>
