<?php
/**
 * Custom fulfillment status for Studio internal delivery (no carrier tracking).
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customer-facing fulfillment steps (admin selects one per order).
 *
 * @return array<string, string>
 */
function atelier_noir_fulfillment_statuses() {
	return array(
		'received'           => __( 'Order received', 'atelier-noir-core' ),
		'in_production'      => __( 'In production', 'atelier-noir-core' ),
		'preparing_delivery' => __( 'Preparing Studio delivery', 'atelier-noir-core' ),
		'out_for_delivery'   => __( 'Out for delivery (Studio team)', 'atelier-noir-core' ),
		'delivered'          => __( 'Delivered', 'atelier-noir-core' ),
	);
}

/**
 * @param WC_Order $order Order.
 * @return string Status key.
 */
function atelier_noir_get_order_fulfillment_status( $order ) {
	if ( ! $order instanceof WC_Order ) {
		return 'received';
	}

	$custom = $order->get_meta( '_atelier_noir_fulfillment_status', true );

	if ( is_string( $custom ) && '' !== $custom ) {
		$legacy_map = array(
			'preparing_shipment' => 'preparing_delivery',
			'shipped'            => 'out_for_delivery',
			'in_delivery'        => 'out_for_delivery',
		);
		if ( isset( $legacy_map[ $custom ] ) ) {
			$custom = $legacy_map[ $custom ];
		}
		if ( array_key_exists( $custom, atelier_noir_fulfillment_statuses() ) ) {
			return $custom;
		}
	}

	$map = array(
		'pending'    => 'received',
		'on-hold'    => 'received',
		'processing' => 'in_production',
		'completed'  => 'delivered',
		'cancelled'  => 'received',
		'refunded'   => 'received',
		'failed'     => 'received',
	);

	$wc_status = $order->get_status();
	return $map[ $wc_status ] ?? 'received';
}

/**
 * @param WC_Order $order Order.
 * @return string
 */
function atelier_noir_get_order_fulfillment_label( $order ) {
	$key = atelier_noir_get_order_fulfillment_status( $order );
	$all = atelier_noir_fulfillment_statuses();
	return $all[ $key ] ?? $key;
}

/**
 * Admin order screen — status shown to the customer.
 *
 * @param WC_Order $order Order.
 */
