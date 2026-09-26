<?php
/**
 * Single Project — full-screen media stack.
 *
 * @package atelier-noir
 */

get_header();

while ( have_posts() ) :
	the_post();
	$sections = function_exists( 'atelier_noir_get_project_sections_for_display' )
		? atelier_noir_get_project_sections_for_display( get_the_ID() )
		: array();
	?>
	<main id="content" class="an-project-single" data-header-theme="dark">
		<?php foreach ( $sections as $i => $section ) : ?>
			<section class="an-project-single__section" aria-label="<?php echo esc_attr( sprintf( /* translators: %d section number */ __( 'Section %d', 'atelier-noir' ), $i + 1 ) ); ?>">
				<?php if ( 'video' === $section['type'] ) : ?>
					<video class="an-project-single__media" autoplay muted loop playsinline>
						<source src="<?php echo esc_url( $section['url'] ); ?>" type="video/mp4">
					</video>
				<?php else : ?>
					<img class="an-project-single__media" src="<?php echo esc_url( $section['url'] ); ?>" alt="" loading="<?php echo 0 === $i ? 'eager' : 'lazy'; ?>" decoding="async">
				<?php endif; ?>
			</section>
		<?php endforeach; ?>
	</main>
	<?php
endwhile;

get_footer();
