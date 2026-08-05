<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_shortcode_options; ?>
			<div class="ht-card">
			  <h3><i class="ti ti-download"></i> <?php _e('Download button GGET shortcode', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'shortcode-s4', __( 'Enable GGET shortcode', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'GGET',
					'section' => 'Download button GGET shortcode',
				) ); ?>
				<?php horsetools_toggle( 'shortcode-s4a', __( 'Display link when seconds expire', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'GGET',
					'section' => 'Download button GGET shortcode',
				) ); ?>
				<?php horsetools_toggle( 'shortcode-s4b', __( 'Center-align button on page', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'GGET',
					'section' => 'Download button GGET shortcode',
				) ); ?>
				<p>
				<input class="ht-input-small" placeholder="10" name="horsetools_shortcode_settings[shortcode-s41]" type="number" value="<?php if(!empty($horsetools_shortcode_options['shortcode-s41'])){echo $horsetools_shortcode_options['shortcode-s41'];} ?>"/>
				<label class="ht-label-right"><?php _e('Enter waiting time', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_shortcode_settings[shortcode-s42]" type="text" data-coloris value="<?php if(!empty($horsetools_shortcode_options['shortcode-s42'])){echo $horsetools_shortcode_options['shortcode-s42'];} ?>"/>
				<label class="ht-right-text"><?php _e('Select button color', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_shortcode_settings[shortcode-s43]" type="text" data-coloris value="<?php if(!empty($horsetools_shortcode_options['shortcode-s43'])){echo $horsetools_shortcode_options['shortcode-s43'];} ?>"/>
				<label class="ht-right-text"><?php _e('Select button border color', 'horse-tools'); ?></label>
				</p>
				<p class="ht-keo">
				<input type="range" name="horsetools_shortcode_settings[shortcode-s44]" min="1" max="7" value="<?php if(!empty($horsetools_shortcode_options['shortcode-s44'])){echo sanitize_text_field($horsetools_shortcode_options['shortcode-s44']);} else { echo '2';} ?>" class="htslide" data-index="7">
				<span><?php _e('Border size', 'horse-tools'); ?> <span id="demo7"></span> PX</span>
				</p>
				<p class="ht-keo">
				<input type="range" name="horsetools_shortcode_settings[shortcode-s45]" min="1" max="50" value="<?php if(!empty($horsetools_shortcode_options['shortcode-s45'])){echo sanitize_text_field($horsetools_shortcode_options['shortcode-s45']);} else { echo '10';} ?>" class="htslide" data-index="8">
				<span><?php _e('Border radius', 'horse-tools'); ?> <span id="demo8"></span> PX</span>
				</p>
				
				<p><input class='ht-input-big ht-view-in' type='text' value='[gget url="<?php _e('Download link', 'horse-tools'); ?>"/]'/></p>
				<p><input class='ht-input-big ht-view-in' type='text' value='[gget url="<?php _e('Download link', 'horse-tools'); ?>"] <?php _e('Button name', 'horse-tools'); ?> [/gget]' /></p>
				<p><input class='ht-input-big ht-view-in' type='text' value='[gget aff="<?php _e('Other links', 'horse-tools'); ?>" url="<?php _e('Download link', 'horse-tools'); ?>"] <?php _e('Button name', 'horse-tools'); ?> [/gget]' /></p>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('This shortcode is used to create a download button with a timeout', 'horse-tools'); ?></p>  
			</div>
