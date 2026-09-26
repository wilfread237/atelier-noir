( function () {
	"use strict";

	// ══════════════════════════════════════════════════════════════════════════
	// Galerie / déclinaisons — flèches de défilement sur les cartes produit
	// ══════════════════════════════════════════════════════════════════════════

	function initCardGallery( card ) {
		var slides  = Array.from( card.querySelectorAll( ".studio-product-card__slide" ) );
		var btnPrev = card.querySelector( ".studio-product-card__arrow--prev" );
		var btnNext = card.querySelector( ".studio-product-card__arrow--next" );

		if ( ! btnPrev && ! btnNext ) {
			return;
		}

		if ( slides.length < 2 ) {
			if ( btnPrev ) {
				btnPrev.style.display = "none";
			}
			if ( btnNext ) {
				btnNext.style.display = "none";
			}
			return;
		}

		var current = 0;

		function goTo( index ) {
			slides[ current ].classList.remove( "is-active" );
			slides[ current ].setAttribute( "aria-hidden", "true" );
			current = ( index + slides.length ) % slides.length;
			slides[ current ].classList.add( "is-active" );
			slides[ current ].setAttribute( "aria-hidden", "false" );
		}

		if ( btnPrev ) {
			btnPrev.addEventListener( "click", function ( e ) {
				e.preventDefault();
				e.stopPropagation();
				goTo( current - 1 );
			} );
		}

		if ( btnNext ) {
			btnNext.addEventListener( "click", function ( e ) {
				e.preventDefault();
				e.stopPropagation();
				goTo( current + 1 );
			} );
		}
	}

	document.querySelectorAll( ".studio-product-card" ).forEach( initCardGallery );

	document.body.addEventListener( "wc_fragments_loaded", function () {
		document.querySelectorAll( ".studio-product-card" ).forEach( initCardGallery );
	} );

	// ══════════════════════════════════════════════════════════════════════════
	// Shop search — live filter (case- and accent-insensitive)
	// ══════════════════════════════════════════════════════════════════════════

	var searchWrap   = document.querySelector( ".studio-shop-search-wrap" );
	var searchToggle = document.querySelector( ".studio-shop-search-toggle" );
	var searchBar    = document.getElementById( "studio-shop-search-bar" );
	var searchInput  = document.getElementById( "studio-shop-search-input" );
	var noResults    = document.getElementById( "studio-shop-no-results" );
	var productsList = document.querySelector( ".woocommerce ul.products" );

	function normalizeSearchTerm( value ) {
		return String( value )
			.toLowerCase()
			.normalize( "NFD" )
			.replace( /[\u0300-\u036f]/g, "" )
			.trim();
	}

	function getProductCards() {
		return Array.from(
			document.querySelectorAll( ".woocommerce ul.products > li.product, .woocommerce ul.products > li.studio-product-card" )
		);
	}

	function setCardVisible( card, visible ) {
		if ( visible ) {
			card.removeAttribute( "hidden" );
			card.classList.remove( "is-filtered-out" );
			card.style.removeProperty( "display" );
		} else {
			card.setAttribute( "hidden", "hidden" );
			card.classList.add( "is-filtered-out" );
			card.style.display = "none";
		}
	}

	function filterProducts( query ) {
		var term    = normalizeSearchTerm( query );
		var visible = 0;
		var cards   = getProductCards();

		cards.forEach( function ( card ) {
			var nameEl = card.querySelector( ".studio-product-card__name, .woocommerce-loop-product__title" );
			var name   = nameEl ? normalizeSearchTerm( nameEl.textContent ) : "";
			var match  = ! term || name.indexOf( term ) !== -1;
			setCardVisible( card, match );
			if ( match ) {
				visible += 1;
			}
		} );

		if ( noResults ) {
			noResults.hidden = ! term || visible > 0;
		}

		if ( productsList ) {
			productsList.classList.toggle( "has-no-visible-products", term.length > 0 && visible === 0 );
		}
	}

	function closeSearch() {
		if ( ! searchBar || ! searchWrap ) {
			return;
		}
		searchBar.classList.remove( "is-open" );
		searchBar.setAttribute( "aria-hidden", "true" );
		searchWrap.classList.remove( "is-search-open" );
		if ( searchToggle ) {
			searchToggle.setAttribute( "aria-expanded", "false" );
		}
		if ( searchInput ) {
			searchInput.value = "";
			filterProducts( "" );
		}
	}

	function openSearch() {
		if ( ! searchBar || ! searchWrap ) {
			return;
		}
		searchBar.classList.add( "is-open" );
		searchBar.setAttribute( "aria-hidden", "false" );
		searchWrap.classList.add( "is-search-open" );
		if ( searchToggle ) {
			searchToggle.setAttribute( "aria-expanded", "true" );
		}
		setTimeout( function () {
			if ( searchInput ) {
				searchInput.focus();
			}
		}, 80 );
	}

	if ( searchToggle && searchBar && searchWrap ) {
		searchToggle.addEventListener( "click", function () {
			if ( searchBar.classList.contains( "is-open" ) ) {
				closeSearch();
				searchToggle.focus();
			} else {
				openSearch();
			}
		} );

		document.addEventListener( "keydown", function ( e ) {
			if ( "Escape" === e.key && searchBar.classList.contains( "is-open" ) ) {
				closeSearch();
				searchToggle.focus();
			}
		} );
	}

	if ( searchInput ) {
		searchInput.addEventListener( "input", function () {
			filterProducts( searchInput.value );
		} );
	}

} )();
