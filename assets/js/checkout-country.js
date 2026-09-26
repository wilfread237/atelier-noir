( function () {
	"use strict";

	var config = window.studioCheckoutCountry;
	if ( ! config || ! config.countries ) {
		return;
	}

	var countries = config.countries;
	var defaultCode = config.default || "";

	var list = Object.keys( countries )
		.map( function ( code ) {
			return { code: code, label: countries[ code ] };
		} )
		.sort( function ( a, b ) {
			return a.label.localeCompare( b.label, undefined, { sensitivity: "base" } );
		} );

	function setSelectValue( select, code ) {
		var setter = Object.getOwnPropertyDescriptor( window.HTMLSelectElement.prototype, "value" );
		if ( setter && setter.set ) {
			setter.set.call( select, code );
		} else {
			select.value = code;
		}
		select.dispatchEvent( new Event( "input", { bubbles: true } ) );
		select.dispatchEvent( new Event( "change", { bubbles: true } ) );
	}

	function initSelect( select ) {
		if ( select.dataset.studioCountryInit || select.multiple ) {
			return;
		}

		var parent = select.closest( ".wc-block-components-country-input, .form-row, .woocommerce-input-wrapper" );
		if ( parent && parent.querySelector( ".studio-country-autocomplete" ) ) {
			return;
		}

		select.dataset.studioCountryInit = "1";

		var wrapper = document.createElement( "div" );
		wrapper.className = "studio-country-autocomplete";

		var input = document.createElement( "input" );
		input.type = "text";
		input.className = "studio-country-autocomplete__input input-text";
		input.autocomplete = "country-name";
		input.setAttribute( "role", "combobox" );
		input.setAttribute( "aria-expanded", "false" );
		input.setAttribute( "aria-autocomplete", "list" );
		input.setAttribute( "aria-label", config.label || "Country / Region" );

		var dropdown = document.createElement( "ul" );
		dropdown.className = "studio-country-autocomplete__list";
		dropdown.hidden = true;
		dropdown.setAttribute( "role", "listbox" );

		select.classList.add( "studio-country-autocomplete__native" );
		select.setAttribute( "aria-hidden", "true" );
		select.tabIndex = -1;

		select.parentNode.insertBefore( wrapper, select );
		wrapper.appendChild( input );
		wrapper.appendChild( dropdown );
		wrapper.appendChild( select );

		function applyCountry( code ) {
			if ( ! code || ! countries[ code ] ) {
				return;
			}
			setSelectValue( select, code );
			input.value = countries[ code ];
			dropdown.hidden = true;
			input.setAttribute( "aria-expanded", "false" );
		}

		function renderOptions( filter ) {
			dropdown.innerHTML = "";
			var term = String( filter || "" ).toLowerCase().trim();
			var matches = list.filter( function ( item ) {
				if ( ! term ) {
					return true;
				}
				return item.label.toLowerCase().indexOf( term ) !== -1
					|| item.code.toLowerCase().indexOf( term ) !== -1;
			} ).slice( 0, 14 );

			matches.forEach( function ( item ) {
				var option = document.createElement( "li" );
				option.className = "studio-country-autocomplete__option";
				option.textContent = item.label;
				option.setAttribute( "role", "option" );
				option.addEventListener( "mousedown", function ( e ) {
					e.preventDefault();
					applyCountry( item.code );
				} );
				dropdown.appendChild( option );
			} );

			dropdown.hidden = matches.length === 0;
			input.setAttribute( "aria-expanded", matches.length > 0 ? "true" : "false" );
		}

		var initial = select.value || defaultCode;
		if ( initial ) {
			applyCountry( initial );
		}

		input.addEventListener( "input", function () {
			renderOptions( input.value );
		} );

		input.addEventListener( "focus", function () {
			renderOptions( input.value );
		} );

		input.addEventListener( "blur", function () {
			window.setTimeout( function () {
				dropdown.hidden = true;
				input.setAttribute( "aria-expanded", "false" );
				if ( select.value && countries[ select.value ] ) {
					input.value = countries[ select.value ];
				}
			}, 160 );
		} );

		input.addEventListener( "keydown", function ( e ) {
			if ( "Escape" === e.key ) {
				dropdown.hidden = true;
				input.setAttribute( "aria-expanded", "false" );
			}
		} );
	}

	function findSelects() {
		return document.querySelectorAll(
			"#billing_country, select#billing-country, select[name=\"billing_country\"], .wc-block-components-address-form select[name=\"country\"]"
		);
	}

	function scan() {
		findSelects().forEach( initSelect );
	}

	if ( document.readyState === "loading" ) {
		document.addEventListener( "DOMContentLoaded", scan );
	} else {
		scan();
	}

	var observer = new MutationObserver( function () {
		scan();
	} );
	observer.observe( document.body, { childList: true, subtree: true } );
} )();
