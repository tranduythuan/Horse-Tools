<?php
/**
 * Horse Tools — shortcode library.
 *
 * A set of ready-made display shortcodes (alerts, accordions/FAQ, tabs,
 * click-to-copy, spoilers, progress bars, countdowns, buttons, obfuscated
 * email, raw output). Loaded with the Shortcode module. Assets (htsc.css/js)
 * are registered up front but only enqueued on pages that actually use one of
 * these shortcodes, so they cost nothing elsewhere.
 *
 * Attributes are escaped; inner content is passed through do_shortcode() so
 * nested shortcodes and author HTML work as they do anywhere in post content.
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Register the shared assets (enqueued on demand by the shortcodes). */
function horsetools_sc_register_assets() {
	wp_register_style( 'horsetools-sc', HORSETOOLS_URL . 'link/shortcode/htsc.css', array(), HORSETOOLS_VERSION );
	wp_register_script( 'horsetools-sc', HORSETOOLS_URL . 'link/shortcode/htsc.js', array(), HORSETOOLS_VERSION, true );
	wp_localize_script(
		'horsetools-sc',
		'horsetoolsCdLabels',
		array(
			'd'    => __( 'days', 'horse-tools' ),
			'h'    => __( 'hrs', 'horse-tools' ),
			'm'    => __( 'min', 'horse-tools' ),
			's'    => __( 'sec', 'horse-tools' ),
			'done' => __( 'Time is up', 'horse-tools' ),
		)
	);
	if ( ! wp_style_is( 'horsetools-tabler', 'registered' ) ) {
		wp_register_style( 'horsetools-tabler', HORSETOOLS_URL . 'link/tabler/tabler-icons.css', array(), HORSETOOLS_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'horsetools_sc_register_assets' );

/** Enqueue the library assets (call from a shortcode that needs them). */
function horsetools_sc_assets( $icons = false ) {
	wp_enqueue_style( 'horsetools-sc' );
	wp_enqueue_script( 'horsetools-sc' );
	if ( $icons ) {
		wp_enqueue_style( 'horsetools-tabler' );
	}
}

/** A safe Tabler icon slug, or ''. */
function horsetools_sc_icon_slug( $name ) {
	$name = preg_replace( '/^ti-/', '', strtolower( trim( (string) $name ) ) );
	return preg_replace( '/[^a-z0-9-]/', '', $name );
}

/* ---- Alert / callout --------------------------------------------------- */
function horsetools_sc_alert( $atts, $content = '' ) {
	$a     = shortcode_atts( array( 'type' => 'info', 'icon' => '' ), $atts, 'ht-alert' );
	$types = array( 'info' => 'info-circle', 'success' => 'circle-check', 'warning' => 'alert-triangle', 'danger' => 'alert-octagon' );
	$type  = isset( $types[ $a['type'] ] ) ? $a['type'] : 'info';
	$icon  = '' !== $a['icon'] ? horsetools_sc_icon_slug( $a['icon'] ) : $types[ $type ];
	horsetools_sc_assets( true );
	return '<div class="ht-alert ht-alert-' . esc_attr( $type ) . '">'
		. ( '' !== $icon ? '<i class="ti ti-' . esc_attr( $icon ) . '" aria-hidden="true"></i>' : '' )
		. '<div>' . do_shortcode( $content ) . '</div></div>';
}
add_shortcode( 'ht-alert', 'horsetools_sc_alert' );

/* ---- Accordion / FAQ --------------------------------------------------- */
function horsetools_sc_accordion( $atts, $content = '' ) {
	$a = shortcode_atts( array( 'exclusive' => '1', 'faq' => '' ), $atts, 'ht-accordion' );
	static $n = 0;
	$n++;
	$exclusive = in_array( strtolower( (string) $a['exclusive'] ), array( '1', 'yes', 'true' ), true );
	$GLOBALS['horsetools_acc_name'] = $exclusive ? 'ht-acc-' . $n : '';
	horsetools_sc_assets();
	$out = '<div class="ht-accordion">' . do_shortcode( $content ) . '</div>';
	$GLOBALS['horsetools_acc_name'] = '';

	// Optional FAQ schema built from the raw item list.
	if ( in_array( strtolower( (string) $a['faq'] ), array( '1', 'yes', 'true' ), true )
		&& preg_match_all( '/\[ht-item[^\]]*\btitle="([^"]*)"[^\]]*\](.*?)\[\/ht-item\]/is', $content, $m, PREG_SET_ORDER ) ) {
		$faqs = array();
		foreach ( $m as $item ) {
			$q = trim( wp_strip_all_tags( $item[1] ) );
			$ans = trim( wp_strip_all_tags( do_shortcode( $item[2] ) ) );
			if ( '' === $q || '' === $ans ) {
				continue;
			}
			$faqs[] = array(
				'@type'          => 'Question',
				'name'           => $q,
				'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $ans ),
			);
		}
		if ( $faqs ) {
			$schema = array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faqs );
			$out   .= '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
		}
	}
	return $out;
}
add_shortcode( 'ht-accordion', 'horsetools_sc_accordion' );

function horsetools_sc_item( $atts, $content = '' ) {
	$a    = shortcode_atts( array( 'title' => '', 'open' => '' ), $atts, 'ht-item' );
	$name = isset( $GLOBALS['horsetools_acc_name'] ) ? $GLOBALS['horsetools_acc_name'] : '';
	$open = in_array( strtolower( (string) $a['open'] ), array( '1', 'yes', 'true', 'open' ), true ) ? ' open' : '';
	return '<details class="ht-acc-item"' . ( '' !== $name ? ' name="' . esc_attr( $name ) . '"' : '' ) . $open . '>'
		. '<summary>' . esc_html( $a['title'] ) . '</summary>'
		. '<div class="ht-acc-body">' . do_shortcode( $content ) . '</div></details>';
}
add_shortcode( 'ht-item', 'horsetools_sc_item' );

/* ---- Single toggle ----------------------------------------------------- */
function horsetools_sc_toggle( $atts, $content = '' ) {
	$a    = shortcode_atts( array( 'title' => '', 'open' => '' ), $atts, 'ht-toggle' );
	$open = in_array( strtolower( (string) $a['open'] ), array( '1', 'yes', 'true', 'open' ), true ) ? ' open' : '';
	horsetools_sc_assets();
	return '<details class="ht-toggle"' . $open . '><summary>' . esc_html( $a['title'] ) . '</summary>'
		. '<div class="ht-acc-body">' . do_shortcode( $content ) . '</div></details>';
}
add_shortcode( 'ht-toggle', 'horsetools_sc_toggle' );

/* ---- Tabs -------------------------------------------------------------- */
function horsetools_sc_tabs( $atts, $content = '' ) {
	$GLOBALS['horsetools_tabs'] = array();
	do_shortcode( $content ); // [ht-tab] fills the buffer
	$tabs = $GLOBALS['horsetools_tabs'];
	unset( $GLOBALS['horsetools_tabs'] );
	if ( empty( $tabs ) ) {
		return '';
	}
	static $n = 0;
	$n++;
	horsetools_sc_assets();
	$nav = '';
	$panels = '';
	foreach ( $tabs as $i => $t ) {
		$active = 0 === $i ? ' active' : '';
		$nav   .= '<button type="button" class="ht-tab-btn' . $active . '" data-tab="' . $n . '-' . $i . '">' . esc_html( $t['title'] ) . '</button>';
		$panels .= '<div class="ht-tab-panel' . $active . '" id="ht-tab-' . $n . '-' . $i . '">' . $t['content'] . '</div>';
	}
	return '<div class="ht-tabs"><div class="ht-tab-nav">' . $nav . '</div><div class="ht-tab-panels">' . $panels . '</div></div>';
}
add_shortcode( 'ht-tabs', 'horsetools_sc_tabs' );

function horsetools_sc_tab( $atts, $content = '' ) {
	$a = shortcode_atts( array( 'title' => '' ), $atts, 'ht-tab' );
	if ( isset( $GLOBALS['horsetools_tabs'] ) && is_array( $GLOBALS['horsetools_tabs'] ) ) {
		$GLOBALS['horsetools_tabs'][] = array( 'title' => $a['title'], 'content' => do_shortcode( $content ) );
	}
	return '';
}
add_shortcode( 'ht-tab', 'horsetools_sc_tab' );

/* ---- Click-to-copy ----------------------------------------------------- */
function horsetools_sc_copy( $atts, $content = '' ) {
	$text = trim( wp_strip_all_tags( $content ) );
	if ( '' === $text ) {
		return '';
	}
	horsetools_sc_assets( true );
	return '<span class="ht-copy" data-copy="' . esc_attr( $text ) . '"><code>' . esc_html( $text ) . '</code>'
		. '<button type="button" class="ht-copy-btn" aria-label="Copy"><i class="ti ti-copy" aria-hidden="true"></i></button></span>';
}
add_shortcode( 'ht-copy', 'horsetools_sc_copy' );

/* ---- Reveal / spoiler -------------------------------------------------- */
function horsetools_sc_reveal( $atts, $content = '' ) {
	$a = shortcode_atts( array( 'title' => __( 'Reveal', 'horse-tools' ) ), $atts, 'ht-reveal' );
	horsetools_sc_assets();
	return '<details class="ht-reveal"><summary>' . esc_html( $a['title'] ) . '</summary>'
		. '<div class="ht-reveal-body">' . do_shortcode( $content ) . '</div></details>';
}
add_shortcode( 'ht-reveal', 'horsetools_sc_reveal' );

/* ---- Progress bar ------------------------------------------------------ */
function horsetools_sc_progress( $atts ) {
	$a     = shortcode_atts( array( 'value' => '0', 'label' => '', 'color' => '' ), $atts, 'ht-progress' );
	$value = max( 0, min( 100, (int) $a['value'] ) );
	$color = '' !== $a['color'] ? horsetools_css_color( $a['color'] ) : '';
	horsetools_sc_assets();
	$style = 'width:' . $value . '%;' . ( '' !== $color ? 'background:' . $color . ';' : '' );
	$head  = ( '' !== $a['label'] || true )
		? '<div class="ht-progress-label"><span>' . esc_html( $a['label'] ) . '</span><span>' . $value . '%</span></div>'
		: '';
	return '<div class="ht-progress">' . $head
		. '<div class="ht-progress-track"><div class="ht-progress-fill" style="' . esc_attr( $style ) . '"></div></div></div>';
}
add_shortcode( 'ht-progress', 'horsetools_sc_progress' );

/* ---- Countdown --------------------------------------------------------- */
function horsetools_sc_countdown( $atts ) {
	$a = shortcode_atts( array( 'date' => '', 'done' => '' ), $atts, 'ht-countdown' );
	$ts = 0;
	if ( '' !== $a['date'] ) {
		try {
			$dt = new DateTime( $a['date'], wp_timezone() );
			$ts = $dt->getTimestamp() * 1000;
		} catch ( Exception $e ) {
			$ts = 0;
		}
	}
	if ( ! $ts ) {
		return '';
	}
	horsetools_sc_assets();
	return '<span class="ht-countdown" data-ts="' . esc_attr( $ts ) . '"'
		. ( '' !== $a['done'] ? ' data-done="' . esc_attr( $a['done'] ) . '"' : '' ) . '></span>';
}
add_shortcode( 'ht-countdown', 'horsetools_sc_countdown' );

/* ---- Button ------------------------------------------------------------ */
function horsetools_sc_button( $atts, $content = '' ) {
	$a = shortcode_atts(
		array( 'url' => '#', 'icon' => '', 'color' => '', 'target' => '', 'size' => '', 'style' => '', 'rel' => '' ),
		$atts,
		'ht-button'
	);
	horsetools_sc_assets( '' !== $a['icon'] );
	$class = 'ht-btn';
	if ( 'big' === $a['size'] ) {
		$class .= ' big';
	}
	if ( 'ghost' === $a['style'] || 'outline' === $a['style'] ) {
		$class .= ' ghost';
	}
	$color   = '' !== $a['color'] ? horsetools_css_color( $a['color'] ) : '';
	$css     = '' !== $color ? ( ( 'ghost' === $a['style'] || 'outline' === $a['style'] ) ? 'color:' . $color . ';' : 'background:' . $color . ';' ) : '';
	$icon    = '' !== $a['icon'] ? '<i class="ti ti-' . esc_attr( horsetools_sc_icon_slug( $a['icon'] ) ) . '" aria-hidden="true"></i> ' : '';
	$blank   = '_blank' === $a['target'] ? ' target="_blank"' : '';
	$rel     = ( '_blank' === $a['target'] ) ? ' rel="' . esc_attr( '' !== $a['rel'] ? $a['rel'] : 'noopener' ) . '"' : ( '' !== $a['rel'] ? ' rel="' . esc_attr( $a['rel'] ) . '"' : '' );
	$label   = do_shortcode( $content );
	if ( '' === trim( wp_strip_all_tags( $label ) ) ) {
		$label = esc_html__( 'Click here', 'horse-tools' );
	}
	return '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $a['url'] ) . '"'
		. ( '' !== $css ? ' style="' . esc_attr( $css ) . '"' : '' ) . $blank . $rel . '>' . $icon . $label . '</a>';
}
add_shortcode( 'ht-button', 'horsetools_sc_button' );

/* ---- Obfuscated email -------------------------------------------------- */
function horsetools_sc_email( $atts, $content = '' ) {
	$a     = shortcode_atts( array( 'text' => '', 'subject' => '' ), $atts, 'ht-email' );
	$email = trim( wp_strip_all_tags( $content ) );
	if ( ! is_email( $email ) ) {
		return '';
	}
	horsetools_sc_assets();
	list( $user, $domain ) = explode( '@', $email );
	return '<a class="ht-email" href="#" data-u="' . esc_attr( $user ) . '" data-d="' . esc_attr( $domain ) . '"'
		. ( '' !== $a['subject'] ? ' data-s="' . esc_attr( $a['subject'] ) . '"' : '' ) . '>'
		. ( '' !== $a['text'] ? esc_html( $a['text'] ) : '' ) . '</a>';
}
add_shortcode( 'ht-email', 'horsetools_sc_email' );

/* ---- Raw (undo wpautop) ------------------------------------------------ */
function horsetools_sc_raw( $atts, $content = '' ) {
	// wpautop runs before shortcodes on the_content, so $content already has
	// <p>/<br> injected. Reverse the common damage.
	$content = preg_replace( '#^\s*<p>#', '', (string) $content );
	$content = preg_replace( '#</p>\s*$#', '', $content );
	$content = preg_replace( '#</p>\s*<p>#', "\n\n", $content );
	$content = preg_replace( '#<br\s*/?>#', "\n", $content );
	return do_shortcode( $content );
}
add_shortcode( 'ht-raw', 'horsetools_sc_raw' );
