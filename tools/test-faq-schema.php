<?php
/**
 * Standalone regression suite for Automatic FAQ Detection.
 *
 * Tests the real production functions in inc/faq-schema.php, inc/sanitize.php,
 * inc/css.php, and inc/shortcodes-lib.php with minimal WordPress shims, plus
 * structural security checks on main/section/seo-faq.php, modal/modal.php,
 * and uninstall.php.
 *
 * Usage: php tools/test-faq-schema.php
 */

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'HORSETOOLS_DIR', dirname( __DIR__ ) . '/' );
define( 'HORSETOOLS_URL', 'https://example.com/wp-content/plugins/horse-tools/' );
define( 'HORSETOOLS_VERSION', '1.3.49' );

if ( ! defined( 'ENT_QUOTES' ) ) { define( 'ENT_QUOTES', 3 ); }
if ( ! defined( 'ENT_HTML5' ) ) { define( 'ENT_HTML5', 48 ); }

/* -------------------------------------------------------------------------
 * Minimal WordPress & Horse Tools shims
 * ---------------------------------------------------------------------- */
$GLOBALS['ht_test_opts']        = array( 'main' => array() );
$GLOBALS['ht_test_posts']       = array();
$GLOBALS['ht_test_meta']        = array();
$GLOBALS['ht_test_cats']        = array();
$GLOBALS['ht_test_permalinks']  = array();
$GLOBALS['ht_test_filters']     = array();
$GLOBALS['ht_test_actions']     = array();
$GLOBALS['ht_test_shortcodes']  = array();
$GLOBALS['ht_test_can']         = true;
$GLOBALS['ht_test_nonce_ok']    = true;
$GLOBALS['ht_test_singular']    = true;
$GLOBALS['ht_test_current_id']  = 0;
$GLOBALS['ht_test_embed']       = false;
$GLOBALS['ht_test_pw_required'] = array();
$GLOBALS['ht_test_cache_del']   = array();
$GLOBALS['ht_test_primed']      = array();
$GLOBALS['ht_test_json_resp']   = null;
$GLOBALS['ht_test_fail_encode'] = false;

class Horsetools_Test_Ajax_Exit extends RuntimeException {}

function __( $s, $d = '' ) { return $s; }
function _e( $s, $d = '' ) { echo $s; }
function _n( $s, $p, $n, $d = '' ) { return 1 === (int) $n ? $s : $p; }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_html__( $s, $d = '' ) { return esc_html( $s ); }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_url_raw( $s ) { return filter_var( (string) $s, FILTER_SANITIZE_URL ); }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_textarea_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_email( $s ) { return trim( (string) $s ); }
function sanitize_key( $s ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $s ) ); }
function wp_kses_post( $s ) { return (string) $s; }
function wp_strip_all_tags( $s, $remove_breaks = false ) {
	$s = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', (string) $s );
	$s = strip_tags( (string) $s );
	if ( $remove_breaks ) {
		$s = preg_replace( '/[\r\n\t ]+/', ' ', $s );
	}
	return trim( $s );
}
function absint( $v ) { return abs( (int) $v ); }
function add_action( $h, $f, $p = 10, $a = 1 ) { $GLOBALS['ht_test_actions'][ $h ][] = $f; }
function add_filter( $h, $f, $p = 10, $a = 1 ) { $GLOBALS['ht_test_filters'][ $h ][] = $f; }
function apply_filters( $h, $v ) {
	$args = func_get_args();
	array_shift( $args );
	if ( isset( $GLOBALS['ht_test_filters'][ $h ] ) ) {
		foreach ( $GLOBALS['ht_test_filters'][ $h ] as $fn ) {
			$args[0] = call_user_func_array( $fn, $args );
		}
	}
	return $args[0];
}
function add_shortcode( $tag, $cb ) { $GLOBALS['ht_test_shortcodes'][ $tag ] = $cb; }
function shortcode_atts( $pairs, $atts, $sc = '' ) {
	$atts = (array) $atts;
	$out  = array();
	foreach ( $pairs as $name => $default ) {
		$out[ $name ] = array_key_exists( $name, $atts ) ? $atts[ $name ] : $default;
	}
	return $out;
}
function do_shortcode( $content ) {
	$content = (string) $content;
	foreach ( $GLOBALS['ht_test_shortcodes'] as $tag => $cb ) {
		$pat = '~\[(' . preg_quote( $tag, '~' ) . ')\b([^\]]*)\](.*?)\[/\1\]~is';
		if ( preg_match_all( $pat, $content, $ms, PREG_SET_ORDER ) ) {
			foreach ( $ms as $m ) {
				$atts = array();
				if ( preg_match_all( '~([a-zA-Z0-9_-]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s\]]+))~', $m[2], $am, PREG_SET_ORDER ) ) {
					foreach ( $am as $a ) {
						$atts[ $a[1] ] = '' !== $a[2] ? $a[2] : ( isset( $a[3] ) && '' !== $a[3] ? $a[3] : ( isset( $a[4] ) ? $a[4] : '' ) );
					}
				}
				$rep     = call_user_func( $cb, $atts, $m[3] );
				$content = str_replace( $m[0], $rep, $content );
			}
		}
	}
	return $content;
}
function wp_register_style() {}
function wp_register_script() {}
function wp_localize_script() {}
function wp_style_is() { return true; }
function wp_enqueue_style() {}
function wp_enqueue_script() {}
function wp_slash( $v ) { return is_string( $v ) ? addslashes( $v ) : $v; }
function wp_unslash( $v ) { return is_string( $v ) ? stripslashes( $v ) : $v; }
function wp_json_encode( $data, $options = 0, $depth = 512 ) {
	if ( $GLOBALS['ht_test_fail_encode'] ) {
		return false;
	}
	return json_encode( $data, $options, $depth );
}
function wpautop( $pee ) {
	$pee = trim( (string) $pee );
	if ( '' === $pee ) {
		return '';
	}
	$pee  = str_replace( array( "\r\n", "\r" ), "\n", $pee ) . "\n\n";
	$pees = preg_split( '/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY );
	$out  = '';
	foreach ( $pees as $tinkle ) {
		$tinkle = trim( $tinkle );
		if ( preg_match( '~^<(?:h[1-6]|div|p|ul|ol|li|dl|dt|dd|details|summary|table|blockquote|section|article|script|style|template|nav|form)\b~i', $tinkle ) ) {
			$out .= $tinkle . "\n";
		} else {
			$out .= '<p>' . str_replace( "\n", "<br />\n", $tinkle ) . "</p>\n";
		}
	}
	return $out;
}
function horsetools_opt( $module, $key = '', $default = null ) {
	$grp = isset( $GLOBALS['ht_test_opts'][ $module ] ) ? $GLOBALS['ht_test_opts'][ $module ] : array();
	if ( '' === $key ) {
		return $grp;
	}
	return array_key_exists( $key, $grp ) ? $grp[ $key ] : $default;
}
function get_option( $name, $default = false ) { return $default; }
function get_post_meta( $post_id, $key = '', $single = false ) {
	$m = isset( $GLOBALS['ht_test_meta'][ $post_id ] ) ? $GLOBALS['ht_test_meta'][ $post_id ] : array();
	if ( '' === $key ) {
		$out = array();
		foreach ( $m as $k => $v ) {
			$out[ $k ] = is_array( $v ) ? $v : array( $v );
		}
		return $out;
	}
	if ( ! array_key_exists( $key, $m ) ) {
		return $single ? '' : array();
	}
	return $single ? $m[ $key ] : array( $m[ $key ] );
}
function update_post_meta( $post_id, $key, $val ) {
	$GLOBALS['ht_test_meta'][ $post_id ][ $key ] = wp_unslash( $val );
	return true;
}
function delete_post_meta( $post_id, $key ) {
	unset( $GLOBALS['ht_test_meta'][ $post_id ][ $key ] );
	return true;
}
function get_post_field( $field, $post_id ) {
	return isset( $GLOBALS['ht_test_posts'][ $post_id ][ $field ] ) ? $GLOBALS['ht_test_posts'][ $post_id ][ $field ] : '';
}
function get_the_title( $post_id = 0 ) {
	$id = $post_id ? $post_id : $GLOBALS['ht_test_current_id'];
	return isset( $GLOBALS['ht_test_posts'][ $id ]['post_title'] ) ? $GLOBALS['ht_test_posts'][ $id ]['post_title'] : '';
}
function get_post_type( $post_id = 0 ) {
	$id = $post_id ? $post_id : $GLOBALS['ht_test_current_id'];
	return isset( $GLOBALS['ht_test_posts'][ $id ]['post_type'] ) ? $GLOBALS['ht_test_posts'][ $id ]['post_type'] : 'post';
}
function get_post_modified_time( $d = 'U', $gmt = false, $post_id = 0 ) {
	return isset( $GLOBALS['ht_test_posts'][ $post_id ]['modified'] ) ? $GLOBALS['ht_test_posts'][ $post_id ]['modified'] : '1700000000';
}
function get_permalink( $post_id = 0 ) {
	if ( array_key_exists( $post_id, $GLOBALS['ht_test_permalinks'] ) ) {
		return $GLOBALS['ht_test_permalinks'][ $post_id ];
	}
	return 'https://example.com/post-' . (int) $post_id . '/';
}
function get_edit_post_link( $post_id, $ctx = '' ) {
	if ( isset( $GLOBALS['ht_test_posts'][ $post_id ]['no_edit_link'] ) ) {
		return null;
	}
	return 'https://example.com/wp-admin/post.php?post=' . (int) $post_id . '&action=edit';
}
function get_the_ID() { return $GLOBALS['ht_test_current_id']; }
function is_singular( $types = '' ) { return (bool) $GLOBALS['ht_test_singular']; }
function is_embed() { return (bool) $GLOBALS['ht_test_embed']; }
function post_password_required( $id = null ) { return ! empty( $GLOBALS['ht_test_pw_required'][ $id ] ); }
function is_object_in_taxonomy( $type, $tax ) { return ( 'post' === $type && 'category' === $tax ); }
function wp_get_post_categories( $post_id ) {
	return isset( $GLOBALS['ht_test_cats'][ $post_id ] ) ? $GLOBALS['ht_test_cats'][ $post_id ] : array();
}
function current_user_can( $cap ) { return (bool) $GLOBALS['ht_test_can']; }
function check_ajax_referer( $action, $arg = false ) {
	if ( ! $GLOBALS['ht_test_nonce_ok'] ) {
		throw new Horsetools_Test_Ajax_Exit( 'bad_nonce' );
	}
	return 1;
}
function wp_send_json_error( $data = null, $status_code = null ) {
	$GLOBALS['ht_test_json_resp'] = array( 'success' => false, 'data' => $data, 'status' => $status_code );
	throw new Horsetools_Test_Ajax_Exit( 'json_error' );
}
function wp_send_json_success( $data = null, $status_code = null ) {
	$GLOBALS['ht_test_json_resp'] = array( 'success' => true, 'data' => $data, 'status' => $status_code );
	throw new Horsetools_Test_Ajax_Exit( 'json_success' );
}
function wp_cache_delete( $key, $group = '' ) {
	$GLOBALS['ht_test_cache_del'][] = array( $key, $group );
	return true;
}
function _prime_post_caches( $ids, $terms = true, $meta = true ) {
	$GLOBALS['ht_test_primed'] = $ids;
}
function add_meta_box() {}

class WP_Query {
	public $posts = array();
	public $found_posts = 0;
	public function __construct( $args = array() ) {
		$types  = isset( $args['post_type'] ) ? (array) $args['post_type'] : array( 'post' );
		$limit  = isset( $args['posts_per_page'] ) ? (int) $args['posts_per_page'] : 10;
		$all    = array();
		foreach ( $GLOBALS['ht_test_posts'] as $pid => $row ) {
			$pt = isset( $row['post_type'] ) ? $row['post_type'] : 'post';
			$st = isset( $row['post_status'] ) ? $row['post_status'] : 'publish';
			if ( 'publish' === $st && in_array( $pt, $types, true ) ) {
				$all[] = $pid;
			}
		}
		if ( isset( $GLOBALS['ht_test_fake_total'] ) && $GLOBALS['ht_test_fake_total'] > count( $all ) ) {
			$this->found_posts = (int) $GLOBALS['ht_test_fake_total'];
		} else {
			$this->found_posts = count( $all );
		}
		$this->posts = array_slice( $all, 0, $limit );
	}
}

class Horsetools_Test_Wpdb {
	public $postmeta = 'wp_postmeta';
	public function get_col( $sql ) {
		$ids = array();
		foreach ( $GLOBALS['ht_test_meta'] as $pid => $meta ) {
			if ( isset( $meta['_ht_faq_ld'] ) || isset( $meta['_ht_faq_stamp'] ) ) {
				$ids[] = $pid;
			}
		}
		return $ids;
	}
	public function query( $sql ) {
		$deleted = 0;
		foreach ( $GLOBALS['ht_test_meta'] as $pid => &$meta ) {
			foreach ( array( '_ht_faq_ld', '_ht_faq_stamp' ) as $k ) {
				if ( isset( $meta[ $k ] ) ) {
					unset( $meta[ $k ] );
					$deleted++;
				}
			}
		}
		return $deleted;
	}
}
$GLOBALS['wpdb'] = new Horsetools_Test_Wpdb();

require_once dirname( __DIR__ ) . '/inc/css.php';
require_once dirname( __DIR__ ) . '/inc/sanitize.php';
require_once dirname( __DIR__ ) . '/inc/shortcodes-lib.php';
require_once dirname( __DIR__ ) . '/inc/faq-schema.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) {
		$pass++;
		echo "  ok    $what\n";
	} else {
		$fail++;
		echo "  FAIL  $what\n        want: " . var_export( $want, true ) . "\n        got : " . var_export( $got, true ) . "\n";
	}
}
function ok( $cond, $what ) {
	eq( (bool) $cond, true, $what );
}

