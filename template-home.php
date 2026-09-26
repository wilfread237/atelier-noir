<?php
/**
 * Template Name: Atelier Noir — Home
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$hero_url  = atelier_noir_resolve_media_mod( 'an_home_hero_attachment', 'an_home_hero_url', 'home-ceo-spotlight.png' );
$void_desk = function_exists( 'atelier_noir_home_void_desktop_url' ) ? atelier_noir_home_void_desktop_url() : atelier_noir_resolve_media_mod( 'an_home_void_desktop_attachment', 'an_home_void_desktop_url', 'pattern-gris-translucide.png' );
$void_mob  = function_exists( 'atelier_noir_home_void_mobile_url' ) ? atelier_noir_home_void_mobile_url() : atelier_noir_resolve_media_mod( 'an_home_void_mobile_attachment', 'an_home_void_mobile_url', 'pattern-gris-translucide.png' );
?>
<main id="content" class="an-home" data-header-theme="dark">

	<section
		class="an-home__section an-home__section--void"
		aria-hidden="true"
		<?php if ( $void_desk || $void_mob ) : ?>
			style="<?php echo esc_attr( '--an-home-void-desktop: url(' . esc_url( $void_desk ?: $void_mob ) . ');' . ( $void_mob ? ' --an-home-void-mobile: url(' . esc_url( $void_mob ) . ');' : '' ) ); ?>"
		<?php endif; ?>
	></section>

	<section class="an-home__section an-home__section--hero" aria-hidden="true">
		<?php if ( $hero_url ) : ?>
			<img class="an-home__hero-image" src="<?php echo esc_url( $hero_url ); ?>" alt="" decoding="async">
		<?php endif; ?>
	</section>

	<section class="an-home-footer" aria-label="<?php esc_attr_e( 'Footer', 'atelier-noir' ); ?>">
		<?php
		get_template_part(
			'template-parts/footer',
			'editorial',
			array(
				'variant'   => 'compact',
				'watermark' => false,
			)
		);
		?>
	</section>

</main>
<?php
get_footer();
