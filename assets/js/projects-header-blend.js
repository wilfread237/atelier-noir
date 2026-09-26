( function () {
	"use strict";

	/*
	 * Page Projects : la bande #an-header-pattern-blend est toujours visible
	 * (voir body.an-header-blend-scroll dans menu-overlay.css) — pas de
	 * toggle brusque. Sa position de fond est pilotée en continu ici pour
	 * rester la suite exacte du motif qui défile dans .an-projects-ambient /
	 * .an-projects__pattern-base juste dessous, du chargement (section 1)
	 * jusqu'au footer.
	 *
	 * Les deux calques partagent la même image, le même repeat-y et la même
	 * taille de tuile (background-size: auto 100vh/100dvh), ancrée en haut
	 * du document. À une position de scroll S, la portion de motif visible
	 * derrière la bande header correspond donc à "S modulo hauteur de tuile" :
	 * on décale le fond de la bande de cette valeur pour qu'elle affiche
	 * exactement le même fragment que le calque du contenu à cet instant,
	 * sans jamais de saut ni de double motif.
	 */
	var band = document.getElementById( "an-header-pattern-blend" );
	if ( ! band ) {
		return;
	}

	function tileHeight() {
		return window.innerHeight || document.documentElement.clientHeight || 1;
	}

	function sync() {
		var th = tileHeight();
		/*
		 * Sur iOS, le rebond ("rubber-band") en haut de page peut rendre
		 * window.scrollY momentanément négatif ; le modulo JS peut alors
		 * retourner une valeur négative (contrairement à Python), ce qui
		 * décalerait le motif dans le mauvais sens. Le double modulo garantit
		 * un résultat toujours positif.
		 */
		var offset = ( ( window.scrollY % th ) + th ) % th;
		band.style.backgroundPositionY = ( -offset ) + "px";
	}

	/*
	 * Double appel différé : au chargement, certains navigateurs restaurent
	 * puis corrigent la position de scroll (scroll restoration, ancre) après
	 * l'exécution synchrone du script, sans déclencher d'évènement "scroll"
	 * intercepté à temps. Deux rappels en rAF suffisent à rattraper l'état final.
	 */
	sync();
	window.requestAnimationFrame( function () {
		window.requestAnimationFrame( sync );
	} );

	var ticking = false;
	function onScrollOrResize() {
		if ( ticking ) {
			return;
		}
		ticking = true;
		window.requestAnimationFrame( function () {
			sync();
			ticking = false;
		} );
	}

	window.addEventListener( "scroll", onScrollOrResize, { passive: true } );
	window.addEventListener( "resize", onScrollOrResize );

} )();
