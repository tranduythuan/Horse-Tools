# Hướng dẫn sử dụng: Nút liên hệ / Chat nổi (Horse Tools)

Tài liệu này hướng dẫn chi tiết cách dùng **tab Chat** trong Horse Tools — nút liên hệ nổi trên máy tính, thanh liên hệ dưới đáy điện thoại, và panel “Dịch vụ”.

Vào **WordPress Admin → Horse Tools → Khách hàng → Chat** để cấu hình. (Nếu module đang tắt, bật nó ở **Horse Tools → Tính năng mở rộng** trước.)

## Mục lục
1. [Bắt đầu: bật module](#1-bắt-đầu-bật-module)
2. [Nút chat nổi (desktop)](#2-nút-chat-nổi-desktop)
   - [Chọn kiểu hiển thị (skin)](#21-chọn-kiểu-hiển-thị-skin)
   - [Thêm các kênh liên hệ](#22-thêm-các-kênh-liên-hệ)
   - [Tùy chỉnh giao diện nút](#23-tùy-chỉnh-giao-diện-nút)
   - [Mã QR quét bằng điện thoại](#24-mã-qr-quét-bằng-điện-thoại)
   - [Giờ làm việc (Online / Away)](#25-giờ-làm-việc-online--away)
   - [Bong bóng chào](#26-bong-bóng-chào)
   - [Tin nhắn soạn sẵn](#27-tin-nhắn-soạn-sẵn)
3. [Các skin đặc biệt: Avatar, Live-chat, Tab-widget](#3-các-skin-đặc-biệt)
4. [Thanh liên hệ trên điện thoại](#4-thanh-liên-hệ-trên-điện-thoại)
5. [Panel “Dịch vụ” (Services)](#5-panel-dịch-vụ-services)
6. [Mẹo & lỗi thường gặp](#6-mẹo--lỗi-thường-gặp)
7. [Khách có thật sự bấm nút không?](#7-khách-có-thật-sự-bấm-nút-không)

---

## 1. Bắt đầu: bật module

Chat có **hai phần độc lập** — bạn có thể bật một hoặc cả hai:

| Muốn hiện gì | Cần bật công tắc |
|---|---|
| Nút chat nổi ở góc màn hình (máy tính + điện thoại) | **Bật nút chat** (*Enable chat button*) |
| Thanh liên hệ dán đáy màn hình điện thoại | **Bật thanh liên hệ** (*Enable contact bar*) |

> ⚠️ **Quan trọng:** trên cùng tab có một công tắc **ON/OFF tổng** ở đầu — phải bật nó thì các mục bên dưới mới hoạt động. Sau đó bật tiếp *Enable chat button* và/hoặc *Enable contact bar*. Nếu không bật ít nhất một trong hai, plugin **không tải** CSS/JS chat ra front-end (để trang nhẹ).

---

## 2. Nút chat nổi (desktop)

### 2.1. Chọn kiểu hiển thị (skin)

Trong khu **“Kiểu hiển thị”**, bấm vào ô hình minh hoạ để chọn 1 trong **17 skin**:

| Skin | Trông như thế nào |
|---|---|
| **Default** | Bong bóng chat, bấm bung danh sách kênh theo cột |
| **Total** | Cột nút tròn kèm nhãn tên |
| **Effective** | Nút tròn có viền nổi |
| **Leaves** | Nút bo góc kiểu chiếc lá |
| **Floating** | Các nút tròn nổi rời rạc |
| **Tap** | Một nút, bấm mới bung ra |
| **Dock** | Khay ngang chứa icon |
| **Pill** | Dải “viên thuốc” ngang |
| **Glass** | Kính mờ bo tròn |
| **Tile** | Ô vuông |
| **Hexagon** | Lục giác |
| **Card** | Thẻ ở góc có tiêu đề + phụ đề theo giờ mở cửa |
| **Sidetab** | Tab dán mép màn hình, bấm mở panel |
| **Speeddial** | Nút tròn (FAB) xòe quạt các kênh |
| **Avatar** | Ảnh đại diện tròn + chấm “online” |
| **Live-chat** | Khung chat có lời chào + nút trả lời nhanh |
| **Tab-widget** | Thẻ nhiều tab: Liên hệ / Dịch vụ / Giờ mở cửa |

> Các skin **Avatar, Live-chat, Tab-widget** dùng thêm vài trường riêng (ảnh đại diện, lời chào, giờ mở cửa) — xem [mục 3](#3-các-skin-đặc-biệt).

### 2.2. Thêm các kênh liên hệ

Trong khu **“Cấu hình nút”**: bấm **“Thêm trường”** để thêm một kênh. Mỗi kênh có các ô:

1. **Loại kênh** — chọn từ danh sách (Phone, Zalo, Messenger…).
2. **Tên nút** — chữ hiện ra khi rê chuột (ví dụ “Gọi ngay”, “Chat Zalo”).
3. **Link / ID / số** — nội dung tuỳ kênh (xem bảng dưới).
4. **Icon** — *chỉ* dùng cho kênh **Custom** (dán mã SVG, hoặc bấm **“Chọn icon có sẵn”** để chèn một biểu tượng Tabler).

Kéo–thả các hàng để đổi thứ tự. Bấm dấu **✕** để xoá một kênh.

**Nhập gì cho mỗi kênh:**

| Kênh | Nhập vào ô “Link / ID / số” | Có QR? |
|---|---|---|
| Phone | Số điện thoại (vd `0901234567`) | – |
| SMS | Số điện thoại | – |
| Mail | Địa chỉ email | – |
| Zalo | Số điện thoại **hoặc** link Zalo/OA | ✅ |
| Messenger | Username trang **hoặc** link `m.me/...` | – |
| Telegram | Username **hoặc** link `t.me/...` | ✅ |
| WhatsApp | Số **dạng quốc tế** (vd `84901234567`) | ✅ |
| Viber | Số điện thoại | ✅ |
| Line | Handle **hoặc** link | ✅ |
| WeChat | **Link đầy đủ** | ✅ (luôn luôn) |
| Skype | Username Skype | – |
| TikTok | Username (không cần `@`) | – |
| Maps | **Link Google Maps đầy đủ** | – |
| Instagram, Facebook, X, YouTube, LinkedIn, Threads, Pinterest, Twitch, VK, Signal | Username/handle **hoặc** link đầy đủ | – |
| Discord, Google (đánh giá), Shopee, Grab, Reddit, Spotify | **Link đầy đủ** | – |
| **Custom** | **Link đầy đủ** bất kỳ (kèm icon SVG riêng) | – |

> 💡 **Quy tắc dễ nhớ:** với **Custom / Maps / WeChat / Discord / Google / Shopee / Grab** → dán **link đầy đủ**. Các kênh còn lại chỉ cần **số / username**, plugin tự ghép thành link đúng (tự bỏ ký tự `@`, `/`, `~` thừa ở đầu). Nếu lỡ dán nguyên link đầy đủ thì cũng vẫn chạy.

### 2.3. Tùy chỉnh giao diện nút

Trong khu **“Tùy chỉnh”**:

| Tùy chọn | Ý nghĩa | Giá trị |
|---|---|---|
| **Vị trí** | Đặt nút ở góc **Trái** hay **Phải** màn hình | Left / Right |
| **Khoảng cách đáy** | Cách đáy màn hình bao nhiêu px | 10–300 (mặc định 10). Đặt ≥300 → nút nằm **giữa** theo chiều dọc |
| **Khoảng cách mép** | Cách mép trái/phải bao nhiêu px | 10–100 (mặc định 10) |
| **Độ mờ** | Độ trong suốt của nút | 0–1 (mặc định 1 = đục hẳn) |
| **Bo góc** | Độ bo tròn của nút | 1–50 px (mặc định 50 = tròn) |
| **Màu nút** / **Màu icon** | Bảng chọn màu | – |
| **Icon nút** | Chọn 1 trong 5 biểu tượng bong bóng | Icon1–5 |
| **Mở tab mới** | Bấm kênh sẽ mở tab mới (`target="_blank"`) | Bật/tắt |
| **Ẩn khi cuộn xuống** | Nút tự ẩn khi kéo trang xuống, hiện lại khi kéo lên | Bật/tắt |

> ⚠️ Phải **chọn Vị trí (Left/Right)** thì các tinh chỉnh khoảng cách / độ mờ / bo góc mới được áp dụng.

### 2.4. Mã QR quét bằng điện thoại

Bật **“Hiện QR để quét trên máy tính”** (*Show a scan-to-open QR on desktop*).

- Khi khách **trên máy tính** bấm vào kênh hỗ trợ QR (Zalo, WhatsApp, Telegram, Viber, Line, WeChat), plugin hiện **mã QR** để khách quét bằng điện thoại → mở thẳng app chat.
- **Trên điện thoại**, các kênh này mở app trực tiếp (không hiện QR) — **trừ WeChat luôn hiện QR** ở mọi thiết bị (vì WeChat không mở được bằng link thường).

### 2.5. Giờ làm việc (Online / Away)

Bật **“Giờ làm việc”** (*Business hours*) để hiện trạng thái **Online / Away** trên các skin Live-chat, Card, Avatar và panel Dịch vụ.

- **Giờ mở** / **Giờ đóng** — chọn giờ (ô chọn giờ).
- **Các ngày** — tick những ngày trong tuần đang hoạt động.

Cách hoạt động: nếu **hôm nay** nằm trong ngày đã tick **và** giờ hiện tại (theo giờ của website) nằm trong khoảng mở–đóng → hiển thị **“Online now”**; ngược lại **“Away — leave a message”**.

- Hỗ trợ **khung qua đêm** (ví dụ mở 20:00, đóng 02:00).
- Để **trống giờ** → coi như mở **cả ngày** trong những ngày đã tick.
- Không bật giờ làm việc → luôn hiển thị Online.

### 2.6. Bong bóng chào

Bật **“Bong bóng chào”** (*Greeting bubble*) để hiện một lời mời chat nhỏ cạnh nút:

- **Nội dung** — ví dụ “Chào bạn! Cần hỗ trợ gì không?”.
- **Trễ (giây)** — chờ mấy giây rồi mới hiện (0–60, mặc định 3).

Khách bấm **✕** để tắt; sau khi tắt, bong bóng **không hiện lại** (ghi nhớ trong trình duyệt của khách).

### 2.7. Tin nhắn soạn sẵn

Ô **“Tin nhắn”** (*Message*): nội dung điền sẵn khi khách bấm **WhatsApp** hoặc **SMS** (khung soạn tin đã có sẵn chữ này). Chỉ áp dụng 2 kênh đó.

---

## 3. Các skin đặc biệt

Ba skin dưới dùng thêm các trường riêng (ở cuối khu cấu hình):

**Avatar** & **Live-chat**
- **Ảnh đại diện** — dán URL ảnh (khuôn mặt tư vấn viên / logo). Bỏ trống thì dùng icon mặc định.

**Live-chat** (khung chat có lời chào)
- **Lời chào** — ví dụ “Xin chào 👋 Bạn cần hỗ trợ gì?”.
- **Trả lời nhanh (Quick replies)** — mỗi dòng là một nút bấm nhanh, viết theo dạng:
  ```
  Nhãn nút|https://link-đích
  ```
  Ví dụ:
  ```
  Bảng giá|https://shop.com/bang-gia
  Đặt hàng|https://shop.com/dat-hang
  Zalo tư vấn|https://zalo.me/0901234567
  ```
  Dòng trống hoặc thiếu nhãn sẽ bị bỏ qua.

**Tab-widget** (thẻ nhiều tab)
- **Giờ mở cửa (dạng text)** — mỗi dòng một ngày, hiển thị nguyên văn trong tab “Giờ mở cửa”. Ví dụ:
  ```
  Thứ 2 – Thứ 6: 8:00 – 21:00
  Thứ 7 – CN: 9:00 – 18:00
  ```
  (Đây chỉ là **văn bản hiển thị**, khác với “Giờ làm việc” ở [mục 2.5](#25-giờ-làm-việc-online--away) vốn tự tính Online/Away.)

---

## 4. Thanh liên hệ trên điện thoại

Khu **“Thanh liên hệ trên điện thoại”** — dải nút dán đáy màn hình điện thoại.

**Bật & kiểu:**
- Bật **“Bật thanh liên hệ”** (*Enable contact bar*).
- **Kiểu thanh** — chọn 1 trong 5: **Default** (nút giữa nổi), **Simple** (mảnh, tối giản), **Docky** (nổi bo tròn cách mép), **Momo** (nút giữa lớn nổi cao), **Lom** (khuyết cong ôm nút giữa).
- **Ẩn khi kéo xuống** — thanh tự ẩn khi cuộn xuống.
- **Hiện cả trên tablet** — mặc định chỉ hiện ở màn hình ≤700px; bật cái này để hiện tới 1024px.

**Nút chính (ở giữa):** có 4 ô — SVG icon, tên, link, và “#id/.class” (mục tiêu). 
- Nếu **để trống link** của nút giữa → bấm vào nó sẽ **mở danh sách kênh chat**.

**4 nút tùy biến:** mỗi nút có SVG icon, tên, link, và ô “#id/.class”. Kéo–thả để sắp xếp.

**Gắn menu WordPress vào thanh:**
1. Vào **Giao diện → Menu**, tạo menu, tick vị trí **“Navigation bar (Horse Tools)”**.
2. Ở một nút trên thanh: điền `#horsenavi` vào ô **“#id/.class”**, và điền `#` vào ô **link**. Bấm nút đó sẽ mở menu.

**Mở panel Dịch vụ từ thanh:** điền `#ht-services` vào ô “#id/.class” của một nút (xem [mục 5](#5-panel-dịch-vụ-services)).

**Ẩn thanh ở một số trang:** ô **“Ẩn ở trang”** — mỗi dòng một slug trang; thanh sẽ không hiện trên các trang đó.

**Màu sắc thanh:** có 6 ô màu — màu nổi bật, màu icon chính, màu chữ icon chính, màu nền thanh, màu nền khung chat, màu chữ khung chat.

> 💡 Nếu bật **cả** nút nổi lẫn thanh mobile: trên điện thoại nút nổi sẽ **tự ẩn** để nhường chỗ cho thanh (khách vẫn vào được các kênh chat qua nút giữa của thanh).

---

## 5. Panel “Dịch vụ” (Services)

Panel trượt lên giới thiệu dịch vụ / khuyến mãi — bấm một nút để mở.

**Bật & cấu hình chung:**
- Tick **“Bật panel dịch vụ”**.
- **Tiêu đề panel** — ví dụ “Dịch vụ của chúng tôi”.
- **Bố cục (13 kiểu):** `bento` (1 ô lớn + ô nhỏ), `grid` (lưới thẻ), `list` (danh sách), `tiles` (ô icon gọn), `chips` (chip nhanh), `story` (vòng tròn kiểu story), `coupon` (mã giảm giá có nút copy), `stacked` (banner xếp chồng), `banner` (hero + lưới), `pricecards` (bảng giá + nút Đặt), `reviews` (đánh giá sao + avatar), `video` (thẻ video có nút play), `masonry` (lưới ảnh so le).
- **Màu chủ đề (7):** gold, blue, green, red, purple, dark, neutral.
- **Kiểu hiển thị (7):** `auto` (ĐT: trượt từ đáy, PC: hộp giữa), `sheet` (trượt từ đáy), `modal` (giữa màn hình), `drawer-right`, `drawer-left`, `corner` (góc), `fullscreen` (toàn màn hình).

**Thêm dịch vụ** — bấm **“Thêm dịch vụ”**, mỗi mục gồm:
- **Tiêu đề** (bắt buộc để không bị bỏ qua)
- **Icon** — tên icon Tabler (vd `snowflake`), có nút chọn icon; hoặc để trống nếu dùng ảnh
- **Ảnh** — URL ảnh (nếu có sẽ thay cho icon)
- **Phụ đề / giá / mã** — dòng mô tả nhỏ
- **Link** — trỏ tới bài viết / trang dịch vụ
- **Nhãn** + **Màu nhãn** — huy hiệu góc: đỏ = HOT, cam = SALE, xanh lá = MỚI, hoặc blue/gold/dark

**Nút “Dịch vụ” nổi trên desktop:** tick **“Nút dịch vụ nổi”** để hiện một nút riêng mở panel trên máy tính — đặt **chữ** và **icon** (tên Tabler, vd `apps`) cho nút.

**Cách mở panel:**
- Trên điện thoại: đặt ô “#id/.class” của một nút thanh = `#ht-services`.
- Trên máy tính: bật “Nút dịch vụ nổi”.

> ⚠️ **Panel Dịch vụ có nút “Lưu dịch vụ” (Save services) RIÊNG.** Cấu hình xong phần Dịch vụ nhớ bấm nút này (khác với nút “Lưu” chung của cả trang). Panel chỉ hiện ra front-end khi đã **bật** và có **ít nhất một dịch vụ**.

---

## 6. Mẹo & lỗi thường gặp

- **Bấm chọn kênh nhưng không hiện gì:** kiểm tra đã bật **công tắc tổng** + **Enable chat button** chưa; và đã **thêm ít nhất một kênh** chưa.
- **Sửa nút giao diện không ăn:** phải chọn **Vị trí Left/Right** thì các tinh chỉnh px/màu/độ mờ mới áp dụng.
- **Panel Dịch vụ không hiện:** đảm bảo đã bấm **“Lưu dịch vụ”** riêng, đã bật panel, và có ≥1 dịch vụ; và đã gắn `#ht-services` vào một nút (mobile) hoặc bật nút nổi (desktop).
- **WeChat luôn hiện QR** kể cả trên điện thoại — đúng thiết kế (WeChat không mở bằng link thường).
- **Chọn icon:** ở ô SVG (kênh Custom, nút thanh) và ô icon Dịch vụ đều có nút mở kho **hơn 5.000 icon Tabler**, tìm được bằng cả **tên tiếng Anh lẫn từ khoá tiếng Việt** (vd gõ “gio hang” ra icon giỏ hàng). Bấm “Load more” để tải thêm.
- **Số WhatsApp/Viber** nên nhập **dạng quốc tế** (vd `84901234567`, bỏ số 0 đầu) để mở đúng.

---

## 7. Khách có thật sự bấm nút không?

**Horse Tools → Khách hàng → Đo lường** trả lời câu đó. Bật lên là mỗi lượt bấm
vào một liên kết liên hệ — nút nổi, thanh dưới đáy điện thoại, chip trả lời nhanh,
kể cả số điện thoại bạn dán thẳng vào bài viết — đều được báo về Google Analytics 4
dưới dạng sự kiện đặt tên theo kênh: `contact_phone`, `contact_zalo`,
`contact_messenger`, `contact_whatsapp`, `contact_telegram`, `contact_viber`,
`contact_sms`, `contact_email`.

- **Không lưu gì trên site của bạn.** Lượt bấm được đẩy thẳng sang mã analytics
  site đã có (GA4 qua `gtag`, hoặc Tag Manager) rồi thôi — không sinh thêm dữ liệu
  phải giữ, không có gì phải khai trong chính sách riêng tư.
- **Site phải có sẵn mã analytics** — Site Kit, MonsterInsights, đoạn GA4 dán tay
  hay Tag Manager đều được. Màn hình này tự dò và báo cho bạn biết nó thấy gì.
- **Xem số ở đâu:** GA4 → *Báo cáo → Mức độ tương tác → Sự kiện*. Tên kênh nằm
  ngay trong **tên sự kiện** nên hiện luôn ở báo cáo chuẩn, không phải khai báo
  thứ nguyên tuỳ chỉnh nào.
- **Chỉ có Tag Manager?** GTM cho bạn `dataLayer` chứ không có `gtag`, nên bạn
  phải tạo một thẻ sự kiện GA4 với trigger Custom Event khớp `contact_.*` (dùng
  kiểu so khớp regex). Chưa làm bước này thì dữ liệu nằm im trong dataLayer.
- **Vẫn phân tách được nguồn.** Sự kiện mang theo source / medium / campaign của
  phiên, nên báo cáo *Thu nạp lưu lượng* sẽ cho biết cuộc gọi đến từ Google Ads,
  tìm kiếm tự nhiên hay Facebook. Lượt từ Google Ads được nhận bằng `gclid` và
  hiện là *Google Ads* chứ không phải *google / organic*.
- Mỗi sự kiện còn kèm `placement` (`floating`, `navbar`, `chatbox`, `services`,
  `quickreply`, `content`) và một `label` ngắn — tiện để biết thanh dưới đáy có
  đáng giá không, hay nút nổi mới là cái gánh hết.

*Tài liệu cho Horse Tools — module Chat. Nhãn trong ảnh chụp có thể là tiếng Anh nếu website đang để ngôn ngữ tiếng Anh; ý nghĩa từng mục vẫn như mô tả ở trên. Xem thêm: [English guide](chat-guide.md).*
