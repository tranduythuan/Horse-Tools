<?php
/**
 * Prove that signing in with Google cannot skip two-factor authentication.
 *
 * This is the one path where a session used to be issued without the second
 * factor ever running, and it is not testable on a live site here — neither
 * site has the Google module switched on, and the flow needs a real OAuth
 * round trip. So the decision itself is tested instead: the real function
 * source is lifted out of inc/goo.php and run against stubs, so this checks
 * the shipped code rather than a copy of it.
 *
 * Usage:  php tools/test-google-login.php
 */

/** Lift one function's source out of a file, by brace matching. */
function ht_extract_function( $file, $name ) {
	$code = file_get_contents( $file );
	if ( ! preg_match( '/function\s+' . preg_quote( $name, '/' ) . '\s*\(/', $code, $m, PREG_OFFSET_CAPTURE ) ) {
		fwrite( STDERR, "không tìm thấy $name trong $file\n" );
		exit( 1 );
	}
	$from  = $m[0][1];
	$start = strpos( $code, '{', $from );
	$d     = 0;
	$len   = strlen( $code );
	for ( $j = $start; $j < $len; $j++ ) {
		if ( '{' === $code[ $j ] ) {
			$d++;
		} elseif ( '}' === $code[ $j ] ) {
			$d--;
			if ( 0 === $d ) {
				return substr( $code, $from, $j - $from + 1 );
			}
		}
	}
	fwrite( STDERR, "không đóng được ngoặc của $name\n" );
	exit( 1 );
}

/* ------------------------------------------------------------- the stubs */

class WP_User {
	public $ID = 0;
	public $user_login = '';
	public function __construct( $id, $login ) {
		$this->ID         = $id;
		$this->user_login = $login;
	}
}

$GLOBALS['log']     = array();   // everything the function did, in order
$GLOBALS['2fa_on']  = array();   // user id => second factor owed?
$GLOBALS['has_woo'] = false;

function horsetools_2fa_second_factor_due( $id ) { return ! empty( $GLOBALS['2fa_on'][ $id ] ); }
function horsetools_2fa_handoff_url( $id, $to = '' ) { return 'https://site.test/wp-login.php?action=horsetools_2fa_start&ht2fa=TOKEN' . ( $to ? '&redirect_to=' . rawurlencode( $to ) : '' ); }
function wp_set_current_user( $id ) { $GLOBALS['log'][] = 'set_current_user'; }
function wp_set_auth_cookie( $id, $r = false ) { $GLOBALS['log'][] = 'AUTH_COOKIE'; }
function do_action( $hook ) { $GLOBALS['log'][] = 'do_action:' . $hook; }
function admin_url( $p = '' ) { return 'https://site.test/wp-admin/'; }
function home_url( $p = '' ) { return 'https://site.test/'; }
function wc_get_page_permalink( $p ) { return 'https://site.test/my-account/'; }
function wp_safe_redirect( $url ) { $GLOBALS['log'][] = 'redirect:' . $url; }
function class_exists_woo() { return $GLOBALS['has_woo']; }

// class_exists('WooCommerce') has to answer from a variable, so declare the
// class only when the case under test wants a WooCommerce site.
function ht_case_reset( $woo = false ) {
	$GLOBALS['log']     = array();
	$GLOBALS['has_woo'] = $woo;
}

// The function calls exit; run each case in a way that survives that.
$src  = ht_extract_function( dirname( __DIR__ ) . '/inc/goo.php', 'horsetools_google_finish_login' );
$src  = str_replace( 'exit;', 'return;', $src );          // let the harness continue
$src  = str_replace( "class_exists( 'WooCommerce' )", 'class_exists_woo()', $src );
$tmp  = __DIR__ . '/_extracted-finish-login.php';
file_put_contents( $tmp, "<?php\n" . $src . "\n" );
require $tmp;
unlink( $tmp );

/* -------------------------------------------------------------- the tests */

$pass = 0;
$fail = 0;
function ok( $cond, $what ) {
	global $pass, $fail;
	if ( $cond ) { $pass++; echo "  ok    $what\n"; } else { $fail++; echo "  FAIL  $what\n"; }
}

echo "\n1. Tài khoản CÓ bật 2FA — không được cấp phiên\n";
ht_case_reset();
$GLOBALS['2fa_on'][7] = true;
horsetools_google_finish_login( new WP_User( 7, 'admin' ) );
$log = $GLOBALS['log'];
ok( ! in_array( 'AUTH_COOKIE', $log, true ), 'KHÔNG đặt cookie đăng nhập' );
ok( ! in_array( 'set_current_user', $log, true ), 'không đặt current user' );
ok( 1 === count( $log ) && 0 === strpos( $log[0], 'redirect:' ), 'chỉ chuyển hướng, không làm gì khác' );
ok( false !== strpos( $log[0], 'action=horsetools_2fa_start' ), 'chuyển tới màn hình nhập mã' );

echo "\n2. Tài khoản KHÔNG bật 2FA — đăng nhập bình thường\n";
ht_case_reset();
$GLOBALS['2fa_on'][8] = false;
horsetools_google_finish_login( new WP_User( 8, 'bob' ) );
$log = $GLOBALS['log'];
ok( in_array( 'AUTH_COOKIE', $log, true ), 'có đặt cookie' );
ok( in_array( 'do_action:wp_login', $log, true ), 'CÓ bắn hook wp_login (trước đây thiếu)' );
ok( array_search( 'AUTH_COOKIE', $log, true ) < array_search( 'do_action:wp_login', $log, true ), 'bắn wp_login SAU khi đặt cookie, giống core' );
ok( 'redirect:https://site.test/wp-admin/' === end( $log ), 'về trang quản trị' );

echo "\n3. Site có WooCommerce — về trang tài khoản\n";
ht_case_reset( true );
$GLOBALS['2fa_on'][9] = false;
horsetools_google_finish_login( new WP_User( 9, 'carol' ) );
ok( 'redirect:https://site.test/my-account/' === end( $GLOBALS['log'] ), 'về My account' );

echo "\n4. WooCommerce + 2FA — vẫn phải qua cửa mã, và giữ đích đến\n";
ht_case_reset( true );
$GLOBALS['2fa_on'][10] = true;
horsetools_google_finish_login( new WP_User( 10, 'dave' ) );
$log = $GLOBALS['log'];
ok( ! in_array( 'AUTH_COOKIE', $log, true ), 'không đặt cookie' );
ok( false !== strpos( $log[0], rawurlencode( 'https://site.test/my-account/' ) ), 'đích đến được mang theo' );

echo "\n5. Không có user (Google trả về rác)\n";
ht_case_reset();
horsetools_google_finish_login( false );
$log = $GLOBALS['log'];
ok( ! in_array( 'AUTH_COOKIE', $log, true ), 'không đặt cookie' );
ok( 'redirect:https://site.test/' === $log[0], 'về trang chủ' );

echo "\n6. Module 2FA tắt (hàm không tồn tại) — không được vỡ\n";
// Simulated by a user id with no entry: second_factor_due returns false, which
// is the same answer the real function_exists() guard produces when the module
// is not loaded at all.
ht_case_reset();
horsetools_google_finish_login( new WP_User( 99, 'eve' ) );
ok( in_array( 'AUTH_COOKIE', $log = $GLOBALS['log'] ) && in_array( 'do_action:wp_login', $log, true ), 'đăng nhập bình thường' );

printf( "\n%d passed, %d failed\n", $pass, $fail );
exit( $fail ? 1 : 0 );
