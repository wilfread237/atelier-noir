<?php
/**
 * Content Product — Studio Theme Child
 * Carte luxe : ratio 3/4 full-bleed, flèches pour déclinaisons.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */
defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

// ── Données produit ──────────────────────────────────────────────────────────
$product_id  = $product->get_id();
$is_variable = $product->is_type( 'variable' );
$permalink   = $product->get_permalink();

// ── Collecte des images (principale + galerie + variations) ──────────────────
$main_id     = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();
$image_ids   = array_filter( array_merge( [ $main_id ], $gallery_ids ) );

if ( $main_id && function_exists( 'atelier_noir_product_needs_demo_images' )
	&& atelier_noir_product_needs_demo_images( $product_id ) ) {
	$image_ids = array();
}

// URLs de secours (import démo si sideload impossible).
$fallback_urls = get_post_meta( $product_id, '_atelier_noir_fallback_image_urls', true );
if ( ! is_array( $fallback_urls ) || empty( $fallback_urls ) ) {
	$fallback_urls = function_exists( 'atelier_noir_demo_product_image_urls_for_slug' )
		? atelier_noir_demo_product_image_urls_for_slug( $product->get_slug() )
		: array();
}

// Pour les produits variables : récupérer les images de variations via
// get_children (IDs seulement) — évite get_available_variations() qui
// déclenche N requêtes lourdes par produit dans la boucle archive.
if ( $is_variable ) {
	$children = $product->get_children(); // IDs des variations, pas de requête lourde
	foreach ( $children as $child_id ) {
		$var_img_id = (int) get_post_thumbnail_id( $child_id );
		if ( $var_img_id && ! in_array( $var_img_id, $image_ids, true ) ) {
			$image_ids[] = $var_img_id;
		}
	}
}

$has_multiple_images = count( $image_ids ) > 1;
// Flèches : navigation entre vues / déclinaisons quand plusieurs images.
$show_arrows = $is_variable || $has_multiple_images;

$price_html = $product->get_price_html();
if ( is_front_page() && $is_variable ) {
	$min_price = $product->get_variation_price( 'min', true );
	if ( is_numeric( $min_price ) ) {
		$price_html = wc_price( $min_price );
	}
}

// ── Placeholder local WooCommerce (disponible même hors connexion) ───────────
$placeholder_src = wc_placeholder_img_src( 'woocommerce_single' );
?>
<li <?php wc_product_class( 'studio-product-card', $product ); ?>>

	<!-- ── Zone image full-bleed ─────────────────────────────────────────── -->
	<a href="<?php echo esc_url( $permalink ); ?>" class="studio-product-card__media-link" tabindex="-1" aria-hidden="true">
		<div class="studio-product-card__media">

			<?php if ( ! empty( $image_ids ) ) : ?>
				<?php foreach ( array_values( $image_ids ) as $i => $img_id ) : ?>
					<div class="studio-product-card__slide<?php echo 0 === $i ? ' is-active' : ''; ?>"
						aria-hidden="<?php echo 0 === $i ? 'false' : 'true'; ?>">
						<?php echo wp_get_attachment_image( $img_id, 'woocommerce_single', false, [
							'class'   => 'studio-product-card__img',
							'loading' => 0 === $i ? 'eager' : 'lazy',
							'alt'     => esc_attr( $product->get_name() ),
						] ); ?>
					</div>
				<?php endforeach; ?>
			<?php elseif ( ! empty( $fallback_urls ) ) : ?>
				<?php foreach ( array_values( $fallback_urls ) as $i => $img_url ) : ?>
					<?php if ( ! is_string( $img_url ) || '' === $img_url ) {
						continue;
					} ?>
					<div class="studio-product-card__slide<?php echo 0 === $i ? ' is-active' : ''; ?>"
						aria-hidden="<?php echo 0 === $i ? 'false' : 'true'; ?>">
						<img class="studio-product-card__img"
							src="<?php echo esc_url( $img_url ); ?>"
							alt="<?php echo esc_attr( $product->get_name() ); ?>"
							loading="<?php echo 0 === $i ? 'eager' : 'lazy'; ?>">
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<!-- Fallback : placeholder local WooCommerce -->
				<div class="studio-product-card__slide is-active" aria-hidden="false">
					<img class="studio-product-card__img studio-product-card__img--placeholder"
						src="<?php echo esc_url( $placeholder_src ); ?>"
						alt="<?php echo esc_attr( $product->get_name() ); ?>"
						loading="eager">
				</div>
			<?php endif; ?>

			<!-- Badge Solde -->
			<?php if ( $product->is_on_sale() ) : ?>
				<span class="studio-product-card__badge"><?php esc_html_e( 'Sale', 'atelier-noir' ); ?></span>
			<?php endif; ?>

			<!-- Overlay hover avec CTA -->
			<div class="studio-product-card__overlay" aria-hidden="true">
				<span class="studio-product-card__overlay-cta">
					<?php echo $is_variable
						? esc_html__( 'Choose options', 'atelier-noir' )
						: esc_html__( 'View product', 'atelier-noir' ); ?>
				</span>
			</div>

			<?php if ( $show_arrows ) : ?>
				<button type="button" class="studio-product-card__arrow studio-product-card__arrow--prev"
					aria-label="<?php esc_attr_e( 'Previous variation', 'atelier-noir' ); ?>"
					tabindex="-1">&#8249;</button>
				<button type="button" class="studio-product-card__arrow studio-product-card__arrow--next"
					aria-label="<?php esc_attr_e( 'Next variation', 'atelier-noir' ); ?>"
					tabindex="-1">&#8250;</button>
			<?php endif; ?>

		</div><!-- .studio-product-card__media -->
	</a>

	<!-- ── Infos produit ─────────────────────────────────────────────────── -->
	<div class="studio-product-card__info">
		<a href="<?php echo esc_url( $permalink ); ?>" class="studio-product-card__name">
			<?php echo esc_html( $product->get_name() ); ?>
		</a>
		<span class="studio-product-card__price">
			<?php echo wp_kses_post( $price_html ); ?>
		</span>
	</div>

</li>