function reset_state( $main_opts = array() ) {
	$GLOBALS['ht_test_opts']        = array( 'main' => $main_opts );
	$GLOBALS['ht_test_posts']       = array();
	$GLOBALS['ht_test_meta']        = array();
	$GLOBALS['ht_test_cats']        = array();
	$GLOBALS['ht_test_permalinks']  = array();
	$GLOBALS['ht_test_filters']     = array();
	$GLOBALS['ht_test_can']         = true;
	$GLOBALS['ht_test_nonce_ok']    = true;
	$GLOBALS['ht_test_singular']    = true;
	$GLOBALS['ht_test_current_id']  = 0;
	$GLOBALS['ht_test_embed']       = false;
	$GLOBALS['ht_test_pw_required'] = array();
	$GLOBALS['ht_test_cache_del']   = array();
	$GLOBALS['ht_test_primed']      = array();
	$GLOBALS['ht_test_json_resp']   = null;
	$GLOBALS['ht_test_fail_encode'] = false;
	unset( $GLOBALS['ht_test_fake_total'] );
}

/* -------------------------------------------------------------------------
 * 1. Unicode, Accent Folding, Entities & Key Matching (G1)
 * ---------------------------------------------------------------------- */
echo "\n1. Unicode, Accent Folding, Entities & Key Matching (G1)\n";
reset_state();
$keys = horsetools_faq_keys();
ok( '' !== $keys, 'default horsetools_faq_keys() is non-empty' );
eq( horsetools_faq_fold( "Ca\xCC\x82u ho\xCC\x89i thu\xCC\x9Bo\xCC\x9B\xCC\x80ng ga\xCC\xA3\xCC\x86p" ), 'cau hoi thuong gap', 'NFD Vietnamese folds identically without intl' );
eq( horsetools_faq_fold( 'C&acirc;u h&#7887;i&nbsp;th&#432;&#7901;ng g&#7863;p' ), 'cau hoi thuong gap', 'HTML entities and &nbsp; fold cleanly' );
eq( horsetools_faq_fold( "H\xE1\xBB\x8Fi \xC4\x91\xC3\xA1p" ), 'hoi dap', 'NFC Vietnamese with đ folds cleanly' );

