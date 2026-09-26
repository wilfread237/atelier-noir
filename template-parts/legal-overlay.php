<?php
/**
 * Legal overlay modal (editorial panel).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$privacy   = (string) atelier_noir_get_theme_mod( 'an_legal_privacy_body', '' );
$cookies   = (string) atelier_noir_get_theme_mod( 'an_legal_cookies_body', '' );
$terms     = (string) atelier_noir_get_theme_mod( 'an_legal_terms_body', '' );
$copyright = function_exists( 'atelier_noir_format_copyright' )
	? atelier_noir_format_copyright( atelier_noir_get_theme_mod( 'an_footer_copyright', gmdate( 'Y' ) . ' ' . strtoupper( atelier_noir_brand_name() ) . '. ALL RIGHTS RESERVED' ) )
	: (string) atelier_noir_get_theme_mod( 'an_footer_copyright', gmdate( 'Y' ) . ' ' . strtoupper( atelier_noir_brand_name() ) . '. ALL RIGHTS RESERVED' );
$logo_url  = function_exists( 'atelier_noir_logo_url' ) ? atelier_noir_logo_url() : '';

if ( '' === trim( $privacy ) && function_exists( 'atelier_noir_default_legal_privacy_body' ) ) {
	$privacy = atelier_noir_default_legal_privacy_body();
}
if ( '' === trim( $cookies ) && function_exists( 'atelier_noir_default_legal_cookies_body' ) ) {
	$cookies = atelier_noir_default_legal_cookies_body();
}
if ( '' === trim( $terms ) && function_exists( 'atelier_noir_default_legal_terms_body' ) ) {
	$terms = atelier_noir_default_legal_terms_body();
}
?>
<div id="an-legal-overlay" class="an-legal-overlay" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Legal information', 'atelier-noir' ); ?>">

	<button type="button" class="an-legal-overlay__close" aria-label="<?php esc_attr_e( 'Close', 'atelier-noir' ); ?>">
		<svg class="an-legal-overlay__close-svg" width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
			<circle cx="22" cy="22" r="20" stroke="#FFFFFF" stroke-width="1"/>
			<path d="M14 14 L30 30 M30 14 L14 30" stroke="#FFFFFF" stroke-width="1" stroke-linecap="round"/>
		</svg>
	</button>

	<div class="an-legal-overlay__inner">
		<div class="an-legal-overlay__head">
			<?php if ( $logo_url ) : ?>
				<img class="an-legal-overlay__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" decoding="async">
			<?php else : ?>
				<p class="an-legal-overlay__brand"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
			<?php endif; ?>

			<nav class="an-legal-overlay__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Legal sections', 'atelier-noir' ); ?>">
				<a href="#" class="an-legal-overlay__tab is-active" role="tab" aria-selected="true" data-tab="privacy"><?php esc_html_e( 'WEBSITE PRIVACY POLICY', 'atelier-noir' ); ?></a>
				<a href="#" class="an-legal-overlay__tab" role="tab" aria-selected="false" data-tab="cookies"><?php esc_html_e( 'COOKIE POLICY', 'atelier-noir' ); ?></a>
				<a href="#" class="an-legal-overlay__tab" role="tab" aria-selected="false" data-tab="terms"><?php esc_html_e( 'TERMS & CONDITIONS', 'atelier-noir' ); ?></a>
			</nav>
		</div>

		<div class="an-legal-overlay__body">
			<div class="an-legal-overlay__pane is-active" data-pane="privacy" role="tabpanel"><?php echo wp_kses_post( wpautop( $privacy ) ); ?></div>
			<div class="an-legal-overlay__pane" data-pane="cookies" role="tabpanel" hidden><?php echo wp_kses_post( wpautop( $cookies ) ); ?></div>
			<div class="an-legal-overlay__pane" data-pane="terms" role="tabpanel" hidden><?php echo wp_kses_post( wpautop( $terms ) ); ?></div>
		</div>

		<p class="an-legal-overlay__copyright"><?php echo esc_html( $copyright ); ?></p>
	</div>
</div>
