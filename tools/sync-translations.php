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
    // SECURITY tab — two-factor authentication (added 1.2.19).
    'Two-factor authentication (2FA)' => 'Xác thực hai lớp (2FA)',
    'Enable two-factor authentication' => 'Bật xác thực hai lớp',
    'Each user turns it on for their own account under Users → Profile (scan a QR with Google Authenticator / Authy). After the password they enter a 6-digit code, so a stolen password alone is not enough. Uses standard WordPress hooks — safe across WordPress updates, and disabling the plugin instantly restores the normal login.'
        => 'Mỗi người tự bật cho tài khoản của mình ở Thành viên → Hồ sơ (quét mã QR bằng Google Authenticator / Authy). Sau mật khẩu phải nhập thêm mã 6 số, nên chỉ có mật khẩu là chưa đủ để vào. Dùng hook chuẩn của WordPress — an toàn qua các bản cập nhật WordPress, và tắt plugin là trả về đăng nhập bình thường ngay.',
    'Allow a recovery code by email' => 'Cho phép mã khôi phục qua email',
    'On the code screen the user can have a one-time code emailed to their account address if they lose their phone.'
        => 'Ở màn hình nhập mã, người dùng có thể yêu cầu gửi mã một lần vào email tài khoản nếu mất điện thoại.',
    'Allow a recovery code by Telegram' => 'Cho phép mã khôi phục qua Telegram',
    'Sends the one-time code to the Telegram bot and chat set in the WooCommerce module. Make sure that chat is private to you.'
        => 'Gửi mã một lần tới bot và chat Telegram đã cấu hình ở module WooCommerce. Hãy đảm bảo chat đó là riêng của bạn.',
    'Everyone also gets one-time backup codes when they enrol — save them. If you are ever locked out with no phone, backup code or recovery channel, rename the plugin folder over FTP to switch 2FA off.'
        => 'Mỗi người khi cài đều nhận được mã dự phòng dùng một lần — hãy lưu lại. Nếu bị khóa mà không có điện thoại, mã dự phòng hay kênh khôi phục, đổi tên thư mục plugin qua FTP để tắt 2FA.',
    'Status' => 'Trạng thái',
    'Two-factor authentication is ON for your account.' => 'Xác thực hai lớp đang BẬT cho tài khoản của bạn.',
    'Turn it off (uncheck stays on)' => 'Tắt đi (không tick thì vẫn bật)',
    'Save these one-time backup codes somewhere safe — each works once if you lose your phone:'
        => 'Lưu các mã dự phòng dùng một lần này ở nơi an toàn — mỗi mã dùng được một lần khi bạn mất điện thoại:',
    'Scan this with Google Authenticator, Authy or a similar app, then enter the 6-digit code it shows to turn 2FA on.'
        => 'Quét mã này bằng Google Authenticator, Authy hoặc app tương tự, rồi nhập mã 6 số nó hiện ra để bật 2FA.',
    'Or enter this key manually:' => 'Hoặc nhập khóa này thủ công:',
    '6-digit code' => 'Mã 6 số',
    '← enter code and press "Update profile" to enable' => '← nhập mã rồi bấm "Cập nhật hồ sơ" để bật',
    'Authentication code' => 'Mã xác thực',
    'Enter the 6-digit code from your app, or a backup code.' => 'Nhập mã 6 số từ app, hoặc một mã dự phòng.',
    'Lost your device?' => 'Mất thiết bị?',
    'Email me a code' => 'Gửi mã vào email tôi',
    'Send a Telegram code' => 'Gửi mã qua Telegram',
    'Trust this device for 30 days' => 'Tin thiết bị này trong 30 ngày',
    'Verify' => 'Xác thực',
    'Invalid code. Please try again.' => 'Mã không đúng. Vui lòng thử lại.',
    'A one-time code has been sent. Enter it above.' => 'Đã gửi mã một lần. Nhập mã đó ở trên.',
    'Could not send the recovery code — check the channel is configured.' => 'Không gửi được mã khôi phục — kiểm tra kênh đã được cấu hình chưa.',
    'Could not send the recovery code — message the bot first, then check your chat ID.'
        => 'Không gửi được mã khôi phục — hãy nhắn cho bot trước, rồi kiểm tra lại chat ID của bạn.',
    'Your one-time login code for %1$s is: %2$s (valid 10 minutes).' => 'Mã đăng nhập một lần cho %1$s là: %2$s (hiệu lực 10 phút).',
    'Your login recovery code' => 'Mã khôi phục đăng nhập của bạn',
    'This account uses two-factor authentication; XML-RPC password login is disabled.'
        => 'Tài khoản này dùng xác thực hai lớp; đăng nhập bằng mật khẩu qua XML-RPC đã bị tắt.',
    'This user has two-factor authentication ON.' => 'Người dùng này đang BẬT xác thực hai lớp.',
    'Detect my chat ID' => 'Dò chat ID của tôi',
    'Checking…' => 'Đang kiểm tra…',
    'Not allowed.' => 'Không được phép.',
    'No Telegram bot token is set yet (WooCommerce module).' => 'Chưa cấu hình token bot Telegram (module WooCommerce).',
    'Telegram returned no updates (the bot may use a webhook, or the token is wrong).'
        => 'Telegram không trả về cập nhật nào (bot có thể đang dùng webhook, hoặc token sai).',
    'No recent chats. Send your bot a message first, then try again.'
        => 'Không có chat gần đây. Hãy nhắn cho bot một tin trước rồi thử lại.',
    'Step 1 —' => 'Bước 1 —',
    'open this site’s Telegram bot and press Start:' => 'mở bot Telegram của site này và bấm Start:',
    'Message the site’s Telegram bot once (ask the site admin which bot if you’re not sure), then use the button below.'
        => 'Nhắn cho bot Telegram của site một lần (nếu không chắc là bot nào thì hỏi admin của site), rồi dùng nút bên dưới.',
    'Step 2 —' => 'Bước 2 —',
    'The code then reaches your own Telegram, not the admin. (You can also type the chat ID number in manually.)'
        => 'Mã khi đó về Telegram của chính bạn, không phải của admin. (Bạn cũng có thể tự gõ số chat ID vào.)',
    'If you lose your phone' => 'Nếu bạn mất điện thoại',
    'Besides your one-time backup codes, a fresh code can also be sent to you here:'
        => 'Ngoài các mã dự phòng dùng một lần, một mã mới cũng có thể được gửi cho bạn ở đây:',
    'By email — a one-time code is sent to your account address:'
        => 'Qua email — một mã dùng một lần được gửi tới địa chỉ email tài khoản của bạn:',
    'By Telegram' => 'Qua Telegram',
    'Telegram recovery is turned on but this site has no bot token yet, so no code can be sent. Add a bot token under %s, then reload this page.'
        => 'Đường lùi qua Telegram đang bật nhưng site chưa có token bot, nên chưa gửi được mã nào. Hãy thêm token bot ở %s, rồi tải lại trang này.',
    'Settings → WooCommerce → “Configure order notifications to be sent to Telegram”'
        => 'Cài đặt → WooCommerce → “Cấu hình thông báo đơn hàng gửi về Telegram”',
    'Telegram recovery isn’t fully set up on this site yet — ask the site admin, or just use email or a backup code instead.'
        => 'Đường lùi qua Telegram trên site này chưa được cấu hình đầy đủ — hãy hỏi admin của site, hoặc dùng email hay mã dự phòng thay thế.',
    'Telegram recovery is ON but this site has no bot token yet, so no recovery code can be sent. Add a bot token in the %1$s module (the “Configure order notifications to be sent to Telegram” section) — it is easy to miss because it lives there, not here — then each user pastes their own chat ID on their profile.'
        => 'Đường lùi qua Telegram đang BẬT nhưng site chưa có token bot, nên chưa gửi được mã khôi phục nào. Hãy thêm token bot trong module %1$s (mục “Cấu hình thông báo đơn hàng gửi về Telegram”) — chỗ này dễ bỏ sót vì nó nằm ở đó chứ không phải ở đây — rồi mỗi người dán chat ID của mình trong hồ sơ.',
    'WooCommerce' => 'WooCommerce',
    'Turn it off for this user (e.g. they lost their device)' => 'Tắt cho người dùng này (vd họ bị mất thiết bị)',
    'Your Telegram chat ID (for recovery codes)' => 'Chat ID Telegram của bạn (để nhận mã khôi phục)',
    'Message the site’s Telegram bot once, then paste YOUR own chat ID here (get it from @userinfobot) so recovery codes go to your Telegram, not the admin.'
        => 'Nhắn cho bot Telegram của site một lần, rồi dán CHAT ID của chính bạn vào đây (lấy từ @userinfobot) để mã khôi phục về Telegram của bạn, không phải của admin.',
    'Uses the site’s Telegram bot (configured in the WooCommerce module), but each user pastes their OWN chat ID on their profile — so a recovery code always reaches that user’s Telegram, never pooled to the admin. Users without a chat ID simply don’t see the Telegram option.'
        => 'Dùng bot Telegram của site (cấu hình ở module WooCommerce), nhưng mỗi người dán CHAT ID của chính mình trong hồ sơ — nên mã khôi phục luôn về Telegram của người đó, không dồn về admin. Ai chưa có chat ID thì không thấy tùy chọn Telegram.',
    // SECURITY tab — custom login question (added 1.2.18).
    'Security question on the login form'
        => 'Câu hỏi bảo mật trên trang đăng nhập',
    'Ask a custom question on the login form'
        => 'Hỏi một câu tùy chọn trên trang đăng nhập',
    'A no-Google, no-badge challenge that loads only on the wp-login.php page — zero effect on your front-end or its speed. Bots that hammer the login page cannot answer a site-specific question. It does not stop a person who reads the question, so pair it with the attempt limiter above.'
        => 'Một lớp thử thách không cần Google, không badge, chỉ tải trên trang wp-login.php — không ảnh hưởng gì tới front-end hay tốc độ. Bot dò trang đăng nhập không thể trả lời một câu hỏi riêng của site. Nó không chặn được người đọc thấy câu hỏi, nên hãy dùng kèm bộ giới hạn đăng nhập ở trên.',
    'Question'
        => 'Câu hỏi',
    'e.g. What is the shop mascot?'
        => 'vd: Linh vật của shop là con gì?',
    'Answer'
        => 'Đáp án',
    'e.g. fox'
        => 'vd: cáo',
    'The answer is matched ignoring case and surrounding spaces. Remember it — if you ever forget it you can still get back in by renaming the plugin folder over FTP to switch the plugin off.'
        => 'Đáp án so khớp không phân biệt hoa thường và bỏ khoảng trắng thừa. Hãy nhớ nó — lỡ quên thì vẫn vào lại được bằng cách đổi tên thư mục plugin qua FTP để tắt plugin.',
    'Wrong answer to the security question.'
        => 'Sai đáp án câu hỏi bảo mật.',
    // CONTENT tab — lightbox engine selector GLightbox/PhotoSwipe (added 1.2.16).
    'Click an image in your content to open it in a full-screen viewer. Choose the engine and how it looks below.'
        => 'Nhấn vào ảnh trong nội dung để mở trình xem toàn màn hình. Chọn engine và kiểu hiển thị bên dưới.',
    'Lightbox engine'
        => 'Engine lightbox',
    'GLightbox — light, images + video, slide effects'
        => 'GLightbox — nhẹ, ảnh + video, hiệu ứng chuyển ảnh',
    'PhotoSwipe — best pinch-zoom / pan for photos'
        => 'PhotoSwipe — zoom/kéo ảnh mượt nhất',
    'Both engines are free and open-source (MIT). GLightbox is the easy all-rounder (adds video + slide transitions); PhotoSwipe has the smoothest zoom/pan for photo galleries.'
        => 'Cả hai engine đều miễn phí, mã nguồn mở (MIT). GLightbox dễ dùng, đa năng (thêm video + hiệu ứng chuyển ảnh); PhotoSwipe có zoom/kéo mượt nhất cho gallery ảnh chụp.',
    'Slide transition (GLightbox)'
        => 'Hiệu ứng chuyển ảnh (GLightbox)',
    'Slide'
        => 'Trượt',
    'Both engines are bundled locally — no external requests. Gallery grouping, looping and the slide transition apply to GLightbox; PhotoSwipe always groups the content images (previous/next) and has its own smooth zoom/pan.'
        => 'Cả hai engine đều đóng gói sẵn trong plugin — không gọi ra ngoài. Gom thư viện, quay vòng và hiệu ứng chuyển ảnh áp dụng cho GLightbox; PhotoSwipe luôn gom các ảnh trong bài (trước/sau) và có zoom/kéo mượt riêng.',
    // CONTENT tab — image lightbox display options (added 1.2.15).
    'Image lightbox'
        => 'Lightbox ảnh',
    'Enable image lightbox'
        => 'Bật lightbox ảnh',
    'Where to run it'
        => 'Chạy ở đâu',
    'Posts only'
        => 'Chỉ bài viết',
    'Posts and pages'
        => 'Bài viết và trang',
    'All single content (incl. custom types)'
        => 'Mọi nội dung đơn (kể cả loại tùy chỉnh)',
    'Group images into a gallery (previous / next)'
        => 'Gom ảnh thành thư viện (trước / sau)',
    'Links every image in the content together so the viewer can flip through them. If an image already links to a file, that link is used.'
        => 'Liên kết mọi ảnh trong nội dung với nhau để có thể lật xem qua lại. Nếu ảnh đã có sẵn liên kết tới file, sẽ dùng liên kết đó.',
    'Caption from'
        => 'Chú thích lấy từ',
    'Image alt text'
        => 'Chữ alt của ảnh',
    'Image title'
        => 'Tiêu đề ảnh',
    'No caption'
        => 'Không chú thích',
    'Image caption (figcaption)'
        => 'Chú thích ảnh (figcaption)',
    'Backdrop theme'
        => 'Theme nền',
    'Dark'
        => 'Tối',
    'Blur (frosted glass)'
        => 'Mờ (kính mờ)',
    'Light'
        => 'Sáng',
    'Cinema (pure black)'
        => 'Rạp phim (đen tuyền)',
    'Accent colour (toolbar / active thumbnail)'
        => 'Màu nhấn (thanh công cụ / ảnh thu nhỏ đang chọn)',
    'Open animation'
        => 'Hiệu ứng mở',
    'Zoom'
        => 'Phóng to',
    'Fade'
        => 'Mờ dần',
    'None (instant)'
        => 'Không (tức thì)',
    'Toolbar'
        => 'Thanh công cụ',
    'Full (zoom, slideshow, fullscreen, download…)'
        => 'Đầy đủ (phóng to, trình chiếu, toàn màn hình, tải về…)',
    'Minimal (counter + close)'
        => 'Tối giản (đếm số + đóng)',
    'Show thumbnail strip'
        => 'Hiện dải ảnh thu nhỏ',
    'Loop back to the first image'
        => 'Quay vòng về ảnh đầu',
    // OPTIMIZE tab — heartbeat, native lazy-load, preload, dashicons (added 1.2.15).
    'Disable Dashicons for visitors'
        => 'Tắt Dashicons cho khách',
    'Removes the admin icon font (Dashicons) on the front-end for logged-out visitors, who never see it. It is kept for logged-in users because the admin bar uses it.'
        => 'Gỡ font icon quản trị (Dashicons) khỏi front-end cho khách chưa đăng nhập — họ không bao giờ thấy nó. Vẫn giữ cho người đã đăng nhập vì thanh admin cần dùng.',
    'Control the Heartbeat API'
        => 'Kiểm soát Heartbeat API',
    'WordPress “Heartbeat” pings the server every 15–60 seconds (autosave, post-lock, dashboard). Slowing or limiting it cuts admin-ajax.php load, especially with several admin tabs open.'
        => 'WordPress “Heartbeat” gửi tín hiệu tới server mỗi 15–60 giây (tự lưu, khóa bài, bảng điều khiển). Làm chậm hoặc giới hạn nó giúp giảm tải admin-ajax.php, nhất là khi mở nhiều tab quản trị.',
    'Heartbeat mode'
        => 'Chế độ Heartbeat',
    'Slow down to 60 seconds (safe)'
        => 'Làm chậm còn 60 giây (an toàn)',
    'Disable on the front-end, slow in admin'
        => 'Tắt ở front-end, làm chậm trong admin',
    'Only in the post editor (autosave/locking)'
        => 'Chỉ trong trình soạn bài (tự lưu/khóa bài)',
    'Preload critical assets'
        => 'Preload tài nguyên quan trọng',
    'Start fetching a few important files immediately (the LCP image, a web font, the main CSS). The type is detected from the file extension. Do not preload many files — it competes with everything else for bandwidth.'
        => 'Bắt đầu tải ngay vài file quan trọng (ảnh LCP, web font, CSS chính). Loại tài nguyên được nhận dạng theo đuôi file. Đừng preload quá nhiều — nó tranh băng thông với mọi thứ khác.',
    'Asset URLs to preload (one per line — .woff2/.css/.js/.jpg/.webp…)'
        => 'URL tài nguyên cần preload (mỗi dòng một mục — .woff2/.css/.js/.jpg/.webp…)',
    'Native image lazy-load + async decode'
        => 'Lazy-load ảnh native + giải mã bất đồng bộ',
    'Adds decoding="async" to images so the browser decodes them off the main thread, and relies on WordPress’ built-in native lazy-load (which correctly keeps the first/LCP image eager). Replaces the old script-based method that removed image src — that hurt SEO and broke images with JavaScript off.'
        => 'Thêm decoding="async" vào ảnh để trình duyệt giải mã ngoài luồng chính, và dựa vào lazy-load native sẵn có của WordPress (giữ đúng ảnh đầu/LCP tải ngay). Thay cho phương pháp cũ dùng script xóa src ảnh — vốn hại SEO và làm hỏng ảnh khi tắt JavaScript.',
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
    // Cookie notice upgrade (Notify → COOKIE)
    'Decline' => 'Từ chối',
    'Buttons' => 'Nút bấm',
    'Accept button text (default: Agree)' => 'Chữ nút Đồng ý (mặc định: Đồng ý)',
    'Policy link text (default: Policy)' => 'Chữ nút Chính sách (mặc định: Chính sách)',
    'Show a “Decline” button' => 'Hiện nút “Từ chối”',
    'Decline button text (default: Decline)' => 'Chữ nút Từ chối (mặc định: Từ chối)',
    'Choose the position (corner box or full-width bar) and customise the message and button labels. The Accept/Decline choice is stored in the browser (localStorage + an “ht_cookie_consent” cookie), so the notice won’t nag returning visitors. Note: this is an informational notice — Horse Tools does not itself block third-party tracking scripts, so for strict consent-gating you would still gate your own scripts on that cookie.'
        => 'Chọn vị trí (hộp góc hoặc thanh ngang full-width) và tùy chỉnh nội dung cùng chữ trên nút. Lựa chọn Đồng ý/Từ chối được lưu trong trình duyệt (localStorage + cookie “ht_cookie_consent”), nên thông báo không làm phiền khách quay lại. Lưu ý: đây là thông báo mang tính thông tin — Horse Tools không tự chặn các script theo dõi của bên thứ ba, nên nếu muốn chặn chặt chẽ bạn vẫn cần tự điều kiện hóa script của mình dựa trên cookie đó.',
    // OPTIMIZE — Delay JavaScript
    'Delay JavaScript until interaction' => 'Hoãn JavaScript đến khi tương tác',
    'Delay JavaScript execution' => 'Hoãn chạy JavaScript',
    'Hold heavy third-party scripts (analytics, tag managers, pixels, chat, ads) until the visitor first interacts — scroll, mouse move, tap, key or click — then run them in order. The single biggest win for Total Blocking Time and “Reduce unused JavaScript”. It re-fires the page-ready events afterwards, keeps script order, and never touches JSON-LD structured data or ES modules.'
        => 'Giữ lại các script nặng của bên thứ ba (analytics, tag manager, pixel, chat, quảng cáo) cho tới khi khách tương tác lần đầu — cuộn, di chuột, chạm, gõ phím hay bấm — rồi chạy chúng theo đúng thứ tự. Đây là mức tối ưu mạnh nhất cho Total Blocking Time và “Reduce unused JavaScript”. Sau đó nó tự kích hoạt lại các sự kiện page-ready, giữ đúng thứ tự script, và không đụng tới dữ liệu JSON-LD hay ES module.',
    'Test the site after turning this on. If something that must work before interaction breaks (a hero slider, a cookie bar), add its handle or file name to a list below. Do not use together with “Delay JS” in another optimiser.'
        => 'Hãy kiểm tra site sau khi bật. Nếu có thứ cần chạy trước khi tương tác bị lỗi (slider đầu trang, thanh cookie), hãy thêm handle hoặc tên file của nó vào danh sách bên dưới. Đừng dùng chung với “Delay JS” của một plugin tối ưu khác.',
    'Mode' => 'Chế độ',
    'Delay only the scripts I list (recommended, safe)' => 'Chỉ hoãn các script tôi liệt kê (khuyên dùng, an toàn)',
    'Delay all scripts except the exclusions (most aggressive)' => 'Hoãn mọi script trừ danh sách loại trừ (mạnh nhất)',
    '“Listed” mode — scripts to delay (one per line: a handle, file name or part of a URL). Leave empty to use the built-in list of common trackers.'
        => 'Chế độ “Liệt kê” — các script cần hoãn (mỗi dòng một: handle, tên file hoặc một phần URL). Để trống sẽ dùng danh sách tracker phổ biến có sẵn.',
    '“All” mode — scripts to NEVER delay (one per line). A hero slider or a cookie-consent script usually belongs here.'
        => 'Chế độ “Tất cả” — các script KHÔNG BAO GIỜ hoãn (mỗi dòng một). Slider đầu trang hay script đồng ý cookie thường nằm ở đây.',
    'Fall-back timer — run the delayed scripts after this many seconds even with no interaction (0 = only on interaction)'
        => 'Bộ đếm dự phòng — chạy các script bị hoãn sau bấy nhiêu giây kể cả khi không có tương tác (0 = chỉ khi tương tác)',
    'Tip: to stop one specific script from ever being delayed, add the attribute data-ht-no-delay to its tag. Logged-in users are never affected.'
        => 'Mẹo: để một script cụ thể không bao giờ bị hoãn, thêm thuộc tính data-ht-no-delay vào thẻ của nó. Người dùng đã đăng nhập không bao giờ bị ảnh hưởng.',
    // OPTIMIZE — script scanner
    'Scan the scripts running on the home page' => 'Quét các script đang chạy trên trang chủ',
    'Scanning…' => 'Đang quét…',
    'No scripts found.' => 'Không tìm thấy script nào.',
    '%d scripts found — “+ Delay” holds one back, “+ Exclude” keeps it running immediately:'
        => 'Tìm thấy %d script — “+ Hoãn” để giữ lại, “+ Loại trừ” để cho chạy ngay:',
    'delayed' => 'đang hoãn',
    'runs now' => 'chạy ngay',
    '+ Delay' => '+ Hoãn',
    '+ Exclude' => '+ Loại trừ',
    'added ✓' => 'đã thêm ✓',
    'Scan failed.' => 'Quét thất bại.',
    'Could not load the home page from the server:' => 'Không tải được trang chủ từ máy chủ:',
    'Could not read the home page (HTTP %s). Your host may block the site from calling itself; check the scripts via “View source” instead.'
        => 'Không đọc được trang chủ (HTTP %s). Host của bạn có thể chặn site tự gọi chính nó; hãy kiểm tra script bằng “Xem nguồn trang” thay thế.',
    // OPTIMIZE — Async CSS
    'Load CSS without blocking render' => 'Nạp CSS không chặn hiển thị',
    'Async CSS — remove render-blocking stylesheets' => 'Async CSS — gỡ CSS chặn hiển thị',
    'Loads stylesheets without blocking the first paint (the media-toggle technique), so the page appears on screen sooner. A big win for First Contentful Paint and Lighthouse’s “Eliminate render-blocking resources”.'
        => 'Nạp CSS mà không chặn lần vẽ đầu tiên (kỹ thuật đổi media), nên trang hiện lên màn hình sớm hơn. Cải thiện lớn cho First Contentful Paint và mục “Eliminate render-blocking resources” của Lighthouse.',
    'The most powerful but riskiest speed option. Without the Critical CSS below, the page can flash unstyled for a moment (FOUC). Paste your above-the-fold CSS, or keep the main theme stylesheet in the exclusion list. Test the front end carefully, and don’t combine it with another plugin that also optimises CSS delivery.'
        => 'Tùy chọn tốc độ mạnh nhất nhưng rủi ro nhất. Nếu không có Critical CSS bên dưới, trang có thể nháy mất định dạng một lúc (FOUC). Hãy dán CSS phần trên màn hình, hoặc để CSS chính của theme trong danh sách loại trừ. Kiểm tra front-end kỹ, và đừng dùng chung với plugin khác cũng tối ưu cách nạp CSS.',
    'Critical CSS — the above-the-fold styles, inlined in the head to prevent the flash (optional, but strongly recommended)'
        => 'Critical CSS — phần định dạng trên màn hình, chèn thẳng vào head để tránh nháy (không bắt buộc nhưng rất nên có)',
    'Stylesheets to keep render-blocking (one per line — a handle or part of the URL). Put your main theme CSS here if you don’t have critical CSS yet.'
        => 'CSS cần giữ chặn-hiển-thị (mỗi dòng một — handle hoặc một phần URL). Nếu chưa có critical CSS thì để CSS chính của theme vào đây.',
    'Only affects enqueued stylesheets, and only for logged-out visitors. A fallback keeps every stylesheet working when JavaScript is turned off.'
        => 'Chỉ tác động đến các CSS được enqueue, và chỉ với khách chưa đăng nhập. Có bản dự phòng để mọi CSS vẫn hoạt động khi tắt JavaScript.',
    // Presets (one-click) — new speed baseline
    'One click for the safe, high-impact speed features — including delaying third-party scripts and deferring JavaScript. It deliberately leaves out the advanced, riskier options (async CSS, the aggressive “delay all” mode), which you can still turn on yourself later. Gutenberg and Classic CSS are left alone, since removing those can change how a theme looks.'
        => 'Một cú bấm cho các tính năng tốc độ an toàn, hiệu quả cao — gồm cả hoãn script bên thứ ba và defer JavaScript. Nó cố ý bỏ qua các tùy chọn nâng cao, rủi ro hơn (async CSS, chế độ “hoãn tất cả”) — bạn có thể tự bật sau. Gutenberg và Classic CSS được giữ nguyên vì gỡ chúng có thể làm đổi giao diện theme.',
    'Delay third-party scripts (analytics, ads, chat) until the visitor interacts — safe built-in list, with a 5-second fall-back'
        => 'Hoãn script bên thứ ba (analytics, quảng cáo, chat) tới khi khách tương tác — danh sách an toàn có sẵn, kèm timer dự phòng 5 giây',
    'Defer JavaScript so it no longer blocks the page' => 'Defer JavaScript để không còn chặn hiển thị trang',
    'Drop Emoji, jQuery Migrate and admin icons; calm the Heartbeat' => 'Tắt Emoji, jQuery Migrate và icon admin; làm dịu Heartbeat',
    'HTML compression; compress JPGs and serve WebP' => 'Nén HTML; nén JPG và phục vụ WebP',
    'Delay analytics/ads/chat until interaction, and defer JavaScript' => 'Hoãn analytics/quảng cáo/chat tới khi tương tác, và defer JavaScript',
    'Delay analytics/ads/chat until interaction, and defer JavaScript (cart & checkout untouched)'
        => 'Hoãn analytics/quảng cáo/chat tới khi tương tác, và defer JavaScript (giỏ hàng & thanh toán không bị đụng)',
    // OPTIMIZE — one-click quick-optimise button
    'One-click safe speed setup' => 'Tối ưu tốc độ an toàn 1-chạm',
    'For non-technical users: this switches on the safe, high-impact speed features — delay & defer JavaScript (safe mode, with a 5-second fall-back), lazy-load, Instant-page, HTML compression, and dropping Emoji / jQuery Migrate / Dashicons. The riskier options (async CSS, “delay all”) are left off. The switches below light up so you can see what changed — then press SAVE to apply.'
        => 'Dành cho người không rành kỹ thuật: nút này bật sẵn các tính năng tốc độ an toàn, hiệu quả cao — hoãn & defer JavaScript (chế độ an toàn, kèm timer dự phòng 5 giây), lazy-load, Instant-page, nén HTML, và tắt Emoji / jQuery Migrate / Dashicons. Các tùy chọn rủi ro hơn (async CSS, “hoãn tất cả”) được để tắt. Các công tắc bên dưới sẽ sáng lên để bạn thấy đã đổi gì — rồi bấm LƯU để áp dụng.',
    '✓ Turned on — press SAVE to apply' => '✓ Đã bật — bấm LƯU để áp dụng',
    // Editor quick-insert button for snippets
    'Shortcode' => 'Shortcode',
    'Insert a snippet' => 'Chèn một snippet',
    'No snippets yet — create them on the Shortcode screen.' => 'Chưa có snippet nào — tạo ở trang Shortcode.',
    'Select a snippet' => 'Chọn một snippet',
    'Pick a snippet to insert' => 'Chọn một snippet để chèn',
    'Horse Tools snippet' => 'Snippet Horse Tools',
    // Table builder
    'Table' => 'Bảng',
    'Insert a table' => 'Chèn một bảng',
    'Type it in' => 'Nhập tay',
    'Paste from Excel' => 'Dán từ Excel',
    'Upload a file' => 'Tải file lên',
    'Rows' => 'Số hàng',
    'Columns' => 'Số cột',
    'Build grid' => 'Tạo lưới',
    'Copy cells in Excel / Google Sheets (or paste CSV) and paste here:'
        => 'Sao chép các ô trong Excel / Google Sheets (hoặc dán CSV) rồi dán vào đây:',
    'Choose a .xlsx, .xls or .csv file:' => 'Chọn file .xlsx, .xls hoặc .csv:',
    'First row is a header' => 'Dòng đầu là tiêu đề',
    'Striped rows' => 'Hàng kẻ sọc',
    'Compact' => 'Gọn',
    'Stack into cards on mobile' => 'Xếp thành thẻ trên điện thoại',
    'Preview' => 'Xem trước',
    'Cancel' => 'Hủy',
    'Insert table' => 'Chèn bảng',
    'Nothing to preview yet.' => 'Chưa có gì để xem trước.',
    'Add some data first.' => 'Hãy nhập dữ liệu trước.',
    'Column' => 'Cột',
    'Reading…' => 'Đang đọc…',
    'rows' => 'hàng',
    'Could not read the file.' => 'Không đọc được file.',
    'Could not load the Excel reader — save the file as CSV and try again.'
        => 'Không tải được bộ đọc Excel — hãy lưu file dạng CSV rồi thử lại.',
    'Horse Tools table' => 'Bảng Horse Tools',
    'No table yet.' => 'Chưa có bảng.',
    'Table ready — click to edit.' => 'Đã có bảng — bấm để sửa.',
    'Create table' => 'Tạo bảng',
    'Edit table' => 'Sửa bảng',
    'Style' => 'Kiểu',
    'Default' => 'Mặc định',
    'Bordered' => 'Kẻ viền',
    'Minimal' => 'Tối giản',
    'Lines only' => 'Chỉ kẻ ngang',
    'Header colour' => 'Màu tiêu đề',
    'Grey' => 'Xám',
    'Blue' => 'Xanh dương',
    'Green' => 'Xanh lá',
    'Orange' => 'Cam',
    'Purple' => 'Tím',
    'Dark' => 'Đậm',
    'Caption' => 'Chú thích',
    'optional title above the table' => 'tiêu đề tùy chọn phía trên bảng',
    // Stored / reusable tables (Phase 1)
    'Save a table' => 'Lưu bảng',
    'Save table' => 'Lưu bảng',
    'Table name' => 'Tên bảng',
    'e.g. Price list' => 'vd: Bảng giá',
    'Saved tables' => 'Bảng đã lưu',
    'Insert a table you saved earlier:' => 'Chèn bảng bạn đã lưu trước đó:',
    'No saved tables yet.' => 'Chưa có bảng nào được lưu.',
    'Tables' => 'Bảng',
    'Add new table' => 'Thêm bảng mới',
    'Name' => 'Tên',
    'Size' => 'Kích thước',
    'Actions' => 'Thao tác',
    'Edit' => 'Sửa',
    'Duplicate' => 'Nhân bản',
    'Delete' => 'Xóa',
    'Build a table once here, then insert it into any post or page with its shortcode. Edit it here and every place that uses it updates automatically.' => 'Tạo bảng một lần tại đây, rồi chèn vào bất kỳ bài viết hay trang nào bằng shortcode. Sửa tại đây thì mọi nơi dùng bảng đều tự cập nhật.',
    'No tables yet. Click “Add new table” to create your first one.' => 'Chưa có bảng nào. Bấm “Thêm bảng mới” để tạo bảng đầu tiên.',
    'Delete this table? Any post using it will lose the table.' => 'Xóa bảng này? Mọi bài viết đang dùng sẽ mất bảng.',
    'Something went wrong. Please try again.' => 'Có lỗi xảy ra. Vui lòng thử lại.',
    'Table %d' => 'Bảng %d',
    'copy' => 'bản sao',
    // PHP snippets (1.2.59)
    'Run this snippet as PHP' => 'Chạy snippet này dưới dạng PHP',
    'PHP runs with full access to your site, so it stays locked. Enter a current code from your authenticator to unlock it for 15 minutes.' => 'PHP có toàn quyền trên site nên luôn ở trạng thái khóa. Nhập mã hiện tại từ ứng dụng xác thực để mở khóa trong 15 phút.',
    '6-digit code' => 'Mã 6 số',
    'Unlock' => 'Mở khóa',
    'Write plain PHP — no opening tag needed. The code is checked for syntax errors before it is saved, and if it ever crashes a page Horse Tools switches it off automatically.' => 'Viết PHP thuần, không cần thẻ mở đầu. Code được kiểm tra lỗi cú pháp trước khi lưu, và nếu làm sập trang thì Horse Tools sẽ tự tắt nó.',
    'Where it runs' => 'Chạy ở đâu',
    'Side of the site' => 'Phía nào của site',
    'Only where I place its shortcode' => 'Chỉ nơi tôi chèn shortcode',
    'Every page load (init)' => 'Mọi lượt tải trang (init)',
    'In the page <head>' => 'Trong <head> của trang',
    'Before </body>' => 'Trước </body>',
    'Above the post content' => 'Phía trên nội dung bài',
    'Below the post content' => 'Phía dưới nội dung bài',
    'Front end only' => 'Chỉ ngoài trang web',
    'Admin only' => 'Chỉ trong quản trị',
    'Front end and admin' => 'Cả ngoài trang lẫn quản trị',
    'PHP editing unlocked for %d minutes.' => 'Đã mở khóa sửa PHP trong %d phút.',
    'Only a full administrator may use PHP snippets.' => 'Chỉ quản trị viên đầy đủ mới được dùng snippet PHP.',
    'To use PHP snippets, switch on two-factor authentication for your own account first (Users → Profile).' => 'Muốn dùng snippet PHP, hãy bật xác thực hai lớp cho chính tài khoản của bạn trước (Tài khoản → Hồ sơ).',
    'To use PHP snippets, switch on two-factor authentication first (Horse Tools → Overview → Security).' => 'Muốn dùng snippet PHP, hãy bật xác thực hai lớp trước (Horse Tools → Tổng quan → Bảo mật).',
    'Your own account must have two-factor authentication switched on before you can use PHP snippets.' => 'Tài khoản của bạn phải bật xác thực hai lớp thì mới dùng được snippet PHP.',
    'Enter a current two-factor code to unlock PHP editing first.' => 'Hãy nhập mã xác thực hai lớp hiện tại để mở khóa sửa PHP trước đã.',
    'Enter your authentication code.' => 'Hãy nhập mã xác thực của bạn.',
    'That code is not right.' => 'Mã không đúng.',
    'PHP syntax error — nothing was saved: %s' => 'Lỗi cú pháp PHP — chưa lưu gì cả: %s',
    'PHP snippets are not available.' => 'Snippet PHP không khả dụng.',
    'You are not allowed to use PHP snippets.' => 'Bạn không được phép dùng snippet PHP.',
    'A PHP snippet changed on your site' => 'Có snippet PHP vừa thay đổi trên site của bạn',
    // Reader tools for tables (1.2.57)
    'Row-number column' => 'Cột số thứ tự',
    'Filter per column' => 'Lọc theo từng cột',
    'Copy / CSV / Print buttons' => 'Nút Sao chép / CSV / In',
    'Freeze first column' => 'Ghim cột đầu khi cuộn',
    'Copy' => 'Sao chép',
    'Copied' => 'Đã sao chép',
    'Print' => 'In',
    'Show columns' => 'Hiện cột',
    'All' => 'Tất cả',
    // Global feature search (1.2.52)
    'Search all plugin features…' => 'Tìm mọi tính năng của plugin…',
    // Richer table themes & colours (1.2.51)
    'Card (shadow)' => 'Nổi khối (đổ bóng)',
    'Dark background' => 'Nền tối',
    'Soft pastel' => 'Pastel dịu',
    'Red' => 'Đỏ',
    'Pink' => 'Hồng',
    'Teal' => 'Xanh ngọc',
    'Indigo' => 'Chàm',
    'Gradient blue-violet' => 'Chuyển sắc xanh–tím',
    'Gradient sunset' => 'Chuyển sắc hoàng hôn',
    'Gradient ocean' => 'Chuyển sắc biển',
    // Pinned total row (1.2.50)
    'Last row is a total row (pinned to the bottom)' => 'Hàng cuối là hàng tổng (ghim dưới đáy)',
    // Export / merge / formulas / per-table CSS (1.2.49)
    'Export CSV' => 'Xuất CSV',
    'Custom CSS' => 'CSS riêng',
    'Merge cells: type #colspan# to merge into the cell on the left, #rowspan# to merge into the cell above. Formulas: =SUM(B2:B10), also AVG / MIN / MAX.' => 'Gộp ô: gõ #colspan# để gộp vào ô bên trái, #rowspan# để gộp vào ô phía trên. Công thức: =SUM(B2:B10), có cả AVG / MIN / MAX.',
    // Google Sheet sync (1.2.48)
    'Google Sheet' => 'Google Sheet',
    'Paste a Google Sheets link (shared: anyone with the link)' => 'Dán link Google Sheets (chia sẻ: ai có link đều xem được)',
    'Pull data' => 'Kéo dữ liệu',
    'The sheet must be shared as “Anyone with the link can view”. With auto-refresh on, the table updates itself from the sheet.' => 'Sheet phải được chia sẻ “Ai có đường liên kết đều có thể xem”. Bật tự làm mới thì bảng sẽ tự cập nhật theo Sheet.',
    'No auto-refresh' => 'Không tự làm mới',
    'Refresh hourly' => 'Tự làm mới mỗi giờ',
    'Refresh daily' => 'Tự làm mới hàng ngày',
    'rows loaded' => 'dòng đã tải',
    'That does not look like a Google Sheets link.' => 'Đây không phải link Google Sheets.',
    'Google refused the request — make sure the sheet is shared as “Anyone with the link can view”.' => 'Google từ chối yêu cầu — hãy chắc chắn Sheet được chia sẻ “Ai có đường liên kết đều có thể xem”.',
    'The sheet is too large (over 1 MB of CSV).' => 'Sheet quá lớn (CSV vượt 1 MB).',
    'The sheet appears to be empty.' => 'Sheet có vẻ đang trống.',
    'Sync from Sheet' => 'Đồng bộ từ Sheet',
    'Syncing…' => 'Đang đồng bộ…',
    'Synced' => 'Đã đồng bộ',
    // Spreadsheet-style grid editor (1.2.47)
    'Insert row below' => 'Chèn hàng bên dưới',
    'Delete row' => 'Xóa hàng',
    'Move row up' => 'Chuyển hàng lên',
    'Move row down' => 'Chuyển hàng xuống',
    'Insert column right' => 'Chèn cột bên phải',
    'Delete column' => 'Xóa cột',
    'Move column left' => 'Chuyển cột sang trái',
    'Move column right' => 'Chuyển cột sang phải',
    'Click again to delete' => 'Bấm lần nữa để xóa',
    // Table fx: sort/search/pagination (1.2.46)
    'Sortable columns' => 'Cột bấm để sắp xếp',
    'Search box' => 'Ô tìm kiếm',
    'Pagination' => 'Phân trang',
    'Rows/page' => 'Dòng/trang',
    'Sorting, search and pagination appear on the published page.' => 'Sắp xếp, tìm kiếm và phân trang sẽ hiển thị trên trang đã đăng (không hiện trong xem trước).',
    'Search the table…' => 'Tìm trong bảng…',
    'No matching rows.' => 'Không có dòng nào khớp.',
    'Previous' => 'Trước',
    'Next' => 'Sau',
    // GitHub self-update (1.2.43)
    'Check for updates' => 'Kiểm tra cập nhật',
    'Horse Tools could not reach GitHub to check for updates. Please try again later.' => 'Horse Tools không kết nối được GitHub để kiểm tra cập nhật. Vui lòng thử lại sau.',
    'Horse Tools %s is available — an update link now appears under the plugin below. WordPress will download it directly from GitHub.' => 'Đã có Horse Tools %s — liên kết cập nhật hiện ngay dưới plugin bên dưới. WordPress sẽ tải trực tiếp từ GitHub.',
    'Horse Tools is up to date (version %s).' => 'Horse Tools đang ở bản mới nhất (phiên bản %s).',
    'All-in-one WordPress toolkit: contact chat, shortcodes, security & privacy, media optimisation, SEO, cleanup and more — in one plugin.' => 'Bộ công cụ WordPress tất-cả-trong-một: nút chat liên hệ, shortcode, bảo mật & quyền riêng tư, tối ưu media, SEO, dọn dẹp và hơn thế — trong một plugin.',
    'See the GitHub release page for details.' => 'Xem trang phát hành trên GitHub để biết chi tiết.',
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
