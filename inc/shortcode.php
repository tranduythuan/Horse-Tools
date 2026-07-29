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

# shortcode icon — Tabler Icons (MIT), bundled with the plugin
if ( isset( $horsetools_shortcode_options['shortcode-s5'] ) ) {
	/**
	 * [ht-icon name="heart" size="24" color="#e11" spin="1" label="Favourite"]
	 *
	 * Renders a single Tabler icon anywhere shortcodes run — post content,
	 * widgets, menus with a shortcode filter. `name` is the Tabler slug (with or
	 * without the ti- prefix). The picker on the Shortcode screen lists every
	 * valid name and copies the finished tag for you.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Safe <i> markup, or '' when the name is missing/invalid.
	 */
	function horsetools_icon_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'name'  => '',
				'size'  => '',
				'color' => '',
				'spin'  => '',
				'label' => '',
				'class' => '',
			),
			$atts,
			'ht-icon'
		);

		// Accept "ti-heart" or "heart"; keep only a safe slug. An unknown slug
		// simply renders nothing rather than breaking the page.
		$name = strtolower( trim( (string) $atts['name'] ) );
		$name = preg_replace( '/^ti-/', '', $name );
		$name = preg_replace( '/[^a-z0-9-]/', '', $name );
		if ( '' === $name ) {
			return '';
		}

		// Load the icon font only on pages that actually place an icon. The
		// style is registered on wp_enqueue_scripts; enqueuing it here (during
		// the_content) makes WordPress print it in the footer.
		wp_enqueue_style( 'horsetools-tabler' );

		$classes = 'ti ti-' . $name;
		if ( '' !== $atts['spin'] && '0' !== $atts['spin'] && 'false' !== $atts['spin'] ) {
			$classes .= ' ht-icon-spin';
		}
		if ( '' !== trim( (string) $atts['class'] ) ) {
			$extra = array_filter( array_map( 'sanitize_html_class', preg_split( '/\s+/', trim( (string) $atts['class'] ) ) ) );
			if ( $extra ) {
				$classes .= ' ' . implode( ' ', $extra );
			}
		}

		$style = '';
		if ( '' !== $atts['size'] ) {
			$px = (int) $atts['size'];
			if ( $px > 0 && $px <= 400 ) {
				$style .= 'font-size:' . $px . 'px;';
			}
		}
		if ( '' !== $atts['color'] ) {
			$color = horsetools_css_color( $atts['color'] );
			if ( '' !== $color ) {
				$style .= 'color:' . $color . ';';
			}
		}

		// Decorative by default (aria-hidden); given a label it becomes an
		// image with an accessible name for screen readers.
		$label = trim( (string) $atts['label'] );
		$a11y  = ( '' !== $label )
			? ' role="img" aria-label="' . esc_attr( $label ) . '"'
			: ' aria-hidden="true"';

		return '<i class="' . esc_attr( $classes ) . '"'
			. ( '' !== $style ? ' style="' . esc_attr( $style ) . '"' : '' )
			. $a11y . '></i>';
	}
	add_shortcode( 'ht-icon', 'horsetools_icon_shortcode' );

	/**
	 * Register (not enqueue) the Tabler font for the front end, plus the spin
	 * keyframes. The shortcode enqueues it on demand.
	 */
	function horsetools_icon_register_assets() {
		wp_register_style( 'horsetools-tabler', HORSETOOLS_URL . 'link/tabler/tabler-icons.css', array(), HORSETOOLS_VERSION );
		wp_add_inline_style(
			'horsetools-tabler',
			'.ht-icon-spin{display:inline-block;animation:ht-icon-spin 1s linear infinite}@keyframes ht-icon-spin{to{transform:rotate(360deg)}}'
		);
	}
	add_action( 'wp_enqueue_scripts', 'horsetools_icon_register_assets' );
}



