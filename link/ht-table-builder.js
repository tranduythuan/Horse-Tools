( function () {
	var T = ( window.htTableI18n ) || {};
	function t( k, d ) { return T[ k ] || d; }
	var STORE = window.htTableStore || {};

	var overlay = null, onInsert = null, uploadData = null, els = {};
	var mode = 'insert', editId = 0, pendingInitial = null;

	function escHtml( s ) { return String( s == null ? '' : s ).replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' ); }
	function escAttr( s ) { return escHtml( s ).replace( /"/g, '&quot;' ); }

	function parseCsvLine( line ) {
		var out = [], cur = '', q = false, i, ch;
		for ( i = 0; i < line.length; i++ ) {
			ch = line[ i ];
			if ( q ) {
				if ( ch === '"' ) { if ( line[ i + 1 ] === '"' ) { cur += '"'; i++; } else { q = false; } }
				else { cur += ch; }
			} else if ( ch === '"' ) { q = true; }
			else if ( ch === ',' ) { out.push( cur ); cur = ''; }
			else { cur += ch; }
		}
		out.push( cur );
		return out;
	}

	function parseDelimited( text ) {
		text = String( text || '' ).replace( /\r\n/g, '\n' ).replace( /\r/g, '\n' ).replace( /\n+$/, '' );
		if ( ! text ) { return []; }
		var lines = text.split( '\n' );
		var useTab = text.indexOf( '\t' ) !== -1;
		return lines.map( function ( l ) { return useTab ? l.split( '\t' ) : parseCsvLine( l ); } );
	}

	function normalize( rows ) {
		rows = ( rows || [] ).filter( function ( r ) { return r && ! ( r.length === 1 && String( r[ 0 ] ).trim() === '' ); } );
		var max = 0;
		rows.forEach( function ( r ) { if ( r.length > max ) { max = r.length; } } );
		return rows.map( function ( r ) { var c = r.slice(); while ( c.length < max ) { c.push( '' ); } return c; } );
	}

	function collect() {
		var active = overlay.querySelector( '.ht-tb-tab.active' );
		var m = active ? active.getAttribute( 'data-tab' ) : 'manual';
		if ( m === 'paste' ) { return normalize( parseDelimited( els.paste.value ) ); }
		if ( m === 'upload' ) { return normalize( uploadData || [] ); }
		// manual
		var rows = Math.max( 1, parseInt( els.rows.value, 10 ) || 0 );
		var cols = Math.max( 1, parseInt( els.cols.value, 10 ) || 0 );
		var data = [];
		for ( var r = 0; r < rows; r++ ) {
			var row = [];
			for ( var c = 0; c < cols; c++ ) {
				var inp = els.grid.querySelector( 'input[data-r="' + r + '"][data-c="' + c + '"]' );
				row.push( inp ? inp.value : '' );
			}
			data.push( row );
		}
		return data;
	}

	function opts() {
		return {
			header: els.header.checked,
			striped: els.striped.checked,
			compact: els.compact.checked,
			stack: els.stack.checked,
			footer: els.footer ? els.footer.checked : false,
			theme: els.theme ? els.theme.value : '',
			hcolor: els.hcolor ? els.hcolor.value : '',
			caption: els.caption ? els.caption.value.trim() : '',
			sort: els.sort ? els.sort.checked : false,
			search: els.search ? els.search.checked : false,
			paginate: els.paginate ? els.paginate.checked : false,
			pagesize: els.pagesize ? Math.max( 1, Math.min( 100, parseInt( els.pagesize.value, 10 ) || 10 ) ) : 10,
			index: els.index ? els.index.checked : false,
			colfilter: els.colfilter ? els.colfilter.checked : false,
			tools: els.tools ? els.tools.checked : false,
			freeze: els.freeze ? els.freeze.checked : false
		};
	}

	function isNum( v ) { v = String( v == null ? '' : v ).trim(); return v !== '' && /^[+\-]?[\d.,\s]+\s*[%đ$₫]?$/.test( v ); }

	// VN/EU-aware numeric parse ("1.790.000", "1,5"); null when not a number.
	function cellNum( v ) {
		v = String( v == null ? '' : v ).trim();
		if ( ! v || ! /^[+\-]?[\d.,\s]+\s*[%đ$₫]?$/.test( v ) ) { return null; }
		var s = v.replace( /[^\d.,\-]/g, '' );
		var d = s.indexOf( '.' ) !== -1, c = s.indexOf( ',' ) !== -1, p;
		if ( d && c ) { s = s.replace( /\./g, '' ).replace( ',', '.' ); }
		else if ( d ) { p = s.split( '.' ); if ( p.length > 2 || ( p.length === 2 && p[ 1 ].length === 3 ) ) { s = s.replace( /\./g, '' ); } }
		else if ( c ) { p = s.split( ',' ); if ( p.length > 2 || ( p.length === 2 && p[ 1 ].length === 3 ) ) { s = s.replace( /,/g, '' ); } else { s = s.replace( ',', '.' ); } }
		var n = parseFloat( s );
		return isNaN( n ) ? null : n;
	}
	function fmtNum( n ) {
		var neg = n < 0 ? '-' : '';
		n = Math.abs( n );
		var isInt = Math.floor( n ) === n;
		var parts = ( isInt ? String( n ) : n.toFixed( 2 ) ).split( '.' );
		parts[ 0 ] = parts[ 0 ].replace( /\B(?=(\d{3})+(?!\d))/g, '.' );
		return neg + ( parts.length > 1 ? parts[ 0 ] + ',' + parts[ 1 ] : parts[ 0 ] );
	}

	// Safe formula subset: a cell that is exactly =SUM/AVG/MIN/MAX(B2:B10).
	// Regex-matched only; values come from a snapshot of the original data.
	function applyFormulas( data ) {
		var src = data.map( function ( r ) { return r.slice(); } );
		function colIdx( letters ) {
			var n = 0;
			letters.toUpperCase().split( '' ).forEach( function ( ch ) { n = n * 26 + ( ch.charCodeAt( 0 ) - 64 ); } );
			return n - 1;
		}
		return data.map( function ( row, r ) {
			return row.map( function ( cell, c ) {
				var m = /^=\s*(SUM|AVG|MIN|MAX)\s*\(\s*([A-Z]{1,2})(\d{1,4})\s*:\s*([A-Z]{1,2})(\d{1,4})\s*\)\s*$/i.exec( String( cell == null ? '' : cell ) );
				if ( ! m ) { return cell; }
				var fn = m[ 1 ].toUpperCase();
				var c1 = colIdx( m[ 2 ] ), r1 = +m[ 3 ] - 1, c2 = colIdx( m[ 4 ] ), r2 = +m[ 5 ] - 1, t;
				if ( c2 < c1 ) { t = c1; c1 = c2; c2 = t; }
				if ( r2 < r1 ) { t = r1; r1 = r2; r2 = t; }
				var vals = [];
				for ( var ri = r1; ri <= r2; ri++ ) {
					for ( var ci = c1; ci <= c2; ci++ ) {
						if ( src[ ri ] && src[ ri ][ ci ] != null ) {
							var n = cellNum( src[ ri ][ ci ] );
							if ( n !== null ) { vals.push( n ); }
						}
					}
				}
				if ( ! vals.length ) { return fn === 'SUM' ? '0' : ''; }
				var res;
				if ( fn === 'SUM' ) { res = vals.reduce( function ( a, b ) { return a + b; }, 0 ); }
				else if ( fn === 'AVG' ) { res = vals.reduce( function ( a, b ) { return a + b; }, 0 ) / vals.length; }
				else if ( fn === 'MIN' ) { res = Math.min.apply( null, vals ); }
				else { res = Math.max.apply( null, vals ); }
				return fmtNum( res );
			} );
		} );
	}

	// #colspan# merges into the nearest real cell to the LEFT, #rowspan# into
	// the nearest real cell ABOVE (never across the header/body boundary).
	function computeSpans( data, headerOn, footerStart ) {
		var cs = {}, rs = {}, skip = {};
		var bodyStart = headerOn ? 1 : 0;
		if ( footerStart == null ) { footerStart = -1; }
		data.forEach( function ( row, r ) {
			row.forEach( function ( cell, c ) {
				var v = String( cell == null ? '' : cell ).trim();
				if ( v === '#colspan#' ) {
					for ( var cc = c - 1; cc >= 0; cc-- ) {
						var left = String( data[ r ][ cc ] == null ? '' : data[ r ][ cc ] ).trim();
						if ( left !== '#colspan#' && left !== '#rowspan#' ) {
							cs[ r + ':' + cc ] = ( cs[ r + ':' + cc ] || 1 ) + 1;
							skip[ r + ':' + c ] = true;
							break;
						}
					}
				} else if ( v === '#rowspan#' ) {
					var limit = r >= bodyStart ? bodyStart : 0;
					if ( footerStart >= 0 && r >= footerStart ) { limit = footerStart; }
					for ( var rr = r - 1; rr >= limit; rr-- ) {
						var up = String( ( data[ rr ] || [] )[ c ] == null ? '' : data[ rr ][ c ] ).trim();
						if ( up !== '#colspan#' && up !== '#rowspan#' ) {
							rs[ rr + ':' + c ] = ( rs[ rr + ':' + c ] || 1 ) + 1;
							skip[ r + ':' + c ] = true;
							break;
						}
					}
				}
			} );
		} );
		return { cs: cs, rs: rs, skip: skip };
	}

	function tableHtml( data, o ) {
		if ( ! data.length ) { return ''; }
		var footerOn = !! o.footer && data.length >= ( o.header ? 3 : 2 );
		var footerStart = footerOn ? data.length - 1 : -1;
		data = applyFormulas( data );
		var sp = computeSpans( data, !! o.header, footerStart );
		var head = o.header ? data[ 0 ] : null;
		var body = o.header ? data.slice( 1 ) : data;
		var foot = footerOn ? body.pop() : null;
		var ncol = 0;
		data.forEach( function ( r ) { if ( r.length > ncol ) { ncol = r.length; } } );
		// A column that is entirely numbers gets right-aligned automatically.
		var right = [];
		for ( var ci = 0; ci < ncol; ci++ ) {
			var any = false, all = true;
			for ( var ri = 0; ri < body.length; ri++ ) {
				var v = String( body[ ri ][ ci ] == null ? '' : body[ ri ][ ci ] ).trim();
				if ( v === '' || v === '#colspan#' || v === '#rowspan#' ) { continue; }
				any = true;
				if ( ! isNum( v ) ) { all = false; break; }
			}
			right[ ci ] = any && all;
		}
		function cls( i ) { return right[ i ] ? ' class="ht-r"' : ''; }
		function spanAttr( r, c ) {
			var a = '';
			if ( sp.cs[ r + ':' + c ] ) { a += ' colspan="' + sp.cs[ r + ':' + c ] + '"'; }
			if ( sp.rs[ r + ':' + c ] ) { a += ' rowspan="' + sp.rs[ r + ':' + c ] + '"'; }
			return a;
		}
		function cellText( v ) {
			v = String( v == null ? '' : v );
			var tv = v.trim();
			return ( tv === '#colspan#' || tv === '#rowspan#' ) ? '' : v;
		}
		var h = '<table>';
		if ( o.caption ) { h += '<caption>' + escHtml( o.caption ) + '</caption>'; }
		if ( head ) {
			h += '<thead><tr>';
			head.forEach( function ( c, i ) {
				if ( sp.skip[ '0:' + i ] ) { return; }
				h += '<th' + cls( i ) + spanAttr( 0, i ) + '>' + escHtml( cellText( c ) ) + '</th>';
			} );
			h += '</tr></thead>';
		}
		h += '<tbody>';
		body.forEach( function ( row, bi ) {
			var r = o.header ? bi + 1 : bi;
			h += '<tr>';
			row.forEach( function ( c, i ) {
				if ( sp.skip[ r + ':' + i ] ) { return; }
				var lbl = head ? escAttr( head[ i ] || '' ) : '';
				h += '<td data-label="' + lbl + '"' + cls( i ) + spanAttr( r, i ) + '>' + escHtml( cellText( c ) ) + '</td>';
			} );
			h += '</tr>';
		} );
		h += '</tbody>';
		if ( foot ) {
			var fr = data.length - 1;
			h += '<tfoot><tr>';
			foot.forEach( function ( c, i ) {
				if ( sp.skip[ fr + ':' + i ] ) { return; }
				var lbl = head ? escAttr( head[ i ] || '' ) : '';
				h += '<td data-label="' + lbl + '"' + cls( i ) + spanAttr( fr, i ) + '>' + escHtml( cellText( c ) ) + '</td>';
			} );
			h += '</tr></tfoot>';
		}
		return h + '</table>';
	}

	function shortcode( data, o ) {
		var inner = tableHtml( data, o );
		if ( ! inner ) { return ''; }
		var a = [];
		if ( o.stack ) { a.push( 'stack="1"' ); }
		if ( ! o.striped ) { a.push( 'striped="0"' ); }
		if ( o.compact ) { a.push( 'compact="1"' ); }
		if ( o.theme ) { a.push( 'theme="' + o.theme + '"' ); }
		if ( o.hcolor ) { a.push( 'hcolor="' + o.hcolor + '"' ); }
		if ( o.sort ) { a.push( 'sort="1"' ); }
		if ( o.search ) { a.push( 'search="1"' ); }
		if ( o.paginate && o.pagesize ) { a.push( 'page="' + o.pagesize + '"' ); }
		if ( o.index ) { a.push( 'index="1"' ); }
		if ( o.colfilter ) { a.push( 'colfilter="1"' ); }
		if ( o.tools ) { a.push( 'tools="1"' ); }
		if ( o.freeze ) { a.push( 'freeze="1"' ); }
		return '[ht-table' + ( a.length ? ' ' + a.join( ' ' ) : '' ) + ']' + inner + '[/ht-table]';
	}

	function activeTab() {
		var a = overlay.querySelector( '.ht-tb-tab.active' );
		return a ? a.getAttribute( 'data-tab' ) : 'manual';
	}

	function renderPreview() {
		if ( activeTab() === 'saved' ) { return; }
		var data = collect(), o = opts();
		var cls = 'ht-table' + ( o.theme ? ' ht-tt-' + o.theme : '' ) + ( o.hcolor ? ' ht-th-' + o.hcolor : '' ) + ( o.stack ? ' ht-table-stack' : '' ) + ( o.striped ? ' ht-table-striped' : '' ) + ( o.compact ? ' ht-table-compact' : '' );
		var html = tableHtml( data, o );
		els.preview.innerHTML = html ? '<div class="' + cls + '"><div class="ht-table-scroll">' + html + '</div></div>' : '<p style="color:#888;margin:0;">' + escHtml( t( 'empty', 'Nothing to preview yet.' ) ) + '</p>';
	}

	function buildGrid() {
		var rows = Math.max( 1, Math.min( 200, parseInt( els.rows.value, 10 ) || 3 ) );
		var cols = Math.max( 1, Math.min( 40, parseInt( els.cols.value, 10 ) || 3 ) );
		els.rows.value = rows; els.cols.value = cols;
		var existing = null;
		try { existing = collectGridRaw(); } catch ( e ) {}
		fillGrid( existing, rows, cols );
	}

	// Render the manual grid from a 2D source (or blanks), rows×cols in size.
	// Every column gets a control strip (insert/delete/move) and every row a
	// control cell — the spreadsheet-style editing that a fixed grid lacked.
	function fillGrid( src, rows, cols ) {
		var html = '<div class="ht-tb-colctls"><span class="ht-tb-rcsp"></span>';
		for ( var cc = 0; cc < cols; cc++ ) {
			html += '<span class="ht-tb-colctl">'
				+ '<button type="button" class="ht-tb-op" data-op="cml" data-j="' + cc + '" title="' + escAttr( t( 'colLeft', 'Move column left' ) ) + '">‹</button>'
				+ '<button type="button" class="ht-tb-op" data-op="cadd" data-j="' + cc + '" title="' + escAttr( t( 'colAdd', 'Insert column right' ) ) + '">＋</button>'
				+ '<button type="button" class="ht-tb-op" data-op="cdel" data-j="' + cc + '" title="' + escAttr( t( 'colDel', 'Delete column' ) ) + '">✕</button>'
				+ '<button type="button" class="ht-tb-op" data-op="cmr" data-j="' + cc + '" title="' + escAttr( t( 'colRight', 'Move column right' ) ) + '">›</button>'
				+ '</span>';
		}
		html += '</div>';
		for ( var r = 0; r < rows; r++ ) {
			html += '<div class="ht-tb-grow">';
			html += '<span class="ht-tb-rowctl">'
				+ '<button type="button" class="ht-tb-op" data-op="rmu" data-i="' + r + '" title="' + escAttr( t( 'rowUp', 'Move row up' ) ) + '">˄</button>'
				+ '<button type="button" class="ht-tb-op" data-op="radd" data-i="' + r + '" title="' + escAttr( t( 'rowAdd', 'Insert row below' ) ) + '">＋</button>'
				+ '<button type="button" class="ht-tb-op" data-op="rdel" data-i="' + r + '" title="' + escAttr( t( 'rowDel', 'Delete row' ) ) + '">✕</button>'
				+ '<button type="button" class="ht-tb-op" data-op="rmd" data-i="' + r + '" title="' + escAttr( t( 'rowDown', 'Move row down' ) ) + '">˅</button>'
				+ '</span>';
			for ( var c = 0; c < cols; c++ ) {
				var v = ( src && src[ r ] && src[ r ][ c ] != null ) ? src[ r ][ c ] : '';
				html += '<input type="text" data-r="' + r + '" data-c="' + c + '" value="' + escAttr( v ) + '"' + ( r === 0 ? ' class="ht-tb-hcell" placeholder="' + escAttr( t( 'colN', 'Column' ) + ' ' + ( c + 1 ) ) + '"' : '' ) + '>';
			}
			html += '</div>';
		}
		els.grid.innerHTML = html;
	}

	// Current grid as a full rows×cols matrix (missing cells become '').
	function readGrid() {
		var rows = Math.max( 1, parseInt( els.rows.value, 10 ) || 1 );
		var cols = Math.max( 1, parseInt( els.cols.value, 10 ) || 1 );
		var data = [];
		for ( var r = 0; r < rows; r++ ) {
			var row = [];
			for ( var c = 0; c < cols; c++ ) {
				var inp = els.grid.querySelector( 'input[data-r="' + r + '"][data-c="' + c + '"]' );
				row.push( inp ? inp.value : '' );
			}
			data.push( row );
		}
		return { data: data, rows: rows, cols: cols };
	}

	// Structural edits: insert/delete/move rows and columns.
	function gridOp( op, i, j ) {
		var g = readGrid(), data = g.data, rows = g.rows, cols = g.cols, tmp;
		if ( 'radd' === op ) {
			if ( rows >= 200 ) { return; }
			var blank = []; for ( var b = 0; b < cols; b++ ) { blank.push( '' ); }
			data.splice( i + 1, 0, blank );
		} else if ( 'rdel' === op ) {
			if ( rows <= 1 ) { return; }
			data.splice( i, 1 );
		} else if ( 'rmu' === op ) {
			if ( i <= 0 ) { return; }
			tmp = data[ i - 1 ]; data[ i - 1 ] = data[ i ]; data[ i ] = tmp;
		} else if ( 'rmd' === op ) {
			if ( i >= rows - 1 ) { return; }
			tmp = data[ i + 1 ]; data[ i + 1 ] = data[ i ]; data[ i ] = tmp;
		} else if ( 'cadd' === op ) {
			if ( cols >= 40 ) { return; }
			data.forEach( function ( r ) { r.splice( j + 1, 0, '' ); } );
		} else if ( 'cdel' === op ) {
			if ( cols <= 1 ) { return; }
			data.forEach( function ( r ) { r.splice( j, 1 ); } );
		} else if ( 'cml' === op ) {
			if ( j <= 0 ) { return; }
			data.forEach( function ( r ) { var x = r[ j - 1 ]; r[ j - 1 ] = r[ j ]; r[ j ] = x; } );
		} else if ( 'cmr' === op ) {
			if ( j >= cols - 1 ) { return; }
			data.forEach( function ( r ) { var x = r[ j + 1 ]; r[ j + 1 ] = r[ j ]; r[ j ] = x; } );
		} else {
			return;
		}
		els.rows.value = data.length;
		els.cols.value = data[ 0 ].length;
		fillGrid( data, data.length, data[ 0 ].length );
		renderPreview();
	}
	function collectGridRaw() {
		var out = [];
		els.grid.querySelectorAll( 'input' ).forEach( function ( inp ) {
			var r = +inp.getAttribute( 'data-r' ), c = +inp.getAttribute( 'data-c' );
			( out[ r ] = out[ r ] || [] )[ c ] = inp.value;
		} );
		return out;
	}

	// Load an existing table's data into the manual grid (used when editing).
	function loadData( data ) {
		data = data || [];
		var rows = Math.max( 1, Math.min( 200, data.length ) );
		var cols = 1;
		data.forEach( function ( r ) { if ( r.length > cols ) { cols = r.length; } } );
		cols = Math.min( 40, cols );
		els.rows.value = rows;
		els.cols.value = cols;
		fillGrid( data, rows, cols );
	}

	function handleFile( file ) {
		if ( ! file ) { return; }
		els.upmsg.textContent = t( 'reading', 'Reading…' );
		var name = ( file.name || '' ).toLowerCase();
		var isText = /\.(csv|tsv|txt)$/.test( name );
		if ( isText ) {
			var fr = new FileReader();
			fr.onload = function () { uploadData = normalize( parseDelimited( fr.result ) ); els.upmsg.textContent = uploadData.length + ' ' + t( 'rows', 'rows' ); renderPreview(); };
			fr.onerror = function () { els.upmsg.textContent = t( 'readfail', 'Could not read the file.' ); };
			fr.readAsText( file );
			return;
		}
		// Excel: needs SheetJS.
		loadXLSX( function ( ok ) {
			if ( ! ok || ! window.XLSX ) { els.upmsg.textContent = t( 'noxlsx', 'Could not load the Excel reader — save the file as CSV and try again.' ); return; }
			var fr2 = new FileReader();
			fr2.onload = function () {
				try {
					var wb = window.XLSX.read( new Uint8Array( fr2.result ), { type: 'array' } );
					var ws = wb.Sheets[ wb.SheetNames[ 0 ] ];
					var rows = window.XLSX.utils.sheet_to_json( ws, { header: 1, blankrows: false, defval: '' } );
					uploadData = normalize( rows.map( function ( r ) { return r.map( function ( c ) { return c == null ? '' : String( c ); } ); } ) );
					els.upmsg.textContent = uploadData.length + ' ' + t( 'rows', 'rows' );
					renderPreview();
				} catch ( e ) { els.upmsg.textContent = t( 'readfail', 'Could not read the file.' ); }
			};
			fr2.onerror = function () { els.upmsg.textContent = t( 'readfail', 'Could not read the file.' ); };
			fr2.readAsArrayBuffer( file );
		} );
	}

	function loadXLSX( cb ) {
		if ( window.XLSX ) { cb( true ); return; }
		if ( ! window.htXlsxUrl ) { cb( false ); return; }
		var s = document.createElement( 'script' );
		s.src = window.htXlsxUrl;
		s.onload = function () { cb( true ); };
		s.onerror = function () { cb( false ); };
		document.head.appendChild( s );
	}

	function switchTab( name ) {
		overlay.querySelectorAll( '.ht-tb-tab' ).forEach( function ( b ) { b.classList.toggle( 'active', b.getAttribute( 'data-tab' ) === name ); } );
		overlay.querySelectorAll( '.ht-tb-pane' ).forEach( function ( p ) { p.style.display = ( p.getAttribute( 'data-pane' ) === name ) ? 'block' : 'none'; } );
		// On the "saved tables" picker there is nothing to style or preview.
		var isSaved = name === 'saved';
		[ '.ht-tb-opts', '.ht-tb-opts2', '.ht-tb-opts3', '.ht-tb-prevwrap' ].forEach( function ( sel ) {
			var el = overlay.querySelector( sel );
			if ( el ) { el.style.display = isSaved ? 'none' : ''; }
		} );
		renderPreview();
	}

	function savedOptionsHtml() {
		var list = ( STORE.tables || [] );
		if ( ! list.length ) { return '<option value="">' + escHtml( t( 'savedNone', 'No saved tables yet.' ) ) + '</option>'; }
		return list.map( function ( x ) { return '<option value="' + escAttr( x.id ) + '">' + escHtml( x.name + '  ·  [ht-table id=' + x.id + ']' ) + '</option>'; } ).join( '' );
	}

	function build() {
		injectStyle();
		var hasSaved = ( STORE.tables || [] ).length > 0;
		overlay = document.createElement( 'div' );
		overlay.className = 'ht-tb-overlay';
		overlay.innerHTML =
			'<div class="ht-tb-modal" role="dialog" aria-modal="true">' +
				'<div class="ht-tb-head"><span class="ht-tb-title">' + escHtml( t( 'title', 'Insert a table' ) ) + '</span><button type="button" class="ht-tb-x" aria-label="Close">&times;</button></div>' +
				'<div class="ht-tb-namerow" style="display:none;"><label>' + escHtml( t( 'nameL', 'Table name' ) ) + ' <input type="text" class="ht-tb-name" placeholder="' + escAttr( t( 'namePh', 'e.g. Price list' ) ) + '"></label></div>' +
				'<div class="ht-tb-sheetrow" style="display:none;">' +
					'<label>' + escHtml( t( 'sheetL', 'Google Sheet' ) ) + ' <input type="url" class="ht-tb-sheet" placeholder="' + escAttr( t( 'sheetPh', 'Paste a Google Sheets link (shared: anyone with the link)' ) ) + '"></label>' +
					'<button type="button" class="button ht-tb-sheetpull">' + escHtml( t( 'sheetPull', 'Pull data' ) ) + '</button>' +
					'<select class="ht-tb-sync"><option value="off">' + escHtml( t( 'syncOff', 'No auto-refresh' ) ) + '</option><option value="hourly">' + escHtml( t( 'syncHourly', 'Refresh hourly' ) ) + '</option><option value="daily">' + escHtml( t( 'syncDaily', 'Refresh daily' ) ) + '</option></select>' +
					'<span class="ht-tb-sheetmsg"></span>' +
					'<span class="ht-tb-sheethint">' + escHtml( t( 'sheetHint', 'The sheet must be shared as “Anyone with the link can view”. With auto-refresh on, the table updates itself from the sheet.' ) ) + '</span>' +
				'</div>' +
				'<div class="ht-tb-cssrow" style="display:none;">' +
					'<label>' + escHtml( t( 'cssL', 'Custom CSS' ) ) + ' <textarea class="ht-tb-css" rows="2" placeholder="' + escAttr( t( 'cssPh', '.ht-table-5 td { font-size: 14px; }' ) ) + '"></textarea></label>' +
				'</div>' +
				'<div class="ht-tb-tabs">' +
					'<button type="button" class="ht-tb-tab active" data-tab="manual">' + escHtml( t( 'manual', 'Type it in' ) ) + '</button>' +
					'<button type="button" class="ht-tb-tab" data-tab="paste">' + escHtml( t( 'paste', 'Paste from Excel' ) ) + '</button>' +
					'<button type="button" class="ht-tb-tab" data-tab="upload">' + escHtml( t( 'upload', 'Upload a file' ) ) + '</button>' +
					( hasSaved ? '<button type="button" class="ht-tb-tab ht-tb-tabsaved" data-tab="saved">' + escHtml( t( 'savedTab', 'Saved tables' ) ) + '</button>' : '' ) +
				'</div>' +
				'<div class="ht-tb-body">' +
					'<div class="ht-tb-pane" data-pane="manual">' +
						'<div class="ht-tb-manualbar">' + escHtml( t( 'rowsL', 'Rows' ) ) + ' <input type="number" min="1" max="200" value="3" class="ht-tb-rows"> ' + escHtml( t( 'colsL', 'Columns' ) ) + ' <input type="number" min="1" max="40" value="3" class="ht-tb-cols"> <button type="button" class="button ht-tb-mkgrid">' + escHtml( t( 'mkgrid', 'Build grid' ) ) + '</button></div>' +
						'<div class="ht-tb-grid"></div>' +
						'<p class="ht-tb-mergehint">' + escHtml( t( 'mergeHint', 'Merge cells: type #colspan# to merge into the cell on the left, #rowspan# to merge into the cell above. Formulas: =SUM(B2:B10), also AVG / MIN / MAX.' ) ) + '</p>' +
					'</div>' +
					'<div class="ht-tb-pane" data-pane="paste" style="display:none;">' +
						'<p class="ht-tb-hint">' + escHtml( t( 'pasteHint', 'Copy cells in Excel / Google Sheets (or paste CSV) and paste here:' ) ) + '</p>' +
						'<textarea class="ht-tb-paste" rows="8" placeholder="Name\tAge\nAn\t20"></textarea>' +
					'</div>' +
					'<div class="ht-tb-pane" data-pane="upload" style="display:none;">' +
						'<p class="ht-tb-hint">' + escHtml( t( 'uploadHint', 'Choose a .xlsx, .xls or .csv file:' ) ) + '</p>' +
						'<input type="file" class="ht-tb-file" accept=".csv,.tsv,.txt,.xlsx,.xls">' +
						'<span class="ht-tb-upmsg"></span>' +
					'</div>' +
					( hasSaved ? '<div class="ht-tb-pane" data-pane="saved" style="display:none;">' +
						'<p class="ht-tb-hint">' + escHtml( t( 'savedHint', 'Insert a table you saved earlier:' ) ) + '</p>' +
						'<select class="ht-tb-saved">' + savedOptionsHtml() + '</select>' +
					'</div>' : '' ) +
					'<div class="ht-tb-opts">' +
						'<label><input type="checkbox" class="ht-tb-header" checked> ' + escHtml( t( 'optHeader', 'First row is a header' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-striped" checked> ' + escHtml( t( 'optStriped', 'Striped rows' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-compact"> ' + escHtml( t( 'optCompact', 'Compact' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-stack"> ' + escHtml( t( 'optStack', 'Stack into cards on mobile' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-footer"> ' + escHtml( t( 'optFooter', 'Last row is a total row (pinned to the bottom)' ) ) + '</label>' +
					'</div>' +
					'<div class="ht-tb-opts2">' +
						'<label>' + escHtml( t( 'themeL', 'Style' ) ) + ' <select class="ht-tb-theme"><option value="">' + escHtml( t( 'themeDefault', 'Default' ) ) + '</option><option value="bordered">' + escHtml( t( 'themeBordered', 'Bordered' ) ) + '</option><option value="minimal">' + escHtml( t( 'themeMinimal', 'Minimal' ) ) + '</option><option value="lines">' + escHtml( t( 'themeLines', 'Lines only' ) ) + '</option><option value="card">' + escHtml( t( 'themeCard', 'Card (shadow)' ) ) + '</option><option value="dark">' + escHtml( t( 'themeDark', 'Dark background' ) ) + '</option><option value="soft">' + escHtml( t( 'themeSoft', 'Soft pastel' ) ) + '</option></select></label>' +
						'<label>' + escHtml( t( 'hcolorL', 'Header colour' ) ) + ' <select class="ht-tb-hcolor"><option value="">' + escHtml( t( 'cGrey', 'Grey' ) ) + '</option><option value="blue">' + escHtml( t( 'cBlue', 'Blue' ) ) + '</option><option value="green">' + escHtml( t( 'cGreen', 'Green' ) ) + '</option><option value="orange">' + escHtml( t( 'cOrange', 'Orange' ) ) + '</option><option value="purple">' + escHtml( t( 'cPurple', 'Purple' ) ) + '</option><option value="red">' + escHtml( t( 'cRed', 'Red' ) ) + '</option><option value="pink">' + escHtml( t( 'cPink', 'Pink' ) ) + '</option><option value="teal">' + escHtml( t( 'cTeal', 'Teal' ) ) + '</option><option value="indigo">' + escHtml( t( 'cIndigo', 'Indigo' ) ) + '</option><option value="dark">' + escHtml( t( 'cDark', 'Dark' ) ) + '</option><option value="gradblue">' + escHtml( t( 'cGradBlue', 'Gradient blue-violet' ) ) + '</option><option value="gradsunset">' + escHtml( t( 'cGradSunset', 'Gradient sunset' ) ) + '</option><option value="gradocean">' + escHtml( t( 'cGradOcean', 'Gradient ocean' ) ) + '</option></select></label>' +
						'<label>' + escHtml( t( 'captionL', 'Caption' ) ) + ' <input type="text" class="ht-tb-caption" placeholder="' + escAttr( t( 'captionPh', 'optional title above the table' ) ) + '"></label>' +
					'</div>' +
					'<div class="ht-tb-opts3">' +
						'<label><input type="checkbox" class="ht-tb-sort"> ' + escHtml( t( 'optSort', 'Sortable columns' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-search"> ' + escHtml( t( 'optSearch', 'Search box' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-paginate"> ' + escHtml( t( 'optPage', 'Pagination' ) ) + '</label>' +
						'<label>' + escHtml( t( 'optPer', 'Rows/page' ) ) + ' <input type="number" class="ht-tb-pagesize" min="1" max="100" value="10"></label>' +
						'<label><input type="checkbox" class="ht-tb-index"> ' + escHtml( t( 'optIndex', 'Row-number column' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-colfilter"> ' + escHtml( t( 'optColfil', 'Filter per column' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-tools"> ' + escHtml( t( 'optTools', 'Copy / CSV / Print buttons' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-freeze"> ' + escHtml( t( 'optFreeze', 'Freeze first column' ) ) + '</label>' +
						'<span class="ht-tb-fxnote">' + escHtml( t( 'fxNote', 'Sorting, search and pagination appear on the published page.' ) ) + '</span>' +
					'</div>' +
					'<div class="ht-tb-prevwrap"><div class="ht-tb-prevlabel">' + escHtml( t( 'preview', 'Preview' ) ) + '</div><div class="ht-tb-preview"></div></div>' +
				'</div>' +
				'<div class="ht-tb-foot"><button type="button" class="button ht-tb-cancel">' + escHtml( t( 'cancel', 'Cancel' ) ) + '</button><button type="button" class="button button-primary ht-tb-insert">' + escHtml( t( 'insert', 'Insert table' ) ) + '</button></div>' +
			'</div>';
		document.body.appendChild( overlay );

		els.title = overlay.querySelector( '.ht-tb-title' );
		els.namerow = overlay.querySelector( '.ht-tb-namerow' );
		els.name = overlay.querySelector( '.ht-tb-name' );
		els.sheetrow = overlay.querySelector( '.ht-tb-sheetrow' );
		els.sheet = overlay.querySelector( '.ht-tb-sheet' );
		els.sync = overlay.querySelector( '.ht-tb-sync' );
		els.sheetmsg = overlay.querySelector( '.ht-tb-sheetmsg' );
		els.cssrow = overlay.querySelector( '.ht-tb-cssrow' );
		els.css = overlay.querySelector( '.ht-tb-css' );
		els.rows = overlay.querySelector( '.ht-tb-rows' );
		els.cols = overlay.querySelector( '.ht-tb-cols' );
		els.grid = overlay.querySelector( '.ht-tb-grid' );
		els.paste = overlay.querySelector( '.ht-tb-paste' );
		els.upmsg = overlay.querySelector( '.ht-tb-upmsg' );
		els.saved = overlay.querySelector( '.ht-tb-saved' );
		els.header = overlay.querySelector( '.ht-tb-header' );
		els.striped = overlay.querySelector( '.ht-tb-striped' );
		els.compact = overlay.querySelector( '.ht-tb-compact' );
		els.stack = overlay.querySelector( '.ht-tb-stack' );
		els.footer = overlay.querySelector( '.ht-tb-footer' );
		els.theme = overlay.querySelector( '.ht-tb-theme' );
		els.hcolor = overlay.querySelector( '.ht-tb-hcolor' );
		els.caption = overlay.querySelector( '.ht-tb-caption' );
		els.sort = overlay.querySelector( '.ht-tb-sort' );
		els.search = overlay.querySelector( '.ht-tb-search' );
		els.paginate = overlay.querySelector( '.ht-tb-paginate' );
		els.pagesize = overlay.querySelector( '.ht-tb-pagesize' );
		els.index = overlay.querySelector( '.ht-tb-index' );
		els.colfilter = overlay.querySelector( '.ht-tb-colfilter' );
		els.tools = overlay.querySelector( '.ht-tb-tools' );
		els.freeze = overlay.querySelector( '.ht-tb-freeze' );
		els.preview = overlay.querySelector( '.ht-tb-preview' );
		els.insertBtn = overlay.querySelector( '.ht-tb-insert' );

		overlay.addEventListener( 'click', function ( e ) {
			var tgt = e.target;
			if ( tgt === overlay || tgt.classList.contains( 'ht-tb-x' ) || tgt.classList.contains( 'ht-tb-cancel' ) ) { closeIt(); return; }
			if ( tgt.classList.contains( 'ht-tb-tab' ) ) { switchTab( tgt.getAttribute( 'data-tab' ) ); return; }
			if ( tgt.classList.contains( 'ht-tb-mkgrid' ) ) { buildGrid(); renderPreview(); return; }
			if ( tgt.classList.contains( 'ht-tb-op' ) ) {
				gridOp( tgt.getAttribute( 'data-op' ), parseInt( tgt.getAttribute( 'data-i' ), 10 ) || 0, parseInt( tgt.getAttribute( 'data-j' ), 10 ) || 0 );
				return;
			}
			if ( tgt.classList.contains( 'ht-tb-insert' ) ) { doPrimary(); return; }
		} );
		// Enter in a grid cell moves to the cell below (adding a row on the last
		// one) — the spreadsheet muscle-memory flow for typing a column of data.
		overlay.addEventListener( 'keydown', function ( e ) {
			var tgt = e.target;
			if ( 'Enter' !== e.key || ! tgt.matches || ! tgt.matches( '.ht-tb-grid input[data-r]' ) ) { return; }
			e.preventDefault();
			var r = parseInt( tgt.getAttribute( 'data-r' ), 10 ), c = parseInt( tgt.getAttribute( 'data-c' ), 10 );
			var next = els.grid.querySelector( 'input[data-r="' + ( r + 1 ) + '"][data-c="' + c + '"]' );
			if ( ! next ) {
				gridOp( 'radd', r, 0 );
				next = els.grid.querySelector( 'input[data-r="' + ( r + 1 ) + '"][data-c="' + c + '"]' );
			}
			if ( next ) { next.focus(); }
		} );
		overlay.addEventListener( 'input', function ( e ) {
			if ( e.target.closest( '.ht-tb-grid, .ht-tb-paste, .ht-tb-opts, .ht-tb-opts2' ) ) { renderPreview(); }
		} );
		overlay.addEventListener( 'change', function ( e ) {
			if ( e.target.closest( '.ht-tb-opts, .ht-tb-opts2' ) ) { renderPreview(); }
		} );
		overlay.querySelector( '.ht-tb-file' ).addEventListener( 'change', function ( e ) { handleFile( e.target.files && e.target.files[ 0 ] ); } );
		overlay.querySelector( '.ht-tb-sheetpull' ).addEventListener( 'click', pullSheet );

		buildGrid();
	}

	// Fetch a public Google Sheet (server-side, via admin-ajax) into the grid.
	function pullSheet() {
		var S = window.htTableStore || {};
		var url = els.sheet ? els.sheet.value.trim() : '';
		if ( ! url || ! S.ajaxurl || ! S.nonce ) { return; }
		els.sheetmsg.textContent = t( 'reading', 'Reading…' );
		els.sheetmsg.style.color = '';
		var body = 'action=horsetools_tbl_sheet_fetch&nonce=' + encodeURIComponent( S.nonce ) + '&url=' + encodeURIComponent( url );
		fetch( S.ajaxurl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body } )
			.then( function ( r ) { return r.json(); } )
			.then( function ( res ) {
				if ( res && res.success && res.data && res.data.data ) {
					loadData( res.data.data );
					switchTab( 'manual' );
					els.sheetmsg.textContent = res.data.data.length + ' ' + t( 'sheetRows', 'rows loaded' );
					els.sheetmsg.style.color = '#1d9e75';
					renderPreview();
				} else {
					els.sheetmsg.textContent = ( res && res.data && res.data.msg ) ? res.data.msg : t( 'readfail', 'Could not read the file.' );
					els.sheetmsg.style.color = '#b32d2e';
				}
			} )
			.catch( function () {
				els.sheetmsg.textContent = t( 'readfail', 'Could not read the file.' );
				els.sheetmsg.style.color = '#b32d2e';
			} );
	}

	// The primary (footer) action — insert a shortcode, insert a saved-table
	// reference, or hand a { name, data, opts } payload back for saving.
	function doPrimary() {
		if ( mode === 'save' ) {
			var data = collect();
			if ( ! data.length || ! data.some( function ( r ) { return r.some( function ( c ) { return String( c ).trim() !== ''; } ); } ) ) {
				showErr(); return;
			}
			if ( typeof onInsert === 'function' ) {
				onInsert( {
					id: editId,
					name: els.name ? els.name.value.trim() : '',
					data: data,
					opts: opts(),
					sheet: els.sheet ? els.sheet.value.trim() : '',
					sync: els.sync ? els.sync.value : 'off',
					css: els.css ? els.css.value : ''
				} );
			}
			closeIt();
			return;
		}
		if ( activeTab() === 'saved' ) {
			var id = els.saved ? els.saved.value : '';
			if ( ! id ) { return; }
			if ( typeof onInsert === 'function' ) { onInsert( '[ht-table id="' + id + '"]' ); }
			closeIt();
			return;
		}
		var sc = shortcode( collect(), opts() );
		if ( ! sc ) { showErr(); return; }
		if ( typeof onInsert === 'function' ) { onInsert( sc ); }
		closeIt();
	}

	function showErr() {
		if ( els.preview ) { els.preview.innerHTML = '<p style="color:#b32d2e;margin:0;">' + escHtml( t( 'emptyErr', 'Add some data first.' ) ) + '</p>'; }
	}

	function applyInitial( init ) {
		// Reset to defaults first (the modal is reused between opens).
		els.header.checked = true; els.striped.checked = true;
		els.compact.checked = false; els.stack.checked = false;
		if ( els.footer ) { els.footer.checked = false; }
		if ( els.theme ) { els.theme.value = ''; }
		if ( els.hcolor ) { els.hcolor.value = ''; }
		if ( els.caption ) { els.caption.value = ''; }
		if ( els.paste ) { els.paste.value = ''; }
		if ( els.name ) { els.name.value = ''; }
		if ( els.sort ) { els.sort.checked = false; }
		if ( els.search ) { els.search.checked = false; }
		if ( els.paginate ) { els.paginate.checked = false; }
		if ( els.pagesize ) { els.pagesize.value = 10; }
		[ 'index', 'colfilter', 'tools', 'freeze' ].forEach( function ( k ) {
			if ( els[ k ] ) { els[ k ].checked = false; }
		} );
		if ( els.sheet ) { els.sheet.value = ''; }
		if ( els.sync ) { els.sync.value = 'off'; }
		if ( els.sheetmsg ) { els.sheetmsg.textContent = ''; }
		if ( els.css ) { els.css.value = ''; }
		uploadData = null;
		if ( ! init ) { els.rows.value = 3; els.cols.value = 3; buildGrid(); switchTab( 'manual' ); return; }
		var o = init.opts || {};
		els.header.checked = o.header === undefined ? true : !! o.header;
		els.striped.checked = o.striped === undefined ? true : !! o.striped;
		els.compact.checked = !! o.compact;
		els.stack.checked = !! o.stack;
		if ( els.footer ) { els.footer.checked = !! o.footer; }
		if ( els.theme ) { els.theme.value = o.theme || ''; }
		if ( els.hcolor ) { els.hcolor.value = o.hcolor || ''; }
		if ( els.caption ) { els.caption.value = o.caption || ''; }
		if ( els.sort ) { els.sort.checked = !! o.sort; }
		if ( els.search ) { els.search.checked = !! o.search; }
		if ( els.paginate ) { els.paginate.checked = !! o.paginate; }
		if ( els.pagesize ) { els.pagesize.value = o.pagesize || 10; }
		[ 'index', 'colfilter', 'tools', 'freeze' ].forEach( function ( k ) {
			if ( els[ k ] ) { els[ k ].checked = !! o[ k ]; }
		} );
		if ( els.sheet && init.sheet != null ) { els.sheet.value = init.sheet; }
		if ( els.sync && init.sync != null ) { els.sync.value = init.sync || 'off'; }
		if ( els.css && init.css != null ) { els.css.value = init.css; }
		if ( els.name && init.name != null ) { els.name.value = init.name; }
		loadData( init.data || [] );
		switchTab( 'manual' );
	}

	function closeIt() { if ( overlay ) { overlay.style.display = 'none'; } }

	function injectStyle() {
		if ( document.getElementById( 'ht-tb-style' ) ) { return; }
		var css = '.ht-tb-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:160000;display:flex;align-items:flex-start;justify-content:center;padding:40px 12px;overflow:auto;}'
			+ '.ht-tb-modal{background:#fff;border-radius:10px;width:760px;max-width:100%;box-shadow:0 10px 40px rgba(0,0,0,.3);}'
			+ '.ht-tb-head{display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid #e2e4e7;font-weight:600;font-size:16px;}'
			+ '.ht-tb-x{border:none;background:none;font-size:24px;line-height:1;cursor:pointer;color:#666;}'
			+ '.ht-tb-namerow{padding:12px 18px 0;font-size:13px;}'
			+ '.ht-tb-namerow input{min-width:280px;padding:5px 8px;border:1px solid #dcdcde;border-radius:4px;margin-left:5px;}'
			+ '.ht-tb-sheetrow{display:flex;flex-wrap:wrap;gap:8px;align-items:center;padding:10px 18px 0;font-size:13px;}'
			+ '.ht-tb-sheetrow label{display:flex;align-items:center;gap:5px;flex:1;min-width:260px;}'
			+ '.ht-tb-sheet{flex:1;min-width:220px;padding:5px 8px;border:1px solid #dcdcde;border-radius:4px;}'
			+ '.ht-tb-sheetmsg{font-size:12px;}'
			+ '.ht-tb-sheethint{flex-basis:100%;font-size:12px;color:#7a8590;}'
			+ '.ht-tb-cssrow{padding:8px 18px 0;font-size:13px;}'
			+ '.ht-tb-cssrow label{display:flex;align-items:flex-start;gap:6px;}'
			+ '.ht-tb-css{flex:1;font-family:monospace;font-size:12px;padding:5px 8px;border:1px solid #dcdcde;border-radius:4px;}'
			+ '.ht-tb-mergehint{margin:6px 0 0;font-size:12px;color:#7a8590;}'
			+ '.ht-tb-tabs{display:flex;gap:4px;padding:10px 18px 0;flex-wrap:wrap;}'
			+ '.ht-tb-tab{border:1px solid transparent;background:#f0f0f1;padding:8px 14px;border-radius:6px 6px 0 0;cursor:pointer;font-size:13px;}'
			+ '.ht-tb-tab.active{background:#fff;border-color:#e2e4e7;border-bottom-color:#fff;font-weight:600;}'
			+ '.ht-tb-tabsaved{background:#eef6ff;}'
			+ '.ht-tb-body{padding:16px 18px;border-top:1px solid #e2e4e7;margin-top:-1px;}'
			+ '.ht-tb-manualbar{margin-bottom:10px;font-size:13px;}'
			+ '.ht-tb-manualbar input{width:60px;}'
			+ '.ht-tb-grid{max-height:220px;overflow:auto;border:1px solid #eee;border-radius:6px;padding:6px;}'
			+ '.ht-tb-grow{display:flex;gap:4px;margin-bottom:4px;align-items:center;}'
			+ '.ht-tb-grow input{flex:1;min-width:70px;padding:5px 7px;border:1px solid #dcdcde;border-radius:4px;font-size:13px;}'
			+ '.ht-tb-grow input.ht-tb-hcell{background:#f5f7fa;font-weight:600;}'
			+ '.ht-tb-colctls{display:flex;gap:4px;margin-bottom:4px;}'
			+ '.ht-tb-rcsp,.ht-tb-rowctl{flex:0 0 96px;display:flex;gap:2px;justify-content:center;}'
			+ '.ht-tb-colctl{flex:1;min-width:70px;display:flex;gap:2px;justify-content:center;}'
			+ '.ht-tb-op{border:1px solid #dcdcde;background:#fff;border-radius:4px;cursor:pointer;font-size:11px;line-height:1;padding:3px 5px;color:#667;}'
			+ '.ht-tb-op:hover{border-color:#2271b1;color:#2271b1;}'
			+ '.ht-tb-paste{width:100%;font-family:monospace;font-size:13px;}'
			+ '.ht-tb-saved{min-width:320px;max-width:100%;padding:6px 8px;font-size:13px;}'
			+ '.ht-tb-hint{font-size:13px;color:#555;margin:0 0 8px;}'
			+ '.ht-tb-upmsg{margin-left:8px;font-size:13px;color:#1d9e75;}'
			+ '.ht-tb-opts{display:flex;flex-wrap:wrap;gap:14px;margin:14px 0;font-size:13px;}'
			+ '.ht-tb-prevlabel{font-size:12px;color:#777;text-transform:uppercase;letter-spacing:.03em;margin-bottom:6px;}'
			+ '.ht-tb-preview{border:1px solid #eee;border-radius:8px;padding:12px;max-height:260px;overflow:auto;background:#fff;}'
			+ '.ht-tb-foot{display:flex;justify-content:flex-end;gap:8px;padding:14px 18px;border-top:1px solid #e2e4e7;}'
			+ '.ht-tb-opts2{display:flex;flex-wrap:wrap;gap:16px;align-items:center;margin:0 0 14px;font-size:13px;}'
			+ '.ht-tb-opts2 select,.ht-tb-opts2 input{margin-left:5px;}'
			+ '.ht-tb-opts2 .ht-tb-caption{padding:5px 8px;border:1px solid #dcdcde;border-radius:4px;min-width:170px;}'
			+ '.ht-tb-opts3{display:flex;flex-wrap:wrap;gap:14px;align-items:center;margin:0 0 14px;font-size:13px;padding:10px 12px;background:#f6f8fb;border-radius:8px;}'
			+ '.ht-tb-opts3 .ht-tb-pagesize{width:62px;margin-left:5px;}'
			+ '.ht-tb-fxnote{flex-basis:100%;font-size:12px;color:#7a8590;}'
			+ '.ht-tb-preview .ht-table{margin:0;}';
		var st = document.createElement( 'style' );
		st.id = 'ht-tb-style';
		st.textContent = css;
		document.head.appendChild( st );
	}

	// open( cb )                       → insert mode (editor): cb receives a shortcode string.
	// open( cb, { mode:'save', ... } ) → save mode (manager): cb receives { id, name, data, opts }.
	//   config.initial = { name, data, opts } pre-fills the builder (for editing).
	window.htTableBuilder = {
		open: function ( cb, config ) {
			config = config || {};
			onInsert = cb;
			mode = config.mode === 'save' ? 'save' : 'insert';
			editId = parseInt( config.id, 10 ) || 0;
			pendingInitial = config.initial || null;
			uploadData = null;
			if ( ! overlay ) { build(); } else { overlay.style.display = 'flex'; }
			// Title, name row and the primary button reflect the mode.
			if ( els.title ) { els.title.textContent = mode === 'save' ? t( 'titleSave', 'Save a table' ) : t( 'title', 'Insert a table' ); }
			if ( els.namerow ) { els.namerow.style.display = mode === 'save' ? 'block' : 'none'; }
			if ( els.sheetrow ) { els.sheetrow.style.display = mode === 'save' ? 'flex' : 'none'; }
			if ( els.cssrow ) { els.cssrow.style.display = mode === 'save' ? 'block' : 'none'; }
			if ( els.insertBtn ) { els.insertBtn.textContent = mode === 'save' ? t( 'save', 'Save table' ) : t( 'insert', 'Insert table' ); }
			applyInitial( pendingInitial );
			renderPreview();
		}
	};
}() );
