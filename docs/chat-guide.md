# User Guide: Floating Contact / Chat Button (Horse Tools)

This guide explains how to use the **CHAT tab** in Horse Tools — the floating contact button on desktop, the contact bar pinned to the bottom of mobile screens, and the “Services” panel.

Go to **WordPress Admin → Horse Tools → CHAT tab** to configure everything.

## Contents
1. [Getting started: turn the module on](#1-getting-started-turn-the-module-on)
2. [Floating chat button (desktop)](#2-floating-chat-button-desktop)
   - [Choose a display style (skin)](#21-choose-a-display-style-skin)
   - [Add contact channels](#22-add-contact-channels)
   - [Customize the button](#23-customize-the-button)
   - [Scan-to-open QR code](#24-scan-to-open-qr-code)
   - [Business hours (Online / Away)](#25-business-hours-online--away)
   - [Greeting bubble](#26-greeting-bubble)
   - [Pre-filled message](#27-pre-filled-message)
3. [Special skins: Avatar, Live-chat, Tab-widget](#3-special-skins)
4. [Contact bar on mobile](#4-contact-bar-on-mobile)
5. [The “Services” panel](#5-the-services-panel)
6. [Tips & common issues](#6-tips--common-issues)

---

## 1. Getting started: turn the module on

Chat has **two independent parts** — you can enable one or both:

| What you want to show | Switch to enable |
|---|---|
| A floating button in the corner (desktop + mobile) | **Enable chat button** |
| A contact bar pinned to the bottom of mobile screens | **Enable contact bar** |

> ⚠️ **Important:** there is a **master ON/OFF switch** at the top of the tab — turn it on first, then enable *Enable chat button* and/or *Enable contact bar*. If neither is on, the plugin **does not load** any chat CSS/JS on the front-end (to keep pages light).

---

## 2. Floating chat button (desktop)

### 2.1. Choose a display style (skin)

In the **“Display options”** area, click a preview tile to pick one of **17 skins**:

| Skin | What it looks like |
|---|---|
| **Default** | A chat bubble that expands the channels in a column |
| **Total** | Column of round buttons with name labels |
| **Effective** | Round buttons with a raised border |
| **Leaves** | Leaf-shaped rounded buttons |
| **Floating** | Separate floating round buttons |
| **Tap** | A single button that expands on click |
| **Dock** | A horizontal tray of icons |
| **Pill** | A horizontal “pill” strip |
| **Glass** | Frosted-glass rounded look |
| **Tile** | Square tiles |
| **Hexagon** | Hexagon buttons |
| **Card** | A corner card with a title + a subtitle driven by business hours |
| **Sidetab** | A tab stuck to the screen edge that opens a panel |
| **Speeddial** | A round FAB that fans the channels out |
| **Avatar** | A round avatar photo with an “online” dot |
| **Live-chat** | A chat panel with a greeting + quick-reply buttons |
| **Tab-widget** | A card with tabs: Contact / Services / Hours |

> The **Avatar, Live-chat and Tab-widget** skins use a few extra fields (avatar, greeting, opening hours) — see [section 3](#3-special-skins).

### 2.2. Add contact channels

In the **“Configure buttons”** area: click **“Add field”** to add a channel. Each channel has these boxes:

1. **Channel type** — pick from the list (Phone, Zalo, Messenger…).
2. **Button name** — the label shown on hover (e.g. “Call now”, “Chat on Zalo”).
3. **Link / ID / number** — depends on the channel (see table below).
4. **Icon** — *only* used for the **Custom** channel (paste SVG markup, or click **“Choose a built-in icon”** to insert a Tabler icon).

Drag rows to reorder them. Click **✕** to remove a channel.

**What to enter per channel:**

| Channel | Put this in “Link / ID / number” | QR? |
|---|---|---|
| Phone | Phone number (e.g. `0901234567`) | – |
| SMS | Phone number | – |
| Mail | Email address | – |
| Zalo | Phone number **or** a Zalo/OA link | ✅ |
| Messenger | Page username **or** an `m.me/...` link | – |
| Telegram | Username **or** a `t.me/...` link | ✅ |
| WhatsApp | Number in **international format** (e.g. `84901234567`) | ✅ |
| Viber | Phone number | ✅ |
| Line | Handle **or** a link | ✅ |
| WeChat | **Full link** | ✅ (always) |
| Skype | Skype username | – |
| TikTok | Username (no `@` needed) | – |
| Maps | **Full Google Maps link** | – |
| Instagram, Facebook, X, YouTube, LinkedIn, Threads, Pinterest, Twitch, VK, Signal | Username/handle **or** a full link | – |
| Discord, Google (reviews), Shopee, Grab, Reddit, Spotify | **Full link** | – |
| **Custom** | **Any full link** (with your own SVG icon) | – |

> 💡 **Rule of thumb:** for **Custom / Maps / WeChat / Discord / Google / Shopee / Grab** → paste the **full link**. For the rest, just the **number / username** is enough — the plugin builds the correct link for you (and strips a leading `@`, `/` or `~`). Pasting a full link also works.

### 2.3. Customize the button

In the **“Customize”** area:

| Option | Meaning | Value |
|---|---|---|
| **Position** | Put the button in the **Left** or **Right** corner | Left / Right |
| **Bottom offset** | Distance from the bottom of the screen, in px | 10–300 (default 10). Set ≥300 → the button sits **vertically centered** |
| **Side offset** | Distance from the left/right edge, in px | 10–100 (default 10) |
| **Opacity** | Transparency of the button | 0–1 (default 1 = solid) |
| **Border radius** | How rounded the button is | 1–50 px (default 50 = round) |
| **Button color** / **Icon color** | Color pickers | – |
| **Button icon** | Pick one of 5 chat-bubble icons | Icon1–5 |
| **Open in a new tab** | Channels open in a new tab (`target="_blank"`) | On/off |
| **Hide when scrolling down** | The button hides as you scroll down, reappears when scrolling up | On/off |

> ⚠️ You must **choose a Position (Left/Right)** for the offset / opacity / radius tweaks to take effect.

### 2.4. Scan-to-open QR code

Enable **“Show a scan-to-open QR on desktop”**.

- When a visitor **on desktop** clicks a QR-capable channel (Zalo, WhatsApp, Telegram, Viber, Line, WeChat), the plugin shows a **QR code** they can scan with their phone to open the chat app directly.
- **On mobile**, these channels open the app directly (no QR) — **except WeChat, which always shows a QR** on every device (WeChat can’t be opened by a normal link).

### 2.5. Business hours (Online / Away)

Enable **“Business hours”** to show an **Online / Away** status on the Live-chat, Card, Avatar skins and the Services panel.

- **Open time** / **Close time** — pick the times.
- **Days** — tick the weekdays you’re open.

How it works: if **today** is a ticked day **and** the current time (in the website’s timezone) is within the open–close window → it shows **“Online now”**; otherwise **“Away — leave a message”**.

- **Overnight windows** are supported (e.g. open 20:00, close 02:00).
- Leave the **times empty** → treated as open **all day** on the ticked days.
- With business hours off → it always shows Online.

### 2.6. Greeting bubble

Enable **“Greeting bubble”** to show a small chat invitation next to the button:

- **Text** — e.g. “Hi! Need any help?”.
- **Delay (seconds)** — wait this many seconds before showing (0–60, default 3).

Visitors click **✕** to dismiss it; once dismissed it **won’t show again** (remembered in the visitor’s browser).

### 2.7. Pre-filled message

The **“Message”** box: text that is pre-filled when a visitor taps **WhatsApp** or **SMS** (the compose box already contains this text). It applies to those two channels only.

---

## 3. Special skins

Three skins use extra fields (at the bottom of the configuration area):

**Avatar** & **Live-chat**
- **Avatar** — paste an image URL (agent’s face / logo). Leave blank to use the default icon.

**Live-chat** (a chat panel with a greeting)
- **Greeting** — e.g. “Hi 👋 How can we help you?”.
- **Quick replies** — each line becomes a quick-tap button, written as:
  ```
  Label|https://target-link
  ```
  Example:
  ```
  Pricing|https://shop.com/pricing
  Order now|https://shop.com/order
  Zalo support|https://zalo.me/0901234567
  ```
  Empty lines or lines without a label are skipped.

**Tab-widget** (a multi-tab card)
- **Opening hours (as text)** — one day per line, shown verbatim in the “Hours” tab. Example:
  ```
  Mon – Fri: 8:00 – 21:00
  Sat – Sun: 9:00 – 18:00
  ```
  (This is **display text only** — different from “Business hours” in [section 2.5](#25-business-hours-online--away), which actually computes Online/Away.)

---

## 4. Contact bar on mobile

The **“Contact bar on mobile”** area — a strip of buttons pinned to the bottom of mobile screens.

**Enable & style:**
- Turn on **“Enable contact bar”**.
- **Bar style** — pick one of 5: **Default** (raised center button), **Simple** (thin, minimal), **Docky** (floating, rounded, inset from the edges), **Momo** (large raised center button), **Lom** (a curved notch hugging the center button).
- **Hide when scrolling down** — the bar hides as you scroll down.
- **Show on tablets too** — by default it only shows on screens ≤700px; enable this to show up to 1024px.

**Center button:** has 4 boxes — SVG icon, name, link, and “#id/.class” (target).
- If you **leave the center button’s link empty** → tapping it **opens the list of chat channels**.

**4 custom buttons:** each has an SVG icon, name, link, and “#id/.class” box. Drag to reorder.

**Attach a WordPress menu to the bar:**
1. Go to **Appearance → Menus**, create a menu, tick the location **“Navigation bar (Horse Tools)”**.
2. On one bar button: put `#horsenavi` in the **“#id/.class”** box, and `#` in the **link** box. Tapping that button opens the menu.

**Open the Services panel from the bar:** put `#ht-services` in a button’s “#id/.class” box (see [section 5](#5-the-services-panel)).

**Hide the bar on some pages:** the **“Hide on page”** box — one page slug per line; the bar won’t show on those pages.

**Bar colors:** there are 6 color boxes — accent color, main icon color, main icon text color, bar background, chat-box background, chat-box text.

> 💡 If you enable **both** the floating button and the mobile bar: on phones the floating button **hides itself** to make room for the bar (visitors still reach the chat channels via the bar’s center button).

---

## 5. The “Services” panel

A slide-up panel that showcases your services / promotions — tap a button to open it.

**Enable & general setup:**
- Tick **“Enable the services panel”**.
- **Panel title** — e.g. “Our services”.
- **Layout (13 styles):** `bento` (one large + small tiles), `grid` (card grid), `list`, `tiles` (compact icon tiles), `chips` (quick chips), `story` (story-style circles), `coupon` (discount codes with a copy button), `stacked` (stacked banners), `banner` (hero + grid), `pricecards` (prices + an Order button), `reviews` (star ratings + avatars), `video` (video cards with a play button), `masonry` (staggered image grid).
- **Theme color (7):** gold, blue, green, red, purple, dark, neutral.
- **Display mode (7):** `auto` (phone: slide-up sheet, desktop: centered modal), `sheet` (slide up from the bottom), `modal` (center), `drawer-right`, `drawer-left`, `corner`, `fullscreen`.

**Add a service** — click **“Add service”**; each item has:
- **Title** (required, otherwise the item is skipped)
- **Icon** — a Tabler icon name (e.g. `snowflake`), with an icon-picker button; or leave blank to use an image
- **Image** — image URL (replaces the icon if set)
- **Subtitle / price / code** — a small description line
- **Link** — to a post / service page
- **Badge** + **Badge color** — a corner badge: red = HOT, amber = SALE, green = NEW, or blue/gold/dark

**Floating “Services” button on desktop:** tick **“Services launcher”** to show a dedicated button that opens the panel on desktop — set its **text** and **icon** (a Tabler name, e.g. `apps`).

**How to open the panel:**
- On mobile: set a bar button’s “#id/.class” to `#ht-services`.
- On desktop: enable the “Services launcher”.

> ⚠️ **The Services panel has its own “Save services” button.** After configuring the Services section, click that button (it’s separate from the page’s main “Save”). The panel only appears on the front-end when it is **enabled** and has **at least one service**.

---

## 6. Tips & common issues

- **Clicking a channel shows nothing:** check that the **master switch** + **Enable chat button** are on, and that you’ve **added at least one channel**.
- **Button styling has no effect:** you must pick a **Position (Left/Right)** for the px/color/opacity tweaks to apply.
- **Services panel doesn’t show:** make sure you clicked the separate **“Save services”**, enabled the panel, and added ≥1 service; and that you attached `#ht-services` to a button (mobile) or enabled the launcher (desktop).
- **WeChat always shows a QR**, even on phones — this is intentional (WeChat can’t be opened by a normal link).
- **Choosing an icon:** the SVG box (Custom channel, bar buttons) and the Services icon box both have a picker for **5,000+ Tabler icons**, searchable by **English names or Vietnamese keywords** (e.g. typing “gio hang” finds the shopping-cart icon). Click “Load more” to load more.
- **WhatsApp/Viber numbers** should be in **international format** (e.g. `84901234567`, drop the leading 0) so they open correctly.

---

*Documentation for Horse Tools — Chat module. UI labels appear in Vietnamese when the site language is Vietnamese; the meaning of each option is the same as described above. See also: [Vietnamese guide](huong-dan-chat.md).*
