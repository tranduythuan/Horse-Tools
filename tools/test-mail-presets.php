<?php
/**
 * Is the table of email services right, and does it warn about the one mistake
 * that produces a working login and undelivered mail?
 *
 * This table replaces knowledge the owner is otherwise expected to have. A wrong
 * port in here is worse than no table at all: without it they look the value up
 * and get it right, with it they trust a number that cannot work and have no way
 * to tell which of the eight fields is the broken one.
 *
 * Usage:  php tools/test-mail-presets.php
 */

define( 'ABSPATH', __DIR__ . '/' );

function __( $s, $d = '' ) { return $s; }
function esc_html__( $s, $d = '' ) { return $s; }

$horsetools_options = array();

require_once dirname( __DIR__ ) . '/inc/mail-presets.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . "\n"; }
}
function ok( $c, $what ) { eq( (bool) $c, true, $what ); }

echo "\n1. Mỗi dòng phải đủ và hợp lệ — thiếu một khoá là màn hình vỡ\n";
$need = array( 'label', 'host', 'port', 'enc', 'user', 'from', 'secret', 'where', 'note' );
foreach ( horsetools_mail_presets() as $key => $row ) {
	$thieu = array_diff( $need, array_keys( $row ) );
	eq( $thieu, array(), "$key — đủ khoá" );
	ok( '' !== $row['label'] && '' !== $row['host'], "$key — có tên và máy chủ" );
	ok( ctype_digit( (string) $row['port'] ) && (int) $row['port'] > 0 && (int) $row['port'] < 65536, "$key — cổng là số hợp lệ ({$row['port']})" );
	ok( in_array( $row['enc'], array( 'tls', 'ssl', '' ), true ), "$key — giao thức nằm trong danh sách form chấp nhận" );
	ok( in_array( $row['from'], array( 'user', 'verified', '' ), true ), "$key — quy tắc người gửi hợp lệ" );
	ok( '' === $row['where'] || 0 === strpos( $row['where'], 'https://' ), "$key — link lấy khoá là https hoặc rỗng" );
}

echo "\n2. Cổng và giao thức phải đi đôi với nhau\n";
// 587 là STARTTLS, 465 là SSL ngầm. Ghép chéo là kết nối treo rồi hết giờ, và
// người dùng không có cách nào biết đó là do cổng hay do mật khẩu.
foreach ( horsetools_mail_presets() as $key => $row ) {
	if ( '587' === (string) $row['port'] ) {
		eq( $row['enc'], 'tls', "$key — cổng 587 phải đi với STARTTLS" );
	}
	if ( '465' === (string) $row['port'] ) {
		eq( $row['enc'], 'ssl', "$key — cổng 465 phải đi với SSL" );
	}
}

echo "\n3. Máy chủ phải là tên miền thật, không phải chỗ điền tạm\n";
foreach ( horsetools_mail_presets() as $key => $row ) {
	ok( (bool) preg_match( '~^[a-z0-9.-]+\.[a-z]{2,}$~', $row['host'] ), "$key — {$row['host']}" );
	ok( false === strpos( $row['host'], 'example' ), "$key — không phải tên ví dụ" );
}

echo "\n4. Dịch vụ có TÊN ĐĂNG NHẬP CỐ ĐỊNH phải nói ra\n";
// Resend muốn đúng chữ "resend", SendGrid muốn "apikey". Người ta điền email vào
// đó rồi không hiểu vì sao báo sai mật khẩu.
eq( horsetools_mail_preset( 'resend' )['user'], 'resend', 'Resend: tên đăng nhập cố định' );
eq( horsetools_mail_preset( 'sendgrid' )['user'], 'apikey', 'SendGrid: tên đăng nhập cố định' );
eq( horsetools_mail_preset( 'gmail' )['user'], '', 'Gmail: dùng chính địa chỉ, không cố định' );
foreach ( array( 'resend', 'sendgrid' ) as $k ) {
	ok( false !== strpos( horsetools_mail_preset( $k )['note'], horsetools_mail_preset( $k )['user'] ), "$k — ghi chú nhắc đúng cái tên đó" );
}

echo "\n5. Tra cứu\n";
eq( horsetools_mail_preset( 'khong-ton-tai' ), null, 'khoá lạ → null' );
ok( is_array( horsetools_mail_preset( 'gmail' ) ), 'khoá thật → mảng' );

