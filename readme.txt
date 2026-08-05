=== Horse Tools ===
Contributors: tranduythuan
Author: Trần Duy Thuận
Author URI: https://tranduythuan.com/
Plugin URI: https://github.com/tranduythuan/Horse-Tools
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: all-in-one, contact-chat, shortcodes, security, seo
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.1
Stable tag: 1.2.96

All-in-one WordPress toolkit: contact chat, shortcodes, security &amp; privacy, media optimisation, SEO, cleanup and more — in one plugin.

== Description ==

Horse Tools bundles the day-to-day tools a WordPress site owner actually needs into one plugin, instead of a dozen separate ones.

Features:

* **Contact chat button** — a floating multi-channel contact widget with 30+ services (Zalo, Messenger, WhatsApp, Telegram, phone, Line, WeChat, Instagram, and more), flexible links, 17 button skins, a mobile Services panel to surface your key pages, business hours, greeting bubble, pre-filled messages and scan-to-open QR codes on desktop.
* **Shortcodes** — a full snippet manager (reusable content/HTML snippets, importable from Shortcoder; optionally **PHP snippets**, gated behind two-factor authentication — see below), 20+ built-in shortcodes for conditional display, layout (accordion, tabs, alerts), dynamic data, QR codes and more, plus a "find where a shortcode is used" tool and an on/off manager.
* **Security hardening** — limit login attempts, block user enumeration, security response headers, disable the file editor, REST/XML-RPC controls and header cleanup.
* **Privacy** — self-host Google Fonts and scan the front end for external requests.
* **Media management** — WebP/AVIF conversion, watermarking, thumbnail control.
* **SEO &amp; content** — table of contents, automatic 301 redirects on slug change with a 404 log, Google Search Console / Indexing API integration.
* **Website optimisation** and a one-click site-health audit.
* **Database and content cleanup tools.**
* **Customisable admin display and colour scheme**, plus configuration presets (Blog, WooCommerce, Performance, Security).
* **Customisable WordPress login screen.**
* **WooCommerce helpers**, mail testing, user role and permission tools, post and page utilities, notifications and popups.

== Privacy ==

Horse Tools does not send any data about your site to us or to any third party. It contacts external services only when you explicitly configure a feature that requires it:

