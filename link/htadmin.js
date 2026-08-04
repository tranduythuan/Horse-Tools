// add code color editor
jQuery(document).ready(function($) {
    $('.ht-dev').each(function() {
        var editor = CodeMirror.fromTextArea(this, {
            lineNumbers: true,
			lineWrapping: true,
			matchBrackets: true,
            mode: 'text/x-perl',
            theme: 'cobalt',
			extraKeys: {
                "Ctrl-Z": "undo",   // hỗ trợ Ctrl + Z để quay lại
                "Ctrl-Y": "redo",   // hỗ trợ Ctrl + Y để làm lại
                "Cmd-Z": "undo",    // hỗ trợ Command + Z trên macOS
                "Cmd-Y": "redo",    // hỗ trợ Command + Y trên macOS
				"Ctrl-F": "find",   // hỗ trợ Ctrl + F để tìm kiếm
                "Cmd-F": "find",
				"Ctrl-H": "replace", // hỗ trợ Ctrl + H để thay thế
                "Cmd-H": "replace",
            }
        });
        $(this).data('CodeMirrorInstance', editor);
		// dong bo voi textarea
		editor.on('change', function() {
            $(editor.getTextArea()).val(editor.getValue());
        });
    });
});
// tab
function httab(evt, tabname) {
  var i, x, sotab;
  x = document.getElementsByClassName("htbox");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
  }
  sotab = document.getElementsByClassName("sotab");
  for (i = 0; i < sotab.length; i++) {
    sotab[i].className = sotab[i].className.replace(" sotab-select", "");
  }
  var pane = document.getElementById(tabname);
  if (pane) { pane.style.display = "block"; }
  if (evt && evt.currentTarget) { evt.currentTarget.className += " sotab-select"; }
  localStorage.setItem('htranksel', tabname);
	// tại codeEditor cho horse-codex2 tab 2 tro di
    jQuery(document).ready(function($) {
      $('.ht-dev').each(function() {
            var editor = $(this).data('CodeMirrorInstance');
            if (editor) {
                editor.refresh(); 
            }
        });
    });
}
function ftSelectedRank() {
  var htranksel = localStorage.getItem('htranksel');
  if (htranksel) {
    var sotab = document.querySelector('[onclick="httab(event, \'' + htranksel + '\')"]');
    if (sotab) {
      sotab.click();
    }
  }
}
document.addEventListener("DOMContentLoaded", function() {
  ftSelectedRank();
});
// display none
function getStyle(x, styleProp) {
    if (x.currentStyle) {
        var y = x.currentStyle[styleProp];
    }
    else if (window.getComputedStyle) {
        var y = document.defaultView.getComputedStyle(x, null).getPropertyValue(styleProp);
    }
    return y;
}
function htnone(e, div_name) {
    var el = document.getElementById(div_name);
    var display = el.style.display || getStyle(el, 'display');
    el.style.display = (display == 'none') ? 'block' : 'none';
    htnone.el = el;
    if (e.stopPropagation) e.stopPropagation();
    e.cancelBubble = true;
    return false;
}
// lay images tu media
jQuery(document).ready(function($) {
    $('.ht-selec').click(function(e) {
        e.preventDefault();
        var inputId = $(this).data('input-id');
        openMediaUploader(inputId);
    });

    function openMediaUploader(inputId) {
        var customUploader = wp.media({
            title: 'Media',
            button: {
                text: 'OK'
            },
            multiple: false
        });

        customUploader.on('select', function() {
            var attachment = customUploader.state().get('selection').first().toJSON();
            var imageUrl = attachment.url;
            $('#' + inputId).val(imageUrl);
        });

        customUploader.open();
    }
});

// kiem tra trang thai check
jQuery(document).ready(function($) {
    $('.toggle-checkbox').each(function() {
        var targetDiv = $('#' + $(this).data('target'));
        if ($(this).is(':checked')) {
            targetDiv.removeClass('noon');
        } else {
            targetDiv.addClass('noon');
        }
        $(this).change(function() {
            if ($(this).is(':checked')) {
                targetDiv.removeClass('noon');
            } else {
                targetDiv.addClass('noon');
            }
        });
    });
});
// thay ray keo qua lai
var sliders = document.querySelectorAll(".htslide");
sliders.forEach(function (slider) {
  var output = document.getElementById("demo" + slider.dataset.index);
  output.innerHTML = slider.value;
  slider.oninput = function () {
    output.innerHTML = this.value;
  };
});