$variants = array(
	'<h2>Frequently Asked Questions</h2><h3>Q1?</h3><p>A1.</p>' => 'English Frequently Asked Questions',
	'<h2>Common Questions</h2><h3>Q1?</h3><p>A1.</p>'            => 'English Common Questions',
	'<h2>Questions &amp; Answers</h2><h3>Q1?</h3><p>A1.</p>'     => 'English Questions & Answers',
	'<h2>Questions and Answers</h2><h3>Q1?</h3><p>A1.</p>'       => 'English Questions and Answers',
	'<h2>Cau hoi thuong gap</h2><h3>Q1?</h3><p>A1.</p>'          => 'Unaccented Vietnamese Cau hoi thuong gap',
	"<h2>Ca\xCC\x82u ho\xCC\x89i thu\xCC\x9Bo\xCC\x9B\xCC\x80ng ga\xCC\xA3\xCC\x86p</h2><h3>Q1?</h3><p>A1.</p>" => 'NFD Vietnamese heading',
	'<h2>C&acirc;u h&#7887;i&nbsp;th&#432;&#7901;ng g&#7863;p</h2><h3>Q1?</h3><p>A1.</p>' => 'Entity-encoded + &nbsp; Vietnamese heading',
	'<h2>Q & A</h2><h3>Q1?</h3><p>A1.</p>'                       => 'Q & A with spaces',
	'<h2>Q/A</h2><h3>Q1?</h3><p>A1.</p>'                         => 'Q/A with slash',
	'<h2>Q&#038;A</h2><h3>Q1?</h3><p>A1.</p>'                    => 'Q&#038;A entity',
	'<h2>F.A.Q.</h2><h3>Q1?</h3><p>A1.</p>'                      => 'F.A.Q. dotted',
	'<h2>Hỏi &amp; Đáp</h2><h3>Q1?</h3><p>A1.</p>'               => 'Hỏi & Đáp',
);
foreach ( $variants as $html => $label ) {
	$p = horsetools_faq_extract( $html, $keys );
	eq( count( $p ), 1, "extracts 1 pair for $label" );
}

reset_state( array( 'faq-keys' => ' , , ' ) );
eq( horsetools_faq_keys(), '', 'comma/whitespace-only faq-keys returns empty string' );
eq( horsetools_faq_extract( '<h2>FAQ</h2><h3>Q?</h3><p>A</p>', '' ), array(), 'empty $keys returns empty array safely' );

/* -------------------------------------------------------------------------
 * 2. False-Positive & Noise Guards (G2)
 * ---------------------------------------------------------------------- */
echo "\n2. False-Positive & Noise Guards (G2)\n";
reset_state( array( 'faq-keys' => 'thường gặp, giải đáp, thắc mắc, FAQ' ) );
$custom_keys = horsetools_faq_keys();
$false_pos = array(
	'<h2>Các lỗi thường gặp</h2><h3>1. Mất kết nối</h3><p>Kiểm tra mạng.</p>' => 'Các lỗi thường gặp',
	'<h2>Những sai lầm thường gặp</h2><h3>1. Chọn sai</h3><p>Giải thích.</p>' => 'Những sai lầm thường gặp',
	'<h2>Sự cố thường gặp</h2><h3>1. Treo máy</h3><p>Khởi động lại.</p>'      => 'Sự cố thường gặp',
	'<h2>Vấn đề thường gặp</h2><h3>1. Chậm</h3><p>Tối ưu.</p>'               => 'Vấn đề thường gặp',
	'<h2>Bệnh thường gặp ở mèo</h2><h3>1. Cảm</h3><p>Đi khám.</p>'           => 'Bệnh thường gặp',
	'<h2>Bạn có thắc mắc?</h2><h3>Gọi ngay</h3><p>0909123456</p>'             => 'Bạn có thắc mắc?',
	'<h2>Giải đáp bài tập toán</h2><h3>Câu 1</h3><p>Đáp án A</p>'            => 'Giải đáp bài tập',
);
foreach ( $false_pos as $html => $label ) {
	eq( horsetools_faq_extract( $html, $custom_keys ), array(), "rejects non-FAQ heading: $label" );
}

reset_state();
$keys = horsetools_faq_keys();
$noise_html = '<h2>Câu hỏi thường gặp</h2>'
	. '<h3>Q1?</h3><p>A1.</p>'
	. '<h3>Lời kết</h3><p>Cảm ơn đã đọc.</p>'
	. '<h3>Liên hệ</h3><p>0909000111</p>'
	. '<h3>Xem thêm</h3><p>Bài khác</p>'
	. '<h3>Tài liệu tham khảo</h3><p>Nguồn A</p>'
	. '<h3>Bài viết liên quan</h3><p>Link B</p>';
$p = horsetools_faq_extract( $noise_html, $keys );
eq( $p, array( array( 'Q1?', 'A1.' ) ), 'editorial/closing subheadings are not promoted and do not bleed into Q1' );

$noise_tags = '<h2>FAQ</h2><h3>Q1?</h3><p>Real answer.</p>'
	. '<figure><img src="a.jpg"><figcaption>Caption noise</figcaption></figure>'
	. '<template><h3>Template Q?</h3><p>Template A</p></template>'
	. '<noscript><h3>Noscript Q?</h3><p>Noscript A</p></noscript>'
	. '<svg><text>SVG noise</text></svg>'
	. '<iframe src="x">Fallback</iframe>'
	. '<nav><h3>Nav Q?</h3><p>Nav A</p></nav>'
	. '<form><p>Form noise</p></form>';
$p = horsetools_faq_extract( $noise_tags, $keys );
eq( $p, array( array( 'Q1?', 'Real answer.' ) ), 'skips figcaption, template, noscript, svg, iframe, nav, form' );

/* -------------------------------------------------------------------------
 * 3. Missed FAQ HTML & Builder Patterns (G3)
 * ---------------------------------------------------------------------- */
echo "\n3. Missed FAQ HTML & Builder Patterns (G3)\n";
reset_state();
$keys = horsetools_faq_keys();

// <details><summary> under <h3> + bare text + trailing outro <p>.
$det_h3 = '<h3>FAQ</h3>'
	. '<details><summary>Q1?</summary><p>Answer 1.</p></details>'
	. '<details><summary>Q2?</summary>Bare answer 2.</details>'
	. '<p>Outro paragraph after accordion.</p>';
eq(
	horsetools_faq_extract( $det_h3, $keys ),
	array( array( 'Q1?', 'Answer 1.' ), array( 'Q2?', 'Bare answer 2.' ) ),
	'<details><summary> works under <h3>, captures bare text, stops outro bleed'
);

// <dl><dt><dd>
$dl = '<h2>FAQ</h2><dl><dt>What is X?</dt><dd>X is a tool.</dd><dt>Is it fast?</dt><dd><p>Very fast.</p><p>Under 10ms.</p></dd></dl>';
eq(
	horsetools_faq_extract( $dl, $keys ),
	array( array( 'What is X?', 'X is a tool.' ), array( 'Is it fast?', 'Very fast. Under 10ms.' ) ),
	'<dl><dt><dd> extracts questions and multi-<p> answers cleanly'
);

// Direct text inside <div>, <td>, <th>, plus <br> spacing, plus <li><h3>
$div_td = '<h2>FAQ</h2>'
	. '<div class="wrap"><h3>Q1?</h3><div class="ans">Answer inside div<br>second line.</div></div>'
	. '<table><tr><th><h3>Q2?</h3></th><td>Answer inside td</td></tr></table>'
	. '<ul><li><h3>Q3?</h3><p>Answer in li</p></li></ul>';
eq(
	horsetools_faq_extract( $div_td, $keys ),
	array(
		array( 'Q1?', 'Answer inside div second line.' ),
		array( 'Q2?', 'Answer inside td' ),
		array( 'Q3?', 'Answer in li' ),
	),
	'captures direct text in div/td, inserts space on <br>, recurses into <li><h3>'
);

// Bold/strong Q&A paragraphs + <p><strong>FAQ</strong></p> section heading
$bold_p = '<p><strong>Câu hỏi thường gặp</strong></p>'
	. '<p><strong>1. Giao hàng mất bao lâu?</strong></p><p>Từ 1 đến 3 ngày.</p>'
	. '<p><b>Hỏi: Có bảo hành không?</b><br>Đáp: Bảo hành 12 tháng.</p>';
eq(
	horsetools_faq_extract( $bold_p, $keys ),
	array(
		array( '1. Giao hàng mất bao lâu?', 'Từ 1 đến 3 ngày.' ),
		array( 'Hỏi: Có bảo hành không?', 'Đáp: Bảo hành 12 tháng.' ),
	),
	'supports <p><strong> section heading, bold-only Q paragraphs, and <b>Hỏi:</b><br>Đáp:'
);

