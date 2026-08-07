<?php
/**
 * Is the debug log actually unreadable over HTTP?
 *
 * It was not. The folder got an `.htaccess` saying deny, and on nginx an
 * `.htaccess` is a text file — nginx has never read one. Checked on a live host
 * on 2026-08-07: requesting `wp-content/horsetools-anchor/.htaccess` came back
 * 200 with the whole file as `application/octet-stream`. The same was true of
 * `wp-content/horsetools-logs/`, where the only thing between a passer-by and a
 * log full of queries and server paths was a sixteen-character name.
 *
 * So the log is now a `.php` file that exits on its first line, which is what
 * inc/anchor.php already did. The important test here is not a string comparison:
 * it runs the log file through the PHP binary and checks that the output is
 * empty. That is the thing being claimed, so that is the thing to prove.
 *
 * The rest is about the ways the guard can be lost — a clear button that
 * truncates it away, a file deleted by hand and recreated by error_log(), an
 * upgrade that leaves the old `.log` sitting there — because a protection that
 * holds only until somebody presses Clear is not one.
 *
 * Usage:  php tools/test-debug-log.php
 */

// WP_DEBUG_LOG is a constant, so one process can only ever see it hold one
// value — and two of the things worth checking need it to hold two different
// ones. The second scenario therefore runs as a child process. It is the same
// file with an argument, so there is nothing extra to keep in step.
$mode = isset( $argv[1] ) ? $argv[1] : '';

$root = sys_get_temp_dir() . '/ht-debuglog-' . bin2hex( random_bytes( 4 ) ) . '/';
mkdir( $root );
mkdir( $root . 'wp-content' );

define( 'ABSPATH', $root );
define( 'WP_CONTENT_DIR', $root . 'wp-content' );

$GLOBALS['opts'] = array();
$GLOBALS['json'] = null;

function __( $s, $d = '' ) { return $s; }
function esc_html( $s ) { return $s; }
function get_option( $k, $default = false ) {
	return array_key_exists( $k, $GLOBALS['opts'] ) ? $GLOBALS['opts'][ $k ] : $default;
}
function update_option( $k, $v, $a = null ) { $GLOBALS['opts'][ $k ] = $v; return true; }
function add_action( $h, $f, $p = 10, $a = 1 ) {}
function is_admin() { return true; }
function wp_doing_ajax() { return false; }
function wp_doing_cron() { return false; }
function current_user_can( $c ) { return true; }
function check_ajax_referer( $a, $b ) { return true; }
function wp_die( $m = '' ) { throw new Sent_Json(); }
function wp_mkdir_p( $dir ) { return is_dir( $dir ) || mkdir( $dir, 0777, true ); }
function wp_unslash( $s ) { return is_string( $s ) ? stripslashes( $s ) : $s; }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }

class Sent_Json extends Exception {}
function wp_send_json_success( $d = null ) { $GLOBALS['json'] = array( true, $d ); throw new Sent_Json(); }
function wp_send_json_error( $d = null, $c = null ) { $GLOBALS['json'] = array( false, $d ); throw new Sent_Json(); }
function call_ajax( $fn ) { $GLOBALS['json'] = null; try { $fn(); } catch ( Sent_Json $e ) {} return $GLOBALS['json']; }

$horsetools_debug_options = array();

require_once dirname( __DIR__ ) . '/inc/server.php';
require_once dirname( __DIR__ ) . '/inc/debug.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . "\n"; }
}
function ok( $c, $what ) { eq( (bool) $c, true, $what ); }

/** What a web server would send back if it handed this file to PHP. */
function served( $path ) {
	$out = array();
	$code = 0;
	exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $path ) . ' 2>&1', $out, $code );
	return implode( "\n", $out );
}

/* -------------------------------------------------------------------------
 * The child run: WP_DEBUG_LOG points at the protected log, so the real AJAX
 * handlers can be called rather than imitated. The button that clears the log
 * is the one place a person can destroy the guard by pressing something, which
 * makes it the branch most worth running for real.
 * ---------------------------------------------------------------------- */
