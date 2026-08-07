# Horse Tools — developer notes

WordPress plugin by Trần Duy Thuận, forked from Foxtool 2.5.3 by Fox Theme (GPLv2, unmaintained upstream) and rebranded in full. The original authorship is credited in `readme.txt` and in the main plugin header; keep that credit in place — it is both a licence obligation and the honest description of where the code came from.

Minimum PHP is **8.1**, set by the bundled Google API client.

## Naming conventions

Everything is prefixed. Do not reintroduce the old `foxtool` / `ft-` names.

| Thing | Convention | Example |
|---|---|---|
| Functions | `horsetools_` | `horsetools_index_now_callback()` |
| Constants | `HORSETOOLS_` | `HORSETOOLS_DIR` |
| Options | `horsetools_*_settings` | `horsetools_gindex_settings` |
| Text domain | `horse-tools` | `__( 'Save', 'horse-tools' )` |
| CSS classes / IDs | `ht-` | `.ht-note`, `#ht-imgstyle` |
| Main file | `horse-tools.php` | plugin slug is `horse-tools` |

Some legacy JavaScript function and DOM-element names still use an `ft` prefix (`fttab`, `ftbox`, `ftslide`, and the `ft_send_email` AJAX action). These are cosmetic and were left alone to avoid churn; rename them as a single deliberate pass if you want, updating PHP, JS and CSS together.

## Layout

```
horse-tools.php     bootstrap: constants, asset enqueue, activation, legacy migration
inc/                feature modules, one per area — always-loaded logic and hooks
  sanitize.php      shared sanitize callbacks for every registered setting
main/               admin screens (one submenu page per file)
main/page/          form partials included by the screens in main/
modal/              modal dialogs
link/               front-end and admin assets (css, js, third-party libraries)
link/google-api/    bundled Google API client — see its UPGRADING.md
lang/               translations (horse-tools-vi, horse-tools-id_ID)
```

## Security rules for this codebase

The upstream plugin had several handlers protected by a nonce alone. A nonce proves the request came from that user's browser; it does not prove the user is allowed to act. Every AJAX handler needs **both**:

```php
if ( ! current_user_can( 'manage_options' ) ) { wp_send_json_error( 'forbidden', 403 ); }
check_ajax_referer( 'horsetools_xxx_nonce', 'nonce' );
```

Related rules learned from the audit:

- Never print a nonce from a global `admin_footer` / `admin_head` hook without a capability guard — that hands it to every logged-in subscriber, including subscribers.
- Never write an attacker-supplied array key into a settings option. Whitelist the key.
- Any outbound request built from user or option input goes through `wp_safe_remote_get()` after `wp_http_validate_url()`, never raw cURL and never straight into a GD function. See `horsetools_fetch_remote_image()` in `inc/media.php`.
- Every `register_setting()` takes a `sanitize_callback` from `inc/sanitize.php`. A handful of keys are intentionally raw (custom header/footer code, ads code, custom SVG icons) and are marked as such — they are writable only by `manage_options`.
- `inc/clean.php` is the reference implementation for a correctly protected AJAX module.
- No telemetry. The plugin must not contact any server that the site owner did not configure themselves. See the Privacy section of `readme.txt`. That includes webfonts and CDN assets in markup — the 503 maintenance page used to `@import` Google Fonts, which leaked every visitor's IP to Google.
- **Never blocklist markup.** The SVG upload check was a list of forbidden strings and it was broken three separate ways (`<svg:script>`, a UTF-16 BOM, `&#106;avascript:`). Parse and rebuild from an allow-list — `enshrined/svg-sanitize` in `link/svg-sanitize/`.
- **`wp_http_validate_url()` is not an SSRF boundary.** It permits `169.254.169.254` and every IPv6 internal address. All outbound fetches built from user or option input go through `horsetools_safe_fetch()` in `inc/http.php`.
- **A role name is not a capability.** `current_user_can('administrator')` happens to work but silently fails for custom roles. Use `manage_options`, or `horsetools_user_meets_role()` in `inc/shortcode.php` when you genuinely mean a role.
- **Do not lock the owner out.** A security feature that fails closed on a transport error, a missing key, or a disabled PHP ini flag can leave an administrator unable to reach their own login form. reCAPTCHA verification fails open and logs; see the note on `horsetools_recaptcha_verify()` in `inc/goo.php`.
- Anything that writes to `wp-config.php` must anchor on a PHP token, validate that the result parses (`token_get_all($src, TOKEN_PARSE)`), and keep a backup. See `inc/debug.php`.
- **An `.htaccess` is not protection.** nginx has never read one, and on nginx the `.htaccess` is itself served on request — confirmed on a live host, HTTP 200 with the file body. Any file this plugin has to keep inside `wp-content` gets a `.php` extension and `<?php exit; ?>` as its first line, which is the file's own first instruction and therefore holds on every server. `horsetools_php_guard()` and `horsetools_guard_directory()` in `inc/server.php`; used by `inc/anchor.php` and `inc/debug.php`. A random file name is a secret, not a guard — keep it, but never let it be the only thing standing there. And whenever you add a "clear this file" button, make sure it does not truncate the first line away.
- No affiliate cookie stuffing, forced clicks, popunders, or hidden windows. The inherited "ads click" feature was removed for this reason and must not come back.

