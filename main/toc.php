<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_toc_options_page() {
	global $horsetools_toc_options;
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="fa-regular fa-list"></i> <?php _e('TOC', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_toc_settings_group'); ?> 
			<!-- SETTING -->
			<div class="sotab-box htbox" id="tab1" >
			<h2><?php _e('TOC', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-list"></i> <?php _e('Table of contents configuration', 'horse-tools') ?></h3>
			    <p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_toc_settings[toc1]" value="1" <?php if ( isset($horsetools_toc_options['toc1']) && 1 == $horsetools_toc_options['toc1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable table of contents', 'horse-tools'); ?></label>
				</p>
				<h4><?php _e('Custom posts will be displayed', 'horse-tools') ?></h4>
				<?php 
				$args = array(
				'public'   => true,
				);
				$post_types = get_post_types($args, 'objects'); 
				foreach ($post_types as $post_type_object) {
					if ($post_type_object->name == 'attachment') {
						continue;
					}
					?>
					<label class="nut-switch">
						<input type="checkbox" name="horsetools_toc_settings[posttype][]" value="<?php echo $post_type_object->name; ?>" <?php if (isset($horsetools_toc_options['posttype']) && in_array($post_type_object->name, $horsetools_toc_options['posttype'])) echo 'checked="checked"'; ?> />
						<span class="slider"></span>
					</label>
					<label class="ht-label-right"><?php echo $post_type_object->labels->name; ?></label>
					</p>
					<?php
				}
				?>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Select the custom post for which you want to display TOC content', 'horse-tools'); ?></p>
				<h4><?php _e('Shortcodes', 'horse-tools') ?></h4>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_toc_settings[shortcode]" value="1" <?php if ( isset($horsetools_toc_options['shortcode']) && 1 == $horsetools_toc_options['shortcode'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Use shortcodes', 'horse-tools'); ?></label>
				</p>
				<input class="ht-input-big ht-view-in" type="text" value="[horsetoc]"/>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('You can use the shortcode in the editor and add it to the position you want, note: only use 1 shortcode in the post', 'horse-tools'); ?></p>
				<h4><?php _e('Advanced configuration', 'horse-tools') ?></h4>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter a title', 'horse-tools') ?>" name="horsetools_toc_settings[tit-c1]" type="text" value="<?php if(!empty($horsetools_toc_options['tit-c1'])){echo sanitize_text_field($horsetools_toc_options['tit-c1']);} ?>"/>
				</p>
				<p style="display:grid;grid-template-columns: 1fr 1fr;max-width: 200px;">
					<?php for ($i = 1; $i <= 6; $i++) { ?>
						<label class="ht-container">h<?php echo $i; ?>
							<input type="checkbox" name="horsetools_toc_settings[tit_h<?php echo $i; ?>]" value="1" 
								   <?php if (isset($horsetools_toc_options['tit_h' . $i]) && 1 == $horsetools_toc_options['tit_h' . $i]) echo 'checked="checked"'; ?> />
							<span class="ht-checkmark"></span>
						</label>
					<?php } ?>
				</p>
				<select id="horsetools-toc-page-select">
					<option value=""><?php _e('Select the page to hide', 'horse-tools'); ?></option>
					<?php
					$pages = get_pages();
					foreach ($pages as $page) {
						echo '<option value="' . esc_attr($page->post_name) . '">' . esc_html($page->post_title) . '</option>';
					}
					?>
				</select>
				<div id="horsetools-toc-tags">
					<?php 
					if (!empty($horsetools_toc_options['toc-page-hi'])) {
						$selected_pages = explode("\n", $horsetools_toc_options['toc-page-hi']);
						foreach ($selected_pages as $page_slug) {
							if (!empty($page_slug)) {
								echo '<span class="horsetools-toc-tag">' . esc_html($page_slug) . ' <span class="remove-tag" data-slug="' . esc_attr($page_slug) . '">&times;</span></span>';
							}
						}
					} 
					?>
				</div>
				<textarea id="horsetools-hi-textarea" name="horsetools_toc_settings[toc-page-hi]" style="display:none;"><?php if(!empty($horsetools_toc_options['toc-page-hi'])){echo esc_textarea($horsetools_toc_options['toc-page-hi']);} ?></textarea>
				<script>
				jQuery(document).ready(function($) {
					function updateNoPageMessage() {
						if ($('#horsetools-toc-tags .horsetools-toc-tag').length === 0) {
							$('#horsetools-toc-tags').append('<span class="htno-page"><?php _e('No pages selected', 'horse-tools'); ?></span>');
						} else {
							$('#horsetools-toc-tags .htno-page').remove();
						}
					}
					$('#horsetools-toc-page-select').change(function() {
						var selectedPage = $(this).val();
						if (selectedPage && !isPageAlreadyAdded(selectedPage)) {
							var newTag = $('<span class="horsetools-toc-tag">' + selectedPage + ' <span class="remove-tag" data-slug="' + selectedPage + '">&times;</span></span>');
							$('#horsetools-toc-tags').append(newTag);
							updateTextarea();
							updateNoPageMessage();
						}
						$(this).val('');
					});
					$(document).on('click', '.remove-tag', function() {
						$(this).parent('.horsetools-toc-tag').remove();
						updateTextarea();
						updateNoPageMessage();
					});
					function isPageAlreadyAdded(pageSlug) {
						var exists = false;
						$('#horsetools-toc-tags .horsetools-toc-tag').each(function() {
							if ($(this).find('.remove-tag').data('slug') === pageSlug) {
								exists = true;
								return false; 
							}
						});
						return exists;
					}
					function updateTextarea() {
						var selectedPages = [];
						$('#horsetools-toc-tags .horsetools-toc-tag').each(function() {
							selectedPages.push($(this).find('.remove-tag').data('slug'));
						});
						$('#horsetools-hi-textarea').val(selectedPages.join("\n"));
					}
					updateNoPageMessage();
				});
				</script>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_toc_settings[tit-c3]" value="1" <?php if ( isset($horsetools_toc_options['tit-c3']) && 1 == $horsetools_toc_options['tit-c3'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Automatically open popup when scrolling down', 'horse-tools'); ?></label>
				</p>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_toc_settings[tit-c4]" value="1" <?php if ( isset($horsetools_toc_options['tit-c4']) && 1 == $horsetools_toc_options['tit-c4'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Disable numbers in front of tags', 'horse-tools'); ?></label>
				</p>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_toc_settings[tit-c5]" value="1" <?php if ( isset($horsetools_toc_options['tit-c5']) && 1 == $horsetools_toc_options['tit-c5'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('List is hidden by default', 'horse-tools'); ?></label>
				</p>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_toc_settings[tit-c6]" value="1" <?php if ( isset($horsetools_toc_options['tit-c6']) && 1 == $horsetools_toc_options['tit-c6'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Hide minimized list', 'horse-tools'); ?></label>
				</p>
				<h4><?php _e('Customize TOC display position', 'horse-tools') ?></h4>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_toc_settings[tag]" value="1" <?php if ( isset($horsetools_toc_options['tag']) && 1 == $horsetools_toc_options['tag'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable customization', 'horse-tools'); ?></label>
				</p>
				<p>
				<input class="ht-input-small" placeholder="<?php _e('Tag', 'horse-tools') ?>" name="horsetools_toc_settings[tag1]" type="text" value="<?php if(!empty($horsetools_toc_options['tag1'])){echo sanitize_text_field($horsetools_toc_options['tag1']);} else {echo sanitize_text_field('h2');} ?>"/>
				<input class="ht-input-small" placeholder="<?php _e('Location', 'horse-tools') ?>" name="horsetools_toc_settings[tag2]" type="number" value="<?php if(!empty($horsetools_toc_options['tag2'])){echo sanitize_text_field($horsetools_toc_options['tag2']);} else {echo sanitize_text_field('1');} ?>"/>
				</p>
				<h4><?php _e('Customize display and colors', 'horse-tools') ?></h4>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_toc_settings[main-color]" value="1" <?php if ( isset($horsetools_toc_options['main-color']) && 1 == $horsetools_toc_options['main-color'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable color and display customization', 'horse-tools'); ?></label>
				</p>
				<h5><?php _e('List color', 'horse-tools'); ?></h5>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_toc_settings[main-c1]" type="text" data-coloris value="<?php if(!empty($horsetools_toc_options['main-c1'])){echo sanitize_text_field($horsetools_toc_options['main-c1']);} ?>"/>
				<label class="ht-right-text"><?php _e('Background color', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_toc_settings[main-c2]" type="text" data-coloris value="<?php if(!empty($horsetools_toc_options['main-c2'])){echo sanitize_text_field($horsetools_toc_options['main-c2']);} ?>"/>
				<label class="ht-right-text"><?php _e('Border color', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_toc_settings[main-c4]" type="text" data-coloris value="<?php if(!empty($horsetools_toc_options['main-c4'])){echo sanitize_text_field($horsetools_toc_options['main-c4']);} ?>"/>
				<label class="ht-right-text"><?php _e('Link color', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_toc_settings[main-c5]" type="text" data-coloris value="<?php if(!empty($horsetools_toc_options['main-c5'])){echo sanitize_text_field($horsetools_toc_options['main-c5']);} ?>"/>
				<label class="ht-right-text"><?php _e('Selection color', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_toc_settings[main-c6]" type="text" data-coloris value="<?php if(!empty($horsetools_toc_options['main-c6'])){echo sanitize_text_field($horsetools_toc_options['main-c6']);} ?>"/>
				<label class="ht-right-text"><?php _e('Scroll bar color', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_toc_settings[main-c7]" type="text" data-coloris value="<?php if(!empty($horsetools_toc_options['main-c7'])){echo sanitize_text_field($horsetools_toc_options['main-c7']);} ?>"/>
				<label class="ht-right-text"><?php _e('H tag color when scrolled to', 'horse-tools'); ?></label>
				</p>
				
				<h5><?php _e('Title color', 'horse-tools'); ?></h5>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_toc_settings[main-t1]" type="text" data-coloris value="<?php if(!empty($horsetools_toc_options['main-t1'])){echo sanitize_text_field($horsetools_toc_options['main-t1']);} ?>"/>
				<label class="ht-right-text"><?php _e('Background title', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_toc_settings[main-t2]" type="text" data-coloris value="<?php if(!empty($horsetools_toc_options['main-t2'])){echo sanitize_text_field($horsetools_toc_options['main-t2']);} ?>"/>
				<label class="ht-right-text"><?php _e('Title text and icon color', 'horse-tools'); ?></label>
				</p>
				
				<h5><?php _e('Quick view button color', 'horse-tools'); ?></h5>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_toc_settings[main-b1]" type="text" data-coloris value="<?php if(!empty($horsetools_toc_options['main-b1'])){echo sanitize_text_field($horsetools_toc_options['main-b1']);} ?>"/>
				<label class="ht-right-text"><?php _e('Icon background color', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_toc_settings[main-b2]" type="text" data-coloris value="<?php if(!empty($horsetools_toc_options['main-b2'])){echo sanitize_text_field($horsetools_toc_options['main-b2']);} ?>"/>
				<label class="ht-right-text"><?php _e('Icon border color', 'horse-tools'); ?></label>
				</p>
				<p style="display:flex;align-items:center;">
				<input class="ht-input-color" name="horsetools_toc_settings[main-b3]" type="text" data-coloris value="<?php if(!empty($horsetools_toc_options['main-b3'])){echo sanitize_text_field($horsetools_toc_options['main-b3']);} ?>"/>
				<label class="ht-right-text"><?php _e('Icon color', 'horse-tools'); ?></label>
				</p>
				
				<h5><?php _e('Customize', 'horse-tools'); ?></h5>
				<p class="ht-keo">
				<input type="range" name="horsetools_toc_settings[main-si1]" min="14" max="30" value="<?php if(!empty($horsetools_toc_options['main-si1'])){echo sanitize_text_field($horsetools_toc_options['main-si1']);} else { echo sanitize_text_field('16');} ?>" class="htslide" data-index="5">
				<span><?php _e('Font size', 'horse-tools'); ?> <span id="demo5"></span> PX</span>
				</p>
				<p class="ht-keo">
				<input type="range" name="horsetools_toc_settings[main-r1]" min="1" max="50" value="<?php if(!empty($horsetools_toc_options['main-r1'])){echo sanitize_text_field($horsetools_toc_options['main-r1']);} else { echo sanitize_text_field('10');} ?>" class="htslide" data-index="2">
				<span><?php _e('Border radius', 'horse-tools'); ?> <span id="demo2"></span> PX</span>
				</p>
				<p class="ht-keo">
				<input type="range" name="horsetools_toc_settings[main-r2]" min="1" max="50" value="<?php if(!empty($horsetools_toc_options['main-r2'])){echo sanitize_text_field($horsetools_toc_options['main-r2']);} else { echo sanitize_text_field('10');} ?>" class="htslide" data-index="3">
				<span><?php _e('Icon border radius', 'horse-tools'); ?> <span id="demo3"></span> PX</span>
				</p>
				<h4><?php _e('Customize icons and positions', 'horse-tools') ?></h4>
				<p>
				<?php $styles = array('Icon1', 'Icon2', 'Icon3', 'Icon4', 'Icon5', 'Icon6'); ?>
				<select name="horsetools_toc_settings[main-ico]"> 
				<?php foreach($styles as $style) { ?> 
				<?php if(isset($horsetools_toc_options['main-ico']) && $horsetools_toc_options['main-ico'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
				<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
				<?php } ?> 
				</select>
				<label class="ht-right-text"><?php _e('Select icon', 'horse-tools'); ?></label>
				</p>
				<p>
				<?php $styles = array('Right', 'Left'); ?>
				<select name="horsetools_toc_settings[main-her1]"> 
				<?php foreach($styles as $style) { ?> 
				<?php if(isset($horsetools_toc_options['main-her1']) && $horsetools_toc_options['main-her1'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
				<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
				<?php } ?> 
				</select>
				<label class="ht-right-text"><?php _e('Popup location', 'horse-tools'); ?></label>
				</p>
				<p class="ht-keo">
				<input type="range" name="horsetools_toc_settings[main-her2]" min="10" max="50" value="<?php if(!empty($horsetools_toc_options['main-her2'])){echo sanitize_text_field($horsetools_toc_options['main-her2']);} else { echo sanitize_text_field('30');} ?>" class="htslide" data-index="1">
				<span><?php _e('Distance above', 'horse-tools'); ?> <span id="demo1"></span> %</span>
				</p>
				<p class="ht-keo">
				<input type="range" name="horsetools_toc_settings[main-her3]" min="55" max="200" value="<?php if(!empty($horsetools_toc_options['main-her3'])){echo sanitize_text_field($horsetools_toc_options['main-her3']);} else { echo sanitize_text_field('55');} ?>" class="htslide" data-index="4">
				<span><?php _e('Border distance', 'horse-tools'); ?> <span id="demo4"></span> PX</span>
				</p>	
				
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
function horsetools_toc_options_link() {
	add_submenu_page ('horsetools-options', 'Toc', '<i class="fa-regular fa-list" style="width:20px;"></i> '. __('TOC', 'horse-tools'), 'manage_options', 'horsetools-toc-options', 'horsetools_toc_options_page');
}
add_action('admin_menu', 'horsetools_toc_options_link');
function horsetools_toc_register_settings() {
	register_setting( 'horsetools_toc_settings_group', 'horsetools_toc_settings', array( 'sanitize_callback' => 'horsetools_sanitize_toc' ) );
}
add_action('admin_init', 'horsetools_toc_register_settings');
// clear cache
function horsetools_toc_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_toc_settings', 'options');
}
add_action('update_option_horsetools_toc_settings', 'horsetools_toc_settings_cache', 10, 2);

