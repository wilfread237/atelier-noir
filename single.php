<?php
/**
 * Article unique — Template éditorial Atelier Noir.
 *
 * Structure :
 *   1. Hero plein cadre (image ou fond sombre)
 *   2. Contenu article (typographie éditoriale)
 *   3. Footer article : retour journal + navigation précédent/suivant
 *
 * @package atelier-noir
 */

get_header();

while ( have_posts() ) :
	the_post();

	$thumb      = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	$cats       = get_the_category();
	$cat        = ! empty( $cats ) ? $cats[0]->name : '';
	$journal_url = get_permalink( (int) get_option( 'page_for_posts' ) );
	?>

	<main class="studio-post" id="post-<?php the_ID(); ?>" role="main">

		<!-- ── Hero article ──────────────────────────────────────────────────── -->
		<header class="studio-post__hero<?php echo $thumb ? ' studio-post__hero--has-image' : ''; ?>"
			<?php if ( $thumb ) : ?>
				style="background-image:url('<?php echo esc_url( $thumb ); ?>')"
			<?php endif; ?>>
			<div class="studio-post__hero-inner">
				<?php if ( $cat ) : ?>
					<a href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>"
						class="studio-post__cat">
						<?php echo esc_html( $cat ); ?>
					</a>
				<?php endif; ?>
				<h1 class="studio-post__title"><?php the_title(); ?></h1>
				<time class="studio-post__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
					<?php echo esc_html( get_the_date( 'j F Y' ) ); ?>
				</time>
			</div>
		</header>

		<!-- ── Contenu ───────────────────────────────────────────────────────── -->
		<div class="studio-post__content">
			<div class="studio-post__content-inner entry-content">
				<?php the_content(); ?>
				<?php
				wp_link_pages( array(
					'before' => '<nav class="studio-post__pages"><span>' . __( 'Pages:', 'atelier-noir' ) . '</span>',
					'after'  => '</nav>',
				) );
				?>
			</div>
		</div>

		<!-- ── Navigation inter-articles ─────────────────────────────────────── -->
		<nav class="studio-post__nav" aria-label="<?php esc_attr_e( 'Post navigation', 'atelier-noir' ); ?>">
			<div class="studio-post__nav-prev">
				<?php previous_post_link( '%link', '← %title' ); ?>
			</div>
			<div class="studio-post__nav-next">
				<?php next_post_link( '%link', '%title →' ); ?>
			</div>
		</nav>

		<!-- ── Lien retour ───────────────────────────────────────────────────── -->
		<footer class="studio-post__footer">
			<a href="<?php echo esc_url( $journal_url ); ?>" class="studio-post__back">
			<?php esc_html_e( '← Back to Project', 'atelier-noir' ); ?>
			</a>
		</footer>

	</main>

	<?php
endwhile;

get_footer();