if ( 'handlers' === $mode ) {
	$path = horsetools_debug_log_path();
	define( 'WP_DEBUG_LOG', $path );

	file_put_contents( $path, "PHP Notice: dong log thu nhat\nPHP Notice: dong thu hai\n", FILE_APPEND );

	$json = call_ajax( 'horsetools_get_debug_log_callback' );
	eq( $json[0], true, 'đọc được log' );
	eq( strpos( $json[1], '<?php' ), false, 'nội dung trả về cho màn hình KHÔNG chứa dòng chặn' );
	ok( false !== strpos( $json[1], 'dong thu hai' ), 'nhưng có đủ các dòng log thật' );

	$json = call_ajax( 'horsetools_clear_debug_log' );
	eq( $json[0], true, 'nút Xoá hết chạy được' );
	eq( file_get_contents( $path ), "<?php exit; ?>\n", 'ĐÂY LÀ CHỖ DỄ HỎNG NHẤT: xoá xong file vẫn còn dòng chặn' );
	eq( served( $path ), '', 'nên log vừa xoá vẫn không tải về được' );

	file_put_contents( $path, "loi moi ngay sau khi xoa\n", FILE_APPEND );
	eq( served( $path ), '', 'và lỗi ghi tiếp sau đó cũng không lộ' );

	$json = call_ajax( 'horsetools_get_debug_log_callback' );
	eq( $json[1], "loi moi ngay sau khi xoa\n", 'màn hình đọc lại đúng phần log, không kèm thẻ PHP' );

	foreach ( (array) glob( horsetools_debug_log_dir() . '/*' ) as $f ) { @unlink( $f ); }
	@unlink( horsetools_debug_log_dir() . '/.htaccess' );
	@rmdir( horsetools_debug_log_dir() );
	@rmdir( WP_CONTENT_DIR );
	@rmdir( ABSPATH );
	printf( "#RESULT %d %d\n", $pass, $fail );
	exit( $fail ? 1 : 0 );
}

echo "\n1. Tên file phải tự bảo vệ được\n";
$name = horsetools_debug_log_name();
ok( (bool) preg_match( '/^debug-[a-f0-9]{16}\.log\.php$/', $name ), 'đuôi .php và 16 ký tự ngẫu nhiên' );
eq( horsetools_debug_log_name(), $name, 'gọi lại vẫn ra đúng tên đó' );

$GLOBALS['opts']['horsetools_debug_log_name'] = 'debug-0123456789abcdef.log';
$new = horsetools_debug_log_name();
ok( $new !== 'debug-0123456789abcdef.log', 'tên kiểu cũ (.log) bị thay — đó là tên tải về được trên nginx' );
ok( (bool) preg_match( '/\.log\.php$/', $new ), 'thay bằng tên kiểu mới' );

echo "\n2. Thư mục và file được dựng đủ\n";
$path = horsetools_debug_log_path();
$dir  = horsetools_debug_log_dir();
ok( is_dir( $dir ), 'có thư mục' );
ok( file_exists( $dir . '/index.php' ), 'có index.php' );
ok( file_exists( $dir . '/.htaccess' ), 'vẫn ghi .htaccess — đúng và miễn phí trên Apache, chỉ là không được tin cậy' );
ok( file_exists( $path ), 'file log được tạo sẵn, không để error_log() tự tạo (nó tạo ra file rỗng, không có dòng chặn)' );
eq( substr( file_get_contents( $path ), 0, 5 ), '<?php', 'mở đầu bằng thẻ PHP' );

echo "\n3. ĐIỀU QUAN TRỌNG NHẤT: máy chủ giao file này cho PHP thì trả về rỗng\n";
file_put_contents( $path, "[07-Aug-2026] PHP Notice: undefined index in /home/site/public_html/wp-content/x.php\n", FILE_APPEND );
file_put_contents( $path, "SELECT * FROM wp_users WHERE user_login = 'admin'\n", FILE_APPEND );
file_put_contents( $path, "<?php echo 'PWNED'; ?>\n", FILE_APPEND );
eq( served( $path ), '', 'chạy qua PHP ra 0 byte — không lộ log, không chạy thứ ai đó ghi vào log' );
ok( false !== strpos( file_get_contents( $path ), 'wp_users' ), 'nhưng nội dung vẫn còn nguyên trong file để chủ site đọc' );

