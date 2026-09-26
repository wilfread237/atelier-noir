( function () {
	"use strict";

	var el = document.getElementById( "studio-site-loader" );
	if ( ! el ) { return; }

	var minMs   = parseInt( el.getAttribute( "data-min-ms" ) || "1800", 10 );
	var start   = Date.now();

	// ── Découper le nom en mots animés ──────────────────────────────
	var brand = el.querySelector( ".studio-site-loader__brand" );
	if ( brand ) {
		var text  = brand.textContent.trim();
		var words = text.split( /\s+/ );
		brand.textContent = "";
		brand.setAttribute( "aria-label", text );

		words.forEach( function ( word ) {
			var outer = document.createElement( "span" );
			outer.className = "studio-site-loader__word";
			var inner = document.createElement( "span" );
			inner.className = "studio-site-loader__word-inner";
			inner.textContent = word;
			outer.appendChild( inner );
			brand.appendChild( outer );
		} );
	}

	// ── Barre de progression ────────────────────────────────────────
	var bar = document.createElement( "span" );
	bar.className = "studio-site-loader__bar";
	bar.style.setProperty( "--loader-duration", ( minMs / 1000 ) + "s" );
	el.appendChild( bar );

	// ── Masquage du loader ──────────────────────────────────────────
	function hideLoader() {
		el.classList.add( "is-done" );
		document.body.classList.remove( "has-studio-loader" );
		// Supprimer du DOM après la transition (accessibilité)
		el.addEventListener( "transitionend", function () {
			if ( el.parentNode ) { el.parentNode.removeChild( el ); }
		}, { once: true } );
	}

	function maybeHide() {
		var elapsed = Date.now() - start;
		var wait    = Math.max( 0, minMs - elapsed );
		window.setTimeout( hideLoader, wait );
	}

	if ( document.readyState === "complete" ) {
		maybeHide();
	} else {
		window.addEventListener( "load", maybeHide );
	}
} )();
