<?php
/**
 * Template Name: Atelier Noir — Contact
 *
 * @package atelier-noir
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$wordmark  = (string) atelier_noir_get_theme_mod( 'an_footer_wordmark', strtoupper( atelier_noir_brand_name() ) );
$cities    = (string) atelier_noir_get_theme_mod( 'an_contact_cities', 'PARIS · LONDON · NEW YORK' );
$email     = atelier_noir_contact_email();
$wm_desk   = atelier_noir_menu_watermark_desktop_url();
$wm_mob    = atelier_noir_menu_watermark_mobile_url();
$logo_url  = function_exists( 'atelier_noir_logo_url' ) ? atelier_noir_logo_url() : '';
?>
<main id="content" class="an-contact-page" data-header-theme="dark">

	<?php if ( $wm_desk || $wm_mob ) : ?>
		<div class="an-contact-page__watermark" aria-hidden="true">
			<picture>
				<?php if ( $wm_mob ) : ?>
					<source media="(max-width: 1024px)" srcset="<?php echo esc_url( $wm_mob ); ?>">
				<?php endif; ?>
				<img src="<?php echo esc_url( $wm_desk ?: $wm_mob ); ?>" alt="" decoding="async">
			</picture>
		</div>
	<?php endif; ?>

	<div class="an-contact-page__inner">
		<?php if ( $logo_url ) : ?>
			<img
				class="an-contact-page__logo"
				src="<?php echo esc_url( $logo_url ); ?>"
				alt="<?php echo esc_attr( $wordmark ); ?>"
				decoding="async"
			>
		<?php else : ?>
			<h1 class="an-contact-page__wordmark"><?php echo esc_html( $wordmark ); ?></h1>
		<?php endif; ?>
		<p class="an-contact-page__cities"><?php echo esc_html( $cities ); ?></p>
		<p class="an-contact-page__email">
			<a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
		</p>
		<div class="an-contact-page__social">
			<?php get_template_part( 'template-parts/social', 'links' ); ?>
		</div>
	</div>

</main>
<?php
get_footer();
