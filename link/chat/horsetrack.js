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
		'telegram.me': 'telegram'
	};

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

	function channelOf(a) {
		var href = a.getAttribute('href') || '';
		var scheme = href.slice(0, href.indexOf(':') + 1).toLowerCase();
		if (CHANNEL_BY_SCHEME[scheme]) {
			return CHANNEL_BY_SCHEME[scheme];
		}
		if (href.indexOf('http') !== 0) {
			return '';
		}
		try {
			return CHANNEL_BY_HOST[new URL(href, location.href).hostname.toLowerCase()] || '';
		} catch (e) {
			return '';
		}
	}

	function placementOf(a) {
		for (var i = 0; i < PLACEMENTS.length; i++) {
			if (a.closest(PLACEMENTS[i][0])) {
				return PLACEMENTS[i][1];
			}
		}
		return 'content';
	}

	function labelOf(a) {
		var t = a.getAttribute('title') || a.getAttribute('aria-label') || a.textContent || '';
		return t.replace(/\s+/g, ' ').trim().slice(0, 80);
	}

	document.addEventListener('click', function (ev) {
		if (ev.button !== 0 && ev.type === 'click') {
			return; // middle/right click opens nothing we can attribute
		}
		var a = ev.target && ev.target.closest ? ev.target.closest('a[href]') : null;
		if (!a) {
			return;
		}
		var channel = channelOf(a);
		if (!channel) {
			return;
		}

		var name = 'contact_' + channel;
		var placement = placementOf(a);
		var label = labelOf(a);

		// transport_type beacon so the request survives the page being replaced:
		// tapping tel: hands the phone to the dialler immediately and an ordinary
		// request is cancelled in flight, losing exactly the clicks that matter
		// most.
		if (typeof window.gtag === 'function') {
			window.gtag('event', name, {
				placement: placement,
				label: label,
				transport_type: 'beacon'
			});
		}

		// Also for Tag Manager, where gtag() may not exist at all. Harmless when
		// nothing is listening.
		if (window.dataLayer && typeof window.dataLayer.push === 'function') {
			window.dataLayer.push({
				event: name,
				contact_channel: channel,
				contact_placement: placement,
				contact_label: label
			});
		}
	}, true);
}());
