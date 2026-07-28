=== Horse Tools ===
Contributors: tranduythuan
Author: Trần Duy Thuận
Author URI: https://tranduythuan.com/
Plugin URI: https://github.com/tranduythuan/Horse-Tools
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: all-in-one, tools, optimization, security, media
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.1
Stable tag: 1.0.0

All-in-one toolkit for managing a WordPress website: chat button, custom login, media optimisation, cleanup and more.

== Description ==

Horse Tools bundles the day-to-day tools a WordPress site owner actually needs into one plugin, instead of a dozen separate ones.

Features:

* Website optimisation
* Security hardening
* Database and content cleanup tools
* Customisable admin display and colour scheme
* Media management: WebP/AVIF conversion, watermarking, thumbnail control
* Post and page utilities
* Mail testing
* WooCommerce helpers
* User role and permission tools
* Customisable WordPress login screen
* Google Search Console / Indexing API integration
* Floating contact chat button
* Table of contents, redirects, notifications and shortcodes

== Privacy ==

Horse Tools does not send any data about your site to us or to any third party. It contacts external services only when you explicitly configure a feature that requires it:

* **Google Indexing API / Search Console** — only when you connect your own Google service account, and only to submit the URLs you request.
* **Google login** — only when you enable it and supply your own OAuth client.
* **Telegram notifications (WooCommerce)** — only when you supply your own bot token and chat ID.

No site URL, administrator e-mail, licence check or usage statistic is transmitted on activation, deactivation, or at any other time.

== Installation ==

= From within WordPress =

1. Visit 'Plugins > Add New'
2. Upload the plugin ZIP
3. Activate 'Horse Tools' from your Plugins page

= Manually =

1. Upload the 'horse-tools' folder to '/wp-content/plugins/'
2. Activate the 'Horse Tools' plugin through the 'Plugins' menu in WordPress

If you previously used Foxtool on this site, Horse Tools imports its settings automatically the first time you activate it. Nothing is overwritten if Horse Tools settings already exist.

== Support ==

Source code, issue tracker and releases: https://github.com/tranduythuan/Horse-Tools

== Credits ==

