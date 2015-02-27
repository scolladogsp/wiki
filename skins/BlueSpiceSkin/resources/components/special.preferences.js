$( document ).ready( function() {
	if (wgCanonicalSpecialPageName !== "WikiAdmin" || mw.util.getParamValue("mode") !== "Preferences")
		return;
	$( '.bs-prefs .bs-prefs-head' ).each( function( i, v ) {
		var parentEl = $( v ).parent( "fieldset.bs-prefs" );
		if ( parentEl.hasClass( "bs-prefs-viewstate-collapsed" ) )
			$( v ).addClass( "icon-arrow-down9" );
		else
			$( v ).addClass( "icon-arrow-up8" );
		$( v ).on( 'click', function( e ) {
			if ( parentEl.hasClass( "bs-prefs-viewstate-collapsed" ) )
				$( v ).removeClass("icon-arrow-up8").addClass( "icon-arrow-down9" );
			else
				$( v ).removeClass("icon-arrow-down9").addClass( "icon-arrow-up8" );
		} );
	} );
} );