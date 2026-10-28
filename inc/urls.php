<?php
/**
 * Theme URL helpers (English slugs with legacy fallbacks).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Permalink for the first matching page slug.
 *
 * @param string[] $slugs    Candidate slugs (first match wins).
 * @param string   $fallback Path if no page exists.
 * @return string
 */
function atelier_noir_page_url( array $slugs, $fallback ) {
	foreach ( $slugs as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page && ! is_wp_error( $page ) ) {
			return get_permalink( $page );
		}
	}
	return home_url( $fallback );
}

/**
 * Legal notice, privacy and terms pages use a light field.
 *
 * @return bool
 */
function atelier_noir_is_legal_document_page() {
	if ( is_page_template(
		array(
			'template-mentions-legales.php',
			'template-politique-confidentialite.php',
			'template-cgv.php',
		)
	) ) {
		return true;
	}

	return is_page(
		array(
			'legal-notice',
			'privacy-policy',
			'terms-and-conditions',
			'mentions-legales',
			'politique-de-confidentialite',
			'cgv',
		)
	);
}

/**
 * @return string
 */
function atelier_noir_legal_notice_url() {
	return atelier_noir_page_url( array( 'legal-notice', 'mentions-legales' ), '/legal-notice/' );
}

/**
 * @return string
 */
function atelier_noir_privacy_policy_url() {
	return atelier_noir_page_url( array( 'privacy-policy', 'politique-de-confidentialite' ), '/privacy-policy/' );
}

/**
 * @return string
 */
function atelier_noir_terms_url() {
	return atelier_noir_page_url( array( 'terms-and-conditions', 'cgv' ), '/terms-and-conditions/' );
}
