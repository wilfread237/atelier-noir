<?php
/**
 * WooCommerce USD-only + attachment URL helper.
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filtre wp_get_attachment_url : retourne l'URL externe directement
 * quand _wp_attached_file est une URL absolue (ex. visuels Unsplash en import).
 */
add_filter( 'wp_get_attachment_url', function ( $url, $post_id ) {
	$file = get_post_meta( $post_id, '_wp_attached_file', true );
	if ( is_string( $file ) && ( str_starts_with( $file, 'https://' ) || str_starts_with( $file, 'http://' ) ) ) {
		return $file;
	}
	return $url;
}, 10, 2 );

/**
 * Force USD store currency (commerce enabled only).
 */
function atelier_noir_currency_filters() {
	if ( ! function_exists( 'atelier_noir_is_commerce_enabled' ) || ! atelier_noir_is_commerce_enabled() ) {
		return;
	}

	add_filter( 'option_woocommerce_currency', function () {
		return 'USD';
	} );

	add_filter( 'woocommerce_currency_symbol', function () {
		return '$';
	} );

	add_filter( 'pre_option_woocommerce_currency_pos', function () {
		return 'left';
	} );

	add_filter( 'pre_option_woocommerce_price_num_decimals', function () {
		return 2;
	} );
}
add_action( 'init', 'atelier_noir_currency_filters', 5 );
