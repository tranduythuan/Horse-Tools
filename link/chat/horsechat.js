document.addEventListener('DOMContentLoaded', function() {
// chat on
let chatlastScrollTop = 0;
const chatmojs = document.getElementById('chat-mojs');
if (chatmojs) {
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollTop > chatlastScrollTop) {
            chatmojs.classList.add('chathi');
        } else {
            chatmojs.classList.remove('chathi');
        }
        chatlastScrollTop = scrollTop <= 0 ? 0 : scrollTop; 
    });
}
// svg to chaton
const chaton2 = document.getElementById('ht-chaton2');
const chatona = document.getElementById('chatona');
if (chaton2 && chatona) {
	const originalIcon = chatona.innerHTML;
	const observer = new MutationObserver(() => {
		if (chaton2.style.display === 'block') {
			chatona.innerHTML = `
				<div class="original-icon" style="display: none;">${originalIcon}</div>
				<svg class="khacus" width="100%" height="100%" viewBox="0 0 70 70" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421">
					<g id="close_1_" transform="matrix(0.0803948,0,0,0.0803948,14.4189,14.4189)">
						<path d="M48.536,508.793C35.983,509.523 23.638,505.35 14.104,497.154C-4.7,478.238 -4.7,447.688 14.104,428.773L425.842,17.033C445.4,-1.268 476.089,-0.251 494.389,19.307C510.938,36.992 511.903,64.176 496.648,82.989L82.483,497.154C73.071,505.232 60.924,509.397 48.536,508.793Z" style="fill:#fff;fill-rule:nonzero"/>
						<path d="M459.791,508.793C447.069,508.739 434.875,503.689 425.842,494.729L14.102,82.988C-3.319,62.644 -0.95,32.029 19.393,14.607C37.55,-0.942 64.328,-0.942 82.483,14.607L496.648,426.347C516.2,444.652 517.211,475.343 498.906,494.896C498.178,495.673 497.425,496.426 496.648,497.154C486.506,505.973 473.16,510.187 459.791,508.793Z" style="fill:#fff;fill-rule:nonzero"/>
					</g>
				</svg>
			`;
		} else if (chaton2.style.display === 'none') {
			chatona.innerHTML = originalIcon;
		}
	});
	observer.observe(chaton2, { attributes: true, attributeFilter: ['style'] });
}
// navi
let navilastScrollTop = 0;
const navimojs = document.getElementById('navi-mojs');
const navimojsc = document.getElementById('ht-navi-chaton');
const navimojsm = document.getElementById('ht-navi-menu');
const chatontab1 = document.getElementById('ht-chaton2');
const chatontab2 = document.getElementById('ht-chaton');
if (navimojs) {
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollTop > navilastScrollTop) {
            navimojs.classList.add('navihi');
			if (navimojsc) {
                navimojsc.style.display = 'none';
            }
            if (navimojsm) {
                navimojsm.style.display = 'none';
            }
			if (chatontab1) {
                chatontab1.style.display = 'none';
            }
			if (chatontab2) {
                chatontab2.style.display = 'none';
            }
        } else {
            navimojs.classList.remove('navihi');
        }
        navilastScrollTop = scrollTop <= 0 ? 0 : scrollTop; 
    });
}
// Greeting bubble: show after a delay unless dismissed before.
var htGreet = document.getElementById('ht-greet');
if (htGreet) {
	var htClosed = false;
	try { htClosed = localStorage.getItem('htGreetClosed') === '1'; } catch (e) {}
	if (!htClosed) {
		var htDelay = (parseInt(htGreet.getAttribute('data-delay'), 10) || 3) * 1000;
		setTimeout(function () { htGreet.style.display = 'block'; }, htDelay);
	}
	var htGx = htGreet.querySelector('.ht-greet-x');
	if (htGx) { htGx.addEventListener('click', function () { htGreet.style.display = 'none'; try { localStorage.setItem('htGreetClosed', '1'); } catch (e) {} }); }
}
// Scan-to-open QR for messaging channels (desktop; WeChat always).
if (window.htChatCfg && htChatCfg.qr && typeof QRCode !== 'undefined') {
	document.addEventListener('click', function (e) {
		var link = e.target.closest('.ht-czal, .ht-cwha, .ht-ctel, .ht-cvib, .ht-cline, .ht-cwc');
		if (!link) { return; }
		var isWeChat = link.classList.contains('ht-cwc');
		if (window.innerWidth <= 700 && !isWeChat) { return; }
		var href = link.getAttribute('href') || '';
		if (!href || href === '#') { return; }
		e.preventDefault();
		htShowQR(href, link.getAttribute('title') || '');
	});
}
function htShowQR(text, title) {
	var old = document.getElementById('ht-qr-modal'); if (old) { old.parentNode.removeChild(old); }
	var wrap = document.createElement('div'); wrap.id = 'ht-qr-modal'; wrap.className = 'ht-qr-modal';
	var box = document.createElement('div'); box.className = 'ht-qr-box';
	var x = document.createElement('button'); x.className = 'ht-qr-x'; x.setAttribute('aria-label', 'Close'); x.innerHTML = '&#215;';
	var h = document.createElement('div'); h.className = 'ht-qr-title'; h.textContent = title || (window.htChatCfg && htChatCfg.qrLbl) || '';
	var qr = document.createElement('div'); qr.className = 'ht-qr-canvas';
	var sub = document.createElement('div'); sub.className = 'ht-qr-sub'; sub.textContent = (window.htChatCfg && htChatCfg.qrLbl) || '';
	box.appendChild(x); box.appendChild(h); box.appendChild(qr); box.appendChild(sub); wrap.appendChild(box); document.body.appendChild(wrap);
	new QRCode(qr, { text: text, width: 200, height: 200, correctLevel: QRCode.CorrectLevel.M });
	function close() { if (wrap.parentNode) { wrap.parentNode.removeChild(wrap); } document.removeEventListener('keydown', onEsc); }
	function onEsc(ev) { if (ev.key === 'Escape') { close(); } }
	wrap.addEventListener('click', function (ev) { if (ev.target === wrap || ev.target === x) { close(); } });
	document.addEventListener('keydown', onEsc);
}
// Tab-widget skin: switch panes.
document.addEventListener('click', function (e) {
	var tab = e.target.closest('.ht-tw-tab');
	if (!tab) { return; }
	var panel = tab.closest('.ht-tw-panel');
	if (!panel) { return; }
	var key = tab.getAttribute('data-tw');
	panel.querySelectorAll('.ht-tw-tab').forEach(function (x) { x.classList.remove('active'); });
	panel.querySelectorAll('.ht-tw-pane').forEach(function (x) { x.classList.toggle('active', x.getAttribute('data-tw') === key); });
	tab.classList.add('active');
});
// navi menu
const horsenavi = document.getElementById('horsenavi');
if (horsenavi) {
	horsenavi.addEventListener('click', function(event) {
		event.preventDefault(); 
		const element = document.querySelector('.ht-navi-me');
		if (element.style.display === 'block') {
			element.style.display = 'none';
		} else {
			element.style.display = 'block';
		}
	});
}
});
