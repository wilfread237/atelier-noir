( function () {
	"use strict";

	// ── Onglets ──────────────────────────────────────────────────────────────
	var tabs   = document.querySelectorAll( ".studio-contact-tab" );
	var panels = document.querySelectorAll( ".studio-contact-panel" );

	if ( ! tabs.length ) { return; }

	function activateTab( tabName ) {
		tabs.forEach( function ( t ) {
			var active = t.dataset.tab === tabName;
			t.classList.toggle( "is-active", active );
			t.setAttribute( "aria-selected", active ? "true" : "false" );
		} );
		panels.forEach( function ( p ) {
			p.classList.toggle( "is-active", p.id === "panel-" + tabName );
		} );
		// Mettre à jour l'URL sans recharger la page
		try {
			var url = new URL( window.location.href );
			url.searchParams.set( "tab", tabName );
			window.history.replaceState( null, "", url.toString() );
		} catch ( e ) {}
	}

	tabs.forEach( function ( tab ) {
		tab.addEventListener( "click", function () {
			activateTab( tab.dataset.tab );
		} );
	} );

	// ── Pré-sélection depuis l'URL ─────────────────────────────────────────
	// Si ?service=xxx → onglet Réservation + pré-remplissage du select
	// Si ?tab=informations → onglet Informations
	( function () {
		var params  = new URLSearchParams( window.location.search );
		var service = params.get( "service" );
		var tab     = params.get( "tab" );

		if ( service ) {
			activateTab( "reservation" );
			var select = document.getElementById( "res-service" );
			if ( select ) {
				// Sélectionner l'option dont la valeur correspond au slug
				var opt = select.querySelector( 'option[value="' + CSS.escape( service ) + '"]' );
				if ( opt ) {
					select.value = service;
					// Déclencher un event "change" pour tout listener éventuel
					select.dispatchEvent( new Event( "change" ) );
				}
			}
		} else if ( tab ) {
			// Whitelist : n'accepter que les noms d'onglets connus
			var allowedTabs = { reservation: 1, informations: 1 };
			if ( allowedTabs[ tab ] ) {
				activateTab( tab );
			}
		}
	} )();

	// ── Validation visuelle à la soumission ─────────────────────────────────
	document.querySelectorAll( ".studio-contact-form" ).forEach( function ( form ) {
		form.addEventListener( "submit", function ( e ) {
			var valid  = true;
			var fields = form.querySelectorAll( "input[required], select[required], textarea[required]" );

			fields.forEach( function ( field ) {
				field.style.borderBottomColor = "";
				if ( ! field.value.trim() ) {
					field.style.borderBottomColor = "#b40000";
					valid = false;
				}
			} );

			if ( ! valid ) {
				e.preventDefault();
				var firstInvalid = form.querySelector( "[style*='b40000']" );
				if ( firstInvalid ) { firstInvalid.focus(); }
			}
		} );
	} );

} )();
