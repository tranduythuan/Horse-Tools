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
           <?php // esc_url_raw restricts the download target to http/https before it is
                 // base64-encoded, so an Author cannot smuggle a javascript:/data: URL
                 // past kses into the client-side link builder (which is XSS-hardened too). ?>
           data-link="<?php echo base64_encode( esc_url_raw( $atts['url'], array( 'http', 'https' ) ) ); ?>"  
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

/* -------------------------------------------------------------------------
 * Editor quick-insert.
 *
 * A writer can't be expected to remember every snippet slug. This adds a
 * "Shortcode" button beside "Add Media" (the Classic editor and the Classic
 * block) that drops down the site's own snippets and inserts
 * [ht-snippet name="…"] at the cursor — visual editor or Text tab.
 * ---------------------------------------------------------------------- */
function horsetools_snippet_editor_button( $editor_id ) {
	echo ' <button type="button" class="button ht-scpick-btn" data-editor="' . esc_attr( $editor_id ) . '">'
		. '<span class="dashicons dashicons-shortcode" style="font-size:16px;height:16px;vertical-align:text-top;"></span> '
		. esc_html__( 'Shortcode', 'horse-tools' ) . '</button>';
	echo ' <button type="button" class="button ht-tbl-btn" data-editor="' . esc_attr( $editor_id ) . '">'
		. '<span class="dashicons dashicons-editor-table" style="font-size:16px;height:16px;vertical-align:text-top;"></span> '
		. esc_html__( 'Table', 'horse-tools' ) . '</button>';

	// The picker + its script are shared by every editor on the page — print once.
	static $printed = false;
	if ( $printed ) {
		return;
	}
	$printed = true;

	$items = '';
	foreach ( horsetools_snippets_get() as $slug => $snip ) {
		if ( empty( $snip['on'] ) ) {
			continue;
		}
		$title  = ! empty( $snip['title'] ) ? $snip['title'] : $slug;
		$items .= '<a href="#" class="ht-scpick-item" data-sc="' . esc_attr( '[ht-snippet name="' . $slug . '"]' ) . '">'
			. '<strong>' . esc_html( $title ) . '</strong> <code>' . esc_html( $slug ) . '</code></a>';
	}
	if ( '' === $items ) {
		$items = '<p style="margin:10px 12px;color:#646970;">' . esc_html__( 'No snippets yet — create them on the Shortcode screen.', 'horse-tools' ) . '</p>';
	}
	?>
	<div id="ht-scpick" style="display:none;position:absolute;z-index:100050;background:#fff;border:1px solid #c3c4c7;box-shadow:0 2px 10px rgba(0,0,0,.15);border-radius:6px;max-height:340px;overflow:auto;min-width:240px;max-width:380px;">
		<div style="padding:8px 12px;border-bottom:1px solid #eee;font-weight:600;"><?php esc_html_e( 'Insert a snippet', 'horse-tools' ); ?></div>
		<?php echo $items; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<style>
	#ht-scpick .ht-scpick-item{display:block;padding:8px 12px;text-decoration:none;color:#1d2327;border-bottom:1px solid #f0f0f1;}
	#ht-scpick .ht-scpick-item:hover{background:#f0f6fc;}
	#ht-scpick .ht-scpick-item code{color:#646970;font-size:11px;}
	.ht-scpick-btn .dashicons{margin-right:2px;}
	</style>
	<script>
	(function(){
		var pick=document.getElementById('ht-scpick'); if(!pick){return;}
		document.body.appendChild(pick);
		var curEd='';
		function hide(){ pick.style.display='none'; }
		function insert(text){
			if(window.tinymce){ var t=tinymce.get(curEd); if(t && !t.isHidden()){ t.execCommand('mceInsertContent',false,text); hide(); return; } }
			var ta=document.getElementById(curEd);
			if(ta && typeof ta.value==='string'){ var s=ta.selectionStart||0,e=ta.selectionEnd||0; ta.value=ta.value.slice(0,s)+text+ta.value.slice(e); ta.selectionStart=ta.selectionEnd=s+text.length; ta.focus(); }
			hide();
		}
		document.addEventListener('click',function(ev){
			var t=ev.target;
			var btn=t.closest ? t.closest('.ht-scpick-btn') : null;
			if(btn){ ev.preventDefault(); curEd=btn.getAttribute('data-editor')||''; var r=btn.getBoundingClientRect();
				pick.style.top=(r.bottom+window.scrollY+4)+'px'; pick.style.left=(r.left+window.scrollX)+'px';
				pick.style.display=(pick.style.display==='none'?'block':'none'); return; }
			var tbl=t.closest ? t.closest('.ht-tbl-btn') : null;
			if(tbl){ ev.preventDefault(); curEd=tbl.getAttribute('data-editor')||''; if(window.htTableBuilder){ window.htTableBuilder.open(function(sc){ insert(sc); }); } else { alert('Table builder not loaded'); } return; }
			var item=t.closest ? t.closest('.ht-scpick-item') : null;
			if(item){ ev.preventDefault(); insert(item.getAttribute('data-sc')); return; }
			if(!t.closest || !t.closest('#ht-scpick')){ hide(); }
		});
	})();
	</script>
	<?php
}
add_action( 'media_buttons', 'horsetools_snippet_editor_button' );

/* -------------------------------------------------------------------------
 * The same quick-insert for the block editor (Gutenberg): a dynamic
 * "Horse Tools snippet" block. You pick a snippet from a dropdown; it is
 * rendered server-side as [ht-snippet name="…"], so there is nothing to keep in
 * sync and no block-validation to go stale.
 * ---------------------------------------------------------------------- */
function horsetools_register_snippet_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return; // WP < 5.0, no block editor.
	}
	wp_register_script(
		'horsetools-snippet-block',
		HORSETOOLS_URL . 'link/ht-snippet-block.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components' ),
		HORSETOOLS_VERSION,
		true
	);
	$list = array();
	foreach ( horsetools_snippets_get() as $slug => $snip ) {
		if ( empty( $snip['on'] ) ) {
			continue;
		}
		$list[] = array( 'slug' => $slug, 'title' => ! empty( $snip['title'] ) ? $snip['title'] : $slug );
	}
	wp_localize_script( 'horsetools-snippet-block', 'htSnippetData', array(
		'list'  => $list,
		'pick'  => __( 'Select a snippet', 'horse-tools' ),
		'hint'  => __( 'Pick a snippet to insert', 'horse-tools' ),
		'label' => __( 'Horse Tools snippet', 'horse-tools' ),
	) );
	register_block_type( 'horse-tools/snippet', array(
		'editor_script'   => 'horsetools-snippet-block',
		'attributes'      => array( 'slug' => array( 'type' => 'string', 'default' => '' ) ),
		'render_callback' => 'horsetools_snippet_block_render',
	) );
}
add_action( 'init', 'horsetools_register_snippet_block' );

function horsetools_snippet_block_render( $attrs ) {
	$slug = isset( $attrs['slug'] ) ? preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $attrs['slug'] ) : '';
	if ( '' === $slug ) {
		return '';
	}
	return do_shortcode( '[ht-snippet name="' . $slug . '"]' );
}

/* -------------------------------------------------------------------------
 * Responsive tables — [ht-table]…<table>…</table>…[/ht-table]
 *
 * The Table builder (editor button / block) produces this. The shortcode wraps
 * the builder's plain <table> in a horizontal-scroll container so it never
 * overflows a phone, and adds the striped / compact / mobile-stack classes. The
 * stylesheet is only loaded on singular views whose content actually contains a
 * table, so table-less pages pay nothing.
 * ---------------------------------------------------------------------- */
