<?php
/**
 * Does the guard defuse an unapproved link without breaking the site's own?
 *
 * This is the only thing in the plugin that changes what a visitor is served,
 * and it runs on every rendered post of a live shop. The failure that matters is
 * not "a bad link survived" — the owner is being told about it either way. It is
 * "the shop's own links broke", silently, on every page, until somebody notices
 * a drop in orders.
 *
 * So most of what follows is the not-list: relative links, anchors, mailto,
 * tel, the site's own domain, approved domains, and every case where the guard
 * has no business acting at all — before a review has happened, in the admin,
 * switched off. Then the handful that prove it does work when it should.
 *
 * Usage:  php tools/test-link-guard.php
 */

define( 'ABSPATH', __DIR__ . '/' );
if ( ! defined( 'ENT_QUOTES' ) ) { define( 'ENT_QUOTES', 3 ); }

function __( $s, $d = '' ) { return $s; }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function get_option( $k, $default = false ) { return array_key_exists( $k, $GLOBALS['opts'] ) ? $GLOBALS['opts'][ $k ] : $default; }
function update_option( $k, $v, $a = null ) { $GLOBALS['opts'][ $k ] = $v; return true; }
function add_action( $h, $f, $p = 10, $a = 1 ) {}
function add_filter( $h, $f, $p = 10, $a = 1 ) {}
function apply_filters( $h, $v ) { return $v; }
function home_url( $p = '' ) { return 'https://giathuanshop.com' . $p; }
function site_url( $p = '' ) { return 'https://giathuanshop.com' . $p; }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function is_admin() { return (bool) $GLOBALS['in_admin']; }

$GLOBALS['opts']     = array();
$GLOBALS['in_admin'] = false;

require_once dirname( __DIR__ ) . '/inc/link-list.php';
require_once dirname( __DIR__ ) . '/inc/link-guard.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what\n        muốn: " . var_export( $want, true ) . "\n        được: " . var_export( $got, true ) . "\n"; }
}
function ok( $c, $what ) { eq( (bool) $c, true, $what ); }

/**
 * Put the site in one state and run the real filter over one piece of content.
 *
 * The real one, not a copy of it. The guard held its allowed-host set in a
 * function-level `static` at first, which made it untestable without
 * reimplementing the logic here — and a test that reimplements the thing it is
 * testing passes whatever the copy does. The static is now built per call
 * instead, which costs nothing and leaves the filter callable.
 */
function run( $html, $mode = 'nofollow', $approved = array( 'doitac.vn' ), $reviewed = true, $admin = false ) {
	$GLOBALS['opts']     = array();
	$GLOBALS['in_admin'] = $admin;
	if ( $reviewed ) {
		$list = array();
		foreach ( $approved as $h ) { $list[ $h ] = 1770000000; }
		$GLOBALS['opts']['horsetools_link_approved'] = $list;
	}
	horsetools_link_guard_set( $mode );
	return horsetools_link_guard_filter( $html );
}

echo "\n1. KHÔNG ĐƯỢC ĐỘNG VÀO — mỗi dòng là một cách làm hỏng site của người ta\n";
$keep = array(
	'<a href="/san-pham/den">đèn lồng</a>'                       => 'đường dẫn tương đối',
	'<a href="#lien-he">Liên hệ</a>'                             => 'neo trong trang',
	'<a href="mailto:inan@giathuanshop.com">Mail</a>'            => 'mailto:',
	'<a href="tel:0988343412">Gọi</a>'                           => 'tel:',
	'<a href="https://giathuanshop.com/den">đèn</a>'             => 'chính site',
	'<a href="https://www.giathuanshop.com/den">đèn</a>'         => 'www. của chính site',
	'<a href="https://doitac.vn/x">đối tác</a>'                  => 'tên miền ĐÃ duyệt',
	'<a href="https://www.doitac.vn/x">đối tác</a>'              => 'www. của tên miền đã duyệt',
	'<a href="javascript:void(0)">mở</a>'                        => 'javascript:',
	'<p>Không có liên kết nào cả.</p>'                           => 'không có thẻ a',
	''                                                           => 'nội dung rỗng',
);
foreach ( $keep as $html => $why ) {
	eq( run( $html ), $html, "$why — giữ nguyên từng ký tự" );
}

echo "\n2. Tên miền chưa duyệt thì gắn nofollow\n";
eq(
	run( '<a href="https://nhacai.xyz/x">chơi ngay</a>' ),
	'<a href="https://nhacai.xyz/x" rel="nofollow">chơi ngay</a>',
	'thêm rel="nofollow"'
);
eq(
	run( '<a rel="noopener" href="https://nhacai.xyz/x">x</a>' ),
	'<a rel="noopener nofollow" href="https://nhacai.xyz/x">x</a>',
	'nối vào rel đang có, không đè mất noopener'
);
eq(
	run( '<a rel="nofollow" href="https://nhacai.xyz/x">x</a>' ),
	'<a rel="nofollow" href="https://nhacai.xyz/x">x</a>',
	'đã nofollow rồi thì không thêm lần nữa'
);
eq(
	run( '<a href="https://nhacai.xyz/x" target="_blank">x</a>' ),
	'<a href="https://nhacai.xyz/x" target="_blank" rel="nofollow">x</a>',
	'giữ nguyên các thuộc tính khác'
);

