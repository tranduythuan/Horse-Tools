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
Stable tag: 1.3.38

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

= 1.3.38 =
* **Horse Tools now checks whether your server can reach any other mail server at all — the question nobody asks, and the answer that explains most vanished email.** WordPress hands each message to a local mail program, that program accepts it (which is why every screen says "sent"), and then finds outbound port 25 closed. DigitalOcean, Google Cloud and many others close it by default so their machines cannot be used for spam. The message waits in a queue nobody reads, and no bounce arrives, because a bounce would have to leave by the same door.
* This is reported **first**, above everything else, because no DNS record can change it. A site in this state can spend weeks adjusting SPF while every message goes on sitting in a queue.
* Diagnosed from two sites on one server with opposite DNS — one publishing a hard-fail SPF, the other publishing none at all — both reporting mail sent and neither delivering anything. Different DNS, identical outcome, so the DNS was not the cause. The earlier releases' focus on SPF was right about what those records say and wrong about why the mail was disappearing.
* The check is a bare TCP connect to a well-known mail exchanger, held for half a day. Nothing is sent and no message is involved; it asks only whether the door opens. It is skipped entirely when the site already sends through an SMTP service, because those use a different port.

= 1.3.37 =
* **If you have already paired Telegram for two-factor recovery, security messages now go there — no extra setup.** Asking for a chat ID was asking a question the plugin had already answered. A site with the bot token filled in and an administrator paired has a working bot, a known chat and a proven route; the alert code reported "no Telegram" anyway and pushed everything into email, because it only knew to look at the WooCommerce order chat field.
* Found on a live site whose owner said the bot had been set up ages ago. They were right — token present, bot reachable, chat on file since the day they turned on two-factor recovery. The gap was mine.
* The screen names where the chat came from, so a site that never typed one anywhere is told why it is suddenly receiving Telegram messages. A dedicated field still wins, then the order chat, then the paired one.

= 1.3.36 =
* **The check-in message now falls back to the other channel — and says so, loudly.** If Telegram will not send, the message goes by email instead, with a line at the top naming the channel that failed and repeating the reason it gave. A quiet fallback is the classic mistake: Telegram breaks, email carries everything without comment, and a year later you believe you have two channels when you have had one since March — and find out when the second one goes too. The delivery is not the news; the failure is.
* Arriving by the backup route is therefore recorded as a **fault**, not a success. It shows on the health card and in the "not protected" list until the main channel works again.
* **Domains you approved a long time ago are now brought back for a second look.** A domain can go bad without anything on your site changing: it expires, somebody else buys it, and a link approved in 2019 points at whatever they sell now. Nothing here moved, so nothing here noticed.
* That nudge is filtered so it cannot become a wall. A site that approved 686 domains in one click would otherwise get all 686 back a year later, which is not a review. Only domains that are **both** over a year old **and** linked from one or two posts are raised — a domain you link from two hundred posts is one whose going bad you would hear about from a customer within the week; a domain reached once, from an article nobody has opened since 2019, is the one that can change hands in silence.
* "Checked — still fine" only resets the clock. It never changes what is approved.

= 1.3.35 =
**A self-audit of everything added this week. Five holes, two of them serious.**

* **You could not clean up.** Both inventories only ever grew: a domain or a number found once stayed on the list, because the steady-state pass reads only posts that changed and a post that no longer contains something cannot report its absence. So the response to an incident broke at the exact moment it mattered — somebody injects a link, the plugin says so, you clean the post, and the domain is *still* unapproved, the health row is *still* red, and the only button that makes it stop is the one that approves the attacker's domain. Doing the right thing left the alarm on; doing the wrong thing turned it off. There is now a **"Read all my content again"** button, and the lists come back matching what is actually there.
* **The anchor was guarding the wrong door.** It watched the approval lists — the expensive thing to tamper with — and not the switches that decide whether any of it runs. An attacker with database access would not add their domain to the approved list; they would set the render guard to "off" and the check-in message to "off", and every alarm would go quiet with no alarm about the quiet. The switches are anchored now.
* **And re-anchoring is per-item, not wholesale.** Writing every mark on every decision would have made the anchor easy to launder: tamper through the database, wait for the owner to confirm something unrelated, and the mismatch is written over as though agreed to. Each anchored thing is refreshed only by the act that legitimately changes it.
* **A confirmed email delivery no longer counts for ever.** Deliverability is not a property of your settings, it is a relationship between two mail systems that drifts. After ninety days the row asks to be tested again rather than resting green on year-old evidence.
* The render guard rebuilt the full approved list for every post — six hundred and eighty-six entries, twenty times over on an archive page, for an answer identical every time. And the check-in message could block an admin page for fifteen seconds; cron now gets first refusal and the admin hook only steps in once a beat is properly late.
* The review screen called a function from another file with no guard, and would have fataled if the two ever loaded apart. The test suite found that one by dying.

