<?php
/**
 * Does the check-in message make silence readable?
 *
 * This is the only feature here whose job is to work when everything else has
 * stopped, so the things worth testing are the awkward ones: that the counter
 * still advances when a send FAILS (otherwise a broken channel leaves no gap and
 * looks exactly like a quiet month), that a failure does not put the site into a
 * retry loop on every admin page load, and that the honest list of what is NOT
 * protected says so while it is true and shuts up when it stops being true.
 *
 * Usage:  php tools/test-heartbeat.php
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'HORSETOOLS_DIR', dirname( __DIR__ ) . '/' );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'DAY_IN_SECONDS', 86400 );
if ( ! defined( 'ENT_QUOTES' ) ) { define( 'ENT_QUOTES', 3 ); }

class WP_Error {
	public $code;
	public $msg;
	public function __construct( $code = '', $msg = '' ) { $this->code = $code; $this->msg = $msg; }
	public function get_error_message() { return $this->msg; }
}
function is_wp_error( $t ) { return $t instanceof WP_Error; }

function __( $s, $d = '' ) { return $s; }
function esc_html__( $s, $d = '' ) { return $s; }
function esc_html( $s ) { return $s; }
function esc_attr( $s ) { return $s; }
function get_option( $k, $default = false ) {
	if ( 'admin_email' === $k ) { return $GLOBALS['admin_email']; }
	if ( 'date_format' === $k ) { return 'd/m/Y'; }
	return array_key_exists( $k, $GLOBALS['opts'] ) ? $GLOBALS['opts'][ $k ] : $default;
}
function update_option( $k, $v, $a = null ) { $GLOBALS['opts'][ $k ] = $v; return true; }
function get_transient( $k ) {
	if ( ! isset( $GLOBALS['trans'][ $k ] ) ) { return false; }
	if ( $GLOBALS['trans'][ $k ]['exp'] <= $GLOBALS['now'] ) { unset( $GLOBALS['trans'][ $k ] ); return false; }
	return $GLOBALS['trans'][ $k ]['v'];
}
function set_transient( $k, $v, $t = 0 ) { $GLOBALS['trans'][ $k ] = array( 'v' => $v, 'exp' => $GLOBALS['now'] + $t ); return true; }
function add_action( $h, $f, $p = 10, $a = 1 ) {}
function add_filter( $h, $f, $p = 10, $a = 1 ) {}
function current_user_can( $c ) { return true; }
function check_ajax_referer( $a, $b ) { return true; }
// The real wp_send_json_* end the request. Without that the AJAX handler runs
// straight past its own error branch into the success branch, and a test that
// checks the result reads the wrong one.
class Sent_Json extends Exception {}
function wp_send_json_error( $d = null ) { $GLOBALS['json'] = array( false, $d ); throw new Sent_Json(); }
function wp_send_json_success( $d = null ) { $GLOBALS['json'] = array( true, $d ); throw new Sent_Json(); }
function call_ajax( $fn ) { try { $fn(); } catch ( Sent_Json $e ) {} return $GLOBALS['json']; }
function wp_create_nonce( $a ) { return 'n'; }
function wp_next_scheduled( $h ) { return false; }
function wp_schedule_event( $t, $r, $h ) { $GLOBALS['scheduled'] = array( $r, $h ); return true; }
function home_url( $p = '' ) { return 'https://giathuanshop.com' . $p; }
function admin_url( $p = '' ) { return 'https://giathuanshop.com/wp-admin/' . $p; }
function get_bloginfo( $k = '' ) { return 'Gia Thuận Shop'; }
function wp_specialchars_decode( $s, $q = null ) { return $s; }
function date_i18n( $f, $t = null ) { return gmdate( 'd/m/Y', null === $t ? $GLOBALS['now'] : $t ); }
function human_time_diff( $from, $to = 0 ) {
	$d = ( $to ? $to : $GLOBALS['now'] ) - $from;
	return (int) round( $d / DAY_IN_SECONDS ) . ' ngày';
}
function is_email( $e ) { return (bool) filter_var( $e, FILTER_VALIDATE_EMAIL ); }
// Chat Telegram mà 2FA đã ghép đôi sẵn — plugin vốn đã biết nó.
$GLOBALS['admins'] = array();
function get_users( $a = array() ) { return array_keys( $GLOBALS['admins'] ); }
function get_user_meta( $id, $k, $single = false ) { return $GLOBALS['admins'][ $id ] ?? ''; }

// `time()` is what the whole schedule turns on, so the test owns the clock.
// Overriding it in the plugin's namespace is not possible here, so the plugin
// files are loaded and the clock is moved by rewriting the stored timestamps.
$GLOBALS['now'] = 1770000000;

/* --- the HTTP floor, stubbed so alert.php itself is under test --- */
$GLOBALS['sent']     = array();
$GLOBALS['tg_reply'] = array( 'code' => 200, 'body' => '{"ok":true}' );
$GLOBALS['mail_ok']  = true;
function wp_safe_remote_post( $url, $args = array() ) {
	$GLOBALS['sent'][] = array( 'how' => 'telegram', 'url' => $url, 'body' => $args['body'] );
	if ( 'network' === $GLOBALS['tg_reply'] ) { return new WP_Error( 'http', 'cURL error 28' ); }
	return $GLOBALS['tg_reply'];
}
function wp_remote_retrieve_response_code( $r ) { return $r['code']; }
function wp_remote_retrieve_body( $r ) { return $r['body']; }
function wp_mail( $to, $subject, $body ) {
	$GLOBALS['sent'][] = array( 'how' => 'email', 'to' => $to, 'subject' => $subject, 'body' => $body );
	return $GLOBALS['mail_ok'];
}