echo "\n3. Những kiểu viết mà kẻ chèn hay dùng\n";
ok( false !== strpos( run( '<a href="//nhacai.xyz/x">x</a>' ), 'nofollow' ), 'thiếu giao thức //' );
ok( false !== strpos( run( "<a href='https://nhacai.xyz/x'>x</a>" ), 'nofollow' ), 'nháy đơn' );
ok( false !== strpos( run( '<a href=https://nhacai.xyz/x>x</a>' ), 'nofollow' ), 'không nháy' );
ok( false !== strpos( run( '<A HREF="https://nhacai.xyz/x">x</A>' ), 'nofollow' ), 'chữ hoa' );
ok( false !== strpos( run( '<a href="https://nhacai.xyz/x">x' ), 'nofollow' ), 'thẻ a không đóng' );
ok( false !== strpos( run( '<a href="https://khuyenmai.doitac.vn/x">x</a>' ), 'nofollow' ), 'tên miền con của tên miền đã duyệt KHÔNG được ăn theo' );

echo "\n4. Nhiều liên kết trong một bài — chỉ đụng đúng cái phải đụng\n";
$mixed = '<p>Xem <a href="https://doitac.vn/a">đối tác</a>, '
	. '<a href="https://nhacai.xyz/b">chơi ngay</a>, '
	. 'và <a href="/lien-he">liên hệ</a>.</p>';
$out = run( $mixed );
eq( substr_count( $out, 'nofollow' ), 1, 'đúng một liên kết bị đụng' );
ok( false !== strpos( $out, '<a href="https://doitac.vn/a">đối tác</a>' ), 'link đối tác nguyên vẹn' );
ok( false !== strpos( $out, '<a href="/lien-he">liên hệ</a>' ), 'link nội bộ nguyên vẹn' );

echo "\n5. Chế độ gỡ hẳn link\n";
eq(
	run( '<p>Xem <a href="https://nhacai.xyz/x">chơi ngay</a> nhé.</p>', 'strip' ),
	'<p>Xem chơi ngay nhé.</p>',
	'thay cả thẻ bằng chính chữ bên trong — câu vẫn đọc được'
);
eq(
	run( '<a href="https://doitac.vn/a">đối tác</a>', 'strip' ),
	'<a href="https://doitac.vn/a">đối tác</a>',
	'tên miền đã duyệt vẫn là liên kết'
);
ok(
	false !== strpos( run( '<a href="https://nhacai.xyz/x">x', 'strip' ), 'nofollow' ),
	'thẻ không đóng thì không gỡ được, nhưng vẫn phải bị nofollow chứ không được thả'
);
eq(
	run( '<a href="https://nhacai.xyz/x"><img src="https://giathuanshop.com/a.jpg"></a>', 'strip' ),
	'<img src="https://giathuanshop.com/a.jpg">',
	'link bọc ảnh thì còn lại cái ảnh'
);

echo "\n6. Khi nào TUYỆT ĐỐI không được chạy\n";
$bad = '<a href="https://nhacai.xyz/x">x</a>';
eq( run( $bad, 'off' ), $bad, 'đang tắt' );
eq( run( $bad, 'nofollow', array(), false ), $bad, 'CHƯA duyệt lần nào — nếu chạy thì bản cập nhật sẽ nofollow sạch mọi link ra ngoài của site' );
eq( run( $bad, 'nofollow', array( 'doitac.vn' ), true, true ), $bad, 'trong trang quản trị' );

echo "\n7. Duyệt rỗng vẫn là đã duyệt\n";
// "Tôi soát rồi, không cái nào là của tôi" — lúc đó chặn hết mới đúng.
eq(
	run( $bad, 'nofollow', array(), true ),
	'<a href="https://nhacai.xyz/x" rel="nofollow">x</a>',
	'soát rồi mà không duyệt cái nào → chặn hết, không phải thả hết'
);

echo "\n8. Chế độ lưu lại\n";
$GLOBALS['opts'] = array();
eq( horsetools_link_guard_mode(), 'off', 'mặc định TẮT — không tự đổi trang của ai khi cập nhật' );
horsetools_link_guard_set( 'nofollow' );
eq( horsetools_link_guard_mode(), 'nofollow', 'lưu được' );
horsetools_link_guard_set( 'strip' );
eq( horsetools_link_guard_mode(), 'strip', 'đổi được' );
horsetools_link_guard_set( 'xoa-het-bai-viet' );
eq( horsetools_link_guard_mode(), 'off', 'giá trị lạ → về tắt, không đoán bừa' );

echo "\n9. Không được làm hỏng HTML\n";
$page = '<div class="row"><a class="btn" href="https://nhacai.xyz/x" data-id=\'7\' title="a > b">x</a></div>';
$out  = run( $page );
ok( false !== strpos( $out, 'class="btn"' ), 'giữ class' );
ok( false !== strpos( $out, "data-id='7'" ), 'giữ thuộc tính nháy đơn' );
ok( false !== strpos( $out, 'rel="nofollow"' ), 'vẫn gắn được nofollow' );
eq( substr_count( $out, '<a ' ), 1, 'không nhân bản thẻ' );
eq( substr_count( $out, '</div>' ), 1, 'không đụng phần còn lại' );

echo "\n10. Thẻ tự đóng và khoảng trắng thừa\n";
ok( false !== strpos( run( '<a href="https://nhacai.xyz/x" >x</a>' ), 'rel="nofollow"' ), 'có khoảng trắng trước dấu đóng' );
eq(
	run( '<a href="https://nhacai.xyz/x"/>' ),
	'<a href="https://nhacai.xyz/x" rel="nofollow"/>',
	'dấu gạch tự đóng ở lại đúng chỗ'
);

echo "\n" . str_repeat( '-', 56 ) . "\n";
echo "  $pass đạt, $fail hỏng\n\n";
exit( $fail ? 1 : 0 );