// Dedicated FAQ page post_title fallback + backwards-compat 1/2-arg callers
$title_only = '<p>Welcome to our help center.</p><h2>How do I reset my password?</h2><p>Click Forgot Password.</p><h2>How to contact support?</h2><p>Email support@example.com.</p>';
eq( horsetools_faq_extract( $title_only, $keys ), array(), '2-arg call without matching in-content heading returns empty' );
eq(
	horsetools_faq_extract( $title_only, $keys, 'Frequently Asked Questions' ),
	array(
		array( 'How do I reset my password?', 'Click Forgot Password.' ),
		array( 'How to contact support?', 'Email support@example.com.' ),
	),
	'3-arg post_title fallback extracts dedicated FAQ page'
);
eq( count( horsetools_faq_extract( '<h2>FAQ</h2><h3>Q?</h3><p>A</p>' ) ), 1, '1-arg backwards-compatible call uses default keys' );

// Shortcodes: [ht-accordion][ht-item], [ht-toggle], >80-char shortcode, and [1] / [section 2] preservation
$sc = '<h2>FAQ</h2>'
	. '[ht-accordion]'
	. '[ht-item title="Double quoted Q1?"]Answer one.[/ht-item]'
	. "[ht-item title='Single quoted Q2?']Answer two.[/ht-item]"
	. '[/ht-accordion]'
	. '[ht-toggle title="Toggle Q3?"]See [section 2] for details [1].[/ht-toggle]'
	. '[vc_tta_section title="Builder Q4?" tab_id="very-long-tab-id-exceeding-eighty-characters-easily-to-verify-no-80-char-cap-bug"]'
	. '[my_long_sc attr="another-very-long-attribute-value-that-is-well-over-eighty-characters-in-length"]Builder answer.[/my_long_sc]'
	. '[/vc_tta_section]';
eq(
	horsetools_faq_extract( $sc, $keys ),
	array(
		array( 'Double quoted Q1?', 'Answer one.' ),
		array( 'Single quoted Q2?', 'Answer two.' ),
		array( 'Toggle Q3?', 'See [section 2] for details [1].' ),
		array( 'Builder Q4?', 'Builder answer.' ),
	),
	'extracts [ht-item] (single/double quotes), [ht-toggle], >80-char shortcodes, preserves [1] and [section 2]'
);

// inc/shortcodes-lib.php [ht-accordion faq="1"] single quote + JSON_UNESCAPED_UNICODE
$sc_out = do_shortcode( "[ht-accordion faq=\"1\"][ht-item title='Giá bao nhiêu?']100.000đ.[/ht-item][/ht-accordion]" );
ok( false !== strpos( $sc_out, 'Giá bao nhiêu?' ), 'horsetools_sc_accordion emits JSON_UNESCAPED_UNICODE for single-quoted title' );
ok( false !== strpos( $sc_out, '100.000đ.' ), 'horsetools_sc_accordion keeps Vietnamese answer unescaped' );

/* -------------------------------------------------------------------------
 * 4. Section & Question State Machine (G4)
 * ---------------------------------------------------------------------- */
echo "\n4. Section & Question State Machine (G4)\n";
reset_state();
$keys = horsetools_faq_keys();

$multi_sec = '<h2>Giới thiệu FAQ</h2><p>Intro text only.</p>'
	. '<h2>Shipping FAQ</h2><h3>Q1?</h3><p>A1</p>'
	. '<h2>Unrelated Section</h2><p>Body text.</p>'
	. '<h2>Billing FAQ</h2><h3>Q2?</h3><p>A2</p>';
eq(
	horsetools_faq_extract( $multi_sec, $keys ),
	array( array( 'Q1?', 'A1' ), array( 'Q2?', 'A2' ) ),
	'continues scanning after section closes and handles multiple FAQ sections'
);

$subhead = '<h2>FAQ</h2>'
	. '<h3>Wrapper Group</h3><h4>Q1?</h4><p>A1</p>'
	. '<h3>How to install?</h3><p>Follow steps:</p><h4>Step 1</h4><p>Download file.</p>'
	. '<h3>How to install?</h3><p>Duplicate responsive block should be ignored.</p>';
eq(
	horsetools_faq_extract( $subhead, $keys ),
	array(
		array( 'Q1?', 'A1' ),
		array( 'How to install?', 'Follow steps: Step 1 Download file.' ),
	),
	'drops empty wrapper heading, keeps deeper subheading inside answer, deduplicates duplicate Q'
);

/* -------------------------------------------------------------------------
 * 5. Foreign Conflict Detection (G5)
 * ---------------------------------------------------------------------- */
echo "\n5. Foreign Conflict Detection (G5)\n";
reset_state();
$GLOBALS['ht_test_posts'][10] = array( 'post_content' => '<h2>FAQ</h2><h3>Q?</h3><p>A</p>' );
$GLOBALS['ht_test_meta'][10]  = array( 'rank_math_schema_FAQPage' => array( '{"@type":"FAQPage"}' ) );
ok( horsetools_faq_foreign( 10 ), 'detects rank_math_schema_* FAQPage meta' );

$GLOBALS['ht_test_posts'][11] = array( 'post_content' => '<!-- wp:yoast/faq-block -->' );
ok( horsetools_faq_foreign( 11 ), 'detects wp:yoast/faq-block' );

$GLOBALS['ht_test_posts'][12] = array( 'post_content' => '<!-- wp:rank-math/faq-block -->' );
ok( horsetools_faq_foreign( 12 ), 'detects wp:rank-math/faq-block' );

$GLOBALS['ht_test_posts'][13] = array( 'post_content' => '<!-- wp:aioseo/faq -->' );
ok( horsetools_faq_foreign( 13 ), 'detects wp:aioseo/faq' );

$GLOBALS['ht_test_posts'][14] = array( 'post_content' => '<!-- wp:uagb/faq -->' );
ok( horsetools_faq_foreign( 14 ), 'detects wp:uagb/faq' );

$GLOBALS['ht_test_posts'][15] = array( 'post_content' => '<h2>FAQ</h2><h3>Q?</h3><p>A</p>' );
$GLOBALS['ht_test_meta'][15]  = array( '_seopress_pro_rich_snippets_type' => array( 'faq' ) );
ok( horsetools_faq_foreign( 15 ), 'detects _seopress_pro_rich_snippets_type = faq' );

$GLOBALS['ht_test_posts'][16] = array( 'post_content' => '[ht-accordion faq="1"][ht-item title="Q?"]A[/ht-item][/ht-accordion]' );
ok( horsetools_faq_foreign( 16 ), 'detects [ht-accordion faq="1"]' );

$GLOBALS['ht_test_posts'][17] = array( 'post_content' => '<div itemscope itemtype="https://schema.org/FAQPage"></div>' );
ok( horsetools_faq_foreign( 17 ), 'detects HTML Microdata itemtype=schema.org/FAQPage' );

$GLOBALS['ht_test_posts'][18] = array( 'post_content' => '<h2>FAQ</h2><h3>How to add schema?</h3><p>Paste &lt;script type="application/ld+json"&gt;{"@type":"FAQPage"}&lt;/script&gt; or &lt;div itemtype="https://schema.org/FAQPage"&gt;.</p>' );
ok( ! horsetools_faq_foreign( 18 ), 'does NOT false-positive on escaped &lt;script&gt;/&lt;div&gt; tutorial prose' );

$GLOBALS['ht_test_posts'][19] = array( 'post_content' => '[ht-accordion][ht-item title="Q?"]A[/ht-item][/ht-accordion]' );
ok( ! horsetools_faq_foreign( 19 ), 'does NOT suppress plain [ht-accordion] without faq attribute' );

/* -------------------------------------------------------------------------
 * 6. Cache, Truncation, Quote Round-Trip, Scope & Privacy (G6)
 * ---------------------------------------------------------------------- */
