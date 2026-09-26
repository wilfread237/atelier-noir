( function () {
	"use strict";

	var root = document.querySelector( ".studio-services-swiper" );
	if ( ! root || typeof Swiper === "undefined" ) {
		var first = document.querySelector( ".studio-service-panel" );
		if ( first ) {
			first.classList.add( "swiper-slide-active" );
		}

		/* Sans Swiper : layout empilé/scrollable, pattern header piloté au scroll. */
		var fallbackFooter = document.querySelector( ".an-services-footer-slide" );
		if ( fallbackFooter ) {
			var isFallbackFooterVisible = function () {
				var rect = fallbackFooter.getBoundingClientRect();
				return rect.top < window.innerHeight && rect.bottom > 0;
			};
			var setFallbackActive = function ( isActive ) {
				document.body.classList.toggle( "an-header-blend-active", isActive );
			};

			if ( typeof window.IntersectionObserver !== "undefined" ) {
				new window.IntersectionObserver(
					function ( entries ) {
						entries.forEach( function ( entry ) {
							setFallbackActive( entry.isIntersecting );
						} );
					},
					{ threshold: 0 }
				).observe( fallbackFooter );
			} else {
				setFallbackActive( isFallbackFooterVisible() );
			}

			/* Filet de sécurité : l'IO peut manquer une mise à jour purement liée au scroll. */
			var fallbackTicking = false;
			window.addEventListener( "scroll", function () {
				if ( fallbackTicking ) {
					return;
				}
				fallbackTicking = true;
				window.requestAnimationFrame( function () {
					setFallbackActive( isFallbackFooterVisible() );
					fallbackTicking = false;
				} );
			}, { passive: true } );
		}
		return;
	}

	var videos = root.querySelectorAll( ".js-service-video" );
	var slides = root.querySelectorAll( ".swiper-slide" );

	function syncVideos( activeIndex ) {
		videos.forEach( function ( vid, i ) {
			if ( i === activeIndex ) {
				vid.play().catch( function () {} );
			} else {
				vid.pause();
			}
		} );
	}

	/*
	 * Pattern par-dessus le header (#an-header-pattern-blend) : visible
	 * uniquement quand le slide footer (.an-services-footer-slide) est actif,
	 * pas dès le chargement de la page. Voir menu-overlay.css.
	 */
	function syncHeaderBlend( activeIndex ) {
		var activeSlide = slides[ activeIndex ];
		var isFooterSlide = !! ( activeSlide && activeSlide.classList.contains( "an-services-footer-slide" ) );
		document.body.classList.toggle( "an-header-blend-active", isFooterSlide );
	}

	/**
	 * Swiper vertical — réglages de transition éditoriale plein écran.
	 * speed 500ms | creative prev [0,0,-1] next [0,100%,0] | mousewheel thresholdDelta 10
	 */
	var swiper = new Swiper( root, {
		direction                      : "vertical",
		slidesPerView                  : 1,
		slidesPerGroup                 : 1,
		spaceBetween                   : 0,
		effect                         : "creative",
		speed                          : 500,
		allowTouchMove                 : true,
		watchSlidesProgress            : true,
		preventInteractionOnTransition : true,
		resistanceRatio                : 0.85,
		creativeEffect                 : {
			limitProgress      : 1,
			progressMultiplier : 1,
			perspective        : true,
			shadowPerProgress  : false,
			prev               : {
				translate : [ 0, 0, -1 ],
				rotate    : [ 0, 0, 0 ],
				opacity   : 1,
				scale     : 1,
			},
			next               : {
				translate : [ 0, "100%", 0 ],
				rotate    : [ 0, 0, 0 ],
				opacity   : 1,
				scale     : 1,
			},
		},
		mousewheel: {
			enabled        : true,
			forceToAxis    : false,
			sensitivity    : 1,
			releaseOnEdges : false,
			thresholdDelta : 10,
		},
		keyboard: {
			enabled        : true,
			onlyInViewport : true,
		},
		on: {
			init: function () {
				syncVideos( this.activeIndex );
				syncHeaderBlend( this.activeIndex );
			},
			slideChange: function () {
				syncVideos( this.activeIndex );
				syncHeaderBlend( this.activeIndex );
			},
			/*
			 * Retour arrière : la slide sortante a .swiper-slide-next et descend,
			 * mais Swiper met la cible en .swiper-slide-active (z-index plus haut).
			 * Résultat : la sortante passe derrière → elle « disparaît » sans descendre visuellement.
			 */
			slidePrevTransitionStart: function () {
				root.classList.add( "is-slide-prev" );
			},
			slideNextTransitionStart: function () {
				root.classList.remove( "is-slide-prev" );
			},
			/* Correctif technique Swiper 11 + creative : transitionend manquant. */
			transitionStart: function () {
				var self = this;
				window.clearTimeout( self.__creativeFallbackTimer );
				self.__creativeFallbackTimer = window.setTimeout( function () {
					if ( ! self.animating ) {
						return;
					}
					self.animating = false;
					self.wrapperEl.dispatchEvent(
						new window.CustomEvent( "transitionend", {
							bubbles    : true,
							cancelable : true,
						} )
					);
				}, self.params.speed + 100 );
			},
			transitionEnd: function () {
				window.clearTimeout( this.__creativeFallbackTimer );
				root.classList.remove( "is-slide-prev" );
			},
		},
	} );

	window.addEventListener( "resize", function () {
		swiper.update();
	} );

} )();
