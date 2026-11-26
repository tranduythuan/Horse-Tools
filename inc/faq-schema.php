<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Automatic FAQPage schema.
 *
 * Reads the "frequently asked questions" section a post already has and emits
 * the matching JSON-LD, so hundreds of existing articles become eligible for
 * FAQ rich results without touching a single one of them.
 *
 * Results are cached in post meta against the post's modified time, rule
 * version, and active FAQ settings fingerprint, so a page view parses nothing
 * once the post has been seen.
 */

/**
 * Bump when the extraction rules or the stand-aside rules change, so cached
 * results from the old rules are recomputed rather than served for ever.
 */
define( 'HORSETOOLS_FAQ_RULES', 5 );

/**
 * Fold a string for case- and accent-insensitive phrase matching.
 *
 * Decodes HTML entities and &nbsp;, normalizes NFC when ext/intl is available,
 * lowercases, strips combining marks (so NFD works even without intl), and
 * folds Vietnamese and common Latin diacritics deterministically.
 *
 * @param string $str Raw text.
 * @return string Folded lowercase string with single ASCII spaces.
 */
function horsetools_faq_fold( $str ) {
	$str = html_entity_decode( (string) $str, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	if ( class_exists( 'Normalizer' ) ) {
		$norm = Normalizer::normalize( $str, Normalizer::FORM_C );
		if ( is_string( $norm ) ) {
			$str = $norm;
		}
	}
	$str = function_exists( 'mb_strtolower' ) ? mb_strtolower( $str, 'UTF-8' ) : strtolower( $str );

	// Strip combining diacritical marks and invisible zero-width/soft-hyphen format characters.
	$tmp = preg_replace( '~[\x{0300}-\x{036F}\x{1AB0}-\x{1AFF}\x{1DC0}-\x{1DFF}\x{20D0}-\x{20FF}\x{FE20}-\x{FE2F}\x{200B}-\x{200F}\x{2060}\x{FEFF}\x{00AD}]+~u', '', $str );
	if ( is_string( $tmp ) ) {
		$str = $tmp;
	}

	static $map = array(
		'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
		'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
		'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
		'ä' => 'a', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'æ' => 'ae',
		'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c',
		'đ' => 'd', 'ď' => 'd', 'ð' => 'd',
		'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
		'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
		'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e',
		'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
		'î' => 'i', 'ï' => 'i', 'ī' => 'i', 'į' => 'i',
		'ł' => 'l', 'ń' => 'n', 'ñ' => 'n', 'ň' => 'n',
		'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
		'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
		'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
		'ö' => 'o', 'ø' => 'o', 'ō' => 'o', 'œ' => 'oe',
		'ř' => 'r', 'ś' => 's', 'š' => 's', 'ş' => 's', 'ß' => 'ss',
		'ť' => 't', 'ţ' => 't',
		'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
		'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
		'û' => 'u', 'ü' => 'u', 'ū' => 'u', 'ů' => 'u',
		'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y', 'ÿ' => 'y',
		'ź' => 'z', 'ż' => 'z', 'ž' => 'z',
	);
	$str = strtr( $str, $map );

	$ws = preg_replace( '~[\x{00A0}\s]+~u', ' ', $str );
	if ( is_string( $ws ) ) {
		$str = $ws;
	}
	return trim( $str );
}

/** The phrases that mark a FAQ section, as one folded case-insensitive regex. */
function horsetools_faq_keys() {
	$raw = trim( (string) horsetools_opt( 'main', 'faq-keys', '' ) );
	if ( '' === $raw ) {
		$raw = 'câu hỏi thường gặp, frequently asked questions, common questions, questions and answers, questions & answers, hỏi đáp, giải đáp thắc mắc, FAQ, Q&A, Q & A, Q/A, F.A.Q';
	}
	$parts = array();
	$split = preg_split( '~[,\n]+~', $raw );
	if ( ! is_array( $split ) ) {
		return '';
	}
	foreach ( $split as $p ) {
		if ( 1 !== @preg_match( '//u', $p ) ) {
			continue;
		}
		$p = horsetools_faq_fold( $p );
		if ( '' === $p || 1 !== @preg_match( '//u', $p ) ) {
			continue;
		}
		if ( 'faq' === $p || 'faqs' === $p ) {
			$parts[] = '(?<![a-z0-9])faqs?(?![a-z0-9])';
			continue;
		}
		if ( 'f.a.q' === $p || 'f.a.q.' === $p ) {
			$parts[] = '(?<![a-z0-9])f\.?\s*a\.?\s*q\.?(?![a-z0-9])';
			continue;
		}
		if ( preg_match( '~^q\s*(?:&|/)\s*a$~', $p ) ) {
			$parts[] = '(?<![a-z0-9])q\s*(?:&|/)\s*a(?![a-z0-9])';
			continue;
		}
		// Preserve word boundaries on ASCII-bounded phrases and tolerate "&", "-", "/"
		// between words without emitting consecutive \s* quantifiers (O(N^4) backtracking).
		$pre = preg_match( '~^[a-z0-9]~', $p ) ? '(?<![a-z0-9])' : '';
		$suf = preg_match( '~[a-z0-9]$~', $p ) ? '(?![a-z0-9])' : '';

		$q = preg_replace( '~\s*&\s*~', '__AMP__', $p );
		$q = preg_replace( '~\s*/\s*~', '__SLASH__', is_string( $q ) ? $q : $p );
		$q = preg_quote( is_string( $q ) ? $q : $p, '~' );
		$q = str_replace( ' ', '(?:\s++(?:(?:&|[-–/])\s*+)?|\s*+(?:&|[-–/])\s*+)', $q );
		$q = str_replace( array( '__AMP__', '__SLASH__' ), array( '(?:\s*+&\s*+|\s++(?:and|va)\s++)', '\s*+/\s*+' ), $q );
		$parts[] = $pre . $q . $suf;
	}
	if ( ! $parts ) {
		return '';
	}
	return '~' . implode( '|', array_unique( $parts ) ) . '~iu';
}

/** Test whether arbitrary text contains any configured FAQ phrase. */
function horsetools_faq_matches_keys( $text, $keys ) {
	if ( '' === $keys || ! is_string( $keys ) ) {
		return false;
	}
	$text = (string) $text;
	if ( '' === trim( $text ) ) {
		return false;
	}
	$folded = horsetools_faq_fold( $text );
	return ( '' !== $folded && 1 === @preg_match( $keys, $folded ) );
}

/**
 * Decide whether a heading text legitimately opens a FAQ section.
 *
 * Rejects common non-FAQ headings matched by bare custom keys like
 * "thường gặp" or "thắc mắc" (e.g. "Các lỗi thường gặp", "Bạn có thắc mắc?").
 */
function horsetools_faq_is_section_heading( $text, $keys ) {
	if ( ! horsetools_faq_matches_keys( $text, $keys ) ) {
		return false;
	}
	$folded = horsetools_faq_fold( $text );
	if ( '' === $folded ) {
		return false;
	}
	// Explicit FAQ markers always qualify.
	if ( preg_match( '~(?<![a-z0-9])(?:cau\s+hoi\s+thuong\s+gap|thac\s+mac\s+thuong\s+gap|faqs?|f\.?\s*a\.?\s*q\.?|frequently\s+asked\s+questions|common\s+questions|questions\s*(?:&|and)\s*answers|hoi\s*(?:&|/|-|–)?\s*dap|q\s*(?:&|/)\s*a|giai\s+dap\s+thac\s+mac)(?![a-z0-9])~i', $folded ) ) {
		return true;
	}
	// Guard bare "thường gặp" / "thắc mắc" / "giải đáp" against troubleshooting or CTA headings.
	if ( preg_match( '~\b(?:loi|sai\s+lam|su\s+co|van\s+de|benh|can\s+benh|trieu\s+chung|dau\s+hieu|nguyen\s+nhan|nham\s+lan|khuyet\s+diem|nhuoc\s+diem|han\s+che|rui\s+ro|thoi\s+quen|tinh\s+huong|truc\s+trac|hu\s+hong)(?:\s+(?:ma\s+)?(?:ban|ta|chung\s+ta|nguoi\s+(?:dung|choi|mua|hoc|benh)|khach\s+hang|moi\s+nguoi))?\s+thuong\s+gap\b~i', $folded ) ) {
		return false;
	}
	if ( preg_match( '~\b(?:ban\s+co\s+thac\s+mac|neu\s+(?:ban\s+)?co\s+thac\s+mac|moi\s+thac\s+mac|lien\s+he\s+(?:de\s+)?(?:giai\s+dap|thac\s+mac)|giai\s+dap\s+(?:bai\s+tap|de\s+thi|giac\s+mo|phong\s+thuy|tu\s+vi))\b~i', $folded ) ) {
		return false;
	}
	return true;
}

/** Identify closing/editorial/navigation subheadings that must not become FAQ Questions. */
function horsetools_faq_is_noise_heading( $text ) {
	$folded = horsetools_faq_fold( $text );
	if ( '' === $folded ) {
		return true;
	}
	$clean = preg_replace( '~[\s:.\-–!?]+$~u', '', $folded );
	$clean = is_string( $clean ) ? trim( $clean ) : $folded;
	if ( '' === $clean ) {
		return true;
	}
	return (bool) preg_match(
		'~^(?:loi\s+ket|ket\s+luan|tong\s+ket|tam\s+ket|lien\s+he(?:\s+(?:chung\s+toi|tu\s+van|ho\s+tro))?|thong\s+tin\s+lien\s+he|xem\s+them|doc\s+them|tai\s+lieu\s+tham\s+khao|nguon\s+tham\s+khao|bai\s+viet\s+lien\s+quan|tin\s+lien\s+quan|chu\s+de\s+lien\s+quan|luu\s+y(?:\s+quan\s+trong)?|ghi\s+chu|conclusion|summary|final\s+thoughts|contact(?:\s+us)?|see\s+also|read\s+more|related\s+(?:posts|articles)|references|note|disclaimer)$~i',
		$clean
	);
}

/** Identify inline callout/note labels (e.g. "Lưu ý:", "Note:") inside bold <p> answers. */
function horsetools_faq_is_note_heading( $text ) {
	$folded = horsetools_faq_fold( $text );
	$clean  = preg_replace( '~[\s:.\-–!?]+$~u', '', $folded );
	$clean  = is_string( $clean ) ? trim( $clean ) : $folded;
	return (bool) preg_match( '~^(?:luu\s+y(?:\s+quan\s+trong)?|ghi\s+chu|note|disclaimer)$~i', $clean );
}

/** Check whether a standalone bold line inside a FAQ section looks like a question. */
function horsetools_faq_looks_like_question( $text ) {
	$text = trim( (string) $text );
	if ( '' === $text || horsetools_faq_is_noise_heading( $text ) ) {
		return false;
	}
	if ( 1 === preg_match( '~[?？]\s*$~u', $text ) ) {
		return true;
	}
	$folded = horsetools_faq_fold( $text );
	return (bool) preg_match( '~^(?:q\s*\d*\s*[:.)\-]|question\s*\d*\s*[:.)\-]|hoi\s*\d*\s*[:.)\-]|cau\s*(?:hoi\s*)?\d+\s*[:.)\-]|cau\s+hoi\s*[:.\-])~i', $folded );
}

/**
 * Convert common accordion/toggle shortcodes into native <details><summary>
 * markup, and strip remaining WordPress shortcodes without harming prose
 * brackets like [1] or [section 2].
 */
function horsetools_faq_prepare_shortcodes( $html ) {
	$html = (string) $html;
	if ( false === strpos( $html, '[' ) ) {
		return $html;
	}

	// Expand paired accordion/toggle item shortcodes in O(N) linear time (supporting arbitrary nesting).
	if ( false !== strpos( $html, '[/' ) ) {
		$item_tags = 'ht-item|ht-toggle|ht-reveal|su_spoiler|vc_tta_section|toggle|accordion-item|sp_faq';
		if ( preg_match( '~\[/(?:' . $item_tags . ')\s*\]~i', $html ) ) {
			$tok_pat = '~\[/(' . $item_tags . ')\s*\]|\[(' . $item_tags . ')\b((?:[^\[\]"\']++|"[^"]*"|\'[^\']*\')*)\]~is';
			if ( preg_match_all( $tok_pat, $html, $tokens, PREG_OFFSET_CAPTURE | PREG_SET_ORDER ) && ! empty( $tokens ) ) {
				$stack      = array();
				$tag_counts = array();
				$pairs      = array();
				$close_set  = array();
				foreach ( $tokens as $idx => $tok ) {
					if ( ! empty( $tok[2][0] ) ) {
						$tag                = strtolower( $tok[2][0] );
						$stack[]            = array( $idx, $tag );
						$tag_counts[ $tag ] = isset( $tag_counts[ $tag ] ) ? $tag_counts[ $tag ] + 1 : 1;
					} elseif ( ! empty( $tok[1][0] ) ) {
						$tag = strtolower( $tok[1][0] );
						if ( empty( $tag_counts[ $tag ] ) ) {
							continue;
						}
						while ( ! empty( $stack ) ) {
							$top_entry = array_pop( $stack );
							$top_idx   = $top_entry[0];
							$top_tag   = $top_entry[1];
							$tag_counts[ $top_tag ]--;
							if ( $top_tag === $tag ) {
								$pairs[ $top_idx ] = $idx;
								$close_set[ $idx ] = true;
								break;
							}
						}
					}
				}

				if ( $pairs ) {
					$frames = array( array( 'buf' => '', 'attrs' => '', 'pos' => 0 ) );
					$len    = strlen( $html );
					foreach ( $tokens as $idx => $tok ) {
						if ( isset( $pairs[ $idx ] ) ) {
							$start = $tok[0][1];
							$end   = $start + strlen( $tok[0][0] );
							$top   = count( $frames ) - 1;
							if ( $start > $frames[ $top ]['pos'] ) {
								$frames[ $top ]['buf'] .= substr( $html, $frames[ $top ]['pos'], $start - $frames[ $top ]['pos'] );
							}
							$frames[ $top ]['pos'] = $end;
							$frames[]              = array(
								'buf'   => '',
								'attrs' => isset( $tok[3][0] ) ? $tok[3][0] : '',
								'pos'   => $end,
							);
						} elseif ( isset( $close_set[ $idx ] ) ) {
							$start = $tok[0][1];
							$end   = $start + strlen( $tok[0][0] );
							$done  = array_pop( $frames );
							if ( $start > $done['pos'] ) {
								$done['buf'] .= substr( $html, $done['pos'], $start - $done['pos'] );
							}
							$top                   = count( $frames ) - 1;
							$frames[ $top ]['pos'] = $end;
							$title                 = '';
							if ( preg_match( '~(?:^|\s)(?:title|question)\s*=\s*(?:"([^"]*)"|\'([^\']*)\')~is', $done['attrs'], $tm ) ) {
								$raw_title = '' !== $tm[1] ? $tm[1] : ( isset( $tm[2] ) ? $tm[2] : '' );
								$title     = trim( html_entity_decode( $raw_title, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
							}
							if ( '' === $title ) {
								$frames[ $top ]['buf'] .= $done['buf'];
							} else {
								$frames[ $top ]['buf'] .= "\n<details><summary>" . htmlspecialchars( $title, ENT_QUOTES, 'UTF-8' ) . "</summary>\n" . $done['buf'] . "\n</details>\n";
							}
						}
					}
					if ( $frames[0]['pos'] < $len ) {
						$frames[0]['buf'] .= substr( $html, $frames[0]['pos'] );
					}
					$html = $frames[0]['buf'];
				}
			}
		}

		// Collect tag names that appear with an explicit closing [/tag].
		$paired = array();
		if ( preg_match_all( '~\[/([a-zA-Z][a-zA-Z0-9_-]*)\s*\]~', $html, $cm ) ) {
			foreach ( $cm[1] as $t ) {
				$paired[ strtolower( $t ) ] = true;
			}
		}
		if ( $paired ) {
			$quoted = array_map(
				function ( $t ) {
					return preg_quote( $t, '~' );
				},
				array_keys( $paired )
			);
			$tmp    = preg_replace( '~\[/?(?:' . implode( '|', $quoted ) . ')(?:\b(?:[^\[\]"\']++|"[^"]*"|\'[^\']*\')*)?\]~is', '', $html );
			if ( is_string( $tmp ) ) {
				$html = $tmp;
			}
		}
	}

	// Strip remaining attribute-bearing or self-closing shortcodes (e.g. [vc_row css="..."], [ht-divider/]),
	// leaving prose brackets like [1], [section 2], or [utf-8] intact and avoiding O(N^2) backtracking.
	$tmp = preg_replace( '~\[[a-zA-Z][a-zA-Z0-9_-]*+(?:\s++[^\[\]=]*+=[^\[\]]*+|\s*+/)\]~s', '', $html );
	if ( is_string( $tmp ) ) {
		$html = $tmp;
	}

	return $html;
}

/**
 * Inspect a <p> element for bold/strong Q&A patterns.
 *
 * Returns null for ordinary answer paragraphs, or one flat token:
 * - array( 'bh', 0, $bold_text ) when <p> contains only <strong>/<b>.
 * - array( 'qa', 0, array( $q, $a ) ) when <p> has <strong>Q</strong><br>A.
 */
function horsetools_faq_parse_bold_p( $p_node, $txt_fn ) {
	$children = array();
	$has_br   = false;
	foreach ( $p_node->childNodes as $ch ) {
		if ( XML_TEXT_NODE === $ch->nodeType ) {
			$clean = preg_replace( '~[\x{00A0}\s]+~u', '', $ch->nodeValue );
			if ( '' === ( is_string( $clean ) ? $clean : trim( $ch->nodeValue ) ) ) {
				continue;
			}
		}
		if ( XML_ELEMENT_NODE === $ch->nodeType && 'br' === strtolower( $ch->nodeName ) ) {
			$has_br = true;
		}
		$children[] = $ch;
	}
	if ( ! $children ) {
		return null;
	}

	// Case 1: <p><strong>Heading or Question?</strong></p>.
	if ( 1 === count( $children ) && XML_ELEMENT_NODE === $children[0]->nodeType ) {
		$ctag = strtolower( $children[0]->nodeName );
		if ( 'strong' === $ctag || 'b' === $ctag ) {
			$btext = $txt_fn( $children[0] );
			if ( '' !== $btext ) {
				return array( 'bh', 0, $btext );
			}
		}
	}

	// Case 2: <p><b>Hỏi: ...</b><br>Đáp: ...</p> or <p><strong>Hỏi:</strong> ...<br>...</p>.
	if ( $has_br ) {
		$lines         = array( '' );
		$line_bold     = array( false );
		$line0_nonbold = false;
		$idx           = 0;
		foreach ( $p_node->childNodes as $ch ) {
			if ( XML_ELEMENT_NODE === $ch->nodeType && 'br' === strtolower( $ch->nodeName ) ) {
				$idx++;
				$lines[ $idx ]     = '';
				$line_bold[ $idx ] = false;
				continue;
			}
			if ( XML_ELEMENT_NODE === $ch->nodeType ) {
				$ctag = strtolower( $ch->nodeName );
				if ( in_array( $ctag, array( 'script', 'style', 'noscript', 'template', 'svg', 'iframe' ), true ) ) {
					continue;
				}
				if ( 0 === $idx && ( 'strong' === $ctag || 'b' === $ctag ) ) {
					$line_bold[0] = true;
				} elseif ( 0 === $idx ) {
					$clean_el = preg_replace( '~[\x{00A0}\s]+~u', '', $ch->textContent );
					if ( '' !== ( is_string( $clean_el ) ? $clean_el : trim( $ch->textContent ) ) ) {
						$line0_nonbold = true;
					}
				}
				$lines[ $idx ] .= $ch->textContent;
			} elseif ( XML_TEXT_NODE === $ch->nodeType ) {
				if ( 0 === $idx ) {
					$clean_tx = preg_replace( '~[\x{00A0}\s]+~u', '', $ch->nodeValue );
					if ( '' !== ( is_string( $clean_tx ) ? $clean_tx : trim( $ch->nodeValue ) ) ) {
						$line0_nonbold = true;
					}
				}
				$lines[ $idx ] .= $ch->nodeValue;
			}
		}
		$clean_line = function ( $s ) {
			$t = preg_replace( '~[\x{00A0}\s]+~u', ' ', (string) $s );
			return trim( is_string( $t ) ? $t : (string) $s );
		};
		$q_line = $clean_line( $lines[0] );
		$rest   = array();
		$total  = count( $lines );
		for ( $i = 1; $i < $total; $i++ ) {
			$part = $clean_line( $lines[ $i ] );
			if ( '' !== $part ) {
				$rest[] = $part;
			}
		}
		$a_line = implode( ' ', $rest );
		if ( $line_bold[0] && ! $line0_nonbold && '' !== $q_line && '' === $a_line ) {
			return array( 'bh', 0, $q_line );
		}
		if ( $line_bold[0] && '' !== $q_line && '' !== $a_line && horsetools_faq_looks_like_question( $q_line ) ) {
			return array( 'qa', 0, array( $q_line, $a_line ) );
		}
	}

	return null;
}

/**
 * Pull question/answer pairs out of post content.
 *
 * @param string $html       Post content (raw is fine).
 * @param string $keys       Regex from horsetools_faq_keys().
 * @param string $post_title Optional post title for dedicated FAQ page fallback.
 * @return array List of array( question, answer ).
 */
function horsetools_faq_extract( $html, $keys = '', $post_title = '' ) {
	if ( 1 === func_num_args() && '' === $keys ) {
		$keys = horsetools_faq_keys();
	}
	if ( '' === $keys || ! is_string( $keys ) || ! class_exists( 'DOMDocument' ) ) {
		return array();
	}

	$html = horsetools_faq_prepare_shortcodes( $html );

	$title_matches = ( '' !== trim( (string) $post_title ) ) && horsetools_faq_is_section_heading( $post_title, $keys );
	if ( ! horsetools_faq_matches_keys( $html, $keys ) && ! $title_matches ) {
		return array();
	}

	$body_html = function_exists( 'wpautop' ) ? wpautop( $html ) : $html;

	$prev = libxml_use_internal_errors( true );
	$doc  = new DOMDocument();
	$doc->loadHTML(
		'<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $body_html,
		LIBXML_NOERROR | LIBXML_NOWARNING
	);
	libxml_clear_errors();
	libxml_use_internal_errors( $prev );

	$body = $doc->getElementsByTagName( 'body' )->item( 0 );
	if ( ! $body ) {
		return array();
	}

	// Flatten the document to a reading-order list of headings, Q&A units, and text blocks.
	$flat = array();
	$txt  = function ( $n ) {
		$clone = $n->cloneNode( true );
		$cdoc  = $clone->ownerDocument;
		foreach ( array( 'script', 'style', 'noscript', 'template', 'svg', 'iframe', 'figcaption' ) as $tag ) {
			$drop = array();
			foreach ( $clone->getElementsByTagName( $tag ) as $el ) {
				$drop[] = $el;
			}
			foreach ( $drop as $el ) {
				if ( $el->parentNode ) {
					$el->parentNode->removeChild( $el );
				}
			}
		}
		if ( $cdoc ) {
			$brs = array();
			foreach ( $clone->getElementsByTagName( 'br' ) as $br ) {
				$brs[] = $br;
			}
			foreach ( $brs as $br ) {
				if ( $br->parentNode ) {
					$br->parentNode->replaceChild( $cdoc->createTextNode( ' ' ), $br );
				}
			}
			foreach ( array( 'p', 'div', 'li', 'dt', 'dd', 'tr', 'td', 'th', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $btag ) {
				$bels = array();
				foreach ( $clone->getElementsByTagName( $btag ) as $bel ) {
					$bels[] = $bel;
				}
				foreach ( $bels as $bel ) {
					if ( $bel->parentNode ) {
						$bel->parentNode->insertBefore( $cdoc->createTextNode( ' ' ), $bel );
						$bel->appendChild( $cdoc->createTextNode( ' ' ) );
					}
				}
			}
		}
		$ws = preg_replace( '~[\x{00A0}\s]+~u', ' ', $clone->textContent );
		return trim( is_string( $ws ) ? $ws : $clone->textContent );
	};

	$txt_inline = function ( $n ) {
		$clone = $n->cloneNode( true );
		$cdoc  = $clone->ownerDocument;
		foreach ( array( 'script', 'style', 'noscript', 'template', 'svg', 'iframe', 'figcaption' ) as $tag ) {
			$drop = array();
			foreach ( $clone->getElementsByTagName( $tag ) as $el ) {
				$drop[] = $el;
			}
			foreach ( $drop as $el ) {
				if ( $el->parentNode ) {
					$el->parentNode->removeChild( $el );
				}
			}
		}
		if ( $cdoc ) {
			$brs = array();
			foreach ( $clone->getElementsByTagName( 'br' ) as $br ) {
				$brs[] = $br;
			}
			foreach ( $brs as $br ) {
				if ( $br->parentNode ) {
					$br->parentNode->replaceChild( $cdoc->createTextNode( ' ' ), $br );
				}
			}
		}
		return (string) $clone->textContent;
	};

	$block_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'ul', 'ol', 'li', 'dl', 'dt', 'dd', 'details', 'blockquote', 'table', 'section', 'article', 'main', 'figure' );
	$has_block  = function ( $node ) use ( $block_tags ) {
		foreach ( $block_tags as $bt ) {
			if ( $node->getElementsByTagName( $bt )->length > 0 ) {
				return true;
			}
		}
		return false;
	};

	$walk = function ( $node ) use ( &$walk, &$flat, $txt, $txt_inline, $has_block ) {
		$inline_buf   = '';
		$flush_inline = function () use ( &$inline_buf, &$flat ) {
			if ( '' === $inline_buf ) {
				return;
			}
			$ws  = preg_replace( '~[\x{00A0}\s]+~u', ' ', $inline_buf );
			$out = trim( is_string( $ws ) ? $ws : $inline_buf );
			$inline_buf = '';
			if ( '' !== $out ) {
				$flat[] = array( 't', 0, $out );
			}
		};

		foreach ( $node->childNodes as $c ) {
			if ( XML_TEXT_NODE === $c->nodeType ) {
				$inline_buf .= $c->nodeValue;
				continue;
			}
			if ( XML_ELEMENT_NODE !== $c->nodeType ) {
				continue;
			}
			$tag = strtolower( $c->nodeName );

			if ( in_array( $tag, array( 'script', 'style', 'noscript', 'template', 'svg', 'iframe', 'nav', 'form', 'figcaption' ), true ) ) {
				$flush_inline();
				continue;
			}
			if ( preg_match( '~^h([1-6])$~', $tag, $m ) ) {
				$flush_inline();
				$flat[] = array( 'h', (int) $m[1], $txt( $c ) );
				continue;
			}
			if ( 'details' === $tag ) {
				$flush_inline();
				$summaries = $c->getElementsByTagName( 'summary' );
				if ( $summaries->length > 0 ) {
					$sum    = $summaries->item( 0 );
					$q_text = $txt( $sum );
					$clone  = $c->cloneNode( true );
					$csums  = $clone->getElementsByTagName( 'summary' );
					if ( $csums->length > 0 && $csums->item( 0 )->parentNode ) {
						$csums->item( 0 )->parentNode->removeChild( $csums->item( 0 ) );
					}
					$a_text = $txt( $clone );
					$flat[] = array( 'qa', 0, array( $q_text, $a_text ) );
				} else {
					$walk( $c );
				}
				continue;
			}
			if ( 'dt' === $tag ) {
				$flush_inline();
				$flat[] = array( 'dt', 0, $txt( $c ) );
				continue;
			}
			if ( 'dd' === $tag ) {
				$flush_inline();
				$flat[] = array( 'dd', 0, $txt( $c ) );
				continue;
			}
			if ( 'p' === $tag ) {
				$flush_inline();
				$bold_tok = horsetools_faq_parse_bold_p( $c, $txt );
				if ( null !== $bold_tok ) {
					$flat[] = $bold_tok;
				} else {
					$flat[] = array( 't', 0, $txt( $c ) );
				}
				continue;
			}
			if ( 'li' === $tag ) {
				$flush_inline();
				if ( $has_block( $c ) ) {
					$walk( $c );
				} else {
					$flat[] = array( 't', 0, $txt( $c ) );
				}
				continue;
			}
			if ( 'blockquote' === $tag ) {
				$flush_inline();
				if ( $has_block( $c ) ) {
					$walk( $c );
				} else {
					$flat[] = array( 't', 0, $txt( $c ) );
				}
				continue;
			}
			if ( 'br' === $tag ) {
				$inline_buf .= ' ';
				continue;
			}
			if ( 'tr' === $tag ) {
				$flush_inline();
				$has_inner_struct = false;
				foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'details', 'dl' ) as $stag ) {
					if ( $c->getElementsByTagName( $stag )->length > 0 ) {
						$has_inner_struct = true;
						break;
					}
				}
				if ( ! $has_inner_struct ) {
					$cells = array();
					foreach ( $c->childNodes as $cell ) {
						if ( XML_ELEMENT_NODE === $cell->nodeType ) {
							$ctag = strtolower( $cell->nodeName );
							if ( 'th' === $ctag || 'td' === $ctag ) {
								$cells[] = $cell;
							}
						}
					}
					if ( 2 === count( $cells ) && 'td' === strtolower( $cells[1]->nodeName ) ) {
						$c0 = $txt( $cells[0] );
						$c1 = $txt( $cells[1] );
						if ( '' !== $c0 && '' !== $c1 && horsetools_faq_looks_like_question( $c0 ) ) {
							$flat[] = array( 'tqa', 0, array( $c0, $c1 ) );
							continue;
						}
					}
				}
				$walk( $c );
				continue;
			}
			if ( in_array( $tag, array( 'div', 'section', 'article', 'main', 'header', 'footer', 'aside', 'figure', 'ul', 'ol', 'dl', 'table', 'thead', 'tbody', 'tfoot', 'td', 'th', 'address' ), true ) ) {
				$flush_inline();
				$walk( $c );
				continue;
			}
			// Inline elements: preserve inline spacing if no nested blocks, otherwise walk.
			if ( $has_block( $c ) ) {
				$flush_inline();
				$walk( $c );
			} else {
				$inline_buf .= $txt_inline( $c );
			}
		}
		$flush_inline();
	};
	$walk( $body );

	$had_section = false;
	$run_machine = function ( $start_in ) use ( $flat, $keys, &$had_section ) {
		$pairs     = array();
		$seen_q    = array();
		$lvl       = 0;
		$q_lvl     = 0;
		$in        = $start_in;
		$bh_opened = false;
		$has_bh_q  = false;
		$q         = '';
		$a         = '';

		$add_pair = function ( $q_text, $a_text ) use ( &$pairs, &$seen_q ) {
			$q_text = trim( (string) $q_text );
			$a_text = trim( (string) $a_text );
			if ( '' === $q_text || '' === $a_text ) {
				return;
			}
			$q_norm = html_entity_decode( $q_text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			if ( class_exists( 'Normalizer' ) ) {
				$norm = Normalizer::normalize( $q_norm, Normalizer::FORM_C );
				if ( is_string( $norm ) ) {
					$q_norm = $norm;
				}
			}
			$q_norm = function_exists( 'mb_strtolower' ) ? mb_strtolower( $q_norm, 'UTF-8' ) : strtolower( $q_norm );
			$ws     = preg_replace( '~[\x{00A0}\s]+~u', ' ', $q_norm );
			$q_key  = trim( is_string( $ws ) ? $ws : $q_norm );
			if ( '' === $q_key || isset( $seen_q[ $q_key ] ) ) {
				return;
			}
			$seen_q[ $q_key ] = true;
			$pairs[]          = array( $q_text, $a_text );
		};

		$flush = function () use ( &$q, &$a, $add_pair ) {
			$add_pair( $q, $a );
			$q = '';
			$a = '';
		};

		foreach ( $flat as $item ) {
			list( $kind, $level, $payload ) = $item;

			if ( 'h' === $kind ) {
				$text = $payload;
				if ( ! $in ) {
					if ( horsetools_faq_is_section_heading( $text, $keys ) ) {
						$in          = true;
						$lvl         = $level;
						$q_lvl       = 0;
						$bh_opened   = false;
						$has_bh_q    = false;
						$had_section = true;
					}
					continue;
				}
				// A heading at the same level or higher closes the current FAQ section,
				// but scanning continues in case another FAQ section appears later.
				if ( $level <= $lvl ) {
					$flush();
					$in        = false;
					$lvl       = 0;
					$q_lvl     = 0;
					$bh_opened = false;
					$has_bh_q  = false;
					if ( horsetools_faq_is_section_heading( $text, $keys ) ) {
						$in          = true;
						$lvl         = $level;
						$had_section = true;
					}
					continue;
				}
				// If a bold <p><strong>FAQ</strong></p> section already produced questions,
				// a trailing non-question heading at or above the question level exits the FAQ section.
				if ( $bh_opened && ! horsetools_faq_looks_like_question( $text )
					&& ( ( $has_bh_q && 0 === $q_lvl )
						|| ( $q_lvl > 0 && $level <= $q_lvl && horsetools_faq_looks_like_question( $q ) ) ) ) {
					$flush();
					$in        = false;
					$lvl       = 0;
					$q_lvl     = 0;
					$bh_opened = false;
					$has_bh_q  = false;
					if ( horsetools_faq_is_section_heading( $text, $keys ) ) {
						$in          = true;
						$lvl         = $level;
						$had_section = true;
					}
					continue;
				}
				// Inline callout/note subheadings (e.g. <h4>Lưu ý:</h4>) inside an active heading question stay in the answer.
				if ( $q_lvl > 0 && $level > $q_lvl && '' !== $q && horsetools_faq_is_note_heading( $text ) ) {
					$a = ( '' === $a ) ? $text : $a . ' ' . $text;
					continue;
				}
				// Editorial/closing subheadings end the section without becoming questions.
				if ( horsetools_faq_is_noise_heading( $text ) ) {
					$flush();
					$in        = false;
					$lvl       = 0;
					$q_lvl     = 0;
					$bh_opened = false;
					$has_bh_q  = false;
					continue;
				}
				// Deeper heading inside an active heading question vs. group wrapper heading.
				if ( $q_lvl > 0 && $level > $q_lvl ) {
					$q_is_q = horsetools_faq_looks_like_question( $q );
					$t_is_q = horsetools_faq_looks_like_question( $text );
					if ( $q_is_q && ! $t_is_q ) {
						// Active question followed by non-question substeps (with or without intro <p>).
						$a = ( '' === $a ) ? $text : $a . ' ' . $text;
						continue;
					}
					if ( $q_is_q && '' !== $a ) {
						$a .= ' ' . $text;
						continue;
					}
					if ( ! $q_is_q && $t_is_q ) {
						// Non-question group/category heading (empty or intro <p>) followed by question subheadings.
						$q     = $text;
						$a     = '';
						$q_lvl = $level;
						continue;
					}
					if ( '' !== $a ) {
						$a .= ' ' . $text;
						continue;
					}
					$q     = $text;
					$q_lvl = $level;
					continue;
				}
				$flush();
				$q     = $text;
				$q_lvl = $level;
				continue;
			}

			if ( 'bh' === $kind ) {
				$text = $payload;
				if ( ! $in ) {
					if ( horsetools_faq_is_section_heading( $text, $keys ) ) {
						$in          = true;
						$lvl         = 1;
						$q_lvl       = 0;
						$bh_opened   = true;
						$has_bh_q    = false;
						$had_section = true;
					}
					continue;
				}
				if ( horsetools_faq_is_noise_heading( $text ) ) {
					if ( horsetools_faq_is_note_heading( $text ) ) {
						if ( '' !== $q && '' !== $text ) {
							$a = ( '' === $a ) ? $text : $a . ' ' . $text;
						}
						continue;
					}
					$flush();
					$in        = false;
					$lvl       = 0;
					$q_lvl     = 0;
					$bh_opened = false;
					$has_bh_q  = false;
					continue;
				}
				if ( horsetools_faq_looks_like_question( $text ) ) {
					if ( 0 === $q_lvl ) {
						$flush();
						$q        = $text;
						$has_bh_q = true;
						continue;
					}
					if ( '' === $a || ! horsetools_faq_looks_like_question( $q ) ) {
						// Drop empty/intro group heading and promote bold question.
						$q        = $text;
						$a        = '';
						$q_lvl    = 0;
						$has_bh_q = true;
						continue;
					}
				}
				if ( '' !== $q && '' !== $text ) {
					$a = ( '' === $a ) ? $text : $a . ' ' . $text;
				}
				continue;
			}

			if ( 'tqa' === $kind ) {
				if ( $in ) {
					if ( $q_lvl > 0 && '' !== $q && horsetools_faq_looks_like_question( $q ) ) {
						$row_txt = trim( $payload[0] . ' ' . $payload[1] );
						if ( '' !== $row_txt ) {
							$a = ( '' === $a ) ? $row_txt : $a . ' ' . $row_txt;
						}
					} else {
						$flush();
						$q_lvl = 0;
						if ( ! horsetools_faq_is_noise_heading( $payload[0] ) ) {
							$add_pair( $payload[0], $payload[1] );
						}
					}
				}
				continue;
			}

			if ( 'qa' === $kind ) {
				if ( $in ) {
					$flush();
					$q_lvl = 0;
					if ( ! horsetools_faq_is_noise_heading( $payload[0] ) ) {
						$add_pair( $payload[0], $payload[1] );
					}
				}
				continue;
			}

			if ( 'dt' === $kind ) {
				if ( $in ) {
					$flush();
					if ( ! horsetools_faq_is_noise_heading( $payload ) ) {
						$q     = $payload;
						$q_lvl = 0;
					}
				}
				continue;
			}

			if ( 'dd' === $kind ) {
				if ( $in && '' !== $q && '' !== $payload ) {
					$a = ( '' === $a ) ? $payload : $a . ' ' . $payload;
				}
				continue;
			}

			if ( 't' === $kind && $in && '' !== $q && '' !== $payload ) {
				$a = ( '' === $a ) ? $payload : $a . ' ' . $payload;
			}
		}
		$flush();
		return $pairs;
	};

	$pairs = $run_machine( false );
	if ( ! $pairs && $title_matches && ! $had_section ) {
		$pairs = $run_machine( true );
	}
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
	$split = preg_split( '~[,\s]+~', $raw );
	if ( ! is_array( $split ) ) {
		return array();
	}
	return array_filter( array_map( 'absint', $split ) );
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
 * Does another plugin or block already publish FAQPage schema for this post?
 * Two FAQPage blocks on one URL is worse than none.
 */
function horsetools_faq_foreign( $post_id ) {
	foreach ( (array) get_post_meta( $post_id ) as $key => $vals ) {
		if ( 0 === strpos( $key, 'rank_math_schema' ) ) {
			foreach ( (array) $vals as $v ) {
				if ( false !== stripos( (string) $v, 'FAQPage' ) ) {
					return true;
				}
			}
		}
		if ( '_seopress_pro_rich_snippets_type' === $key ) {
			foreach ( (array) $vals as $v ) {
				$val = strtolower( trim( (string) $v ) );
				if ( 'faq' === $val || 'faqpage' === $val ) {
					return true;
				}
			}
		}
	}

	$content = (string) get_post_field( 'post_content', $post_id );
	foreach ( array( 'wp:yoast/faq-block', 'wp:rank-math/faq-block', 'wp:aioseo/faq', 'wp:uagb/faq' ) as $marker ) {
		if ( false !== strpos( $content, $marker ) ) {
			return true;
		}
	}

	// Horse Tools' own [ht-accordion faq="1|yes|true"] emits its own FAQPage JSON-LD.
	if ( false !== stripos( $content, '[ht-accordion' )
		&& preg_match_all( '~\[ht-accordion\b((?:[^\[\]"\']++|"[^"]*"|\'[^\']*\')*)\]~i', $content, $am ) ) {
		foreach ( $am[1] as $acc_attrs ) {
			if ( preg_match( '~(?:^|\s)faq\s*=\s*(?:"(?:1|yes|true)"|\'(?:1|yes|true)\'|(?:1|yes|true))(?:\s|/|$)~i', $acc_attrs ) ) {
				return true;
			}
		}
	}

	// Only real <script> or HTML Microdata elements count — escaped tutorial prose
	// (&lt;script&gt;...FAQPage or &lt;div itemtype=...&gt;) must not silence us.
	if ( false !== stripos( $content, 'FAQPage' ) ) {
		if ( preg_match_all( '~<script\b[^>]*ld\+json[^>]*>~i', $content, $sm, PREG_OFFSET_CAPTURE ) ) {
			foreach ( $sm[0] as $tag ) {
				$start = $tag[1] + strlen( $tag[0] );
				$end   = preg_match( '~</script\b[^>]*>~i', $content, $cm, PREG_OFFSET_CAPTURE, $start ) ? $cm[0][1] : false;
				$body  = ( false === $end ) ? substr( $content, $start ) : substr( $content, $start, $end - $start );
				if ( false !== stripos( $body, 'FAQPage' ) ) {
					return true;
				}
			}
		}
		if ( preg_match( '~<[a-z][a-z0-9:-]*\b[^>]*\bitemtype\s*=\s*(?:"[^"]*schema\.org/FAQPage[^"]*"|\'[^\']*schema\.org/FAQPage[^\']*\')[^>]*>~i', $content ) ) {
			return true;
		}
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
 * The JSON-LD for one post, computed at most once per edit/settings/permalink state.
 *
 * @param int  $post_id
 * @param bool $force Recompute even when the cache looks current.
 * @return string JSON, or '' when this post gets no schema.
 */
function horsetools_faq_json( $post_id, $force = false ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return '';
	}

	// Always check foreign conflict outside the stale-cache window so installing
	// or removing another FAQ plugin takes effect immediately.
	if ( horsetools_faq_foreign( $post_id ) ) {
		return '';
	}

	$keys      = horsetools_faq_keys();
	$min       = horsetools_faq_min();
	$maxlen    = horsetools_faq_maxlen();
	$permalink = get_permalink( $post_id );
	$permalink = is_string( $permalink ) ? $permalink : '';

	$sig    = md5( $keys . '|' . $min . '|' . $maxlen . '|' . $permalink );
	$stamp  = get_post_modified_time( 'U', true, $post_id ) . '#' . HORSETOOLS_FAQ_RULES . '#' . $sig;
	$cached = get_post_meta( $post_id, '_ht_faq_ld', true );
	$seen   = (string) get_post_meta( $post_id, '_ht_faq_stamp', true );

	if ( ! $force && is_string( $cached ) && $seen === $stamp ) {
		return $cached;
	}

	$json  = '';
	$pairs = horsetools_faq_extract(
		(string) get_post_field( 'post_content', $post_id ),
		$keys,
		(string) get_the_title( $post_id )
	);

	if ( count( $pairs ) >= $min ) {
		$items = array();
		foreach ( $pairs as $pair ) {
			$answer = $pair[1];
			if ( function_exists( 'mb_strlen' ) && mb_strlen( $answer, 'UTF-8' ) > $maxlen ) {
				$cut   = mb_substr( $answer, 0, $maxlen, 'UTF-8' );
				$space = function_exists( 'mb_strrpos' ) ? mb_strrpos( $cut, ' ', 0, 'UTF-8' ) : false;
				if ( false !== $space && $space > (int) ( $maxlen * 0.6 ) ) {
					$cut = mb_substr( $cut, 0, $space, 'UTF-8' );
				}
				$answer = rtrim( $cut, " \t\n\r\0\x0B.,;:!?" ) . '…';
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
		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $items,
		);
		if ( '' !== $permalink ) {
			$schema['mainEntityOfPage'] = $permalink;
		}
		// JSON_UNESCAPED_SLASHES is deliberately NOT set: with "/" escaped, a
		// stray </script> inside the content cannot break out of the block.
		$encoded = wp_json_encode( $schema, JSON_UNESCAPED_UNICODE );
		if ( is_string( $encoded ) && '' !== $encoded ) {
			$json = $encoded;
		}
	}

	// wp_slash() before storing, because update_post_meta() runs wp_unslash().
	update_post_meta( $post_id, '_ht_faq_ld', wp_slash( $json ) );
	update_post_meta( $post_id, '_ht_faq_stamp', $stamp );
	return $json;
}

/** Is this post in scope (type + category)? */
function horsetools_faq_in_scope( $post_id ) {
	$type = get_post_type( $post_id );
	if ( ! in_array( $type, horsetools_faq_types(), true ) ) {
		return false;
	}
	$cats = horsetools_faq_cats();
	if ( ! $cats ) {
		return true;
	}
	if ( function_exists( 'is_object_in_taxonomy' ) && ! is_object_in_taxonomy( $type, 'category' ) ) {
		return true;
	}
	$has = wp_get_post_categories( $post_id );
	return (bool) array_intersect( $cats, (array) $has );
}

function horsetools_faq_head() {
	if ( ( function_exists( 'is_embed' ) && is_embed() )
		|| ! horsetools_opt( 'main', 'faq-schema1', '' )
		|| ! is_singular( horsetools_faq_types() ) ) {
		return;
	}
	$id = get_the_ID();
	if ( ! $id
		|| ( function_exists( 'post_password_required' ) && post_password_required( $id ) )
		|| ! horsetools_faq_in_scope( $id ) ) {
		return;
	}
	$json = horsetools_faq_json( $id );
	if ( ! is_string( $json ) || '' === $json ) {
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
 * Site-wide scan — which posts get schema, and which look like they should
 * but do not.
 * ---------------------------------------------------------------------- */
function horsetools_faq_scan_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'forbidden', 403 );
	}
	check_ajax_referer( 'horsetools_faq', 'nonce' );

	$keys = horsetools_faq_keys();
	$min  = horsetools_faq_min();

	$limit = 2000;
	$q     = new WP_Query( array(
		'post_type'        => horsetools_faq_types(),
		'post_status'      => 'publish',
		'posts_per_page'   => $limit,
		'fields'           => 'ids',
		'suppress_filters' => true,
		'orderby'          => 'ID',
		'order'            => 'DESC',
	) );
	$ids       = is_array( $q->posts ) ? $q->posts : array();
	$total     = isset( $q->found_posts ) ? (int) $q->found_posts : count( $ids );
	$truncated = $total > count( $ids );

	if ( $ids && function_exists( '_prime_post_caches' ) ) {
		_prime_post_caches( $ids, true, true );
	}

	$ok = 0; $few = array(); $none = array(); $skip = 0; $foreign = 0; $q_total = 0;
	if ( '' === $keys ) {
		$skip = count( $ids );
	} else {
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
			$title   = (string) get_the_title( $id );
			$pairs   = horsetools_faq_extract( $content, $keys, $title );
			$n       = count( $pairs );

			if ( $n >= $min ) {
				$ok++;
				$q_total += $n;
			} elseif ( $n > 0 ) {
				$link  = get_edit_post_link( $id, 'raw' );
				$few[] = array( 'id' => $id, 'n' => $n, 'title' => $title, 'link' => is_string( $link ) ? $link : '' );
			} else {
				$prep  = horsetools_faq_prepare_shortcodes( $content );
				$looks = horsetools_faq_is_section_heading( $title, $keys );
				if ( ! $looks && preg_match_all( '~<(?:h[1-6]|strong|b)\b[^>]*>(.*?)</(?:h[1-6]|strong|b)>~is', $prep, $hm ) ) {
					foreach ( $hm[1] as $htxt ) {
						if ( horsetools_faq_is_section_heading( wp_strip_all_tags( $htxt ), $keys ) ) {
							$looks = true;
							break;
						}
					}
				}
				if ( $looks ) {
					$link   = get_edit_post_link( $id, 'raw' );
					$none[] = array( 'id' => $id, 'n' => 0, 'title' => $title, 'link' => is_string( $link ) ? $link : '' );
				} else {
					$skip++;
				}
			}
		}
	}

	wp_send_json_success( array(
		'ok'        => $ok,
		'avg'       => $ok ? round( $q_total / $ok, 1 ) : 0,
		'few'       => array_slice( $few, 0, 100 ),
		'none'      => array_slice( $none, 0, 100 ),
		'fewN'      => count( $few ),
		'noneN'     => count( $none ),
		'skip'      => $skip,
		'foreign'   => $foreign,
		'total'     => $total,
		'scanned'   => count( $ids ),
		'truncated' => $truncated,
	) );
}
add_action( 'wp_ajax_horsetools_faq_scan', 'horsetools_faq_scan_ajax' );

/** Forget every cached result, so the next view of each post re-reads it. */
function horsetools_faq_flush_ajax() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'forbidden', 403 );
	}
	check_ajax_referer( 'horsetools_faq', 'nonce' );

	global $wpdb;
	$pids = $wpdb->get_col( "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key IN ('_ht_faq_ld','_ht_faq_stamp')" ); // phpcs:ignore WordPress.DB
	$n    = $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('_ht_faq_ld','_ht_faq_stamp')" ); // phpcs:ignore WordPress.DB
	if ( ! empty( $pids ) && function_exists( 'wp_cache_delete' ) ) {
		foreach ( $pids as $pid ) {
			wp_cache_delete( (int) $pid, 'post_meta' );
		}
	}
	wp_send_json_success( array( 'rows' => (int) $n ) );
}
add_action( 'wp_ajax_horsetools_faq_flush', 'horsetools_faq_flush_ajax' );

/* -------------------------------------------------------------------------
 * Editor hint: how many questions this post will publish, while writing it.
 * ---------------------------------------------------------------------- */
function horsetools_faq_meta_box( $post_type = '', $post = null ) {
	if ( ! horsetools_opt( 'main', 'faq-schema1', '' ) ) {
		return;
	}
	if ( $post && isset( $post->ID ) && ! horsetools_faq_in_scope( $post->ID ) ) {
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
add_action( 'add_meta_boxes', 'horsetools_faq_meta_box', 10, 2 );

function horsetools_faq_meta_render( $post ) {
	if ( ! $post || ! isset( $post->ID ) || ! horsetools_faq_in_scope( $post->ID ) ) {
		return;
	}
	if ( horsetools_faq_foreign( $post->ID ) ) {
		echo '<p>' . esc_html__( 'Another plugin already publishes FAQ schema for this post, so Horse Tools is staying out of the way.', 'horse-tools' ) . '</p>';
		return;
	}
	$pairs = horsetools_faq_extract( (string) $post->post_content, horsetools_faq_keys(), (string) get_the_title( $post->ID ) );
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
