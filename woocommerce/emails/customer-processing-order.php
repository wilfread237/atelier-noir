<?php
/**
 * Email order confirmation — Atelier Noir
 * Sent to the customer when their order is received and being processed.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 10.4.0
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );
$first_name                 = $order->get_billing_first_name();
$order_number               = $order->get_order_number();
$order_url                  = $order->get_view_order_url();
$account_url                = wc_get_account_endpoint_url( 'orders' );

do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<?php echo $email_improvements_enabled ? '<div class="email-introduction">' : ''; ?>

<p>
<?php
if ( ! empty( $first_name ) ) {
	/* translators: %s: customer first name */
	printf( esc_html__( 'Hello %s,', 'atelier-noir' ), esc_html( $first_name ) );
} else {
	esc_html_e( 'Hello,', 'atelier-noir' );
}
?>
</p>

<p><?php esc_html_e( 'We have received your order and our team is processing it now.', 'atelier-noir' ); ?></p>

<p>
<?php
/* translators: %s: order number */
printf(
	wp_kses( __( 'Your order number is <strong>#%s</strong>.', 'atelier-noir' ), [ 'strong' => [] ] ),
	esc_html( $order_number )
);
?>
</p>

<p>
<?php
printf(
	wp_kses(
		/* translators: %s: account area link */
		__( 'You can track your order status at any time from <a href="%s">your account</a>.', 'atelier-noir' ),
		[ 'a' => [ 'href' => [] ] ]
	),
	esc_url( $account_url )
);
?>
</p>

<?php echo $email_improvements_enabled ? '</div>' : ''; ?>

<?php

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_order_meta',    $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
	echo $email_improvements_enabled
		? '<table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tr><td class="email-additional-content">'
		: '';
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo $email_improvements_enabled ? '</td></tr></table>' : '';
}
?>

<!-- Team message -->
<?php echo $email_improvements_enabled ? '<table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tr><td class="email-additional-content">' : ''; ?>
<p style="margin-top:24px; padding-top:24px; border-top:1px solid #e0ddd8; font-style:italic; color:#555;">
	<?php echo esc_html( sprintf( /* translators: %s: brand name. */ __( 'Thank you for your trust. We are preparing your order with the care and rigor that define %s.', 'atelier-noir' ), atelier_noir_brand_name() ) ); ?>
</p>
<p style="color:#555;">
	<?php echo esc_html( sprintf( /* translators: %s: brand name. */ __( 'The %s team', 'atelier-noir' ), atelier_noir_brand_name() ) ); ?><br/>
	<a href="mailto:<?php echo esc_attr( atelier_noir_email_orders() ); ?>" style="color:#0a0a0a;"><?php echo esc_html( atelier_noir_email_orders() ); ?></a>
</p>
<?php echo $email_improvements_enabled ? '</td></tr></table>' : ''; ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
