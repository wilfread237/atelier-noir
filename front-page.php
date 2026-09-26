<?php
/**
 * Page d'accueil (enfant) : secours FR si pas encore de contenu Elementor / éditeur.
 *
 * When the static front page is the Loader, render the standalone loader template
 * (WordPress always prefers front-page.php over the page's assigned template).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_loader_front = function_exists( 'atelier_noir_is_loader_page' ) && atelier_noir_is_loader_page();
$is_editor       = function_exists( 'atelier_noir_is_elementor_editor_or_preview' )
	&& atelier_noir_is_elementor_editor_or_preview();

if ( $is_loader_front && ! $is_editor ) {
	$loader_tpl = get_stylesheet_directory() . '/template-loader.php';
	if ( is_readable( $loader_tpl ) ) {
		include $loader_tpl;
		return;
	}
}

get_header();

if ( is_front_page() && is_home() ) :
	?>
	<section class="archive container section">
		<?php get_template_part( 'template-parts/posts', 'grid' ); ?>
	</section>
	<?php
else :
	while ( have_posts() ) :
		the_post();
		$page_id = get_the_ID();

		$use_customizer_block = function_exists( 'atelier_noir_front_should_render_customizer_home' )
			&& atelier_noir_front_should_render_customizer_home( $page_id )
			&& ! atelier_noir_is_elementor_editor_or_preview()
			&& ! atelier_noir_page_built_with_elementor( $page_id );

		if ( $use_customizer_block ) {
			get_template_part( 'template-parts/home', 'customizer' );
			// Même logique que Contact / Services : the_content() présent si Elementor est installé (éditeur / premier enregistrement).
			if ( class_exists( '\Elementor\Plugin' ) ) {
				?>
				<div class="page-content__entry studio-native-the-content-slot">
					<?php the_content(); ?>
				</div>
				<?php
			}
		} else {
			?>
			<section class="front-page page-content section">
				<article <?php post_class(); ?>>
					<div class="page-content__entry">
						<?php the_content(); ?>
					</div>
				</article>
			</section>
			<?php
		}
	endwhile;
endif;

get_footer();
