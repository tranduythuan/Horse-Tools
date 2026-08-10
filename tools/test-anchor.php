<?php
/**
 * Does the anchor notice a hand at the database, and stay quiet otherwise?
 *
 * The value of this file is entirely in what it refuses to report. It runs on
 * every admin page load of a live shop, and its alarm says something as serious
 * as any message this plugin can print — "somebody reached past your screens".
 * One false positive and it is worth less than nothing, because the next one,
 * the real one, reads the same.
 *
 * So the cases below are mostly the ones where it must NOT fire: a post being
 * edited, a number being seen more often, the owner confirming the same set
 * twice, a version that did not watch a given option yet. And two that prove
 * the honest limits — an attacker who confirms through the screen is not
 * caught, and neither is one who can write files.
 *
 * Usage:  php tools/test-anchor.php
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'MINUTE_IN_SECONDS', 60 );

$GLOBALS['root'] = sys_get_temp_dir() . '/ht-anchor-test-' . bin2hex( random_bytes( 4 ) );
mkdir( $GLOBALS['root'] . '/wp-content', 0777, true );
define( 'WP_CONTENT_DIR', $GLOBALS['root'] . '/wp-content' );

function __( $s, $d = '' ) { return $s; }
function esc_html__( $s, $d = '' ) { return $s; }
function esc_html( $s ) { return $s; }
function esc_attr( $s ) { return $s; }
function get_option( $k, $default = false ) { return array_key_exists( $k, $GLOBALS['opts'] ) ? $GLOBALS['opts'][ $k ] : $default; }
function update_option( $k, $v, $a = null ) { $GLOBALS['opts'][ $k ] = $v; return true; }
function delete_option( $k ) { unset( $GLOBALS['opts'][ $k ] ); return true; }
function add_action( $h, $f, $p = 10, $a = 1 ) {}
function add_filter( $h, $f, $p = 10, $a = 1 ) {}
function current_user_can( $c ) { return true; }
function check_ajax_referer( $a, $b ) { return true; }
function wp_send_json_error( $d = null ) {}
function wp_send_json_success( $d = null ) {}
function wp_create_nonce( $a ) { return 'n'; }
function home_url( $p = '' ) { return 'https://giathuanshop.com' . $p; }
function wp_mkdir_p( $d ) { return is_dir( $d ) || mkdir( $d, 0777, true ); }
function wp_is_writable( $d ) { return is_writable( $d ); }
function wp_json_encode( $v ) { return json_encode( $v, JSON_UNESCAPED_UNICODE ); }
function wp_unslash( $s ) { return is_string( $s ) ? stripslashes( $s ) : $s; }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function horsetools_is_plugin_screen() { return false; }
function horsetools_admin_banner( $t, $h ) {}
// Các công tắc phòng thủ mà neo giờ canh thêm.
if ( ! defined( 'HORSETOOLS_LINK_GUARD' ) ) { define( 'HORSETOOLS_LINK_GUARD', 'horsetools_link_guard' ); }
$GLOBALS['horsetools_options'] = array();

$GLOBALS['opts'] = array();

// The directory guards and the `<?php exit;` first line now come from the shared
// helpers, so the anchor and the debug log cannot drift into having different
// ideas about what actually protects a file in wp-content.
require_once dirname( __DIR__ ) . '/inc/server.php';
require_once dirname( __DIR__ ) . '/inc/anchor.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . "\n"; }
}
function ok( $c, $what ) { eq( (bool) $c, true, $what ); }
function state() { return horsetools_anchor_status()['state']; }

function wipe() {
	$GLOBALS['opts'] = array();
	foreach ( (array) glob( horsetools_anchor_dir() . '/*' ) as $f ) { @unlink( $f ); }
	foreach ( (array) glob( horsetools_anchor_dir() . '/.*' ) as $f ) { if ( is_file( $f ) ) { @unlink( $f ); } }
}

/** What a confirmed set of approvals looks like. */
function approve_some() {
	$GLOBALS['opts']['horsetools_contact_baseline'] = array(
		'phone:0988343412' => array( 'type' => 'phone', 'value' => '0988343412', 'count' => 3 ),
		'phone:0838216168' => array( 'type' => 'phone', 'value' => '0838216168', 'count' => 1 ),
	);
	$GLOBALS['opts']['horsetools_link_approved'] = array( 'zalo.me' => 1770000000, 'facebook.com' => 1770000000 );
	$GLOBALS['opts']['horsetools_contact_baseline_content'] = array();
}

