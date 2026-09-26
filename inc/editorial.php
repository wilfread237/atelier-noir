<?php
/**
 * Editorial layout helpers (maquette SPJ).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pages using the new editorial system (no legacy footer).
 *
 * @return bool
 */
function atelier_noir_uses_editorial_layout() {
	if ( is_page_template(
		array(
			'template-home.php',
			'template-services.php',
			'template-projects.php',
			'template-contact.php',
		)
	) ) {
		return true;
	}
	return is_singular( 'an_project' );
}

/**
 * Footer is rendered inside the page template.
 *
 * @return bool
 */
function atelier_noir_has_inline_footer() {
	return atelier_noir_uses_editorial_layout();
}

/**
 * @return string
 */
function atelier_noir_contact_email() {
	$email = (string) atelier_noir_get_theme_mod(
		'an_contact_email',
		atelier_noir_email_contact()
	);
	return sanitize_email( $email ) ?: atelier_noir_email_contact();
}

/**
 * Build mailto URL for service inquiry.
 *
 * @param array<string, mixed> $service Service row.
 * @return string
 */
function atelier_noir_service_mailto_url( $service ) {
	$title = '';
	if ( ! empty( $service['title'] ) ) {
		$title = (string) $service['title'];
	} elseif ( ! empty( $service['id'] ) ) {
		$title = ucwords( str_replace( array( '-', '_' ), ' ', (string) $service['id'] ) );
	}
	$subject = 'Inquiry - ' . $title;
	return 'mailto:' . rawurlencode( atelier_noir_contact_email() ) . '?subject=' . rawurlencode( $subject );
}

/**
 * Enqueue shared editorial assets.
 */
function atelier_noir_enqueue_editorial_assets() {
	if ( ! atelier_noir_uses_editorial_layout() && ! is_singular( 'an_project' ) ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$ver = wp_get_theme()->get( 'Version' );

	$footer_css = $dir . '/assets/css/footer-editorial.css';
	if ( file_exists( $footer_css ) ) {
		wp_enqueue_style(
			'atelier-noir-footer-editorial',
			$uri . '/assets/css/footer-editorial.css',
			array( 'atelier-noir' ),
			(string) filemtime( $footer_css )
		);
	}

	$legal_js = $dir . '/assets/js/legal-overlay.js';
	if ( file_exists( $legal_js ) ) {
		wp_enqueue_script(
			'atelier-noir-legal-overlay',
			$uri . '/assets/js/legal-overlay.js',
			array(),
			(string) filemtime( $legal_js ),
			true
		);
	}

	$legal_css = $dir . '/assets/css/legal-overlay.css';
	if ( file_exists( $legal_css ) ) {
		wp_enqueue_style(
			'atelier-noir-legal-overlay',
			$uri . '/assets/css/legal-overlay.css',
			array( 'atelier-noir-footer-editorial' ),
			(string) filemtime( $legal_css )
		);
	}

	if ( is_page_template( 'template-contact.php' ) ) {
		$contact_css = $dir . '/assets/css/contact-page.css';
		if ( file_exists( $contact_css ) ) {
			wp_enqueue_style(
				'atelier-noir-contact-page',
				$uri . '/assets/css/contact-page.css',
				array( 'atelier-noir' ),
				(string) filemtime( $contact_css )
			);
		}
	}

	if ( is_page_template( 'template-projects.php' ) || is_singular( 'an_project' ) ) {
		$projects_css = $dir . '/assets/css/projects.css';
		if ( file_exists( $projects_css ) ) {
			wp_enqueue_style(
				'atelier-noir-projects',
				$uri . '/assets/css/projects.css',
				array( 'atelier-noir' ),
				(string) filemtime( $projects_css )
			);
		}
	}

	if ( is_page_template( 'template-projects.php' ) ) {
		$projects_blend_js = $dir . '/assets/js/projects-header-blend.js';
		if ( file_exists( $projects_blend_js ) ) {
			wp_enqueue_script(
				'atelier-noir-projects-header-blend',
				$uri . '/assets/js/projects-header-blend.js',
				array(),
				(string) filemtime( $projects_blend_js ),
				true
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'atelier_noir_enqueue_editorial_assets', 26 );

/**
 * Legal overlay must sit at document level (wp_footer), not inside page
 * stacking contexts (Projects isolation / Services Swiper transform), so
 * position:fixed covers the full viewport — same as live Home.
 */
function atelier_noir_render_legal_overlay() {
	static $rendered = false;
	if ( $rendered ) {
		return;
	}
	if ( ! function_exists( 'atelier_noir_uses_editorial_layout' ) || ! atelier_noir_uses_editorial_layout() ) {
		return;
	}
	$rendered = true;
	get_template_part( 'template-parts/legal', 'overlay' );
}
add_action( 'wp_footer', 'atelier_noir_render_legal_overlay', 5 );

/**
 * Ajoute an-full-blend au <body> pour les pages nécessitant
 * le pattern fixe au-dessus du header (contact uniquement).
 *
 * Projects : le pattern vit dans .an-projects-ambient (scroll naturel,
 * comme Home void). Pas de calque fixe #an-body-blend — évite le décalage
 * visuel au scroll mobile quand le footer entre dans le viewport.
 *
 * an-header-blend (Projects, Services) : bande fixe limitée à la hauteur du
 * header uniquement (voir #an-header-pattern-blend) — le motif recouvre le
 * logo/hamburger comme la maquette, sans le décalage plein écran ci-dessus.
 */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_page_template( 'template-contact.php' ) ) {
			$classes[] = 'an-full-blend';
		}
		if ( is_page_template( array( 'template-projects.php', 'template-services.php' ) ) || is_singular( 'an_project' ) ) {
			$classes[] = 'an-header-blend';
		}
		/*
		 * Projects (listing) uniquement : bande de motif toujours visible et
		 * synchronisée en continu avec le scroll (voir projects-header-blend.js),
		 * plutôt qu'un affichage brusque (toggle) qui créait un décalage visible
		 * entre le motif fixe de la bande et le motif du contenu déjà défilé.
		 */
		if ( is_page_template( 'template-projects.php' ) ) {
			$classes[] = 'an-header-blend-scroll';
		}
		return $classes;
	},
	20
);