* **Google Indexing API / Search Console** — only when you connect your own Google service account, and only to submit the URLs you request.
* **Google login** — only when you enable it and supply your own OAuth client.
* **Google reCAPTCHA** — only when you enable it and supply your own reCAPTCHA site/secret keys. It loads Google's reCAPTCHA script on the login and registration forms (and, on stores, the WooCommerce login/registration forms) to tell humans from bots. Google's Terms of Service (https://policies.google.com/terms) and Privacy Policy (https://policies.google.com/privacy) apply to that check.
* **Telegram notifications (WooCommerce)** — only when you supply your own bot token and chat ID.
* **Update check (GitHub)** — a few times a day the plugin asks the public GitHub API whether a newer release exists, and downloads updates from GitHub when you choose to update. The request carries no site data at all — GitHub sees only your server's IP address, the same as any outbound request. The plugin author sees only GitHub's aggregate download counter, never who or which site downloaded.

No site URL, administrator e-mail, licence check or usage statistic is transmitted on activation, deactivation, or at any other time.

== Languages ==

The interface is fully translatable and ships with the following bundled translations:

* Vietnamese (vi) — complete, maintained by the author
* Indonesian (id_ID), Thai (th), Chinese Simplified (zh_CN), Japanese (ja), Hindi (hi_IN)
* Spanish (es_ES), French (fr_FR), German (de_DE), Portuguese – Brazil (pt_BR), Russian (ru_RU), Arabic (ar)

All languages other than Vietnamese are machine-generated as a starting point and improvements are welcome — corrections can be sent through the GitHub repository. The plugin also loads any translation placed in `wp-content/languages/plugins/`.

== Installation ==

= From within WordPress =

1. Visit 'Plugins > Add New'
2. Upload the plugin ZIP
3. Activate 'Horse Tools' from your Plugins page

= Manually =

1. Upload the 'horse-tools' folder to '/wp-content/plugins/'
2. Activate the 'Horse Tools' plugin through the 'Plugins' menu in WordPress

If you previously used Foxtool on this site, Horse Tools imports its settings automatically the first time you activate it. Nothing is overwritten if Horse Tools settings already exist.

= Upload fails or the browser shows ERR_HTTP2_PROTOCOL_ERROR =

This is not a plugin fault — it means your web server rejected the upload before WordPress ever saw it, almost always because the ZIP is larger than the server's upload limit. On nginx this is `client_max_body_size` (its default is only 1 MB), which is separate from PHP's `upload_max_filesize`. Fixes:

* Raise the nginx limit in your site's server block, e.g. `client_max_body_size 64M;`, and reload nginx. (On a two-tier / proxy setup, set it on the public-facing server block that terminates the browser connection.)
* Or skip the browser upload entirely: extract the ZIP and upload the `horse-tools` folder to `wp-content/plugins/` over SFTP / your host's file manager. This bypasses the upload limit and the server's unzip step, and is the fastest, most reliable way to install or update.

== Support ==

Source code, issue tracker and releases: https://github.com/tranduythuan/Horse-Tools

== Credits ==

Horse Tools is developed and maintained by **Trần Duy Thuận** (https://tranduythuan.com/).

It began as a fork of **Foxtool** by **Fox Theme**, released under the GPLv2 licence. Fox Theme built the original feature set over several years and it remains the foundation of this plugin; that work is gratefully acknowledged here. The original project is no longer maintained by its author, and Horse Tools continues its development independently — rebranded, security-hardened, with its dependencies refreshed and its interface rebuilt.

Under the GPLv2, this fork is distributed under the same licence as the original. See the Changelog for the full list of what changed.

= Bundled third-party libraries =

All bundled libraries are free/open-source under GPL-compatible licences, and their original copyright notices are retained in the respective files:

* **SheetJS Community Edition** (`xlsx`, Apache-2.0) — reads uploaded .xlsx/.xls/.csv files in the browser for the Table builder. No spreadsheet data is sent anywhere.
* **GLightbox** and **PhotoSwipe** (MIT) — the image lightbox engines.
* **CodeMirror** (MIT), **Coloris** (MIT), **Select2** (MIT), **instant.page** (MIT), and the Google API PHP client (Apache-2.0).

Apache-2.0 is compatible with the GPLv3, and Horse Tools is licensed "GPLv2 or later", so the combined distribution is fully licence-compliant.

== Changelog ==

= 1.2.96 =
* **The analytics check now runs in your own browser, which is the only place that can see what a visitor sees.** Every server-side answer was a guess: a host can block the site from fetching itself, route that request to another site on the same account, or serve it a page built for bots — and if the tag was placed in a theme file rather than a setting, there was nothing in the database to find either. The screen now reads the home page from the browser you have open, without cookies so the tag is not withheld the way it is for logged-in users. The server-side check remains as the fallback when the browser cannot run it.
* A loopback that lands on another WordPress site on the same hosting account is no longer accepted as this site's home page — it has to carry this site's own host name.
* **Fixed: the "no analytics found" message shipped in English.** The compile step that turns the translations into the file WordPress reads was cut short, so the Vietnamese for that one string never made it into 1.2.95. The release check now compares the compiled file against its source and refuses to publish when they disagree, which is what let this through.

= 1.2.95 =
* **The measurement screen stops saying "no analytics" when there is analytics.** It searched a hand-written list of option names, so it only ever found the plugins it already knew about — and because that list also matched dozens of unrelated rows, a row limit could push the one row holding the ID out of the results. It now searches the settings by the shape of a measurement ID instead of by option name, which finds it wherever it was put: Site Kit, MonsterInsights, a header-and-footer plugin, a theme option, or pasted into the plugin's own code box. A GA4 property is preferred over a Tag Manager container rather than whichever came first.
* **A loopback that lands somewhere else is no longer read as "you have no tag".** On shared hosting a site fetching itself often reaches the default vhost or a bot-check page, which answers 200 with no analytics in it. The body is now checked for being our own page before its contents are believed, and the request identifies itself as a browser so optimisers do not serve it a stripped page.
* **When nothing is found, the screen says so without asserting it.** The old wording flatly stated there was nowhere for clicks to be recorded. It now explains that the switch can be turned on regardless — the tracker looks for analytics at the moment of the click, so it starts working the instant a tag is present.
* **The admin is fully Vietnamese again.** Regrouping the screens created 85 new strings — the group names, every tab name, and the description under each one on the Overview page — and none of them had a translation, so the interface read half Vietnamese and half English. All 85 are translated; 1,613 of 1,613 translatable strings now have Vietnamese.
* **A release can no longer ship untranslated strings.** The build now refuses to publish if any string is missing its Vietnamese, which is the check that was missing when the mixed-language interface went out.
* Fixed the translation checker itself: it read the catalogue line by line, so any entry gettext had wrapped across several lines looked untranslated. It was reporting 97 strings missing that were all present.
* **The guides match the new structure.** The README (English and Vietnamese) gains a table of what lives on which screen and which tab, the "I can't find the Popup" answer no longer points at a menu entry that does not exist, and the chat guide's opening path is now Customers → Chat.
* **The chat guide explains the new click measurement** — which events reach GA4, where to read them, what Tag Manager users have to build themselves, and how the traffic source is still attributed.

= 1.2.94 =
* **Fixed a double-count risk in the new click measurement.** A site can load both gtag and Tag Manager against the same property, and the click was being sent down both routes. It now takes one route only.
* **Tag Manager users are told what they still have to do.** Tag Manager does not forward events on its own, so the measurement screen now shows the three steps needed to build the tag — otherwise the switch looks on and nothing ever arrives.
* Analytics pasted into the plugin's own code boxes is recognised too, which is an ordinary way to install GA4 and was previously reported as "not found".

= 1.2.93 =
* **Fixed: the measurement screen said no analytics was installed on sites that plainly have it.** It checked by asking the server to open its own home page, and many hosts block exactly that — so a failed check was reported as a definite "not found". It now reads the ID that Site Kit, MonsterInsights or a Tag Manager plugin has already saved, which needs no network at all, and when it genuinely cannot look it says so instead of guessing.

= 1.2.92 =
* **You can now see whether anyone actually presses the contact buttons.** One switch on Customers → Measurement sends an event to the Google Analytics your site already loads, every time a visitor taps a phone, Zalo, Messenger, Telegram, WhatsApp, Viber, SMS or email link. Links inside your posts count too, not only the chat buttons. Nothing is stored on your site and no personal data is involved.
* **Nothing to configure in Analytics.** The channel is part of the event name — contact_phone, contact_zalo and so on — so the breakdown shows up in the standard Events report on its own. The screen also tells you whether a tag was found on your site at all, which is the thing people usually get stuck on.
* **Fixed: redirects were losing the tracking parameters on the address.** A visitor arriving from an ad at /old-page/?gclid=… was sent to the bare new address, so Analytics filed the visit under organic or direct and Google Ads never learned the click led anywhere. Everything on the address is now carried across, unless the redirect sets that parameter itself.

= 1.2.91 =
* **Fixed: FAQ schema was invalid on any post whose answer contains a quotation mark.** WordPress strips one level of backslashes from anything saved as post meta, which removed the escaping from every quote inside the stored JSON. Search engines rejected the whole block — Google's Rich Results Test reports "Missing ',' or '}' in object declaration" — while the page itself looked perfectly normal. Every affected post regenerates itself on its next view; there is nothing to click.

= 1.2.90 =
* **Fixed: deleting an uploaded font did nothing.** The delete link still pointed at the old Fonts screen, which no longer exists as a page of its own, so it answered "Sorry, you are not allowed to access this page" and the font stayed. It now points at the screen you are on.
* **Fixed: every old bookmark showed that same permissions message.** The thirteen addresses that moved into tabs were meant to redirect since 1.2.73, but WordPress rejects an unknown ?page= before the redirect could run, so none of them ever did. They redirect now.

= 1.2.89 =
* **Font settings are now saved by the screen's own Save button.** 1.2.83 only relabelled the second button so the two could be told apart; the settings have now moved into the screen's form, so there is one Save again. Uploading a font keeps its own button, but that one says UPLOAD, so there is nothing to mistake.
* **Listing artwork added**, at img/brand/. Like the mark file, it is generated from the same function the plugin renders rather than drawn again, and the build fails if any of the three files no longer matches.

= 1.2.88 =
* **The Horse Tools mark is now a file as well as code**, at img/horse-tools-mark.svg, for anything outside PHP — a README, a listing icon, a favicon. It is generated from the same function every screen renders, not drawn a second time, and a check in the build fails if the two ever disagree.

= 1.2.87 =
* **Clicking an entry in the table of contents now always jumps to the heading.** It asked the browser for a smooth scroll and cancelled the ordinary link jump first — so anywhere smooth scrolling is unavailable or suppressed, clicking an entry did nothing at all. It still scrolls smoothly where it can, and jumps where it cannot.

= 1.2.86 =
* **Fixes a fatal error on the front end, introduced in 1.2.67. Update immediately if you are on any version from 1.2.67 onward.** Two helpers that check colours and sizes before they go into a style block lived in the settings file, and 1.2.67 stopped loading that file on the front end. Any page then died with a critical error as soon as one of these features had a colour set: chat, the custom login page, dark mode, popups and cookie notices, search, shortcodes, and the table of contents. Sites with no colours configured were unaffected, which is why it went unnoticed.
* The two helpers now live in their own file that always loads. Nothing else changes.

= 1.2.85 =
* **The table of contents no longer shows the old Foxtool mark.** The default icon was still the logo of the plugin this one was forked from, sitting on the front end of every site using the contents list. It is the Horse Tools mark now.
* **You can paste your own icon.** Any SVG, in a box under the icon chooser; leave it empty and the chosen icon is used as before. Scripts, event handlers and links are stripped from anything pasted, because this is echoed on every page that shows a contents list.
* **The icon has its own colour setting.** Leave it empty and it follows the title colour exactly as it did before. Circles, rectangles and polygons are coloured too, not just paths, so a pasted icon is not left half black.

= 1.2.84 =
* **Removed the rest of the shouty duplicate headings.** 1.2.83 cleared them from the folded-in sections but missed the thirteen original tabs, so Users still opened onto USER, Optimisation onto OPTIMIZE, Site onto DISPLAY and Admin area onto CUSTOM. All gone; the tab already names what you are looking at.

= 1.2.83 =
* **Fixed: the Appearance screen had two identical Save buttons, and the big one did not save fonts.** Editing a font setting and pressing the Save at the bottom of the screen looked like it worked and wrote nothing. The font section now says so and its own button is labelled for what it saves.
* **Removed the shouty duplicate headings inside tabs.** Every screen folded into a tab kept its old page title as a heading, so opening the Popup tab showed 'POPUP' again, and several disagreed with the tab above them — 'Index now' opened onto 'INDEX', 'Table of contents' onto 'TOC', 'Site search' onto 'HORSE SEARCH'. The tab already names the section.

= 1.2.82 =
* **Fixes the Fonts tab opening blank, and the same fault waiting in Backup.** Both screens keep their own internal tabs, and a tab inside a tab is hidden by the tab switcher — so the tab opened at no height at all with everything present but invisible. Their inner sections now simply stack.

= 1.2.81 =
* **Fonts and Backup are now tabs too, and the regrouping is complete.** Fonts sits on Appearance, Backup on Tools. The menu is down from sixteen entries to nine screens named after what they do, plus Extend and About.
* These two took a different approach from the rest. Both do real work before drawing anything — reading a font deletion, building the export file, preparing an import preview — so lifting their markup into a tab would have left that behind and shown an empty panel. Instead each screen learned to render without its own page frame, and the tab asks it to.

= 1.2.80 =
* **Fixes assets that stopped loading after the regrouping.** The code editor, the media picker, the font selector and the table builder were each loaded by naming the screen they belonged to. Those screens became tabs, the names stopped matching anything, and the assets quietly stopped loading — the code boxes would have been plain textareas and the image pickers would not have opened. Which screen needs what is now one list instead of three scattered checks.
* The site health panel's debug link pointed at a screen that no longer exists.

= 1.2.79 =
* **Shortcodes and the table manager move onto the Content screen.** The six shortcode groups become tabs, and the stored-tables manager joins them. Two more menu entries gone.
* A tab can now hold a screen that has forms of its own — a file upload, a download, a list with its own buttons. Those render outside the shared form, because a form inside a form is invalid and browsers drop the inner one along with whatever it was meant to submit.
* Fonts and Backup stay where they are for now. Both do work before drawing anything — reading a deleted font, building an export file, preparing an import preview — and that work lives above the markup, so lifting the markup alone would leave the tab looking empty. They need their own change rather than a rushed one here.

= 1.2.78 =
* **Fixes the cleanup schedule tab, which 1.2.77 shipped empty.** The schedule needs a list of what can be cleaned and a list of frequencies, both of which were built by the screen it was lifted out of, so the tab drew its heading and then nothing. It also carried a second form and Save button of its own, inside the screen's form, which browsers discard.
* Restored the keyboard-shortcut hint above the code editors, lost in the same way.

= 1.2.77 =
* **Add code, Debug and Clean move onto grouped screens.** Custom CSS and the four code boxes plus the debug settings are tabs on Tools; the database cleanup is on Speed, because clearing out revisions and orphaned media is what it is actually for. Three more menu entries gone.
* Old addresses still work.

= 1.2.76 =
* **Notifications, ads and site search move onto grouped screens.** The ad-block notice, notification bar, popup and cookie notice are now four tabs on Customers, next to ad clicks, AdSense and ads.txt; site search is a tab on Appearance. Three more menu entries gone, nothing removed.
* Each of those screens had its own tab strip, so the tabs are flattened into the screen rather than nested — a tab inside a tab is hidden by the tab switcher and opens empty, which is the fault fixed in 1.2.75.
* Old addresses still work.

= 1.2.75 =
* **Fixed: the table of contents tab on the SEO screen opened empty.** It kept the tab strip from its old screen, and a tab inside a tab stays hidden — so clicking it showed nothing at all while the settings were still there in the page. Only visible with the table of contents module switched on, which is why it survived the previous release.
* A section can no longer bring a tab strip of its own onto a grouped screen, so this cannot happen again as the remaining screens are folded in.

= 1.2.74 =
* **Fixes a critical error on the SEO screen introduced in 1.2.73.** The tabs folded in from Redirects, Index now and the table of contents belong to optional modules; on a site where one of those modules is switched off, the code behind that tab is not loaded and rendering it stopped the whole screen with a critical error. Those tabs now appear only when their module is on, exactly as their old menu entries did. Update straight past 1.2.73 if you are on it.
* A section that fails can no longer take a whole screen down with it. It now says so in place and the rest of the screen — including the Save button — keeps working.

= 1.2.73 =
* **Redirects, Index now and the table of contents are now tabs on the SEO screen** instead of three separate menu entries. They were always SEO work; having them as their own items was what made the menu long without making anything easier to find. The maintenance page (503) went to Security, where it belongs — it was only next to redirects because they share a file.
* Old bookmarks still work: the previous addresses now take you to the right tab rather than showing a permissions error.
* Behind this, a screen can now hold settings from several groups at once and still save each one without touching the others — the SEO screen writes four different stored settings from a single Save button.

= 1.2.72 =
* **Fixed: the “fix this” links in the site health panel led nowhere.** They pointed at the Overview screen, which held every setting until the previous release and now holds none, so clicking one looked like it worked and did nothing. Each link now goes to the screen that setting actually lives on.
* Fixed the menu order — the grouped screens were interleaved with Extend and Clean, because a screen registered without a position lands wherever registration order happens to put it.
* The group cards on Overview now fit two to a row instead of one.

= 1.2.71 =
* **The settings are now grouped by what they do.** Instead of one screen with thirteen tabs, there are eight screens named after the job: Speed, SEO, Security, Content, Appearance, Customers, Accounts & Email, and Tools. Nothing has been removed or renamed — a setting that was in the MEDIA tab is under Speed because compressing images is how you make pages lighter, and one that was in GOOGLE is under Accounts & Email because signing in with Google is a way for people to log in.
* **Overview is now a way in rather than a wall of settings.** It shows the eight groups with a line about what is in each, alongside the site health panel and the search box. If you are not sure which group holds a setting, type its name into the search box and it will take you straight to it.
* This also fixes what was making that screen slow: it was 546 KB of HTML, of which 260 KB was the twelve tabs you were not looking at, hidden by JavaScript after the browser had already built them. Each screen now sends only its own.

= 1.2.70 =
* **There is now a Security screen, and protecting your login is finally in one place.** It was split across three unrelated tabs: the lockout, security question and two-factor settings under SECURITY; the reCAPTCHA check under GOOGLE, because reCAPTCHA happens to be a Google product; and moving or restyling the login page under CUSTOM. Anyone hardening their login had to know to visit all three. It is one job, so it is one screen.
* Like the SEO settings before them, reCAPTCHA and the login-page settings no longer depend on the tab they came from being switched on.
* Internally the screens now share one shell rather than each being its own copy. Nine hand-written near-copies would have drifted apart, which is how the settings came to need reorganising in the first place.

= 1.2.69 =
* **There is now an SEO screen.** Everything on it already existed, buried in the CONTENT tab: removing the category and tag slug from permalinks, adding .html to pages, using image titles as alt text, adding nofollow and a new tab to external links, and the automatic FAQ schema. None of it was findable by anyone looking for SEO — which is how a site owner reasonably concludes the plugin has no SEO features. Nothing about what these settings do has changed, only where they live.
* **Those features no longer depend on the CONTENT switch.** They are implemented in the file the CONTENT tab loads, so with CONTENT off they would have been switched on from the SEO screen and quietly done nothing. The file is now also loaded when any of these features is on by itself.
* The two screens write the same stored option and each now claims only the fields it renders, so saving one leaves the other alone. This is what the last three releases were building towards.

= 1.2.68 =
* **One Save button can now write more than one group of settings.** WordPress binds a settings form to exactly one stored option, which is fine while each screen maps to one option and stops being fine as soon as screens are grouped by subject: an SEO screen holds the FAQ settings alongside redirects, index-now and the table of contents, and each of those is a separate option — four forms and four Save buttons on one screen. Saving now goes through its own handler that writes any number of options from a single submission, while each one still only rewrites the keys that screen actually rendered.
* Measured before changing anything, and the earlier plan for this release was aimed at the wrong target. Loading admin PHP was not the cost: the settings screen sends 546 KB of HTML and 4,388 elements, of which 260 KB is the twelve tabs you are not looking at (the chat tab alone is 100 KB). That is fixed by splitting the screen up, which is the next release, so rearranging the loading of files that are about to be reorganised anyway was dropped rather than done twice.
* No visible change. The existing screen posts through the new handler and still saves exactly as before.

= 1.2.67 =
* **Fixed: the daily cleanup has never actually run.** Three modules were skipped on any request that was not the admin area, to keep them off page views. WP-Cron is not an admin request, so the file that handles the scheduled cleanup was skipped there too — the event fired, found nothing listening, and WordPress marked it done and rescheduled it. It failed silently rather than erroring, which is why it went unnoticed. Loading is now decided by a test that counts cron and WP-CLI as well as the admin area, so the handler is present when the event fires. Turn the schedule off and on again if you want to be sure of a fresh next-run time.
* **A page view no longer loads the settings sanitiser or the updater** — 33 KB of PHP that a visitor could never reach, out of the 70 KB the plugin always loaded. Sanitising happens when settings are written, which only the admin screens, the activation migration and WP-CLI ever do; the update check runs in the admin area and from cron. Both are still loaded in every context that needs them. With OPcache the saving is memory and a little include work rather than recompilation, so expect a small improvement, not a transformed site.

= 1.2.66 =
* **Groundwork for reorganising the settings screens: a form can now save its own part of the settings without erasing the rest.** All thirteen tabs share one stored option, and a settings form replaces that option wholesale — correct only while a single form renders every field. Splitting the tabs across screens would have meant saving one screen wiped the others. A form now declares which keys it is responsible for, and saving drops exactly those before applying what was submitted. The declaration cannot be worked out at save time from what arrives: an unticked checkbox sends nothing, and is indistinguishable from a field the form never had — so the form states its scope up front, read back from the markup it actually rendered, which covers fields written as raw HTML and repeater rows alike.
* Nothing changes on screen in this release. While one screen still renders every tab it also claims every stored key, so saving behaves exactly as before even for a field the scanner might have missed.

= 1.2.65 =
* **FAQ schema: stands aside when the post already carries its own.** Found on a live site: an article whose FAQ answers had been marked up by hand years ago got a second FAQPage block from us, so one page published two conflicting sets of questions. Detection previously knew about Rank Math and Yoast only, and a JSON-LD block pasted straight into the post leaves no trace to look for — nothing in the editor shows it is there. We now look for a real script block in the content, while ignoring the word where a post merely writes about schema in escaped example code. There is also a filter, `horsetools_faq_foreign`, for an SEO plugin we do not recognise.
* **FAQ schema: code no longer leaks into an answer.** The same article showed the second, worse half of the problem — its last answer contained the entire source of that pasted block, published as if a reader had written it. The walker skipped script and style tags, but the answer text was read from the whole paragraph, and WordPress can leave a script inside the very paragraph it follows. Script, style, noscript and template contents are now removed before an answer is read.
* Cached results now record which version of these rules produced them, so a fix like this recomputes posts by itself. Previously the cache was keyed to the post edit time alone, and a corrected rule kept serving the old answer on every post nobody happened to edit again.

= 1.2.64 =
* Restores a filename that went missing from the 1.2.63 changelog text. No code change — this release doubles as the first live exercise of the new CDN-based update check.

= 1.2.63 =
* **Update checks no longer use the GitHub API.** Each release now carries a small manifest file, and the plugin reads that from the release download CDN instead of calling api.github.com. The reason is capacity: the API allows 60 unauthenticated requests an hour **per IP address**, which is plenty for one site but is shared by every site behind the same address on shared hosting — and a conditional request does not help, as a 304 response still counts against it (measured). Release downloads were never subject to that limit, and now the version check is not either, so the plugin scales to any number of installations. The check also spreads itself out — the result is kept 12 to 16 hours, jittered, so installations do not drift into checking in unison. The API remains as a fallback for older releases. Nothing about the safety changes: only the official `horse-tools-*.zip` asset of the official repository is ever accepted, now verified by filename as well as by repository.

= 1.2.62 =
* **Fix: the two FAQ number boxes looked broken when empty.** They start empty because nothing has been saved yet, and the defaults (2 questions, 500 characters) live in the code — so the screen gave no hint of what was actually in force. Both now show their default as placeholder text and say so in the help note, and their labels read plainly. Leaving them empty keeps using the defaults, exactly as before.

= 1.2.61 =
* **New: automatic FAQ schema.** Horse Tools now reads the “frequently asked questions” section your posts already contain and publishes the matching FAQPage JSON-LD, so an entire archive becomes eligible for Google's FAQ rich results without editing a single article. It is deliberately forgiving about how that section is written: the FAQ can sit anywhere in the post, including nested inside a page builder's markup; questions may be any heading level below the section heading, or an accordion's summary; text inside shortcodes is read rather than discarded; and the phrases that identify the section (“thường gặp”, “hỏi đáp”, “FAQ”, “Q&A”…) are a list you can extend, matched case- and accent-insensitively. **A “Scan the whole site” button reports exactly what will happen** — how many posts qualify, which ones have a FAQ section but too few questions, and which mention one but could not be read at all, each linking straight to the editor — so nothing fails silently. The post editor shows the questions it found while you write. Results are cached per post and recomputed only when that post is edited, and if another SEO plugin already publishes FAQ schema for a post, Horse Tools leaves it alone rather than duplicating it. Under Overview → Content.

= 1.2.60 =
* **Fix: PHP snippets refused to appear on well-hardened sites.** 1.2.59 treated DISALLOW_FILE_EDIT as a reason to hide the feature — but that constant only closes WordPress's built-in theme/plugin file editor, and it is set on most careful sites (by the host, by a security plugin, or by this plugin's own Security tab). Blocking on it shut out exactly the site owners the feature is meant for, while stopping nobody determined — an administrator who wants PHP can still install any snippet plugin — and it pushed people to switch off real hardening in order to use a feature that is better protected than the editor that constant closes. PHP snippets now work alongside it and simply note that it is set. DISALLOW_FILE_MODS still blocks, because there the platform genuinely allows no code changes at all, and HORSETOOLS_NO_PHP remains the explicit off switch.

