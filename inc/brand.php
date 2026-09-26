<?php
/**
 * Brand identity: colors, typography, logos, favicon.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Brand name shown in emails, legal pages and fallbacks (Settings → General → Site Title).
 *
 * @return string
 */
function atelier_noir_brand_name() {
	$name = trim( wp_strip_all_tags( (string) get_bloginfo( 'name' ) ) );
	return (string) apply_filters( 'atelier_noir_brand_name', '' !== $name ? $name : 'Atelier Noir' );
}

/**
 * Self-hosted fonts + CSS custom properties.
 */
function atelier_noir_brand_enqueue_assets() {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$ver = file_exists( $dir . '/assets/css/brand.css' ) ? (string) filemtime( $dir . '/assets/css/brand.css' ) : '1.0.0';

	wp_enqueue_style(
		'studio-brand-fonts',
		$uri . '/assets/css/fonts.css',
		array(),
		$ver
	);
	wp_enqueue_style(
		'studio-brand',
		$uri . '/assets/css/brand.css',
		array( 'studio-brand-fonts' ),
		$ver
	);
	wp_enqueue_style(
		'studio-header-menu',
		$uri . '/assets/css/header-menu.css',
		array( 'studio-brand' ),
		file_exists( $dir . '/assets/css/header-menu.css' ) ? (string) filemtime( $dir . '/assets/css/header-menu.css' ) : $ver
	);
}
add_action( 'wp_enqueue_scripts', 'atelier_noir_brand_enqueue_assets', 5 );

/**
 * Theme favicon (when no Site Icon is set in WP Customizer).
 */
function atelier_noir_output_favicon() {
	if ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
		return;
	}

	$icon = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'favicon.png' )
		: '';

	if ( '' === $icon ) {
		return;
	}

	$apple = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'apple-touch-icon.png' )
		: $icon;

	printf(
		'<link rel="icon" href="%1$s" sizes="32x32">' . "\n" .
		'<link rel="icon" href="%1$s" sizes="192x192">' . "\n" .
		'<link rel="apple-touch-icon" href="%2$s">' . "\n",
		esc_url( $icon ),
		esc_url( $apple )
	);
}
add_action( 'wp_head', 'atelier_noir_output_favicon', 1 );

/**
 * Unified site logo URL (single asset — desktop, tablet, mobile).
 *
 * @return string
 */
function atelier_noir_logo_url() {
	$url = atelier_noir_resolve_media_mod(
		'an_logo_attachment',
		'an_logo_url',
		'logo-header-desktop.png'
	);
	if ( '' !== $url ) {
		return $url;
	}

	return atelier_noir_logo_desktop_url();
}

/**
 * Logo URLs — white wordmark on transparent (editorial dark header).
 * @deprecated Use atelier_noir_logo_url().
 */
function atelier_noir_logo_desktop_url() {
	if ( function_exists( 'atelier_noir_resolve_media_mod' ) ) {
		$url = atelier_noir_resolve_media_mod(
			'an_logo_desktop_attachment',
			'an_logo_desktop_url',
			'logo-header-desktop.png'
		);
		if ( '' !== $url ) {
			return $url;
		}
	}

	$att_id = absint( get_theme_mod( 'atelier_noir_logo_desktop_attachment', 0 ) );
	if ( $att_id ) {
		$url = wp_get_attachment_url( $att_id );
		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}
	}

	$custom = (string) get_theme_mod( 'atelier_noir_logo_desktop_url', '' );
	if ( '' !== trim( $custom ) ) {
		return esc_url( $custom );
	}

	return function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'logo-header-desktop.png' )
		: '';
}

/**
 * @deprecated Use atelier_noir_logo_url().
 * @return string
 */
function atelier_noir_logo_mobile_url() {
	return atelier_noir_logo_url();
}

/**
 * Render site logo markup (single responsive image).
 */
function atelier_noir_render_logo( $class = 'studio-header__logo' ) {
	$home = function_exists( 'atelier_noir_home_url' ) ? atelier_noir_home_url() : home_url( '/home/' );
	$logo = atelier_noir_logo_url();
	if ( '' === $logo ) {
		?>
		<a class="<?php echo esc_attr( $class ); ?> studio-header__site-name" href="<?php echo esc_url( $home ); ?>" rel="home">
			<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
		</a>
		<?php
		return;
	}
	?>
	<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $home ); ?>" rel="home">
		<img
			class="studio-logo"
			src="<?php echo esc_url( $logo ); ?>"
			alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
			decoding="async"
		>
	</a>
	<?php
}
