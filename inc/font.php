<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
# tai font len wp
// dương dan
function horsetools_font_upload_dir($dir){
    return array(
        'path' => $dir['basedir'] . '/horsetools-fonts',
        'url' => $dir['baseurl'] . '/horsetools-fonts',
        'subdir' => '/horsetools-fonts',
    ) + $dir;
}
// cho phep tai file font
function horsetools_engine_font_filetypes( $data, $file, $filename, $mimes, $real_mime ) {
	if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
	return $data;
	}
	$wp_file_type = wp_check_filetype( $filename, $mimes );
	if ( 'woff2' === $wp_file_type['ext'] ) {
	$data['ext'] = 'woff2';
	$data['type'] = 'font/woff2';
	}
	if ( 'ttf' === $wp_file_type['ext'] ) {
	$data['ext'] = 'ttf';
	$data['type'] = 'font/ttf';
	}
	if ( 'otf' === $wp_file_type['ext'] ) {
	$data['ext'] = 'otf';
	$data['type'] = 'font/otf';
	}
	if ( 'off' === $wp_file_type['ext'] ) {
	$data['ext'] = 'off';
	$data['type'] = 'font/off';
	}
	return $data;
	}
add_filter( 'wp_check_filetype_and_ext', 'horsetools_engine_font_filetypes', 10, 5 );
function horsetools_allow_custom_mime_types( $mimes ) {
    $mimes['ttf'] = 'font/ttf';   
    $mimes['woff2'] = 'font/woff2'; 
    $mimes['otf'] = 'font/otf';   
    $mimes['off'] = 'font/off';    
    return $mimes;
}
add_filter( 'upload_mimes', 'horsetools_allow_custom_mime_types' );
// Upload Font
function horsetools_upload_font($file){
    if (!isset($_POST['horsetools_font_name'])) {
        return;
    }
	// Kiểm tra loại file được tải lên
    $allowed_formats = array('woff2', 'ttf', 'otf', 'off');
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($file_ext), $allowed_formats)) {
        return;
    }
    $font_name = $_POST['horsetools_font_name'];
    $upload_dir = wp_upload_dir();
    $custom_dir = $upload_dir['basedir'] . '/horsetools-fonts';
    if (!file_exists($custom_dir)) {
        wp_mkdir_p($custom_dir);
    }
    if (!function_exists('wp_handle_upload')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    add_filter('upload_dir', 'horsetools_font_upload_dir');
    $upload_overrides = array('test_form' => false);
    $movefile = wp_handle_upload($file, $upload_overrides, $custom_dir);
    remove_filter('upload_dir', 'horsetools_font_upload_dir');
    if ($movefile && !isset($movefile['error'])) {
        horsetools_save_font_entry_to_db($font_name, $movefile['url'], $movefile['file']);
        horsetools_overwrite_font_style($custom_dir);
        echo "1";
    } else {
        echo $movefile['error'];
    }
}
// Overwrite Font File
function horsetools_overwrite_font_style($upload_dir){
    ob_start();
    $fontsData = horsetools_get_uploaded_font_data();
    if (!empty($fontsData)):
        foreach ($fontsData as $key => $fontData): ?>
	@font-face {
	    font-family: '<?php echo esc_html($fontData['font_name']) ?>';
	    src: <?php if (file_exists($fontData['font_url'])) {?>url('<?php echo wp_make_link_relative($fontData['font_url']) ?>'),<?php }?>
	    url('<?php echo wp_make_link_relative($fontData['font_url']) ?>') format("truetype");
	}
	.font-demo.<?php echo esc_html($fontData['font_name']) ?>{
		font-family: '<?php echo esc_html($fontData['font_name']) ?>', sans-serif  !important;
	}
	<?php endforeach;
    endif;
    $content = ob_get_contents();
    file_put_contents($upload_dir . '/horsetools-fonts.css', $content);
    ob_end_clean();
}
// get font data
function horsetools_get_uploaded_font_data(){
    $fontsRawData = get_option('horsetools_font_settings');
    return json_decode($fontsRawData, true);
}
// get ver data
function horsetools_fver(){
    $fontsData = horsetools_get_uploaded_font_data();
    $latestKey = null;
    if (!empty($fontsData)){
        foreach ($fontsData as $key => $fontData){
            $latestKey = $key;
        }
    }
    return $latestKey;
}
// sever link font data
function horsetools_save_font_entry_to_db($font_name, $font_url, $font_path){
    $fontsData = horsetools_get_uploaded_font_data();
    if (empty($fontsData)):
        $fontsData = array();
    endif;
    $fontArrayKey = date('ymdhis');
    $fontsData[$fontArrayKey] = array(
        'font_name' => sanitize_title($font_name),
        'font_url' => $font_url,
        'font_path' => $font_path,
    );
    $updateFontData = json_encode($fontsData);
    update_option('horsetools_font_settings', $updateFontData);
}
// delete font
function horsetools_delete_font() {
    if ( ! current_user_can('manage_options') ) {
        return array( 'status' => __('Insufficient permissions', 'horse-tools') );
    }
    $key_to_delete = sanitize_key( isset($_GET['delete_font_key']) ? $_GET['delete_font_key'] : '' );
    check_admin_referer( 'horsetools_delete_font_' . $key_to_delete );
    $upload_dir = wp_upload_dir();
    $custom_dir = $upload_dir['basedir'] . '/horsetools-fonts';
    $fontsData = horsetools_get_uploaded_font_data();
    if (isset($fontsData[$key_to_delete])) {
        $font_path = realpath($fontsData[$key_to_delete]['font_path']);
        // Only ever delete inside the fonts folder. The path comes out of an
        // option, so today it is only ever one this plugin wrote — but that is
        // an assumption about every other piece of code on the site, and the
        // cost of not assuming it is one comparison. realpath() has already
        // resolved any ".." by this point, so comparing prefixes is enough.
        $custom_real = realpath($custom_dir);
        $inside      = $font_path && $custom_real
            && 0 === strpos($font_path, $custom_real . DIRECTORY_SEPARATOR);
        if ($inside && is_file($font_path)) {
            if (@unlink($font_path)) {
                $return['status'] = __('Deleted successfully', 'horse-tools');
            } else {
                $return['status'] = __('Failed to delete the file', 'horse-tools');
            }
        } else {
            $return['status'] = __('The file path is invalid or not a file', 'horse-tools');
        }
        unset($fontsData[$key_to_delete]);
        $updateFontData = json_encode($fontsData);
        update_option('horsetools_font_settings', $updateFontData);
    } else {
        $return['status'] = __('Font does not exist', 'horse-tools');
    }
    horsetools_overwrite_font_style($custom_dir);
    return $return;
}
// ajax upload file font
function horsetools_upload_fonts(){
	check_ajax_referer('horsetools_font_nonce', 'security');
	if (!current_user_can('manage_options')){
        wp_die(__('Insufficient permissions', 'horse-tools'));
    }
    $file = $_FILES["horsetools_upload_file"];
    $result = horsetools_upload_font($file);
    echo $result;
    die();
}
add_action('wp_ajax_horsetools_upload_fonts', 'horsetools_upload_fonts');
// add css admin
function horsetools_font_customize_enqueue() {
	$upload_dir = wp_get_upload_dir();
    $font_css_file = $upload_dir['basedir'] . '/horsetools-fonts/horsetools-fonts.css';
    if (file_exists($font_css_file) && filesize($font_css_file) > 0) {
	wp_enqueue_style('horsetools-fonts', wp_make_link_relative(wp_get_upload_dir()['baseurl']. '/horsetools-fonts/horsetools-fonts.css'), array(), horsetools_fver());
	}
}
add_action( 'admin_head', 'horsetools_font_customize_enqueue' );
add_action('wp_enqueue_scripts', 'horsetools_font_customize_enqueue', 101);
# set font tren web
function horsetools_font_sethead() {
	global $horsetools_fontset_options;
	$fontsData = horsetools_get_uploaded_font_data();
	if (!empty($fontsData)){
	$p_contents = array(
	'body div',
	'body p',
	'body a',
	'body span',
	'body button',
	'body input',
	'body textarea',
	'body select',
	'body h1',
	'body h2',
	'body h3',
	'body h4',
	'body h5',
	'bdy h6',
	);
	echo '<style>body #wpadminbar a, body #wpadminbar{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell, Helvetica Neue",sans-serif !important}';
	for ($i = 1; $i <= 14; $i++) { 
		if(isset($horsetools_fontset_options['font' . $i]) && $horsetools_fontset_options['font' . $i] !== 'Default'){
			$ht_font_value = preg_replace('/[<>{};\\\\\/()@]/', '', wp_strip_all_tags( (string) $horsetools_fontset_options['font' . $i] ));
			$ht_font_value = esc_html( trim( $ht_font_value ) );
			if ( '' !== $ht_font_value ) {
				echo $p_contents[$i - 1] ."{ font-family: 'Dashicons', ". $ht_font_value ." !important;}";
			}
		}
	}
	echo '</style>';
	}
}
add_action( 'wp_head', 'horsetools_font_sethead' );
// add font to editor
function horsetools_custom_fonts($init) {
    $fontsData = horsetools_get_uploaded_font_data();
    $custom_fonts = '';
    if (!empty($fontsData)) {
        foreach ($fontsData as $fontData) {
            $custom_fonts .= $fontData['font_name'] . '=' . $fontData['font_name'] . ', sans-serif;';
        }
    }
    $theme_advanced_fonts = "Andale Mono=andale mono,times;" .
                            "Arial=arial,helvetica,sans-serif;" .
                            "Arial Black=arial black,avant garde;" .
                            "Book Antiqua=book antiqua,palatino;" .
                            "Comic Sans MS=comic sans ms,sans-serif;" .
                            "Courier New=courier new,courier;" .
                            "Georgia=georgia,palatino;" .
                            "Helvetica=helvetica;" .
                            "Impact=impact,chicago;" .
                            "Symbol=symbol;" .
                            "Tahoma=tahoma,arial,helvetica,sans-serif;" .
                            "Terminal=terminal,monaco;" .
                            "Times New Roman=times new roman,times;" .
                            "Trebuchet MS=trebuchet ms,geneva;" .
                            "Verdana=verdana,geneva;" .
                            "Webdings=webdings;" .
                            "Wingdings=wingdings,zapf dingbats";
    if (!empty($custom_fonts)) {
        $theme_advanced_fonts .= ';' . $custom_fonts;
    }
    $init['font_formats'] = $theme_advanced_fonts;
    return $init;
}
add_filter('tiny_mce_before_init', 'horsetools_custom_fonts');
