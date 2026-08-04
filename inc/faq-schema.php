<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Automatic FAQPage schema.
 *
 * Reads the "frequently asked questions" section a post already has and emits
 * the matching JSON-LD, so hundreds of existing articles become eligible for
 * FAQ rich results without touching a single one of them.
 *
 * The parser is deliberately forgiving about how that section is written — the
 * first version of this (as a hand-written snippet) only understood one exact
 * shape, and quietly produced nothing for everything else. It now walks the
 * document recursively, so a FAQ nested inside a builder's <div> is found; it
 * accepts any heading level below the section heading, plus <summary> for
 * accordions; it keeps text inside shortcodes; and it matches the section by a
 * list of phrases the site owner can extend.
 *
 * Results are cached in post meta against the post's modified time, so a page
 * view parses nothing once the post has been seen.
 */

/**
 * Bump when the extraction rules or the stand-aside rules change, so cached
 * results from the old rules are recomputed rather than served for ever.
 */
define( 'HORSETOOLS_FAQ_RULES', 2 );

/** The phrases that mark a FAQ section, as one case-insensitive regex. */
function horsetools_faq_keys() {
	$raw = trim( (string) horsetools_opt( 'main', 'faq-keys', '' ) );
	if ( '' === $raw ) {
		$raw = 'thường gặp, hỏi đáp, giải đáp, thắc mắc, FAQ, Q&A';
	}
	$parts = array();
	foreach ( preg_split( '~[,\n]+~u', $raw ) as $p ) {
		$p = trim( $p );
		if ( '' === $p ) {
			continue;
		}
		// Let "hỏi đáp" also match "hỏi & đáp" / "hỏi &amp; đáp" / "hỏi-đáp",
		// and "Q&A" match "Q&amp;A" — the same heading, written differently.
		// preg_quote leaves spaces and ampersands alone, so both are replaced
		// on the quoted string directly (ampersand first, or the pattern the
		// space rule inserts would be rewritten again).
		$q       = preg_quote( $p, '~' );
		$q       = str_replace( '&', '(?:&amp;|&)', $q );
		$q       = str_replace( ' ', '\s*(?:&amp;|[-&–])?\s*', $q );
		$parts[] = $q;
	}
	if ( ! $parts ) {
		return '';
	}
	return '~' . implode( '|', $parts ) . '~iu';
}

/**
 * Pull question/answer pairs out of post content.
 *
 * @param string $html Post content (raw is fine).
 * @param string $keys Regex from horsetools_faq_keys().
 * @return array List of array( question, answer ).
 */
