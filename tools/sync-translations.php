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
    // OPTIMIZE tab — defer JS + preconnect (added 1.2.14).
    'JavaScript &amp; connections'
        => 'JavaScript &amp; kết nối',
    'Defer JavaScript'
        => 'Hoãn tải JavaScript (defer)',
    'Add “defer” to front-end scripts so they no longer block the page from rendering; they run in order once the HTML is parsed. jQuery is never deferred (inline snippets depend on it). Big Core Web Vitals win.'
        => 'Thêm “defer” vào các script front-end để chúng không còn chặn hiển thị trang; chúng chạy theo thứ tự sau khi HTML được phân tích xong. jQuery không bao giờ bị hoãn (các đoạn inline phụ thuộc vào nó). Cải thiện Core Web Vitals rõ rệt.',
    'If a theme/plugin script misbehaves, add its handle or file name to the exclusion list below. Disable if you already use a full-page optimiser that defers scripts.'
        => 'Nếu một script của theme/plugin trục trặc, thêm handle hoặc tên file của nó vào danh sách loại trừ bên dưới. Tắt đi nếu bạn đã dùng plugin tối ưu toàn trang có chức năng hoãn script.',
    'Scripts to exclude from defer (one per line — a script handle or part of its URL)'
        => 'Script loại trừ khỏi defer (mỗi dòng một mục — handle hoặc một phần URL của script)',
    'Preconnect to third-party hosts'
        => 'Preconnect tới các host bên thứ ba',
    'Tell the browser to start the DNS + TCP + TLS handshake to external hosts early (fonts, CDN, analytics), so their files arrive sooner. Adds preconnect and dns-prefetch hints to the page head.'
        => 'Yêu cầu trình duyệt bắt đầu bắt tay DNS + TCP + TLS tới các host bên ngoài sớm (font, CDN, analytics) để file của chúng về nhanh hơn. Thêm gợi ý preconnect và dns-prefetch vào phần head của trang.',
    'Hosts to preconnect (one per line — host or full URL)'
        => 'Host cần preconnect (mỗi dòng một mục — host hoặc URL đầy đủ)',
    'Only add hosts the page really uses. Preconnecting to a host you do not load from wastes a connection.'
        => 'Chỉ thêm host mà trang thật sự dùng. Preconnect tới host không tải gì sẽ lãng phí một kết nối.',
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
    // Notify page "how to use" intros (1.2.x)
    'Spots visitors who have an ad-blocker turned on and shows them a message asking to switch it off. To use: turn on the switch, type a title and message, then pick the button colours below.'
        => 'Phát hiện khách đang bật trình chặn quảng cáo và hiện thông báo mời họ tắt đi. Cách dùng: bật công tắc, nhập tiêu đề và nội dung, rồi chọn màu nút bên dưới.',
    'Shows a thin notice bar across the very top of every page — handy for a promotion, a hotline, or a delivery note. To use: turn on the switch, type your message, then choose the background colour.'
        => 'Hiện một thanh thông báo mảnh chạy ngang trên cùng của mọi trang — tiện để báo khuyến mãi, hotline hay lịch giao hàng. Cách dùng: bật công tắc, nhập nội dung, rồi chọn màu nền.',
    'Shows an eye-catching popup in the middle of the screen when someone opens your site — great for a sale, an announcement or a poster. To use: turn on the switch, pick a layout below, add an image / title / content, then set how many hours before it shows again.'
        => 'Hiện một cửa sổ nổi bật giữa màn hình khi khách mở web — rất hợp để chạy khuyến mãi, thông báo hay treo poster. Cách dùng: bật công tắc, chọn kiểu bên dưới, thêm ảnh / tiêu đề / nội dung, rồi đặt số giờ trước khi nó hiện lại.',
    'Shows a small cookie notice in the corner so your site meets privacy rules. To use: turn on the switch, type the notice and your policy-page link, then choose which side it appears on.'
        => 'Hiện một thông báo cookie nhỏ ở góc màn hình để site tuân thủ quy định về quyền riêng tư. Cách dùng: bật công tắc, nhập nội dung và link trang chính sách, rồi chọn hiển thị ở góc nào.',
    // Popup upgrade (effect / position / trigger)
    'Entrance effect' => 'Hiệu ứng xuất hiện',
    'Where it appears' => 'Hiển thị ở đâu',
    'When it appears' => 'Khi nào hiện',
    'Seconds to wait, or scroll percent (used by the two options above)'
        => 'Số giây chờ, hoặc phần trăm cuộn (dùng cho hai lựa chọn trên)',
    'Fade in' => 'Mờ dần',
    'Zoom in' => 'Phóng to dần',
    'Slide up' => 'Trượt lên',
    'Bounce' => 'Nảy lên',
    'Centre of screen' => 'Giữa màn hình',
    'Corner — bottom right' => 'Góc dưới bên phải',
    'Corner — bottom left' => 'Góc dưới bên trái',
    'Bar across the bottom' => 'Thanh ngang dưới đáy',
    'As soon as the page opens' => 'Ngay khi mở trang',
    'After a few seconds' => 'Sau vài giây',
    'After scrolling down' => 'Sau khi cuộn xuống',
    'When about to leave the page' => 'Khi khách sắp rời trang',
    // More popup entrance effects + attention wiggle
    'Zoom out' => 'Thu nhỏ dần',
    'Pop' => 'Bật ra',
    'Slide down' => 'Trượt xuống',
    'Slide in from right' => 'Trượt vào từ phải',
    'Slide in from left' => 'Trượt vào từ trái',
    'Swing' => 'Đung đưa',
    'Rotate in' => 'Xoay vào',
    'Flip' => 'Lật',
    'Sharpen (blur to clear)' => 'Rõ dần (từ mờ sang nét)',
    'Wiggle now and then to catch the eye' => 'Thỉnh thoảng lắc nhẹ để gây chú ý',
    // Icon picker (Chat tab)
    'Choose an icon' => 'Chọn icon',
    'Choose icon' => 'Chọn icon',
    'Load more icons' => 'Tải thêm icon',
    'Search: cart, gift, phone… (English name, or Vietnamese for popular icons)'
        => 'Tìm: cart, gift, phone… (tên tiếng Anh, hoặc tiếng Việt cho icon thông dụng)',
    'Click an icon to drop it into the selected field. Search by its English name (cart, gift, phone…), or Vietnamese for popular icons. Over 5,000 in total — use “Load more” to see them all.'
        => 'Bấm một icon để chèn vào ô đang chọn. Tìm bằng tên tiếng Anh (cart, gift, phone…), hoặc tiếng Việt cho các icon thông dụng. Tổng cộng hơn 5.000 icon — bấm “Tải thêm” để xem hết.',
    'No icon matches that.' => 'Không tìm thấy icon phù hợp.',
    'Loading icons…' => 'Đang tải icon…',
    'Could not load the icon list.' => 'Không tải được danh sách icon.',
    // Snippet manager (Shortcode tab)
    'Advanced options — device, visitors, schedule, tags (optional)'
        => 'Tùy chọn nâng cao — thiết bị, khách, lịch hiện, thẻ (không bắt buộc)',
    'Search snippets by name, description or tag…' => 'Tìm snippet theo tên, mô tả hoặc thẻ…',
    'No snippet matches your search.' => 'Không có snippet nào khớp tìm kiếm.',
    // Login limiter: lockout unit + real-IP behind proxy
    'Lockout length' => 'Thời gian khóa',
    'Minute(s)' => 'Phút',
    'Hour(s)' => 'Giờ',
    'Day(s)' => 'Ngày',
    'Lockout time unit' => 'Đơn vị thời gian khóa',
    'Too many failed attempts. Try again in about %s.' => 'Sai quá nhiều lần. Thử lại sau khoảng %s.',
    'Site is behind Cloudflare or a proxy (use the real visitor IP)'
        => 'Site nằm sau Cloudflare hoặc proxy (dùng IP thật của khách)',
    'Reset all login lockouts now' => 'Gỡ tất cả khóa đăng nhập ngay',
    'Cleared all lockouts.' => 'Đã gỡ tất cả khóa.',
    'Locked yourself out? Log in from a different network (e.g. your phone on mobile data = a different IP), then click the button above to clear all lockouts — or just wait for the lockout time to pass. As a last resort, disable the plugin by renaming its folder via FTP.'
        => 'Lỡ tự khóa mình? Hãy đăng nhập từ một mạng khác (ví dụ dùng 4G điện thoại = IP khác), rồi bấm nút trên để gỡ tất cả khóa — hoặc chờ hết thời gian khóa. Cùng lắm, tắt plugin bằng cách đổi tên thư mục plugin qua FTP.',
    'By default the lockout counts the direct connection IP, which cannot be faked. Only turn this on if your site is reachable ONLY through Cloudflare or a proxy — it then reads the real visitor IP from the proxy header (CF-Connecting-IP / X-Forwarded-For). Do NOT enable it on a normally-hosted site: those headers can be forged, letting an attacker dodge the lockout or get an innocent visitor locked out.'
        => 'Mặc định, bộ khóa đếm theo IP kết nối trực tiếp — thứ không thể giả mạo. Chỉ bật mục này nếu site của bạn CHỈ truy cập được qua Cloudflare hoặc proxy — khi đó nó đọc IP thật của khách từ header của proxy (CF-Connecting-IP / X-Forwarded-For). ĐỪNG bật trên site host thông thường: các header đó có thể bị làm giả, khiến kẻ tấn công né được khóa hoặc làm khách vô tội bị khóa oan.',
    // Redirects page: clearer tab names + how-to intros
    'Redirects (301)' => 'Chuyển hướng (301)',
    'Broken links (404)' => 'Link hỏng (404)',
    'Maintenance (503)' => 'Bảo trì (503)',
    'When a URL changes or an old link should point somewhere new, this sends visitors (and Google) straight to the right page instead of a “not found” error — so you don’t lose traffic.'
        => 'Khi một URL đổi hoặc một link cũ cần trỏ tới chỗ mới, phần này đưa khách (và Google) thẳng tới đúng trang thay vì gặp lỗi “không tìm thấy” — để bạn không mất lượt truy cập.',
    'Handle dead links: send visitors who hit a “404 – not found” page to a page you choose, and keep a log of the broken URLs so you know what to fix.'
        => 'Xử lý link chết: đưa khách gặp trang “404 – không tìm thấy” sang trang bạn chọn, và ghi lại các URL hỏng để bạn biết mà sửa.',
    'Temporarily close the site with a “under maintenance” notice while you make changes. You (and other logged-in admins) still see the site normally, so you can work in peace.'
        => 'Tạm đóng website với thông báo “đang bảo trì” trong lúc bạn chỉnh sửa. Bạn (và các quản trị viên đã đăng nhập) vẫn xem site bình thường, nên cứ yên tâm làm việc.',
    'Write your block just like a post: type text and use the toolbar to insert links, images and formatting. Need raw HTML/CSS/JS? Switch to the “Text” tab. Placeholders like {{url}} still work in both.'
        => 'Soạn khối nội dung như khi viết bài: gõ chữ và dùng thanh công cụ để chèn link, ảnh và định dạng. Cần dán HTML/CSS/JS thô? Chuyển sang tab “Văn bản”. Các placeholder như {{url}} vẫn hoạt động ở cả hai.',
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
