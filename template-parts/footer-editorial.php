<?php
/**
 * Editorial footer block (Services / Projects / Home split panel).
 *
 * @package atelier-noir
 *
 * @var array $args {
 *   @type string $variant   full|compact
 *   @type bool   $watermark Show full-page watermark behind footer
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$variant   = isset( $args['variant'] ) ? (string) $args['variant'] : 'full';
$watermark = ! empty( $args['watermark'] );

$wordmark  = (string) atelier_noir_get_theme_mod( 'an_footer_wordmark', strtoupper( atelier_noir_brand_name() ) );
$copyright = function_exists( 'atelier_noir_format_copyright' )
	? atelier_noir_format_copyright( atelier_noir_get_theme_mod( 'an_footer_copyright', gmdate( 'Y' ) . ' ' . strtoupper( atelier_noir_brand_name() ) . '. ALL RIGHTS RESERVED' ) )
	: (string) atelier_noir_get_theme_mod( 'an_footer_copyright', gmdate( 'Y' ) . ' ' . strtoupper( atelier_noir_brand_name() ) . '. ALL RIGHTS RESERVED' );
$instagram = (string) atelier_noir_get_theme_mod( 'an_footer_instagram_url', 'https://instagram.com/' );
$contact   = atelier_noir_contact_page_url();
$wm_desk    = atelier_noir_menu_watermark_desktop_url();
$wm_mob     = atelier_noir_menu_watermark_mobile_url();
$pattern_url    = $wm_desk ?: $wm_mob;
$pattern_mobile = $wm_mob ?: $pattern_url;
$footer_style   = '';
if ( $watermark && $pattern_url ) {
	$footer_style = sprintf(
		'--an-footer-pattern: url(%1$s); --an-footer-pattern-mobile: url(%2$s);',
		esc_url( $pattern_url ),
		esc_url( $pattern_mobile )
	);
}
?>
<footer
	class="an-footer-editorial an-footer-editorial--<?php echo esc_attr( $variant ); ?><?php echo $watermark ? ' an-footer-editorial--watermark' : ''; ?>"
	role="contentinfo"
	<?php if ( $footer_style ) : ?>
		style="<?php echo esc_attr( $footer_style ); ?>"
	<?php endif; ?>
>

	<?php if ( $watermark && $pattern_url ) : ?>
		<div class="an-footer-editorial__watermark an-footer-editorial__watermark--base" aria-hidden="true"></div>
	<?php endif; ?>

	<div class="an-footer-editorial__inner">
		<div class="an-footer-editorial__logo">
			<?php atelier_noir_render_wordmark( 'an-wordmark an-wordmark--footer' ); ?>
		</div>

		<p class="an-footer-editorial__copy"><?php echo esc_html( $copyright ); ?></p>

		<nav class="an-footer-editorial__links" aria-label="<?php esc_attr_e( 'Footer links', 'atelier-noir' ); ?>">
			<a href="#" class="an-footer-editorial__link an-legal-open" data-legal-tab="privacy" role="button">
				<?php esc_html_e( 'LEGAL', 'atelier-noir' ); ?>
			</a>
			<a class="an-footer-editorial__link" href="<?php echo esc_url( $contact ); ?>"><?php esc_html_e( 'CONTACT', 'atelier-noir' ); ?></a>
			<?php if ( $instagram ) : ?>
				<a class="an-footer-editorial__link" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'INSTAGRAM', 'atelier-noir' ); ?></a>
			<?php endif; ?>
		</nav>
	</div>

	<?php if ( $watermark && $pattern_url ) : ?>
		<div class="an-footer-editorial__watermark an-footer-editorial__watermark--blend" aria-hidden="true"></div>
	<?php endif; ?>

</footer>