Horse Tools is developed and maintained by **Trần Duy Thuận** (https://tranduythuan.com/).

It began as a fork of **Foxtool** by **Fox Theme**, released under the GPLv2 licence. Fox Theme built the original feature set over several years and it remains the foundation of this plugin; that work is gratefully acknowledged here. The original project is no longer maintained by its author, and Horse Tools continues its development independently — rebranded, security-hardened, with its dependencies refreshed and its interface rebuilt.

Under the GPLv2, this fork is distributed under the same licence as the original. See the Changelog for the full list of what changed.

== Changelog ==

= 1.0.0 =
Initial release of Horse Tools, forked from Foxtool 2.5.3.

Features that were silently broken and now work:

* **SMTP mail.** The protocol dropdown offered `starttls`, which PHPMailer does not accept — it opened a plaintext socket, never issued STARTTLS, and authentication failed. It was also the first entry, so it was the pre-selected default. Any site that saved it had all outgoing mail silently stop. Existing values are migrated on read.
* **Post content was being corrupted on save.** With "Remove img tag attributes" on, the content filter wrote `<body>…your post…</body>` into `post_content` every time. Browsers ignore the stray tags, which is why nobody noticed, but excerpts, RSS and search all saw them and the block editor offered a content-recovery prompt.
* **"Add Classic Editor button" hijacked every save.** It called `wp_redirect()` and `exit()` from inside `save_post`, so every later `save_post` callback was skipped (third-party post meta silently vanished), `wp_after_insert_post()` never fired, and the "Post updated" notice disappeared. It now uses `redirect_post_location`.
* **Three cleanup tools always reported success.** Delete revisions, auto-drafts and trashed posts returned without sending a response, so the interface printed "deleted" whether it had removed four thousand rows or none. They also ran raw `DELETE FROM wp_posts`, orphaning post meta, term relationships and attachment files — a cleanup tool that made the database dirtier. They now use `wp_delete_post()` and report the real count.
* **"Delete comments containing links" could never finish, and deleted the wrong comments.** It re-queried the same 200 rows forever with a one-second sleep between passes until PHP timed out, and its match condition was "contains a link **or** the author filled in the Website field" — so it deleted most legitimate comments on most sites. It now pages properly and matches the comment body only.
* **The type-to-confirm prompt was impossible to pass.** It asked you to type `horsetools` and compared against `horse-tools`.
* **"Disable other data sources" left the main feed open.** It hooked every feed type except `do_feed_rss2` — the default — so `/feed/` and `/comments/feed/` kept serving. Three of the six hooks it did register do not exist in WordPress. It also returned HTTP 500; it now returns 410.
* **"Disable Emoji" stopped removing the emoji stylesheet** when WordPress 6.4 moved it to `wp_enqueue_emoji_styles()`.
* **"Remove protocols from all URLs" never ran** unless "Remove relative domain" was also on, and emitted a PHP warning on every page otherwise.
* **Ad-blocker detection injected a `<div>` into `<head>`**, which closes `</head>` early — so every Open Graph tag, canonical link and JSON-LD block printed by any other plugin at a later priority ended up in the body where crawlers do not look.
* **The table of contents rendered on archives, search results and inside RSS items**, producing duplicate DOM ids so the widget listed the first post's headings for the whole page. Same fix applied to the Fancybox wrapper.
* **TOC anchors dropped every digit and every `Đ`.** "Đường" became `uong`, "Bước 2" and "Bước 3" collided, and a heading of only digits or CJK produced `href="#"` — which made the click handler throw and stop working entirely.
* **Comment-reply notifications** fired on every top-level comment against a null parent, emailed people their own replies, and notified post authors about spam.
* **The popup's "show again after N hours" setting was ignored** when the visitor closed it with the X, because the close button does not exist yet at the moment the handler was bound.
* **Telegram order notifications** used `file_get_contents()` on a remote URL, so on any host with `allow_url_fopen` disabled they silently vanished — and a slow response blocked the customer's checkout.
* **"The original image size when added to the post"** ran `update_option()` on every request, front end included, and its off state permanently overwrote the site's own Settings → Media choice.
* Google reCAPTCHA v3 now has a configurable score threshold instead of a hard-coded 0.5.

Crashes fixed:

* Every image routine was raw GD with no availability check, so the first upload on an Imagick-only host — common on managed WordPress — was a fatal error. Choosing a JPG watermark logo was also a guaranteed fatal on every upload, because the code called `imagecreatefrompng()` on it while the interface said "PNG, JPG".
* The thumbnail cleaner loaded every attachment in the media library as a full post object before applying its own 500-item limit.

Performance:

* The admin screens — 17 files, about 162 KB of PHP — were included on every front-end request, although every hook in them is admin-only. With three admin-only feature modules, about 199 KB of PHP no longer runs on each page view.
* jQuery was enqueued unconditionally on every front-end page. The script it was loaded for contains no jQuery; on a block theme this plugin was adding roughly 30 KB to every page on its own.
* Smooth-scroll was loading render-blocking in `<head>` because `true` was passed where the version string belongs.

Translations:

* Added `lang/horse-tools.pot` (801 strings) and the tooling to regenerate it, report coverage and compile `.mo` files without needing gettext or WP-CLI. Vietnamese is complete.

Removed as abusive:

* The "ads appear when clicking on the website" feature. It registered a document-wide click listener for every non-administrator visitor and opened an affiliate URL in a deliberately off-screen window on any click. That is affiliate cookie stuffing — fraud against both the visitor and the affiliate network, a breach of Google AdSense policy, and grounds for having a domain blacklisted.

Security fixes, second pass (found by an adversarial audit of the first pass):

* **Critical** — stored XSS in the user profile screen. The custom-avatar meta value was saved from `$_POST` with no validation and echoed into the admin user-edit page unescaped, so any Subscriber could plant script that ran in an Administrator's session the moment that admin opened their profile. Now stored as a validated attachment ID and escaped on output.
* Stored XSS in avatar markup: `display_name` was interpolated into single-quoted HTML attributes, and WordPress does not encode single quotes in that field.
* Google OAuth `state` was a WordPress nonce, which for logged-out visitors is identical for everyone and was printed in public HTML — providing no CSRF protection at all. Replaced with a single-use random value bound to the browser by cookie and transient.
* The Google login callback ignored the site's own "Anyone can register" setting.
* reCAPTCHA verification used `file_get_contents()`, so on a host with `allow_url_fopen` disabled — or simply with the secret key not yet pasted in — every login attempt failed and locked the site owner out with no way back except editing the database. Rewritten on the WordPress HTTP API with a timeout, failing open with a log entry instead of locking everyone out.
* reCAPTCHA v3 checked the score but never `success`, `action` or `hostname`, so a token minted for any action on any domain sharing the site key was accepted at login. WooCommerce registration had no v3 coverage at all.
* `login_form_middle` is a filter, not an action; hooking an echoing callback to it put the token field outside the `<form>`, so any theme using `wp_login_form()` could never log in.
* The SVG upload blocklist was defeated three ways — `<svg:script>`, a UTF-16 BOM file, and `&#106;avascript:`. Replaced with `enshrined/svg-sanitize`, which parses and rebuilds the document from an allow-list, plus a pass that strips external references so an uploaded SVG cannot phone home.
* SSRF: `wp_http_validate_url()` does not block `169.254.169.254` (cloud instance metadata) or any IPv6 internal address. Added an explicit post-resolution address check and refused redirects.
* Unpublished posts leaked: the search index was written on `wp_insert_post`, which fires for drafts, pending, private and scheduled posts, into a world-readable JSON file.
* The hidden-admin filter was inverted — it hid the account from administrators browsing `users.php` while leaving it fully visible at `/wp-json/wp/v2/users`.
* The hide-login feature was bypassed by requesting `/wp-login%2Ephp`.
* The `wp-config.php` writer anchored on the raw text `require_once`, so a host comment containing that word caused a define to be spliced into the middle of it, producing a file that no longer parsed — taking the site and wp-admin down with no way to deactivate the plugin. Now anchors on a real PHP token, validates the result parses before writing, and keeps a backup.
* `flush_rewrite_rules()` ran on every request when ads.txt was enabled.
* Contributors could spend the site's Google Indexing API quota on arbitrary URLs.
* Cache poisoning via the client-supplied `Host` header in the HTML minifier, which also corrupted JSON responses.
* Open redirect via `wp_redirect()` on an unvalidated path option; raw `header("Location: …")` replaced with the WordPress redirect functions.
* `javascript:` URIs accepted by the `[gget]` shortcode.
* Role names passed to `current_user_can()` where a capability was expected.
* The custom login-page code feature never ran at all — a misplaced closing brace meant its `add_action()` sat inside its own callback.

Security fixes, first pass:

* Settings import (`main/export.php`) required no nonce or capability check, allowing a CSRF attack to overwrite every plugin option — including the arbitrary header/footer code field — and inject script sitewide. Now nonce-protected, capability-checked and whitelist-validated.
* The `toggle_watermark` AJAX handler had no capability check and accepted an arbitrary option key, letting any logged-in subscriber rewrite plugin settings. Now restricted to `manage_options` with a strict key whitelist.
* Three Google Indexing AJAX handlers had no capability check, and their nonce was printed for every logged-in user. A subscriber could use the site's own Google service account to submit `URL_DELETED` for arbitrary URLs and deindex the site. All three now check capabilities, and the nonce is no longer leaked.
* Global KSES filtering on taxonomy term descriptions was disabled sitewide, letting an Editor store script that executed in an Administrator session. Replaced with a scoped `wp_kses_allowed_html` filter.
* Server-side request forgery in the remote-image importer and in the watermark/frame image fetch. Both now use WordPress's SSRF-protected HTTP API with URL validation, a sane timeout and content verification.
* Google OAuth login did not verify the `email_verified` claim, continued executing after a failed token exchange, and passed an unvalidated role to `wp_insert_user()`. All three fixed.
* Post duplication checked a nonce but not `current_user_can`, allowing users to clone content they could not otherwise access.
* SVG uploads were permitted with no sanitisation, including by the option advertised as upload hardening. SVG is now rejected from the hardening whitelist and screened for script constructs elsewhere.
* Debug log read endpoint had no capability check.
* `wp-config.php` was parsed and rewritten on every request; now only on an authenticated admin request when a setting actually changes.
* CSRF on font deletion and on the hidden-admin reset link.
* Removed unnecessary `wp_ajax_nopriv_` registrations on the font upload and mail endpoints.
* Added `sanitize_callback` to every registered setting and escaped option output throughout.
* Added directory-access protection to the bundled Google API library.

Privacy:

* Removed undisclosed telemetry that sent the site URL and the administrator's e-mail address to a third-party Google Form on activation and deactivation, via a base64-obfuscated URL.
* Removed the original author's donation and affiliate panels, including personal bank, Momo and PayPal details.

Dependencies:

* Rebuilt the bundled Google API client from a real `composer.json`. `firebase/php-jwt` v5.2.0 → v7.1.0, `guzzlehttp/guzzle` 7.0.1 → 7.15.2, `guzzlehttp/psr7` 1.6.1 → 2.13.0, `google/apiclient` v2.7.0 → v2.19.4. `phpseclib` 2.0.28 is no longer a dependency at all. Every CVE listed in the previous snapshot is resolved.
* Trimmed 327 unused Google service definitions; only Indexing, OAuth2 and SearchConsole ship.
* Switched from the deprecated `Google_Client` / `Google_Service_Oauth2` aliases to the namespaced `\Google\Client` / `\Google\Service\Oauth2`.
* Replaced the dead `plus.login` OAuth scope (Google+ shut down in 2019) with `openid` / `email` / `profile`.

Compatibility:

* Added `Requires PHP`, `Requires at least` and `Tested up to` headers. The minimum is now PHP 8.1, which is what the current Google API client requires.
* Fixed PHP 8.1 null-argument deprecations.

Design:

* New gold visual identity across the plugin panel and the optional wp-admin colour scheme. Semantic status colours (success green, error red, warning amber) are intentionally unchanged.
* New brand mark, replacing the original author's logo.
