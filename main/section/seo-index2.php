<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_gindex_options; ?>
			<h3>Google API settings</h3>
			<div class="ht-card">
			  <h3><i class="ti ti-settings"></i> <?php _e('Configure Google index API', 'horse-tools') ?></h3>
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
						<input type="checkbox" name="horsetools_gindex_settings[posttype][]" value="<?php echo $post_type_object->name; ?>" <?php if (isset($horsetools_gindex_options['posttype']) && in_array($post_type_object->name, $horsetools_gindex_options['posttype'])) echo 'checked="checked"'; ?> />
						<span class="slider"></span>
					</label>
					<label class="ht-label-right"><?php echo $post_type_object->labels->name; ?></label>
					</p>
					<?php
				}
				?>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('The "Index Now" link will be added in the custom post management section to allow quick index submission', 'horse-tools'); ?></p>
				<h4><?php _e('Add Google json code', 'horse-tools') ?></h4>
				<div id="sortable-list">
				<div data-id="1" class="ui-state-default ht-button-grid">
				<textarea style="height:200px" class="ht-code-textarea" name="horsetools_gindex_settings[json1]" placeholder="<?php _e('Enter json', 'horse-tools'); ?>"><?php if(!empty($horsetools_gindex_options['json1'])){echo esc_textarea($horsetools_gindex_options['json1']);} ?></textarea>
				</div>
				<?php
				if (is_array($horsetools_gindex_options) || is_object($horsetools_gindex_options)) {
					foreach ($horsetools_gindex_options as $key => $value) {
						if (preg_match('/^json(\d+)$/', $key, $matches) && $matches[1] != 1) {
							$n = $matches[1];
							echo '<div data-id="' . $n . '" class="ui-state-default ht-button-grid">';
							echo '<textarea style="height:200px" class="ht-code-textarea" placeholder="'. __('Enter json', 'horse-tools') .'" type="text" name="horsetools_gindex_settings[json' . $n . ']">' . sanitize_text_field($horsetools_gindex_options['json' . $n]) . '</textarea>';
							echo '<span id="ht-chatx">&#x2715</span>';
							echo '</div>';
						}
					}
				}
				?>
				</div>
				<span id="ht-chatmore"><i class="ti ti-plus"></i> <?php _e('Add json', 'horse-tools'); ?></span>
			</div>
