<?php
/**
 * Template Name: Services Studio
 * Description: Canevas Elementor — tout le contenu, y compris les fonds de page, se gère dans Elementor.
 *
 * @package atelier-noir
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="studio-elementor-page studio-elementor-page--services-studio">
		<?php the_content(); ?>
	</div>
	<?php
endwhile;

get_footer();
