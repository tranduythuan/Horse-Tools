<?php
/**
 * Does the contact watcher recognise a phone number, and refuse a price?
 *
 * This detector's whole value is that it almost never cries wolf. It watches
 * numbers, and a Vietnamese page is full of numbers that are not phone numbers
 * — prices written 1.790.000, order references, product codes, dates. One false
 * alarm a week and the owner stops reading it, and then it may as well not
 * exist.
 *
 * The other half is that the same number written four ways has to compare
 * equal, or simply reformatting the hotline reads as somebody swapping it.
 *
 * Usage:  php tools/test-watch-contact.php
 */

define( 'ABSPATH', __DIR__ . '/' );

define( 'HOUR_IN_SECONDS', 3600 );

function __( $s, $d = '' ) { return $s; }
function esc_html__( $s, $d = '' ) { return $s; }
function esc_html( $s ) { return $s; }
function esc_attr( $s ) { return $s; }
function get_option( $k, $default = false ) { return $GLOBALS['opts'][ $k ] ?? $default; }
function update_option( $k, $v, $a = null ) { $GLOBALS['opts'][ $k ] = $v; return true; }
function get_transient( $k ) { return $GLOBALS['trans'][ $k ] ?? false; }
function set_transient( $k, $v, $t = 0 ) { $GLOBALS['trans'][ $k ] = $v; return true; }
function delete_transient( $k ) { unset( $GLOBALS['trans'][ $k ] ); return true; }
function add_action( $h, $f, $p = 10, $a = 1 ) {}
function current_user_can( $c ) { return true; }
function check_ajax_referer( $a, $b ) { return true; }
function wp_send_json_error( $d = null ) {}
function wp_send_json_success( $d = null ) {}
function wp_create_nonce( $a ) { return 'nonce'; }
function horsetools_is_plugin_screen() { return false; }
function horsetools_option_names() { return array( 'horsetools_settings' ); }
$GLOBALS['opts']  = array();
$GLOBALS['trans'] = array();

require_once dirname( __DIR__ ) . '/inc/watch-contact.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . "\n"; }
}
function ok( $c, $what ) { eq( (bool) $c, true, $what ); }

echo "\n1. Cùng một số viết bốn kiểu phải bằng nhau\n";
$want = '0988343412';
foreach ( array( '0988343412', '0988.34.34.12', '0988 34 34 12', '+84988343412', '84988343412', '0988-34-34-12' ) as $form ) {
	eq( horsetools_contact_phone( $form ), $want, "'$form'" );
}

echo "\n2. KHÔNG được nhận nhầm — đây là chỗ quyết định tính năng sống hay chết\n";
$reject = array(
	'1.790.000'      => 'giá tiền',
	'13.969.000'     => 'giá tiền lớn',
	'4.600'          => 'đơn giá',
	'2026'           => 'năm',
	'12345'          => 'số ngắn',
	'0123456789012'  => 'mã tham chiếu 13 số',
	'123456789'      => 'không bắt đầu bằng 0',
	'65/9m Hau Lan 4' => 'địa chỉ',
	''               => 'chuỗi rỗng',
);
foreach ( $reject as $raw => $why ) {
	eq( horsetools_contact_phone( $raw ), '', "$why: '$raw'" );
}

echo "\n2b. Số quốc tế phải NHẬN, không được vứt\n";
// Vứt chúng đi có nghĩa là thay hotline bằng số nước ngoài chỉ bị phát hiện
// nhờ số cũ biến mất, còn THÊM một số nước ngoài thì hoàn toàn im lặng.
eq( horsetools_contact_phone( '+14155550123' ), '+14155550123', 'số Mỹ' );
eq( horsetools_contact_phone( '+1 (415) 555-0123' ), '+14155550123', 'số Mỹ có dấu ngoặc và gạch' );
eq( horsetools_contact_phone( '+8613800138000' ), '+8613800138000', 'số Trung Quốc' );
eq( horsetools_contact_phone( '+79001234567' ), '+79001234567', 'số Nga' );
eq( horsetools_contact_phone( '0084988343412' ), '0988343412', '00 84 vẫn quy về dạng trong nước' );
eq( horsetools_contact_phone( '+84838216168' ), '0838216168', '+84 vẫn quy về dạng trong nước, không thành +84…' );

echo "\n2c. Nhưng dấu + không được biến mọi thứ thành số\n";
eq( horsetools_contact_phone( '+1.790.000' ), '', 'giá có dấu + vẫn quá ngắn → loại' );
eq( horsetools_contact_phone( '+123' ), '', 'quá ngắn' );
eq( horsetools_contact_phone( '+1234567890123456789' ), '', 'quá dài, ngoài E.164' );
eq( horsetools_contact_phone( '2+3' ), '', 'biểu thức toán' );

echo "\n3. Rút từ liên kết\n";
$html = '<a href="tel:0838216168">Hotline</a> '
	. '<a href="https://zalo.me/0917940068">Zalo</a> '
	. '<a href="https://m.me/giathuanshop">Nhắn tin</a> '
	. '<a href="mailto:Tuongvm.2oco@Gmail.com">Mail</a>';
