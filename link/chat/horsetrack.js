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
		'viber:': 'viber',
		'skype:': 'skype'
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
		'join.skype.com': 'skype',
		'web.skype.com': 'skype',
		'line.me': 'line',
		'zalo.com.vn': 'zalo',
		'www.tiktok.com': 'tiktok',
		'tiktok.com': 'tiktok',
		'vt.tiktok.com': 'tiktok',
		'maps.app.goo.gl': 'maps'
	};

	// Tapping one of these hands the phone straight to another app. The page is
	// suspended the moment it happens, which is the whole problem this file has
	// to work around — see send() below.
	var HANDOFF = { 'tel:': 1, 'sms:': 1, 'mailto:': 1, 'viber:': 1, 'skype:': 1 };

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
	 * events for a moment and sends them together. That is invisible on an
	 * ordinary link, because the batch is flushed as the page unloads. It is
	 * fatal on tel: and sms:, where the phone switches to the dialler and
	 * suspends the page while the batch is still waiting: the click that matters
	 * most is the one that never arrives.
	 *
	 * (An earlier version passed transport_type:'beacon' believing it solved
	 * this. That field belongs to Universal Analytics; GA4 ignores it, which is
	 * why phone clicks were missing while every other channel reported fine.)
	 *
	 * So for those links the default is cancelled, the event is sent with a
	 * callback, and the browser is sent to the href once the callback fires — or
	 * after 350ms, whichever comes first. A hard timeout is not optional: if
	 * anything at all goes wrong the link must still open, or the plugin has
	 * turned a working phone number into a dead one.
	 */
	function send(name, params, ev, a) {
		var href = a.getAttribute('href') || '';
		var hold = !!HANDOFF[schemeOf(href)] &&
			!ev.defaultPrevented &&
			!ev.metaKey && !ev.ctrlKey && !ev.shiftKey && !ev.altKey &&
			a.target !== '_blank';

		var released = false;
		var release = function () {
			if (released) { return; }
			released = true;
			if (hold) {
				window.location.href = href;
			}
		};

		if (DEBUG) {
			params.debug_mode = true;
		}

		if (typeof window.gtag === 'function') {
			if (hold) {
				ev.preventDefault();
				params.event_callback = release;
				params.event_timeout = 300;
				setTimeout(release, 350);
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
				setTimeout(release, 350);
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

	document.addEventListener('click', function (ev) {
		if (ev.button !== 0 && ev.type === 'click') {
			return; // middle/right click opens nothing we can attribute
		}
		var a = ev.target && ev.target.closest ? ev.target.closest('a[href]') : null;
		if (!a) {
			return;
		}
		var placement = placementOf(a);
		var channel = channelOf(a, '' !== placement);
		if (!channel) {
			return;
		}

		send('contact_' + channel, {
			channel: channel,
			placement: placement || 'content',
			label: labelOf(a)
		}, ev, a);
	}, true);
}());