echo "\n1. Chưa có gì thì chưa có neo\n";
wipe();
eq( state(), 'none', 'chưa quyết định gì → chưa có neo' );
eq( horsetools_anchor_file(), '', 'chưa có file' );

echo "\n2. Chốt xong thì ghi neo\n";
wipe();
approve_some();
ok( horsetools_anchor_write(), 'ghi được' );
ok( '' !== horsetools_anchor_file(), 'có file trên đĩa' );
eq( state(), 'ok', 'khớp' );

echo "\n3. File neo KHÔNG được lộ nội dung nếu web server trả thẳng nó ra\n";
$raw = file_get_contents( horsetools_anchor_file() );
ok( 0 === strpos( $raw, '<?php exit;' ), 'mở đầu bằng exit — PHP chạy nó thì không in gì' );
ok( false !== strpos( basename( horsetools_anchor_file() ), 'ht-anchor-' ), 'tên có phần ngẫu nhiên, không đoán được URL' );
ok( file_exists( horsetools_anchor_dir() . '/index.php' ), 'có index.php' );
ok( file_exists( horsetools_anchor_dir() . '/.htaccess' ), 'có .htaccess' );
$ht = file_get_contents( horsetools_anchor_dir() . '/.htaccess' );
ok( false !== strpos( $ht, 'Require all denied' ) && false !== strpos( $ht, 'Deny from all' ), 'cả hai cách viết, mỗi cái trong guard riêng' );

echo "\n4. TAY SỬA THẲNG DATABASE — đây là toàn bộ lý do file này tồn tại\n";
wipe();
approve_some();
horsetools_anchor_write();
eq( state(), 'ok', 'yên' );
// Kẻ tấn công có quyền ghi DB tự duyệt tên miền của nó.
$GLOBALS['opts']['horsetools_link_approved']['nhacai.xyz'] = 1770000000;
eq( state(), 'mismatch', 'tự thêm tên miền vào danh sách đã duyệt → BỊ BẮT' );
eq( horsetools_anchor_mismatches(), array( 'horsetools_link_approved' ), 'chỉ tay đúng cái bị đụng' );

wipe(); approve_some(); horsetools_anchor_write();
$GLOBALS['opts']['horsetools_contact_baseline']['phone:0999999999'] = array( 'type' => 'phone', 'value' => '0999999999', 'count' => 1 );
eq( state(), 'mismatch', 'tự thêm số điện thoại vào mốc đã xác nhận → BỊ BẮT' );

wipe(); approve_some(); horsetools_anchor_write();
unset( $GLOBALS['opts']['horsetools_link_approved']['zalo.me'] );
eq( state(), 'mismatch', 'xoá bớt cũng bị bắt' );

wipe(); approve_some(); horsetools_anchor_write();
$GLOBALS['opts']['horsetools_link_approved'] = array();
eq( state(), 'mismatch', 'xoá sạch danh sách duyệt cũng bị bắt' );

wipe(); approve_some(); horsetools_anchor_write();
unset( $GLOBALS['opts']['horsetools_link_approved'] );
eq( state(), 'mismatch', 'xoá hẳn cả option cũng bị bắt — không phải "chưa từng duyệt"' );

echo "\n5. Xoá dòng option để giấu neo thì KHÔNG ăn thua\n";
wipe(); approve_some(); horsetools_anchor_write();
$GLOBALS['opts'] = array();       // xoá sạch mọi option, kể cả nếu có option trỏ đường dẫn
approve_some();
eq( state(), 'ok', 'tìm neo bằng cách quét thư mục, không hỏi database' );

echo "\n6. KHÔNG được kêu oan — đây là chỗ quyết định nó sống hay bị tắt\n";
wipe(); approve_some(); horsetools_anchor_write();

$GLOBALS['opts']['horsetools_contact_baseline']['phone:0988343412']['count'] = 99;
eq( state(), 'ok', 'số lần xuất hiện đổi (sửa bài) → KHÔNG kêu, đó là quan sát chứ không phải quyết định' );

$GLOBALS['opts']['horsetools_link_approved'] = array( 'facebook.com' => 1780000000, 'zalo.me' => 1780000000 );
eq( state(), 'ok', 'đổi thứ tự khoá và đổi mốc thời gian duyệt → KHÔNG kêu' );

