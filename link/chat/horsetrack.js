/**
 * Horse Tools — record contact-button clicks in Google Analytics 4.
 *
 * Channels are recognised by the link itself, not by the class the plugin puts
 * on its own buttons. The quick-reply chips in the chat panel are tel: and
 * zalo.me links with a different class, and a site owner may well paste a phone
 * link straight into an article — matching on the href catches all of them, and
 * keeps working if the markup is ever restyled.
 *
 * The channel goes in the event NAME rather than a parameter. GA4 shows event
 * names in its standard reports with no setup at all, while a custom parameter
 * stays invisible until three custom dimensions have been registered by hand —
 * which is exactly the step a non-technical owner gives up on, and gets no
 * warning about when they skip it.
 */
(function () {
	'use strict';

	var CHANNEL_BY_SCHEME = {
		'tel:': 'phone',
		'sms:': 'sms',
		'mailto:': 'email',
		'viber:': 'viber'
	};

	var CHANNEL_BY_HOST = {
		'zalo.me': 'zalo',
		'chat.zalo.me': 'zalo',
		'm.me': 'messenger',
		'messenger.com': 'messenger',
		'www.messenger.com': 'messenger',
		'wa.me': 'whatsapp',
		'api.whatsapp.com': 'whatsapp',
		'web.whatsapp.com': 'whatsapp',
		't.me': 'telegram',
		'telegram.me': 'telegram',
		'line.me': 'line',
		'zalo.com.vn': 'zalo',
		// Two hosts the domain-name fallback gets wrong: it would read these as
		// "qq" and "g", which nobody would recognise in a report.
		'weixin.qq.com': 'wechat',
		'wechat.com': 'wechat',
		'g.page': 'google',
		'www.tiktok.com': 'tiktok',
		'tiktok.com': 'tiktok',
		'vt.tiktok.com': 'tiktok',
		'maps.app.goo.gl': 'maps'
	};

	// Tapping one of these hands the phone straight to another app. The page is
	// suspended the moment it happens, which is the whole problem this file has
	// to work around — see send() below.
	var HANDOFF = { 'tel:': 1, 'sms:': 1, 'mailto:': 1, 'viber:': 1 };

	// Where on the page the link sits, worked out from the nearest container the
	// plugin renders. Useful for the question owners actually ask: is the bar at
	// the bottom of the phone screen pulling its weight, or is it the floating
	// button doing all the work?
	var PLACEMENTS = [
		['.ht-navi', 'navbar'],
		['.ht-chatbox', 'chatbox'],
		['.ht-chaton', 'floating'],
		['.ht-tw-pane', 'services'],
		['.ht-lc-quick', 'quickreply']
	];

	// ?ht_debug=1 makes the events show up in GA4's DebugView, which is otherwise
	// blind to them: DebugView only lists devices that flag themselves, so an
	// owner testing their own buttons sees an empty screen and concludes nothing
	// works. Kept for the rest of the session so it survives the navigation a
	// contact link causes.
	var DEBUG = (function () {
		try {
			if (location.search.indexOf('ht_debug=1') > -1) {
				sessionStorage.setItem('htDebug', '1');
			}
			return sessionStorage.getItem('htDebug') === '1';
		} catch (e) {
			return location.search.indexOf('ht_debug=1') > -1;
		}
	}());

	/**
	 * A running log on the phone itself, in debug mode only.
	 *
	 * "It seems to work sometimes" is not something either side can act on, and
	 * GA4 is a poor instrument for answering it: the report is minutes behind, it
	 * is filtered by ad blockers, and a click that never left the phone looks
	 * exactly like one that left and was dropped later. This records what
	 * happened at the moment of the click — was the event confirmed dispatched
	 * before the link opened, and how long it took — and keeps it across the
	 * navigation, so tapping a button and coming back shows the answer.
	 */
	var LOG_KEY = 'htDebugLog';

	function logLine(text) {
		if (!DEBUG) { return; }
		try {
			var lines = JSON.parse(sessionStorage.getItem(LOG_KEY) || '[]');
			lines.push(text);
			sessionStorage.setItem(LOG_KEY, JSON.stringify(lines.slice(-12)));
		} catch (e) {}
		renderLog();
	}

	function renderLog() {
		if (!DEBUG || !document.body) { return; }
		var lines;
		try {
			lines = JSON.parse(sessionStorage.getItem(LOG_KEY) || '[]');
		} catch (e) {
			lines = [];
		}
		var box = document.getElementById('ht-debug-log');
		if (!box) {
			box = document.createElement('div');
			box.id = 'ht-debug-log';
			// Top of the screen, and transparent to touch. The first version sat
			// bottom-left — directly on top of the contact bar it exists to test,
			// so the buttons could not be tapped at all. pointer-events:none means
			// it can never do that again, wherever it ends up.
			box.setAttribute('style', 'position:fixed;left:8px;right:8px;top:8px;z-index:2147483647;' +
				'pointer-events:none;max-width:min(92vw,420px);max-height:30vh;overflow:hidden;' +
				'background:#101418;color:#d7e2ea;font:12px/1.5 ui-monospace,Menlo,Consolas,monospace;' +
				'padding:8px 10px;border-radius:8px;box-shadow:0 4px 18px rgba(0,0,0,.4);opacity:.94');
			document.body.appendChild(box);
		}
		box.textContent = '';
		var head = document.createElement('div');
		head.setAttribute('style', 'color:#7fd1a8;margin-bottom:4px');
		head.textContent = 'Horse Tools — contact tracking (ht_debug=1)';
		box.appendChild(head);
		if (!lines.length) {
			var none = document.createElement('div');
			none.textContent = 'No contact click recorded yet. Tap a button.';
			box.appendChild(none);
		}
		// Newest first: the panel is short and cannot be scrolled (it has to stay
		// transparent to touch), so the line you just made must be the visible one.
		lines.slice().reverse().forEach(function (line) {
			var row = document.createElement('div');
			if (line.indexOf('NOT confirmed') > -1) { row.setAttribute('style', 'color:#ff9b9b'); }
			row.textContent = line;
			box.appendChild(row);
		});
	}

	if (DEBUG) {
		if ('loading' === document.readyState) {
			document.addEventListener('DOMContentLoaded', renderLog);
		} else {
			renderLog();
		}
	}

	function schemeOf(href) {
		return href.slice(0, href.indexOf(':') + 1).toLowerCase();
	}

	function hostOf(href) {
		try {
			return new URL(href, location.href).hostname.toLowerCase();
		} catch (e) {
			return '';
		}
	}

	/**
	 * Turn a host we do not know into a channel name.
	 *
	 * A Custom button can point anywhere — Line, WeChat, Instagram, a booking
	 * form — and those clicks were previously invisible because the host was not
	 * on the list. There is no list that can cover them, so the host becomes the
	 * name: chat.example.vn -> contact_example.
	 */
	function slugOf(host) {
		var parts = host.replace(/^www\./, '').split('.');
		var word = parts.length > 2 ? parts[parts.length - 2] : parts[0];
		word = word.toLowerCase().replace(/[^a-z0-9]/g, '');
		return word.slice(0, 24);
	}

	function channelOf(a, inWidget) {
		var href = a.getAttribute('href') || '';
		var scheme = schemeOf(href);
		if (CHANNEL_BY_SCHEME[scheme]) {
			return CHANNEL_BY_SCHEME[scheme];
		}
		if (href.indexOf('http') !== 0) {
			return '';
		}
		var host = hostOf(href);
		if (CHANNEL_BY_HOST[host]) {
			return CHANNEL_BY_HOST[host];
		}
		if ((host === 'www.google.com' || host === 'google.com') && href.indexOf('/maps') > -1) {
			return 'maps';
		}
		// Anything else counts only inside one of the plugin's contact widgets.
		// Firing on every outbound link in an article would drown the report.
		if (inWidget && host && host !== location.hostname) {
			return slugOf(host);
		}
		return '';
	}

	function placementOf(a) {
		for (var i = 0; i < PLACEMENTS.length; i++) {
			if (a.closest(PLACEMENTS[i][0])) {
				return PLACEMENTS[i][1];
			}
		}
		return '';
	}

	function labelOf(a) {
		var t = a.getAttribute('title') || a.getAttribute('aria-label') || a.textContent || '';
		return t.replace(/\s+/g, ' ').trim().slice(0, 80);
	}

	/**
	 * Hand the event to analytics, and hold the link until it has gone.
	 *
	 * GA4 does not send an event the instant gtag() is called — it collects
	 * events for a moment and sends them together. That batch is flushed as the
	 * page unloads, which is why an ordinary link is fine. A contact link is not
	 * an ordinary link: it hands the device to another app, and the page is
	 * suspended with the batch still waiting.
	 *
	 * (An earlier version passed transport_type:'beacon' believing it solved
	 * this. That field belongs to Universal Analytics; GA4 ignores it.)
	 *
	 * Every contact link is held, not only the tel: family. The first attempt at
	 * this held only the schemes that name an app — tel:, sms:, mailto:, viber: —
	 * and treated zalo.me and m.me as ordinary web links. On a phone they are
	 * not: they switch to the app just the same. Messenger survived only because
	 * Facebook serves a real page before handing over, which gives the flush time
	 * to happen; Zalo hands over immediately and the event was lost every time.
	 *
	 * The wait is also no longer ended by event_callback alone. That callback
	 * means gtag has dispatched the request, not that it has left the machine, so
	 * releasing on it produced a click that arrived sometimes and not others. A
	 * floor of 200ms has to pass as well.
	 *
	 * A hard cap still opens the link whatever happens. That is not optional: a
	 * plugin that turns a working phone number into a dead one is far worse than
	 * one that loses a statistic.
	 */
	var HOLD_FLOOR = 200;
	var HOLD_CAP = 450;

	function send(name, params, ev, a) {
		var href = a.getAttribute('href') || '';
		var hold = ('' !== href) &&
			0 !== href.indexOf('#') &&
			!ev.defaultPrevented &&
			!ev.metaKey && !ev.ctrlKey && !ev.shiftKey && !ev.altKey &&
			a.target !== '_blank';

		var started = new Date();
		var dispatched = false;
		var dispatchedAt = 0;
		var released = false;
		var release = function () {
			if (released) { return; }
			released = true;
			if (DEBUG) {
				logLine(
					started.toTimeString().slice(0, 8) + '  ' + name + '  ' +
					(dispatched
						? 'sent in ' + dispatchedAt + 'ms'
						: 'NOT confirmed after ' + (new Date() - started) + 'ms') +
					(hold ? '' : ' (link not held)')
				);
			}
			if (hold) {
				window.location.href = href;
			}
		};

		if (DEBUG) {
			params.debug_mode = true;
		}

		if (typeof window.gtag === 'function') {
			var markSent = function () {
				dispatched = true;
				dispatchedAt = new Date() - started;
			};
			if (hold) {
				ev.preventDefault();
				var floorPassed = false;
				setTimeout(function () {
					floorPassed = true;
					if (dispatched) { release(); }
				}, HOLD_FLOOR);
				params.event_callback = function () {
					markSent();
					if (floorPassed) { release(); }
				};
				params.event_timeout = HOLD_CAP;
				setTimeout(release, HOLD_CAP);
			} else if (DEBUG) {
				params.event_callback = markSent;
				setTimeout(release, HOLD_CAP);
			}
			// One route only. A site can have gtag() and Tag Manager at once,
			// both feeding the same property, and sending to both would count
			// every contact twice. gtag() wins because it reaches GA4 by itself;
			// the dataLayer push needs a tag and trigger built in Tag Manager.
			window.gtag('event', name, params);
			return;
		}

		if (window.dataLayer && typeof window.dataLayer.push === 'function') {
			if (hold) {
				ev.preventDefault();
				setTimeout(release, HOLD_CAP);
			}
			window.dataLayer.push({
				event: name,
				contact_channel: params.channel,
				contact_placement: params.placement,
				contact_label: params.label,
				eventCallback: release
			});
		}
	}

	function linkOf(ev) {
		return ev.target && ev.target.closest ? ev.target.closest('a[href]') : null;
	}

	function describe(a) {
		var placement = placementOf(a);
		var channel = channelOf(a, '' !== placement);
		if (!channel) {
			return null;
		}
		return {
			name: 'contact_' + channel,
			params: { channel: channel, placement: placement || 'content', label: labelOf(a) }
		};
	}

	/**
	 * Only a completed click counts.
	 *
	 * Sending on press instead would remove the short wait below, but it would
	 * also count a finger that came down on the button and moved away again. A
	 * contact figure is only worth reading if every number in it was a decision
	 * somebody actually made, so the wait stays and the press is ignored.
	 */
	document.addEventListener('click', function (ev) {
		if (ev.button !== 0 && ev.type === 'click') {
			return; // middle/right click opens nothing we can attribute
		}
		var a = linkOf(ev);
		if (!a) {
			return;
		}
		var hit = describe(a);
		if (!hit) {
			return;
		}
		send(hit.name, hit.params, ev, a);
	}, true);
}());
