<?php
/**
 * My Account page — Atelier Noir
 *
 * Override de woocommerce/myaccount/my-account.php
 * Ajout d'un fil d'Ariane + marqueur has-navigation pour le CSS de grille.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

// Résoudre le titre de la section active
$current_endpoint = WC()->query->get_current_endpoint();
$menu_items       = wc_get_account_menu_items();
$current_label    = isset( $menu_items[ $current_endpoint ] )
	? $menu_items[ $current_endpoint ]
	: __( 'Dashboard', 'atelier-noir' );
?>

<!-- ── Fil d'Ariane ──────────────────────────────────────────────────── -->
<div class="studio-account-breadcrumb">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( atelier_noir_brand_name() ); ?></a>
	<span aria-hidden="true">/</span>
	<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My account', 'atelier-noir' ); ?></a>
	<?php if ( $current_endpoint ) : ?>
		<span aria-hidden="true">/</span>
		<span aria-current="page"><?php echo esc_html( $current_label ); ?></span>
	<?php endif; ?>
</div>

<!-- ── Layout sidebar + contenu ──────────────────────────────────────── -->
<?php
/**
 * My Account navigation.
 * @since 2.6.0
 */
do_action( 'woocommerce_account_navigation' );
?>

<div class="woocommerce-MyAccount-content">
	<?php
	/**
	 * My Account content.
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_content' );
	?>
</div>

<!-- ── Barre navigation mobile (fixée en bas) ────────────────────────── -->
<nav class="studio-account-mobile-bar" aria-label="<?php esc_attr_e( 'Main navigation', 'atelier-noir' ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">← <?php esc_html_e( 'Home', 'atelier-noir' ); ?></a>
	<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ?: home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Shop', 'atelier-noir' ); ?></a>
	<a href="<?php echo esc_url( home_url( '/project/' ) ); ?>"><?php esc_html_e( 'Project', 'atelier-noir' ); ?></a>
	<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'atelier-noir' ); ?></a>
</nav>
