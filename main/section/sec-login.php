<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
  <h3><i class="ti ti-tools"></i> <?php _e('Change login link', 'horse-tools') ?></h3>
	<!-- thay doi link dăng nhap 1 -->
	<?php horsetools_toggle( 'custom-chan1', __( 'Enable for use', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Change login link',
	) ); ?>
	<p>
	<input class="ht-input-big" name="horsetools_settings[custom-chan11]" type="text" value="<?php if(!empty($horsetools_options['custom-chan11'])){echo sanitize_text_field($horsetools_options['custom-chan11']);} ?>" placeholder="<?php _e('Enter a suffix', 'horse-tools'); ?>" />
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Enable this feature and enter the login link suffix', 'horse-tools'); ?><br>
	<?php _e('Example: domain.com/wp-admin where wp-admin is the suffix', 'horse-tools'); ?><br>
	<b><?php if(!empty($horsetools_options['custom-chan11'])){ echo __('Login link:', 'horse-tools') .' '. home_url('/'. $horsetools_options['custom-chan11']);} ?></b>
	</p>

  <h3><i class="ti ti-palette"></i> <?php _e('Custom WordPress login page', 'horse-tools') ?></h3>
	<!-- set quyen truy cap 1 -->
	<?php horsetools_toggle( 'custom-ad1', __( 'Enable custom functionality', 'horse-tools' ), array(
		'tab'         => 'CUSTOM',
		'section'     => 'Custom WordPress login page',
		'description' => __( 'Enable this feature to be able to use the customizations below', 'horse-tools' ),
	) ); ?>
	
	<h4><?php _e('Select the displayed logo', 'horse-tools') ?></h4>
	<p style="display:flex;">
	<input id="ht-add2" class="ht-input-big" name="horsetools_settings[custom-logo1]" type="text" value="<?php if(!empty($horsetools_options['custom-logo1'])){echo sanitize_text_field($horsetools_options['custom-logo1']);} ?>" placeholder="<?php _e('Add the image logo link here', 'horse-tools'); ?>" />
	<button class="ht-selec" data-input-id="ht-add2"><?php _e('Select image', 'horse-tools'); ?></button>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('The standard size for the logo is 280x80 PX', 'horse-tools'); ?></p>
	
	<h4><?php _e('Customize background', 'horse-tools') ?></h4>
	
	<p>
	<?php $styles = array('None', 'Auto', 'Color', 'Upload'); ?>
	<select name="horsetools_settings[custom-bg1]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($horsetools_options['custom-bg1']) && $horsetools_options['custom-bg1'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ht-right-text"><?php _e('Background', 'horse-tools'); ?></label>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('None: default not selected, Auto: background image automatically changes each time the page is loaded, Color: change the color you want, Upload: upload the background image you want', 'horse-tools'); ?></p>
	
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[custom-bg11]" type="text" data-coloris value="<?php if(!empty($horsetools_options['custom-bg11'])){echo esc_attr($horsetools_options['custom-bg11']);} ?>"/>
	<label class="ht-right-text"><?php _e('Background color', 'horse-tools'); ?></label>
	</p>
	
	<p style="display:flex;">
	<input id="ht-add3" class="ht-input-big" name="horsetools_settings[custom-bg12]" type="text" value="<?php if(!empty($horsetools_options['custom-bg12'])){echo sanitize_text_field($horsetools_options['custom-bg12']);} ?>" placeholder="<?php _e('Add background image link', 'horse-tools'); ?>" />
	<button class="ht-selec" data-input-id="ht-add3"><?php _e('Select image', 'horse-tools'); ?></button>
	</p>
	
	<h4><?php _e('Customize elements on the page', 'horse-tools') ?></h4>
	
	<!-- tuy bien bang nhap -->
	<?php horsetools_toggle( 'custom-main1', __( 'Enable element customization', 'horse-tools' ), array(
		'tab'         => 'CUSTOM',
		'section'     => 'Custom WordPress login page',
		'description' => __( 'You can further customize your login page by enabling this feature', 'horse-tools' ),
	) ); ?>

	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[custom-main11]" type="text" data-coloris value="<?php if(!empty($horsetools_options['custom-main11'])){echo sanitize_text_field($horsetools_options['custom-main11']);} ?>"/>
	<label class="ht-right-text"><?php _e('Background color of the input form', 'horse-tools'); ?></label>
	</p>
	
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[custom-main12]" type="text" data-coloris value="<?php if(!empty($horsetools_options['custom-main12'])){echo sanitize_text_field($horsetools_options['custom-main12']);} ?>"/>
	<label class="ht-right-text"><?php _e('Input / select box color', 'horse-tools'); ?></label>
	</p>
	
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[custom-main13]" type="text" data-coloris value="<?php if(!empty($horsetools_options['custom-main13'])){echo sanitize_text_field($horsetools_options['custom-main13']);} ?>"/>
	<label class="ht-right-text"><?php _e('Text color of the input / select box', 'horse-tools'); ?></label>
	</p>
	
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[custom-main14]" type="text" data-coloris value="<?php if(!empty($horsetools_options['custom-main14'])){echo sanitize_text_field($horsetools_options['custom-main14']);} ?>"/>
	<label class="ht-right-text"><?php _e('Button color', 'horse-tools'); ?></label>
	</p>
	
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[custom-main15]" type="text" data-coloris value="<?php if(!empty($horsetools_options['custom-main15'])){echo sanitize_text_field($horsetools_options['custom-main15']);} ?>"/>
	<label class="ht-right-text"><?php _e('Button text color', 'horse-tools'); ?></label>
	</p>
	
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[custom-main16]" type="text" data-coloris value="<?php if(!empty($horsetools_options['custom-main16'])){echo sanitize_text_field($horsetools_options['custom-main16']);} ?>"/>
	<label class="ht-right-text"><?php _e('Display text color', 'horse-tools'); ?></label>
	</p>
	
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[custom-main17]" type="text" data-coloris value="<?php if(!empty($horsetools_options['custom-main17'])){echo sanitize_text_field($horsetools_options['custom-main17']);} ?>"/>
	<label class="ht-right-text"><?php _e('Link display color', 'horse-tools'); ?></label>
	</p>
	
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[custom-main18]" min="1" max="20" value="<?php if(!empty($horsetools_options['custom-main18'])){echo sanitize_text_field($horsetools_options['custom-main18']);} else { echo sanitize_text_field('7');} ?>" class="htslide" data-index="9">
	<span><?php _e('Border radius', 'horse-tools'); ?> <span id="demo9"></span> PX</span>
	</p>
	
	<?php horsetools_toggle( 'custom-main19', __( 'Hide back link', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Custom WordPress login page',
		'parent'  => 'custom-main1',
	) ); ?>

	<?php horsetools_toggle( 'custom-main20', __( 'Hide language options', 'horse-tools' ), array(
		'tab'     => 'SECURITY',
		'section' => 'Custom WordPress login page',
		'parent'  => 'custom-main1',
	) ); ?>
 
