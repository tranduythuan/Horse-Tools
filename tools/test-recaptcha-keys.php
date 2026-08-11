<?php
/**
 * reCAPTCHA: khoá sai có được nói ra không, và defer có phá thứ tự script không?
 *
 * Sinh ra từ một site thật không đăng nhập được. Chuỗi nhân quả đã dựng lại được
 * bằng trình duyệt trên chính trang đó:
 *   khoá v2 dán nhầm vào ô v3  →  api.js?render=<khoá> bị Google từ chối
 *   →  grecaptcha không tồn tại  →  script nội tuyến ném ReferenceError
 *   →  ô token ẩn rỗng  →  server từ chối (fail closed)
 *   →  bộ lọc che lỗi đổi thành "Đăng nhập thất bại"  →  người dùng đọc ra "sai mật khẩu".
 *
 * Usage:  php tools/test-recaptcha-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

function __( $s, $d = '' ) { return $s; }
function esc_html__( $s, $d = '' ) { return $s; }

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . "\n"; }
}
function ok( $c, $what ) { eq( (bool) $c, true, $what ); }

/* ---------------------------------------------------------------------------
 * 1. Kết luận về cặp khoá  (horsetools_recaptcha_verdict trong inc/goo.php)
 * ------------------------------------------------------------------------ */

// Tách nguyên hàm ra khỏi goo.php — file đó đăng ký hook lúc load và kéo theo
// cả Google API client, không require thẳng được trong môi trường test.
// Repo có chỗ CRLF chỗ LF; không chuẩn hoá thì regex trượt và test "không tìm
// thấy hàm" chứ không phải "hàm sai".
$src = str_replace( "\r\n", "\n", file_get_contents( dirname( __DIR__ ) . '/inc/goo.php' ) );
if ( ! preg_match( '/\nfunction horsetools_recaptcha_verdict\(.*?\n\}\n/s', $src, $m ) ) {
	fwrite( STDERR, "Không tìm thấy horsetools_recaptcha_verdict() trong inc/goo.php\n" );
	exit( 2 );
}
$tmp = sys_get_temp_dir() . '/ht_verdict_' . getmypid() . '.php';
file_put_contents( $tmp, "<?php\n" . $m[0] );
require $tmp;
unlink( $tmp );

echo "\n1. Khoá v2 nằm trong ô v3 — đúng lỗi của site trong video\n";
$v = horsetools_recaptcha_verdict( 'V3', 400, array( 'invalid-input-response' ), true );
eq( $v['ok'], false, 'Google từ chối khoá → báo hỏng' );
ok( false !== strpos( $v['message'], 'v2' ), 'nói thẳng là khoá v2' );
ok( false !== strpos( $v['message'], 'V2' ), 'và chỉ ra cách sửa: chuyển dropdown sang V2' );

echo "\n2. Cặp khoá tốt\n";
$v = horsetools_recaptcha_verdict( 'V3', 200, array( 'invalid-input-response' ), true );
eq( $v['ok'], true, 'loader 200 + secret nhận token rác → đạt' );
// "invalid-input-response" là câu trả lời LÀNH MẠNH: token mình gửi vốn là rác.
// Nhầm nó thành lỗi thì cặp khoá đúng lại bị báo sai.
ok( false === strpos( $v['message'], 'Secret' ), 'không đổ lỗi oan cho secret' );

echo "\n3. Secret sai\n";
$v = horsetools_recaptcha_verdict( 'V3', 200, array( 'invalid-input-secret' ), true );
eq( $v['ok'], false, 'Google không nhận secret → báo hỏng' );
ok( false !== strpos( $v['message'], 'Secret' ), 'chỉ đúng vào ô Secret' );

echo "\n4. Không được phán khi không biết\n";
// Mạng hỏng thì không nói gì được về khoá — đúng nguyên tắc mà chính hàm xác
// minh đang dùng (lỗi truyền tải thì fail open).
$v = horsetools_recaptcha_verdict( 'V3', null, array( 'invalid-input-response' ), true );
eq( $v['ok'], true, 'không gọi được loader → không kết tội khoá' );
$v = horsetools_recaptcha_verdict( 'V2', null, array( 'invalid-input-response' ), true );
eq( $v['ok'], true, 'chế độ V2 thì không kiểm loader v3' );
// Khoá v2 ở chế độ V2 là ĐÚNG — không được báo hỏng.
$v = horsetools_recaptcha_verdict( 'V2', 400, array( 'invalid-input-response' ), true );
eq( $v['ok'], true, 'V2: loader v3 hỏng là chuyện đương nhiên, bỏ qua' );

