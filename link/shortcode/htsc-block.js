/* Horse Tools — Gutenberg block: pick a shortcode / snippet and insert it.
   No build step: uses the wp.* globals directly. */
( function ( wp ) {
	if ( ! wp || ! wp.blocks || ! wp.element || ! window.horsetoolsSC ) { return; }

	var el = wp.element.createElement;
	var SC = window.horsetoolsSC;
	var SelectControl = wp.components.SelectControl;
	var TextareaControl = wp.components.TextareaControl;
	var useBlockProps = wp.blockEditor && wp.blockEditor.useBlockProps ? wp.blockEditor.useBlockProps : null;

	var options = [ { label: SC.i18n.choose, value: '' } ].concat(
		( SC.items || [] ).map( function ( it ) { return { label: it.label, value: it.insert }; } )
	);

	wp.blocks.registerBlockType( 'horse-tools/shortcode', {
		apiVersion: 2,
		title: SC.i18n.title,
		description: SC.i18n.button,
		icon: 'shortcode',
		category: 'widgets',
		keywords: [ 'shortcode', 'horse', 'ht' ],
		attributes: { code: { type: 'string', default: '' } },

		edit: function ( props ) {
			var code = props.attributes.code || '';
			var blockProps = useBlockProps ? useBlockProps() : {};
			return el(
				'div',
				blockProps,
				el( SelectControl, {
					label: SC.i18n.title,
					value: '',
					options: options,
					onChange: function ( v ) {
						if ( v ) { props.setAttributes( { code: code ? code + '\n' + v : v } ); }
					}
				} ),
				el( TextareaControl, {
					label: SC.i18n.shortcode,
					value: code,
					rows: 3,
					onChange: function ( v ) { props.setAttributes( { code: v } ); }
				} )
			);
		},

		// Save the raw shortcode text so WordPress processes it on the front end.
		save: function ( props ) {
			return el( wp.element.RawHTML, {}, props.attributes.code || '' );
		}
	} );
} )( window.wp );