echo "\n6. Cache, Truncation, Quote Round-Trip, Scope & Privacy (G6)\n";
reset_state( array( 'faq-schema1' => '1', 'faq-min' => '2', 'faq-maxlen' => '80' ) );
$long_ans = 'Đây là một câu trả lời rất dài với nhiều chữ tiếng Việt có dấu để kiểm tra cắt chuỗi theo ranh giới từ không bị cắt ngang giữa chữ.';
$GLOBALS['ht_test_posts'][20] = array(
	'post_type'    => 'post',
	'post_status'  => 'publish',
	'post_title'   => 'Test FAQ Post',
	'post_content' => '<h2>FAQ</h2><h3>Câu "1" là gì?</h3><p>' . $long_ans . '</p><h3>Câu 2?</h3><p>Có dấu "ngoặc kép" và </script> thẻ.</p>',
	'modified'     => '1700000100',
);
$json = horsetools_faq_json( 20 );
$dec  = json_decode( $json, true );
ok( is_array( $dec ) && 'FAQPage' === $dec['@type'], 'JSON-LD decodes cleanly' );
eq( count( $dec['mainEntity'] ), 2, 'contains 2 Question items' );
ok( false === strpos( $json, '</script>' ), 'slashes escaped so </script> cannot break out' );
$t_ans = $dec['mainEntity'][0]['acceptedAnswer']['text'];
ok( mb_strlen( $t_ans, 'UTF-8' ) <= 81 && '…' === mb_substr( $t_ans, -1, 1, 'UTF-8' ), 'word-boundary UTF-8 truncation ends with ellipsis' );
ok( false === strpos( $t_ans, 'kiể…' ) && false === strpos( $t_ans, 'chữ…' ), 'did not split mid-word arbitrarily' );

// Settings change invalidates cache even with same modified time.
$GLOBALS['ht_test_opts']['main']['faq-min'] = '3';
eq( horsetools_faq_json( 20 ), '', 'changing faq-min invalidates cached _ht_faq_ld immediately' );
$GLOBALS['ht_test_opts']['main']['faq-min'] = '2';
$GLOBALS['ht_test_permalinks'][20] = 'https://example.com/renamed-slug/';
$json2 = horsetools_faq_json( 20 );
$dec2  = json_decode( $json2, true );
eq( $dec2['mainEntityOfPage'], 'https://example.com/renamed-slug/', 'changing permalink invalidates cache' );

// Permalink false guard
$GLOBALS['ht_test_permalinks'][20] = false;
$json3 = horsetools_faq_json( 20, true );
$dec3  = json_decode( $json3, true );
ok( ! isset( $dec3['mainEntityOfPage'] ), 'omits mainEntityOfPage when get_permalink() returns false' );

// Foreign conflict bypasses stale cache
$GLOBALS['ht_test_meta'][20]['rank_math_schema_faq'] = array( 'FAQPage' );
eq( horsetools_faq_json( 20 ), '', 'foreign conflict suppresses schema even when _ht_faq_ld was already cached' );
unset( $GLOBALS['ht_test_meta'][20]['rank_math_schema_faq'] );

// wp_json_encode() false guard
$GLOBALS['ht_test_fail_encode'] = true;
$bad_json = horsetools_faq_json( 20, true );
eq( $bad_json, '', 'wp_json_encode() returning false produces empty string, not false' );
$GLOBALS['ht_test_fail_encode'] = false;
horsetools_faq_clear( 20 );
ok( ! isset( $GLOBALS['ht_test_meta'][20]['_ht_faq_stamp'] ), 'horsetools_faq_clear() deletes _ht_faq_stamp' );

// Malformed/unclosed HTML and invalid UTF-8 safety
$malformed = '<h2>FAQ</h2><h3>Unclosed heading<p>Answer one.<h3>Q2?</h3><p>Answer two.';
eq( count( horsetools_faq_extract( $malformed, $keys ) ), 2, 'handles malformed/unclosed HTML cleanly' );
$bad_utf8 = "<h2>FAQ</h2><h3>Q1?</h3><p>Bad byte \xFF here.</p>";
$bad_res  = horsetools_faq_extract( $bad_utf8, $keys );
ok( is_array( $bad_res ), 'handles invalid UTF-8 without null preg_replace crash' );

// Scope: faq-pages + faq-cats
$GLOBALS['ht_test_opts']['main']['faq-pages'] = '1';
$GLOBALS['ht_test_opts']['main']['faq-cats']  = '5, 9';
$GLOBALS['ht_test_posts'][21] = array( 'post_type' => 'page', 'post_content' => '<h2>FAQ</h2><h3>Q1?</h3><p>A1</p><h3>Q2?</h3><p>A2</p>' );
$GLOBALS['ht_test_posts'][22] = array( 'post_type' => 'post', 'post_content' => '<h2>FAQ</h2><h3>Q1?</h3><p>A1</p><h3>Q2?</h3><p>A2</p>' );
$GLOBALS['ht_test_cats'][22]  = array( 3 );
$GLOBALS['ht_test_cats'][20]  = array( 5 );
ok( horsetools_faq_in_scope( 21 ), 'page stays in scope even when faq-cats is set' );
ok( ! horsetools_faq_in_scope( 22 ), 'post outside faq-cats is excluded' );
ok( horsetools_faq_in_scope( 20 ), 'post inside faq-cats is included' );

// horsetools_faq_head privacy guards
$GLOBALS['ht_test_cats'][20] = array( 5 );
$GLOBALS['ht_test_current_id'] = 20;
$GLOBALS['ht_test_pw_required'][20] = true;
ob_start();
horsetools_faq_head();
eq( ob_get_clean(), '', 'post_password_required() suppresses wp_head JSON-LD' );

$GLOBALS['ht_test_pw_required'][20] = false;
$GLOBALS['ht_test_embed'] = true;
ob_start();
horsetools_faq_head();
eq( ob_get_clean(), '', 'is_embed() suppresses wp_head JSON-LD' );

$GLOBALS['ht_test_embed'] = false;
ob_start();
horsetools_faq_head();
$head_out = ob_get_clean();
ok( 0 === strpos( $head_out, '<script type="application/ld+json">' ), 'horsetools_faq_head emits JSON-LD for valid public post' );

// Meta box respects horsetools_faq_in_scope()
ob_start();
horsetools_faq_meta_render( (object) array( 'ID' => 22, 'post_content' => $GLOBALS['ht_test_posts'][22]['post_content'] ) );
eq( ob_get_clean(), '', 'horsetools_faq_meta_render stays silent for out-of-scope post' );

/* -------------------------------------------------------------------------
 * 7. Sanitization, AJAX Security, Flush Cache & Repo Checks (G7)
 * ---------------------------------------------------------------------- */
echo "\n7. Sanitization, AJAX Security, Flush Cache & Repo Checks (G7)\n";
eq( horsetools_sanitize_field( 'faq-min', '0' ), '1', 'faq-min clamps low bound to 1' );
eq( horsetools_sanitize_field( 'faq-min', '99' ), '20', 'faq-min clamps high bound to 20' );
eq( horsetools_sanitize_field( 'faq-min', '' ), '', 'faq-min preserves empty default' );
eq( horsetools_sanitize_field( 'faq-maxlen', '10' ), '80', 'faq-maxlen clamps low bound to 80' );
eq( horsetools_sanitize_field( 'faq-maxlen', '9999' ), '5000', 'faq-maxlen clamps high bound to 5000' );
eq( horsetools_sanitize_field( 'faq-maxlen', '' ), '', 'faq-maxlen preserves empty default' );

// Capability check runs BEFORE nonce check and returns 403 forbidden
reset_state( array( 'faq-schema1' => '1' ) );
$GLOBALS['ht_test_can']      = false;
$GLOBALS['ht_test_nonce_ok'] = false;
try {
	horsetools_faq_scan_ajax();
} catch ( Horsetools_Test_Ajax_Exit $e ) {}
eq( $GLOBALS['ht_test_json_resp'], array( 'success' => false, 'data' => 'forbidden', 'status' => 403 ), 'scan AJAX enforces manage_options before nonce with 403' );

$GLOBALS['ht_test_json_resp'] = null;
try {
	horsetools_faq_flush_ajax();
} catch ( Horsetools_Test_Ajax_Exit $e ) {}
eq( $GLOBALS['ht_test_json_resp'], array( 'success' => false, 'data' => 'forbidden', 'status' => 403 ), 'flush AJAX enforces manage_options before nonce with 403' );

// Scan AJAX with empty keys, truncated flag, cache priming, null edit link
reset_state( array( 'faq-keys' => ' , ' ) );
$GLOBALS['ht_test_posts'][30] = array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'T', 'post_content' => '<h2>FAQ</h2>', 'no_edit_link' => true );
$GLOBALS['ht_test_fake_total'] = 2500;
try {
	horsetools_faq_scan_ajax();
} catch ( Horsetools_Test_Ajax_Exit $e ) {}
ok( true === $GLOBALS['ht_test_json_resp']['success'], 'scan AJAX succeeds with empty faq-keys' );
eq( $GLOBALS['ht_test_json_resp']['data']['truncated'], true, 'scan AJAX reports truncated when found_posts > scanned' );
eq( $GLOBALS['ht_test_json_resp']['data']['total'], 2500, 'scan AJAX reports total found_posts' );
eq( $GLOBALS['ht_test_primed'], array( 30 ), 'scan AJAX primes post caches' );

