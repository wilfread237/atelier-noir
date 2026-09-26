<?php
/**
 * Editorial full-screen menu overlay (maquette).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Home content URL (/home/), not the loader root.
 *
 * @return string
 */
function atelier_noir_home_url() {
	if ( function_exists( 'atelier_noir_loader_redirect_url' ) ) {
		return atelier_noir_loader_redirect_url();
	}

	$home_id = function_exists( 'atelier_noir_get_home_page_id' )
		? atelier_noir_get_home_page_id()
		: 0;

	if ( $home_id > 0 ) {
		$permalink = get_permalink( $home_id );
		if ( is_string( $permalink ) && '' !== $permalink ) {
			return esc_url( $permalink );
		}
	}

	return esc_url( home_url( '/home/' ) );
}

/**
 * Whether the editorial menu replaces the legacy drawer.
 *
 * @return bool
 */
function atelier_noir_use_editorial_menu() {
	return (bool) apply_filters( 'atelier_noir_use_editorial_menu', true );
}

/**
 * Vertical menu icon URL.
 *
 * @return string
 */
function atelier_noir_menu_icon_url() {
	return atelier_noir_resolve_media_mod(
		'an_menu_icon_attachment',
		'an_menu_icon_url',
		'menu-icon-vertical.png'
	);
}

/**
 * Translucent watermark for menu overlay.
 *
 * @return string
 */
function atelier_noir_menu_watermark_desktop_url() {
	return atelier_noir_resolve_media_mod(
		'an_watermark_desktop_attachment',
		'an_watermark_desktop_url',
		'pattern-gris-translucide.png'
	);
}

/**
 * @return string
 */
function atelier_noir_menu_watermark_mobile_url() {
	return atelier_noir_resolve_media_mod(
		'an_watermark_mobile_attachment',
		'an_watermark_mobile_url',
		'pattern-gris-translucide.png'
	);
}

/**
 * Build menu path URL from Customizer relative path.
 *
 * @param string $mod_key Theme mod key for path.
 * @param string $default Default path e.g. /home/.
 * @return string
 */
function atelier_noir_menu_path_url( $mod_key, $default ) {
	$path = (string) atelier_noir_get_theme_mod( $mod_key, $default );
	$path = '/' . ltrim( trim( $path ), '/' );
	return esc_url( home_url( $path ) );
}

/**
 * Editorial menu items (label, url, lowercase label flag).
 *
 * @return array<int, array{label:string,url:string,lowercase:bool}>
 */
function atelier_noir_menu_overlay_items() {
	$items = array(
		array(
			'label'     => (string) atelier_noir_get_theme_mod( 'an_menu_label_us', 'US' ),
			'url'       => atelier_noir_menu_path_url( 'an_menu_path_us', '/home/' ),
			'lowercase' => false,
		),
		array(
			'label'     => (string) atelier_noir_get_theme_mod( 'an_menu_label_us_to_you', 'US TO YOU' ),
			'url'       => atelier_noir_menu_path_url( 'an_menu_path_us_to_you', '/services/' ),
			'lowercase' => false,
		),
		array(
			'label'     => (string) atelier_noir_get_theme_mod( 'an_menu_label_together', 'together' ),
			'url'       => atelier_noir_menu_path_url( 'an_menu_path_together', '/projects/' ),
			'lowercase' => true,
		),
		array(
			'label'     => (string) atelier_noir_get_theme_mod( 'an_menu_label_you_to_us', 'YOU TO US' ),
			'url'       => atelier_noir_menu_path_url( 'an_menu_path_you_to_us', '/contact/' ),
			'lowercase' => false,
		),
	);

	return apply_filters( 'atelier_noir_menu_overlay_items', $items );
}

/**
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function atelier_noir_customize_menu_overlay( $wp_customize ) {
	$wp_customize->add_section(
		'an_menu_overlay',
		array(
			'title'       => __( 'Atelier Noir — Menu overlay', 'atelier-noir' ),
			'description' => __( 'Full-screen navigation (US / together / …). Paths are relative to the site URL.', 'atelier-noir' ),
			'priority'    => 30,
		)
	);

	$fields = array(
		array( 'an_menu_label_us', __( 'Label — US', 'atelier-noir' ), 'US', 'text' ),
		array( 'an_menu_path_us', __( 'Path — US', 'atelier-noir' ), '/home/', 'text' ),
		array( 'an_menu_label_us_to_you', __( 'Label — US TO YOU', 'atelier-noir' ), 'US TO YOU', 'text' ),
		array( 'an_menu_path_us_to_you', __( 'Path — US TO YOU', 'atelier-noir' ), '/services/', 'text' ),
		array( 'an_menu_label_together', __( 'Label — together', 'atelier-noir' ), 'together', 'text' ),
		array( 'an_menu_path_together', __( 'Path — together', 'atelier-noir' ), '/projects/', 'text' ),
		array( 'an_menu_label_you_to_us', __( 'Label — YOU TO US', 'atelier-noir' ), 'YOU TO US', 'text' ),
		array( 'an_menu_path_you_to_us', __( 'Path — YOU TO US', 'atelier-noir' ), '/contact/', 'text' ),
	);

	foreach ( $fields as $field ) {
		list( $id, $label, $default, $type ) = $field;
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $default,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'an_menu_overlay',
				'type'    => $type,
			)
		);
	}

	$wp_customize->add_setting(
		'an_menu_icon_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'an_menu_icon_attachment',
			array(
				'label'     => __( 'Menu icon (vertical)', 'atelier-noir' ),
				'section'   => 'an_menu_overlay',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'an_watermark_desktop_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'an_watermark_desktop_attachment',
			array(
				'label'     => __( 'Watermark — desktop', 'atelier-noir' ),
				'section'   => 'an_menu_overlay',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'an_watermark_mobile_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'an_watermark_mobile_attachment',
			array(
				'label'     => __( 'Watermark — mobile', 'atelier-noir' ),
				'section'   => 'an_menu_overlay',
				'mime_type' => 'image',
			)
		)
	);
}
add_action( 'customize_register', 'atelier_noir_customize_menu_overlay', 36 );

/**
 * Menu overlay styles.
 */
function atelier_noir_enqueue_menu_overlay_assets() {
	if ( ! atelier_noir_use_editorial_menu() ) {
		return;
	}
	if ( function_exists( 'atelier_noir_is_loader_page' ) && atelier_noir_is_loader_page() ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/menu-overlay.css';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'atelier-noir-menu-overlay',
		get_stylesheet_directory_uri() . '/assets/css/menu-overlay.css',
		array( 'studio-header-menu', 'atelier-noir' ),
		(string) filemtime( $path )
	);
}
add_action( 'wp_enqueue_scripts', 'atelier_noir_enqueue_menu_overlay_assets', 25 );
