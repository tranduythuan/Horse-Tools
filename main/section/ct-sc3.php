<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_shortcode_options; ?>
			<h2><?php _e('DATE', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-calendar"></i> <?php _e('Shortcode to display date', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'shortcode-s3', __( 'Enable date shortcode', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'DATE',
					'section' => 'Shortcode to display date',
				) ); ?>
				<p>
				<?php $styles = array('VI', 'EN'); ?>
				<select name="horsetools_shortcode_settings[shortcode-s31]"> 
				<?php foreach($styles as $style) { ?> 
				<?php if(isset($horsetools_shortcode_options['shortcode-s31']) && $horsetools_shortcode_options['shortcode-s31'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
				<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
				<?php } ?> 
				</select>
				<label class="ht-right-text"><?php _e('Display type', 'horse-tools'); ?></label>
				</p>
				<p><input class="ht-input-big ht-view-in" type="text" value="[titday]"/></p>
				<p><input class="ht-input-big ht-view-in" type="text" value="[titmonth]"/></p>
				<p><input class="ht-input-big ht-view-in" type="text" value="[tityear]"/></p>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('This shortcode is used to display the date in the post title. Please note that you need to enable the shortcode usage in the post title under the POST, PAGE section', 'horse-tools'); ?></p>   				
			</div>
