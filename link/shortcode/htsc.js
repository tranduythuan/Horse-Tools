/* Horse Tools — shortcode library behaviour. Vanilla JS, no dependencies. */
(function () {
	'use strict';

	// Tabs: click a button to reveal its panel.
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('.ht-tab-btn');
		if (btn) {
			var wrap = btn.closest('.ht-tabs');
			var id = btn.getAttribute('data-tab');
			wrap.querySelectorAll('.ht-tab-btn').forEach(function (b) { b.classList.remove('active'); });
			wrap.querySelectorAll('.ht-tab-panel').forEach(function (p) { p.classList.remove('active'); });
			btn.classList.add('active');
			var panel = wrap.querySelector('#ht-tab-' + id);
			if (panel) { panel.classList.add('active'); }
			return;
		}
		// Privacy video facade: load the real player only on click.
		var vp = e.target.closest('.ht-video-play');
		if (vp) {
			var box = vp.closest('.ht-video');
			var type = box.getAttribute('data-type'), id = box.getAttribute('data-id');
			var src = type === 'vimeo'
				? 'https://player.vimeo.com/video/' + encodeURIComponent(id) + '?autoplay=1'
				: 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(id) + '?autoplay=1&rel=0';
			var f = document.createElement('iframe');
			f.setAttribute('src', src);
			f.setAttribute('allow', 'autoplay; encrypted-media; picture-in-picture; fullscreen');
			f.setAttribute('allowfullscreen', '');
			f.setAttribute('loading', 'lazy');
			box.innerHTML = '';
			box.classList.add('playing');
			box.appendChild(f);
			return;
		}
		// Click-to-copy.
		var cb = e.target.closest('.ht-copy-btn');
		if (cb) {
			var box = cb.closest('.ht-copy');
			var text = box ? box.getAttribute('data-copy') : '';
			var mark = function () { cb.classList.add('done'); setTimeout(function () { cb.classList.remove('done'); }, 1200); };
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(text).then(mark);
			} else {
				var ta = document.createElement('textarea'); ta.value = text; document.body.appendChild(ta);
				ta.select(); try { document.execCommand('copy'); mark(); } catch (x) {} document.body.removeChild(ta);
			}
			return;
		}
	});

	// Obfuscated email: assemble the address on load so it is never in the HTML.
	function initEmails() {
		document.querySelectorAll('a.ht-email').forEach(function (a) {
			if (a.dataset.done) { return; }
			a.dataset.done = '1';
			var addr = (a.getAttribute('data-u') || '') + '@' + (a.getAttribute('data-d') || '');
			var subj = a.getAttribute('data-s');
			a.setAttribute('href', 'mailto:' + addr + (subj ? '?subject=' + encodeURIComponent(subj) : ''));
			if (!a.textContent.trim()) { a.textContent = addr; }
		});
	}

	// Countdown: tick every second.
	function pad(n) { return (n < 10 ? '0' : '') + n; }
	function initCountdowns() {
		var els = document.querySelectorAll('.ht-countdown');
		if (!els.length) { return; }
		var L = window.horsetoolsCdLabels || { d: 'days', h: 'hrs', m: 'min', s: 'sec', done: 'Time is up' };
		function cell(v, label) { return '<span class="ht-cd-cell"><b>' + pad(v) + '</b><span>' + label + '</span></span>'; }
		function tick() {
			var now = Date.now();
			els.forEach(function (el) {
				var target = parseInt(el.getAttribute('data-ts'), 10);
				if (!target) { return; }
				var diff = Math.floor((target - now) / 1000);
				if (diff <= 0) { el.classList.add('done'); el.innerHTML = '<span>' + (el.getAttribute('data-done') || L.done) + '</span>'; return; }
				var d = Math.floor(diff / 86400), h = Math.floor((diff % 86400) / 3600), m = Math.floor((diff % 3600) / 60), s = diff % 60;
				el.innerHTML = cell(d, L.d) + cell(h, L.h) + cell(m, L.m) + cell(s, L.s);
			});
		}
		tick();
		setInterval(tick, 1000);
	}

	// QR codes: render locally with the vendored QRCode.js (no external calls).
	function initQR() {
		if (typeof QRCode === 'undefined') { return; }
		document.querySelectorAll('.ht-qr').forEach(function (el) {
			if (el.dataset.done) { return; }
			el.dataset.done = '1';
			var size = parseInt(el.getAttribute('data-size'), 10) || 160;
			new QRCode(el, { text: el.getAttribute('data-text') || '', width: size, height: size, correctLevel: QRCode.CorrectLevel.M });
		});
	}

	function init() { initEmails(); initCountdowns(); initQR(); }
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
