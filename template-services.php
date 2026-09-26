<?php
/**
 * Template Name: Atelier Noir — Services
 *
 * @package atelier-noir
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$services = function_exists( 'atelier_noir_get_services_list' )
	? atelier_noir_get_services_list()
	: atelier_noir_services_defaults();

get_header();

while ( have_posts() ) :
	the_post();
	?>
<main id="content" class="studio-services-page an-services" data-header-theme="dark">

	<div class="studio-services-intro" aria-hidden="true">
		<h1 class="studio-services-intro__title"><?php the_title(); ?></h1>
	</div>

	<div class="studio-services-swiper swiper" aria-live="polite">
		<div class="swiper-wrapper">

		<?php foreach ( $services as $index => $service ) : ?>
		<?php
		$label = ! empty( $service['title'] ) ? (string) $service['title'] : (string) $service['id'];
		$mailto = atelier_noir_service_mailto_url( $service );
		?>
		<section
			id="service-<?php echo esc_attr( $service['id'] ); ?>"
			class="swiper-slide studio-service-panel"
			data-service="<?php echo esc_attr( $service['id'] ); ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
		>
			<div class="studio-service-panel__media" aria-hidden="true">
				<?php if ( 'video' === $service['type'] && ! empty( $service['video'] ) ) : ?>
					<video class="studio-service-panel__video js-service-video" autoplay muted loop playsinline poster="<?php echo esc_url( $service['poster'] ?? '' ); ?>">
						<source src="<?php echo esc_url( $service['video'] ); ?>" type="video/mp4">
					</video>
				<?php elseif ( ! empty( $service['image'] ) ) : ?>
					<img class="studio-service-panel__image" src="<?php echo esc_url( $service['image'] ); ?>" alt="" loading="lazy" decoding="async">
				<?php else : ?>
					<div class="studio-service-panel__placeholder"></div>
				<?php endif; ?>
			</div>

			<div class="studio-service-panel__scrim" aria-hidden="true"></div>

			<div class="studio-service-panel__content">
				<div class="studio-service-panel__content-inner js-service-content">
					<?php if ( ! empty( $service['title'] ) ) : ?>
						<h2 class="studio-service-panel__title"><?php echo esc_html( $service['title'] ); ?></h2>
					<?php endif; ?>
					<?php if ( ! empty( $service['text'] ) ) : ?>
						<p class="studio-service-panel__text"><?php echo esc_html( $service['text'] ); ?></p>
					<?php endif; ?>
					<a class="studio-service-panel__cta" href="<?php echo esc_url( $mailto ); ?>">
						<span class="studio-service-panel__cta-label"><?php echo esc_html( $service['cta_en'] ); ?></span>
					</a>
				</div>
			</div>
		</section>
		<?php endforeach; ?>

		<section class="swiper-slide an-services-footer-slide">
			<?php
			get_template_part(
				'template-parts/footer',
				'editorial',
				array(
					'variant'   => 'full',
					'watermark' => true,
				)
			);
			?>
		</section>

		</div>
	</div>

</main>
	<?php
endwhile;

get_footer();
