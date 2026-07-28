<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('USER', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check9" data-target="play9" type="checkbox" name="horsetools_settings[user]" value="1" <?php if ( isset($horsetools_options['user']) && 1 == $horsetools_options['user'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play9" class="ht-card toggle-div">
  <h3><i class="fa-regular fa-lock"></i> <?php _e('Set access and viewing permissions', 'horse-tools') ?></h3>
	<!-- set quyen truy cap 1 -->
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[user-post1]" value="1" <?php if ( isset($horsetools_options['user-post1']) && 1 == $horsetools_options['user-post1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Filter posts and images', 'horse-tools'); ?></label>
	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('With this feature, regular users can only view their own posts and images they uploaded, while the admin can view all of them', 'horse-tools'); ?></p>
	
	<!-- set quyen truy cap 2 -->
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[user-wp1]" value="1" <?php if (isset($horsetools_options['user-wp1']) && 1 == $horsetools_options['user-wp1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Only admin has access to the admin page', 'horse-tools'); ?></label>
	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('With this feature, regular users cannot access the WordPress admin page', 'horse-tools'); ?></p>
	
	<!-- set quyen truy cap 3 -->
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[user-id1]" value="1" <?php if (isset($horsetools_options['user-id1']) && 1 == $horsetools_options['user-id1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Display ID in the management page', 'horse-tools'); ?></label>
	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Allow displaying member IDs on the profile management page', 'horse-tools'); ?></p>
  
  <h3><i class="fa-regular fa-list-dropdown"></i> <?php _e('Option to display the Admin bar', 'horse-tools') ?></h3>				  
	<!-- admin bar -->
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[user-bar1]" value="1" <?php if ( isset($horsetools_options['user-bar1']) && 1 == $horsetools_options['user-bar1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Disable the Admin Bar', 'horse-tools'); ?></label>
	
	<p>
	<?php $styles = array('All', 'User'); ?>
	<select name="horsetools_settings[user-bar11]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($horsetools_options['user-bar11']) && $horsetools_options['user-bar11'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ht-right-text"><?php _e('Role', 'horse-tools'); ?></label>
	</p>
	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you find the Admin Bar distracting every time you view the website, you can turn it off (there is an option for you to turn off all or only turn off for users)', 'horse-tools'); ?></p>
	
  <h3><i class="fa-regular fa-user"></i> <?php _e('Add avatar upload functionality', 'horse-tools') ?></h3>
	<!-- set quyen truy cap 1 -->
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[user-upav1]" value="1" <?php if ( isset($horsetools_options['user-upav1']) && 1 == $horsetools_options['user-upav1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Allow avatar upload', 'horse-tools'); ?></label>
	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('With this feature, there will be an additional button in the profile section allowing users to upload avatars', 'horse-tools'); ?></p>
</div>	