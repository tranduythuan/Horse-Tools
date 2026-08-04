<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_notify_options; ?>
			<h2><?php _e('POPUP', 'horse-tools'); ?></h2>
			<div class="ht-card">
			   <h3><i class="ti ti-app-window"></i> <?php _e('Create an outstanding popup', 'horse-tools') ?></h3>
				<div class="ht-howto"><i class="ti ti-info-circle"></i><span><?php _e( 'Shows an eye-catching popup in the middle of the screen when someone opens your site — great for a sale, an announcement or a poster. To use: turn on the switch, pick a layout below, add an image / title / content, then set how many hours before it shows again.', 'horse-tools' ); ?></span></div>
				<?php horsetools_toggle( 'notify-popup1', __( 'Enable popup', 'horse-tools' ), array(
					'module'  => 'notify',
					'tab'     => 'POPUP',
					'section' => 'Create an outstanding popup',
				) ); ?>
				<p>
				<input class="ht-input-small" name="horsetools_notify_settings[notify-popup-c1]" type="number" value="<?php if(!empty($horsetools_notify_options['notify-popup-c1'])){echo sanitize_text_field($horsetools_notify_options['notify-popup-c1']);} ?>"/>
				<label class="ht-label-right"><?php _e('Popup save time (.. hours)', 'horse-tools'); ?></label>
				</p>
				<?php
				$ht_pop_anim = !empty($horsetools_notify_options['notify-popup-anim']) ? $horsetools_notify_options['notify-popup-anim'] : 'fade';
				$ht_pop_pos  = !empty($horsetools_notify_options['notify-popup-pos'])  ? $horsetools_notify_options['notify-popup-pos']  : 'center';
				$ht_pop_trig = !empty($horsetools_notify_options['notify-popup-trig']) ? $horsetools_notify_options['notify-popup-trig'] : 'load';
				$ht_pop_anims = array(
					'fade' => __('Fade in', 'horse-tools'),
					'zoom' => __('Zoom in', 'horse-tools'),
					'zoom-out' => __('Zoom out', 'horse-tools'),
					'pop' => __('Pop', 'horse-tools'),
					'slide-up' => __('Slide up', 'horse-tools'),
					'slide-down' => __('Slide down', 'horse-tools'),
					'slide-left' => __('Slide in from right', 'horse-tools'),
					'slide-right' => __('Slide in from left', 'horse-tools'),
					'bounce' => __('Bounce', 'horse-tools'),
					'swing' => __('Swing', 'horse-tools'),
					'rotate' => __('Rotate in', 'horse-tools'),
					'flip' => __('Flip', 'horse-tools'),
					'blur' => __('Sharpen (blur to clear)', 'horse-tools'),
				);
				$ht_pop_poss  = array( 'center' => __('Centre of screen', 'horse-tools'), 'toast-br' => __('Corner — bottom right', 'horse-tools'), 'toast-bl' => __('Corner — bottom left', 'horse-tools'), 'bar-bottom' => __('Bar across the bottom', 'horse-tools') );
				$ht_pop_trigs = array( 'load' => __('As soon as the page opens', 'horse-tools'), 'delay' => __('After a few seconds', 'horse-tools'), 'scroll' => __('After scrolling down', 'horse-tools'), 'exit' => __('When about to leave the page', 'horse-tools') );
				?>
				<p>
				<select name="horsetools_notify_settings[notify-popup-anim]">
				<?php foreach ($ht_pop_anims as $k => $v) { echo '<option value="'. esc_attr($k) .'"'. selected($ht_pop_anim, $k, false) .'>'. esc_html($v) .'</option>'; } ?>
				</select>
				<label class="ht-right-text"><?php _e('Entrance effect', 'horse-tools'); ?></label>
				</p>
				<p>
				<select name="horsetools_notify_settings[notify-popup-pos]">
				<?php foreach ($ht_pop_poss as $k => $v) { echo '<option value="'. esc_attr($k) .'"'. selected($ht_pop_pos, $k, false) .'>'. esc_html($v) .'</option>'; } ?>
				</select>
				<label class="ht-right-text"><?php _e('Where it appears', 'horse-tools'); ?></label>
				</p>
				<p>
				<select name="horsetools_notify_settings[notify-popup-trig]">
				<?php foreach ($ht_pop_trigs as $k => $v) { echo '<option value="'. esc_attr($k) .'"'. selected($ht_pop_trig, $k, false) .'>'. esc_html($v) .'</option>'; } ?>
				</select>
				<label class="ht-right-text"><?php _e('When it appears', 'horse-tools'); ?></label>
				</p>
				<p>
				<input class="ht-input-small" name="horsetools_notify_settings[notify-popup-trigval]" type="number" min="0" value="<?php if(isset($horsetools_notify_options['notify-popup-trigval'])){echo (int) $horsetools_notify_options['notify-popup-trigval'];} ?>"/>
				<label class="ht-label-right"><?php _e('Seconds to wait, or scroll percent (used by the two options above)', 'horse-tools'); ?></label>
				</p>
				<?php horsetools_toggle( 'notify-popup-attn', __( 'Wiggle now and then to catch the eye', 'horse-tools' ), array(
					'module'  => 'notify',
					'tab'     => 'POPUP',
					'section' => 'Create an outstanding popup',
				) ); ?>
				<div id="ht-imgstyle" class="ht-imgstyle ht-imgstyle4">
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/popup/1.png'); ?>" data-value="1" class="<?php if(isset($horsetools_notify_options['notify-popup-c2']) && $horsetools_notify_options['notify-popup-c2'] == '1') echo 'selected'; ?>" />
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/popup/2.png'); ?>" data-value="2" class="<?php if(isset($horsetools_notify_options['notify-popup-c2']) && $horsetools_notify_options['notify-popup-c2'] == '2') echo 'selected'; ?>" />
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/popup/3.png'); ?>" data-value="3" class="<?php if(isset($horsetools_notify_options['notify-popup-c2']) && $horsetools_notify_options['notify-popup-c2'] == '3') echo 'selected'; ?>" />
					<img src="<?php echo esc_url(HORSETOOLS_URL .'img/popup/4.png'); ?>" data-value="4" class="<?php if(isset($horsetools_notify_options['notify-popup-c2']) && $horsetools_notify_options['notify-popup-c2'] == '4') echo 'selected'; ?>" />
				</div>
				<input type="hidden" name="horsetools_notify_settings[notify-popup-c2]" id="cutop11" value="<?php if(!empty($horsetools_notify_options['notify-popup-c2'])){echo sanitize_text_field($horsetools_notify_options['notify-popup-c2']);} else {echo sanitize_text_field('1');} ?>" />
				<script>
					document.addEventListener("DOMContentLoaded", function() {
						var imgStyles = document.querySelectorAll('#ht-imgstyle img');
						imgStyles.forEach(function(img) {
							img.addEventListener('click', function() {
								var selectedStyle = this.getAttribute('data-value');
								document.getElementById('cutop11').value = selectedStyle;
								imgStyles.forEach(function(img) {
									img.classList.remove('selected');
								});
								this.classList.add('selected');
							});
						});
					});
				</script>
				<p style="display:flex;">
				<input id="ht-add1" class="ht-input-big" name="horsetools_notify_settings[notify-popup11]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-popup11'])){echo sanitize_text_field($horsetools_notify_options['notify-popup11']);} ?>" placeholder="<?php _e('Add images', 'horse-tools'); ?>" />
				<button class="ht-selec" data-input-id="ht-add1"><?php _e('Select image', 'horse-tools'); ?></button>
				</p>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter title', 'horse-tools') ?>" name="horsetools_notify_settings[notify-popup12]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-popup12'])){echo sanitize_text_field($horsetools_notify_options['notify-popup12']);} ?>"/>
				</p>
				<div class="ht-classic">
				<?php
				$popup_editor = !empty($horsetools_notify_options['notify-popup13']) ? wp_kses_post($horsetools_notify_options['notify-popup13']) : '';
				ob_start();
				wp_editor(
					$popup_editor,
					'userpostcontent',
					array(
						'textarea_name' => 'horsetools_notify_settings[notify-popup13]',
						'media_buttons' => false,
					)
				);
				$editor_contents = ob_get_clean();
				echo $editor_contents;
				?>
				</div>
				<p>
				<input class="ht-input-big" placeholder="<?php _e('Enter image link', 'horse-tools') ?>" name="horsetools_notify_settings[notify-popup14]" type="text" value="<?php if(!empty($horsetools_notify_options['notify-popup14'])){echo sanitize_text_field($horsetools_notify_options['notify-popup14']);} ?>"/>
				</p>
				
				<p class="ht-keo">
				<input type="range" name="horsetools_notify_settings[notify-popup-m1]" min="1" max="50" value="<?php if(!empty($horsetools_notify_options['notify-popup-m1'])){echo sanitize_text_field($horsetools_notify_options['notify-popup-m1']);} else { echo sanitize_text_field('10');} ?>" class="htslide" data-index="1">
				<span><?php _e('Border radius', 'horse-tools'); ?> <span id="demo1"></span> PX</span>
				</p>
				
				<p class="ht-keo">
				<input type="range" name="horsetools_notify_settings[notify-popup-m2]" min="300" max="1000" value="<?php if(!empty($horsetools_notify_options['notify-popup-m2'])){echo sanitize_text_field($horsetools_notify_options['notify-popup-m2']);} else { echo sanitize_text_field('800');} ?>" class="htslide" data-index="2">
				<span><?php _e('Max width', 'horse-tools'); ?> <span id="demo2"></span> PX</span>
				</p>
				
				<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Enter the content you want to display and configure the customizations above so the popup can appear when users visit your website', 'horse-tools'); ?></p> 				
			</div>
