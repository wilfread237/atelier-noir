<?php
/**
 * Template Name: Home — sections PHP
 * Description: Homepage modulaire PHP — compatible Elementor via the_content().
 *
 * @package atelier-noir
 */

get_header();

while ( have_posts() ) :
	the_post();
	$post_id = get_the_ID();

	if ( atelier_noir_page_built_with_elementor( $post_id ) || atelier_noir_is_elementor_editor_or_preview() ) {
		?>
		<section class="home home--elementor section">
			<div class="container">
				<div class="page-content__entry">
					<?php the_content(); ?>
				</div>
			</div>
		</section>
		<?php
		continue;
	}
	?>
	<div class="home">
		<?php get_template_part( 'template-parts/home/hero' ); ?>
		<?php get_template_part( 'template-parts/home/about' ); ?>
		<?php get_template_part( 'template-parts/home/services' ); ?>
		<?php get_template_part( 'template-parts/home/founder' ); ?>
		<?php get_template_part( 'template-parts/home/journal' ); ?>
		<?php get_template_part( 'template-parts/home/merch' ); ?>
		<?php get_template_part( 'template-parts/home/contact-cta' ); ?>
	</div>
	<?php
	if ( class_exists( '\Elementor\Plugin' ) ) {
		?>
		<div class="page-content__entry studio-native-the-content-slot">
			<?php the_content(); ?>
		</div>
		<?php
	}
endwhile;

get_footer();