= 1.3.34 =
* **Horse Tools now tells you whether your site is allowed to send mail as its own domain — and whether anything it sends actually arrives.** These are the two questions behind almost every "the customer never got the email", and WordPress answers neither.
* **The diagnosis reads your domain's own DNS records, live.** No account, no API key, no external service: SPF, DMARC and MX, and an evaluation of the SPF record against this server. It is deliberate about the third answer — where the honest result is "cannot tell from here", it says that instead of guessing. A checker that cries wolf is one people learn to ignore, and this one prints an accusation on a shop owner's screen.
* It will not accuse the web server when the site is sending through an SMTP service, because then it is the service's address being checked and not yours — that false alarm is the classic one.
* **It guesses the right provider from your MX records.** If your domain receives mail through Google or Zoho, sending through the same service is the setting most likely to work, and the screen says so rather than expecting you to know.
* **"Test email sent successfully" now means what it says, which is very little.** `wp_mail()` returning true means the message left the building. So the test sends, then asks the one participant who can actually see the result: did it land in the inbox, in spam, or nowhere? The answer is kept with the date, and the health card reports what was observed instead of what was switched on. Change the sender or the password and the proof stops counting, because it is no longer about the thing that is running.
* Covered by `tools/test-mail.php`: 54 checks, most of them about the SPF evaluator refusing to conclude — `ptr`, `exists:`, a record that includes itself, the ten-lookup limit, and a TXT record split into chunks that would evaluate to something else entirely if read short. The suite never touches real DNS; it runs in seven hundredths of a second.

= 1.3.33 =
* **The blue button on the outbound-links screen approved fifty of six hundred and eighty-six.** Somebody who meant "yes, all of this is mine" pressed the biggest button on the page, got 7% of the way, and had thirteen more pages to go with nothing on screen saying so. When the waiting list runs past one page, agreeing to the whole list is the common intent and now gets the primary button; the per-page action is secondary and states its own scope.
* **Approving a page now says how many are left.** "Approved 50" and nothing else reads like the job is finished.
* The health row said "a domain you have not approved", singular, on a site where 636 were waiting — which reads as one stray link to go and look at rather than a list barely started. It says the number now.

= 1.3.32 =
* **The contact banner had the same fault the domain list just had, somewhere else.** A knowledge blog with 866 posts turns up 31 contact details — most of them example addresses out of tutorials about email — and all 31 were printed into a banner 810 pixels tall, sitting on top of every screen in the plugin.
* Twelve are shown and the rest fold into one line you can click open. Nothing is hidden and nothing is dropped; a wall is not more informative than a list, it is less, because nobody reads it and the confirm button underneath becomes a formality.
* Rarest first, the same reasoning as the domain list: the hotline appears in two hundred posts and a number somebody quietly added appears in one, so ordering by how often each was seen puts the odd one where the eye lands.

= 1.3.31 =
* **The review screen was one table of every domain, which on a real site meant 686 rows.** That is not a review, it is a wall — and it hid two real faults behind the length.
* **It could not be saved.** The form posted a hidden field and a checkbox for every domain: 1372 fields on that site, against PHP's default `max_input_vars` of 1000, which drops the rest without a word. You would tick everything, press save, and several hundred domains would quietly stay unapproved with nothing on screen to explain it. Nothing on this screen scales with the number of rows any more — only the exceptions are posted, and “Approve everything” sends one field.
* **It was slow for a reason that had nothing to do with the size of the list.** Every row printed up to eight post titles, which is five thousand title lookups on one page. Post titles now appear only in the short list where you would actually go and look, and the posts on a page are fetched in one query instead of one each.
* **Two lists instead of one**, because they are two different jobs: *Waiting for you* is a decision to make, fifty at a time, worst first; *Already approved* is a reference you occasionally look something up in, collapsed, with a search box and no per-row cost.
* Fixed while rebuilding: saving the guard setting on its own used to record that a review had happened. On a site that had approved nothing, that is the exact state in which the guard defuses *every* outbound link — so choosing what happens to unapproved domains is now firmly not the same act as saying which domains are approved.

= 1.3.30 =
* **Makes 1.3.29 actually apply to the sites that needed it.** Raising the link inventory's ceiling changed how much a collector keeps, which is a change in how it reads — and that has to bump the scan signature or nothing re-reads. It did not. So on the one site that had already hit the old ceiling, the inventory stayed truncated at 400 and the flag that reports truncation, which is only set while collecting, stayed unset. The site carried on reporting that its content was watched. A fix that only reaches installations which have not yet hit the bug is not a fix.
* Every site now re-reads its content once. On a small site that is a few seconds; on a large one it continues a batch at a time as you use the admin, and the screens say so while it runs.

= 1.3.29 =
* **The outbound-link list stopped recording at 400 domains, silently.** Found by running it on a real site rather than by thinking about it: a blog of 866 posts that cites its sources went straight through the ceiling, and from that point the inventory quietly ignored everything new. That is the worst way for this to fail — a list with gaps in it still answers "is this domain new?", and answers "no" for every domain it never managed to record.
* The ceiling is now 2000, which is roughly 300 KB in an option that does not autoload. More importantly, **reaching it is now reported** instead of passing in silence: the health row goes red, the screen says the list is incomplete, and neither pretends the content is being watched.
* On a site with a long list, the review screen now says what the first pass is actually for. Nobody audits four hundred rows, and pretending otherwise just makes the screen a lie — the point is to agree that today's list is the baseline, so that tomorrow's arrival stands out.

