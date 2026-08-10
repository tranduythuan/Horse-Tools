<?php
/**
 * Does the shared content walk finish, resume, and know when it is out of date?
 *
 * Two watchers now read every published post through this one cursor, and more
 * will. The dangerous failure is not a crash — it is the walk quietly declaring
 * itself finished over results that were produced by code that no longer exists,
 * because then every screen reports "all agreed" about a set nobody has actually
 * looked at with the current rules.
 *
 * Usage:  php tools/test-watch-scan.php
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'MINUTE_IN_SECONDS', 60 );

function __( $s, $d = '' ) { return $s; }
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
function wp_create_nonce( $a ) { return 'n'; }
function check_ajax_referer( $a, $b ) { return true; }
function wp_send_json_error( $d = null ) {}
function wp_send_json_success( $d = null ) {}
function esc_attr( $s ) { return $s; }
function esc_html_e( $s, $d = '' ) { echo $s; }
function wp_json_encode( $v ) { return json_encode( $v ); }
function wp_doing_ajax() { return false; }
function wp_doing_cron() { return false; }
function get_post_types( $a = array(), $b = 'names' ) { return array( 'post' => 'post', 'page' => 'page', 'attachment' => 'attachment' ); }
function post_type_exists( $t ) { return false; }
function current_time( $t ) { return $GLOBALS['clock']; }

$GLOBALS['opts']    = array();
$GLOBALS['trans']   = array();
$GLOBALS['filters'] = array();
$GLOBALS['clock']   = '2026-08-07 10:00:00';

/**
 * A posts table just real enough: LIMIT/OFFSET paging on the first pass and
 * `post_modified > since` afterwards are the two behaviours under test, so both
 * are honoured rather than stubbed away.
 */
class Fake_Wpdb {
	public $posts = 'wp_posts';
	public $rows  = array();
	public $queries = 0;

	public function prepare( $sql, $args ) { return array( $sql, $args ); }

	public function get_var( $q ) {
		$this->queries++;
		return count( $this->rows );
	}

	public function get_results( $q ) {
		$this->queries++;
		list( $sql, $args ) = $q;
		if ( false !== strpos( $sql, 'post_modified >' ) ) {
			$since = $args[ count( $args ) - 2 ];
			$limit = (int) $args[ count( $args ) - 1 ];
			$out   = array();
			foreach ( $this->rows as $row ) {
				if ( $row->post_modified > $since ) { $out[] = $row; }
			}
			usort( $out, function ( $a, $b ) { return strcmp( $a->post_modified, $b->post_modified ); } );
			return array_slice( $out, 0, $limit );
		}
		$limit  = (int) $args[ count( $args ) - 2 ];
		$offset = (int) $args[ count( $args ) - 1 ];
		$rows   = $this->rows;
		usort( $rows, function ( $a, $b ) { return $a->ID <=> $b->ID; } );
		return array_slice( $rows, $offset, $limit );
	}
}
$wpdb = new Fake_Wpdb();
function fake_post( $id, $modified = '2026-01-01 00:00:00' ) {
	return (object) array(
		'ID'            => $id,
		'post_title'    => 'Bài ' . $id,
		'post_excerpt'  => '',
		'post_content'  => 'nội dung ' . $id,
		'post_modified' => $modified,
	);
}
for ( $i = 1; $i <= 7; $i++ ) { $wpdb->rows[] = fake_post( $i ); }

require_once dirname( __DIR__ ) . '/inc/watch-scan.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . "\n"; }
}
function ok( $c, $what ) { eq( (bool) $c, true, $what ); }

// Two collectors, each recording which post IDs it was handed.
$GLOBALS['seen_a'] = array();
$GLOBALS['seen_b'] = array();
function collector_a( $c ) {
	$c['alpha'] = array(
		'batch' => function ( $rows ) { foreach ( $rows as $r ) { $GLOBALS['seen_a'][] = (int) $r->ID; } },
		'reset' => function () { $GLOBALS['seen_a'] = array(); },
	);
	return $c;
}
function collector_b( $c ) {
	$c['beta'] = array(
		'batch' => function ( $rows ) { foreach ( $rows as $r ) { $GLOBALS['seen_b'][] = (int) $r->ID; } },
		'reset' => function () { $GLOBALS['seen_b'] = array(); },
	);
	return $c;
}
add_filter( 'horsetools_scan_collectors', 'collector_a' );

echo "\n1. Không có collector nào thì không đọc gì cả\n";
$GLOBALS['filters'] = array();
$before = $wpdb->queries;
$r = horsetools_scan_batch( 3 );
eq( $wpdb->queries, $before, 'không chạm vào cơ sở dữ liệu' );
eq( $r['done'], true, 'coi như xong' );
add_filter( 'horsetools_scan_collectors', 'collector_a' );

echo "\n2. Lượt đầu đi theo lô và nhớ chỗ đang dở\n";
$r = horsetools_scan_batch( 3 );
eq( $r['scanned'], 3, 'lô đầu ba bài' );
eq( $r['offset'], 3, 'con trỏ ở 3' );
eq( $r['total'], 7, 'biết tổng cộng 7' );
eq( $r['done'], false, 'chưa xong' );
eq( $GLOBALS['seen_a'], array( 1, 2, 3 ), 'collector nhận đúng ba bài đầu' );
eq( horsetools_scan_finished(), false, 'chưa được coi là xong' );

$r = horsetools_scan_batch( 3 );
eq( $r['offset'], 6, 'tiếp tục từ chỗ dở, không đọc lại' );
eq( $GLOBALS['seen_a'], array( 1, 2, 3, 4, 5, 6 ), 'không bài nào bị đọc hai lần' );

