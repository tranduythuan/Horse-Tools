<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_options;
# Remove jquery-migrate
if(isset($horsetools_options['speed-off1'])){
function horsetools_remove_jquery_migrate( $scripts ) {
   if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $script = $scripts->registered['jquery'];
   if ( $script->deps ) { 
        $script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
 }
 }
 }
add_action( 'wp_default_scripts', 'horsetools_remove_jquery_migrate' );
}
# tắt Gutenberg CSS o home
if(isset($horsetools_options['speed-off2'])){
function horsetools_remove_wp_block_library_css() {
    if ( is_front_page() ) {
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );
        wp_dequeue_style( 'wc-blocks-style' );
    }
}
add_action( 'wp_enqueue_scripts', 'horsetools_remove_wp_block_library_css', 100 );
}
# tắt Classic CSS o home
if(isset($horsetools_options['speed-off3'])){
function horsetools_classic_styles_off() {
	if ( is_front_page()) {
    wp_dequeue_style( 'classic-theme-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'horsetools_classic_styles_off', 20 );
}
# tắt emoji 
if(isset($horsetools_options['speed-off4'])){
function horsetools_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	// WordPress 6.4 deprecated print_emoji_styles() and moved the CSS to
	// wp_enqueue_emoji_styles(), so the two removals above have matched nothing
	// since then and the emoji stylesheet kept printing on every page.
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'admin_print_styles', 'wp_enqueue_emoji_styles' );
	remove_action( 'embed_head', 'print_emoji_detection_script' );
	add_filter( 'tiny_mce_plugins', function ( $plugins ) {
		return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : $plugins;
	} );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );		
}
add_action( 'init', 'horsetools_disable_emojis' );
}
# gioi han so ban ghi trong csdl 
if(isset($horsetools_options['speed-data1'])){
function horsetools_limit_post_revisions($num, $post) {
	global $horsetools_options;
	if(!empty($horsetools_options['speed-data11'])){
    $limit = $horsetools_options['speed-data11'];
	} else {
	$limit = 3;	
	}
    return $limit;
}
add_filter('wp_revisions_to_keep', 'horsetools_limit_post_revisions', 10, 2);	
}
# gioi han thoi gian luu bai viet tu dong pút
if(isset($horsetools_options['speed-data2'])){
	if (!defined('AUTOSAVE_INTERVAL')) {
		$secon = !empty($horsetools_options['speed-data21']) ? $horsetools_options['speed-data21'] : 1;
		define('AUTOSAVE_INTERVAL', $secon * MINUTE_IN_SECONDS);
	}
}
# thu vien instant-page.js tai truoc link khi di chuot
if(isset($horsetools_options['speed-link1'])){
function horsetools_instantpage_scripts() {
  wp_enqueue_script( 'instantpage', HORSETOOLS_URL . 'link/instantpage.js', array(), '5.7.0', true );
}
add_action( 'wp_enqueue_scripts', 'horsetools_instantpage_scripts' );
function horsetools_instantpage_loader_tag( $tag, $handle ) {
  if ( 'instantpage' === $handle ) {
    if ( strpos( $tag, 'text/javascript' ) !== false ) {
      $tag = str_replace( 'text/javascript', 'module', $tag );
    }
    else {
      $tag = str_replace( '<script ', "<script type='module' ", $tag );
    }
  }
  return $tag;
}
add_filter( 'script_loader_tag', 'horsetools_instantpage_loader_tag', 10, 2 );
}
# cuon trang muot ma
if(isset($horsetools_options['speed-link2'])){
function horsetools_smooth_scripts() {
	// `true` was being passed as $ver, not $in_footer — so this loaded
	// render-blocking in <head> with ?ver=1, inside the Optimize feature set.
	wp_enqueue_script( 'smooth-scroll', HORSETOOLS_URL . 'link/smooth-scroll.min.js', array(), HORSETOOLS_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'horsetools_smooth_scripts' );
}
# lazyload hinh anh — native
if(isset($horsetools_options['speed-lazy1'])){
// The old version loaded lazysizes and STRIPPED src from every <img>, replacing
// it with data-src. That hid images from Google and from no-JS users and fought
// the browser's own lazy-load. Modern WordPress (6.0+) already adds
// loading="lazy" natively AND correctly skips the first/LCP image — so we must
// not re-add that. Here we only add decoding="async", a safe hint that lets the
// browser decode images off the main thread; src and the LCP image are untouched.
function horsetools_native_lazyload_content( $content ) {
	if ( is_admin() || is_feed() ) {
		return $content;
	}
	// Quote-aware match so a literal ">" inside an attribute value (e.g.
	// alt="a > b" or a data-json attribute) doesn't cut the tag short.
	return preg_replace_callback( '/<img\b(?:"[^"]*"|\'[^\']*\'|[^>"\'])*>/i', function ( $m ) {
		$tag = $m[0];
		if ( false === stripos( $tag, ' src=' ) || false !== stripos( $tag, 'decoding=' ) ) {
			return $tag;
		}
		return preg_replace( '/<img\b/i', '<img decoding="async"', $tag, 1 );
	}, $content );
}
add_filter( 'the_content', 'horsetools_native_lazyload_content', 20 );
add_filter( 'post_thumbnail_html', 'horsetools_native_lazyload_content', 20 );
add_filter( 'widget_text', 'horsetools_native_lazyload_content', 20 );
}
# tuy chon nen html
function horsetools_minify_html_output($buffer) {
	global $horsetools_options;
	if ( substr( ltrim( $buffer ), 0, 5) == '<?xml' )
		return ( $buffer );
	if ( isset($horsetools_options['speed-zip16']) && mb_detect_encoding($buffer, 'UTF-8', true) )
		$mod = '/u';
	else
		$mod = '/s';
	$buffer = str_replace(array (chr(13) . chr(10), chr(9)), array (chr(10), ''), $buffer);
	$buffer = str_ireplace(array ('<script', '/script>', '<pre', '/pre>', '<textarea', '/textarea>', '<style', '/style>'), array ('M1N1FY-ST4RT<script', '/script>M1N1FY-3ND', 'M1N1FY-ST4RT<pre', '/pre>M1N1FY-3ND', 'M1N1FY-ST4RT<textarea', '/textarea>M1N1FY-3ND', 'M1N1FY-ST4RT<style', '/style>M1N1FY-3ND'), $buffer);
	$split = explode('M1N1FY-3ND', $buffer);
	$buffer = ''; 
	for ( $i=0; $i<count($split); $i++ ) {
		$ii = strpos($split[$i], 'M1N1FY-ST4RT');
		if ( $ii !== false ) {
			$process = substr($split[$i], 0, $ii);
			$asis = substr($split[$i], $ii + 12);
			if ( substr($asis, 0, 7) == '<script' ) {
				$split2 = explode(chr(10), $asis);
				$asis = '';
				for ( $iii = 0; $iii < count($split2); $iii ++ ) {
					if ( $split2[$iii] )
						$asis .= trim($split2[$iii]) . chr(10);
					if ( isset($horsetools_options['speed-zip11']) ) {
						$last = substr(trim($split2[$iii]), -1);
						if ( strpos($split2[$iii], '//') !== false && ($last == ';' || $last == '>' || $last == '{' || $last == '}' || $last == ',') )
							$asis .= chr(10);
					}
				}
				if ( $asis )
					$asis = substr($asis, 0, -1);
				if ( isset($horsetools_options['speed-zip12']) )
					$asis = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis);
				if ( isset($horsetools_options['speed-zip11']) )
					$asis = str_replace(array (';' . chr(10), '>' . chr(10), '{' . chr(10), '}' . chr(10), ',' . chr(10)), array(';', '>', '{', '}', ','), $asis);
			} else if ( substr($asis, 0, 6) == '<style' ) {
				$asis = preg_replace(array ('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod), array('>', '<', '\\1'), $asis);
				if ( isset($horsetools_options['speed-zip12']) )
					$asis = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis);
				$asis = str_replace(array (chr(10), ' {', '{ ', ' }', '} ', '( ', ' )', ' :', ': ', ' ;', '; ', ' ,', ', ', ';}'), array('', '{', '{', '}', '}', '(', ')', ':', ':', ';', ';', ',', ',', '}'), $asis);
			}
		} else {
			$process = $split[$i];
			$asis = '';
		}
		$process = preg_replace(array ('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod), array('>', '<', '\\1'), $process);
		if ( isset($horsetools_options['speed-zip12']) )
			$process = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->' . $mod, '', $process);
		$buffer .= $process.$asis;
	}
	$buffer = str_replace(array (chr(10) . '<script', chr(10) . '<style', '*/' . chr(10), 'M1N1FY-ST4RT'), array('<script', '<style', '*/', ''), $buffer);
	if ( isset($horsetools_options['speed-zip13']) && strtolower( substr( ltrim( $buffer ), 0, 15 ) ) == '<!doctype html>' )
		$buffer = str_replace( ' />', '>', $buffer );
	if ( isset($horsetools_options['speed-zip14']) ) {
		// Use the configured site host, never HTTP_HOST — that header is supplied
		// by the client, so a request with Host: fonts.googleapis.com would rewrite
		// every reference to that host into a same-origin path. Behind a cache that
		// keys on path alone, that is cache poisoning for every later visitor.
		$site_host = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( $site_host ) {
			$buffer = str_replace(
				array( 'https://' . $site_host . '/', 'http://' . $site_host . '/', '//' . $site_host . '/' ),
				array( '/', '/', '/' ),
				$buffer
			);
		}
	}
	if (isset($horsetools_options['speed-zip15']))
		$buffer = str_replace( array( 'http://', 'https://' ), '//', $buffer );
	return ( $buffer );
}
function horsetools_init_minify_html(){
	global $horsetools_options;
	// Only buffer real page views. The minifier collapses whitespace runs,
	// which silently corrupts whitespace inside JSON string values — so it must
	// never see an AJAX, REST, cron or feed response.
	if ( wp_doing_ajax() || wp_doing_cron() || is_feed()
		|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
		|| ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}
	if(isset($horsetools_options['speed-zip1']) && !is_user_logged_in()){
		ob_start('horsetools_minify_html_output');
	}
}
add_action( 'init', 'horsetools_init_minify_html', 1 );

/* -------------------------------------------------------------------------
 * Defer front-end JavaScript.
 *
 * Render-blocking <script src> in the <head> is the single most common
 * PageSpeed complaint. `defer` tells the browser to download the script
 * without blocking parsing and to run it — in document order — after the HTML
 * is parsed. We use defer, not async, precisely because defer PRESERVES ORDER,
 * so a script and its dependencies still run in the right sequence.
 *
 * What we never touch:
 *   - jQuery core: countless inline snippets run right after it expecting $ to
 *     exist, and those inline tags are NOT deferred, so deferring jQuery itself
 *     would break them. Excluded by default.
 *   - Anything the user lists (handle or a substring of the URL).
 *   - Scripts already marked async/defer or type="module" (modules defer
 *     natively), and inline scripts (no src).
 * ---------------------------------------------------------------------- */
if ( isset( $horsetools_options['speed-defer1'] ) ) {
	function horsetools_defer_scripts( $tag, $handle ) {
		if ( is_admin() ) {
			return $tag;
		}
		global $horsetools_options;
		// jQuery is never safe to defer (see the note above).
		$skip = array( 'jquery', 'jquery-core' );
		if ( ! empty( $horsetools_options['speed-defer-exclude'] ) ) {
			$extra = preg_split( '/[\s,]+/', (string) $horsetools_options['speed-defer-exclude'], -1, PREG_SPLIT_NO_EMPTY );
			if ( is_array( $extra ) ) {
				$skip = array_merge( $skip, $extra );
			}
		}
		foreach ( $skip as $needle ) {
			$needle = trim( (string) $needle );
			if ( '' !== $needle && ( $handle === $needle || false !== strpos( $tag, $needle ) ) ) {
				return $tag;
			}
		}
		// Only external scripts, and never double up on async/defer/module.
		if ( false === strpos( $tag, ' src=' ) ) {
			return $tag;
		}
		if ( false !== strpos( $tag, ' defer' ) || false !== strpos( $tag, ' async' )
			|| false !== strpos( $tag, "type='module'" ) || false !== strpos( $tag, 'type="module"' ) ) {
			return $tag;
		}
		// Leave alone any script that carries an inline before/after companion.
		// wp_add_inline_script() prints that inline part WITHOUT defer, so it runs
		// immediately — before this deferred file — and would call functions the
		// file has not defined yet. These are exactly the scripts defer must skip.
		// Ask WP_Scripts directly (version-independent) and, as a textual backstop,
		// bail if the tag itself already contains more than one <script element.
		$wp_scripts = wp_scripts();
		if ( $wp_scripts && ( $wp_scripts->get_data( $handle, 'after' ) || $wp_scripts->get_data( $handle, 'before' ) ) ) {
			return $tag;
		}
		if ( substr_count( $tag, '<script' ) > 1 ) {
			return $tag;
		}
		return preg_replace( '/<script(\s)/', '<script defer$1', $tag, 1 );
	}
	add_filter( 'script_loader_tag', 'horsetools_defer_scripts', 20, 2 );
}

/* -------------------------------------------------------------------------
 * Delay JavaScript until the first user interaction.
 *
 * The heaviest scripts on most sites are third-party — analytics, tag managers,
 * pixels, chat widgets, ad and A/B tags. They block the main thread during load
 * even though nothing on screen needs them until the visitor does something.
 * This rewrites those scripts to an inert type the browser will not execute,
 * then swaps them back to life on the FIRST interaction (key, mouse, wheel,
 * touch, scroll, click) or an optional fall-back timer. It is the single biggest
 * lever for Total Blocking Time and Lighthouse "Reduce unused JavaScript".
 *
 * Two modes:
 *   listed : delay only scripts whose src/inline body matches a keyword. Safe,
 *            and ships with a sensible default list when the box is left empty.
 *   all    : delay every script except an exclusion list. Most powerful, and
 *            because inline scripts are delayed alongside their files, order and
 *            jQuery dependencies are preserved.
 *
 * What sets this apart from the usual implementation:
 *   - it re-fires DOMContentLoaded and load once the delayed scripts have run,
 *     so libraries that hook those events still initialise;
 *   - restored scripts run strictly in document order (external ones wait for
 *     onload) so dependencies never race;
 *   - it never touches JSON-LD / JSON structured data (SEO), ES modules,
 *     document.write scripts, this plugin's own loader, or anything the theme
 *     marks with data-ht-no-delay.
 *
 * It runs in its own output buffer at template_redirect, so it also catches the
 * hard-coded inline third-party tags that script_loader_tag never sees. When the
 * HTML-minify buffer is also on, this (inner) buffer transforms first and minify
 * runs over the result.
 * ---------------------------------------------------------------------- */
if ( isset( $horsetools_options['speed-delay1'] ) ) {

	// Default keywords for "listed" mode when the user leaves the box empty: the
	// usual heavy third parties, none needed before the visitor interacts.
	function horsetools_delay_default_list() {
		return array(
			'googletagmanager', 'gtag/js', 'gtag(', 'google-analytics', 'analytics.js', 'ga.js',
			'connect.facebook', 'fbevents', 'fbq(', 'facebook.net',
			'googlesyndication', 'adsbygoogle', 'doubleclick',
			'hotjar', 'clarity.ms',
			'tawk.to', 'crisp.chat', 'tidio', 'intercom', 'zdassets', 'zendesk', 'livechatinc', 'drift.com',
			'recaptcha', 'disqus',
			'analytics.tiktok', 'matomo', 'piwik', 'criteo', 'taboola', 'outbrain', 'onesignal',
		);
	}

	// Does this script (identified by its src URL or, for inline, its body) get
	// delayed under the current mode and lists?
	function horsetools_delay_should( $needle, $mode, $list, $exclude ) {
		if ( 'all' === $mode ) {
			foreach ( $exclude as $kw ) {
				if ( '' !== $kw && false !== stripos( $needle, $kw ) ) {
					return false;
				}
			}
			return true;
		}
		foreach ( $list as $kw ) {
			if ( '' !== $kw && false !== stripos( $needle, $kw ) ) {
				return true;
			}
		}
		return false;
	}

	// The buffer callback: rewrite matching <script> tags and, if any matched,
	// append the interaction loader.
	function horsetools_delay_transform( $buffer ) {
		global $horsetools_options;
		if ( '' === $buffer || false === stripos( $buffer, '<script' ) ) {
			return $buffer;
		}
		$mode    = ( ! empty( $horsetools_options['speed-delay-mode'] ) && 'all' === $horsetools_options['speed-delay-mode'] ) ? 'all' : 'listed';
		$user    = isset( $horsetools_options['speed-delay-list'] ) ? preg_split( '/[\r\n,]+/', (string) $horsetools_options['speed-delay-list'], -1, PREG_SPLIT_NO_EMPTY ) : array();
		$user    = is_array( $user ) ? array_map( 'trim', $user ) : array();
		$list    = ( 'listed' === $mode && empty( $user ) ) ? horsetools_delay_default_list() : $user;
		$exclude = isset( $horsetools_options['speed-delay-exclude'] ) ? preg_split( '/[\r\n,]+/', (string) $horsetools_options['speed-delay-exclude'], -1, PREG_SPLIT_NO_EMPTY ) : array();
		$exclude = is_array( $exclude ) ? array_map( 'trim', $exclude ) : array();
		if ( 'all' === $mode ) {
			// jQuery core must keep running at parse time even in "all" mode: an
			// inline script that is NOT delayed (an excluded one, or one using
			// document.write) may call $ / jQuery immediately, and delaying jQuery
			// core would throw a ReferenceError. jQuery *plugins* are still delayed.
			$exclude = array_merge( $exclude, array( 'jquery.js', 'jquery.min.js', 'jquery-migrate', '/jquery/jquery', 'code.jquery.com/jquery', 'googleapis.com/ajax/libs/jquery' ) );
		}

		// Strip a real type attribute whether it is quoted or not.
		$strip_type = '#\stype\s*=\s*("[^"]*"|\'[^\']*\'|[^\s"\'>]+)#i';
		$count = 0;
		$out   = preg_replace_callback(
			// Quote-aware attribute run (a quote matches only its string branch, so
			// no super-linear backtracking), and a whitespace-tolerant terminator.
			'#<script\b((?:"[^"]*"|\'[^\']*\'|[^"\'>])*)>([\s\S]*?)</script\s*>#i',
			function ( $m ) use ( $mode, $list, $exclude, $strip_type, &$count ) {
				$attrs = $m[1];
				$body  = $m[2];
				// Never touch structured data, JSON, ES modules, import maps,
				// templates, this plugin's own loader, or explicit opt-outs.
				if ( preg_match( '#type\s*=\s*["\']?\s*(application/ld\+json|application/json|module|importmap|text/template|text/html|text/x-template)#i', $attrs ) ) {
					return $m[0];
				}
				if ( false !== stripos( $attrs, 'data-ht-no-delay' ) || false !== stripos( $attrs, 'data-ht-delay-loader' ) ) {
					return $m[0];
				}
				$has_src = preg_match( '#\ssrc\s*=\s*["\']?([^"\'\s>]+)#i', $attrs, $srcm );
				if ( ! $has_src ) {
					// document.write() cannot run once deferred; empty scripts are
					// pointless to delay.
					if ( '' === trim( $body ) || false !== stripos( $body, 'document.write' ) ) {
						return $m[0];
					}
				}
				$needle = $has_src ? $srcm[1] : $body;
				if ( ! horsetools_delay_should( $needle, $mode, $list, $exclude ) ) {
					return $m[0];
				}
				$count++;
				if ( $has_src ) {
					// Rename exactly the src occurrence we detected (not a blind
					// first match, which could land inside another attribute value).
					$src_rewritten = preg_replace( '#^\s+src#i', ' data-ht-src', $srcm[0], 1 );
					$new = str_replace( $srcm[0], $src_rewritten, $attrs );
					$new = preg_replace( $strip_type, '', $new );
					return '<script type="ht/delayed"' . $new . '></script>';
				}
				$new = preg_replace( $strip_type, '', $attrs );
				return '<script type="ht/delayed"' . $new . '>' . $body . '</script>';
			},
			$buffer
		);

		if ( ! $count || null === $out ) {
			return $buffer; // nothing delayed — leave the page byte-for-byte as it was
		}
		$timeout = isset( $horsetools_options['speed-delay-timeout'] ) ? (int) $horsetools_options['speed-delay-timeout'] : 0;
		$timeout = max( 0, min( 60, $timeout ) ) * 1000;
		// Carry a CSP nonce onto the loader if the page uses one, so a strict
		// script-src policy does not block the one script that revives the rest.
		$nonce = '';
		if ( preg_match( '#<script[^>]*\snonce=(["\'])([^"\']+)\1#i', $out, $nm ) ) {
			$nonce = ' nonce="' . esc_attr( $nm[2] ) . '"';
		}
		$loader  = '<script data-ht-delay-loader' . $nonce . '>' . horsetools_delay_loader_js( $timeout ) . '</script>';
		if ( false !== stripos( $out, '</body>' ) ) {
			return preg_replace( '#</body>#i', $loader . '</body>', $out, 1 );
		}
		return $out . $loader;
	}

	// The loader. Compact and inline so it can never itself be delayed, minified
	// away or reordered by another optimiser.
	function horsetools_delay_loader_js( $timeout_ms ) {
		$js  = "(function(){var d=false,t=null,E=['keydown','mousedown','mousemove','wheel','touchstart','touchmove','touchend','click','scroll'];";
		$js .= "function load(){if(d)return;d=true;E.forEach(function(e){window.removeEventListener(e,load,{passive:true})});if(t)clearTimeout(t);";
		// Capture DOMContentLoaded/load handlers that the DELAYED scripts register
		// while they run, then fire only those afterwards. This is why we don't
		// blindly re-dispatch the events globally: that would re-run handlers the
		// page already ran at real load time (double analytics hits, re-inited
		// widgets). Only late libraries that missed the events get a replay.
		$js .= "var q=[],dA=document.addEventListener,wA=window.addEventListener;";
		$js .= "function cap(tg,orig){return function(ty,fn,o){if((ty==='DOMContentLoaded'||ty==='load')&&typeof fn==='function'){q.push([fn,ty])}else{orig.call(tg,ty,fn,o)}}}";
		$js .= "document.addEventListener=cap(document,dA);window.addEventListener=cap(window,wA);";
		$js .= "var n=document.querySelectorAll('script[type=\"ht/delayed\"]'),i=0;(function go(){";
		$js .= "if(i>=n.length){document.addEventListener=dA;window.addEventListener=wA;q.forEach(function(h){try{h[0].call(h[1]==='load'?window:document,new Event(h[1]))}catch(e){}});return}";
		$js .= "var o=n[i++],s=document.createElement('script'),a,x;for(a=0;a<o.attributes.length;a++){x=o.attributes[a];if(x.name==='type'||x.name==='data-ht-src')continue;s.setAttribute(x.name,x.value)}";
		$js .= "var src=o.getAttribute('data-ht-src');if(src){s.src=src;s.onload=s.onerror=go;o.parentNode.replaceChild(s,o)}else{s.text=o.textContent;o.parentNode.replaceChild(s,o);go()}})()}";
		$js .= "E.forEach(function(e){window.addEventListener(e,load,{passive:true})});";
		if ( $timeout_ms > 0 ) {
			$js .= "t=setTimeout(load,{$timeout_ms});";
		}
		$js .= "})();";
		return $js;
	}

	function horsetools_delay_init() {
		if ( is_admin() || is_user_logged_in() ) {
			return;
		}
		if ( wp_doing_ajax() || wp_doing_cron() || is_feed() || is_embed()
			|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			|| ( defined( 'WP_CLI' ) && WP_CLI )
			|| ( function_exists( 'is_customize_preview' ) && is_customize_preview() )
			|| ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ) {
			return;
		}
		ob_start( 'horsetools_delay_transform' );
	}
	add_action( 'template_redirect', 'horsetools_delay_init', 1 );
}

/* -------------------------------------------------------------------------
 * "What scripts does my site run?" scanner.
 *
 * The delay / exclude / defer boxes above ask for script handles or URL
 * keywords — but a non-technical owner has no idea what their site actually
 * loads. This fetches the real home page (as a logged-out visitor would see it),
 * lists every <script> it finds, flags which are already being delayed, and
 * lets the admin drop each one into the delay or exclude box with one click. No
 * need to read source or know a single handle. Admin-only, nonce-protected.
 * ---------------------------------------------------------------------- */
function horsetools_speed_scanjs_ajax() {
	check_ajax_referer( 'horsetools_speed_scanjs', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'msg' => esc_html__( 'Not allowed.', 'horse-tools' ) ) );
	}
	$res = wp_remote_get( add_query_arg( 'ht_scan', time(), home_url( '/' ) ), array(
		'timeout'     => 20,
		'sslverify'   => false,
		'redirection' => 3,
		'user-agent'  => 'Mozilla/5.0 (HorseTools script scan)',
		'headers'     => array( 'Cache-Control' => 'no-cache', 'Pragma' => 'no-cache' ),
	) );
	if ( is_wp_error( $res ) ) {
		wp_send_json_error( array( 'msg' => esc_html__( 'Could not load the home page from the server:', 'horse-tools' ) . ' ' . $res->get_error_message() ) );
	}
	$code = (int) wp_remote_retrieve_response_code( $res );
	$html = (string) wp_remote_retrieve_body( $res );
	if ( $code >= 400 || '' === $html ) {
		/* translators: %s: HTTP status code. */
		wp_send_json_error( array( 'msg' => sprintf( esc_html__( 'Could not read the home page (HTTP %s). Your host may block the site from calling itself; check the scripts via “View source” instead.', 'horse-tools' ), $code ? $code : '0' ) ) );
	}
	$site_host = wp_parse_url( home_url(), PHP_URL_HOST );
	// Inline snippets rarely carry a URL, so recognise the common ones by the
	// global they define and suggest a matching keyword.
	$known = array(
		'gtag('       => 'gtag(',
		'fbq('        => 'fbq(',
		'adsbygoogle' => 'adsbygoogle',
		'dataLayer'   => 'googletagmanager',
		'_gaq'        => 'google-analytics',
		'ttq.'        => 'analytics.tiktok',
		'hotjar'      => 'hotjar',
		'clarity'     => 'clarity.ms',
		'Tawk_API'    => 'tawk.to',
		'twq('        => 'twitter',
		'snaptr('     => 'snapchat',
		'pintrk('     => 'pinterest',
	);
	$found = array();
	if ( preg_match_all( '#<script\b((?:"[^"]*"|\'[^\']*\'|[^"\'>])*)>([\s\S]*?)</script\s*>#i', $html, $mm, PREG_SET_ORDER ) ) {
		foreach ( $mm as $m ) {
			$attrs = $m[1];
			$body  = $m[2];
			if ( false !== stripos( $attrs, 'data-ht-delay-loader' ) ) {
				continue;
			}
			if ( preg_match( '#type\s*=\s*["\']?\s*(application/ld\+json|application/json|module|importmap|text/template|text/html|text/x-template)#i', $attrs ) ) {
				continue;
			}
			$delayed = ( false !== stripos( $attrs, 'ht/delayed' ) );
			if ( preg_match( '#\s(?:data-ht-src|src)\s*=\s*["\']?([^"\'\s>]+)#i', $attrs, $sm ) ) {
				$src  = html_entity_decode( $sm[1] );
				$host = (string) wp_parse_url( $src, PHP_URL_HOST );
				$path = (string) wp_parse_url( $src, PHP_URL_PATH );
				$file = $path ? basename( $path ) : '';
				if ( $host && $host !== $site_host ) {
					$key   = $host;
					$label = $host . $path;
				} else {
					$key   = $file ? $file : ( $path ? $path : $src );
					$label = $path ? $path : $src;
				}
				$id = 'e|' . strtolower( $key );
			} else {
				$trim = trim( $body );
				if ( '' === $trim ) {
					continue;
				}
				$key = '';
				foreach ( $known as $needle => $kw ) {
					if ( false !== stripos( $body, $needle ) ) {
						$key = $kw;
						break;
					}
				}
				$snippet = trim( preg_replace( '/\s+/', ' ', substr( $trim, 0, 55 ) ) );
				$label   = 'inline: ' . $snippet . ( strlen( $trim ) > 55 ? '…' : '' );
				$id      = 'i|' . ( '' !== $key ? $key : substr( md5( $body ), 0, 8 ) );
			}
			if ( ! isset( $found[ $id ] ) ) {
				$found[ $id ] = array(
					'type'    => ( 'e' === $id[0] ? 'external' : 'inline' ),
					'label'   => $label,
					'keyword' => $key,
					'delayed' => $delayed,
				);
			} elseif ( $delayed ) {
				$found[ $id ]['delayed'] = true;
			}
		}
	}
	wp_send_json_success( array( 'scripts' => array_values( $found ), 'total' => count( $found ) ) );
}
add_action( 'wp_ajax_horsetools_speed_scanjs', 'horsetools_speed_scanjs_ajax' );

/* -------------------------------------------------------------------------
 * Preconnect / DNS-prefetch hints.
 *
 * For every third-party origin the page pulls from (Google Fonts, a CDN, an
 * analytics host...), the browser must resolve DNS, open a TCP connection and
 * negotiate TLS before the first byte. Announcing those origins in the <head>
 * lets the browser start that handshake immediately, in parallel with parsing,
 * shaving the round-trips off the critical path. dns-prefetch is the low-cost
 * fallback for browsers that ignore preconnect.
 * ---------------------------------------------------------------------- */
if ( isset( $horsetools_options['speed-pre1'] ) && ! empty( $horsetools_options['speed-pre-hosts'] ) ) {
	function horsetools_preconnect_hints() {
		global $horsetools_options;
		$lines = preg_split( '/[\r\n,]+/', (string) $horsetools_options['speed-pre-hosts'], -1, PREG_SPLIT_NO_EMPTY );
		if ( ! is_array( $lines ) ) {
			return;
		}
		$seen = array();
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			// Accept a bare host (fonts.gstatic.com) or a full URL; reduce to origin.
			if ( false === strpos( $line, '//' ) ) {
				$origin = 'https://' . $line;
			} else {
				$p = wp_parse_url( $line );
				if ( empty( $p['host'] ) ) {
					continue;
				}
				$scheme = ! empty( $p['scheme'] ) ? $p['scheme'] : 'https';
				$origin = $scheme . '://' . $p['host'];
			}
			$origin = esc_url( $origin );
			if ( '' === $origin || isset( $seen[ $origin ] ) ) {
				continue;
			}
			$seen[ $origin ] = true;
			// crossorigin is required for the preconnect to be reused by font
			// requests (which are always CORS); harmless for other origins.
			echo '<link rel="preconnect" href="' . $origin . '" crossorigin>' . "\n";
			echo '<link rel="dns-prefetch" href="' . $origin . '">' . "\n";
		}
	}
	add_action( 'wp_head', 'horsetools_preconnect_hints', 1 );
}

