<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_notify_options; ?>
			<h2><?php _e('COOKIE', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-cookie"></i> <?php _e('Set up cookie notifications', 'horse-tools') ?></h3>
				<div class="ht-howto"><i class="ti ti-info-circle"></i><span><?php _e( 'Shows a small cookie notice in the corner so your site meets privacy rules. To use: turn on the switch, type the notice and your policy-page link, then choose which side it appears on.', 'horse-tools' ); ?></span></div>
				<?php horsetools_toggle( 'notify-cookie1', __( 'Enable cookie', 'horse-tools' ), array(
					'module'  => 'notify',
					'tab'     => 'COOKIE',
					'section' => 'Set up cookie notifications',
				) ); ?>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_notify_settings[notify-cookie-c1]" type="text" data-coloris value="<?php if(!empty($horsetools_notify_options['notify-cookie-c1'])){echo sanitize_text_field($horsetools_notify_options['notify-cookie-c1']);} ?>"/>
				<label class="ht-right-text"><?php _e('Select title color and button', 'horse-tools'); ?></label>
				</p>
				<p>
				<?php $styles = array('Left', 'Right', 'Bar'); ?>
				<select name="horsetools_notify_settings[notify-cookie-c2]">
				<?php foreach($styles as $style) { ?> 
				<?php if(isset($horsetools_notify_options['notify-cookie-c2']) && $horsetools_notify_options['notify-cookie-c2'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
				<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
				<?php } ?> 
				</select>
				<label class="ht-right-text"><?php _e('Location', 'horse-tools'); ?></label>
				</p>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter the policy page link', 'horse-tools') ?>" name="horsetools_notify_settings[notify-cookie-l1]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-cookie-l1'])){echo sanitize_text_field($horsetools_notify_options['notify-cookie-l1']);} ?>"/>
				</p>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter cookie title', 'horse-tools') ?>" name="horsetools_notify_settings[notify-cookie11]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-cookie11'])){echo sanitize_text_field($horsetools_notify_options['notify-cookie11']);} ?>"/>
				</p>
				<p>
				<textarea style="height:150px;" class="ht-code-textarea" name="horsetools_notify_settings[notify-cookie12]" placeholder="<?php _e('Enter cookie content', 'horse-tools'); ?>"><?php if(!empty($horsetools_notify_options['notify-cookie12'])){echo esc_textarea($horsetools_notify_options['notify-cookie12']);} ?></textarea>
				</p>
				<h3><i class="ti ti-forms"></i> <?php _e('Buttons', 'horse-tools') ?></h3>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Accept button text (default: Agree)', 'horse-tools') ?>" name="horsetools_notify_settings[notify-cookie13]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-cookie13'])){echo sanitize_text_field($horsetools_notify_options['notify-cookie13']);} ?>"/>
				</p>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Policy link text (default: Policy)', 'horse-tools') ?>" name="horsetools_notify_settings[notify-cookie14]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-cookie14'])){echo sanitize_text_field($horsetools_notify_options['notify-cookie14']);} ?>"/>
				</p>
				<?php horsetools_toggle( 'notify-cookie2', __( 'Show a “Decline” button', 'horse-tools' ), array(
					'module'  => 'notify',
					'tab'     => 'COOKIE',
					'section' => 'Set up cookie notifications',
					'parent'  => 'notify-cookie1',
				) ); ?>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Decline button text (default: Decline)', 'horse-tools') ?>" name="horsetools_notify_settings[notify-cookie15]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-cookie15'])){echo sanitize_text_field($horsetools_notify_options['notify-cookie15']);} ?>"/>
				</p>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Choose the position (corner box or full-width bar) and customise the message and button labels. The Accept/Decline choice is stored in the browser (localStorage + an “ht_cookie_consent” cookie), so the notice won’t nag returning visitors. Note: this is an informational notice — Horse Tools does not itself block third-party tracking scripts, so for strict consent-gating you would still gate your own scripts on that cookie.', 'horse-tools'); ?></p>
			</div>
