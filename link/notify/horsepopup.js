/**
 * Horse Tools popup — custom show/hide with a choice of entrance effect,
 * display position (centre / corner toast / bottom bar) and trigger
 * (on load / after N seconds / after scrolling N% / exit-intent).
 *
 * Frequency: once dismissed, it stays hidden for "save time" hours
 * (localStorage). jQuery is only used as the ready wrapper.
 */
jQuery(function () {
	var root = document.getElementById('ht-popup-root');
	if (!root) { return; }

	var KEY   = 'htpopup';
	var hours = parseInt(root.getAttribute('data-time'), 10) || 0;
	var trig  = root.getAttribute('data-trig') || 'load';
	var val   = parseInt(root.getAttribute('data-trigval'), 10) || 0;

	// Respect the cooldown: if it was dismissed less than `hours` ago, stay away.
	if (hours > 0) {
		var last = parseInt(localStorage.getItem(KEY), 10) || 0;
		if (last && (Date.now() - last) < hours * 3600 * 1000) { return; }
	}

	var shown = false;
	var attn  = root.getAttribute('data-attn') === '1';
	var attnTimer = null, attnInterval = null;

	function onEsc(e) { if (e.key === 'Escape') { hide(); } }

	function wiggle() {
		root.classList.remove('ht-attn');
		void root.offsetWidth;
		root.classList.add('ht-attn');
	}

	function show() {
		if (shown) { return; }
		shown = true;
		root.style.display = '';         // CSS decides flex/centre/corner
		void root.offsetWidth;           // reflow so the transition runs
		root.classList.add('show');
		document.addEventListener('keydown', onEsc);
		if (attn) {
			attnTimer = setTimeout(function () {
				wiggle();
				attnInterval = setInterval(wiggle, 9000);
			}, 2500);
		}
	}

	function hide() {
		root.classList.remove('show');
		clearTimeout(attnTimer);
		clearInterval(attnInterval);
		try { localStorage.setItem(KEY, String(Date.now())); } catch (e) {}
		document.removeEventListener('keydown', onEsc);
		setTimeout(function () { root.style.display = 'none'; }, 350);
	}

	root.addEventListener('click', function (e) {
		if (e.target.closest('[data-ht-popup-close]')) { hide(); }
	});

	var isMobile = /Mobi|Android|iPhone|iPad/i.test(navigator.userAgent);

	if (trig === 'delay') {
		setTimeout(show, Math.max(0, val) * 1000);
	} else if (trig === 'scroll') {
		var pct = Math.min(Math.max(val || 30, 1), 100);
		var onScroll = function () {
			var h = document.documentElement.scrollHeight - window.innerHeight;
			var sc = h > 0 ? (window.pageYOffset / h * 100) : 100;
			if (sc >= pct) {
				window.removeEventListener('scroll', onScroll);
				show();
			}
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	} else if (trig === 'exit') {
		if (isMobile) {
			// No cursor to leave on touch devices — fall back to a short delay.
			setTimeout(show, (val > 0 ? val : 8) * 1000);
		} else {
			var onLeave = function (e) {
				if (e.clientY <= 0) {
					document.removeEventListener('mouseout', onLeave);
					show();
				}
			};
			document.addEventListener('mouseout', onLeave);
		}
	} else {
		show(); // load
	}
});
