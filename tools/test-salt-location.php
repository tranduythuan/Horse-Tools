<?php
/**
 * Does the salt check actually tell a file-backed key from a database one?
 *
 * Getting this wrong is worse than not having it. A false "fail" tells owners
 * their site is exposed when it is not, and they learn to ignore the notice; a
 * false "pass" leaves the PHP snippet signature quietly worthless while the
 * health card says everything is fine.
 *
 * wp_salt() ignores a constant that is empty, that is still the placeholder, or
 * whose value is shared with another of the eight. Rather than reimplement
 * those rules, horsetools_salt_location() compares what WordPress actually
 * produced against what the database holds. These cases exercise that.
 *
 * Usage:  php tools/test-salt-location.php
 */

define( 'ABSPATH', __DIR__ . '/' );

/* ---------------------------------------------------------------- the fake */

$GLOBALS['site_options'] = array();
$GLOBALS['salt_value']   = '';

function get_site_option( $k, $d = false ) {
	return array_key_exists( $k, $GLOBALS['site_options'] ) ? $GLOBALS['site_options'][ $k ] : $d;
}
function wp_salt( $scheme = 'auth' ) { return $GLOBALS['salt_value']; }
function wp_generate_password( $len = 12, $special = true, $extra = false ) {
	return substr( str_repeat( 'aB3$xY7!', 20 ), 0, $len );
}
function current_user_can( $c ) { return true; }
function add_action( $h, $f, $p = 10, $a = 1 ) {}
function esc_html_e( $s, $d = '' ) {}
function esc_html__( $s, $d = '' ) { return $s; }
function __( $s, $d = '' ) { return $s; }
function esc_textarea( $s ) { return $s; }
function wp_json_encode( $v ) { return json_encode( $v ); }
function horsetools_is_plugin_screen() { return false; }

require_once dirname( __DIR__ ) . '/inc/salt.php';

/* -------------------------------------------------------------- the harness */

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got '$got', want '$want'\n"; }
}

/** 64 characters of pretend entropy, distinct per label. */
function ent( $label ) {
	return substr( str_pad( $label, 64, strrev( md5( $label ) ) ), 0, 64 );
}

echo "\n1. Khoá nằm trong wp-config (trường hợp bình thường)\n";
// Nothing in the options table; wp_salt() returned constants.
$GLOBALS['site_options'] = array();
$GLOBALS['salt_value']   = ent( 'file-key' ) . ent( 'file-salt' );
eq( horsetools_salt_location(), 'file', 'không có gì trong DB → file' );

// WordPress may have stored values earlier and now the constants win.
$GLOBALS['site_options'] = array( 'auth_key' => ent( 'stale-key' ), 'auth_salt' => ent( 'stale-salt' ) );
$GLOBALS['salt_value']   = ent( 'file-key' ) . ent( 'file-salt' );
eq( horsetools_salt_location(), 'file', 'DB có giá trị cũ nhưng hằng số thắng → file' );

echo "\n2. Cả hai nửa nằm trong database — đây là ca phải bắt\n";
$k = ent( 'db-key' );
$s = ent( 'db-salt' );
$GLOBALS['site_options'] = array( 'auth_key' => $k, 'auth_salt' => $s );
$GLOBALS['salt_value']   = $k . $s;
eq( horsetools_salt_location(), 'db', 'khớp chính xác chuỗi ghép → db' );

echo "\n3. Một nửa file, một nửa database — an toàn, chỉ báo nhẹ\n";
$GLOBALS['site_options'] = array( 'auth_key' => $k, 'auth_salt' => ent( 'unused' ) );
$GLOBALS['salt_value']   = $k . ent( 'file-salt' );
eq( horsetools_salt_location(), 'partial', 'nửa đầu từ DB → partial' );

$GLOBALS['site_options'] = array( 'auth_key' => ent( 'unused' ), 'auth_salt' => $s );
$GLOBALS['salt_value']   = ent( 'file-key' ) . $s;
eq( horsetools_salt_location(), 'partial', 'nửa sau từ DB → partial' );

echo "\n4. Không được nhầm 'db' khi chỉ trùng một phần\n";
// The stored key happens to be a prefix, but the whole thing does not match.
$GLOBALS['site_options'] = array( 'auth_key' => $k, 'auth_salt' => $s );
$GLOBALS['salt_value']   = $k . ent( 'file-salt' );
eq( horsetools_salt_location(), 'partial', 'nửa đầu khớp nhưng nửa sau không → partial, KHÔNG phải db' );

echo "\n5. Hàng rỗng và bất thường\n";
$GLOBALS['site_options'] = array( 'auth_key' => '', 'auth_salt' => '' );
$GLOBALS['salt_value']   = ent( 'file-key' ) . ent( 'file-salt' );
eq( horsetools_salt_location(), 'file', 'DB rỗng → file' );

$GLOBALS['site_options'] = array();
$GLOBALS['salt_value']   = '';
eq( horsetools_salt_location(), 'unknown', 'wp_salt() rỗng → unknown, không đoán bừa' );

echo "\n6. Liệt kê hằng số không dùng được\n";
// None are defined in this harness, so all eight should be reported.
eq( count( horsetools_salt_unusable() ), 8, 'chưa khai báo hằng số nào → cả 8 đều không dùng được' );

define( 'AUTH_KEY', ent( 'real-auth-key' ) );
define( 'AUTH_SALT', 'put your unique phrase here' );
define( 'SECURE_AUTH_KEY', ent( 'shared' ) );
define( 'SECURE_AUTH_SALT', ent( 'shared' ) );   // duplicated — WordPress ignores both
$bad = horsetools_salt_unusable();
eq( in_array( 'AUTH_KEY', $bad, true ), false, 'hằng số hợp lệ không bị liệt kê' );
eq( in_array( 'AUTH_SALT', $bad, true ), true, 'giá trị mẫu mặc định bị liệt kê' );
eq( in_array( 'SECURE_AUTH_KEY', $bad, true ), true, 'giá trị trùng nhau bị liệt kê (WordPress bỏ qua cả hai)' );
eq( in_array( 'SECURE_AUTH_SALT', $bad, true ), true, 'cả cái còn lại của cặp trùng' );
eq( in_array( 'NONCE_SALT', $bad, true ), true, 'hằng số chưa khai báo bị liệt kê' );

echo "\n7. Khoá gợi ý\n";
$lines = horsetools_salt_suggest();
eq( substr_count( $lines, 'define(' ), 8, 'đủ 8 dòng' );
eq( (bool) preg_match( "/^define\( 'AUTH_KEY', '.{10,}' \);$/m", $lines ), true, 'đúng cú pháp PHP' );
eq( false !== strpos( $lines, "'NONCE_SALT'" ), true, 'có cả hằng số cuối' );

printf( "\n%d passed, %d failed\n", $pass, $fail );
exit( $fail ? 1 : 0 );
