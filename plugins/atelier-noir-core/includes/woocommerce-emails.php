<?php
/**
 * Configuration des emails WooCommerce — Atelier Noir.
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce email IDs routed via the orders address.
 *
 * @return string[]
 */
function atelier_noir_wc_email_orders_ids() {
	return array(
		'new_order',
		'cancelled_order',
		'failed_order',
		'customer_processing_order',
		'customer_completed_order',
		'customer_on_hold_order',
		'customer_refunded_order',
		'customer_partially_refunded_order',
		'customer_cancelled_order',
		'customer_failed_order',
		'customer_invoice',
		'customer_note',
		'customer_review_request',
		'customer_fulfillment_created',
		'customer_fulfillment_updated',
		'customer_fulfillment_deleted',
		'customer_pos_completed_order',
		'customer_pos_refunded_order',
	);
}

/**
 * Account / service emails → contact address.
 *
 * @return string[]
 */
function atelier_noir_wc_email_contact_ids() {
	return array(
		'customer_new_account',
		'customer_reset_password',
	);
}

/**
 * @param object $email_obj WC_Email instance.
 * @return bool
 */
function atelier_noir_wc_email_uses_orders_address( $email_obj ) {
	if ( ! is_object( $email_obj ) || empty( $email_obj->id ) ) {
		return false;
	}
	if ( in_array( $email_obj->id, atelier_noir_wc_email_contact_ids(), true ) ) {
		return false;
	}
	return in_array( $email_obj->id, atelier_noir_wc_email_orders_ids(), true );
}

add_filter( 'woocommerce_email_from_address', function ( $from_email, $email_obj ) {
	return atelier_noir_wc_email_uses_orders_address( $email_obj )
		? atelier_noir_email_orders()
		: atelier_noir_email_contact();
}, 10, 2 );

add_filter( 'woocommerce_email_from_name', function () {
	return atelier_noir_brand_name();
} );

add_filter( 'woocommerce_new_order_email_allows_resend', '__return_true' );

/**
 * Force admin notification recipients for commerce emails.
 */
function atelier_noir_wc_email_force_admin_recipient( $recipient, $order, $email ) {
	if ( is_object( $email ) && atelier_noir_wc_email_uses_orders_address( $email ) ) {
		return atelier_noir_email_orders();
	}
	return $recipient;
}

add_filter( 'woocommerce_email_recipient_new_order', 'atelier_noir_wc_email_force_admin_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_cancelled_order', 'atelier_noir_wc_email_force_admin_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_failed_order', 'atelier_noir_wc_email_force_admin_recipient', 10, 3 );

add_filter( 'option_woocommerce_new_order_settings', function ( $settings ) {
	if ( is_array( $settings ) ) {
		$settings['recipient'] = atelier_noir_email_orders();
	}
	return $settings;
} );

add_filter( 'option_woocommerce_cancelled_order_settings', function ( $settings ) {
	if ( is_array( $settings ) ) {
		$settings['recipient'] = atelier_noir_email_orders();
	}
	return $settings;
} );

add_filter( 'option_woocommerce_failed_order_settings', function ( $settings ) {
	if ( is_array( $settings ) ) {
		$settings['recipient'] = atelier_noir_email_orders();
	}
	return $settings;
} );

add_filter( 'woocommerce_email_styles', function ( $css ) {
	return $css . '
body { background-color: #f5f3ef !important; }
#wrapper { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important; }
#template_header { background-color: #0a0a0a !important; border-radius: 0 !important; }
#template_header h1,
#template_header h1 a { color: #ffffff !important; font-size: 22px !important;
	font-weight: 400 !important; letter-spacing: 0.08em !important; }
.email-logo-text { color: #0a0a0a !important; font-size: 15px !important;
	font-weight: 500 !important; letter-spacing: 0.12em !important;
	text-transform: uppercase !important; padding: 20px 0 0 !important; }
#body_content { background-color: #ffffff !important; }
#body_content_inner { font-size: 14px !important; line-height: 1.8 !important; color: #1e1e1e !important; }
#template_footer #credit { font-size: 11px !important; letter-spacing: 0.06em !important; color: #787c82 !important; }
a { color: #0a0a0a !important; text-decoration: underline !important; }
th { background-color: #0a0a0a !important; color: #ffffff !important; }
.button, td.order-actions .button { background-color: #0a0a0a !important;
	color: #ffffff !important; border-radius: 0 !important;
	font-size: 11px !important; letter-spacing: 0.1em !important;
	text-transform: uppercase !important; padding: 10px 24px !important; }
';
}, 20 );
