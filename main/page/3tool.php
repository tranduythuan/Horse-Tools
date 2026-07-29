<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('TOOL', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check3" data-target="play3" type="checkbox" name="horsetools_settings[tool]" value="1" <?php if ( isset($horsetools_options['tool']) && 1 == $horsetools_options['tool'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play3" class="ht-card toggle-div">
  <h3><i class="ti ti-edit"></i> <?php _e('Text editor tool', 'horse-tools') ?></h3>
	<!-- tool class 1 -->
	<?php horsetools_toggle( 'tool-edit1', __( 'Enable Classic Editor', 'horse-tools' ), array(
		'tab'         => 'TOOL',
		'section'     => 'Text editor tool',
		'description' => __( 'If you find the new editor too difficult to use, then revert it to the Classic Editor version', 'horse-tools' ),
	) ); ?>
	<!-- tool class 11 -->
	<?php horsetools_toggle( 'tool-edit11', __( 'Enhance features for Classic Editor', 'horse-tools' ), array(
		'tab'         => 'TOOL',
		'section'     => 'Text editor tool',
		'description' => __( 'Enable this feature if you want to add additional functionalities to the Classic Editor to enhance professional editing', 'horse-tools' ),
	) ); ?>
	<!-- tool class 11 -->
	<?php horsetools_toggle( 'tool-edit12', __( 'Add Classic Editor button', 'horse-tools' ), array(
		'tab'         => 'TOOL',
		'section'     => 'Text editor tool',
		'description' => __( 'Enable this feature if you want to add the Classic Editor button in the post and page management interface. With this feature, you dont need to set the Classic Editor as default but can use it in parallel', 'horse-tools' ),
	) ); ?>
  <h3><i class="ti ti-box"></i> <?php _e('Optimize Widgets', 'horse-tools') ?></h3>
	<!-- tool class 1 -->
	<?php horsetools_toggle( 'tool-widget1', __( 'Enable Classic Widget', 'horse-tools' ), array(
		'tab'         => 'TOOL',
		'section'     => 'Optimize Widgets',
		'description' => __( 'If you find the new Widget Manager too difficult to use, then revert it to the Classic Widget version', 'horse-tools' ),
	) ); ?>


  <h3><i class="ti ti-settings"></i> <?php _e('Automatic updates', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'tool-upload1', __( 'Do not auto-install core updates', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Automatic updates',
	) ); ?>
	<?php horsetools_toggle( 'tool-upload2', __( 'Do not auto-install language pack updates', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Automatic updates',
	) ); ?>
	<?php horsetools_toggle( 'tool-upload3', __( 'Do not auto-install theme updates', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Automatic updates',
	) ); ?>
	<?php horsetools_toggle( 'tool-upload4', __( 'Do not auto-install plugin updates', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Automatic updates',
	) ); ?>
	<?php horsetools_toggle( 'tool-upload5', __( 'Hide the update & maintenance notice', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Automatic updates',
	) ); ?>

	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('These stop WordPress installing updates on its own — the site still checks for them, so the Dashboard and Plugins screens keep showing what is available and you apply updates when you choose. It never stops checking, so you are never left unaware of a security release.', 'horse-tools'); ?></p>

  <h3><i class="ti ti-settings"></i> <?php _e('Management tool', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'tool-mana23', __( 'Add an attribution line when visitors copy text', 'horse-tools' ), array(
		'tab'         => 'TOOL',
		'section'     => 'Management tool',
		'description' => __( 'When someone copies text from your site, a short line you set is added after it — their selection is kept, nothing is replaced. Useful for a "Source: yoursite.com" credit.', 'horse-tools' ),
	) ); ?>
	<?php horsetools_input( 'tool-mana22', __( 'Attribution line', 'horse-tools' ), array(
		'tab'         => 'TOOL',
		'section'     => 'Management tool',
		'placeholder' => 'Source: example.com',
		'parent'      => 'tool-mana23',
	) ); ?>

	<!-- tool manager 2 -->
	<?php horsetools_toggle( 'tool-mana3', __( 'Enable Classic Editor in category description', 'horse-tools' ), array(
		'tab'         => 'TOOL',
		'section'     => 'Management tool',
		'description' => __( 'This feature allows you to add the Classic Editor to the category description box when editing posts or products', 'horse-tools' ),
	) ); ?>

  <h3><i class="ti ti-eye-off"></i> <?php _e('Hide the tools you want', 'horse-tools') ?></h3>
	<?php global $menu;
	if (is_array($menu)) {
		foreach ($menu as $index => $item) {
			if(!empty($item[0])){ ?>
			<p>
			<label class="nut-switch">
			<input type="checkbox" name="horsetools_settings[tool-hiden<?php echo $index; ?>]" value="1" <?php if ( isset($horsetools_options['tool-hiden'. $index]) && 1 == $horsetools_options['tool-hiden'. $index] ) echo 'checked="checked"'; ?> />
			<span class="slider"></span></label>
			<label class="ht-label-right"><?php echo preg_replace('/\d/', '', $item[0]); ?></label>
			</p>
			<?php }
		}
	} ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('If you find the tools above unnecessary, you can hide them to make the WP admin interface cleaner. This function only hides them without blocking access to their links', 'horse-tools'); ?></p>
</div>