echo "\n5. Secret rỗng = reCAPTCHA không làm gì cả, phải nói ra\n";
$v = horsetools_recaptcha_verdict( 'V3', 200, array(), false );
eq( $v['ok'], false, 'thiếu secret → không coi là đạt' );
ok( false !== strpos( $v['message'], 'nothing at all' ), 'nói rõ là đang không bảo vệ gì' );

/* ---------------------------------------------------------------------------
 * 2. Defer không được đảo thứ tự phụ thuộc  (inc/speed.php)
 * ------------------------------------------------------------------------ */

echo "\n6. Defer: không được defer thứ mà script KHÔNG defer đang cần\n";

class HT_FakeScript {
	public $deps;
	public function __construct( $deps = array() ) { $this->deps = $deps; }
}
class HT_FakeScripts {
	public $registered = array();
	public $inline     = array();
	public function get_data( $handle, $key ) {
		return ( isset( $this->inline[ $handle ] ) && in_array( $key, $this->inline[ $handle ], true ) ) ? 'x' : false;
	}
}
$GLOBALS['ht_fake_scripts'] = null;
function wp_scripts() { return $GLOBALS['ht_fake_scripts']; }

$src = str_replace( "\r\n", "\n", file_get_contents( dirname( __DIR__ ) . '/inc/speed.php' ) );
if ( ! preg_match( '/\n\tfunction horsetools_defer_blocked_handles\(\).*?\n\t\}\n/s', $src, $m ) ) {
	fwrite( STDERR, "Không tìm thấy horsetools_defer_blocked_handles() trong inc/speed.php\n" );
	exit( 2 );
}
$tmp = sys_get_temp_dir() . '/ht_defer_' . getmypid() . '.php';
file_put_contents( $tmp, "<?php\n" . $m[0] );
require $tmp;
unlink( $tmp );

// Đồ hình copy nguyên từ trang đăng nhập của site đó.
$s = new HT_FakeScripts();
$s->registered = array(
	'clipboard'    => new HT_FakeScript(),
	'dom-ready'    => new HT_FakeScript(),
	'hooks'        => new HT_FakeScript(),
	'i18n'         => new HT_FakeScript( array( 'hooks' ) ),
	'a11y'         => new HT_FakeScript( array( 'dom-ready', 'i18n' ) ),
	'user-profile' => new HT_FakeScript( array( 'clipboard', 'a11y', 'jquery' ) ),
	'jquery'       => new HT_FakeScript(),
	'lonely'       => new HT_FakeScript(),
);
// Chính hai anh này mang inline nên bị bỏ qua, chạy trước, và gọi vào thứ chưa nạp.
$s->inline = array( 'user-profile' => array( 'before' ), 'a11y' => array( 'after' ) );
$GLOBALS['ht_fake_scripts'] = $s;

$blocked = horsetools_defer_blocked_handles();
ok( isset( $blocked['clipboard'] ), 'clipboard bị chặn defer (user-profile cần nó) — chính lỗi ClipboardJS is not defined' );
ok( isset( $blocked['dom-ready'] ), 'dom-ready bị chặn defer (a11y cần nó)' );
ok( isset( $blocked['a11y'] ), 'a11y bị chặn (user-profile cần nó)' );
ok( isset( $blocked['i18n'] ), 'i18n bị chặn (a11y cần nó)' );
ok( isset( $blocked['hooks'] ), 'hooks bị chặn — phụ thuộc bắc cầu, không chỉ một tầng' );
ok( ! isset( $blocked['lonely'] ), 'script không ai phụ thuộc thì VẪN được defer (không tắt luôn tính năng)' );

echo "\n7. Defer: các ca biên\n";
$s2 = new HT_FakeScripts();
$s2->registered = array( 'a' => new HT_FakeScript( array( 'b' ) ), 'b' => new HT_FakeScript( array( 'a' ) ) );
$s2->inline     = array( 'a' => array( 'after' ) );
$GLOBALS['ht_fake_scripts'] = $s2;
$b = horsetools_defer_blocked_handles();
ok( isset( $b['a'] ) && isset( $b['b'] ), 'phụ thuộc vòng tròn: chặn cả hai, không treo' );

$s3 = new HT_FakeScripts();
$s3->registered = array( 'x' => new HT_FakeScript(), 'y' => new HT_FakeScript( array( 'x' ) ) );
$s3->inline     = array();
$GLOBALS['ht_fake_scripts'] = $s3;
eq( horsetools_defer_blocked_handles(), array(), 'không có script nào mang inline → không chặn ai, defer chạy hết' );

$GLOBALS['ht_fake_scripts'] = null;
eq( horsetools_defer_blocked_handles(), array(), 'chưa có wp_scripts() → không nổ' );

echo "\n" . str_repeat( '-', 56 ) . "\n";
echo "  $pass đạt, $fail hỏng\n\n";
exit( $fail ? 1 : 0 );
