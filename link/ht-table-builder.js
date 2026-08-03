( function () {
	var T = ( window.htTableI18n ) || {};
	function t( k, d ) { return T[ k ] || d; }

	var overlay = null, onInsert = null, uploadData = null, els = {};

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
		var mode = active ? active.getAttribute( 'data-tab' ) : 'manual';
		if ( mode === 'paste' ) { return normalize( parseDelimited( els.paste.value ) ); }
		if ( mode === 'upload' ) { return normalize( uploadData || [] ); }
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
			theme: els.theme ? els.theme.value : '',
			hcolor: els.hcolor ? els.hcolor.value : '',
			caption: els.caption ? els.caption.value.trim() : ''
		};
	}

	function isNum( v ) { v = String( v == null ? '' : v ).trim(); return v !== '' && /^[+\-]?[\d.,\s]+\s*[%đ$₫]?$/.test( v ); }

	function tableHtml( data, o ) {
		if ( ! data.length ) { return ''; }
		var head = o.header ? data[ 0 ] : null;
		var body = o.header ? data.slice( 1 ) : data;
		var ncol = 0;
		data.forEach( function ( r ) { if ( r.length > ncol ) { ncol = r.length; } } );
		// A column that is entirely numbers gets right-aligned automatically.
		var right = [];
		for ( var ci = 0; ci < ncol; ci++ ) {
			var any = false, all = true;
			for ( var ri = 0; ri < body.length; ri++ ) {
				var v = body[ ri ][ ci ];
				if ( String( v == null ? '' : v ).trim() === '' ) { continue; }
				any = true;
				if ( ! isNum( v ) ) { all = false; break; }
			}
			right[ ci ] = any && all;
		}
		function cls( i ) { return right[ i ] ? ' class="ht-r"' : ''; }
		var h = '<table>';
		if ( o.caption ) { h += '<caption>' + escHtml( o.caption ) + '</caption>'; }
		if ( head ) {
			h += '<thead><tr>';
			head.forEach( function ( c, i ) { h += '<th' + cls( i ) + '>' + escHtml( c ) + '</th>'; } );
			h += '</tr></thead>';
		}
		h += '<tbody>';
		body.forEach( function ( row ) {
			h += '<tr>';
			row.forEach( function ( c, i ) {
				var lbl = head ? escAttr( head[ i ] || '' ) : '';
				h += '<td data-label="' + lbl + '"' + cls( i ) + '>' + escHtml( c ) + '</td>';
			} );
			h += '</tr>';
		} );
		return h + '</tbody></table>';
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
		return '[ht-table' + ( a.length ? ' ' + a.join( ' ' ) : '' ) + ']' + inner + '[/ht-table]';
	}

	function renderPreview() {
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
		var html = '';
		for ( var r = 0; r < rows; r++ ) {
			html += '<div class="ht-tb-grow">';
			for ( var c = 0; c < cols; c++ ) {
				var v = ( existing && existing[ r ] && existing[ r ][ c ] != null ) ? existing[ r ][ c ] : '';
				html += '<input type="text" data-r="' + r + '" data-c="' + c + '" value="' + escAttr( v ) + '"' + ( r === 0 ? ' class="ht-tb-hcell" placeholder="' + escAttr( t( 'colN', 'Column' ) + ' ' + ( c + 1 ) ) + '"' : '' ) + '>';
			}
			html += '</div>';
		}
		els.grid.innerHTML = html;
	}
	function collectGridRaw() {
		var out = [];
		els.grid.querySelectorAll( 'input' ).forEach( function ( inp ) {
			var r = +inp.getAttribute( 'data-r' ), c = +inp.getAttribute( 'data-c' );
			( out[ r ] = out[ r ] || [] )[ c ] = inp.value;
		} );
		return out;
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

	function build() {
		injectStyle();
		overlay = document.createElement( 'div' );
		overlay.className = 'ht-tb-overlay';
		overlay.innerHTML =
			'<div class="ht-tb-modal" role="dialog" aria-modal="true">' +
				'<div class="ht-tb-head"><span>' + escHtml( t( 'title', 'Insert a table' ) ) + '</span><button type="button" class="ht-tb-x" aria-label="Close">&times;</button></div>' +
				'<div class="ht-tb-tabs">' +
					'<button type="button" class="ht-tb-tab active" data-tab="manual">' + escHtml( t( 'manual', 'Type it in' ) ) + '</button>' +
					'<button type="button" class="ht-tb-tab" data-tab="paste">' + escHtml( t( 'paste', 'Paste from Excel' ) ) + '</button>' +
					'<button type="button" class="ht-tb-tab" data-tab="upload">' + escHtml( t( 'upload', 'Upload a file' ) ) + '</button>' +
				'</div>' +
				'<div class="ht-tb-body">' +
					'<div class="ht-tb-pane" data-pane="manual">' +
						'<div class="ht-tb-manualbar">' + escHtml( t( 'rowsL', 'Rows' ) ) + ' <input type="number" min="1" max="200" value="3" class="ht-tb-rows"> ' + escHtml( t( 'colsL', 'Columns' ) ) + ' <input type="number" min="1" max="40" value="3" class="ht-tb-cols"> <button type="button" class="button ht-tb-mkgrid">' + escHtml( t( 'mkgrid', 'Build grid' ) ) + '</button></div>' +
						'<div class="ht-tb-grid"></div>' +
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
					'<div class="ht-tb-opts">' +
						'<label><input type="checkbox" class="ht-tb-header" checked> ' + escHtml( t( 'optHeader', 'First row is a header' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-striped" checked> ' + escHtml( t( 'optStriped', 'Striped rows' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-compact"> ' + escHtml( t( 'optCompact', 'Compact' ) ) + '</label>' +
						'<label><input type="checkbox" class="ht-tb-stack"> ' + escHtml( t( 'optStack', 'Stack into cards on mobile' ) ) + '</label>' +
					'</div>' +
					'<div class="ht-tb-opts2">' +
						'<label>' + escHtml( t( 'themeL', 'Style' ) ) + ' <select class="ht-tb-theme"><option value="">' + escHtml( t( 'themeDefault', 'Default' ) ) + '</option><option value="bordered">' + escHtml( t( 'themeBordered', 'Bordered' ) ) + '</option><option value="minimal">' + escHtml( t( 'themeMinimal', 'Minimal' ) ) + '</option><option value="lines">' + escHtml( t( 'themeLines', 'Lines only' ) ) + '</option></select></label>' +
						'<label>' + escHtml( t( 'hcolorL', 'Header colour' ) ) + ' <select class="ht-tb-hcolor"><option value="">' + escHtml( t( 'cGrey', 'Grey' ) ) + '</option><option value="blue">' + escHtml( t( 'cBlue', 'Blue' ) ) + '</option><option value="green">' + escHtml( t( 'cGreen', 'Green' ) ) + '</option><option value="orange">' + escHtml( t( 'cOrange', 'Orange' ) ) + '</option><option value="purple">' + escHtml( t( 'cPurple', 'Purple' ) ) + '</option><option value="dark">' + escHtml( t( 'cDark', 'Dark' ) ) + '</option></select></label>' +
						'<label>' + escHtml( t( 'captionL', 'Caption' ) ) + ' <input type="text" class="ht-tb-caption" placeholder="' + escAttr( t( 'captionPh', 'optional title above the table' ) ) + '"></label>' +
					'</div>' +
					'<div class="ht-tb-prevwrap"><div class="ht-tb-prevlabel">' + escHtml( t( 'preview', 'Preview' ) ) + '</div><div class="ht-tb-preview"></div></div>' +
				'</div>' +
				'<div class="ht-tb-foot"><button type="button" class="button ht-tb-cancel">' + escHtml( t( 'cancel', 'Cancel' ) ) + '</button><button type="button" class="button button-primary ht-tb-insert">' + escHtml( t( 'insert', 'Insert table' ) ) + '</button></div>' +
			'</div>';
		document.body.appendChild( overlay );

		els.rows = overlay.querySelector( '.ht-tb-rows' );
		els.cols = overlay.querySelector( '.ht-tb-cols' );
		els.grid = overlay.querySelector( '.ht-tb-grid' );
		els.paste = overlay.querySelector( '.ht-tb-paste' );
		els.upmsg = overlay.querySelector( '.ht-tb-upmsg' );
		els.header = overlay.querySelector( '.ht-tb-header' );
		els.striped = overlay.querySelector( '.ht-tb-striped' );
		els.compact = overlay.querySelector( '.ht-tb-compact' );
		els.stack = overlay.querySelector( '.ht-tb-stack' );
		els.theme = overlay.querySelector( '.ht-tb-theme' );
		els.hcolor = overlay.querySelector( '.ht-tb-hcolor' );
		els.caption = overlay.querySelector( '.ht-tb-caption' );
		els.preview = overlay.querySelector( '.ht-tb-preview' );

		overlay.addEventListener( 'click', function ( e ) {
			var tgt = e.target;
			if ( tgt === overlay || tgt.classList.contains( 'ht-tb-x' ) || tgt.classList.contains( 'ht-tb-cancel' ) ) { closeIt(); return; }
			if ( tgt.classList.contains( 'ht-tb-tab' ) ) {
				overlay.querySelectorAll( '.ht-tb-tab' ).forEach( function ( b ) { b.classList.remove( 'active' ); } );
				tgt.classList.add( 'active' );
				overlay.querySelectorAll( '.ht-tb-pane' ).forEach( function ( p ) { p.style.display = ( p.getAttribute( 'data-pane' ) === tgt.getAttribute( 'data-tab' ) ) ? 'block' : 'none'; } );
				renderPreview();
				return;
			}
			if ( tgt.classList.contains( 'ht-tb-mkgrid' ) ) { buildGrid(); renderPreview(); return; }
			if ( tgt.classList.contains( 'ht-tb-insert' ) ) {
				var sc = shortcode( collect(), opts() );
				if ( ! sc ) { els.preview.innerHTML = '<p style="color:#b32d2e;margin:0;">' + escHtml( t( 'emptyErr', 'Add some data first.' ) ) + '</p>'; return; }
				if ( typeof onInsert === 'function' ) { onInsert( sc ); }
				closeIt();
				return;
			}
		} );
		overlay.addEventListener( 'input', function ( e ) {
			if ( e.target.closest( '.ht-tb-grid, .ht-tb-paste, .ht-tb-opts, .ht-tb-opts2' ) ) { renderPreview(); }
		} );
		overlay.addEventListener( 'change', function ( e ) {
			if ( e.target.closest( '.ht-tb-opts, .ht-tb-opts2' ) ) { renderPreview(); }
		} );
		overlay.querySelector( '.ht-tb-file' ).addEventListener( 'change', function ( e ) { handleFile( e.target.files && e.target.files[ 0 ] ); } );

		buildGrid();
	}

	function closeIt() { if ( overlay ) { overlay.style.display = 'none'; } }

	function injectStyle() {
		if ( document.getElementById( 'ht-tb-style' ) ) { return; }
		var css = '.ht-tb-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:160000;display:flex;align-items:flex-start;justify-content:center;padding:40px 12px;overflow:auto;}'
			+ '.ht-tb-modal{background:#fff;border-radius:10px;width:760px;max-width:100%;box-shadow:0 10px 40px rgba(0,0,0,.3);}'
			+ '.ht-tb-head{display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid #e2e4e7;font-weight:600;font-size:16px;}'
			+ '.ht-tb-x{border:none;background:none;font-size:24px;line-height:1;cursor:pointer;color:#666;}'
			+ '.ht-tb-tabs{display:flex;gap:4px;padding:10px 18px 0;}'
			+ '.ht-tb-tab{border:1px solid transparent;background:#f0f0f1;padding:8px 14px;border-radius:6px 6px 0 0;cursor:pointer;font-size:13px;}'
			+ '.ht-tb-tab.active{background:#fff;border-color:#e2e4e7;border-bottom-color:#fff;font-weight:600;}'
			+ '.ht-tb-body{padding:16px 18px;border-top:1px solid #e2e4e7;margin-top:-1px;}'
			+ '.ht-tb-manualbar{margin-bottom:10px;font-size:13px;}'
			+ '.ht-tb-manualbar input{width:60px;}'
			+ '.ht-tb-grid{max-height:220px;overflow:auto;border:1px solid #eee;border-radius:6px;padding:6px;}'
			+ '.ht-tb-grow{display:flex;gap:4px;margin-bottom:4px;}'
			+ '.ht-tb-grow input{flex:1;min-width:70px;padding:5px 7px;border:1px solid #dcdcde;border-radius:4px;font-size:13px;}'
			+ '.ht-tb-grow input.ht-tb-hcell{background:#f5f7fa;font-weight:600;}'
			+ '.ht-tb-paste{width:100%;font-family:monospace;font-size:13px;}'
			+ '.ht-tb-hint{font-size:13px;color:#555;margin:0 0 8px;}'
			+ '.ht-tb-upmsg{margin-left:8px;font-size:13px;color:#1d9e75;}'
			+ '.ht-tb-opts{display:flex;flex-wrap:wrap;gap:14px;margin:14px 0;font-size:13px;}'
			+ '.ht-tb-prevlabel{font-size:12px;color:#777;text-transform:uppercase;letter-spacing:.03em;margin-bottom:6px;}'
			+ '.ht-tb-preview{border:1px solid #eee;border-radius:8px;padding:12px;max-height:260px;overflow:auto;background:#fff;}'
			+ '.ht-tb-foot{display:flex;justify-content:flex-end;gap:8px;padding:14px 18px;border-top:1px solid #e2e4e7;}'
			+ '.ht-tb-opts2{display:flex;flex-wrap:wrap;gap:16px;align-items:center;margin:0 0 14px;font-size:13px;}'
			+ '.ht-tb-opts2 select,.ht-tb-opts2 input{margin-left:5px;}'
			+ '.ht-tb-opts2 .ht-tb-caption{padding:5px 8px;border:1px solid #dcdcde;border-radius:4px;min-width:170px;}'
			+ '.ht-tb-preview .ht-table{margin:0;}';
		var st = document.createElement( 'style' );
		st.id = 'ht-tb-style';
		st.textContent = css;
		document.head.appendChild( st );
	}

	window.htTableBuilder = {
		open: function ( cb ) {
			onInsert = cb;
			uploadData = null;
			if ( ! overlay ) { build(); } else { overlay.style.display = 'flex'; }
			renderPreview();
		}
	};
}() );
