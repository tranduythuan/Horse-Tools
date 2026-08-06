<?php
/**
 * Prove the Telegram pairing hands back only the chat that sent YOUR code.
 *
 * The old "detect my chat ID" returned every recent chat of the site's bot to
 * any logged-in user, which on a shop meant customers could read each other's
 * Telegram names and ids. The matcher below is what replaced it, and it is the
 * piece that decides whose chat gets handed over — so it is the piece worth
 * testing against the shapes Telegram actually sends.
 *
 * Usage:  php tools/test-2fa-pairing.php
 */

define( 'ABSPATH', __DIR__ );

/** Lift one function's source out of a file, by brace matching. */
function ht_extract_function( $file, $name ) {
	$code = file_get_contents( $file );
	if ( ! preg_match( '/function\s+' . preg_quote( $name, '/' ) . '\s*\(/', $code, $m, PREG_OFFSET_CAPTURE ) ) {
		fwrite( STDERR, "không tìm thấy $name\n" );
		exit( 1 );
	}
	$from  = $m[0][1];
	$start = strpos( $code, '{', $from );
	$d     = 0;
	for ( $j = $start, $len = strlen( $code ); $j < $len; $j++ ) {
		if ( '{' === $code[ $j ] ) { $d++; } elseif ( '}' === $code[ $j ] ) {
			$d--;
			if ( 0 === $d ) { return substr( $code, $from, $j - $from + 1 ); }
		}
	}
	fwrite( STDERR, "không đóng được ngoặc\n" );
	exit( 1 );
}

$tmp = __DIR__ . '/_extracted-tg-match.php';
file_put_contents( $tmp, "<?php\n" . ht_extract_function( dirname( __DIR__ ) . '/inc/2fa.php', 'horsetools_2fa_tg_match_chat' ) . "\n" );
require $tmp;
unlink( $tmp );

$pass = 0;
$fail = 0;
function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) { $pass++; echo "  ok    $what\n"; }
	else { $fail++; echo "  FAIL  $what — got " . var_export( $got, true ) . ", want " . var_export( $want, true ) . "\n"; }
}

/** A getUpdates result, as Telegram sends it. */
function upd( $chat_id, $text, $name = 'Someone' ) {
	return array( 'update_id' => 1, 'message' => array(
		'chat' => array( 'id' => $chat_id, 'first_name' => $name, 'username' => strtolower( $name ) ),
		'text' => $text,
	) );
}

echo "\n1. Chỉ trả về chat đã gửi ĐÚNG mã của mình\n";
$updates = array(
	upd( 111, 'xin chao',      'An' ),
	upd( 222, 'HT-AAA111',     'Binh' ),   // người khác, mã khác
	upd( 333, 'HT-ZZZ999',     'Cuong' ),  // của tôi
	upd( 444, 'HT-AAA111 nhe', 'Dung' ),
);
eq( horsetools_2fa_tg_match_chat( $updates, 'HT-ZZZ999' ), '333', 'ra đúng chat của mình' );
eq( horsetools_2fa_tg_match_chat( $updates, 'HT-AAA111' ), '222', 'người khác ra chat của họ, không phải của tôi' );

echo "\n2. Không có mã thì không ai được trả về\n";
eq( horsetools_2fa_tg_match_chat( $updates, 'HT-NOPE00' ), '', 'mã chưa gửi → rỗng' );
eq( horsetools_2fa_tg_match_chat( $updates, '' ), '', 'mã rỗng KHÔNG khớp bừa (đây là chỗ dễ hổng nhất)' );
eq( horsetools_2fa_tg_match_chat( $updates, '   ' ), '', 'toàn khoảng trắng cũng vậy' );
eq( horsetools_2fa_tg_match_chat( array(), 'HT-ZZZ999' ), '', 'không có tin nhắn nào → rỗng' );

echo "\n3. Không lộ gì ngoài id\n";
$out = horsetools_2fa_tg_match_chat( $updates, 'HT-ZZZ999' );
eq( is_string( $out ), true, 'trả về một chuỗi id, không phải object có tên/username' );
eq( false === strpos( $out, 'Cuong' ), true, 'không kèm tên' );

echo "\n4. Các hình dạng lạ Telegram vẫn gửi\n";
eq( horsetools_2fa_tg_match_chat( array( array( 'update_id' => 9 ) ), 'HT-X' ), '', 'update không có message' );
eq( horsetools_2fa_tg_match_chat( array( array( 'message' => array( 'chat' => array( 'id' => 5 ) ) ) ), 'HT-X' ), '', 'message không có text' );
eq( horsetools_2fa_tg_match_chat( array( array( 'message' => array( 'text' => 'HT-X' ) ) ), 'HT-X' ), '', 'message không có chat id' );
eq( horsetools_2fa_tg_match_chat( array( 'rác', null, 5 ), 'HT-X' ), '', 'phần tử không phải mảng' );
eq(
	horsetools_2fa_tg_match_chat( array( array( 'channel_post' => array( 'chat' => array( 'id' => -100 ), 'text' => 'HT-X' ) ) ), 'HT-X' ),
	'-100',
	'channel_post (id âm) vẫn nhận'
);

echo "\n5. Chữ hoa thường và mã nằm giữa câu\n";
eq( horsetools_2fa_tg_match_chat( array( upd( 777, 'ht-zzz999' ) ), 'HT-ZZZ999' ), '777', 'không phân biệt hoa thường' );
eq( horsetools_2fa_tg_match_chat( array( upd( 888, 'ma cua toi la HT-ZZZ999 nhe' ) ), 'HT-ZZZ999' ), '888', 'mã nằm giữa câu' );

echo "\n6. Ai tới trước được trước (một mã chỉ thuộc một người)\n";
eq(
	horsetools_2fa_tg_match_chat( array( upd( 1, 'HT-DUP' ), upd( 2, 'HT-DUP' ) ), 'HT-DUP' ),
	'1',
	'hai người cùng gửi một mã → lấy cái đầu, không mơ hồ'
);

printf( "\n%d passed, %d failed\n", $pass, $fail );
exit( $fail ? 1 : 0 );
