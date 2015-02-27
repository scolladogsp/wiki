( function ( mw, bs, $, undefined ) {
	$( '.multiselectsortlist' ).sortable( {
		update: function( event, ui ) {
			$( this ).next().children().remove(); //Remove all "option" tags from the hidden "select" element
			$( this ).children().each( function( index, element ) {
				$( this ).parent().next() //The "select" element
				.append( '<option selected="selected" value="' + $(this).attr( 'data-value' ) + '">' + $(this).html() + '</option>' );
				//We have to use .attr( 'data-value' ) instead of .data('value' ) because of some jQuery version issues. Maybe correct this in future versions.
			});
		}
	});
}( mediaWiki, blueSpice, jQuery ) );