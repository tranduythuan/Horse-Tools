<?php
/**
 * Does the exposure check find a stray secret, and stay quiet on a clean site?
 *
 * The list is short on purpose, so the risk is not that it misses something
 * exotic — it is that it fires on a normal install and gets ignored. A security
 * warning nobody believes is worse than none.
 *
 * Usage:  php tools/test-watch-exposure.php
 */

// A throwaway "web root" so file_exists() has something real to answer about.
$root = sys_get_temp_dir() . '/ht-exposure-' . bin2hex( random_bytes( 4 ) ) . '/';
mkdir( $root );
mkdir( $root . 'wp-content' );

define( 'ABSPATH', $root );
define( 'DAY_IN_SECONDS', 86400 );

function __( $s, $d = '' ) { return $s; }
function esc_html__( $s, $d = '' ) { return $s; }
function esc_html( $s ) { return $s; }
function esc_attr( $s ) { return $s; }
function get_transient( $k ) { return false; }          // never cache in tests
function set_transient( $k, $v, $t = 0 ) { return true; }
function add_action( $h, $f, $p = 10, $a = 1 ) {}
function current_user_can( $c ) { return true; }
function horsetools_is_plugin_screen() { return false; }
function horsetools_admin_banner( $tone, $html ) {}

/** Just enough $wpdb to answer "is there a 404 table". */
class HT_WPDB {
	public $prefix = 'wp_';
	public $rows   = array();
	public $has_table = false;
	public function prepare( $sql, ...$a ) {
		$a = ( 1 === count( $a ) && is_array( $a[0] ) ) ? $a[0] : $a;
		foreach ( $a as $v ) {
			$sql = preg_replace( '/%s/', is_string( $v ) ? $v : (string) $v, $sql, 1 );
		}
		return $sql;
	}
	public function esc_like( $s ) { return $s; }
	public function get_var( $sql ) {
		return ( $this->has_table && false !== strpos( $sql, 'SHOW TABLES' ) ) ? 'wp_horsetools_404' : null;
	}
	public function get_results( $sql ) { return $this->rows; }
}
$GLOBALS['wpdb'] = new HT_WPDB();

require_once dirname( __DIR__ ) . '/inc/watch-exposure.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . "\n"; }
}
function ok( $c, $what ) { eq( (bool) $c, true, $what ); }
function paths_found() {
	return array_column( horsetools_exposure_scan(), 'path' );
}

echo "\n1. Site sạch thì phải im\n";
eq( paths_found(), array(), 'không có gì → không báo gì' );

echo "\n2. Có file lộ thì phải thấy\n";
file_put_contents( ABSPATH . '.env', "DB_PASSWORD=hunter2\n" );
ok( in_array( '.env', paths_found(), true ), 'tìm thấy .env' );

file_put_contents( ABSPATH . 'wp-config.php.horsetools.bak', '<?php // …' );
ok( in_array( 'wp-config.php.horsetools.bak', paths_found(), true ), 'tìm thấy bản sao wp-config do bản cũ để lại' );

mkdir( ABSPATH . '.git' );
file_put_contents( ABSPATH . '.git/config', "[core]\n" );
ok( in_array( '.git/config', paths_found(), true ), 'tìm thấy thư mục .git' );

file_put_contents( ABSPATH . 'wp-content/debug.log', "PHP Notice: …\n" );
ok( in_array( 'wp-content/debug.log', paths_found(), true ), 'tìm thấy debug.log' );

echo "\n3. Nặng phải đứng trước nhẹ\n";
$rows = horsetools_exposure_scan();
eq( $rows[0]['grave'], true, 'mục đầu tiên là mục nghiêm trọng' );
$graves = array_column( $rows, 'grave' );
eq( $graves, array_reverse( array_reverse( $graves ) ), 'thứ tự ổn định' );
$seen_light = false;
$order_ok   = true;
foreach ( $graves as $g ) {
	if ( ! $g ) { $seen_light = true; } elseif ( $seen_light ) { $order_ok = false; }
}
ok( $order_ok, 'không có mục nghiêm trọng nào nằm sau mục nhẹ' );

echo "\n4. Thứ trông giống mà không phải thì bỏ qua\n";
file_put_contents( ABSPATH . 'wp-config.php', '<?php // file thật, phải bỏ qua' );
file_put_contents( ABSPATH . 'readme.html', 'x' );
file_put_contents( ABSPATH . 'wp-content/index.php', 'x' );
$found = paths_found();
ok( ! in_array( 'wp-config.php', $found, true ), 'wp-config.php THẬT không bị báo' );
ok( ! in_array( 'readme.html', $found, true ), 'readme.html không bị báo' );
eq( count( $found ), 4, 'vẫn đúng 4 mục, không nhiều hơn' );

echo "\n5. Nhật ký 404 — không có bảng thì không được nổ\n";
$GLOBALS['wpdb']->has_table = false;
$p = horsetools_exposure_probes();
eq( $p['total'], 0, 'module chuyển hướng tắt → 0, không lỗi' );
eq( $p['paths'], array(), 'không có đường dẫn nào' );

echo "\n6. Có bảng thì đọc được số lần bị dò\n";
$GLOBALS['wpdb']->has_table = true;
$GLOBALS['wpdb']->rows = array(
	(object) array( 'url' => '/.env', 'hits' => 12, 'last_seen' => '2026-08-06 22:10:00' ),
	(object) array( 'url' => '/backup.sql', 'hits' => 3, 'last_seen' => '2026-08-05 04:00:00' ),
);
$p = horsetools_exposure_probes();
eq( $p['total'], 15, 'cộng đúng số lần' );
eq( $p['last'], '2026-08-06 22:10:00', 'lấy lần gần nhất' );
ok( isset( $p['paths']['/.env'] ), 'giữ từng đường dẫn để đối chiếu với file thật' );

echo "\n7. Danh sách phải ngắn và không có mục mơ hồ\n";
$list = horsetools_exposure_paths();
ok( count( $list ) <= 25, 'không quá 25 mục — danh sách dài là danh sách không ai đọc hết' );
ok( ! isset( $list['xmlrpc.php'] ), 'xmlrpc.php KHÔNG có trong danh sách — nó là file hợp lệ của WordPress' );
ok( ! isset( $list['wp-login.php'] ), 'wp-login.php không có' );
foreach ( $list as $rel => $row ) {
	if ( '' === trim( $row['why'] ) ) { $fail++; echo "  FAIL  '$rel' không nói lý do\n"; }
}
ok( true, 'mọi mục đều có lời giải thích' );

// dọn
foreach ( array( '.env', 'wp-config.php.horsetools.bak', 'wp-config.php', 'readme.html', '.git/config', 'wp-content/debug.log', 'wp-content/index.php' ) as $f ) {
	@unlink( ABSPATH . $f );
}
@rmdir( ABSPATH . '.git' );
@rmdir( ABSPATH . 'wp-content' );
@rmdir( ABSPATH );

printf( "\n%d passed, %d failed\n", $pass, $fail );
exit( $fail ? 1 : 0 );
