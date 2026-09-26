( function () {
	"use strict";

	var overlay = document.getElementById( "an-legal-overlay" );
	if ( ! overlay ) {
		return;
	}

	var closeBtn = overlay.querySelector( ".an-legal-overlay__close" );
	var tabs     = overlay.querySelectorAll( ".an-legal-overlay__tab" );
	var panes    = overlay.querySelectorAll( ".an-legal-overlay__pane" );

	function open( tab ) {
		overlay.hidden = false;
		overlay.setAttribute( "aria-hidden", "false" );
		document.body.classList.add( "an-legal-open" );
		if ( tab ) {
			activateTab( tab );
		}
	}

	function close() {
		overlay.hidden = true;
		overlay.setAttribute( "aria-hidden", "true" );
		document.body.classList.remove( "an-legal-open" );
	}

	function activateTab( name ) {
		tabs.forEach( function ( t ) {
			var on = t.getAttribute( "data-tab" ) === name;
			t.classList.toggle( "is-active", on );
			t.setAttribute( "aria-selected", on ? "true" : "false" );
		} );
		panes.forEach( function ( p ) {
			var on = p.getAttribute( "data-pane" ) === name;
			p.classList.toggle( "is-active", on );
			p.hidden = ! on;
		} );
	}

	document.querySelectorAll( ".an-legal-open" ).forEach( function ( btn ) {
		btn.addEventListener( "click", function ( e ) {
			e.preventDefault();
			open( btn.getAttribute( "data-legal-tab" ) || "privacy" );
		} );
	} );

	tabs.forEach( function ( tab ) {
		tab.addEventListener( "click", function ( e ) {
			e.preventDefault();
			activateTab( tab.getAttribute( "data-tab" ) );
		} );
	} );

	if ( closeBtn ) {
		closeBtn.addEventListener( "click", close );
	}
} )();