= 1.3.28 =
* **A link to a domain you never approved can now be defused while the page is being printed.** Everything else here reports, and reporting leaves a window: the link goes in, the warning appears on a screen, and the link keeps working until somebody logs in and reads that screen. On the site this was written for, that window was two years wide. This closes it — whatever the link was worth to whoever put it there stops being worth that within one page view.
* Two strengths, on the Outbound links screen. **Add `nofollow`** keeps the link working for a reader and stops it passing any SEO value, which is the entire reason a link like that is worth paying for. **Take the link away, keep the words** stops the click too, and is more likely to get in your way.
* **Nothing is written to your posts.** Only what gets printed changes. Switch it off and every link is back exactly as it was on the next page load.
* **Off by default, and silent until you have been through the list once.** Without that second condition, installing this update would have quietly nofollowed every outbound link on every site — the partner, the courier, the payment gateway — because nothing had been approved yet.
* Every other gate fails open too: relative links, page anchors, `mailto:`, `tel:`, `javascript:`, your own domain and its `www.` are all left untouched by construction, not by a list. A subdomain of an approved domain is *not* covered by it, which is deliberate — `promo.example.com` is frequently not the same people as `example.com`, and on a compromised site it is frequently not the same people on purpose.
* Host normalisation and the approved list moved to `inc/link-list.php`, so a product page reaches them without loading six hundred lines of review screen. The inventory and the screen stay behind `is_admin()`.
* Covered by `tools/test-link-guard.php`: 43 checks, most of them the not-list — the cases where touching the markup would break a working shop. 504 checks across thirteen files in total.

= 1.3.27 =
* **The debug log was downloadable on every nginx site, and this fixes it rather than warning about it.** Switching on WP_DEBUG_LOG here writes the log to `wp-content/horsetools-logs/` instead of WordPress' public `wp-content/debug.log`, with an `.htaccess` in the folder saying deny. nginx has never read an `.htaccess` and never will. Verified on a live host on 7 August 2026: requesting the `.htaccess` itself came back HTTP 200 with all 134 bytes of it. The log beside it was served the same way — database queries, absolute server paths, fragments of failed requests — and the only thing in the way was a sixteen-character file name.
* The log is now a `.php` file whose first line is an opening tag and an `exit`. A server that hands it over hands it to PHP, PHP stops on the first instruction, and the response is zero bytes — on Apache, nginx, Caddy and IIS alike, with no configuration and no secret to keep. This is exactly what the anchor file has done since 1.3.26; the log should have been doing it from the start.
* **A log written by an earlier version is taken in rather than left or deleted.** Its contents are appended to the protected file and the exposed copy is removed, and this happens whether or not logging is switched on now — turning the feature off last year did not un-publish the file. `wp-content/debug.log`, WordPress' own default, is taken in the same way when logging is on.
* The one file it will not move is the one WP_DEBUG_LOG still points at. On a site where wp-config.php is not writable, renaming it would only have PHP recreate the same exposed name on the next notice, on every admin page load, for ever. There the honest outcome is the exposure warning, and that is what happens.
* **The "Clear all" button no longer truncates the guard away.** It used to write an empty string, which on a `.php` log leaves a file with no first instruction — served in full the moment the next notice is appended to it. A protection that holds until somebody presses Clear is not a protection. The log viewer hides that first line too, since it is plumbing and showing it invites somebody to helpfully delete it.
* **A downloadable file left in this plugin's own folders is now reported as one.** Not "this server ignores .htaccess", which is true of a very large share of WordPress sites, permanently, with nothing the owner can do about it — a warning that is always on is a warning that is never read. It becomes a finding only when the server is one that ignores `.htaccess` **and** there is a file in those folders that is not protecting itself, and then it names that file. It joins the same list, the same count and the same health row as a stray wp-config copy, because to the owner it is the same sentence.
* Which server is in front of PHP is read from `SERVER_SOFTWARE` and nothing else. A loopback HTTP request would be a stronger test and is deliberately not used: it is slow, and the hosts that block loopback requests are exactly the hosts where the answer matters. Apache and LiteSpeed are recorded as reading `.htaccess`; nginx, Caddy, lighttpd, OpenResty and IIS as not; anything unrecognised as **unknown** rather than guessed at. The answer is remembered so that WP-Cron and WP-CLI, which have no `SERVER_SOFTWARE` at all, do not report something different from the screens.
* Uninstalling now removes the `.htaccess` from the log folder as well. `glob()` does not return dot-files, so the old cleanup left one file behind and the folder with it.
* Covered by `tools/test-debug-log.php`: 53 checks. The central one does not compare strings — it runs the log file through the PHP binary and requires the output to be empty, including after twenty appends, after the Clear button, and after somebody deletes the file by hand and PHP recreates it. `tools/test-watch-exposure.php` grows to 32, most of the new ones about the warning staying silent when nothing is actually exposed.

= 1.3.26 =
* **What you approved is now kept outside the database as well as in it.** Every baseline here lived in `wp_options`: the contact details you confirmed, the domains you approved. That is fine against somebody editing posts and useless against somebody who can write to the database directly — an injection in another plugin, a leaked database password, a backup tool with its own hole. They do not need to hide their link; they add their own domain to the approved list, and from then on the watcher reports "all clear" about it for ever. A watch that vouches for the attacker is worse than no watch.
* Confirming through a screen writes both copies. A hand at the database can only reach one. When the two disagree, something changed what you agreed to without going through the screens — and that is the loudest thing this plugin says.
* **It leaves the innocent explanation open**, because there is one: restoring a database backup, or copying a database from staging, moves the options without moving the files and lands here honestly. The warning offers "that was me" and a button, rather than announcing a break-in to somebody who just restored a backup.
* **Only decisions are fingerprinted, never observations.** What the scanner found changes whenever a post is edited and is supposed to; what a person agreed to changes only when a person agrees to something. So a phone number appearing five more times is not a change, and a domain being added to the approved list is.
* The anchor is found by looking in the folder, not by reading a path out of an option — otherwise the same attacker deletes one row and the plugin decides there never was an anchor. Deleting the file itself is loud.
* **What it does not defend, stated in the code rather than left to be discovered:** an attacker with a real administrator account presses the same button you do and both copies update — only the check-in message reaching a human catches that. An attacker who can write files rewrites the anchor as easily as the database. It defends exactly the middle case, which is also the common one.
* Covered by `tools/test-anchor.php`: 35 checks, most of them about what must **not** raise the alarm — an edited post, a changed count, a re-confirmation, an anchor written by an older version — plus two that prove the limits above are real.

