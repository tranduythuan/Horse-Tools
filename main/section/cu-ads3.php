<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_ads_options; ?>
			<h2><?php _e('ADS.TXT', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="ti ti-file-check"></i> <?php _e('Set up the ads.txt file', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'ads-adstxt1', __( 'Enable ads.txt', 'horse-tools' ), array(
					'module'  => 'ads',
					'tab'     => 'ADS.TXT',
					'section' => 'Set up the ads.txt file',
				) ); ?>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('You can preview your file here:', 'horse-tools'); echo ' <a target="_blank" href="' . esc_url(home_url( '/ads.txt' )) . '">ads.txt</a>'; ?><br>
				<?php _e('For an Nginx server, if the ads.txt file already exists in the root directory of the website, it will prioritize the static file, so this function will not work. If you want to use it, you can either configure Nginx or delete the static file before proceeding', 'horse-tools'); ?>
				</p>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_ads_settings[ads-adstxt2]" placeholder="<?php _e('Enter code here', 'horse-tools'); ?>"><?php if(!empty($horsetools_ads_options['ads-adstxt2'])){echo esc_textarea($horsetools_ads_options['ads-adstxt2']);} ?></textarea>
				</p>
			</div>