= 1.2.59 =
* **New: PHP snippets — with the strongest safety net of any snippet plugin I know of.** A snippet can now be marked "run as PHP", which finally makes things like an automatic FAQ schema or a related-posts block possible without a separate code plugin. Because running PHP is the most powerful thing a plugin can offer, it is fenced in: only a full administrator (super admin on multisite) can see it, **their own account must have two-factor authentication switched on**, and editing PHP stays locked until they type a **current authenticator code**, which opens a 15-minute window. Code is **checked for syntax errors before it is saved**, so a typo can no longer white-screen the site, and if a snippet ever does crash a page it is **switched off automatically** with an explanation. Each snippet is **signed with the site's own secret key**: code written straight into the database — the usual pay-off of an SQL-injection hole in some other plugin — has no valid signature and simply refuses to run. Saving or enabling PHP sends an **alert to your Telegram (or e-mail)** and is written to an audit log with user, time and IP. You choose where each snippet runs (its own shortcode, the head, the footer, above/below post content, or every page load) and on which side of the site. And if the worst happens, `define( 'HORSETOOLS_NO_PHP', true )` in wp-config.php stops every PHP snippet, so a site is always recoverable over FTP.
* Corrected the plugin description, which had claimed PHP snippets before they existed.

= 1.2.58 =
* **Fix: printed and exported tables lost some row numbers, and repeated a merged total.** Reported from a real print-out. Row numbers were read back from the page, so rows that had never been displayed (page 3 of a paginated table, say) came out blank; they are now numbered from their position in the exported set, so Print, Copy and CSV always number every row. And a cell merged across columns — the amount in a “TOTAL” row — was written once per column it spanned, printing the figure twice; a merged cell now contributes its text once, in the column it starts in.

