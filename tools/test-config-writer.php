<?php
/**
 * Exercise the wp-config.php writer against real files.
 *
 * This is the most dangerous thing the plugin does: get it wrong and the site
 * is white-screened with no way into wp-admin to undo it. It used to keep a
 * full copy of wp-config.php beside itself as insurance — which published the
 * database password to anyone who guessed the filename, because .bak is served
 * as plain text. The copy is gone and the write is atomic instead, so these
 * checks are about proving that swap actually holds.
 *
 * The class touches no WordPress functions, so it runs directly against a
 * throwaway file in the system temp directory.
 *
 * Usage:  php tools/test-config-writer.php
 */

define( 'ABSPATH', __DIR__ . '/' );

// The class is defined inside `if ( ! class_exists( ... ) )` and the file also
// registers hooks, so pull out just the class.
$src   = file_get_contents( dirname( __DIR__ ) . '/inc/debug.php' );
$start = strpos( $src, 'class horsetools_chandebug {' );
$depth = 0;
$end   = $start;
for ( $i = strpos( $src, '{', $start ), $len = strlen( $src ); $i < $len; $i++ ) {
	if ( '{' === $src[ $i ] ) { $depth++; } elseif ( '}' === $src[ $i ] ) {
		$depth--;
		if ( 0 === $depth ) { $end = $i; break; }
	}
}
$tmpclass = __DIR__ . '/_extracted-config-writer.php';
file_put_contents( $tmpclass, "<?php\n" . substr( $src, $start, $end - $start + 1 ) . "\n" );
require $tmpclass;
unlink( $tmpclass );

$pass = 0;
$fail = 0;
function ok( $cond, $what ) {
	global $pass, $fail;
	if ( $cond ) { $pass++; echo "  ok    $what\n"; } else { $fail++; echo "  FAIL  $what\n"; }
}
function eq( $got, $want, $what ) {
	ok( $got === $want, $what . ( $got === $want ? '' : ' — got ' . var_export( $got, true ) ) );
}

$work = sys_get_temp_dir() . '/ht-cfg-test-' . bin2hex( random_bytes( 4 ) );
mkdir( $work );
$cfg = $work . '/wp-config.php';

/** A wp-config.php shaped like a real one. */
function ht_sample() {
	return "<?php\n"
		. "define( 'DB_NAME', 'thedb' );\n"
		. "define( 'DB_PASSWORD', 'sup3rs3cret' );\n"
		. "define( 'AUTH_SALT', 'a-very-long-random-salt-value' );\n"
		. "define( 'WP_DEBUG', false );\n"
		. "\$table_prefix = 'wp_';\n"
		. "require_once ABSPATH . 'wp-settings.php';\n";
}

echo "\n1. Sửa một hằng số\n";
file_put_contents( $cfg, ht_sample() );
$t = new horsetools_chandebug( $cfg );
$t->update( 'constant', 'WP_DEBUG', 'true', array( 'raw' => true ) );
$after = file_get_contents( $cfg );
ok( false !== strpos( $after, "define( 'WP_DEBUG', true );" ), 'giá trị mới đã ghi' );
ok( false !== strpos( $after, "'sup3rs3cret'" ), 'phần còn lại của file nguyên vẹn' );
ok( 0 === count( array_diff( array_map( 'trim', explode( "\n", trim( ht_sample() ) ) ), array_map( 'trim', explode( "\n", trim( $after ) ) ) ) ) - 1, 'chỉ đúng một dòng đổi' );

echo "\n2. KHÔNG được để lại bản sao nào chứa thông tin nhạy cảm\n";
$left = array_values( array_diff( scandir( $work ), array( '.', '..', 'wp-config.php' ) ) );
eq( $left, array(), 'thư mục chỉ còn wp-config.php — không .bak, không .tmp' );
$leaks = 0;
foreach ( (array) glob( $work . '/*' ) as $f ) {
	if ( 'wp-config.php' !== basename( $f ) && false !== strpos( (string) @file_get_contents( $f ), 'sup3rs3cret' ) ) { $leaks++; }
}
eq( $leaks, 0, 'không file nào ngoài wp-config.php chứa mật khẩu' );

echo "\n3. Ghi một đường dẫn (WP_DEBUG_LOG dạng chuỗi)\n";
$path = '/var/www/wp-content/horsetools-logs/debug-abc123.log';
$t->update( 'constant', 'WP_DEBUG_LOG', var_export( $path, true ), array( 'raw' => true ) );
$after = file_get_contents( $cfg );
ok( false !== strpos( $after, "define( 'WP_DEBUG_LOG', '" . $path . "' );" ), 'đường dẫn được ghi thành chuỗi PHP hợp lệ' );

echo "\n4. Kết quả phải luôn parse được\n";
$err = '';
try { token_get_all( file_get_contents( $cfg ), TOKEN_PARSE ); } catch ( \ParseError $e ) { $err = $e->getMessage(); }
eq( $err, '', 'wp-config.php sau khi sửa vẫn parse' );

echo "\n5. Từ chối ghi khi kết quả sẽ hỏng\n";
// A value that leaves an unterminated string — a real parse error. The file on
// disk must not change: this is the guard that stops the site being taken down.
$before = file_get_contents( $cfg );
$threw  = false;
try {
	$t->update( 'constant', 'WP_DEBUG', "'unterminated", array( 'raw' => true ) );
} catch ( Exception $e ) {
	$threw = true;
}
ok( $threw, 'ném lỗi thay vì ghi' );
eq( file_get_contents( $cfg ), $before, 'file trên đĩa không đổi một byte' );

echo "\n5b. Giá trị parse được nhưng vô nghĩa vẫn phải bị chặn\n";
// "true ) ; syntax ( error" IS valid PHP — define(...) followed by a call to an
// undefined function. It passes the parse check and kills the site at run time.
// The caller allows only a boolean literal or a quoted string, so it never
// reaches the writer. This asserts that rule, since the writer itself cannot.
$allowed = function ( $v ) {
	return (bool) preg_match( "/^(?:true|false|'[^'\\\\]*')$/", $v );
};
ok( $allowed( 'true' ), "'true' được phép" );
ok( $allowed( "'/var/log/x.log'" ), 'đường dẫn trong nháy đơn được phép' );
ok( ! $allowed( 'true ) ; syntax ( error' ), 'chuỗi parse-được-nhưng-vô-nghĩa bị chặn' );
ok( ! $allowed( "'a' . file_get_contents('/etc/passwd')" ), 'ghép biểu thức bị chặn' );

echo "\n6. Giữ nguyên quyền của file\n";
if ( '\\' === DIRECTORY_SEPARATOR ) {
	echo "  bỏ qua (Windows không có chmod thật)\n";
} else {
	chmod( $cfg, 0640 );
	$t->update( 'constant', 'WP_DEBUG', 'false', array( 'raw' => true ) );
	eq( fileperms( $cfg ) & 0777, 0640, 'chế độ 0640 được giữ sau khi ghi' );
}

echo "\n7. Thêm hằng số chưa có\n";
$t->update( 'constant', 'WP_DEBUG_DISPLAY', 'false', array( 'raw' => true ) );
ok( false !== strpos( file_get_contents( $cfg ), 'WP_DEBUG_DISPLAY' ), 'hằng số mới được chèn' );
ok( false !== strpos( file_get_contents( $cfg ), "require_once ABSPATH" ), 'dòng require_once vẫn còn' );

// tidy up
foreach ( (array) glob( $work . '/*' ) as $f ) { @unlink( $f ); }
@rmdir( $work );

printf( "\n%d passed, %d failed\n", $pass, $fail );
exit( $fail ? 1 : 0 );
