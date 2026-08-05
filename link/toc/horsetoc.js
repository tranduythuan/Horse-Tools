document.addEventListener("DOMContentLoaded", function () {
    const tocMain = document.querySelector(".ht-toc-main");
    if (!tocMain) return;
    const tocList = document.getElementById("ht-toc-list");
	const tocTitHi = document.querySelector(".ht-toc-tit-hi");
    const articleContent = document.getElementById("ht-toc");
    const tocClose = document.querySelector(".ht-toc-close");
    const tocPlaceholder = document.querySelector(".ht-toc-placeholder");
    const headingsSelector = tocPlaceholder.getAttribute("data-h");
    const headingsnumber = tocPlaceholder.getAttribute("data-on");
	const headingsicon = tocPlaceholder.getAttribute("data-ico");
    if (!headingsSelector) return;
    const headings = articleContent.querySelectorAll(headingsSelector);
    if (headings.length === 0) {
        tocMain.style.display = 'none';
        return;
    }
    // Thiết lập đối tượng để xác định level
    const headingLevels = Array.from(new Set(Array.from(headings).map(h => h.tagName))).sort();
    const levelsMap = headingLevels.reduce((acc, tag, index) => {
        acc[tag] = index; 
        return acc;
    }, {});
	//
    let counters = Array(headingLevels.length).fill(0);
    let headingLinks = [];
    let ItemList = [];

	function convertToSlug(text, index) {
		// NFD does not decompose \u0110/\u0111 (U+0110/U+0111), so the old filter stripped
		// them outright \u2014 "\u0110\u01b0\u1eddng" became "uong". Map them explicitly first.
		text = text.replace(/\u0110/g, "D").replace(/\u0111/g, "d");
		text = text.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
		// Keep digits and hyphens. Dropping digits meant "B\u01b0\u1edbc 2" and "B\u01b0\u1edbc 3"
		// produced the same slug, and a heading made only of digits or CJK
		// collapsed to "" \u2014 the anchor then became href="#" and the click
		// handler ran document.querySelector("#"), which throws a SyntaxError
		// and kills the whole handler.
		text = text.toLowerCase().replace(/[^a-z0-9\s-]/g, "").replace(/\s+/g, "-");
		text = text.replace(/-+/g, "-").replace(/^-+|-+$/g, "");
		return text || "toc-" + index;
	}
    const usedIds = {};

    headings.forEach((heading, index) => {
        if (heading.textContent.trim() === "") return;
        if (heading.textContent === "Table of Contents") return;
        // Lấy level dựa trên thẻ tiêu đề
        const level = levelsMap[heading.tagName];
        counters[level]++;
        counters.fill(0, level + 1);
		//
        let baseId = convertToSlug(heading.textContent.trim(), index);
        let uniqueId = baseId;
        let count = 1;
        while (usedIds[uniqueId]) {
            uniqueId = `${baseId}-${count}`;
            count++;
        }
        usedIds[uniqueId] = true;
        heading.id = uniqueId;
		//
        const indent = counters.slice(0, level + 1).filter(num => num > 0).join(".");
        const listItem = document.createElement("li");
        listItem.classList.add(`ht-toc-level-${level + 1}`);

        const link = document.createElement("a");
        link.href = `#${heading.id}`;
        const span = document.createElement("span");
        if (headingsnumber === "on") {
            span.textContent = `${indent}. ${heading.textContent}`;
        } else {
            span.textContent = `${heading.textContent}`;
        }
        link.appendChild(span);
        listItem.appendChild(link);

        tocList.appendChild(listItem);
        headingLinks.push({ link: link, heading: heading });

        ItemList.push({
            "@type": "ListItem",
            "position": index + 1,
            "name": heading.textContent,
            "item": window.location.href.split("#")[0] + `#${heading.id}`
        });

        link.addEventListener("click", function (event) {
            event.preventDefault();
            const targetHeading = document.querySelector(this.getAttribute("href"));
            const offset = 50;
            const elementPosition = targetHeading.getBoundingClientRect().top;
            const startPosition = window.pageYOffset;
            const offsetPosition = elementPosition + startPosition - offset;
            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth"
            });

            // Not every browser or embedded view honours smooth scrolling, and
            // some pages suppress it. preventDefault() has already cancelled the
            // ordinary anchor jump, so where smooth scrolling does nothing the
            // link does nothing at all — worse than the behaviour it replaced.
            // If nothing has moved shortly after, jump.
            setTimeout(function () {
                if (Math.abs(window.pageYOffset - startPosition) < 2 &&
                    Math.abs(offsetPosition - startPosition) > 2) {
                    window.scrollTo(0, offsetPosition);
                }
            }, 300);

            targetHeading.classList.add("ht-toc-light");
            setTimeout(() => {
                targetHeading.classList.remove("ht-toc-light");
            }, 1000);
        });
    });

    const jsonLdScript = document.createElement("script");
    jsonLdScript.type = "application/ld+json";
    jsonLdScript.textContent = JSON.stringify({
        "@context": "https://schema.org",
        "@type": "ItemList",
        "itemListElement": ItemList
    });
    document.head.appendChild(jsonLdScript);

    window.tocclose = function () {
        if (tocMain.classList.contains("ht-toc-main-open")) {
            tocMain.classList.remove("ht-toc-main-open");
        } else {
            tocMain.classList.add("ht-toc-main-open");
        }
    };

    document.addEventListener("click", function (event) {
        if (!tocMain.contains(event.target) && tocMain.classList.contains("ht-toc-main-open")) {
            tocMain.classList.remove("ht-toc-main-open");
        }
    });

    tocMain.addEventListener("click", function (event) {
        event.stopPropagation();
    });

    function highlightCurrentHeading() {
        let currentHeading = null;
        for (const { link, heading } of headingLinks) {
            const rect = heading.getBoundingClientRect();
            if (rect.top >= 0 && rect.top <= window.innerHeight) {
                currentHeading = link;
                break;
            }
        }
        headingLinks.forEach(({ link }) => {
            link.classList.remove("active-toc-link");
        });
        if (currentHeading) {
            currentHeading.classList.add("active-toc-link");
        }
        const tocRect = articleContent.getBoundingClientRect();
        tocMain.style.display = tocRect.bottom > 0 ? 'block' : 'none';
        tocClose.style.display = tocRect.bottom > 0 ? 'flex' : 'none';
		if (tocPlaceholder) {
			const nftoc = tocPlaceholder.offsetHeight;
			const oftoc = tocPlaceholder.getBoundingClientRect().top + window.pageYOffset;
			if ((window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop) > oftoc + nftoc) {
					tocPlaceholder.style.height = nftoc + "px";
					if (!headingsicon || headingsicon !== "off") {
						tocMain.classList.add("ht-toc-main-vuot");
					}
			} else {
					tocPlaceholder.style.height = "auto";
					if (!headingsicon || headingsicon !== "off") {
						tocMain.classList.remove("ht-toc-main-vuot");
						tocClose.style.display = 'none';
			        }
			}
		}
    }
    highlightCurrentHeading();
    document.addEventListener("scroll", function () {
        highlightCurrentHeading();
    });

	let horseiconan = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 1024 1024"><path fill="currentColor" d="M104.704 338.752a64 64 0 0 1 90.496 0l316.8 316.8l316.8-316.8a64 64 0 0 1 90.496 90.496L557.248 791.296a64 64 0 0 1-90.496 0L104.704 429.248a64 64 0 0 1 0-90.496"/></svg>';
	let horseiconhien = '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 1024 1024"><path fill="currentColor" d="M104.704 685.248a64 64 0 0 0 90.496 0l316.8-316.8l316.8 316.8a64 64 0 0 0 90.496-90.496L557.248 232.704a64 64 0 0 0-90.496 0L104.704 594.752a64 64 0 0 0 0 90.496"/></svg>';
	function initTocIcon() {
		const isTocListHidden = getComputedStyle(tocList).display === "none"; 
		if (isTocListHidden) {
			tocTitHi.classList.add("ht-toc-tit-hi-active");
			tocTitHi.innerHTML = horseiconan; 
		} else {
			tocTitHi.classList.remove("ht-toc-tit-hi-active");
			tocTitHi.innerHTML = horseiconhien; 
		}
	}
	function toclisthi() {
		if (getComputedStyle(tocList).display === "none") {
			tocList.style.display = "block"; 
			tocTitHi.classList.remove("ht-toc-tit-hi-active");
			tocTitHi.innerHTML = horseiconhien; 
		} else {
			tocList.style.display = "none"; 
			tocTitHi.classList.add("ht-toc-tit-hi-active");
			tocTitHi.innerHTML = horseiconan; 
		}
	}
	window.onload = function() {
		initTocIcon();
	};
	if (tocTitHi) {
		tocTitHi.addEventListener("click", function () {
			toclisthi();
		});
	}
});
