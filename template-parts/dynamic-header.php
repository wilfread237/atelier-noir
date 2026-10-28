<?php
/**
 * Header — logo + vertical menu icon + editorial overlay.
 *
 * @package atelier-noir
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$use_editorial = function_exists( 'atelier_noir_use_editorial_menu' ) && atelier_noir_use_editorial_menu();
$is_landing    = is_front_page() && ! is_home();
$header_class  = 'studio-header' . ( $is_landing ? ' studio-header--landing' : '' );
?>
<header id="site-header" class="<?php echo esc_attr( $header_class ); ?>" role="banner" data-header-theme="dark">
	<div class="studio-header__inner">
		<div class="studio-header__brand">
			<?php
			if ( function_exists( 'atelier_noir_render_logo' ) ) {
				atelier_noir_render_logo();
			} elseif ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				$home = function_exists( 'atelier_noir_home_url' ) ? atelier_noir_home_url() : home_url( '/home/' );
				?>
				<a class="studio-header__site-name" href="<?php echo esc_url( $home ); ?>" rel="home">
					<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
				</a>
				<?php
			}
			?>
		</div>

		<div class="studio-header__end">
			<p class="studio-header__origin">
				<?php
				if ( $is_landing ) {
					echo esc_html__( 'Made in Douala', 'atelier-noir' );
					echo '<span class="studio-header__origin-dot" aria-hidden="true"></span>';
				} else {
					echo esc_html__( 'Made in Douala .', 'atelier-noir' );
				}
				?>
			</p>
			<button
				class="studio-header__toggle"
				type="button"
				aria-expanded="false"
				aria-controls="studio-mobile-menu"
				aria-label="<?php esc_attr_e( 'Open menu', 'atelier-noir' ); ?>"
			>
				<span class="studio-header__toggle-bar"></span>
				<span class="studio-header__toggle-bar"></span>
			</button>
		</div>
	</div>
</header>

<?php
if ( $use_editorial ) {
	$_blend_url = function_exists( 'atelier_noir_menu_watermark_desktop_url' )
		? atelier_noir_menu_watermark_desktop_url()
		: '';
	if ( $_blend_url ) {
		?>
		<div
			id="an-body-blend"
			aria-hidden="true"
			style="--an-body-pattern:url('<?php echo esc_url( $_blend_url ); ?>')"
		></div>
		<?php
	}

	/*
	 * Bande fixe au-dessus du header (logo + icône menu) — maquette Projects/Services.
	 * Contrairement à #an-body-blend (plein écran), limitée à la hauteur du header :
	 * pas de décalage au scroll, seulement le motif visible par-dessus le logo/hamburger.
	 * Activée via le body class an-header-blend (Projects, Services).
	 */
	$_blend_mobile = function_exists( 'atelier_noir_menu_watermark_mobile_url' )
		? atelier_noir_menu_watermark_mobile_url()
		: '';
	if ( $_blend_url ) {
		?>
		<div
			id="an-header-pattern-blend"
			aria-hidden="true"
			style="--an-header-pattern:url('<?php echo esc_url( $_blend_url ); ?>'); --an-header-pattern-mobile:url('<?php echo esc_url( $_blend_mobile ?: $_blend_url ); ?>')"
		></div>
		<?php
	}

	get_template_part( 'template-parts/menu', 'overlay' );
	return;
}

$account_url = function_exists( 'wc_get_account_endpoint_url' )
	? wc_get_account_endpoint_url( 'dashboard' )
	: wp_login_url( get_permalink() );

$account_label = is_user_logged_in()
	? __( 'My account', 'atelier-noir' )
	: __( 'Sign in', 'atelier-noir' );

$cart_url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
$cart_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
?>

<div
	id="studio-mobile-menu"
	class="studio-mobile-menu"
	aria-hidden="true"
	role="dialog"
	aria-label="<?php esc_attr_e( 'Site menu', 'atelier-noir' ); ?>"
>
	<button class="studio-mobile-menu__close" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'atelier-noir' ); ?>">
		<span></span>
		<span></span>
	</button>

	<nav class="studio-mobile-menu__nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'atelier-noir' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_class'     => 'studio-mobile-menu__list',
				'container'      => false,
				'fallback_cb'    => false,
				'depth'          => 1,
			)
		);
		?>
	</nav>

	<div class="studio-mobile-menu__actions" aria-label="<?php esc_attr_e( 'Quick actions', 'atelier-noir' ); ?>">
		<div class="studio-mobile-menu__cart" id="studio-header-cart">
			<?php if ( function_exists( 'WC' ) ) : ?>
			<button
				type="button"
				class="studio-mobile-menu__action studio-header__cart studio-header__cart--toggle"
				aria-expanded="false"
				aria-controls="studio-mini-cart"
				aria-label="<?php esc_attr_e( 'Cart', 'atelier-noir' ); ?>"
			>
				<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
					<path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6l-1-3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>
				</svg>
				<?php if ( $cart_count > 0 ) : ?>
					<span class="studio-header__cart-count"><?php echo esc_html( (string) $cart_count ); ?></span>
				<?php endif; ?>
			</button>
			<div id="studio-mini-cart" class="studio-mini-cart" hidden>
				<?php if ( function_exists( 'woocommerce_mini_cart' ) ) : ?>
					<?php woocommerce_mini_cart(); ?>
				<?php else : ?>
					<p><a href="<?php echo esc_url( $cart_url ); ?>"><?php esc_html_e( 'View cart', 'atelier-noir' ); ?></a></p>
				<?php endif; ?>
			</div>
			<?php else : ?>
			<a href="<?php echo esc_url( $cart_url ); ?>" class="studio-mobile-menu__action studio-header__cart" aria-label="<?php esc_attr_e( 'Cart', 'atelier-noir' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
					<path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6l-1-3H2"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>
				</svg>
				<span class="studio-mobile-menu__action-label"><?php esc_html_e( 'Cart', 'atelier-noir' ); ?></span>
			</a>
			<?php endif; ?>
		</div>

		<a href="<?php echo esc_url( $account_url ); ?>" class="studio-mobile-menu__action studio-mobile-menu__account studio-header__account" aria-label="<?php echo esc_attr( $account_label ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
				<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
			</svg>
			<span class="studio-mobile-menu__action-label"><?php echo esc_html( $account_label ); ?></span>
		</a>
	</div>
</div>
