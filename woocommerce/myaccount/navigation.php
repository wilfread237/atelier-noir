<?php
/**
 * My Account navigation — Atelier Noir
 *
 * Override de woocommerce/myaccount/navigation.php
 * Ajout : greeting utilisateur + liens retour vers le site.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );

$current_user = wp_get_current_user();
$first_name   = $current_user->first_name ?: $current_user->display_name;
?>

<nav class="woocommerce-MyAccount-navigation studio-account-nav" aria-label="<?php esc_html_e( 'Account navigation', 'atelier-noir' ); ?>">

	<!-- ── Identité utilisateur ───────────────────────────────────────────── -->
	<div class="studio-account-nav__identity">
		<div class="studio-account-nav__avatar" aria-hidden="true">
			<?php echo esc_html( mb_strtoupper( mb_substr( $first_name, 0, 1 ) ) ); ?>
		</div>
		<div class="studio-account-nav__user-info">
			<p class="studio-account-nav__greeting">
				<?php esc_html_e( 'Hello,', 'atelier-noir' ); ?>
			</p>
			<p class="studio-account-nav__name">
				<?php echo esc_html( $first_name ); ?>
			</p>
		</div>
	</div>

	<!-- ── Navigation compte ─────────────────────────────────────────────── -->
	<ul class="studio-account-nav__list">
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="<?php echo esc_attr( implode( ' ', explode( ' ', wc_get_account_menu_item_classes( $endpoint ) ) ) ); ?>">
				<a
					href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
					<?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>
				>
					<?php echo esc_html( $label ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<!-- ── Retour sur le site ────────────────────────────────────────────── -->
	<div class="studio-account-nav__site-links" aria-label="<?php esc_html_e( 'Main navigation', 'atelier-noir' ); ?>">
		<p class="studio-account-nav__site-links-label">
			<?php esc_html_e( 'Explore the Studio', 'atelier-noir' ); ?>
		</p>
		<ul>
			<li>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span aria-hidden="true">←</span> <?php esc_html_e( 'Home', 'atelier-noir' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ?: home_url( '/shop/' ) ); ?>">
					<?php esc_html_e( 'Shop', 'atelier-noir' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( home_url( '/project/' ) ); ?>">
					<?php esc_html_e( 'Project', 'atelier-noir' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">
					<?php esc_html_e( 'Services', 'atelier-noir' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
					<?php esc_html_e( 'Contact', 'atelier-noir' ); ?>
				</a>
			</li>
		</ul>
	</div>

</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
