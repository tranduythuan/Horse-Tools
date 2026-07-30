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

/** Layout slugs that have a distinct renderer (more added over time). */
function horsetools_services_layouts() {
	return array( 'bento', 'grid', 'list', 'tiles', 'chips', 'story', 'coupon', 'stacked', 'banner', 'pricecards', 'reviews', 'video', 'masonry' );
}

/** Presentation modes (how the panel appears). auto = sheet on phones, modal on desktop. */
function horsetools_services_modes() {
	return array( 'auto', 'sheet', 'modal', 'drawer-right', 'drawer-left', 'corner', 'fullscreen' );
}

/** The stored config, normalised. */
function horsetools_services_get() {
	$s = get_option( 'horsetools_services', array() );
	$s = is_array( $s ) ? $s : array();
	return array(
		'on'            => ! empty( $s['on'] ),
		'title'         => isset( $s['title'] ) && '' !== $s['title'] ? $s['title'] : __( 'Services', 'horse-tools' ),
		'layout'        => isset( $s['layout'] ) && in_array( $s['layout'], horsetools_services_layouts(), true ) ? $s['layout'] : 'bento',
		'color'         => isset( $s['color'] ) && isset( horsetools_services_themes()[ $s['color'] ] ) ? $s['color'] : 'gold',
		'display'       => isset( $s['display'] ) && in_array( $s['display'], horsetools_services_modes(), true ) ? $s['display'] : 'auto',
		'launcher'      => ! empty( $s['launcher'] ),
		'launcher_text' => isset( $s['launcher_text'] ) ? $s['launcher_text'] : '',
		'launcher_icon' => isset( $s['launcher_icon'] ) && '' !== $s['launcher_icon'] ? preg_replace( '/[^a-z0-9-]/', '', strtolower( $s['launcher_icon'] ) ) : 'apps',
		'items'         => ( isset( $s['items'] ) && is_array( $s['items'] ) ) ? array_values( $s['items'] ) : array(),
	);
}

/**
 * Floating desktop launcher button that opens the panel. Hidden on phones by
 * CSS (there the bottom bar is the trigger).
 *
 * @return string
 */
function horsetools_services_launcher() {
	$cfg = horsetools_services_get();
	if ( ! $cfg['on'] || empty( $cfg['items'] ) || ! $cfg['launcher'] ) {
		return '';
	}
	$accent = horsetools_services_themes()[ $cfg['color'] ];
	$text   = trim( (string) $cfg['launcher_text'] );
	return '<button type="button" class="ht-svc-launch" style="background:' . esc_attr( $accent ) . '" aria-label="' . esc_attr( '' !== $text ? $text : $cfg['title'] ) . '">'
		. '<i class="ti ti-' . esc_attr( $cfg['launcher_icon'] ) . '" aria-hidden="true"></i>'
		. ( '' !== $text ? '<span>' . esc_html( $text ) . '</span>' : '' ) . '</button>';
}