function horsetools_faq_extract( $html, $keys ) {
	if ( '' === $keys || ! class_exists( 'DOMDocument' ) ) {
		return array();
	}
	// Drop shortcode tags but keep what is inside them, so a FAQ written inside
	// [accordion]…[/accordion] still reads.
	$html = preg_replace( '~\[/?[^\]\[]{1,80}\]~u', '', (string) $html );
	if ( ! preg_match( $keys, $html ) ) {
		return array();
	}

	$prev = libxml_use_internal_errors( true );
	$doc  = new DOMDocument();
	$doc->loadHTML(
		'<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . wpautop( $html ),
		LIBXML_NOERROR | LIBXML_NOWARNING
	);
	libxml_clear_errors();
	libxml_use_internal_errors( $prev );

	$body = $doc->getElementsByTagName( 'body' )->item( 0 );
	if ( ! $body ) {
		return array();
	}

	// Flatten the document to a reading-order list of headings and text blocks.
	// Recursion is the point: a heading three <div>s deep still lands here.
	$flat = array();
	// Skipping <script> while walking is not enough: wpautop can leave one
	// inside the very paragraph that answers a question, and textContent
	// happily returns its source. A page with a hand-pasted JSON-LD block after
	// the last answer would otherwise publish that block's source code as the
	// answer's text. Nothing here is text a reader ever sees, so remove it.
	$txt = function ( $n ) {
		$clone = $n->cloneNode( true );
		foreach ( array( 'script', 'style', 'noscript', 'template' ) as $tag ) {
			$drop = array();
			foreach ( $clone->getElementsByTagName( $tag ) as $el ) {
				$drop[] = $el;
			}
			foreach ( $drop as $el ) {
				$el->parentNode->removeChild( $el );
			}
		}
		return trim( preg_replace( '~\s+~u', ' ', $clone->textContent ) );
	};
	$walk = function ( $node ) use ( &$walk, &$flat, $txt ) {
		foreach ( $node->childNodes as $c ) {
			if ( XML_ELEMENT_NODE !== $c->nodeType ) {
				continue;
			}
			$tag = strtolower( $c->nodeName );
			if ( preg_match( '~^h([1-6])$~', $tag, $m ) ) {
				$flat[] = array( 'h', (int) $m[1], $txt( $c ) );
				continue;
			}
			if ( 'summary' === $tag ) { // the question of an accordion
				$flat[] = array( 'h', 3, $txt( $c ) );
				continue;
			}
			if ( in_array( $tag, array( 'p', 'li', 'blockquote', 'dd', 'figcaption' ), true ) ) {
				$flat[] = array( 't', 0, $txt( $c ) );
				continue;
			}
			if ( in_array( $tag, array( 'script', 'style', 'nav', 'form' ), true ) ) {
				continue;
			}
			$walk( $c );
		}
	};
	$walk( $body );

	$pairs = array();
	$lvl   = 0;
	$in    = false;
	$q     = '';
	$a     = '';
	$flush = function () use ( &$pairs, &$q, &$a ) {
		if ( '' !== $q && '' !== $a ) {
			$pairs[] = array( $q, $a );
		}
		$q = '';
		$a = '';
	};
	foreach ( $flat as $item ) {
		list( $kind, $level, $text ) = $item;
		if ( 'h' === $kind ) {
			if ( ! $in ) {
				if ( preg_match( $keys, $text ) ) {
					$in  = true;
					$lvl = $level;
				}
				continue;
			}
			// A heading at the same level or higher means the section ended.
			if ( $level <= $lvl ) {
				$flush();
				break;
			}
			$flush();
			$q = $text;
			continue;
		}
		if ( $in && '' !== $q && '' !== $text ) {
			$a = ( '' === $a ) ? $text : $a . ' ' . $text;
		}
	}
	$flush();
	return $pairs;
}

/** Post types the feature applies to. */
function horsetools_faq_types() {
	$types = array( 'post' );
	if ( horsetools_opt( 'main', 'faq-pages', '' ) ) {
		$types[] = 'page';
	}
	return $types;
}

/** Categories it is limited to, empty meaning all. */
function horsetools_faq_cats() {
	$raw = trim( (string) horsetools_opt( 'main', 'faq-cats', '' ) );
	if ( '' === $raw ) {
		return array();
	}
	return array_filter( array_map( 'absint', preg_split( '~[,\s]+~', $raw ) ) );
}

function horsetools_faq_min() {
	$n = (int) horsetools_opt( 'main', 'faq-min', 2 );
	return max( 1, min( 20, $n ? $n : 2 ) );
}

function horsetools_faq_maxlen() {
	$n = (int) horsetools_opt( 'main', 'faq-maxlen', 500 );
	return max( 80, min( 5000, $n ? $n : 500 ) );
}

/**
 * Does another plugin already publish FAQPage schema for this post? Rank Math,
 * Yoast and friends can, and two FAQPage blocks on one URL is worse than none.
 */
function horsetools_faq_foreign( $post_id ) {
	// Rank Math stores its schema as post meta rows named rank_math_schema_*.
	foreach ( get_post_meta( $post_id ) as $key => $vals ) {
		if ( 0 !== strpos( $key, 'rank_math_schema' ) ) {
			continue;
		}
		foreach ( (array) $vals as $v ) {
			if ( false !== stripos( (string) $v, 'FAQPage' ) ) {
				return true;
			}
		}
	}
	// Yoast's FAQ block leaves its own marker in the content.
	$content = (string) get_post_field( 'post_content', $post_id );
	if ( false !== strpos( $content, 'wp:yoast/faq-block' ) ) {
		return true;
	}
	// An FAQ block written by hand and pasted into the post — the way this was
	// done before any plugin offered it. It is easy to miss because nothing
	// records that it is there: it is just a <script> in the middle of the
	// article, and the post looks ordinary in the editor. Publishing a second
	// FAQPage alongside it puts two conflicting blocks on one URL.
	//
	// Only a real script counts. A post explaining schema markup shows its
	// examples escaped (&lt;script&gt;), so the word alone must not silence us.
	if ( false !== stripos( $content, 'FAQPage' )
		&& preg_match( '~<script[^>]*ld\+json[^>]*>(?:(?!</script>)[\s\S])*?FAQPage~i', $content ) ) {
		return true;
	}
	/**
	 * Whether some other source already publishes FAQ schema for this post.
	 *
	 * Lets a site suppress ours for an SEO plugin we do not recognise.
	 *
	 * @param bool $foreign
	 * @param int  $post_id
	 */
	return (bool) apply_filters( 'horsetools_faq_foreign', false, $post_id );
}