= 1.3.25 =
* **A numbered check-in message, so that silence becomes a signal.** Every other watcher here reports trouble, and none of them can report the one failure that matters most: themselves stopping. Switch the plugin off, delete the options, let the site die — everything goes quiet, and quiet is exactly what a site with nothing wrong also looks like. Two years of casino links looked like quiet too.
* So the site now sends you a short message on a schedule, with a number on it, **even when nothing is wrong**. Three different failures each leave their own mark: numbers that skip mean messages were sent and never reached you (a blocked bot, a spam filter); a message that arrives late means nothing was running the schedule; nothing at all means something stopped it — and that is the one you can only notice from outside the site, which is why every message says when the next is due.
* The counter advances on every **attempt**, not on every success. If it only advanced on success there would be no gap to see, and a channel that has been failing for a month would look identical to a month with no messages due.
* The message carries the state, not just "still here" — settings contacts, content contacts, outbound domains, exposed files, each with a plain OK or a warning. A heartbeat that says nothing is one you stop opening, and then the number in it stops being read too.
* Telegram if the site has a bot, email otherwise, with a **test button** that reports what the channel actually said — "chat not found", "bot was blocked by the user" — instead of just "failed". A channel nobody has ever seen work is not a channel.
* **An honest list of what is not protected**, on the health card and the settings screen. A green tick meaning "this is switched on" is not the same as "you would find out", and the difference is the whole point.
* Driven by WP-Cron **and** by admin page loads, because neither alone is enough: cron does not run on a site with no visitors, and an admin hook does not run on a site whose owner does not log in. The cron half is loaded outside the `is_admin()` gate — registering a cron hook only in requests where cron never runs is how this plugin once shipped a scheduled task that never fired once.
* Covered by `tools/test-heartbeat.php`: 54 checks, including that a failed send still advances the counter, that a failure does not turn into a retry loop on every admin page load, and that the "not protected" list says so while it is true and stops when it stops being true.

= 1.3.24 =
* **Fixes the "These are correct — remember them" button, which did nothing on a site that had already confirmed its settings.** An early `return` sat between the button and the script that made it work, so in exactly that state — settings agreed, content read, content waiting for its first confirmation — the button was printed with nothing listening to it. It looked identical to the two cases that did work. A confirm button that silently does nothing is worse than no button at all: you believe you have agreed to a baseline that was never written, and the watch you think is on is off.
* The handler is now delegated from the document rather than bound to the button. WordPress moves admin notices around after the page is parsed — that is how this same banner once ended up inside a hidden tab pane — and a handler attached to a node that is then relocated is a handler that quietly stops existing.
* Two `static` caches removed from the contact status functions. They saved one array_diff and froze the answer for the rest of the request, so anything that confirmed a baseline and then asked again was told the old answer.
* `tools/test-watch-contact.php` now renders the banner in each of the states a site can actually be in and checks that wherever the button appears, the thing that makes it work appears too. Reintroducing the bug fails exactly the two cases it broke.

= 1.3.23 =
* **Fixes the "Outbound links" screen, which 1.3.22 made impossible to open.** It was registered under the plugin menu and then hidden again with `remove_submenu_page()` — the usual trick for a screen you arrive at from a warning. It does not work: WordPress decides whether you may open a plugin page by looking the slug up in the submenu and reading the capability off the entry it finds there, so removing the entry answers every visit, administrators included, with "Sorry, you are not allowed to access this page."
* It now sits in the menu next to Security, where it belongs. "Where does my content link to?" is a question this dashboard could not answer at all until yesterday; putting the answer behind a warning that only shows up once something is wrong gave most of that back.

= 1.3.22 =
* **Horse Tools now keeps a list of every other website your content links to.** This is the one aimed squarely at what actually happens: an administrator account nobody recognises edits three old posts, leaves links in them, and the site runs that way for years. Nothing breaks, nothing looks wrong, and no amount of reading the dashboard would show it — because until now the dashboard had no list of who the site links to.
* The unit is the domain, not the link. A page with forty links to eight domains is eight decisions, and a person will make eight. It also survives the obvious evasion: changing the path, the link text or the tracking parameters every week does not change the domain, and the domain is the thing being paid for.
* **A first-review screen, with the rarely-linked domains at the top.** The one added without your knowing is almost never the one you link to from two hundred posts, so the ordering does the work — and it needs no list of known-bad domains to do it. There is no keyword blacklist in this plugin and there is not going to be one; a list like that is out of date the week it ships.
* Reads the ways a link hides from someone skimming: the protocol-relative `//domain/…`, the unclosed anchor tag, the bare URL, single-quoted and unquoted attributes, a subdomain of a domain you do trust. A `<script>` or `<iframe>` loading from somewhere else is called out separately — that only reaches a post body if whoever wrote it was an administrator.
* Whether a link passes SEO value is shown per domain, because a `nofollow` link is not what a paid link is bought for.
* **One walk over your content instead of one per watcher.** The contact watch and the link inventory now share a single batched pass, so eight hundred post bodies are read once. The cursor carries a signature of what is reading it: add a watcher, or change how an existing one reads a post, and the pass starts again by itself rather than reporting old results as current. Covered by `tools/test-watch-scan.php` (30 checks) and `tools/test-watch-links.php` (67 checks).

