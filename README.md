# Horse Tools

**Language:** **English** · [Tiếng Việt](README.vi.md)

[![Latest release](https://img.shields.io/github/v/release/tranduythuan/Horse-Tools?label=release)](https://github.com/tranduythuan/Horse-Tools/releases)
[![License: GPL v2+](https://img.shields.io/badge/license-GPLv2%2B-blue.svg)](license.txt)
![WordPress 6.0+](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)
![PHP 8.1+](https://img.shields.io/badge/PHP-8.1%2B-777bb4.svg)

All-in-one WordPress toolkit: a contact chat button, shortcodes & snippets, security hardening, privacy, media optimisation, SEO, cleanup and more — in one plugin, instead of a dozen separate ones.

Horse Tools is an actively-maintained, security-hardened fork of **Foxtool** by Fox Theme (see [Credits](#credits)).

---

## Features

- **Contact chat button** — a floating multi-channel contact widget with 11 built-in channel types (phone, SMS, e-mail, Zalo, Messenger, Telegram, WhatsApp, Viber, Skype, TikTok, Maps) plus a Custom button that points anywhere, 17 button skins, a built-in icon picker (5,000+ Tabler icons), business hours (online / away), a greeting bubble, pre-filled messages, and scan-to-open QR codes on desktop.
- **Mobile Services panel** — a conversion-focused slide-up/toast panel to surface your key pages and offers, with several layouts, themes and display modes.
- **Popup** — an image/content popup with 4 layouts, **13 entrance effects**, 4 display positions (centre, corner toast, bottom bar) and 4 triggers (on load, after N seconds, after scrolling, or exit-intent).
- **Shortcodes & snippet manager** — create reusable content/HTML/JS snippets (edited in the familiar WordPress Visual/Text editor — insert links, images and formatting, or paste raw code), 20+ built-in shortcodes for conditional display, layout (accordion, tabs, alerts), dynamic data and QR codes, a "find where a shortcode is used" tool, and an on/off manager.
- **Security hardening** — limit login attempts, block user enumeration, security response headers, disable the file editor, REST/XML-RPC controls and header cleanup.
- **Privacy** — self-host Google Fonts and scan the front end for external requests.
- **Media** — WebP/AVIF conversion, watermarking and thumbnail control.
- **SEO** — FAQ rich-result schema generated from the article's own Q&A headings, a table of contents, automatic 301 redirects on slug change with a 404 log, permalink and image-alt tidying, and Google Search Console / Indexing API integration.
- **Contact-click measurement** — records which contact buttons visitors actually press (phone, Zalo, Messenger, WhatsApp, Telegram, Viber…) as GA4 events, using whatever analytics tag the site already loads. Nothing is stored on the site.
- **Optimisation & health** — site optimisation options and a one-click site-health audit.
- **Cleanup** — database and content cleanup tools.
- **Admin & login** — customisable admin display and colour scheme, configuration presets (Blog, WooCommerce, Performance, Security), and a customisable WordPress login screen.
- **Extras** — WooCommerce helpers, mail testing, user role/permission tools, post & page utilities, notifications and popups.

## Where everything lives

The admin menu is organised by the job you are doing, not by which module a
setting happens to belong to. **Horse Tools** in the WordPress sidebar opens an
**Overview** page listing the groups; each group is one page with tabs across the
top and a single Save button.

| Group | Tabs |
| --- | --- |
| **Speed** | Optimisation · Images · Clean posts · Clean comments · Clean media · Cleanup schedule |
| **SEO** | Links & URLs · Rich results (FAQ schema) · Redirects 301 · Broken links 404 · Index now · Table of contents |
| **Security** | Protection · Login page · Maintenance 503 |
| **Content** | Posts · Lock content · Signature · Date shortcodes · Google fetch · Icons · Snippets · Tables |
| **Appearance** | Site · Admin area · Site search · Fonts · Font settings |
| **Customers** | Chat · WooCommerce · Ad-block notice · Notification bar · Popup · Cookie notice · Ad clicks · AdSense · ads.txt · Measurement |
| **Accounts & Email** | Users · Email · Google sign-in |
| **Tools** | Admin tools · Plugin settings · Custom CSS · Code in head/body/footer/login · Debug · Backup |

Two more entries sit below them: **Extend**, where optional modules are switched
on and off, and **About**.

Only the group you open is loaded, so a screen you never visit costs nothing.
Old bookmarks to the previous per-module pages still work — they redirect to the
group and tab that setting now lives on.

## Installation

### From within WordPress (recommended)

1. Download the latest `horse-tools-x.y.z.zip` from the [Releases page](https://github.com/tranduythuan/Horse-Tools/releases).
2. In WordPress: **Plugins → Add New → Upload Plugin**, choose the ZIP, then **Install Now**.
3. Activate **Horse Tools**. To update later, upload the newer ZIP and choose **Replace current with uploaded**.

### Manually

1. Unzip and upload the `horse-tools` folder to `/wp-content/plugins/`.
2. Activate the plugin from the **Plugins** menu.

> **Coming from Foxtool?** Horse Tools imports Foxtool's settings automatically the first time you activate it. Nothing is overwritten if Horse Tools settings already exist.

**Requirements:** WordPress 6.0+ · PHP 8.1+

## Languages

The interface is fully translatable and ships with bundled translations:

- **Vietnamese (vi)** — complete, maintained by the author.
- Indonesian, Thai, Chinese (Simplified), Japanese, Hindi, Spanish, French, German, Portuguese (Brazil), Russian, Arabic — machine-generated starting points; improvements welcome.

The plugin also loads any translation placed in `wp-content/languages/plugins/`.

## Privacy

Horse Tools does not send any data about your site to us or any third party. It only contacts external services you explicitly configure (Google Indexing API / Search Console with your own service account, Google login with your own OAuth client, or Telegram notifications with your own bot). No site URL, admin e-mail, licence check or usage statistic is transmitted.

## Documentation & changelog

- **User guides:** [Chat / contact button guide](docs/chat-guide.md) · [phiên bản Tiếng Việt](docs/huong-dan-chat.md).
- Full feature notes and the complete changelog live in [`readme.txt`](readme.txt) (WordPress.org format).
- Release downloads and notes: [Releases](https://github.com/tranduythuan/Horse-Tools/releases).

## FAQ

**Is it free?**
Yes — free and open-source under the GPLv2 licence.

**I currently use Foxtool. What do I do?**
Deactivate Foxtool and activate Horse Tools; it imports Foxtool's settings automatically on first activation. Don't run both at once.

**Will it slow my site down?**
No. Each feature's CSS/JS loads only on the pages that use it, and nothing is added to the front end for features you haven't switched on.

**Does it send my data anywhere?**
No — see [Privacy](#privacy). It only contacts services you explicitly configure with your own credentials.

**How do I update?**
Download the newer ZIP from [Releases](https://github.com/tranduythuan/Horse-Tools/releases) and upload it via **Plugins → Add New → Upload Plugin**, then choose **“Replace current with uploaded”**. Your settings are kept.

**I can't find the Popup (or another feature) — where is it?**
Most modules are opt-in. Turn them on under the **Extend** page first; the feature's tab then appears inside the group it belongs to — see [Where everything lives](#where-everything-lives). There is also a search box on the Overview page that finds a setting by name.

**How do I show a promotional popup?**
Enable the **Notify** module on the Extend page, then go to **Customers → Popup**: switch it on and choose a layout, entrance effect, position and trigger.

**Does the plugin do anything for SEO?**
Yes — the **SEO** group covers permalinks, image alt text, external links, FAQ rich results, 301 redirects with a 404 log, Google Indexing API and the table of contents.

**Can I use it in my language?**
Vietnamese is complete; 11 more languages ship as machine-translated starting points. You can also drop your own `.mo` into `wp-content/languages/plugins/`.

**Where do I report a bug or request a feature?**
Open a GitHub [issue](https://github.com/tranduythuan/Horse-Tools/issues).

## Contributing

Issues and pull requests are welcome — bug reports, translation fixes and feature ideas. Please open an [issue](https://github.com/tranduythuan/Horse-Tools/issues) to discuss larger changes first.

## Credits

Horse Tools is developed and maintained by **[Trần Duy Thuận](https://tranduythuan.com/)**.

It began as a fork of **Foxtool** by **Fox Theme**, released under GPLv2. Fox Theme built the original feature set over several years and it remains the foundation of this plugin; that work is gratefully acknowledged. The original project is no longer maintained by its author, and Horse Tools continues its development independently — rebranded, security-hardened, with dependencies refreshed and the interface rebuilt.

## Licence

Distributed under the **GPLv2 or later** licence — see [`license.txt`](license.txt).
