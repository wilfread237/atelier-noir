<?php
/**
 * Header Atelier Noir — transparent overlay, dark au scroll.
 *
 * @package atelier-noir
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_name = get_bloginfo( 'name' );
?>
<header id="site-header" class="site-header studio-header" role="banner">
	<div class="studio-header__inner">

		<!-- Logo / nom du studio -->
		<div class="studio-header__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="studio-header__site-name" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php echo esc_html( $site_name ); ?>
				</a>
			<?php endif; ?>
		</div>

		<!-- Navigation principale -->
		<?php
		$nav_html = wp_nav_menu( array(
			'theme_location' => 'primary',
			'menu_class'     => 'studio-nav__list',
			'container'      => false,
			'fallback_cb'    => false,
			'echo'           => false,
			'depth'          => 1,
		) );
		?>
		<?php if ( $nav_html ) : ?>
			<nav class="studio-header__nav studio-nav" aria-label="<?php esc_attr_e( 'Main navigation', 'atelier-noir' ); ?>">
				<?php echo $nav_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</nav>
		<?php endif; ?>

		<!-- Bouton hamburger mobile -->
		<button
			class="studio-header__toggle"
			aria-expanded="false"
			aria-controls="studio-mobile-menu"
			aria-label="<?php esc_attr_e( 'Open menu', 'atelier-noir' ); ?>"
		>
			<span class="studio-header__toggle-bar"></span>
			<span class="studio-header__toggle-bar"></span>
		</button>

	</div>

	<!-- Menu mobile (overlay) -->
	<div id="studio-mobile-menu" class="studio-mobile-menu" aria-hidden="true">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'menu_class'     => 'studio-mobile-menu__list',
			'container'      => false,
			'fallback_cb'    => false,
			'depth'          => 1,
		) );
		?>
	</div>
</header>
