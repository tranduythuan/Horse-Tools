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
    // Automatic FAQ schema (1.2.61)
    'FAQ schema (rich results)' => 'FAQ schema (kết quả nổi bật)',
    'Publish FAQ schema automatically from the FAQ section in a post' => 'Tự động gắn FAQ schema từ mục hỏi đáp có sẵn trong bài',
    'Reads the “frequently asked questions” part your posts already have — a heading that names the section, then one heading per question with its answer below — and publishes the matching JSON-LD so Google can show them as FAQ rich results. Nothing in your posts is changed. If another SEO plugin already publishes FAQ schema for a post, Horse Tools stays out of the way.' => 'Đọc phần “câu hỏi thường gặp” bài của bạn đã có sẵn — một tiêu đề đặt tên cho mục, rồi mỗi câu hỏi là một tiêu đề kèm câu trả lời bên dưới — rồi gắn JSON-LD tương ứng để Google hiển thị dạng kết quả nổi bật. Không sửa gì trong bài viết. Nếu một plugin SEO khác đã gắn FAQ schema cho bài đó thì Horse Tools tự nhường.',
    'Apply to Pages as well, not just Posts' => 'Áp dụng cho cả Trang, không chỉ Bài viết',
    'Phrases that mark the FAQ section' => 'Các cụm từ nhận diện mục hỏi đáp',
    'Comma separated. A heading containing any of these opens the FAQ section; every heading below it until the next heading of the same level is treated as a question.' => 'Ngăn cách bằng dấu phẩy. Tiêu đề nào chứa một trong các cụm này sẽ mở mục hỏi đáp; mọi tiêu đề nhỏ hơn bên dưới, cho tới tiêu đề cùng cấp kế tiếp, được coi là câu hỏi.',
    'Only in these category IDs' => 'Chỉ áp dụng cho các ID chuyên mục',
    'leave empty for all categories' => 'để trống nghĩa là mọi chuyên mục',
    'Minimum number of questions' => 'Số câu hỏi tối thiểu',
    'A post with fewer questions than this gets no schema. Leave empty to use 2, which is the safer choice.' => 'Bài có ít câu hỏi hơn mức này sẽ không được gắn schema. Để trống thì dùng mức 2 — an toàn hơn.',
    'Longest answer to publish (characters)' => 'Câu trả lời dài nhất được gắn (ký tự)',
    'A longer answer is shortened in the schema only — your post is never touched. Leave empty to use 500.' => 'Câu trả lời dài hơn sẽ được rút gọn CHỈ trong schema — bài viết của bạn không hề bị đụng tới. Để trống thì dùng mức 500.',
    'Scan the whole site' => 'Quét toàn bộ site',
    'Clear cache & re-read' => 'Xóa cache & đọc lại',
    'Scanning…' => 'Đang quét…',
    '%1$d post(s) will publish FAQ schema (%2$s questions on average).' => '%1$d bài sẽ được gắn FAQ schema (trung bình %2$s câu hỏi).',
    '%d post(s) have a FAQ section but too few questions:' => '%d bài có mục hỏi đáp nhưng quá ít câu hỏi:',
    '%d post(s) mention a FAQ but no question could be read — unusual structure:' => '%d bài có nhắc tới hỏi đáp nhưng không đọc được câu hỏi nào — cấu trúc lạ:',
    '%d post(s) already get FAQ schema from another plugin, so they are left alone.' => '%d bài đã được plugin khác gắn FAQ schema nên được để nguyên.',
    '%d post(s) have no FAQ section.' => '%d bài không có mục hỏi đáp.',
    'Cache cleared. Each post will be read again the next time it is viewed.' => 'Đã xóa cache. Mỗi bài sẽ được đọc lại ở lượt xem kế tiếp.',
    'FAQ schema (Horse Tools)' => 'FAQ schema (Horse Tools)',
    'Another plugin already publishes FAQ schema for this post, so Horse Tools is staying out of the way.' => 'Một plugin khác đã gắn FAQ schema cho bài này nên Horse Tools tự nhường.',
    '%d question found' => 'Tìm thấy %d câu hỏi',
    '%d questions found' => 'Tìm thấy %d câu hỏi',
    'Only %1$d question found; at least %2$d are needed before schema is published.' => 'Chỉ tìm thấy %1$d câu hỏi; cần ít nhất %2$d thì mới gắn schema.',
    'No FAQ section recognised in this post.' => 'Không nhận ra mục hỏi đáp nào trong bài này.',
    // PHP snippets (1.2.59) + hardening-constant policy (1.2.60)
    'This site has WordPress\'s built-in file editor switched off (DISALLOW_FILE_EDIT) — good. PHP snippets still work, because they carry their own protections: two-factor unlock, a syntax check before saving, auto-disable on a crash, and code signing. To switch them off as well, add HORSETOOLS_NO_PHP to wp-config.php.' => 'Site này đã tắt trình sửa file có sẵn của WordPress (DISALLOW_FILE_EDIT) — rất tốt. Snippet PHP vẫn dùng được vì nó có lớp bảo vệ riêng: mở khóa bằng xác thực hai lớp, kiểm tra cú pháp trước khi lưu, tự tắt khi gây lỗi và ký xác thực code. Nếu muốn tắt luôn cả snippet PHP, hãy thêm HORSETOOLS_NO_PHP vào wp-config.php.',
    'PHP snippets are unavailable because this site sets DISALLOW_FILE_MODS — the platform does not allow code changes at all.' => 'Snippet PHP không khả dụng vì site đặt DISALLOW_FILE_MODS — nền tảng không cho phép thay đổi code.',
    'This site sets DISALLOW_FILE_MODS, so running PHP from the database is not allowed.' => 'Site này đặt DISALLOW_FILE_MODS nên không được chạy PHP từ cơ sở dữ liệu.',
    'PHP snippets are switched off by HORSETOOLS_NO_PHP in wp-config.php.' => 'Snippet PHP đã bị tắt bởi HORSETOOLS_NO_PHP trong wp-config.php.',
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
	'SAVE FONT SETTINGS' => 'LƯU CÀI ĐẶT FONT',
	'Fonts have their own Save button below. The Save at the bottom of this screen does not write font settings.' => 'Phần font có nút Lưu riêng bên dưới. Nút Lưu ở cuối màn hình này không ghi cài đặt font.',
	'Submit URLs' => 'Gửi đường dẫn',
	'Google API settings' => 'Cài đặt Google API',

	'Your own icon (paste an SVG)' => 'Icon riêng của bạn (dán mã SVG)',
	'Leave empty to use the icon chosen above; anything pasted here replaces it. Its colour comes from the Icon colour setting below, so the fill in your SVG is ignored. Scripts and event handlers are removed.' => 'Để trống thì dùng icon chọn ở trên; dán gì vào đây sẽ thay thế icon đó. Màu lấy từ ô Màu icon bên dưới, nên thuộc tính fill trong SVG bị bỏ qua. Mã script và trình xử lý sự kiện đều bị loại.',
	'Icon colour' => 'Màu icon',
	'Leave empty and the icon follows the list colour.' => 'Để trống thì icon lấy theo màu tiêu đề danh sách.',

	'Font settings' => 'Cài đặt font',
	'Upload a font on the Fonts tab first, then choose where to use it here.' => 'Tải font lên ở tab Fonts trước, rồi chọn nơi dùng font đó tại đây.',

	'Measurement' => 'Đo lường',
	'Contact click measurement' => 'Đo lượt bấm nút liên hệ',
	'Record contact button clicks' => 'Ghi nhận lượt bấm nút liên hệ',
	'Check again' => 'Kiểm tra lại',
	'Where to read the numbers' => 'Xem số liệu ở đâu',
	'Analytics found on your site: %s. Clicks will be recorded there.' => 'Đã tìm thấy Analytics trên site: %s. Lượt bấm sẽ được ghi vào đó.',
	'No Google Analytics tag was found on your site, so there is nowhere for clicks to be recorded yet. Install Site Kit by Google (or any GA4 plugin) first — this setting will then start working on its own.' => 'Chưa tìm thấy mã Google Analytics trên site, nên lượt bấm chưa có chỗ để ghi. Hãy cài Site Kit by Google (hoặc plugin GA4 bất kỳ) trước — sau đó cài đặt này tự chạy.',
	'Sends one event to your analytics each time a visitor taps a phone, Zalo, Messenger, Telegram, WhatsApp, Viber, SMS or email link — including links inside your posts, not only the chat buttons. Nothing is stored on your site and no personal data is involved.' => 'Mỗi lần khách bấm link gọi điện, Zalo, Messenger, Telegram, WhatsApp, Viber, SMS hay email thì gửi một sự kiện sang analytics — tính cả link nằm trong bài viết chứ không riêng nút chat. Không lưu gì trên site và không dính dữ liệu cá nhân.',
	'In Google Analytics: Reports → Engagement → Events. Each channel appears under its own name — contact_phone, contact_zalo, contact_messenger and so on — so there is nothing to configure first. Allow 24 hours for the standard reports; to see it immediately instead, open Admin → DebugView and tap a button on your phone.' => 'Trong Google Analytics: Báo cáo → Mức độ tương tác → Sự kiện. Mỗi kênh hiện dưới tên riêng — contact_phone, contact_zalo, contact_messenger… — nên không phải cấu hình gì trước. Báo cáo thường cần khoảng 24 giờ; muốn thấy ngay thì mở Quản trị → DebugView rồi bấm thử trên điện thoại.',
	'Optional: in Admin → Events, switch on "Mark as key event" for the channels that matter to you. They then appear in the acquisition reports, so you can see which traffic source produces contacts, and they can be imported into Google Ads as a conversion.' => 'Tuỳ chọn: vào Quản trị → Sự kiện, bật "Đánh dấu là sự kiện chính" cho kênh anh quan tâm. Khi đó chúng hiện trong báo cáo thu nạp để biết nguồn nào đẻ ra liên hệ, và nhập được sang Google Ads làm chuyển đổi.',
	'Read these as intent, not outcome: a tap means someone opened the dialler or the chat app, not that a call connected or a message was sent. Ad blockers also stop some clicks reaching Analytics, so the real number is a little higher than the one you see.' => 'Hiểu đây là ý định chứ không phải kết quả: một lượt bấm nghĩa là khách đã mở màn hình gọi hoặc mở app chat, không có nghĩa cuộc gọi đã kết nối hay tin nhắn đã gửi. Trình chặn quảng cáo cũng làm rơi một phần, nên số thật cao hơn số anh thấy đôi chút.',

	'Could not check: this server was unable to open your own home page, which many hosts block. That says nothing about whether analytics is installed — if you have Site Kit or another GA4 plugin, switch this on and check the result in Analytics under Admin → DebugView.' => 'Chưa kiểm được: máy chủ không tự mở được trang chủ của chính nó, nhiều nhà cung cấp hosting chặn việc này. Điều đó KHÔNG có nghĩa là site chưa có analytics — nếu anh đang dùng Site Kit hay plugin GA4 khác thì cứ bật lên, rồi kiểm kết quả trong Analytics ở Quản trị → DebugView.',

	'You are using Tag Manager — one setup step is required' => 'Anh đang dùng Tag Manager — cần cấu hình một bước',
	'Tag Manager does not pass events on by itself. The click is placed in the dataLayer and stops there until you build a tag for it, so nothing reaches Analytics until this is done once.' => 'Tag Manager không tự chuyển tiếp sự kiện. Lượt bấm được đưa vào dataLayer rồi nằm im ở đó cho tới khi anh tạo tag cho nó — chưa làm thì Analytics không nhận được gì.',
	'1. Triggers → New → Custom Event, event name ^contact_ with "use regex matching" ticked. 2. Tags → New → Google Analytics GA4 Event, pointing at your measurement ID, Event Name set to {{Event}} so each channel keeps its own name, using the trigger from step 1. 3. Submit and publish the container.' => '1. Triggers → New → Custom Event, tên sự kiện ^contact_ và tích "use regex matching". 2. Tags → New → Google Analytics GA4 Event, trỏ tới mã đo lường của anh, Event Name đặt là {{Event}} để mỗi kênh giữ tên riêng, dùng trigger ở bước 1. 3. Submit rồi publish container.',

	'Speed' => 'Tốc độ',
	'SEO' => 'SEO',
	'Appearance' => 'Giao diện',
	'Customers' => 'Khách hàng',
	'Tools' => 'Công cụ',
	'Accounts & Email' => 'Tài khoản & Email',
	'Users' => 'Người dùng',
	'Google sign-in' => 'Đăng nhập Google',
	'Posts' => 'Bài viết',
	'Lock content' => 'Khoá nội dung',
	'Signature' => 'Chữ ký',
	'Date shortcodes' => 'Shortcode ngày tháng',
	'Google fetch' => 'Lấy dữ liệu Google',
	'Icons' => 'Biểu tượng',
	'Snippets' => 'Đoạn mã',
	'Chat' => 'Chat',
	'Ad-block notice' => 'Thông báo chặn quảng cáo',
	'Notification bar' => 'Thanh thông báo',
	'Popup' => 'Popup',
	'Cookie notice' => 'Thông báo cookie',
	'Ad clicks' => 'Ép click quảng cáo',
	'AdSense' => 'AdSense',
	'ads.txt' => 'ads.txt',
	'Site' => 'Ngoài site',
	'Admin area' => 'Trong quản trị',
	'Site search' => 'Tìm kiếm trên site',
	'Fonts' => 'Phông chữ',
	'Protection' => 'Bảo vệ',
	'Login page' => 'Trang đăng nhập',
	'Maintenance 503' => 'Trang bảo trì 503',
	'Links & URLs' => 'Liên kết & đường dẫn',
	'Rich results' => 'Kết quả nổi bật',
	'Redirects 301' => 'Chuyển hướng 301',
	'Broken links 404' => 'Liên kết gãy 404',
	'Table of contents' => 'Mục lục',
	'Optimisation' => 'Tối ưu',
	'Images' => 'Hình ảnh',
	'Clean posts' => 'Dọn bài viết',
	'Clean comments' => 'Dọn bình luận',
	'Clean media' => 'Dọn thư viện',
	'Cleanup schedule' => 'Lịch dọn dẹp',
	'Admin tools' => 'Công cụ quản trị',
	'Plugin settings' => 'Cài đặt plugin',
	'Code in head' => 'Chèn code vào head',
	'Code in body' => 'Chèn code vào body',
	'Code in footer' => 'Chèn code vào footer',
	'Code on login' => 'Chèn code trang đăng nhập',
	'Quick guide — how the chat feature works' => 'Hướng dẫn nhanh — chức năng chat hoạt động thế nào',
	'Pick a button skin' => 'Chọn kiểu nút',
	'Add contact channels' => 'Thêm kênh liên hệ',
	'Services panel (mobile)' => 'Bảng dịch vụ (điện thoại)',
	'Panel layout' => 'Bố cục bảng',
	'Theme colour' => 'Màu chủ đạo',
	'Display style (where the panel appears)' => 'Kiểu hiển thị (bảng hiện ở đâu)',
	'Custom / Maps:' => 'Tuỳ chỉnh / Bản đồ:',
	'Choose a built-in icon' => 'Chọn biểu tượng có sẵn',
	'— or paste your own SVG.' => '— hoặc dán mã SVG của bạn.',
	'Click an SVG box, then' => 'Bấm vào ô SVG, rồi',
	'Save' => 'Lưu',
	'Two-factor authentication' => 'Xác thực hai lớp',
	'CSV' => 'CSV',

	'Scripts, styles, lazy loading, image compression and WebP.' => 'Script, CSS, hoãn tải ảnh, nén ảnh và WebP.',
	'Permalinks, image alt text, external links and FAQ schema.' => 'Đường dẫn tĩnh, alt ảnh, link ra ngoài và schema FAQ.',
	'Login lockout, two-factor, reCAPTCHA and the login page.' => 'Khoá đăng nhập, xác thực hai lớp, reCAPTCHA và trang đăng nhập.',
	'Post images, duplicating posts and the image lightbox.' => 'Ảnh trong bài, nhân bản bài viết và xem ảnh phóng to.',
	'Dark mode, scrollbar, effects, and the look of the admin area.' => 'Chế độ tối, thanh cuộn, hiệu ứng và giao diện trang quản trị.',
	'Chat buttons, contact channels and WooCommerce tweaks.' => 'Nút chat, kênh liên hệ và tinh chỉnh WooCommerce.',
	'Roles, avatars, SMTP and signing in with Google.' => 'Phân quyền, ảnh đại diện, SMTP và đăng nhập bằng Google.',
	'Admin housekeeping and the plugin\'s own settings.' => 'Dọn dẹp trang quản trị và cài đặt của chính plugin.',
	'Settings are grouped by what they do. Pick a group below, or search for a setting by name in the panel on the right.' => 'Cài đặt được gom theo công việc. Chọn một nhóm bên dưới, hoặc tìm theo tên trong ô bên phải.',
	'For an Nginx server, if the ads.txt file already exists in the root directory of the website, it will prioritize the static file, so this function will not work. If you want to use it, you can either configure Nginx or delete the static file before proceeding' => 'Với máy chủ Nginx, nếu thư mục gốc đã có sẵn tệp ads.txt thì máy chủ ưu tiên tệp tĩnh đó và chức năng này sẽ không chạy. Muốn dùng thì cấu hình lại Nginx hoặc xoá tệp tĩnh trước',
	'This feature allows you to add CSS, JS, and HTML code to your WordPress site through hooks like WP head, WP body, WP footer, and WP login, making it easy and convenient to supplement your code' => 'Cho phép chèn CSS, JS và HTML vào site qua các móc WP head, WP body, WP footer và WP login — bổ sung code nhanh gọn',
	'This feature helps optimize cleanup tasks such as clearing content, comments, and the media library. It makes your website cleaner and more optimized' => 'Hỗ trợ dọn dẹp nội dung, bình luận và thư viện media, giúp site gọn gàng và nhẹ hơn',
	'Add fonts to your website with just a few simple steps. Additionally, it allows you to quickly set fonts in the specific areas you want to change, offering great convenience' => 'Thêm phông chữ cho site chỉ với vài bước, và gán nhanh phông cho đúng khu vực bạn muốn đổi',
	'This feature allows you to configure website redirects with multiple options such as 301, 404, and 503, making management much simpler' => 'Cấu hình chuyển hướng cho site với nhiều lựa chọn 301, 404 và 503, quản lý đơn giản hơn nhiều',
	'Leverage Google’s Indexing API to speed up the indexing of your website on Google’s search engine. You can add as many API keys as you like for unlimited indexing capacity' => 'Dùng Indexing API của Google để đẩy nhanh việc lập chỉ mục site trên Google. Thêm bao nhiêu khoá API cũng được, không giới hạn hạn mức',
	'The table of contents is an incredibly useful feature that allows readers to easily grasp the content by summarizing the headings (h-tags) in the article' => 'Mục lục giúp người đọc nắm nhanh nội dung bằng cách tóm tắt các tiêu đề (thẻ h) trong bài',
	'To add advertisements to your website, simply enable this feature. It provides you with various ad options to meet your distribution needs' => 'Bật tính năng này để đưa quảng cáo lên site, với nhiều lựa chọn vị trí và hình thức hiển thị',
	'Support quick notification setup, allowing web managers to easily deliver useful and timely content, helping users stay informed effortlessly' => 'Thiết lập thông báo nhanh, giúp người quản trị truyền đạt nội dung hữu ích và kịp thời tới người xem',
	'This feature compiles various shortcode templates with essential functionalities that are frequently used to enhance the user experience in WordPress' => 'Tập hợp nhiều mẫu shortcode với các chức năng hay dùng, giúp cải thiện trải nghiệm trên WordPress',
	'If youre tired of WordPress default search tool because its too slow, try out our lightning-fast search feature to give your users an exceptional and speedy search experience' => 'Nếu bạn thấy công cụ tìm kiếm mặc định của WordPress quá chậm, hãy thử tìm kiếm nhanh của plugin để người dùng có trải nghiệm mượt hơn hẳn',
	'If you need to monitor WordPress debug files without accessing the file manager, this tool is made just for that purpose' => 'Nếu cần theo dõi tệp debug của WordPress mà không phải mở trình quản lý tệp, công cụ này sinh ra cho việc đó',
	'If you want to export or import the settings of the Horse Tools plugin, enable this feature. It allows you to easily transfer configurations from this site to another' => 'Bật tính năng này nếu muốn xuất hoặc nhập cài đặt của Horse Tools, để chuyển cấu hình từ site này sang site khác',
	'Accepted Font Format : woff2, ttf, otf, off | Font Size: Upto 25 MB' => 'Định dạng nhận: woff2, ttf, otf, off | Dung lượng: tối đa 25 MB',
	'The "Index Now" link will be added in the custom post management section to allow quick index submission' => 'Liên kết "Index Now" sẽ xuất hiện trong màn quản lý bài viết để gửi lập chỉ mục nhanh',
	'This section could not be displayed. The rest of the screen still works.' => 'Không hiển thị được mục này. Phần còn lại của màn hình vẫn dùng bình thường.',
	'Enter the title and content you want to display when ad-blocker is detected' => 'Nhập tiêu đề và nội dung muốn hiện khi phát hiện trình chặn quảng cáo',
	'Enter the content you want to display in the notification, and customize the colors to match your preferences. A notification will appear at the top of your website, making it easy for users to see' => 'Nhập nội dung muốn hiện trong thông báo và chỉnh màu theo ý bạn. Thông báo sẽ nằm ở đầu site cho người xem dễ thấy',
	'Enter the content you want to display and configure the customizations above so the popup can appear when users visit your website' => 'Nhập nội dung muốn hiện và cấu hình các tuỳ chọn ở trên để popup xuất hiện khi khách vào site',
	'This function will redirect all of your website pages to the destination page of your choice' => 'Chuyển hướng toàn bộ trang trên site về địa chỉ bạn chọn',
	'Redirect the 404 page to the homepage or a custom page of your choice, leave the field blank if you want to redirect to the homepage' => 'Chuyển hướng trang 404 về trang chủ hoặc một trang bạn chọn; để trống nếu muốn về trang chủ',
	'All links on your website will redirect to the maintenance page, and only logged-in admin accounts can view the content' => 'Mọi liên kết trên site sẽ chuyển về trang bảo trì, chỉ tài khoản quản trị đã đăng nhập mới xem được nội dung',
	'Automatic initialization after <span id="sogiay" style="padding: 5px;">3</span>s' => 'Tự khởi động sau <span id="sogiay" style="padding: 5px;">3</span> giây',
	'You can use the shortcode to add the search icon to the location you want' => 'Dùng shortcode để đặt biểu tượng tìm kiếm vào vị trí bạn muốn',
	'Configure the options and create search data. If you want to refresh, you can delete the search data and recreate it. After enabling quick search and completing data creation, a quick search popup will appear when you enter the search box on the website' => 'Cấu hình các tuỳ chọn rồi tạo dữ liệu tìm kiếm. Muốn làm mới thì xoá dữ liệu và tạo lại. Sau khi bật tìm kiếm nhanh và tạo xong dữ liệu, popup tìm kiếm sẽ hiện khi khách bấm vào ô tìm kiếm trên site',
	'This shortcode allows you to lock any content, and only the selected group of logged-in users can view it' => 'Shortcode này khoá một đoạn nội dung bất kỳ, chỉ nhóm người dùng đã đăng nhập mà bạn chọn mới xem được',
	'If you want to display your signature anywhere, you can create content above and then use the generated shortcode at your desired location' => 'Muốn hiện chữ ký ở đâu thì soạn nội dung ở trên rồi dán shortcode sinh ra vào vị trí đó',
	'This shortcode is used to display the date in the post title. Please note that you need to enable the shortcode usage in the post title under the POST, PAGE section' => 'Shortcode này hiện ngày tháng trong tiêu đề bài viết. Lưu ý phải bật cho phép dùng shortcode trong tiêu đề ở phần Bài viết',
	'This snippet’s code no longer matches its signature — it was changed outside this screen and will not run. Review it and save again to re-sign it.' => 'Mã của đoạn này không còn khớp chữ ký — nó đã bị sửa từ nơi khác nên sẽ không chạy. Hãy xem lại rồi lưu để ký lại.',
	'You can use the shortcode in the editor and add it to the position you want, note: only use 1 shortcode in the post' => 'Dùng shortcode trong trình soạn thảo và đặt vào vị trí bạn muốn; lưu ý mỗi bài chỉ nên dùng 1 shortcode',
	'None: default not selected, Auto: background image automatically changes each time the page is loaded, Color: change the color you want, Upload: upload the background image you want' => 'Không: mặc định không chọn. Tự động: ảnh nền tự đổi mỗi lần tải trang. Màu: chọn màu bạn muốn. Tải lên: tự tải ảnh nền của bạn',
	'You can further customize your login page by enabling this feature' => 'Bật tính năng này để tuỳ biến sâu hơn trang đăng nhập',
	'Retrieve the Site Key and Secret Key from your Google reCAPTCHA project and add them to the two fields above' => 'Lấy Site Key và Secret Key từ dự án Google reCAPTCHA của bạn rồi điền vào hai ô ở trên',
	'Convert paths from (domain.com/category/news/latest-news, domain.com/category/news) to (domain.com/latest-news, domain.com/news)' => 'Đổi đường dẫn từ (domain.com/category/news/latest-news, domain.com/category/news) thành (domain.com/latest-news, domain.com/news)',
	'If you enable this feature, your Pages will have .html appended to them, for example: domain.com/page.html' => 'Bật lên thì các Trang sẽ có đuôi .html, ví dụ: domain.com/page.html',
	'This feature will use the title of the post as the description for the image when uploaded' => 'Lấy tiêu đề bài viết làm mô tả cho ảnh khi tải lên',
	'This feature will add nofollow and _blank to external links on your site' => 'Thêm nofollow và _blank cho các liên kết ra ngoài trên site',
	'The changing image is square, with the standard size being 100x100 pixels' => 'Ảnh thay thế dạng vuông, kích thước chuẩn là 100x100 pixel',
	'Enable this feature if you want to customize the footer in the WP admin' => 'Bật nếu bạn muốn tuỳ biến chân trang trong khu quản trị WordPress',
	'You can disable default widgets on the dashboard that you dont use' => 'Bạn có thể tắt các widget mặc định trên bảng tin mà mình không dùng',
	'You can create your widget by activating it and entering content into the box below' => 'Bật lên rồi nhập nội dung vào ô bên dưới để tạo widget của riêng bạn',
	'Enable and configure the functions below to enable Google sign-in to work' => 'Bật và cấu hình các mục bên dưới để đăng nhập bằng Google hoạt động',
	'Copy the link below to add it to the Authorized redirect URLs in your Google Developers project' => 'Sao chép liên kết bên dưới rồi thêm vào mục Authorized redirect URLs trong dự án Google Developers của bạn',
	'You can customize the role of successful registrants, with the default role being "subscriber"' => 'Bạn có thể chọn vai trò cho người đăng ký thành công, mặc định là "subscriber"',
	'Retrieve the API Client ID and Client Secret from your Google Developers project and add them to the two fields above' => 'Lấy Client ID và Client Secret từ dự án Google Developers của bạn rồi điền vào hai ô ở trên',
	'You can paste the shortcode into the position where you want the login button to appear' => 'Dán shortcode vào vị trí bạn muốn nút đăng nhập xuất hiện',
	'Enable to display the Google login button on the default WordPress login form' => 'Bật để hiện nút đăng nhập Google trên form đăng nhập mặc định của WordPress',
	'Enable to display the Google login button on the WooCommerce login form' => 'Bật để hiện nút đăng nhập Google trên form đăng nhập của WooCommerce',
	'choose how the floating button looks from the visual grid below (17 styles). What you see is what visitors get.' => 'chọn kiểu nút nổi từ lưới mẫu bên dưới (17 kiểu). Bạn thấy sao thì khách thấy vậy.',
	'pick a channel (Zalo, Messenger, Phone…) and its logo is added automatically. For a custom button, choose a built-in icon or paste your own SVG.' => 'chọn một kênh (Zalo, Messenger, Điện thoại…) là logo tự được gắn. Với nút tự tạo, chọn biểu tượng có sẵn hoặc dán mã SVG của bạn.',
	'a panel that slides up to show your services or articles. Pick its layout, colour and how it appears from the visual grids. To open it, point a bottom-bar item at' => 'một bảng trượt lên để giới thiệu dịch vụ hoặc bài viết. Chọn bố cục, màu và cách xuất hiện từ lưới mẫu. Muốn mở bảng thì trỏ một mục ở thanh dưới tới',
	'a bar pinned to the bottom of the phone screen. Choose one of 5 styles from the grid.' => 'một thanh ghim ở đáy màn hình điện thoại. Chọn một trong 5 kiểu từ lưới mẫu.',
	'a greeting bubble, business hours (shows Online / Away), scan-to-open QR on desktop, and pre-filled WhatsApp / SMS messages.' => 'bong bóng chào, giờ làm việc (hiện Đang online / Bận), mã QR quét để mở trên máy tính, và tin nhắn WhatsApp / SMS soạn sẵn.',
	'click SAVE CONTENT at the bottom of the page. The Services panel has its own “Save services” button.' => 'bấm LƯU NỘI DUNG ở cuối trang. Riêng bảng Dịch vụ có nút “Lưu dịch vụ” của nó.',
	'Pick a channel (Zalo, Messenger, Phone…) and its logo is added automatically — no icon needed.' => 'Chọn một kênh (Zalo, Messenger, Điện thoại…) là logo tự được gắn — không cần chọn biểu tượng.',
	'Phone, SMS, Messenger, Telegram, Zalo, Whatsapp, Viber, Skype, Tiktok, Mail:' => 'Điện thoại, SMS, Messenger, Telegram, Zalo, Whatsapp, Viber, Skype, Tiktok, Mail:',
	'Only for a custom icon: click the SVG box below, then' => 'Chỉ dùng khi muốn biểu tượng riêng: bấm vào ô SVG bên dưới, rồi',
	'Step 1: Go to Appearance > Menus > Create a new menu > check Navigation bar (Horse Tools)' => 'Bước 1: Vào Giao diện > Menu > Tạo menu mới > tích chọn Thanh điều hướng (Horse Tools)',
	'Step 2: Below, if you want the menu to open on a specific button, add <b style="color:red">#horsenavi</b> to the field (#id or .class). For (Enter link), enter <b style="color:red">#</b>. Note: Only add it to one of the 5 buttons below' => 'Bước 2: Bên dưới, nếu muốn menu mở ở một nút cụ thể thì thêm <b style="color:red">#horsenavi</b> vào ô (#id hoặc .class). Ở ô (Nhập liên kết) thì điền <b style="color:red">#</b>. Lưu ý: chỉ thêm vào một trong 5 nút bên dưới',
	'jQuery Migrate is a library used to maintain the operation of certain themes, plugins that rely on older code. If your website no longer relies on this library, you can disable it' => 'jQuery Migrate là thư viện giúp một số giao diện và plugin viết theo code cũ chạy được. Nếu site bạn không còn cần tới nó thì có thể tắt',
	'Instant-page is a library that allows you to preload the content of a linked page into the browser memory simply by hovering over the link. When you click on the link, it provides a remarkably fast loading experience' => 'Instant-page tải trước nội dung trang đích vào bộ nhớ trình duyệt ngay khi khách rê chuột lên liên kết, nên lúc bấm vào trang mở gần như tức thì',
	'Smooth-scroll is a library that enables you to create a smooth scrolling effect, providing users with a perception of faster page navigation' => 'Smooth-scroll tạo hiệu ứng cuộn mượt, khiến người dùng cảm thấy trang chuyển nhanh hơn',
	'With this feature, HTML will be compressed into a single line, removing unnecessary characters and whitespace to speed up page loading' => 'Nén HTML về một dòng, bỏ ký tự và khoảng trắng thừa để trang tải nhanh hơn',
	'Do not enable if you are using optimization plugins with similar functionality (conflict)' => 'Đừng bật nếu bạn đang dùng plugin tối ưu có chức năng tương tự (sẽ xung đột)',
	'If you enable this feature and set automatic revision limit and automatic save time for posts or pages, it will reduce the amount of data stored in the database' => 'Bật tính năng này rồi đặt giới hạn bản nháp và thời gian tự lưu cho bài viết hoặc trang sẽ giảm lượng dữ liệu lưu trong cơ sở dữ liệu',
	'If you find the new editor too difficult to use, then revert it to the Classic Editor version' => 'Nếu thấy trình soạn thảo mới khó dùng, hãy quay về bản Classic Editor',
	'Enable this feature if you want to add additional functionalities to the Classic Editor to enhance professional editing' => 'Bật nếu bạn muốn bổ sung thêm chức năng cho Classic Editor để soạn thảo chuyên nghiệp hơn',
	'Enable this feature if you want to add the Classic Editor button in the post and page management interface. With this feature, you dont need to set the Classic Editor as default but can use it in parallel' => 'Bật nếu bạn muốn có thêm nút Classic Editor trong màn quản lý bài viết và trang. Như vậy không cần đặt Classic Editor làm mặc định mà vẫn dùng song song được',
	'If you find the new Widget Manager too difficult to use, then revert it to the Classic Widget version' => 'Nếu thấy trình quản lý widget mới khó dùng, hãy quay về bản Classic Widget',
	'This feature allows you to add the Classic Editor to the category description box when editing posts or products' => 'Thêm Classic Editor vào ô mô tả chuyên mục khi sửa bài viết hoặc sản phẩm',
	'If you find the tools above unnecessary, you can hide them to make the WP admin interface cleaner. This function only hides them without blocking access to their links' => 'Nếu thấy các công cụ ở trên không cần thiết, bạn có thể ẩn đi cho giao diện quản trị gọn hơn. Chức năng này chỉ ẩn chứ không chặn truy cập qua liên kết',
	'If you want to use font Awesome, you can enable it (it an icon font). You can search for icons at:' => 'Nếu muốn dùng Font Awesome (bộ phông biểu tượng) thì bật lên. Bạn có thể tra biểu tượng tại:',
	'Choose decorations for the website, such as Christmas or Lunar New Year (If any effects cause issues on your website, it may be due to javascript conflicts. You can switch to other effects to use)' => 'Chọn hiệu ứng trang trí cho site, ví dụ Giáng sinh hay Tết (nếu hiệu ứng nào gây lỗi thì thường do xung đột JavaScript, bạn đổi sang hiệu ứng khác)',
	'This function allows you to initiate a dark mode library, enabling the website to switch between light and dark modes' => 'Bật thư viện chế độ tối, cho phép site chuyển qua lại giữa nền sáng và nền tối',
	'This function allows you to customize the color of the scrollbar to your liking' => 'Tuỳ chỉnh màu thanh cuộn theo ý bạn',
	'8-bit PNG images are not supported by the GD library, and this format already has perfect compression and does not need further processing' => 'Thư viện GD không hỗ trợ ảnh PNG 8-bit, mà định dạng này vốn đã nén rất tốt nên không cần xử lý thêm',
	'If you have members and do not want them to upload images with excessively large sizes, causing storage space usage, you can limit the maximum size allowed for upload' => 'Nếu site có thành viên và bạn không muốn họ tải lên ảnh quá nặng làm tốn dung lượng, hãy giới hạn kích thước tối đa cho phép',
	'Enable this feature if you want JFIF format images to be uploaded to the media. The uploaded images will be converted to JPG or WEBP format according to the configuration' => 'Bật nếu bạn muốn cho phép tải ảnh định dạng JFIF lên thư viện. Ảnh tải lên sẽ được chuyển sang JPG hoặc WEBP theo cấu hình',
	'You can adjust the compression level of the image from 5 to 100 (100 being no compression)' => 'Chỉnh mức nén ảnh từ 5 đến 100 (100 là không nén)',
	'Convert PNG images to JPG upon upload, and compress the images according to the JPG configuration' => 'Chuyển ảnh PNG sang JPG khi tải lên và nén theo cấu hình JPG',
	'Convert JPG and PNG images to WEBP upon upload, and compress the images according to the configuration you enter' => 'Chuyển ảnh JPG và PNG sang WEBP khi tải lên và nén theo cấu hình bạn nhập',
	'Convert JPG and PNG images to AVIF upon upload, and compress the images according to the configuration you enter' => 'Chuyển ảnh JPG và PNG sang AVIF khi tải lên và nén theo cấu hình bạn nhập',
	'Limits the maximum width and height of JPG, PNG, WEBP images upon upload. You can leave it blank if you want to keep the original size' => 'Giới hạn chiều rộng và chiều cao tối đa của ảnh JPG, PNG, WEBP khi tải lên. Để trống nếu muốn giữ nguyên kích thước gốc',
	'The feature adds advanced functions when uploading images, such as adding frames, flipping images... allowing you to automatically customize your images during upload. Picture frame (PNG)' => 'Bổ sung các chức năng nâng cao khi tải ảnh như thêm khung, lật ảnh… để tự động xử lý ảnh ngay lúc tải lên. Khung ảnh (PNG)',
	'If you want your uploaded images to be watermarked, please use this function and configure it above. Watermark (PNG, JPG)' => 'Nếu muốn đóng dấu chìm lên ảnh tải lên thì dùng chức năng này và cấu hình ở trên. Dấu chìm (PNG, JPG)',
	'Enable this feature if you want images in posts copied from another source to be stored on your website' => 'Bật nếu bạn muốn ảnh trong bài chép từ nguồn khác được lưu về site của mình',
	'This function allows you to delete images attached to posts when deleting the posts themselves. Note that if multiple posts use the same image, it will also be deleted when removing the post' => 'Xoá luôn ảnh đính kèm khi xoá bài viết. Lưu ý nếu nhiều bài dùng chung một ảnh thì ảnh đó cũng bị xoá theo',
	'Enable this feature if you want the first image in the post to become the featured image if the featured image field is empty. Additionally, you can select a default featured image in case both the featured image and the images in the post are empty' => 'Bật nếu bạn muốn lấy ảnh đầu tiên trong bài làm ảnh đại diện khi ô ảnh đại diện còn trống. Ngoài ra bạn có thể chọn một ảnh đại diện mặc định cho trường hợp bài không có ảnh nào',
	'Enable this feature if you want the original image size to be selected by default whenever adding images to the post' => 'Bật nếu bạn muốn mặc định chọn kích thước ảnh gốc mỗi khi chèn ảnh vào bài',
	'If you want to enable the feature to duplicate posts, pages, or custom post types, please activate this function' => 'Bật chức năng này nếu bạn muốn nhân bản bài viết, trang hoặc loại nội dung tuỳ chỉnh',
	'This feature allows you to add Shortcode to post titles, which is very convenient for using custom tools' => 'Cho phép dùng shortcode ngay trong tiêu đề bài viết, rất tiện khi dùng các công cụ tuỳ chỉnh',
	'This feature allows you to set newly edited posts to be displayed first in the main loop' => 'Đưa bài vừa được sửa lên đầu danh sách hiển thị',
	'Enable and add the category IDs you want to hide from the main loop displaying posts on the homepage, for example: 1, 2, 3' => 'Bật rồi nhập ID các chuyên mục muốn ẩn khỏi danh sách bài viết ngoài trang chủ, ví dụ: 1, 2, 3',
	'Enable SMTP mail to allow SMTP mail to function and send emails when you perform a test' => 'Bật SMTP để chức năng gửi mail qua SMTP hoạt động và gửi được thư khi bạn bấm gửi thử',
	'You need to activate this feature and customize the content below so that new registered users can receive emails' => 'Bật tính năng này và soạn nội dung bên dưới để người đăng ký mới nhận được email',
	'If someone replies to a comment, there will be an email notification sent to the commenter' => 'Khi có người trả lời bình luận, hệ thống sẽ gửi email báo cho người đã bình luận',
	'With this feature, you can notify your orders to your Telegram group, helping you manage orders conveniently' => 'Gửi thông báo đơn hàng về nhóm Telegram của bạn để tiện theo dõi',
	'With this feature, regular users can only view their own posts and images they uploaded, while the admin can view all of them' => 'Người dùng thường chỉ xem được bài viết và ảnh của chính họ, còn quản trị viên xem được tất cả',
	'With this feature, regular users cannot access the WordPress admin page' => 'Người dùng thường sẽ không vào được trang quản trị WordPress',
	'If you find the Admin Bar distracting every time you view the website, you can turn it off (there is an option for you to turn off all or only turn off for users)' => 'Nếu thấy thanh Admin Bar vướng mắt mỗi lần xem site thì có thể tắt (có tuỳ chọn tắt cho tất cả hoặc chỉ tắt cho người dùng thường)',
	'With this feature, there will be an additional button in the profile section allowing users to upload avatars' => 'Thêm một nút trong trang hồ sơ để người dùng tự tải ảnh đại diện lên',
	'This feature allows you to only display Horse Tools to a specific Admin account' => 'Chỉ hiển thị Horse Tools cho một tài khoản quản trị nhất định',
	'You can hide Horse Tools from the WP menu, but you can still access it through the link. This will hide Horse Tools for all accounts' => 'Ẩn Horse Tools khỏi menu WordPress nhưng vẫn vào được qua đường dẫn. Cách này ẩn với mọi tài khoản',
	'If you dont want to automatically switch the language based on WordPress context, please select the language you prefer' => 'Nếu không muốn ngôn ngữ tự đổi theo WordPress, hãy chọn ngôn ngữ bạn muốn dùng',
	'If you receive this email, then the SMTP email sending function is working well' => 'Nếu bạn nhận được thư này thì chức năng gửi mail qua SMTP đang hoạt động tốt',
	'Hello %s. A new comment has been posted on your article "%s". You can view the comment here: %s' => 'Xin chào %s. Bài viết "%s" của bạn vừa có bình luận mới. Bạn có thể xem tại đây: %s',
	'Please disable ad-blocker on your browser to access and view the content' => 'Vui lòng tắt trình chặn quảng cáo trên trình duyệt để xem được nội dung',
	'This website uses cookies to ensure you get the best experience on our site. By continuing to browse, you agree to our use of cookies. For more information, please read our Cookie Policy' => 'Website này dùng cookie để mang lại trải nghiệm tốt nhất cho bạn. Khi tiếp tục duyệt web, bạn đồng ý với việc chúng tôi sử dụng cookie. Xem thêm tại Chính sách cookie',
	'The PHP snippet "%s" stopped the page it ran on, so Horse Tools switched it off. Edit it and switch it back on when it is fixed.' => 'Đoạn PHP "%s" làm chết trang mà nó chạy, nên Horse Tools đã tắt nó đi. Hãy sửa lại rồi bật lên khi đã xong.',
	'Heads up: a PHP snippet was just changed on %1$s. Snippet: "%2$s". By: %3$s (IP %4$s). If this was not you, change your password and review Horse Tools → Shortcode immediately.' => 'Lưu ý: vừa có một đoạn PHP bị thay đổi trên %1$s. Đoạn mã: "%2$s". Bởi: %3$s (IP %4$s). Nếu không phải bạn, hãy đổi mật khẩu và kiểm tra ngay Horse Tools → Shortcode.',
	'Switch on Horse Tools two-factor authentication (Security tab) before using PHP snippets.' => 'Hãy bật xác thực hai lớp của Horse Tools (mục Bảo mật) trước khi dùng đoạn mã PHP.',
	'You are not allowed to change these settings.' => 'Bạn không có quyền thay đổi các cài đặt này.',

	'No Google Analytics tag turned up, either in your settings or on the home page. If you have not installed one yet, Site Kit by Google (or any GA4 plugin) is the usual way. If you know you do have one — added through Tag Manager, a caching layer, or a consent tool that loads it later — this check simply could not see it: switch the setting on anyway. It looks for analytics at the moment of the click, not now, so it starts working the instant a tag is present.' => 'Không tìm ra mã Google Analytics nào, cả trong cài đặt lẫn trên trang chủ. Nếu bạn chưa cài thì Site Kit by Google (hoặc plugin GA4 bất kỳ) là cách phổ biến nhất. Còn nếu bạn biết chắc site đã có mã — gắn qua Tag Manager, qua lớp cache, hay qua công cụ xin đồng ý cookie nạp muộn — thì chỉ là lần kiểm tra này không nhìn thấy: cứ bật lên bình thường. Nó dò analytics ngay lúc khách bấm chứ không phải lúc này, nên có mã là chạy ngay.',

	'Checked from your own browser: the home page a visitor receives carries no Google Analytics tag. Install Site Kit by Google, or any GA4 plugin, and this setting starts working on its own.' => 'Đã kiểm tra từ chính trình duyệt của bạn: trang chủ mà khách nhận được không có mã Google Analytics nào. Hãy cài Site Kit by Google, hoặc plugin GA4 bất kỳ, rồi cài đặt này sẽ tự chạy.',

	'Tag Manager is installed on this site. If you already built click tags there — a trigger on tel: links, on zalo.me, on m.me — switching this on records the same click twice, once under your own event name and once as contact_phone. Open your container, check whether such tags exist, and pause either those or this setting. Keep whichever one also fires your Google Ads conversions; this setting does not.' => 'Site này có cài Tag Manager. Nếu bạn đã dựng sẵn thẻ theo dõi lượt bấm ở đó — trigger cho link tel:, cho zalo.me, cho m.me — thì bật cái này lên sẽ ghi nhận cùng một cú bấm hai lần, một lần theo tên sự kiện của bạn và một lần thành contact_phone. Hãy mở container ra kiểm tra xem có thẻ nào như vậy không, rồi tạm dừng một trong hai bên. Giữ lại bên nào đang bắn cả chuyển đổi Google Ads; cài đặt này thì không bắn.',
	'Sends one event to your analytics each time a visitor taps a phone, SMS, email, Zalo, Messenger, Telegram, WhatsApp, Viber, Skype, Line, TikTok or Maps link — including links inside your posts, not only the chat buttons. A Custom chat button pointing anywhere else is recorded under its own domain name. Nothing is stored on your site and no personal data is involved.' => 'Mỗi lần khách bấm link gọi điện, SMS, email, Zalo, Messenger, Telegram, WhatsApp, Viber, Skype, Line, TikTok hay Bản đồ thì gửi một sự kiện sang analytics — tính cả link nằm trong bài viết chứ không riêng nút chat. Nút Custom trỏ đi đâu khác thì được ghi nhận theo tên miền của nó. Không lưu gì trên site và không dính dữ liệu cá nhân.',
	'To check it works right now rather than waiting: open your site with %s on the end of the address, tap a contact button, and watch GA4 → Admin → DebugView. Without that flag DebugView stays empty however many times you tap, because it only lists devices that identify themselves as debug devices — which is not a fault in the buttons.' => 'Muốn kiểm tra ngay thay vì ngồi chờ: mở site với %s ở cuối địa chỉ, bấm một nút liên hệ, rồi xem GA4 → Quản trị → DebugView. Không có cờ này thì DebugView cứ trống dù bạn bấm bao nhiêu lần, vì nó chỉ liệt kê thiết bị tự khai là thiết bị debug — chứ không phải nút bị lỗi.',

	'Sends one event to your analytics each time a visitor taps a contact link — phone, SMS, email, Zalo, Messenger, Telegram, WhatsApp, Viber, Line, WeChat, TikTok, Maps and the rest. Any other chat button is recorded under its own site name, so Instagram, Shopee, Signal and anything you add later are covered without waiting for an update. Phone, SMS, email and Viber links count anywhere on the site, including inside your posts; the others count inside the chat widgets, so an article full of outbound links does not flood the report. Nothing is stored on your site and no personal data is involved.' => 'Mỗi lần khách bấm một link liên hệ thì gửi một sự kiện sang analytics — gọi điện, SMS, email, Zalo, Messenger, Telegram, WhatsApp, Viber, Line, WeChat, TikTok, Bản đồ và các kênh còn lại. Nút chat nào khác thì được ghi nhận theo tên trang của nó, nên Instagram, Shopee, Signal hay bất cứ kênh nào bạn thêm sau này đều có, không phải chờ bản cập nhật. Link gọi điện, SMS, email và Viber được tính ở mọi nơi trên site, kể cả trong bài viết; các kênh còn lại chỉ tính trong khu vực nút chat, để bài viết nhiều link ra ngoài không làm ngập báo cáo. Không lưu gì trên site và không dính dữ liệu cá nhân.',
	'Skype (service closed)' => 'Skype (đã ngừng hoạt động)',
	'One of your buttons is set to Skype. Microsoft closed Skype on 5 May 2025 and moved everyone to Teams, so that button now opens nothing for visitors. Change it to another channel — it has been left in place rather than removed for you, so that nothing changes on your site without your say-so.' => 'Một trong các nút của bạn đang đặt là Skype. Microsoft đã đóng cửa Skype ngày 5/5/2025 và chuyển hết người dùng sang Teams, nên nút đó giờ bấm vào không mở được gì. Hãy đổi sang kênh khác — plugin để nguyên chứ không tự xoá giúp, để không có gì trên site bạn thay đổi mà bạn không biết.',

	'Nothing to set up' => 'Không cần thiết lập gì',
	'Your site loads the Google tag, so a click goes straight to Analytics. This is the usual case, and it includes sites where Analytics was installed through Tag Manager — a GA4 tag inside a container loads the Google tag itself.' => 'Site của bạn có nạp thẻ Google, nên mỗi cú bấm đi thẳng sang Analytics. Đây là trường hợp phổ biến nhất, tính cả site cài Analytics qua Tag Manager — thẻ GA4 nằm trong container tự nạp thẻ Google.',
	'Install analytics first' => 'Cài analytics trước đã',
	'The page carries no analytics at all yet, so there is nowhere for a click to be recorded. Install Site Kit by Google, or any GA4 plugin, and this setting starts working on its own with nothing further to configure.' => 'Trang chưa có analytics nào cả, nên lượt bấm không có chỗ để ghi. Hãy cài Site Kit by Google hoặc plugin GA4 bất kỳ, rồi cài đặt này tự chạy mà không phải cấu hình thêm gì.',
	'Your site has Tag Manager but no Google tag — one setup step is required' => 'Site của bạn có Tag Manager nhưng không có thẻ Google — cần làm thêm một bước',
	'Your container has no GA4 tag in it, so Tag Manager has nothing to pass the click on to. It is placed in the dataLayer and stops there until you build the tag below — until then nothing reaches Analytics.' => 'Container của bạn không có thẻ GA4 nào, nên Tag Manager không có chỗ để chuyển tiếp lượt bấm. Nó nằm lại trong dataLayer cho tới khi bạn dựng thẻ theo hướng dẫn bên dưới — chưa dựng thì không có gì tới được Analytics.',
	'If your site has Tag Manager but no Google tag' => 'Nếu site của bạn có Tag Manager nhưng không có thẻ Google',
	'This could not be checked from here, so the instructions are shown just in case. To find out whether they apply to you: open your site, press F12, and type gtag in the console. An answer of "function" means you have nothing to do; "undefined" means follow the steps below.' => 'Từ đây không kiểm tra được nên hướng dẫn cứ hiện ra phòng khi cần. Muốn biết nó có áp dụng cho bạn không: mở site, bấm F12, gõ gtag vào console. Trả lời "function" là bạn không phải làm gì; trả lời "undefined" thì làm theo các bước bên dưới.',
	'1. Triggers → New → Custom Event, event name ^contact_ with "use regex matching" ticked — one trigger covers every channel, including any added later. 2. Tags → New → Google Analytics GA4 Event, pointing at your measurement ID, Event Name set to {{Event}} so each channel keeps its own name, using the trigger from step 1. 3. Optionally add event parameters placement and label, taking their values from Data Layer Variables contact_placement and contact_label. 4. Submit and publish the container.' => '1. Điều kiện kích hoạt → Mới → Sự kiện tuỳ chỉnh, tên sự kiện ^contact_ và tick "Sử dụng khớp biểu thức chính quy" — một trigger là đủ cho mọi kênh, kể cả kênh thêm sau này. 2. Thẻ → Mới → Google Analytics: Sự kiện GA4, trỏ vào mã đo lường của bạn, đặt Tên sự kiện = {{Event}} để mỗi kênh giữ tên riêng, dùng trigger ở bước 1. 3. Tuỳ chọn: thêm tham số sự kiện placement và label, lấy giá trị từ Biến lớp dữ liệu contact_placement và contact_label. 4. Bấm Gửi và xuất bản container.',

	'Checking your site…' => 'Đang kiểm tra site của bạn…',

	// Snippets moved to one record each; the pickers and the list now search
	// and page instead of showing everything at once (1.3.8).
	'That snippet no longer exists.' => 'Snippet này không còn nữa.',
	'%1$d of %2$d' => '%1$d trong %2$d',
	'Page %1$d of %2$d' => 'Trang %1$d / %2$d',
	'Search by name…' => 'Tìm theo tên…',
	'Nothing matches that.' => 'Không có gì khớp.',
	'Showing the first %1$d of %2$d — keep typing to narrow it down.' => 'Đang hiện %1$d đầu tiên trong %2$d — gõ thêm để thu hẹp lại.',
	'Loading…' => 'Đang tải…',
	'Could not load the list.' => 'Không tải được danh sách.',

	// Telegram pairing now matches a per-user code instead of listing every
	// chat the site's bot has ever received (1.3.11).
	'Your pairing code has expired. Reload this page to get a new one.' => 'Mã ghép nối của bạn đã hết hạn. Tải lại trang này để lấy mã mới.',
	'No message containing %s yet. Send that exact code to the bot, then press this again.' => 'Chưa thấy tin nhắn nào chứa %s. Hãy gửi đúng mã đó cho bot rồi bấm lại.',
	'open this site’s Telegram bot:' => 'mở bot Telegram của site này:',
	'open the site’s Telegram bot (ask the site admin which bot if you’re not sure).' => 'mở bot Telegram của site (không rõ bot nào thì hỏi quản trị viên).',
	'send it this code:' => 'gửi cho bot mã này:',
	'Step 3 —' => 'Bước 3 —',
	'The code is how the site tells your chat apart from everyone else’s — it finds only the chat that sent it. Recovery codes then reach your own Telegram, not the admin. (You can also type the chat ID number in manually.)' => 'Mã này là cách site phân biệt chat của bạn với của người khác — nó chỉ tìm đúng chat đã gửi mã. Mã khôi phục sau đó về thẳng Telegram của bạn, không phải của quản trị viên. (Bạn cũng có thể tự nhập số chat ID.)',
	'remember to press “Update profile”.' => 'nhớ bấm “Cập nhật hồ sơ”.',

	// Where the site's signing keys actually live (1.3.14).
	'Signing keys are in wp-config.php, not the database' => 'Khoá ký nằm trong wp-config.php, không nằm trong database',
	'One half is in the database — still safe, but tidy it up' => 'Một nửa nằm trong database — vẫn an toàn, nhưng nên dọn lại cho gọn',
	'Paste the eight keys into wp-config.php — see the notice at the top of any Horse Tools screen' => 'Dán 8 khoá vào wp-config.php — xem thông báo ở đầu bất kỳ màn hình Horse Tools nào',
	'Could not be determined' => 'Không xác định được',
	'This site’s signing keys are stored in the database, not in wp-config.php.' => 'Khoá ký của site này đang nằm trong database, không nằm trong wp-config.php.',
	'Two Horse Tools protections are signed with those keys: the PHP snippet signature, which is meant to refuse code written straight into the database, and the “trusted device” cookie that skips two-factor authentication. Anyone who can read your database can read the keys, so both can be forged. Moving the keys into wp-config.php — a file — restores them.' => 'Hai lớp bảo vệ của Horse Tools được ký bằng khoá đó: chữ ký snippet PHP — thứ sinh ra để từ chối mã được ghi thẳng vào database — và cookie “thiết bị tin cậy” cho phép bỏ qua xác thực hai lớp. Ai đọc được database của bạn là đọc được khoá, nên cả hai đều giả mạo được. Chuyển khoá vào wp-config.php — một file — là khôi phục lại được.',
	'Show new keys to paste' => 'Hiện khoá mới để dán',
	'Paste all eight lines into wp-config.php, above the line that requires wp-settings.php. Replace any existing lines with the same names. Everyone signed in — including you — will be signed out afterwards, which is the point: it invalidates every cookie issued with the old keys.' => 'Dán cả 8 dòng vào wp-config.php, phía trên dòng require wp-settings.php. Nếu đã có dòng cùng tên thì thay thế. Sau đó mọi người đang đăng nhập — kể cả bạn — sẽ bị đăng xuất, và đó chính là mục đích: nó vô hiệu hoá mọi cookie đã cấp bằng khoá cũ.',
	'Copy all eight lines' => 'Sao chép cả 8 dòng',

	// Watching the site's own contact details for changes (1.3.15).
	'Contact details are the ones you confirmed' => 'Thông tin liên hệ đúng như đã xác nhận',
	'Confirm them once, from the notice on any Horse Tools screen' => 'Xác nhận một lần, ở thông báo trên bất kỳ màn hình Horse Tools nào',
	'Something changed — see the notice on any Horse Tools screen' => 'Có thứ đã thay đổi — xem thông báo trên bất kỳ màn hình Horse Tools nào',
	'Phone number' => 'Số điện thoại',
	'Email address' => 'Địa chỉ email',
	'These are correct — remember them' => 'Đúng rồi — ghi nhớ đi',
	'Horse Tools can watch your contact details for changes.' => 'Horse Tools có thể canh thông tin liên hệ của bạn xem có bị đổi không.',
	'It found %d contact details in your settings — phone numbers, Zalo, Messenger, email. Confirm them once and you will be told if any of them ever change. Changing the hotline on a shop is the most direct attack there is, and the quietest.' => 'Đã tìm thấy %d thông tin liên hệ trong cài đặt — số điện thoại, Zalo, Messenger, email. Xác nhận một lần, rồi sau này cái nào đổi là bạn được báo. Đổi hotline của một shop là đòn trực diện nhất, và cũng lặng lẽ nhất.',
	'These are the phone numbers, Zalo, Messenger links and email addresses currently in your settings. Check them, then confirm — after that you will be told if any of them ever change. Changing the hotline on a shop is the most direct attack there is, and the quietest.' => 'Đây là các số điện thoại, Zalo, liên kết Messenger và địa chỉ email đang có trong cài đặt của bạn. Xem lại rồi bấm xác nhận — sau đó cái nào đổi là bạn được báo. Đổi hotline của một shop là đòn trực diện nhất, và cũng lặng lẽ nhất.',
	'Your contact details have changed.' => 'Thông tin liên hệ của bạn đã thay đổi.',
	'New' => 'Mới',
	'Gone' => 'Mất',
	'If you just changed these yourself, confirm them. If you did not, somebody else did — check who last edited your settings before changing anything else.' => 'Nếu bạn vừa tự đổi thì bấm xác nhận. Nếu không phải bạn thì là người khác — hãy xem ai sửa cài đặt gần nhất trước khi đụng vào bất cứ thứ gì khác.',

	// Files in the web root that should not be there (1.3.20).
	'No downloadable secrets in the site folder' => 'Không có file bí mật nào tải về được trong thư mục site',
	'See the banner at the top of any Horse Tools screen' => 'Xem bảng thông báo ở đầu bất kỳ màn hình Horse Tools nào',
	'Contains database credentials or signing keys.' => 'Chứa thông tin đăng nhập database hoặc khoá ký.',
	'A database dump: everything on the site, downloadable.' => 'Bản kết xuất database: toàn bộ site, ai cũng tải được.',
	'A .git directory exposes the whole source history, and often credentials with it.' => 'Thư mục .git để lộ toàn bộ lịch sử mã nguồn, và thường lộ cả mật khẩu kèm theo.',
	'Version-control metadata exposes the source.' => 'Dữ liệu quản lý phiên bản để lộ mã nguồn.',
	'A private key.' => 'Một khoá riêng tư.',
	'A site archive, downloadable by anyone who guesses the name.' => 'Bản nén của site, ai đoán trúng tên là tải được.',
	'Publishes the server configuration, paths and loaded modules.' => 'Phơi bày cấu hình máy chủ, đường dẫn và các module đang nạp.',
	'Usually a phpinfo page.' => 'Thường là một trang phpinfo.',
	'A database client. Anyone who finds it is one password from the database.' => 'Một công cụ truy cập database. Ai tìm ra nó thì chỉ còn cách database đúng một mật khẩu.',
	'PHP errors: server paths, fragments of queries, sometimes the contents of a failed request.' => 'Lỗi PHP: đường dẫn máy chủ, mảnh câu truy vấn, đôi khi cả nội dung của yêu cầu bị lỗi.',
	'Server errors, including absolute paths.' => 'Lỗi máy chủ, kèm đường dẫn tuyệt đối.',
	'There are files in your site’s folder that anyone can download.' => 'Trong thư mục site của bạn có những file ai cũng tải về được.',
	'Somebody has asked for this path %d times.' => 'Đã có người hỏi tới đường dẫn này %d lần.',
	'Delete them, or move them somewhere outside the folder your site is served from. If one of them is a copy of wp-config.php, change your database password afterwards — assume it has been read.' => 'Hãy xoá chúng đi, hoặc chuyển ra ngoài thư mục mà site đang chạy. Nếu có cái nào là bản sao của wp-config.php thì sau đó đổi luôn mật khẩu database — cứ coi như nó đã bị đọc.',

	// Watching contact details inside post content (1.3.21).
	'Contact details in your posts are watched' => 'Thông tin liên hệ trong bài viết được canh',
	'Reading your content: %1$d of %2$d' => 'Đang đọc nội dung: %1$d / %2$d',
	'Reading your content…' => 'Đang đọc nội dung…',
	'Confirm them once, from the banner on any Horse Tools screen' => 'Xác nhận một lần, ở bảng thông báo trên bất kỳ màn hình Horse Tools nào',
	'A new one appeared in a post — see the banner' => 'Có cái mới xuất hiện trong một bài — xem bảng thông báo',
	'Finished reading your posts and pages.' => 'Đã đọc xong bài viết và trang của bạn.',
	'These contact details appear in your content. Confirm them and you will be told if a new one ever turns up in a post — which is what happens when somebody edits an old article to put their own number in it.' => 'Đây là những thông tin liên hệ xuất hiện trong nội dung của bạn. Xác nhận đi, rồi sau này có cái mới nào lòi ra trong bài là bạn được báo — đó chính là chuyện xảy ra khi ai đó sửa một bài cũ để nhét số của họ vào.',
	'A contact detail that was not there before has appeared in your content.' => 'Trong nội dung của bạn vừa xuất hiện một thông tin liên hệ trước đây không có.',

	// The outbound-link inventory and its review screen (1.3.22).
	'Outbound links' => 'Liên kết ra ngoài',
	'Outbound links are watched' => 'Liên kết ra ngoài được canh',
	'Where your content links to' => 'Nội dung của bạn trỏ đi đâu',
	'Still reading your content' => 'Đang đọc nội dung',
	'Still reading your content. Come back in a minute.' => 'Đang đọc nội dung. Lát nữa quay lại nhé.',
	'Still reading your content: %1$d of %2$d. Come back in a minute.' => 'Đang đọc nội dung: %1$d / %2$d. Lát nữa quay lại nhé.',
	'Your posts and pages do not link anywhere outside this site.' => 'Bài viết và trang của bạn không trỏ ra ngoài site này chỗ nào cả.',
	'Every other website your posts and pages point at, one row per domain. Untick anything you do not recognise, then save — you will be told the moment a domain that is not on this list turns up in your content.' => 'Toàn bộ những website khác mà bài viết và trang của bạn trỏ tới, mỗi tên miền một dòng. Cái nào bạn không nhận ra thì bỏ tick rồi lưu lại — sau đó hễ có tên miền nào không nằm trong danh sách này lòi ra trong nội dung là bạn được báo ngay.',
	'Rarely-linked domains are listed first, because the one that was added without your knowing is almost never the one you link to from two hundred posts.' => 'Tên miền ít được trỏ tới nhất nằm trên đầu, vì cái bị chèn vào mà bạn không hay biết gần như không bao giờ là cái bạn trỏ tới từ hai trăm bài.',
	'Domain' => 'Tên miền',
	'Links' => 'Số liên kết',
	'In' => 'Trong bài',
	'Link text' => 'Chữ hiển thị',
	'Loads a script or an embedded frame from this domain' => 'Có nạp script hoặc khung nhúng từ tên miền này',
	'passes SEO value' => 'có truyền giá trị SEO',
	'Tick all' => 'Tick hết',
	'Untick all' => 'Bỏ tick hết',
	'Save this list' => 'Lưu danh sách này',
	'Saved. Everything on this page is approved.' => 'Đã lưu. Mọi thứ trên trang này đều đã duyệt.',
	'Saved. %d domain is still not approved.' => 'Đã lưu. Còn %d tên miền chưa duyệt.',
	'Review the list' => 'Xem lại danh sách',
	'Go through the list once and approve it' => 'Soát qua danh sách một lần rồi duyệt',
	'A domain you have not approved is linked from your content' => 'Nội dung của bạn đang trỏ tới một tên miền bạn chưa duyệt',
	'Horse Tools can tell you when your content starts linking somewhere new.' => 'Horse Tools báo cho bạn khi nội dung bắt đầu trỏ tới một nơi mới.',
	'Your posts and pages link to %d other domain. Go through it once and confirm which ones belong there; after that, a domain that was not on the list is worth one sentence on your screen instead of two years of nobody noticing.' => 'Bài viết và trang của bạn trỏ tới %d tên miền khác. Soát qua một lần và xác nhận cái nào đúng là của bạn; sau đó, một tên miền không có trong danh sách chỉ tốn của bạn một dòng trên màn hình, thay vì hai năm không ai để ý.',
	'Your content links to %d domain you have not approved.' => 'Nội dung của bạn đang trỏ tới %d tên miền bạn chưa duyệt.',

	// The numbered check-in message (1.3.25).
	'Check-in' => 'Báo còn sống',
	'Tell me the site is still being watched' => 'Báo cho tôi biết site vẫn đang được canh',
	'You would be told if this site went quiet' => 'Site im tiếng thì bạn sẽ được báo',
	'Send me a regular check-in message' => 'Gửi tin báo định kỳ cho tôi',
	'The first one goes out as soon as you save, so you find out straight away whether the channel works.' => 'Tin đầu tiên đi ngay khi bạn lưu, để biết luôn kênh gửi có chạy hay không.',
	'Everything else on this page warns you when something is wrong — but only on a screen you have to open. It cannot warn you that it stopped: a site with the plugin switched off looks exactly like a site with nothing wrong.' => 'Mọi thứ khác trên trang này chỉ báo khi có chuyện — mà báo trên một màn hình bạn phải tự mở ra xem. Nó không thể báo rằng chính nó đã ngừng: một site bị tắt plugin trông y hệt một site không có gì sai.',
	'So this sends you a short message on a schedule, with a number on it, even when everything is fine. Numbers that skip mean messages were sent and never reached you. A message that arrives late means nothing was running the schedule. Nothing at all means something stopped it — and that is the one you can only notice from outside the site, which is why each message tells you when to expect the next.' => 'Nên cái này gửi cho bạn một tin ngắn theo lịch, có đánh số, kể cả khi mọi thứ đều ổn. Số nhảy cóc nghĩa là tin có gửi mà không tới tay bạn. Tin tới trễ nghĩa là không có gì chạy lịch cả. Không có tin nào nghĩa là có thứ đã chặn nó lại — và đó là trường hợp duy nhất chỉ đứng ngoài site mới thấy được, nên tin nào cũng nói luôn khi nào có tin kế.',
	'How often' => 'Bao lâu một lần',
	'Every day' => 'Mỗi ngày',
	'Every week (recommended)' => 'Mỗi tuần (nên chọn)',
	'Every two weeks' => 'Hai tuần một lần',
	'Every month' => 'Mỗi tháng',
	'Weekly is the useful setting. Daily turns the one message that is supposed to mean something into noise within a fortnight, and once it is noise a gap in the numbering is not noticed either.' => 'Mỗi tuần là mức dùng được. Mỗi ngày thì chỉ hai tuần là cái tin lẽ ra phải có ý nghĩa biến thành tiếng ồn, mà đã thành tiếng ồn thì số nhảy cóc cũng chẳng ai để ý.',
	'Telegram chat ID for security messages (optional)' => 'Chat ID Telegram cho tin bảo mật (không bắt buộc)',
	'Leave this empty and the message goes to the same Telegram chat as your order notifications, or by email if you have not set a bot up. Orders go to whoever packs them; this does not, so there is a field of its own.' => 'Để trống thì tin đi vào đúng chat Telegram nhận đơn hàng, hoặc gửi email nếu bạn chưa dựng bot. Đơn hàng thì ai gói hàng nhận, còn cái này thì không, nên nó có ô riêng.',
	'Right now these would go to: %s' => 'Hiện tại tin sẽ gửi tới: %s',
	'Email is the fallback and not a good one — a security message sent by a shop\'s own server is exactly the kind that lands in spam. Set up the Telegram bot under WooCommerce if you can.' => 'Email chỉ là phương án chót và không hay ho gì — tin bảo mật do chính máy chủ cửa hàng gửi ra đúng là loại hay rơi vào spam. Dựng được bot Telegram bên WooCommerce thì nên dựng.',
	'Send a test message now' => 'Gửi thử một tin ngay',
	'Send a test message' => 'Gửi thử một tin',
	'Sending…' => 'Đang gửi…',
	'The request itself failed.' => 'Bản thân yêu cầu đã hỏng.',
	'Sent. Check %s — if nothing arrives, the channel is not working even though the site thinks it is.' => 'Đã gửi. Kiểm tra %s — nếu không thấy gì tới thì kênh không chạy, dù site tưởng là chạy.',
	'Last message: #%1$d, %2$s ago.' => 'Tin gần nhất: #%1$d, %2$s trước.',
	'Next one due %s.' => 'Tin kế tới hạn %s.',
	'The site handed it over successfully. That is not the same as it arriving — if the numbers you receive skip, the channel is dropping them.' => 'Site đã chuyển đi thành công. Cái đó không đồng nghĩa với việc nó tới nơi — nếu số bạn nhận được nhảy cóc thì kênh đang làm rơi tin.',
	'It failed: %s' => 'Gửi hỏng: %s',
	'Horse Tools beat #%1$d — %2$s' => 'Horse Tools nhịp #%1$d — %2$s',
	'Horse Tools — %s' => 'Horse Tools — %s',
	'Next beat due: %s' => 'Nhịp kế tới hạn: %s',
	'If it does not arrive, something stopped it — check the site.' => 'Nếu không thấy nó tới, tức là có thứ đã chặn lại — kiểm tra site đi.',
	'Contact details in your settings' => 'Thông tin liên hệ trong cài đặt',
	'Contact details in your posts' => 'Thông tin liên hệ trong bài viết',
	'Where your content links to' => 'Nội dung trỏ đi đâu',
	'Downloadable secrets in the site folder' => 'File bí mật tải về được trong thư mục site',
	'not confirmed yet' => 'chưa xác nhận',
	'not reviewed yet' => 'chưa soát lần nào',
	'still reading' => 'đang đọc',
	'none' => 'không có',
	'CHANGED' => 'ĐÃ ĐỔI',
	'%d NEW' => '%d CÁI MỚI',
	'%d FOUND' => 'TÌM THẤY %d',
	'%d domains, all approved' => '%d tên miền, đã duyệt hết',
	'NOT APPROVED: %s' => 'CHƯA DUYỆT: %s',
	'If this site stops working, or Horse Tools is switched off, nobody is told. Every warning here is only seen by someone who logs in and looks.' => 'Nếu site này ngừng chạy, hoặc Horse Tools bị tắt, sẽ không ai được báo. Mọi cảnh báo ở đây chỉ người nào đăng nhập vào và chịu nhìn mới thấy.',
	'Turn on the regular check-in message' => 'Bật tin báo định kỳ',
	'The check-in message is on but has never been sent. Until one arrives you do not know the channel works.' => 'Tin báo định kỳ đang bật nhưng chưa gửi lần nào. Chừng nào chưa có tin nào tới thì bạn chưa biết kênh có chạy hay không.',
	'The last check-in message could not be sent: %s' => 'Tin báo gần nhất không gửi được: %s',
	'Fix the channel and send a test message' => 'Sửa kênh gửi rồi gửi thử một tin',
	'The last check-in message went out %s ago, which is later than it should be. Nothing has been running the schedule.' => 'Tin báo gần nhất đi từ %s trước, trễ hơn mức lẽ ra phải có. Không có gì chạy lịch cả.',
	'Check that WP-Cron is running on this site' => 'Kiểm tra xem WP-Cron trên site này còn chạy không',
	'Your content has not been read all the way through yet (%1$d of %2$d), so nothing found in it can be trusted as complete.' => 'Nội dung của bạn chưa được đọc hết một lượt (%1$d / %2$d), nên những gì tìm được trong đó chưa thể coi là đầy đủ.',
	'Your content has not been read all the way through yet.' => 'Nội dung của bạn chưa được đọc hết một lượt.',
	'It continues on its own each time you open an admin page' => 'Nó tự chạy tiếp mỗi lần bạn mở một trang quản trị',
	'Security tab → Check-in → turn on the regular message' => 'Tab Bảo mật → Báo còn sống → bật tin báo định kỳ',
	'Turned on but never sent — send a test message' => 'Đã bật mà chưa gửi lần nào — gửi thử một tin',
	'The last one could not be sent — Security tab → Check-in' => 'Tin gần nhất không gửi được — tab Bảo mật → Báo còn sống',
	'Overdue — nothing has been running the schedule' => 'Quá hạn — không có gì chạy lịch cả',
	'Telegram (chat %s)' => 'Telegram (chat %s)',
	'Email to %s' => 'Email tới %s',
	'Nothing to send.' => 'Không có gì để gửi.',
	'Not due yet.' => 'Chưa tới hạn.',
	'This site has no valid admin email address.' => 'Site này không có địa chỉ email quản trị hợp lệ.',
	'WordPress could not hand the message to the mail server.' => 'WordPress không chuyển được tin sang máy chủ mail.',

	// The anchor file — decisions kept outside the database (1.3.26).
	'What you approved is also kept outside the database' => 'Những gì bạn đã duyệt còn được giữ ngoài database',
	'No copy on disk yet — it is written the first time you confirm something' => 'Chưa có bản sao trên đĩa — nó được ghi lần đầu tiên bạn xác nhận một thứ gì đó',
	'wp-content is not writable, so there is nowhere to keep the copy' => 'wp-content không ghi được, nên không có chỗ nào để giữ bản sao',
	'They no longer match — see the banner at the top of any Horse Tools screen' => 'Hai bên không còn khớp — xem bảng thông báo trên đầu bất kỳ màn hình Horse Tools nào',
	'Something changed what you approved, without going through this screen.' => 'Có thứ gì đó đã sửa những gì bạn duyệt, mà không đi qua màn hình này.',
	'Horse Tools keeps a copy of your decisions in a file as well as in the database, precisely so that a change made straight to the database can be seen. These no longer match:' => 'Horse Tools giữ một bản sao các quyết định của bạn trong một file, song song với database, chính là để nhìn ra được khi có ai sửa thẳng vào database. Những mục sau đã không còn khớp:',
	'If you have just restored a database backup, or copied the database from another copy of this site, that is the explanation — the file stayed while the database went back in time. Press the button and it will match again.' => 'Nếu bạn vừa phục hồi một bản sao lưu database, hoặc chép database từ một bản khác của site này, thì đó chính là lý do — file thì đứng yên còn database thì lùi về quá khứ. Bấm nút là hai bên khớp lại.',
	'If you have not done either of those things, then something can write to your database that should not be able to. Changing passwords will not fix that on its own; the way in has to be found.' => 'Còn nếu bạn không làm cả hai việc đó, thì có thứ gì đó đang ghi được vào database của bạn mà lẽ ra không được phép. Đổi mật khẩu một mình không sửa được chuyện này; phải tìm cho ra đường nó vào.',
	'That was me — line them up again' => 'Tôi làm đó — cho khớp lại đi',
	'the contact details you confirmed in your settings' => 'thông tin liên hệ bạn đã xác nhận trong cài đặt',
	'the contact details you confirmed in your posts' => 'thông tin liên hệ bạn đã xác nhận trong bài viết',
	'the list of domains you approved' => 'danh sách tên miền bạn đã duyệt',
	'Your approvals match their copy on disk' => 'Những gì bạn duyệt khớp với bản sao trên đĩa',
	'THEY DO NOT — something changed them without going through the screens' => 'KHÔNG KHỚP — có thứ gì đó đã sửa mà không đi qua màn hình nào',
	'no copy on disk yet' => 'chưa có bản sao trên đĩa',

);

