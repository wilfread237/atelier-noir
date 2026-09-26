<?php
/**
 * Customizer — Contact page + legal page placeholders.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param string $key     Theme mod key.
 * @param string $default Default English text.
 * @return string
 */
function atelier_noir_contact_mod( $key, $default = '' ) {
	$val = get_theme_mod( $key, $default );
	return is_string( $val ) ? $val : $default;
}

/**
 * @param string $key     Theme mod key.
 * @param string $default Default value.
 * @return string
 */
function atelier_noir_legal_mod( $key, $default = '' ) {
	$val = get_theme_mod( $key, $default );
	return is_string( $val ) ? $val : $default;
}

/**
 * Legal field or visible [To be completed] placeholder.
 *
 * @param string $key              Theme mod key.
 * @param string $placeholder_label Label shown when empty.
 */
function atelier_noir_legal_field_html( $key, $placeholder_label ) {
	$value = atelier_noir_legal_mod( $key, '' );
	if ( '' !== trim( $value ) ) {
		echo esc_html( $value );
		return;
	}
	echo '<span class="studio-legal__todo">[' . esc_html( $placeholder_label ) . ']</span>';
}

/**
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function atelier_noir_customize_register_legal_contact( $wp_customize ) {
	$wp_customize->add_section(
		'atelier_noir_contact_page',
		array(
			'title'       => __( 'Atelier Noir — Contact page', 'atelier-noir' ),
			'description' => __( 'Texts for the PHP contact template (tabs, form notices). Layout and forms are not changed here.', 'atelier-noir' ),
			'priority'    => 36,
		)
	);

	$contact_fields = array(
		'atelier_noir_contact_intro_booking'      => array(
			'label'       => __( 'Booking tab — introduction', 'atelier-noir' ),
			'type'        => 'textarea',
			'default'     => 'Book a session with our studio. Tell us which service you are interested in — we will get back to you within 48 hours.',
		),
		'atelier_noir_contact_intro_information'  => array(
			'label'       => __( 'Information tab — introduction', 'atelier-noir' ),
			'type'        => 'textarea',
			'default'     => 'For any question, complaint or partnership request, use this form. Our team will respond as soon as possible.',
		),
		'atelier_noir_contact_form_legal'         => array(
			'label'       => __( 'Form footer (both tabs)', 'atelier-noir' ),
			'description' => __( 'Legal notice under the submit button (required fields, privacy).', 'atelier-noir' ),
			'type'        => 'text',
			'default'     => '* Required fields. Your data is never sold.',
		),
		'atelier_noir_contact_success_booking'    => array(
			'label'   => __( 'Success message — booking', 'atelier-noir' ),
			'type'    => 'text',
			'default' => 'Your request has been sent. We will reply within 48 hours.',
		),
		'atelier_noir_contact_success_information' => array(
			'label'   => __( 'Success message — information', 'atelier-noir' ),
			'type'    => 'text',
			'default' => 'Your message has been sent. We will respond as soon as possible.',
		),
	);

	foreach ( $contact_fields as $id => $args ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $args['label'],
				'description' => $args['description'] ?? '',
				'section'     => 'atelier_noir_contact_page',
				'type'        => $args['type'],
			)
		);
	}

	$wp_customize->add_section(
		'atelier_noir_legal_page',
		array(
			'title'       => __( 'Atelier Noir — Legal pages', 'atelier-noir' ),
			'description' => __( 'Company and hosting details shown on Legal Notice, Terms of Sale and Privacy Policy. Empty fields keep a [To be completed] placeholder on the site.', 'atelier-noir' ),
			'priority'    => 37,
		)
	);

	$legal_fields = array(
		'atelier_noir_legal_form'              => array(
			'label'   => __( 'Legal form', 'atelier-noir' ),
			'default' => '',
			'placeholder' => 'SASU / SARL / Sole proprietor…',
		),
		'atelier_noir_legal_share_capital'     => array(
			'label'   => __( 'Share capital', 'atelier-noir' ),
			'default' => '',
			'placeholder' => 'To be completed',
		),
		'atelier_noir_legal_registered_office' => array(
			'label'   => __( 'Registered office (full address)', 'atelier-noir' ),
			'default' => '',
			'placeholder' => 'Full address',
		),
		'atelier_noir_legal_siret'             => array(
			'label'   => __( 'SIRET number', 'atelier-noir' ),
			'default' => '',
			'placeholder' => 'SIRET number',
		),
		'atelier_noir_legal_vat'               => array(
			'label'   => __( 'EU VAT number', 'atelier-noir' ),
			'default' => '',
			'placeholder' => 'FR XX XXX XXX XXX',
		),
		'atelier_noir_legal_publication_director' => array(
			'label'   => __( 'Publication director', 'atelier-noir' ),
			'default' => '',
			'placeholder' => 'Full name',
		),
		'atelier_noir_legal_host_name'         => array(
			'label'   => __( 'Hosting provider — name', 'atelier-noir' ),
			'default' => '',
			'placeholder' => 'Production hosting provider name',
		),
		'atelier_noir_legal_host_address'      => array(
			'label'   => __( 'Hosting provider — address', 'atelier-noir' ),
			'default' => '',
			'placeholder' => 'Hosting provider address',
		),
		'atelier_noir_legal_host_website'      => array(
			'label'   => __( 'Hosting provider — website URL', 'atelier-noir' ),
			'default' => '',
			'placeholder' => 'Hosting provider URL',
		),
		'atelier_noir_legal_mediator'            => array(
			'label'   => __( 'Consumer mediator', 'atelier-noir' ),
			'default' => '',
			'placeholder' => 'Name and contact details of the mediator',
		),
		'atelier_noir_legal_delivery_zones'      => array(
			'label'       => __( 'Delivery zones (Terms of Sale)', 'atelier-noir' ),
			'description' => __( 'Optional extra text after the default delivery paragraph.', 'atelier-noir' ),
			'type'        => 'textarea',
			'default'     => '',
			'placeholder' => 'Specify zones if necessary',
		),
		'atelier_noir_legal_email_provider'    => array(
			'label'   => __( 'Email service provider (Privacy)', 'atelier-noir' ),
			'default' => '',
			'placeholder' => 'Brevo / Postmark',
		),
	);

	foreach ( $legal_fields as $id => $args ) {
		$sanitize = ( isset( $args['type'] ) && 'textarea' === $args['type'] ) ? 'sanitize_textarea_field' : 'sanitize_text_field';
		if ( 'atelier_noir_legal_host_website' === $id ) {
			$sanitize = 'esc_url_raw';
		}

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => $sanitize,
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $args['label'],
				'description' => $args['description'] ?? '',
				'section'     => 'atelier_noir_legal_page',
				'type'        => $args['type'] ?? 'text',
			)
		);
	}
}
add_action( 'customize_register', 'atelier_noir_customize_register_legal_contact', 36 );
