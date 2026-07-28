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
  <h3><i class="fa-regular fa-pen-to-square"></i> <?php _e('Text editor tool', 'horse-tools') ?></h3>
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
  <h3><i class="fa-regular fa-box"></i> <?php _e('Optimize Widgets', 'horse-tools') ?></h3>
	<!-- tool class 1 -->
	<?php horsetools_toggle( 'tool-widget1', __( 'Enable Classic Widget', 'horse-tools' ), array(
		'tab'         => 'TOOL',
		'section'     => 'Optimize Widgets',
		'description' => __( 'If you find the new Widget Manager too difficult to use, then revert it to the Classic Widget version', 'horse-tools' ),
	) ); ?>

  <h3><i class="fa-regular fa-gear"></i> <?php _e('Disable automatic updates', 'horse-tools') ?></h3>
	<!-- tool off upload 1 -->
	<?php horsetools_toggle( 'tool-upload1', __( 'Disable core updates', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Disable automatic updates',
	) ); ?>
	<!-- tool off upload 2 -->
	<?php horsetools_toggle( 'tool-upload2', __( 'Disable language pack updates', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Disable automatic updates',
	) ); ?>
	<!-- tool off upload 3 -->
	<?php horsetools_toggle( 'tool-upload3', __( 'Disable theme updates', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Disable automatic updates',
	) ); ?>
	<!-- tool off upload 4 -->
	<?php horsetools_toggle( 'tool-upload4', __( 'Disable plugin updates', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Disable automatic updates',
	) ); ?>
	<!-- tool off upload 5 -->
	<?php horsetools_toggle( 'tool-upload5', __( 'Disable update & maintenance notifications', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Disable automatic updates',
	) ); ?>
	<!-- tool off upload 6 -->
	<?php horsetools_toggle( 'tool-upload6', __( 'Disable automatic update checking (core, language packs, themes, plugins)', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Disable automatic updates',
	) ); ?>

	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('You can disable the automatic update feature of WordPress', 'horse-tools'); ?></p>

  <h3><i class="fa-regular fa-gear"></i> <?php _e('Management tool', 'horse-tools') ?></h3>
	<!-- tool manager 2 -->
	<?php horsetools_toggle( 'tool-mana2', __( 'Disallow text copying and access to DevTools', 'horse-tools' ), array(
		'tab'         => 'TOOL',
		'section'     => 'Management tool',
		'description' => __( 'This function prevents users from copying text, accessing right-click options, and accessing DevTools', 'horse-tools' ),
	) ); ?>

	<?php horsetools_toggle( 'tool-mana21', __( 'Copy pre-set content', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Management tool',
	) ); ?>
	<?php horsetools_toggle( 'tool-mana23', __( 'Attach copyright content', 'horse-tools' ), array(
		'tab'     => 'TOOL',
		'section' => 'Management tool',
		'parent'  => 'tool-mana21',
	) ); ?>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Enter content here', 'horse-tools'); ?>" name="horsetools_settings[tool-mana22]" type="text" value="<?php if(!empty($horsetools_options['tool-mana22'])){echo sanitize_text_field($horsetools_options['tool-mana22']);} ?>"/>
	</p>
	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If users copy content on the page, instead of receiving the content, they will receive the content you have set', 'horse-tools'); ?></p>

	<!-- tool manager 2 -->
	<?php horsetools_toggle( 'tool-mana3', __( 'Enable Classic Editor in category description', 'horse-tools' ), array(
		'tab'         => 'TOOL',
		'section'     => 'Management tool',
		'description' => __( 'This feature allows you to add the Classic Editor to the category description box when editing posts or products', 'horse-tools' ),
	) ); ?>

  <h3><i class="fa-regular fa-eye-slash"></i> <?php _e('Hide the tools you want', 'horse-tools') ?></h3>
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
	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you find the tools above unnecessary, you can hide them to make the WP admin interface cleaner. This function only hides them without blocking access to their links', 'horse-tools'); ?></p>
</div>