$viPo = $root . '/lang/horse-tools-vi.po';
$added = horsetools_append_po( $viPo, $vi );
printf( "horse-tools-vi.po: %d entries appended\n", $added );

/**
 * How many plural forms each language actually has.
 *
 * This is not decoration. A .po here stores one translation per string, and
 * WordPress picks a translation by plural index: with the English default
 * `nplurals=2`, any `_n()` call with a count other than 1 asks for index 1,
 * finds nothing there, and falls back to the English plural. So `%d question
 * found` was translated into Vietnamese and displayed in English the moment
 * there were two questions.
 *
 * Vietnamese, Thai, Chinese and Indonesian mark no plural at all — one form is
 * the truthful answer for them, and it also happens to be the one that makes the
 * single stored translation the one that gets used. The rest keep the value
 * their grammar calls for; where nothing is translated the fallback to English
 * is the correct outcome anyway.
 */
$plural_forms = array(
    'horse-tools-vi'    => 'nplurals=1; plural=0;',
    'horse-tools-th'    => 'nplurals=1; plural=0;',
    'horse-tools-zh_CN' => 'nplurals=1; plural=0;',
    'horse-tools-ja'    => 'nplurals=1; plural=0;',
    'horse-tools-id_ID' => 'nplurals=1; plural=0;',
    'horse-tools-ru_RU' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<12 || n%100>14) ? 1 : 2);',
    'horse-tools-ar'    => 'nplurals=6; plural=(n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5);',
);

// Recompile every .po to its .mo so the shipped binaries match the sources.
foreach ( glob( $root . '/lang/*.po' ) as $po ) {
    $entries = horsetools_read_po( $po );
    $locale  = basename( $po, '.po' );
    $header  = "Project-Id-Version: Horse Tools\n"
        . "MIME-Version: 1.0\n"
        . "Content-Type: text/plain; charset=UTF-8\n"
        . "Content-Transfer-Encoding: 8bit\n"
        . 'Plural-Forms: ' . ( $plural_forms[ $locale ] ?? 'nplurals=2; plural=(n != 1);' ) . "\n"
        . "X-Domain: horse-tools\n";
    $count = horsetools_write_mo( $root . '/lang/' . $locale . '.mo', $entries, $header );
    printf( "%-28s compiled %d entries -> %s.mo\n", basename( $po ), $count, $locale );
}
