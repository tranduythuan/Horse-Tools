/**
 * Horse Tools — table interactivity (sort / search / pagination).
 *
 * Vanilla JS on purpose: no jQuery, no DataTables (~5 KB vs ~120 KB+). It runs
 * only on tables that ask for it via data attributes emitted by the renderer:
 *   <div class="ht-table" data-ht-sort="1" data-ht-search="1" data-ht-page="10">
 *
 * Vietnamese-friendly: search is diacritic-insensitive ("chuot" matches
 * "Chuột"), and numeric sorting understands VN formats ("1.790.000", "1,5").
 */
( function () {
	var T = window.htTableFx || {};

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

	function init( wrap ) {
		var doSort = wrap.getAttribute( 'data-ht-sort' ) === '1';
		var doSearch = wrap.getAttribute( 'data-ht-search' ) === '1';
		var per = parseInt( wrap.getAttribute( 'data-ht-page' ), 10 ) || 0;
		if ( ! doSort && ! doSearch && ! per ) { return; }
		var table = wrap.querySelector( 'table' );
		var tbody = table && table.querySelector( 'tbody' );
		if ( ! tbody ) { return; }
		var rows = [].slice.call( tbody.querySelectorAll( 'tr' ) );
		var ths = [].slice.call( table.querySelectorAll( 'thead th' ) );
		var ncol = ths.length || ( rows[ 0 ] ? rows[ 0 ].children.length : 1 );
		var state = { q: '', col: -1, dir: 1, page: 1 };
		var inp = null, nav = null;

		if ( doSearch ) {
			var bar = document.createElement( 'div' );
			bar.className = 'ht-t-bar';
			inp = document.createElement( 'input' );
			inp.type = 'search';
			inp.className = 'ht-t-search';
			inp.placeholder = T.search || 'Search the table…';
			inp.setAttribute( 'aria-label', inp.placeholder );
			bar.appendChild( inp );
			wrap.insertBefore( bar, wrap.firstChild );
			inp.addEventListener( 'input', function () { state.q = inp.value; state.page = 1; render(); } );
		}
		if ( per ) {
			nav = document.createElement( 'div' );
			nav.className = 'ht-t-pagenav';
			wrap.appendChild( nav );
		}
		if ( doSort ) {
			ths.forEach( function ( th, i ) {
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

		function cellText( tr, i ) { var c = tr.children[ i ]; return c ? c.textContent : ''; }

		function render() {
			var list = rows;
			if ( state.q ) {
				var q = norm( state.q );
				list = list.filter( function ( tr ) { return norm( tr.textContent ).indexOf( q ) !== -1; } );
			}
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
			var total = list.length, show = list;
			if ( per ) {
				var pages = Math.max( 1, Math.ceil( total / per ) );
				if ( state.page > pages ) { state.page = pages; }
				var s = ( state.page - 1 ) * per;
				show = list.slice( s, s + per );
			}
			tbody.innerHTML = '';
			if ( ! show.length ) {
				var tr = document.createElement( 'tr' );
				var td = document.createElement( 'td' );
				td.colSpan = ncol;
				td.className = 'ht-t-empty';
				td.textContent = T.empty || 'No matching rows.';
				tr.appendChild( td );
				tbody.appendChild( tr );
			} else {
				show.forEach( function ( r ) { tbody.appendChild( r ); } );
			}
			if ( per ) { renderNav( total ); }
		}

		function renderNav( total ) {
			var pages = Math.max( 1, Math.ceil( total / per ) );
			nav.innerHTML = '';
			var prev = document.createElement( 'button' );
			prev.type = 'button';
			prev.textContent = '‹ ' + ( T.prev || 'Previous' );
			prev.disabled = state.page <= 1;
			prev.addEventListener( 'click', function () { state.page--; render(); } );
			var info = document.createElement( 'span' );
			info.className = 'ht-t-pageinfo';
			var s = total ? ( ( state.page - 1 ) * per + 1 ) : 0;
			var e = Math.min( state.page * per, total );
			info.textContent = s + '–' + e + ' / ' + total;
			var next = document.createElement( 'button' );
			next.type = 'button';
			next.textContent = ( T.next || 'Next' ) + ' ›';
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