$GLOBALS['opts']        = array();
$GLOBALS['trans']       = array();
$GLOBALS['admin_email'] = 'chu@giathuanshop.com';
$GLOBALS['fake_contact']  = array( 'state' => 'clean', 'added' => array(), 'removed' => array(), 'count' => 0 );
$GLOBALS['fake_link']     = array( 'state' => 'clean', 'new' => array(), 'total' => 0, 'progress' => array() );
$horsetools_options     = array();

require_once dirname( __DIR__ ) . '/inc/alert.php';
require_once dirname( __DIR__ ) . '/inc/watch-heartbeat.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . "\n"; }
}
function ok( $c, $what ) { eq( (bool) $c, true, $what ); }

function reset_all() {
	$GLOBALS['opts']  = array();
	$GLOBALS['trans'] = array();
	$GLOBALS['sent']  = array();
	$GLOBALS['horsetools_options'] = array( 'watch-hb' => 1 );
	$GLOBALS['tg_reply'] = array( 'code' => 200, 'body' => '{"ok":true}' );
	$GLOBALS['mail_ok']  = true;
}
/** Move the recorded send back in time, which is the same as moving the clock forward. */
function age_last_beat( $seconds ) {
	$s = $GLOBALS['opts'][ HORSETOOLS_HB ];
	$s['sent'] -= $seconds;
	$GLOBALS['opts'][ HORSETOOLS_HB ] = $s;
}

echo "\n1. Kênh gửi\n";
reset_all();
eq( horsetools_alert_channel(), 'email', 'chưa có bot → email' );
$GLOBALS['horsetools_options']['woo-tele11'] = 'TOKEN';
eq( horsetools_alert_channel(), 'email', 'có token mà chưa có chat → vẫn email' );
$GLOBALS['horsetools_options']['woo-tele12'] = '111';
eq( horsetools_alert_channel(), 'telegram', 'đủ token + chat → Telegram' );
eq( horsetools_alert_chat(), '111', 'chưa khai riêng thì dùng chat của đơn hàng' );
$GLOBALS['horsetools_options']['watch-tg'] = '222';
eq( horsetools_alert_chat(), '222', 'khai riêng thì ưu tiên — cảnh báo bảo mật không đi chung với đơn hàng' );