/* ---------------------------------------------------------------------------
   Horse Tools admin UI (inc/ui.php + inc/registry.php)
   ------------------------------------------------------------------------ */
(function () {
	'use strict';

	var reg = window.horsetoolsRegistry;

	/* ---- Dependent fields ------------------------------------------------
	   Set the real `disabled` attribute. The old approach put
	   pointer-events:none on a wrapper, which blocks the mouse but not the
	   keyboard — so tabbing into a greyed-out card and hitting Space still
	   saved the value, and turning the parent back on later brought a pile of
	   settings alive that the user never knowingly enabled. */
	function syncDependents() {
		document.querySelectorAll('[data-ht-parent]').forEach(function (field) {
			var parent = document.getElementById(field.getAttribute('data-ht-parent'));
			if (!parent) { return; }
			var on = parent.checked;
			field.classList.toggle('ht-dep-off', !on);
			field.querySelectorAll('input, select, textarea, button').forEach(function (el) {
				el.disabled = !on;
			});
		});
	}

	document.addEventListener('change', function (e) {
		if (e.target && e.target.type === 'checkbox') { syncDependents(); }
	});

	/* ---- Unsaved-changes bar ---------------------------------------------
	   Twelve copies of a silent auto-save used to POST the entire form on every
	   checkbox change, reporting the result only to console.log. Tell the user
	   instead. */
	function initDirtyBar(form) {
		if (!form || !reg) { return; }
		var bar = null;
		var count = 0;
		var changed = Object.create(null);

		function render() {
			if (!count) {
				if (bar) { bar.remove(); bar = null; }
				return;
			}
			if (!bar) {
				bar = document.createElement('div');
				bar.className = 'ht-dirty-bar';
				bar.setAttribute('role', 'status');
				bar.innerHTML =
					'<span class="ht-dirty-text"></span>' +
					'<button type="button" class="ht-dirty-save"></button>' +
					'<button type="button" class="ht-dirty-discard"></button>';
				bar.querySelector('.ht-dirty-save').textContent = reg.i18n.save;
				bar.querySelector('.ht-dirty-discard').textContent = reg.i18n.discard;
				bar.querySelector('.ht-dirty-save').addEventListener('click', function () {
					window.onbeforeunload = null;
					form.submit();
				});
				bar.querySelector('.ht-dirty-discard').addEventListener('click', function () {
					window.onbeforeunload = null;
					window.location.reload();
				});
				document.body.appendChild(bar);
			}
			bar.querySelector('.ht-dirty-text').textContent =
				reg.i18n.unsaved + ' (' + count + ')';
		}

		form.addEventListener('change', function (e) {
			var el = e.target;
			if (!el || !el.name) { return; }
			changed[el.name] = true;
			count = Object.keys(changed).length;
			render();
			window.onbeforeunload = function () { return reg.i18n.unsaved; };
		});
		form.addEventListener('submit', function () { window.onbeforeunload = null; });

		// The Add-code screen has a deliberate quick-save button that posts the
		// form over AJAX. It announces success so the bar can clear itself
		// rather than keep warning about changes that are already saved.
		document.addEventListener('horsetools:saved', function () {
			changed = Object.create(null);
			count = 0;
			window.onbeforeunload = null;
			render();
		});
	}

	/* ---- Sidebar: search + what's enabled --------------------------------- */
	function jumpTo(id) {
		var el = document.getElementById(id);
		if (!el) { return; }
		// Reveal the tab that holds it before scrolling.
		var box = el.closest('.sotab-box');
		if (box && box.id) {
			var btn = document.querySelector('[onclick*="' + box.id + '"]');
			if (btn) { btn.click(); }
		}
		var row = el.closest('.ht-field') || el;
		row.scrollIntoView({ block: 'center', behavior: 'smooth' });
		row.classList.remove('ht-flash');
		void row.offsetWidth;
		row.classList.add('ht-flash');
		if (typeof el.focus === 'function') { el.focus({ preventScroll: true }); }
	}

	// Lowercase + strip Vietnamese diacritics so "bao mat" matches "Bảo mật".
	function normText(s) {
		s = String(s == null ? '' : s).toLowerCase();
		try { s = s.normalize('NFD').replace(/[̀-ͯ]/g, ''); } catch (e) {}
		return s.replace(/đ/g, 'd');
	}

	function listItem(field) {
		var li = document.createElement('li');
		var btn = document.createElement('button');
		btn.type = 'button';
		btn.textContent = field.label || field.key;
		var onOtherPage = field.page && reg.current && field.page !== reg.current;
		var pageTitle = (reg.pages && reg.pages[field.page]) || '';
		var crumbParts = [onOtherPage ? pageTitle : '', field.tab, field.section].filter(Boolean);
		if (crumbParts.length) {
			var crumb = document.createElement('span');
			crumb.className = 'ht-side-crumb';
			crumb.textContent = crumbParts.join(' › ');
			btn.appendChild(crumb);
		}
		btn.addEventListener('click', function () {
			// On this page: open the tab and flash the control. On another page:
			// deep-link there — #ht-jump is handled on load at the destination.
			if (document.getElementById(field.id)) {
				jumpTo(field.id);
			} else if (field.page) {
				window.location.href = 'admin.php?page=' + encodeURIComponent(field.page)
					+ (field.id ? '#ht-jump=' + encodeURIComponent(field.id) : '');
			}
		});
		li.appendChild(btn);
		return li;
	}

	function initSidebar() {
		var host = document.querySelector('.ht-sidebar');
		if (!host || !reg || !reg.fields.length) { return; }

		var search = document.createElement('input');
		search.type = 'search';
		search.className = 'ht-side-search';
		search.placeholder = reg.i18n.search;
		search.setAttribute('aria-label', reg.i18n.search);

		var results = document.createElement('ul');
		results.className = 'ht-side-list';

		var activeTitle = document.createElement('p');
		activeTitle.className = 'ht-side-title';

		var activeList = document.createElement('ul');
		activeList.className = 'ht-side-list';

		host.prepend(activeList);
		host.prepend(activeTitle);
		host.prepend(results);
		host.prepend(search);

		var activeSet = {};
		reg.active.forEach(function (id) { activeSet[id] = true; });
		var activeFields = reg.fields.filter(function (f) { return activeSet[f.id]; });

		activeTitle.textContent = reg.i18n.activeTitle;
		if (activeFields.length) {
			activeTitle.textContent += ' (' + activeFields.length + ')';
			activeFields.forEach(function (f) { activeList.appendChild(listItem(f)); });
		} else {
			var none = document.createElement('p');
			none.className = 'ht-side-empty';
			none.textContent = reg.i18n.noneActive;
			activeList.appendChild(none);
		}

		// Screens themselves are searchable too ("Bảng" finds the Tables page).
		var pageEntries = [];
		Object.keys(reg.pages || {}).forEach(function (slug) {
			pageEntries.push({ id: '', label: reg.pages[slug], tab: '', section: '', page: slug, key: '' });
		});

		search.addEventListener('input', function () {
			var q = normText(search.value.trim());
			results.innerHTML = '';
			var showing = q.length > 0;
			activeTitle.hidden = showing;
			activeList.hidden = showing;
			if (!showing) { return; }

			var pool = pageEntries.concat(reg.fields);
			var hits = pool.filter(function (f) {
				var page = (reg.pages && reg.pages[f.page]) || '';
				return normText(f.label + ' ' + f.key + ' ' + f.section + ' ' + f.tab + ' ' + page)
					.indexOf(q) !== -1;
			}).slice(0, 25);

			if (!hits.length) {
				var empty = document.createElement('p');
				empty.className = 'ht-side-empty';
				empty.textContent = reg.i18n.noResults;
				results.appendChild(empty);
				return;
			}
			hits.forEach(function (f) { results.appendChild(listItem(f)); });
		});
	}

	function jumpFromHash() {
		var m = /#ht-jump=([^&]+)/.exec(location.hash || '');
		if (m) { jumpTo(decodeURIComponent(m[1])); }
	}
	// Health-card "fix" links (and any link carrying #ht-jump=<field-id>) open
	// the right tab and scroll to the exact control, instead of just reloading
	// the page to its default tab.
	document.addEventListener('click', function (e) {
		var a = e.target.closest('a[href*="#ht-jump="]');
		if (!a) { return; }
		var m = /#ht-jump=([^&]+)/.exec(a.getAttribute('href') || '');
		if (!m) { return; }
		var id = decodeURIComponent(m[1]);
		if (document.getElementById(id)) { e.preventDefault(); jumpTo(id); }
	});
	window.addEventListener('hashchange', jumpFromHash);

	document.addEventListener('DOMContentLoaded', function () {
		syncDependents();
		initSidebar();
		initDirtyBar(document.querySelector('.ht-wrap form[action$="options.php"]'));
		jumpFromHash();
	});
})();
