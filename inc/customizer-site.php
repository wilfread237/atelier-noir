<?php
/**
 * Personnaliseur : contenu site (accueil, services, contact, typo, animations).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HTML minimal pour formulaires (Personnaliseur — réservé aux administrateurs).
 *
 * @param string $html Contenu.
 * @return string
 */
function atelier_noir_sanitize_form_html( $html ) {
	$allowed = array(
		'p'        => array(),
		'br'       => array(),
		'label'    => array(),
		'input'    => array(
			'type'     => array(),
			'name'     => array(),
			'value'    => array(),
			'required' => array(),
			'class'    => array(),
		),
		'select'   => array(
			'name'  => array(),
			'class' => array(),
		),
		'option'   => array(
			'value'    => array(),
			'selected' => array(),
		),
		'textarea' => array(
			'name'  => array(),
			'rows'  => array(),
			'class' => array(),
		),
		'button'   => array(
			'type'  => array(),
			'class' => array(),
		),
	);
	return wp_kses( (string) $html, $allowed );
}

/**
 * Formulaire contact : HTML structuré ou shortcode (CF7, etc.).
 *
 * @param string $html Contenu.
 * @return string
 */
function atelier_noir_sanitize_contact_block( $html ) {
	$html = (string) $html;
	if ( preg_match( '/\[[\w_\-]+[^\]]*\]/', $html ) ) {
		return sanitize_textarea_field( $html );
	}
	return atelier_noir_sanitize_form_html( $html );
}