/* -------------------------------------------------------------------------
 * Control the WordPress Heartbeat API.
 *
 * Heartbeat POSTs to admin-ajax.php on a timer (every 15–60s) for autosave,
 * post-lock and dashboard widgets. On a busy site — or with several admin tabs
 * open — that is a steady stream of PHP requests. Three safe levels:
 *   slow     : keep it, but at the slowest interval (60s).
 *   frontend : drop it on the front-end (visitors never need it), 60s in admin.
 *   minimal  : allow it only in the post editor (where autosave/locking live).
 * ---------------------------------------------------------------------- */
if ( isset( $horsetools_options['speed-hb1'] ) ) {
	$horsetools_hb_mode = ! empty( $horsetools_options['speed-hb2'] ) ? $horsetools_options['speed-hb2'] : 'slow';

	if ( 'frontend' === $horsetools_hb_mode ) {
		add_action( 'init', function () {
			if ( ! is_admin() ) {
				wp_deregister_script( 'heartbeat' );
			}
		}, 1 );
	} elseif ( 'minimal' === $horsetools_hb_mode ) {
		add_action( 'init', function () {
			global $pagenow;
			if ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) {
				wp_deregister_script( 'heartbeat' );
			}
		}, 1 );
	}
	// Slow the tick wherever Heartbeat still runs. WP clamps interval to 15–120s.
	add_filter( 'heartbeat_settings', function ( $settings ) {
		$settings['interval'] = 60;
		return $settings;
	} );
}