/**
 * The JSON-LD for one post, computed at most once per edit.
 *
 * @param int  $post_id
 * @param bool $force Recompute even when the cache looks current.
 * @return string JSON, or '' when this post gets no schema.
 */
function horsetools_faq_json( $post_id, $force = false ) {
	$post_id = (int) $post_id;
	// The stamp carries the rule version as well as the edit time, so that
	// changing how questions are found or when we stand aside re-computes every
	// post by itself. Keying on the edit time alone meant a fixed rule kept
	// serving the old answer on posts nobody happened to edit again.
	$stamp   = get_post_modified_time( 'U', true, $post_id ) . '#' . HORSETOOLS_FAQ_RULES;
	$cached  = get_post_meta( $post_id, '_ht_faq_ld', true );
	$seen    = (string) get_post_meta( $post_id, '_ht_faq_stamp', true );

	if ( ! $force && is_string( $cached ) && $seen === $stamp ) {
		return $cached;
	}

	$json  = '';
	$pairs = array();
	if ( ! horsetools_faq_foreign( $post_id ) ) {
		$pairs = horsetools_faq_extract( (string) get_post_field( 'post_content', $post_id ), horsetools_faq_keys() );
	}

	if ( count( $pairs ) >= horsetools_faq_min() ) {
		$max   = horsetools_faq_maxlen();
		$items = array();
		foreach ( $pairs as $pair ) {
			$answer = $pair[1];
			if ( function_exists( 'mb_strlen' ) && mb_strlen( $answer ) > $max ) {
				$answer = rtrim( mb_substr( $answer, 0, $max ) ) . '…';
			}
			$items[] = array(
				'@type'          => 'Question',
				'name'           => $pair[0],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer,
				),
			);
		}
		// JSON_UNESCAPED_SLASHES is deliberately NOT set: with "/" escaped, a
		// stray </script> inside the content cannot break out of the block.
		$json = wp_json_encode(
			array(
				'@context'         => 'https://schema.org',
				'@type'            => 'FAQPage',
				'mainEntityOfPage' => get_permalink( $post_id ),
				'mainEntity'       => $items,
			),
			JSON_UNESCAPED_UNICODE
		);
	}

	update_post_meta( $post_id, '_ht_faq_ld', $json );
	update_post_meta( $post_id, '_ht_faq_stamp', $stamp );
	return $json;
}

/** Is this post in scope (type + category)? */
function horsetools_faq_in_scope( $post_id ) {
	if ( ! in_array( get_post_type( $post_id ), horsetools_faq_types(), true ) ) {
		return false;
	}
	$cats = horsetools_faq_cats();
	if ( ! $cats ) {
		return true;
	}
	$has = wp_get_post_categories( $post_id );
	return (bool) array_intersect( $cats, (array) $has );
}

function horsetools_faq_head() {
	if ( ! horsetools_opt( 'main', 'faq-schema1', '' ) || ! is_singular( horsetools_faq_types() ) ) {
		return;
	}
	$id = get_the_ID();
	if ( ! $id || ! horsetools_faq_in_scope( $id ) ) {
		return;
	}
	$json = horsetools_faq_json( $id );
	if ( '' === $json ) {
		return;
	}
	echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- JSON, slashes escaped
}
add_action( 'wp_head', 'horsetools_faq_head', 20 );

/** Editing a post invalidates its cache immediately. */
function horsetools_faq_clear( $post_id ) {
	delete_post_meta( $post_id, '_ht_faq_stamp' );
}
add_action( 'save_post', 'horsetools_faq_clear' );

/* -------------------------------------------------------------------------
 * Site-wide scan — the part a hand-written snippet cannot give you: which
 * posts will get schema, and which look like they should but do not.
 * ---------------------------------------------------------------------- */
