<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('SETTING', 'horse-tools'); ?></h2>
  <h3><i class="fa-regular fa-bomb"></i> <?php _e('Advanced settings', 'horse-tools') ?></h3>
	<!-- horsetools 1 -->
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[horsetools1]" value="1" <?php if ( isset($horsetools_options['horsetools1']) && 1 == $horsetools_options['horsetools1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Hide Admin account from profile page', 'horse-tools'); ?></label>
	<p>
	<?php
	$horseadmins = horsetools_get_admin_users();
    echo '<select name="horsetools_settings[horsetools11]">';
	echo '<option value="">'. __('Do not select', 'horse-tools') .'</option>';
    foreach ($horseadmins as $admin) {
		$selected = isset($horsetools_options['horsetools11']) && $horsetools_options['horsetools11'] == $admin->ID ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($admin->ID) . '" '. $selected .'>' . esc_html($admin->display_name) . '</option>';
    }
    echo '</select>';
	?>
	<label class="ht-right-text"><?php _e('Select admin', 'horse-tools'); ?></label>
	</p>
	<p class="ht-note ht-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you want to hide a specific Administrator account, select the user', 'horse-tools'); ?></p>
	<!-- horsetools 1 -->
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[horsetools2]" value="1" <?php if ( isset($horsetools_options['horsetools2']) && 1 == $horsetools_options['horsetools2'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Limit Horse Tools display', 'horse-tools'); ?></label>
	<p>
	<?php
    echo '<select name="horsetools_settings[horsetools21]">';
	echo '<option value="">'. __('Do not select', 'horse-tools') .'</option>';
    foreach ($horseadmins as $admin) {
		$selected = isset($horsetools_options['horsetools21']) && $horsetools_options['horsetools21'] == $admin->ID ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($admin->ID) . '" '. $selected .'>' . esc_html($admin->display_name) . '</option>';
    }
    echo '</select>';
	?>
	<label class="ht-right-text"><?php _e('Select admin', 'horse-tools'); ?></label>
	</p>
	<p class="ht-note ht-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This feature allows you to only display Horse Tools to a specific Admin account', 'horse-tools'); ?><br>
	<b><?php _e('Delete settings:', 'horse-tools'); ?>
	<a href="<?php echo esc_url( wp_nonce_url( admin_url('?del=adminhorsetools'), 'horsetools_del_hidden_admin' ) ); ?>"><?php _e('Reset this setting', 'horse-tools'); ?></a></b>
	</p>
  <h3><i class="fa-regular fa-eye-slash"></i> <?php _e('Hide Horse Tools', 'horse-tools') ?></h3>
	<!-- tool hiden 1 -->
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[horsetools3]" value="1" <?php if ( isset($horsetools_options['horsetools3']) && 1 == $horsetools_options['horsetools3'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Hide Horse Tools', 'horse-tools'); ?></label>
	</p>
	<p class="ht-note ht-note-red"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('You can hide Horse Tools from the WP menu, but you can still access it through the link. This will hide Horse Tools for all accounts', 'horse-tools'); ?><br>
	<b><?php echo admin_url('/admin.php?page=horsetools-options');?></b>
	</p>
  <h3><i class="fa-regular fa-eye-slash"></i> <?php _e('Hide plugins from the manager', 'horse-tools') ?></h3>	
	<!-- tool hiden 2 -->
	<?php
	$all_plugins = get_plugins(); 
	$p = 0;
	foreach ($all_plugins as $plugin_path => $plugin_info) {
		$p++;
		?>
		<p>
			<label class="nut-switch">
				<input type="checkbox" name="horsetools_settings[horsetools-pu<?php echo $p; ?>]" value="1" <?php if ( isset($horsetools_options['horsetools-pu'. $p]) && 1 == $horsetools_options['horsetools-pu'. $p] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span>
			</label>
			<label class="ht-label-right"><?php echo esc_html($plugin_info['Name']); ?></label>
		</p>
	<?php } ?>
	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This feature will hide the plugin you want from the plugin management page', 'horse-tools'); ?>
	</p>
  <h3><i class="fa-regular fa-language"></i> <?php _e('Display language settings', 'horse-tools') ?></h3>	
	<p>
	<?php $styles = array('Automatic', 'English', 'Việt Nam', 'Indonesia'); ?>
	<select name="horsetools_settings[lang]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($horsetools_options['lang']) && $horsetools_options['lang'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ht-right-text"><?php _e('Language', 'horse-tools'); ?></label>
	</p>
	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you dont want to automatically switch the language based on WordPress context, please select the language you prefer', 'horse-tools'); ?>
	</p>
  <h3><i class="fa-regular fa-palette"></i> <?php _e('Customize display', 'horse-tools') ?></h3>
	<div id="ht-imgstyle" class="ht-imgstyle">
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/style/1.jpg'); ?>" data-value="Default" class="<?php if(isset($horsetools_options['horsetools5']) && $horsetools_options['horsetools5'] == 'Default') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/style/2.jpg'); ?>" data-value="WordPress" class="<?php if(isset($horsetools_options['horsetools5']) && $horsetools_options['horsetools5'] == 'WordPress') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/style/3.jpg'); ?>" data-value="Bright" class="<?php if(isset($horsetools_options['horsetools5']) && $horsetools_options['horsetools5'] == 'Bright') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/style/4.jpg'); ?>" data-value="Girly" class="<?php if(isset($horsetools_options['horsetools5']) && $horsetools_options['horsetools5'] == 'Girly') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/style/5.jpg'); ?>" data-value="Black" class="<?php if(isset($horsetools_options['horsetools5']) && $horsetools_options['horsetools5'] == 'Black') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/style/6.jpg'); ?>" data-value="Coffe" class="<?php if(isset($horsetools_options['horsetools5']) && $horsetools_options['horsetools5'] == 'Coffe') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/style/7.jpg'); ?>" data-value="Rocket" class="<?php if(isset($horsetools_options['horsetools5']) && $horsetools_options['horsetools5'] == 'Rocket') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/style/8.jpg'); ?>" data-value="Blue" class="<?php if(isset($horsetools_options['horsetools5']) && $horsetools_options['horsetools5'] == 'Blue') echo 'selected'; ?>" />
	</div>
	<input type="hidden" name="horsetools_settings[horsetools5]" id="horsetools5" value="<?php if(!empty($horsetools_options['horsetools5'])){echo sanitize_text_field($horsetools_options['horsetools5']);} else {echo sanitize_text_field('Default');} ?>" />
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var imgStyles = document.querySelectorAll('#ht-imgstyle img');
			imgStyles.forEach(function(img) {
				img.addEventListener('click', function() {
					var selectedStyle = this.getAttribute('data-value');
					document.getElementById('horsetools5').value = selectedStyle;
					imgStyles.forEach(function(img) {
						img.classList.remove('selected');
					});
					this.classList.add('selected');
				});
			});
		});
		jQuery(document).ready(function($) {
			$('#ht-imgstyle img').click(function() { 
				var selectedStyle = $(this).attr('data-value');
				$('#horsetools5').val(selectedStyle);
				$('.ht-imgstyle img').removeClass('selected');
				$(this).addClass('selected');
				var currentForm = $(this).closest('form');
				$.ajax({
					type: 'POST',
					url: currentForm.attr('action'),
					data: currentForm.serialize(),
					success: function(response) {
						location.reload(); 
					},
				});
			});
		});
	</script>
	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Choose display interface according to your preference', 'horse-tools'); ?>
	</p>
	<p>
	<input class="ht-input-big" placeholder="<?php _e('Enter name', 'horse-tools'); ?>" name="horsetools_settings[horsetools6]" type="text" value="<?php if(!empty($horsetools_options['horsetools6'])){echo sanitize_text_field($horsetools_options['horsetools6']);} ?>"/>
	</p>
	<p>
	<?php $styles = array('icon 1', 'icon 2', 'icon 3', 'icon 4', 'icon 5', 'icon 6'); ?>
	<select name="horsetools_settings[horsetools61]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($horsetools_options['horsetools61']) && $horsetools_options['horsetools61'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ht-right-text"><?php _e('Icon', 'horse-tools'); ?></label>
	</p>
	<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Change display name and icon to your preference', 'horse-tools'); ?>
	</p>
	