horsetools_anchor_write();
horsetools_anchor_write();
eq( state(), 'ok', 'ghi neo hai lần liên tiếp vẫn khớp' );

echo "\n7. Chốt lại qua màn hình thì hai bên đi cùng nhau\n";
wipe(); approve_some(); horsetools_anchor_write();
$GLOBALS['opts']['horsetools_link_approved']['doitac.vn'] = 1770000000;
eq( state(), 'mismatch', 'trước khi neo lại thì lệch' );
horsetools_anchor_touch();
eq( state(), 'ok', 'neo lại thì khớp' );

echo "\n8. Bản neo cũ không biết một option thì không được coi là bị sửa\n";
wipe(); approve_some(); horsetools_anchor_write();
$path = horsetools_anchor_file();
$raw  = file_get_contents( $path );
$data = json_decode( substr( $raw, strpos( $raw, "\n" ) + 1 ), true );
unset( $data['marks']['horsetools_link_approved'] );   // như neo do bản cũ ghi
file_put_contents( $path, "<?php exit; ?>\n" . json_encode( $data ) );
eq( state(), 'ok', 'khoá chưa từng được neo thì bỏ qua, không phải báo động' );

echo "\n9. File neo hỏng hoặc bị xoá\n";
wipe(); approve_some(); horsetools_anchor_write();
file_put_contents( horsetools_anchor_file(), "<?php exit; ?>\nkhông phải json" );
eq( state(), 'none', 'file hỏng → coi như chưa có, không phải "khớp"' );

wipe(); approve_some(); horsetools_anchor_write();
unlink( horsetools_anchor_file() );
eq( state(), 'none', 'xoá file → nói là chưa có neo' );

echo "\n10. Không ghi được thì phải nói, không được giả vờ ổn\n";
// Chặn bằng cách đặt một TỆP đúng chỗ thư mục neo phải nằm. Cách này chạy trên
// mọi hệ điều hành, khác với chmod — Windows bỏ qua chmod trên thư mục, nên bài
// test dựa vào quyền sẽ âm thầm không kiểm được gì.
wipe();
$dir = horsetools_anchor_dir();
foreach ( (array) glob( $dir . '/*' ) as $f ) { @unlink( $f ); }
@rmdir( $dir );
file_put_contents( $dir, 'chỗ này bị chiếm' );
approve_some();
eq( horsetools_anchor_write(), false, 'ghi hỏng thì trả về false, không nuốt' );
eq( state(), 'unwritable', 'và nói rõ là không có chỗ để giữ bản sao — không phải "chưa neo"' );
unlink( $dir );

echo "\n11. Giới hạn thật, nói thẳng ra\n";
wipe(); approve_some(); horsetools_anchor_write();
// (a) Kẻ có tài khoản admin thật: bấm đúng nút chủ site bấm, cả hai bên cùng đổi.
$GLOBALS['opts']['horsetools_link_approved']['nhacai.xyz'] = 1770000000;
horsetools_anchor_touch();
eq( state(), 'ok', 'admin thật thì KHÔNG bắt được — chỉ tin báo tới tay người mới bắt' );
// (b) Kẻ ghi được file: sửa cả hai bên.
$GLOBALS['opts']['horsetools_link_approved']['thu2.xyz'] = 1770000000;
horsetools_anchor_write();
eq( state(), 'ok', 'ghi được file thì cũng KHÔNG bắt được — không plugin PHP nào chống nổi' );

echo "\n11b. CÔNG TẮC — kẻ tấn công không sửa danh sách duyệt, nó TẮT phòng thủ\n";
// Neo chỉ canh danh sách duyệt là canh nhầm cửa. Sửa danh sách duyệt là nước đi
// đắt và bị bắt to; TẮT canh gác là nước đi rẻ và trước đây không ai canh.
wipe();
$GLOBALS['horsetools_options'] = array( 'watch-hb' => 1 );
$GLOBALS['opts']['horsetools_link_guard'] = 'nofollow';
approve_some();
horsetools_anchor_write();
eq( state(), 'ok', 'khớp' );

$GLOBALS['opts']['horsetools_link_guard'] = 'off';       // ghi thẳng SQL
eq( state(), 'mismatch', 'TẮT guard bằng SQL → BỊ BẮT' );
eq( horsetools_anchor_mismatches(), array( '@switches' ), 'chỉ tay đúng vào công tắc' );