echo "\n4. error_log() ghi nối vào cuối, dòng chặn không suy suyển\n";
for ( $i = 0; $i < 20; $i++ ) {
	file_put_contents( $path, "line $i\n", FILE_APPEND );
}
eq( substr( file_get_contents( $path ), 0, 5 ), '<?php', 'ghi 20 lần vẫn còn dòng đầu' );
eq( served( $path ), '', 'và vẫn trả về rỗng' );

echo "\n5. Xoá log KHÔNG được xoá luôn dòng chặn\n";
$json = call_ajax( 'horsetools_clear_debug_log' );
eq( $json[0], false, 'chưa bật WP_DEBUG_LOG thì báo lỗi chứ không im lặng coi như xong' );

// Nút thật chạy trong tiến trình con, vì nó cần WP_DEBUG_LOG trỏ vào file này.
$out  = array();
$code = 0;
exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' handlers 2>&1', $out, $code );
foreach ( $out as $line ) {
	if ( 0 === strpos( $line, '#RESULT ' ) ) {
		list( , $p, $f ) = explode( ' ', $line );
		$pass += (int) $p;
		$fail += (int) $f;
		continue;
	}
	echo $line . "\n";
}

$plain = $dir . '/plain.txt';
file_put_contents( $plain, "khong co dong chan\nnoi dung\n" );
horsetools_debug_log_truncate( $plain );
eq( file_get_contents( $plain ), '', 'log thường (chủ site tự trỏ WP_DEBUG_LOG đi chỗ khác) thì vẫn xoá sạch như trước' );
unlink( $plain );

echo "\n6. Người ta xoá file bằng tay, PHP tạo lại — dòng chặn phải quay lại\n";
unlink( $path );
horsetools_debug_log_guard_keep( $path );
ok( file_exists( $path ), 'tạo lại' );
eq( file_get_contents( $path ), "<?php exit; ?>\n", 'với dòng chặn' );

file_put_contents( $path, "PHP Fatal error: nothing in front of me\n" );  // như error_log() tự tạo
horsetools_debug_log_guard_keep( $path );
eq( substr( file_get_contents( $path ), 0, 5 ), '<?php', 'file trần bị vá lại' );
ok( false !== strpos( file_get_contents( $path ), 'nothing in front of me' ), 'giữ nguyên nội dung đã có' );
eq( served( $path ), '', 'và lại kín' );

$outside = $root . 'wp-content/somebody-elses.log';
file_put_contents( $outside, "day la log cua nguoi khac\n" );
horsetools_debug_log_guard_keep( $outside );
eq( file_get_contents( $outside ), "day la log cua nguoi khac\n", 'file ngoài thư mục của plugin thì KHÔNG đụng vào — chủ site trỏ WP_DEBUG_LOG đi đâu là việc của họ' );
unlink( $outside );

echo "\n7. Log kiểu cũ để lại phải được thu về, không được xoá mất\n";
$orphan = $dir . '/debug-aaaabbbbccccdddd.log';
file_put_contents( $orphan, "loi cu 1\nloi cu 2\n" );
eq( horsetools_debug_log_secure_orphans(), 1, 'thấy và thu 1 file' );
ok( ! file_exists( $orphan ), 'file .log cũ không còn nằm đó nữa' );
ok( false !== strpos( file_get_contents( $path ), 'loi cu 2' ), 'nội dung cũ được nối vào file mới, không bị vứt đi' );
eq( served( $path ), '', 'sau khi nhập vào vẫn kín' );

eq( horsetools_debug_log_secure_orphans(), 0, 'không còn gì thì không làm gì' );
ok( file_exists( $path ), 'và tuyệt đối không tự nuốt chính nó — debug-*.log không khớp debug-*.log.php' );

echo "\n8. wp-content/debug.log (mặc định của WordPress) cũng phải thu về\n";
file_put_contents( WP_CONTENT_DIR . '/debug.log', "log mac dinh cua wordpress\n" );
eq( horsetools_debug_log_adopt(), 1, 'thu về 1 file' );
ok( ! file_exists( WP_CONTENT_DIR . '/debug.log' ), 'không còn file công khai' );
ok( false !== strpos( file_get_contents( $path ), 'log mac dinh cua wordpress' ), 'nội dung được giữ' );

