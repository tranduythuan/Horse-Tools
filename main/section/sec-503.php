<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_redirects_options; ?>
			<div class="ht-howto"><i class="ti ti-info-circle"></i><span><?php _e( 'Temporarily close the site with a “under maintenance” notice while you make changes. You (and other logged-in admins) still see the site normally, so you can work in peace.', 'horse-tools' ); ?></span></div>
			<div class="ht-card">
			  <h3><i class="ti ti-bug"></i> <?php _e('Maintenance mode for developers (503)', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'redi3', __( 'Enable 503 maintenance mode', 'horse-tools' ), array(
					'module'      => 'redirect',
					'tab'         => '503',
					'section'     => 'Maintenance mode for developers (503)',
					'description' => __( 'All links on your website will redirect to the maintenance page, and only logged-in admin accounts can view the content', 'horse-tools' ),
				) ); ?>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter title', 'horse-tools') ?>" name="horsetools_redirects_settings[redi31]" type="text" value="<?php if(!empty($horsetools_redirects_options['redi31'])){echo sanitize_text_field($horsetools_redirects_options['redi31']);} ?>"/>
				</p>
				<p>
				<textarea style="height:150px;" class="ht-code-textarea" name="horsetools_redirects_settings[redi32]" placeholder="<?php _e('Enter content here', 'horse-tools'); ?>"><?php if(!empty($horsetools_redirects_options['redi32'])){echo esc_textarea($horsetools_redirects_options['redi32']);} ?></textarea>
				</p>
			</div>