= 1.3.21 =
* **The contact watch now covers your posts and pages, not just your settings.** A number swapped inside an old article is the same attack as one swapped in the chat button, and on a site with hundreds of posts nobody re-reads the old ones. Content is walked in batches that resume where they stopped, and once the first pass is finished only what has been edited is read again — on a site where nothing changed, that costs one indexed query.
* Content is judged on what **appeared**, not on what left. A number vanishing usually means a post was deleted or rewritten, which is ordinary work and would be a weekly false alarm; a number appearing is the swap, because whoever edits one post leaves the original standing in all the others.
* Its baseline is kept separate from the settings one. Sharing it would mean that the moment the first pass finished, every number in every post appeared as a change on a site whose owner had just confirmed everything — an alarm caused by nothing but the plugin's own progress.

= 1.3.20 =
* **Horse Tools now checks whether your site is handing out its own secrets.** A stray copy of wp-config, a database dump, a .git directory, a forgotten phpinfo — these are the first things anyone scanning a WordPress site asks for, and finding one is worth more to them than every casino link they could inject. It looks for the short list of files that should never sit in a web root, and says something only when one is actually there.
* It does not report being scanned. Every site on the internet is probed for these constantly; an alert about it is one you learn to ignore within a week. The 404 log the redirects module already keeps is used for the other half of the sentence instead — not "you are being scanned", but "the thing somebody went looking for on this site is here", which is the only reading of a 404 log worth waking up for.
* Covered by `tools/test-watch-exposure.php`: 20 checks against real files in a throwaway folder, including that a clean site stays silent, that the real wp-config.php is not mistaken for a stray copy of one, and that nothing breaks when the redirects module — and so the 404 log — is switched off.

= 1.3.19 =
* **A chat button's icon is no longer mistaken for your phone number.** Custom icons are stored as inline SVG, and 1.3.17 — which taught the watcher that a leading 00 means an international number — started scraping the digits out of one: `viewBox="0 0 24 24" … stroke-width="1.8" … d="M4 5h16v10H8l-4 4z"` reduces to seventeen digits beginning 00. It was listed as a contact number, with the whole SVG printed as its value. A phone number now has to be a short string containing nothing but a phone number, which is what it always should have been.

= 1.3.18 =
* **The contact and signing-key warnings are now where you can see them.** WordPress moves anything styled as an admin notice with a line of JavaScript, dropping it after the first heading inside the page — and on a Horse Tools screen that lands it *inside a tab*, so whichever tab happened to be open decided whether you ever saw it. The warning was there, in the page, and invisible. Both now render as their own banner at the top of the screen, in or out of any tab.

= 1.3.17 =
* **The contact watcher now recognises foreign numbers instead of ignoring them.** It converted `+84…` to the usual `0…` form and threw away everything else, so replacing your hotline with an overseas number was noticed only because the old one had vanished — and *adding* one was not noticed at all, which is the easier attack and the quieter one. A leading `+` or `00` is now taken as the statement it is, the same way `tel:` is; a bare run of digits still has to look like a Vietnamese number, which is what keeps prices and order references out.

= 1.3.16 =
* The contact-details notice now shows you the details. It said "found 8" and asked you to confirm them without listing what the eight were — which is asking somebody to click yes at nothing, and is exactly how a confirmation step turns into a formality nobody reads.

= 1.3.15 =
* **Horse Tools now watches your own contact details.** Your hotline, Zalo, Messenger and email are read out of your settings, you confirm them once, and you are told if any of them ever change. Swapping the number on a shop's floating chat button is the most direct attack there is — one value, one option, every page, and it takes the customers straight to somebody else. There is no link left behind to find and nothing for a link scanner to see.
* It compares, it does not guess. Trying to work out which number on a page "is the hotline" means being wrong about prices and product codes; recording what is there and reporting a difference does not. `0988.34.34.12`, `0988 34 34 12` and `+84988343412` are recognised as the same number, so reformatting your own hotline is not reported as a change — and `1.790.000` is not reported as a phone number.
* The settings are walked rather than named. The chat buttons keep their value in `chat-nut3<n>` today, the navbar and services panel elsewhere, and those names have moved before — a watcher that has to be told each key is one that goes quiet the next time one is renamed.
* **Cleanup no longer deletes the last month of post revisions.** Revisions are the only way back after somebody edits a published post, and the only source of a diff showing what was added. Horse Tools deletes them, and can be set to do it on a schedule, so it was quietly removing its own recovery path. Anything older than thirty days still goes — that is where the space actually is — and the preview count now matches what will really be removed.
* Covered by `tools/test-watch-contact.php`: 36 checks, most of them about what must **not** be flagged.

= 1.3.14 =
* **Horse Tools now tells you where this site's signing keys are kept.** Two of its protections are signed with `wp_salt()`: the PHP snippet signature, which exists so that code written straight into the database — the usual pay-off of an SQL-injection hole in some *other* plugin — is refused, and the "trusted device" cookie that lets a browser skip two-factor authentication for thirty days. Both promises hold only while the key is in a file. WordPress reads it from wp-config.php when the constants are there, and quietly generates one into the options table when they are not — the very place such an attacker already is. Neither WordPress nor this plugin has ever said so. The Site Health card now reports it, and a notice on the plugin's own screens offers eight freshly generated lines to paste. **Nothing is written to wp-config.php; the lines are yours to copy.**
* The check compares what `wp_salt()` actually produced against what the options table holds, rather than second-guessing WordPress's own rules about empty, placeholder and duplicated constants. A site with one half in a file and the other in the database is reported as fine, because it is — the half an attacker cannot see is still 64 characters of entropy.
* Covered by `tools/test-salt-location.php`: 17 checks, including the case that must not be got wrong in either direction — a partial match reported as fully exposed would train owners to ignore the notice, and a miss would leave the snippet signature quietly worthless while the health card said everything was fine.

