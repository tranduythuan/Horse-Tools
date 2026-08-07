<?php
/**
 * Does the outbound-link inventory see what was actually put in the page?
 *
 * This is the detector aimed at the thing that happened: an administrator
 * account nobody recognised edited three old posts, left casino links in them,
 * and the site ran that way for over two years. Every way of hiding such a link
 * from a person skimming the post is a case below — the protocol-relative URL,
 * the unclosed anchor, the bare URL, the script tag, the subdomain, the
 * tracking parameters that change every week while the host does not.
 *
 * The other half is silence. A shop links to Facebook, YouTube, a courier and a
 * payment gateway on every page; if those read as findings the owner stops
 * looking, and then this is worth nothing.
 *
 * Usage:  php tools/test-watch-links.php
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'MINUTE_IN_SECONDS', 60 );

function __( $s, $d = '' ) { return $s; }
function _n( $a, $b, $n, $d = '' ) { return 1 === $n ? $a : $b; }
function esc_html__( $s, $d = '' ) { return $s; }
function esc_html_e( $s, $d = '' ) { echo $s; }
function esc_html( $s ) { return $s; }
function esc_attr( $s ) { return $s; }
function esc_url( $s ) { return $s; }
function checked( $a, $b = true, $e = true ) {}
function get_option( $k, $default = false ) { return array_key_exists( $k, $GLOBALS['opts'] ) ? $GLOBALS['opts'][ $k ] : $default; }
function update_option( $k, $v, $a = null ) { $GLOBALS['opts'][ $k ] = $v; return true; }
function delete_option( $k ) { unset( $GLOBALS['opts'][ $k ] ); return true; }
function get_transient( $k ) { return $GLOBALS['trans'][ $k ] ?? false; }
function set_transient( $k, $v, $t = 0 ) { $GLOBALS['trans'][ $k ] = $v; return true; }
function add_action( $h, $f, $p = 10, $a = 1 ) {}
function add_filter( $h, $f, $p = 10, $a = 1 ) { $GLOBALS['filters'][ $h ][] = $f; }
function apply_filters( $h, $v ) {
	foreach ( $GLOBALS['filters'][ $h ] ?? array() as $f ) { $v = $f( $v ); }
	return $v;
}
function current_user_can( $c ) { return true; }
function admin_url( $p = '' ) { return 'https://shop.example/wp-admin/' . $p; }
function home_url( $p = '' ) { return 'https://giathuanshop.com' . $p; }
function site_url( $p = '' ) { return 'https://giathuanshop.com' . $p; }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function wp_strip_all_tags( $s ) { return trim( strip_tags( preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $s ) ) ); }
function wp_nonce_field( $a, $b ) {}
function wp_verify_nonce( $a, $b ) { return true; }
function sanitize_key( $s ) { return $s; }
function sanitize_text_field( $s ) { return trim( $s ); }
function wp_unslash( $s ) { return $s; }
function get_the_title( $id ) { return 'Post ' . $id; }
function get_edit_post_link( $id ) { return 'edit.php?post=' . $id; }
function horsetools_is_plugin_screen() { return false; }
function horsetools_current_admin_page() { return ''; }
// The confirm paths re-anchor their decisions to a file; that file has its own
// suite (tools/test-anchor.php). Here it only has to exist.
function horsetools_anchor_touch() { $GLOBALS["anchored"] = ( $GLOBALS["anchored"] ?? 0 ) + 1; }
function horsetools_admin_banner( $t, $h ) {}
function number_format_i18n( $n ) { return (string) $n; }
function _prime_post_caches( $ids, $a = true, $b = true ) {}
function add_query_arg( $k, $v, $u ) { return $u . '&' . $k . '=' . $v; }
function esc_attr_e( $s, $d = '' ) { echo $s; }
function wp_json_encode( $v ) { return json_encode( $v ); }
// The screen writes the guard mode; the guard itself has its own suite.
function horsetools_link_guard_set( $m ) { $GLOBALS['opts']['horsetools_link_guard'] = in_array($m,array('nofollow','strip'),true)?$m:'off'; }
function horsetools_link_guard_mode() { $m = $GLOBALS['opts']['horsetools_link_guard'] ?? 'off'; return in_array($m,array('nofollow','strip'),true)?$m:'off'; }
function horsetools_link_guard_active() { return 'off' !== horsetools_link_guard_mode() && horsetools_link_reviewed(); }
function horsetools_group_menu( $s, $t, $i, $c, $p ) { $GLOBALS['menu_slug'] = 'horsetools-' . $s; }
function add_submenu_page() {}

// The shared walk. The link watcher asks it whether the first pass has finished
// before it says anything, so the tests drive that directly.
$GLOBALS['scan_done'] = true;
function horsetools_scan_finished() { return (bool) $GLOBALS['scan_done']; }
function horsetools_scan_progress() { return array( 'read' => 0, 'total' => 0 ); }

$GLOBALS['opts']    = array();
$GLOBALS['trans']   = array();
$GLOBALS['filters'] = array();

// Host normalisation and the approved list moved to inc/link-list.php so the
// front end can reach them without the review screen; watch-links.php builds
// on top of them.
require_once dirname( __DIR__ ) . '/inc/link-list.php';
require_once dirname( __DIR__ ) . '/inc/watch-links.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . "\n"; }
}
function ok( $c, $what ) { eq( (bool) $c, true, $what ); }

$self = array( 'giathuanshop.com' );
function hosts( $html ) {
	global $self;
	return array_keys( horsetools_link_extract( $html, $self ) );
}

echo "\n1. Rút tên miền — mọi cách viết cùng một nơi phải gom về một dòng\n";
foreach ( array(
	'https://vidu.com/a'            => 'vidu.com',
	'http://vidu.com/a'             => 'vidu.com',
	'https://www.vidu.com/a'        => 'vidu.com',
	'https://VIDU.com/A'            => 'vidu.com',
	'https://vidu.com./a'           => 'vidu.com',
	'https://vidu.com:8080/a'       => 'vidu.com',    // cổng không đổi chủ sở hữu
	'//vidu.com/a'                  => 'vidu.com',
	'https://vidu.com?utm_source=x' => 'vidu.com',
) as $url => $want ) {
	eq( horsetools_link_host( $url ), $want, "'$url'" );
}

echo "\n2. Không phải liên kết thì không được thành tên miền\n";
foreach ( array(
	'mailto:a@vidu.com' => 'email',
	'tel:0988343412'    => 'số điện thoại',
	'#khuyen-mai'       => 'neo trong trang',
	'/san-pham/den'     => 'đường dẫn tương đối',
	'javascript:void(0)' => 'javascript:',
	'data:text/html;base64,AAAA' => 'data:',
	''                  => 'rỗng',
	'https://localhost/x' => 'không có dấu chấm',
) as $url => $why ) {
	eq( horsetools_link_host( $url ), '', "$why — '$url'" );
}

echo "\n3. Liên kết ra ngoài trong bài\n";
$html = '<p>Xem thêm <a href="https://doitac.vn/bai-viet">đối tác của chúng tôi</a>.</p>';
$r    = horsetools_link_extract( $html, $self );
eq( array_keys( $r ), array( 'doitac.vn' ), 'một tên miền' );
eq( $r['doitac.vn']['count'], 1, 'một liên kết' );
eq( $r['doitac.vn']['anchor'], 'đối tác của chúng tôi', 'giữ chữ hiển thị' );
eq( $r['doitac.vn']['dofollow'], 1, 'không có nofollow thì là dofollow' );

echo "\n4. Liên kết về chính site KHÔNG được tính\n";
eq( hosts( '<a href="https://giathuanshop.com/den">đèn</a>' ), array(), 'cùng host' );
eq( hosts( '<a href="https://www.giathuanshop.com/den">đèn</a>' ), array(), 'www. của chính nó' );
eq( hosts( '<a href="/den-long">đèn lồng</a>' ), array(), 'đường dẫn tương đối' );

echo "\n5. Những kiểu giấu link — mỗi dòng là một cách qua mắt người đọc lướt\n";
eq( hosts( '<a href="//nhacai.xyz/x">chơi ngay</a>' ), array( 'nhacai.xyz' ), 'thiếu giao thức //' );
eq( hosts( "<a href='https://nhacai.xyz/x'>x</a>" ), array( 'nhacai.xyz' ), 'nháy đơn' );
eq( hosts( '<a href=https://nhacai.xyz/x>x</a>' ), array( 'nhacai.xyz' ), 'không nháy' );
eq( hosts( '<A HREF="https://nhacai.xyz/x">x</A>' ), array( 'nhacai.xyz' ), 'chữ hoa' );
eq( hosts( '<a  rel="nofollow"  href="https://nhacai.xyz/x" >x</a>' ), array( 'nhacai.xyz' ), 'thuộc tính đảo thứ tự' );
eq( hosts( '<a href="https://nhacai.xyz/x">x' ), array( 'nhacai.xyz' ), 'thẻ a không đóng' );
eq( hosts( 'Truy cập https://nhacai.xyz/x ngay' ), array( 'nhacai.xyz' ), 'URL viết trần' );
eq( hosts( '<a href="https://khuyenmai.doitac.vn/x">x</a>' ), array( 'khuyenmai.doitac.vn' ), 'tên miền con là một dòng riêng' );

echo "\n6. nofollow phải được ghi nhận — link trả giá trị SEO mới là thứ được mua\n";
$r = horsetools_link_extract( '<a rel="nofollow" href="https://nhacai.xyz/x">x</a>', $self );
eq( $r['nhacai.xyz']['dofollow'], 0, 'rel=nofollow' );
$r = horsetools_link_extract( '<a rel="ugc nofollow" href="https://nhacai.xyz/x">x</a>', $self );
eq( $r['nhacai.xyz']['dofollow'], 0, 'rel="ugc nofollow"' );
$r = horsetools_link_extract( '<a rel="noopener" href="https://nhacai.xyz/x">x</a>', $self );
eq( $r['nhacai.xyz']['dofollow'], 1, 'rel=noopener vẫn là dofollow' );

echo "\n7. Script và iframe lạ — nặng hơn một cái link\n";
$r = horsetools_link_extract( '<script src="https://cdn-la.com/a.js"></script>', $self );
eq( $r['cdn-la.com']['embed'], 1, '<script src>' );
eq( $r['cdn-la.com']['link'], 0, 'không tính là link' );
$r = horsetools_link_extract( '<iframe src="//khung-la.com/f"></iframe>', $self );
eq( $r['khung-la.com']['embed'], 1, '<iframe src>' );
$r = horsetools_link_extract( '<link rel="stylesheet" href="https://css-la.com/a.css">', $self );
eq( $r['css-la.com']['embed'], 1, '<link href>' );

echo "\n8. KHÔNG được đếm hai lần\n";
$r = horsetools_link_extract( '<a href="https://doitac.vn/a">đối tác</a>', $self );
eq( $r['doitac.vn']['count'], 1, 'href không bị quét lại thành URL trần' );
$r = horsetools_link_extract( '<script src="https://cdn-la.com/a.js">var u="https://trong-js.com/x";</script>', $self );
ok( ! isset( $r['trong-js.com'] ), 'URL nằm trong thân script không phải link của bài' );

echo "\n9. Gom nhiều liên kết cùng một nơi về một dòng\n";
$r = horsetools_link_extract(
	'<a href="https://doitac.vn/a">một</a> <a href="https://doitac.vn/b?utm=1">hai</a> <a href="https://www.doitac.vn/c">ba</a>',
	$self
);
eq( count( $r ), 1, 'ba liên kết, một dòng' );
eq( $r['doitac.vn']['count'], 3, 'đếm đủ ba' );
eq( $r['doitac.vn']['anchor'], 'một', 'giữ chữ hiển thị đầu tiên' );

echo "\n10. Trang bình thường của một cửa hàng phải im lặng đúng mức\n";
$shop = '<p>Liên hệ <a href="tel:0988343412">0988343412</a> hoặc '
	. '<a href="https://zalo.me/0988343412">Zalo</a>. Giá 1.790.000đ. '
	. '<a href="https://giathuanshop.com/den-long">Xem đèn lồng</a> '
	. '<a href="/lien-he">Liên hệ</a> <img src="https://giathuanshop.com/a.jpg">';
eq( hosts( $shop ), array( 'zalo.me' ), 'chỉ Zalo là ra ngoài; tel:, giá, ảnh và link nội bộ đều im' );

echo "\n11. Thu gom qua nhiều lô — số đếm phải cộng dồn, bài phải ghi lại\n";
$GLOBALS['opts'] = array();
$row = function ( $id, $content ) {
	return (object) array( 'ID' => $id, 'post_title' => '', 'post_excerpt' => '', 'post_content' => $content );
};
horsetools_link_collect( array(
	$row( 11, '<a href="https://doitac.vn/a">đối tác</a>' ),
	$row( 12, '<a href="https://doitac.vn/b">đối tác</a> <a href="https://nhacai.xyz/x">chơi ngay</a>' ),
) );
horsetools_link_collect( array( $row( 13, '<a href="https://doitac.vn/c">đối tác</a>' ) ) );
$f = horsetools_link_found();
eq( count( $f ), 2, 'hai tên miền' );
eq( $f['doitac.vn']['count'], 3, 'cộng dồn qua hai lô' );
eq( $f['doitac.vn']['posts'], array( 11, 12, 13 ), 'nhớ bài nào' );
eq( $f['nhacai.xyz']['posts'], array( 12 ), 'bài duy nhất chứa link lạ' );

echo "\n12. Quét lại phải xoá sạch trước — nếu không số đếm nhân đôi\n";
horsetools_link_collect_reset();
eq( horsetools_link_found(), array(), 'sạch' );
horsetools_link_collect( array( $row( 11, '<a href="https://doitac.vn/a">x</a>' ) ) );
eq( horsetools_link_found()['doitac.vn']['count'], 1, 'chạy lại không cộng vào cái cũ' );

echo "\n13. Chưa quét xong thì KHÔNG được kết luận gì\n";
$GLOBALS['scan_done'] = false;
eq( horsetools_link_found(), array(), 'chưa đọc hết thì chưa có danh sách' );
eq( horsetools_link_status()['state'], 'scanning', 'trạng thái: đang đọc' );
$GLOBALS['scan_done'] = true;

echo "\n14. Duyệt\n";
$GLOBALS['opts'] = array();
horsetools_link_collect( array(
	$row( 11, '<a href="https://doitac.vn/a">x</a>' ),
	$row( 12, '<a href="https://nhacai.xyz/x">chơi ngay</a>' ),
) );
eq( horsetools_link_status()['state'], 'unset', 'chưa duyệt lần nào' );
eq( horsetools_link_reviewed(), false, 'chưa có danh sách duyệt' );

horsetools_link_approve( array( 'doitac.vn' ) );
eq( horsetools_link_reviewed(), true, 'đã duyệt một lần' );
$s = horsetools_link_status();
eq( $s['state'], 'changed', 'còn một tên miền chưa duyệt' );
eq( array_keys( $s['new'] ), array( 'nhacai.xyz' ), 'đúng cái chưa duyệt' );

horsetools_link_approve( array( 'nhacai.xyz' ) );
eq( horsetools_link_status()['state'], 'clean', 'duyệt hết thì im' );

echo "\n15. Duyệt xong mà xuất hiện tên miền mới thì phải kêu\n";
horsetools_link_collect( array( $row( 14, '<a href="https://moi-toanh.com/x">x</a>' ) ) );
$s = horsetools_link_status();
eq( $s['state'], 'changed', 'kêu' );
eq( array_keys( $s['new'] ), array( 'moi-toanh.com' ), 'đúng cái mới' );

echo "\n16. Bỏ duyệt\n";
horsetools_link_revoke( array( 'doitac.vn' ) );
ok( isset( horsetools_link_status()['new']['doitac.vn'] ), 'bỏ duyệt thì quay lại danh sách chờ' );
ok( horsetools_link_reviewed(), 'bỏ duyệt không xoá dấu đã từng duyệt' );

echo "\n17. Duyệt rồi thì lưu cả thời điểm — sau này còn hỏi lại được\n";
$GLOBALS['opts'] = array();
horsetools_link_approve( array( 'doitac.vn' ) );
$a = horsetools_link_approved();
ok( isset( $a['doitac.vn'] ) && $a['doitac.vn'] > 1700000000, 'có mốc thời gian' );
$t = $a['doitac.vn'];
horsetools_link_approve( array( 'doitac.vn' ) );
eq( horsetools_link_approved()['doitac.vn'], $t, 'duyệt lại không dập mốc cũ' );

echo "\n18. Tên miền gửi lên từ biểu mẫu phải được chuẩn hoá như lúc quét\n";
$GLOBALS['opts'] = array();
horsetools_link_approve( array( 'WWW.DoiTac.VN', 'https://khac.com/x' ) );
eq( array_keys( horsetools_link_approved() ), array( 'doitac.vn', 'khac.com' ), 'hoa/thường, www., cả URL đầy đủ' );

echo "\n19. Sắp xếp: cái ít link nhất lên trước — đó chính là phần soát\n";
$order = array_keys( horsetools_link_sort( array(
	'facebook.com' => array( 'host' => 'facebook.com', 'count' => 200, 'posts' => array( 1, 2, 3, 4, 5, 6, 7, 8 ) ),
	'nhacai.xyz'   => array( 'host' => 'nhacai.xyz', 'count' => 1, 'posts' => array( 40 ) ),
	'doitac.vn'    => array( 'host' => 'doitac.vn', 'count' => 9, 'posts' => array( 1, 2, 3 ) ),
) ) );
eq( $order, array( 'nhacai.xyz', 'doitac.vn', 'facebook.com' ), 'cái lạ nằm dòng đầu, không cần danh sách đen nào' );

echo "\n20. Trần — và chạm trần thì PHẢI nói ra\n";
// Một site thật (866 bài, hay dẫn nguồn) đâm thẳng qua trần 400 cũ, và danh sách
// lặng lẽ ngừng ghi. Danh sách thiếu vẫn trả lời câu "tên miền này có mới không"
// bằng "không" cho mọi thứ nó chưa từng ghi — đúng câu nó không được phép sai.
$GLOBALS['opts'] = array();
$big = '';
for ( $i = 0; $i < HORSETOOLS_LINK_MAX + 50; $i++ ) { $big .= '<a href="https://site' . $i . '.com/x">x</a>'; }
eq( horsetools_link_truncated(), false, 'chưa chạm thì không kêu' );
horsetools_link_collect( array( $row( 20, $big ) ) );
eq( count( horsetools_link_found() ), HORSETOOLS_LINK_MAX, 'dừng ở ' . HORSETOOLS_LINK_MAX );
eq( horsetools_link_truncated(), true, 'và ghi nhận là đã thiếu' );
eq( horsetools_link_status()['truncated'], true, 'trạng thái mang cờ đó ra ngoài' );
horsetools_link_collect_reset();
eq( horsetools_link_truncated(), false, 'quét lại từ đầu thì xoá cờ — nếu không nó kẹt mãi' );

echo "\n21. Lưu chỉ gửi lên NGOẠI LỆ, không gửi cả danh sách\n";
// Bản cũ gửi 1 input ẩn + 1 checkbox cho MỖI tên miền. Site 686 tên miền =
// 1372 trường, mà PHP `max_input_vars` mặc định dừng ở 1000 và lặng lẽ vứt phần
// dư — chủ site tick hết, bấm lưu, rồi vài trăm tên miền vẫn không được duyệt
// mà màn hình không nói gì. Bất cứ thứ gì tăng theo số dòng đều sai ở đây.
function screen_post( array $post ) {
	$_POST = array_merge( array( 'ht_links_nonce' => 'n' ), $post );
	ob_start();
	horsetools_link_screen();
	$out = ob_get_clean();
	$_POST = array();
	return $out;
}
$GLOBALS['opts'] = array();
horsetools_link_collect( array(
	$row( 30, '<a href="https://a1.com/x">x</a><a href="https://a2.com/x">x</a><a href="https://a3.com/x">x</a>' ),
) );

screen_post( array( 'do' => 'approve', 'pick' => array( 'a1.com', 'a2.com' ) ) );
eq( array_keys( horsetools_link_approved() ), array( 'a1.com', 'a2.com' ), 'duyệt đúng cái được tick' );
eq( array_keys( horsetools_link_status()['new'] ), array( 'a3.com' ), 'cái không tick vẫn nằm ở hàng chờ' );

screen_post( array( 'do' => 'revoke', 'pick' => array( 'a1.com' ) ) );
ok( ! isset( horsetools_link_approved()['a1.com'] ), 'bỏ duyệt được' );
ok( isset( horsetools_link_approved()['a2.com'] ), 'và không đụng cái khác' );

screen_post( array( 'do' => 'approve_all' ) );
eq( count( horsetools_link_approved() ), 3, 'nút duyệt hết không cần liệt kê từng dòng — 1 trường thay vì 1372' );
eq( horsetools_link_status()['state'], 'clean', 'sạch' );

echo "\n22. Đặt chế độ chặn KHÔNG được tính là đã soát danh sách\n";
// Nếu tính, thì trên site chưa duyệt gì: reviewed=true + danh sách duyệt rỗng
// = guard vô hiệu hoá TOÀN BỘ link ra ngoài của site, chỉ vì người ta bấm lưu
// một cái radio.
$GLOBALS['opts'] = array();
horsetools_link_collect( array( $row( 31, '<a href="https://doitac.vn/x">x</a>' ) ) );
eq( horsetools_link_reviewed(), false, 'chưa soát' );
screen_post( array( 'guard' => 'nofollow' ) );
eq( horsetools_link_guard_mode(), 'nofollow', 'chế độ được lưu' );
eq( horsetools_link_reviewed(), false, 'nhưng VẪN chưa soát — guard chưa được phép chạy' );
eq( horsetools_link_guard_active(), false, 'nên guard nằm im' );

echo "\n23. \"Tôi soát rồi, không cái nào của tôi\" vẫn là một câu trả lời\n";
screen_post( array( 'do' => 'approve', 'pick' => array() ) );
eq( horsetools_link_reviewed(), true, 'bấm duyệt mà không tick gì → đã soát' );
eq( horsetools_link_guard_active(), true, 'và lúc đó guard mới chặn hết' );

echo "\n24. Màn duyệt phải nằm trong menu — giấu nó đi là WordPress chặn luôn\n";
// remove_submenu_page() từng được dùng để giấu trang này. WordPress quyết định
// bạn có được mở admin.php?page=… hay không bằng cách tra slug trong $submenu và
// đọc quyền từ mục tìm thấy; gỡ mục đó ra là chính admin cũng bị chặn.
horsetools_link_menu();
eq( $GLOBALS['menu_slug'] ?? '', 'horsetools-links', 'đăng ký vào menu plugin, không giấu' );
ok( false !== strpos( horsetools_link_screen_url(), 'page=horsetools-links' ), 'đường dẫn khớp đúng slug đã đăng ký' );

echo "\n" . str_repeat( '-', 56 ) . "\n";
echo "  $pass đạt, $fail hỏng\n\n";
exit( $fail ? 1 : 0 );
