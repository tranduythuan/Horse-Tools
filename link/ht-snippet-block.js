( function ( wp ) {
	if ( ! wp || ! wp.blocks ) { return; }
	var el = wp.element.createElement;
	var useState = wp.element.useState;
	var useEffect = wp.element.useEffect;
	var ComboboxControl = wp.components.ComboboxControl;
	var SelectControl = wp.components.SelectControl;
	var useBlockProps = ( wp.blockEditor && wp.blockEditor.useBlockProps ) ? wp.blockEditor.useBlockProps : function () { return {}; };
	var D = window.htSnippetData || { pick: 'Select a snippet', hint: 'Pick a snippet to insert', label: 'Horse Tools snippet' };
	var T = D.i18n || {};

	// The editor is never handed the site's snippets up front — it asks for the
	// ones matching what the author typed. A site with a thousand snippets loads
	// exactly as fast as one with three, and the menu stays a menu.
	var seq = 0;
	function search( term, done ) {
		var mine = ++seq;
		var body = 'action=horsetools_snip_pick&nonce=' + encodeURIComponent( D.nonce || '' ) +
			'&s=' + encodeURIComponent( term || '' );
		window.fetch( D.ajax, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body
		} )
			.then( function ( r ) { return r.json(); } )
			.then( function ( j ) {
				if ( mine !== seq ) { return; }
				done( ( j && j.success && j.data && j.data.items ) ? j.data : null );
			} )
			.catch( function () { if ( mine === seq ) { done( null ); } } );
	}

	function Picker( props ) {
		var slug = props.attributes.slug;
		var optState = useState( [] );
		var opts = optState[ 0 ], setOpts = optState[ 1 ];
		var noteState = useState( '' );
		var note = noteState[ 0 ], setNote = noteState[ 1 ];

		function apply( d ) {
			if ( ! d ) { setNote( T.fail || '' ); return; }
			var list = d.items.map( function ( s ) {
				return { label: s.title + ' (' + s.slug + ')', value: s.slug };
			} );
			// Keep whatever is already chosen selectable even when it is not in
			// the current results, or the control blanks the author's choice.
			var here = d.items.some( function ( s ) { return s.slug === slug; } );
			if ( slug && ! here ) {
				list.unshift( { label: slug, value: slug } );
			}
			setOpts( list );
			setNote(
				d.more
					? ( T.more || '' ).replace( '%1$d', d.items.length ).replace( '%2$d', d.total )
					: ( d.items.length ? '' : ( T.none || '' ) )
			);
		}

		useEffect( function () { search( '', apply ); }, [] );

		var control = ComboboxControl
			? el( ComboboxControl, {
				label: D.label,
				value: slug,
				options: opts,
				allowReset: true,
				placeholder: T.search,
				onFilterValueChange: function ( v ) { search( v, apply ); },
				onChange: function ( v ) { props.setAttributes( { slug: v || '' } ); }
			} )
			: el( SelectControl, {
				label: D.label,
				value: slug,
				options: [ { label: '— ' + D.pick + ' —', value: '' } ].concat( opts ),
				onChange: function ( v ) { props.setAttributes( { slug: v } ); }
			} );

		return el(
			'div', useBlockProps( { style: { border: '1px dashed #c3c4c7', borderRadius: '6px', padding: '10px' } } ),
			control,
			note ? el( 'p', { style: { color: '#646970', fontSize: '12px', margin: '4px 0 0' } }, note ) : null,
			slug
				? el( 'code', {}, '[ht-snippet name="' + slug + '"]' )
				: el( 'p', { style: { color: '#888', margin: 0 } }, D.hint )
		);
	}

	wp.blocks.registerBlockType( 'horse-tools/snippet', {
		title: D.label,
		description: D.hint,
		icon: 'shortcode',
		category: 'widgets',
		attributes: { slug: { type: 'string', default: '' } },
		edit: Picker,
		save: function () { return null; }
	} );
}( window.wp ) );
