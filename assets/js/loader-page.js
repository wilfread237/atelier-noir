( function () {
	"use strict";

	var root = document.getElementById( "an-loader-page" );
	if ( ! root ) {
		return;
	}

	var delayMs = parseInt( root.getAttribute( "data-delay-ms" ) || "7000", 10 );
	var target  = root.getAttribute( "data-redirect" ) || "/home/";
	var start   = Date.now();

	function go() {
		window.location.replace( target );
	}

	function scheduleRedirect() {
		var elapsed = Date.now() - start;
		var wait    = Math.max( 0, delayMs - elapsed );
		window.setTimeout( go, wait );
	}

	if ( document.readyState === "complete" ) {
		scheduleRedirect();
	} else {
		window.addEventListener( "load", scheduleRedirect );
	}
} )();
