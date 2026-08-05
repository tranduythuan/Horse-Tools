<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_notify_options; ?>
			<div class="ht-card">
			   <h3><i class="ti ti-bell"></i> <?php _e('Notification at the top of the page', 'horse-tools') ?></h3>
				<div class="ht-howto"><i class="ti ti-info-circle"></i><span><?php _e( 'Shows a thin notice bar across the very top of every page — handy for a promotion, a hotline, or a delivery note. To use: turn on the switch, type your message, then choose the background colour.', 'horse-tools' ); ?></span></div>
				<?php horsetools_toggle( 'notify-notis1', __( 'Enable notification', 'horse-tools' ), array(
					'module'  => 'notify',
					'tab'     => 'NOTIFY',
					'section' => 'Notification at the top of the page',
				) ); ?>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_notify_settings[notify-notis-c1]" type="text" data-coloris value="<?php if(!empty($horsetools_notify_options['notify-notis-c1'])){echo sanitize_text_field($horsetools_notify_options['notify-notis-c1']);} ?>"/>
				<label class="ht-right-text"><?php _e('Select background color', 'horse-tools'); ?></label>
				</p>
				<p>
				<textarea style="height:150px;" class="ht-code-textarea" name="horsetools_notify_settings[notify-notis11]" placeholder="<?php _e('Enter content here', 'horse-tools'); ?>"><?php if(!empty($horsetools_notify_options['notify-notis11'])){echo esc_textarea($horsetools_notify_options['notify-notis11']);} ?></textarea>
				</p>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Enter the content you want to display in the notification, and customize the colors to match your preferences. A notification will appear at the top of your website, making it easy for users to see', 'horse-tools'); ?></p> 				
			</div>