function horsetools_faq_scan_ajax() {
	check_ajax_referer( 'horsetools_faq', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}

	$keys = horsetools_faq_keys();
	$min  = horsetools_faq_min();
	$ids  = get_posts( array(
		'post_type'        => horsetools_faq_types(),
		'post_status'      => 'publish',
		'numberposts'      => 2000,
		'fields'           => 'ids',
		'suppress_filters' => true,
	) );

	$ok = 0; $few = array(); $none = array(); $skip = 0; $foreign = 0; $q_total = 0;
	foreach ( $ids as $id ) {
		if ( ! horsetools_faq_in_scope( $id ) ) {
			$skip++;
			continue;
		}
		if ( horsetools_faq_foreign( $id ) ) {
			$foreign++;
			continue;
		}
		$content = (string) get_post_field( 'post_content', $id );
		$looks   = preg_match( $keys, preg_replace( '~\[/?[^\]\[]{1,80}\]~u', '', $content ) );
		$pairs   = $looks ? horsetools_faq_extract( $content, $keys ) : array();
		$n       = count( $pairs );

		if ( $n >= $min ) {
			$ok++;
			$q_total += $n;
		} elseif ( $n > 0 ) {
			$few[] = array( 'id' => $id, 'n' => $n, 'title' => get_the_title( $id ), 'link' => get_edit_post_link( $id, 'raw' ) );
		} elseif ( $looks ) {
			$none[] = array( 'id' => $id, 'n' => 0, 'title' => get_the_title( $id ), 'link' => get_edit_post_link( $id, 'raw' ) );
		} else {
			$skip++;
		}
	}

	wp_send_json_success( array(
		'ok'      => $ok,
		'avg'     => $ok ? round( $q_total / $ok, 1 ) : 0,
		'few'     => array_slice( $few, 0, 100 ),
		'none'    => array_slice( $none, 0, 100 ),
		'fewN'    => count( $few ),
		'noneN'   => count( $none ),
		'skip'    => $skip,
		'foreign' => $foreign,
		'total'   => count( $ids ),
	) );
}
add_action( 'wp_ajax_horsetools_faq_scan', 'horsetools_faq_scan_ajax' );

/** Forget every cached result, so the next view of each post re-reads it. */
function horsetools_faq_flush_ajax() {
	check_ajax_referer( 'horsetools_faq', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	global $wpdb;
	$n = $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('_ht_faq_ld','_ht_faq_stamp')" ); // phpcs:ignore WordPress.DB
	wp_send_json_success( array( 'rows' => (int) $n ) );
}
add_action( 'wp_ajax_horsetools_faq_flush', 'horsetools_faq_flush_ajax' );

/* -------------------------------------------------------------------------
 * Editor hint: how many questions this post will publish, while writing it.
 * ---------------------------------------------------------------------- */
function horsetools_faq_meta_box() {
	if ( ! horsetools_opt( 'main', 'faq-schema1', '' ) ) {
		return;
	}
	add_meta_box(
		'horsetools-faq',
		__( 'FAQ schema (Horse Tools)', 'horse-tools' ),
		'horsetools_faq_meta_render',
		horsetools_faq_types(),
		'side',
		'low'
	);
}
add_action( 'add_meta_boxes', 'horsetools_faq_meta_box' );

function horsetools_faq_meta_render( $post ) {
	if ( horsetools_faq_foreign( $post->ID ) ) {
		echo '<p>' . esc_html__( 'Another plugin already publishes FAQ schema for this post, so Horse Tools is staying out of the way.', 'horse-tools' ) . '</p>';
		return;
	}
	$pairs = horsetools_faq_extract( (string) $post->post_content, horsetools_faq_keys() );
	$n     = count( $pairs );
	$min   = horsetools_faq_min();
	if ( $n >= $min ) {
		echo '<p><strong>' . esc_html( sprintf( _n( '%d question found', '%d questions found', $n, 'horse-tools' ), $n ) ) . '</strong></p><ol style="margin:0 0 0 18px;padding:0">';
		foreach ( array_slice( $pairs, 0, 8 ) as $p ) {
			echo '<li style="margin:0 0 4px">' . esc_html( $p[0] ) . '</li>';
		}
		echo '</ol>';
	} elseif ( $n > 0 ) {
		echo '<p>' . esc_html( sprintf(
			/* translators: 1: found, 2: minimum */
			__( 'Only %1$d question found; at least %2$d are needed before schema is published.', 'horse-tools' ),
			$n,
			$min
		) ) . '</p>';
	} else {
		echo '<p>' . esc_html__( 'No FAQ section recognised in this post.', 'horse-tools' ) . '</p>';
	}
}
