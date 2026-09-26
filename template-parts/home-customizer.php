<?php
/**
 * Accueil piloté par le Personnaliseur (textes, médias, options).
 *
 * @package atelier-noir
 */

$video    = apply_filters( 'atelier_noir_hero_video_url', get_theme_mod( 'atelier_noir_eyes_video_url', '' ) );
$poster   = get_theme_mod( 'atelier_noir_eyes_poster_url', '' );
$yt_embed = function_exists( 'atelier_noir_is_youtube_url' ) && atelier_noir_is_youtube_url( $video )
	? atelier_noir_youtube_embed_url( $video )
	: '';
$kicker  = get_theme_mod( 'atelier_noir_eyes_kicker', '' );
$hero    = function_exists( 'atelier_noir_get_eyes_hero_copy' ) ? atelier_noir_get_eyes_hero_copy() : array( 'line1' => '', 'line2' => '', 'tagline' => '' );
$img_f   = get_theme_mod( 'atelier_noir_founder_image_url', '' );
$lab     = get_theme_mod( 'atelier_noir_studio_label', '' );
$tit     = get_theme_mod( 'atelier_noir_studio_title', '' );
$txt     = get_theme_mod( 'atelier_noir_studio_text', '' );
$cta_l   = get_theme_mod( 'atelier_noir_studio_cta_label', '' );
$cta_u   = get_theme_mod( 'atelier_noir_studio_cta_url', '' );
?>
<div class="studio-home-customizer">
	<section class="studio-home-fallback__eyes studio-eyes-catcher js-reveal" id="accueil-eyes">
		<div class="studio-eyes-catcher__media" aria-hidden="true">
			<?php if ( $yt_embed ) : ?>
				<iframe
					class="studio-eyes-catcher__iframe"
					src="<?php echo esc_url( $yt_embed ); ?>"
					title="<?php esc_attr_e( 'Homepage hero video', 'atelier-noir' ); ?>"
					allow="autoplay; encrypted-media; picture-in-picture"
					allowfullscreen
					loading="eager"
					tabindex="-1"
				></iframe>
			<?php elseif ( $video ) : ?>
				<video
					class="studio-eyes-catcher__video"
					autoplay
					muted
					loop
					playsinline
					<?php
					if ( $poster ) {
						echo ' poster="' . esc_url( $poster ) . '"';
					}
					?>
				>
					<source src="<?php echo esc_url( $video ); ?>" type="video/mp4">
				</video>
			<?php else : ?>
				<div class="studio-eyes-catcher__placeholder">
					<p class="studio-eyes-catcher__placeholder-label"><?php esc_html_e( 'Add the homepage video URL in Customizer (Atelier Noir — Home: Eyes catcher).', 'atelier-noir' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<div class="studio-eyes-catcher__overlay">
			<div class="container">
				<?php if ( $kicker ) : ?>
					<p class="studio-eyes-catcher__kicker js-text-reveal"><?php echo esc_html( $kicker ); ?></p>
				<?php endif; ?>
				<?php if ( $hero['line1'] || $hero['line2'] ) : ?>
					<h1 class="studio-eyes-catcher__overlay-title js-text-reveal">
						<?php if ( $hero['line1'] ) : ?>
							<span class="studio-eyes-catcher__title-line"><?php echo esc_html( $hero['line1'] ); ?></span>
						<?php endif; ?>
						<?php if ( $hero['line2'] ) : ?>
							<span class="studio-eyes-catcher__title-line"><?php echo esc_html( $hero['line2'] ); ?></span>
						<?php endif; ?>
					</h1>
				<?php endif; ?>
				<?php if ( ! empty( $hero['tagline'] ) ) : ?>
					<p class="studio-eyes-catcher__tagline js-text-reveal"><?php echo esc_html( $hero['tagline'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="studio-home-fallback__studio studio-studio-founder section js-reveal" id="accueil-studio">
		<div class="container studio-studio-founder__grid">
			<figure class="studio-studio-founder__figure">
				<?php if ( $img_f ) : ?>
					<img class="studio-studio-founder__img" src="<?php echo esc_url( $img_f ); ?>" alt="" loading="lazy" width="900" height="1200">
				<?php else : ?>
					<div class="studio-studio-founder__photo" role="img" aria-hidden="true"></div>
				<?php endif; ?>
			</figure>
			<div class="studio-studio-founder__content">
				<?php if ( $lab ) : ?>
					<p class="section-label"><?php echo esc_html( $lab ); ?></p>
				<?php endif; ?>
				<?php if ( $tit ) : ?>
					<h2 class="section-title js-text-reveal"><?php echo esc_html( $tit ); ?></h2>
				<?php endif; ?>
				<?php if ( $txt ) : ?>
					<div class="studio-studio-founder__text"><?php echo wp_kses_post( wpautop( $txt ) ); ?></div>
				<?php endif; ?>
				<?php if ( $cta_l && $cta_u ) : ?>
					<a class="button studio-studio-founder__cta" href="<?php echo esc_url( $cta_u ); ?>"><?php echo esc_html( $cta_l ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php if ( get_theme_mod( 'atelier_noir_home_show_journal', false ) ) : ?>
		<section class="studio-home-customizer__journal section js-reveal">
			<div class="container">
				<p class="section-label"><?php esc_html_e( 'Project', 'atelier-noir' ); ?></p>
				<h2 class="section-title js-text-reveal"><?php esc_html_e( 'News & projects', 'atelier-noir' ); ?></h2>
				<div class="home-journal__grid">
					<?php
					$q = new WP_Query(
						array(
							'post_type'      => 'post',
							'posts_per_page' => 3,
						)
					);
					if ( $q->have_posts() ) :
						while ( $q->have_posts() ) :
							$q->the_post();
							?>
							<article <?php post_class( 'journal-card' ); ?>>
								<a href="<?php the_permalink(); ?>">
									<h3 class="journal-card__title"><?php the_title(); ?></h3>
									<p class="journal-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
								</a>
							</article>
							<?php
						endwhile;
						wp_reset_postdata();
					else :
						?>
						<p><?php esc_html_e( 'Publish posts to populate this section.', 'atelier-noir' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( get_theme_mod( 'atelier_noir_home_show_shop_teaser', false ) && class_exists( 'WooCommerce' ) ) : ?>
		<section class="studio-home-customizer__merch section js-reveal">
			<div class="container">
				<p class="section-label"><?php esc_html_e( 'Shop', 'atelier-noir' ); ?></p>
				<h2 class="section-title js-text-reveal"><?php esc_html_e( 'Selection', 'atelier-noir' ); ?></h2>
				<div class="home-merch__grid">
					<?php echo do_shortcode( '[products limit="3" columns="3" orderby="date" order="DESC"]' ); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<p class="studio-home-fallback__hint container">
		<?php esc_html_e( 'Homepage texts and media: Appearance → Customize (Studio sections). Theme docs: Appearance → Studio · Documentation.', 'atelier-noir' ); ?>
	</p>
</div>
