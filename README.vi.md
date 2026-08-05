# Horse Tools

**Ngôn ngữ:** [English](README.md) · **Tiếng Việt**

[![Bản phát hành](https://img.shields.io/github/v/release/tranduythuan/Horse-Tools?label=release)](https://github.com/tranduythuan/Horse-Tools/releases)
[![Giấy phép: GPL v2+](https://img.shields.io/badge/license-GPLv2%2B-blue.svg)](license.txt)
![WordPress 6.0+](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)
![PHP 8.1+](https://img.shields.io/badge/PHP-8.1%2B-777bb4.svg)

Bộ công cụ WordPress tất-cả-trong-một: nút chat liên hệ, shortcode & snippet, tăng cường bảo mật, riêng tư, tối ưu ảnh, SEO, dọn dẹp và hơn thế nữa — gói gọn trong **một plugin** thay vì cả chục plugin rời rạc.

Horse Tools là bản fork được **duy trì tích cực và tăng cường bảo mật** từ **Foxtool** của Fox Theme (xem [Ghi công](#ghi-công)).

---

## Tính năng

- **Nút chat liên hệ** — widget liên hệ đa kênh nổi với 29 kênh dựng sẵn (điện thoại, SMS, email, Zalo, Messenger, Telegram, WhatsApp, Viber, Line, WeChat, Signal, Shopee, Grab, Instagram, Facebook, Threads, TikTok, YouTube, X, Pinterest, LinkedIn, Reddit, Twitch, Spotify, VK, Discord, Google, Bản đồ…) cùng nút Custom trỏ tới đâu cũng được, 17 kiểu nút, bộ chọn icon tích hợp (hơn 5.000 icon Tabler), giờ làm việc (đang trực tuyến / ngoài giờ), bong bóng chào, tin nhắn soạn sẵn, và mã QR quét-để-mở trên máy tính.
- **Panel Dịch vụ cho di động** — bảng trượt/toast hướng tới chuyển đổi, làm nổi bật các trang & ưu đãi chính, với nhiều bố cục, tông màu và kiểu hiển thị.
- **Popup** — popup ảnh/nội dung với 4 bố cục, **13 hiệu ứng xuất hiện**, 4 vị trí hiển thị (giữa màn hình, toast ở góc, thanh dưới đáy) và 4 kiểu kích hoạt (ngay khi mở, sau vài giây, sau khi cuộn, hoặc khi khách sắp rời trang).
- **Shortcode & quản lý snippet** — tạo các đoạn nội dung/HTML/JS dùng lại (soạn bằng trình soạn thảo WordPress quen thuộc — tab Trực quan để chèn link/ảnh/định dạng, hoặc tab Văn bản để dán mã), hơn 20 shortcode dựng sẵn cho hiển thị theo điều kiện, bố cục (accordion, tab, cảnh báo), dữ liệu động và mã QR, công cụ "tìm shortcode đang dùng ở đâu", và bảng bật/tắt shortcode.
- **Tăng cường bảo mật** — giới hạn số lần đăng nhập, chặn dò tên người dùng, header bảo mật, tắt trình sửa file, kiểm soát REST/XML-RPC và dọn header.
- **Riêng tư** — tự host Google Fonts và quét các request ra ngoài ở giao diện người dùng.
- **Ảnh & media** — chuyển đổi WebP/AVIF, đóng dấu mờ, kiểm soát thumbnail.
- **SEO** — schema FAQ cho kết quả nổi bật, tự sinh từ chính các tiêu đề hỏi–đáp trong bài; mục lục tự động; tự tạo chuyển hướng 301 khi đổi đường dẫn kèm nhật ký 404; dọn đường dẫn tĩnh và alt ảnh; tích hợp Google Search Console / Indexing API.
- **Đo lượt bấm liên hệ** — ghi nhận khách có thật sự bấm nút liên hệ nào không (điện thoại, Zalo, Messenger, WhatsApp, Telegram, Viber…) dưới dạng sự kiện GA4, dùng luôn mã analytics site đã có. Không lưu gì trên site.
- **Tối ưu & sức khỏe site** — các tùy chọn tối ưu và kiểm tra sức khỏe site một chạm.
- **Dọn dẹp** — công cụ dọn cơ sở dữ liệu và nội dung.
- **Quản trị & đăng nhập** — tùy biến giao diện quản trị và bảng màu, cấu hình mẫu sẵn (Blog, WooCommerce, Hiệu năng, Bảo mật), và tùy biến trang đăng nhập WordPress.
- **Khác** — hỗ trợ WooCommerce, kiểm tra gửi mail, công cụ vai trò/quyền người dùng, tiện ích bài viết & trang, thông báo và popup.

## Các chức năng nằm ở đâu

Menu quản trị được sắp theo **việc bạn đang làm**, không theo module nào chứa
cài đặt đó. Bấm **Horse Tools** ở thanh bên WordPress sẽ mở trang **Tổng quan**
liệt kê các nhóm; mỗi nhóm là một trang có các tab ở trên và **một nút Lưu duy nhất**.

| Nhóm | Các tab |
| --- | --- |
| **Tốc độ** | Tối ưu · Ảnh · Dọn bài viết · Dọn bình luận · Dọn media · Lịch dọn dẹp |
| **SEO** | Liên kết & đường dẫn · Kết quả nổi bật (schema FAQ) · Chuyển hướng 301 · Liên kết hỏng 404 · Index now · Mục lục |
| **Bảo mật** | Bảo vệ · Trang đăng nhập · Bảo trì 503 |
| **Nội dung** | Bài viết · Khoá nội dung · Chữ ký · Shortcode ngày · Google fetch · Biểu tượng · Đoạn mã · Bảng |
| **Giao diện** | Website · Khu quản trị · Tìm kiếm · Phông chữ · Cài đặt phông |
| **Khách hàng** | Chat · WooCommerce · Chặn ad-block · Thanh thông báo · Popup · Thông báo cookie · Lượt bấm quảng cáo · AdSense · ads.txt · Đo lường |
| **Tài khoản & Email** | Người dùng · Email · Đăng nhập Google |
| **Công cụ** | Công cụ quản trị · Cài đặt plugin · CSS tuỳ chỉnh · Mã ở head/body/footer/đăng nhập · Debug · Sao lưu |

Bên dưới còn hai mục: **Tính năng mở rộng (Extend)** để bật/tắt các module tuỳ
chọn, và **Giới thiệu**.

Chỉ nhóm bạn mở mới được nạp, nên màn hình không bao giờ vào thì không tốn gì.
Các liên kết cũ tới trang riêng của từng module vẫn dùng được — chúng tự chuyển
hướng tới đúng nhóm và tab chứa cài đặt đó.

## Cài đặt

### Trong WordPress (khuyến nghị)

1. Tải bản `horse-tools-x.y.z.zip` mới nhất ở [trang Releases](https://github.com/tranduythuan/Horse-Tools/releases).
2. Trong WordPress: **Plugins → Cài mới → Tải plugin lên**, chọn file ZIP, rồi **Cài đặt**.
3. **Kích hoạt** Horse Tools. Khi cần cập nhật, tải file ZIP mới lên và chọn **"Thay thế bản hiện tại bằng bản tải lên"**.

### Thủ công

1. Giải nén và tải thư mục `horse-tools` vào `/wp-content/plugins/`.
2. Kích hoạt plugin trong menu **Plugins**.

> **Đang dùng Foxtool?** Horse Tools tự động nhập cài đặt của Foxtool trong lần kích hoạt đầu tiên. Sẽ không ghi đè nếu bạn đã có cài đặt Horse Tools.

**Yêu cầu:** WordPress 6.0+ · PHP 8.1+

## Ngôn ngữ

Giao diện dịch được hoàn toàn và đi kèm các bản dịch sẵn:

- **Tiếng Việt (vi)** — hoàn chỉnh, do tác giả duy trì.
- Indonesia, Thái, Trung (giản thể), Nhật, Hindi, Tây Ban Nha, Pháp, Đức, Bồ Đào Nha (Brazil), Nga, Ả Rập — bản dịch máy làm điểm khởi đầu; hoan nghênh góp ý cải thiện.

Plugin cũng tự nạp bản dịch đặt trong `wp-content/languages/plugins/`.

## Riêng tư

Horse Tools **không** gửi bất kỳ dữ liệu nào về site của bạn cho chúng tôi hay bên thứ ba. Plugin chỉ kết nối tới các dịch vụ ngoài mà **bạn tự cấu hình** (Google Indexing API / Search Console bằng tài khoản dịch vụ của bạn, đăng nhập Google bằng OAuth của bạn, hoặc thông báo Telegram bằng bot của bạn). Không có URL site, email quản trị, kiểm tra bản quyền hay thống kê sử dụng nào được gửi đi.

## Tài liệu & nhật ký thay đổi

- **Hướng dẫn sử dụng:** [Hướng dẫn nút liên hệ / Chat](docs/huong-dan-chat.md) · [English version](docs/chat-guide.md).
- Ghi chú tính năng đầy đủ và toàn bộ nhật ký thay đổi nằm trong [`readme.txt`](readme.txt) (định dạng WordPress.org).
- Tải bản phát hành và xem ghi chú: [Releases](https://github.com/tranduythuan/Horse-Tools/releases).

## Câu hỏi thường gặp (FAQ)

**Có miễn phí không?**
Có — miễn phí và mã nguồn mở theo giấy phép GPLv2.

**Tôi đang dùng Foxtool thì sao?**
Tắt Foxtool và kích hoạt Horse Tools; nó tự nhập cài đặt của Foxtool trong lần kích hoạt đầu. Đừng bật cả hai cùng lúc.

**Có làm chậm web không?**
Không. CSS/JS của mỗi tính năng chỉ tải trên trang có dùng, và không thêm gì ở giao diện người dùng cho tính năng bạn chưa bật.

**Plugin có gửi dữ liệu đi đâu không?**
Không — xem mục [Riêng tư](#riêng-tư). Chỉ kết nối tới dịch vụ mà bạn tự cấu hình bằng thông tin của mình.

**Cập nhật thế nào?**
Tải file ZIP mới ở [Releases](https://github.com/tranduythuan/Horse-Tools/releases), tải lên qua **Plugins → Cài mới → Tải plugin lên**, rồi chọn **“Thay thế bản hiện tại bằng bản tải lên”**. Cài đặt của bạn được giữ nguyên.

**Không thấy Popup (hoặc tính năng khác) ở đâu?**
Phần lớn module là tùy chọn. Hãy bật chúng ở trang **Tính năng mở rộng (Extend)** trước; tab của tính năng đó sẽ hiện ra trong đúng nhóm chứa nó — xem [Các chức năng nằm ở đâu](#các-chức-năng-nằm-ở-đâu). Trang Tổng quan cũng có ô tìm kiếm để tra cài đặt theo tên.

**Làm sao hiện popup khuyến mãi?**
Bật module **Notify** ở trang Extend, rồi vào **Khách hàng → Popup**: bật lên và chọn bố cục, hiệu ứng xuất hiện, vị trí và kiểu kích hoạt.

**Plugin có hỗ trợ SEO không?**
Có — nhóm **SEO** lo đường dẫn tĩnh, alt ảnh, liên kết ra ngoài, schema FAQ cho kết quả nổi bật, chuyển hướng 301 kèm nhật ký 404, Google Indexing API và mục lục.

**Dùng được tiếng của tôi không?**
Tiếng Việt hoàn chỉnh; 11 ngôn ngữ khác là bản dịch máy làm điểm khởi đầu. Bạn cũng có thể bỏ file `.mo` của mình vào `wp-content/languages/plugins/`.

**Báo lỗi hoặc góp ý tính năng ở đâu?**
Mở một [issue](https://github.com/tranduythuan/Horse-Tools/issues) trên GitHub.

## Đóng góp

Rất hoan nghênh issue và pull request — báo lỗi, sửa bản dịch, ý tưởng tính năng. Với thay đổi lớn, vui lòng mở [issue](https://github.com/tranduythuan/Horse-Tools/issues) để trao đổi trước.

## Ghi công

Horse Tools được phát triển và duy trì bởi **[Trần Duy Thuận](https://tranduythuan.com/)**.

Plugin khởi đầu là bản fork của **Foxtool** do **Fox Theme** phát hành theo GPLv2. Fox Theme đã xây dựng bộ tính năng gốc qua nhiều năm và đó vẫn là nền tảng của plugin này; xin ghi nhận công sức đó. Dự án gốc hiện tác giả không còn duy trì, và Horse Tools tiếp tục phát triển độc lập — đổi thương hiệu, tăng cường bảo mật, cập nhật thư viện phụ thuộc và dựng lại giao diện.

## Giấy phép

Phát hành theo giấy phép **GPLv2 hoặc mới hơn** — xem [`license.txt`](license.txt).