function horsetools_table_shortcode( $atts, $content = '' ) {
	$a = shortcode_atts( array(
		'id'      => '', // when set, render a stored table from the library
		'stack'   => '0', // 1 = stack each row into a card on small screens
		'striped' => '1',
		'compact' => '0',
		'theme'   => '', // bordered | minimal | lines
		'hcolor'  => '', // blue | green | orange | purple | dark
		'sort'    => '0', // 1 = clickable column sorting on the front end
		'search'  => '0', // 1 = search box above the table
		'page'    => '0', // rows per page (0 = no pagination)
	), $atts, 'ht-table' );

	// Stored, reusable table: [ht-table id="5"]. The saved options win; any
	// striped/theme/etc. attrs on the shortcode are ignored so "edit once,
	// update everywhere" holds.
	if ( '' !== trim( (string) $a['id'] ) ) {
		$t = horsetools_table_get( (int) $a['id'] );
		if ( ! $t || empty( $t['data'] ) ) {
			return '';
		}
		return horsetools_table_render_data(
			$t['data'],
			isset( $t['opts'] ) ? $t['opts'] : array(),
			(int) $a['id'],
			isset( $t['css'] ) ? (string) $t['css'] : ''
		);
	}

	$inner = trim( (string) $content );
	if ( '' === $inner ) {
		return '';
	}
	$cls = 'ht-table';
	if ( '1' === (string) $a['stack'] ) {
		$cls .= ' ht-table-stack';
	}
	if ( '1' === (string) $a['striped'] ) {
		$cls .= ' ht-table-striped';
	}
	if ( '1' === (string) $a['compact'] ) {
		$cls .= ' ht-table-compact';
	}
	if ( in_array( $a['theme'], array( 'bordered', 'minimal', 'lines', 'card', 'dark', 'soft' ), true ) ) {
		$cls .= ' ht-tt-' . $a['theme'];
	}
	if ( in_array( $a['hcolor'], array( 'blue', 'green', 'orange', 'purple', 'dark', 'red', 'teal', 'pink', 'indigo', 'gradblue', 'gradsunset', 'gradocean' ), true ) ) {
		$cls .= ' ht-th-' . $a['hcolor'];
	}
	$fx = horsetools_table_fx_attrs( array(
		'sort'     => '1' === (string) $a['sort'],
		'search'   => '1' === (string) $a['search'],
		'paginate' => (int) $a['page'] > 0,
		'pagesize' => (int) $a['page'],
	) );
	return '<div class="' . esc_attr( $cls ) . '"' . $fx . '><div class="ht-table-scroll">' . do_shortcode( $inner ) . '</div></div>';
}
add_shortcode( 'ht-table', 'horsetools_table_shortcode' );

/** Data attributes that switch on ht-table-fx.js behaviours for one table. */
function horsetools_table_fx_attrs( $opts ) {
	$fx = '';
	if ( ! empty( $opts['sort'] ) ) {
		$fx .= ' data-ht-sort="1"';
	}
	if ( ! empty( $opts['search'] ) ) {
		$fx .= ' data-ht-search="1"';
	}
	$per = isset( $opts['pagesize'] ) ? (int) $opts['pagesize'] : 0;
	if ( ! empty( $opts['paginate'] ) && $per > 0 ) {
		$fx .= ' data-ht-page="' . min( 100, max( 1, $per ) ) . '"';
	}
	return $fx;
}

