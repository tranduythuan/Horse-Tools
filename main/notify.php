<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_notify_options_page() {
	global $horsetools_notify_options;
	ob_start(); 
	?>
	<div class="wrap ht-wrap">
	<div class="ht-wrap-top">
	</div>
	<div class="ht-wrap2">
	  <div class="ht-box">
		<div class="ht-menu">
			<div class="ht-logo ht-logoquay">
			<a class="ht-logoquaya" href="https://example.com" target="_blank">
			<span><?php horsetools_logo(); ?></span>
			</a>
			</div>
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="fa-regular fa-shield-halved"></i> <?php _e('BLOCKER', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="fa-regular fa-bell"></i> <?php _e('NOTIFY', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab3')"><i class="fa-regular fa-window"></i> <?php _e('POPUP', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab4')"><i class="fa-regular fa-cookie-bite"></i> <?php _e('COOKIE', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_notify_settings_group'); ?> 
			<!-- BLOCKER -->
			<div class="sotab-box htbox" id="tab1" >
			<h2><?php _e('BLOCKER', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="fa-regular fa-shield-halved"></i> <?php _e('Browser ad-block notification', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_notify_settings[notify-block1]" value="1" <?php if ( isset($horsetools_notify_options['notify-block1']) && 1 == $horsetools_notify_options['notify-block1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable ad-block detection', 'horse-tools'); ?></label>
				</p>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_notify_settings[notify-block11]" value="1" <?php if ( isset($horsetools_notify_options['notify-block11']) && 1 == $horsetools_notify_options['notify-block11'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Only notify, do not block access', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_notify_settings[notify-block-c1]" type="text" data-coloris value="<?php if(!empty($horsetools_notify_options['notify-block-c1'])){echo sanitize_text_field($horsetools_notify_options['notify-block-c1']);} ?>"/>
				<label class="ht-right-text"><?php _e('Select button color', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_notify_settings[notify-block-c2]" type="text" data-coloris value="<?php if(!empty($horsetools_notify_options['notify-block-c2'])){echo sanitize_text_field($horsetools_notify_options['notify-block-c2']);} ?>"/>
				<label class="ht-right-text"><?php _e('Select button border color', 'horse-tools'); ?></label>
				</p>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter title', 'horse-tools') ?>" name="horsetools_notify_settings[notify-block12]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-block12'])){echo sanitize_text_field($horsetools_notify_options['notify-block12']);} ?>"/>
				</p>
				<p>
				<textarea style="height:150px;" class="ht-code-textarea" name="horsetools_notify_settings[notify-block13]" placeholder="<?php _e('Enter content here', 'horse-tools'); ?>"><?php if(!empty($horsetools_notify_options['notify-block13'])){echo esc_textarea($horsetools_notify_options['notify-block13']);} ?></textarea>
				</p>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enter the title and content you want to display when ad-blocker is detected', 'horse-tools'); ?></p>   
			</div>
			</div>
			<!-- NOTIFY -->
			<div class="sotab-box htbox" id="tab2" style="display:none">
			<h2><?php _e('NOTIFY', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="fa-regular fa-bell"></i> <?php _e('Notification at the top of the page', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_notify_settings[notify-notis1]" value="1" <?php if ( isset($horsetools_notify_options['notify-notis1']) && 1 == $horsetools_notify_options['notify-notis1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable notification', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_notify_settings[notify-notis-c1]" type="text" data-coloris value="<?php if(!empty($horsetools_notify_options['notify-notis-c1'])){echo sanitize_text_field($horsetools_notify_options['notify-notis-c1']);} ?>"/>
				<label class="ht-right-text"><?php _e('Select background color', 'horse-tools'); ?></label>
				</p>
				<p>
				<textarea style="height:150px;" class="ht-code-textarea" name="horsetools_notify_settings[notify-notis11]" placeholder="<?php _e('Enter content here', 'horse-tools'); ?>"><?php if(!empty($horsetools_notify_options['notify-notis11'])){echo esc_textarea($horsetools_notify_options['notify-notis11']);} ?></textarea>
				</p>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enter the content you want to display in the notification, and customize the colors to match your preferences. A notification will appear at the top of your website, making it easy for users to see', 'horse-tools'); ?></p> 				
			</div>
			</div>
			<!-- POPUP -->
			<div class="sotab-box htbox" id="tab3" style="display:none">
			<h2><?php _e('POPUP', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="fa-regular fa-window"></i> <?php _e('Create an outstanding popup', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_notify_settings[notify-popup1]" value="1" <?php if ( isset($horsetools_notify_options['notify-popup1']) && 1 == $horsetools_notify_options['notify-popup1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable popup', 'horse-tools'); ?></label>
				</p>
				<p>
				<input class="ht-input-small" name="horsetools_notify_settings[notify-popup-c1]" type="number" value="<?php if(!empty($horsetools_notify_options['notify-popup-c1'])){echo sanitize_text_field($horsetools_notify_options['notify-popup-c1']);} ?>"/>
				<label class="ht-label-right"><?php _e('Popup save time (.. hours)', 'horse-tools'); ?></label>
				</p>
				<div id="ht-imgstyle" class="ht-imgstyle ht-imgstyle4">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/popup/1.png'); ?>" data-value="1" class="<?php if(isset($horsetools_notify_options['notify-popup-c2']) && $horsetools_notify_options['notify-popup-c2'] == '1') echo 'selected'; ?>" />
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/popup/2.png'); ?>" data-value="2" class="<?php if(isset($horsetools_notify_options['notify-popup-c2']) && $horsetools_notify_options['notify-popup-c2'] == '2') echo 'selected'; ?>" />
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/popup/3.png'); ?>" data-value="3" class="<?php if(isset($horsetools_notify_options['notify-popup-c2']) && $horsetools_notify_options['notify-popup-c2'] == '3') echo 'selected'; ?>" />
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/popup/4.png'); ?>" data-value="4" class="<?php if(isset($horsetools_notify_options['notify-popup-c2']) && $horsetools_notify_options['notify-popup-c2'] == '4') echo 'selected'; ?>" />
				</div>
				<input type="hidden" name="horsetools_notify_settings[notify-popup-c2]" id="cutop11" value="<?php if(!empty($horsetools_notify_options['notify-popup-c2'])){echo sanitize_text_field($horsetools_notify_options['notify-popup-c2']);} else {echo sanitize_text_field('1');} ?>" />
				<script>
					document.addEventListener("DOMContentLoaded", function() {
						var imgStyles = document.querySelectorAll('#ht-imgstyle img');
						imgStyles.forEach(function(img) {
							img.addEventListener('click', function() {
								var selectedStyle = this.getAttribute('data-value');
								document.getElementById('cutop11').value = selectedStyle;
								imgStyles.forEach(function(img) {
									img.classList.remove('selected');
								});
								this.classList.add('selected');
							});
						});
					});
				</script>
				<p style="display:flex;">
				<input id="ht-add1" class="ht-input-big" name="horsetools_notify_settings[notify-popup11]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-popup11'])){echo sanitize_text_field($horsetools_notify_options['notify-popup11']);} ?>" placeholder="<?php _e('Add images', 'horse-tools'); ?>" />
				<button class="ht-selec" data-input-id="ht-add1"><?php _e('Select image', 'horse-tools'); ?></button>
				</p>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter title', 'horse-tools') ?>" name="horsetools_notify_settings[notify-popup12]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-popup12'])){echo sanitize_text_field($horsetools_notify_options['notify-popup12']);} ?>"/>
				</p>
				<div class="ht-classic">
				<?php
				$popup_editor = !empty($horsetools_notify_options['notify-popup13']) ? wp_kses_post($horsetools_notify_options['notify-popup13']) : '';
				ob_start();
				wp_editor(
					$popup_editor,
					'userpostcontent',
					array(
						'textarea_name' => 'horsetools_notify_settings[notify-popup13]',
						'media_buttons' => false,
					)
				);
				$editor_contents = ob_get_clean();
				echo $editor_contents;
				?>
				</div>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter image link', 'horse-tools') ?>" name="horsetools_notify_settings[notify-popup14]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-popup14'])){echo sanitize_text_field($horsetools_notify_options['notify-popup14']);} ?>"/>
				</p>
				
				<p class="ht-keo">
				<input type="range" name="horsetools_notify_settings[notify-popup-m1]" min="1" max="50" value="<?php if(!empty($horsetools_notify_options['notify-popup-m1'])){echo sanitize_text_field($horsetools_notify_options['notify-popup-m1']);} else { echo sanitize_text_field('10');} ?>" class="htslide" data-index="1">
				<span><?php _e('Border radius', 'horse-tools'); ?> <span id="demo1"></span> PX</span>
				</p>
				
				<p class="ht-keo">
				<input type="range" name="horsetools_notify_settings[notify-popup-m2]" min="300" max="1000" value="<?php if(!empty($horsetools_notify_options['notify-popup-m2'])){echo sanitize_text_field($horsetools_notify_options['notify-popup-m2']);} else { echo sanitize_text_field('800');} ?>" class="htslide" data-index="2">
				<span><?php _e('Max width', 'horse-tools'); ?> <span id="demo2"></span> PX</span>
				</p>
				
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enter the content you want to display and configure the customizations above so the popup can appear when users visit your website', 'horse-tools'); ?></p> 				
			</div>
			</div>
			<!-- COOKIE -->
			<div class="sotab-box htbox" id="tab4" style="display:none">
			<h2><?php _e('COOKIE', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-cookie-bite"></i> <?php _e('Set up cookie notifications', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_notify_settings[notify-cookie1]" value="1" <?php if ( isset($horsetools_notify_options['notify-cookie1']) && 1 == $horsetools_notify_options['notify-cookie1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable cookie', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_notify_settings[notify-cookie-c1]" type="text" data-coloris value="<?php if(!empty($horsetools_notify_options['notify-cookie-c1'])){echo sanitize_text_field($horsetools_notify_options['notify-cookie-c1']);} ?>"/>
				<label class="ht-right-text"><?php _e('Select title color and button', 'horse-tools'); ?></label>
				</p>
				<p>
				<?php $styles = array('Left', 'Right'); ?>
				<select name="horsetools_notify_settings[notify-cookie-c2]"> 
				<?php foreach($styles as $style) { ?> 
				<?php if(isset($horsetools_notify_options['notify-cookie-c2']) && $horsetools_notify_options['notify-cookie-c2'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
				<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
				<?php } ?> 
				</select>
				<label class="ht-right-text"><?php _e('Location', 'horse-tools'); ?></label>
				</p>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter the policy page link', 'horse-tools') ?>" name="horsetools_notify_settings[notify-cookie-l1]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-cookie-l1'])){echo sanitize_text_field($horsetools_notify_options['notify-cookie-l1']);} ?>"/>
				</p>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter cookie title', 'horse-tools') ?>" name="horsetools_notify_settings[notify-cookie11]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-cookie11'])){echo sanitize_text_field($horsetools_notify_options['notify-cookie11']);} ?>"/>
				</p>
				<p>
				<textarea style="height:150px;" class="ht-code-textarea" name="horsetools_notify_settings[notify-cookie12]" placeholder="<?php _e('Enter cookie content', 'horse-tools'); ?>"><?php if(!empty($horsetools_notify_options['notify-cookie12'])){echo esc_textarea($horsetools_notify_options['notify-cookie12']);} ?></textarea>
				</p>  
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Display your cookie notice to inform users about cookie use and allow them to manage their preferences easily', 'horse-tools'); ?></p> 
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
function horsetools_notify_options_link() {
	add_submenu_page ('horsetools-options', 'Notify', '<i class="fa-regular fa-bell" style="width:20px;"></i> '. __('Notify', 'horse-tools'), 'manage_options', 'horsetools-notify-options', 'horsetools_notify_options_page');
}
add_action('admin_menu', 'horsetools_notify_options_link');
function horsetools_notify_register_settings() {
	register_setting( 'horsetools_notify_settings_group', 'horsetools_notify_settings', array( 'sanitize_callback' => 'horsetools_sanitize_notify' ) );
}
add_action('admin_init', 'horsetools_notify_register_settings');
// clear cache
function horsetools_notify_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_notify_settings', 'options');
}
add_action('update_option_horsetools_notify_settings', 'horsetools_notify_settings_cache', 10, 2);

