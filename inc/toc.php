<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $horsetools_toc_options;
if (isset($horsetools_toc_options['toc1'])){
# add css js chat web
function horsetools_enqueue_toc(){
	global $horsetools_toc_options;
	if (isset($horsetools_toc_options['posttype']) && !empty($horsetools_toc_options['posttype'])) {
		$current_post_type = get_post_type();
        if (in_array($current_post_type, $horsetools_toc_options['posttype'])) {
			wp_enqueue_style('htoc-css', HORSETOOLS_URL . 'link/toc/horsetoc.css', array(), HORSETOOLS_VERSION);
			wp_enqueue_script('htoc-js', HORSETOOLS_URL . 'link/toc/horsetoc.js', array(), HORSETOOLS_VERSION, true);
		}
			
	}
}
add_action('wp_enqueue_scripts', 'horsetools_enqueue_toc');
# functions add
/**
 * Sanitise a pasted SVG icon.
 *
 * Allows only the shape elements an icon needs, and no attribute that can
 * execute anything: no script, no on* handler, no href. wp_kses drops the
 * rest silently rather than refusing, so a nearly-right paste still works
 * instead of leaving the icon blank with no explanation.
 *
 * @param string $svg
 * @return string Empty when nothing usable survives.
 */
function horsetools_toc_clean_svg( $svg ) {
	$svg = trim( (string) $svg );
	if ( '' === $svg || false === stripos( $svg, '<svg' ) ) {
		return '';
	}
	$common = array( 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'opacity' => true, 'transform' => true, 'class' => true, 'style' => true, 'fill-rule' => true, 'clip-rule' => true );
	$allowed = array(
		'svg'      => array_merge( $common, array( 'xmlns' => true, 'viewbox' => true, 'width' => true, 'height' => true, 'role' => true, 'aria-label' => true, 'aria-hidden' => true, 'focusable' => true ) ),
		'path'     => array_merge( $common, array( 'd' => true ) ),
		'g'        => $common,
		'circle'   => array_merge( $common, array( 'cx' => true, 'cy' => true, 'r' => true ) ),
		'ellipse'  => array_merge( $common, array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ) ),
		'rect'     => array_merge( $common, array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true ) ),
		'line'     => array_merge( $common, array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ) ),
		'polyline' => array_merge( $common, array( 'points' => true ) ),
		'polygon'  => array_merge( $common, array( 'points' => true ) ),
		'title'    => array(),
	);
	$clean = wp_kses( $svg, $allowed );
	return ( false !== stripos( $clean, '<svg' ) ) ? $clean : '';
}

