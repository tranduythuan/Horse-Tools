<?php
/**
 * Phase 1 Test Suite:
 * - Deterministic plugin hiding via basename hash (horsetools-hp-<md5>)
 * - Design System: 3 modern presets (Default, WordPress, Dark) + legacy fallback
 * - Clean ft* symbols in link/htadmin.js and main/page/5media.php
 * - WooCommerce conditional guards
 *
 * Usage: php tools/test-phase1.php
 */

define( 'ABSPATH', __DIR__ . '/' );

if ( ! function_exists( 'get_plugins' ) ) {
	function get_plugins() { return array(); }
}

$pass = 0;
$fail = 0;

function eq( $got, $want, $what ) {
	global $pass, $fail;
	if ( $got === $want ) {
		$pass++;
		echo "  ok    $what\n";
	} else {
		$fail++;
		echo "  FAIL  $what — got " . var_export( $got, true ) . ", expected " . var_export( $want, true ) . "\n";
	}
}

function ok( $condition, $what ) {
	eq( (bool) $condition, true, $what );
}

echo "=== KIỂM THỬ GIAI ĐOẠN 1 (PHASE 1 TEST SUITE) ===\n\n";

/* ---------------------------------------------------------------------------
 * 1. Ẩn plugin theo MD5 hash basename thay vì ordinal position
 * ------------------------------------------------------------------------ */
echo "1. Ẩn plugin theo MD5 hash basename (Deterministic Plugin Hiding)\n";

$src = str_replace( "\r\n", "\n", file_get_contents( dirname( __DIR__ ) . '/inc/horsetools.php' ) );
if ( ! preg_match( '/\nfunction horsetools_hide_plugins\(.*?\n\}\n/s', $src, $m ) ) {
	echo "  FAIL  Không tìm thấy hàm horsetools_hide_plugins() trong inc/horsetools.php\n";
	$fail++;
} else {
	eval( $m[0] );

	$mock_plugins = array(
		'akismet/akismet.php'         => array( 'Name' => 'Akismet' ),
		'woocommerce/woocommerce.php' => array( 'Name' => 'WooCommerce' ),
		'contact-form-7/wp-contact-form-7.php' => array( 'Name' => 'Contact Form 7' ),
		'hello.php'                   => array( 'Name' => 'Hello Dolly' ),
	);

	// Case 1: Ẩn đúng plugin chỉ định theo hash
	global $horsetools_options;
	$horsetools_options = array(
		'horsetools-hp-' . md5( 'woocommerce/woocommerce.php' ) => 1,
	);
	$filtered = horsetools_hide_plugins( $mock_plugins );
	ok( ! isset( $filtered['woocommerce/woocommerce.php'] ), 'WooCommerce bị ẩn thành công' );
	ok( isset( $filtered['akismet/akismet.php'] ), 'Akismet vẫn hiển thị' );
	ok( isset( $filtered['contact-form-7/wp-contact-form-7.php'] ), 'Contact Form 7 vẫn hiển thị' );
	ok( isset( $filtered['hello.php'] ), 'Hello Dolly vẫn hiển thị' );

	// Case 2: Đổi thứ tự mảng không làm ẩn sai plugin (ngược lại với bug cũ lấy index số thứ tự)
	$reordered_plugins = array(
		'hello.php'                   => array( 'Name' => 'Hello Dolly' ),
		'contact-form-7/wp-contact-form-7.php' => array( 'Name' => 'Contact Form 7' ),
		'woocommerce/woocommerce.php' => array( 'Name' => 'WooCommerce' ),
		'akismet/akismet.php'         => array( 'Name' => 'Akismet' ),
	);
	$filtered_reordered = horsetools_hide_plugins( $reordered_plugins );
	ok( ! isset( $filtered_reordered['woocommerce/woocommerce.php'] ), 'WooCommerce vẫn bị ẩn chính xác khi thứ tự mảng đảo lộn' );
	ok( isset( $filtered_reordered['hello.php'] ), 'Hello Dolly không bị ẩn nhầm' );

	// Case 3: Không chọn ẩn plugin nào
	$horsetools_options = array();
	$unfiltered = horsetools_hide_plugins( $mock_plugins );
	eq( count( $unfiltered ), 4, 'Khi không cấu hình ẩn thì giữ nguyên toàn bộ plugin' );
}