echo "\n9. Máy chủ nào đọc .htaccess, máy chủ nào không\n";
$_SERVER['SERVER_SOFTWARE'] = 'nginx/1.24.0';
eq( horsetools_htaccess_state(), 'ignored', 'nginx — chắc chắn không đọc' );
ok( horsetools_htaccess_ignored(), 'và cờ rút gọn cũng vậy' );
$_SERVER['SERVER_SOFTWARE'] = 'Apache/2.4.58 (Unix)';
eq( horsetools_htaccess_state(), 'honoured', 'Apache — có đọc' );
$_SERVER['SERVER_SOFTWARE'] = 'LiteSpeed';
eq( horsetools_htaccess_state(), 'honoured', 'LiteSpeed — có đọc' );
$_SERVER['SERVER_SOFTWARE'] = 'Microsoft-IIS/10.0';
eq( horsetools_htaccess_state(), 'ignored', 'IIS — không đọc' );
$_SERVER['SERVER_SOFTWARE'] = 'mot-thu-gi-do-la-hoac';
eq( horsetools_htaccess_state(), 'unknown', 'không nhận ra thì nói là không biết, KHÔNG đoán — cảnh báo vì đoán sai là cảnh báo không ai tin' );

echo "\n10. Cron và WP-CLI không có SERVER_SOFTWARE — phải nhớ lại được\n";
$_SERVER['SERVER_SOFTWARE'] = 'nginx/1.24.0';
horsetools_server_software_record();
eq( $GLOBALS['opts']['horsetools_server_software'], 'nginx/1.24.0', 'ghi nhớ khi có request web' );
unset( $_SERVER['SERVER_SOFTWARE'] );
eq( horsetools_htaccess_state(), 'ignored', 'trong cron vẫn trả lời đúng như lúc chạy web — nếu không thì tin báo định kỳ nói một đằng, màn hình nói một nẻo' );

echo "\n11. Màn hình xem log phải cắt dòng chặn đi\n";
eq( horsetools_php_guard_strip( "<?php exit; ?>\nmot dong log\n" ), "mot dong log\n", 'cắt đúng một dòng đầu' );
eq( horsetools_php_guard_strip( "khong co gi de cat\n" ), "khong co gi de cat\n", 'file không có dòng chặn thì để nguyên' );
eq( horsetools_php_guard_strip( '<?php exit;' ), '', 'file chỉ có mỗi dòng chặn → rỗng, không hiện thẻ PHP ra cho người ta xoá' );

// WP_DEBUG_LOG là hằng số, định nghĩa được đúng một lần — nên phần dùng tới nó
// nằm cuối cùng.
echo "\n12. Log ĐANG được dùng thì không được đổi tên khi wp-config.php không ghi được\n";
$stuck = $dir . '/debug-1111222233334444.log';
file_put_contents( $stuck, "van dang duoc ghi\n" );
define( 'WP_DEBUG_LOG', $stuck );
eq( horsetools_debug_log_current(), $stuck, 'đọc từ hằng số, không tự suy ra' );
eq( horsetools_debug_log_secure_orphans(), 0, 'bỏ qua — đổi tên nó thì PHP tạo lại ngay dưới đúng cái tên hở đó, mỗi lần mở trang admin một lần, mãi mãi' );
ok( file_exists( $stuck ), 'file vẫn còn' );
eq( file_get_contents( $stuck ), "van dang duoc ghi\n", 'và còn nguyên' );
// Đó là lúc cảnh báo "file tải về được" phải nổ, và nó có nổ — xem
// tools/test-watch-exposure.php phần 8.

// dọn
foreach ( (array) glob( $dir . '/*' ) as $f ) { @unlink( $f ); }
@unlink( $dir . '/.htaccess' );
@rmdir( $dir );
foreach ( (array) glob( WP_CONTENT_DIR . '/*' ) as $f ) { @unlink( $f ); }
@rmdir( WP_CONTENT_DIR );
@rmdir( $root );

echo "\n" . str_repeat( '-', 56 ) . "\n";
echo "  $pass đạt, $fail hỏng\n\n";
exit( $fail ? 1 : 0 );
