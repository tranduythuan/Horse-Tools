<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_ads_options; ?>
			<h2><?php _e('ADSENSE', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="ti ti-ad"></i> <?php _e('Set up Adsense ads', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'ads-sense1', __( 'Enable Adsense', 'horse-tools' ), array(
					'module'  => 'ads',
					'tab'     => 'ADSENSE',
					'section' => 'Set up Adsense ads',
				) ); ?>
				<p>
				<textarea style="height:90px;" class="ht-code-textarea" name="horsetools_ads_settings[ads-sense11]" placeholder="<?php _e('Enter Adsense code here', 'horse-tools'); ?>"><?php if(!empty($horsetools_ads_options['ads-sense11'])){echo esc_textarea($horsetools_ads_options['ads-sense11']);} ?></textarea>
				</p>
				<h4><?php _e('Option to display ads in custom posts', 'horse-tools') ?></h4>
				<?php 
				$args = array(
				'public'   => true,
				);
				$post_types = get_post_types($args, 'objects'); 
				foreach ($post_types as $post_type_object) {
					if ($post_type_object->name == 'attachment') {
						continue;
					}
					?>
					<label class="nut-switch">
						<input type="checkbox" name="horsetools_ads_settings[posttype][]" value="<?php echo $post_type_object->name; ?>" <?php if (isset($horsetools_ads_options['posttype']) && in_array($post_type_object->name, $horsetools_ads_options['posttype'])) echo 'checked="checked"'; ?> />
						<span class="slider"></span>
					</label>
					<label class="ht-label-right"><?php echo $post_type_object->labels->name; ?></label>
					</p>
					<?php
				}
				?>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Select the custom post for which you want to show ads', 'horse-tools'); ?></p>
				<h5><?php _e('Top and bottom positions', 'horse-tools') ?></h5>
				<p>
				<textarea style="height:90px;" class="ht-code-textarea" name="horsetools_ads_settings[ads-sense-p1]" placeholder="<?php _e('Add ads to the top of the article', 'horse-tools'); ?>"><?php if(!empty($horsetools_ads_options['ads-sense-p1'])){echo esc_textarea($horsetools_ads_options['ads-sense-p1']);} ?></textarea>
				</p>
				<p>
				<textarea style="height:90px;" class="ht-code-textarea" name="horsetools_ads_settings[ads-sense-p2]" placeholder="<?php _e('Add ads at the bottom of the article', 'horse-tools'); ?>"><?php if(!empty($horsetools_ads_options['ads-sense-p2'])){echo esc_textarea($horsetools_ads_options['ads-sense-p2']);} ?></textarea>
				</p>
				<h5><?php _e('Custom position in posts', 'horse-tools') ?></h5>
				<p>
				<input class="ht-input-small" placeholder="<?php _e('Tag', 'horse-tools') ?>" name="horsetools_ads_settings[ads-sense-c1]" type="text" value="<?php if(!empty($horsetools_ads_options['ads-sense-c1'])){echo sanitize_text_field($horsetools_ads_options['ads-sense-c1']);} else {echo sanitize_text_field('p');} ?>"/>
				<input class="ht-input-small" placeholder="<?php _e('Quantity', 'horse-tools') ?>" name="horsetools_ads_settings[ads-sense-c2]" type="number" value="<?php if(!empty($horsetools_ads_options['ads-sense-c2'])){echo sanitize_text_field($horsetools_ads_options['ads-sense-c2']);} else {echo sanitize_text_field('10');} ?>"/>
				</p>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Enter the tag and tag number for the ad to appear', 'horse-tools'); ?></p>
				<p>
				<textarea style="height:90px;" class="ht-code-textarea" name="horsetools_ads_settings[ads-sense-c3]" placeholder="<?php _e('Add ads to custom post placement', 'horse-tools'); ?>"><?php if(!empty($horsetools_ads_options['ads-sense-c3'])){echo esc_textarea($horsetools_ads_options['ads-sense-c3']);} ?></textarea>
				</p>
			</div>
