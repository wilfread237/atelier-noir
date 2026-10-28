<?php
/**
 * Instagram, LinkedIn and TikTok icons shared by the footer and contact page.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ig = function_exists( 'atelier_noir_footer_mod' )
	? atelier_noir_footer_mod( 'atelier_noir_footer_social_instagram', 'https://instagram.com' )
	: 'https://instagram.com';
$li = function_exists( 'atelier_noir_footer_mod' )
	? atelier_noir_footer_mod( 'atelier_noir_footer_social_linkedin', 'https://linkedin.com' )
	: 'https://linkedin.com';
$tt = function_exists( 'atelier_noir_footer_mod' )
	? atelier_noir_footer_mod( 'atelier_noir_footer_social_tiktok', 'https://tiktok.com' )
	: 'https://tiktok.com';
?>
<?php if ( $ig ) : ?>
	<a href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Instagram', 'atelier-noir' ); ?>">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
			<circle cx="12" cy="12" r="4"/>
			<circle cx="17.5" cy="6.5" r="0.8" fill="currentColor" stroke="none"/>
		</svg>
	</a>
<?php endif; ?>
<?php if ( $li ) : ?>
	<a href="<?php echo esc_url( $li ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'LinkedIn', 'atelier-noir' ); ?>">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
			<rect x="2" y="9" width="4" height="12"/>
			<circle cx="4" cy="4" r="2"/>
		</svg>
	</a>
<?php endif; ?>
<?php if ( $tt ) : ?>
	<a href="<?php echo esc_url( $tt ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'TikTok', 'atelier-noir' ); ?>">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
			<path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34l-.01-8.03a8.17 8.17 0 0 0 4.78 1.52V5.35a4.85 4.85 0 0 1-1-.66z"/>
		</svg>
	</a>
<?php endif; ?>
