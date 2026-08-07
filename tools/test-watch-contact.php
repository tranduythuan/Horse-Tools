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
function add_filter( $h, $f, $p = 10, $a = 1 ) {}
function delete_option( $k ) { unset( $GLOBALS['opts'][ $k ] ); return true; }
function current_user_can( $c ) { return true; }
// The shared walk over post content lives in inc/watch-scan.php; this suite is
// about what the watcher makes of the text, not about the cursor.
function horsetools_scan_finished() { return true; }
function horsetools_scan_progress() { return array( 'read' => 0, 'total' => 0 ); }
function check_ajax_referer( $a, $b ) { return true; }
function wp_send_json_error( $d = null ) {}
function wp_send_json_success( $d = null ) {}
function wp_create_nonce( $a ) { return 'nonce'; }
$GLOBALS['on_screen'] = false;
function horsetools_is_plugin_screen() { return (bool) $GLOBALS['on_screen']; }
function horsetools_admin_banner( $tone, $html ) { echo '<div class="ht-banner ht-banner-' . $tone . '">' . $html . '</div>'; }
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

echo "\n2d. Chuỗi không phải số thì không được cạo chữ số ra\n";
// Đây là icon SVG thật của nút chat. Nó rút ra 00242418451610844 — mười bảy
// chữ số bắt đầu bằng 00, đúng hình dạng một số quốc tế — và từng bị báo là
// số điện thoại của shop, in nguyên cả đoạn SVG ra màn hình.
$svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v10H8l-4 4z"/></svg>';
eq( horsetools_contact_phone( $svg ), '', 'icon SVG không phải số điện thoại' );
eq( horsetools_contact_phone( '<path d="M0 0 24 24 84 1234567"/>' ), '', 'mảnh SVG khác' );
eq( horsetools_contact_phone( 'Gọi 0988343412 nhé' ), '', 'cả câu văn thì không — chỉ chuỗi thuần số mới tính' );
eq( horsetools_contact_phone( '2026-08-07 09:15:00' ), '', 'dấu thời gian' );
eq( horsetools_contact_phone( 'rgba(0, 0, 0, 0.05)' ), '', 'giá trị CSS' );
// Mà những dạng viết thật của số điện thoại thì vẫn phải qua.
foreach ( array( '0988343412', '0988.34.34.12', '0988 34 34 12', '+84 988 343 412', '+1 (415) 555-0123', '0084988343412' ) as $ok ) {
	ok( '' !== horsetools_contact_phone( $ok ), "vẫn nhận: '$ok'" );
}

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

/* -------------------------------------------------------------------------
 * The banner itself.
 *
 * Everything above tests what the watcher concludes. None of it noticed that in
 * one of the three states the confirm button was printed with nothing bound to
 * it — an early `return` sat between the button and the script that made it
 * work. It looked exactly like the two working cases and did nothing when
 * pressed, which is worse than having no button: the owner believes they have
 * agreed to a baseline that was never written.
 *
 * So: render each state and check that wherever the button appears, the thing
 * that makes it work appears too.
 * ---------------------------------------------------------------------- */

$GLOBALS['on_screen'] = true;

function render_notice() {
	ob_start();
	horsetools_contact_notice();
	return ob_get_clean();
}

/** Put the watcher into one of the states the owner can actually be in. */
function put_state( $settings_state, $content_state ) {
	$GLOBALS['opts']  = array();
	$GLOBALS['trans'] = array();
	$GLOBALS['opts']['horsetools_settings'] = array( 'chat-nut31' => '0838216168' );

	if ( 'unset' !== $settings_state ) {
		horsetools_contact_baseline_set( horsetools_contact_scan_settings() );
		delete_transient( 'horsetools_contact_now' );
		if ( 'changed' === $settings_state ) {
			$GLOBALS['opts']['horsetools_settings']['chat-nut31'] = '0900111222';
		}
	}

	$GLOBALS['opts'][ HORSETOOLS_CONTACT_CONTENT ] = array(
		'phone:0389737412' => array( 'type' => 'phone', 'value' => '0389737412', 'raw' => '0389737412', 'count' => 1 ),
	);
	if ( 'unset' !== $content_state ) {
		update_option( HORSETOOLS_CONTACT_CONTENT_B, 'clean' === $content_state ? horsetools_contact_content_found() : array(), false );
	}
}

echo "\n10. Bảng thông báo: có nút thì PHẢI có thứ làm nút chạy\n";
$cases = array(
	'cài đặt chưa chốt'        => array( 'unset', 'unset' ),
	'cài đặt đã đổi'           => array( 'changed', 'clean' ),
	'cài đặt sạch, nội dung chờ chốt lần đầu' => array( 'clean', 'unset' ),
	'cài đặt sạch, nội dung có số mới'        => array( 'clean', 'changed' ),
);
foreach ( $cases as $why => $state ) {
	put_state( $state[0], $state[1] );
	$html = render_notice();
	$has_button = false !== strpos( $html, 'id="ht-contact-confirm"' );
	$has_script = false !== strpos( $html, 'horsetools_contact_confirm' );
	ok( $has_button, "$why — có nút" );
	eq( $has_button && $has_script, true, "$why — VÀ có mã chạy nút" );
}

echo "\n11. Không có gì để hỏi thì không được hiện bảng nào\n";
put_state( 'clean', 'clean' );
eq( render_notice(), '', 'cả hai bên đều sạch → im lặng' );

echo "\n12. Ngoài màn hình của plugin thì không chen vào\n";
$GLOBALS['on_screen'] = false;
put_state( 'unset', 'unset' );
eq( render_notice(), '', 'màn hình khác → không hiện' );
$GLOBALS['on_screen'] = true;

echo "\n13. Bảng thông báo phải liệt kê ra, không được bắt gật đầu với con số\n";
put_state( 'clean', 'unset' );
ok( false !== strpos( render_notice(), '0389737412' ), 'số tìm được có in ra để người ta soi' );

echo "\n14. Chốt xong thì bảng phải tắt\n";
put_state( 'clean', 'unset' );
ok( '' !== render_notice(), 'trước khi chốt: có hỏi' );
horsetools_contact_confirm();
horsetools_contact_content_confirm();
eq( render_notice(), '', 'sau khi chốt: im — trạng thái không bị đóng băng trong cùng một request' );

printf( "\n%d passed, %d failed\n", $pass, $fail );
exit( $fail ? 1 : 0 );