/**
 * Enregistrement des contrôles (priorité après couleurs / loader).
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function atelier_noir_customize_register_site( $wp_customize ) {
	$wp_customize->add_section(
		'atelier_noir_mode',
		array(
			'title'       => __( 'Atelier Noir — display mode', 'atelier-noir' ),
			'description' => __( 'Choose whether the homepage is driven by the Customizer or by the page (Elementor / editor).', 'atelier-noir' ),
			'priority'    => 28,
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_home_source',
		array(
			'default'           => 'auto',
			'sanitize_callback' => function ( $value ) {
				$ok = array( 'auto', 'customizer', 'page' );
				return in_array( $value, $ok, true ) ? $value : 'auto';
			},
		)
	);
	$wp_customize->add_control(
		'atelier_noir_home_source',
		array(
			'label'   => __( 'Homepage content source', 'atelier-noir' ),
			'section' => 'atelier_noir_mode',
			'type'    => 'select',
			'choices' => array(
				'auto'        => __( 'Auto: Customizer if page is empty, otherwise page (Elementor)', 'atelier-noir' ),
				'customizer'  => __( 'Always Customizer (ignore Elementor on homepage)', 'atelier-noir' ),
				'page'        => __( 'Always page (Elementor / blocks)', 'atelier-noir' ),
			),
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_animations_on',
		array(
			'default'           => true,
			'sanitize_callback' => function ( $v ) {
				return (bool) $v;
			},
		)
	);
	$wp_customize->add_control(
		'atelier_noir_animations_on',
		array(
			'label'   => __( 'Enable GSAP animations on homepage', 'atelier-noir' ),
			'section' => 'atelier_noir_mode',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_home_show_journal',
		array(
			'default'           => false,
			'sanitize_callback' => function ( $v ) {
				return (bool) $v;
			},
		)
	);
	$wp_customize->add_control(
		'atelier_noir_home_show_journal',
		array(
			'label'   => __( 'Homepage: show Project (blog) preview', 'atelier-noir' ),
			'section' => 'atelier_noir_mode',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_home_show_shop_teaser',
		array(
			'default'           => false,
			'sanitize_callback' => function ( $v ) {
				return (bool) $v;
			},
		)
	);
	$wp_customize->add_control(
		'atelier_noir_home_show_shop_teaser',
		array(
			'label'       => __( 'Homepage: show shop preview (WooCommerce)', 'atelier-noir' ),
			'description' => __( 'Requires WooCommerce to be active.', 'atelier-noir' ),
			'section'     => 'atelier_noir_mode',
			'type'        => 'checkbox',
		)
	);

	$font_choices = array(
		'System'             => __( 'System', 'atelier-noir' ),
		'Inter'              => 'Inter',
		'Cormorant Garamond' => 'Cormorant Garamond',
		'Italiana'           => 'Italiana',
		'Syne'               => 'Syne',
		'Bodoni Moda'        => 'Bodoni Moda',
		'Outfit'             => 'Outfit',
		'DM Sans'            => 'DM Sans',
	);

	$wp_customize->add_section(
		'atelier_noir_typo',
		array(
			'title'    => __( 'Atelier Noir — typography', 'atelier-noir' ),
			'priority' => 29,
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_font_heading',
		array(
			'default'           => 'Outfit',
			'sanitize_callback'   => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'atelier_noir_font_heading',
		array(
			'label'   => __( 'Heading font', 'atelier-noir' ),
			'section' => 'atelier_noir_typo',
			'type'    => 'select',
			'choices' => $font_choices,
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_font_body',
		array(
			'default'           => 'Inter',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'atelier_noir_font_body',
		array(
			'label'   => __( 'Body font', 'atelier-noir' ),
			'section' => 'atelier_noir_typo',
			'type'    => 'select',
			'choices' => $font_choices,
		)
	);

	$wp_customize->add_section(
		'atelier_noir_brand_logos',
		array(
			'title'       => __( 'Atelier Noir — Logo', 'atelier-noir' ),
			'description' => __( 'Single logo used site-wide (header, footer, legal overlay). Upload in Media or paste an image URL. Default: bundled Atelier Noir wordmark.', 'atelier-noir' ),
			'priority'    => 31,
		)
	);

	$logo_default = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'logo-header-desktop.png' )
		: '';

	$wp_customize->add_setting(
		'an_logo_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'an_logo_attachment',
			array(
				'label'     => __( 'Site logo', 'atelier-noir' ),
				'section'   => 'atelier_noir_brand_logos',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'an_logo_url',
		array(
			'default'           => $logo_default,
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'an_logo_url',
		array(
			'label'       => __( 'Site logo URL (fallback)', 'atelier-noir' ),
			'description' => __( 'Used when no file is selected above.', 'atelier-noir' ),
			'section'     => 'atelier_noir_brand_logos',
			'type'        => 'url',
		)
	);

	/* Legacy keys — kept for backward compatibility, not shown in UI. */
	$wp_customize->add_setting(
		'atelier_noir_logo_desktop_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_logo_mobile_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_logo_desktop_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_logo_mobile_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_section(
		'atelier_noir_home_eyes',
		array(
			'title'       => __( 'Atelier Noir — Home: Eyes catcher', 'atelier-noir' ),
			'description' => __( 'Full-screen hero video. Upload the file in Media, copy its file URL, and paste it below — or use the media picker. Direct .mp4 / .webm / .mov links and YouTube URLs are supported.', 'atelier-noir' ),
			'priority'    => 32,
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_eyes_video_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'atelier_noir_eyes_video_attachment',
			array(
				'label'      => __( 'Hero video (media library)', 'atelier-noir' ),
				'section'    => 'atelier_noir_home_eyes',
				'mime_type'  => 'video',
				'description'=> __( 'MP4/WebM upload. Takes priority over the external URL below.', 'atelier-noir' ),
			)
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_eyes_poster_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'atelier_noir_eyes_poster_attachment',
			array(
				'label'     => __( 'Poster image (media library)', 'atelier-noir' ),
				'section'   => 'atelier_noir_home_eyes',
				'mime_type' => 'image',
			)
		)
	);

	$eyes_fields = array(
		'atelier_noir_eyes_video_url'  => array(
			'label' => __( 'Hero video URL (from Media library or external)', 'atelier-noir' ),
			'type'  => 'url',
		),
		'atelier_noir_eyes_poster_url' => array( 'label' => __( 'Poster image URL (optional)', 'atelier-noir' ), 'type' => 'url' ),
		'atelier_noir_eyes_kicker'       => array( 'label' => __( 'Short label (eyebrow)', 'atelier-noir' ), 'type' => 'text' ),
		'atelier_noir_eyes_title_line1' => array( 'label' => __( 'Overlay title — line 1', 'atelier-noir' ), 'type' => 'text', 'default' => 'Atelier' ),
		'atelier_noir_eyes_title_line2' => array( 'label' => __( 'Overlay title — line 2', 'atelier-noir' ), 'type' => 'text', 'default' => 'Noir' ),
		'atelier_noir_eyes_tagline'     => array( 'label' => __( 'Commercial tagline (optional)', 'atelier-noir' ), 'type' => 'textarea', 'default' => '' ),
	);

	foreach ( $eyes_fields as $id => $cfg ) {
		$sanitize = 'url' === $cfg['type'] ? 'esc_url_raw' : ( 'textarea' === $cfg['type'] ? 'sanitize_textarea_field' : 'sanitize_text_field' );
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $cfg['default'] ?? '',
				'sanitize_callback' => $sanitize,
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $cfg['label'],
				'description' => ( 'atelier_noir_eyes_tagline' === $id )
					? __( 'Short commercial phrase displayed below the title.', 'atelier-noir' )
					: '',
				'section'     => 'atelier_noir_home_eyes',
				'type'        => 'textarea' === $cfg['type'] ? 'textarea' : ( 'url' === $cfg['type'] ? 'url' : 'text' ),
			)
		);
	}

	$wp_customize->add_section(
		'atelier_noir_home_studio',
		array(
			'title'       => __( 'Atelier Noir — Home: Founder biography', 'atelier-noir' ),
			'description' => __( 'CEO portrait and biography (Elementor home section).', 'atelier-noir' ),
			'priority'    => 33,
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_founder_image_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'atelier_noir_founder_image_attachment',
			array(
				'label'     => __( 'Founder portrait (media library)', 'atelier-noir' ),
				'section'   => 'atelier_noir_home_studio',
				'mime_type' => 'image',
			)
		)
	);

	$studio = array(
		'atelier_noir_founder_image_url' => array( 'label' => __( 'Founder portrait URL', 'atelier-noir' ), 'type' => 'url' ),
		'atelier_noir_studio_label'      => array( 'label' => __( 'Section eyebrow', 'atelier-noir' ), 'type' => 'text' ),
		'atelier_noir_studio_title'      => array( 'label' => __( 'Headline', 'atelier-noir' ), 'type' => 'text' ),
		'atelier_noir_studio_text'       => array( 'label' => __( 'Biography', 'atelier-noir' ), 'type' => 'textarea' ),
		'atelier_noir_studio_cta_label'   => array( 'label' => __( 'Button label', 'atelier-noir' ), 'type' => 'text' ),
		'atelier_noir_studio_cta_url'    => array( 'label' => __( 'Button link', 'atelier-noir' ), 'type' => 'url' ),
	);

	foreach ( $studio as $id => $cfg ) {
		$sanitize = 'sanitize_textarea_field';
		if ( 'url' === $cfg['type'] ) {
			$sanitize = 'esc_url_raw';
		} elseif ( 'text' === $cfg['type'] ) {
			$sanitize = 'sanitize_text_field';
		}
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => $sanitize,
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $cfg['label'],
				'section' => 'atelier_noir_home_studio',
				'type'    => 'textarea' === $cfg['type'] ? 'textarea' : ( 'url' === $cfg['type'] ? 'url' : 'text' ),
			)
		);
	}

	$wp_customize->add_section(
		'atelier_noir_services_panel',
		array(
			'title'       => __( 'Atelier Noir — Services page', 'atelier-noir' ),
			'description' => __( 'Full-screen service slides. Each service links to Contact via ?service=slug. Use direct MP4/WebM video URLs from the Media library.', 'atelier-noir' ),
			'priority'    => 34,
		)
	);

	// Contact page copy: edit with Elementor (Pages → Contact → Edit with Elementor).

	// ── Popup newsletter (s’affiche sur le site public) ─────────────────────
	$wp_customize->add_section(
		'atelier_noir_newsletter_popup',
		array(
			'title'       => __( 'Atelier Noir — popup newsletter', 'atelier-noir' ),
			'description' => __( 'Newsletter popup copy and visual (shown after a few seconds). Subscribe and dismiss buttons are managed by the theme.', 'atelier-noir' ),
			'priority'    => 36,
		)
	);

	$nl_defaults = array(
		'atelier_noir_nl_eyebrow'    => atelier_noir_brand_name(),
		'atelier_noir_nl_title_1'    => 'Be among',
		'atelier_noir_nl_title_2'    => 'the first to know.',
		'atelier_noir_nl_subtitle'   => 'Editorials, behind the scenes, and exclusive previews.',
		'atelier_noir_nl_privacy'    => 'Your data stays confidential. Unsubscribe anytime.',
		'atelier_noir_nl_visual_url' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&q=80',
	);

	foreach ( array( 'atelier_noir_nl_eyebrow', 'atelier_noir_nl_title_1', 'atelier_noir_nl_title_2' ) as $nl_id ) {
		$wp_customize->add_setting(
			$nl_id,
			array(
				'default'           => $nl_defaults[ $nl_id ],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$nl_id,
			array(
				'label'   => 'atelier_noir_nl_eyebrow' === $nl_id
					? __( 'Eyebrow (small text above)', 'atelier-noir' )
					: ( 'atelier_noir_nl_title_1' === $nl_id ? __( 'Headline — line 1', 'atelier-noir' ) : __( 'Headline — line 2', 'atelier-noir' ) ),
				'section' => 'atelier_noir_newsletter_popup',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_setting(
		'atelier_noir_nl_subtitle',
		array(
			'default'           => $nl_defaults['atelier_noir_nl_subtitle'],
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'atelier_noir_nl_subtitle',
		array(
			'label'   => __( 'Subtitle', 'atelier-noir' ),
			'section' => 'atelier_noir_newsletter_popup',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_nl_privacy',
		array(
			'default'           => $nl_defaults['atelier_noir_nl_privacy'],
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'atelier_noir_nl_privacy',
		array(
			'label'   => __( 'Privacy note (below the form)', 'atelier-noir' ),
			'section' => 'atelier_noir_newsletter_popup',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_nl_visual_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'atelier_noir_nl_visual_attachment',
			array(
				'label'     => __( 'Left panel image (media library)', 'atelier-noir' ),
				'section'   => 'atelier_noir_newsletter_popup',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_nl_visual_url',
		array(
			'default'           => $nl_defaults['atelier_noir_nl_visual_url'],
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'atelier_noir_nl_visual_url',
		array(
			'label'       => __( 'Image — external URL (if no media library image)', 'atelier-noir' ),
			'description' => __( 'Used only when no image is selected above.', 'atelier-noir' ),
			'section'     => 'atelier_noir_newsletter_popup',
			'type'        => 'url',
		)
	);
}
add_action( 'customize_register', 'atelier_noir_customize_register_site', 35 );

/**
 * Chargement conditionnel des polices Google (hors polices embarquées dans assets/fonts).
 */
function atelier_noir_enqueue_google_fonts() {
	$h = get_theme_mod( 'atelier_noir_font_heading', 'Outfit' );
	$b = get_theme_mod( 'atelier_noir_font_body', 'Inter' );

	$self_hosted = array( 'System', 'Outfit', 'Inter' );

	$families = array();
	if ( $h && ! in_array( $h, $self_hosted, true ) ) {
		$families[] = str_replace( ' ', '+', $h ) . ':wght@400;600';
	}
	if ( $b && ! in_array( $b, $self_hosted, true ) && $b !== $h ) {
		$families[] = str_replace( ' ', '+', $b ) . ':wght@300;400;500';
	} elseif ( $b && ! in_array( $b, $self_hosted, true ) && $b === $h ) {
		$families[] = str_replace( ' ', '+', $b ) . ':wght@300;400;500;600';
	}

	if ( empty( $families ) ) {
		return;
	}

	$query = implode( '&family=', array_unique( $families ) );
	$url   = 'https://fonts.googleapis.com/css2?family=' . $query . '&display=swap';

	wp_enqueue_style(
		'atelier-noir-fonts',
		esc_url( $url ),
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'atelier_noir_enqueue_google_fonts', 5 );

/**
 * Variables CSS pour les polices.
 */
function atelier_noir_typography_css() {
	$h = get_theme_mod( 'atelier_noir_font_heading', 'Outfit' );
	$b = get_theme_mod( 'atelier_noir_font_body', 'Inter' );

	$h_stack = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
	$b_stack = $h_stack;

	if ( $h && 'System' !== $h ) {
		$h_stack = '"' . esc_attr( $h ) . '", "Times New Roman", serif';
	}
	if ( $b && 'System' !== $b ) {
		$b_stack = '"' . esc_attr( $b ) . '", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
	}

	$css = ':root { --studio-font-heading: ' . $h_stack . '; --studio-font-body: ' . $b_stack . '; }
		h1, h2, h3, .section-title, .studio-site-loader__brand { font-family: var(--studio-font-heading); }
		body { font-family: var(--studio-font-body); }';

	wp_add_inline_style( 'atelier-noir', $css );
}
add_action( 'wp_enqueue_scripts', 'atelier_noir_typography_css', 32 );

/**
 * Affiche un bloc formulaire (HTML autorisé ou shortcodes).
 *
 * @param string $theme_mod_key Clé theme_mod.
 */
function atelier_noir_echo_form_block( $theme_mod_key ) {
	$raw = get_theme_mod( $theme_mod_key, '' );
	$raw = is_string( $raw ) ? $raw : '';
	if ( '' === trim( $raw ) ) {
		return;
	}

	$trim = ltrim( $raw );
	if ( '[' === substr( $trim, 0, 1 ) || preg_match( '/\[[\w_\-]+[\s\S]*\]/', $raw ) ) {
		echo do_shortcode( $raw );
		return;
	}

	$allowed = array(
		'p'        => array(),
		'br'       => array(),
		'label'    => array(),
		'input'    => array(
			'type'     => array(),
			'name'     => array(),
			'value'    => array(),
			'required' => array(),
			'class'    => array(),
		),
		'select'   => array(
			'name'  => array(),
			'class' => array(),
		),
		'option'   => array(
			'value'    => array(),
			'selected' => array(),
		),
		'textarea' => array(
			'name'  => array(),
			'rows'  => array(),
			'class' => array(),
		),
		'button'   => array(
			'type'  => array(),
			'class' => array(),
		),
	);
	echo wp_kses( $raw, $allowed );
}
