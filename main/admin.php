<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_options_page() {
	global $horsetools_options;
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="ti ti-gauge"></i> <?php _e('OPTIMIZE', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="ti ti-shield-half"></i> <?php _e('SECURITY', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab3')"><i class="ti ti-tools"></i> <?php _e('TOOL', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab4')"><i class="ti ti-device-desktop"></i> <?php _e('DISPLAY', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab5')"><i class="ti ti-photo"></i> <?php _e('MEDIA', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab6')"><i class="ti ti-notes"></i> <?php _e('CONTENT', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab7')"><i class="ti ti-mail"></i> <?php _e('MAIL', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab8')"><i class="ti ti-shopping-cart"></i> <?php _e('WOO', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab9')"><i class="ti ti-user"></i> <?php _e('USER', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab10')"><i class="ti ti-brand-wordpress"></i> <?php _e('CUSTOM', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab11')"><i class="ti ti-brand-google"></i> <?php _e('GOOGLE', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab12')"><i class="ti ti-message"></i> <?php _e('CHAT', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab-ft1')"><i class="ti ti-settings"></i> <?php _e('SETTING', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php if( isset($_GET['settings-updated']) ) {
				require_once( HORSETOOLS_DIR . 'main/completed.php');
			} ?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_settings_group'); ?> 
			<!-- trang toi uu -->
			<div class="sotab-box htbox" id="tab1">
				<?php include( HORSETOOLS_DIR . 'main/page/1speed.php'); ?>
			</div>
			<!-- trang bao mat -->
			<div class="sotab-box htbox" id="tab2" style="display:none">	
				<?php include( HORSETOOLS_DIR . 'main/page/2scuri.php'); ?>
			</div>
			<!-- trang cong cu -->
			<div class="sotab-box htbox" id="tab3" style="display:none">	
				<?php include( HORSETOOLS_DIR . 'main/page/3tool.php'); ?>
			</div>
			<!-- trang hien thi -->
			<div class="sotab-box htbox" id="tab4" style="display:none">
				<?php include( HORSETOOLS_DIR . 'main/page/4main.php'); ?>
			</div>
			<!-- trang media -->
			<div class="sotab-box htbox" id="tab5" style="display:none">	
				<?php include( HORSETOOLS_DIR . 'main/page/5media.php'); ?>
			</div>
			<!-- trang chuyen muc bai viet -->
			<div class="sotab-box htbox" id="tab6" style="display:none">	
				<?php include( HORSETOOLS_DIR . 'main/page/6post.php'); ?>
			</div>
			<!-- trang mail -->
			<div class="sotab-box htbox" id="tab7" style="display:none">	
				<?php include( HORSETOOLS_DIR . 'main/page/7mail.php'); ?>
			</div>
			<!-- trang woocommere -->
			<div class="sotab-box htbox" id="tab8" style="display:none">	
				<?php include( HORSETOOLS_DIR . 'main/page/8woo.php'); ?>
			</div>
			<!-- trang user -->
			<div class="sotab-box htbox" id="tab9" style="display:none">	
				<?php include( HORSETOOLS_DIR . 'main/page/9user.php'); ?>
			</div>
			<!-- trang custom -->
			<div class="sotab-box htbox" id="tab10" style="display:none">
				<?php include( HORSETOOLS_DIR . 'main/page/10custom.php'); ?>
			</div>
			<!-- trang goole -->
			<div class="sotab-box htbox" id="tab11" style="display:none">
				<?php include( HORSETOOLS_DIR . 'main/page/11google.php'); ?>
			</div>
			<!-- trang chat -->
			<div class="sotab-box htbox" id="tab12" style="display:none">
				<?php include( HORSETOOLS_DIR . 'main/page/12chat.php'); ?>
			</div>
			<!-- trang cai dat -->
			<div class="sotab-box htbox" id="tab-ft1" style="display:none">
				<?php include( HORSETOOLS_DIR . 'main/page/ht-setting.php'); ?> 
			</div>
			<div class="ht-submit">
				<button type="submit"><i class="ti ti-device-floppy"></i> <?php _e('SAVE CONTENT', 'horse-tools'); ?></button>
			</div>
				<button id="ht-save-fast" type="submit"><i class="ti ti-device-floppy"></i></button>
			</form>
		</div>
		
	  </div>
	  <div class="ht-sidebar">
	  </div>
	</div>	
	</div>
	<?php
	// style horsetools
	require_once( HORSETOOLS_DIR . 'main/style.php');
	echo ob_get_clean();
}
function horsetools_tool_add_options_link() {
	global $horsetools_options;
	$icon = horsetools_icon();
	$name = !empty($horsetools_options['horsetools6']) ? $horsetools_options['horsetools6'] : 'Horse Tools';
	add_menu_page($name, $name, 'manage_options', 'horsetools-options', 'horsetools_options_page', $icon, 70);
	add_submenu_page('horsetools-options', $name, '<i class="ti ti-adjustments" style="width:20px;"></i> '. __('Overview', 'horse-tools'), 'manage_options', 'horsetools-options');
}
add_action('admin_menu', 'horsetools_tool_add_options_link');
function horsetools_tool_register_settings() {
	register_setting( 'horsetools_settings_group', 'horsetools_settings', array( 'sanitize_callback' => 'horsetools_sanitize_main' ) );
}
add_action('admin_init', 'horsetools_tool_register_settings');
// clear cache
function horsetools_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_settings', 'options');
}
add_action('update_option_horsetools_settings', 'horsetools_settings_cache', 10, 2);