echo "\n6. Nhận ra dịch vụ từ cấu hình có sẵn\n";
// Site điền tay từ mấy năm trước không được bắt làm lại từ đầu.
$GLOBALS['horsetools_options'] = array( 'mail-gsmtp15' => 'smtp.gmail.com' );
eq( horsetools_mail_preset_detect(), 'gmail', 'nhận ra Gmail qua máy chủ' );
$GLOBALS['horsetools_options'] = array( 'mail-gsmtp15' => 'SMTP.GMAIL.COM ' );
eq( horsetools_mail_preset_detect(), 'gmail', 'không phân biệt hoa thường và khoảng trắng' );
$GLOBALS['horsetools_options'] = array( 'mail-gsmtp15' => 'mail.hostcuatoi.vn' );
eq( horsetools_mail_preset_detect(), '', 'máy chủ lạ thì không đoán bừa' );
$GLOBALS['horsetools_options'] = array();
eq( horsetools_mail_preset_detect(), '', 'chưa điền gì thì không đoán' );

echo "\n7. Khoá đã chọn\n";
$GLOBALS['horsetools_options'] = array( 'mail-preset' => 'brevo' );
eq( horsetools_mail_preset_key(), 'brevo', 'đọc được lựa chọn' );
$GLOBALS['horsetools_options'] = array( 'mail-preset' => 'linh-tinh' );
eq( horsetools_mail_preset_key(), '', 'giá trị rác bị bỏ, không tin dữ liệu trong option' );

echo "\n7b. Gợi ý từ bản ghi MX — phải trỏ vào dòng có thật trong danh sách\n";
eq( horsetools_mail_preset_for_mx( 'google' ), 'gmail', 'MX Google → dòng Gmail' );
eq( horsetools_mail_preset_for_mx( 'zoho' ), 'zoho', 'MX Zoho → dòng Zoho' );
eq( horsetools_mail_preset_for_mx( 'larksuite' ), '', 'Lark không có trong danh sách → không gợi ý bừa' );
eq( horsetools_mail_preset_for_mx( 'cpanel' ), '', 'cPanel không có trong danh sách → im' );
eq( horsetools_mail_preset_for_mx( '' ), '', 'không đoán được nhà cung cấp → im' );
// Gợi ý trỏ vào khoá không tồn tại thì màn hình gọi ['label'] trên null → trắng trang.
foreach ( array( 'google', 'zoho', 'microsoft', 'yandex' ) as $mx ) {
	ok( null !== horsetools_mail_preset( horsetools_mail_preset_for_mx( $mx ) ), "$mx — khoá gợi ý có thật trong bảng" );
}

echo "\n8. CẢNH BÁO NGƯỜI GỬI ≠ TÀI KHOẢN — lỗi tạo ra \"đăng nhập được mà thư không tới\"\n";
$GLOBALS['horsetools_options'] = array(
	'mail-preset'  => 'gmail',
	'mail-gsmtp13' => 'toi@gmail.com',
	'mail-gsmtp12' => 'lienhe@tenmiencuatoi.com',
);
$w = horsetools_mail_from_warning();
ok( '' !== $w, 'Gmail + hai địa chỉ khác nhau → cảnh báo' );
ok( false !== strpos( $w, 'toi@gmail.com' ) && false !== strpos( $w, 'lienhe@tenmiencuatoi.com' ), 'nói ra CẢ HAI địa chỉ, để người ta thấy chỗ lệch' );

$GLOBALS['horsetools_options']['mail-gsmtp12'] = 'toi@gmail.com';
eq( horsetools_mail_from_warning(), '', 'trùng nhau thì im' );
$GLOBALS['horsetools_options']['mail-gsmtp12'] = 'TOI@Gmail.COM';
eq( horsetools_mail_from_warning(), '', 'khác hoa thường vẫn là trùng' );

$GLOBALS['horsetools_options']['mail-gsmtp12'] = '';
eq( horsetools_mail_from_warning(), '', 'thiếu một vế thì không phán' );

echo "\n8b. Dịch vụ KHÔNG có quy tắc đó thì không được kêu\n";
$GLOBALS['horsetools_options'] = array(
	'mail-preset'  => 'brevo',
	'mail-gsmtp13' => 'toi@gmail.com',
	'mail-gsmtp12' => 'lienhe@tenmiencuatoi.com',
);
eq( horsetools_mail_from_warning(), '', 'Brevo cho gửi bằng tên miền đã xác minh → khác nhau là bình thường' );

$GLOBALS['horsetools_options'] = array(
	'mail-gsmtp13' => 'toi@gmail.com',
	'mail-gsmtp12' => 'lienhe@tenmiencuatoi.com',
);
eq( horsetools_mail_from_warning(), '', 'chưa chọn và không nhận ra dịch vụ → không phán' );

$GLOBALS['horsetools_options']['mail-gsmtp15'] = 'smtp.gmail.com';
ok( '' !== horsetools_mail_from_warning(), 'nhưng nhận ra Gmail qua máy chủ thì vẫn cảnh báo, dù chưa chọn gì' );

echo "\n" . str_repeat( '-', 56 ) . "\n";
echo "  $pass đạt, $fail hỏng\n\n";
exit( $fail ? 1 : 0 );