echo "\n1b. Chat mà plugin VỐN ĐÃ BIẾT — 2FA ghép đôi rồi thì đừng hỏi lại\n";
// Lỗi thật, tìm ra trên site thật: chủ site nói "bot điền sẵn đời nào rồi", và
// đúng — token 46 ký tự, bot @TDTNotifyBot, chat 724254522 nằm sẵn trong user
// meta từ lúc ghép đôi 2FA. Nhưng hàm này chỉ nhìn ô chat của đơn hàng
// WooCommerce, nên nó báo "không có Telegram" và đẩy tin bảo mật vào spam.
reset_all();
$GLOBALS['horsetools_options']['woo-tele11'] = 'TOKEN';
$GLOBALS['admins'] = array();
eq( horsetools_alert_channel(), 'email', 'không ai ghép đôi → email' );

$GLOBALS['admins'] = array( 7 => '724254522' );
eq( horsetools_alert_paired_chat(), '724254522', 'lấy được chat 2FA đã ghép' );
eq( horsetools_alert_chat(), '724254522', 'và dùng nó khi không có ô nào được khai' );
eq( horsetools_alert_channel(), 'telegram', 'site có bot + có chat đã ghép → Telegram, không cần khai thêm gì' );
ok( false !== strpos( horsetools_alert_target(), 'two-factor' ), 'màn hình nói rõ chat này lấy từ đâu — không được làm người ta bất ngờ' );

$GLOBALS['admins'] = array( 9 => '999', 3 => '333' );
eq( horsetools_alert_paired_chat(), '999', 'nhiều admin thì lấy cái get_users trả về trước — kết quả phải ỔN ĐỊNH, kể cả trong cron' );

$GLOBALS['horsetools_options']['woo-tele12'] = '111';
eq( horsetools_alert_chat(), '111', 'ô đơn hàng vẫn ưu tiên hơn chat 2FA' );
$GLOBALS['horsetools_options']['watch-tg'] = '222';
eq( horsetools_alert_chat(), '222', 'ô bảo mật riêng ưu tiên cao nhất' );
$GLOBALS['admins'] = array();

echo "\n2. Gửi thật qua Telegram\n";
reset_all();
$GLOBALS['horsetools_options']['woo-tele11'] = 'TOKEN';
$GLOBALS['horsetools_options']['watch-tg']   = '222';
eq( horsetools_alert_send( 'xin chào' )['ok'], true, 'Telegram trả ok:true → thành công' );
eq( $GLOBALS['sent'][0]['body']['chat_id'], '222', 'gửi đúng chat' );
eq( $GLOBALS['sent'][0]['body']['text'], 'xin chào', 'gửi đúng nội dung' );

echo "\n3. Telegram báo lỗi thì phải nói lại nguyên văn, không nuốt\n";
reset_all();
$GLOBALS['horsetools_options']['woo-tele11'] = 'TOKEN';
$GLOBALS['horsetools_options']['watch-tg']   = '222';
$GLOBALS['tg_reply'] = array( 'code' => 400, 'body' => '{"ok":false,"description":"chat not found"}' );
$GLOBALS['admin_email'] = '';   // chặn hẳn đường lui để xét riêng Telegram
$r = horsetools_alert_send( 'x' );
eq( $r['ok'], false, 'trả về hỏng' );
eq( $r['tried']['telegram'], 'chat not found', 'giữ nguyên lời Telegram — đó là thứ giúp người ta sửa được' );
$GLOBALS['tg_reply'] = 'network';
eq( horsetools_alert_send( 'x' )['ok'], false, 'mạng hỏng cũng là hỏng' );
$GLOBALS['admin_email'] = 'chu@giathuanshop.com';

echo "\n4. Email là sàn cuối\n";
reset_all();
eq( horsetools_alert_send( 'x', 'chủ đề' )['ok'], true, 'gửi email' );
eq( $GLOBALS['sent'][0]['how'], 'email', 'đúng đường email' );
eq( $GLOBALS['sent'][0]['to'], 'chu@giathuanshop.com', 'tới admin_email' );
$GLOBALS['mail_ok'] = false;
eq( horsetools_alert_send( 'x' )['ok'], false, 'mail server từ chối → hỏng' );

