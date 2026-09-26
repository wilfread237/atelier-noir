<?php
/**
 * Editorial footer — Customizer + helpers.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function atelier_noir_customize_footer_editorial( $wp_customize ) {
	$wp_customize->add_section(
		'an_footer_editorial',
		array(
			'title'    => __( 'Atelier Noir — Footer: wordmark and copyright', 'atelier-noir' ),
			'priority' => 32,
		)
	);

	$text_fields = array(
		'an_footer_wordmark'      => array( __( 'Wordmark fallback (text)', 'atelier-noir' ), strtoupper( atelier_noir_brand_name() ) ),
		'an_footer_copyright'     => array( __( 'Copyright line (© added automatically)', 'atelier-noir' ), gmdate( 'Y' ) . ' ' . strtoupper( atelier_noir_brand_name() ) . '. ALL RIGHTS RESERVED' ),
		'an_contact_email'        => array( __( 'Contact email', 'atelier-noir' ), atelier_noir_email_contact() ),
		'an_contact_cities'       => array( __( 'Cities line (Contact page)', 'atelier-noir' ), 'PARIS · LONDON · NEW YORK' ),
		'an_footer_instagram_url'  => array( __( 'Instagram URL', 'atelier-noir' ), 'https://instagram.com/' ),
	);

	foreach ( $text_fields as $id => $row ) {
		list( $label, $default ) = $row;
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
				'section' => 'an_footer_editorial',
				'type'    => 'text',
			)
		);
	}

	foreach ( array(
		'an_home_hero_attachment'           => __( 'Home — CEO hero image', 'atelier-noir' ),
		'an_home_void_desktop_attachment'   => __( 'Home — section 1 watermark (desktop)', 'atelier-noir' ),
		'an_home_void_mobile_attachment'    => __( 'Home — section 1 watermark (mobile / tablet)', 'atelier-noir' ),
	) as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				$id,
				array(
					'label'     => $label,
					'section'   => 'an_footer_editorial',
					'mime_type' => 'image',
				)
			)
		);
	}

	$hero_default = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'home-ceo-spotlight.png' )
		: '';
	$void_pattern_default = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'pattern-gris-translucide.png' )
		: '';
	$void_desk_default = $void_pattern_default;
	$void_mob_default  = $void_pattern_default;

	$wp_customize->add_setting(
		'an_home_hero_url',
		array(
			'default'           => $hero_default,
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'an_home_hero_url',
		array(
			'label'       => __( 'Home — CEO hero image URL', 'atelier-noir' ),
			'description' => __( 'Used when no file is uploaded above. Site logo is configured under Studio → Logo.', 'atelier-noir' ),
			'section'     => 'an_footer_editorial',
			'type'        => 'url',
		)
	);

	foreach (
		array(
			'an_home_void_desktop_url' => array(
				__( 'Home — section 1 watermark URL (desktop)', 'atelier-noir' ),
				$void_desk_default,
				__( 'Pattern only — upload a translucent pattern image.', 'atelier-noir' ),
			),
			'an_home_void_mobile_url'  => array(
				__( 'Home — section 1 watermark URL (mobile / tablet)', 'atelier-noir' ),
				$void_mob_default,
				__( 'Pattern only — upload a translucent pattern image.', 'atelier-noir' ),
			),
		) as $id => $row
	) {
		list( $label, $default, $desc ) = $row;
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $default,
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $label,
				'description' => $desc,
				'section'     => 'an_footer_editorial',
				'type'        => 'url',
			)
		);
	}

	$legal_fields = array(
		'an_legal_privacy_body' => __( 'Privacy policy (overlay)', 'atelier-noir' ),
		'an_legal_cookies_body' => __( 'Cookie policy (overlay)', 'atelier-noir' ),
		'an_legal_terms_body'   => __( 'Terms & conditions (overlay)', 'atelier-noir' ),
	);

	foreach ( $legal_fields as $id => $label ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'wp_kses_post',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'an_footer_editorial',
				'type'    => 'textarea',
			)
		);
	}
}
add_action( 'customize_register', 'atelier_noir_customize_footer_editorial', 37 );

/**
 * @return string
 */
function atelier_noir_footer_logo_desktop_url() {
	return atelier_noir_resolve_media_mod(
		'an_footer_logo_desktop_attachment',
		'an_footer_logo_desktop_url',
		'logo-footer-gray.svg'
	);
}

/**
 * @return string
 */
function atelier_noir_footer_logo_mobile_url() {
	$url = atelier_noir_resolve_media_mod(
		'an_footer_logo_mobile_attachment',
		'an_footer_logo_mobile_url',
		''
	);
	return $url ?: atelier_noir_footer_logo_desktop_url();
}

/**
 * @return string
 */
function atelier_noir_contact_page_url() {
	return atelier_noir_menu_path_url( 'an_menu_path_you_to_us', '/contact/' );
}

/**
 * Copyright line with leading © (single symbol).
 *
 * @param string $line Raw theme mod value.
 * @return string
 */
function atelier_noir_format_copyright( $line ) {
	$line = trim( (string) $line );
	if ( '' === $line ) {
		$line = gmdate( 'Y' ) . ' ' . strtoupper( atelier_noir_brand_name() ) . '. ALL RIGHTS RESERVED';
	}
	$line = preg_replace( '/^©+\s*/u', '', $line );
	return '©' . $line;
}

/**
 * Home section 1 — translucent pattern only (no center wordmark).
 *
 * @return string
 */
function atelier_noir_home_void_desktop_url() {
	return atelier_noir_resolve_media_mod(
		'an_home_void_desktop_attachment',
		'an_home_void_desktop_url',
		'pattern-gris-translucide.png'
	);
}

/**
 * @return string
 */
function atelier_noir_home_void_mobile_url() {
	$url = atelier_noir_resolve_media_mod(
		'an_home_void_mobile_attachment',
		'an_home_void_mobile_url',
		'pattern-gris-translucide.png'
	);
	return $url ?: atelier_noir_home_void_desktop_url();
}
