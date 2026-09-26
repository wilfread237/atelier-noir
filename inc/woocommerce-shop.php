<?php
/**
 * Personnalisations de la boutique WooCommerce — Atelier Noir.
 *
 * - Grille 3 colonnes, 6 produits / page (2 lignes), pagination WooCommerce native
 * - Suppression des éléments UI par défaut (tri, compteur)
 * - Icône recherche produit dans l'en-tête de la page boutique
 * - Pas de fil d’Ariane WooCommerce sur tout le catalogue (boutique, catégories, étiquettes, archives attributs)
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Forcer 3 colonnes, 6 produits par page (2 rangées de 3). La pagination s’affiche dès qu’il y a plus de 6 produits. */
add_filter( 'loop_shop_columns',  function () { return 3; },  999 );
add_filter( 'loop_shop_per_page', function () { return 6; }, 20 );

/** Masquer le titre « Shop » répétitif (le menu suffit). */
add_filter( 'woocommerce_show_page_title', '__return_false' );

/** Supprimer le compteur de résultats et le sélecteur de tri natifs. */
add_action( 'init', function () {
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
} );

/**
 * Retire le fil d’Ariane WooCommerce sur les vues « liste catalogue » (pas sur fiche produit, panier, checkout, compte).
 * Ne provoque pas de bug fonctionnel : c’est uniquement de l’affichage. Le SEO peut toujours s’appuyer sur un plugin
 * (Yoast, etc.) pour les données structurées si tu les configures séparément.
 */
add_action(
	'wp',
	function () {
		if ( ! function_exists( 'is_woocommerce' ) || ! is_woocommerce() ) {
			return;
		}
		if ( function_exists( 'is_cart' ) && is_cart() ) {
			return;
		}
		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return;
		}
		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			return;
		}
		if ( function_exists( 'is_product' ) && is_product() ) {
			return;
		}
		$catalog_view = ( function_exists( 'is_shop' ) && is_shop() )
			|| ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() );
		if ( ! $catalog_view ) {
			return;
		}
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	},
	5
);

/**
 * Icône de recherche dans l'en-tête de la page boutique.
 * Positionnée en justify-between avec le titre via CSS.
 */
function atelier_noir_shop_search_icon() {
	if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
		return;
	}
	?>
	<div class="studio-shop-search-wrap">
		<button
			class="studio-shop-search-toggle"
			aria-label="<?php esc_attr_e( 'Search for a product', 'atelier-noir' ); ?>"
			aria-expanded="false"
			aria-controls="studio-shop-search-bar"
			type="button"
		>
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
				stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"
				aria-hidden="true">
				<circle cx="11" cy="11" r="8"/>
				<line x1="21" y1="21" x2="16.65" y2="16.65"/>
			</svg>
		</button>
		<div id="studio-shop-search-bar" class="studio-shop-search-bar" aria-hidden="true">
			<label class="screen-reader-text" for="studio-shop-search-input"><?php esc_html_e( 'Search for a product', 'atelier-noir' ); ?></label>
			<input
				type="search"
				id="studio-shop-search-input"
				class="studio-shop-search-input"
				placeholder="<?php esc_attr_e( 'Search…', 'atelier-noir' ); ?>"
				autocomplete="off"
				aria-label="<?php esc_attr_e( 'Search for a product', 'atelier-noir' ); ?>"
			>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_archive_description', 'atelier_noir_shop_search_icon', 20 );

/**
 * Message « aucun résultat » (filtrage live JS).
 */
function atelier_noir_shop_no_results_message() {
	if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
		return;
	}
	echo '<p id="studio-shop-no-results" class="studio-shop-no-results" hidden>';
	esc_html_e( 'No products match your search.', 'atelier-noir' );
	echo '</p>';
}
add_action( 'woocommerce_before_shop_loop', 'atelier_noir_shop_no_results_message', 5 );

/**
 * Home shop teaser markup (products inside editorial rail layout).
 *
 * @return string
 */
function atelier_noir_render_home_merch_teaser() {
	$shop_url = function_exists( 'wc_get_page_permalink' )
		? wc_get_page_permalink( 'shop' )
		: home_url( '/shop/' );

	$products_html = '';
	if ( class_exists( 'WooCommerce' ) ) {
		$products_html = do_shortcode( '[products limit="3" columns="3" orderby="date" order="DESC"]' );
	}

	return sprintf(
		'<div class="studio-merch-editorial"><span class="studio-merch-editorial__rail" aria-hidden="true">%s</span><div class="studio-merch-editorial__products">%s</div><p class="studio-merch-editorial__cta-wrap"><a href="%s" class="studio-merch-cta studio-merch-cta--filled">%s</a></p></div>',
		esc_html( strtoupper( atelier_noir_brand_name() ) ),
		$products_html,
		esc_url( $shop_url ),
		esc_html__( 'View the collection', 'atelier-noir' )
	);
}

/**
 * Shortcode for Elementor home section — keeps product grid inside .studio-merch-editorial__products.
 *
 * @return string
 */
function atelier_noir_shortcode_home_merch_teaser() {
	return atelier_noir_render_home_merch_teaser();
}
add_shortcode( 'atelier_noir_home_merch_teaser', 'atelier_noir_shortcode_home_merch_teaser' );

/**
 * Checkout — country autocomplete (searchable list, default = detected / store country).
 */
function atelier_noir_enqueue_checkout_country() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$js  = $dir . '/assets/js/checkout-country.js';

	if ( ! file_exists( $js ) ) {
		return;
	}

	wp_enqueue_script(
		'studio-checkout-country',
		$uri . '/assets/js/checkout-country.js',
		array(),
		(string) filemtime( $js ),
		true
	);

	$customer_country = '';
	if ( WC()->customer ) {
		$customer_country = WC()->customer->get_billing_country();
	}
	if ( '' === $customer_country ) {
		$customer_country = WC()->countries->get_base_country();
	}

	wp_localize_script(
		'studio-checkout-country',
		'studioCheckoutCountry',
		array(
			'countries' => WC()->countries->get_allowed_countries(),
			'default'   => $customer_country,
			'label'     => __( 'Country / Region', 'atelier-noir' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'atelier_noir_enqueue_checkout_country', 25 );