= 1.3.13 =
* **Security: the Debug module no longer leaves a readable copy of wp-config.php on the server.** Before changing a debug constant it kept the original as `wp-config.php.horsetools.bak`, right beside the real one, so that a bad write could be undone over FTP. The intent was sound and the filename was the problem: `.bak` is not a PHP extension, so a web server hands that file over as plain text — database password, every salt, the table prefix — to anyone who asks for it by name. An `.htaccess` would not have covered nginx. The copy is gone: wp-config.php is now written to a temporary file and renamed into place, which is atomic and protects against the half-written file the backup existed for. Updating deletes any copy already on the server. **Check your site for `wp-config.php.horsetools.bak` if you ever used this module — if it is still there, delete it and change your database password.**
* **Security: the debug log is no longer written where anyone can read it.** Switching logging on used to fill `wp-content/debug.log`, which is public by default. PHP errors carry absolute server paths, fragments of SQL and sometimes the contents of the request that failed. The log now goes to its own folder under an unguessable name, with the folder blocked as well; an existing log is moved there rather than left behind or thrown away.
* The value written into wp-config.php is now checked against the two shapes this module ever produces. The existing parse check catches broken syntax, but `true ) ; foo ( bar` is perfectly valid PHP and would have taken a site down when it ran rather than when it parsed. Found by writing the test, not in the wild.
* Covered by `tools/test-config-writer.php`: 15 checks against real files, including that nothing containing a password is left in the directory afterwards.

= 1.3.12 =
* **A snippet body is stored exactly as you typed it again.** Snippets became one record each in 1.3.8, and a record is a post — which means WordPress ran the body through the same filters it runs a blog post through. On most sites those filters change nothing and nobody would ever notice. On a site where "correct invalidly nested XHTML" is switched on, on multisite, or with another plugin filtering saved content, they would: an unclosed tag gets closed, PHP gets rewritten. That matters most for PHP snippets, which are signed character by character and refuse to run — reporting themselves as tampered with — if the body comes back even slightly different. The body is now checked after saving and put back byte for byte if anything touched it. Sites where nothing touches it pay one cached read and no extra write.
* Found by checking rather than by a report; the sites I can test on were not affected, because a single-site administrator is allowed to save unfiltered HTML.

= 1.3.11 =
* **"Detect my chat ID" no longer shows you everyone else's Telegram.** Setting up Telegram recovery used to list every recent chat the site's bot had received, for you to pick yours out of — which on a shop, where customers have accounts, meant any logged-in user could read the names, @usernames and chat IDs of everyone else who had messaged it. You are now given a short pairing code to send to the bot, and the site hands back only the chat that sent it. Nobody learns anybody else's chat, and it is easier too: nothing to pick out of a list of people who might all be called the same thing.
* **The public search index no longer includes password-protected posts.** Adding a post one at a time already excluded them; rebuilding the whole index did not, so the title, price and link of every protected post could end up in a file anyone can read. The two paths agree now.
* **Deleting a font is confined to the fonts folder.** The check was written but never applied, so the delete trusted whatever path was stored. It needs administrator rights to reach either way; this is the guard that stops a mistake somewhere else turning into a deleted file somewhere it shouldn't.
* **The snippet editor is no longer sent a snippet's signature.** The signature is checked on the server and only the answer is needed in the browser, so it stops travelling over the wire.
* **`CLAUDE.md` and `README.md` are no longer packaged.** They are notes for whoever works on the plugin, not for the sites running it, and they were sitting at a guessable public URL.

= 1.3.10 =
* **Security: signing in with Google no longer skips two-factor authentication.** Google proves you control that mailbox, which is one factor — but that flow issued the session itself, by calling WordPress's cookie function directly. That fires no login hook, so this plugin's own second factor never ran: an administrator with 2FA switched on could be signed straight in by whoever held their Google account. The password door was locked and this one was not. When a second factor is owed, no session is issued there at all now; the browser is sent to the normal code screen, which grants the session only once the code is right. Nothing else about the flow changes, and sites without 2FA see no difference — except that the login hook now fires the way it should, so login logs, WooCommerce sessions and other plugins finally see these sign-ins too. **Only affects sites that had switched Google sign-in on.**
* **Security: a command-line script inside a bundled library is no longer shipped.** `svg-scanner.php` came with the SVG sanitiser, was used by nothing here, and had no guard against being requested over the web. It reads `$argv` — which on any host with `register_argc_argv` enabled is filled in from the query string — and then tries to read the filename it finds there. It is deleted, and the packaging rules now keep this class of file out even if the library brings another one back. The SVG sanitiser itself is untouched and still strips scripts from uploaded SVGs.
* Both were found by an audit of the whole plugin rather than by anything going wrong: SQL, all 52 AJAX endpoints, dynamic code execution, redirects, outbound requests, IP handling and file writes were checked alongside them.

