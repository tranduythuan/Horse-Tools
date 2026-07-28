<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_ads_options_page() {
	global $horsetools_ads_options;
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="fa-regular fa-computer-mouse"></i> <?php _e('CLICK', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="fa-regular fa-rectangle-ad"></i> <?php _e('ADSENSE', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab3')"><i class="fa-regular fa-file-check"></i> <?php _e('ADS.TXT', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php'); 
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_ads_settings_group'); ?> 
			<!-- CLICK -->
			<div class="sotab-box htbox" id="tab1" >
			<h2><?php _e('CLICK', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="fa-regular fa-circle-exclamation"></i> <?php _e('Forced ad clicks — removed', 'horse-tools') ?></h3>
				<p class="ht-note ht-note-red"><i class="fa-regular fa-lightbulb-on"></i>
				<?php _e('This feature was removed in Horse Tools 1.0.0. It opened an affiliate URL in a hidden off-screen window every time a visitor clicked anywhere on the page. That is affiliate cookie stuffing: it deceives your visitors, breaks the terms of every major affiliate network and of Google AdSense, and can get your domain and your ad accounts banned.', 'horse-tools'); ?>
				<br><br>
				<?php _e('If you had it enabled it is now inactive. The AdSense and ads.txt tools on the other tabs are unaffected.', 'horse-tools'); ?>
				</p>
			</div>
			</div>
			<!-- ADSENSE -->
			<div class="sotab-box htbox" id="tab2" style="display:none">
			<h2><?php _e('ADSENSE', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="fa-regular fa-rectangle-ad"></i> <?php _e('Set up Adsense ads', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_ads_settings[ads-sense1]" value="1" <?php if ( isset($horsetools_ads_options['ads-sense1']) && 1 == $horsetools_ads_options['ads-sense1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable Adsense', 'horse-tools'); ?></label>
				</p>
				<p>
				<textarea style="height:90px;" class="ht-code-textarea" name="horsetools_ads_settings[ads-sense11]" placeholder="<?php _e('Enter Adsense code here', 'horse-tools'); ?>"><?php if(!empty($horsetools_ads_options['ads-sense11'])){echo esc_textarea($horsetools_ads_options['ads-sense11']);} ?></textarea>
				</p>
				<h4><?php _e('Option to display ads in custom posts', 'horse-tools') ?></h4>
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
						<input type="checkbox" name="horsetools_ads_settings[posttype][]" value="<?php echo $post_type_object->name; ?>" <?php if (isset($horsetools_ads_options['posttype']) && in_array($post_type_object->name, $horsetools_ads_options['posttype'])) echo 'checked="checked"'; ?> />
						<span class="slider"></span>
					</label>
					<label class="ht-label-right"><?php echo $post_type_object->labels->name; ?></label>
					</p>
					<?php
				}
				?>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Select the custom post for which you want to show ads', 'horse-tools'); ?></p>
				<h5><?php _e('Top and bottom positions', 'horse-tools') ?></h5>
				<p>
				<textarea style="height:90px;" class="ht-code-textarea" name="horsetools_ads_settings[ads-sense-p1]" placeholder="<?php _e('Add ads to the top of the article', 'horse-tools'); ?>"><?php if(!empty($horsetools_ads_options['ads-sense-p1'])){echo esc_textarea($horsetools_ads_options['ads-sense-p1']);} ?></textarea>
				</p>
				<p>
				<textarea style="height:90px;" class="ht-code-textarea" name="horsetools_ads_settings[ads-sense-p2]" placeholder="<?php _e('Add ads at the bottom of the article', 'horse-tools'); ?>"><?php if(!empty($horsetools_ads_options['ads-sense-p2'])){echo esc_textarea($horsetools_ads_options['ads-sense-p2']);} ?></textarea>
				</p>
				<h5><?php _e('Custom position in posts', 'horse-tools') ?></h5>
				<p>
				<input class="ht-input-small" placeholder="<?php _e('Tag', 'horse-tools') ?>" name="horsetools_ads_settings[ads-sense-c1]" type="text" value="<?php if(!empty($horsetools_ads_options['ads-sense-c1'])){echo sanitize_text_field($horsetools_ads_options['ads-sense-c1']);} else {echo sanitize_text_field('p');} ?>"/>
				<input class="ht-input-small" placeholder="<?php _e('Quantity', 'horse-tools') ?>" name="horsetools_ads_settings[ads-sense-c2]" type="number" value="<?php if(!empty($horsetools_ads_options['ads-sense-c2'])){echo sanitize_text_field($horsetools_ads_options['ads-sense-c2']);} else {echo sanitize_text_field('10');} ?>"/>
				</p>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('Enter the tag and tag number for the ad to appear', 'horse-tools'); ?></p>
				<p>
				<textarea style="height:90px;" class="ht-code-textarea" name="horsetools_ads_settings[ads-sense-c3]" placeholder="<?php _e('Add ads to custom post placement', 'horse-tools'); ?>"><?php if(!empty($horsetools_ads_options['ads-sense-c3'])){echo esc_textarea($horsetools_ads_options['ads-sense-c3']);} ?></textarea>
				</p>
			</div>
			</div>
			<!-- ADSTXT -->
			<div class="sotab-box htbox" id="tab3" style="display:none">
			<h2><?php _e('ADS.TXT', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="fa-regular fa-file-check"></i> <?php _e('Set up the ads.txt file', 'horse-tools') ?></h3>
				<p>
				<label class="nut-switch">
				<input type="checkbox" name="horsetools_ads_settings[ads-adstxt1]" value="1" <?php if ( isset($horsetools_ads_options['ads-adstxt1']) && 1 == $horsetools_ads_options['ads-adstxt1'] ) echo 'checked="checked"'; ?> />
				<span class="slider"></span></label>
				<label class="ht-label-right"><?php _e('Enable ads.txt', 'horse-tools'); ?></label>
				</p>
				<p class="ht-note"><i class="fa-regular fa-lightbulb-on"></i> <?php _e('You can preview your file here:', 'horse-tools'); echo ' <a target="_blank" href="' . esc_url(home_url( '/ads.txt' )) . '">ads.txt</a>'; ?><br>
				<?php _e('For an Nginx server, if the ads.txt file already exists in the root directory of the website, it will prioritize the static file, so this function will not work. If you want to use it, you can either configure Nginx or delete the static file before proceeding', 'horse-tools'); ?>
				</p>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_ads_settings[ads-adstxt2]" placeholder="<?php _e('Enter code here', 'horse-tools'); ?>"><?php if(!empty($horsetools_ads_options['ads-adstxt2'])){echo esc_textarea($horsetools_ads_options['ads-adstxt2']);} ?></textarea>
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
function horsetools_ads_options_link() {
	add_submenu_page ('horsetools-options', 'Ads', '<i class="fa-regular fa-rectangle-ad" style="width:20px;"></i> '. __('Ads', 'horse-tools'), 'manage_options', 'horsetools-ads-options', 'horsetools_ads_options_page');
}
add_action('admin_menu', 'horsetools_ads_options_link');
function horsetools_ads_register_settings() {
	register_setting( 'horsetools_ads_settings_group', 'horsetools_ads_settings', array( 'sanitize_callback' => 'horsetools_sanitize_ads' ) );
}
add_action('admin_init', 'horsetools_ads_register_settings');
// clear cache
function horsetools_ads_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_ads_settings', 'options');
}
add_action('update_option_horsetools_ads_settings', 'horsetools_ads_settings_cache', 10, 2);

