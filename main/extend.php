<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_extend_options_page() {
	global $horsetools_extend_options;
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="ti ti-share"></i> <?php _e('FREE', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_extend_settings_group'); ?> 
			<!-- FREE -->
			<div class="sotab-box htbox" id="tab1">
			<h2><?php _e('FREE', 'horse-tools'); ?></h2>
			<div class="ht-card ht-extend-grid">
				<div class="ht-extend-box <?php if ( isset($horsetools_extend_options['code']) && 1 == $horsetools_extend_options['code'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more1"><?php _e('Add code', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more1" type="checkbox" name="horsetools_extend_settings[code]" value="1" <?php if ( isset($horsetools_extend_options['code']) && 1 == $horsetools_extend_options['code'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more1">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/code.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('This feature allows you to add CSS, JS, and HTML code to your WordPress site through hooks like WP head, WP body, WP footer, and WP login, making it easy and convenient to supplement your code', 'horse-tools'); ?>
					</div>
				</div>
			    <div class="ht-extend-box <?php if ( isset($horsetools_extend_options['clean']) && 1 == $horsetools_extend_options['clean'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more2"><?php _e('Clean', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more2" type="checkbox" name="horsetools_extend_settings[clean]" value="1" <?php if ( isset($horsetools_extend_options['clean']) && 1 == $horsetools_extend_options['clean'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more2">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/clean.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('This feature helps optimize cleanup tasks such as clearing content, comments, and the media library. It makes your website cleaner and more optimized', 'horse-tools'); ?>
					</div>
				</div>
				<div class="ht-extend-box <?php if ( isset($horsetools_extend_options['font']) && 1 == $horsetools_extend_options['font'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more3"><?php _e('Font', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more3" type="checkbox" name="horsetools_extend_settings[font]" value="1" <?php if ( isset($horsetools_extend_options['font']) && 1 == $horsetools_extend_options['font'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more3">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/font.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('Add fonts to your website with just a few simple steps. Additionally, it allows you to quickly set fonts in the specific areas you want to change, offering great convenience', 'horse-tools'); ?>
					</div>
				</div>
				<div class="ht-extend-box <?php if ( isset($horsetools_extend_options['redirect']) && 1 == $horsetools_extend_options['redirect'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more4"><?php _e('Redirects', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more4" type="checkbox" name="horsetools_extend_settings[redirect]" value="1" <?php if ( isset($horsetools_extend_options['redirect']) && 1 == $horsetools_extend_options['redirect'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more4">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/redirect.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('This feature allows you to configure website redirects with multiple options such as 301, 404, and 503, making management much simpler', 'horse-tools'); ?>
					</div>
				</div>
				<div class="ht-extend-box <?php if ( isset($horsetools_extend_options['index']) && 1 == $horsetools_extend_options['index'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more5"><?php _e('Index now', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more5" type="checkbox" name="horsetools_extend_settings[index]" value="1" <?php if ( isset($horsetools_extend_options['index']) && 1 == $horsetools_extend_options['index'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more5">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/index.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('Leverage Google’s Indexing API to speed up the indexing of your website on Google’s search engine. You can add as many API keys as you like for unlimited indexing capacity', 'horse-tools'); ?>
					</div>
				</div>
				<div class="ht-extend-box <?php if ( isset($horsetools_extend_options['toc']) && 1 == $horsetools_extend_options['toc'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more6"><?php _e('TOC', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more6" type="checkbox" name="horsetools_extend_settings[toc]" value="1" <?php if ( isset($horsetools_extend_options['toc']) && 1 == $horsetools_extend_options['toc'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more6">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/toc.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('The table of contents is an incredibly useful feature that allows readers to easily grasp the content by summarizing the headings (h-tags) in the article', 'horse-tools'); ?>
					</div>
				</div>
				<div class="ht-extend-box <?php if ( isset($horsetools_extend_options['ads']) && 1 == $horsetools_extend_options['ads'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more9"><?php _e('Ads', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more9" type="checkbox" name="horsetools_extend_settings[ads]" value="1" <?php if ( isset($horsetools_extend_options['ads']) && 1 == $horsetools_extend_options['ads'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more9">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/ads.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('To add advertisements to your website, simply enable this feature. It provides you with various ad options to meet your distribution needs', 'horse-tools'); ?>
					</div>
				</div>
				<div class="ht-extend-box <?php if ( isset($horsetools_extend_options['notify']) && 1 == $horsetools_extend_options['notify'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more11"><?php _e('Notify', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more11" type="checkbox" name="horsetools_extend_settings[notify]" value="1" <?php if ( isset($horsetools_extend_options['notify']) && 1 == $horsetools_extend_options['notify'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more11">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/notify.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('Support quick notification setup, allowing web managers to easily deliver useful and timely content, helping users stay informed effortlessly', 'horse-tools'); ?>
					</div>
				</div>
				<div class="ht-extend-box <?php if ( isset($horsetools_extend_options['shortcode']) && 1 == $horsetools_extend_options['shortcode'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more12"><?php _e('Shortcode', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more12" type="checkbox" name="horsetools_extend_settings[shortcode]" value="1" <?php if ( isset($horsetools_extend_options['shortcode']) && 1 == $horsetools_extend_options['shortcode'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more12">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/shortcode.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('This feature compiles various shortcode templates with essential functionalities that are frequently used to enhance the user experience in WordPress', 'horse-tools'); ?>
					</div>
				</div>
				<div class="ht-extend-box <?php if ( isset($horsetools_extend_options['search']) && 1 == $horsetools_extend_options['search'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more10"><?php _e('Horse Search', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more10" type="checkbox" name="horsetools_extend_settings[search]" value="1" <?php if ( isset($horsetools_extend_options['search']) && 1 == $horsetools_extend_options['search'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more10">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/search.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('If youre tired of WordPress default search tool because its too slow, try out our lightning-fast search feature to give your users an exceptional and speedy search experience', 'horse-tools'); ?>
					</div>
				</div>
				<div class="ht-extend-box <?php if ( isset($horsetools_extend_options['debug']) && 1 == $horsetools_extend_options['debug'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more7"><?php _e('Debug', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more7" type="checkbox" name="horsetools_extend_settings[debug]" value="1" <?php if ( isset($horsetools_extend_options['debug']) && 1 == $horsetools_extend_options['debug'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more7">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/debug.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('If you need to monitor WordPress debug files without accessing the file manager, this tool is made just for that purpose', 'horse-tools'); ?>
					</div>
				</div>
				<div class="ht-extend-box <?php if ( isset($horsetools_extend_options['export']) && 1 == $horsetools_extend_options['export'] ) echo 'ht-extend-sel'; ?>">
					<div class="ht-extend-tit">
						<label class="ht-extend-tit-span" for="more8"><?php _e('Export', 'horse-tools'); ?></label>
						<div>
						<span class="nut-switch">
						<input id="more8" type="checkbox" name="horsetools_extend_settings[export]" value="1" <?php if ( isset($horsetools_extend_options['export']) && 1 == $horsetools_extend_options['export'] ) echo 'checked="checked"'; ?> />
						<span class="slider"></span></span>
						</div>
					</div>
					<div class="ht-extend-card more8">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/extend/export.png'); ?>"/>
					<span class="ht-extend-free"><?php _e('Free', 'horse-tools'); ?></span>
					<?php _e('If you want to export or import the settings of the Horse Tools plugin, enable this feature. It allows you to easily transfer configurations from this site to another', 'horse-tools'); ?>
					</div>
				</div>
			</div>
			</div>
			<div class="ht-submit">
				<button type="submit"><i class="ti ti-device-floppy"></i> <?php _e('SAVE CONTENT', 'horse-tools'); ?></button>
			</div>
			</form>
		</div>
	  </div>
      <div class="ht-sidebar">
	  </div>
	</div>	
	</div>
	<script>
		jQuery(document).ready(function($) {
			function searchToggle(selector) {
				$(selector).each(function() {
					var id = $(this).attr('id');
					var relatedClass = '.' + id; 
					if ($(this).is(':checked')) {
						$(relatedClass).css('opacity', '1');
						$(relatedClass).css('filter', 'grayscale(0)');						
					} else {
						$(relatedClass).css('opacity', '0.6'); 
						$(relatedClass).css('filter', 'grayscale(100%)');
					}
				});
			}
			searchToggle('input[id^="more"]');
			$('input[id^="more"]').change(function() {
				searchToggle('input[id^="more"]');
			});
		});
	</script>
	<?php
	// style horsetools
	require_once( HORSETOOLS_DIR . 'main/style.php');
	echo ob_get_clean();
}
function horsetools_extend_options_link() {
	add_submenu_page ('horsetools-options', 'Extend', '<i class="ti ti-plus" style="width:20px;"></i> '. __('Extend', 'horse-tools'), 'manage_options', 'horsetools-extend-options', 'horsetools_extend_options_page');
}
add_action('admin_menu', 'horsetools_extend_options_link');
function horsetools_extend_register_settings() {
	register_setting( 'horsetools_extend_settings_group', 'horsetools_extend_settings', array( 'sanitize_callback' => 'horsetools_sanitize_extend' ) );
}
add_action('admin_init', 'horsetools_extend_register_settings');
// clear cache
function horsetools_extend_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_extend_settings', 'options');
}
add_action('update_option_horsetools_extend_settings', 'horsetools_extend_settings_cache', 10, 2);

