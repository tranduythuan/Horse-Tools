<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options; ?>
<div class="ht-on">
<label class="nut-hton">
<input class="toggle-checkbox" id="check5" data-target="play5" type="checkbox" name="horsetools_settings[media]" value="1" <?php if ( isset($horsetools_options['media']) && 1 == $horsetools_options['media'] ) echo 'checked="checked"'; ?> />
<span class="htder"></span></label>
<label class="ht-on-right"><?php _e('ON/OFF', 'horse-tools'); ?></label>
</div>
<div id="play5" class="ht-card toggle-div">
  <h3><i class="ti ti-crop"></i> <?php _e('Image crop configuration', 'horse-tools') ?></h3>
	<!-- upload hinh anh 1 -->
	<?php horsetools_toggle( 'media-up1', __( 'Stop cropping all', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Image crop configuration',
	) ); ?>
	<?php
	global $_wp_additional_image_sizes;
	$image_sizes = get_intermediate_image_sizes();
	if (isset($_wp_additional_image_sizes) && count($_wp_additional_image_sizes) > 0) {
		$image_sizes = array_merge($image_sizes, array_keys($_wp_additional_image_sizes));
	}
	$image_sizes = array_unique($image_sizes);
	$selected_sizes = array(); 
	foreach ($image_sizes as $i => $size) {
		$width = isset($_wp_additional_image_sizes[$size]['width']) ? $_wp_additional_image_sizes[$size]['width'] : get_option($size.'_size_w');
		?>
		<p>
			<label class="nut-switch">
				<input type="checkbox" name="horsetools_settings[main-img][]" value="<?php echo $size; ?>" <?php if (isset($horsetools_options['main-img']) && in_array($size, $horsetools_options['main-img'])) echo 'checked="checked"'; ?> />
				<span class="slider"></span>
			</label>
			<label class="ht-label-right"><?php echo __('Stop cropping', 'horse-tools') .' '. $size .' (W: '. $width .')'; ?></label>
		</p>
	<?php } ?>
   <h3><i class="ti ti-photo-video"></i> <?php _e('Advanced photo upload', 'horse-tools') ?></h3>
	<?php horsetools_toggle( 'media-title1', __( 'Automatic name', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Advanced photo upload',
	) ); ?>
	<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e('The file name will be your domain name', 'horse-tools'); ?></p>
	<?php horsetools_toggle( 'media-png-8', __( 'Ignore 8-bit png', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Advanced photo upload',
	) ); ?>
	<p class="ht-note ht-note-red"><i class="ti ti-bulb"></i> <?php _e('8-bit PNG images are not supported by the GD library, and this format already has perfect compression and does not need further processing', 'horse-tools'); ?></p>		
  <h3><i class="ti ti-upload"></i> <?php _e('Configure image upload function', 'horse-tools') ?></h3>
	<!-- upload hinh anh 2 -->
	<?php horsetools_toggle( 'media-up2', __( 'Enable upload size limit', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Configure image upload function',
	) ); ?>

	<?php horsetools_input( 'media-up21', __( 'MB', 'horse-tools' ), array(
		'tab'         => 'MEDIA',
		'section'     => 'Configure image upload function',
		'type'        => 'number',
		'class'       => 'ht-input-small',
		'placeholder' => '10',
		'parent'      => 'media-up2',
	) ); ?>

	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('If you have members and do not want them to upload images with excessively large sizes, causing storage space usage, you can limit the maximum size allowed for upload', 'horse-tools'); ?></p>
	
	<!-- upload hinh anh 3 -->
	<?php horsetools_toggle( 'media-up3', __( 'Allow uploading SVG files', 'horse-tools' ), array(
		'tab'         => 'MEDIA',
		'section'     => 'Configure image upload function',
		'description' => __( 'Enable this feature if you want SVG images to be uploaded to the media', 'horse-tools' ),
	) ); ?>

	<!-- upload hinh anh 4 -->
	<?php horsetools_toggle( 'media-up4', __( 'Allow uploading JFIF files', 'horse-tools' ), array(
		'tab'         => 'MEDIA',
		'section'     => 'Configure image upload function',
		'description' => __( 'Enable this feature if you want JFIF format images to be uploaded to the media. The uploaded images will be converted to JPG or WEBP format according to the configuration', 'horse-tools' ),
	) ); ?>
  <h3><i class="ti ti-file-zip"></i> <?php _e('Compress JPG images upon upload', 'horse-tools') ?></h3>
	<!-- upload hinh anh 1 -->
	<?php horsetools_toggle( 'media-zip1', __( 'Enable JPG image compression', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Compress JPG images upon upload',
	) ); ?>

	<p class="ht-keo">
	<input type="range" name="horsetools_settings[media-zip11]" min="10" max="90" value="<?php if(!empty($horsetools_options['media-zip11'])){echo sanitize_text_field($horsetools_options['media-zip11']);} else {echo sanitize_text_field('60');} ?>" class="htslide" data-index="10">
	<span><?php _e('Compression level (', 'horse-tools'); ?><b><span id="demo10"></span></b> - 90)</span>
	</p>
	
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('You can adjust the compression level of the image from 5 to 100 (100 being no compression)', 'horse-tools'); ?></p>	
	
	<!-- png to jpg -->
	<?php horsetools_toggle( 'media-zip12', __( 'Convert PNG to JPG', 'horse-tools' ), array(
		'tab'         => 'MEDIA',
		'section'     => 'Compress JPG images upon upload',
		'description' => __( 'Convert PNG images to JPG upon upload, and compress the images according to the JPG configuration', 'horse-tools' ),
	) ); ?>
  <h3><i class="ti ti-file-zip"></i> <?php _e('Convert images to WEBP upon upload', 'horse-tools') ?></h3>
	<!-- jpg,png, gif to webp -->
	<?php horsetools_toggle( 'media-webp1', __( 'Convert JPG and PNG to WEBP', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Convert images to WEBP upon upload',
	) ); ?>

	<p class="ht-keo">
	<input type="range" name="horsetools_settings[media-webp11]" min="10" max="90" value="<?php if(!empty($horsetools_options['media-webp11'])){echo sanitize_text_field($horsetools_options['media-webp11']);} else {echo sanitize_text_field('60');} ?>" class="htslide" data-index="11">
	<span><?php _e('Compression level (', 'horse-tools'); ?><b><span id="demo11"></span></b> - 90)</span>
	</p>
	
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Convert JPG and PNG images to WEBP upon upload, and compress the images according to the configuration you enter', 'horse-tools'); ?></p>
  
  <h3><i class="ti ti-file-zip"></i> <?php _e('Convert images to AVIF upon upload', 'horse-tools') ?></h3>
	<!-- jpg,png, gif to avif -->
	<?php horsetools_toggle( 'media-avif1', __( 'Allow uploading AVIF image format', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Convert images to AVIF upon upload',
	) ); ?>
	<?php horsetools_toggle( 'media-avif2', __( 'Convert JPG and PNG to AVIF', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Convert images to AVIF upon upload',
		'parent'  => 'media-avif1',
	) ); ?>

	<p class="ht-keo">
	<input type="range" name="horsetools_settings[media-avif21]" min="10" max="90" value="<?php if(!empty($horsetools_options['media-avif21'])){echo sanitize_text_field($horsetools_options['media-avif21']);} else {echo sanitize_text_field('60');} ?>" class="htslide" data-index="12">
	<span><?php _e('Compression level (', 'horse-tools'); ?><b><span id="demo12"></span></b> - 90)</span>
	</p>
	
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Convert JPG and PNG images to AVIF upon upload, and compress the images according to the configuration you enter', 'horse-tools'); ?><br>
	<b><?php _e('Only supported from PHP 8.1 and above', 'horse-tools'); ?></b>
	</p>
  
  <h3><i class="ti ti-crop"></i> <?php _e('Limits the size of JPG, PNG, WEBP, AVIF images when uploading', 'horse-tools') ?></h3>
	<!-- upload hinh anh 1 -->
	<?php horsetools_toggle( 'media-zip2', __( 'Limit JPG, PNG, WEBP, AVIF images', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Limits the size of JPG, PNG, WEBP, AVIF images when uploading',
	) ); ?>

	<p style="display:flex;align-items:center;">
	<input class="ht-input-small" placeholder="" name="horsetools_settings[media-zip21]" type="number" value="<?php if(!empty($horsetools_options['media-zip21'])){echo sanitize_text_field($horsetools_options['media-zip21']);} ?>"/>
	<span style="margin-right:7px;"><?php _e('Max width', 'horse-tools'); ?></span>
	<input class="ht-input-small" placeholder="" name="horsetools_settings[media-zip22]" type="number" value="<?php if(!empty($horsetools_options['media-zip22'])){echo sanitize_text_field($horsetools_options['media-zip22']);} ?>"/>
	<span><?php _e('Max height', 'horse-tools'); ?></span>
	</p>
	
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('Limits the maximum width and height of JPG, PNG, WEBP images upon upload. You can leave it blank if you want to keep the original size', 'horse-tools'); ?></p>	
	
  <h3><i class="ti ti-frame"></i> <?php _e('Automatically add a frame when uploading an image', 'horse-tools') ?></h3>
	<!-- them logo vao hinh anh -->
	<?php horsetools_toggle( 'media-cutop1', __( 'Enable the picture frame', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Automatically add a frame when uploading an image',
	) ); ?>
	<?php horsetools_toggle( 'media-cutop-hook', __( 'Process cropped images', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Automatically add a frame when uploading an image',
	) ); ?>
	<!-- khung -->
	<div id="ht-imgstyle2" class="ht-imgstyle">
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/khung/1.png'); ?>" data-value="1" class="<?php if(isset($horsetools_options['media-cutop11']) && $horsetools_options['media-cutop11'] == '1') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/khung/2.png'); ?>" data-value="2" class="<?php if(isset($horsetools_options['media-cutop11']) && $horsetools_options['media-cutop11'] == '2') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/khung/3.png'); ?>" data-value="3" class="<?php if(isset($horsetools_options['media-cutop11']) && $horsetools_options['media-cutop11'] == '3') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/khung/4.png'); ?>" data-value="4" class="<?php if(isset($horsetools_options['media-cutop11']) && $horsetools_options['media-cutop11'] == '4') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/khung/5.png'); ?>" data-value="5" class="<?php if(isset($horsetools_options['media-cutop11']) && $horsetools_options['media-cutop11'] == '5') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/khung/6.png'); ?>" data-value="6" class="<?php if(isset($horsetools_options['media-cutop11']) && $horsetools_options['media-cutop11'] == '6') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/khung/7.png'); ?>" data-value="7" class="<?php if(isset($horsetools_options['media-cutop11']) && $horsetools_options['media-cutop11'] == '7') echo 'selected'; ?>" />
		<img src="<?php echo esc_url(HORSETOOLS_URL .'img/khung/8.png'); ?>" data-value="8" class="<?php if(isset($horsetools_options['media-cutop11']) && $horsetools_options['media-cutop11'] == '8') echo 'selected'; ?>" />
	</div>
	<input type="hidden" name="horsetools_settings[media-cutop11]" id="cutop11" value="<?php if(!empty($horsetools_options['media-cutop11'])){echo sanitize_text_field($horsetools_options['media-cutop11']);} else {echo sanitize_text_field('1');} ?>" />
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var imgStyles = document.querySelectorAll('#ht-imgstyle2 img');
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
	<input id="ht-add6" class="ht-input-big" name="horsetools_settings[media-cutop12]" type="text" value="<?php if(!empty($horsetools_options['media-cutop12'])){echo sanitize_text_field($horsetools_options['media-cutop12']);} ?>" placeholder="<?php _e('Add picture frame link here', 'horse-tools'); ?>" />
	<button class="ht-selec" data-input-id="ht-add6"><?php _e('Select image', 'horse-tools'); ?></button>
	</p>
	<!-- lat anh -->
	<?php horsetools_toggle( 'media-cutop13', __( 'Flip photo', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Automatically add a frame when uploading an image',
	) ); ?>
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[media-cutop-c1]" type="text" data-coloris value="<?php if(!empty($horsetools_options['media-cutop-c1'])){echo esc_attr($horsetools_options['media-cutop-c1']);} ?>"/>
	<label class="ht-right-text"><?php _e('Overlay color', 'horse-tools'); ?></label>
	</p>
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[media-cutop14]" min="10" max="100" value="<?php if(!empty($horsetools_options['media-cutop14'])){echo sanitize_text_field($horsetools_options['media-cutop14']);} else { echo sanitize_text_field('100');} ?>" class="htslide" data-index="17">
	<span><?php _e('Transparency level', 'horse-tools'); ?> <span id="demo17"></span> %</span>
	</p>
	
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('The feature adds advanced functions when uploading images, such as adding frames, flipping images... allowing you to automatically customize your images during upload. Picture frame (PNG)', 'horse-tools'); ?></p>

  <h3><i class="ti ti-circles-relation"></i> <?php _e('Configure adding a watermark to images upon upload', 'horse-tools') ?></h3>
	<!-- them logo vao hinh anh -->
	<?php horsetools_toggle( 'media-logo1', __( 'Add watermark to images upon upload', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Configure adding a watermark to images upon upload',
	) ); ?>
	<?php horsetools_toggle( 'media-logo-hook', __( 'Process cropped images', 'horse-tools' ), array(
		'tab'     => 'MEDIA',
		'section' => 'Configure adding a watermark to images upon upload',
	) ); ?>
	<p>
	<input class="ht-input-big" name="horsetools_settings[media-logo10]" type="text" value="<?php if(!empty($horsetools_options['media-logo10'])){echo sanitize_text_field($horsetools_options['media-logo10']);} ?>" placeholder="<?php _e('Enter content', 'horse-tools'); ?>" />
	</p>
	
	<p style="display:flex;align-items:center;">
	<input class="ht-input-color" name="horsetools_settings[media-logo10-c1]" type="text" data-coloris value="<?php if(!empty($horsetools_options['media-logo10-c1'])){echo esc_attr($horsetools_options['media-logo10-c1']);} ?>"/>
	<label class="ht-right-text"><?php _e('Text color', 'horse-tools'); ?></label>
	</p>
	
	<p style="display:flex;">
	<input id="ht-add1" class="ht-input-big" name="horsetools_settings[media-logo11]" type="text" value="<?php if(!empty($horsetools_options['media-logo11'])){echo sanitize_text_field($horsetools_options['media-logo11']);} ?>" placeholder="<?php _e('Add the logo image link here', 'horse-tools'); ?>" />
	<button class="ht-selec" data-input-id="ht-add1"><?php _e('Select image', 'horse-tools'); ?></button>
	</p>
	<p>
    <?php 
    $positions = array(
        'Top Left' => 'ht-wtop-left',
        'Top Center' => 'ht-wtop-center',
        'Top Right' => 'ht-wtop-right',
        'Center' => 'ht-wcenter',
        'Bottom Left' => 'ht-wbottom-left',
        'Bottom Center' => 'ht-wbottom-center',
        'Bottom Right' => 'ht-wbottom-right'
    ); 
    $selected_position = isset($horsetools_options['media-logo12']) ? $horsetools_options['media-logo12'] : 'Center'; ?>
    <div class="ht-watermark-sel">
        <?php foreach ($positions as $label => $value) { $is_selected = ($selected_position == $label) ? 'ht-wselected' : ''; ?>
            <label class="ht-watermark-box <?php echo $value . ' ' . $is_selected; ?>">
                <input type="radio" name="horsetools_settings[media-logo12]" value="<?php echo $label; ?>" <?php echo ($is_selected) ? 'checked' : ''; ?>>
                <span class="ht-watermark-label"><?php echo $label; ?></span>
            </label>
        <?php } ?>
    </div>
	</p>
	<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ftwBoxes = document.querySelectorAll('.ht-watermark-box');
        ftwBoxes.forEach(box => {
            box.addEventListener('click', function () {
                ftwBoxes.forEach(b => b.classList.remove('ht-wselected'));
                this.classList.add('ht-wselected');
            });
        });
    });
	</script>
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[media-logo13]" min="10" max="100" value="<?php if(!empty($horsetools_options['media-logo13'])){echo sanitize_text_field($horsetools_options['media-logo13']);} else { echo sanitize_text_field('100');} ?>" class="htslide" data-index="16">
	<span><?php _e('Watermark image compared to main image', 'horse-tools'); ?> <span id="demo16"></span> %</span>
	</p>
	<p class="ht-keo">
	<input type="range" name="horsetools_settings[media-logo14]" min="10" max="100" value="<?php if(!empty($horsetools_options['media-logo14'])){echo sanitize_text_field($horsetools_options['media-logo14']);} else { echo sanitize_text_field('100');} ?>" class="htslide" data-index="15">
	<span><?php _e('Transparency level', 'horse-tools'); ?> <span id="demo15"></span> %</span>
	</p>
	<p class="ht-note"><i class="ti ti-bulb"></i> <?php _e('If you want your uploaded images to be watermarked, please use this function and configure it above. Watermark (PNG, JPG)', 'horse-tools'); ?></p>
</div>	