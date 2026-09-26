/**
 * Atelier Noir — Animations GSAP + vidéo héro
 *
 * Chargé uniquement sur les pages qui en ont besoin :
 *   - Accueil (front-page)
 *   - Page Services (template-services.php / template-services-studio.php)
 *
 * Dépendances : gsap, gsap-scrolltrigger (CDN, chargés avant ce script)
 * La logique header + menu mobile est dans header.js (toutes les pages).
 */
( function () {
	"use strict";

	// ── Vidéo héro : pause au scroll, reprise au retour ──────────────────────

	var heroSection = document.querySelector( ".elementor-element-studio-hero, .studio-eyes-catcher" );

	if ( heroSection && "IntersectionObserver" in window ) {

		var heroPausedByScroll = false;

		function sendFrameCmd( iframe, ytCmd, vimeoCmd ) {
			if ( ! iframe || ! iframe.contentWindow ) { return; }
			try {
				iframe.contentWindow.postMessage(
					JSON.stringify( { event: "command", func: ytCmd, args: [] } ), "*"
				);
				if ( vimeoCmd ) {
					iframe.contentWindow.postMessage(
						JSON.stringify( { method: vimeoCmd } ), "*"
					);
				}
			} catch ( e ) {}
		}

		var heroObserver = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					var iframes = heroSection.querySelectorAll( "iframe" );
					var videos  = heroSection.querySelectorAll( "video" );

					if ( ! entry.isIntersecting ) {
						heroPausedByScroll = true;
						iframes.forEach( function ( f ) { sendFrameCmd( f, "pauseVideo", "pause" ); } );
						videos.forEach(  function ( v ) { v.pause(); } );
					} else if ( heroPausedByScroll ) {
						heroPausedByScroll = false;
						iframes.forEach( function ( f ) { sendFrameCmd( f, "playVideo", "play" ); } );
						videos.forEach(  function ( v ) { v.play().catch( function () {} ); } );
					}
				} );
			},
			{ threshold: 0.1 }
		);

		heroObserver.observe( heroSection );
	}

	// ── Animations GSAP au scroll ─────────────────────────────────────────────

	if ( typeof window.gsap === "undefined" || typeof window.ScrollTrigger === "undefined" ) {
		return;
	}

	window.gsap.registerPlugin( window.ScrollTrigger );

	document.querySelectorAll( ".js-reveal" ).forEach( function ( el ) {
		window.gsap.fromTo( el,
			{ autoAlpha: 0, y: 32 },
			{ autoAlpha: 1, y: 0, duration: 0.9, ease: "power2.out",
			  scrollTrigger: { trigger: el, start: "top 82%", once: true } }
		);
	} );

	document.querySelectorAll( ".js-text-reveal" ).forEach( function ( el ) {
		window.gsap.fromTo( el,
			{ autoAlpha: 0, y: 24 },
			{ autoAlpha: 1, y: 0, duration: 0.85, delay: 0.08, ease: "power2.out",
			  scrollTrigger: { trigger: el, start: "top 88%", once: true } }
		);
	} );

	document.querySelectorAll( "[data-studio-reveal]" ).forEach( function ( el ) {
		window.gsap.fromTo( el,
			{ autoAlpha: 0, y: 20 },
			{ autoAlpha: 1, y: 0, duration: 0.8, ease: "power2.out",
			  scrollTrigger: { trigger: el, start: "top 85%", once: true } }
		);
	} );

} )();
