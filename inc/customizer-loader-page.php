<?php
/**
 * Customizer — Loader page (standalone /) + global editorial defaults.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function atelier_noir_customize_loader_page( $wp_customize ) {
	$wp_customize->add_section(
		'an_loader_page',
		array(
			'title'       => __( 'Atelier Noir — Loader page', 'atelier-noir' ),
			'description' => __( 'The site root (/) shows the loader, then redirects to Home. Loader images should include your centered wordmark. Home section 1 and overlay watermarks use the pattern image set below.', 'atelier-noir' ),
			'priority'    => 29,
		)
	);

	$wp_customize->add_setting(
		'an_loader_delay_ms',
		array(
			'default'           => 7000,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'an_loader_delay_ms',
		array(
			'label'       => __( 'Loader duration before redirect (ms)', 'atelier-noir' ),
			'section'     => 'an_loader_page',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 15000,
				'step' => 100,
			),
		)
	);

	$wp_customize->add_setting(
		'an_loader_redirect_path',
		array(
			'default'           => '/home/',
			'sanitize_callback' => function ( $value ) {
				$value = trim( (string) $value );
				return '/' . ltrim( $value, '/' );
			},
		)
	);
	$wp_customize->add_control(
		'an_loader_redirect_path',
		array(
			'label'       => __( 'Redirect path after loader', 'atelier-noir' ),
			'description' => __( 'Relative path, e.g. /home/', 'atelier-noir' ),
			'section'     => 'an_loader_page',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'an_loader_disable',
		array(
			'default'           => false,
			'sanitize_callback' => function ( $v ) {
				return (bool) $v;
			},
		)
	);
	$wp_customize->add_control(
		'an_loader_disable',
		array(
			'label'       => __( 'Skip loader (redirect immediately)', 'atelier-noir' ),
			'section'     => 'an_loader_page',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'an_loader_desktop_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'an_loader_desktop_attachment',
			array(
				'label'       => __( 'Loader image — desktop / laptop', 'atelier-noir' ),
				'description' => __( 'Optional. Overrides the default brand asset when set.', 'atelier-noir' ),
				'section'     => 'an_loader_page',
				'mime_type'   => 'image',
			)
		)
	);

	$loader_desk_default = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'loader-desktop.png' )
		: '';

	$wp_customize->add_setting(
		'an_loader_desktop_url',
		array(
			'default'           => $loader_desk_default,
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'an_loader_desktop_url',
		array(
			'label'       => __( 'Loader image URL — desktop / laptop', 'atelier-noir' ),
			'description' => __( 'Used when no file is uploaded above. Also powers Home section 1 background.', 'atelier-noir' ),
			'section'     => 'an_loader_page',
			'type'        => 'url',
		)
	);

	$wp_customize->add_setting(
		'an_loader_mobile_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'an_loader_mobile_attachment',
			array(
				'label'       => __( 'Loader image — mobile / tablet', 'atelier-noir' ),
				'description' => __( 'Optional. Overrides the default brand asset when set.', 'atelier-noir' ),
				'section'     => 'an_loader_page',
				'mime_type'   => 'image',
			)
		)
	);

	$loader_mob_default = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'loader-mobile.png' )
		: '';

	$wp_customize->add_setting(
		'an_loader_mobile_url',
		array(
			'default'           => $loader_mob_default,
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'an_loader_mobile_url',
		array(
			'label'       => __( 'Loader image URL — mobile / tablet', 'atelier-noir' ),
			'description' => __( 'Used when no file is uploaded above. Also powers Home section 1 on viewports ≤1024px.', 'atelier-noir' ),
			'section'     => 'an_loader_page',
			'type'        => 'url',
		)
	);

	$wp_customize->add_setting(
		'an_loader_video_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'an_loader_video_attachment',
			array(
				'label'       => __( 'Eyes catcher video (optional)', 'atelier-noir' ),
				'description' => __( 'When set, replaces the loader image.', 'atelier-noir' ),
				'section'     => 'an_loader_page',
				'mime_type'   => 'video',
			)
		)
	);

	$wp_customize->add_setting(
		'an_newsletter_popup_enabled',
		array(
			'default'           => false,
			'sanitize_callback' => function ( $v ) {
				return (bool) $v;
			},
		)
	);
	$wp_customize->add_control(
		'an_newsletter_popup_enabled',
		array(
			'label'   => __( 'Enable newsletter popup (legacy)', 'atelier-noir' ),
			'section' => 'an_loader_page',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'atelier_noir_customize_loader_page', 35 );

/**
 * Global black site background override.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function atelier_noir_customize_global_black( $wp_customize ) {
	$wp_customize->add_setting(
		'an_site_black_background',
		array(
			'default'           => true,
			'sanitize_callback' => function ( $v ) {
				return (bool) $v;
			},
		)
	);
	$wp_customize->add_control(
		'an_site_black_background',
		array(
			'label'   => __( 'Site-wide black background', 'atelier-noir' ),
			'section' => 'atelier_noir_theme_colors',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'atelier_noir_customize_global_black', 32 );
