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
		'stack'   => '0', // 1 = stack each row into a card on small screens
		'striped' => '1',
		'compact' => '0',
		'theme'   => '', // bordered | minimal | lines
		'hcolor'  => '', // blue | green | orange | purple | dark
	), $atts, 'ht-table' );
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
	if ( in_array( $a['theme'], array( 'bordered', 'minimal', 'lines' ), true ) ) {
		$cls .= ' ht-tt-' . $a['theme'];
	}
	if ( in_array( $a['hcolor'], array( 'blue', 'green', 'orange', 'purple', 'dark' ), true ) ) {
		$cls .= ' ht-th-' . $a['hcolor'];
	}
	return '<div class="' . esc_attr( $cls ) . '"><div class="ht-table-scroll">' . do_shortcode( $inner ) . '</div></div>';
}
add_shortcode( 'ht-table', 'horsetools_table_shortcode' );

function horsetools_table_css_maybe() {
	if ( ! is_singular() ) {
		return;
	}
	$p = get_post();
	if ( $p && false !== strpos( (string) $p->post_content, '[ht-table' ) ) {
		wp_enqueue_style( 'horsetools-table', HORSETOOLS_URL . 'link/ht-table.css', array(), HORSETOOLS_VERSION );
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
		'hcolorL'    => __( 'Header colour', 'horse-tools' ),
		'cGrey'      => __( 'Grey', 'horse-tools' ),
		'cBlue'      => __( 'Blue', 'horse-tools' ),
		'cGreen'     => __( 'Green', 'horse-tools' ),
		'cOrange'    => __( 'Orange', 'horse-tools' ),
		'cPurple'    => __( 'Purple', 'horse-tools' ),
		'cDark'      => __( 'Dark', 'horse-tools' ),
		'captionL'   => __( 'Caption', 'horse-tools' ),
		'captionPh'  => __( 'optional title above the table', 'horse-tools' ),
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
	if ( in_array( $hook, array( 'post.php', 'post-new.php', 'widgets.php' ), true ) ) {
		wp_enqueue_script( 'horsetools-table-builder' );
		// So the modal's live preview shows the real styles (themes, header
		// colour, alignment) exactly as they will appear on the site.
		wp_enqueue_style( 'horsetools-table', HORSETOOLS_URL . 'link/ht-table.css', array(), HORSETOOLS_VERSION );
	}
}
add_action( 'admin_enqueue_scripts', 'horsetools_table_builder_classic' );

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
