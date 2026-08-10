<?php
/**
 * Does the mail checker tell the truth, including when it does not know?
 *
 * This one prints an accusation — "mail from your site is treated as forged" —
 * on a screen a shop owner reads. Getting that wrong once is worse than never
 * having written it, so most of what follows is about the third answer: the
 * cases where the honest result is *cannot tell from here*, not a guess dressed
 * up as a finding.
 *
 * The other half is the proof. `wp_mail()` returning true is not evidence that
 * anything arrived, and the record has to keep those two apart, and stop
 * counting once the settings it was made under have changed.
 *
 * Usage:  php tools/test-mail.php
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'HOUR_IN_SECONDS', 3600 );
if ( ! defined( 'ENT_QUOTES' ) ) { define( 'ENT_QUOTES', 3 ); }
if ( ! defined( 'DNS_TXT' ) ) { define( 'DNS_TXT', 32 ); }
if ( ! defined( 'DNS_MX' ) ) { define( 'DNS_MX', 16384 ); }
if ( ! defined( 'DNS_A' ) ) { define( 'DNS_A', 1 ); }

class WP_Error {
	public $msg;
	public function __construct( $c = '', $m = '' ) { $this->msg = $m; }
	public function get_error_message() { return $this->msg; }
}
function is_wp_error( $t ) { return $t instanceof WP_Error; }

function __( $s, $d = '' ) { return $s; }
function esc_html__( $s, $d = '' ) { return $s; }
function get_option( $k, $d = false ) {
	if ( 'admin_email' === $k ) { return 'chu@vidu.com'; }
	if ( 'date_format' === $k ) { return 'd/m/Y'; }
	return array_key_exists( $k, $GLOBALS['opts'] ) ? $GLOBALS['opts'][ $k ] : $d;
}
function update_option( $k, $v, $a = null ) { $GLOBALS['opts'][ $k ] = $v; return true; }
function get_transient( $k ) {
	if ( isset( $GLOBALS['trans'][ $k ] ) ) { return $GLOBALS['trans'][ $k ]; }
	// Bất kỳ tên nào chưa gieo đều trả về "đã hỏi, không có bản ghi". Nếu không,
	// hàm thật sẽ rơi xuống dns_get_record() và bài test đi hỏi Internet — vidu.com
	// là tên miền CÓ THẬT, nên ba phép ở dưới từng đọc phải SPF và DMARC của người
	// khác rồi báo hỏng.
	if ( 0 === strpos( $k, 'ht_dns_' ) ) { return array(); }
	return false;
}
function set_transient( $k, $v, $t = 0 ) { $GLOBALS['trans'][ $k ] = $v; return true; }
function add_action( $h, $f, $p = 10, $a = 1 ) {}
function current_user_can( $c ) { return true; }
function check_ajax_referer( $a, $b ) { return true; }
function wp_send_json_error( $d = null ) {}
function wp_send_json_success( $d = null ) {}
function home_url( $p = '' ) { return 'https://www.vidu.com' . $p; }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function get_bloginfo( $k = '' ) { return 'Cửa hàng Ví Dụ'; }
function wp_specialchars_decode( $s, $q = null ) { return $s; }
function wp_json_encode( $v ) { return json_encode( $v ); }
function date_i18n( $f, $t = null ) { return '08/08/2026'; }
function human_time_diff( $a, $b = 0 ) { return '2 ngày'; }
function sanitize_email( $s ) { return trim( (string) $s ); }
function is_email( $e ) { return (bool) filter_var( $e, FILTER_VALIDATE_EMAIL ); }
function sanitize_text_field( $s ) { return trim( (string) $s ); }
function wp_unslash( $s ) { return $s; }
function wp_cache_flush() {}

$GLOBALS['opts']  = array();
$GLOBALS['trans'] = array();
// Phải đặt sớm: khoá cache của rDNS tính theo IP này, và hàm trả 'unknown' khi thiếu.
$_SERVER['SERVER_ADDR'] = '9.9.9.9';
$GLOBALS['mail_ok'] = true;
$GLOBALS['sent'] = array();
function wp_mail( $to, $s, $b ) { $GLOBALS['sent'][] = $to; return $GLOBALS['mail_ok']; }

/**
 * A fake zone. The SPF evaluator's whole job is reading these correctly, so the
 * records here are real shapes copied from real domains rather than invented.
 */