## Toolchain

A portable PHP 8.3 + Composer live at `D:\Claude-code\Tools\_toolchain` (outside the plugin, so they never ship):

```bash
D:/Claude-code/Tools/_toolchain/php/php.exe -l inc/media.php
D:/Claude-code/Tools/_toolchain/php/php.exe D:/Claude-code/Tools/_toolchain/composer.phar audit
```

PHPCS with WordPress-Coding-Standards and PHPCompatibilityWP is installed at `_toolchain/phpcs`; `phpcs.xml.dist` in the plugin root selects the security and PHP 8.1–8.4 compatibility sniffs.

```bash
D:/Claude-code/Tools/_toolchain/php/php.exe D:/Claude-code/Tools/_toolchain/phpcs/vendor/squizlabs/php_codesniffer/bin/phpcs --report=summary
```

Note that `WordPress.Security.EscapeOutput` fires on every `_e()` call and on the intentionally-raw option keys, so triage by sniff type rather than by raw count. The SQL findings in `inc/clean.php`, `inc/post.php` and `inc/horsetools.php` were checked by hand and are false positives — literal SQL with `$wpdb->prefix`, a correct `prepare()`, and table names from `SHOW TABLES` respectively.

Front-end JS is checked with `node --check`.

## Design system

Gold, anchored on warm charcoal. The tokens live in the `:root` block at the top of `link/htadmin.css`; `main/style.php` overrides them per optional skin.

| Token | Value | Role |
|---|---|---|
| `--color` | `#8a6100` | text and borders on white — deep enough for 5.5:1 contrast |
| `--colorbar` | `#8a6100` | side rail background |
| `--gradient` | `#6e4d00 → #c99200` | primary button |
| `--logo` | `#e0a500` | bright gold logo plate |
| `--icon` | `#ffd766` | rail icons on the dark gold bar |
| `--nutbor` | `#5c4000` | button underline |

Two rules to keep:

- **Bright gold never carries text.** `--logo` and `--icon` only appear on the dark bar or behind a dark mark. Text-bearing gold is `--color`.
- **Semantic colours are not brand colours.** Success green, error red, warning amber and the traffic-light indicators stay as they are. Recolouring a "Settings saved" bar to gold removes the signal it exists to carry.

The optional wp-admin colour scheme is registered in `inc/horsetools.php` (`base #1f1b16`, `focus #8a6100`, `current #e0a500`, `gradient #c79200`) with a matching inline stylesheet.

## Residual risk, stated plainly

