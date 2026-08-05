<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_shortcode_options; ?>
			<h2><?php _e('SIGN', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-signature"></i> <?php _e('Signature shortcode', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'shortcode-s2', __( 'Enable signature shortcode', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'SIGN',
					'section' => 'Signature shortcode',
				) ); ?>
				<div class="ht-classic">
				<?php
				$shortcode_s21 = !empty($horsetools_shortcode_options['shortcode-s21']) ? wp_kses_post($horsetools_shortcode_options['shortcode-s21']) : '';
				ob_start();
				wp_editor(
					$shortcode_s21,
					'userpostcontent',
					array(
						'textarea_name' => 'horsetools_shortcode_settings[shortcode-s21]',
						'media_buttons' => false,
					)
				);
				$editor_contents = ob_get_clean();
				echo $editor_contents;
				?>
				</div>
				<p>
				<input class="ht-input-big ht-view-in" type="text" value="[sign]"/>
				</p>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('If you want to display your signature anywhere, you can create content above and then use the generated shortcode at your desired location', 'horse-tools'); ?></p>   				
			</div>