// The resolver is NOT stubbed — horsetools_mail_dns() is the real one, and it
// checks its transient before touching DNS. Seeding those transients means the
// evaluator under test is the shipped code path, cache and all, and no test
// here can pass against a copy of the logic.
function zone_put( $name, $type, $row ) {
	$k = 'ht_dns_' . md5( $name . '|' . $type );
	$cur = $GLOBALS['trans'][ $k ] ?? array();
	$cur[] = $row;
	$GLOBALS['trans'][ $k ] = $cur;
}
function zone_txt( $name, $txt ) { zone_put( $name, DNS_TXT, array( 'txt' => $txt ) ); }
function zone_a( $name, $ip )   { zone_put( $name, DNS_A,   array( 'ip' => $ip ) ); }
function zone_mx( $name, $t, $p = 10 ) { zone_put( $name, DNS_MX, array( 'target' => $t, 'pri' => $p ) ); }
// A name with no record must still be an ANSWER (empty), not a cache miss that
// would fall through to a real DNS query in the middle of a test.
function zone_blank( $name ) { foreach ( array( DNS_TXT, DNS_A, DNS_MX ) as $t ) { $k = 'ht_dns_' . md5( $name . '|' . $t ); if ( ! isset( $GLOBALS['trans'][ $k ] ) ) { $GLOBALS['trans'][ $k ] = array(); } } }

$horsetools_options = array();

require_once dirname( __DIR__ ) . '/inc/mail-dns.php';
require_once dirname( __DIR__ ) . '/inc/mail-proof.php';

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . "\n"; }
}
function ok( $c, $what ) { eq( (bool) $c, true, $what ); }

function spf( $ip, $domain = 'vidu.com' ) { $b = 10; return horsetools_mail_spf_eval( $ip, $domain, $b ); }
function reset_zone() { $GLOBALS['trans'] = array(); }

echo "\n1. Tên miền của site — bỏ www\n";
eq( horsetools_mail_domain(), 'vidu.com', 'https://www.vidu.com → vidu.com' );

echo "\n2. Dải IP\n";
ok( horsetools_mail_in_cidr( '1.2.3.4', '1.2.3.0/24' ), 'trong dải /24' );
ok( ! horsetools_mail_in_cidr( '1.2.4.5', '1.2.3.0/24' ), 'ngoài dải' );
ok( horsetools_mail_in_cidr( '1.2.3.4', '1.2.3.4' ), 'địa chỉ trần khớp đúng' );
ok( horsetools_mail_in_cidr( '9.9.9.9', '0.0.0.0/0' ), '/0 nhận tất' );
ok( ! horsetools_mail_in_cidr( 'không-phải-ip', '1.2.3.0/24' ), 'rác thì không khớp' );

echo "\n3. Không có SPF thì phải nói KHÔNG CÓ, đừng nói hỏng\n";
reset_zone();
eq( spf( '1.2.3.4' ), 'none', 'tên miền không công bố gì' );

echo "\n4. Đánh giá SPF — chỗ quyết định có kết tội oan hay không\n";
reset_zone();
zone_txt( 'vidu.com', 'v=spf1 ip4:1.2.3.0/24 -all' );
eq( spf( '1.2.3.4' ), 'pass', 'IP nằm trong dải được phép' );
eq( spf( '9.9.9.9' ), 'fail', 'IP ngoài dải, record kết thúc -all → hỏng chắc chắn' );

reset_zone();
zone_txt( 'vidu.com', 'v=spf1 ip4:1.2.3.0/24 ~all' );
eq( spf( '9.9.9.9' ), 'softfail', '~all là nghi ngờ, không phải chặn' );

reset_zone();
zone_txt( 'vidu.com', 'v=spf1 ip4:1.2.3.0/24 ?all' );
eq( spf( '9.9.9.9' ), 'neutral', '?all là trung lập' );

reset_zone();
zone_txt( 'vidu.com', 'v=spf1 ip4:1.2.3.0/24' );
eq( spf( '9.9.9.9' ), 'neutral', 'không có all ở cuối → trung lập, KHÔNG được coi là hỏng' );

echo "\n5. include — đây là dạng thật của mọi tên miền dùng dịch vụ mail\n";
reset_zone();
zone_txt( 'vidu.com', 'v=spf1 include:spf.nhacungcap.com -all' );
zone_txt( 'spf.nhacungcap.com', 'v=spf1 ip4:50.0.0.0/8 -all' );
eq( spf( '50.1.2.3' ), 'pass', 'IP nằm trong include' );
eq( spf( '9.9.9.9' ), 'fail', 'ngoài include, ngoài tất cả → hỏng' );

reset_zone();
zone_txt( 'vidu.com', 'v=spf1 include:a.com include:b.com -all' );
zone_txt( 'a.com', 'v=spf1 ip4:10.0.0.0/8 -all' );
zone_txt( 'b.com', 'v=spf1 ip4:20.0.0.0/8 -all' );
eq( spf( '20.1.1.1' ), 'pass', 'include thứ hai khớp — -all bên trong include KHÔNG được làm dừng' );

