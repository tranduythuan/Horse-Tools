<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_notify_options; ?>
			<h2><?php _e('BLOCKER', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="ti ti-shield-half"></i> <?php _e('Browser ad-block notification', 'horse-tools') ?></h3>
				<div class="ht-howto"><i class="ti ti-info-circle"></i><span><?php _e( 'Spots visitors who have an ad-blocker turned on and shows them a message asking to switch it off. To use: turn on the switch, type a title and message, then pick the button colours below.', 'horse-tools' ); ?></span></div>
				<?php horsetools_toggle( 'notify-block1', __( 'Enable ad-block detection', 'horse-tools' ), array(
					'module'  => 'notify',
					'tab'     => 'BLOCKER',
					'section' => 'Browser ad-block notification',
				) ); ?>
				<?php horsetools_toggle( 'notify-block11', __( 'Only notify, do not block access', 'horse-tools' ), array(
					'module'  => 'notify',
					'tab'     => 'BLOCKER',
					'section' => 'Browser ad-block notification',
				) ); ?>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_notify_settings[notify-block-c1]" type="text" data-coloris value="<?php if(!empty($horsetools_notify_options['notify-block-c1'])){echo sanitize_text_field($horsetools_notify_options['notify-block-c1']);} ?>"/>
				<label class="ht-right-text"><?php _e('Select button color', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_notify_settings[notify-block-c2]" type="text" data-coloris value="<?php if(!empty($horsetools_notify_options['notify-block-c2'])){echo sanitize_text_field($horsetools_notify_options['notify-block-c2']);} ?>"/>
				<label class="ht-right-text"><?php _e('Select button border color', 'horse-tools'); ?></label>
				</p>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter title', 'horse-tools') ?>" name="horsetools_notify_settings[notify-block12]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-block12'])){echo sanitize_text_field($horsetools_notify_options['notify-block12']);} ?>"/>
				</p>
				<p>
				<textarea style="height:150px;" class="ht-code-textarea" name="horsetools_notify_settings[notify-block13]" placeholder="<?php _e('Enter content here', 'horse-tools'); ?>"><?php if(!empty($horsetools_notify_options['notify-block13'])){echo esc_textarea($horsetools_notify_options['notify-block13']);} ?></textarea>
				</p>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Enter the title and content you want to display when ad-blocker is detected', 'horse-tools'); ?></p>   
			</div>
