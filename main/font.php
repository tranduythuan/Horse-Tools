<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function horsetools_font_options_page() {
	if( isset($_GET['delete_font_key']) ){
       $delete = horsetools_delete_font();
    }
	ob_start(); 
	?>
	<div class="wrap ht-wrap">
	<div class="ht-wrap-top">
	</div>
	<div class="ht-wrap2">
	  <div class="ht-box">
		<div class="ht-menu">
			<div class="ht-logo ht-logoquay">
			<a class="ht-logoquaya" href="https://example.com" target="_blank">
			<span><?php horsetools_logo(); ?></span>
			</a>
			</div>
			<button class="sotab sotab-select" onclick="httab(event, 'tab1')"><i class="fa-regular fa-book-font"></i> <?php _e('ADD FONT', 'horse-tools'); ?></button>
			<button class="sotab" onclick="httab(event, 'tab2')"><i class="fa-regular fa-font-case"></i> <?php _e('USE FONT', 'horse-tools'); ?></button>
		</div>

		<div class="ht-main">
			<?php 
			if( isset($_GET['valid']) && $_GET['valid'] == 'true' ){
                    echo '<div class="ht-updated">'. __('Upload font success', 'horse-tools'). '</div>';
            } else if( isset($_GET['valid']) && $_GET['valid'] == 'false' ){
                    echo '<div class="ht-updated">'. __('Upload font error', 'horse-tools'). '</div>';
            }
                if( isset($_GET['delete_font_key']) ){
                    echo '<div class="ht-updated">'. esc_html( isset($delete['status']) ? $delete['status'] : '' ) .'</div>';
            }
			?>
			<!-- FONT -->
			<div class="sotab-box htbox" id="tab1" style="margin-bottom:-80px;">
			<form id="ht-form-font" method="post" enctype="multipart/form-data">
			<h2><?php _e('ADD FONT', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-book-font"></i> <?php _e('Add fonts', 'horse-tools') ?></h3>
			    <p>
				<input class="ht-input-big" type="text" placeholder="<?php _e('Name the font', 'horse-tools'); ?>" name="horsetools_font_name" required />
				</p>
				<label class="ht-label-file">
				<input class="ht-input-file" type="file" name="horsetools_upload_file" required />
				<b id="file-co"><?php _e('BROWSE FILE', 'horse-tools'); ?></b>
				<p><em><?php _e('Accepted Font Format : woff2, ttf, otf, off | Font Size: Upto 25 MB', 'horse-tools'); ?></em></p>
				</label>
				<div class="ht-label-sub">
					<button type="submit"><i class="fa-regular fa-folder-arrow-up"></i> <?php _e('UPLOAD FONT', 'horse-tools'); ?></button>
				</div>
			    <ul class="font-list">
                    <?php 
                    $fontsData = horsetools_get_uploaded_font_data();
                    if (!empty($fontsData)){
                         foreach ($fontsData as $key => $fontData){ ?>
                            <li>
                                <article class="ht-box-font">
                                    <div class="ht-box-top">
                                        <div class="font-name font-demo <?php echo esc_attr($fontData['font_name']);?>"><?php echo esc_html($fontData['font_name']);?></div>
                                        <div class="font-class"><?php _e('font-family:', 'horse-tools'); ?> '<?php echo esc_html($fontData['font_name']);?>', sans-serif;</div>
                                    </div>
                                    <div class="ht-box-dow">
                                        <div class="font-dele">
										<a onclick="if (!confirm('<?php _e('Do you want to delete?', 'horse-tools'); ?>')){return false;}" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=horsetools-font-options&delete_font_key=' . rawurlencode( sanitize_key( $key ) ) ), 'horsetools_delete_font_' . sanitize_key( $key ) ) ); ?>"><i class="fa-regular fa-trash"></i></a>
										</div>
                                    </div>
                                </article>
                            </li>
                    <?php }
                    }
                    ?>
                </ul>
			</div>
			</form>
			</div>
			<!-- set font -->
			<div class="sotab-box htbox" id="tab2" style="display:none">
			<?php 
			global $horsetools_fontset_options;
			if( isset($_GET['settings-updated']) ) { 
				require_once( HORSETOOLS_DIR . 'main/completed.php');
			}
			?>
			<form method="post" action="options.php">
			<?php settings_fields('horsetools_fontset_settings_group'); ?>
			<h2><?php _e('USE FONT', 'horse-tools'); ?></h2>
			<div class="ht-card">
			  <h3><i class="fa-regular fa-font-case"></i> <?php _e('Use fonts on the web', 'horse-tools') ?></h3>
				<?php
				if (!empty($fontsData)){
					$p_contents = array(
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">div</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">p</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">a</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">span</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">button</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">input</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">textarea</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">select</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">h1</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">h2</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">h3</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">h4</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">h5</i> tag</h4>',
						'<h4>'. __('Choose a font for the', 'horse-tools'). ' <i style="color:#ff4444">h6</i> tag</h4>',
					);
					echo '<div class="ht-font-box">';
					for ($i = 1; $i <= 14; $i++) { 
					echo '<div class="ht-font-sel">' . $p_contents[$i - 1] . ''; 
						$selected = ($horsetools_fontset_options['font' . $i] ?? '') === 'Default' ? 'selected="selected"' : ''; ?>
						<p>
						<select name="horsetools_fontset_settings[font<?php echo $i; ?>]" class="font-select select2" style="width:100%;">
							<option value="Default" <?php echo $selected; ?>>Default</option> 
							<?php 
							foreach ($fontsData as $fontData) {
								$selected = ($horsetools_fontset_options['font' . $i] ?? '') === $fontData['font_name'] ? 'selected="selected"' : ''; ?>
								<option value="<?php echo esc_attr($fontData['font_name']); ?>" <?php echo $selected; ?>><?php echo esc_html($fontData['font_name']); ?></option>
							<?php } ?>
						</select>
						</p>
						<div id="fontview<?php echo $i; ?>" class="font-view">This is a font demo</div>
						</div>
					<?php 
					} 
					echo '</div>';
				} 
				?>
			<div class="ht-submit">
			<button type="submit"><i class="fa-regular fa-floppy-disk"></i> <?php _e('SAVE CONTENT', 'horse-tools'); ?></button>
			</div>
			</form>
			</div>
			</div>
			
		</div>
	  </div>
      <div class="ht-sidebar">
	  </div>
	</div>	
	</div>
	<script>
	jQuery('#ht-form-font').on('submit', function(e){
		e.preventDefault();
		var formData = new FormData(this);
		var ajax_nonce = '<?php echo wp_create_nonce('horsetools_font_nonce'); ?>';
		formData.append('action', 'horsetools_upload_fonts');
		formData.append('security', ajax_nonce);
		jQuery.ajax({
			url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
			type: 'POST',
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function(){},
			success: function(response) {
				if (response == '1' || response == 1) {
					window.location.href = "admin.php?page=horsetools-font-options&valid=true";
				} else {
					window.location.href = "admin.php?page=horsetools-font-options&valid=false";
				}
			},
		})
	});
	jQuery(document).ready(function($){
		$('.ht-input-file').change(function() {
			var fileName = $(this).val().split('\\').pop();
			$('#file-co').text(fileName);
		});
		$('.font-select').change(function(){
			var selectedFont = $(this).val(); 
			var divId = $(this).attr('name').replace('horsetools_fontset_settings[font', 'fontview').replace(']', '');
			if(selectedFont === 'Default') {
				$('#' + divId).hide();
			} else {
				$('#' + divId).css('font-family', selectedFont);
				$('#' + divId).show(); 
			} 
		});
		$('.font-select').each(function() {
			var selectedFont = $(this).val(); 
			var divId = $(this).attr('name').replace('horsetools_fontset_settings[font', 'fontview').replace(']', '');
			if(selectedFont === 'Default') {
				$('#' + divId).hide();
			} else {
				$('#' + divId).css('font-family', selectedFont);
				$('#' + divId).show(); 
			}
		});
	});
	// select font
	jQuery(document).ready(function($) {
		$('.select2').select2({
			width: 'resolve'  
		});
	});
	</script>
	<?php
	// style horsetools
	require_once( HORSETOOLS_DIR . 'main/style.php');
	echo ob_get_clean();
}
function horsetools_font_options_link() {
	add_submenu_page ('horsetools-options', 'Font', '<i class="fa-regular fa-book-font" style="width:20px;"></i> '. __('Font', 'horse-tools'), 'manage_options', 'horsetools-font-options', 'horsetools_font_options_page');
}
add_action('admin_menu', 'horsetools_font_options_link');
function horsetools_font_register_settings() {
	register_setting( 'horsetools_fontset_settings_group', 'horsetools_fontset_settings', array( 'sanitize_callback' => 'horsetools_sanitize_font' ) );
}
add_action('admin_init', 'horsetools_font_register_settings');
// clear cache
function horsetools_fontset_settings_cache($old_value, $value) {
    wp_cache_delete('horsetools_fontset_settings', 'options');
}
add_action('update_option_horsetools_fontset_settings', 'horsetools_fontset_settings_cache', 10, 2);