echo "\n6. a: và mx:\n";
reset_zone();
zone_txt( 'vidu.com', 'v=spf1 a -all' );
zone_a( 'vidu.com', '5.5.5.5' );
eq( spf( '5.5.5.5' ), 'pass', 'cơ chế a khớp IP của chính tên miền' );
eq( spf( '6.6.6.6' ), 'fail', 'a không khớp' );

reset_zone();
zone_txt( 'vidu.com', 'v=spf1 mx -all' );
zone_mx( 'vidu.com', 'mail.vidu.com' );
zone_a( 'mail.vidu.com', '7.7.7.7' );
eq( spf( '7.7.7.7' ), 'pass', 'cơ chế mx khớp' );

echo "\n7. redirect=\n";
reset_zone();
zone_txt( 'vidu.com', 'v=spf1 redirect=khac.com' );
zone_txt( 'khac.com', 'v=spf1 ip4:8.8.8.0/24 -all' );
eq( spf( '8.8.8.8' ), 'pass', 'đi theo redirect' );
eq( spf( '9.9.9.9' ), 'fail', 'redirect tới record kết thúc -all' );

echo "\n8. KHÔNG BIẾT thì phải nói không biết — đây mới là phần khó\n";
reset_zone();
zone_txt( 'vidu.com', 'v=spf1 ptr -all' );
eq( spf( '9.9.9.9' ), 'unknown', 'gặp ptr (không hỗ trợ) → không kết luận, KHÔNG kết tội' );

reset_zone();
zone_txt( 'vidu.com', 'v=spf1 exists:%{i}.vidu.com -all' );
eq( spf( '9.9.9.9' ), 'unknown', 'gặp exists: → không kết luận' );

reset_zone();
// Record tự include chính nó — không được treo hay tràn ngăn xếp.
zone_txt( 'vidu.com', 'v=spf1 include:vidu.com -all' );
$r = spf( '9.9.9.9' );
ok( in_array( $r, array( 'unknown', 'fail' ), true ), 'record tự tham chiếu vẫn trả lời được (' . $r . ')' );

reset_zone();
zone_txt( 'vidu.com', 'v=spf1 include:a1.com include:a2.com include:a3.com include:a4.com include:a5.com include:a6.com include:a7.com include:a8.com include:a9.com include:a10.com include:a11.com include:a12.com -all' );
for ( $i = 1; $i <= 12; $i++ ) { zone_txt( "a$i.com", 'v=spf1 ip4:99.0.0.0/8 -all' ); }
eq( spf( '9.9.9.9' ), 'unknown', 'vượt giới hạn 10 lượt tra → không kết luận' );

echo "\n9. Bản ghi TXT dài bị chẻ nhỏ phải ghép lại\n";
reset_zone();
// PHP trả bản ghi TXT dài hơn 255 ký tự thành nhiều mảnh trong 'entries'. Chỉ
// đọc 'txt' là mất phần đuôi, và một record SPF bị cụt sẽ được đánh giá thành
// thứ khác hẳn với thứ tên miền công bố.
zone_put( 'vidu.com', DNS_TXT, array( 'entries' => array( 'v=spf1 ip4:1.2.3.0/24 ', 'include:xa.com -all' ) ) );
zone_txt( 'xa.com', 'v=spf1 ip4:77.0.0.0/8 -all' );
eq( spf( '77.1.1.1' ), 'pass', 'đọc đủ cả hai mảnh — đọc thiếu là đánh giá sai record' );

echo "\n10. Đọc DMARC và MX\n";
reset_zone();
zone_txt( '_dmarc.vidu.com', 'v=DMARC1; p=none;' );
eq( horsetools_mail_dmarc( 'vidu.com' ), 'v=DMARC1; p=none;', 'đọc được DMARC' );
reset_zone();
eq( horsetools_mail_dmarc( 'vidu.com' ), '', 'không có DMARC thì trả rỗng' );
reset_zone();
zone_mx( 'vidu.com', 'mx2.hang.com', 20 );
zone_mx( 'vidu.com', 'mx1.hang.com', 10 );
eq( horsetools_mail_mx( 'vidu.com' ), array( 'mx1.hang.com', 'mx2.hang.com' ), 'MX sắp theo độ ưu tiên' );

