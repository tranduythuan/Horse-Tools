<?php
/**
 * Fill in the Vietnamese strings added in Horse Tools 1.0.0 and recompile the
 * .mo files from their .po sources.
 *
 * Usage:  php tools/sync-translations.php [plugin-root]
 *
 * Re-running is safe: entries that already have a translation are left alone.
 */

require_once __DIR__ . '/po-lib.php';

$root = rtrim( $argv[1] ?? dirname( __DIR__ ), '/\\' );

$vi = array(
    'Author and maintainer:'
        => 'Tác giả và người duy trì:',
    'Licence:'
        => 'Giấy phép:',
    'Original contributors:'
        => 'Người đóng góp cho bản gốc:',
    'Foxtool by Fox Theme (Nguyễn Ngọc Hoàn)'
        => 'Foxtool của Fox Theme (Nguyễn Ngọc Hoàn)',
    'Horse Tools is built on %s, which is no longer maintained by its author. That original work is gratefully acknowledged and this fork continues under the same GPLv2 licence.'
        => 'Horse Tools được phát triển dựa trên %s, hiện tác giả không còn duy trì. Xin ghi nhận công sức của bản gốc; bản phát triển này tiếp tục theo cùng giấy phép GPLv2.',
    'Duplicate content'
        => 'Nhân bản nội dung',
    'Hide plugins from the manager'
        => 'Ẩn plugin khỏi trang quản lý',
    'This feature will hide the plugin you want from the plugin management page'
        => 'Tính năng này sẽ ẩn plugin bạn chọn khỏi trang quản lý plugin',
    'Reset this setting'
        => 'Đặt lại tuỳ chọn này',
    'Forced ad clicks — removed'
        => 'Ép click quảng cáo — đã gỡ bỏ',
    'This feature was removed in Horse Tools 1.0.0. It opened an affiliate URL in a hidden off-screen window every time a visitor clicked anywhere on the page. That is affiliate cookie stuffing: it deceives your visitors, breaks the terms of every major affiliate network and of Google AdSense, and can get your domain and your ad accounts banned.'
        => 'Tính năng này đã bị gỡ trong Horse Tools 1.0.0. Nó mở một liên kết affiliate trong cửa sổ ẩn ngoài màn hình mỗi khi khách click vào bất kỳ đâu trên trang. Đó là hành vi nhồi cookie affiliate: lừa dối người truy cập, vi phạm điều khoản của mọi mạng affiliate lớn và của Google AdSense, và có thể khiến tên miền cùng tài khoản quảng cáo của bạn bị khoá.',
    'If you had it enabled it is now inactive. The AdSense and ads.txt tools on the other tabs are unaffected.'
        => 'Nếu trước đây bạn có bật, giờ nó đã ngừng hoạt động. Các công cụ AdSense và ads.txt ở những tab khác không bị ảnh hưởng.',
    'I am sorry, you can only upload image files in the formats .GIF, .JPG, .PNG, .WEBP'
        => 'Rất tiếc, bạn chỉ có thể tải lên ảnh ở các định dạng .GIF, .JPG, .PNG, .WEBP',
    'You do not have permission to upload SVG files.'
        => 'Bạn không có quyền tải lên tệp SVG.',
    'SVG uploads are unavailable: the sanitiser library is missing.'
        => 'Không thể tải lên SVG: thiếu thư viện làm sạch tệp.',
    'Unable to read the uploaded SVG file.'
        => 'Không đọc được tệp SVG vừa tải lên.',
    'Unable to write the sanitised SVG file.'
        => 'Không ghi được tệp SVG sau khi làm sạch.',
    'This SVG file could not be parsed and was rejected.'
        => 'Không phân tích được tệp SVG này nên đã từ chối.',
    'That address cannot be fetched from this server.'
        => 'Máy chủ không được phép truy cập địa chỉ này.',
    'Security check failed. Please start the sign-in again.'
        => 'Kiểm tra bảo mật thất bại. Vui lòng đăng nhập lại từ đầu.',
    'Your Google account email address is not verified.'
        => 'Địa chỉ email của tài khoản Google chưa được xác minh.',
    'Registration is disabled on this site.'
        => 'Trang web này đang tắt chức năng đăng ký.',
    'You do not have permission to do this.'
        => 'Bạn không có quyền thực hiện thao tác này.',
    'You are not allowed to duplicate this item.'
        => 'Bạn không được phép nhân bản mục này.',
    'You are not allowed to create this post type.'
        => 'Bạn không được phép tạo loại nội dung này.',
    'Feeds are not available on this site. %s'
        => 'Trang web này không cung cấp nguồn cấp dữ liệu (feed). %s',
    'Feeds disabled'
        => 'Đã tắt nguồn cấp dữ liệu',
    'Go to the home page'
        => 'Về trang chủ',
    'None'
        => 'Không dùng',
    'v3 score threshold (0 – 1)'
        => 'Ngưỡng điểm v3 (0 – 1)',
    'The score threshold applies to reCAPTCHA v3 only. Google returns 1.0 for traffic it is confident is human and 0.0 for traffic it is confident is a bot; 0.5 is the recommended starting point. Raise it to block more aggressively, lower it if real visitors are being turned away.'
        => 'Ngưỡng điểm chỉ áp dụng cho reCAPTCHA v3. Google trả về 1.0 khi chắc chắn là người thật và 0.0 khi chắc chắn là bot; nên bắt đầu từ 0.5. Tăng lên để chặn gắt hơn, giảm xuống nếu khách thật bị chặn nhầm.',
    'If the Secret key is empty the check is skipped entirely rather than rejecting every login.'
        => 'Nếu để trống Secret key thì bước kiểm tra sẽ được bỏ qua hoàn toàn, thay vì từ chối mọi lượt đăng nhập.',
    'Source code and issues:'
        => 'Mã nguồn và báo lỗi:',
    'Search settings…'
        => 'Tìm trong cài đặt…',
    'No setting matches that.'
        => 'Không có cài đặt nào khớp.',
    'Currently enabled'
        => 'Đang bật',
    'Nothing on this page is enabled yet.'
        => 'Trang này chưa bật cài đặt nào.',
    '%d enabled'
        => 'Đang bật %d',
    'You have unsaved changes.'
        => 'Bạn có thay đổi chưa lưu.',
    'Save changes'
        => 'Lưu thay đổi',
    'Discard'
        => 'Bỏ thay đổi',
);

$viPo = $root . '/lang/horse-tools-vi.po';
$added = horsetools_append_po( $viPo, $vi );
printf( "horse-tools-vi.po: %d entries appended\n", $added );

// Recompile every .po to its .mo so the shipped binaries match the sources.
foreach ( glob( $root . '/lang/*.po' ) as $po ) {
    $entries = horsetools_read_po( $po );
    $locale  = basename( $po, '.po' );
    $header  = "Project-Id-Version: Horse Tools\n"
        . "MIME-Version: 1.0\n"
        . "Content-Type: text/plain; charset=UTF-8\n"
        . "Content-Transfer-Encoding: 8bit\n"
        . "Plural-Forms: nplurals=2; plural=(n != 1);\n"
        . "X-Domain: horse-tools\n";
    $count = horsetools_write_mo( $root . '/lang/' . $locale . '.mo', $entries, $header );
    printf( "%-28s compiled %d entries -> %s.mo\n", basename( $po ), $count, $locale );
}
