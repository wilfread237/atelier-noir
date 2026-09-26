<?php
/**
 * Project — blog listing (formerly Journal).
 *
 * @package atelier-noir
 */

get_header();

$paged         = max( 1, (int) get_query_var( 'paged' ) );
$is_first_page = ( $paged <= 1 );
$total_posts   = (int) wp_count_posts( 'post' )->publish;
$total_pages   = function_exists( 'atelier_noir_project_total_pages' )
	? atelier_noir_project_total_pages( $total_posts )
	: 1;
?>

<main id="main" class="studio-journal studio-project<?php echo $is_first_page ? '' : ' studio-project--subpage'; ?>" role="main"<?php echo $is_first_page ? '' : ' data-header-theme="light"'; ?>>

	<?php if ( have_posts() ) : ?>

		<?php if ( $is_first_page ) : ?>
			<?php
			the_post();
			$featured_id   = get_the_ID();
			$featured_img  = get_the_post_thumbnail_url( $featured_id, 'full' );
			$featured_cats = get_the_category();
			$featured_cat  = ! empty( $featured_cats ) ? $featured_cats[0]->name : '';
			?>

			<section class="studio-journal__featured studio-section-full" id="post-<?php echo esc_attr( $featured_id ); ?>" data-header-theme="dark">
				<a href="<?php the_permalink(); ?>" class="studio-journal__featured-link" aria-label="<?php the_title_attribute(); ?>">
					<div class="studio-journal__featured-media"
						<?php if ( $featured_img ) : ?>
							style="background-image:url('<?php echo esc_url( $featured_img ); ?>')"
						<?php endif; ?>
						aria-hidden="true">
					</div>
					<div class="studio-journal__featured-body">
						<?php if ( $featured_cat ) : ?>
							<span class="studio-journal__cat"><?php echo esc_html( $featured_cat ); ?></span>
						<?php endif; ?>
						<h1 class="studio-journal__featured-title"><?php the_title(); ?></h1>
						<div class="studio-journal__featured-meta">
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
							</time>
							<span class="studio-journal__featured-read"><?php esc_html_e( 'Read', 'atelier-noir' ); ?> →</span>
						</div>
					</div>
				</a>
			</section>
		<?php endif; ?>

		<?php
		$grid_count = 0;
		if ( have_posts() ) :
			?>
		<div class="studio-journal__grid" data-header-theme="light">
			<?php
			while ( have_posts() ) :
				the_post();
				// Page 1: skip the featured post in the grid (already shown above).
				if ( $is_first_page && (int) get_the_ID() === (int) ( $featured_id ?? 0 ) ) {
					continue;
				}
				++$grid_count;
				$thumb = get_the_post_thumbnail_url( get_the_ID(), 'large' );
				$cats  = get_the_category();
				$cat   = ! empty( $cats ) ? $cats[0]->name : '';
				?>
				<article class="studio-journal__card" id="post-<?php the_ID(); ?>">
					<a href="<?php the_permalink(); ?>" class="studio-journal__card-link">
						<div class="studio-journal__card-media"
							<?php if ( $thumb ) : ?>
								style="background-image:url('<?php echo esc_url( $thumb ); ?>')"
							<?php endif; ?>
							aria-hidden="true">
						</div>
						<div class="studio-journal__card-body">
							<?php if ( $cat ) : ?>
								<span class="studio-journal__cat"><?php echo esc_html( $cat ); ?></span>
							<?php endif; ?>
							<h2 class="studio-journal__card-title"><?php the_title(); ?></h2>
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" class="studio-journal__card-date">
								<?php echo esc_html( get_the_date( 'F j, Y' ) ); ?>
							</time>
						</div>
					</a>
				</article>
			<?php endwhile; ?>
		</div>
			<?php
		endif;
		?>

		<?php if ( $total_pages > 1 ) : ?>
			<?php
			$posts_page_id = (int) get_option( 'page_for_posts', 0 );
			$base          = $posts_page_id
				? trailingslashit( get_permalink( $posts_page_id ) ) . 'page/%#%/'
				: str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) );

			$pagination = paginate_links(
				array(
					'base'      => $base,
					'total'     => $total_pages,
					'current'   => $paged,
					'mid_size'  => 0,
					'prev_text' => '←',
					'next_text' => '→',
					'type'      => 'list',
				)
			);
			if ( is_string( $pagination ) && '' !== trim( wp_strip_all_tags( $pagination ) ) ) {
				?>
		<nav class="studio-journal__pagination" aria-label="<?php esc_attr_e( 'Posts navigation', 'atelier-noir' ); ?>">
			<?php echo wp_kses_post( $pagination ); ?>
		</nav>
				<?php
			}
		endif;
		?>

	<?php else : ?>
		<p class="studio-journal__empty"><?php esc_html_e( 'No posts yet.', 'atelier-noir' ); ?></p>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