$GLOBALS['opts']['horsetools_link_guard'] = 'nofollow';
eq( state(), 'ok', 'trả lại thì hết kêu' );
$GLOBALS['horsetools_options']['watch-hb'] = 0;          // ghi thẳng SQL
eq( state(), 'mismatch', 'TẮT nhịp tim bằng SQL → BỊ BẮT' );

echo "\n11c. Đổi qua màn hình thì KHÔNG kêu\n";
horsetools_anchor_touch( array( '@switches' ) );
eq( state(), 'ok', 'neo lại phần công tắc → chấp nhận' );

echo "\n11d. Neo lại một thứ KHÔNG được rửa sạch thứ khác\n";
// Đây là lỗ nếu neo lại tất cả mỗi lần: kẻ tấn công sửa danh sách duyệt bằng SQL
// rồi chờ chủ site bấm lưu một cài đặt bất kỳ, thế là dấu vết bị ghi đè.
wipe();
$GLOBALS['horsetools_options'] = array( 'watch-hb' => 1 );
$GLOBALS['opts']['horsetools_link_guard'] = 'nofollow';
approve_some();
horsetools_anchor_write();
$GLOBALS['opts']['horsetools_link_approved']['nhacai.xyz'] = 1770000000;  // SQL
eq( horsetools_anchor_mismatches(), array( 'horsetools_link_approved' ), 'phát hiện' );
horsetools_anchor_touch( array( '@switches' ) );   // chủ site đổi một công tắc, việc không liên quan
eq( horsetools_anchor_mismatches(), array( 'horsetools_link_approved' ), 'vẫn còn dấu vết — KHÔNG bị rửa' );
horsetools_anchor_touch( array( 'horsetools_link_approved' ) );
eq( state(), 'ok', 'chỉ khi chính danh sách đó được duyệt lại mới hết' );

echo "\n11e. Bản neo cũ chưa biết công tắc thì đừng kêu oan\n";
wipe();
approve_some();
horsetools_anchor_write();
$path = horsetools_anchor_file();
$raw  = file_get_contents( $path );
$data = json_decode( substr( $raw, strpos( $raw, "\n" ) + 1 ), true );
unset( $data['marks']['@switches'] );                    // như neo do bản 1.3.33 ghi
file_put_contents( $path, "<?php exit; ?>\n" . json_encode( $data ) );
$GLOBALS['opts']['horsetools_link_guard'] = 'strip';
eq( state(), 'ok', 'khoá chưa từng được neo thì bỏ qua, không phải báo động' );

echo "\n12. Vân tay\n";
eq( horsetools_anchor_fingerprint( array( 'a' => 1, 'b' => 2 ) ), horsetools_anchor_fingerprint( array( 'b' => 9, 'a' => 8 ) ), 'chỉ tính khoá, không tính giá trị, không phụ thuộc thứ tự' );
ok( horsetools_anchor_fingerprint( array( 'a' => 1 ) ) !== horsetools_anchor_fingerprint( array( 'a' => 1, 'b' => 1 ) ), 'thêm khoá thì đổi vân tay' );
eq( horsetools_anchor_fingerprint( 'không phải mảng' ), '', 'không phải mảng thì không có vân tay' );

echo "\n13. Chưa xác nhận KHÁC với xác nhận rỗng\n";
wipe();
$GLOBALS['opts']['horsetools_link_approved'] = array();   // "tôi đã soát, không cái nào của tôi"
horsetools_anchor_write();
eq( state(), 'ok', 'duyệt rỗng vẫn là một quyết định' );
unset( $GLOBALS['opts']['horsetools_link_approved'] );
eq( state(), 'mismatch', 'biến mất hẳn thì khác — và bị bắt' );

// dọn
foreach ( (array) glob( horsetools_anchor_dir() . '/*' ) as $f ) { @unlink( $f ); }
foreach ( (array) glob( horsetools_anchor_dir() . '/.*' ) as $f ) { if ( is_file( $f ) ) { @unlink( $f ); } }
@rmdir( horsetools_anchor_dir() );
@rmdir( WP_CONTENT_DIR );
@rmdir( $GLOBALS['root'] );

echo "\n" . str_repeat( '-', 56 ) . "\n";
echo "  $pass đạt, $fail hỏng\n\n";
exit( $fail ? 1 : 0 );
