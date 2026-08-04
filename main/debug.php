<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_debug_options_page() {
	global $horsetools_debug_options;
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
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="ti ti-terminal-2"></i> <?php _e('DEBUG', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php');   
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_debug_settings_group'); ?> 
			<!-- DEBUG -->
			<div class="sotab-box htbox" id="tab1">
			<h2><?php _e('DEBUG', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-bug-off"></i> <?php _e('Debug settings', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'debug1', __( 'Enable WP_DEBUG', 'horse-tools' ), array(
					'module'  => 'debug',
					'tab'     => 'DEBUG',
					'section' => 'Debug settings',
				) ); ?>
				<?php horsetools_toggle( 'debug2', __( 'Enable WP_DEBUG_LOG', 'horse-tools' ), array(
					'module'  => 'debug',
					'tab'     => 'DEBUG',
					'section' => 'Debug settings',
				) ); ?>
				<?php horsetools_toggle( 'debug3', __( 'Enable WP_DEBUG_DISPLAY', 'horse-tools' ), array(
					'module'  => 'debug',
					'tab'     => 'DEBUG',
					'section' => 'Debug settings',
				) ); ?>
				<?php
				if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
				$debug_log_path = WP_CONTENT_DIR . '/debug.log';
				if (file_exists($debug_log_path)) { ?>
						<p>
						<div class="ht-pre-tit"><span></span><span></span><span></span></div>
						<div class="ht-pre-me">
							<a class="delete-debug" href="javascript:void(0)" id="delete-debug"><i class="ti ti-trash"></i> <?php _e('Clear all', 'horse-tools'); ?></a>
							<a class="delete-debug" href="javascript:void(0)" id="load-debug"><i class="ti ti-refresh"></i> <?php _e('Refresh', 'horse-tools'); ?></a>
						</div>
						<div id="delete-debug-not"></div>
						<textarea id="debug-log-content" class="ht-pre"></textarea>
						</p>
						<?php
					} else {
						echo '<div class="ebug">'. __('The debug file does not exist', 'horse-tools') .'</div>';
					}
				} else {
					echo '<div class="ebug">'. __('Debug is not enabled', 'horse-tools') .'</div>';
				}
				?>  
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
		$('#delete-debug').on('click', function(e) {
			var ajax_nonce = '<?php echo wp_create_nonce('horsetools_nonce_deldebug'); ?>';
			e.preventDefault();
			var data = {
				'action': 'horsetools_clear_debug_log',
				'security': ajax_nonce,
			};
			$.post(ajaxurl, data, function(response) {
				if (response.success) {
					$('#delete-debug-not').html('<span><?php _e("Cleaned the screen", "horse-tools"); ?></span>');
					loadDebugLog();
				} else {
					$('#delete-debug-not').html('<span><?php _e("Error cannot be cleaned", "horse-tools"); ?></span>');
				}
			});
		});
		$('#load-debug').on('click', function(e) {
			e.preventDefault();
			$('#delete-debug-not').html('<span><?php _e("Screen refreshed", "horse-tools"); ?></span>');
			loadDebugLog(); 
		});
		function loadDebugLog(){
			var ajax_nonce = '<?php echo wp_create_nonce('horsetools_nonce_getdebug'); ?>';
			var data = {
				action: 'horsetools_get_debug_log',
				security: ajax_nonce
			};
			$.post(ajaxurl, data, function(response) {
				if (response.success) {
					$('#debug-log-content').val(response.data);
				} else {
					alert(response.data);
				}
			});
		}
		loadDebugLog();
	});
	</script>
	<?php
	// style horsetools
	require_once( HORSETOOLS_DIR . 'main/style.php');
	echo ob_get_clean();
}
function horsetools_debug_options_link() {
	add_submenu_page ('horsetools-options', 'Debug', '<i class="ti ti-settings" style="width:20px;"></i> '. __('Debug', 'horse-tools'), 'manage_options', 'horsetools-debug-options', 'horsetools_debug_options_page');
}
// Menu removed in 1.2.77: now tabs on a grouped screen.
// add_action('admin_menu', 'horsetools_debug_options_link');
function horsetools_debug_register_settings() {
	register_setting( 'horsetools_debug_settings_group', 'horsetools_debug_settings', array( 'sanitize_callback' => 'horsetools_sanitize_debug' ) );
}
add_action('admin_init', 'horsetools_debug_register_settings');
// clear cache
function horsetools_debug_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_debug_settings', 'options');
}
add_action('update_option_horsetools_debug_settings', 'horsetools_debug_settings_cache', 10, 2);









