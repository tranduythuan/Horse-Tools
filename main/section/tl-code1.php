<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_code_options; ?>
			<h2><?php _e('CSS', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-code"></i> <?php _e('Add CSS to your website', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code1]" placeholder="<?php _e('Enter CSS here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code1'])){echo esc_textarea($horsetools_code_options['code1']);} ?></textarea>
				</p>
			  <h3><i class="ti ti-code"></i> <?php _e('Add CSS for tablet size', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code11]" placeholder="<?php _e('Enter CSS here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code11'])){echo esc_textarea($horsetools_code_options['code11']);} ?></textarea>
				</p>
			  <h3><i class="ti ti-code"></i> <?php _e('Add CSS for mobile size', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code12]" placeholder="<?php _e('Enter CSS here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code12'])){echo esc_textarea($horsetools_code_options['code12']);} ?></textarea>
				</p>
			</div>	