= 1.2.57 =
* **Reader tools for tables — the features people expect from a paid table plugin.** Four new switches in the table builder: a **row-number column**; a **filter dropdown under each column header** (built from that column's own values, so a price list can be narrowed to one warranty period in a click); **Copy / CSV / Print buttons** for visitors, plus a **show/hide columns** menu; and **freeze the first column** so the product name stays put while a wide table scrolls sideways. Searching now also **highlights the matches** inside the table. Copy, CSV and Print all export exactly what the reader is looking at — current filters and hidden columns respected, every matching row included rather than just the open page — and Print opens a clean printable sheet without a pop-up window. Merged cells stay aligned when a column is hidden, a pinned total row is never touched, and the whole thing is still dependency-free JavaScript that loads only on pages containing a table.

= 1.2.56 =
* **Readable names for search results that had none.** A handful of controls carry no visible label at all — the contact-channel picker, for instance — and were listed by their internal key (“chat-nut11”). Such a result now takes the name of the section it belongs to, plus its row number when it sits in a repeating list (“Buttons 1”, “Buttons 2”), and the location crumb no longer repeats that name back.

= 1.2.55 =
* **Search also matches things a label doesn’t say.** Typing “zalo” used to find nothing, because the contact-channel setting is labelled just “Channel” — Zalo is one of its dropdown choices. Each indexed setting now carries hidden keywords: **every choice in its dropdown** and **its help note**. So “zalo”, “messenger”, “viber” find the channel picker, and searching a phrase from a hint finds the setting it describes. The keywords only affect matching; results still show the clean label and location.

= 1.2.54 =
* **The search now covers every setting in the plugin, on every screen.** In 1.2.52 only screens built with the newer settings framework were indexed; the older hand-written screens (Extend, Add code, Cleanup and friends) contributed nothing but their name. The index now also *reads the markup each screen produces*: every control belonging to the plugin is picked up, with its label taken from its real `<label>`, its tab from the tab button it sits under, and its section from the nearest heading above it. Controls that have no HTML id are addressed by their field name, so clicking a result still lands on the exact control. Nothing has to be maintained by hand — whatever a screen renders is what the search finds, including anything added in future.

= 1.2.53 =
* **Fix: the sidebar search box never actually appeared.** A long-standing timing bug — the admin script reads its settings index at load time, but since the script was moved into the page `<head>` (so that hosts which drop footer scripts don't break the tabs) the index, which is printed at the end of the page, did not exist yet. The whole sidebar panel — search box *and* the “Currently enabled” summary — silently did nothing. The script now reads the index when the page is ready, so both work. Found while live-testing the new plugin-wide search in 1.2.52.

= 1.2.52 =
* **The sidebar search now finds features across the WHOLE plugin.** It used to search only the screen you were on; with dozens of features spread over many screens and tabs, a new user had no way to know where anything lived. The search box (top of the sidebar on every Horse Tools screen) now indexes **every setting on every screen** — type a feature name and each result shows its full location (Screen › Tab › Section); click it and you land on the right screen with the right tab open and the exact control highlighted. Matching is diacritic-insensitive, so typing “bao mat” finds “Bảo mật”. Screen names themselves are searchable too. The index builds itself from the real settings screens (no hand-maintained list to go stale) and is cached per version and language.

= 1.2.51 =
* **Richer table looks: 3 new styles and 7 new header colours.** New styles: **Card** (an elevated, softly-rounded block with a shadow and no hard outline), **Dark background** (a full dark table — dark rows, matched borders, search box and pagination restyled to fit), and **Soft pastel** (the whole table tinted with gentle pastels *mixed automatically from whichever header colour you pick* — choose Pink and the stripes, hover and footer all turn soft pink). New header colours: **Red, Pink, Teal, Indigo** plus three **gradients** (blue–violet, sunset, ocean). Everything combines freely with the existing options and shows live in the builder preview.

= 1.2.50 =
* **Fix: total rows no longer get mixed in by sorting.** Live testing showed that a “TỔNG CỘNG” row was sorted in among the products and landed on whatever page pagination put it. New option in the builder: **“Last row is a total row (pinned to the bottom)”** — the row is rendered as a real `<tfoot>`, which sorting, searching and pagination never touch: it stays pinned under the table on every page, always visible, and gets a bold/grey emphasis by default (customisable per-table CSS can target `tfoot`). Merges (`#colspan#`) still work inside the pinned row; a `#rowspan#` can no longer reach from the pinned row into the body. Works in the mobile card layout too.

= 1.2.49 =
* **Tables, final piece: export, merged cells, formulas, per-table CSS.** Every stored table gains an **Export CSV** button (Excel-ready UTF-8, Vietnamese intact). Cells can be **merged**: type `#colspan#` to merge into the cell on the left, `#rowspan#` into the cell above — TablePress-compatible keywords, working in the header too and never crossing the header/body boundary. A safe **formula subset** computes totals at render time: a cell of exactly `=SUM(B2:B10)` (or AVG / MIN / MAX) is evaluated over the range — Vietnamese number formats understood, results formatted the same way; it is pattern-matched only, no expression engine, so nothing else can run. And each table accepts **its own custom CSS** (scoped by its `ht-table-{id}` class, printed only on pages showing that table; `<` is stripped so the style block can't be escaped). The builder shows a hint for the merge/formula keywords, and the live preview renders merges and formulas exactly like the site.

= 1.2.48 =
* **New: Google Sheets sync for stored tables.** Paste the link of a public Google Sheet (shared as “Anyone with the link can view”) into the table builder, click **Pull data**, and the sheet lands in the grid — no API key, no Google account connection, the server reads the sheet's CSV export directly. Optionally set the table to **refresh itself hourly or daily** (via WP-Cron), so editing the spreadsheet updates the website on its own; a **“Sync from Sheet”** button on the Tables screen refreshes on demand. Safety rails: only `docs.google.com` links are accepted, downloads are capped at 1 MB / 500 rows / 40 columns, every cell is sanitised as plain text, and a sheet that stops responding is retried on the next interval instead of hammering Google. Privacy: your server only ever *reads* the public sheet; nothing is sent.

= 1.2.47 =
* **New: spreadsheet-style editing in the table builder.** The manual grid is no longer a fixed block: every column gets small controls to **insert a column, delete it, or move it left/right**, and every row gets the same for **inserting below, deleting, and moving up/down** — so you can restructure a table without retyping it. Pressing **Enter** in a cell jumps to the cell below (and adds a row on the last one), the way a spreadsheet does. Also replaced the Tables screen's old browser confirm dialog for Delete with a friendlier two-step button: first click arms it (“Click again to delete”), second click deletes.

= 1.2.46 =
* **New: sortable, searchable, paginated tables.** Every table — stored or inline — can now switch on three reader-facing behaviours: **click a column header to sort** (understands Vietnamese number formats, so “1.790.000” sorts as a number, and mixed text like “12 tháng” sorts naturally), a **search box** that matches without typing accents (“chuot” finds “Chuột”), and **pagination** with a configurable rows-per-page. Three checkboxes in the table builder (or `sort="1" search="1" page="10"` on the shortcode). Implemented in ~5 KB of dependency-free JavaScript loaded only on pages that contain a table — no jQuery, no DataTables, nothing added to the rest of the site.

= 1.2.45 =
* **Fix: the “Tables” screen was unreachable.** The Horse Tools → Tables submenu (introduced in 1.2.39) registered before its parent menu existed, so WordPress produced a broken menu link and opening the screen said “you are not allowed to access this page”. Found in live testing; the submenu now registers after the parent and the Tables manager opens normally. No other changes.

= 1.2.44 =
* **Vietnamese translation for the new update screens.** The 1.2.43 release shipped the GitHub self-update feature with its six interface strings (“Check for updates”, the up-to-date / new-version notices, …) not yet compiled into the bundled Vietnamese translation; they showed in English. Recompiled — Vietnamese admins now see them translated. This release also serves as the first live test of the new one-click GitHub update path.

= 1.2.43 =
* **New: one-click updates straight from GitHub — no more manual uploads.** Horse Tools now reports its new releases to WordPress' own update system: when a new version is published, an ordinary **Update** link appears on the Plugins page (and in Dashboard → Updates), and your **server downloads the package directly from GitHub** — exactly how wordpress.org plugins update. The ZIP never travels through your own connection again, which eliminates the browser-upload failures (HTTP/3 aborts, 408 timeouts) that hit owners on slow uplinks. Includes a **“Check for updates”** link on the plugin row for an instant check, a “View details” popup showing the release notes, and support for WordPress auto-updates. Only the official `horse-tools-*.zip` asset of the official repository is ever accepted as an update source, and results are cached so GitHub is asked at most a few times a day.

= 1.2.42 =
* **Smaller download + install troubleshooting.** Replaced the maintenance-page (503) background — a 1.3 MB animated GIF — with a lightweight CSS panel, trimming the download by over a megabyte with no visible change. Added an Installation section explaining the `ERR_HTTP2_PROTOCOL_ERROR` some servers throw on plugin upload: it is the web server rejecting an upload larger than nginx's `client_max_body_size` (default 1 MB), fixed by raising that limit or by uploading the folder over SFTP. Combined with 1.2.40–1.2.41, the plugin is now roughly half its former size and unpacks far fewer files.

= 1.2.41 =
* **Faster install: ~280 files instead of ~760.** The bundled Google API client — nearly two thirds of the plugin's files, and only needed by the optional Google Login and SEO Indexing / Search Console features — now ships as a single compressed file and is unpacked automatically the first time one of those features is used. A normal install therefore unpacks far fewer files, which is dramatically faster on hosts that lack the PHP `zip` extension (where WordPress falls back to a slow one-file-at-a-time unpacker). Nothing changes for you: if you use Google Login or SEO Indexing it just works; the library is unpacked once behind the scenes. No feature removed.

= 1.2.40 =
* **Much smaller, faster install.** The bundled Google API client had been pruned to just the three services the plugin uses (Indexing, OAuth2, Search Console), but its Composer class map was never regenerated — so it still listed ~36,000 classes pointing at files that no longer existed, bloating two autoload files to about 12 MB of dead entries. Those are now rebuilt to the 401 real classes (~96 KB), and the gettext `.po` sources (kept in the repository) no longer ship in the ZIP. The download drops by several megabytes and installs reliably on low-powered hosts where the previous upload could stall or fail. No feature change — the SEO Indexing / Search Console integration works exactly as before.

= 1.2.39 =
* **New: reusable, stored tables (a “Tables” manager).** The first step toward a full table tool. Build a table once on the new **Horse Tools → Tables** screen, then drop it into any post or page with `[ht-table id="5"]`. Edit it in one place and every post that uses it updates automatically — no more copy-pasting the same table around. Duplicate and delete from the list; each row shows the exact shortcode to copy. The editor’s **Table** button also gains a **Saved tables** tab so you can insert an existing table without leaving the post. Stored tables share the same styles, header colours, caption and automatic number-alignment as inline ones, and render server-side so they always reflect the current data. (More is coming: front-end sorting/search/pagination, a spreadsheet-style row/column editor, Google Sheets sync, and export.)

= 1.2.38 =
* **Table builder — richer styling.** Following the plain first version, the builder now offers a **Style** (Default, Bordered, Minimal, Lines only), a **Header colour** (grey, blue, green, orange, purple, dark), and an optional **Caption** (a title above the table). Number-only columns are now **right-aligned automatically**, and the live preview shows all of it exactly as it will look on the site. The result no longer looks like one flat default table.

= 1.2.37 =
* **Licensing housekeeping.** Added a “Bundled third-party libraries” section to the readme crediting each bundled library and its licence (SheetJS Apache-2.0, GLightbox / PhotoSwipe / CodeMirror / Coloris / Select2 / instant.page MIT, Google API client Apache-2.0). All are free/open-source and GPL-compatible; no code or licence changed.

= 1.2.36 =
* **New: a responsive Table builder in the editor.** A “Table” button (Classic editor) and a “Horse Tools table” block (Gutenberg) open a builder with three ways to make a table: **type it in** a grid, **paste from Excel / Google Sheets** (or CSV), or **upload a .xlsx / .xls / .csv file**. Options for a header row, striped rows, compact spacing, and stacking each row into a card on phones. The table is wrapped so it scrolls instead of overflowing on mobile, and its stylesheet only loads on pages that actually have a table. Excel files are read in the browser (SheetJS, bundled, loaded only when you pick a file) — nothing is uploaded to any third party.

= 1.2.35 =
* **New: a “Horse Tools snippet” block for the block editor (Gutenberg).** The companion to the Classic-editor button added in 1.2.34 — add the block, pick a snippet from the dropdown, and it renders on the page. Now writers can insert their snippets from either editor without remembering a single slug. (The block is dynamic, so its output always stays in sync with the snippet.)

= 1.2.34 =
* **New: a “Shortcode” button in the post editor to insert your snippets.** Writers no longer have to remember snippet slugs — a button next to “Add Media” (Classic editor and the Classic block) drops down the site’s own snippets and inserts `[ht-snippet name="…"]` at the cursor, in the visual editor or the Text tab. Only appears when the Shortcode module is on.

= 1.2.33 =
* **Fixed: the security-question field on the login form was narrower than the username and password boxes** when the custom login screen was on. The input used to sit inside its label (which shrank it); it is now a full-width field that lines up exactly with the other login boxes.

= 1.2.32 =
* **New: a “One-click safe speed setup” button right on the Optimize screen.** No need to find the presets page — one button at the top of Optimize switches on all the safe, high-impact speed features (delay & defer JavaScript in safe mode, lazy-load, Instant-page, HTML compression, and dropping Emoji / jQuery Migrate / Dashicons), and deliberately leaves the risky ones (async CSS, “delay all”) off. The switches light up so you can see exactly what changed, then you press Save.

= 1.2.31 =
* **One-click speed for non-technical owners.** The built-in configuration presets (Extend → Backup/Import) now switch on the new safe, high-impact speed features for you — no handles, no critical CSS, nothing to understand. The **Performance** preset (and the Blog and WooCommerce presets) now enable Delay JavaScript in its safe *Listed* mode with a 5-second fall-back, Defer JavaScript, disable Dashicons for visitors and calm the Heartbeat, on top of what they already did. The risky options (async CSS, the aggressive "delay all" mode) are deliberately left out so a preset never breaks a site.

= 1.2.30 =
* **New (Optimize): Load CSS without blocking render (async CSS).** Stylesheets in the head normally block the browser from painting anything until they finish downloading. This loads them with the media-toggle technique so the page appears on screen sooner — a big win for First Contentful Paint and Lighthouse's "Eliminate render-blocking resources". Comes with a **Critical CSS** box (inlined at the top of the head so the first paint already looks right, preventing the unstyled flash) and an exclusion list to keep chosen stylesheets blocking. A no-JavaScript fallback keeps every stylesheet working, and logged-in users are unaffected. It is the most powerful CSS option — test the front end after enabling.

= 1.2.29 =
* **Hardening for Delay JavaScript.** The delay engine now only ever touches classic executable JavaScript (an allow-list), so newer inert `<script>` types can never be delayed by mistake — this specifically protects **speculation rules** (Speculative Loading / prerender), **Partytown**, framework `<template>` scripts and the `text/plain` blobs consent managers use, which the aggressive *All* mode could otherwise have broken. The scanner uses the same rule, so its list matches exactly what can be delayed while still showing anything already delayed.

= 1.2.28 =
* **New (Optimize): "Scan the scripts running on the home page" button.** The Delay JavaScript boxes ask for script names, but a non-technical owner rarely knows what their site loads. This one click fetches the live home page (as a visitor sees it), lists every script it finds — external files and recognised inline trackers like Google Analytics, GTM, AdSense, Facebook, Hotjar — flags which are already delayed, and lets you drop each one straight into the Delay or Exclude list. No source-reading or handles required.

= 1.2.27 =
* **New (Optimize): Delay JavaScript until interaction.** Holds heavy third-party scripts — analytics, tag managers, pixels, chat widgets, ad and A/B tags — until the visitor first interacts (scroll, mouse move, tap, key or click), then runs them in the original order. It is the single biggest lever for Total Blocking Time and Lighthouse "Reduce unused JavaScript".
    * Two modes: *Listed* (delay only scripts you name, with a built-in default list of common trackers) or *All* (delay everything except an exclusion list).
    * Preserves execution order (external scripts wait for load) and re-fires DOMContentLoaded / load **only for the delayed scripts**, so late libraries still initialise without double-running the ones the page already ran.
    * Never touches JSON-LD structured data, ES modules, document.write scripts, or anything tagged `data-ht-no-delay`; keeps jQuery core live even in *All* mode; skips logged-in users, AJAX/REST/feeds/embeds/customizer/AMP; and carries a CSP nonce onto its loader when the page uses one. Optional fall-back timer.

= 1.2.26 =
* **Fixed: the Clean module's "Delete cropped image" tool crashed on sites that had the Media module switched off.** Its helper functions lived in the Media module, so clicking a size button returned a server error (nothing was deleted) whenever Media wasn't active. The helpers now live in the Clean module itself, so it is fully self-contained. Every other cleanup tool (revisions, autosaves, trash, comments, 404 media/thumbnails, scheduled cleanup) was unaffected.

= 1.2.25 =
* **Removed leftover Foxtool branding from the display-style preview thumbnails.** Seven of the eight "Customize display" preview images (Settings → General) still showed the old Foxtool logo baked into the corner; they now carry the Horse Tools mark like the rest of the plugin.

= 1.2.24 =
* **Cookie notice — big customisation upgrade** (Notify module → COOKIE tab). New **full-width bottom bar** layout in addition to the left/right corner box. You can now set the **Accept** and **Policy** button labels, and optionally show a **Decline** button with its own label. The visitor's Accept/Decline choice is stored in the browser (localStorage + an `ht_cookie_consent` cookie) so the notice never nags returning visitors — and so you can gate your own scripts on that cookie if you want stricter consent behaviour. Everything is still output on `wp_footer` (update-safe).

= 1.2.23 =
* **2FA recovery on the profile is now self-explanatory.** A new "If you lose your phone" panel lists every recovery channel the admin switched on: **email** (shows the exact address a code goes to) and **Telegram**.
* **The profile now shows WHICH Telegram bot to message.** "Detect my chat ID" only works after you've messaged the site's bot — but a user (e.g. an editor on someone else's site) had no way to know which bot that is. The profile now shows the bot's @username as a clickable link (Step 1 — open it and press Start), then the Detect button (Step 2).
* **Clear warning when the bot isn't set up.** If Telegram recovery is switched on but no bot token has been entered (the token lives in the WooCommerce module — easy to miss), both the Security settings page and the profile now say so, and link admins straight to where to set it. Regular users are told to use email or a backup code instead.

= 1.2.22 =
* **Fixed: on the 2FA code screen, the "code sent" and "wrong code" messages were replaced by the generic "Login failed" text** whenever the "hide login errors" (block user-enumeration) option was on — so you couldn't tell whether a Telegram / email recovery code had actually been sent. The 2FA screen now prints its own messages, unaffected by that filter.

= 1.2.21 =
* **2FA: a "Detect my chat ID" button on your profile.** It reads your Telegram chat ID from the site's own bot (no dependency on any third-party @userinfobot that could disappear) — message the bot, click the button, and pick yourself from the list. You can still type the number in manually.

= 1.2.20 =
* **2FA: Telegram recovery is now per-user.** Each user pastes their OWN Telegram chat ID on their profile (the site still uses one shared bot), so a recovery code always reaches that user's own Telegram instead of being pooled to the admin. Users who haven't set a chat ID simply don't see the Telegram option. (Email recovery was already per-user — it goes to each user's own account address.)
* **2FA: admins can now reset another user's two-factor authentication** from that user's profile (Users → edit the user → "Turn it off for this user") — the escape hatch when someone loses their device, without touching FTP.

= 1.2.19 =
* **New: two-factor authentication (2FA), opt-in and per user (Security tab).** Each user turns it on for their own account under Users → Profile: scan a QR code with Google Authenticator / Authy, confirm one code, and from then on the login asks for a 6-digit code after the password — so a stolen password alone is no longer enough. You also get one-time backup codes, plus an optional "trust this device for 30 days". If you lose your phone, recover with a backup code or — each toggleable in settings — a one-time code sent to your email or Telegram; last resort, switch the plugin off over FTP. Built entirely on standard WordPress hooks, so it's safe across WordPress updates and disabling the plugin restores the normal login instantly. Notes: XML-RPC password login is blocked for 2FA accounts (use an application password for automation), and wrong codes are rate-limited and count toward the login-attempt lockout.

= 1.2.18 =
* **New: a custom security question on the login form (Security tab).** Set your own question and answer; the wp-login.php page then asks it and refuses the login when the answer is wrong. It's a no-Google, no-badge, no-external-request way to stop the bots that hammer wp-login.php, and it loads *only* on the login page — your front-end and its speed are completely untouched. It won't stop a person who reads the visible question, so keep the login-attempt limiter on as well. The answer is matched ignoring case and spaces; if you ever forget it, switch the plugin off over FTP to get back in.

= 1.2.17 =
* **Fixed: the lightbox backdrop themes (Dark / Blur / Light / Cinema) had no effect.** The theme styles were printed before the lightbox library's own stylesheet, so — at the same CSS specificity — the library's default black backdrop always won and every theme looked identical. The theme now takes priority, so Blur (frosted glass), Light, Cinema and your accent colour actually show. Verified live on GLightbox. (The lightbox itself, its open/slide animations and looping were working correctly — only the backdrop appearance was affected.)
* Internal: the leftover `.fancybox` content-wrapper CSS class from the old engine was renamed to `.ht-lightbox` (no visible change).

= 1.2.16 =
* **The image lightbox is now free and open-source — Fancybox has been replaced with your choice of GLightbox or PhotoSwipe (both MIT-licensed).** Fancybox requires a paid licence for commercial sites; the two new engines are free for any use, so there is no licensing worry and nothing phones home (both are bundled in the plugin). Pick the engine in Content → Image lightbox:
  * **GLightbox** — the easy all-rounder: images plus video (YouTube/Vimeo/local), and it adds slide/fade/zoom transitions between images (which the old Fancybox build could not do).
  * **PhotoSwipe** — the smoothest pinch-zoom and pan, ideal for photo galleries.
* All the display options carry over: where it runs, gallery grouping with previous/next, caption source (alt / image caption / title / none), open animation, backdrop themes (Dark / Blur / Light / Cinema) and an accent colour, plus a zoom-in cursor on images.
* Removed the Fancybox-only "toolbar" and "thumbnail strip" options (the free engines have their own controls). The slide transition and looping apply to GLightbox.

= 1.2.15 =
Optimize tab — four new performance tools:
* **Control the Heartbeat API.** WordPress pings the server every 15–60s (autosave, post-lock, dashboard). Slow it to 60s, disable it on the front-end, or allow it only in the post editor — cuts admin-ajax.php load, especially with several admin tabs open.
* **Native image lazy-load + async decode.** The old “lazy load” removed each image’s src (bad for SEO and for visitors with JavaScript off). It now adds decoding="async" and relies on WordPress’ built-in native lazy-load, which correctly keeps the first/LCP image loading eagerly.
* **Preload critical assets.** Paste a few important URLs (the LCP image, a web font, the main CSS) and the browser starts fetching them immediately; the type is detected from the file extension.
* **Disable Dashicons for visitors.** Drops the admin icon font on the front-end for logged-out visitors, who never see it (kept for logged-in users, whose admin bar uses it).

Content tab — the image lightbox is now fully configurable:
* Choose where it runs (posts / posts + pages / all single content), group images into a gallery with previous/next, and pick the caption source (alt, the image caption/figcaption, the title, or none).
* Pick the open animation (zoom / fade / none), the toolbar (full / minimal / none), and toggle the thumbnail strip and looping.
* **New backdrop themes** — Dark, Blur (frosted glass), Light and Cinema — plus an accent colour for the toolbar and the active thumbnail. Images now show a zoom-in cursor so visitors know they can click.

= 1.2.14 =
* **New: Defer JavaScript (Optimize tab).** Adds `defer` to front-end scripts so they no longer block the page from painting — a direct Core Web Vitals / PageSpeed win. Scripts still run in their original order once the HTML is parsed. jQuery is never deferred (inline snippets rely on it), and any script that ships an inline companion is detected and left alone automatically, so nothing breaks. An exclusion box lets you skip a specific script by its handle or file name if a theme/plugin misbehaves. Leave it off if you already use a full-page optimiser that defers scripts.
* **New: Preconnect to third-party hosts (Optimize tab).** List the external hosts your pages load from (Google Fonts, a CDN, analytics…) and the browser starts the DNS + connection + TLS handshake early, so those files arrive sooner. Adds standard preconnect and dns-prefetch hints to the page head.
* Fixed: on the Google Index tab, checking a URL that Google reports as 404 (not indexed yet) could show a generic error instead of a clean “not indexed” result — a dead error-handling branch that never matched. It now reads the real HTTP status correctly.

= 1.2.13 =
* Fixed the lockout time-unit dropdown showing the wrong word for “Hours” in some languages (it collided with an unrelated “opening hours” translation). The three units now use their own labels.

= 1.2.12 =
* **New “Reset all login lockouts now” button** in Security → Limit login attempts. Clears every current lockout instantly — essential now that a lockout can last days. If you ever lock yourself out, sign in from a different network (your phone on mobile data is a different IP), then click it; or wait for the lockout to expire; or, as a last resort, rename the plugin folder via FTP to disable it. Works even on hosts with an object cache (Redis) — the plugin keeps its own list of locked addresses so it can clear them reliably.

= 1.2.11 =
Login lockout improvements.

* **Lockout length can now be set in minutes, hours or days** (was minutes only) — pick the number and a unit. The lockout message now shows a friendly “try again in about …” time.
* **New “site is behind Cloudflare or a proxy” option** so the lockout counts the real visitor IP. Off by default and safe: normally the lockout uses the direct connection IP, which can’t be forged. Only enable it when your site is reachable solely through Cloudflare/a proxy — it then reads the real IP from the proxy header (CF-Connecting-IP / X-Forwarded-For). Leaving it off on a directly-reachable site is the secure choice (those headers are spoofable).

Note: a successful login clears the failure count, but only while you are *not* already locked out — once an address is locked, even the correct password is refused until the lockout time expires. That is by design; it is what makes the lockout effective.

= 1.2.10 =
* **Fixed (the real root cause): the settings page could be cut off on a site where the chat feature wasn't set up yet.** The admin Chat tab always renders, but it called a Services helper that lives in a file only loaded when the chat module is active — so on a fresh/unconfigured site it hit a fatal “Call to undefined function”, which silently truncated the whole page (missing later tabs, the Save button, the sidebar and the footer scripts — which looked like tabs and other controls not working). The tab now loads that dependency itself, so the page always renders in full. This is the underlying cause behind the 1.2.8/1.2.9 tab symptoms.

= 1.2.9 =
* **Fixed (root cause): the plugin's admin script and colour picker now load in the page head instead of the footer.** On some hosts a heavy admin page silently dropped the plugin's footer &lt;script&gt; tags, so on the main settings page the tab handler, colour picker, sidebar search and dependent toggles all went missing (the head-loaded CSS was fine). Loading them in the head — which prints reliably — restores full functionality on those hosts. Complements the inline tab fallback added in 1.2.8.

= 1.2.8 =
* **Fixed: admin tabs could stop responding on some hosts.** On sites where a cache/optimise plugin (e.g. LiteSpeed) blocks, defers or combines the plugin's admin script, the tab buttons threw “httab is not defined” and nothing switched. Tab switching is now defined inline in the page head, so the tabs always work regardless of how the host handles scripts. (For full admin functionality on such hosts, exclude Horse Tools from JS optimisation.)
* Clearer Redirects tab names — “301 / 404 / 503” are now “Redirects (301) / Broken links (404) / Maintenance (503)” — each with a short “what it does / how to use” note. (First of a wider pass to make every section beginner-friendly.)

= 1.2.7 =
* **Snippet content box is now the familiar WordPress editor.** Instead of a code-only box, the snippet content field is the same Visual/Text editor you use when writing a post — type text and use the toolbar to insert links, images and formatting (Visual tab), or paste raw HTML/CSS/JS (Text tab). Much friendlier for non-coders; advanced code still works. Placeholders like {{url}} work in both tabs.

= 1.2.6 =
* **Fixed: settings that saved only through their own button now also save with the page’s main “Save content” button.** The Services panel (including the desktop “Services” launcher toggle) and the “turn shortcodes on/off” manager each have their own section Save; clicking the big Save at the bottom used to skip them, so a change like un-ticking the launcher appeared to “not stick”. The main Save now persists them too.

= 1.2.5 =
Friendlier admin, a much richer popup, and Vietnamese everywhere.

* **Site Health fix links now jump straight to the setting.** Each item in the “Site health” card opens the right tab and scrolls to the exact control (with a highlight), instead of just reloading the page to the first tab.
* **Plain-language “what it does / how to use” intros** added to sections, starting with the Notify/Popup page — aimed at first-time users.
* **Popup — big upgrade.** Beyond the 4 layouts it now offers **13 entrance effects** (fade, zoom, pop, slide in any direction, bounce, swing, rotate, flip, sharpen…), **4 display positions** (centre, bottom-right / bottom-left corner toast, full bottom bar), **4 triggers** (on load, after N seconds, after scrolling a %, or exit-intent), and an optional “wiggle to catch the eye”. Respects the visitor’s reduced-motion setting.
* **Vietnamese translations** filled in for all the new interface text (popup, snippet manager, icon picker, health, help intros).

= 1.2.4 =
A much bigger, more visual icon picker on the Chat tab.

* The icon picker now offers the **entire Tabler set (5,000+ icons)** instead of a fixed 24. Type to filter (Vietnamese keywords work for common icons too), and a **“Load more”** button reveals the rest in batches so the dialog stays fast.
* Every icon shows as a real preview, so you pick what you see.
* The **Services panel** icon fields now have the same **“Choose icon” button** right beside them — no more typing a Tabler name by hand or guessing. It fills the field for you; the mobile contact-bar SVG fields keep their in-place picker button too.

= 1.2.3 =
Live-chat polish for mobile, and a friendlier snippet manager.

* Fixed the **misaligned person icon** in the Livechat skin's default avatar — the placeholder is now a properly centred circle.
* The **business-hours status** (open / away) now also shows in the mobile **Services panel** header. On phones the floating chat widget is replaced by the bottom contact bar, so previously mobile visitors never saw whether the shop was open; now they do when the schedule is configured.
* **Snippet manager** — the content box is now a proper **code editor** (syntax highlighting, line numbers) using WordPress's own bundled CodeMirror. The many per-snippet conditions (device, visitors, schedule, tags) are tidied into a collapsible **“Advanced options”** section so the create form is simple by default. Added a **search box** and a live count to the snippet list so it stays manageable with hundreds of snippets.

= 1.2.2 =
Improved the built-in icon picker on the Chat tab.

* The picker now offers **46 icons** (up from 24) and its search matches **Vietnamese keywords** as well as the English name — typing "ship" finds the truck, "giảm giá" the discount tag, "mua" the cart, "tư vấn" the headset, and so on.
* Added a **“Choose icon” button directly under every SVG field** — the contact-button rows, the mobile contact-bar buttons, and any row you add later — so you can open the picker in place and it inserts into that exact field, instead of only from the tip at the top of the section.

= 1.2.1 =
A bug fix plus a big usability pass on the contact-chat admin screen.

Fixed:

* **On/off toggle switches were only half-clickable.** On the Extend screen and in the shared settings UI, the sliding on/off switch sat in a plain `<span>`, and the checkbox behind it was a zero-size hidden element — so clicking the switch itself did nothing and only the text label toggled it. The switch is now a real `<label>`, so clicking anywhere on it works, matching every other tab.

Chat admin — see what you pick:

* Replaced the stale illustration images and plain dropdowns with **live preview grids drawn in CSS** (no image files). The 17 button skins, the 5 mobile contact-bar styles, and the Services panel's 13 layouts, 7 theme colours and 7 display modes are now visual mockups you click, so you can tell what each one does before saving. The saved values are unchanged, so existing configurations keep working.

Icons from the plugin, not a third-party site:

* Removed the "go to lineicons.com and copy an SVG" instruction. Known channels (Zalo, Messenger, Phone, …) already show their own logo automatically; for a custom button, a built-in icon picker inserts a ready-made SVG (24 common icons, searchable) into the field. Services-panel icons get autocomplete for common Tabler icon names.

Added:

* A collapsible **Quick guide** at the top of the Chat tab explaining the whole flow in six steps.

= 1.2.0 =
A feature release: a full shortcode/snippet toolkit, a rebuilt multi-channel contact chat, a privacy module, and eleven new interface languages.

Shortcodes &amp; snippets:

* **Snippet manager** — create reusable snippets (content, HTML or PHP) and drop them anywhere with a shortcode. Each snippet has a display name, description, tags for grouping, a temporary-disable switch, an "off for administrators" switch, and a per-device (mobile / tablet / desktop) restriction — modelled on Shortcoder, which you can import your existing snippets from so you can leave that plugin behind without touching your published content.
* **20+ built-in shortcodes** — conditional display (`[ht-if]` / `[ht-else]` driven by easy dropdowns: logged-in state, role, device, date range, page type and more), layout (accordion, tabs, alerts, columns, buttons), dynamic data (post fields, counts, reading time), and media (QR code, click-to-load video facade, icons).
* **Shortcode tools** — find every post or page a shortcode is used on, an insert button in the editor, and a central on/off manager for the plugin's own shortcodes and snippets. It never rewrites shortcodes belonging to other plugins.

Contact chat, rebuilt:

* **30+ contact channels** — Zalo, Messenger, WhatsApp, Telegram, phone, SMS, email, Line, WeChat, Instagram, Facebook, X, YouTube, LinkedIn, Discord, TikTok, Shopee, Grab and more, each accepting either a username/number or a full URL.
* **17 button skins** for the launcher, an attractive mobile **Services panel** (13 layouts × 7 colour themes × 6 display modes, with badges) so you can put your key services and articles one tap away instead of buried in a menu, and a customisable panel title.
* **Extras** — a greeting bubble, business hours with an online/offline state, pre-filled WhatsApp/SMS messages, scan-to-open QR codes on desktop (including WeChat), and separate mobile / tablet / desktop visibility.

Privacy:

* **Self-host Google Fonts** — serve Google Fonts from your own server so no visitor request reaches Google, plus a scanner that lists the external requests your front end still makes.

SEO &amp; site management:

* **Automatic 301 redirects** when a post or page slug changes, with a 404 log so you can catch broken links.
* **Site-health audit** — a one-click score of your configuration with concrete suggestions.
* **Configuration presets** — apply a sensible starting set of options for a Blog, WooCommerce store, Performance or Security focus in one click.

Under the hood:

* Replaced the bundled commercial **Font Awesome Pro** icon set (which was not licensed for redistribution) with Tabler Icons in wp-admin and Font Awesome Free on the front end, and removed a bundled Times New Roman font file. All icons now ship under permissive licences (CC0 / MIT / SIL OFL).
* **`[gget]` download button** — fixed a stored/DOM cross-site-scripting hole: the target URL was stored base64-encoded (bypassing content filtering) and written to the page with `innerHTML`, so an Author could plant script that ran for any visitor. The URL is now validated to `http`/`https` on the server and built through the DOM on the client, with dangerous URI schemes blocked.
* Improved settings backup / import.

Translations:

* Added eleven interface languages: Indonesian, Thai, Chinese (Simplified), Japanese, Hindi, Spanish, French, German, Portuguese (Brazil), Russian and Arabic. See the Languages section. The translation string catalogue grew to 1,158 strings.

= 1.1.0 =
Security tab rebuilt: dropped features that were theatre or actively harmful, and replaced them with real hardening.

Removed:

* "Block non-image uploads" — it blocked installing plugins/themes from a ZIP and the WXR importer, and broke the plugin's own SVG upload, while protecting nothing.
* "Remove ?ver= from CSS/JS" — ?ver is the cache-buster; stripping it serves stale assets to returning visitors and CDNs after every update.
* "Prevent SQL injection / XSS" — ran only for logged-in non-admins, matched four fixed strings in the URL, and its 255-character cap broke long legitimate admin URLs.
* "Disable automatic update checking" (and its `remove_all_filters('plugins_api')`) — left the site unable to learn a security release existed and broke every other plugin's update mechanism.
* "Disallow text copying / DevTools" — put `user-select:none` on everything, so visitors could not select text in the search box, comment field or checkout, while stopping nobody.
* "Copy pre-set content" (clipboard replacement) — silently replaced whatever a visitor copied, breaking coupon codes, addresses and phone numbers.

Narrowed:

* "Disable automatic updates" is now "Automatic updates": it stops WordPress *installing* updates on its own (the supported, safe half) but the site keeps *checking*, so you are never left unaware of a security release.
* The copy feature is now append-only attribution: a line you set is added after the visitor's selection; nothing is replaced.

Kept: Disable REST API (now with a clear warning about what it breaks), Disable XML-RPC, wp-embed, X-Pingback, header cleanup, feed disabling, generator-tag removal.

Added:

* **Limit login attempts** — lock out an IP after N failed logins for M minutes, with an optional admin email. The real brute-force defence that replaces the fake "SQL protection".
* **Block user enumeration** — blocks `?author=N` scans, removes the users REST endpoint for anonymous requests, strips the author from oEmbed, and makes login errors generic.
* **Security response headers** — X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, optional HSTS (with a warning) and an advanced Content-Security-Policy field.
* **Disable the theme & plugin file editor** — closes the built-in code editor, a classic post-compromise escalation path.

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

== Upgrade Notice ==

= 1.2.94 =
Fixes a double-count risk and explains the extra step Tag Manager users need.

= 1.2.93 =
Fixes the measurement screen wrongly reporting that no analytics is installed.

= 1.2.92 =
Adds contact-click measurement, and stops redirects losing ad tracking parameters.

= 1.2.91 =
Fixes invalid FAQ schema on posts whose answers contain quotation marks. Update if you use FAQ schema.

= 1.2.90 =
Fixes font deletion and the redirects for addresses that moved into tabs.

= 1.2.89 =
Font settings now use the screen's own Save button. Brand artwork added.

= 1.2.88 =
Adds a standalone SVG of the brand mark, generated from the same source the plugin renders.

= 1.2.87 =
Table of contents entries now always jump to their heading, even where smooth scrolling is unavailable.

= 1.2.86 =
Fixes a critical error on the front end affecting every version since 1.2.67. Update immediately.

= 1.2.85 =
The table of contents icon is the Horse Tools mark, can be replaced with your own SVG, and has its own colour.

= 1.2.84 =
Removes the duplicate headings 1.2.83 missed on the original thirteen tabs.

= 1.2.83 =
Fixes two Save buttons on Appearance and removes duplicate headings inside tabs.

= 1.2.82 =
Fixes the Fonts tab opening blank. Update if you installed 1.2.81.

= 1.2.81 =
Fonts and Backup become tabs. The settings reorganisation is complete.

= 1.2.80 =
Fixes the code editor, media picker and table builder not loading after the regrouping.

= 1.2.79 =
Shortcodes and the table manager become tabs on the Content screen.

= 1.2.78 =
Fixes the cleanup schedule tab, which shipped empty in 1.2.77.

= 1.2.77 =
Add code, Debug and Clean become tabs on the Tools and Speed screens.

= 1.2.76 =
Notifications, ads and site search become tabs on the Customers and Appearance screens.

= 1.2.75 =
Fixes the table of contents tab opening empty on the SEO screen.

= 1.2.74 =
Fixes a critical error on the SEO screen in 1.2.73. Update if you installed that version.

= 1.2.73 =
Redirects, Index now and the table of contents move onto the SEO screen as tabs.

= 1.2.72 =
Fixes the site health panel links, which pointed at a screen that no longer holds settings.

= 1.2.71 =
Settings are now grouped across eight screens named after what they do. Nothing removed or renamed.

= 1.2.70 =
Adds a Security screen that reunites login protection from three separate tabs.

= 1.2.69 =
Adds an SEO screen holding settings that were previously buried in the CONTENT tab.

= 1.2.68 =
Internal: one Save button can now write several settings groups. No visible change.

= 1.2.67 =
Fixes a scheduled cleanup that never ran, and stops page views loading admin-only code.

= 1.2.66 =
Internal groundwork for the settings reorganisation. No visible change.

= 1.2.65 =
Fixes two FAQ schema faults found live: a duplicate FAQPage on posts that already had their own, and pasted code being published as answer text. Cached results recompute themselves.

= 1.2.64 =
Changelog wording fix; also the first release checked through the new CDN manifest.

= 1.2.63 =
Version checks move off the GitHub API onto the release CDN, so the plugin scales to any number of sites.

= 1.2.62 =
The FAQ schema number fields now show their defaults instead of appearing blank.

= 1.2.61 =
Adds automatic FAQ schema built from the FAQ section your posts already have, with a whole-site scan that shows exactly which posts qualify.

= 1.2.60 =
PHP snippets no longer hide themselves on sites that set DISALLOW_FILE_EDIT — a constant most hardened sites use.

= 1.2.59 =
Adds optional PHP snippets, gated behind two-factor authentication, a 15-minute unlock, pre-save syntax checking, auto-disable on crash and code signing.

= 1.2.58 =
Fixes missing row numbers and a duplicated merged total in printed/copied/exported tables.

= 1.2.57 =
Tables gain reader tools: row numbers, per-column filters, Copy/CSV/Print, show-hide columns, a frozen first column and search highlighting.

= 1.2.56 =
Unlabelled settings (like the contact-channel picker) now appear in search under their section name instead of an internal key.

= 1.2.55 =
Search now matches dropdown choices and help notes too, so "zalo" finds the contact-channel setting labelled only "Channel".

= 1.2.54 =
Search coverage is now complete: every setting on every screen is findable, including the older hand-written screens.

= 1.2.53 =
Fixes the sidebar panel (search box and "Currently enabled" list) never rendering — required for 1.2.52's plugin-wide search to be usable at all.

= 1.2.52 =
The sidebar search now covers the whole plugin: find any feature by name (accents optional), click, and land on the right screen, tab and control.

= 1.2.51 =
Tables get 3 new styles (Card, Dark, Soft pastel) and 7 new header colours including gradients.

= 1.2.50 =
Total rows can now be pinned to the bottom of the table (rendered as tfoot) so sorting, search and pagination never displace them.

= 1.2.49 =
Tables complete the TablePress-class set: CSV export, merged cells (#colspan#/#rowspan#), safe =SUM/AVG/MIN/MAX formulas, and per-table custom CSS.

= 1.2.48 =
Stored tables can now pull their data from a public Google Sheet and refresh hourly/daily — edit the spreadsheet, the website updates itself. No API key needed.

= 1.2.47 =
The table builder's grid becomes a mini-spreadsheet: insert/delete/move rows and columns anywhere, and Enter jumps to the next cell.

= 1.2.46 =
Tables gain click-to-sort columns, an accent-insensitive search box, and pagination — three checkboxes in the builder, ~5 KB of dependency-free JS.

= 1.2.43 =
Install this version once, and every future update is one click: WordPress downloads new releases directly from GitHub — no more uploading ZIPs through the browser.

= 1.2.42 =
Over a megabyte smaller (the 503 page's heavy GIF is gone), plus install-troubleshooting notes for the ERR_HTTP2_PROTOCOL_ERROR upload issue.

= 1.2.41 =
Installs much faster: the large Google API library (only for Google Login / SEO Indexing) now ships compressed and unpacks on first use, so a normal install handles ~280 files instead of ~760.

= 1.2.40 =
Fixes a bloated bundled Google library (~12 MB of dead autoload entries) that made the ZIP large and could stall installs on weak hosts. Much smaller download; no feature change.

= 1.2.39 =
Adds reusable stored tables: build once under Horse Tools → Tables, insert anywhere with [ht-table id="N"], edit once and every post updates.

= 1.2.38 =
The Table builder gains styles, header colours, a caption, and automatic right-alignment for number columns.

= 1.2.36 =
Adds a responsive Table builder to the editor — type it, paste from Excel, or upload an .xlsx/.csv — for both the Classic editor and the block editor.

= 1.2.35 =
Adds a “Horse Tools snippet” block so the block editor (Gutenberg) can insert snippets too, matching the Classic-editor button.

= 1.2.34 =
Adds a “Shortcode” button to the post editor (Classic editor / Classic block) that lists your snippets and inserts them at the cursor.

= 1.2.33 =
Fixes the security-question box on the login form being narrower than the username and password fields.

= 1.2.32 =
Adds a “One-click safe speed setup” button to the Optimize screen so anyone can enable the safe speed features without touching the presets page.

= 1.2.31 =
The Performance/Blog/WooCommerce presets now enable the new safe speed features (delay + defer JS) in one click — no technical setup needed.

= 1.2.30 =
Adds "Load CSS without blocking render" (async CSS) with a Critical CSS box to the Optimize module — a major First Contentful Paint win. Test the front end after enabling.

= 1.2.29 =
Safety hardening for Delay JavaScript: it now only delays real JavaScript, never speculation rules, Partytown or other inert script types — protecting the aggressive "All" mode.

= 1.2.28 =
Adds a one-click "Scan the scripts running on the home page" button so you can see what your site loads and choose what to delay or exclude — no technical knowledge needed.

= 1.2.27 =
Adds "Delay JavaScript until interaction" to the Optimize module — a major Core Web Vitals win for Total Blocking Time. Test the front end after enabling.

= 1.2.26 =
Fixes the Clean module's "Delete cropped image" tool crashing when the Media module was switched off.

= 1.2.25 =
Cleans the last of the old Foxtool logo out of the display-style preview thumbnails, replacing it with the Horse Tools mark.

= 1.2.24 =
Cookie notice upgrade: new full-width bar layout, customisable Accept/Policy/Decline button labels, an optional Decline button, and the visitor's choice is remembered.

= 1.2.23 =
Clearer 2FA recovery: a "If you lose your phone" panel lists email + Telegram, shows the bot's @username to message, and warns (with a link) when the bot token isn't set up yet.

= 1.2.22 =
Fixes the 2FA code screen showing a generic error instead of the real "code sent" / "wrong code" message.

= 1.2.21 =
2FA: adds a "Detect my chat ID" button that reads your Telegram chat ID from the site's own bot.

= 1.2.20 =
2FA: Telegram recovery codes now go to each user's own chat, and admins can reset a locked-out user's 2FA.

= 1.2.19 =
Adds opt-in, per-user two-factor authentication (TOTP) with backup codes and optional email/Telegram recovery.

= 1.2.18 =
Adds an optional custom security question on the login form — a lightweight, no-Google anti-bot layer that loads only on wp-login.php.

= 1.2.17 =
Fixes the lightbox backdrop themes (Dark / Blur / Light / Cinema) and accent colour, which had no visible effect in 1.2.16.

= 1.2.16 =
The image lightbox is now free/open-source: Fancybox is replaced by your choice of GLightbox or PhotoSwipe (both MIT). No commercial-licence worry; adds video and slide transitions.

= 1.2.15 =
Four new Optimize tools (Heartbeat control, native lazy-load, Preload, disable Dashicons) and a fully configurable image lightbox (themes, animations, toolbar, captions, thumbnails).

= 1.2.14 =
Adds “Defer JavaScript” and “Preconnect” to the Optimize tab for faster page loads (Core Web Vitals), plus a Google Index status fix.

= 1.2.13 =
Fixes the lockout time-unit dropdown mislabelling “Hours”.

= 1.2.12 =
Adds a “Reset all login lockouts” button so you can undo a lockout instantly (important if you use a multi-day lockout).

= 1.2.11 =
Login lockout can now be set in minutes/hours/days, and can count the real visitor IP when the site is behind Cloudflare/a proxy.

= 1.2.10 =
Root-cause fix: the settings page no longer gets truncated on sites where the chat feature isn't configured yet (a fatal in the Chat tab cut off the rest of the page).

= 1.2.9 =
Loads the admin script + colour picker in the head so the whole settings page works on hosts that drop footer scripts.

= 1.2.8 =
Fixes admin tabs not switching on hosts whose cache/optimise plugin blocks the admin script; clearer Redirects tab names.

= 1.2.7 =
The snippet content box is now the familiar WordPress Visual/Text editor with insert-link and insert-image buttons, instead of a code-only box.

= 1.2.6 =
Fixes Services panel and shortcode on/off changes being lost when you use the page’s main Save button instead of the section’s own Save.

= 1.2.5 =
Site Health links now jump to the exact setting, the popup gains 13 effects / positions / triggers, and all new text is translated to Vietnamese.

= 1.2.4 =
The Chat icon picker now covers all 5,000+ Tabler icons with search + load-more, and the Services icon fields get their own “Choose icon” button.

= 1.2.3 =
Fixes the off-centre Livechat avatar, shows open/away status in the mobile Services panel, and adds a code editor + search to the snippet manager.

= 1.2.2 =
Bigger icon picker (46 icons) with Vietnamese keyword search, and a “Choose icon” button under every SVG field.

= 1.2.1 =
Fixes the half-clickable on/off switches and rebuilds the chat admin with visual preview grids and a built-in icon picker. Settings are preserved.

= 1.2.0 =
Adds a shortcode/snippet toolkit, a rebuilt multi-channel contact chat, a privacy module and eleven new languages. Includes a fix for a stored XSS in the [gget] download button — upgrading is recommended.

= 1.1.0 =
Rebuilds the Security tab: removes features that were security theatre and adds real hardening (login-attempt limiting, user-enumeration blocking, security headers).
