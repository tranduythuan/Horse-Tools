<?php
/**
 * Horse Tools — mobile "Services" slide-up panel.
 *
 * A conversion-focused panel that opens from a bottom-bar item (target
 * #ht-services). One list of service items (icon or image, title, subtitle,
 * link, badge) is rendered through any of several mobile layouts chosen in the
 * admin, in a colour theme the site owner picks. Everything is local — no
 * external requests.
 *
 * Stored in the option horsetools_services:
 *   on, title, layout, color, items[] { icon, img, title, sub, link, badge, badge_color }
 *
 * @package Horse Tools
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Colour themes: name => accent used for header, CTA, tiles, default badge. */
function horsetools_services_themes() {
	return array(
		'gold'    => '#e0a800',
		'blue'    => '#2f6fed',
		'green'   => '#1d9e75',
		'red'     => '#e24b4a',
		'purple'  => '#7f77dd',
		'dark'    => '#2b2b2b',
		'neutral' => '#555555',
	);
}

/** Preset badge colours. */
function horsetools_services_badges() {
	return array(
		'red'   => array( '#fceceb', '#a32d2d' ),
		'amber' => array( '#faeeda', '#854f0b' ),
		'green' => array( '#eaf3de', '#3b6d11' ),
		'blue'  => array( '#e6f1fb', '#185fa5' ),
		'gold'  => array( '#fff3d6', '#8a5a00' ),
		'dark'  => array( '#2b2b2b', '#ffffff' ),
	);
}

/** All available layouts: slug => label (translated at UI time). */
function horsetools_services_layouts() {
	return array( 'bento', 'grid', 'list', 'banner', 'chips', 'tiles', 'coupon', 'story', 'price', 'pricecards' );
}

/** The stored config, normalised. */
function horsetools_services_get() {
	$s = get_option( 'horsetools_services', array() );
	$s = is_array( $s ) ? $s : array();
	return array(
		'on'     => ! empty( $s['on'] ),
		'title'  => isset( $s['title'] ) && '' !== $s['title'] ? $s['title'] : __( 'Services', 'horse-tools' ),
		'layout' => isset( $s['layout'] ) && in_array( $s['layout'], horsetools_services_layouts(), true ) ? $s['layout'] : 'bento',
		'color'  => isset( $s['color'] ) && isset( horsetools_services_themes()[ $s['color'] ] ) ? $s['color'] : 'gold',
		'items'  => ( isset( $s['items'] ) && is_array( $s['items'] ) ) ? array_values( $s['items'] ) : array(),
	);
}

/** Icon markup for one item: an image, or a Tabler glyph on a tinted tile. */
function horsetools_services_icon( $item, $accent ) {
	$img = isset( $item['img'] ) ? trim( (string) $item['img'] ) : '';
	if ( '' !== $img ) {
		return '<span class="ht-svc-ic ht-svc-img" style="background-image:url(' . esc_url( $img ) . ')"></span>';
	}
	$icon = isset( $item['icon'] ) ? preg_replace( '/[^a-z0-9-]/', '', strtolower( (string) $item['icon'] ) ) : '';
	if ( '' === $icon ) {
		$icon = 'point';
	}
	return '<span class="ht-svc-ic" style="background:' . esc_attr( $accent ) . '1f"><i class="ti ti-' . esc_attr( $icon ) . '" style="color:' . esc_attr( $accent ) . '" aria-hidden="true"></i></span>';
}

/** Badge markup, or ''. */
function horsetools_services_badge( $item ) {
	$text = isset( $item['badge'] ) ? trim( (string) $item['badge'] ) : '';
	if ( '' === $text ) {
		return '';
	}
	$presets = horsetools_services_badges();
	$key     = isset( $item['badge_color'] ) ? (string) $item['badge_color'] : 'red';
	if ( isset( $presets[ $key ] ) ) {
		$bg = $presets[ $key ][0];
		$fg = $presets[ $key ][1];
	} elseif ( preg_match( '/^#[0-9a-f]{3,6}$/i', $key ) ) {
		$bg = $key;
		$fg = '#ffffff';
	} else {
		$bg = $presets['red'][0];
		$fg = $presets['red'][1];
	}
	return '<span class="ht-svc-badge" style="background:' . esc_attr( $bg ) . ';color:' . esc_attr( $fg ) . '">' . esc_html( $text ) . '</span>';
}

/** Safe href for an item. */
function horsetools_services_href( $item ) {
	$link = isset( $item['link'] ) ? trim( (string) $item['link'] ) : '';
	return '' !== $link ? esc_url( $link ) : '#';
}

/**
 * Render the whole panel for the front end.
 *
 * @return string Panel HTML, or '' when off / empty.
 */
