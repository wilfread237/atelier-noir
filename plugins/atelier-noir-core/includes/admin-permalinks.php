<?php
/**
 * Admin — flush permalinks for Project pagination (/project/page/N/).
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle one-click permalink flush.
 */
function atelier_noir_handle_permalink_flush_action() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( empty( $_GET['atelier_noir_flush_permalinks'] ) || empty( $_GET['_wpnonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'atelier_noir_flush_permalinks' ) ) {
		return;
	}

	if ( function_exists( 'atelier_noir_register_posts_page_pagination_rewrite' ) ) {
		atelier_noir_register_posts_page_pagination_rewrite();
	}
	flush_rewrite_rules( false );
	update_option( 'atelier_noir_permalinks_notice_dismissed', '1', false );

	$redirect = wp_get_referer() ? wp_get_referer() : admin_url();
	wp_safe_redirect( add_query_arg( 'atelier_noir_permalinks_flushed', '1', $redirect ) );
	exit;
}
add_action( 'admin_init', 'atelier_noir_handle_permalink_flush_action' );

/**
 * @return string
 */
function atelier_noir_permalink_flush_url() {
	return wp_nonce_url(
		add_query_arg( 'atelier_noir_flush_permalinks', '1', admin_url( 'index.php' ) ),
		'atelier_noir_flush_permalinks'
	);
}

/**
 * Dashboard notice with one-click flush.
 */
function atelier_noir_permalink_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! empty( $_GET['atelier_noir_permalinks_flushed'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Permalinks refreshed. Project pagination URLs should work now.', 'atelier-noir-core' ) . '</p></div>';
		return;
	}

	if ( get_option( 'atelier_noir_permalinks_notice_dismissed' ) ) {
		return;
	}

	$page_id = (int) get_option( 'page_for_posts', 0 );
	if ( $page_id <= 0 ) {
		return;
	}

	$slug = get_post_field( 'post_name', $page_id );
	if ( ! is_string( $slug ) || '' === $slug ) {
		return;
	}

	printf(
		'<div class="notice notice-info"><p>%1$s <a class="button button-secondary" href="%2$s" style="margin-left:0.5rem;">%3$s</a></p></div>',
		esc_html(
			sprintf(
				/* translators: %s: posts page slug */
				__( 'Atelier Noir: if /%s/page/2/ returns a 404, refresh permalinks once.', 'atelier-noir-core' ),
				$slug
			)
		),
		esc_url( atelier_noir_permalink_flush_url() ),
		esc_html__( 'Refresh permalinks', 'atelier-noir-core' )
	);
}
add_action( 'admin_notices', 'atelier_noir_permalink_admin_notice' );
