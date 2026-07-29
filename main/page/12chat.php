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
	<div id="ht-imgstyle3" class="ht-imgstyle ht-imgstyle6">
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/chat/1.png'); ?>" data-value="Default" class="<?php if(isset($horsetools_options['chat-nut-skin']) && $horsetools_options['chat-nut-skin'] == 'Default') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/chat/2.png'); ?>" data-value="Total" class="<?php if(isset($horsetools_options['chat-nut-skin']) && $horsetools_options['chat-nut-skin'] == 'Total') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/chat/3.png'); ?>" data-value="Effective" class="<?php if(isset($horsetools_options['chat-nut-skin']) && $horsetools_options['chat-nut-skin'] == 'Effective') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/chat/4.png'); ?>" data-value="Leaves" class="<?php if(isset($horsetools_options['chat-nut-skin']) && $horsetools_options['chat-nut-skin'] == 'Leaves') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/chat/5.png'); ?>" data-value="Floating" class="<?php if(isset($horsetools_options['chat-nut-skin']) && $horsetools_options['chat-nut-skin'] == 'Floating') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/chat/6.png'); ?>" data-value="Tap" class="<?php if(isset($horsetools_options['chat-nut-skin']) && $horsetools_options['chat-nut-skin'] == 'Tap') echo 'selected'; ?>" />
		<?php
		$horsetools_newskins = array( 'Dock' => 'ti-layout-grid', 'Pill' => 'ti-capsule-horizontal', 'Glass' => 'ti-square-rounded', 'Tile' => 'ti-square', 'Hexagon' => 'ti-hexagon' );
		foreach ( $horsetools_newskins as $sk => $ic ) {
			$sel = ( isset($horsetools_options['chat-nut-skin']) && $horsetools_options['chat-nut-skin'] == $sk ) ? ' selected' : '';
			echo '<span class="ht-skin-tile'. $sel .'" data-value="'. esc_attr($sk) .'"><i class="ti '. esc_attr($ic) .'"></i>'. esc_html($sk) .'</span>';
		}
		?>
	</div>
	<style>
	.ht-skin-tile{display:inline-flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;width:74px;height:74px;border:2px solid #eee;border-radius:10px;cursor:pointer;font-size:11px;color:#666;vertical-align:top;margin:4px}
	.ht-skin-tile i{font-size:24px;color:#8a5a00}
	.ht-skin-tile.selected{border-color:#e0a800;background:#fff9e6}
	</style>
	<input type="hidden" name="horsetools_settings[chat-nut-skin]" id="chat-nut-skin" value="<?php if(!empty($horsetools_options['chat-nut-skin'])){echo sanitize_text_field($horsetools_options['chat-nut-skin']);} else {echo sanitize_text_field('Default');} ?>" />
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var imgStyles = document.querySelectorAll('#ht-imgstyle3 img, #ht-imgstyle3 .ht-skin-tile');
			imgStyles.forEach(function(img) {
				img.addEventListener('click', function() {
					var selectedStyle = this.getAttribute('data-value');
					document.getElementById('chat-nut-skin').value = selectedStyle;
					imgStyles.forEach(function(img) {
						img.classList.remove('selected');
					});
					this.classList.add('selected');
				});
			});
		});
	</script>
	<h4><?php _e('Configure buttons', 'horse-tools'); ?></h4>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Visit this page to get the SVG icon:', 'horse-tools'); ?> <b><a target="_blank" href="https://lineicons.com/free-icons">lineicons.com</a> [Copy SVG]</b><br>
	<b>Custom, Maps:</b> <?php _e('Enter link', 'horse-tools'); ?><br>
	<b>Phone, SMS, Messenger, Telegram, Zalo, Whatsapp, Viber, Skype, Tiktok, Mail:</b> <?php _e('Enter ID', 'horse-tools'); ?>
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
	<div id="ht-imgstyle4" class="ht-imgstyle ht-imgstyle5">
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/chat/n1.png'); ?>" data-value="Default" class="<?php if(isset($horsetools_options['chat-nav-skin']) && $horsetools_options['chat-nav-skin'] == 'Default') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/chat/n2.png'); ?>" data-value="Simple" class="<?php if(isset($horsetools_options['chat-nav-skin']) && $horsetools_options['chat-nav-skin'] == 'Simple') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/chat/n3.png'); ?>" data-value="Docky" class="<?php if(isset($horsetools_options['chat-nav-skin']) && $horsetools_options['chat-nav-skin'] == 'Docky') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/chat/n4.png'); ?>" data-value="Momo" class="<?php if(isset($horsetools_options['chat-nav-skin']) && $horsetools_options['chat-nav-skin'] == 'Momo') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/chat/n5.png'); ?>" data-value="Lom" class="<?php if(isset($horsetools_options['chat-nav-skin']) && $horsetools_options['chat-nav-skin'] == 'Lom') echo 'selected'; ?>" />
	</div>
	<input type="hidden" name="horsetools_settings[chat-nav-skin]" id="chat-nav-skin" value="<?php if(!empty($horsetools_options['chat-nav-skin'])){echo sanitize_text_field($horsetools_options['chat-nav-skin']);} else {echo sanitize_text_field('Default');} ?>" />
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var imgStyles = document.querySelectorAll('#ht-imgstyle4 img');
			imgStyles.forEach(function(img) {
				img.addEventListener('click', function() {
					var selectedStyle = this.getAttribute('data-value');
					document.getElementById('chat-nav-skin').value = selectedStyle;
					imgStyles.forEach(function(img) {
						img.classList.remove('selected');
					});
					this.classList.add('selected');
				});
			});
		});
	</script>
	<h4><?php _e('Main button', 'horse-tools'); ?></h4>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Enable the contact bar and configure the content below for use', 'horse-tools'); ?><br>
	<?php _e('Visit this page to get the SVG icon:', 'horse-tools'); ?> <b><a target="_blank" href="https://lineicons.com/free-icons">lineicons.com</a> [Copy SVG]</b><br>
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
		<p style="display:flex;gap:10px;flex-wrap:wrap;">
			<select id="ht-svc-layout" class="ht-svc-sel"><?php foreach ( $horsetools_svc_layouts as $k => $v ) { echo '<option value="' . esc_attr( $k ) . '"' . selected( $horsetools_svc_cfg['layout'], $k, false ) . '>' . esc_html( $v ) . '</option>'; } ?></select>
			<select id="ht-svc-color" class="ht-svc-sel"><?php foreach ( $horsetools_svc_colors as $k => $v ) { echo '<option value="' . esc_attr( $k ) . '"' . selected( $horsetools_svc_cfg['color'], $k, false ) . '>' . esc_html( $v ) . '</option>'; } ?></select>
			<?php
			$horsetools_svc_modes = array(
				'auto' => __( 'Auto (sheet on phone, modal on desktop)', 'horse-tools' ),
				'sheet' => __( 'Bottom sheet', 'horse-tools' ), 'modal' => __( 'Centered modal', 'horse-tools' ),
				'drawer-right' => __( 'Right drawer', 'horse-tools' ), 'drawer-left' => __( 'Left drawer', 'horse-tools' ),
				'corner' => __( 'Corner card', 'horse-tools' ), 'fullscreen' => __( 'Full screen', 'horse-tools' ),
			);
			?>
			<select id="ht-svc-display" class="ht-svc-sel"><?php foreach ( $horsetools_svc_modes as $k => $v ) { echo '<option value="' . esc_attr( $k ) . '"' . selected( $horsetools_svc_cfg['display'], $k, false ) . '>' . esc_html( $v ) . '</option>'; } ?></select>
		</p>
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
				+ '<div class="row2"><input class="f-icon" placeholder="'+esc(I18N.icon)+'" value="'+esc(it.icon)+'"><input class="f-sub" placeholder="'+esc(I18N.sub)+'" value="'+esc(it.sub)+'"></div>'
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
</div>		
