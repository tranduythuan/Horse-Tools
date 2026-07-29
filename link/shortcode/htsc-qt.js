/* Horse Tools — Classic editor (Text mode) Quicktags button.
   Adds a "Horse Tools" button that inserts a chosen shortcode. */
( function () {
	if ( typeof QTags === 'undefined' || ! window.horsetoolsSC ) { return; }
	var SC = window.horsetoolsSC;

	QTags.addButton( 'horsetools_sc', SC.i18n.title, function () {
		var items = SC.items || [];
		if ( ! items.length ) { return; }
		var lines = items.map( function ( it, i ) { return ( i + 1 ) + '. ' + it.label; } ).join( '\n' );
		var pick = window.prompt( SC.i18n.pick + '\n\n' + lines, '1' );
		if ( null === pick ) { return; }
		var idx = parseInt( pick, 10 ) - 1;
		if ( items[ idx ] ) {
			QTags.insertContent( items[ idx ].insert );
		}
	} );
} )();
