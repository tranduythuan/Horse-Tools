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
	function fillGrid( src, rows, cols ) {
		var html = '';
		for ( var r = 0; r < rows; r++ ) {
			html += '<div class="ht-tb-grow">';
			for ( var c = 0; c < cols; c++ ) {
				var v = ( src && src[ r ] && src[ r ][ c ] != null ) ? src[ r ][ c ] : '';
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
		[ '.ht-tb-opts', '.ht-tb-opts2', '.ht-tb-prevwrap' ].forEach( function ( sel ) {
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

		els.title = overlay.querySelector( '.ht-tb-title' );
		els.namerow = overlay.querySelector( '.ht-tb-namerow' );
		els.name = overlay.querySelector( '.ht-tb-name' );
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
		els.theme = overlay.querySelector( '.ht-tb-theme' );
		els.hcolor = overlay.querySelector( '.ht-tb-hcolor' );
		els.caption = overlay.querySelector( '.ht-tb-caption' );
		els.preview = overlay.querySelector( '.ht-tb-preview' );
		els.insertBtn = overlay.querySelector( '.ht-tb-insert' );

		overlay.addEventListener( 'click', function ( e ) {
			var tgt = e.target;
			if ( tgt === overlay || tgt.classList.contains( 'ht-tb-x' ) || tgt.classList.contains( 'ht-tb-cancel' ) ) { closeIt(); return; }
			if ( tgt.classList.contains( 'ht-tb-tab' ) ) { switchTab( tgt.getAttribute( 'data-tab' ) ); return; }
			if ( tgt.classList.contains( 'ht-tb-mkgrid' ) ) { buildGrid(); renderPreview(); return; }
			if ( tgt.classList.contains( 'ht-tb-insert' ) ) { doPrimary(); return; }
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

	// The primary (footer) action — insert a shortcode, insert a saved-table
	// reference, or hand a { name, data, opts } payload back for saving.
	function doPrimary() {
		if ( mode === 'save' ) {
			var data = collect();
			if ( ! data.length || ! data.some( function ( r ) { return r.some( function ( c ) { return String( c ).trim() !== ''; } ); } ) ) {
				showErr(); return;
			}
			if ( typeof onInsert === 'function' ) { onInsert( { id: editId, name: els.name ? els.name.value.trim() : '', data: data, opts: opts() } ); }
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
		if ( els.theme ) { els.theme.value = ''; }
		if ( els.hcolor ) { els.hcolor.value = ''; }
		if ( els.caption ) { els.caption.value = ''; }
		if ( els.paste ) { els.paste.value = ''; }
		if ( els.name ) { els.name.value = ''; }
		uploadData = null;
		if ( ! init ) { els.rows.value = 3; els.cols.value = 3; buildGrid(); switchTab( 'manual' ); return; }
		var o = init.opts || {};
		els.header.checked = o.header === undefined ? true : !! o.header;
		els.striped.checked = o.striped === undefined ? true : !! o.striped;
		els.compact.checked = !! o.compact;
		els.stack.checked = !! o.stack;
		if ( els.theme ) { els.theme.value = o.theme || ''; }
		if ( els.hcolor ) { els.hcolor.value = o.hcolor || ''; }
		if ( els.caption ) { els.caption.value = o.caption || ''; }
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
			+ '.ht-tb-tabs{display:flex;gap:4px;padding:10px 18px 0;flex-wrap:wrap;}'
			+ '.ht-tb-tab{border:1px solid transparent;background:#f0f0f1;padding:8px 14px;border-radius:6px 6px 0 0;cursor:pointer;font-size:13px;}'
			+ '.ht-tb-tab.active{background:#fff;border-color:#e2e4e7;border-bottom-color:#fff;font-weight:600;}'
			+ '.ht-tb-tabsaved{background:#eef6ff;}'
			+ '.ht-tb-body{padding:16px 18px;border-top:1px solid #e2e4e7;margin-top:-1px;}'
			+ '.ht-tb-manualbar{margin-bottom:10px;font-size:13px;}'
			+ '.ht-tb-manualbar input{width:60px;}'
			+ '.ht-tb-grid{max-height:220px;overflow:auto;border:1px solid #eee;border-radius:6px;padding:6px;}'
			+ '.ht-tb-grow{display:flex;gap:4px;margin-bottom:4px;}'
			+ '.ht-tb-grow input{flex:1;min-width:70px;padding:5px 7px;border:1px solid #dcdcde;border-radius:4px;font-size:13px;}'
			+ '.ht-tb-grow input.ht-tb-hcell{background:#f5f7fa;font-weight:600;}'
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
			if ( els.insertBtn ) { els.insertBtn.textContent = mode === 'save' ? t( 'save', 'Save table' ) : t( 'insert', 'Insert table' ); }
			applyInitial( pendingInitial );
			renderPreview();
		}
	};
}() );