echo "\n3. Lô cuối ngắn hơn thì kết thúc\n";
$r = horsetools_scan_batch( 3 );
eq( $r['scanned'], 1, 'còn đúng một bài' );
eq( $r['done'], true, 'xong' );
eq( horsetools_scan_finished(), true, 'giờ mới được coi là xong' );
eq( $GLOBALS['seen_a'], range( 1, 7 ), 'đủ bảy bài' );

echo "\n4. Xong rồi thì chỉ đọc bài vừa sửa\n";
$GLOBALS['seen_a'] = array();
$r = horsetools_scan_batch( 3 );
eq( $r['scanned'], 0, 'không có gì đổi thì không đọc gì' );
eq( $GLOBALS['seen_a'], array(), 'collector không bị gọi' );

$GLOBALS['clock'] = '2026-08-07 11:00:00';
$wpdb->rows[]     = fake_post( 8, '2026-08-07 10:30:00' );
$r = horsetools_scan_batch( 3 );
eq( $r['scanned'], 1, 'bài mới sửa được đọc' );
eq( $GLOBALS['seen_a'], array( 8 ), 'đúng bài đó' );

$GLOBALS['seen_a'] = array();
$r = horsetools_scan_batch( 3 );
eq( $r['scanned'], 0, 'đọc xong rồi thì thôi, không lặp lại mãi' );

echo "\n5. Thêm một collector mới thì phải đọc lại từ đầu\n";
$sig_before = horsetools_scan_signature();
add_filter( 'horsetools_scan_collectors', 'collector_b' );
ok( horsetools_scan_signature() !== $sig_before, 'chữ ký đổi' );
eq( horsetools_scan_finished(), false, 'kết quả cũ KHÔNG còn được coi là xong' );

$GLOBALS['seen_a'] = array( 999 ); // rác từ lượt trước
$r = horsetools_scan_batch( 100 );
eq( $GLOBALS['seen_a'], range( 1, 8 ), 'collector cũ cũng bị xoá và đọc lại — nếu không, số đếm nhân đôi' );
eq( $GLOBALS['seen_b'], range( 1, 8 ), 'collector mới thấy toàn bộ nội dung' );
eq( horsetools_scan_finished(), true, 'xong lại' );

echo "\n6. Thứ tự đăng ký không được làm đổi chữ ký\n";
$sig = horsetools_scan_signature();
$GLOBALS['filters'] = array();
add_filter( 'horsetools_scan_collectors', 'collector_b' );
add_filter( 'horsetools_scan_collectors', 'collector_a' );
eq( horsetools_scan_signature(), $sig, 'đăng ký ngược lại vẫn cùng chữ ký' );
eq( horsetools_scan_finished(), true, 'nên không bắt quét lại vô cớ' );

echo "\n7. Nhịp chạy nền không được đọc dồn dập\n";
$GLOBALS['trans'] = array();
$GLOBALS['seen_a'] = array();
horsetools_scan_tick();
horsetools_scan_tick();
horsetools_scan_tick();
eq( $GLOBALS['seen_a'], array(), 'đã xong và chưa có gì đổi thì ba lần gọi cũng không đọc gì' );
ok( isset( $GLOBALS['trans']['horsetools_scan_tick'] ), 'có khoá chặn 5 giây' );

echo "\n7b. DỌN DẸP — sau khi gỡ thứ bị chèn, phải có đường về\n";
// Đây là lỗ nặng nhất tìm ra khi tự rà soát. Kho chỉ THÊM, không bao giờ BỚT, và
// lượt quét thường chỉ đọc bài vừa sửa — mà một bài không còn chứa thứ gì thì
// không thể tự báo là nó không còn chứa. Hậu quả: kẻ tấn công chèn link, plugin
// báo, chủ site dọn bài — và tên miền vẫn nằm nguyên trong danh sách chưa duyệt,
// dòng sức khoẻ vẫn đỏ, và cái nút duy nhất làm nó im là nút DUYỆT tên miền của
// kẻ tấn công. Làm đúng thì chuông vẫn kêu, làm sai thì chuông tắt.
$GLOBALS['seen_a'] = array();
$GLOBALS['seen_b'] = array();
$wpdb->rows = array();
for ( $i = 1; $i <= 3; $i++ ) { $wpdb->rows[] = fake_post( $i ); }
$GLOBALS['opts'] = array();
$GLOBALS['trans'] = array();
horsetools_scan_batch( 100 );
eq( horsetools_scan_finished(), true, 'quét xong' );
eq( $GLOBALS['seen_a'], array( 1, 2, 3 ), 'đọc cả ba bài' );

// Chủ site dọn bài 2 rồi muốn danh sách phản ánh hiện tại.
$GLOBALS['seen_a'] = array();
horsetools_scan_reset();
eq( horsetools_scan_finished(), false, 'quét lại: chưa xong nữa' );
eq( $GLOBALS['seen_a'], array(), 'và collector đã bị xoá sạch — nếu không thì số cũ vẫn còn' );
horsetools_scan_batch( 100 );
eq( $GLOBALS['seen_a'], array( 1, 2, 3 ), 'đọc lại toàn bộ từ đầu' );
eq( horsetools_scan_finished(), true, 'xong lại' );

echo "\n8. Tệp đính kèm không phải nội dung\n";
ok( ! in_array( 'attachment', horsetools_scan_post_types(), true ), 'bỏ attachment' );
ok( in_array( 'post', horsetools_scan_post_types(), true ), 'giữ post' );

echo "\n" . str_repeat( '-', 56 ) . "\n";
echo "  $pass đạt, $fail hỏng\n\n";
exit( $fail ? 1 : 0 );