echo "\n5. Lịch\n";
reset_all();
eq( horsetools_hb_interval(), 7 * DAY_IN_SECONDS, 'mặc định 1 tuần' );
$GLOBALS['horsetools_options']['watch-hb-days'] = 30;
eq( horsetools_hb_interval(), 30 * DAY_IN_SECONDS, 'chọn 30 ngày' );
$GLOBALS['horsetools_options']['watch-hb-days'] = 9999;
eq( horsetools_hb_interval(), 7 * DAY_IN_SECONDS, 'giá trị lạ → về mặc định, không tính ra lịch điên' );

echo "\n6. Tắt thì không bao giờ tới hạn\n";
reset_all();
$GLOBALS['horsetools_options']['watch-hb'] = 0;
eq( horsetools_hb_due(), false, 'tắt → không gửi' );
horsetools_hb_tick();
eq( count( $GLOBALS['sent'] ), 0, 'và tick cũng không gửi gì' );

echo "\n7. Nhịp đầu tiên đi ngay — để biết kênh có chạy hay không\n";
reset_all();
eq( horsetools_hb_due(), true, 'chưa gửi lần nào → tới hạn ngay' );
eq( horsetools_hb_fire(), true, 'gửi được' );
eq( horsetools_hb_state()['seq'], 1, 'nhịp số 1' );
eq( horsetools_hb_due(), false, 'gửi xong thì chưa tới hạn nữa' );

echo "\n8. Đúng chu kỳ mới gửi tiếp\n";
age_last_beat( 6 * DAY_IN_SECONDS );
eq( horsetools_hb_due(), false, 'mới 6 ngày → chưa' );
age_last_beat( 1 * DAY_IN_SECONDS );
eq( horsetools_hb_due(), true, 'đủ 7 ngày → tới hạn' );
horsetools_hb_fire();
eq( horsetools_hb_state()['seq'], 2, 'nhịp số 2' );

echo "\n9. GỬI HỎNG CŨNG PHẢI TĂNG SỐ — đây là chỗ quyết định tính năng có nghĩa hay không\n";
reset_all();
$GLOBALS['horsetools_options']['woo-tele11'] = 'TOKEN';
$GLOBALS['horsetools_options']['watch-tg']   = '222';
horsetools_hb_fire();
eq( horsetools_hb_state()['seq'], 1, 'nhịp 1 gửi được' );
$GLOBALS['tg_reply'] = array( 'code' => 403, 'body' => '{"ok":false,"description":"bot was blocked by the user"}' );
$GLOBALS['mail_ok'] = false;   // chặn cả đường lui, nếu không email sẽ đỡ mất
age_last_beat( 8 * DAY_IN_SECONDS );
ok( is_wp_error( horsetools_hb_fire() ), 'nhịp 2 gửi không được (cả hai kênh đều hỏng)' );
eq( horsetools_hb_state()['seq'], 2, 'số VẪN tăng — nếu không thì kênh hỏng cả tháng trông y hệt tháng không có nhịp nào' );
eq( horsetools_hb_state()['ok'], false, 'ghi nhận là hỏng' );
ok( false !== strpos( horsetools_hb_state()['error'], 'bot was blocked by the user' ), 'ghi lại lý do của kênh chính' );
$GLOBALS['mail_ok'] = true;
age_last_beat( 8 * DAY_IN_SECONDS );
$GLOBALS['tg_reply'] = array( 'code' => 200, 'body' => '{"ok":true}' );
horsetools_hb_fire();
eq( horsetools_hb_state()['seq'], 3, 'nhịp 3' );
// Chủ site nhận được #1 rồi #3: khoảng trống đó chính là thông tin.

