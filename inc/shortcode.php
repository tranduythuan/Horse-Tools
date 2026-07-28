<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } 
global $horsetools_shortcode_options;
/**
 * Does the current user hold the given role, or a more privileged one?
 *
 * Ranked by the standard WordPress role hierarchy. An unknown role slug (from
 * a custom role) is matched exactly rather than ranked, which is the safe
 * reading — it never grants more than was asked for.
 */
function horsetools_user_meets_role( $required_role ) {
    if ( ! is_user_logged_in() ) {
        return false;
    }
    if ( current_user_can( 'manage_options' ) ) {
        return true;
    }

    $user = wp_get_current_user();
    $roles = (array) $user->roles;

    if ( in_array( $required_role, $roles, true ) ) {
        return true;
    }

    $rank = array(
        'subscriber'    => 1,
        'contributor'   => 2,
        'author'        => 3,
        'editor'        => 4,
        'shop_manager'  => 4,
        'administrator' => 5,
    );
    if ( ! isset( $rank[ $required_role ] ) ) {
        return false;
    }
    foreach ( $roles as $role ) {
        if ( isset( $rank[ $role ] ) && $rank[ $role ] >= $rank[ $required_role ] ) {
            return true;
        }
    }
    return false;
}

# shortcode an noi dung theo nhom
if (isset($horsetools_shortcode_options['shortcode-s1'])){
function horsetools_content_pro($atts, $content = null) {
    global $horsetools_shortcode_options;
    $roleuser = !empty($horsetools_shortcode_options['shortcode-s11']) ? sanitize_key($horsetools_shortcode_options['shortcode-s11']) : 'subscriber';
    $locked_content = !empty($horsetools_shortcode_options['shortcode-s12']) ? wp_kses_post($horsetools_shortcode_options['shortcode-s12']) : __('This content is locked! You need to log in to view', 'horse-tools');
    // shortcode-s11 holds a ROLE slug, so it must be compared against the
    // user's roles — not passed to current_user_can(), which expects a
    // capability. The old form happened to work only for a user whose role
    // slug matched exactly, so with the default "subscriber" an Editor was
    // denied content that was meant for "subscriber and above".
    if ( horsetools_user_meets_role( $roleuser ) ) {
        return '<div>'. do_shortcode($content) .'</div>';
    } else {
        return '<div class="ht-vip">' . $locked_content . '</div><style>.ht-vip{box-sizing: border-box;background: #ffb9905c;border: 1px solid #ff7829bf;padding: 30px;border-radius: 5px;font-weight: bold;color: #e35602}</style>';
    }
}
add_shortcode('vip', 'horsetools_content_pro');
}
# shortcode chữ ký
if (isset($horsetools_shortcode_options['shortcode-s2'])){
function horsetools_sign_shortcode(){
	global $horsetools_shortcode_options;
	$shortcode_s21 = !empty($horsetools_shortcode_options['shortcode-s21']) ? $horsetools_shortcode_options['shortcode-s21'] : '';
    return '<div>'. do_shortcode(wpautop($shortcode_s21)) .'</div>'; 
}
add_shortcode('sign', 'horsetools_sign_shortcode');
}
# shortcode titday
if (isset($horsetools_shortcode_options['shortcode-s3'])){
// titday
function horsetools_dateday_shortcode(){
	global $horsetools_shortcode_options;
	if(isset($horsetools_shortcode_options['shortcode-s31']) && $horsetools_shortcode_options['shortcode-s31'] == 'EN'){
	$date = date_i18n('Y/m/d');	
	} else {
    $date = date_i18n('d/m/Y');
	}
    return $date;
}
add_shortcode('titday', 'horsetools_dateday_shortcode');
// titmoth
function horsetools_datemonth_shortcode(){
	global $horsetools_shortcode_options;
	if(isset($horsetools_shortcode_options['shortcode-s31']) && $horsetools_shortcode_options['shortcode-s31'] == 'EN'){
	$date = date_i18n('Y/m');	
	} else {
    $date = date_i18n('m/Y');
	}
    return $date;
}
add_shortcode('titmonth', 'horsetools_datemonth_shortcode');
// tityear
function horsetools_dateyear_shortcode(){
    $date = date_i18n('Y');
    return $date;
}
add_shortcode('tityear', 'horsetools_dateyear_shortcode');
}
# shortcode gget
if (isset($horsetools_shortcode_options['shortcode-s4'])){
function horsetools_gget_shortcode($atts, $content = null) {
	global $horsetools_shortcode_options;
	$time = !empty($horsetools_shortcode_options['shortcode-s41']) ? absint($horsetools_shortcode_options['shortcode-s41']) : '10';
	$win = isset($horsetools_shortcode_options['shortcode-s4a']) ? 'true' : 'false';
    $atts = shortcode_atts(
        array(
            'url' => '', 
			'aff' => 'javascript:void(0);', 
            'timer' => $time,     
            'window' => $win,  
        ),
        $atts,
        'gget'
    );
    if (empty($content)) {
        $content = __('Download', 'horse-tools');
    }
	$target_attr = !empty($atts['aff']) && $atts['aff'] !== 'javascript:void(0);' ? ' target="_blank"' : '';
    ob_start();
    ?>
    <div class="horseggetpro" data-secon="<?php _e('Please wait', 'horse-tools'); ?>" data-next="<?php _e('Continue', 'horse-tools'); ?>">
        <a class="horsegget horsegetskin"
		   <?php // esc_url, not esc_attr: esc_attr stops attribute breakout but
		         // happily passes javascript:, and this value comes from a
		         // shortcode attribute in post content, i.e. from any Author. ?>
		   href="<?php echo esc_url($atts['aff']); ?>"
		   <?php echo $target_attr; ?>
           data-timer="<?php echo esc_attr($atts['timer']); ?>" 
           data-link="<?php echo base64_encode($atts['url']); ?>"  
           data-window="<?php echo esc_attr($atts['window']); ?>">
           <span class="ggettext"><svg xmlns="http://www.w3.org/2000/svg" width="25px" height="25px" viewBox="0 0 512 512"><path fill="currentColor" d="M376 160H272v153.37l52.69-52.68a16 16 0 0 1 22.62 22.62l-80 80a16 16 0 0 1-22.62 0l-80-80a16 16 0 0 1 22.62-22.62L240 313.37V160H136a56.06 56.06 0 0 0-56 56v208a56.06 56.06 0 0 0 56 56h240a56.06 56.06 0 0 0 56-56V216a56.06 56.06 0 0 0-56-56ZM272 48a16 16 0 0 0-32 0v112h32Z"/></svg> <?php echo esc_html($content); ?></span>
        </a>
    </div>
    <?php
    $output = ob_get_clean();
    return $output;
}
add_shortcode('gget', 'horsetools_gget_shortcode');
function horsetools_gget_enqueue() {
	wp_enqueue_style( 'horsegget', HORSETOOLS_URL . 'link/shortcode/horsegget.css', array(), HORSETOOLS_VERSION);
	wp_enqueue_script( 'horsegget', HORSETOOLS_URL . 'link/shortcode/horsegget.js', array(), HORSETOOLS_VERSION, true);
}
add_action('wp_enqueue_scripts', 'horsetools_gget_enqueue');
function horsetools_gget_shortcode_css(){
	global $horsetools_shortcode_options;
	$colorbg = !empty($horsetools_shortcode_options['shortcode-s42']) ? '--ggetcolor:'. horsetools_css_color($horsetools_shortcode_options['shortcode-s42']) .';' : NULL;
	$colorbo = !empty($horsetools_shortcode_options['shortcode-s43']) && !empty($horsetools_shortcode_options['shortcode-s44']) ? 'a.horsegget.horsegetskin {border-bottom:'. horsetools_css_number($horsetools_shortcode_options['shortcode-s44']) .'px solid '. horsetools_css_color($horsetools_shortcode_options['shortcode-s43']) .' !important;}' : NULL;
	$borderru = !empty($horsetools_shortcode_options['shortcode-s45']) ? '--ggetborra:'. horsetools_css_number($horsetools_shortcode_options['shortcode-s45']) .'px;' : NULL;
	$center = isset($horsetools_shortcode_options['shortcode-s4b']) ? '.horseggetpro {text-align:center !important;}' : NULL;
	echo '<style>:root{'. $colorbg . $borderru .'}'. $colorbo . $center .'</style>';
}
add_action('wp_head', 'horsetools_gget_shortcode_css');
}