function horsetools_table_css_maybe() {
	if ( ! is_singular() ) {
		return;
	}
	$p = get_post();
	if ( $p && false !== strpos( (string) $p->post_content, '[ht-table' ) ) {
		wp_enqueue_style( 'horsetools-table', HORSETOOLS_URL . 'link/ht-table.css', array(), HORSETOOLS_VERSION );
		// Sort / search / pagination. ~5 KB vanilla, footer-loaded, and a no-op
		// unless a table on the page carries the data-ht-* attributes.
		wp_enqueue_script( 'horsetools-table-fx', HORSETOOLS_URL . 'link/ht-table-fx.js', array(), HORSETOOLS_VERSION, true );
		wp_localize_script( 'horsetools-table-fx', 'htTableFx', array(
			'search' => __( 'Search the table…', 'horse-tools' ),
			'empty'  => __( 'No matching rows.', 'horse-tools' ),
			'prev'   => __( 'Previous', 'horse-tools' ),
			'next'   => __( 'Next', 'horse-tools' ),
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'horsetools_table_css_maybe' );

/* -------------------------------------------------------------------------
 * The table builder itself — a modal shared by the Classic "Table" media button
 * and the Gutenberg "Horse Tools table" block. Three ways in: type it, paste
 * from Excel/Sheets, or upload a .csv/.xlsx (SheetJS is bundled and loaded only
 * when an Excel file is picked). It emits a [ht-table] shortcode.
 * ---------------------------------------------------------------------- */
function horsetools_table_builder_i18n() {
	return array(
		'title'      => __( 'Insert a table', 'horse-tools' ),
		'manual'     => __( 'Type it in', 'horse-tools' ),
		'paste'      => __( 'Paste from Excel', 'horse-tools' ),
		'upload'     => __( 'Upload a file', 'horse-tools' ),
		'rowsL'      => __( 'Rows', 'horse-tools' ),
		'colsL'      => __( 'Columns', 'horse-tools' ),
		'mkgrid'     => __( 'Build grid', 'horse-tools' ),
		'pasteHint'  => __( 'Copy cells in Excel / Google Sheets (or paste CSV) and paste here:', 'horse-tools' ),
		'uploadHint' => __( 'Choose a .xlsx, .xls or .csv file:', 'horse-tools' ),
		'optHeader'  => __( 'First row is a header', 'horse-tools' ),
		'optStriped' => __( 'Striped rows', 'horse-tools' ),
		'optCompact' => __( 'Compact', 'horse-tools' ),
		'optStack'   => __( 'Stack into cards on mobile', 'horse-tools' ),
		'optFooter'  => __( 'Last row is a total row (pinned to the bottom)', 'horse-tools' ),
		'preview'    => __( 'Preview', 'horse-tools' ),
		'cancel'     => __( 'Cancel', 'horse-tools' ),
		'insert'     => __( 'Insert table', 'horse-tools' ),
		'empty'      => __( 'Nothing to preview yet.', 'horse-tools' ),
		'emptyErr'   => __( 'Add some data first.', 'horse-tools' ),
		'colN'       => __( 'Column', 'horse-tools' ),
		'reading'    => __( 'Reading…', 'horse-tools' ),
		'rows'       => __( 'rows', 'horse-tools' ),
		'readfail'   => __( 'Could not read the file.', 'horse-tools' ),
		'noxlsx'     => __( 'Could not load the Excel reader — save the file as CSV and try again.', 'horse-tools' ),
		'themeL'     => __( 'Style', 'horse-tools' ),
		'themeDefault' => __( 'Default', 'horse-tools' ),
		'themeBordered' => __( 'Bordered', 'horse-tools' ),
		'themeMinimal' => __( 'Minimal', 'horse-tools' ),
		'themeLines' => __( 'Lines only', 'horse-tools' ),
		'themeCard'  => __( 'Card (shadow)', 'horse-tools' ),
		'themeDark'  => __( 'Dark background', 'horse-tools' ),
		'themeSoft'  => __( 'Soft pastel', 'horse-tools' ),
		'hcolorL'    => __( 'Header colour', 'horse-tools' ),
		'cGrey'      => __( 'Grey', 'horse-tools' ),
		'cBlue'      => __( 'Blue', 'horse-tools' ),
		'cGreen'     => __( 'Green', 'horse-tools' ),
		'cOrange'    => __( 'Orange', 'horse-tools' ),
		'cPurple'    => __( 'Purple', 'horse-tools' ),
		'cDark'      => __( 'Dark', 'horse-tools' ),
		'cRed'       => __( 'Red', 'horse-tools' ),
		'cPink'      => __( 'Pink', 'horse-tools' ),
		'cTeal'      => __( 'Teal', 'horse-tools' ),
		'cIndigo'    => __( 'Indigo', 'horse-tools' ),
		'cGradBlue'  => __( 'Gradient blue-violet', 'horse-tools' ),
		'cGradSunset' => __( 'Gradient sunset', 'horse-tools' ),
		'cGradOcean' => __( 'Gradient ocean', 'horse-tools' ),
		'captionL'   => __( 'Caption', 'horse-tools' ),
		'captionPh'  => __( 'optional title above the table', 'horse-tools' ),
		'cssL'       => __( 'Custom CSS', 'horse-tools' ),
		'cssPh'      => '.ht-table-5 td { font-size: 14px; }',
		'mergeHint'  => __( 'Merge cells: type #colspan# to merge into the cell on the left, #rowspan# to merge into the cell above. Formulas: =SUM(B2:B10), also AVG / MIN / MAX.', 'horse-tools' ),
		'sheetL'     => __( 'Google Sheet', 'horse-tools' ),
		'sheetPh'    => __( 'Paste a Google Sheets link (shared: anyone with the link)', 'horse-tools' ),
		'sheetPull'  => __( 'Pull data', 'horse-tools' ),
		'sheetHint'  => __( 'The sheet must be shared as “Anyone with the link can view”. With auto-refresh on, the table updates itself from the sheet.', 'horse-tools' ),
		'syncOff'    => __( 'No auto-refresh', 'horse-tools' ),
		'syncHourly' => __( 'Refresh hourly', 'horse-tools' ),
		'syncDaily'  => __( 'Refresh daily', 'horse-tools' ),
		'sheetRows'  => __( 'rows loaded', 'horse-tools' ),
		'rowAdd'     => __( 'Insert row below', 'horse-tools' ),
		'rowDel'     => __( 'Delete row', 'horse-tools' ),
		'rowUp'      => __( 'Move row up', 'horse-tools' ),
		'rowDown'    => __( 'Move row down', 'horse-tools' ),
		'colAdd'     => __( 'Insert column right', 'horse-tools' ),
		'colDel'     => __( 'Delete column', 'horse-tools' ),
		'colLeft'    => __( 'Move column left', 'horse-tools' ),
		'colRight'   => __( 'Move column right', 'horse-tools' ),
		'optSort'    => __( 'Sortable columns', 'horse-tools' ),
		'optSearch'  => __( 'Search box', 'horse-tools' ),
		'optPage'    => __( 'Pagination', 'horse-tools' ),
		'optPer'     => __( 'Rows/page', 'horse-tools' ),
		'fxNote'     => __( 'Sorting, search and pagination appear on the published page.', 'horse-tools' ),
		'titleSave'  => __( 'Save a table', 'horse-tools' ),
		'save'       => __( 'Save table', 'horse-tools' ),
		'nameL'      => __( 'Table name', 'horse-tools' ),
		'namePh'     => __( 'e.g. Price list', 'horse-tools' ),
		'savedTab'   => __( 'Saved tables', 'horse-tools' ),
		'savedHint'  => __( 'Insert a table you saved earlier:', 'horse-tools' ),
		'savedNone'  => __( 'No saved tables yet.', 'horse-tools' ),
		'blockTitle' => __( 'Horse Tools table', 'horse-tools' ),
		'blockEmpty' => __( 'No table yet.', 'horse-tools' ),
		'blockDone'  => __( 'Table ready — click to edit.', 'horse-tools' ),
		'blockCreate'=> __( 'Create table', 'horse-tools' ),
		'blockEdit'  => __( 'Edit table', 'horse-tools' ),
	);
}

function horsetools_table_register() {
	wp_register_script( 'horsetools-table-builder', HORSETOOLS_URL . 'link/ht-table-builder.js', array(), HORSETOOLS_VERSION, true );
	wp_localize_script( 'horsetools-table-builder', 'htTableI18n', horsetools_table_builder_i18n() );
	// Library access (list of saved tables + save endpoint) for the builder's
	// "Saved tables" tab and the manager screen. Only admins get the nonce.
	$can   = current_user_can( 'manage_options' );
	$store = array();
	foreach ( horsetools_tables_get() as $id => $t ) {
		$store[] = array( 'id' => (int) $id, 'name' => isset( $t['name'] ) ? (string) $t['name'] : ( 'Table ' . $id ) );
	}
	wp_localize_script( 'horsetools-table-builder', 'htTableStore', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => $can ? wp_create_nonce( 'horsetools_tbl' ) : '',
		'canSave' => $can,
		'tables'  => $store,
	) );
	wp_add_inline_script( 'horsetools-table-builder', 'window.htXlsxUrl=' . wp_json_encode( HORSETOOLS_URL . 'link/xlsx.mini.min.js' ) . ';', 'before' );

	if ( function_exists( 'register_block_type' ) ) {
		wp_register_script( 'horsetools-table-block', HORSETOOLS_URL . 'link/ht-table-block.js', array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'horsetools-table-builder' ), HORSETOOLS_VERSION, true );
		register_block_type( 'horse-tools/table', array(
			'editor_script'   => 'horsetools-table-block',
			'attributes'      => array( 'content' => array( 'type' => 'string', 'default' => '' ) ),
			'render_callback' => 'horsetools_table_block_render',
		) );
	}
}
add_action( 'init', 'horsetools_table_register' );

function horsetools_table_block_render( $attrs ) {
	$content = isset( $attrs['content'] ) ? (string) $attrs['content'] : '';
	return '' === trim( $content ) ? '' : do_shortcode( $content );
}

// Classic editor / widgets screen: make the builder available for the media button.
function horsetools_table_builder_classic( $hook ) {
	if ( in_array( $hook, array( 'post.php', 'post-new.php', 'widgets.php' ), true ) || 'horsetools-tables' === horsetools_current_admin_page() ) {
		wp_enqueue_script( 'horsetools-table-builder' );
		// So the modal's live preview shows the real styles (themes, header
		// colour, alignment) exactly as they will appear on the site.
		wp_enqueue_style( 'horsetools-table', HORSETOOLS_URL . 'link/ht-table.css', array(), HORSETOOLS_VERSION );
	}
}
add_action( 'admin_enqueue_scripts', 'horsetools_table_builder_classic' );

/* =========================================================================
 * Stored, reusable tables (Phase 1 of the "TablePress-like" set).
 *
 * A table is saved once in the horsetools_tables option (id => name/data/opts)
 * and inserted anywhere with [ht-table id="N"]. Edit it once on the Tables
 * screen and every post that embeds it updates. Rendering is server-side so the
 * output always reflects the current data + options.
 * ====================================================================== */
function horsetools_tables_get() {
	$t = get_option( 'horsetools_tables', array() );
	return is_array( $t ) ? $t : array();
}
function horsetools_table_get( $id ) {
	$t = horsetools_tables_get();
	return isset( $t[ $id ] ) ? $t[ $id ] : null;
}

/** A cell that is entirely a number (optionally with %, currency) → right-align. */
function horsetools_table_is_num( $v ) {
	$v = trim( (string) $v );
	return '' !== $v && (bool) preg_match( '/^[+\-]?[\d.,\s]+\s*[%đ$₫]?$/u', $v );
}

/** Parse a cell as a number, VN/EU separators aware ("1.790.000", "1,5"). */
function horsetools_table_num( $v ) {
	$v = trim( (string) $v );
	if ( '' === $v || ! preg_match( '/^[+\-]?[\d.,\s]+\s*[%đ$₫]?$/u', $v ) ) {
		return null;
	}
	$s = preg_replace( '/[^\d.,\-]/', '', $v );
	$d = false !== strpos( $s, '.' );
	$c = false !== strpos( $s, ',' );
	if ( $d && $c ) {
		$s = str_replace( ',', '.', str_replace( '.', '', $s ) );
	} elseif ( $d ) {
		$p = explode( '.', $s );
		if ( count( $p ) > 2 || ( 2 === count( $p ) && 3 === strlen( $p[1] ) ) ) {
			$s = str_replace( '.', '', $s );
		}
	} elseif ( $c ) {
		$p = explode( ',', $s );
		if ( count( $p ) > 2 || ( 2 === count( $p ) && 3 === strlen( $p[1] ) ) ) {
			$s = str_replace( ',', '', $s );
		} else {
			$s = str_replace( ',', '.', $s );
		}
	}
	return is_numeric( $s ) ? (float) $s : null;
}

/** "B" → 1, "AA" → 26 (0-based column index from spreadsheet letters). */
function horsetools_table_col_idx( $letters ) {
	$n = 0;
	foreach ( str_split( strtoupper( $letters ) ) as $ch ) {
		$n = $n * 26 + ( ord( $ch ) - 64 );
	}
	return $n - 1;
}

/**
 * Evaluate the safe formula subset: a cell that is exactly
 * =SUM(B2:B10) / =AVG(...) / =MIN(...) / =MAX(...).
 * Regex-matched only — no expression parser, no eval, nothing else runs.
 * Values are read from a snapshot of the ORIGINAL data, so evaluation order
 * doesn't matter and a formula inside its own range is simply ignored
 * (formula text isn't numeric). Results use VN formatting (1.790.000 / 1,50).
 */
function horsetools_table_apply_formulas( $data ) {
	$src = $data;
	foreach ( $data as $r => $row ) {
		foreach ( $row as $c => $cell ) {
			if ( ! is_string( $cell ) || '' === $cell || '=' !== $cell[0] ) {
				continue;
			}
			if ( ! preg_match( '/^=\s*(SUM|AVG|MIN|MAX)\s*\(\s*([A-Z]{1,2})(\d{1,4})\s*:\s*([A-Z]{1,2})(\d{1,4})\s*\)\s*$/i', $cell, $m ) ) {
				continue;
			}
			$fn = strtoupper( $m[1] );
			$c1 = horsetools_table_col_idx( $m[2] );
			$r1 = (int) $m[3] - 1;
			$c2 = horsetools_table_col_idx( $m[4] );
			$r2 = (int) $m[5] - 1;
			if ( $c2 < $c1 ) { $t = $c1; $c1 = $c2; $c2 = $t; }
			if ( $r2 < $r1 ) { $t = $r1; $r1 = $r2; $r2 = $t; }
			$vals = array();
			for ( $ri = $r1; $ri <= $r2; $ri++ ) {
				for ( $ci = $c1; $ci <= $c2; $ci++ ) {
					if ( isset( $src[ $ri ][ $ci ] ) ) {
						$n = horsetools_table_num( $src[ $ri ][ $ci ] );
						if ( null !== $n ) {
							$vals[] = $n;
						}
					}
				}
			}
			if ( empty( $vals ) ) {
				$data[ $r ][ $c ] = 'SUM' === $fn ? '0' : '';
				continue;
			}
			switch ( $fn ) {
				case 'SUM': $res = array_sum( $vals ); break;
				case 'AVG': $res = array_sum( $vals ) / count( $vals ); break;
				case 'MIN': $res = min( $vals ); break;
				default:    $res = max( $vals );
			}
			$data[ $r ][ $c ] = ( floor( $res ) == $res )
				? number_format( $res, 0, ',', '.' )
				: number_format( $res, 2, ',', '.' );
		}
	}
	return $data;
}

/**
 * Merge-cell keywords, TablePress-compatible: a cell containing exactly
 * "#colspan#" merges into the nearest real cell to its LEFT; "#rowspan#"
 * merges into the nearest real cell ABOVE (never across the thead/tbody
 * boundary). Returns [colspanMap, rowspanMap, skipMap] keyed "r:c".
 * A keyword with no valid owner renders as an ordinary empty cell.
 */
function horsetools_table_spans( $data, $has_header, $footer_start = -1 ) {
	$cs = array();
	$rs = array();
	$skip = array();
	$body_start = $has_header ? 1 : 0;
	foreach ( $data as $r => $row ) {
		foreach ( $row as $c => $cell ) {
			$cell = trim( (string) $cell );
			if ( '#colspan#' === $cell ) {
				for ( $cc = $c - 1; $cc >= 0; $cc-- ) {
					$left = trim( (string) ( isset( $data[ $r ][ $cc ] ) ? $data[ $r ][ $cc ] : '' ) );
					if ( '#colspan#' !== $left && '#rowspan#' !== $left ) {
						$key = $r . ':' . $cc;
						$cs[ $key ] = ( isset( $cs[ $key ] ) ? $cs[ $key ] : 1 ) + 1;
						$skip[ $r . ':' . $c ] = true;
						break;
					}
				}
			} elseif ( '#rowspan#' === $cell ) {
				$limit = ( $r >= $body_start ) ? $body_start : 0;
				// The footer (tfoot) is its own section too: a rowspan may not
				// reach from the footer up into the body.
				if ( $footer_start >= 0 && $r >= $footer_start ) {
					$limit = $footer_start;
				}
				for ( $rr = $r - 1; $rr >= $limit; $rr-- ) {
					$up = trim( (string) ( isset( $data[ $rr ][ $c ] ) ? $data[ $rr ][ $c ] : '' ) );
					if ( '#colspan#' !== $up && '#rowspan#' !== $up ) {
						$key = $rr . ':' . $c;
						$rs[ $key ] = ( isset( $rs[ $key ] ) ? $rs[ $key ] : 1 ) + 1;
						$skip[ $r . ':' . $c ] = true;
						break;
					}
				}
			}
		}
	}
	return array( $cs, $rs, $skip );
}

/** Build the responsive table HTML from a 2D data array + options. Mirrors the
 *  JavaScript builder so a stored table and an inline one look identical. */
function horsetools_table_render_data( $data, $opts, $id = 0, $css = '' ) {
	$data = is_array( $data ) ? array_values( $data ) : array();
	if ( empty( $data ) ) {
		return '';
	}
	$opts    = is_array( $opts ) ? $opts : array();
	$header  = ! empty( $opts['header'] );
	// A pinned total row: rendered as a real <tfoot>, which sorting, search and
	// pagination never touch (they operate on tbody rows only).
	$footer_on    = ! empty( $opts['footer'] ) && count( $data ) >= ( $header ? 3 : 2 );
	$footer_start = $footer_on ? count( $data ) - 1 : -1;
	// Formulas first (they may live in merged owner cells), then merge keywords.
	$data = horsetools_table_apply_formulas( $data );
	list( $span_cs, $span_rs, $span_skip ) = horsetools_table_spans( $data, $header, $footer_start );
	$caption = isset( $opts['caption'] ) ? (string) $opts['caption'] : '';
	$head    = $header ? array_map( 'strval', (array) $data[0] ) : null;
	$body    = $header ? array_slice( $data, 1 ) : $data;
	$foot    = null;
	if ( $footer_on ) {
		$foot = array_pop( $body );
	}

	$ncol = 0;
	foreach ( $data as $r ) {
		$ncol = max( $ncol, count( (array) $r ) );
	}
	$right = array();
	for ( $ci = 0; $ci < $ncol; $ci++ ) {
		$any = false;
		$all = true;
		foreach ( $body as $r ) {
			$r = (array) $r;
			$v = trim( (string) ( isset( $r[ $ci ] ) ? $r[ $ci ] : '' ) );
			if ( '' === $v || '#colspan#' === $v || '#rowspan#' === $v ) {
				continue;
			}
			$any = true;
			if ( ! horsetools_table_is_num( $v ) ) {
				$all = false;
				break;
			}
		}
		$right[ $ci ] = $any && $all;
	}
	$rc = function ( $i ) use ( $right ) {
		return ! empty( $right[ $i ] ) ? ' class="ht-r"' : '';
	};
	$span_attr = function ( $r, $c ) use ( $span_cs, $span_rs ) {
		$key = $r . ':' . $c;
		$a   = '';
		if ( isset( $span_cs[ $key ] ) ) {
			$a .= ' colspan="' . (int) $span_cs[ $key ] . '"';
		}
		if ( isset( $span_rs[ $key ] ) ) {
			$a .= ' rowspan="' . (int) $span_rs[ $key ] . '"';
		}
		return $a;
	};

	$h = '<table>';
	if ( '' !== $caption ) {
		$h .= '<caption>' . esc_html( $caption ) . '</caption>';
	}
	if ( $head ) {
		$h .= '<thead><tr>';
		foreach ( $head as $i => $c ) {
			if ( isset( $span_skip[ '0:' . $i ] ) ) {
				continue;
			}
			$cv = trim( (string) $c );
			if ( '#colspan#' === $cv || '#rowspan#' === $cv ) {
				$cv = ''; // keyword with no valid owner: plain empty cell
			}
			$h .= '<th' . $rc( $i ) . $span_attr( 0, $i ) . '>' . esc_html( $cv ) . '</th>';
		}
		$h .= '</tr></thead>';
	}
	$h .= '<tbody>';
	foreach ( $body as $bi => $row ) {
		$row = (array) $row;
		$ri  = $header ? $bi + 1 : $bi; // absolute row in the matrix
		$h  .= '<tr>';
		for ( $i = 0; $i < $ncol; $i++ ) {
			if ( isset( $span_skip[ $ri . ':' . $i ] ) ) {
				continue;
			}
			$c = trim( (string) ( isset( $row[ $i ] ) ? $row[ $i ] : '' ) );
			if ( '#colspan#' === $c || '#rowspan#' === $c ) {
				$c = '';
			}
			$lbl = ( $head && isset( $head[ $i ] ) ) ? esc_attr( $head[ $i ] ) : '';
			$h  .= '<td data-label="' . $lbl . '"' . $rc( $i ) . $span_attr( $ri, $i ) . '>' . esc_html( $c ) . '</td>';
		}
		$h .= '</tr>';
	}
	$h .= '</tbody>';
	if ( null !== $foot ) {
		$fr = count( $data ) - 1; // absolute matrix row of the footer
		$h .= '<tfoot><tr>';
		for ( $i = 0; $i < $ncol; $i++ ) {
			if ( isset( $span_skip[ $fr . ':' . $i ] ) ) {
				continue;
			}
			$c = trim( (string) ( isset( $foot[ $i ] ) ? $foot[ $i ] : '' ) );
			if ( '#colspan#' === $c || '#rowspan#' === $c ) {
				$c = '';
			}
			$lbl = ( $head && isset( $head[ $i ] ) ) ? esc_attr( $head[ $i ] ) : '';
			$h  .= '<td data-label="' . $lbl . '"' . $rc( $i ) . $span_attr( $fr, $i ) . '>' . esc_html( $c ) . '</td>';
		}
		$h .= '</tr></tfoot>';
	}
	$h .= '</table>';

	$cls = 'ht-table';
	if ( ! empty( $opts['stack'] ) ) {
		$cls .= ' ht-table-stack';
	}
	if ( ! isset( $opts['striped'] ) || ! empty( $opts['striped'] ) ) {
		$cls .= ' ht-table-striped';
	}
	if ( ! empty( $opts['compact'] ) ) {
		$cls .= ' ht-table-compact';
	}
	$theme = isset( $opts['theme'] ) ? $opts['theme'] : '';
	if ( in_array( $theme, array( 'bordered', 'minimal', 'lines', 'card', 'dark', 'soft' ), true ) ) {
		$cls .= ' ht-tt-' . $theme;
	}
	$hcolor = isset( $opts['hcolor'] ) ? $opts['hcolor'] : '';
	if ( in_array( $hcolor, array( 'blue', 'green', 'orange', 'purple', 'dark', 'red', 'teal', 'pink', 'indigo', 'gradblue', 'gradsunset', 'gradocean' ), true ) ) {
		$cls .= ' ht-th-' . $hcolor;
	}
	$style = '';
	if ( $id > 0 ) {
		$cls .= ' ht-table-' . (int) $id;
		// Per-table CSS, admin-authored (same trust level as Customizer
		// "Additional CSS"). '<' is stripped at save AND here, so the block can
		// never be broken out of.
		$css = str_replace( '<', '', (string) $css );
		if ( '' !== trim( $css ) ) {
			$style = '<style>' . $css . '</style>';
		}
	}
	return $style . '<div class="' . esc_attr( $cls ) . '"' . horsetools_table_fx_attrs( $opts ) . '><div class="ht-table-scroll">' . $h . '</div></div>';
}

/** Sanitise a posted table into { name, data(2D strings), opts(whitelist) }. */
function horsetools_table_sanitize_payload() {
	$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$data = isset( $_POST['data'] ) ? json_decode( wp_unslash( $_POST['data'] ), true ) : array();
	$opts = isset( $_POST['opts'] ) ? json_decode( wp_unslash( $_POST['opts'] ), true ) : array();
	$clean = array();
	if ( is_array( $data ) ) {
		foreach ( $data as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$cr = array();
			foreach ( $row as $cell ) {
				$cr[] = sanitize_textarea_field( (string) $cell );
			}
			$clean[] = $cr;
		}
	}
	$opts = is_array( $opts ) ? $opts : array();
	$copt = array(
		'header'  => ! empty( $opts['header'] ),
		'striped' => ! isset( $opts['striped'] ) || ! empty( $opts['striped'] ),
		'compact' => ! empty( $opts['compact'] ),
		'stack'   => ! empty( $opts['stack'] ),
		'footer'  => ! empty( $opts['footer'] ),
		'theme'   => in_array( isset( $opts['theme'] ) ? $opts['theme'] : '', array( 'bordered', 'minimal', 'lines', 'card', 'dark', 'soft' ), true ) ? $opts['theme'] : '',
		'hcolor'  => in_array( isset( $opts['hcolor'] ) ? $opts['hcolor'] : '', array( 'blue', 'green', 'orange', 'purple', 'dark', 'red', 'teal', 'pink', 'indigo', 'gradblue', 'gradsunset', 'gradocean' ), true ) ? $opts['hcolor'] : '',
		'caption' => isset( $opts['caption'] ) ? sanitize_text_field( (string) $opts['caption'] ) : '',
		// Front-end interactivity (ht-table-fx.js).
		'sort'     => ! empty( $opts['sort'] ),
		'search'   => ! empty( $opts['search'] ),
		'paginate' => ! empty( $opts['paginate'] ),
		'pagesize' => min( 100, max( 1, isset( $opts['pagesize'] ) ? (int) $opts['pagesize'] : 10 ) ),
	);
	// Google Sheet source: only accept a real Sheets URL; sync only with a sheet.
	$sheet = isset( $_POST['sheet'] ) ? esc_url_raw( wp_unslash( $_POST['sheet'] ) ) : '';
	if ( '' !== $sheet && ! horsetools_table_sheet_csv_url( $sheet ) ) {
		$sheet = '';
	}
	$sync = isset( $_POST['sync'] ) ? sanitize_key( wp_unslash( $_POST['sync'] ) ) : 'off';
	if ( '' === $sheet || ! in_array( $sync, array( 'hourly', 'daily' ), true ) ) {
		$sync = 'off';
	}
	// Per-table CSS: admin-trusted, but '<' is stripped (invalid in CSS anyway)
	// so a </style> breakout is impossible, and the size is capped.
	$css = isset( $_POST['css'] ) ? (string) wp_unslash( $_POST['css'] ) : '';
	$css = substr( str_replace( '<', '', $css ), 0, 5000 );
	return array( 'name' => $name, 'data' => $clean, 'opts' => $copt, 'sheet' => $sheet, 'sync' => $sync, 'css' => $css );
}

/* ---- Google Sheet sync (Phase 4). A public sheet ("Anyone with the link can
 * view") exports as CSV without any API key: /export?format=csv&gid=N. The
 * server fetches it — on demand from the builder/manager, and on WP-Cron for
 * tables set to auto-refresh — and replaces the stored table's data. ---- */

/** Turn any pasted Google Sheets URL into its CSV export URL, or false. */
function horsetools_table_sheet_csv_url( $url ) {
	if ( ! preg_match( '#^https://docs\.google\.com/spreadsheets/d/([a-zA-Z0-9_-]+)#', (string) $url, $m ) ) {
		return false;
	}
	$gid = 0;
	if ( preg_match( '/[#?&]gid=(\d+)/', (string) $url, $g ) ) {
		$gid = (int) $g[1];
	}
	return 'https://docs.google.com/spreadsheets/d/' . $m[1] . '/export?format=csv&gid=' . $gid;
}

/** Fetch + parse a public sheet. Returns a rows×cols string matrix or WP_Error. */
function horsetools_table_fetch_sheet( $url ) {
	$csv_url = horsetools_table_sheet_csv_url( $url );
	if ( ! $csv_url ) {
		return new WP_Error( 'bad_url', __( 'That does not look like a Google Sheets link.', 'horse-tools' ) );
	}
	$res = wp_remote_get( $csv_url, array( 'timeout' => 15, 'redirection' => 3, 'user-agent' => 'horse-tools-sheet-sync' ) );
	if ( is_wp_error( $res ) ) {
		return $res;
	}
	if ( 200 !== wp_remote_retrieve_response_code( $res ) ) {
		return new WP_Error( 'not_public', __( 'Google refused the request — make sure the sheet is shared as “Anyone with the link can view”.', 'horse-tools' ) );
	}
	$body = wp_remote_retrieve_body( $res );
	if ( strlen( $body ) > 1048576 ) {
		return new WP_Error( 'too_big', __( 'The sheet is too large (over 1 MB of CSV).', 'horse-tools' ) );
	}
	// A login/consent HTML page means the sheet is not actually public.
	if ( false !== stripos( substr( $body, 0, 300 ), '<html' ) ) {
		return new WP_Error( 'not_public', __( 'Google refused the request — make sure the sheet is shared as “Anyone with the link can view”.', 'horse-tools' ) );
	}
	// fgetcsv on a temp stream handles quoted commas AND quoted newlines.
	$fh = fopen( 'php://temp', 'r+' );
	fwrite( $fh, $body );
	rewind( $fh );
	$data = array();
	while ( false !== ( $row = fgetcsv( $fh, null, ',', '"', '' ) ) ) {
		if ( count( $data ) >= 500 ) {
			break; // cap: nobody wants a 10k-row HTML table on a page
		}
		$row = array_slice( (array) $row, 0, 40 );
		$clean = array();
		$hasContent = false;
		foreach ( $row as $cell ) {
			$cell = sanitize_textarea_field( (string) $cell );
			if ( '' !== trim( $cell ) ) {
				$hasContent = true;
			}
			$clean[] = $cell;
		}
		if ( $hasContent ) {
			$data[] = $clean;
		}
	}
	fclose( $fh );
	if ( empty( $data ) ) {
		return new WP_Error( 'empty', __( 'The sheet appears to be empty.', 'horse-tools' ) );
	}
	// Pad every row to the widest row so the grid/table stays rectangular.
	$max = 0;
	foreach ( $data as $r ) {
		$max = max( $max, count( $r ) );
	}
	foreach ( $data as $k => $r ) {
		while ( count( $data[ $k ] ) < $max ) {
			$data[ $k ][] = '';
		}
	}
	return $data;
}

/** Builder "Pull data" button: fetch a sheet and hand the matrix to the grid. */
function horsetools_tbl_sheet_fetch_ajax() {
	check_ajax_referer( 'horsetools_tbl', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	$url  = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';
	$data = horsetools_table_fetch_sheet( $url );
	if ( is_wp_error( $data ) ) {
		wp_send_json_error( array( 'msg' => $data->get_error_message() ) );
	}
	wp_send_json_success( array( 'data' => $data ) );
}
add_action( 'wp_ajax_horsetools_tbl_sheet_fetch', 'horsetools_tbl_sheet_fetch_ajax' );

/** Manager "Sync now" button: refresh one table from its sheet immediately. */
function horsetools_tbl_sync_now_ajax() {
	check_ajax_referer( 'horsetools_tbl', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	$id     = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	$tables = horsetools_tables_get();
	if ( ! isset( $tables[ $id ] ) || empty( $tables[ $id ]['sheet'] ) ) {
		wp_send_json_error();
	}
	$data = horsetools_table_fetch_sheet( $tables[ $id ]['sheet'] );
	if ( is_wp_error( $data ) ) {
		wp_send_json_error( array( 'msg' => $data->get_error_message() ) );
	}
	$tables[ $id ]['data']      = $data;
	$tables[ $id ]['last_sync'] = time();
	update_option( 'horsetools_tables', $tables, false );
	wp_send_json_success( array( 'rows' => count( $data ) ) );
}
add_action( 'wp_ajax_horsetools_tbl_sync_now', 'horsetools_tbl_sync_now_ajax' );

/** Keep the hourly cron scheduled exactly while any table auto-syncs. */
function horsetools_tables_sync_schedule() {
	$need = false;
	foreach ( horsetools_tables_get() as $t ) {
		if ( ! empty( $t['sheet'] ) && ! empty( $t['sync'] ) && 'off' !== $t['sync'] ) {
			$need = true;
			break;
		}
	}
	$next = wp_next_scheduled( 'horsetools_tables_sync' );
	if ( $need && ! $next ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'hourly', 'horsetools_tables_sync' );
	} elseif ( ! $need && $next ) {
		wp_unschedule_event( $next, 'horsetools_tables_sync' );
	}
}
add_action( 'admin_init', 'horsetools_tables_sync_schedule' );

function horsetools_tables_sync_run() {
	$tables  = horsetools_tables_get();
	$changed = false;
	foreach ( $tables as $id => $t ) {
		if ( empty( $t['sheet'] ) || empty( $t['sync'] ) || 'off' === $t['sync'] ) {
			continue;
		}
		$age = time() - (int) ( isset( $t['last_sync'] ) ? $t['last_sync'] : 0 );
		$min = ( 'daily' === $t['sync'] ) ? DAY_IN_SECONDS - 300 : HOUR_IN_SECONDS - 300;
		if ( $age < $min ) {
			continue;
		}
		// Stamp before the result so a broken sheet is retried on the NEXT
		// interval instead of on every cron tick.
		$tables[ $id ]['last_sync'] = time();
		$changed                    = true;
		$data = horsetools_table_fetch_sheet( $t['sheet'] );
		if ( ! is_wp_error( $data ) ) {
			$tables[ $id ]['data'] = $data;
		}
	}
	if ( $changed ) {
		update_option( 'horsetools_tables', $tables, false );
	}
}
add_action( 'horsetools_tables_sync', 'horsetools_tables_sync_run' );

function horsetools_tbl_save_ajax() {
	check_ajax_referer( 'horsetools_tbl', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	$id      = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	$payload = horsetools_table_sanitize_payload();
	$tables  = horsetools_tables_get();
	if ( $id <= 0 || ! isset( $tables[ $id ] ) ) {
		$id = 1;
		foreach ( array_keys( $tables ) as $k ) {
			if ( (int) $k >= $id ) {
				$id = (int) $k + 1;
			}
		}
	}
	$tables[ $id ] = array(
		'id'        => $id,
		'name'      => '' !== $payload['name'] ? $payload['name'] : sprintf( __( 'Table %d', 'horse-tools' ), $id ),
		'data'      => $payload['data'],
		'opts'      => $payload['opts'],
		'sheet'     => $payload['sheet'],
		'sync'      => $payload['sync'],
		'css'       => $payload['css'],
		'last_sync' => isset( $tables[ $id ]['last_sync'] ) ? $tables[ $id ]['last_sync'] : 0,
	);
	update_option( 'horsetools_tables', $tables, false );
	horsetools_tables_sync_schedule();
	wp_send_json_success( array( 'id' => $id, 'name' => $tables[ $id ]['name'] ) );
}
add_action( 'wp_ajax_horsetools_tbl_save', 'horsetools_tbl_save_ajax' );

function horsetools_tbl_delete_ajax() {
	check_ajax_referer( 'horsetools_tbl', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	$id     = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	$tables = horsetools_tables_get();
	unset( $tables[ $id ] );
	update_option( 'horsetools_tables', $tables, false );
	horsetools_tables_sync_schedule();
	wp_send_json_success();
}
add_action( 'wp_ajax_horsetools_tbl_delete', 'horsetools_tbl_delete_ajax' );

function horsetools_tbl_duplicate_ajax() {
	check_ajax_referer( 'horsetools_tbl', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	$id     = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	$tables = horsetools_tables_get();
	if ( ! isset( $tables[ $id ] ) ) {
		wp_send_json_error();
	}
	$newid = 1;
	foreach ( array_keys( $tables ) as $k ) {
		if ( (int) $k >= $newid ) {
			$newid = (int) $k + 1;
		}
	}
	$copy         = $tables[ $id ];
	$copy['id']   = $newid;
	$copy['name'] = $copy['name'] . ' (' . __( 'copy', 'horse-tools' ) . ')';
	$tables[ $newid ] = $copy;
	update_option( 'horsetools_tables', $tables, false );
	wp_send_json_success( array( 'id' => $newid ) );
}
add_action( 'wp_ajax_horsetools_tbl_duplicate', 'horsetools_tbl_duplicate_ajax' );

/**
 * The "Tables" manager screen. Lists saved tables, lets you add / edit (via the
 * shared builder in save mode), duplicate and delete them, and shows the
 * [ht-table id="N"] shortcode to copy.
 */
function horsetools_tables_menu() {
	add_submenu_page(
		'horsetools-options',
		__( 'Tables', 'horse-tools' ),
		'<i class="ti ti-table" style="width:20px;"></i> ' . __( 'Tables', 'horse-tools' ),
		'manage_options',
		'horsetools-tables',
		'horsetools_tables_page'
	);
}
// Priority 20: this file is included before main/admin.php, so at the default
// priority this submenu would register BEFORE the parent "Horse Tools" menu
// exists. WordPress then computes the page hook without the parent slug and the
// screen becomes unreachable — the menu shows a bare "horsetools-tables" href
// and opening admin.php?page=horsetools-tables says "you are not allowed".
// Registering after the parent (priority 10) fixes both.
add_action( 'admin_menu', 'horsetools_tables_menu', 20 );

function horsetools_tables_page() {
	$tables = horsetools_tables_get();
	// Pass the full data to the page so Edit opens the builder pre-filled without
	// another round-trip. Admin-only screen, capability-checked by the submenu.
	$payload = array();
	foreach ( $tables as $id => $tb ) {
		$payload[ (string) $id ] = array(
			'name'  => isset( $tb['name'] ) ? (string) $tb['name'] : ( 'Table ' . $id ),
			'data'  => isset( $tb['data'] ) ? $tb['data'] : array(),
			'opts'  => isset( $tb['opts'] ) ? $tb['opts'] : array(),
			'sheet' => isset( $tb['sheet'] ) ? (string) $tb['sheet'] : '',
			'sync'  => isset( $tb['sync'] ) ? (string) $tb['sync'] : 'off',
			'css'   => isset( $tb['css'] ) ? (string) $tb['css'] : '',
		);
	}
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Tables', 'horse-tools' ); ?></h1>
		<button type="button" class="page-title-action" id="ht-tbl-new"><?php esc_html_e( 'Add new table', 'horse-tools' ); ?></button>
		<p class="description" style="margin-top:8px;max-width:760px;">
			<?php esc_html_e( 'Build a table once here, then insert it into any post or page with its shortcode. Edit it here and every place that uses it updates automatically.', 'horse-tools' ); ?>
		</p>
		<table class="widefat striped" style="margin-top:14px;max-width:920px;">
			<thead><tr>
				<th style="width:60px;">ID</th>
				<th><?php esc_html_e( 'Name', 'horse-tools' ); ?></th>
				<th style="width:110px;"><?php esc_html_e( 'Size', 'horse-tools' ); ?></th>
				<th style="width:210px;"><?php esc_html_e( 'Shortcode', 'horse-tools' ); ?></th>
				<th style="width:230px;"><?php esc_html_e( 'Actions', 'horse-tools' ); ?></th>
			</tr></thead>
			<tbody id="ht-tbl-rows">
			<?php if ( empty( $tables ) ) : ?>
				<tr class="ht-tbl-empty"><td colspan="5"><?php esc_html_e( 'No tables yet. Click “Add new table” to create your first one.', 'horse-tools' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $tables as $id => $tb ) :
					$rows = is_array( $tb['data'] ) ? count( $tb['data'] ) : 0;
					$cols = 0;
					if ( is_array( $tb['data'] ) ) {
						foreach ( $tb['data'] as $r ) { $cols = max( $cols, is_array( $r ) ? count( $r ) : 0 ); }
					}
					?>
					<tr data-id="<?php echo (int) $id; ?>">
						<td><?php echo (int) $id; ?></td>
						<td class="ht-tbl-name"><strong><?php echo esc_html( $tb['name'] ); ?></strong></td>
						<td><?php echo (int) $rows . ' × ' . (int) $cols; ?></td>
						<td><code class="ht-tbl-sc">[ht-table id="<?php echo (int) $id; ?>"]</code></td>
						<td>
							<button type="button" class="button ht-tbl-edit"><?php esc_html_e( 'Edit', 'horse-tools' ); ?></button>
							<button type="button" class="button ht-tbl-dup"><?php esc_html_e( 'Duplicate', 'horse-tools' ); ?></button>
							<button type="button" class="button ht-tbl-del"><?php esc_html_e( 'Delete', 'horse-tools' ); ?></button>
							<button type="button" class="button ht-tbl-csv"><?php esc_html_e( 'Export CSV', 'horse-tools' ); ?></button>
							<?php if ( ! empty( $tb['sheet'] ) ) : ?>
								<button type="button" class="button ht-tbl-sync" title="<?php echo esc_attr( $tb['sheet'] ); ?>"><?php esc_html_e( 'Sync from Sheet', 'horse-tools' ); ?></button>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
	<script>
	( function () {
		var STORE = <?php echo wp_json_encode( $payload ); ?>;
		var NONCE = <?php echo wp_json_encode( wp_create_nonce( 'horsetools_tbl' ) ); ?>;
		var AJAX  = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		var STR   = {
			sure: <?php echo wp_json_encode( __( 'Click again to delete', 'horse-tools' ) ); ?>,
			fail: <?php echo wp_json_encode( __( 'Something went wrong. Please try again.', 'horse-tools' ) ); ?>,
			syncing: <?php echo wp_json_encode( __( 'Syncing…', 'horse-tools' ) ); ?>,
			synced: <?php echo wp_json_encode( __( 'Synced', 'horse-tools' ) ); ?>
		};
		function post( action, data, cb ) {
			data = data || {};
			data.action = action; data.nonce = NONCE;
			var body = Object.keys( data ).map( function ( k ) { return encodeURIComponent( k ) + '=' + encodeURIComponent( data[ k ] ); } ).join( '&' );
			fetch( AJAX, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body } )
				.then( function ( r ) { return r.json(); } ).then( cb ).catch( function () { alert( STR.fail ); } );
		}
		function save( id, initial ) {
			if ( ! window.htTableBuilder ) { return; }
			window.htTableBuilder.open( function ( payload ) {
				post( 'horsetools_tbl_save', {
					id: payload.id || 0,
					name: payload.name || '',
					data: JSON.stringify( payload.data || [] ),
					opts: JSON.stringify( payload.opts || {} ),
					sheet: payload.sheet || '',
					sync: payload.sync || 'off',
					css: payload.css || ''
				}, function ( res ) {
					if ( res && res.success ) { location.reload(); } else { alert( STR.fail ); }
				} );
			}, { mode: 'save', id: id, initial: initial } );
		}
		var newBtn = document.getElementById( 'ht-tbl-new' );
		if ( newBtn ) { newBtn.addEventListener( 'click', function () { save( 0, null ); } ); }
		var tbody = document.getElementById( 'ht-tbl-rows' );
		if ( tbody ) {
			tbody.addEventListener( 'click', function ( e ) {
				var tr = e.target.closest( 'tr[data-id]' );
				if ( ! tr ) { return; }
				var id = tr.getAttribute( 'data-id' );
				if ( e.target.classList.contains( 'ht-tbl-edit' ) ) {
					var init = STORE[ id ] || null;
					save( parseInt( id, 10 ), init );
				} else if ( e.target.classList.contains( 'ht-tbl-dup' ) ) {
					post( 'horsetools_tbl_duplicate', { id: id }, function ( res ) { if ( res && res.success ) { location.reload(); } else { alert( STR.fail ); } } );
				} else if ( e.target.classList.contains( 'ht-tbl-csv' ) ) {
					// Client-side CSV export: BOM so Excel opens UTF-8 (Vietnamese)
					// correctly; every cell quoted.
					var tbl = STORE[ id ];
					if ( ! tbl ) { return; }
					var csv = ( tbl.data || [] ).map( function ( row ) {
						return row.map( function ( cell ) {
							return '"' + String( cell == null ? '' : cell ).replace( /"/g, '""' ) + '"';
						} ).join( ',' );
					} ).join( '\r\n' );
					var blob = new Blob( [ '﻿' + csv ], { type: 'text/csv;charset=utf-8' } );
					var a = document.createElement( 'a' );
					a.href = URL.createObjectURL( blob );
					a.download = 'ht-table-' + id + '.csv';
					document.body.appendChild( a );
					a.click();
					a.remove();
					setTimeout( function () { URL.revokeObjectURL( a.href ); }, 5000 );
				} else if ( e.target.classList.contains( 'ht-tbl-sync' ) ) {
					var sb = e.target, sbLabel = sb.textContent;
					sb.disabled = true;
					sb.textContent = STR.syncing;
					post( 'horsetools_tbl_sync_now', { id: id }, function ( res ) {
						if ( res && res.success ) {
							sb.textContent = STR.synced + ' (' + res.data.rows + ')';
							setTimeout( function () { location.reload(); }, 700 );
						} else {
							sb.disabled = false;
							sb.textContent = sbLabel;
							alert( ( res && res.data && res.data.msg ) ? res.data.msg : STR.fail );
						}
					} );
				} else if ( e.target.classList.contains( 'ht-tbl-del' ) ) {
					// Two-step confirm instead of a native confirm() dialog: first
					// click arms the button for 4 s, second click deletes.
					var b = e.target;
					if ( b.dataset.armed === '1' ) {
						post( 'horsetools_tbl_delete', { id: id }, function ( res ) { if ( res && res.success ) { location.reload(); } else { alert( STR.fail ); } } );
						return;
					}
					b.dataset.armed = '1';
					b.dataset.label = b.textContent;
					b.textContent = STR.sure;
					b.style.borderColor = '#b32d2e';
					b.style.color = '#b32d2e';
					setTimeout( function () {
						if ( b.dataset.armed === '1' ) {
							b.dataset.armed = '';
							b.textContent = b.dataset.label;
							b.style.borderColor = '';
							b.style.color = '';
						}
					}, 4000 );
				}
			} );
		}
	}() );
	</script>
	<?php
}

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

/**
 * Turn off individual Horse Tools shortcodes the admin has disabled.
 *
 * Runs after every tag is registered (priority 100, later than the [sc]
 * compat at 99). It only ever removes this plugin's OWN tags — the manager
 * that writes this option is limited to the Horse Tools + snippet list, so it
 * never touches shortcodes owned by other plugins.
 */
add_action( 'init', function () {
	$disabled = get_option( 'horsetools_sc_disabled', array() );
	if ( ! is_array( $disabled ) ) {
		return;
	}
	foreach ( $disabled as $tag ) {
		$tag = sanitize_key( $tag );
		if ( '' !== $tag && shortcode_exists( $tag ) ) {
			remove_shortcode( $tag );
		}
	}
}, 100 );

require_once HORSETOOLS_DIR . 'inc/shortcodes-lib.php';
