<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_gindex_options; ?>
			<h2><?php _e('INDEX', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-hand-finger"></i> <?php _e('Enter manually', 'horse-tools') ?></h3>
			    
				<?php 
				// bo dem requet tông
				if (is_array($horsetools_gindex_options) || is_object($horsetools_gindex_options)) {
					$count = 0; 
					foreach ($horsetools_gindex_options as $key => $value) {
						if (preg_match('/^json(\d+)$/', $key, $matches)) {
							$count++;
						}
					}
					$data_t = $count * 200;
					$data_i = !empty(get_transient('horsetools_index_count')) ? get_transient('horsetools_index_count') : 0;
					if ($data_i >= $data_t) {
						$data_full = 0;
					} else {
						$data_full = $data_t - $data_i;
					}

					echo '<div class="ht-index-count">';
					echo '<span>'. __('Total:', 'horse-tools') .' '. $data_t .'</span>';
					echo '<span>'. __('Use:', 'horse-tools') .' '. $data_i .'</span>';
					echo '<span>'. __('Still:', 'horse-tools') .' '. $data_full .'</span>';
					echo '</div>';
				}
				?>
				<textarea style="height:500px" class="ht-code-textarea" name="horsetools_gindex_settings[url]" placeholder="<?php _e('Enter the url', 'horse-tools'); ?>"></textarea>
				<div class="ht-index-nut">
					<span class="index-action" data-action="update"><i class="ti ti-player-play"></i> <?php _e('INDEX', 'horse-tools'); ?></span>
					<span class="index-action" data-action="delete"><i class="ti ti-trash"></i> <?php _e('DEL', 'horse-tools'); ?></span>
					<span class="index-action-check" ><i class="ti ti-circle-check"></i> <?php _e('CHECK', 'horse-tools'); ?></span>
				</div>
				<div class="emed" style="display:none"><div class="ht-sload"></div> <?php _e('Please wait', 'horse-tools'); ?></div>
				<div id="index-bao"></div>
			</div>	
