( function ( $ ) {
	"use strict";

	function syncJson( $wrap ) {
		var $input = $wrap.closest( ".customize-control" ).find( ".studio-services-json" );
		var rows   = [];
		$wrap.find( ".studio-services-row" ).each( function () {
			var $row = $( this );
			var title = $row.find( ".studio-svc-title" ).val();
			if ( ! title ) {
				return;
			}
			rows.push( {
				id:     $row.find( ".studio-svc-id" ).val() || title.toLowerCase().replace( /\s+/g, "-" ),
				title:  title,
				text:   $row.find( ".studio-svc-text" ).val(),
				video:  $row.find( ".studio-svc-video" ).val(),
				image:  $row.find( ".studio-svc-image" ).val(),
				poster: $row.find( ".studio-svc-image" ).val(),
			} );
		} );
		$input.val( JSON.stringify( rows ) ).trigger( "change" );
	}

	$( document ).on( "input change", ".studio-services-row input, .studio-services-row textarea", function () {
		syncJson( $( this ).closest( ".studio-services-repeater" ) );
	} );

	$( document ).on( "click", ".studio-services-add", function ( e ) {
		e.preventDefault();
		var $control = $( this ).closest( ".customize-control" );
		var $wrap    = $control.find( ".studio-services-repeater" );
		var template = $control.find( ".studio-services-row-template" ).html();
		if ( ! $wrap.length || ! template ) {
			return;
		}
		$wrap.append( template.replace( /__INDEX__/g, String( Date.now() ) ) );
		syncJson( $wrap );
	} );

	$( document ).on( "click", ".studio-services-remove", function ( e ) {
		e.preventDefault();
		var $wrap = $( this ).closest( ".studio-services-repeater" );
		$( this ).closest( ".studio-services-row" ).remove();
		syncJson( $wrap );
	} );
}( jQuery ) );