// Scan AJAX with default keys + null edit link + prose /faq not counted as unusual
reset_state();
$GLOBALS['ht_test_posts'][31] = array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'Blog', 'post_content' => '<p>See https://example.com/faq for info.</p>' );
$GLOBALS['ht_test_posts'][32] = array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'One Q', 'post_content' => '<h2>FAQ</h2><h3>Only 1?</h3><p>Yes.</p>', 'no_edit_link' => true );
try {
	horsetools_faq_scan_ajax();
} catch ( Horsetools_Test_Ajax_Exit $e ) {}
eq( $GLOBALS['ht_test_json_resp']['data']['noneN'], 0, 'URL mentioning /faq is not falsely flagged as unusual structure' );
eq( $GLOBALS['ht_test_json_resp']['data']['fewN'], 1, '1-question FAQ post listed under few' );
eq( $GLOBALS['ht_test_json_resp']['data']['few'][0]['link'], '', 'null edit link is normalized to empty string' );

// Flush AJAX clears object cache
$GLOBALS['ht_test_meta'][31]['_ht_faq_ld']    = '{}';
$GLOBALS['ht_test_meta'][32]['_ht_faq_stamp'] = '123';
try {
	horsetools_faq_flush_ajax();
} catch ( Horsetools_Test_Ajax_Exit $e ) {}
eq( $GLOBALS['ht_test_json_resp']['data']['rows'], 2, 'flush AJAX deletes cached postmeta rows' );
ok( in_array( array( 31, 'post_meta' ), $GLOBALS['ht_test_cache_del'], true ), 'flush AJAX clears WP post_meta object cache' );

// Structural checks on seo-faq.php, modal/modal.php, uninstall.php
$seo_faq   = file_get_contents( dirname( __DIR__ ) . '/main/section/seo-faq.php' );
$modal_php = file_get_contents( dirname( __DIR__ ) . '/modal/modal.php' );
$uninst    = file_get_contents( dirname( __DIR__ ) . '/uninstall.php' );
ok( false === strpos( $seo_faq, 'innerHTML' ), 'seo-faq.php has zero innerHTML XSS sinks' );
ok( false !== strpos( $seo_faq, 'textContent' ) && false !== strpos( $seo_faq, 'safeUrl' ), 'seo-faq.php builds DOM with textContent and URL scheme validation' );
ok( false !== strpos( $modal_php, 'wp_doing_ajax' ) && false !== strpos( $modal_php, 'inc/faq-schema.php' ), 'modal/modal.php loads inc/faq-schema.php on wp_doing_ajax()' );
ok( false !== strpos( $uninst, '_ht_faq_ld' ) && false !== strpos( $uninst, '_ht_faq_stamp' ), 'uninstall.php cleans up _ht_faq_ld and _ht_faq_stamp' );

/* -------------------------------------------------------------------------
 * 8. Iteration 2 Regression Tests (Reviewer + Challenger Defects 1-13)
 * ---------------------------------------------------------------------- */
echo "\n8. Iteration 2 Regression Tests (Defects 1-13)\n";
reset_state();
$keys = horsetools_faq_keys();

// Defect 1: No O(N^4) PCRE backtracking on long whitespace after "questions".
$t0     = microtime( true );
$ws_str = 'questions' . str_repeat( ' ', 2000 ) . 'xyz';
$ws_hit = horsetools_faq_matches_keys( $ws_str, $keys );
$ws_err = preg_last_error();
$ws_ms  = ( microtime( true ) - $t0 ) * 1000;
eq( $ws_hit, false, 'D1: long whitespace after "questions" does not match' );
eq( $ws_err, 0, 'D1: horsetools_faq_matches_keys() has zero PCRE backtrack error' );
ok( $ws_ms < 50, 'D1: horsetools_faq_matches_keys() completes in <50ms (' . round( $ws_ms, 2 ) . 'ms)' );
$raw_match = @preg_match( $keys, $ws_str );
eq( $raw_match, 0, 'D1: compiled $keys regex itself does not match' );
eq( preg_last_error(), 0, 'D1: compiled $keys regex has zero PCRE backtrack error even on raw whitespace' );

// Defect 2: >32 KB JSON-LD <script> block with FAQPage at the end & unclosed <script>.
$big_ld = '<script type="application/ld+json">{"@context":"https://schema.org","@graph":[{"@type":"WebPage","description":"'
	. str_repeat( 'a', 33000 )
	. '"},{"@type":"FAQPage","mainEntity":[]}]}</script>';
$GLOBALS['ht_test_posts'][40] = array( 'post_content' => $big_ld );
ok( horsetools_faq_foreign( 40 ), 'D2: detects FAQPage inside >32KB JSON-LD <script> without PREG_JIT_STACKLIMIT_ERROR' );
$GLOBALS['ht_test_posts'][41] = array( 'post_content' => '<script type="application/ld+json">{"@type":"FAQPage"}' );
ok( horsetools_faq_foreign( 41 ), 'D2: detects FAQPage inside unclosed JSON-LD <script>' );
$GLOBALS['ht_test_posts'][42] = array( 'post_content' => '<script type="application/ld+json">{"@type":"Article"}</script><p>FAQPage mentioned in text</p>' );
ok( ! horsetools_faq_foreign( 42 ), 'D2: does not false-positive when FAQPage is only outside <script>' );

// Defect 3: <p><strong>Lưu ý:</strong></p> inside an active answer does not drop subsequent Q&As,
// while <p><strong>Lời kết</strong></p> still closes the section cleanly.
$note_mid = '<h2>FAQ</h2><h3>Q1?</h3><p>A1.</p><p><strong>Lưu ý:</strong></p><p>Chú ý nhỏ.</p><h3>Q2?</h3><p>A2.</p>';
eq(
	horsetools_faq_extract( $note_mid, $keys ),
	array(
		array( 'Q1?', 'A1. Lưu ý: Chú ý nhỏ.' ),
		array( 'Q2?', 'A2.' ),
	),
	'D3: <p><strong>Lưu ý:</strong></p> inside active answer keeps Q2?'
);
$note_end = '<h2>FAQ</h2><h3>Q1?</h3><p>A1.</p><p><strong>Lời kết</strong></p><p>Cảm ơn.</p>';
eq(
	horsetools_faq_extract( $note_end, $keys ),
	array( array( 'Q1?', 'A1.' ) ),
	'D3: <p><strong>Lời kết</strong></p> still closes FAQ section cleanly'
);

// Defect 5: Bold <p><strong>1. Question?</strong></p> under <h3> group wrapper.
$bold_under_h3 = '<h2>FAQ</h2><h3>Nhóm 1: Thanh toán</h3><p><strong>1. Question?</strong></p><p>Answer 1</p><p><strong>2. Question two?</strong></p><p>Answer 2</p>';
eq(
	horsetools_faq_extract( $bold_under_h3, $keys ),
	array(
		array( '1. Question?', 'Answer 1' ),
		array( '2. Question two?', 'Answer 2' ),
	),
	'D5: bold <p><strong> questions under <h3> group wrapper are promoted cleanly'
);
$bold_under_h3_intro = '<h2>FAQ</h2><h3>Nhóm 1: Thanh toán</h3><p>Hướng dẫn thanh toán:</p><p><strong>1. Question?</strong></p><p>Answer 1</p>';
eq(
	horsetools_faq_extract( $bold_under_h3_intro, $keys ),
	array( array( '1. Question?', 'Answer 1' ) ),
	'D5: <h3> group wrapper with intro <p> is dropped when bold question follows'
);

// Defect 6: <p><strong>FAQ</strong></p> bold section opener followed by <h2> questions (and trailing noise/section exit).
$bh_h2 = '<p><strong>Câu hỏi thường gặp</strong></p><h2>Q1?</h2><p>A1</p><h2>Q2?</h2><p>A2</p><h2>Lời kết</h2><p>Cảm ơn</p>';
eq(
	horsetools_faq_extract( $bh_h2, $keys ),
	array(
		array( 'Q1?', 'A1' ),
		array( 'Q2?', 'A2' ),
	),
	'D6: <p><strong>FAQ</strong></p> captures <h2> questions and stops on <h2>Lời kết</h2>'
);
$bh_bh_then_h2 = '<p><strong>Câu hỏi thường gặp</strong></p><p><strong>1. Q1?</strong></p><p>A1</p><h2>About Us</h2><p>Company info</p>';
eq(
	horsetools_faq_extract( $bh_bh_then_h2, $keys ),
	array( array( '1. Q1?', 'A1' ) ),
	'D6: bold Q&A section exits cleanly on unrelated <h2>About Us</h2>'
);

