<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_shortcode_options_page() {
	global $horsetools_shortcode_options;
	ob_start(); 
	?>
	<div class="wrap ht-wrap">
	<div class="ht-wrap-top">
	</div>
	<div class="ht-wrap2">
	  <div class="ht-box">
		<div class="ht-menu">
			<div class="ht-logo ht-logoquay">
			<a class="ht-logoquaya" href="https://tranduythuan.com/" target="_blank">
			<span><?php horsetools_logo(); ?></span>
			</a>
			</div>
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="fa-regular fa-lock"></i> <?php _e('LOCKVIP', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="fa-regular fa-signature"></i> <?php _e('SIGN', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab3')"><i class="fa-regular fa-calendar"></i> <?php _e('DATE', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab4')"><i class="fa-regular fa-download"></i> <?php _e('GGET', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_shortcode_settings_group'); ?> 
			<!-- LOCKVIP -->
			<div class="sotab-box htbox" id="tab1" >
			<h2><?php _e('LOCKVIP', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-lock"></i> <?php _e('Shortcode content visible only to group of users', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_shortcode_settings[shortcode-s1]" value="1" <?php if ( isset($horsetools_shortcode_options['shortcode-s1']) && 1 == $horsetools_shortcode_options['shortcode-s1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable shortcode lock', 'horse-tools'); ?></label>
				</p>
				<?php
				$roles = get_editable_roles();
				echo '<select name="horsetools_shortcode_settings[shortcode-s11]">';
				echo '<option value="">Default</option>';
				foreach ($roles as $role_name => $role_info) {
					if ( true ) { // all roles selectable; horsetools_user_meets_role() ranks them
					if(isset($horsetools_shortcode_options['shortcode-s11']) && $horsetools_shortcode_options['shortcode-s11'] == $role_name) { $selected = 'selected="selected"'; } else { $selected = NULL; }
					echo '<option value="'. $role_name .'" '. $selected .'>'. $role_name .'</option>';
					}
				}
				echo '</select>';
				?>
				<label class="ht-right-text"><?php _e('Role', 'horse-tools'); ?></label>
				<p>
				<textarea style="height:100px;" class="ht-code-textarea" name="horsetools_shortcode_settings[shortcode-s12]" placeholder="<?php _e('Enter note content', 'horse-tools'); ?>"><?php if(!empty($horsetools_shortcode_options['shortcode-s12'])){echo esc_textarea($horsetools_shortcode_options['shortcode-s12']);} ?></textarea>
				</p>
				<input class="ht-input-big ht-view-in" type="text" value="[vip] <?php _e('Content to be hidden', 'horse-tools'); ?> [/vip]"/>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This shortcode allows you to lock any content, and only the selected group of logged-in users can view it', 'horse-tools'); ?></p>     
			</div>
			</div>
			<!-- SIGN -->
			<div class="sotab-box htbox" id="tab2" style="display:none">
			<h2><?php _e('SIGN', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-signature"></i> <?php _e('Signature shortcode', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_shortcode_settings[shortcode-s2]" value="1" <?php if ( isset($horsetools_shortcode_options['shortcode-s2']) && 1 == $horsetools_shortcode_options['shortcode-s2'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable signature shortcode', 'horse-tools'); ?></label>
				</p>
				<div class="ht-classic">
				<?php
				$shortcode_s21 = !empty($horsetools_shortcode_options['shortcode-s21']) ? wp_kses_post($horsetools_shortcode_options['shortcode-s21']) : '';
				ob_start();
				wp_editor(
					$shortcode_s21,
					'userpostcontent',
					array(
						'textarea_name' => 'horsetools_shortcode_settings[shortcode-s21]',
						'media_buttons' => false,
					)
				);
				$editor_contents = ob_get_clean();
				echo $editor_contents;
				?>
				</div>
				<p>
				<input class="ht-input-big ht-view-in" type="text" value="[sign]"/>
				</p>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('If you want to display your signature anywhere, you can create content above and then use the generated shortcode at your desired location', 'horse-tools'); ?></p>   				
			</div>
			</div>
			<!-- DATE -->
			<div class="sotab-box htbox" id="tab3" style="display:none">
			<h2><?php _e('DATE', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-calendar"></i> <?php _e('Shortcode to display date', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_shortcode_settings[shortcode-s3]" value="1" <?php if ( isset($horsetools_shortcode_options['shortcode-s3']) && 1 == $horsetools_shortcode_options['shortcode-s3'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable date shortcode', 'horse-tools'); ?></label>
				</p>
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
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This shortcode is used to display the date in the post title. Please note that you need to enable the shortcode usage in the post title under the POST, PAGE section', 'horse-tools'); ?></p>   				
			</div>
			</div>
			<!-- GGET -->
			<div class="sotab-box htbox" id="tab4" style="display:none">
			<h2><?php _e('GGET', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-download"></i> <?php _e('Download button GGET shortcode', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_shortcode_settings[shortcode-s4]" value="1" <?php if ( isset($horsetools_shortcode_options['shortcode-s4']) && 1 == $horsetools_shortcode_options['shortcode-s4'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable GGET shortcode', 'horse-tools'); ?></label>
				</p>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_shortcode_settings[shortcode-s4a]" value="1" <?php if ( isset($horsetools_shortcode_options['shortcode-s4a']) && 1 == $horsetools_shortcode_options['shortcode-s4a'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Display link when seconds expire', 'horse-tools'); ?></label>
				</p>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_shortcode_settings[shortcode-s4b]" value="1" <?php if ( isset($horsetools_shortcode_options['shortcode-s4b']) && 1 == $horsetools_shortcode_options['shortcode-s4b'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Center-align button on page', 'horse-tools'); ?></label>
				</p>
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
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This shortcode is used to create a download button with a timeout', 'horse-tools'); ?></p>  
			</div>
			</div>
			<div class="ht-submit">
				<button type="submit"><i class="fa-regular fa-floppy-disk"></i> <?php _e('SAVE CONTENT', 'horse-tools'); ?></button>
			</div>
				<button id="ht-save-fast" type="submit"><i class="fa-regular fa-floppy-disk"></i></button>
			</form>
		</div>
	  </div>
      <div class="ht-sidebar">
	  </div>
	</div>	
	</div>
	<script>
        jQuery(document).ready(function($) {
			// ajax select
			$('form input[type="checkbox"]').change(function() {
				var currentForm = $(this).closest('form');
				$.ajax({
					type: 'POST',
					url: currentForm.attr('action'), 
					data: currentForm.serialize(), 
					success: function(response) {
						console.log('Turn on successfully');
					},
					error: function() {
						console.log('Error in AJAX request');
					}
				});
			});
		});
	</script>
	<?php
	// style horsetools
	require_once( HORSETOOLS_DIR . 'main/style.php');
	echo ob_get_clean();
}
function horsetools_shortcode_options_link() {
	add_submenu_page ('horsetools-options', 'Shortcode', '<i class="fa-regular fa-brackets-square" style="width:20px;"></i> '. __('Shortcode', 'horse-tools'), 'manage_options', 'horsetools-shortcode-options', 'horsetools_shortcode_options_page');
}
add_action('admin_menu', 'horsetools_shortcode_options_link');
function horsetools_shortcode_register_settings() {
	register_setting( 'horsetools_shortcode_settings_group', 'horsetools_shortcode_settings', array( 'sanitize_callback' => 'horsetools_sanitize_shortcode' ) );
}
add_action('admin_init', 'horsetools_shortcode_register_settings');
// clear cache
function horsetools_shortcode_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_shortcode_settings', 'options');
}
add_action('update_option_horsetools_shortcode_settings', 'horsetools_shortcode_settings_cache', 10, 2);

