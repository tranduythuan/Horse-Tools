<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('CUSTOM', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check10" data-target="play10" type="checkbox" name="horsetools_settings[custom]" value="1" <?php if ( isset($horsetools_options['custom']) && 1 == $horsetools_options['custom'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play10" class="ht-card toggle-div">
  <h3><i class="ti ti-brand-wordpress"></i> <?php _e('Change WordPress logo in the Admin bar', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'custom-logbar1', __( 'Disable logo display', 'horse-tools' ), array(
		'tab'     => 'CUSTOM',
		'section' => 'Change WordPress logo in the Admin bar',
	) ); ?>
	<?php horsetools_toggle( 'custom-logbar2', __( 'Enable logo customization', 'horse-tools' ), array(
		'tab'     => 'CUSTOM',
		'section' => 'Change WordPress logo in the Admin bar',
	) ); ?>
	<p style="display:flex;">
	<input id="ht-add4" class="ht-input-big" name="horsetools_settings[custom-logbar21]" type="text" value="<?php if(!empty($horsetools_options['custom-logbar21'])){echo sanitize_text_field($horsetools_options['custom-logbar21']);} ?>" placeholder="<?php _e('Add logo link', 'horse-tools'); ?>" />
	<button class="ht-selec" data-input-id="ht-add4"><?php _e('Select image', 'horse-tools'); ?></button>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('The changing image is square, with the standard size being 100x100 pixels', 'horse-tools'); ?></p>
	
  <h3><i class="ti ti-forms"></i> <?php _e('Modify the footer content of WP admin', 'horse-tools') ?></h3>
	<!-- tuy chinh chan trang -->
	<?php horsetools_toggle( 'custom-foo1', __( 'Enable custom footer', 'horse-tools' ), array(
		'tab'         => 'CUSTOM',
		'section'     => 'Modify the footer content of WP admin',
		'description' => __( 'Enable this feature if you want to customize the footer in the WP admin', 'horse-tools' ),
	) ); ?>
	<p>
	<textarea class="ht-textarea" name="horsetools_settings[custom-foo11]" placeholder="<?php _e('Please enter the content here', 'horse-tools'); ?>"><?php if(!empty($horsetools_options['custom-foo11'])){echo esc_textarea($horsetools_options['custom-foo11']);} ?></textarea>
	</p>
  
  <h3><i class="ti ti-app-window"></i> <?php _e('Customize dashboard widgets', 'horse-tools') ?></h3>
	
	<h4><?php _e('Disable unused widgets', 'horse-tools') ?></h4>
	<!-- tuy chinh bang tin -->
	<?php horsetools_toggle( 'custom-home1', __( 'Disable statistics widget', 'horse-tools' ), array(
		'tab'     => 'CUSTOM',
		'section' => 'Customize dashboard widgets',
	) ); ?>

	<?php horsetools_toggle( 'custom-home2', __( 'Disable WordPress info widget', 'horse-tools' ), array(
		'tab'     => 'CUSTOM',
		'section' => 'Customize dashboard widgets',
	) ); ?>

	<?php horsetools_toggle( 'custom-home3', __( 'Disable quick draft widget', 'horse-tools' ), array(
		'tab'     => 'CUSTOM',
		'section' => 'Customize dashboard widgets',
	) ); ?>

	<?php horsetools_toggle( 'custom-home4', __( 'Disable recent posts widget', 'horse-tools' ), array(
		'tab'     => 'CUSTOM',
		'section' => 'Customize dashboard widgets',
	) ); ?>

	<?php horsetools_toggle( 'custom-home5', __( 'Disable welcome widget', 'horse-tools' ), array(
		'tab'     => 'CUSTOM',
		'section' => 'Customize dashboard widgets',
	) ); ?>

	<?php horsetools_toggle( 'custom-home6', __( 'Disable health widget', 'horse-tools' ), array(
		'tab'     => 'CUSTOM',
		'section' => 'Customize dashboard widgets',
	) ); ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('You can disable default widgets on the dashboard that you dont use', 'horse-tools'); ?></p>
	
	<h4><?php _e('Your custom widget', 'horse-tools') ?></h4>
	<?php horsetools_toggle( 'custom-wid1', __( 'Enable custom widget', 'horse-tools' ), array(
		'tab'     => 'CUSTOM',
		'section' => 'Customize dashboard widgets',
	) ); ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('You can create your widget by activating it and entering content into the box below', 'horse-tools'); ?></p>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Widget title', 'horse-tools') ?>" name="horsetools_settings[custom-wid11]" type="text" value="<?php if(!empty($horsetools_options['custom-wid11'])){echo sanitize_text_field($horsetools_options['custom-wid11']);} ?>"/>
	</p>
	<div class="ht-classic">
	<?php
	$custom_wid12 = !empty($horsetools_options['custom-wid12']) ? wp_kses_post($horsetools_options['custom-wid12']) : '';
	ob_start();
	wp_editor(
		$custom_wid12,
		'userpostcontent',
		array(
			'textarea_name' => 'horsetools_settings[custom-wid12]',
			'media_buttons' => false,
		)
	);
	$horsetools_widget = ob_get_clean();
	echo $horsetools_widget;
	?>
	</div>
</div>	