<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_debug_options; ?>
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
				$debug_log_path = function_exists( 'horsetools_debug_log_current' ) ? horsetools_debug_log_current() : WP_CONTENT_DIR . '/debug.log';
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
