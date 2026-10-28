<?php
/**
 * Landing — black field, centered white and orange type.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact  = home_url( '/contact/' );
$services = home_url( '/services/' );
?>
<main id="content" class="an-landing" data-header-theme="dark">
	<section class="an-landing__stage" aria-labelledby="an-landing-title">
		<div class="an-landing__intro">
			<h1 id="an-landing-title" class="an-landing__title">
				<span><?php esc_html_e( 'Creating brands', 'atelier-noir' ); ?> <br class="an-landing__break"><?php esc_html_e( 'that feel', 'atelier-noir' ); ?></span>
				<span class="an-landing__accent"><?php esc_html_e( 'impossible to ignore.', 'atelier-noir' ); ?></span>
			</h1>
			<p class="an-landing__lead">
				<?php esc_html_e( 'Independent creative studio building memorable identities, digital experiences and motion systems for ambitious companies.', 'atelier-noir' ); ?>
			</p>
			<div class="an-landing__actions">
				<a class="an-landing__btn an-landing__btn--solid" href="<?php echo esc_url( $contact ); ?>">
					<?php esc_html_e( 'Start project', 'atelier-noir' ); ?>
					<span aria-hidden="true">↗</span>
				</a>
				<a class="an-landing__btn" href="<?php echo esc_url( $services ); ?>">
					<?php esc_html_e( 'Explore services', 'atelier-noir' ); ?>
					<span aria-hidden="true">→</span>
				</a>
			</div>
		</div>
	</section>
</main>
