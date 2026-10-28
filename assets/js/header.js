/**
 * Studio header — menu toggle (fixed dark; no adaptive scroll color).
 */
( function () {
	"use strict";

	var header  = document.querySelector( ".studio-header, #site-header" );
	var toggle  = document.querySelector( ".studio-header__toggle" );
	var overlay = document.getElementById( "studio-mobile-menu" );
	var closer  = overlay ? overlay.querySelector( ".studio-mobile-menu__close" ) : null;

	if ( header && header.getAttribute( "data-header-theme" ) !== "light" ) {
		header.setAttribute( "data-header-theme", "dark" );
		document.body.setAttribute( "data-header-theme", "dark" );
	}

	if ( ! toggle || ! overlay ) {
		return;
	}

	function openMenu() {
		overlay.classList.add( "is-open" );
		overlay.setAttribute( "aria-hidden", "false" );
		toggle.setAttribute( "aria-expanded", "true" );
		document.body.classList.add( "studio-menu-open" );
		if ( header ) {
			header.classList.add( "studio-header--menu-open" );
		}
	}

	function closeMenu() {
		overlay.classList.remove( "is-open" );
		overlay.setAttribute( "aria-hidden", "true" );
		toggle.setAttribute( "aria-expanded", "false" );
		document.body.classList.remove( "studio-menu-open" );
		if ( header ) {
			header.classList.remove( "studio-header--menu-open" );
		}
	}

	toggle.addEventListener( "click", function () {
		if ( overlay.classList.contains( "is-open" ) ) {
			closeMenu();
		} else {
			openMenu();
		}
	} );

	if ( closer ) {
		closer.addEventListener( "click", closeMenu );
	}

	overlay.querySelectorAll( "a" ).forEach( function ( link ) {
		link.addEventListener( "click", closeMenu );
	} );

	document.addEventListener( "keydown", function ( e ) {
		if ( "Escape" === e.key && overlay.classList.contains( "is-open" ) ) {
			closeMenu();
			toggle.focus();
		}
	} );

	var origin = header ? header.querySelector( ".studio-header__origin" ) : null;
	if ( origin ) {
		var lastY = window.scrollY;
		var desktop = window.matchMedia( "(min-width: 901px)" );

		function syncOrigin() {
			var y = window.scrollY;
			var goingDown = y > lastY && y > 32;
			if ( desktop.matches && goingDown ) {
				header.classList.add( "is-origin-hidden" );
			} else if ( ! desktop.matches || y < lastY || y <= 8 ) {
				header.classList.remove( "is-origin-hidden" );
			}
			lastY = y;
		}

		window.addEventListener( "scroll", syncOrigin, { passive: true } );
		desktop.addEventListener( "change", syncOrigin );
	}
} )();