/* -------------------------------------------------------------------------
 * Custom snippets — the Horse Tools equivalent of Shortcoder.
 *
 * Each snippet is a named block of raw HTML/CSS/JS the admin creates on the
 * Shortcode screen; it is output with [ht-snippet name="slug"]. For sites
 * migrating from Shortcoder, a [sc name="slug"] compatibility tag renders the
 * same snippet so existing post content keeps working after that plugin is
 * switched off. Placeholders in either the Shortcoder %%param%% or the native
 * {{param}} style are filled from the shortcode's own attributes plus a few
 * built-ins.
 *
 * Snippet content is stored and echoed raw. It is only writable through the
 * plugin's own manage_options-gated screen — the same trust model WordPress
 * already applies to a user with unfiltered_html — so it is deliberately not
 * run through wp_kses.
 * ---------------------------------------------------------------------- */

/** All defined snippets: slug => array( title, content, on ). */
function horsetools_snippets_get() {
	$s = get_option( 'horsetools_snippets', array() );
	return is_array( $s ) ? $s : array();
}

/**
 * Evaluate the display conditions shared by snippets and [ht-if].
 *
 * Every condition is opt-in: an empty field means "no restriction", so an
 * unconfigured snippet always shows. Returns true when the content may render.
 *
 * @param array $c device|login|role|no_admin|date_from|date_to.
 * @return bool
 */
function horsetools_condition_passes( array $c ) {
	if ( ! empty( $c['device'] ) ) {
		$mobile = function_exists( 'wp_is_mobile' ) ? wp_is_mobile() : false;
		if ( 'mobile' === $c['device'] && ! $mobile ) {
			return false;
		}
		if ( 'desktop' === $c['device'] && $mobile ) {
			return false;
		}
	}
	if ( ! empty( $c['login'] ) ) {
		$in = is_user_logged_in();
		if ( 'in' === $c['login'] && ! $in ) {
			return false;
		}
		if ( 'out' === $c['login'] && $in ) {
			return false;
		}
	}
	if ( ! empty( $c['no_admin'] ) && current_user_can( 'manage_options' ) ) {
		return false;
	}
	if ( ! empty( $c['role'] ) && ! horsetools_user_meets_role( $c['role'] ) ) {
		return false;
	}
	$today = current_time( 'Y-m-d' );
	if ( ! empty( $c['date_from'] ) && $today < $c['date_from'] ) {
		return false;
	}
	if ( ! empty( $c['date_to'] ) && $today > $c['date_to'] ) {
		return false;
	}
	return true;
}

/**
 * Render one snippet by slug, filling placeholders from $atts + built-ins.
 *
 * @param string $slug
 * @param array  $atts Shortcode attributes (arbitrary keys become params).
 * @return string
 */
function horsetools_render_snippet( $slug, $atts ) {
	$snips = horsetools_snippets_get();
	$slug  = sanitize_key( $slug );
	if ( '' === $slug || empty( $snips[ $slug ] ) ) {
		return '';
	}
	$s = $snips[ $slug ];
	if ( empty( $s['on'] ) ) {
		return '';
	}
	if ( ! horsetools_condition_passes( array(
		'device'    => isset( $s['device'] ) ? $s['device'] : '',
		'login'     => isset( $s['login'] ) ? $s['login'] : '',
		'role'      => isset( $s['role'] ) ? $s['role'] : '',
		'no_admin'  => ! empty( $s['no_admin'] ),
		'date_from' => isset( $s['date_from'] ) ? $s['date_from'] : '',
		'date_to'   => isset( $s['date_to'] ) ? $s['date_to'] : '',
	) ) ) {
		return '';
	}
	$content = (string) $s['content'];
	$atts    = is_array( $atts ) ? $atts : array();

	$built = array(
		'currentyear' => date_i18n( 'Y' ),
		'currentdate' => date_i18n( get_option( 'date_format' ) ),
		'postid'      => (string) get_the_ID(),
		'posttitle'   => get_the_title(),
		'sitename'    => get_bloginfo( 'name' ),
		'siteurl'     => home_url( '/' ),
	);

	$content = preg_replace_callback(
		'/%%([a-z0-9_\-]+)%%|\{\{([a-z0-9_\-]+)\}\}/i',
		function ( $m ) use ( $atts, $built ) {
			$key = strtolower( '' !== $m[1] ? $m[1] : $m[2] );
			if ( array_key_exists( $key, $atts ) ) {
				return (string) $atts[ $key ];
			}
			if ( isset( $built[ $key ] ) ) {
				return (string) $built[ $key ];
			}
			return '';
		},
		$content
	);

	return do_shortcode( $content );
}