// Defect 7: Distinguish <h3> group wrapper + intro <p> + <h4> Qs (P3.4) vs <h3>Q1?</h3><h4>Substep A</h4> (P3.2).
$p34 = '<h2>FAQ</h2><h3>Nhóm 1: Thanh toán</h3><p>Dưới đây là các câu hỏi về thanh toán:</p><h4>Câu hỏi 1?</h4><p>Trả lời 1</p><h4>Câu hỏi 2?</h4><p>Trả lời 2</p>';
eq(
	horsetools_faq_extract( $p34, $keys ),
	array(
		array( 'Câu hỏi 1?', 'Trả lời 1' ),
		array( 'Câu hỏi 2?', 'Trả lời 2' ),
	),
	'D7 (P3.4): <h3> group wrapper with intro <p> promotes <h4> questions'
);
$p32 = '<h2>FAQ</h2><h3>Q1?</h3><h4>Substep A</h4><p>Step A</p><h4>Substep B</h4><p>Step B</p><h3>Q2?</h3><p>A2</p>';
eq(
	horsetools_faq_extract( $p32, $keys ),
	array(
		array( 'Q1?', 'Substep A Step A Substep B Step B' ),
		array( 'Q2?', 'A2' ),
	),
	'D7 (P3.2): <h3>Q1?</h3> followed immediately by <h4>Substep</h4> keeps Q1? active'
);

// Defect 8: <table><tr><th>Q?</th><td>A</td></tr></table> without false-positiving on data tables.
$tbl_faq = '<h2>FAQ</h2><table><tr><th>Q1?</th><td>A1</td></tr><tr><th>Q2?</th><td>A2</td></tr></table>';
eq(
	horsetools_faq_extract( $tbl_faq, $keys ),
	array(
		array( 'Q1?', 'A1' ),
		array( 'Q2?', 'A2' ),
	),
	'D8: extracts <table><tr><th>Q?</th><td>A</td></tr></table>'
);
$tbl_data = '<h2>FAQ</h2><h3>Q1?</h3><p>See table:</p><table><tr><th>Item</th><th>Price</th></tr><tr><th>Widget</th><td>$10</td></tr></table>';
eq(
	horsetools_faq_extract( $tbl_data, $keys ),
	array( array( 'Q1?', 'See table: Item Price Widget $10' ) ),
	'D8: regular data table inside answer is not split into fake questions'
);

// Defect 9: Shortcode & [ht-accordion] fixes.
$sc_data_title = do_shortcode( '[ht-accordion faq="1"][ht-item data-title="WRONG"]Ans[/ht-item][/ht-accordion]' );
ok( false === strpos( $sc_data_title, 'application/ld+json' ), 'D9: [ht-accordion] ignores data-title="WRONG"' );
$sc_spaces_title = do_shortcode( '[ht-accordion faq="1"][ht-item title = "Spaces?"]Ans[/ht-item][/ht-accordion]' );
ok( false !== strpos( $sc_spaces_title, '"name":"Spaces?"' ), 'D9: [ht-accordion] matches title = "Spaces?" with spaces around =' );
$sc_bracket_title = '<h2>FAQ</h2>[ht-accordion][ht-item title="Price [USD]?"]$10[/ht-item][/ht-accordion]';
eq(
	horsetools_faq_extract( $sc_bracket_title, $keys ),
	array( array( 'Price [USD]?', '$10' ) ),
	'D9: [ht-item title="Price [USD]?"] parses inner brackets cleanly'
);
$prose_utf8 = '<h2>FAQ</h2><h3>Encoding?</h3><p>See [1], [section 2], [array][0], [utf-8], and [note].</p>';
eq(
	horsetools_faq_extract( $prose_utf8, $keys ),
	array( array( 'Encoding?', 'See [1], [section 2], [array][0], [utf-8], and [note].' ) ),
	'D9: preserves standalone prose brackets including [utf-8]'
);
$GLOBALS['ht_test_posts'][43] = array( 'post_content' => '[ht-accordion data-faq="1"][ht-item title="Q?"]A[/ht-item][/ht-accordion]' );
ok( ! horsetools_faq_foreign( 43 ), 'D9: horsetools_faq_foreign does not false-positive on data-faq="1"' );

// Defect 10: Invisible Unicode characters U+200B..U+200D, U+FEFF, U+00AD.
$zw_cases = array(
	"<h2>Ca\xCC\x82u\xE2\x80\x8B ho\xCC\x89i thu\xCC\x9Bo\xCC\x9B\xCC\x80ng ga\xCC\xA3\xCC\x86p</h2><h3>Q?</h3><p>A</p>" => 'U+200B zero-width space',
	"<h2>F\xE2\x80\x8BAQ</h2><h3>Q?</h3><p>A</p>" => 'U+200B inside FAQ',
	"<h2>\xEF\xBB\xBFFAQ\xE2\x80\x8D</h2><h3>Q?</h3><p>A</p>" => 'U+FEFF BOM and U+200D ZWJ',
	"<h2>C\xC3\xA2u\xC2\xAD h\xE1\xBB\x8Fi th\xC6\xB0\xE1\xBB\x9Dng g\xE1\xBA\xB7p</h2><h3>Q?</h3><p>A</p>" => 'U+00AD soft hyphen',
);
foreach ( $zw_cases as $zhtml => $zlabel ) {
	eq( count( horsetools_faq_extract( $zhtml, $keys ) ), 1, "D10: handles invisible char ($zlabel)" );
}

// Defect 11: Invalid UTF-8 in faq-keys does not break valid sibling keys.
reset_state( array( 'faq-keys' => "faq, \xFF\xFE, customkey" ) );
$bad_utf8_keys = horsetools_faq_keys();
ok( '' !== $bad_utf8_keys, 'D11: invalid UTF-8 entry in faq-keys still produces valid regex' );
eq( count( horsetools_faq_extract( '<h2>FAQ</h2><h3>Q?</h3><p>A</p>', $bad_utf8_keys ) ), 1, 'D11: valid sibling key "faq" still matches' );
eq( count( horsetools_faq_extract( '<h2>customkey</h2><h3>Q?</h3><p>A</p>', $bad_utf8_keys ) ), 1, 'D11: valid sibling key "customkey" still matches' );

// Defect 12: Linear O(N) scaling on 5,000+ unbalanced [ brackets.
reset_state();
$keys = horsetools_faq_keys();
$t1 = microtime( true );
$unbal = '<h2>FAQ</h2><h3>Q?</h3><p>' . str_repeat( '[foo bar ', 5000 ) . 'end.</p>';
$unbal_res = horsetools_faq_extract( $unbal, $keys );
$unbal_ms  = ( microtime( true ) - $t1 ) * 1000;
eq( count( $unbal_res ), 1, 'D12: extracts Q&A with 5,000 unbalanced [ tokens' );
ok( $unbal_ms < 100, 'D12: 5,000 unbalanced [ tokens run in linear time (' . round( $unbal_ms, 2 ) . 'ms)' );

// Defect 13: Minor Challenger edge cases (P2.5, P3.8, P3.14).
reset_state( array( 'faq-keys' => 'thường gặp' ) );
$bare_tg = horsetools_faq_keys();
eq( horsetools_faq_extract( '<h2>Nhược điểm thường gặp</h2><h3>1. Tốn pin</h3><p>Chi tiết.</p>', $bare_tg ), array(), 'D13 (P2.5): rejects "Nhược điểm thường gặp"' );
eq( horsetools_faq_extract( '<h2>Những vấn đề bạn thường gặp</h2><h3>1. Chậm</h3><p>Chi tiết.</p>', $bare_tg ), array(), 'D13 (P2.5): rejects "Những vấn đề bạn thường gặp"' );

reset_state();
$keys = horsetools_faq_keys();
$p38 = '<h2>FAQ</h2><p><strong>Q3?</strong><br></p><p>A3</p>';
eq( horsetools_faq_extract( $p38, $keys ), array( array( 'Q3?', 'A3' ) ), 'D13 (P3.8): <p><strong>Q3?</strong><br></p><p>A3</p> falls back to bh' );

