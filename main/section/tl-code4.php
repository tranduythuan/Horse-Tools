<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_code_options;
// Set in the body of the screen this section came from.
$htnote = '<div class="htnotecode"><span><i class="ti ti-arrow-left"></i> Ctrl-Z, Cmd-Z: Undo</span><span><i class="ti ti-arrow-right"></i> Ctrl-Y, Cmd-Y: Redo</span> <span><i class="ti ti-search"></i> Ctrl-F, Cmd-F: Find</span><span><i class="ti ti-filter"></i> Ctrl-H, Cmd-H: Replace</span></div>';
?>
			<h2><?php _e('WP FOOTER', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-code"></i> <?php _e('Add code to WP footer', 'horse-tools') ?></h3>
				<?php echo $htnote; ?>
				<p>
				<textarea class="ht-code-textarea ht-dev" name="horsetools_code_settings[code4]" placeholder="<?php _e('Enter code here', 'horse-tools'); ?>"><?php if(!empty($horsetools_code_options['code4'])){echo esc_textarea($horsetools_code_options['code4']);} ?></textarea>
				</p>
			</div>	
