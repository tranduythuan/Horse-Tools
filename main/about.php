<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_about_options_page() {
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="fa-regular fa-bomb"></i> <?php _e('ABOUT', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="fa-regular fa-database"></i> <?php _e('DATABASE', 'horse-tools'); ?></button>
		</div>
		<div class="ht-main">
			<!-- about web -->
			<div class="sotab-box htbox" id="tab1" style="margin-bottom:-60px;">
				<?php include( HORSETOOLS_DIR . 'main/page/ht-about1.php'); ?> 
			</div>
			<!-- about database -->
			<div class="sotab-box htbox" id="tab2" style="display:none;margin-bottom:-60px;">
				<?php include( HORSETOOLS_DIR . 'main/page/ht-about2.php'); ?> 
			</div>
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
function horsetools_about_options_link() {
	add_submenu_page ('horsetools-options', 'About', '<i class="fa-regular fa-bomb" style="width:20px;"></i> '. __('About', 'horse-tools'), 'manage_options', 'horsetools-about-options', 'horsetools_about_options_page');
}
add_action('admin_menu', 'horsetools_about_options_link');