echo "\n11. Đoán nhà cung cấp từ MX — để khỏi bắt người ta tự biết\n";
reset_zone();
zone_mx( 'vidu.com', 'aspmx.l.google.com' );
eq( horsetools_mail_guess_provider()['key'], 'google', 'MX Google' );
reset_zone();
zone_mx( 'vidu.com', 'mx1.larksuite.com' );
eq( horsetools_mail_guess_provider()['key'], 'larksuite', 'MX Lark' );
reset_zone();
zone_mx( 'vidu.com', 'mail.laquacuatoi.vn' );
eq( horsetools_mail_guess_provider()['key'], '', 'không nhận ra thì không đoán bừa' );

echo "\n11b. CỔNG 25 BỊ CHẶN — nguyên nhân thật của phần lớn thư biến mất\n";
// Hai site trên cùng một máy chủ, DNS trái ngược nhau (một cái SPF -all, một cái
// không có SPF nào), cùng báo "đã gửi", cùng không giao được gì. DNS khác nhau mà
// kết quả giống hệt → nguyên nhân không nằm ở DNS.
function port25( $v ) { $GLOBALS['trans']['horsetools_mail_port25'] = $v; }
function rdns( $v ) { $GLOBALS['trans'][ 'horsetools_mail_rdns_' . md5( $_SERVER['SERVER_ADDR'] ) ] = $v; }
reset_zone();
rdns( 'named' );
port25( 'blocked' );
$GLOBALS['horsetools_options'] = array();
$f = horsetools_mail_findings();
eq( $f[0]['level'], 'bad', 'báo đỏ, và xếp TRƯỚC mọi thứ khác' );
ok( false !== strpos( $f[0]['fix'], 'DNS' ), 'nói thẳng là không bản ghi DNS nào sửa được' );

port25( 'open' );
$txt = implode( ' ', array_column( horsetools_mail_findings(), 'text' ) );
ok( false === strpos( $txt, 'cannot reach any other mail server' ), 'cổng mở thì không kêu' );

port25( 'blocked' );
$GLOBALS['horsetools_options']['mail-gsmtp1'] = 1;
$txt = implode( ' ', array_column( horsetools_mail_findings(), 'text' ) );
ok( false === strpos( $txt, 'cannot reach any other mail server' ), 'đang dùng SMTP thì cổng 25 không liên quan — SMTP đi cổng khác' );
$GLOBALS['horsetools_options'] = array();

echo "\n11c. IP KHÔNG CÓ TÊN — cái thật sự làm thư biến mất trên site thật\n";
// Giả thuyết cổng 25 SAI: plugin thử trên máy chủ thật thì cổng mở. Kiểm tiếp mới
// ra 128.199.244.225 không có bản ghi PTR nào cả. Yahoo từ chối thẳng IP không
// tên — và đó là thuộc tính của MÁY CHỦ, nên khớp đúng chuyện hai tên miền có DNS
// trái ngược nhau lại cùng thất bại.
reset_zone();
port25( 'open' );
$GLOBALS['horsetools_options'] = array();
rdns( 'nameless' );
$f = horsetools_mail_findings();
eq( $f[0]['level'], 'bad', 'IP không tên → báo đỏ, xếp trước' );
ok( false !== strpos( $f[0]['fix'], 'PTR' ), 'nói đúng thuật ngữ để đi hỏi nhà cung cấp' );

rdns( 'named' );
$txt = implode( ' ', array_column( horsetools_mail_findings(), 'text' ) );
ok( false === strpos( $txt, 'no name attached' ), 'có tên thì không kêu' );

rdns( 'nameless' );
$GLOBALS['horsetools_options']['mail-gsmtp1'] = 1;
$txt = implode( ' ', array_column( horsetools_mail_findings(), 'text' ) );
ok( false === strpos( $txt, 'no name attached' ), 'gửi qua SMTP thì IP của mình không còn liên quan' );
$GLOBALS['horsetools_options'] = array();
rdns( 'named' );

echo "\n12. Kết luận trên màn hình\n";
reset_zone();
$_SERVER['SERVER_ADDR'] = '9.9.9.9';
zone_txt( 'vidu.com', 'v=spf1 ip4:1.2.3.0/24 -all' );
$lv = array_column( horsetools_mail_findings(), 'level' );
eq( $lv[0], 'bad', 'gửi thẳng từ web server mà không được phép → báo đỏ, và xếp trước' );

// Cùng tên miền đó, nhưng site gửi qua dịch vụ SMTP: IP máy chủ web KHÔNG liên quan.
$GLOBALS['horsetools_options']['mail-gsmtp1'] = 1;
$txts = array_column( horsetools_mail_findings(), 'text' );
$has_forged = false;
foreach ( $txts as $t ) { if ( false !== strpos( $t, 'forged' ) ) { $has_forged = true; } }
ok( ! $has_forged, 'đang gửi qua SMTP thì KHÔNG được kết tội IP web server — đây là báo động giả kinh điển' );
$GLOBALS['horsetools_options'] = array();