function horsetools_toc_fun(){
	global $horsetools_toc_options;
	$tags = []; 
	$all_tags = ['h2', 'h3', 'h4', 'h5', 'h6']; 
	$has_checked_tags = false; 
	for ($i = 1; $i <= 6; $i++) {
		if (isset($horsetools_toc_options['tit_h' . $i]) && 1 == $horsetools_toc_options['tit_h' . $i]) {
			$tags[] = 'h' . $i; 
			$has_checked_tags = true; 
		}
	}
	if (!$has_checked_tags) {
		$tagh = implode(', ', $all_tags); 
	} else {
		$tagh = implode(', ', $tags); 
	}
	$title = !empty($horsetools_toc_options['tit-c1']) ? esc_html($horsetools_toc_options['tit-c1']) : __('Table of Contents', 'horse-tools');
	$show = !empty($horsetools_toc_options['tit-c3']) ? 'ht-toc-main-open' : NULL;
	$onnumber = !isset($horsetools_toc_options['tit-c4']) ? 'data-on="on"' : NULL;
	$hiddenlist = isset($horsetools_toc_options['tit-c5']) ? 'style="display:none"' : NULL;
	$hiddenicon = isset($horsetools_toc_options['tit-c6']) ? 'data-ico="off"' : NULL;
	// The default was still Foxtool's mark — the shape this plugin was forked
	// from — sitting on the front end of every site using the table of contents.
	// currentColor so it takes the list colour like the other icons do.
	$dtocicon = function_exists( 'horsetools_brand_mark_svg' )
		? horsetools_brand_mark_svg( 'currentColor' )
		: '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/></svg>';
	if (isset($horsetools_toc_options['main-ico'])) {
		$tocico_option = $horsetools_toc_options['main-ico'];
		switch ($tocico_option) {
			case 'Icon2':
				$tocicoset = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-list-task" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2 2.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5V3a.5.5 0 0 0-.5-.5zM3 3H2v1h1z"/><path d="M5 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M5.5 7a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1zm0 4a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1z"/><path fill-rule="evenodd" d="M1.5 7a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5zM2 7h1v1H2zm0 3.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm1 .5H2v1h1z"/></svg>';
				break;
			case 'Icon3':
				$tocicoset = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-list-ol" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5"/><path d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635z"/></svg>';
				break;
			case 'Icon4':
				$tocicoset = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-list-nested" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.5 11.5A.5.5 0 0 1 5 11h10a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5m-2-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m-2-4A.5.5 0 0 1 1 3h10a.5.5 0 0 1 0 1H1a.5.5 0 0 1-.5-.5"/></svg>';
				break;
			case 'Icon5':
				$tocicoset = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>';
				break;
			case 'Icon6':
				$tocicoset = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/></svg>';
				break;
			default:
				$tocicoset = $dtocicon;
		}
	} else {
		$tocicoset = $dtocicon;
	}

	// A pasted SVG wins over every built-in choice. Kept to <svg> only and run
	// through wp_kses so a stored icon can never carry a script or an event
	// handler — this is echoed on the front end of every page with a contents
	// list, so it is exactly the wrong place to trust stored markup.
	if ( ! empty( $horsetools_toc_options['main-icosvg'] ) ) {
		$custom = horsetools_toc_clean_svg( $horsetools_toc_options['main-icosvg'] );
		if ( '' !== $custom ) {
			$tocicoset = $custom;
		}
	}

	$iconcl ='<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 1024 1024"><path fill="currentColor" d="M195.2 195.2a64 64 0 0 1 90.496 0L512 421.504L738.304 195.2a64 64 0 0 1 90.496 90.496L602.496 512L828.8 738.304a64 64 0 0 1-90.496 90.496L512 602.496L285.696 828.8a64 64 0 0 1-90.496-90.496L421.504 512L195.2 285.696a64 64 0 0 1 0-90.496"/></svg>';
	$iconhi = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 1024 1024"><path fill="currentColor" d="M104.704 685.248a64 64 0 0 0 90.496 0l316.8-316.8l316.8 316.8a64 64 0 0 0 90.496-90.496L557.248 232.704a64 64 0 0 0-90.496 0L104.704 594.752a64 64 0 0 0 0 90.496"/></svg>';
	$toc_html = '<div class="ht-toc-placeholder" data-h="'. $tagh .'" '. $onnumber .' '. $hiddenicon .'><div class="ht-toc-main '. $show .' ">
		<div class="ht-toc-close" onclick="tocclose();" style="display:none">'. $tocicoset .'</div>
		<div class="ht-toc-tit"><span class="ht-toc-tit-sp"><span class="ht-toc-tit-svg">'. $tocicoset .'</span><span class="ht-toc-close2" onclick="tocclose();">'. $iconcl .'</span>'. $title .'</span><span class="ht-toc-tit-hi">'. $iconhi .'</span></div>
		<div class="ht-toc-scrol">
		<ol id="ht-toc-list" '. $hiddenlist .'>
		</ol>
		</div>
	</div></div>';
	return $toc_html;	
}
# add content
function horsetools_toc( $content ) {
	// the_content also runs on archives, search results, feeds and secondary
	// loops. Without this guard every post in a full-content archive got its own
	// <div id="ht-toc">, producing duplicate DOM ids — and the widget script,
	// which does getElementById("ht-toc"), then listed the first post's headings
	// for the whole page. The markup also shipped inside RSS items.
	//
	// is_singular()/in_the_loop()/is_main_query() only describe the page's main
	// query, not which post is actually being filtered right now. A "related
	// posts" block or a card list on a singular page runs its own WP_Query and
	// calls the_post() on that — the main query's flags stay unchanged, so the
	// checks above still pass, and get_the_excerpt() on that secondary query
	// applies this same the_content filter to a different post before trimming
	// it, leaving the TOC title text stuck to the front of that post's excerpt.
	// Comparing the post actually being filtered to the queried object closes
	// that gap.
	if ( ! is_singular() || ! in_the_loop() || ! is_main_query() || is_feed() || get_the_ID() !== get_queried_object_id() ) {
		return $content;
	}	global $horsetools_toc_options;
	$pages_list = explode("\n", str_replace("\r", "",  $horsetools_toc_options['toc-page-hi'] ?? ''));
	$toc_status = get_post_meta(get_the_ID(), 'toc_status', true);
	if (isset($horsetools_toc_options['posttype']) && !isset($horsetools_toc_options['tag']) && !empty($horsetools_toc_options['posttype']) && !is_page($pages_list) && $toc_status !== 'disabled') {
		$current_post_type = get_post_type();
        if (in_array($current_post_type, $horsetools_toc_options['posttype'])) {
			$settoc = !isset($horsetools_toc_options['shortcode']) ? horsetools_toc_fun() : NULL;
			return $settoc .'<div id="ht-toc">'. $content . '</div>';
		}		
	}
	return $content;
}
add_filter( 'the_content', 'horsetools_toc' );
# shortcode
function horsetools_toc_shortcode($atts) {
	global $horsetools_toc_options;
	$pages_list = explode("\n", str_replace("\r", "",  $horsetools_toc_options['toc-page-hi'] ?? ''));
	$toc_status = get_post_meta(get_the_ID(), 'toc_status', true);
	if (isset($horsetools_toc_options['posttype']) && !empty($horsetools_toc_options['posttype']) && !is_page($pages_list) && $toc_status !== 'disabled') {
		$current_post_type = get_post_type();
        if (in_array($current_post_type, $horsetools_toc_options['posttype'])) {
		$settoc = isset($horsetools_toc_options['shortcode']) ? horsetools_toc_fun() : NULL;
		return $settoc;
		}
	}
	return;
}
add_shortcode('horsetoc', 'horsetools_toc_shortcode');
# vi tri tuy bien theo the h
function horsetools_toc_after( $insertion, $paragraph_id, $content ) {
	global $horsetools_toc_options;
	$tag = !empty($horsetools_toc_options['tag1']) ? preg_replace('/[^a-z0-9]/i', '', (string) $horsetools_toc_options['tag1']) : 'h2';
    $pattern = "/(<$tag\b[^>]*>)(.*?)(<\/$tag>)/is";
    $matches = array();
    preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
    $new_content = '';
    $last_pos = 0;
    foreach ($matches[0] as $index => $match) {
        $pos = $match[1];
        $length = strlen($match[0]);
        if ( $paragraph_id == ($index + 1) ) {
            $new_content .= substr($content, $last_pos, $pos - $last_pos) . $insertion . $match[0];
        } else {
            $new_content .= substr($content, $last_pos, $pos - $last_pos) . $match[0];
        }
        $last_pos = $pos + $length;
    }
    $new_content .= substr($content, $last_pos);
    return $new_content;
}
// add vao content
function horsetools_add_content_toc($content) {
	// the_content also runs on archives, search results, feeds and secondary
	// loops. Without this guard every post in a full-content archive got its own
	// <div id="ht-toc">, producing duplicate DOM ids — and the widget script,
	// which does getElementById("ht-toc"), then listed the first post's headings
	// for the whole page. The markup also shipped inside RSS items.
	//
	// See horsetools_toc() above for why get_the_ID() must also match the
	// queried object: a secondary loop (related posts, card lists) on a
	// singular page leaves is_singular()/in_the_loop()/is_main_query() looking
	// unchanged while filtering a different post's excerpt.
	if ( ! is_singular() || ! in_the_loop() || ! is_main_query() || is_feed() || get_the_ID() !== get_queried_object_id() ) {
		return $content;
	}	global $horsetools_toc_options;
	$pages_list = explode("\n", str_replace("\r", "",  $horsetools_toc_options['toc-page-hi'] ?? ''));
	$toc_status = get_post_meta(get_the_ID(), 'toc_status', true);
	if (isset($horsetools_toc_options['posttype']) && isset($horsetools_toc_options['tag']) && !empty($horsetools_toc_options['posttype']) && !is_page($pages_list) && $toc_status !== 'disabled'){
		$current_post_type = get_post_type();
        if (in_array($current_post_type, $horsetools_toc_options['posttype'])) {
		$settoc = !isset($horsetools_toc_options['shortcode']) ? horsetools_toc_fun() : NULL;
		$pin = !empty($horsetools_toc_options['tag2']) ? absint($horsetools_toc_options['tag2']) : '1';
		return '<div id="ht-toc">'. horsetools_toc_after($settoc, $pin, $content) .'</div>';
		}
	}
	return $content;
}
add_filter('the_content', 'horsetools_add_content_toc');
# color
function horsetools_color_toc(){
	global $horsetools_toc_options;
	$pages_list = explode("\n", str_replace("\r", "",  $horsetools_toc_options['toc-page-hi'] ?? ''));
	$toc_status = get_post_meta(get_the_ID(), 'toc_status', true);
	if (isset($horsetools_toc_options['posttype']) && !empty($horsetools_toc_options['posttype']) && !is_page($pages_list) && $toc_status !== 'disabled') {
		$current_post_type = get_post_type();
		if (in_array($current_post_type, $horsetools_toc_options['posttype'])) {
				echo '<style>';
				if (isset($horsetools_toc_options['main-color'])){
				$bgr = !empty($horsetools_toc_options['main-c1']) ? '--tocbgr:'. horsetools_css_color($horsetools_toc_options['main-c1']) .';' : NULL;
				$bor = !empty($horsetools_toc_options['main-c2']) ? '--tocbor:'. horsetools_css_color($horsetools_toc_options['main-c2']) .';' : NULL;
				$lin = !empty($horsetools_toc_options['main-c4']) ? '--toclin:'. horsetools_css_color($horsetools_toc_options['main-c4']) .';' : NULL;
				$sec = !empty($horsetools_toc_options['main-c5']) ? '--tocsec:'. horsetools_css_color($horsetools_toc_options['main-c5']) .';' : NULL;
				
				$titback = !empty($horsetools_toc_options['main-t1']) ? '--toctitback:'. horsetools_css_color($horsetools_toc_options['main-t1']) .';' : NULL;
				$tittext = !empty($horsetools_toc_options['main-t2']) ? '--toctit:'. horsetools_css_color($horsetools_toc_options['main-t2']) .';' : NULL;
				$ticon   = !empty($horsetools_toc_options['main-c8']) ? '--tocico:'. horsetools_css_color($horsetools_toc_options['main-c8']) .';' : NULL;
				
				$scr = !empty($horsetools_toc_options['main-c6']) ? '.ht-toc-scrol *{scrollbar-color: '. horsetools_css_color($horsetools_toc_options['main-c6']) .' #ffffff00;}.ht-toc-scrol ::-webkit-scrollbar-thumb{background-color: '. horsetools_css_color($horsetools_toc_options['main-c6']) .';}' : NULL;
				$lig = !empty($horsetools_toc_options['main-c7']) ? '--toclight:'. horsetools_css_color($horsetools_toc_options['main-c7']) .';' : NULL;
				
				$nutbgr = !empty($horsetools_toc_options['main-b1']) ? '--tocnutbgr:'. horsetools_css_color($horsetools_toc_options['main-b1']) .';' : NULL;
				$nutbor = !empty($horsetools_toc_options['main-b2']) ? '--tocnutbor:'. horsetools_css_color($horsetools_toc_options['main-b2']) .';' : NULL;
				$nutico = !empty($horsetools_toc_options['main-b3']) ? '--tocnutico:'. horsetools_css_color($horsetools_toc_options['main-b3']) .';' : NULL;
				
				$fontsize = !empty($horsetools_toc_options['main-si1']) && $horsetools_toc_options['main-si1'] != 16 ? '--tocsize:'. horsetools_css_number($horsetools_toc_options['main-si1'], '16') .'px;' : NULL;
				
				$mradius = !empty($horsetools_toc_options['main-r1']) && $horsetools_toc_options['main-r1'] != 10 ? '--tocradius:'. horsetools_css_number($horsetools_toc_options['main-r1'], '10') .'px;' : NULL;
				$nradius = !empty($horsetools_toc_options['main-r2']) && $horsetools_toc_options['main-r2'] != 10 ? '--tocnutradius:'. horsetools_css_number($horsetools_toc_options['main-r2'], '10') .'px;' : NULL;
				
				echo ':root{'. $bgr . $bor . $titback . $tittext . $ticon . $lin . $sec . $lig . $nutbgr . $nutbor . $nutico . $mradius . $nradius . $fontsize .'} '. $scr;
				}
				$piton = isset($horsetools_toc_options['main-her1']) && $horsetools_toc_options['main-her1'] == 'Left' ? '.ht-toc-close{right:-55px;left:unset;}.ht-toc-main.ht-toc-main-vuot.ht-toc-main-open{left:10px;right:unset;}.ht-toc-main-vuot {left:-350px;right:unset;transition:left 0.7s ease;}@media(max-width: 400px){.ht-toc-main-open{left: unset;right: 10px !important;}}' : NULL;
				$long = !empty($horsetools_toc_options['main-her2']) && $horsetools_toc_options['main-her2'] != 30 ? '.ht-toc-close {top: '. horsetools_css_number($horsetools_toc_options['main-her2'], '30') .'%;}' : NULL;
				$bien = !empty($horsetools_toc_options['main-her3']) && isset($horsetools_toc_options['main-her1']) && $horsetools_toc_options['main-her1'] == 'Left' ? '.ht-toc-close {right: -'. horsetools_css_number($horsetools_toc_options['main-her3']) .'px;left: unset}' : '.ht-toc-close {left: -'. horsetools_css_number($horsetools_toc_options['main-her3']) .'px;}';
				echo $piton . $long . $bien;
				echo '</style>';
		}
			
	}
}
add_action('wp_footer', 'horsetools_color_toc');
# nut bat tat toc o page, post, product
function horsetools_add_toc_metabox() {
    global $horsetools_toc_options;
    if (isset($horsetools_toc_options['posttype']) && !empty($horsetools_toc_options['posttype'])) {
        $custom_post_types = $horsetools_toc_options['posttype']; 
        add_meta_box('toc_metabox', __('TOC Settings', 'horse-tools'),'horsetools_render_toc_metabox', $custom_post_types,'side', 'high');
    }
}
add_action('add_meta_boxes', 'horsetools_add_toc_metabox');
function horsetools_render_toc_metabox($post) {
    $toc_status = get_post_meta($post->ID, 'toc_status', true);
    $toc_enabled = ($toc_status === 'enabled' || $toc_status === '');
    $toc_disabled = $toc_status === 'disabled';
    ?>
    <label>
        <input type="radio" name="toc_status" value="enabled" <?php checked($toc_enabled); ?> />
        <?php _e('Enabled TOC', 'horse-tools'); ?>
    </label><br/>
    <label>
        <input type="radio" name="toc_status" value="disabled" <?php checked($toc_disabled); ?> />
        <?php _e('Disabled TOC', 'horse-tools'); ?>
    </label>
    <?php
    wp_nonce_field('horsetools_save_toc_metabox', 'horsetools_toc_metabox_nonce');
}
function horsetools_save_toc_metabox($post_id) {
    if (!isset($_POST['horsetools_toc_metabox_nonce']) || !wp_verify_nonce($_POST['horsetools_toc_metabox_nonce'], 'horsetools_save_toc_metabox')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['toc_status'])) {
        update_post_meta($post_id, 'toc_status', sanitize_text_field($_POST['toc_status']));
    }
}
add_action('save_post', 'horsetools_save_toc_metabox');
# add rankmath
function horsetools_add_toc_rankmathseo($toc_plugins) {
    $toc_plugins['horsetools/horsetools.php'] = 'Horse Tools';
    return $toc_plugins;
}
add_filter('rank_math/researches/toc_plugins', 'horsetools_add_toc_rankmathseo');
}