/* -------------------------------------------------------------------------
 * Preload critical assets.
 *
 * preconnect only warms the connection; preload actually starts fetching a
 * specific file early. Best for the LCP image, a web font, or the main CSS.
 * We derive the required `as` (and font type + crossorigin) from the file
 * extension so the hint is valid — a preload with the wrong `as` is ignored
 * and warns in the console.
 * ---------------------------------------------------------------------- */
if ( isset( $horsetools_options['speed-preload1'] ) && ! empty( $horsetools_options['speed-preload-urls'] ) ) {
	function horsetools_preload_assets() {
		global $horsetools_options;
		$lines = preg_split( '/[\r\n]+/', (string) $horsetools_options['speed-preload-urls'], -1, PREG_SPLIT_NO_EMPTY );
		if ( ! is_array( $lines ) ) {
			return;
		}
		$font_types = array( 'woff2' => 'font/woff2', 'woff' => 'font/woff', 'ttf' => 'font/ttf', 'otf' => 'font/otf' );
		$seen       = array();
		foreach ( $lines as $line ) {
			$url = esc_url( trim( $line ) );
			if ( '' === $url || isset( $seen[ $url ] ) ) {
				continue;
			}
			$seen[ $url ] = true;
			$ext   = strtolower( (string) pathinfo( (string) wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
			$as    = '';
			$extra = '';
			if ( isset( $font_types[ $ext ] ) ) {
				$as    = 'font';
				$extra = ' type="' . $font_types[ $ext ] . '" crossorigin';
			} elseif ( 'css' === $ext ) {
				$as = 'style';
			} elseif ( 'js' === $ext ) {
				$as = 'script';
			} elseif ( in_array( $ext, array( 'jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'svg' ), true ) ) {
				$as = 'image';
			}
			if ( '' === $as ) {
				continue; // unknown type — skip rather than emit an invalid hint
			}
			echo '<link rel="preload" href="' . $url . '" as="' . $as . '"' . $extra . '>' . "\n";
		}
	}
	add_action( 'wp_head', 'horsetools_preload_assets', 2 );
}

/* -------------------------------------------------------------------------
 * Drop the Dashicons stylesheet for logged-out visitors.
 *
 * Dashicons is the admin icon font. Many themes enqueue it on the front-end but
 * only actually need it for the logged-in admin bar, so anonymous visitors
 * download an icon font they never see. Keep it whenever a user is logged in
 * (the admin bar really uses it).
 * ---------------------------------------------------------------------- */
if ( isset( $horsetools_options['speed-dash1'] ) ) {
	function horsetools_dequeue_dashicons() {
		if ( ! is_user_logged_in() ) {
			// Only dequeue, never deregister: if another front-end stylesheet
			// declares dashicons as a dependency, deregistering it would drop
			// that stylesheet too. Dequeuing removes it when nothing needs it,
			// and leaves it in place (pulled back as a dep) when something does.
			wp_dequeue_style( 'dashicons' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'horsetools_dequeue_dashicons', 100 );
}