- **DNS rebinding.** `horsetools_safe_fetch()` resolves the host, checks every address, then hands the URL to the WP HTTP API, which resolves it again. A short-TTL attacker domain retains a narrow race window. Closing it fully means pinning the connection to the vetted IP, which the WP HTTP API does not expose. The window is blind — every caller verifies the response really is an image before using it.
- **Concealment features.** "Hide admin account", "hide plugins from the manager" and "hide login" are inherited features that exist to conceal things from other administrators. They are now implemented correctly, but they are indistinguishable from a backdoor to a reviewer and will be treated as such by security scanners and by the WordPress.org plugin team. Consider dropping them.
- **The plugin-hiding filter selects by ordinal position** in `get_plugins()`, so installing or removing any plugin silently hides a different one than the admin chose. Re-key it on the plugin basename.
- **`inc/scuri.php`'s request-URI "SQL protection"** matches four fixed strings in the URI only. It stops nothing a real attacker would send while its 255-character cap breaks long legitimate admin URLs for Editors. Recommend removing it rather than maintaining it.

## Recurring bug patterns in this codebase

Found repeatedly during the audits. Check for these before touching anything.

- **Brace-less `if` followed by a comment.** A `//` line is not a statement, so the `if` binds to whatever comes *after* the comment. This silently broke the custom-login-code feature and the "remove protocols" minify option. Always brace.
- **`return true` in an AJAX handler.** admin-ajax echoes `0` and dies; the jQuery `success` callback still fires, so the UI reports success unconditionally. Always end with `wp_send_json_success()` / `wp_send_json_error()`.
- **`the_content` filters with no context guard.** They also run on archives, search, feeds and secondary loops. Guard with `is_singular() && in_the_loop() && is_main_query() && ! is_feed()`.
- **`get_posts()` without `'fields' => 'ids'`** when you only need IDs — hydrating a whole media library into `WP_Post` objects is an out-of-memory. Also remember attachments are `post_status = 'inherit'`, not `publish`.
- **Paged `do…while` loops with no offset.** If the loop filters what it deletes, the same page comes back forever.
- **`wp_redirect(); exit;` from inside a `save_post`-family hook.** It aborts the rest of the save. Use `redirect_post_location`.
- **Raw `DELETE FROM {$wpdb->posts}`.** Orphans meta, terms and files and skips `before_delete_post`. Use `wp_delete_post( $id, true )`.
- **Bare `date()`.** Use `current_time()` for storage and `wp_date()` for display, or timestamps come out in the server's timezone. Note `date('h')` is the 12-hour clock — it collided in the font-upload key generator.
- **`wp_enqueue_script( $h, $src, array(), true )`.** The fourth parameter is `$ver`, not `$in_footer`; this loads render-blocking in `<head>` with `?ver=1`.
- **Unguarded GD.** Every image function needs `horsetools_gd_available()` or a `function_exists()` check — Imagick-only hosts are common. Open images with `horsetools_open_image()`, never `imagecreatefrompng()` on a path the UI lets the user point at a JPG.
- **PHP values interpolated into inline JS.** Needs `esc_js()` or `wp_json_encode()`; a single quote otherwise kills the whole `<script>` block.

## Translations

`lang/horse-tools.pot` is generated; do not hand-edit it.

```bash
php tools/make-pot.php        # regenerate the template from source
php tools/i18n-status.php     # coverage report (--all lists every missing string)
php tools/sync-translations.php   # add the Vietnamese strings and recompile every .mo
```

`tools/po-lib.php` has a small .po reader and a .mo compiler, so no gettext or WP-CLI install is needed. Vietnamese is at 100%; Indonesian is at 96% and its missing strings are listed by `i18n-status.php`.

## Known follow-ups

- `.po` / `.mo` translations were carried over from Foxtool; msgids that mention the old product name no longer match and need regenerating with a fresh `.pot`.
- `img/style/2.jpg` … `8.jpg` still preview the inherited skins. Only `1.jpg` (the default) was regenerated in gold; regenerate the rest with a GD script if you keep those skins.
- `main/style.php` still offers the seven inherited skins (WordPress, Bright, Girly, Black, Coffe, Rocket, Blue). Consider trimming to a smaller set that fits the brand.