echo "\n9b. CHUYỂN KÊNH DỰ PHÒNG — và nó phải ỒN ÀO\n";
// Dự phòng im lặng là sai lầm kinh điển: Telegram hỏng, email lặng lẽ gánh hết,
// một năm sau chủ site tưởng mình có hai kênh trong khi chỉ còn một từ tháng Ba
// — và biết được đúng lúc cái thứ hai cũng hỏng nốt.
reset_all();
$GLOBALS['horsetools_options']['woo-tele11'] = 'TOKEN';
$GLOBALS['horsetools_options']['watch-tg']   = '222';
eq( horsetools_alert_channels(), array( 'telegram', 'email' ), 'có hai kênh, Telegram trước' );

$GLOBALS['tg_reply'] = array( 'code' => 403, 'body' => '{"ok":false,"description":"bot was blocked by the user"}' );
$r = horsetools_alert_send( 'nội dung gốc' );
eq( $r['ok'], true, 'Telegram hỏng → email vẫn đưa được tin đi' );
eq( $r['channel'], 'email', 'đi bằng email' );
eq( $r['fell_back'], true, 'và biết là đã phải dùng đường lui' );
eq( $r['tried']['telegram'], 'bot was blocked by the user', 'nhớ vì sao kênh chính hỏng' );

$body = end( $GLOBALS['sent'] )['body'];
ok( false !== strpos( $body, 'nội dung gốc' ), 'nội dung gốc vẫn nguyên' );
ok( false !== strpos( $body, 'Telegram' ), 'tin nói rõ kênh nào đã hỏng' );
ok( false !== strpos( $body, 'bot was blocked by the user' ), 'và nhắc lại nguyên văn lý do — đó mới là tin tức' );

echo "\n9c. Đi bằng đường lui là một LỖI phải báo, không phải chuyện êm xuôi\n";
reset_all();
$GLOBALS['horsetools_options']['woo-tele11'] = 'TOKEN';
$GLOBALS['horsetools_options']['watch-tg']   = '222';
$GLOBALS['tg_reply'] = array( 'code' => 403, 'body' => '{"ok":false,"description":"chat not found"}' );
horsetools_hb_fire();
eq( horsetools_hb_state()['ok'], true, 'nhịp vẫn đi được' );
eq( horsetools_hb_state()['fell_back'], true, 'nhưng ghi nhận là bằng đường lui' );
$keys = array_column( horsetools_watch_gaps(), 'key' );
ok( in_array( 'hb-fallback', $keys, true ), 'và nằm trong danh sách "chưa được bảo vệ" — im lặng ở đây là giấu lỗi' );

echo "\n9d. Không còn kênh nào thì nói thẳng\n";
reset_all();
$GLOBALS['admin_email'] = '';
eq( horsetools_alert_channels(), array(), 'không kênh nào' );
eq( horsetools_alert_send( 'x' )['ok'], false, 'không gửi được' );
$GLOBALS['admin_email'] = 'chu@giathuanshop.com';

echo "\n10. Gửi hỏng KHÔNG được biến thành vòng lặp thử lại\n";
reset_all();
$GLOBALS['mail_ok'] = false;
horsetools_hb_fire();
eq( count( $GLOBALS['sent'] ), 1, 'thử một lần' );
eq( horsetools_hb_due(), false, 'hỏng rồi vẫn tính là đã tới lượt — không thử lại mỗi lần mở trang admin' );

echo "\n11. Khoá chống gửi trùng\n";
reset_all();
horsetools_hb_tick();
horsetools_hb_tick();
horsetools_hb_tick();
eq( count( $GLOBALS['sent'] ), 1, 'ba lần tick liên tiếp chỉ ra một tin' );
eq( horsetools_hb_state()['seq'], 1, 'và chỉ một số' );

echo "\n12. Nội dung tin nhắn\n";
reset_all();
$msg = horsetools_hb_compose( 42 );
ok( false !== strpos( $msg, '#42' ), 'có số nhịp' );
ok( false !== strpos( $msg, 'giathuanshop.com' ), 'có tên site' );
ok( false !== strpos( $msg, 'Next beat due' ), 'có hạn nhịp kế — không có cái này thì không ai biết khi nào nên lo' );