function atelier_noir_admin_order_fulfillment_fields( $order ) {
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	$statuses = atelier_noir_fulfillment_statuses();
	$current  = $order->get_meta( '_atelier_noir_fulfillment_status', true );
	if ( ! is_string( $current ) || ! array_key_exists( $current, $statuses ) ) {
		$current = atelier_noir_get_order_fulfillment_status( $order );
	}

	echo '<div class="studio-admin-order-fulfillment" style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid #dcdcde;">';
	echo '<h3 style="margin:0 0 0.75rem;">' . esc_html__( 'Customer order status (Studio)', 'atelier-noir-core' ) . '</h3>';
	echo '<p class="form-field form-field-wide">';
	echo '<label for="atelier_noir_fulfillment_status">' . esc_html__( 'Status shown to the customer', 'atelier-noir-core' ) . '</label>';
	echo '<select id="atelier_noir_fulfillment_status" name="atelier_noir_fulfillment_status" style="width:100%;">';
	foreach ( $statuses as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '"' . selected( $current, $key, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></p>';
	echo '<p class="description">' . esc_html__( 'Update as your team progresses (production → Studio delivery → delivered). The customer sees this in My account → Orders.', 'atelier-noir-core' ) . '</p>';
	echo '</div>';
}
add_action( 'woocommerce_admin_order_data_after_order_details', 'atelier_noir_admin_order_fulfillment_fields', 20 );

/**
 * Save admin fulfillment status.
 *
 * @param int $order_id Order ID.
 */
function atelier_noir_save_admin_order_fulfillment_fields( $order_id ) {
	if ( ! is_admin() || ! current_user_can( 'edit_shop_orders' ) ) {
		return;
	}
	if ( ! isset( $_POST['atelier_noir_fulfillment_status'] ) ) {
		return;
	}

	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	$statuses = atelier_noir_fulfillment_statuses();
	$key      = sanitize_key( wp_unslash( $_POST['atelier_noir_fulfillment_status'] ) );
	if ( array_key_exists( $key, $statuses ) ) {
		$order->update_meta_data( '_atelier_noir_fulfillment_status', $key );
		$order->save();
	}
}
add_action( 'woocommerce_process_shop_order_meta', 'atelier_noir_save_admin_order_fulfillment_fields', 20, 1 );

/**
 * My account — Studio status label in orders list.
 *
 * @param WC_Order $order Order.
 */
function atelier_noir_account_orders_column_status( $order ) {
	echo '<span class="studio-order-status-label">' . esc_html( atelier_noir_get_order_fulfillment_label( $order ) ) . '</span>';
}

add_action( 'init', function () {
	remove_action( 'woocommerce_my_account_my_orders_column_order-status', 'woocommerce_account_orders_column_order-status' );
	add_action( 'woocommerce_my_account_my_orders_column_order-status', 'atelier_noir_account_orders_column_status', 10 );
}, 20 );

/**
 * Order detail — status timeline for the customer.
 *
 * @param int $order_id Order ID.
 */
function atelier_noir_view_order_fulfillment_block( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	$statuses   = atelier_noir_fulfillment_statuses();
	$current    = atelier_noir_get_order_fulfillment_status( $order );
	$keys       = array_keys( $statuses );
	$current_ix = array_search( $current, $keys, true );
	if ( false === $current_ix ) {
		$current_ix = 0;
	}

	echo '<section class="studio-order-fulfillment" aria-labelledby="studio-order-fulfillment-title">';
	echo '<h2 id="studio-order-fulfillment-title" class="studio-order-fulfillment__title">' . esc_html__( 'Order status', 'atelier-noir-core' ) . '</h2>';
	echo '<p class="studio-order-fulfillment__current">' . esc_html( atelier_noir_get_order_fulfillment_label( $order ) ) . '</p>';

	echo '<ol class="studio-order-fulfillment__steps">';
	foreach ( $keys as $ix => $key ) {
		$classes = 'studio-order-fulfillment__step';
		if ( $ix <= $current_ix ) {
			$classes .= ' is-done';
		}
		if ( $ix === $current_ix ) {
			$classes .= ' is-active';
		}
		echo '<li class="' . esc_attr( $classes ) . '"><span>' . esc_html( $statuses[ $key ] ) . '</span></li>';
	}
	echo '</ol>';
	echo '</section>';
}
add_action( 'woocommerce_view_order', 'atelier_noir_view_order_fulfillment_block', 5 );

/**
 * Customer emails — current Studio fulfillment status.
 *
 * @param WC_Order $order Order.
 * @param bool     $sent_to_admin Admin context.
 * @param bool     $plain_text Plain text email.
 * @param WC_Email $email Email object.
 */
function atelier_noir_email_order_fulfillment_status( $order, $sent_to_admin, $plain_text, $email ) {
	if ( $sent_to_admin || ! $order instanceof WC_Order ) {
		return;
	}

	$allowed_ids = array( 'customer_processing_order', 'customer_completed_order', 'customer_on_hold_order' );
	if ( ! is_object( $email ) || ! in_array( $email->id, $allowed_ids, true ) ) {
		return;
	}

	$status = atelier_noir_get_order_fulfillment_label( $order );

	if ( $plain_text ) {
		echo "\n" . esc_html__( 'Current order status:', 'atelier-noir-core' ) . ' ' . esc_html( $status ) . "\n";
		return;
	}

	echo '<p style="margin-top:20px;padding-top:16px;border-top:1px solid #e0ddd8;">';
	echo '<strong>' . esc_html__( 'Current order status:', 'atelier-noir-core' ) . '</strong> ' . esc_html( $status );
	echo '</p>';
}
add_action( 'woocommerce_email_after_order_table', 'atelier_noir_email_order_fulfillment_status', 15, 4 );
