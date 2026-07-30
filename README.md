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

- **Contact chat button** — a floating multi-channel contact widget (Zalo, Messenger, WhatsApp, Telegram, phone, Line, WeChat, Instagram and 30+ more), 17 button skins, a built-in icon picker (5,000+ Tabler icons), business hours (online / away), a greeting bubble, pre-filled messages, and scan-to-open QR codes on desktop.
- **Mobile Services panel** — a conversion-focused slide-up/toast panel to surface your key pages and offers, with several layouts, themes and display modes.
- **Popup** — an image/content popup with 4 layouts, **13 entrance effects**, 4 display positions (centre, corner toast, bottom bar) and 4 triggers (on load, after N seconds, after scrolling, or exit-intent).
- **Shortcodes & snippet manager** — create reusable content/HTML/JS snippets (edited in the familiar WordPress Visual/Text editor — insert links, images and formatting, or paste raw code), 20+ built-in shortcodes for conditional display, layout (accordion, tabs, alerts), dynamic data and QR codes, a "find where a shortcode is used" tool, and an on/off manager.
- **Security hardening** — limit login attempts, block user enumeration, security response headers, disable the file editor, REST/XML-RPC controls and header cleanup.
- **Privacy** — self-host Google Fonts and scan the front end for external requests.
- **Media** — WebP/AVIF conversion, watermarking and thumbnail control.
- **SEO & content** — table of contents, automatic 301 redirects on slug change with a 404 log, and Google Search Console / Indexing API integration.
- **Optimisation & health** — site optimisation options and a one-click site-health audit.
- **Cleanup** — database and content cleanup tools.
- **Admin & login** — customisable admin display and colour scheme, configuration presets (Blog, WooCommerce, Performance, Security), and a customisable WordPress login screen.
- **Extras** — WooCommerce helpers, mail testing, user role/permission tools, post & page utilities, notifications and popups.

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

- Full feature notes and the complete changelog live in [`readme.txt`](readme.txt) (WordPress.org format).
- Release downloads and notes: [Releases](https://github.com/tranduythuan/Horse-Tools/releases).

## Contributing

Issues and pull requests are welcome — bug reports, translation fixes and feature ideas. Please open an [issue](https://github.com/tranduythuan/Horse-Tools/issues) to discuss larger changes first.

## Credits

Horse Tools is developed and maintained by **[Trần Duy Thuận](https://tranduythuan.com/)**.

It began as a fork of **Foxtool** by **Fox Theme**, released under GPLv2. Fox Theme built the original feature set over several years and it remains the foundation of this plugin; that work is gratefully acknowledged. The original project is no longer maintained by its author, and Horse Tools continues its development independently — rebranded, security-hardened, with dependencies refreshed and the interface rebuilt.

## Licence

Distributed under the **GPLv2 or later** licence — see [`license.txt`](license.txt).
