<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_shortcode_options; ?>
			<h2><?php _e('LOCKVIP', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="ti ti-lock"></i> <?php _e('Shortcode content visible only to group of users', 'horse-tools') ?></h3>
				<?php horsetools_toggle( 'shortcode-s1', __( 'Enable shortcode lock', 'horse-tools' ), array(
					'module'  => 'shortcode',
					'tab'     => 'LOCKVIP',
					'section' => 'Shortcode content visible only to group of users',
				) ); ?>
				<?php
				$roles = get_editable_roles();
				echo '<select name="horsetools_shortcode_settings[shortcode-s11]">';
				echo '<option value="">Default</option>';
				foreach ($roles as $role_name => $role_info) {
					if ( true ) { // all roles selectable; horsetools_user_meets_role() ranks them
					if(isset($horsetools_shortcode_options['shortcode-s11']) && $horsetools_shortcode_options['shortcode-s11'] == $role_name) { $selected = 'selected="selected"'; } else { $selected = NULL; }
					echo '<option value="'. $role_name .'" '. $selected .'>'. $role_name .'</option>';
					}
				}
				echo '</select>';
				?>
				<label class="ht-right-text"><?php _e('Role', 'horse-tools'); ?></label>
				<p>
				<textarea style="height:100px;" class="ht-code-textarea" name="horsetools_shortcode_settings[shortcode-s12]" placeholder="<?php _e('Enter note content', 'horse-tools'); ?>"><?php if(!empty($horsetools_shortcode_options['shortcode-s12'])){echo esc_textarea($horsetools_shortcode_options['shortcode-s12']);} ?></textarea>
				</p>
				<input class="ht-input-big ht-view-in" type="text" value="[vip] <?php _e('Content to be hidden', 'horse-tools'); ?> [/vip]"/>
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('This shortcode allows you to lock any content, and only the selected group of logged-in users can view it', 'horse-tools'); ?></p>     
			</div>
