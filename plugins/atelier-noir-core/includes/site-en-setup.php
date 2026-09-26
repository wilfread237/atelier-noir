<?php
/**
 * One-click English + USD site setup (admin).
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce + WordPress locale options.
 */
function atelier_noir_apply_wc_usd_options() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	update_option( 'woocommerce_currency', 'USD' );
	update_option( 'woocommerce_currency_pos', 'left' );
	update_option( 'woocommerce_price_decimal_sep', '.' );
	update_option( 'woocommerce_price_thousand_sep', ',' );
	update_option( 'woocommerce_price_num_decimals', '2' );
}

/**
 * Rename / migrate a page slug and title.
 *
 * @param string[] $old_slugs Candidate slugs (first match wins).
 * @param string   $new_slug  New slug (empty = keep current).
 * @param string   $title     New title.
 * @return int Page ID or 0.
 */
function atelier_noir_migrate_page( array $old_slugs, $new_slug, $title ) {
	foreach ( $old_slugs as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page && ! is_wp_error( $page ) ) {
			$update = array(
				'ID'         => (int) $page->ID,
				'post_title' => $title,
			);
			if ( $new_slug && $new_slug !== $slug ) {
				$update['post_name'] = $new_slug;
			}
			wp_update_post( $update );
			return (int) $page->ID;
		}
	}
	return 0;
}

/**
 * Apply English page titles/slugs, USD, re-import Elementor EN content, menu, demo posts/products.
 *
 * @return array{ok:bool, messages:string[]}
 */
function atelier_noir_apply_en_usd_setup() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return array(
			'ok'       => false,
			'messages' => array( 'Insufficient permissions.' ),
		);
	}

	$messages = array();

	// Site language (frontend filter already forces en_US in inc/brand.php).
	update_option( 'WPLANG', '' );

	atelier_noir_apply_wc_usd_options();
	$messages[] = 'WooCommerce currency set to USD.';

	// Page titles / slugs.
	$home_id = atelier_noir_migrate_page( array( 'accueil', 'home' ), 'home', 'Home' );
	if ( $home_id ) {
		$messages[] = 'Home page title updated.';
	}

	$project_id = atelier_noir_migrate_page( array( 'journal', 'project' ), 'project', 'Project' );
	if ( $project_id ) {
		update_option( 'page_for_posts', $project_id );
		$messages[] = 'Blog page renamed to Project (/project/).';
	}

	$shop_id = atelier_noir_migrate_page( array( 'boutique', 'shop' ), 'shop', 'Shop' );
	if ( $shop_id && class_exists( 'WooCommerce' ) ) {
		update_option( 'woocommerce_shop_page_id', $shop_id );
		$messages[] = 'Shop page slug set to /shop/.';
	}

	atelier_noir_migrate_page( array( 'services' ), 'services', 'Services' );
	atelier_noir_migrate_page( array( 'contact' ), 'contact', 'Contact' );

	// English customizer defaults (does not overwrite media URLs).
	if ( function_exists( 'atelier_noir_get_import_defaults' ) ) {
		foreach ( atelier_noir_get_import_defaults() as $key => $value ) {
			set_theme_mod( $key, $value );
		}
		$messages[] = 'Customizer defaults refreshed (English).';
	}

	if ( function_exists( 'atelier_noir_seed_services_json' ) ) {
		atelier_noir_seed_services_json();
		$messages[] = 'Services list seeded (Customizer repeater).';
	}

	// Re-apply Elementor pages, menu, posts, products (English kit).
	if ( function_exists( 'atelier_noir_import_full_site_content' ) ) {
		$result   = atelier_noir_import_full_site_content();
		$messages = array_merge( $messages, isset( $result['messages'] ) ? (array) $result['messages'] : array() );
	}

	flush_rewrite_rules( false );
	update_option( 'atelier_noir_en_usd_applied_at', time() );

	return array(
		'ok'       => true,
		'messages' => $messages,
	);
}