echo "\n13. Tin nhắn phải chở trạng thái, không chỉ 'còn sống'\n";
function horsetools_contact_status() { return $GLOBALS['fake_contact']; }
function horsetools_link_status() { return $GLOBALS['fake_link']; }
$GLOBALS['fake_contact'] = array( 'state' => 'clean', 'added' => array(), 'removed' => array(), 'count' => 3 );
$GLOBALS['fake_link']    = array( 'state' => 'clean', 'new' => array(), 'total' => 6, 'progress' => array() );
$msg = horsetools_hb_compose( 1 );
ok( false === strpos( $msg, '[!]' ), 'mọi thứ ổn → không có dấu cảnh báo nào' );
ok( false !== strpos( $msg, '6 domains' ), 'có nói số tên miền' );

$GLOBALS['fake_link'] = array( 'state' => 'changed', 'new' => array( 'nhacai.xyz' => 1 ), 'total' => 7, 'progress' => array() );
$msg = horsetools_hb_compose( 2 );
ok( false !== strpos( $msg, '[!]' ), 'có vấn đề → có dấu cảnh báo' );
ok( false !== strpos( $msg, 'nhacai.xyz' ), 'và gọi tên thẳng ra trong tin nhắn' );

echo "\n14. Danh sách 'chưa được bảo vệ' phải nói thật\n";
reset_all();
$GLOBALS['horsetools_options']['watch-hb'] = 0;
$keys = array_column( horsetools_watch_gaps(), 'key' );
ok( in_array( 'hb-off', $keys, true ), 'tắt nhịp → nói rõ là sẽ không ai biết' );

reset_all();
$keys = array_column( horsetools_watch_gaps(), 'key' );
ok( in_array( 'hb-never', $keys, true ), 'bật mà chưa gửi lần nào → chưa chứng minh được kênh chạy' );

reset_all();
$GLOBALS['mail_ok'] = false;
horsetools_hb_fire();
$keys = array_column( horsetools_watch_gaps(), 'key' );
ok( in_array( 'hb-fail', $keys, true ), 'gửi hỏng → nói ra' );

reset_all();
horsetools_hb_fire();
eq( horsetools_watch_gaps(), array(), 'gửi được → im, không bịa thêm việc' );

age_last_beat( 7 * DAY_IN_SECONDS + 3 * DAY_IN_SECONDS );
$keys = array_column( horsetools_watch_gaps(), 'key' );
ok( in_array( 'hb-late', $keys, true ), 'quá hạn nhiều ngày → nói là không có gì chạy lịch' );

age_last_beat( -9 * DAY_IN_SECONDS ); // kéo về lại 1 ngày trước
eq( horsetools_watch_gaps(), array(), 'mới gửi hôm qua → im' );

echo "\n15. Nút gửi thử\n";
reset_all();
horsetools_hb_fire();                       // nhịp 1, chưa tới hạn nhịp sau
$before = horsetools_hb_state()['seq'];
call_ajax( 'horsetools_hb_test_ajax' );
eq( $GLOBALS['json'][0], true, 'gửi thử được dù chưa tới hạn' );
eq( horsetools_hb_state()['seq'], $before + 1, 'gửi thử cũng tính một số — người nhận thấy nó trong chuỗi, không phải tin lạ' );

reset_all();
$GLOBALS['mail_ok'] = false;
call_ajax( 'horsetools_hb_test_ajax' );
eq( $GLOBALS['json'][0], false, 'gửi thử hỏng thì báo hỏng ngay tại chỗ' );

echo "\n16. Lịch cron phải được đăng ký\n";
reset_all();
horsetools_hb_schedule();
eq( $GLOBALS['scheduled'], array( 'daily', 'horsetools_heartbeat_cron' ), 'chạy hằng ngày và chỉ hỏi "đã tới hạn chưa" — cron lỡ một ngày thì trễ một ngày, không mất cả nhịp' );

echo "\n" . str_repeat( '-', 56 ) . "\n";
echo "  $pass đạt, $fail hỏng\n\n";
exit( $fail ? 1 : 0 );