$got = horsetools_contact_extract( $html );
ok( isset( $got['phone:0838216168'] ), 'tel: ra số điện thoại' );
ok( isset( $got['zalo:0917940068'] ), 'zalo.me số được chuẩn hoá như số điện thoại' );
ok( isset( $got['messenger:giathuanshop'] ), 'm.me ra tên trang' );
ok( isset( $got['email:tuongvm.2oco@gmail.com'] ), 'mailto: viết thường lại' );

echo "\n4. Số viết trần trong bài\n";
$text = 'Liên hệ 0988.34.34.12 hoặc 0917 940 068 để đặt lô hàng 1.790.000đ cho 300 cái.';
$got  = horsetools_contact_extract( $text );
ok( isset( $got['phone:0988343412'] ), 'bắt được số thứ nhất' );
ok( isset( $got['phone:0917940068'] ), 'bắt được số thứ hai' );
eq( count( array_filter( $got, function ( $r ) { return 'phone' === $r['type']; } ) ), 2, 'đúng 2 số — giá 1.790.000 và 300 không bị tính' );

echo "\n4b. Số quốc tế viết trần trong bài\n";
$got = horsetools_contact_extract( 'Hotline quốc tế +1 415 555 0123, giá +1.790.000đ, gọi 0988343412.' );
ok( isset( $got['phone:+14155550123'] ), 'bắt được số nước ngoài viết trần' );
ok( isset( $got['phone:0988343412'] ), 'vẫn bắt số trong nước' );
ok( ! isset( $got['phone:+1790000'] ), 'giá viết kèm dấu + không bị tính' );

echo "\n4c. Thêm một số lạ mà không xoá số cũ — trước đây hoàn toàn im lặng\n";
$before = horsetools_contact_extract( 'Gọi 0988343412' );
$after  = horsetools_contact_extract( 'Gọi 0988343412 hoặc +14155550123' );
$d      = horsetools_contact_diff( $after, $before );
eq( count( $d['added'] ), 1, 'phát hiện đúng một số mới' );
eq( count( $d['removed'] ), 0, 'không có gì mất — kẻ tấn công chỉ thêm vào' );

echo "\n5. Cùng số xuất hiện nhiều lần thì gom lại\n";
$got = horsetools_contact_extract( 'Gọi 0988343412 · hoặc tel:0988.34.34.12 · hoặc 0988 34 34 12' );
eq( $got['phone:0988343412']['count'], 3, 'gộp thành một mục, đếm 3' );
eq( count( $got ), 1, 'chỉ một danh tính' );

echo "\n6. Quét cài đặt của plugin — không cần biết tên khoá\n";
$GLOBALS['opts']['horsetools_settings'] = array(
	'chat-nut11' => 'Phone',
	'chat-nut31' => '0838216168',
	'chat-nut12' => 'Zalo',
	'chat-nut32' => '0917940068',
	'chat-nut13' => 'Messenger',
	'chat-nut33' => 'giathuanshop',
	'chat-msg'   => 'Chào anh chị',
	'some-price' => '1.790.000',
	'nested'     => array( 'deep' => array( 'mail' => 'shop@giathuan.vn' ) ),
);
$found = horsetools_contact_scan_settings();
ok( isset( $found['phone:0838216168'] ), 'số hotline trong chat-nut31' );
ok( isset( $found['phone:0917940068'] ), 'số Zalo (lưu dạng số trần)' );
ok( isset( $found['email:shop@giathuan.vn'] ), 'email lồng sâu trong mảng' );
ok( ! isset( $found['phone:1790000'] ), 'giá tiền trong cài đặt không bị tính' );

echo "\n7. So với mốc\n";
$base = $found;
// Kẻ tấn công đổi hotline sang số của họ.
$GLOBALS['opts']['horsetools_settings']['chat-nut31'] = '0900111222';
$now  = horsetools_contact_scan_settings();
$diff = horsetools_contact_diff( $now, $base );
ok( isset( $diff['added']['phone:0900111222'] ), 'số lạ bị phát hiện là MỚI' );
ok( isset( $diff['removed']['phone:0838216168'] ), 'số cũ bị phát hiện là MẤT' );
eq( count( $diff['added'] ), 1, 'không báo thừa' );

echo "\n8. Đổi cách viết KHÔNG được coi là thay đổi\n";
$GLOBALS['opts']['horsetools_settings']['chat-nut31'] = '+84 838 216 168';
$now  = horsetools_contact_scan_settings();
$diff = horsetools_contact_diff( $now, $base );
eq( count( $diff['added'] ), 0, 'viết lại cùng số → không có gì mới' );
eq( count( $diff['removed'] ), 0, 'và không có gì mất' );

echo "\n9. Mốc\n";
$GLOBALS['opts'] = array();
ok( ! horsetools_contact_has_baseline(), 'chưa có mốc' );
horsetools_contact_baseline_set( array( 'phone:0988343412' => array( 'type' => 'phone' ) ) );
ok( horsetools_contact_has_baseline(), 'đã có mốc' );
horsetools_contact_baseline_set( array() );
ok( horsetools_contact_has_baseline(), 'mốc rỗng vẫn là đã chốt, không phải chưa chốt' );

printf( "\n%d passed, %d failed\n", $pass, $fail );
exit( $fail ? 1 : 0 );
