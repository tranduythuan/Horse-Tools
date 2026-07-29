/* Horse Tools — Services panel behaviour. Vanilla, no dependencies.
   Triggers: any #ht-services element (bottom-bar item) or .ht-svc-launch
   (desktop launcher). Presentation mode from data-mode (auto = sheet on
   phones, modal on desktop). */
(function () {
	'use strict';

	function ready(fn) {
		if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', fn); }
		else { fn(); }
	}

	ready(function () {
		var wrap = document.getElementById('ht-services-panel');
		if (!wrap) { return; }
		var sheet = wrap.querySelector('.ht-svc-sheet');
		var MODES = ['sheet', 'modal', 'drawer-right', 'drawer-left', 'corner', 'fullscreen'];
		var curMode = 'sheet';

		function resolveMode() {
			var m = wrap.getAttribute('data-mode') || 'auto';
			if (m === 'auto') { m = window.innerWidth <= 700 ? 'sheet' : 'modal'; }
			return MODES.indexOf(m) === -1 ? 'sheet' : m;
		}
		function setMode(m) {
			MODES.forEach(function (x) { wrap.classList.remove('ht-svc-m-' + x); });
			wrap.classList.add('ht-svc-m-' + m);
			curMode = m;
		}

		function open(e) {
			if (e) { e.preventDefault(); }
			setMode(resolveMode());
			wrap.style.display = 'flex';
			requestAnimationFrame(function () { wrap.classList.add('ht-open'); });
			document.addEventListener('keydown', onKey);
		}
		function close() {
			wrap.classList.remove('ht-open');
			document.removeEventListener('keydown', onKey);
			setTimeout(function () { if (!wrap.classList.contains('ht-open')) { wrap.style.display = 'none'; } }, 320);
		}
		function onKey(e) { if (e.key === 'Escape') { close(); } }

		// Any trigger opens the panel.
		document.addEventListener('click', function (e) {
			var t = e.target.closest('#ht-services, .ht-svc-launch');
			if (t) { open(e); }
		});

		wrap.addEventListener('click', function (e) {
			if (e.target.closest('[data-svc-close]') || e.target.classList.contains('ht-svc-backdrop')) { close(); return; }
			var copy = e.target.closest('.ht-svc-copy');
			if (copy) {
				e.preventDefault();
				var code = copy.getAttribute('data-code') || '';
				var mark = function () { copy.classList.add('ht-done'); setTimeout(function () { copy.classList.remove('ht-done'); }, 1200); };
				if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(code).then(mark); }
				else { var ta = document.createElement('textarea'); ta.value = code; document.body.appendChild(ta); ta.select(); try { document.execCommand('copy'); mark(); } catch (x) {} document.body.removeChild(ta); }
			}
		});

		// Swipe down to dismiss — sheet mode only.
		var startY = null, curY = 0;
		sheet.addEventListener('touchstart', function (e) { if (curMode !== 'sheet') { return; } startY = e.touches[0].clientY; curY = 0; }, { passive: true });
		sheet.addEventListener('touchmove', function (e) {
			if (startY === null || curMode !== 'sheet' || sheet.scrollTop > 0) { return; }
			curY = e.touches[0].clientY - startY;
			if (curY > 0) { sheet.style.transform = 'translateY(' + curY + 'px)'; }
		}, { passive: true });
		sheet.addEventListener('touchend', function () {
			if (curMode !== 'sheet') { return; }
			sheet.style.transform = '';
			if (curY > 90) { close(); }
			startY = null; curY = 0;
		});
	});
})();