= 1.3.9 =
* **Opening a snippet to edit no longer ticks "Run this snippet as PHP".** The four yes/no settings are stored as post meta, which returns them as text, and the editor is fed them as JSON — where the string "0" is not empty and therefore counts as true. So every snippet opened for editing came up marked as PHP, disabled snippets came up as enabled, and saving was refused with a demand for a two-factor code. On a site where PHP editing was already unlocked it would have been worse: saving would have turned a plain HTML snippet into a PHP one. The flags now cross that boundary as numbers. Introduced in 1.3.8, found on a live site within the hour.
* **"Clear the form" now clears the PHP switch too**, instead of carrying it over from the last snippet edited into the next one saved.
= 1.3.8 =
* **Snippets are no longer kept in one lump.** Every snippet on the site — names, settings and the whole body of each — lived in a single database row that was read in full to fetch any one of them, and read on every front-end request just to find out which snippets wanted a PHP hook. Each snippet is now its own record. Fetching one costs one lookup; the front end reads a small index instead of the bodies; changing one snippet no longer rewrites all of them. Nothing about how you use them changes, and `[ht-snippet name="…"]` is unchanged. The old row is not deleted — it is kept as `horsetools_snippets_legacy`, and the move resumes safely if it is interrupted part-way.
* **The pickers search instead of listing everything.** The Shortcode button in the Classic editor and the snippet block in the block editor used to be handed every snippet on the site when the page loaded, and drew them as one long menu — fine at three, unusable well before a hundred, and it put every snippet name into the source of every editor page. Both now ask for what you type, twenty at a time. The fixed shortcode menu no longer carries snippets at all; it offers the empty `[ht-snippet]` tag and leaves choosing a name to the picker.
* **The snippets screen pages.** Twenty-five at a time, with the search box and the tag filter both answered by the server, so opening the screen no longer means loading the site's entire snippet library into the page. A snippet's body is fetched when you open it to edit, not before.
* **Tags became real tags.** They were an array inside a database row, so filtering by one meant a text match against a serialised string. They are a taxonomy now: the tag menu shows what is actually in use with a count beside each, and filtering is an indexed lookup.
* **Searching finds snippets by the name you type the shortcode with.** WordPress's own search looks inside post content, which for a snippet is HTML — searching "div" would have matched nearly everything while searching the snippet's own name matched nothing. Search now looks at the display name, the description and the slug.
* The snippets screen existed twice, byte for byte, in two files. It is one file now, so a change to it happens once.
* Covered by a test suite that runs the storage layer against a stand-in WordPress: 42 checks over the migration, an interrupted migration resuming, the record shape, the PHP index, paging, searching, tag round-tripping and deletion.
= 1.3.7 =
* **Opening the Customers screen no longer waits for the site to answer itself.** The measurement check made up to two blocking requests while the screen was being built, and the tab is not gated behind the feature — so every admin who opened Customers paid for it once an hour, whether or not they had ever switched contact tracking on. The hosts it stalls hardest are the ones that block a site from fetching itself, which are precisely the hosts this check exists for: eight seconds of nothing. The screen now draws immediately from cache and asks for the answer over ajax afterwards.
* **"Check again" now really checks everything again.** It cleared two cached answers but not the one holding the Tag Manager container reading, which lives for a day — so fixing your container and pressing the button showed you yesterday's verdict.
* Found by reading the two days of changes back rather than by anything breaking. Nothing else turned up: no leftovers from the approaches that were tried and dropped, the ajax endpoint checks capability before nonce, the option query escapes the LIKE wildcards, the container ID is regex-bounded before it reaches a URL, and everything printed into the page is escaped.

= 1.3.6 =
* **A contact link can no longer be left dead by a browser that refuses the delayed open.** Holding a link means cancelling the tap and going to the address a moment later, from a timer rather than from the tap itself, and some browsers restrict exactly that. Being wrong about which ones would not cost a statistic — it would cost a phone button that does nothing, on a platform nobody here can test. So the plugin no longer needs to know: after holding, it checks whether its own navigation actually happened, and if it did not, holding is switched off for the rest of the session and every later tap opens the browser's own way. At most one tap is affected, on browsers that would otherwise have been broken outright. Where the check misfires — a desktop showing an "open with" prompt and staying on the page — holding was doing nothing useful there anyway, because nothing was going to suspend that page. Verified against a refusing browser, a real navigation, and an app taking the page over; only the first switches it off.

= 1.3.5 =
* **The measurement screen now works out which case your site is in, instead of leaving you to.** A click takes one of two routes: straight to Analytics if the page loads the Google tag, or into the dataLayer if it does not, where it waits for a tag to be built in Tag Manager. The screen used to show the Tag Manager instructions to anyone who had a container at all — which is the wrong question, because a GA4 tag inside a container loads the Google tag itself. Most Tag Manager users were being sent on an errand they did not need. The container is now opened and read: if it has a GA4 tag in it, the screen says there is nothing to set up. Four outcomes, one shown at a time — ready, install analytics first, build the tag, or "could not check" with the one-line console test that settles it.
* **The Tag Manager instructions gained the step that was missing.** One trigger with regex `^contact_` covers every channel including any added later, and there is now a step for pulling `placement` and `label` through as event parameters — without it those two never reach Analytics on a Tag Manager site.
* **The debug panel says which route each click actually took** — `via gtag` or `via dataLayer — needs a GTM tag`. Reported at the moment of the click rather than worked out in advance, so it settles the question even where the check above cannot.

= 1.3.4 =
* **An unused slot on the bottom contact bar no longer draws a dead button.** The four slots were drawn on the strength of their icon, so a slot nobody had filled in still appeared in the bar — same size, same style as the real buttons, and nothing at all when tapped. Found on a live site, where the third slot had been sitting there unused. A slot is now drawn only if something has been put in it.
* **And a slot with a link but no icon keeps its link.** The same test caught this: because the icon decided everything, filling in a name and a destination but leaving the icon empty produced a button with the fallback icon and no destination at all. It now uses the fallback icon and keeps the link, which is what anyone filling that form would expect.