function horsetools_services_render() {
	$cfg = horsetools_services_get();
	if ( ! $cfg['on'] || empty( $cfg['items'] ) ) {
		return '';
	}
	$accent = horsetools_services_themes()[ $cfg['color'] ];
	$items  = $cfg['items'];

	$body = horsetools_services_layout_body( $cfg['layout'], $items, $accent );

	$out  = '<div class="ht-svc-wrap ht-svc-theme-' . esc_attr( $cfg['color'] ) . '" id="ht-services-panel" style="display:none" data-svc>';
	$out .= '<div class="ht-svc-backdrop" data-svc-close></div>';
	$out .= '<div class="ht-svc-sheet ht-svc-l-' . esc_attr( $cfg['layout'] ) . '" role="dialog" aria-modal="true" aria-label="' . esc_attr( $cfg['title'] ) . '" style="--svc-accent:' . esc_attr( $accent ) . '">';
	$out .= '<button class="ht-svc-handle" type="button" data-svc-close aria-label="' . esc_attr__( 'Close', 'horse-tools' ) . '"></button>';
	$out .= '<div class="ht-svc-head"><span class="ht-svc-title">' . esc_html( $cfg['title'] ) . '</span>';
	$out .= '<button class="ht-svc-x" type="button" data-svc-close aria-label="' . esc_attr__( 'Close', 'horse-tools' ) . '"><i class="ti ti-x" aria-hidden="true"></i></button></div>';
	$out .= '<div class="ht-svc-body">' . $body . '</div>';
	$out .= '</div></div>';
	return $out;
}

/**
 * Build the items markup for one layout.
 *
 * @param string $layout
 * @param array  $items
 * @param string $accent
 * @return string
 */
function horsetools_services_layout_body( $layout, $items, $accent ) {
	$card = function ( $item, $extra_class = '' ) use ( $accent ) {
		$sub = isset( $item['sub'] ) && '' !== trim( (string) $item['sub'] )
			? '<span class="ht-svc-sub">' . esc_html( $item['sub'] ) . '</span>' : '';
		return '<a class="ht-svc-item ' . esc_attr( $extra_class ) . '" href="' . horsetools_services_href( $item ) . '" rel="nofollow">'
			. horsetools_services_badge( $item )
			. horsetools_services_icon( $item, $accent )
			. '<span class="ht-svc-txt"><span class="ht-svc-tt">' . esc_html( isset( $item['title'] ) ? $item['title'] : '' ) . '</span>'
			. $sub . '</span></a>';
	};

	switch ( $layout ) {
		case 'coupon':
			$out = '';
			foreach ( $items as $it ) {
				$code = isset( $it['sub'] ) ? trim( (string) $it['sub'] ) : '';
				$out .= '<div class="ht-svc-coupon">' . horsetools_services_icon( $it, $accent )
					. '<div class="ht-svc-cp-mid"><span class="ht-svc-tt">' . esc_html( isset( $it['title'] ) ? $it['title'] : '' ) . '</span>'
					. ( '' !== $code ? '<span class="ht-svc-code">' . esc_html( $code ) . '</span>' : '' ) . '</div>'
					. ( '' !== $code ? '<button type="button" class="ht-svc-copy" data-code="' . esc_attr( $code ) . '"><i class="ti ti-copy" aria-hidden="true"></i></button>' : '' )
					. '</div>';
			}
			return $out;

		case 'chips':
			$out = '<div class="ht-svc-chips">';
			foreach ( $items as $it ) {
				$icon = isset( $it['icon'] ) ? preg_replace( '/[^a-z0-9-]/', '', strtolower( (string) $it['icon'] ) ) : '';
				$out .= '<a class="ht-svc-chip" href="' . horsetools_services_href( $it ) . '" rel="nofollow">'
					. ( '' !== $icon ? '<i class="ti ti-' . esc_attr( $icon ) . '" aria-hidden="true"></i> ' : '' )
					. esc_html( isset( $it['title'] ) ? $it['title'] : '' ) . '</a>';
			}
			return $out . '</div>';

		case 'story':
			$out = '<div class="ht-svc-story">';
			foreach ( $items as $it ) {
				$out .= $card( $it, 'ht-svc-story-i' );
			}
			return $out . '</div>';

		case 'list':
		case 'price':
			$out = '';
			foreach ( $items as $it ) {
				$out .= $card( $it, 'ht-svc-row' );
			}
			return $out;

		case 'tiles':
			$out = '<div class="ht-svc-tiles">';
			foreach ( $items as $it ) {
				$out .= $card( $it, 'ht-svc-tile' );
			}
			return $out . '</div>';

		case 'bento':
			$out   = '<div class="ht-svc-bento">';
			$first = true;
			foreach ( $items as $it ) {
				$out  .= $card( $it, $first ? 'ht-svc-big' : 'ht-svc-cell' );
				$first = false;
			}
			return $out . '</div>';

		case 'grid':
		default:
			$out = '<div class="ht-svc-grid">';
			foreach ( $items as $it ) {
				$out .= $card( $it, 'ht-svc-cell' );
			}
			return $out . '</div>';
	}
}