/** Output the panel (and launcher) in the footer, once, when enabled. */
function horsetools_services_footer() {
	$cfg = horsetools_services_get();
	if ( ! $cfg['on'] || empty( $cfg['items'] ) ) {
		return;
	}
	echo horsetools_services_render();   // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside
	echo horsetools_services_launcher(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside
}
add_action( 'wp_footer', 'horsetools_services_footer', 60 );

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

	$out  = '<div class="ht-svc-wrap ht-svc-theme-' . esc_attr( $cfg['color'] ) . '" id="ht-services-panel" style="display:none" data-svc data-mode="' . esc_attr( $cfg['display'] ) . '">';
	$out .= '<div class="ht-svc-backdrop" data-svc-close></div>';
	$out .= '<div class="ht-svc-sheet ht-svc-l-' . esc_attr( $cfg['layout'] ) . '" role="dialog" aria-modal="true" aria-label="' . esc_attr( $cfg['title'] ) . '" style="--svc-accent:' . esc_attr( $accent ) . '">';
	$out .= '<button class="ht-svc-handle" type="button" data-svc-close aria-label="' . esc_attr__( 'Close', 'horse-tools' ) . '"></button>';
	$out .= '<div class="ht-svc-head"><div class="ht-svc-htxt"><span class="ht-svc-title">' . esc_html( $cfg['title'] ) . '</span>';
	// Business-hours status. On phones the floating chat widget is hidden in
	// favour of the bottom bar, so this panel header is where mobile visitors
	// see whether the shop is open. Only shown when a schedule is configured.
	global $horsetools_options;
	if ( isset( $horsetools_options['chat-bh'] ) && function_exists( 'horsetools_chat_is_open' ) ) {
		$svc_open = horsetools_chat_is_open();
		$out .= '<span class="ht-svc-status ' . ( $svc_open ? 'ht-svc-open' : 'ht-svc-closed' ) . '">● '
			. ( $svc_open ? esc_html__( 'Online now', 'horse-tools' ) : esc_html__( 'Away — leave a message', 'horse-tools' ) )
			. '</span>';
	}
	$out .= '</div>';
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

		case 'stacked':
			$out = '<div class="ht-svc-stacked">';
			foreach ( $items as $it ) {
				$out .= $card( $it, 'ht-svc-stk' );
			}
			return $out . '</div>';

		case 'banner':
			$out   = '<div class="ht-svc-grid">';
			$first = true;
			foreach ( $items as $it ) {
				$out  .= $card( $it, $first ? 'ht-svc-bnr' : 'ht-svc-cell' );
				$first = false;
			}
			return $out . '</div>';

		case 'pricecards':
			$out = '';
			foreach ( $items as $it ) {
				$sub = ( isset( $it['sub'] ) && '' !== trim( (string) $it['sub'] ) ) ? '<span class="ht-svc-sub">' . esc_html( $it['sub'] ) . '</span>' : '';
				$out .= '<a class="ht-svc-item ht-svc-priced" href="' . horsetools_services_href( $it ) . '" rel="nofollow">'
					. horsetools_services_icon( $it, $accent )
					. '<span class="ht-svc-txt"><span class="ht-svc-tt">' . esc_html( isset( $it['title'] ) ? $it['title'] : '' ) . '</span>' . $sub . '</span>'
					. '<span class="ht-svc-go">' . esc_html__( 'Order', 'horse-tools' ) . '</span></a>';
			}
			return $out;

		case 'reviews':
			$out = '';
			foreach ( $items as $it ) {
				$name  = trim( (string) ( isset( $it['title'] ) ? $it['title'] : '' ) );
				$quote = isset( $it['sub'] ) ? trim( (string) $it['sub'] ) : '';
				$init  = '' !== $name ? mb_strtoupper( mb_substr( $name, 0, 1 ) ) : '★';
				$out  .= '<div class="ht-svc-review"><div class="ht-svc-rv-top">'
					. '<span class="ht-svc-avatar" style="background:' . esc_attr( $accent ) . '22;color:' . esc_attr( $accent ) . '">' . esc_html( $init ) . '</span>'
					. '<div><span class="ht-svc-tt">' . esc_html( $name ) . '</span><span class="ht-svc-stars">★★★★★</span></div></div>'
					. ( '' !== $quote ? '<p class="ht-svc-quote">' . esc_html( $quote ) . '</p>' : '' ) . '</div>';
			}
			return $out;

		case 'video':
			$out = '<div class="ht-svc-videos">';
			foreach ( $items as $it ) {
				$img = isset( $it['img'] ) ? trim( (string) $it['img'] ) : '';
				$bg  = '' !== $img ? ' style="background-image:url(' . esc_url( $img ) . ')"' : ' style="background:' . esc_attr( $accent ) . '"';
				$dur = ( isset( $it['sub'] ) && '' !== trim( (string) $it['sub'] ) ) ? '<span class="ht-svc-dur">' . esc_html( $it['sub'] ) . '</span>' : '';
				$out .= '<a class="ht-svc-video" href="' . horsetools_services_href( $it ) . '" rel="nofollow">'
					. '<span class="ht-svc-thumb"' . $bg . '><span class="ht-svc-play"><i class="ti ti-player-play" aria-hidden="true"></i></span>' . $dur . '</span>'
					. '<span class="ht-svc-tt">' . esc_html( isset( $it['title'] ) ? $it['title'] : '' ) . '</span></a>';
			}
			return $out . '</div>';

		case 'masonry':
			$out = '<div class="ht-svc-masonry">';
			$i   = 0;
			foreach ( $items as $it ) {
				$img  = isset( $it['img'] ) ? trim( (string) $it['img'] ) : '';
				$bg   = '' !== $img ? 'background-image:url(' . esc_url( $img ) . ')' : 'background:' . esc_attr( $accent );
				$tall = ( 0 === $i % 3 ) ? ' ht-svc-tall' : '';
				$out .= '<a class="ht-svc-mas' . $tall . '" href="' . horsetools_services_href( $it ) . '" rel="nofollow" style="' . esc_attr( $bg ) . '"><span class="ht-svc-mas-tt">' . esc_html( isset( $it['title'] ) ? $it['title'] : '' ) . '</span></a>';
				$i++;
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

/* ---- Admin: save the Services config ----------------------------------- */
add_action( 'wp_ajax_horsetools_services_save', 'horsetools_services_save_ajax' );
function horsetools_services_save_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'msg' => __( 'Permission denied.', 'horse-tools' ) ) );
	}
	check_ajax_referer( 'horsetools_services', 'nonce' );
	$raw  = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '';
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		wp_send_json_error( array( 'msg' => __( 'Could not read the data.', 'horse-tools' ) ) );
	}
	$clean = array(
		'on'            => ! empty( $data['on'] ) ? 1 : 0,
		'title'         => sanitize_text_field( isset( $data['title'] ) ? $data['title'] : '' ),
		'layout'        => in_array( isset( $data['layout'] ) ? $data['layout'] : '', horsetools_services_layouts(), true ) ? $data['layout'] : 'bento',
		'color'         => isset( horsetools_services_themes()[ isset( $data['color'] ) ? $data['color'] : '' ] ) ? $data['color'] : 'gold',
		'display'       => in_array( isset( $data['display'] ) ? $data['display'] : '', horsetools_services_modes(), true ) ? $data['display'] : 'auto',
		'launcher'      => ! empty( $data['launcher'] ) ? 1 : 0,
		'launcher_text' => sanitize_text_field( isset( $data['launcher_text'] ) ? $data['launcher_text'] : '' ),
		'launcher_icon' => preg_replace( '/[^a-z0-9-]/', '', strtolower( isset( $data['launcher_icon'] ) ? $data['launcher_icon'] : '' ) ),
		'items'         => array(),
	);
	foreach ( (array) ( isset( $data['items'] ) ? $data['items'] : array() ) as $it ) {
		if ( ! is_array( $it ) ) {
			continue;
		}
		$title = sanitize_text_field( isset( $it['title'] ) ? $it['title'] : '' );
		$link  = esc_url_raw( isset( $it['link'] ) ? $it['link'] : '' );
		$img   = esc_url_raw( isset( $it['img'] ) ? $it['img'] : '' );
		if ( '' === $title && '' === $link && '' === $img ) {
			continue; // skip blank rows
		}
		$clean['items'][] = array(
			'icon'        => preg_replace( '/[^a-z0-9-]/', '', strtolower( isset( $it['icon'] ) ? $it['icon'] : '' ) ),
			'img'         => $img,
			'title'       => $title,
			'sub'         => sanitize_text_field( isset( $it['sub'] ) ? $it['sub'] : '' ),
			'link'        => $link,
			'badge'       => sanitize_text_field( isset( $it['badge'] ) ? $it['badge'] : '' ),
			'badge_color' => sanitize_text_field( isset( $it['badge_color'] ) ? $it['badge_color'] : 'red' ),
		);
	}
	update_option( 'horsetools_services', $clean, false );
	wp_send_json_success( array( 'items' => count( $clean['items'] ) ) );
}
