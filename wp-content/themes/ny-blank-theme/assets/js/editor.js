/**
 * Block editor tweaks that need JavaScript.
 *
 * This runs in the editor window, outside the iframed canvas, so it must not assume
 * that document or window is the canvas.
 */
( function ( wp ) {
	// Rich-text formats removed from the toolbar: highlight (text colour), inline image
	// and maths, as they let editors restyle text or need extra front-end support.
	const removedFormats = [ 'core/text-color', 'core/image', 'core/math' ];

	wp.domReady( () => {
		removedFormats.forEach( ( name ) => {
			if ( wp.data.select( 'core/rich-text' ).getFormatType( name ) ) {
				wp.richText.unregisterFormatType( name );
			}
		} );
	} );
} )( window.wp );
