( function () {
	'use strict';

	var form    = document.getElementById( 'studio-footer-nl-form' );
	var input   = document.getElementById( 'studio-footer-nl-email' );
	var consent = document.getElementById( 'studio-footer-nl-consent' );
	var submit  = document.getElementById( 'studio-footer-nl-submit' );
	var success = form ? form.querySelector( '.studio-footer__nl-success' ) : null;
	var error   = form ? form.querySelector( '.studio-footer__nl-error' ) : null;

	if ( ! form || ! input || ! submit || ! consent ) {
		return;
	}

	var ajaxurl = ( typeof atelierNoirFooterNL !== 'undefined' && atelierNoirFooterNL.ajaxurl ) ? atelierNoirFooterNL.ajaxurl : '/wp-admin/admin-ajax.php';

	function isValidEmail( value ) {
		return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( value );
	}

	function toggleSubmit() {
		var show = isValidEmail( input.value.trim() ) && consent.checked;
		if ( show ) {
			submit.hidden = false;
			requestAnimationFrame( function () {
				submit.classList.add( 'is-visible' );
			} );
		} else {
			submit.classList.remove( 'is-visible' );
			submit.hidden = true;
		}
	}

	input.addEventListener( 'input', toggleSubmit );
	input.addEventListener( 'blur', toggleSubmit );
	consent.addEventListener( 'change', toggleSubmit );

	form.addEventListener( 'submit', function ( e ) {
		e.preventDefault();
		if ( ! isValidEmail( input.value.trim() ) || ! consent.checked ) {
			return;
		}

		submit.disabled = true;
		if ( error ) {
			error.hidden = true;
		}

		var data = new FormData( form );
		data.append( 'action', 'atelier_noir_newsletter_subscribe' );

		fetch( ajaxurl, {
			method: 'POST',
			body:   data,
			credentials: 'same-origin',
		} )
			.then( function ( res ) {
				return res.json();
			} )
			.then( function ( json ) {
				if ( json && json.success ) {
					if ( success ) {
						success.hidden = false;
					}
					form.classList.add( 'is-submitted' );
					input.value = '';
					consent.checked = false;
					submit.hidden = true;
					submit.classList.remove( 'is-visible' );
				} else if ( error ) {
					error.hidden = false;
				}
			} )
			.catch( function () {
				if ( error ) {
					error.hidden = false;
				}
			} )
			.finally( function () {
				submit.disabled = false;
			} );
	} );
} )();
