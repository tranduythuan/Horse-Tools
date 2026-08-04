/**
 * Horse Tools — table interactivity (sort / search / pagination and the
 * reader-facing tools: index column, per-column filters, match highlighting,
 * copy / CSV / print / column visibility, frozen first column).
 *
 * Vanilla JS on purpose: no jQuery, no DataTables. It runs only on tables that
 * ask for it via data attributes emitted by the renderer:
 *   <div class="ht-table" data-ht-sort="1" data-ht-search="1" data-ht-page="10"
 *        data-ht-index="1" data-ht-colfilter="1" data-ht-tools="1"
 *        data-ht-freeze="1">
 *
 * Vietnamese-friendly: search is diacritic-insensitive ("chuot" matches
 * "Chuột"), and numeric sorting understands VN formats ("1.790.000", "1,5").
 *
 * Only <tbody> rows are ever touched — a pinned total row lives in <tfoot> and
 * is left alone by every feature here, by construction.
 */
( function () {
	var T = window.htTableFx || {};
	function t( k, d ) { return T[ k ] || d; }

	// Lowercase + strip diacritics (đ→d) so search works without typing accents.
	function norm( s ) {
		s = String( s == null ? '' : s ).toLowerCase();
		try { s = s.normalize( 'NFD' ).replace( /[̀-ͯ]/g, '' ); } catch ( e ) {}
		return s.replace( /đ/g, 'd' );
	}

	// Parse a cell as a number, handling VN/EU separators. null = not a number.
	// Only fully-numeric cells qualify (same rule as the auto right-align), so a
	// text column like "iPhone 15" never gets mistaken for numbers — mixed text
	// still sorts naturally via localeCompare's numeric collation.
	function numVal( s ) {
		s = String( s == null ? '' : s ).trim();
		if ( ! s ) { return null; }
		if ( ! /^[+\-]?[\d.,\s]+\s*[%đ$₫]?$/.test( s ) ) { return null; }
		s = s.replace( /[^\d.,\-]/g, '' );
		if ( ! s || s === '-' ) { return null; }
		var hasD = s.indexOf( '.' ) !== -1, hasC = s.indexOf( ',' ) !== -1;
		if ( hasD && hasC ) {
			// 1.790.000,50 → dots are thousands, comma is decimal.
			s = s.replace( /\./g, '' ).replace( ',', '.' );
		} else if ( hasD ) {
			var p = s.split( '.' );
			if ( p.length > 2 || ( p.length === 2 && p[ 1 ].length === 3 ) ) { s = s.replace( /\./g, '' ); }
		} else if ( hasC ) {
			var q = s.split( ',' );
			if ( q.length > 2 || ( q.length === 2 && q[ 1 ].length === 3 ) ) { s = s.replace( /,/g, '' ); }
			else { s = s.replace( ',', '.' ); }
		}
		var n = parseFloat( s );
		return isNaN( n ) ? null : n;
	}

	function el( tag, cls, text ) {
		var e = document.createElement( tag );
		if ( cls ) { e.className = cls; }
		if ( text != null ) { e.textContent = text; }
		return e;
	}

	/**
	 * The cell that covers a given column index in a row, accounting for
	 * colspans — needed so hiding a column stays aligned in rows that merge
	 * cells (a "TOTAL" footer spanning two columns, merged headers…).
	 */
	function cellAt( tr, col ) {
		var at = 0;
		for ( var i = 0; i < tr.children.length; i++ ) {
			var c = tr.children[ i ];
			var span = parseInt( c.getAttribute( 'colspan' ), 10 ) || 1;
			if ( col < at + span ) { return c; }
			at += span;
		}
		return null;
	}

	function init( wrap ) {
		var doSort = wrap.getAttribute( 'data-ht-sort' ) === '1';
		var doSearch = wrap.getAttribute( 'data-ht-search' ) === '1';
		var per = parseInt( wrap.getAttribute( 'data-ht-page' ), 10 ) || 0;
		var doIndex = wrap.getAttribute( 'data-ht-index' ) === '1';
		var doFilter = wrap.getAttribute( 'data-ht-colfilter' ) === '1';
		var doTools = wrap.getAttribute( 'data-ht-tools' ) === '1';
		var doFreeze = wrap.getAttribute( 'data-ht-freeze' ) === '1';
		if ( ! doSort && ! doSearch && ! per && ! doIndex && ! doFilter && ! doTools && ! doFreeze ) { return; }

		var table = wrap.querySelector( 'table' );
		var tbody = table && table.querySelector( 'tbody' );
		if ( ! tbody ) { return; }
		var thead = table.querySelector( 'thead' );
		var tfoot = table.querySelector( 'tfoot' );
		var headRow = thead ? thead.rows[ 0 ] : null;

		if ( doFreeze ) { wrap.classList.add( 'ht-t-freeze' ); }

		// ---- Index column: one extra cell at the front of every row --------
		if ( doIndex ) {
			if ( headRow ) {
				var ith = el( 'th', 'ht-t-idx', t( 'indexHead', '#' ) );
				headRow.insertBefore( ith, headRow.firstChild );
			}
			[].slice.call( tbody.rows ).forEach( function ( tr ) {
				tr.insertBefore( el( 'td', 'ht-t-idx', '' ), tr.firstChild );
			} );
			if ( tfoot ) {
				[].slice.call( tfoot.rows ).forEach( function ( tr ) {
					tr.insertBefore( el( 'td', 'ht-t-idx', '' ), tr.firstChild );
				} );
			}
		}

		var rows = [].slice.call( tbody.rows );
		var ths = headRow ? [].slice.call( headRow.cells ) : [];
		var ncol = 0;
		ths.forEach( function ( th ) { ncol += parseInt( th.getAttribute( 'colspan' ), 10 ) || 1; } );
		if ( ! ncol ) { ncol = rows[ 0 ] ? rows[ 0 ].children.length : 1; }

		// Original markup of each row, so highlighting can always be undone
		// without damaging links or other markup inside the cells.
		rows.forEach( function ( tr ) { tr.setAttribute( 'data-ht-html', '' ); tr._htHtml = tr.innerHTML; } );

		var state = { q: '', col: -1, dir: 1, page: 1, filters: {}, hidden: {} };
		var inp = null, nav = null, bar = null;

		function cellText( tr, i ) { var c = tr.children[ i ]; return c ? c.textContent : ''; }

		/* ---- Toolbar: search box + action buttons ---------------------- */
		if ( doSearch || doTools ) {
			bar = el( 'div', 'ht-t-bar' );
			wrap.insertBefore( bar, wrap.firstChild );
		}
		if ( doSearch ) {
			inp = document.createElement( 'input' );
			inp.type = 'search';
			inp.className = 'ht-t-search';
			inp.placeholder = t( 'search', 'Search the table…' );
			inp.setAttribute( 'aria-label', inp.placeholder );
			bar.appendChild( inp );
			inp.addEventListener( 'input', function () { state.q = inp.value; state.page = 1; render(); } );
		}
		if ( doTools ) {
			var tools = el( 'div', 'ht-t-tools' );
			bar.appendChild( tools );

			var bCopy = el( 'button', 'ht-t-btn', t( 'copy', 'Copy' ) );
			bCopy.type = 'button';
			bCopy.addEventListener( 'click', function () { copyOut( bCopy ); } );
			tools.appendChild( bCopy );

			var bCsv = el( 'button', 'ht-t-btn', t( 'csv', 'CSV' ) );
			bCsv.type = 'button';
			bCsv.addEventListener( 'click', csvOut );
			tools.appendChild( bCsv );

			var bPrint = el( 'button', 'ht-t-btn', t( 'print', 'Print' ) );
			bPrint.type = 'button';
			bPrint.addEventListener( 'click', printOut );
			tools.appendChild( bPrint );

			// Column visibility: a small popover of checkboxes.
			var colWrap = el( 'span', 'ht-t-colmenu' );
			var bCols = el( 'button', 'ht-t-btn', t( 'columns', 'Columns' ) );
			bCols.type = 'button';
			bCols.setAttribute( 'aria-expanded', 'false' );
			var panel = el( 'div', 'ht-t-colpanel' );
			panel.hidden = true;
			ths.forEach( function ( th, i ) {
				if ( doIndex && i === 0 ) { return; }
				var lab = el( 'label' );
				var cb = document.createElement( 'input' );
				cb.type = 'checkbox';
				cb.checked = true;
				cb.addEventListener( 'change', function () {
					setColumn( i, cb.checked );
				} );
				lab.appendChild( cb );
				lab.appendChild( document.createTextNode( ' ' + ( th.textContent.trim() || ( t( 'column', 'Column' ) + ' ' + ( i + 1 ) ) ) ) );
				panel.appendChild( lab );
			} );
			bCols.addEventListener( 'click', function () {
				panel.hidden = ! panel.hidden;
				bCols.setAttribute( 'aria-expanded', panel.hidden ? 'false' : 'true' );
			} );
			document.addEventListener( 'click', function ( e ) {
				if ( ! panel.hidden && ! colWrap.contains( e.target ) ) {
					panel.hidden = true;
					bCols.setAttribute( 'aria-expanded', 'false' );
				}
			} );
			colWrap.appendChild( bCols );
			colWrap.appendChild( panel );
			tools.appendChild( colWrap );
		}

		if ( per ) {
			nav = el( 'div', 'ht-t-pagenav' );
			wrap.appendChild( nav );
		}

		/* ---- Sorting --------------------------------------------------- */
		if ( doSort ) {
			ths.forEach( function ( th, i ) {
				if ( doIndex && i === 0 ) { return; } // the counter has no order of its own
				th.classList.add( 'ht-t-sortable' );
				th.setAttribute( 'tabindex', '0' );
				th.setAttribute( 'role', 'button' );
				function go() {
					if ( state.col === i ) { state.dir = -state.dir; } else { state.col = i; state.dir = 1; }
					state.page = 1;
					render();
				}
				th.addEventListener( 'click', go );
				th.addEventListener( 'keydown', function ( e ) {
					if ( e.key === 'Enter' || e.key === ' ' ) { e.preventDefault(); go(); }
				} );
			} );
		}

		/* ---- Per-column filter dropdowns ------------------------------- */
		if ( doFilter && headRow && thead ) {
			var frow = el( 'tr', 'ht-t-filterrow' );
			ths.forEach( function ( th, i ) {
				var cell = el( 'th' );
				if ( ! ( doIndex && i === 0 ) ) {
					var values = {};
					rows.forEach( function ( tr ) {
						var v = cellText( tr, i ).trim();
						if ( v ) { values[ v ] = true; }
					} );
					var list = Object.keys( values ).sort( function ( a, b ) {
						var an = numVal( a ), bn = numVal( b );
						if ( an !== null && bn !== null ) { return an - bn; }
						return a.localeCompare( b, 'vi', { numeric: true, sensitivity: 'base' } );
					} );
					if ( list.length > 1 && list.length <= 200 ) {
						var sel = document.createElement( 'select' );
						sel.className = 'ht-t-colsel';
						sel.setAttribute( 'aria-label', th.textContent.trim() );
						sel.appendChild( new Option( t( 'filterAll', 'All' ), '' ) );
						list.forEach( function ( v ) { sel.appendChild( new Option( v, v ) ); } );
						sel.addEventListener( 'change', function () {
							if ( sel.value ) { state.filters[ i ] = sel.value; } else { delete state.filters[ i ]; }
							state.page = 1;
							render();
						} );
						cell.appendChild( sel );
					}
				}
				frow.appendChild( cell );
			} );
			thead.appendChild( frow );
		}

		/* ---- Column visibility ----------------------------------------- */
		function setColumn( i, show ) {
			if ( show ) { delete state.hidden[ i ]; } else { state.hidden[ i ] = true; }
			var allRows = [].slice.call( thead ? thead.rows : [] )
				.concat( rows )
				.concat( [].slice.call( tfoot ? tfoot.rows : [] ) );
			allRows.forEach( function ( tr ) {
				var c = cellAt( tr, i );
				if ( ! c ) { return; }
				var span = parseInt( c.getAttribute( 'colspan' ), 10 ) || 1;
				if ( span > 1 ) {
					// A merged cell keeps its place but narrows by one column.
					if ( c.getAttribute( 'data-ht-span' ) === null ) { c.setAttribute( 'data-ht-span', span ); }
					var base = parseInt( c.getAttribute( 'data-ht-span' ), 10 ) || span;
					var hiddenInside = 0;
					var start = 0, k;
					for ( k = 0; k < tr.children.length; k++ ) {
						if ( tr.children[ k ] === c ) { break; }
						start += parseInt( tr.children[ k ].getAttribute( 'colspan' ), 10 ) || 1;
					}
					for ( k = start; k < start + base; k++ ) {
						if ( state.hidden[ k ] ) { hiddenInside++; }
					}
					var left = Math.max( 1, base - hiddenInside );
					c.setAttribute( 'colspan', left );
					c.style.display = hiddenInside >= base ? 'none' : '';
				} else {
					c.style.display = show ? '' : 'none';
				}
			} );
		}

		/* ---- Highlighting ---------------------------------------------- */
		var hlOn = false;
		function clearHighlight() {
			if ( ! hlOn ) { return; }
			rows.forEach( function ( tr ) { tr.innerHTML = tr._htHtml; } );
			hlOn = false;
			// Re-hiding survives the innerHTML rewrite.
			Object.keys( state.hidden ).forEach( function ( i ) { setColumn( +i, false ); } );
		}
		function highlight( list, q ) {
			clearHighlight();
			if ( ! q ) { return; }
			list.forEach( function ( tr ) {
				var walker = document.createTreeWalker( tr, NodeFilter.SHOW_TEXT, null );
				var nodes = [], n;
				while ( ( n = walker.nextNode() ) ) { nodes.push( n ); }
				nodes.forEach( function ( node ) {
					var text = node.nodeValue;
					var hay = norm( text );
					var at = hay.indexOf( q );
					if ( at === -1 ) { return; }
					// norm() is 1:1 on length (lowercase + mark removal on
					// composed chars), so offsets map straight back.
					var frag = document.createDocumentFragment();
					var pos = 0;
					while ( at !== -1 ) {
						if ( at > pos ) { frag.appendChild( document.createTextNode( text.slice( pos, at ) ) ); }
						var mark = el( 'mark', 'ht-t-hl', text.slice( at, at + q.length ) );
						frag.appendChild( mark );
						pos = at + q.length;
						at = hay.indexOf( q, pos );
					}
					if ( pos < text.length ) { frag.appendChild( document.createTextNode( text.slice( pos ) ) ); }
					node.parentNode.replaceChild( frag, node );
				} );
			} );
			hlOn = true;
		}

		/* ---- Data out: copy / CSV / print ------------------------------ */
		function visibleCols() {
			var out = [];
			for ( var i = 0; i < ncol; i++ ) { if ( ! state.hidden[ i ] ) { out.push( i ); } }
			return out;
		}
		// Everything the current filters leave in, not just the open page.
		function matrix() {
			var cols = visibleCols();
			var data = [];
			if ( ths.length ) {
				data.push( cols.map( function ( i ) { return ths[ i ] ? ths[ i ].textContent.trim() : ''; } ) );
			}
			filtered().forEach( function ( tr ) {
				data.push( cols.map( function ( i ) { return cellText( tr, i ).trim(); } ) );
			} );
			if ( tfoot && tfoot.rows.length ) {
				var fr = tfoot.rows[ 0 ];
				data.push( cols.map( function ( i ) { var c = cellAt( fr, i ); return c ? c.textContent.trim() : ''; } ) );
			}
			return data;
		}
		function copyOut( btn ) {
			var text = matrix().map( function ( r ) { return r.join( '\t' ); } ).join( '\n' );
			var done = function () {
				var old = btn.textContent;
				btn.textContent = t( 'copied', 'Copied' );
				setTimeout( function () { btn.textContent = old; }, 1500 );
			};
			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( text ).then( done, function () { fallbackCopy( text, done ); } );
			} else {
				fallbackCopy( text, done );
			}
		}
		function fallbackCopy( text, done ) {
			var ta = document.createElement( 'textarea' );
			ta.value = text;
			ta.setAttribute( 'readonly', '' );
			ta.style.position = 'fixed';
			ta.style.opacity = '0';
			document.body.appendChild( ta );
			ta.select();
			try { document.execCommand( 'copy' ); done(); } catch ( e ) {}
			ta.remove();
		}
		function csvOut() {
			var csv = matrix().map( function ( r ) {
				return r.map( function ( c ) { return '"' + String( c ).replace( /"/g, '""' ) + '"'; } ).join( ',' );
			} ).join( '\r\n' );
			var blob = new Blob( [ '﻿' + csv ], { type: 'text/csv;charset=utf-8' } );
			var a = document.createElement( 'a' );
			a.href = URL.createObjectURL( blob );
			a.download = 'table.csv';
			document.body.appendChild( a );
			a.click();
			a.remove();
			setTimeout( function () { URL.revokeObjectURL( a.href ); }, 5000 );
		}
		function printOut() {
			var data = matrix();
			var esc = function ( s ) {
				return String( s ).replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
			};
			var html = '<table><thead><tr>' + ( data[ 0 ] || [] ).map( function ( c ) {
				return '<th>' + esc( c ) + '</th>';
			} ).join( '' ) + '</tr></thead><tbody>' + data.slice( 1 ).map( function ( r ) {
				return '<tr>' + r.map( function ( c ) { return '<td>' + esc( c ) + '</td>'; } ).join( '' ) + '</tr>';
			} ).join( '' ) + '</tbody></table>';
			var cap = table.querySelector( 'caption' );
			// An off-screen iframe prints without tripping pop-up blockers.
			var fr = document.createElement( 'iframe' );
			fr.style.position = 'fixed';
			fr.style.right = '0';
			fr.style.bottom = '0';
			fr.style.width = '0';
			fr.style.height = '0';
			fr.style.border = '0';
			document.body.appendChild( fr );
			var d = fr.contentWindow.document;
			d.open();
			d.write( '<!doctype html><meta charset="utf-8"><title>' + esc( document.title ) + '</title>'
				+ '<style>body{font:14px/1.5 system-ui,sans-serif;margin:16px;}'
				+ 'h1{font-size:18px;margin:0 0 12px;}'
				+ 'table{border-collapse:collapse;width:100%;}'
				+ 'th,td{border:1px solid #999;padding:6px 9px;text-align:left;}'
				+ 'thead th{background:#eee;}</style>'
				+ ( cap ? '<h1>' + esc( cap.textContent.trim() ) + '</h1>' : '' ) + html );
			d.close();
			fr.contentWindow.focus();
			fr.contentWindow.print();
			setTimeout( function () { fr.remove(); }, 1000 );
		}

		/* ---- The pipeline: filter → sort → paginate --------------------- */
		function filtered() {
			var list = rows;
			var cols = Object.keys( state.filters );
			if ( cols.length ) {
				list = list.filter( function ( tr ) {
					return cols.every( function ( i ) { return cellText( tr, +i ).trim() === state.filters[ i ]; } );
				} );
			}
			if ( state.q ) {
				var q = norm( state.q );
				list = list.filter( function ( tr ) { return norm( tr.textContent ).indexOf( q ) !== -1; } );
			}
			return list;
		}

		function render() {
			clearHighlight();
			var list = filtered();

			if ( state.col > -1 ) {
				var i = state.col, dir = state.dir;
				// Sort numerically only if every non-empty cell in the column parses.
				var numeric = true, seen = false;
				for ( var k = 0; k < list.length; k++ ) {
					var v = cellText( list[ k ], i ).trim();
					if ( ! v ) { continue; }
					seen = true;
					if ( numVal( v ) === null ) { numeric = false; break; }
				}
				numeric = numeric && seen;
				list = list.slice().sort( function ( a, b ) {
					var av = cellText( a, i ).trim(), bv = cellText( b, i ).trim();
					if ( numeric ) {
						var an = numVal( av ), bn = numVal( bv );
						an = an === null ? -Infinity : an;
						bn = bn === null ? -Infinity : bn;
						return ( an - bn ) * dir;
					}
					return av.localeCompare( bv, 'vi', { numeric: true, sensitivity: 'base' } ) * dir;
				} );
			}
			ths.forEach( function ( th, i ) {
				th.classList.remove( 'ht-t-asc', 'ht-t-desc' );
				th.removeAttribute( 'aria-sort' );
				if ( i === state.col ) {
					th.classList.add( state.dir === 1 ? 'ht-t-asc' : 'ht-t-desc' );
					th.setAttribute( 'aria-sort', state.dir === 1 ? 'ascending' : 'descending' );
				}
			} );

			var total = list.length, show = list, start = 0;
			if ( per ) {
				var pages = Math.max( 1, Math.ceil( total / per ) );
				if ( state.page > pages ) { state.page = pages; }
				start = ( state.page - 1 ) * per;
				show = list.slice( start, start + per );
			}

			tbody.innerHTML = '';
			if ( ! show.length ) {
				var tr = el( 'tr' );
				var td = el( 'td', 'ht-t-empty', t( 'empty', 'No matching rows.' ) );
				td.colSpan = ncol;
				tr.appendChild( td );
				tbody.appendChild( tr );
			} else {
				show.forEach( function ( r, n ) {
					if ( doIndex && r.cells[ 0 ] ) { r.cells[ 0 ].textContent = start + n + 1; }
					tbody.appendChild( r );
				} );
			}
			if ( state.q ) { highlight( show, norm( state.q ) ); }
			if ( per ) { renderNav( total ); }
		}

		function renderNav( total ) {
			var pages = Math.max( 1, Math.ceil( total / per ) );
			nav.innerHTML = '';
			var prev = el( 'button', '', '‹ ' + t( 'prev', 'Previous' ) );
			prev.type = 'button';
			prev.disabled = state.page <= 1;
			prev.addEventListener( 'click', function () { state.page--; render(); } );
			var info = el( 'span', 'ht-t-pageinfo' );
			var s = total ? ( ( state.page - 1 ) * per + 1 ) : 0;
			var e = Math.min( state.page * per, total );
			info.textContent = s + '–' + e + ' / ' + total;
			var next = el( 'button', '', t( 'next', 'Next' ) + ' ›' );
			next.type = 'button';
			next.disabled = state.page >= pages;
			next.addEventListener( 'click', function () { state.page++; render(); } );
			nav.appendChild( prev );
			nav.appendChild( info );
			nav.appendChild( next );
		}

		render();
	}

	function boot() {
		[].slice.call( document.querySelectorAll( '.ht-table' ) ).forEach( function ( w ) {
			try { init( w ); } catch ( e ) {}
		} );
	}
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
