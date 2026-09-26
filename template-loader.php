<?php
/**
 * Template Name: Atelier Noir — Loader
 * Description: Standalone entry page at site root. Redirects to Home after a delay.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$delay_ms     = max( 0, (int) atelier_noir_get_theme_mod( 'an_loader_delay_ms', 7000 ) );
$skip_loader  = (bool) atelier_noir_get_theme_mod( 'an_loader_disable', false );
$redirect_url = atelier_noir_loader_redirect_url();
// Même visuel sur tous les breakpoints : logo principal + pattern (maquette LOADER).
$loader_img   = atelier_noir_resolve_media_mod( 'an_loader_desktop_attachment', 'an_loader_desktop_url', 'loader-desktop.png' );
$pattern_url  = function_exists( 'atelier_noir_brand_asset_uri' )
	? atelier_noir_brand_asset_uri( 'pattern-gris-translucide.png' )
	: '';
$wordmark_url = atelier_noir_resolve_media_mod(
	'an_loader_wordmark_attachment',
	'an_loader_wordmark_url',
	'logo-header-desktop.png'
);
$video_att    = absint( atelier_noir_get_theme_mod( 'an_loader_video_attachment', 0 ) );
$video_url    = $video_att ? (string) wp_get_attachment_url( $video_att ) : '';
$has_video    = '' !== $video_url;

if ( $skip_loader ) {
	wp_safe_redirect( $redirect_url );
	exit;
}

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'an-loader-page' ); ?>>
<?php wp_body_open(); ?>

<main
	class="an-loader-page__main"
	id="an-loader-page"
	data-delay-ms="<?php echo esc_attr( (string) $delay_ms ); ?>"
	data-redirect="<?php echo esc_url( $redirect_url ); ?>"
>

	<?php if ( $has_video ) : ?>
		<video class="an-loader-page__media an-loader-page__media--video" autoplay muted loop playsinline>
			<source src="<?php echo esc_url( $video_url ); ?>" type="<?php echo esc_attr( get_post_mime_type( $video_att ) ?: 'video/mp4' ); ?>">
		</video>
	<?php else : ?>
		<img
			class="an-loader-page__media an-loader-page__media--image an-loader-page__media--desktop-only"
			src="<?php echo esc_url( $loader_img ); ?>"
			alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
			width="1440"
			height="810"
			decoding="async"
			fetchpriority="high"
		>
		<div
			class="an-loader-page__scene an-loader-page__scene--mobile-only"
			<?php if ( $pattern_url ) : ?>
				style="<?php echo esc_attr( '--an-loader-pattern: url(' . esc_url( $pattern_url ) . ');' ); ?>"
			<?php endif; ?>
		>
			<?php if ( $wordmark_url ) : ?>
				<img
					class="an-loader-page__wordmark"
					src="<?php echo esc_url( $wordmark_url ); ?>"
					alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
					width="1024"
					height="103"
					decoding="async"
					fetchpriority="high"
				>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</main>

<?php wp_footer(); ?>
</body>
</html>
