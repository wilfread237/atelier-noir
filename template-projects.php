<?php
/**
 * Template Name: Atelier Noir — Projects
 *
 * Empty state : ambient pattern section + footer (pattern jusqu'au header).
 * With projects : media feed (sans pattern) + footer (pattern conservé via
 * les calques main). La bande header #an-header-pattern-blend reste masquée
 * dès qu'il y a un feed — c'était le bug "DIO jusqu'au header" sur les médias.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$projects = new WP_Query(
	array(
		'post_type'      => 'an_project',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

$feed = array();
if ( $projects->have_posts() ) {
	while ( $projects->have_posts() ) {
		$projects->the_post();
		$sections = function_exists( 'atelier_noir_get_project_sections_for_display' )
			? atelier_noir_get_project_sections_for_display( get_the_ID() )
			: array();
		foreach ( $sections as $section ) {
			$feed[] = $section;
		}
	}
	wp_reset_postdata();
}

$has_feed       = ! empty( $feed );
$wm_desk        = atelier_noir_menu_watermark_desktop_url();
$wm_mob         = atelier_noir_menu_watermark_mobile_url();
$pattern_url    = $wm_desk ?: $wm_mob;
$pattern_mobile = $wm_mob ?: $pattern_url;
$main_style     = '';
if ( $pattern_url ) {
	$main_style = sprintf(
		'--an-projects-pattern: url(%1$s); --an-projects-pattern-mobile: url(%2$s);',
		esc_url( $pattern_url ),
		esc_url( $pattern_mobile )
	);
}
?>
<main
	id="content"
	class="an-projects<?php echo $pattern_url ? ' an-projects--pattern' : ''; ?><?php echo $has_feed ? ' an-projects--feed' : ''; ?>"
	data-header-theme="dark"
	<?php if ( $main_style ) : ?>
		style="<?php echo esc_attr( $main_style ); ?>"
	<?php endif; ?>
>

	<?php if ( $pattern_url ) : ?>
		<div class="an-projects__pattern-base" aria-hidden="true"></div>
		<div class="an-projects__pattern-blend" aria-hidden="true"></div>
	<?php endif; ?>

	<?php if ( ! $has_feed ) : ?>
	<section class="an-projects-ambient" aria-label="<?php esc_attr_e( 'Projects', 'atelier-noir' ); ?>"></section>
	<?php else : ?>
	<div class="an-projects-feed" aria-label="<?php esc_attr_e( 'Projects', 'atelier-noir' ); ?>">
		<?php foreach ( $feed as $i => $section ) : ?>
			<section class="an-projects-feed__section">
				<?php if ( 'video' === $section['type'] ) : ?>
					<video class="an-projects-feed__media" autoplay muted loop playsinline>
						<source src="<?php echo esc_url( $section['url'] ); ?>" type="video/mp4">
					</video>
				<?php else : ?>
					<img
						class="an-projects-feed__media"
						src="<?php echo esc_url( $section['url'] ); ?>"
						alt="<?php echo esc_attr( $section['text'] ?? '' ); ?>"
						loading="<?php echo 0 === $i ? 'eager' : 'lazy'; ?>"
						decoding="async"
					>
				<?php endif; ?>
				<?php if ( ! empty( $section['text'] ) ) : ?>
					<p class="an-projects-feed__caption"><?php echo esc_html( $section['text'] ); ?></p>
				<?php endif; ?>
			</section>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<section class="an-projects-footer">
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
