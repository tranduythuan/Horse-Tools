<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('DISPLAY', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check4" data-target="play4" type="checkbox" name="horsetools_settings[main]" value="1" <?php if ( isset($horsetools_options['main']) && 1 == $horsetools_options['main'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play4" class="ht-card toggle-div">
  <h3><i class="ti ti-icons"></i> <?php _e('Add font Awesome to the website', 'horse-tools') ?></h3>
	<!-- main add font icon 1 -->
	<?php horsetools_toggle( 'main-add1', __( 'Enable font Awesome', 'horse-tools' ), array(
		'tab'     => 'DISPLAY',
		'section' => 'Add font Awesome to the website',
	) ); ?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('If you want to use font Awesome, you can enable it (it an icon font). You can search for icons at:', 'horse-tools'); ?><br>
	<b><a target="_blank" href="https://fontawesome.com/search"><?php _e('Access Font Awesome to find icons', 'horse-tools'); ?></a></b><br>
	<?php _e('Free to use the "solid" and "brands" styles', 'horse-tools'); ?>
	</p>
  <h3><i class="ti ti-snowflake"></i> <?php _e('Decorative effects for the website', 'horse-tools') ?></h3>
	<!-- add font Google 1 -->
	<?php $styles = array('None', 'Snow1', 'Snow2', 'Lunar1', 'Lunar2', 'Vietnam', 'Indonesia'); ?>
	<select name="horsetools_settings[main-hover1]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($horsetools_options['main-hover1']) && $horsetools_options['main-hover1'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ht-right-text"><?php _e('Effect', 'horse-tools'); ?></label>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Choose decorations for the website, such as Christmas or Lunar New Year (If any effects cause issues on your website, it may be due to javascript conflicts. You can switch to other effects to use)', 'horse-tools'); ?></p>
  <h3><i class="ti ti-contrast"></i> <?php _e('Dark mode', 'horse-tools') ?></h3>
	<!-- darkmode -->
	<?php horsetools_toggle( 'main-mode1', __( 'Enable dark mode', 'horse-tools' ), array(
		'tab'     => 'DISPLAY',
		'section' => 'Dark mode',
	) ); ?>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[main-mode-c1]" type="text" data-coloris value="<?php if(!empty($horsetools_options['main-mode-c1'])){echo sanitize_text_field($horsetools_options['main-mode-c1']);} ?>"/>
	<label class="ht-right-text"><?php _e('Icon color', 'horse-tools'); ?></label>
	</p>
	<p>
	<?php $styles = array('Default', 'Toggle', 'System', 'Time'); ?>
	<select name="horsetools_settings[main-mode10]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($horsetools_options['main-mode10']) && $horsetools_options['main-mode10'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ht-right-text"><?php _e('Display type', 'horse-tools'); ?></label>
	</p>
	<p>
	<?php $styles = array('Left', 'Right'); ?>
	<select name="horsetools_settings[main-mode11]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($horsetools_options['main-mode11']) && $horsetools_options['main-mode11'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ht-right-text"><?php _e('Location', 'horse-tools'); ?></label>
	</p>
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[main-mode12]" min="10" max="300" value="<?php if(!empty($horsetools_options['main-mode12'])){echo sanitize_text_field($horsetools_options['main-mode12']);} else { echo sanitize_text_field('30');} ?>" class="htslide" data-index="5">
	<span><?php _e('Spacing below', 'horse-tools'); ?> <span id="demo5"></span> PX</span>
	</p>
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[main-mode13]" min="10" max="100" value="<?php if(!empty($horsetools_options['main-mode13'])){echo sanitize_text_field($horsetools_options['main-mode13']);} else { echo sanitize_text_field('30');} ?>" class="htslide" data-index="6">
	<span><?php _e('Border distance', 'horse-tools'); ?> <span id="demo6"></span> PX</span>
	</p>
	<?php horsetools_toggle( 'main-mode-s1', __( 'Use shortcodes', 'horse-tools' ), array(
		'tab'     => 'DISPLAY',
		'section' => 'Dark mode',
		'parent'  => 'main-mode1',
	) ); ?>
	<input class="ht-input-big ht-view-in" type="text" value="[horsedark]"/>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('This function allows you to initiate a dark mode library, enabling the website to switch between light and dark modes', 'horse-tools'); ?></p>
   <h3><i class="ti ti-mouse"></i> <?php _e('Customize scroll bar', 'horse-tools') ?></h3>
	<!-- main add font icon 1 -->
	<?php horsetools_toggle( 'main-scroll1', __( 'Enable', 'horse-tools' ), array(
		'tab'     => 'DISPLAY',
		'section' => 'Customize scroll bar',
	) ); ?>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[main-scroll11]" type="text" data-coloris value="<?php if(!empty($horsetools_options['main-scroll11'])){echo sanitize_text_field($horsetools_options['main-scroll11']);} ?>"/>
	<label class="ht-right-text"><?php _e('Background color', 'horse-tools'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[main-scroll12]" type="text" data-coloris value="<?php if(!empty($horsetools_options['main-scroll12'])){echo sanitize_text_field($horsetools_options['main-scroll12']);} ?>"/>
	<label class="ht-right-text"><?php _e('Bar color', 'horse-tools'); ?></label>
	</p>
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[main-scroll13]" min="1" max="10" value="<?php if(!empty($horsetools_options['main-scroll13'])){echo sanitize_text_field($horsetools_options['main-scroll13']);} else { echo sanitize_text_field('1');} ?>" class="htslide" data-index="13">
	<span><?php _e('Border radius', 'horse-tools'); ?> <span id="demo13"></span> PX</span>
	</p>
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[main-scroll14]" min="7" max="20" value="<?php if(!empty($horsetools_options['main-scroll14'])){echo sanitize_text_field($horsetools_options['main-scroll14']);} else { echo sanitize_text_field('10');} ?>" class="htslide" data-index="14">
	<span><?php _e('Bar size', 'horse-tools'); ?> <span id="demo14"></span> PX</span>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('This function allows you to customize the color of the scrollbar to your liking', 'horse-tools'); ?></p>
</div>	