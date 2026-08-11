<?php
/**
 * Câu hỏi bảo mật ở màn đăng nhập: câu trả lời ĐÚNG có được chấp nhận không?
 *
 * Bộ test này sinh ra từ một video người dùng gửi: site tiếng Việt, câu hỏi
 * "Biệt danh của vợ", gõ đúng mật khẩu mà màn hình chỉ báo "Đăng nhập thất bại".
 * Nguyên nhân: `strtolower()` chỉ hạ chữ ASCII, nên "Đào" và "đào" là hai chuỗi
 * khác nhau — và biệt danh tiếng Việt gần như luôn bắt đầu bằng chữ hoa có dấu.
 *
 * Usage:  php tools/test-login-question.php
 */

define( 'ABSPATH', __DIR__ . '/' );

function __( $s, $d = '' ) { return $s; }
function esc_html__( $s, $d = '' ) { return $s; }
function wp_unslash( $s ) { return $s; }
function add_action() {}
function add_filter() {}

class WP_Error {
	private $msg;
	public function __construct( $code = '', $message = '' ) { $this->msg = $message; }
	public function get_error_message() { return $this->msg; }
}

// Bật đúng hai khối cần kiểm. Các khối khác của scuri.php không được bật, nên
// file chỉ khai báo hàm chứ không đụng gì tới môi trường.
$horsetools_options = array(
	'scuri-enum1' => 1,
	'scuri-lq1'   => 1,
	'scuri-lq-q'  => 'Biệt danh của vợ',
	'scuri-lq-a'  => 'Đào',
);

require_once dirname( __DIR__ ) . '/inc/scuri.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . "\n"; }
}
function same( $a, $b, $what ) { eq( horsetools_lq_norm( $a ) === horsetools_lq_norm( $b ), true, $what ); }
function diff( $a, $b, $what ) { eq( horsetools_lq_norm( $a ) === horsetools_lq_norm( $b ), false, $what ); }

echo "\n1. LỖI TỪ VIDEO — chữ hoa có dấu phải khớp chữ thường\n";
// strtolower() để nguyên Đ, Á, Ơ, Ú… nên đây là những câu trả lời đúng bị từ chối.
same( 'Đào', 'đào', 'Đào = đào' );
same( 'Ánh', 'ánh', 'Ánh = ánh' );
same( 'Út', 'út', 'Út = út' );
same( 'Ơn', 'ơn', 'Ơn = ơn' );
same( 'Ổi', 'ổi', 'Ổi = ổi' );
same( 'Ếch', 'ếch', 'Ếch = ếch' );
same( 'Ăn Mày', 'ăn mày', 'Ăn Mày = ăn mày' );
same( 'BÉ BO', 'bé bo', 'BÉ BO = bé bo' );

echo "\n2. Cùng một chữ, hai cách mã hoá — CHỈ xử lý được khi host có ext intl\n";
// "ắ" liền một ký tự (U+1EAF) và "ă" + dấu sắc rời trông y hệt nhau trên màn hình.
// Không có intl thì không chuẩn hoá được, và đây là chỗ ghi lại sự thật đó thay
// vì để nó im lặng: bộ test này chạy trên PHP toolchain KHÔNG có intl.
if ( class_exists( 'Normalizer' ) ) {
	same( "b\xE1\xBA\xAFc", "ba\xCC\x86\xCC\x81c", 'bắc dựng sẵn = bắc tổ hợp' );
	same( "Ho\xCC\x80a", 'Hòa', 'Hòa tổ hợp = Hòa dựng sẵn' );
} else {
	echo "  bỏ qua  host không có ext intl — hai cách mã hoá vẫn sẽ lệch nhau ở đây\n";
	// Nhưng dù không có intl, mọi thứ ở mục 1 vẫn phải đúng — đó mới là lỗi thật.
	same( 'Đào', 'đào', 'không có intl thì phần hạ chữ vẫn phải chạy' );
}

echo "\n3. Khoảng trắng thừa không phải là sai\n";
same( '  Bé Hai  ', 'Bé Hai', 'thừa hai đầu' );
same( 'Bé  Hai', 'Bé Hai', 'thừa ở giữa' );
same( "Bé\tHai", 'Bé Hai', 'tab thay khoảng trắng' );

