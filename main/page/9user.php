<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check9" data-target="play9" type="checkbox" name="horsetools_settings[user]" value="1" <?php if ( isset($horsetools_options['user']) && 1 == $horsetools_options['user'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play9" class="ht-card toggle-div">
  <h3><i class="ti ti-lock"></i> <?php _e('Set access and viewing permissions', 'horse-tools') ?></h3>
	<!-- set quyen truy cap 1 -->
	<?php horsetools_toggle( 'user-post1', __( 'Filter posts and images', 'horse-tools' ), array(
		'tab'         => 'USER',
		'section'     => 'Set access and viewing permissions',
		'description' => __( 'With this feature, regular users can only view their own posts and images they uploaded, while the admin can view all of them', 'horse-tools' ),
	) ); ?>

	<!-- set quyen truy cap 2 -->
	<?php horsetools_toggle( 'user-wp1', __( 'Only admin has access to the admin page', 'horse-tools' ), array(
		'tab'         => 'USER',
		'section'     => 'Set access and viewing permissions',
		'description' => __( 'With this feature, regular users cannot access the WordPress admin page', 'horse-tools' ),
	) ); ?>

	<!-- set quyen truy cap 3 -->
	<?php horsetools_toggle( 'user-id1', __( 'Display ID in the management page', 'horse-tools' ), array(
		'tab'         => 'USER',
		'section'     => 'Set access and viewing permissions',
		'description' => __( 'Allow displaying member IDs on the profile management page', 'horse-tools' ),
	) ); ?>
  
  <h3><i class="ti ti-list-details"></i> <?php _e('Option to display the Admin bar', 'horse-tools') ?></h3>				  
	<!-- admin bar -->
	<?php horsetools_toggle( 'user-bar1', __( 'Disable the Admin Bar', 'horse-tools' ), array(
		'tab'     => 'USER',
		'section' => 'Option to display the Admin bar',
	) ); ?>

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
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('If you find the Admin Bar distracting every time you view the website, you can turn it off (there is an option for you to turn off all or only turn off for users)', 'horse-tools'); ?></p>
	
  <h3><i class="ti ti-user"></i> <?php _e('Add avatar upload functionality', 'horse-tools') ?></h3>
	<!-- set quyen truy cap 1 -->
	<?php horsetools_toggle( 'user-upav1', __( 'Allow avatar upload', 'horse-tools' ), array(
		'tab'         => 'USER',
		'section'     => 'Add avatar upload functionality',
		'description' => __( 'With this feature, there will be an additional button in the profile section allowing users to upload avatars', 'horse-tools' ),
	) ); ?>
</div>	