= 1.3.3 =
* **Fixed: the debug panel sat on top of the buttons it exists to test.** It was pinned to the bottom-left corner, which on a phone is exactly where the contact bar is, so the buttons could not be tapped at all. It now sits at the top and is transparent to touch — taps pass straight through it to whatever is underneath, so it can never block anything again. Newest line first, since a panel that cannot be scrolled must show the line you just made.

= 1.3.2 =
* **A running log on the phone itself, so "it seems flaky" can be checked instead of guessed.** Open the site with ?ht_debug=1 and a small panel appears in the corner. Every contact button you tap adds a line: the time, the event name, and either how many milliseconds it took to be confirmed sent, or — in red — that it was never confirmed. The log survives the jump to the dialler or the chat app, so tapping a button and coming back shows what happened to it. Analytics is a poor instrument for this question: the report is minutes behind, ad blockers filter it, and a click that never left the phone looks identical to one that left and was dropped later. This measures at the moment of the click. It appears only with the debug flag on; ordinary visitors never see it and nothing extra is loaded for them.

= 1.3.1 =
* **Fixed: Zalo clicks were lost, and phone clicks arrived only sometimes.** The link was held back until the event had been sent, but only for the schemes that name an app — tel:, sms:, mailto:, viber:. Zalo and Messenger links look like ordinary web links and were treated as such, when on a phone they hand over to the app exactly the same way. Messenger got away with it because Facebook serves a real page before handing over, which leaves time for the event to go; Zalo hands over at once, so the event never left. Every contact link is now held, unless it opens in a new tab, where the page survives and there is nothing to protect against.
* **And the wait now actually waits.** It used to end the moment GA4's callback fired, but that callback means the request has been handed to the browser, not that it has left the machine — which is why the same phone button reported one time and not the next. A floor of 200ms has to pass as well, and the link still opens by 450ms whatever happens, so a contact button can never be left hanging.

= 1.3.0 =
* **Skype is marked as closed.** Microsoft shut Skype down on 5 May 2025 and moved everyone to Teams, so a skype: link opens nothing — yet the channel was still offered as though it worked. It is now labelled "service closed" in the channel list, and a site with a Skype button set sees a notice saying what happened. The button is left in place rather than removed for you: nothing on your site should change without your say-so.
* **All 29 channels are measured, not twelve.** The chat button offers far more channels than the click tracker knew about, because half of them live in a separate file the tracker's list was never checked against. Every channel is now covered, and anything added in future is covered automatically — a button whose channel is not on the list is recorded under its own site name. WeChat and Google Business were reading as "qq" and "g", which nobody would recognise in a report; both are named properly now.
* Corrected the channel count in both READMEs. Yesterday's release changed it from "30+" to 11, which was wrong in the other direction — it counted only the channels in one of the two files. There are 29 plus a Custom button.

= 1.2.99 =
* **Only a completed click is counted.** 1.2.98 sent the event the moment a finger came down on the button, to remove a wait that turned out not to be noticeable in practice. It also counted a press that moved away again. A contact figure is only worth reading if every number in it was a decision somebody actually made, so that is reverted: the event goes out on the click, as it did in 1.2.97, and the link is held for the few moments it takes to send — never more than 350ms, and it opens regardless if anything goes wrong.

= 1.2.98 =
* **The call button is instant again.** Yesterday's fix held the link back until the event had been sent, which worked but was felt: a third of a second before the dialler appears makes a button feel broken, and a measurement feature has no business making the thing it measures worse. The event is now sent when the button is pressed rather than when the press completes — the 50–300ms between the two is free time, so the request is already on its way before the dialler takes over and nothing has to be held at all. Nothing is sent twice, and a keyboard activation, where there is no press to hook, still falls back to the old reliable-but-slower path.

= 1.2.97 =
* **Fixed: phone clicks were never recorded.** Every other channel reported correctly, which is what made this hard to see. Tapping a phone number hands the device straight to the dialler and suspends the page — and GA4 does not send an event the moment gtag is called, it collects events for a short while and sends them together. On an ordinary link that batch is flushed as the page unloads; on a tel: link the page never unloads, it freezes, and the click that matters most is the one that never arrives. The link is now held for as long as it takes the event to leave, and no longer: a hard 350ms timeout opens the dialler regardless, because a plugin that turns a working phone number into a dead one is far worse than a missing statistic. Same fix for SMS and e-mail links.
* The previous code passed transport_type:'beacon' believing it prevented exactly this. That field belongs to Universal Analytics; GA4 ignores it. The comment claiming otherwise has gone with it.
* **Four more channels, and Custom buttons at last.** Skype, Line, TikTok and Maps links are now recognised, and a Custom chat button pointing anywhere else is recorded under its own domain name — previously every one of those clicks was invisible. Outside the chat widgets only known channels count, so an article full of outbound links does not flood the report.
* **?ht_debug=1 makes GA4's DebugView usable.** Open the site with that on the end of the address and the events identify themselves as debug events. Without it DebugView stays empty however many buttons you tap, and there was nothing on screen to explain why.
* **A warning when Tag Manager is present.** If click tags already exist in a container, switching this on counts the same click twice under two names. The screen now says so, and points out that the Tag Manager side may also be firing Google Ads conversions, which this setting does not.
* Corrected the channel count in both READMEs: 11 built-in channel types plus a Custom button, not "30+".

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