function horsetools_snippet_shortcode( $atts ) {
	$atts = is_array( $atts ) ? $atts : array();
	$name = isset( $atts['name'] ) ? $atts['name'] : '';
	return horsetools_render_snippet( $name, $atts );
}
add_shortcode( 'ht-snippet', 'horsetools_snippet_shortcode' );

/**
 * Shortcoder [sc name="…"] compatibility, registered late and only if nothing
 * else already owns the `sc` tag — so an active Shortcoder install always wins
 * and there is no conflict; we only step in once it is gone.
 */
add_action( 'init', function () {
	if ( ! shortcode_exists( 'sc' ) ) {
		add_shortcode( 'sc', 'horsetools_snippet_shortcode' );
	}
}, 99 );

/**
 * [ht-if]…[ht-else]…[/ht-if] — show content only when conditions are met.
 *
 * [ht-if device="mobile" login="in" role="editor" admin="hide" from="2026-01-01" to="2026-12-31"]
 *   Shown when all conditions pass.
 * [ht-else]
 *   Shown otherwise (optional).
 * [/ht-if]
 */
function horsetools_if_shortcode( $atts, $content = '' ) {
	$atts = shortcode_atts(
		array( 'device' => '', 'login' => '', 'role' => '', 'admin' => '', 'from' => '', 'to' => '' ),
		$atts,
		'ht-if'
	);
	$parts = preg_split( '/\[ht-else\s*\/?\]/', (string) $content, 2 );
	$yes   = $parts[0];
	$no    = isset( $parts[1] ) ? $parts[1] : '';
	$pass  = horsetools_condition_passes( array(
		'device'    => strtolower( $atts['device'] ),
		'login'     => strtolower( $atts['login'] ),
		'role'      => sanitize_key( $atts['role'] ),
		'no_admin'  => in_array( strtolower( (string) $atts['admin'] ), array( 'hide', '1', 'yes', 'true' ), true ),
		'date_from' => $atts['from'],
		'date_to'   => $atts['to'],
	) );
	return do_shortcode( $pass ? $yes : $no );
}
add_shortcode( 'ht-if', 'horsetools_if_shortcode' );
add_shortcode( 'ht-else', '__return_empty_string' ); // harmless if used on its own

/**
 * Run shortcodes in a few extra contexts, each behind its own toggle. Core
 * runs them in post content and text widgets but not these.
 */
if ( isset( $horsetools_shortcode_options['shortcode-inwidget'] ) ) {
	add_filter( 'widget_text', 'do_shortcode', 11 );
	add_filter( 'widget_block_content', 'do_shortcode', 11 );
}
if ( isset( $horsetools_shortcode_options['shortcode-inexcerpt'] ) ) {
	add_filter( 'the_excerpt', 'do_shortcode', 11 );
	add_filter( 'get_the_excerpt', 'do_shortcode', 11 );
}
if ( isset( $horsetools_shortcode_options['shortcode-inmenu'] ) ) {
	add_filter( 'wp_nav_menu_items', 'do_shortcode', 11 );
}
if ( isset( $horsetools_shortcode_options['shortcode-interm'] ) ) {
	add_filter( 'term_description', 'do_shortcode', 11 );
	add_filter( 'category_description', 'do_shortcode', 11 );
}

require_once HORSETOOLS_DIR . 'inc/shortcodes-lib.php';
