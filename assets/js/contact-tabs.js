( function () {
	"use strict";

	var root = document.querySelector( "[data-studio-contact-tabs]" );
	if ( ! root ) {
		return;
	}

	var tabs = root.querySelectorAll( ".studio-contact-tabs__tab" );
	var panels = root.querySelectorAll( ".studio-contact-tabs__panel" );

	tabs.forEach( function ( tab ) {
		tab.addEventListener( "click", function () {
			var idx = tab.getAttribute( "data-tab" );
			tabs.forEach( function ( t ) {
				var on = t === tab;
				t.classList.toggle( "is-active", on );
				t.setAttribute( "aria-selected", on ? "true" : "false" );
			} );
			panels.forEach( function ( p ) {
				var match = p.getAttribute( "data-panel" ) === idx;
				p.classList.toggle( "is-active", match );
				if ( match ) {
					p.removeAttribute( "hidden" );
				} else {
					p.setAttribute( "hidden", "hidden" );
				}
			} );
		} );
	} );
} )();
