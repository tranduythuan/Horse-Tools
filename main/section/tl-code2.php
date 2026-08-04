<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_code_options; ?>
			<h2><?php _e('WP HEAD', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-code"></i> <?php _e('Add code to WP head', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code2]" placeholder="<?php _e('Enter code here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code2'])){echo esc_textarea($horsetools_code_options['code2']);} ?></textarea>
				</p>
			</div>