echo "\n4. KHÔNG được nới lỏng — bỏ dấu vẫn phải là sai\n";
diff( 'Đào', 'dao', 'đào ≠ dao (không bỏ dấu)' );
diff( 'Bé Hai', 'Be Hai', 'bé ≠ be' );
diff( 'vợ', 'vơ', 'vợ ≠ vơ' );
diff( 'Bé Hai', 'Bé Ba', 'câu trả lời khác thì vẫn sai' );
diff( 'Bé Hai', '', 'bỏ trống thì không khớp' );

echo "\n5. Rỗng và rác\n";
eq( horsetools_lq_norm( '' ), '', 'rỗng → rỗng' );
eq( horsetools_lq_norm( '   ' ), '', 'toàn khoảng trắng → rỗng' );
eq( horsetools_lq_norm( 'ABC' ), 'abc', 'ASCII vẫn hạ chữ như cũ' );

echo "\n6. Che lỗi đăng nhập KHÔNG được nuốt lời nhắn của câu hỏi bảo mật\n";
// Che lỗi là để không lộ tài khoản nào có thật. Câu hỏi bảo mật không tiết lộ
// điều đó — nuốt nó đi chỉ lấy mất manh mối duy nhất của chính chủ.
unset( $GLOBALS['horsetools_lq_failed'] );
eq( horsetools_generic_login_error( 'Unknown username. Check again or try your email address.' ),
	'Login failed. Check your details and try again.',
	'lỗi thường vẫn bị làm chung chung (không lộ tài khoản)' );
eq( horsetools_generic_login_error( 'The password you entered for the username admin is incorrect.' ),
	'Login failed. Check your details and try again.',
	'sai mật khẩu vẫn bị làm chung chung' );

$GLOBALS['horsetools_lq_failed'] = true;
eq( horsetools_generic_login_error( 'Wrong answer to the security question.' ),
	'Wrong answer to the security question.',
	'sai câu hỏi bảo mật thì được nói thẳng' );
eq( horsetools_generic_login_error( 'The security question below the password was left empty.' ),
	'The security question below the password was left empty.',
	'bỏ trống câu hỏi thì được nói thẳng' );
unset( $GLOBALS['horsetools_lq_failed'] );

echo "\n7. Cửa kiểm thật — đúng câu trả lời phải đi qua\n";
$GLOBALS['pagenow'] = 'wp-login.php';
$user = (object) array( 'ID' => 1 );

$_POST['horsetools_lq'] = 'đào'; // chính là ca gãy trong video: lưu "Đào", gõ "đào"
eq( horsetools_login_question_check( $user, 'kien' ), $user, 'gõ "đào" khi lưu "Đào" → vào được' );

$_POST['horsetools_lq'] = 'Đào';
eq( horsetools_login_question_check( $user, 'kien' ), $user, 'gõ y hệt → vào được' );

$_POST['horsetools_lq'] = 'con mèo';
eq( horsetools_login_question_check( $user, 'kien' ) instanceof WP_Error, true, 'sai thì chặn' );

$_POST['horsetools_lq'] = '';
$e = horsetools_login_question_check( $user, 'kien' );
eq( $e instanceof WP_Error, true, 'bỏ trống thì chặn' );
eq( false !== strpos( $e->get_error_message(), 'left empty' ), true, 'và nói rõ là bỏ trống, không nói sai' );

unset( $_POST['horsetools_lq'] );
eq( horsetools_login_question_check( $user, '' ), $user, 'chỉ mở trang, chưa gửi gì → không phán' );

$GLOBALS['pagenow'] = 'index.php';
$_POST['horsetools_lq'] = 'sai bét';
eq( horsetools_login_question_check( $user, 'kien' ), $user, 'form khác wp-login.php (Woo, theme, XML-RPC) → không đụng tới' );

echo "\n" . str_repeat( '-', 56 ) . "\n";
echo "  $pass đạt, $fail hỏng\n\n";
exit( $fail ? 1 : 0 );
