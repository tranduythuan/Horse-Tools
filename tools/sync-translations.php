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
    // Clean screen — schedule + confirmation
    'SCHEDULE'
        => 'LỊCH',
    'Automatic cleanup'
        => 'Dọn dẹp tự động',
    'Save schedule'
        => 'Lưu lịch',
    'Off'     => 'Tắt',
    'Daily'   => 'Hằng ngày',
    'Weekly'  => 'Hằng tuần',
    'Monthly' => 'Hằng tháng',
    'Cancel'  => 'Huỷ',
    'Confirm deletion'
        => 'Xác nhận xoá',
    'Pending comments'  => 'Bình luận chờ duyệt',
    'Spam comments'     => 'Bình luận spam',
    'Trashed comments'  => 'Bình luận trong thùng rác',
    'Comments containing links'
        => 'Bình luận chứa liên kết',
    'I understand this permanently deletes data.'
        => 'Tôi hiểu thao tác này xoá dữ liệu vĩnh viễn.',
    'Nothing to delete.'
        => 'Không có gì để xoá.',
    'Error — nothing was deleted.'
        => 'Lỗi — không xoá được gì.',
    'up to %s'
        => 'tối đa %s',
    'Deleted: %s'
        => 'Đã xoá: %s',
    'Deleted %1$s of %2$s scanned'
        => 'Đã xoá %1$s trong %2$s đã quét',
    'Next automatic check: %s'
        => 'Lần kiểm tra tự động kế tiếp: %s',
    'Permanently delete %1$s from “%2$s”? This cannot be undone.'
        => 'Xoá vĩnh viễn %1$s từ “%2$s”? Không thể hoàn tác.',
    'Run “%s” now? This permanently deletes matching items and cannot be undone.'
        => 'Chạy “%s” ngay bây giờ? Thao tác xoá vĩnh viễn các mục khớp và không thể hoàn tác.',
    'Deleting is permanent. Revisions, autosaves and trashed content (posts, pages, products) are removed together with their metadata and attached files.'
        => 'Xoá là vĩnh viễn. Bản nháp, bản lưu tự động và nội dung trong thùng rác (bài viết, trang, sản phẩm) bị xoá cùng metadata và tệp đính kèm.',
    '"Comments containing links" matches links in the comment body only. The figure shown is the total comment count, not a match count, so confirm carefully.'
        => '“Bình luận chứa liên kết” chỉ khớp liên kết trong nội dung bình luận. Con số hiển thị là tổng số bình luận, không phải số khớp, nên hãy xác nhận cẩn thận.',
    'Removes attachments whose file is missing from disk. Every attachment is scanned, so the count is known only after it runs.'
        => 'Xoá các tệp đính kèm có file đã mất khỏi ổ đĩa. Mọi tệp đính kèm đều được quét, nên chỉ biết số lượng sau khi chạy.',
    'Removes metadata entries for thumbnail files that are missing from disk. It does not delete images.'
        => 'Xoá các mục metadata của ảnh thu nhỏ đã mất khỏi ổ đĩa. Nó không xoá ảnh.',
    'Use with care: your theme may need several image sizes to display correctly.'
        => 'Dùng thận trọng: giao diện của bạn có thể cần nhiều kích thước ảnh để hiển thị đúng.',
    'Cleanup runs on WordPress cron, which only fires when your site receives traffic. Weekly and monthly are measured from the last run.'
        => 'Việc dọn dẹp chạy bằng cron của WordPress, vốn chỉ kích hoạt khi website có lượt truy cập. Hằng tuần và hằng tháng được tính từ lần chạy gần nhất.',
    'Deleting comments by link pattern is intentionally excluded from automatic cleanup — it stays a manual action.'
        => 'Việc xoá bình luận theo mẫu liên kết được cố ý loại khỏi dọn dẹp tự động — nó vẫn là thao tác thủ công.',
    // Backup / export-import screen
    'BACKUP' => 'SAO LƯU',
    'Backup' => 'Sao lưu',
    'Added'   => 'Thêm',
    'Changed' => 'Thay đổi',
    'Removed' => 'Xoá',
    'Setting group' => 'Nhóm cài đặt',
    'These groups will change' => 'Các nhóm sẽ thay đổi',
    'Preview changes' => 'Xem trước thay đổi',
    'Apply import' => 'Áp dụng nhập',
    'Download .json' => 'Tải .json',
    'Choose a file' => 'Chọn tệp',
    'Undo the last import' => 'Hoàn tác lần nhập gần nhất',
    'Restore previous configuration' => 'Khôi phục cấu hình trước đó',
    'Paste an export here, or upload a .json file'
        => 'Dán dữ liệu xuất vào đây, hoặc tải lên tệp .json',
    'That is not a valid Horse Tools export file.'
        => 'Đây không phải tệp xuất hợp lệ của Horse Tools.',
    'The file contained no Horse Tools settings to import.'
        => 'Tệp không chứa cài đặt Horse Tools nào để nhập.',
    'Imported %d setting group(s). You can undo this below.'
        => 'Đã nhập %d nhóm cài đặt. Bạn có thể hoàn tác bên dưới.',
    'The previous configuration has been restored.'
        => 'Đã khôi phục cấu hình trước đó.',
    'There is no backup to restore.'
        => 'Không có bản sao lưu để khôi phục.',
    'This includes every setting group, whether or not its module is currently enabled. Uploaded font files are not included — they live on this site.'
        => 'Bao gồm mọi nhóm cài đặt, bất kể module đang bật hay tắt. Không bao gồm tệp phông chữ đã tải lên — chúng nằm trên site này.',
    'Applying overwrites these groups with the imported values. A one-click backup is kept so you can undo it.'
        => 'Việc áp dụng sẽ ghi đè các nhóm này bằng giá trị đã nhập. Một bản sao lưu được giữ lại để bạn hoàn tác bằng một cú nhấp.',
    'The configuration from before your most recent import is stored. Restoring reverts every group to that snapshot.'
        => 'Cấu hình trước lần nhập gần nhất được lưu lại. Khôi phục sẽ đưa mọi nhóm về đúng bản chụp đó.',
    // 404 log
    '404 log' => 'Nhật ký 404',
    'Record 404 hits' => 'Ghi lại lượt truy cập 404',
    'Requested URL' => 'URL được yêu cầu',
    'Hits' => 'Lượt',
    'Last seen' => 'Lần cuối',
    'Actions' => 'Hành động',
    'Redirect' => 'Chuyển hướng',
    'Ignore' => 'Bỏ qua',
    'No 404s recorded yet.' => 'Chưa ghi nhận lỗi 404 nào.',
    'Clear the whole log' => 'Xoá toàn bộ nhật ký',
    'Create a 301 redirect from this URL' => 'Tạo chuyển hướng 301 từ URL này',
    'Hide this URL from the log' => 'Ẩn URL này khỏi nhật ký',
    'Delete this log entry' => 'Xoá mục nhật ký này',
    'Log the dead URLs anonymous visitors actually hit, so you can turn the busy ones into redirects. Logged-in users, bots and asset requests are not recorded, and nothing leaves your site.'
        => 'Ghi lại các URL hỏng mà khách vãng lai thực sự truy cập, để bạn biến những URL nhiều lượt thành chuyển hướng. Không ghi người dùng đã đăng nhập, bot và các yêu cầu tài nguyên tĩnh, và không có gì rời khỏi site của bạn.',
    // Security tab rebuild
    'Limit login attempts' => 'Giới hạn số lần đăng nhập',
    'Lock out repeated failed logins' => 'Khoá khi đăng nhập sai nhiều lần',
    'After too many failed logins from the same address, block further attempts for a while. This is the real defence against password guessing.'
        => 'Sau quá nhiều lần đăng nhập sai từ cùng một địa chỉ, chặn tạm các lần thử tiếp theo. Đây là biện pháp thật sự chống dò mật khẩu.',
    'Attempts before lockout' => 'Số lần sai trước khi khoá',
    'Lockout length (minutes)' => 'Thời gian khoá (phút)',
    'Email me when an address is locked out' => 'Gửi email cho tôi khi một địa chỉ bị khoá',
    'A login was locked out on your site' => 'Có một địa chỉ đăng nhập bị khoá trên site của bạn',
    'The address %1$s was locked out after repeated failed logins on %2$s.'
        => 'Địa chỉ %1$s đã bị khoá sau nhiều lần đăng nhập sai trên %2$s.',
    'Too many failed attempts. Try again in about %d minute(s).'
        => 'Quá nhiều lần thử sai. Vui lòng thử lại sau khoảng %d phút.',
    'Login failed. Check your details and try again.'
        => 'Đăng nhập thất bại. Kiểm tra lại thông tin và thử lại.',
    'Block user enumeration' => 'Chặn dò tên người dùng',
    'Hide usernames from scanners' => 'Ẩn tên người dùng khỏi công cụ quét',
    'Blocks ?author=N scans, removes the users REST endpoint for anonymous requests, strips the author from oEmbed, and makes login errors generic so they do not reveal whether the username or the password was wrong.'
        => 'Chặn quét ?author=N, gỡ endpoint users REST với khách vãng lai, bỏ tác giả khỏi oEmbed, và làm lỗi đăng nhập chung chung để không lộ sai tên hay sai mật khẩu.',
    'Security response headers' => 'HTTP header bảo mật',
    'Send security headers' => 'Gửi các header bảo mật',
    'Add hardening headers to front-end responses. Each one below is optional.'
        => 'Thêm các header tăng cường bảo mật cho phản hồi front-end. Mỗi mục dưới đây là tuỳ chọn.',
    'X-Frame-Options: SAMEORIGIN (block clickjacking)' => 'X-Frame-Options: SAMEORIGIN (chặn clickjacking)',
    'X-Content-Type-Options: nosniff' => 'X-Content-Type-Options: nosniff',
    'Referrer-Policy: strict-origin-when-cross-origin' => 'Referrer-Policy: strict-origin-when-cross-origin',
    'Permissions-Policy: block geolocation, mic and camera' => 'Permissions-Policy: chặn định vị, mic và camera',
    'HSTS (force HTTPS for 180 days)' => 'HSTS (ép HTTPS trong 180 ngày)',
    'Only enable once HTTPS works everywhere. Browsers will refuse plain HTTP to your site for six months, and it cannot be undone quickly.'
        => 'Chỉ bật khi HTTPS đã chạy ở mọi nơi. Trình duyệt sẽ từ chối HTTP thường trong sáu tháng, và không thể huỷ nhanh.',
    'Content-Security-Policy (advanced, leave blank if unsure)' => 'Content-Security-Policy (nâng cao, để trống nếu không chắc)',
    'A wrong CSP silently breaks scripts, styles and images. Test with browser dev tools before relying on it.'
        => 'CSP sai sẽ âm thầm làm hỏng script, style và ảnh. Hãy kiểm tra bằng dev tools của trình duyệt trước khi dựa vào nó.',
    'Lock down the admin' => 'Khoá chặt khu quản trị',
    'Disable the theme & plugin file editor' => 'Tắt trình sửa file theme & plugin',
    'Removes the built-in code editor under Appearance and Plugins. If an attacker gets into wp-admin, they cannot use it to edit PHP files. You edit files over SFTP instead.'
        => 'Gỡ trình sửa code trong Appearance và Plugins. Nếu kẻ tấn công vào được wp-admin, chúng không thể dùng nó để sửa file PHP. Bạn sửa file qua SFTP.',
    'Disable unused endpoints' => 'Tắt các endpoint không dùng',
    'Disable REST API for anonymous visitors' => 'Tắt REST API cho khách vãng lai',
    'This blocks the REST API for logged-out visitors. It WILL break: WooCommerce cart and checkout for guests, Contact Form 7 and other REST-based forms, comment submission on block themes, and oEmbed. Only enable it if your site uses none of these.'
        => 'Chặn REST API cho khách chưa đăng nhập. Nó SẼ làm hỏng: giỏ hàng và thanh toán WooCommerce cho khách, Contact Form 7 và các form dùng REST, gửi bình luận trên block theme, và oEmbed. Chỉ bật nếu site của bạn không dùng những thứ này.',
    'Recommended. xmlrpc.php is a common brute-force and pingback-amplification target and almost nothing uses it now (except Jetpack).'
        => 'Khuyến nghị. xmlrpc.php là đích brute-force và khuếch đại pingback phổ biến, hầu như không còn gì dùng nó (trừ Jetpack).',
    'Removes wp-embed.js if you do not embed other WordPress posts.'
        => 'Gỡ wp-embed.js nếu bạn không nhúng bài viết WordPress khác.',
    'Removes the X-Pingback header. Pairs with disabling XML-RPC.'
        => 'Gỡ header X-Pingback. Đi kèm với việc tắt XML-RPC.',
    'Disable feeds (RSS/Atom)' => 'Tắt feed (RSS/Atom)',
    'Turns off the RSS and Atom feeds if your site does not publish one.'
        => 'Tắt feed RSS và Atom nếu site của bạn không xuất bản feed.',
    'Tidy up' => 'Dọn dẹp',
    'Remove unnecessary header tags' => 'Gỡ các thẻ header thừa',
    'Removes the RSD, WLW manifest and adjacent-post link tags from the page head.'
        => 'Gỡ các thẻ RSD, WLW manifest và link bài kề khỏi phần head.',
    'Remove the WordPress version tag' => 'Gỡ thẻ phiên bản WordPress',
    'Removes the generator meta tag. A small tidy-up — not a security measure on its own, since asset fingerprints reveal the version anyway.'
        => 'Gỡ thẻ meta generator. Chỉ là dọn dẹp nhỏ — không phải biện pháp bảo mật tự thân, vì dấu vết tài nguyên vẫn lộ phiên bản.',
    // Tool tab
    'Automatic updates' => 'Cập nhật tự động',
    'Do not auto-install core updates' => 'Không tự cài cập nhật lõi',
    'Do not auto-install language pack updates' => 'Không tự cài cập nhật gói ngôn ngữ',
    'Do not auto-install theme updates' => 'Không tự cài cập nhật theme',
    'Do not auto-install plugin updates' => 'Không tự cài cập nhật plugin',
    'Hide the update & maintenance notice' => 'Ẩn thông báo cập nhật & bảo trì',
    'These stop WordPress installing updates on its own — the site still checks for them, so the Dashboard and Plugins screens keep showing what is available and you apply updates when you choose. It never stops checking, so you are never left unaware of a security release.'
        => 'Những mục này ngăn WordPress tự cài cập nhật — site vẫn kiểm tra, nên trang Dashboard và Plugins vẫn hiện các bản có sẵn và bạn tự áp dụng khi muốn. Nó không bao giờ ngừng kiểm tra, nên bạn không bao giờ bị bỏ lỡ một bản vá bảo mật.',
    'Add an attribution line when visitors copy text' => 'Thêm dòng ghi nguồn khi khách copy nội dung',
    'When someone copies text from your site, a short line you set is added after it — their selection is kept, nothing is replaced. Useful for a "Source: yoursite.com" credit.'
        => 'Khi ai đó copy nội dung từ site của bạn, một dòng ngắn bạn đặt được thêm vào sau — phần chọn của họ vẫn giữ nguyên, không thay thế gì. Hữu ích cho dòng "Nguồn: site.com".',
    'Attribution line' => 'Dòng ghi nguồn',
    // Automatic redirects (slug change)
    'Automatic redirects' => 'Chuyển hướng tự động',
    'To' => 'Đến',
    'Clear all automatic redirects' => 'Xoá tất cả chuyển hướng tự động',
    'Create a 301 automatically when a post permalink changes' => 'Tự tạo 301 khi đường dẫn bài viết thay đổi',
    'No automatic redirects yet. Change a published URL and one will appear here.'
        => 'Chưa có chuyển hướng tự động nào. Đổi một URL đã đăng và nó sẽ xuất hiện ở đây.',
    'When you change a published post or page URL — its slug, its parent, or the whole path — the old address is redirected to the new one. WordPress already does this for a simple slug change; this also covers moves that core misses, and only ever acts on a URL that would otherwise 404.'
        => 'Khi bạn đổi URL của bài viết hoặc trang đã đăng — slug, chuyên mục cha, hay toàn bộ đường dẫn — địa chỉ cũ sẽ được chuyển hướng sang địa chỉ mới. WordPress đã làm điều này cho việc đổi slug đơn giản; tính năng này bắt thêm các trường hợp core bỏ sót, và chỉ tác động lên URL vốn sẽ trả về 404.',
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
