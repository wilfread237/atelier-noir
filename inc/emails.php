<?php
/**
 * Centralized email addresses (default: Settings → General → Administration Email Address).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contact, services, claims, general inquiries.
 */
function atelier_noir_email_contact() {
	return apply_filters( 'atelier_noir_email_contact', (string) get_option( 'admin_email' ) );
}

/**
 * Orders, commerce, transactional WooCommerce.
 */
function atelier_noir_email_orders() {
	return apply_filters( 'atelier_noir_email_orders', atelier_noir_email_contact() );
}

/**
 * Newsletter signup is handled by the Atelier Noir Core plugin.
 *
 * @return bool
 */
function atelier_noir_newsletter_available() {
	return (bool) has_action( 'wp_ajax_nopriv_atelier_noir_newsletter_subscribe' );
}