/* ---------------------------------------------------------------------------
 * 2. Tinh gọn Design System: 3 skins hiện đại + fallback
 * ------------------------------------------------------------------------ */
echo "\n2. Hệ thống Giao diện (Design System 3 Modern Skins)\n";

$style_file = dirname( __DIR__ ) . '/main/style.php';
$style_src = file_get_contents( $style_file );

function render_style_output( $skin_name ) {
	global $horsetools_options;
	$horsetools_options = array( 'horsetools5' => $skin_name );
	ob_start();
	include dirname( __DIR__ ) . '/main/style.php';
	return ob_get_clean();
}

// Case 1: WordPress Native
$wp_out = render_style_output( 'WordPress' );
ok( strpos( $wp_out, '#2271b1' ) !== false, 'Skin WordPress áp dụng đúng màu gốc #2271b1' );

// Case 2: Dark Mode
$dark_out = render_style_output( 'Dark' );
ok( strpos( $dark_out, '--colorbar' ) !== false && ( strpos( $dark_out, '#1e1e1e' ) !== false || strpos( $dark_out, '#2f2f2f' ) !== false ), 'Skin Dark Mode xuất biến màu tối chuẩn' );

// Case 3: Black alias cho Dark Mode
$black_out = render_style_output( 'Black' );
ok( strpos( $black_out, '--colorbar' ) !== false, 'Skin Black tương thích ngược với Dark Mode' );

// Case 4: Default hoặc skin cũ bị loại bỏ (Bright, Girly, Rocket, Coffe, Blue)
$bright_out = render_style_output( 'Bright' );
eq( $bright_out, '', 'Skin cũ Bright fallback về Default (không xuất CSS ghi đè rác)' );
$default_out = render_style_output( 'Default' );
eq( $default_out, '', 'Skin Default không cần style ghi đè, kế thừa htadmin.css' );

/* ---------------------------------------------------------------------------
 * 3. Chuẩn hóa định danh legacy ft* sang ht*
 * ------------------------------------------------------------------------ */
echo "\n3. Chuẩn hóa định danh legacy ft* sang ht*\n";

$htadmin_js = file_get_contents( dirname( __DIR__ ) . '/link/htadmin.js' );
ok( strpos( $htadmin_js, 'function htSelectedRank' ) !== false, 'htadmin.js định nghĩa function htSelectedRank()' );
ok( strpos( $htadmin_js, 'window.ftSelectedRank = htSelectedRank' ) !== false || strpos( $htadmin_js, 'ftSelectedRank = htSelectedRank' ) !== false, 'htadmin.js giữ alias ftSelectedRank tương thích ngược' );

$media_page = file_get_contents( dirname( __DIR__ ) . '/main/page/5media.php' );
ok( strpos( $media_page, 'htwBoxes' ) !== false, 'main/page/5media.php đã chuẩn hóa thành htwBoxes' );
ok( strpos( $media_page, 'ftwBoxes' ) === false, 'main/page/5media.php không còn tàn dư ftwBoxes' );

/* ---------------------------------------------------------------------------
 * 4. Conditional Loading cho WooCommerce
 * ------------------------------------------------------------------------ */
echo "\n4. Nạp module có điều kiện & an toàn cho WooCommerce\n";

$woo_inc = file_get_contents( dirname( __DIR__ ) . '/inc/woo.php' );
ok( preg_match( '/class_exists\s*\(\s*[\'"]WooCommerce[\'"]\s*\)/', $woo_inc ), 'inc/woo.php kiểm tra class_exists("WooCommerce") ngay đầu file' );

$modal_php = file_get_contents( dirname( __DIR__ ) . '/modal/modal.php' );
ok( strpos( $modal_php, 'class_exists' ) !== false && strpos( $modal_php, 'WooCommerce' ) !== false, 'modal/modal.php bỏ qua nạp inc/woo.php nếu không có WooCommerce' );

echo "\n--------------------------------------------------------\n";
echo "  Kết quả: $pass đạt, $fail hỏng\n";

if ( $fail > 0 ) {
	exit( 1 );
}
exit( 0 );
