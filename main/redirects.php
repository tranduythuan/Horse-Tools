<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_redirects_options_page() {
	global $horsetools_redirects_options;
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="fa-regular fa-compass"></i> <?php _e('301', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="fa-regular fa-do-not-enter"></i> <?php _e('404', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab3')"><i class="fa-regular fa-bug"></i> <?php _e('503', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_redirects_settings_group'); ?> 
			<!-- 301 -->
			<div class="sotab-box htbox" id="tab1">
			<h2><?php _e('301', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-compass"></i> <?php _e('Redirect 301 whole page', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_redirects_settings[redi11]" value="1" <?php if ( isset($horsetools_redirects_options['redi11']) && 1 == $horsetools_redirects_options['redi11'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable site-wide 301 redirects', 'horse-tools'); ?></label>
				</p>
				<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[redi12]" value="<?php if(!empty($horsetools_redirects_options['redi12'])){echo sanitize_text_field($horsetools_redirects_options['redi12']);} ?>" />
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This function will redirect all of your website pages to the destination page of your choice', 'horse-tools'); ?></p>
			  <h3><i class="fa-regular fa-compass"></i> <?php _e('Redirect 301 to a custom page', 'horse-tools') ?></h3>
			    <p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_redirects_settings[redi1]" value="1" <?php if ( isset($horsetools_redirects_options['redi1']) && 1 == $horsetools_redirects_options['redi1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable 301 redirection', 'horse-tools'); ?></label>
				</p>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('This function allows you to redirect 301 links to the target page', 'horse-tools'); ?></p>
				
				<div id="sortable-list">
				<div data-id="1" class="ui-state-default ht-button-grid">
				<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[rechan11]" value="<?php if(!empty($horsetools_redirects_options['rechan11'])){echo sanitize_text_field($horsetools_redirects_options['rechan11']);} ?>" />
				<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[rechan21]" value="<?php if(!empty($horsetools_redirects_options['rechan21'])){echo sanitize_text_field($horsetools_redirects_options['rechan21']);} ?>" />
				</div>
				<?php
				if (is_array($horsetools_redirects_options) || is_object($horsetools_redirects_options)) {
					foreach ($horsetools_redirects_options as $key => $value) {
						if (preg_match('/^rechan1(\d+)$/', $key, $matches) && $matches[1] != 1) {
							$n = $matches[1];
							echo '<div data-id="' . $n . '" class="ui-state-default ht-button-grid">';
							echo '<input class="ht-input-big" placeholder="'. __('Enter the link', 'horse-tools') .'" type="text" name="horsetools_redirects_settings[rechan1' . $n . ']" value="' . sanitize_text_field($horsetools_redirects_options['rechan1' . $n]) . '" />';
							echo '<input class="ht-input-big" placeholder="'. __('Enter the link', 'horse-tools') .'" type="text" name="horsetools_redirects_settings[rechan2' . $n . ']" value="' . sanitize_text_field($horsetools_redirects_options['rechan2' . $n]) . '" />';
							echo '<span id="ht-chatx">&#x2715</span>';
							echo '</div>';
						}
					}
				}
				?>
				</div>
				<span id="ht-chatmore"><i class="fa-regular fa-plus"></i> <?php _e('Add link', 'horse-tools'); ?></span>
			</div>	
			</div>
			<!-- 404 -->
			<div class="sotab-box htbox" id="tab2" style="display:none">
			<h2><?php _e('404', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-do-not-enter"></i> <?php _e('404 redirects', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_redirects_settings[redi2]" value="1" <?php if ( isset($horsetools_redirects_options['redi2']) && 1 == $horsetools_redirects_options['redi2'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable 404 redirection', 'horse-tools'); ?></label>
				</p>
				<select id="horsetools-toc-page-select">
					<option value=""><?php _e('Select redirect page', 'horse-tools'); ?></option>
					<?php
					$pages = get_pages();
					foreach ($pages as $page) {
						echo '<option value="' . esc_attr($page->post_name) . '">' . esc_html($page->post_title) . '</option>';
					}
					?>
				</select>
				<div id="horsetools-toc-tags">
					<?php 
					if (!empty($horsetools_redirects_options['redi21'])) {
						$page_slug = $horsetools_redirects_options['redi21'];
						if (!empty($page_slug)) {
							echo '<span class="horsetools-toc-tag">' . esc_html($page_slug) . ' <span class="remove-tag" data-slug="' . esc_attr($page_slug) . '">&times;</span></span>';
						}
					} 
					?>
				</div>
				<input id="horsetools-hi-input" class="ht-input-big" type="text" style="display:none;" name="horsetools_redirects_settings[redi21]" value="<?php if(!empty($horsetools_redirects_options['redi21'])){echo sanitize_text_field($horsetools_redirects_options['redi21']);} ?>" />
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Redirect the 404 page to the homepage or a custom page of your choice, leave the field blank if you want to redirect to the homepage', 'horse-tools'); ?></p>
			</div>
			</div>
			<!-- 503 -->
			<div class="sotab-box htbox" id="tab3" style="display:none">
			<h2><?php _e('503', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-bug"></i> <?php _e('Maintenance mode for developers (503)', 'horse-tools') ?></h3>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_redirects_settings[redi3]" value="1" <?php if ( isset($horsetools_redirects_options['redi3']) && 1 == $horsetools_redirects_options['redi3'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable 503 maintenance mode', 'horse-tools'); ?></label>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('All links on your website will redirect to the maintenance page, and only logged-in admin accounts can view the content', 'horse-tools'); ?></p>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter title', 'horse-tools') ?>" name="horsetools_redirects_settings[redi31]" type="text" value="<?php if(!empty($horsetools_redirects_options['redi31'])){echo sanitize_text_field($horsetools_redirects_options['redi31']);} ?>"/>
				</p>
				<p>
				<textarea style="height:150px;" class="ht-code-textarea" name="horsetools_redirects_settings[redi32]" placeholder="<?php _e('Enter content here', 'horse-tools'); ?>"><?php if(!empty($horsetools_redirects_options['redi32'])){echo esc_textarea($horsetools_redirects_options['redi32']);} ?></textarea>
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
			// them link
			var count = 0;
			$('#ht-chatmore').click(function() {
				var count = $('#sortable-list .ui-state-default:last').data('id') + 1;
				var newDiv = $('<div data-id="' + count + '" class="ui-state-default ht-button-grid">' +
					'<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[rechan1' + count + ']" />' +
					'<input class="ht-input-big" placeholder="<?php _e('Enter the link', 'horse-tools'); ?>" type="text" name="horsetools_redirects_settings[rechan2' + count + ']" />' +
					'<span id="ht-chatx">&#x2715</span>' +
					'</div>');
				$('#sortable-list').append(newDiv);
			});
			$('#sortable-list').on('click', '#ht-chatx', function() {
				$(this).parent('.ui-state-default').remove();
				count--;
			});
			// chon trang can chuyen
			function updateNoPageMessage() {
				if ($('#horsetools-toc-tags .horsetools-toc-tag').length === 0) {
					$('#horsetools-toc-tags').append('<span class="htno-page"><?php _e("No pages selected", "horse-tools"); ?></span>');
				} else {
					$('#horsetools-toc-tags .htno-page').remove();
				}
			}
			$('#horsetools-toc-page-select').change(function() {
				var selectedPage = $(this).val();
				if (selectedPage) {
					var formattedPage = selectedPage; // Prepend slash
					$('#horsetools-toc-tags').html('<span class="horsetools-toc-tag">' + formattedPage + ' <span class="remove-tag" data-slug="' + formattedPage + '">&times;</span></span>');
					$('#horsetools-hi-input').val(formattedPage); // Set the input value with slash
					updateNoPageMessage();
				}
				$(this).val('');
			});
			$(document).on('click', '.remove-tag', function() {
				$(this).parent('.horsetools-toc-tag').remove();
				$('#horsetools-hi-input').val('');
				updateNoPageMessage();
			});
			updateNoPageMessage();
		});
	</script>
	<?php
	// style horsetools
	require_once( HORSETOOLS_DIR . 'main/style.php');
	echo ob_get_clean();
}
function horsetools_redirects_options_link() {
	add_submenu_page ('horsetools-options', 'Redirects', '<i class="fa-regular fa-compass" style="width:20px;"></i> '. __('Redirects', 'horse-tools'), 'manage_options', 'horsetools-redirects-options', 'horsetools_redirects_options_page');
}
add_action('admin_menu', 'horsetools_redirects_options_link');
function horsetools_redirects_register_settings() {
	register_setting( 'horsetools_redirects_settings_group', 'horsetools_redirects_settings', array( 'sanitize_callback' => 'horsetools_sanitize_redirects' ) );
}
add_action('admin_init', 'horsetools_redirects_register_settings');
// clear cache
function horsetools_redirects_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_redirects_settings', 'options');
}
add_action('update_option_horsetools_redirects_settings', 'horsetools_redirects_settings_cache', 10, 2);

