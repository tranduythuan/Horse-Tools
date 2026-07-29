<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_code_options_page() {
	global $horsetools_code_options;
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="ti ti-brand-css3"></i> <?php _e('CSS', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="ti ti-code"></i> <?php _e('WP HEAD', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab3')"><i class="ti ti-code"></i> <?php _e('WP BODY', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab4')"><i class="ti ti-code"></i> <?php _e('WP FOOTER', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab5')"><i class="ti ti-login"></i> <?php _e('WP LOGIN', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php'); 
			}
			$htnote = '<div class="htnotecode"><span><i class="ti ti-arrow-left"></i> Ctrl-Z, Cmd-Z: Undo</span><span><i class="ti ti-arrow-right"></i> Ctrl-Y, Cmd-Y: Redo</span> <span><i class="ti ti-search"></i> Ctrl-F, Cmd-F: Find</span><span><i class="ti ti-filter"></i> Ctrl-H, Cmd-H: Replace</span></div>';
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_code_settings_group'); ?> 
			<!-- CSS -->
			<div class="sotab-box htbox" id="tab1">
			<h2><?php _e('CSS', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-code"></i> <?php _e('Add CSS to your website', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code1]" placeholder="<?php _e('Enter CSS here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code1'])){echo esc_textarea($horsetools_code_options['code1']);} ?></textarea>
				</p>
			  <h3><i class="ti ti-code"></i> <?php _e('Add CSS for tablet size', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code11]" placeholder="<?php _e('Enter CSS here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code11'])){echo esc_textarea($horsetools_code_options['code11']);} ?></textarea>
				</p>
			  <h3><i class="ti ti-code"></i> <?php _e('Add CSS for mobile size', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code12]" placeholder="<?php _e('Enter CSS here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code12'])){echo esc_textarea($horsetools_code_options['code12']);} ?></textarea>
				</p>
			</div>	
			</div>
			<!-- Javascript 1 -->
			<div class="sotab-box htbox" id="tab2" style="display:none">
			<h2><?php _e('WP HEAD', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-code"></i> <?php _e('Add code to WP head', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code2]" placeholder="<?php _e('Enter code here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code2'])){echo esc_textarea($horsetools_code_options['code2']);} ?></textarea>
				</p>
			</div>
			</div>
			<!-- Javascript 2 -->
			<div class="sotab-box htbox" id="tab3" style="display:none">
			<h2><?php _e('WP BODY', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-code"></i> <?php _e('Add code to WP body', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code3]" placeholder="<?php _e('Enter code here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code3'])){echo esc_textarea($horsetools_code_options['code3']);} ?></textarea>
				</p>
			</div>
			</div>
			<!-- Javascript 3 -->
			<div class="sotab-box htbox" id="tab4" style="display:none">
			<h2><?php _e('WP FOOTER', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-code"></i> <?php _e('Add code to WP footer', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code4]" placeholder="<?php _e('Enter code here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code4'])){echo esc_textarea($horsetools_code_options['code4']);} ?></textarea>
				</p>
			</div>	
			</div>
			<!-- login 1 -->
			<div class="sotab-box htbox" id="tab5" style="display:none">
			<h2><?php _e('WP LOGIN', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-code"></i> <?php _e('Add code to WP login', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code5]" placeholder="<?php _e('Enter code here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code5'])){echo esc_textarea($horsetools_code_options['code5']);} ?></textarea>
				</p>
			</div>	
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
	<script>
	jQuery(document).ready(function($) {
		$('#ht-save-fast').click(function(e) {
			e.preventDefault(); 
			var currentForm = $(this).closest('form');
			$.ajax({
				type: 'POST',
				url: currentForm.attr('action'),  
				data: currentForm.serialize(),   
				success: function(response) {
					$('#ht-save-fast').html('<i class="ti ti-check"></i>');
					setTimeout(function() {
						$('#ht-save-fast').html('<i class="ti ti-device-floppy"></i>');
					}, 1000);
					// This is a deliberate quick-save, unlike the silent
					// auto-save handlers that were removed. Tell the
					// unsaved-changes bar so it stops warning about work that
					// has in fact just been saved.
					document.dispatchEvent(new CustomEvent('horsetools:saved'));
				},
				error: function() {
					$('#ht-save-fast').html('<i class="ti ti-alert-triangle"></i>');
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
function horsetools_code_options_link() {
	add_submenu_page ('horsetools-options', 'Code', '<i class="ti ti-code" style="width:20px;"></i> '. __('Add code', 'horse-tools'), 'manage_options', 'horsetools-code-options', 'horsetools_code_options_page');
}
add_action('admin_menu', 'horsetools_code_options_link');
function horsetools_code_register_settings() {
	register_setting( 'horsetools_code_settings_group', 'horsetools_code_settings', array( 'sanitize_callback' => 'horsetools_sanitize_code' ) );
}
add_action('admin_init', 'horsetools_code_register_settings');
// clear cache
function horsetools_code_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_code_settings', 'options');
}
add_action('update_option_horsetools_code_settings', 'horsetools_code_settings_cache', 10, 2);

