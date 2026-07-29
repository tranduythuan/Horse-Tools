/* Horse Tools — Services slide-up panel behaviour. Vanilla, no dependencies.
   A bottom-bar item with id "ht-services" opens #ht-services-panel. */
(function () {
	'use strict';

	function ready(fn) {
		if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', fn); }
		else { fn(); }
	}

	ready(function () {
		var wrap = document.getElementById('ht-services-panel');
		var trigger = document.getElementById('ht-services');
		if (!wrap) { return; }
		var sheet = wrap.querySelector('.ht-svc-sheet');

		function open(e) {
			if (e) { e.preventDefault(); }
			wrap.style.display = 'flex';
			// next frame so the transition runs
			requestAnimationFrame(function () { wrap.classList.add('ht-open'); });
			document.addEventListener('keydown', onKey);
		}
		function close() {
			wrap.classList.remove('ht-open');
			document.removeEventListener('keydown', onKey);
			var done = function () { wrap.style.display = 'none'; sheet.removeEventListener('transitionend', done); };
			sheet.addEventListener('transitionend', done);
			setTimeout(function () { wrap.style.display = 'none'; }, 350);
		}
		function onKey(e) { if (e.key === 'Escape') { close(); } }

		if (trigger) { trigger.addEventListener('click', open); }

		wrap.addEventListener('click', function (e) {
			if (e.target.closest('[data-svc-close]') || e.target.classList.contains('ht-svc-backdrop')) { close(); return; }
			var copy = e.target.closest('.ht-svc-copy');
			if (copy) {
				e.preventDefault();
				var code = copy.getAttribute('data-code') || '';
				var mark = function () { copy.classList.add('ht-done'); setTimeout(function () { copy.classList.remove('ht-done'); }, 1200); };
				if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(code).then(mark); }
				else { var t = document.createElement('textarea'); t.value = code; document.body.appendChild(t); t.select(); try { document.execCommand('copy'); mark(); } catch (x) {} document.body.removeChild(t); }
			}
		});

		// Swipe the sheet down to dismiss.
		var startY = null, curY = 0;
		sheet.addEventListener('touchstart', function (e) { startY = e.touches[0].clientY; curY = 0; }, { passive: true });
		sheet.addEventListener('touchmove', function (e) {
			if (startY === null || sheet.scrollTop > 0) { return; }
			curY = e.touches[0].clientY - startY;
			if (curY > 0) { sheet.style.transform = 'translateY(' + curY + 'px)'; }
		}, { passive: true });
		sheet.addEventListener('touchend', function () {
			sheet.style.transform = '';
			if (curY > 90) { close(); }
			startY = null; curY = 0;
		});
	});
})();
