<?php
/**
 * Font settings, as a section of the Appearance screen.
 *
 * Split out of the font manager so it renders inside the screen's own form.
 * It used to carry a second Save button, identical to the screen's and writing
 * a different option — press the wrong one and the edit is lost with no sign
 * that anything went wrong. The upload form stays separate because it carries
 * a file, but its button says UPLOAD, so there is nothing to mistake.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_fontset_options;
// Built by the upload section in the screen this came from; recomputed here so
// the section never depends on having been rendered after it.
$fontsData = function_exists( 'horsetools_get_uploaded_font_data' ) ? horsetools_get_uploaded_font_data() : array();
if ( empty( $fontsData ) ) {
	printf( '<p class="ht-note"><i class="ti ti-bulb"></i> %s</p>',
		esc_html__( 'Upload a font on the Fonts tab first, then choose where to use it here.', 'horse-tools' ) );
}
?>
			<div class="ht-card">
			  <h3><i class="ti ti-letter-case"></i> <?php _e('Use fonts on the web', 'horse-tools') ?></h3>
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
			</div>
