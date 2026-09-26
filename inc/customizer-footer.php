<?php
/**
 * Customizer — footer copy and social links.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function atelier_noir_register_footer_customizer( $wp_customize ) {
	$wp_customize->add_section(
		'atelier_noir_footer_panel',
		array(
			'title'       => __( 'Atelier Noir — Footer: tagline and social', 'atelier-noir' ),
			'description' => __( 'Tagline, contact emails and social profile URLs shown in the site footer.', 'atelier-noir' ),
			'priority'    => 36,
		)
	);

	$fields = array(
		'atelier_noir_footer_tagline'        => array(
			'label'   => __( 'Brand tagline', 'atelier-noir' ),
			'type'    => 'textarea',
			'default' => 'Fashion — Creative identity — Art direction',
		),
		'atelier_noir_footer_email_contact'  => array(
			'label'   => __( 'Contact email', 'atelier-noir' ),
			'type'    => 'email',
			'default' => atelier_noir_email_contact(),
		),
		'atelier_noir_footer_email_orders'   => array(
			'label'   => __( 'Orders email', 'atelier-noir' ),
			'type'    => 'email',
			'default' => atelier_noir_email_orders(),
		),
		'atelier_noir_footer_social_instagram' => array(
			'label'   => __( 'Instagram URL', 'atelier-noir' ),
			'type'    => 'url',
			'default' => 'https://instagram.com',
		),
		'atelier_noir_footer_social_linkedin' => array(
			'label'   => __( 'LinkedIn URL', 'atelier-noir' ),
			'type'    => 'url',
			'default' => 'https://linkedin.com',
		),
		'atelier_noir_footer_social_tiktok'  => array(
			'label'   => __( 'TikTok URL', 'atelier-noir' ),
			'type'    => 'url',
			'default' => 'https://tiktok.com',
		),
	);

	foreach ( $fields as $id => $cfg ) {
		$sanitize = 'url' === $cfg['type'] ? 'esc_url_raw' : ( 'email' === $cfg['type'] ? 'sanitize_email' : 'sanitize_textarea_field' );
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $cfg['default'],
				'sanitize_callback' => $sanitize,
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $cfg['label'],
				'section' => 'atelier_noir_footer_panel',
				'type'    => 'textarea' === $cfg['type'] ? 'textarea' : ( 'url' === $cfg['type'] ? 'url' : 'text' ),
			)
		);
	}
}
add_action( 'customize_register', 'atelier_noir_register_footer_customizer', 38 );

/**
 * @param string $mod Theme mod key.
 * @param string $fallback Default value.
 * @return string
 */
function atelier_noir_footer_mod( $mod, $fallback = '' ) {
	$value = get_theme_mod( $mod, $fallback );
	return is_string( $value ) ? $value : $fallback;
}
