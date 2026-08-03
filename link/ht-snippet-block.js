( function ( wp ) {
	if ( ! wp || ! wp.blocks ) { return; }
	var el = wp.element.createElement;
	var SelectControl = wp.components.SelectControl;
	var useBlockProps = ( wp.blockEditor && wp.blockEditor.useBlockProps ) ? wp.blockEditor.useBlockProps : function () { return {}; };
	var D = window.htSnippetData || { list: [], pick: 'Select a snippet', hint: 'Pick a snippet to insert', label: 'Horse Tools snippet' };

	wp.blocks.registerBlockType( 'horse-tools/snippet', {
		title: D.label,
		description: D.hint,
		icon: 'shortcode',
		category: 'widgets',
		attributes: { slug: { type: 'string', default: '' } },
		edit: function ( props ) {
			var opts = [ { label: '— ' + D.pick + ' —', value: '' } ];
			( D.list || [] ).forEach( function ( s ) {
				opts.push( { label: s.title + ' (' + s.slug + ')', value: s.slug } );
			} );
			return el(
				'div', useBlockProps( { style: { border: '1px dashed #c3c4c7', borderRadius: '6px', padding: '10px' } } ),
				el( SelectControl, {
					label: D.label,
					value: props.attributes.slug,
					options: opts,
					onChange: function ( v ) { props.setAttributes( { slug: v } ); }
				} ),
				props.attributes.slug
					? el( 'code', {}, '[ht-snippet name="' + props.attributes.slug + '"]' )
					: el( 'p', { style: { color: '#888', margin: 0 } }, D.hint )
			);
		},
		save: function () { return null; }
	} );
}( window.wp ) );
