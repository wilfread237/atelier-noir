<?php
/**
 * WooCommerce single product + search empty state.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Remove product tabs (description, additional info, reviews). */
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
	unset( $tabs['description'], $tabs['additional_information'], $tabs['reviews'] );
	return $tabs;
}, 98 );

/**
 * Map legacy French global attribute labels to English on the storefront.
 *
 * @param string     $label   Attribute label.
 * @param string     $name    Attribute name or slug (pa_color, pa_size, …).
 * @param WC_Product $product Product object.
 */
function atelier_noir_translate_attribute_label( $label, $name, $product ) {
	$slug_map = array(
		'pa_couleur' => 'Color',
		'pa_taille'  => 'Size',
		'pa_color'   => 'Color',
		'pa_size'    => 'Size',
	);

	if ( isset( $slug_map[ $name ] ) ) {
		return __( $slug_map[ $name ], 'atelier-noir' );
	}

	$label_map = array(
		'couleur' => 'Color',
		'taille'  => 'Size',
	);

	$key = strtolower( trim( $label ) );
	if ( isset( $label_map[ $key ] ) ) {
		return __( $label_map[ $key ], 'atelier-noir' );
	}

	return $label;
}
add_filter( 'woocommerce_attribute_label', 'atelier_noir_translate_attribute_label', 10, 3 );

/** Custom empty product search results. */
add_action( 'woocommerce_no_products_found', function () {
	if ( ! is_search() ) {
		return;
	}
	$query   = get_search_query();
	$contact = atelier_noir_email_contact();
	?>
	<div class="studio-shop-empty">
		<p class="studio-shop-empty__results">
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'RESULTS FOR \'%s\' (%d)', 'atelier-noir' ),
				esc_html( $query ),
				0
			);
			?>
		</p>
		<p class="studio-shop-empty__lead"><?php esc_html_e( 'UNFORTUNATELY THERE ARE NO RESULTS MATCHING YOUR SEARCH.', 'atelier-noir' ); ?></p>
		<p class="studio-shop-empty__contact">
			<?php
			printf(
				/* translators: %s: contact email */
				esc_html__( 'FEEL FREE TO CONTACT CLIENT SERVICE BY EMAIL AT %s.', 'atelier-noir' ),
				esc_html( $contact )
			);
			?>
		</p>
		<p class="studio-shop-empty__assist"><?php esc_html_e( 'IT WILL BE OUR PLEASURE TO ASSIST YOU.', 'atelier-noir' ); ?></p>
		<p class="studio-shop-empty__again"><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'SEARCH AGAIN', 'atelier-noir' ); ?></a></p>
	</div>
	<?php
}, 5 );

remove_action( 'woocommerce_no_products_found', 'wc_no_products_found', 10 );