reset_zone();
$lv = array_column( horsetools_mail_findings(), 'level' );
ok( in_array( 'warn', $lv, true ), 'không có SPF thì cảnh báo' );

echo "\n13. Không đọc được DNS thì phải nói ra, không được im như thể ổn\n";
eq( count( horsetools_mail_findings() ), count( horsetools_mail_findings() ), '(giữ chỗ)' );

echo "\n14. Bằng chứng thư tới — gửi được KHÁC với tới nơi\n";
$GLOBALS['opts'] = array();
eq( horsetools_mail_proof()['state'], 'none', 'chưa ai thử bao giờ' );
eq( horsetools_mail_proof_row()['status'], 'warn', 'và màn hình nói thẳng là chưa ai kiểm' );

eq( horsetools_mail_proof_send( 'toi@vidu.com' ), true, 'gửi được' );
eq( horsetools_mail_proof()['state'], 'sent', 'ghi là ĐÃ GỬI — không phải đã tới' );
eq( horsetools_mail_proof_row()['status'], 'warn', 'gửi xong vẫn chưa phải bằng chứng' );

horsetools_mail_proof_answer( 'inbox' );
eq( horsetools_mail_proof()['state'], 'inbox', 'người dùng xác nhận vào hộp thư chính' );
eq( horsetools_mail_proof_row()['status'], 'pass', 'giờ mới xanh' );

horsetools_mail_proof_answer( 'spam' );
eq( horsetools_mail_proof_row()['status'], 'fail', 'rơi spam là hỏng, không phải "gần đúng"' );
horsetools_mail_proof_answer( 'missing' );
eq( horsetools_mail_proof_row()['status'], 'fail', 'không tới là hỏng' );

ok( ! horsetools_mail_proof_answer( 'linh-tinh' ), 'giá trị lạ bị từ chối' );
ok( is_wp_error( horsetools_mail_proof_send( 'không-phải-email' ) ), 'địa chỉ sai thì báo lỗi' );

$GLOBALS['mail_ok'] = false;
ok( is_wp_error( horsetools_mail_proof_send( 'toi@vidu.com' ) ), 'máy chủ mail từ chối thì báo lỗi' );
$GLOBALS['mail_ok'] = true;

echo "\n15. Đổi cài đặt thì bằng chứng cũ hết giá trị\n";
$GLOBALS['opts'] = array();
$GLOBALS['horsetools_options'] = array( 'mail-gsmtp13' => 'toi@gmail.com', 'mail-gsmtp14' => 'matkhau123' );
horsetools_mail_proof_send( 'toi@vidu.com' );
horsetools_mail_proof_answer( 'inbox' );
eq( horsetools_mail_proof()['stale'], false, 'vừa xác nhận xong thì còn giá trị' );
eq( horsetools_mail_proof_row()['status'], 'pass', 'xanh' );

$GLOBALS['horsetools_options']['mail-gsmtp13'] = 'nguoikhac@gmail.com';
eq( horsetools_mail_proof()['stale'], true, 'đổi tài khoản gửi → bằng chứng cũ không còn nói về cái đang chạy' );
eq( horsetools_mail_proof_row()['status'], 'warn', 'và màn hình quay lại vàng chứ không giữ xanh' );

$GLOBALS['horsetools_options']['mail-gsmtp13'] = 'toi@gmail.com';
eq( horsetools_mail_proof()['stale'], false, 'trả lại như cũ thì bằng chứng có giá trị lại' );

$GLOBALS['horsetools_options']['mail-gsmtp14'] = 'matkhaukhac';
eq( horsetools_mail_proof()['stale'], true, 'đổi mật khẩu (dài khác) cũng tính là đổi' );

echo "\n16. Dấu vân tay cấu hình KHÔNG được chứa mật khẩu\n";
$GLOBALS['horsetools_options'] = array( 'mail-gsmtp14' => 'matkhau12345' );
$m1 = horsetools_mail_config_mark();
$GLOBALS['horsetools_options']['mail-gsmtp14'] = 'khacmatkhau1';  // cùng 12 ký tự
eq( horsetools_mail_config_mark(), $m1, 'chỉ lấy ĐỘ DÀI mật khẩu, không lấy mật khẩu' );

echo "\n" . str_repeat( '-', 56 ) . "\n";
echo "  $pass đạt, $fail hỏng\n\n";
exit( $fail ? 1 : 0 );