$p314 = '<h2>FAQ</h2><p>Intro only.</p><h2>About Us</h2><p>We are a company.</p>';
eq( horsetools_faq_extract( $p314, $keys, 'Frequently Asked Questions' ), array(), 'D13 (P3.14): empty inner FAQ section does not trigger post_title fallback onto About Us' );

/* -------------------------------------------------------------------------
 * 9. Iteration 3 Regression Tests (Challenger 2 Edge Cases)
 * ---------------------------------------------------------------------- */
echo "\n9. Iteration 3 Regression Tests (Challenger 2 Edge Cases)\n";
reset_state();
$keys = horsetools_faq_keys();

// F6b: Bold section opener + <h2> Qs + trailing <h2>Next Normal Section</h2>.
$f6b = '<p><strong>Câu hỏi thường gặp</strong></p><h2>Q1?</h2><p>A1</p><h2>Q2?</h2><p>A2</p><h2>Next Normal Section</h2><p>Normal body content here.</p>';
eq(
	horsetools_faq_extract( $f6b, $keys ),
	array( array( 'Q1?', 'A1' ), array( 'Q2?', 'A2' ) ),
	'F6b: <p><strong>FAQ</strong></p> + <h2> Qs exits on trailing <h2>Next Normal Section</h2>'
);

// N1: Bold section opener + <p><strong> Qs + trailing <h3>Next Normal Section</h3>.
$n1 = '<p><strong>FAQ</strong></p><p><strong>Q1?</strong></p><p>A1</p><p><strong>Q2?</strong></p><p>A2</p><h3>Next Normal Section</h3><p>Normal body.</p>';
eq(
	horsetools_faq_extract( $n1, $keys ),
	array( array( 'Q1?', 'A1' ), array( 'Q2?', 'A2' ) ),
	'N1: <p><strong>FAQ</strong></p> + <p><strong> Qs exits on trailing <h3>Next Normal Section</h3>'
);
$single_bh = '<p><strong>FAQ</strong></p><p><strong>Q1?</strong></p><p>A1</p>';
eq(
	horsetools_faq_extract( $single_bh, $keys ),
	array( array( 'Q1?', 'A1' ) ),
	'N1 guard: <p><strong>FAQ</strong></p><p><strong>Q1?</strong></p> still extracts cleanly'
);

// N2: HTML entity &quot; / &#039; inside [ht-item title="..."] does not truncate question.
$n2_html = '<h2>FAQ</h2>[ht-item title="What &quot;is&quot; this?"]Answer 1[/ht-item]';
eq(
	horsetools_faq_extract( $n2_html, $keys ),
	array( array( 'What "is" this?', 'Answer 1' ) ),
	'N2: [ht-item title="What &quot;is&quot; this?"] preserves full quoted question'
);
$n2_sq = "<h2>FAQ</h2>[ht-item title='It&#039;s ready?']Yes[/ht-item]";
eq(
	horsetools_faq_extract( $n2_sq, $keys ),
	array( array( "It's ready?", 'Yes' ) ),
	'N2: single-quoted [ht-item title] decodes &#039; after attribute match'
);

// N7: O(N) linear scaling in horsetools_faq_prepare_shortcodes() when [/ exists.
$t_n7a   = microtime( true );
$n7a_in  = str_repeat( '[ht-item title="x"] ', 8000 ) . '[/other]';
$n7a_out = horsetools_faq_prepare_shortcodes( $n7a_in );
$n7a_ms  = ( microtime( true ) - $t_n7a ) * 1000;
ok( is_string( $n7a_out ) && false === strpos( $n7a_out, '[/other]' ), 'N7a: strips standalone [/other] and attribute-bearing shortcodes cleanly' );
ok( $n7a_ms < 50, 'N7a: 8,000 [ht-item title="x"] + [/other] runs in <50ms (' . round( $n7a_ms, 2 ) . 'ms)' );

$t_n7b   = microtime( true );
$n7b_in  = str_repeat( '[ht-item ', 8000 ) . '[/ht-item]';
$n7b_out = horsetools_faq_prepare_shortcodes( $n7b_in );
$n7b_ms  = ( microtime( true ) - $t_n7b ) * 1000;
ok( false !== strpos( $n7b_out, '[ht-item ' ), 'N7b: handles 8,000 malformed [ht-item + [/ht-item] safely' );
ok( $n7b_ms < 50, 'N7b: 8,000 malformed [ht-item + [/ht-item] runs in <50ms (' . round( $n7b_ms, 2 ) . 'ms)' );

$t_n7c   = microtime( true );
$n7c_in  = str_repeat( '[ht-item title="x"] ', 8000 ) . '[/ht-item]';
$n7c_out = horsetools_faq_prepare_shortcodes( $n7c_in );
$n7c_ms  = ( microtime( true ) - $t_n7c ) * 1000;
ok( false !== strpos( $n7c_out, '<details><summary>x</summary>' ), 'N7c: matches final [ht-item] with [/ht-item]' );
ok( $n7c_ms < 50, 'N7c: 8,000 [ht-item title="x"] + [/ht-item] runs in <50ms (' . round( $n7c_ms, 2 ) . 'ms)' );

$n7_nest = '<h2>FAQ</h2>[ht-item title="Outer?"][ht-toggle title="Inner?"]Inner body[/ht-toggle][/ht-item]';
eq(
	horsetools_faq_extract( $n7_nest, $keys ),
	array( array( 'Outer?', 'Inner? Inner body' ) ),
	'N7d: nested [ht-toggle] inside [ht-item] flattens cleanly inside outer details'
);

// F11 residual: invalid UTF-8 in faq-keys must not become "??" and match "What??".
reset_state( array( 'faq-keys' => "faq, \xFF\xFE, test" ) );
$f11_keys = horsetools_faq_keys();
ok( ! horsetools_faq_matches_keys( 'What??', $f11_keys ), 'F11: invalid UTF-8 in faq-keys does not match "What??"' );
eq(
	horsetools_faq_extract( '<h2>What??</h2><h3>Item 1</h3><p>Desc 1</p>', $f11_keys ),
	array(),
	'F11: "What??" heading does not open FAQ section'
);
ok( horsetools_faq_matches_keys( 'Test', $f11_keys ), 'F11: valid sibling key "test" still matches' );

// N4: </script > with whitespace before > closes JSON-LD script block cleanly.
reset_state();
$keys = horsetools_faq_keys();
$GLOBALS['ht_test_posts'][44] = array( 'post_content' => '<script type="application/ld+json">{"@type":"Article"}</script ><p>Tutorial mentions FAQPage</p>' );
ok( ! horsetools_faq_foreign( 44 ), 'N4: </script > with space closes JSON-LD block without false-positive on trailing FAQPage prose' );

// N5: <h4>Lưu ý:</h4> inside active <h3> question stays in answer and preserves <h3>Q2?</h3>.
$n5 = '<h2>FAQ</h2><h3>Q1?</h3><p>A1.</p><h4>Lưu ý:</h4><p>Note text.</p><h3>Q2?</h3><p>A2.</p>';
eq(
	horsetools_faq_extract( $n5, $keys ),
	array(
		array( 'Q1?', 'A1. Lưu ý: Note text.' ),
		array( 'Q2?', 'A2.' ),
	),
	'N5: <h4>Lưu ý:</h4> inside active <h3> question stays in answer and keeps Q2?'
);

// N6: Nested <table> in <td> and comparison <table> under active <h3> question.
$n6a = '<h2>FAQ</h2><table><tr><th>Q1?</th><td>A1<table><tr><th>Sub?</th><td>SubA</td></tr></table></td></tr></table>';
eq(
	horsetools_faq_extract( $n6a, $keys ),
	array( array( 'Q1?', 'A1 Sub? SubA' ) ),
	'N6a: nested <table> inside <td> keeps outer Q1? and flattens inner table'
);
$n6b = '<h2>FAQ</h2><h3>Which plan?</h3><table><tr><th>Need priority support?</th><td>Choose Pro</td></tr></table>';
eq(
	horsetools_faq_extract( $n6b, $keys ),
	array( array( 'Which plan?', 'Need priority support? Choose Pro' ) ),
	'N6b: comparison <table> under active <h3> question stays part of answer'
);

echo "\n" . str_repeat( '-', 56 ) . "\n";
echo "  $pass đạt, $fail hỏng\n\n";
exit( $fail ? 1 : 0 );
