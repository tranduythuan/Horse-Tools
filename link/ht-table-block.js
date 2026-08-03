( function ( wp ) {
	if ( ! wp || ! wp.blocks ) { return; }
	var el = wp.element.createElement;
	var useBlockProps = ( wp.blockEditor && wp.blockEditor.useBlockProps ) ? wp.blockEditor.useBlockProps : function () { return {}; };
	var T = window.htTableI18n || {};

	wp.blocks.registerBlockType( 'horse-tools/table', {
		title: T.blockTitle || 'Horse Tools table',
		description: T.blockEmpty || 'Insert a responsive table.',
		icon: 'editor-table',
		category: 'widgets',
		attributes: { content: { type: 'string', default: '' } },
		edit: function ( props ) {
			function openBuilder() {
				if ( window.htTableBuilder ) {
					window.htTableBuilder.open( function ( sc ) { props.setAttributes( { content: sc } ); } );
				}
			}
			var has = !! props.attributes.content;
			return el(
				'div',
				useBlockProps( { style: { border: '1px dashed #c3c4c7', borderRadius: '6px', padding: '16px', textAlign: 'center' } } ),
				el( 'p', { style: { margin: '0 0 10px', color: '#555' } }, has ? ( T.blockDone || 'Table ready.' ) : ( T.blockEmpty || 'No table yet.' ) ),
				el( 'button', { type: 'button', className: 'button', onClick: openBuilder }, has ? ( T.blockEdit || 'Edit table' ) : ( T.blockCreate || 'Create table' ) )
			);
		},
		save: function () { return null; }
	} );
}( window.wp ) );
