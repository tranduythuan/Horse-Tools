<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<h2><?php _e('CHAT', 'horse-tools'); ?></h2>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check12" data-target="play12" type="checkbox" name="horsetools_settings[chat]" value="1" <?php if ( isset($horsetools_options['chat']) && 1 == $horsetools_options['chat'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play12" class="ht-card toggle-div">
	<datalist id="ht-ti-names">
		<?php foreach ( array('snowflake','gift','discount-2','rosette-discount-check','star','heart','flame','bolt','clock','phone','mail','map-pin','shopping-cart','message','truck','tag','bell','calendar','camera','home','user','sparkles','headset','brand-facebook','brand-instagram','brand-youtube') as $tiname ) { echo '<option value="'. esc_attr($tiname) .'"></option>'; } ?>
	</datalist>
	<details class="ht-guide">
		<summary><i class="ti ti-help-circle"></i> <?php _e('Quick guide — how the chat feature works', 'horse-tools'); ?></summary>
		<div class="ht-guide-body">
			<div class="ht-guide-step"><span class="ht-guide-n">1</span><div><b><?php _e('Pick a button skin', 'horse-tools'); ?></b> — <?php _e('choose how the floating button looks from the visual grid below (17 styles). What you see is what visitors get.', 'horse-tools'); ?></div></div>
			<div class="ht-guide-step"><span class="ht-guide-n">2</span><div><b><?php _e('Add contact channels', 'horse-tools'); ?></b> — <?php _e('pick a channel (Zalo, Messenger, Phone…) and its logo is added automatically. For a custom button, choose a built-in icon or paste your own SVG.', 'horse-tools'); ?></div></div>
			<div class="ht-guide-step"><span class="ht-guide-n">3</span><div><b><?php _e('Services panel (mobile)', 'horse-tools'); ?></b> — <?php _e('a panel that slides up to show your services or articles. Pick its layout, colour and how it appears from the visual grids. To open it, point a bottom-bar item at', 'horse-tools'); ?> <code>#ht-services</code>.</div></div>
			<div class="ht-guide-step"><span class="ht-guide-n">4</span><div><b><?php _e('Contact bar on mobile', 'horse-tools'); ?></b> — <?php _e('a bar pinned to the bottom of the phone screen. Choose one of 5 styles from the grid.', 'horse-tools'); ?></div></div>
			<div class="ht-guide-step"><span class="ht-guide-n">5</span><div><b><?php _e('Extras', 'horse-tools'); ?></b> — <?php _e('a greeting bubble, business hours (shows Online / Away), scan-to-open QR on desktop, and pre-filled WhatsApp / SMS messages.', 'horse-tools'); ?></div></div>
			<div class="ht-guide-step"><span class="ht-guide-n">6</span><div><b><?php _e('Save', 'horse-tools'); ?></b> — <?php _e('click SAVE CONTENT at the bottom of the page. The Services panel has its own “Save services” button.', 'horse-tools'); ?></div></div>
		</div>
	</details>
	<style>
	.ht-guide{border:1px solid #f0e2b8;background:#fffdf5;border-radius:12px;margin:0 0 18px;overflow:hidden}
	.ht-guide>summary{cursor:pointer;list-style:none;padding:13px 16px;font-size:14px;font-weight:600;color:#8a5a00;display:flex;align-items:center;gap:8px}
	.ht-guide>summary::-webkit-details-marker{display:none}
	.ht-guide>summary::after{content:'▾';margin-left:auto;transition:.2s}
	.ht-guide[open]>summary::after{transform:rotate(180deg)}
	.ht-guide-body{padding:4px 16px 14px}
	.ht-guide-step{display:flex;gap:11px;align-items:flex-start;padding:7px 0;font-size:13px;line-height:1.5;border-top:1px solid #f4ecd2}
	.ht-guide-step:first-child{border-top:0}
	.ht-guide-n{flex:0 0 auto;width:22px;height:22px;border-radius:50%;background:#e0a800;color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center}
	</style>
  <h3><i class="ti ti-message"></i> <?php _e('Create a chat feature for users', 'horse-tools') ?></h3>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[chat-nut1]" value="1" <?php if ( isset($horsetools_options['chat-nut1']) && 1 == $horsetools_options['chat-nut1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Enable chat button', 'horse-tools'); ?></label>
	</p>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[chat-nut-js]" value="1" <?php if ( isset($horsetools_options['chat-nut-js']) && 1 == $horsetools_options['chat-nut-js'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Hide chat button when scrolling down', 'horse-tools'); ?></label>
	</p>
	<h4><?php _e('Display options', 'horse-tools'); ?></h4>
	<div id="ht-skin-pick" class="htp-grid"></div>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('The Avatar, Live-chat and Tab-widget skins use the fields below.', 'horse-tools'); ?></p>
	<p><input class="ht-input-big" placeholder="<?php esc_attr_e( 'Avatar image URL (Avatar / Live-chat skin)', 'horse-tools' ); ?>" type="text" name="horsetools_settings[chat-avatar]" value="<?php if(!empty($horsetools_options['chat-avatar'])){echo esc_attr($horsetools_options['chat-avatar']);} ?>" /></p>
	<p><input class="ht-input-big" placeholder="<?php esc_attr_e( 'Greeting message (Live-chat skin)', 'horse-tools' ); ?>" type="text" name="horsetools_settings[chat-greet]" value="<?php if(!empty($horsetools_options['chat-greet'])){echo esc_attr($horsetools_options['chat-greet']);} ?>" /></p>
	<p><textarea style="height:70px" class="ht-code-textarea" name="horsetools_settings[chat-quick]" placeholder="<?php esc_attr_e( 'Quick replies, one per line as  Label|https://link  (Live-chat skin)', 'horse-tools' ); ?>"><?php if(!empty($horsetools_options['chat-quick'])){echo esc_textarea($horsetools_options['chat-quick']);} ?></textarea></p>
	<p><textarea style="height:90px" class="ht-code-textarea" name="horsetools_settings[chat-hours]" placeholder="<?php esc_attr_e( 'Opening hours, one line per day (Tab-widget skin)', 'horse-tools' ); ?>"><?php if(!empty($horsetools_options['chat-hours'])){echo esc_textarea($horsetools_options['chat-hours']);} ?></textarea></p>

	<h4><?php _e('Extras', 'horse-tools'); ?></h4>
	<p><input class="ht-input-big" placeholder="<?php esc_attr_e( 'Pre-filled message for WhatsApp / SMS (optional)', 'horse-tools' ); ?>" type="text" name="horsetools_settings[chat-msg]" value="<?php if(!empty($horsetools_options['chat-msg'])){echo esc_attr($horsetools_options['chat-msg']);} ?>" /></p>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[chat-bubble]" value="1" <?php if ( isset($horsetools_options['chat-bubble']) && 1 == $horsetools_options['chat-bubble'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Show a greeting bubble near the button', 'horse-tools'); ?></label>
	</p>
	<p><input class="ht-input-big" placeholder="<?php esc_attr_e( 'Greeting bubble text (e.g. Hi! Need help?)', 'horse-tools' ); ?>" type="text" name="horsetools_settings[chat-bubble-text]" value="<?php if(!empty($horsetools_options['chat-bubble-text'])){echo esc_attr($horsetools_options['chat-bubble-text']);} ?>" /></p>
	<p>
	<input class="ht-input-small" type="number" min="0" max="60" placeholder="3" name="horsetools_settings[chat-bubble-delay]" value="<?php if(isset($horsetools_options['chat-bubble-delay']) && $horsetools_options['chat-bubble-delay']!==''){echo (int)$horsetools_options['chat-bubble-delay'];} ?>" />
	<label class="ht-label-right"><?php _e('Seconds before the bubble appears', 'horse-tools'); ?></label>
	</p>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[chat-qr]" value="1" <?php if ( isset($horsetools_options['chat-qr']) && 1 == $horsetools_options['chat-qr'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Show a scan-to-open QR on desktop (Zalo, WhatsApp, Telegram, Viber, Line; WeChat always)', 'horse-tools'); ?></label>
	</p>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[chat-bh]" value="1" <?php if ( isset($horsetools_options['chat-bh']) && 1 == $horsetools_options['chat-bh'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Business hours (shows Online / Away on the Live-chat, Card and Avatar skins)', 'horse-tools'); ?></label>
	</p>
	<p>
	<input class="ht-input-small" type="time" name="horsetools_settings[chat-bh-open]" value="<?php echo esc_attr( $horsetools_options['chat-bh-open'] ?? '' ); ?>" />
	<input class="ht-input-small" type="time" name="horsetools_settings[chat-bh-close]" value="<?php echo esc_attr( $horsetools_options['chat-bh-close'] ?? '' ); ?>" />
	<label class="ht-label-right"><?php _e('Open – close time', 'horse-tools'); ?></label>
	</p>
	<p>
	<?php
	$horsetools_bh_days = ( isset($horsetools_options['chat-bh-days']) && is_array($horsetools_options['chat-bh-days']) ) ? array_map('intval', $horsetools_options['chat-bh-days']) : array();
	$horsetools_daynames = array( 1 => __('Mon','horse-tools'), 2 => __('Tue','horse-tools'), 3 => __('Wed','horse-tools'), 4 => __('Thu','horse-tools'), 5 => __('Fri','horse-tools'), 6 => __('Sat','horse-tools'), 0 => __('Sun','horse-tools') );
	foreach ( $horsetools_daynames as $dnum => $dname ) {
		$dchk = in_array($dnum, $horsetools_bh_days, true) ? 'checked="checked"' : '';
		echo '<label class="ht-container" style="display:inline-block;margin:0 12px 6px 0">'. esc_html($dname) .' <input type="checkbox" name="horsetools_settings[chat-bh-days][]" value="'. $dnum .'" '. $dchk .' /><span class="ht-checkmark"></span></label>';
	}
	?>
	</p>
	<input type="hidden" name="horsetools_settings[chat-nut-skin]" id="chat-nut-skin" value="<?php if(!empty($horsetools_options['chat-nut-skin'])){echo sanitize_text_field($horsetools_options['chat-nut-skin']);} else {echo sanitize_text_field('Default');} ?>" />
	<h4><?php _e('Configure buttons', 'horse-tools'); ?></h4>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Pick a channel (Zalo, Messenger, Phone…) and its logo is added automatically — no icon needed.', 'horse-tools'); ?><br>
	<b><?php _e('Custom / Maps:', 'horse-tools'); ?></b> <?php _e('Enter link', 'horse-tools'); ?> · <b><?php _e('Phone, SMS, Messenger, Telegram, Zalo, Whatsapp, Viber, Skype, Tiktok, Mail:', 'horse-tools'); ?></b> <?php _e('Enter ID', 'horse-tools'); ?><br>
	<?php _e('Only for a custom icon: click the SVG box below, then', 'horse-tools'); ?> <button type="button" class="ht-icon-open"><i class="ti ti-mood-smile"></i> <?php _e('Choose a built-in icon', 'horse-tools'); ?></button> <?php _e('— or paste your own SVG.', 'horse-tools'); ?>
	</p>
	<div id="sortable-list">
	<div data-id="1" class="ui-state-default ht-button-grid">
	<?php $styles = array('Custom', 'Phone', 'SMS', 'Zalo', 'Messenger', 'Telegram', 'Whatsapp', 'Viber', 'Line', 'WeChat', 'Signal', 'Shopee', 'Grab', 'Instagram', 'Facebook', 'Threads', 'Tiktok', 'Youtube', 'X', 'Pinterest', 'Linkedin', 'Reddit', 'Twitch', 'Spotify', 'VK', 'Discord', 'Google', 'Skype', 'Mail', 'Maps'); ?>
	<select name="horsetools_settings[chat-nut11]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($horsetools_options['chat-nut11']) && $horsetools_options['chat-nut11'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<div class="ht-button-grid-in">
	<input class="ht-input-big" placeholder="<?php _e('Enter button name', 'horse-tools'); ?>" type="text" name="horsetools_settings[chat-nut21]" value="<?php if(!empty($horsetools_options['chat-nut21'])){echo sanitize_text_field($horsetools_options['chat-nut21']);} ?>" />
	<input class="ht-input-big" placeholder="<?php _e('Enter link', 'horse-tools'); ?>" type="text" name="horsetools_settings[chat-nut31]" value="<?php if(!empty($horsetools_options['chat-nut31'])){echo sanitize_text_field($horsetools_options['chat-nut31']);} ?>" />
	<textarea style="height:40px;" class="ht-code-textarea" name="horsetools_settings[chat-nut41]" placeholder="<?php _e('Enter the SVG icon', 'horse-tools'); ?>"><?php if(!empty($horsetools_options['chat-nut41'])){echo esc_textarea($horsetools_options['chat-nut41']);} ?></textarea>
	</div>
	<div class="fr-move"><i class="ti ti-grip-vertical"></i></div>
	</div>
	<?php
	if (is_array($horsetools_options) || is_object($horsetools_options)) {
		foreach ($horsetools_options as $key => $value) {
			if (preg_match('/^chat-nut1(\d+)$/', $key, $matches) && $matches[1] != 1) {
				$n = $matches[1];
				echo '<div data-id="' . $n . '" class="ui-state-default ht-button-grid">';
				echo '<select name="horsetools_settings[chat-nut1' . $n . ']">';
				foreach ($styles as $style) {
					$selected = ($style == $value) ? 'selected="selected"' : '';
					echo '<option value="' . $style . '" ' . $selected . '>' . $style . '</option>';
				}
				echo '</select>';
				echo '<div class="ht-button-grid-in">';
				echo '<input class="ht-input-big" placeholder="'. __('Enter button name', 'horse-tools') .'" type="text" name="horsetools_settings[chat-nut2' . $n . ']" value="' . sanitize_text_field($horsetools_options['chat-nut2' . $n]) . '" />';
				echo '<input class="ht-input-big" placeholder="'. __('Enter link', 'horse-tools') .'" type="text" name="horsetools_settings[chat-nut3' . $n . ']" value="' . sanitize_text_field($horsetools_options['chat-nut3' . $n]) . '" />';
				echo '<textarea style="height:40px;" class="ht-code-textarea" name="horsetools_settings[chat-nut4' . $n . ']" placeholder="'. __('Enter the SVG icon', 'horse-tools') .'">' . esc_textarea($horsetools_options['chat-nut4' . $n]) . '</textarea>';
				echo '</div>';
				echo '<div class="fr-move"><i class="ti ti-grip-vertical"></i></div><span id="ht-chatx">&#x2715</span>';
				echo '</div>';
			}
		}
	}
	?>
	</div>
	<span id="ht-chatmore"><i class="ti ti-plus"></i> <?php _e('Add field', 'horse-tools'); ?></span>
	<script>
	jQuery(document).ready(function($){
		var count = 0;
		$('#ht-chatmore').click(function() {
			var count = $('#sortable-list .ui-state-default:last').data('id') + 1;
			var newDiv = $('<div data-id="' + count + '" class="ui-state-default ht-button-grid">' +
				'<select name="horsetools_settings[chat-nut1' + count + ']">' +
				'<?php foreach ( $styles as $s ) { echo '<option value="' . esc_attr( $s ) . '">' . esc_html( $s ) . '</option>'; } ?>' +
				'</select>' +
				'<div class="ht-button-grid-in">' +
				'<input class="ht-input-big" placeholder="<?php _e('Enter button name', 'horse-tools'); ?>" type="text" name="horsetools_settings[chat-nut2' + count + ']" />' +
				'<input class="ht-input-big" placeholder="<?php _e('Enter link', 'horse-tools'); ?>" type="text" name="horsetools_settings[chat-nut3' + count + ']" />' +
				'<textarea style="height:40px;" class="ht-code-textarea" name="horsetools_settings[chat-nut4' + count + ']" placeholder="<?php _e('Enter the SVG icon', 'horse-tools'); ?>"></textarea>' +
				'</div>' +
				'<div class="fr-move"><i class="ti ti-grip-vertical"></i></div><span id="ht-chatx">&#x2715</span>' +
				'</div>');
			$('#sortable-list').append(newDiv);
		});
		$('#sortable-list').on('click', '#ht-chatx', function() {
			$(this).parent('.ui-state-default').remove();
			count--;
		});
	});
	// keo qua lai
	jQuery(function($) {
		$("#sortable-list").sortable({
			connectWith: "#sortable-list",
			update: function(event, ui) {
				updateNames();
			}
		}).disableSelection();

		function updateNames() {
			$("#sortable-list .ui-state-default").each(function(index) {
				var newIndex = index + 1;
				var selectName = 'horsetools_settings[chat-nut1' + newIndex + ']';
				var inputName2Name = 'horsetools_settings[chat-nut2' + newIndex + ']';
				var inputName3Name = 'horsetools_settings[chat-nut3' + newIndex + ']';
				var textareaName = 'horsetools_settings[chat-nut4' + newIndex + ']';
				$(this).find("select").attr("name", selectName);
				$(this).find('input[name^="horsetools_settings[chat-nut2"]').attr("name", inputName2Name);
				$(this).find('input[name^="horsetools_settings[chat-nut3"]').attr("name", inputName3Name);
				$(this).find('textarea[name^="horsetools_settings[chat-nut4"]').attr("name", textareaName);
			});
		}
	});
	</script>
	<h4><?php _e('Default style', 'horse-tools'); ?></h4>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[chat-nut-color]" type="text" data-coloris value="<?php if(!empty($horsetools_options['chat-nut-color'])){echo esc_attr($horsetools_options['chat-nut-color']);} ?>"/>
	<label class="ht-right-text"><?php _e('Select button color', 'horse-tools'); ?></label>
	</p>
	<p>
	<?php $styles = array('Icon1', 'Icon2', 'Icon3', 'Icon4', 'Icon5'); ?>
	<select name="horsetools_settings[chat-nut-ico]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($horsetools_options['chat-nut-ico']) && $horsetools_options['chat-nut-ico'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ht-right-text"><?php _e('Select icon', 'horse-tools'); ?></label>
	</p>
	
	<h4><?php _e('Customize chat button', 'horse-tools'); ?></h4>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[chat-nut-new]" value="1" <?php if ( isset($horsetools_options['chat-nut-new']) && 1 == $horsetools_options['chat-nut-new'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Open in a new tab', 'horse-tools'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[chat-ico-color]" type="text" data-coloris value="<?php if(!empty($horsetools_options['chat-ico-color'])){echo esc_attr($horsetools_options['chat-ico-color']);} ?>"/>
	<label class="ht-right-text"><?php _e('Select icon color', 'horse-tools'); ?></label>
	</p>
	<p>
	<?php $styles = array('Left', 'Right'); ?>
	<select name="horsetools_settings[chat-nut-mar]"> 
	<?php foreach($styles as $style) { ?> 
	<?php if(isset($horsetools_options['chat-nut-mar']) && $horsetools_options['chat-nut-mar'] == $style) { $selected = 'selected="selected"'; } else { $selected = ''; } ?>
	<option value="<?php echo $style; ?>" <?php echo $selected; ?>><?php echo $style; ?></option> 
	<?php } ?> 
	</select>
	<label class="ht-right-text"><?php _e('Button position', 'horse-tools'); ?></label>
	</p>
	
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[chat-nut-bot]" min="10" max="300" value="<?php if(!empty($horsetools_options['chat-nut-bot'])){echo sanitize_text_field($horsetools_options['chat-nut-bot']);} else { echo sanitize_text_field('10');} ?>" class="htslide" data-index="1">
	<span><?php _e('Spacing below', 'horse-tools'); ?> <span id="demo1"></span> PX</span>
	</p>
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[chat-nut-lr]" min="10" max="100" value="<?php if(!empty($horsetools_options['chat-nut-lr'])){echo sanitize_text_field($horsetools_options['chat-nut-lr']);} else { echo sanitize_text_field('10');} ?>" class="htslide" data-index="2">
	<span><?php _e('Border distance', 'horse-tools'); ?> <span id="demo2"></span> PX</span>
	</p>
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[chat-nut-op]" min="0" max="1" step="0.1" value="<?php if(!empty($horsetools_options['chat-nut-op'])){echo sanitize_text_field($horsetools_options['chat-nut-op']);} else { echo sanitize_text_field('1');} ?>" class="htslide" data-index="3">
	<span><?php _e('Transparency level', 'horse-tools'); ?> <span id="demo3"></span></span>
	</p>
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[chat-nut-rus]" min="1" max="50" value="<?php if(!empty($horsetools_options['chat-nut-rus'])){echo sanitize_text_field($horsetools_options['chat-nut-rus']);} else { echo sanitize_text_field('50');} ?>" class="htslide" data-index="4">
	<span><?php _e('Border radius', 'horse-tools'); ?> <span id="demo4"></span> PX</span>
	</p>
	
	
  <h3><i class="ti ti-menu-2"></i> <?php _e('Contact bar on mobile', 'horse-tools') ?></h3>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[chat-nav1]" value="1" <?php if ( isset($horsetools_options['chat-nav1']) && 1 == $horsetools_options['chat-nav1'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Enable contact bar', 'horse-tools'); ?></label>
	</p>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[chat-nav-js]" value="1" <?php if ( isset($horsetools_options['chat-nav-js']) && 1 == $horsetools_options['chat-nav-js'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Hide bar when pulled down', 'horse-tools'); ?></label>
	</p>
	<p>
	<label class="nut-switch">
	<input type="checkbox" name="horsetools_settings[chat-nav-tablet]" value="1" <?php if ( isset($horsetools_options['chat-nav-tablet']) && 1 == $horsetools_options['chat-nav-tablet'] ) echo 'checked="checked"'; ?> />
	<span class="slider"></span></label>
	<label class="ht-label-right"><?php _e('Show the bar on tablets too (up to 1024px)', 'horse-tools'); ?></label>
	</p>
	<h4><?php _e('Display options', 'horse-tools'); ?></h4>
	<div id="ht-bar-pick" class="htp-grid"></div>
	<input type="hidden" name="horsetools_settings[chat-nav-skin]" id="chat-nav-skin" value="<?php if(!empty($horsetools_options['chat-nav-skin'])){echo sanitize_text_field($horsetools_options['chat-nav-skin']);} else {echo sanitize_text_field('Default');} ?>" />
	<h4><?php _e('Main button', 'horse-tools'); ?></h4>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Enable the contact bar and configure the content below for use', 'horse-tools'); ?><br>
	<?php _e('Click an SVG box, then', 'horse-tools'); ?> <button type="button" class="ht-icon-open"><i class="ti ti-mood-smile"></i> <?php _e('Choose a built-in icon', 'horse-tools'); ?></button> <?php _e('— or paste your own SVG.', 'horse-tools'); ?><br>
	<b><?php _e('To create a menu on the navigation bar:', 'horse-tools'); ?></b><br>
	<?php _e('Step 1: Go to Appearance > Menus > Create a new menu > check Navigation bar (Horse Tools)', 'horse-tools'); ?><br>
	<?php _e('Step 2: Below, if you want the menu to open on a specific button, add <b style="color:red">#horsenavi</b> to the field (#id or .class). For (Enter link), enter <b style="color:red">#</b>. Note: Only add it to one of the 5 buttons below', 'horse-tools'); ?>
	</p>
	<div class="ht-button-grid2">
		<div class="ht-button-grid-in2">
			<textarea style="height:60px;" class="ht-code-textarea" name="horsetools_settings[chat-nav01]" placeholder="<?php _e('Enter the SVG icon', 'horse-tools'); ?>"><?php if(!empty($horsetools_options['chat-nav01'])){echo esc_textarea($horsetools_options['chat-nav01']);} ?></textarea>
			<input class="ht-input-big" placeholder="<?php _e('Enter name', 'horse-tools'); ?>" type="text" name="horsetools_settings[chat-nav02]" value="<?php if(!empty($horsetools_options['chat-nav02'])){echo sanitize_text_field($horsetools_options['chat-nav02']);} ?>" />
			<input class="ht-input-big" placeholder="<?php _e('Enter link', 'horse-tools'); ?>" type="text" name="horsetools_settings[chat-nav03]" value="<?php if(!empty($horsetools_options['chat-nav03'])){echo sanitize_text_field($horsetools_options['chat-nav03']);} ?>" />
			<input class="ht-input-big" placeholder="<?php _e('#id or .class', 'horse-tools'); ?>" type="text" name="horsetools_settings[chat-nav04]" value="<?php if(!empty($horsetools_options['chat-nav04'])){echo sanitize_text_field($horsetools_options['chat-nav04']);} ?>" />
		</div>
	</div>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('"Enter link" left blank if you want to call the chat list', 'horse-tools'); ?></p>
	<h4><?php _e('4 customizable buttons', 'horse-tools'); ?></h4>
	<div id="sortable-list2">
	<?php
	for ($i = 1; $i <= 4; $i++) { ?>
		<div class="ui-state-default2 ht-button-grid2">
		 <div class="ht-button-grid-in2">
			<textarea style="height:60px;" class="ht-code-textarea" name="horsetools_settings[chat-nav1<?php echo $i ?>]" placeholder="<?php _e('Enter the SVG icon', 'horse-tools'); ?>"><?php if(!empty($horsetools_options['chat-nav1'. $i])){echo esc_textarea($horsetools_options['chat-nav1'. $i]);} ?></textarea>
			<input class="ht-input-big" placeholder="<?php _e('Enter name', 'horse-tools'); ?>" type="text" name="horsetools_settings[chat-nav2<?php echo $i ?>]" value="<?php if(!empty($horsetools_options['chat-nav2'. $i])){echo sanitize_text_field($horsetools_options['chat-nav2'. $i]);} ?>" />
			<input class="ht-input-big" placeholder="<?php _e('Enter link', 'horse-tools'); ?>" type="text" name="horsetools_settings[chat-nav3<?php echo $i ?>]" value="<?php if(!empty($horsetools_options['chat-nav3'. $i])){echo sanitize_text_field($horsetools_options['chat-nav3'. $i]);} ?>" />
			<input class="ht-input-big" placeholder="<?php _e('#id or .class', 'horse-tools'); ?>" type="text" name="horsetools_settings[chat-nav4<?php echo $i ?>]" value="<?php if(!empty($horsetools_options['chat-nav4'. $i])){echo sanitize_text_field($horsetools_options['chat-nav4'. $i]);} ?>" />
		 </div>
		 <div class="fr-move"><i class="ti ti-grip-vertical"></i></div>
		</div>
	<?php } ?>
	</div>
	<script>
	jQuery(function($) {
		$("#sortable-list2").sortable({
			connectWith: "#sortable-list2",
			update: function(event, ui){
				updateNames();
			}
		}).disableSelection();
		function updateNames() {
			$("#sortable-list2 .ui-state-default2").each(function(index) {
				var newIndex = index + 1;
				var textareaName = 'horsetools_settings[chat-nav1' + newIndex + ']';
				var inputName2Name = 'horsetools_settings[chat-nav2' + newIndex + ']';
				var inputName3Name = 'horsetools_settings[chat-nav3' + newIndex + ']';
				$(this).find('textarea[name^="horsetools_settings[chat-nav1"]').attr("name", textareaName);
				$(this).find('input[name^="horsetools_settings[chat-nav2"]').attr("name", inputName2Name);
				$(this).find('input[name^="horsetools_settings[chat-nav3"]').attr("name", inputName3Name);
			});
		}
	});
	</script>
	<h4><?php _e('Customize', 'horse-tools'); ?></h4>
	<select id="horsetools-toc-page-select">
		<option value=""><?php _e('Select the page to hide', 'horse-tools'); ?></option>
		<?php
		$pages = get_pages();
		foreach ($pages as $page) {
			echo '<option value="' . esc_attr($page->post_name) . '">' . esc_html($page->post_title) . '</option>';
		}
		?>
	</select>
	<div id="horsetools-toc-tags">
		<?php 
		if (!empty($horsetools_options['chat-nav-hi'])) {
			$selected_pages = explode("\n", $horsetools_options['chat-nav-hi'] ?? '');
			foreach ($selected_pages as $page_slug) {
				if (!empty($page_slug)) {
					echo '<span class="horsetools-toc-tag">' . esc_html($page_slug) . ' <span class="remove-tag" data-slug="' . esc_attr($page_slug) . '">&times;</span></span>';
				}
			}
		} 
		?>
	</div>
	<textarea id="horsetools-hi-textarea" name="horsetools_settings[chat-nav-hi]" style="display:none;"><?php if(!empty($horsetools_options['chat-nav-hi'])){echo esc_textarea($horsetools_options['chat-nav-hi']);} ?></textarea>
	<script>
	jQuery(document).ready(function($) {
		function updateNoPageMessage() {
			if ($('#horsetools-toc-tags .horsetools-toc-tag').length === 0) {
				$('#horsetools-toc-tags').append('<span class="htno-page"><?php _e('No pages selected', 'horse-tools'); ?></span>');
			} else {
				$('#horsetools-toc-tags .htno-page').remove();
			}
		}
		$('#horsetools-toc-page-select').change(function() {
			var selectedPage = $(this).val();
			if (selectedPage && !isPageAlreadyAdded(selectedPage)) {
				var newTag = $('<span class="horsetools-toc-tag">' + selectedPage + ' <span class="remove-tag" data-slug="' + selectedPage + '">&times;</span></span>');
				$('#horsetools-toc-tags').append(newTag);
				updateTextarea();
				updateNoPageMessage();
			}
			$(this).val('');
		});
		$(document).on('click', '.remove-tag', function() {
			$(this).parent('.horsetools-toc-tag').remove();
			updateTextarea();
			updateNoPageMessage();
		});
		function isPageAlreadyAdded(pageSlug) {
			var exists = false;
			$('#horsetools-toc-tags .horsetools-toc-tag').each(function() {
				if ($(this).find('.remove-tag').data('slug') === pageSlug) {
					exists = true;
					return false; 
				}
			});
			return exists;
		}
		function updateTextarea() {
			var selectedPages = [];
			$('#horsetools-toc-tags .horsetools-toc-tag').each(function() {
				selectedPages.push($(this).find('.remove-tag').data('slug'));
			});
			$('#horsetools-hi-textarea').val(selectedPages.join("\n"));
		}
		updateNoPageMessage();
	});
	</script>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[chat-nav-c1]" type="text" data-coloris value="<?php if(!empty($horsetools_options['chat-nav-c1'])){echo esc_attr($horsetools_options['chat-nav-c1']);} ?>"/>
	<label class="ht-right-text"><?php _e('Outstanding color', 'horse-tools'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[chat-nav-c3]" type="text" data-coloris value="<?php if(!empty($horsetools_options['chat-nav-c3'])){echo esc_attr($horsetools_options['chat-nav-c3']);} ?>"/>
	<label class="ht-right-text"><?php _e('Main icon color', 'horse-tools'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[chat-nav-c31]" type="text" data-coloris value="<?php if(!empty($horsetools_options['chat-nav-c31'])){echo esc_attr($horsetools_options['chat-nav-c31']);} ?>"/>
	<label class="ht-right-text"><?php _e('Main icon text color', 'horse-tools'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[chat-nav-c4]" type="text" data-coloris value="<?php if(!empty($horsetools_options['chat-nav-c4'])){echo esc_attr($horsetools_options['chat-nav-c4']);} ?>"/>
	<label class="ht-right-text"><?php _e('Bar background color', 'horse-tools'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[chat-nav-c5]" type="text" data-coloris value="<?php if(!empty($horsetools_options['chat-nav-c5'])){echo esc_attr($horsetools_options['chat-nav-c5']);} ?>"/>
	<label class="ht-right-text"><?php _e('Chat background color', 'horse-tools'); ?></label>
	</p>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[chat-nav-c6]" type="text" data-coloris value="<?php if(!empty($horsetools_options['chat-nav-c6'])){echo esc_attr($horsetools_options['chat-nav-c6']);} ?>"/>
	<label class="ht-right-text"><?php _e('Chat text color', 'horse-tools'); ?></label>
	</p>
  <h3><i class="ti ti-layout-bottombar-expand"></i> <?php _e('Services panel (slide-up from the bottom bar)', 'horse-tools') ?></h3>
	<?php
	$horsetools_svc_cfg = horsetools_services_get();
	$horsetools_svc_layouts = array(
		'bento'      => __( 'Bento (1 big + cells)', 'horse-tools' ),
		'grid'       => __( 'Card grid', 'horse-tools' ),
		'list'       => __( 'List', 'horse-tools' ),
		'tiles'      => __( 'Compact icon tiles', 'horse-tools' ),
		'chips'      => __( 'Quick chips', 'horse-tools' ),
		'story'      => __( 'Story circles', 'horse-tools' ),
		'coupon'     => __( 'Coupon codes', 'horse-tools' ),
		'stacked'    => __( 'Stacked banners', 'horse-tools' ),
		'banner'     => __( 'Featured banner + grid', 'horse-tools' ),
		'pricecards' => __( 'Price + order button', 'horse-tools' ),
		'reviews'    => __( 'Customer reviews', 'horse-tools' ),
		'video'      => __( 'Video cards', 'horse-tools' ),
		'masonry'    => __( 'Photo masonry', 'horse-tools' ),
	);
	$horsetools_svc_colors = array(
		'gold' => __( 'Gold', 'horse-tools' ), 'blue' => __( 'Blue', 'horse-tools' ), 'green' => __( 'Green', 'horse-tools' ),
		'red' => __( 'Red', 'horse-tools' ), 'purple' => __( 'Purple', 'horse-tools' ), 'dark' => __( 'Dark', 'horse-tools' ), 'neutral' => __( 'Neutral', 'horse-tools' ),
	);
	$horsetools_svc_badgecolors = array( 'red' => 'HOT (đỏ)', 'amber' => 'SALE (cam)', 'green' => 'MỚI (xanh)', 'blue' => 'BLUE', 'gold' => 'GOLD', 'dark' => 'DARK' );
	?>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Build a rich list of services or articles that slides up when a visitor taps a bottom-bar item. To open it, set one bottom-bar item’s “#id or .class” field above to', 'horse-tools'); ?> <code>#ht-services</code>.</p>

	<div class="ht-svc-admin" data-nonce="<?php echo esc_attr( wp_create_nonce( 'horsetools_services' ) ); ?>" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
		<p>
			<label class="ht-container"><?php _e( 'Enable the services panel', 'horse-tools' ); ?>
				<input type="checkbox" id="ht-svc-on" <?php checked( $horsetools_svc_cfg['on'], true ); ?> />
				<span class="ht-checkmark"></span></label>
		</p>
		<p><input type="text" id="ht-svc-title" class="ht-input-big" placeholder="<?php esc_attr_e( 'Panel title (e.g. Our services)', 'horse-tools' ); ?>" value="<?php echo esc_attr( $horsetools_svc_cfg['title'] ); ?>" /></p>
		<?php
		$horsetools_svc_modes = array(
			'auto' => __( 'Auto (sheet on phone, modal on desktop)', 'horse-tools' ),
			'sheet' => __( 'Bottom sheet', 'horse-tools' ), 'modal' => __( 'Centered modal', 'horse-tools' ),
			'drawer-right' => __( 'Right drawer', 'horse-tools' ), 'drawer-left' => __( 'Left drawer', 'horse-tools' ),
			'corner' => __( 'Corner card', 'horse-tools' ), 'fullscreen' => __( 'Full screen', 'horse-tools' ),
		);
		?>
		<div style="display:none">
			<select id="ht-svc-layout"><?php foreach ( $horsetools_svc_layouts as $k => $v ) { echo '<option value="' . esc_attr( $k ) . '"' . selected( $horsetools_svc_cfg['layout'], $k, false ) . '>' . esc_html( $v ) . '</option>'; } ?></select>
			<select id="ht-svc-color"><?php foreach ( $horsetools_svc_colors as $k => $v ) { echo '<option value="' . esc_attr( $k ) . '"' . selected( $horsetools_svc_cfg['color'], $k, false ) . '>' . esc_html( $v ) . '</option>'; } ?></select>
			<select id="ht-svc-display"><?php foreach ( $horsetools_svc_modes as $k => $v ) { echo '<option value="' . esc_attr( $k ) . '"' . selected( $horsetools_svc_cfg['display'], $k, false ) . '>' . esc_html( $v ) . '</option>'; } ?></select>
		</div>
		<div class="htp-sub"><?php _e('Panel layout', 'horse-tools'); ?></div>
		<div id="ht-svc-layout-pick"></div>
		<div class="htp-sub"><?php _e('Theme colour', 'horse-tools'); ?></div>
		<div id="ht-svc-color-pick"></div>
		<div class="htp-sub"><?php _e('Display style (where the panel appears)', 'horse-tools'); ?></div>
		<div id="ht-svc-mode-pick"></div>
		<p style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
			<label class="ht-container" style="margin:0"><?php _e( 'Show a floating “Services” button on desktop', 'horse-tools' ); ?>
				<input type="checkbox" id="ht-svc-launcher" <?php checked( $horsetools_svc_cfg['launcher'], true ); ?> />
				<span class="ht-checkmark"></span></label>
			<input type="text" id="ht-svc-ltext" class="ht-svc-sel" style="width:160px" placeholder="<?php esc_attr_e( 'Button text', 'horse-tools' ); ?>" value="<?php echo esc_attr( $horsetools_svc_cfg['launcher_text'] ); ?>" />
			<input type="text" id="ht-svc-licon" class="ht-svc-sel" style="width:150px" placeholder="<?php esc_attr_e( 'Icon (e.g. apps)', 'horse-tools' ); ?>" value="<?php echo esc_attr( $horsetools_svc_cfg['launcher_icon'] ); ?>" />
		</p>

		<div id="ht-svc-rows"></div>
		<p class="ht-snip-actions">
			<button type="button" class="ht-priv-btn" id="ht-svc-add"><i class="ti ti-plus"></i> <?php _e( 'Add service', 'horse-tools' ); ?></button>
			<button type="button" class="ht-priv-btn" id="ht-svc-save"><i class="ti ti-device-floppy"></i> <?php _e( 'Save services', 'horse-tools' ); ?></button>
		</p>
		<div id="ht-svc-msg"></div>
	</div>
	<style>
	.ht-svc-admin .ht-priv-btn{display:inline-flex;align-items:center;gap:6px;background:#fff;border:1px solid #e0a800;color:#8a5a00;padding:8px 14px;border-radius:8px;cursor:pointer;font-size:13px}
	.ht-svc-admin .ht-priv-btn:hover{background:#fff9e6}
	.ht-svc-sel{padding:8px 10px;border:1px solid #ddd;border-radius:8px;font-size:13px}
	.ht-svc-r{border:1px solid #eee;border-radius:10px;padding:10px;margin-bottom:9px;background:#fafafa;position:relative}
	.ht-svc-r input{width:100%;padding:7px 9px;border:1px solid #ddd;border-radius:7px;font-size:13px;margin-bottom:6px;box-sizing:border-box}
	.ht-svc-r .row2{display:grid;grid-template-columns:1fr 1fr;gap:8px}
	.ht-svc-r .badgewrap{display:flex;gap:8px}
	.ht-svc-r .badgewrap select{padding:7px;border:1px solid #ddd;border-radius:7px;font-size:12px}
	.ht-svc-del{position:absolute;top:8px;right:10px;color:#c0392b;cursor:pointer;font-size:16px}
	.ht-svc-mv{cursor:grab;color:#bbb}
	.ht-svc-msg{padding:8px 12px;border-radius:8px;font-size:13px;background:#f4f6f8;margin-top:6px}
	.ht-svc-msg.ok{background:#eafaf0;color:#1e6b3f}.ht-svc-msg.err{background:#fdecea;color:#8a1c12}
	</style>
	<script>
	(function(){
		var root = document.querySelector('.ht-svc-admin');
		if(!root || root.dataset.ready){ return; }
		root.dataset.ready='1';
		var AJAX=root.dataset.ajax, NONCE=root.dataset.nonce;
		var rowsEl=document.getElementById('ht-svc-rows'), msg=document.getElementById('ht-svc-msg');
		var badgeOpts = <?php echo wp_json_encode( $horsetools_svc_badgecolors ); ?>;
		var initial = <?php echo wp_json_encode( $horsetools_svc_cfg['items'] ); ?>;
		var I18N = {
			saved: <?php echo wp_json_encode( __( 'Saved %d service(s).', 'horse-tools' ) ); ?>,
			fail: <?php echo wp_json_encode( __( 'Something went wrong.', 'horse-tools' ) ); ?>,
			icon: <?php echo wp_json_encode( __( 'Tabler icon name (e.g. snowflake) — or leave blank and use an image', 'horse-tools' ) ); ?>,
			img: <?php echo wp_json_encode( __( 'Image URL (optional, replaces the icon)', 'horse-tools' ) ); ?>,
			title: <?php echo wp_json_encode( __( 'Title', 'horse-tools' ) ); ?>,
			sub: <?php echo wp_json_encode( __( 'Subtitle / price / coupon code', 'horse-tools' ) ); ?>,
			link: <?php echo wp_json_encode( __( 'Link (article or service page)', 'horse-tools' ) ); ?>,
			badge: <?php echo wp_json_encode( __( 'Badge text (optional)', 'horse-tools' ) ); ?>,
			nobadge: <?php echo wp_json_encode( __( 'No badge colour', 'horse-tools' ) ); ?>
		};
		function esc(s){ return String(s==null?'':s).replace(/"/g,'&quot;'); }
		function rowHtml(it){
			it=it||{};
			var opts='<option value="">'+esc(I18N.nobadge)+'</option>';
			Object.keys(badgeOpts).forEach(function(k){ opts+='<option value="'+k+'"'+(it.badge_color===k?' selected':'')+'>'+esc(badgeOpts[k])+'</option>'; });
			return '<div class="ht-svc-r">'
				+ '<span class="ht-svc-del" title="x">&#x2715;</span>'
				+ '<input class="f-title" placeholder="'+esc(I18N.title)+'" value="'+esc(it.title)+'">'
				+ '<div class="row2"><input class="f-icon" list="ht-ti-names" placeholder="'+esc(I18N.icon)+'" value="'+esc(it.icon)+'"><input class="f-sub" placeholder="'+esc(I18N.sub)+'" value="'+esc(it.sub)+'"></div>'
				+ '<input class="f-link" placeholder="'+esc(I18N.link)+'" value="'+esc(it.link)+'">'
				+ '<input class="f-img" placeholder="'+esc(I18N.img)+'" value="'+esc(it.img)+'">'
				+ '<div class="badgewrap"><input class="f-badge" placeholder="'+esc(I18N.badge)+'" value="'+esc(it.badge)+'"><select class="f-bc">'+opts+'</select></div>'
				+ '</div>';
		}
		function addRow(it){ rowsEl.insertAdjacentHTML('beforeend', rowHtml(it)); }
		(initial.length?initial:[{}]).forEach(addRow);

		rowsEl.addEventListener('click', function(e){ var d=e.target.closest('.ht-svc-del'); if(d){ d.closest('.ht-svc-r').remove(); } });
		document.getElementById('ht-svc-add').addEventListener('click', function(){ addRow({}); });

		function collect(){
			var items=[];
			rowsEl.querySelectorAll('.ht-svc-r').forEach(function(r){
				items.push({
					title:r.querySelector('.f-title').value, icon:r.querySelector('.f-icon').value, sub:r.querySelector('.f-sub').value,
					link:r.querySelector('.f-link').value, img:r.querySelector('.f-img').value,
					badge:r.querySelector('.f-badge').value, badge_color:r.querySelector('.f-bc').value
				});
			});
			return { on: document.getElementById('ht-svc-on').checked?1:0, title:document.getElementById('ht-svc-title').value,
				layout:document.getElementById('ht-svc-layout').value, color:document.getElementById('ht-svc-color').value,
				display:document.getElementById('ht-svc-display').value,
				launcher:document.getElementById('ht-svc-launcher').checked?1:0,
				launcher_text:document.getElementById('ht-svc-ltext').value, launcher_icon:document.getElementById('ht-svc-licon').value,
				items:items };
		}
		document.getElementById('ht-svc-save').addEventListener('click', function(){
			var body='action=horsetools_services_save&nonce='+encodeURIComponent(NONCE)+'&data='+encodeURIComponent(JSON.stringify(collect()));
			fetch(AJAX,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body})
				.then(function(r){return r.json();})
				.then(function(res){ msg.className='ht-svc-msg '+(res&&res.success?'ok':'err'); msg.textContent = res&&res.success ? I18N.saved.replace('%d',res.data.items) : ((res&&res.data&&res.data.msg)||I18N.fail); })
				.catch(function(){ msg.className='ht-svc-msg err'; msg.textContent=I18N.fail; });
		});
	})();
	</script>

	<style id="htp-css">
	.htp-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(118px,1fr));gap:10px;margin:6px 0 4px}
	.htp-sw{display:grid;grid-template-columns:repeat(auto-fill,minmax(84px,1fr));gap:10px;margin:6px 0 4px}
	.htp-tile{border:2px solid #eee;border-radius:12px;padding:8px 6px 10px;cursor:pointer;text-align:center;background:#fff;transition:.15s}
	.htp-tile:hover{border-color:#e0a800}
	.htp-tile.sel{border-color:#e0a800;background:#fff9e6}
	.htp-tile .htp-name{margin-top:8px;font-size:12px;font-weight:600;color:#333}
	.htp-tile .htp-desc{font-size:10.5px;color:#888;margin-top:1px;line-height:1.3}
	.htp-sub{font-size:12px;font-weight:700;color:#8a5a00;margin:16px 0 4px;text-transform:uppercase;letter-spacing:.03em}
	.htp-stage{position:relative;height:92px;border-radius:10px;background:linear-gradient(135deg,rgba(125,140,160,.10),rgba(125,140,160,.03));overflow:hidden;border:1px solid #eee}
	.htp-stage.phone{background:repeating-conic-gradient(#0000 0 25%, rgba(125,140,160,.08) 0 50%) 0/16px 16px}
	.htp-b{position:absolute;display:flex;align-items:center;justify-content:center;color:#fff;font-size:9px}
	.htp-circle{width:20px;height:20px;border-radius:50%}
	.htp-sq{width:20px;height:20px;border-radius:5px}
	.htp-hex{width:22px;height:20px;clip-path:polygon(25% 0,75% 0,100% 50%,75% 100%,25% 100%,0 50%)}
	.htp-teal{background:#4bb8a9}.htp-orange{background:#f2924e}.htp-grey{background:#b9bdc4}
	.htp-stack{right:12px;bottom:8px;display:flex;flex-direction:column;gap:6px;align-items:center;position:absolute}
	.htp-stack .htp-b{position:relative;right:auto;left:auto;top:auto;bottom:auto}
	.htp-sh{box-shadow:0 3px 8px rgba(0,0,0,.18)}
	.htp-lbl{position:absolute;height:16px;border-radius:8px;background:#556;opacity:.5}
	.htp-d{width:12px;height:12px;border-radius:50%;display:inline-block}
	.htp-mbar{position:absolute;left:0;right:0;bottom:0;height:30px;display:flex;align-items:center;justify-content:space-around;padding:0 8px;box-shadow:0 -3px 8px rgba(0,0,0,.12)}
	.htp-mbar.notch{clip-path:polygon(0 0,38% 0,42% 45%,58% 45%,62% 0,100% 0,100% 100%,0 100%)}
	.htp-center{position:absolute;left:50%;transform:translateX(-50%);bottom:14px;width:26px;height:26px;border-radius:50%;box-shadow:0 3px 8px rgba(0,0,0,.25);border:3px solid #fff}
	.htp-spw{position:absolute;inset:8px;display:flex;flex-direction:column;gap:4px}
	.htp-sph{height:11px;border-radius:3px;background:#4bb8a9;opacity:.85;flex:0 0 auto}
	.htp-spb{background:#e5e7eb;border-radius:3px}
	.htp-dot{width:12px;height:12px;border-radius:50%;background:#4bb8a9;flex:0 0 auto}
	.htp-ln{height:4px;border-radius:2px;background:#e5e7eb;display:block}
	.htp-chip{height:11px;border-radius:6px;background:#e5e7eb}
	.htp-star{width:5px;height:5px;border-radius:50%;background:#f2924e;display:inline-block}
	.htp-play{width:0;height:0;border-left:9px solid #fff;border-top:5px solid transparent;border-bottom:5px solid transparent}
	.htp-scrim{position:absolute;inset:0;background:rgba(40,45,55,.30)}
	.htp-pnl{position:absolute;background:#4bb8a9;opacity:.92;border-radius:6px}
	.htp-swatch{width:34px;height:34px;border-radius:50%;margin:0 auto;border:2px solid #fff;box-shadow:0 0 0 1px #ddd}
	</style>
	<script id="htp-js">
	(function(){
		if(window.htpDone){return;} window.htpDone=1;
		var TL='htp-teal',OR='htp-orange',GR='htp-grey';
		function c(k){return '<span class="htp-b htp-circle '+k+' htp-sh"></span>';}
		function sq(k){return '<span class="htp-b htp-sq '+k+' htp-sh"></span>';}
		function hx(k){return '<span class="htp-b htp-hex '+k+' htp-sh"></span>';}
		function stack(i){return '<div class="htp-stack">'+i+'</div>';}
		function sp(b){return '<div class="htp-spw"><div class="htp-sph"></div>'+b+'</div>';}
		function rep(s,n){var o='';for(var i=0;i<n;i++){o+=s;}return o;}
		var dotRow='<div style="display:flex;gap:5px;align-items:center"><span class="htp-dot"></span><span class="htp-ln" style="flex:1"></span></div>';
		function barIcons(){var s='';for(var i=0;i<5;i++){s+='<i class="htp-d '+(i===2?OR:GR)+'" style="width:14px;height:14px"></i>';}return s;}

		var SKINS=[
		 ['Default','Default','Cột nút tròn', stack(c(TL)+c(OR)+c(GR))],
		 ['Total','Total','Nút tròn + nhãn', '<span class="htp-b htp-lbl" style="right:34px;bottom:36px;width:36px"></span>'+stack(c(TL)+c(OR)+c(GR))],
		 ['Effective','Effective','Tròn viền nổi', stack('<span class="htp-b htp-circle htp-teal htp-sh" style="outline:3px solid rgba(255,255,255,.6)"></span>'+c(OR)+c(GR))],
		 ['Leaves','Leaves','Bo góc lá', stack('<span class="htp-b htp-teal htp-sh" style="width:20px;height:20px;border-radius:50% 4px 50% 50%"></span><span class="htp-b htp-orange htp-sh" style="width:20px;height:20px;border-radius:50% 4px 50% 50%"></span>')],
		 ['Floating','Floating','Tròn nổi rời', '<span class="htp-b htp-circle htp-teal htp-sh" style="right:14px;bottom:8px"></span><span class="htp-b htp-circle htp-orange htp-sh" style="right:40px;bottom:20px"></span><span class="htp-b htp-circle htp-grey htp-sh" style="right:16px;bottom:40px"></span>'],
		 ['Tap','Tap','1 nút bung ra', '<span class="htp-b htp-circle htp-teal htp-sh" style="right:12px;bottom:8px;width:26px;height:26px">+</span>'],
		 ['Dock','Dock','Khay ngang icon', '<span class="htp-b htp-sh" style="right:10px;bottom:10px;width:78px;height:26px;border-radius:14px;background:#fff;border:1px solid #e5e7eb;gap:6px;padding:0 8px"><i class="htp-d htp-teal"></i><i class="htp-d htp-orange"></i><i class="htp-d htp-grey"></i></span>'],
		 ['Pill','Pill','Viên thuốc ngang', '<span class="htp-b htp-sh" style="right:10px;bottom:12px;width:74px;height:24px;border-radius:14px;background:linear-gradient(90deg,#4bb8a9,#f2924e)"></span>'],
		 ['Glass','Glass','Kính mờ bo tròn', '<span class="htp-b htp-sh" style="right:12px;bottom:10px;width:34px;height:60px;border-radius:16px;background:rgba(255,255,255,.55);border:1px solid rgba(255,255,255,.5);flex-direction:column;gap:6px"><i class="htp-d htp-teal"></i><i class="htp-d htp-orange"></i></span>'],
		 ['Tile','Tile','Ô vuông', stack(sq(TL)+sq(OR)+sq(GR))],
		 ['Hexagon','Hexagon','Lục giác', stack(hx(TL)+hx(OR)+hx(GR))],
		 ['Card','Card','Thẻ góc có tiêu đề', '<span class="htp-b htp-sh" style="right:12px;bottom:8px;width:66px;height:52px;border-radius:10px;background:#fff;border:1px solid #e5e7eb;flex-direction:column;padding:0;overflow:hidden"><i style="width:100%;height:16px;background:#4bb8a9"></i></span>'],
		 ['Sidetab','Sidetab','Tab dán mép phải', '<span class="htp-b htp-teal htp-sh" style="right:0;bottom:24px;width:16px;height:52px;border-radius:8px 0 0 8px"></span>'],
		 ['Speeddial','Speeddial','FAB xòe quạt', '<span class="htp-b htp-circle htp-grey htp-sh" style="right:16px;bottom:40px;width:14px;height:14px"></span><span class="htp-b htp-circle htp-orange htp-sh" style="right:36px;bottom:26px;width:14px;height:14px"></span><span class="htp-b htp-circle htp-teal htp-sh" style="right:12px;bottom:8px;width:26px;height:26px">+</span>'],
		 ['Avatar','Avatar','Ảnh đại diện tròn', '<span class="htp-b htp-circle htp-sh" style="right:12px;bottom:8px;width:30px;height:30px;background:radial-gradient(circle at 50% 35%,#ffd9a8 0 38%,#4bb8a9 40%)"></span>'],
		 ['Livechat','Livechat','Khung chat + lời chào', '<span class="htp-b htp-sh" style="right:12px;bottom:8px;width:78px;height:64px;border-radius:12px;background:#fff;border:1px solid #e5e7eb;flex-direction:column;padding:0;overflow:hidden;align-items:stretch"><i style="height:18px;background:#4bb8a9"></i><i style="margin:6px;height:10px;width:60%;background:#e5e7eb;border-radius:5px"></i><i style="margin:0 6px;height:10px;width:80%;background:#e5e7eb;border-radius:5px"></i></span>'],
		 ['Tabwidget','Tabwidget','Widget nhiều tab', '<span class="htp-b htp-sh" style="right:12px;bottom:8px;width:78px;height:60px;border-radius:12px;background:#fff;border:1px solid #e5e7eb;flex-direction:column;padding:0;overflow:hidden;align-items:stretch"><i style="height:16px;background:linear-gradient(90deg,#4bb8a9 50%,#b9bdc4 50%)"></i><i style="margin:8px 6px;height:10px;background:#e5e7eb;border-radius:5px"></i></span>']
		];
		var BARS=[
		 ['Default','Default','Thanh đặc, giữa nổi', '<div class="htp-mbar" style="background:#3f4650">'+barIcons()+'</div><i class="htp-center htp-teal"></i>'],
		 ['Simple','Simple','Tối giản, mảnh', '<div class="htp-mbar" style="background:#eceef1;height:20px">'+barIcons()+'</div><i class="htp-center htp-orange" style="width:22px;height:22px;bottom:22px"></i>'],
		 ['Docky','Docky','Nổi bo tròn, cách mép', '<div class="htp-mbar" style="left:12px;right:12px;bottom:8px;border-radius:16px;background:#3f4650">'+barIcons()+'</div>'],
		 ['Momo','Momo','Nút giữa lớn nổi cao', '<div class="htp-mbar" style="background:#eceef1">'+barIcons()+'</div><i class="htp-center htp-teal" style="width:30px;height:30px;bottom:20px"></i>'],
		 ['Lom','Lom','Khuyết cong ôm nút giữa', '<div class="htp-mbar notch" style="background:#3f4650">'+barIcons()+'</div><i class="htp-center htp-orange" style="width:28px;height:28px;bottom:24px"></i>']
		];
		var SVC=[
		 ['bento','Bento','1 lớn + ô nhỏ', sp('<div style="flex:1;display:grid;grid-template-columns:1.4fr 1fr;grid-template-rows:1fr 1fr;gap:4px"><div class="htp-spb" style="grid-row:1/3;background:#4bb8a9;opacity:.5"></div><div class="htp-spb"></div><div class="htp-spb"></div></div>')],
		 ['grid','Lưới thẻ','Lưới thẻ đều', sp('<div style="flex:1;display:grid;grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr;gap:4px">'+rep('<div class="htp-spb"></div>',4)+'</div>')],
		 ['list','Danh sách','Hàng ngang có icon', sp('<div style="flex:1;display:flex;flex-direction:column;gap:6px;justify-content:center">'+rep(dotRow,3)+'</div>')],
		 ['tiles','Ô icon gọn','Nhiều icon nhỏ', sp('<div style="flex:1;display:grid;grid-template-columns:repeat(4,1fr);grid-template-rows:1fr 1fr;gap:4px">'+rep('<div class="htp-spb"></div>',8)+'</div>')],
		 ['chips','Chip nhanh','Nhãn bo tròn', sp('<div style="flex:1;display:flex;flex-wrap:wrap;gap:5px;align-content:center">'+['28','36','22','30','26','34'].map(function(w){return '<span class="htp-chip" style="width:'+w+'px"></span>';}).join('')+'</div>')],
		 ['story','Vòng tròn story','Kiểu story tròn', sp('<div style="flex:1;display:flex;gap:7px;align-items:center;justify-content:center">'+rep('<span style="width:22px;height:22px;border-radius:50%;background:#e5e7eb;border:2px solid #f2924e"></span>',4)+'</div>')],
		 ['coupon','Mã giảm giá','Vé giảm giá', sp('<div style="flex:1;display:flex;flex-direction:column;gap:5px;justify-content:center">'+rep('<div style="display:flex;align-items:center;gap:5px;border:1.5px dashed #f2924e;border-radius:5px;padding:3px 5px"><span style="width:14px;height:14px;border-radius:3px;background:#f2924e"></span><span class="htp-ln" style="flex:1"></span></div>',2)+'</div>')],
		 ['stacked','Banner xếp chồng','Banner full ngang', sp('<div style="flex:1;display:flex;flex-direction:column;gap:5px">'+rep('<div class="htp-spb" style="flex:1"></div>',3)+'</div>')],
		 ['banner','Banner nổi bật + lưới','1 hero + lưới', sp('<div style="flex:1;display:flex;flex-direction:column;gap:4px"><div class="htp-spb" style="flex:1.3;background:#4bb8a9;opacity:.5"></div><div style="flex:1;display:grid;grid-template-columns:1fr 1fr;gap:4px"><div class="htp-spb"></div><div class="htp-spb"></div></div></div>')],
		 ['pricecards','Giá + nút đặt','Có giá + nút CTA', sp('<div style="flex:1;display:flex;flex-direction:column;gap:8px;justify-content:center">'+rep('<div style="display:flex;justify-content:space-between;align-items:center;gap:6px"><span class="htp-ln" style="width:45%"></span><span style="width:28px;height:13px;border-radius:4px;background:#f2924e"></span></div>',2)+'</div>')],
		 ['reviews','Đánh giá khách hàng','Avatar + sao', sp('<div style="flex:1;display:flex;flex-direction:column;gap:5px;justify-content:center"><div style="display:flex;gap:5px;align-items:center"><span class="htp-dot" style="background:#f2924e"></span><span style="display:flex;gap:3px">'+rep('<span class="htp-star"></span>',4)+'</span></div><span class="htp-ln"></span><span class="htp-ln" style="width:70%"></span></div>')],
		 ['video','Thẻ video','Có nút play', sp('<div style="flex:1;display:grid;grid-template-columns:1fr 1fr;gap:4px">'+rep('<div class="htp-spb" style="display:flex;align-items:center;justify-content:center"><span class="htp-play"></span></div>',2)+'</div>')],
		 ['masonry','Lưới ảnh masonry','Ảnh so le', sp('<div style="flex:1;display:grid;grid-template-columns:repeat(3,1fr);gap:4px;align-items:start">'+['70%','45%','55%','40%','65%','48%'].map(function(h){return '<div class="htp-spb" style="height:'+h+'"></div>';}).join('')+'</div>')]
		];
		var THEMES=[['gold','Gold','#e0a800'],['blue','Blue','#2f6fed'],['green','Green','#1d9e75'],['red','Red','#e24b4a'],['purple','Purple','#7f77dd'],['dark','Dark','#2b2b2b'],['neutral','Neutral','#555555']];
		var MODES=[
		 ['auto','Tự động','ĐT: sheet · PC: modal', '<div class="htp-scrim"></div><div class="htp-pnl" style="left:8px;right:8px;bottom:8px;height:46%;border-radius:7px 7px 0 0"></div>'],
		 ['sheet','Sheet','Trượt lên từ đáy', '<div class="htp-scrim"></div><div class="htp-pnl" style="left:6px;right:6px;bottom:6px;height:52%;border-radius:8px 8px 0 0"></div>'],
		 ['modal','Modal','Chính giữa', '<div class="htp-scrim"></div><div class="htp-pnl" style="left:20%;right:20%;top:24%;bottom:24%"></div>'],
		 ['drawer-right','Drawer phải','Từ mép phải', '<div class="htp-scrim"></div><div class="htp-pnl" style="right:6px;top:6px;bottom:6px;width:44%;border-radius:8px 0 0 8px"></div>'],
		 ['drawer-left','Drawer trái','Từ mép trái', '<div class="htp-scrim"></div><div class="htp-pnl" style="left:6px;top:6px;bottom:6px;width:44%;border-radius:0 8px 8px 0"></div>'],
		 ['corner','Góc','Popup góc dưới', '<div class="htp-scrim"></div><div class="htp-pnl" style="right:8px;bottom:8px;width:48%;height:46%"></div>'],
		 ['fullscreen','Toàn màn hình','Phủ kín', '<div class="htp-pnl" style="inset:6px"></div>']
		];
		function normalize(items){return items.map(function(it){return it.length===4?it:[it[0],it[1],'',it[2]];});}
		function mount(id, items, target, phone){
			var el=document.getElementById(id); if(!el||!target){return;}
			items=normalize(items);
			var cur=target.value||'';
			el.className='htp-grid';
			el.innerHTML=items.map(function(it){
				var stageCls='htp-stage'+(phone?' phone':'');
				return '<div class="htp-tile'+(it[0]===cur?' sel':'')+'" data-v="'+it[0]+'"><div class="'+stageCls+'">'+it[3]+'</div><div class="htp-name">'+it[1]+'</div>'+(it[2]?'<div class="htp-desc">'+it[2]+'</div>':'')+'</div>';
			}).join('');
			el.querySelectorAll('.htp-tile').forEach(function(t){
				t.addEventListener('click',function(){
					el.querySelectorAll('.htp-tile').forEach(function(x){x.classList.remove('sel');});
					t.classList.add('sel');
					target.value=t.getAttribute('data-v');
					if(target.tagName==='SELECT'){ target.dispatchEvent(new Event('change')); }
				});
			});
		}
		function mountThemes(id, target){
			var el=document.getElementById(id); if(!el||!target){return;}
			var cur=target.value||'';
			el.className='htp-sw';
			el.innerHTML=THEMES.map(function(t){
				return '<div class="htp-tile'+(t[0]===cur?' sel':'')+'" data-v="'+t[0]+'" style="padding:12px 6px"><span class="htp-swatch" style="background:'+t[2]+'"></span><div class="htp-name" style="margin-top:8px">'+t[1]+'</div></div>';
			}).join('');
			el.querySelectorAll('.htp-tile').forEach(function(t){
				t.addEventListener('click',function(){
					el.querySelectorAll('.htp-tile').forEach(function(x){x.classList.remove('sel');});
					t.classList.add('sel'); target.value=t.getAttribute('data-v'); target.dispatchEvent(new Event('change'));
				});
			});
		}
		function init(){
			mount('ht-skin-pick', SKINS, document.getElementById('chat-nut-skin'), false);
			mount('ht-bar-pick', BARS, document.getElementById('chat-nav-skin'), true);
			mount('ht-svc-layout-pick', SVC, document.getElementById('ht-svc-layout'), false);
			mountThemes('ht-svc-color-pick', document.getElementById('ht-svc-color'));
			mount('ht-svc-mode-pick', MODES, document.getElementById('ht-svc-display'), false);
		}
		if(document.readyState!=='loading'){init();}else{document.addEventListener('DOMContentLoaded',init);}
	})();
	</script>

	<div id="ht-icon-modal" class="ht-icon-modal" hidden>
		<div class="ht-icon-backdrop"></div>
		<div class="ht-icon-dialog">
			<div class="ht-icon-head"><b><?php _e('Choose a built-in icon', 'horse-tools'); ?></b><span class="ht-icon-close" title="close">&#x2715;</span></div>
			<input type="text" class="ht-icon-search" placeholder="<?php esc_attr_e('Search: phone, chat, cart, star…', 'horse-tools'); ?>">
			<div class="ht-icon-grid" id="ht-icon-grid"></div>
			<div class="ht-icon-hint"><i class="ti ti-bulb"></i> <?php _e('Click an icon to insert it into the selected SVG box. These use the current icon colour automatically.', 'horse-tools'); ?></div>
		</div>
	</div>
	<style>
	.ht-icon-open{display:inline-flex;align-items:center;gap:4px;background:#fff;border:1px solid #e0a800;color:#8a5a00;padding:3px 10px;border-radius:7px;cursor:pointer;font-size:12px;vertical-align:middle}
	.ht-icon-open:hover{background:#fff9e6}
	.ht-icon-modal{position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center}
	.ht-icon-modal[hidden]{display:none}
	.ht-icon-backdrop{position:absolute;inset:0;background:rgba(20,22,26,.45)}
	.ht-icon-dialog{position:relative;background:#fff;border-radius:14px;width:min(560px,92vw);max-height:82vh;display:flex;flex-direction:column;padding:16px;box-shadow:0 20px 60px rgba(0,0,0,.3)}
	.ht-icon-head{display:flex;align-items:center;justify-content:space-between;font-size:15px;color:#8a5a00;margin-bottom:10px}
	.ht-icon-close{cursor:pointer;font-size:18px;color:#999;line-height:1}
	.ht-icon-close:hover{color:#c0392b}
	.ht-icon-search{width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:9px;font-size:13px;margin-bottom:12px;box-sizing:border-box}
	.ht-icon-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(70px,1fr));gap:8px;overflow:auto;padding:2px}
	.ht-icon-cell{display:flex;flex-direction:column;align-items:center;gap:4px;border:2px solid #eee;border-radius:10px;padding:9px 4px;cursor:pointer;background:#fff;color:#3a3f47}
	.ht-icon-cell:hover{border-color:#e0a800;background:#fff9e6}
	.ht-icon-cell svg{width:24px;height:24px}
	.ht-icon-cell span{font-size:9.5px;color:#888}
	.ht-icon-hint{margin-top:12px;font-size:12px;color:#8a5a00;background:#fff9e6;border-radius:8px;padding:8px 10px}
	</style>
	<script>
	(function(){
		var modal=document.getElementById('ht-icon-modal'); if(!modal){return;}
		var grid=document.getElementById('ht-icon-grid');
		var search=modal.querySelector('.ht-icon-search');
		var last=null;
		function isSvgField(el){ return el && el.tagName==='TEXTAREA' && /\[chat-(nut4|nav0|nav1)/.test(el.name||''); }
		document.addEventListener('focusin', function(e){ if(isSvgField(e.target)){ last=e.target; } });
		var S='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">';
		var ICONS=[
			['phone', S+'<path d="M6 3h3l2 5-2 1a11 11 0 005 5l1-2 5 2v3a2 2 0 01-2 2A16 16 0 014 5a2 2 0 012-2z"/></svg>'],
			['chat', S+'<path d="M4 5h16v10H8l-4 4z"/></svg>'],
			['message', S+'<path d="M4 5h16v10H8l-4 4z"/><path d="M8 9h8M8 12h5"/></svg>'],
			['mail', S+'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>'],
			['map', S+'<path d="M12 21s7-6.5 7-11a7 7 0 10-14 0c0 4.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>'],
			['cart', S+'<circle cx="9" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/><path d="M3 4h2l2.4 12h10l2-8H6.4"/></svg>'],
			['user', S+'<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/></svg>'],
			['home', S+'<path d="M4 11l8-7 8 7"/><path d="M6 10v10h12V10"/></svg>'],
			['star', S+'<path d="M12 3l2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9 6.8 19l1-5.8L3.5 9.2l5.9-.9z"/></svg>'],
			['heart', S+'<path d="M12 20s-7-4.5-7-10a4 4 0 017-2 4 4 0 017 2c0 5.5-7 10-7 10z"/></svg>'],
			['clock', S+'<circle cx="12" cy="12" r="8"/><path d="M12 8v4l3 2"/></svg>'],
			['info', S+'<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>'],
			['help', S+'<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 113 2.5c-.7.3-1 .8-1 1.5M12 17h.01"/></svg>'],
			['gift', S+'<rect x="4" y="9" width="16" height="11" rx="1"/><path d="M4 13h16M12 9v11M12 9c-3-4-6-1-3 0M12 9c3-4 6-1 3 0"/></svg>'],
			['tag', S+'<path d="M4 4h7l9 9-7 7-9-9z"/><circle cx="8" cy="8" r="1.2"/></svg>'],
			['bell', S+'<path d="M6 16V10a6 6 0 1112 0v6l2 2H4z"/><path d="M10 20a2 2 0 004 0"/></svg>'],
			['send', S+'<path d="M4 12l16-8-6 16-3-6-7-2z"/></svg>'],
			['calendar', S+'<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 9h16M8 3v4M16 3v4"/></svg>'],
			['camera', S+'<rect x="3" y="7" width="18" height="13" rx="2"/><circle cx="12" cy="13" r="3.3"/><path d="M8 7l2-3h4l2 3"/></svg>'],
			['photo', S+'<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10" r="1.4"/><path d="M4 17l5-5 4 4 3-3 4 4"/></svg>'],
			['download', S+'<path d="M12 4v10m0 0l-4-4m4 4l4-4M5 20h14"/></svg>'],
			['link', S+'<path d="M10 14a4 4 0 005.7 0l3-3a4 4 0 00-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 00-5.7 0l-3 3a4 4 0 005.7 5.7l1-1"/></svg>'],
			['search', S+'<circle cx="11" cy="11" r="6"/><path d="M20 20l-4-4"/></svg>'],
			['headset', S+'<path d="M4 13v-1a8 8 0 0116 0v1"/><rect x="3" y="13" width="4" height="6" rx="1"/><rect x="17" y="13" width="4" height="6" rx="1"/><path d="M19 19a4 4 0 01-4 3h-3"/></svg>']
		];
		grid.innerHTML=ICONS.map(function(it){ return '<button type="button" class="ht-icon-cell" title="'+it[0]+'" data-svg="'+it[1].replace(/"/g,'&quot;')+'">'+it[1]+'<span>'+it[0]+'</span></button>'; }).join('');
		function openM(){ modal.hidden=false; search.value=''; filter(''); search.focus(); }
		function closeM(){ modal.hidden=true; }
		function filter(q){ q=(q||'').toLowerCase(); grid.querySelectorAll('.ht-icon-cell').forEach(function(c){ c.style.display = c.title.indexOf(q)>=0 ? '' : 'none'; }); }
		document.addEventListener('click', function(e){
			var openBtn=e.target.closest('.ht-icon-open');
			if(openBtn){ e.preventDefault(); openM(); return; }
			var cell=e.target.closest('.ht-icon-cell');
			if(cell){
				var svg=cell.getAttribute('data-svg');
				var tgt=last || document.querySelector('textarea[name*="chat-nut4"], textarea[name*="chat-nav0"], textarea[name*="chat-nav1"]');
				if(tgt){ tgt.value=svg; tgt.dispatchEvent(new Event('input',{bubbles:true})); last=tgt; }
				closeM(); return;
			}
			if(e.target.closest('.ht-icon-close') || e.target.classList.contains('ht-icon-backdrop')){ closeM(); }
		});
		search.addEventListener('input', function(){ filter(search.value); });
		document.addEventListener('keydown', function(e){ if(e.key==='Escape' && !modal.hidden){ closeM(); } });
	})();
	</script>
</div>	
