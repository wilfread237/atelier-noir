<?php
/**
 * Optional WooCommerce module (dormant by default for client site).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return bool
 */
function atelier_noir_is_commerce_enabled() {
	return (bool) atelier_noir_get_theme_mod( 'an_enable_commerce', false )
		&& class_exists( 'WooCommerce' );
}

/**
 * Load WooCommerce theme integrations only when commerce is enabled.
 */
function atelier_noir_maybe_load_commerce_modules() {
	if ( ! atelier_noir_is_commerce_enabled() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	require_once $dir . '/inc/woocommerce-shop.php';
	require_once $dir . '/inc/woocommerce-product.php';
}
add_action( 'after_setup_theme', 'atelier_noir_maybe_load_commerce_modules', 15 );

/**
 * Customizer toggle for dormant commerce.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function atelier_noir_customize_commerce( $wp_customize ) {
	$wp_customize->add_section(
		'an_commerce',
		array(
			'title'       => __( 'Atelier Noir — Commerce', 'atelier-noir' ),
			'description' => __( 'Enable WooCommerce storefront (Shop, cart, checkout). Off by default for editorial sites.', 'atelier-noir' ),
			'priority'    => 33,
		)
	);

	$wp_customize->add_setting(
		'an_enable_commerce',
		array(
			'default'           => false,
			'sanitize_callback' => function ( $value ) {
				return (bool) $value;
			},
		)
	);
	$wp_customize->add_control(
		'an_enable_commerce',
		array(
			'label'   => __( 'Enable WooCommerce storefront', 'atelier-noir' ),
			'section' => 'an_commerce',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'atelier_noir_customize_commerce', 38 );
