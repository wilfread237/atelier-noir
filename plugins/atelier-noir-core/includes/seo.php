<?php
/**
 * SEO — Open Graph : logo brand sur toutes les pages partagées.
 *
 * Priorité d'image OG :
 *  1. Customizer (an_og_default_image_url) — surcharge manuelle
 *  2. Asset thème assets/images/brand/og-default.jpg (1200×630, logo centré fond blanc)
 *
 * Yoast : titre, image et description forcés (écrase les réglages par page, ex. Home).
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Titre OG/Twitter unique (nom de marque). */
function atelier_noir_og_brand_title(): string {
	return strtoupper( atelier_noir_brand_name() );
}

/**
 * URL de l'image Open Graph par défaut (logo fond blanc).
 *
 * @return string
 */
function atelier_noir_og_default_image_url(): string {
	$custom = (string) atelier_noir_get_theme_mod( 'an_og_default_image_url', '' );
	if ( '' !== $custom ) {
		return esc_url_raw( $custom );
	}

	if ( function_exists( 'atelier_noir_brand_asset_uri' ) ) {
		return atelier_noir_brand_asset_uri( 'og-default.jpg' );
	}

	return '';
}

/**
 * @return bool
 */
function atelier_noir_force_og_default_image(): bool {
	return (bool) apply_filters( 'atelier_noir_force_og_default_image', true );
}

/**
 * @return string
 */
function atelier_noir_resolve_og_image(): string {
	$url = atelier_noir_og_default_image_url();
	return '' !== $url ? $url : '';
}

/**
 * Image OG structurée pour Yoast (URL + dimensions).
 *
 * @return array<string, int|string>|null
 */
function atelier_noir_og_image_payload(): ?array {
	$url = atelier_noir_resolve_og_image();
	if ( '' === $url ) {
		return null;
	}

	return array(
		'url'    => $url,
		'width'  => 1200,
		'height' => 630,
	);
}

/**
 * Supprime les images sociales Yoast par page (ex. portrait fond rose sur Home).
 *
 * @param int $page_id Page ID.
 */
function atelier_noir_clear_yoast_page_social_images( int $page_id ): void {
	if ( $page_id <= 0 ) {
		return;
	}

	foreach (
		array(
			'_yoast_wpseo_opengraph-image',
			'_yoast_wpseo_opengraph-image-id',
			'_yoast_wpseo_twitter-image',
			'_yoast_wpseo_twitter-image-id',
		) as $meta_key
	) {
		delete_post_meta( $page_id, $meta_key );
	}
}

/**
 * Nettoie les meta Yoast Social sur Home + Loader (une fois).
 */
function atelier_noir_clear_yoast_home_social_images(): void {
	if ( get_option( 'an_yoast_home_social_cleared_v2', false ) ) {
		return;
	}

	$page_ids = array_filter(
		array(
			function_exists( 'atelier_noir_get_home_page_id' )
				? (int) atelier_noir_get_home_page_id()
				: 0,
			function_exists( 'atelier_noir_get_loader_page_id' )
				? (int) atelier_noir_get_loader_page_id()
				: (int) get_option( 'page_on_front', 0 ),
		)
	);

	foreach ( array_unique( $page_ids ) as $page_id ) {
		atelier_noir_clear_yoast_page_social_images( (int) $page_id );
	}

	update_option( 'an_yoast_home_social_cleared_v2', time(), false );
}
add_action( 'init', 'atelier_noir_clear_yoast_home_social_images', 20 );

/**
 * Yoast SEO — titre, image, description (toutes pages).
 */
add_filter(
	'wpseo_opengraph_title',
	static function () {
		return atelier_noir_og_brand_title();
	},
	999
);

add_filter(
	'wpseo_twitter_title',
	static function () {
		return atelier_noir_og_brand_title();
	},
	999
);

add_filter(
	'wpseo_opengraph_desc',
	static function () {
		return '';
	},
	999
);

add_filter(
	'wpseo_twitter_description',
	static function () {
		return '';
	},
	999
);

add_filter(
	'wpseo_twitter_card_type',
	static function () {
		return 'summary_large_image';
	},
	999
);

/*
 * Yoast 14+ : titre/description uniquement (ne pas injecter open_graph_images :
 * Yoast 28 attend des objets Image internes — un stdClass provoque une erreur fatale).
 */
add_filter(
	'wpseo_frontend_presentation',
	static function ( $presentation ) {
		if ( ! is_object( $presentation ) ) {
			return $presentation;
		}

		$presentation->open_graph_title       = atelier_noir_og_brand_title();
		$presentation->open_graph_description = '';
		$presentation->twitter_title          = atelier_noir_og_brand_title();
		$presentation->twitter_description    = '';

		if ( atelier_noir_force_og_default_image() ) {
			$url = atelier_noir_resolve_og_image();
			if ( '' !== $url ) {
				$presentation->twitter_image = $url;
			}
			// opengraph_image_id = 0 empêche Image_Presenter d’émettre og:image malgré le filtre URL.
			if ( property_exists( $presentation, 'open_graph_image_id' ) && 0 === (int) $presentation->open_graph_image_id ) {
				$presentation->open_graph_image_id = null;
			}
		}

		return $presentation;
	},
	999
);

add_filter(
	'wpseo_opengraph_image',
	static function ( $image ) {
		if ( ! atelier_noir_force_og_default_image() ) {
			return ! empty( $image ) ? $image : atelier_noir_resolve_og_image();
		}
		$default = atelier_noir_resolve_og_image();
		return '' !== $default ? $default : $image;
	},
	999
);

add_filter(
	'wpseo_twitter_image',
	static function ( $image ) {
		if ( ! atelier_noir_force_og_default_image() ) {
			return ! empty( $image ) ? $image : atelier_noir_resolve_og_image();
		}
		$default = atelier_noir_resolve_og_image();
		return '' !== $default ? $default : $image;
	},
	999
);

/**
 * Yoast 14+ : si l’image OG par défaut est forcée, on retire les presenters
 * image Yoast (opengraph_image_id = 0 les bloque) et on émet nos balises en wp_head.
 */
add_filter(
	'wpseo_frontend_presenter_classes',
	static function ( $presenters ) {
		if ( ! atelier_noir_force_og_default_image() || ! is_array( $presenters ) ) {
			return $presenters;
		}

		$blocked = array(
			'Yoast\\WP\\SEO\\Presenters\\Open_Graph\\Image_Presenter',
			'Yoast\\WP\\SEO\\Presenters\\Twitter\\Image_Presenter',
		);

		return array_values(
			array_filter(
				$presenters,
				static function ( $class ) use ( $blocked ) {
					return ! in_array( $class, $blocked, true );
				}
			)
		);
	},
	999
);

/**
 * Balises OG/Twitter image (Yoast 28 : wpseo_opengraph / wpseo_twitter supprimés depuis v14).
 */
add_action(
	'wp_head',
	static function () {
		if ( ! defined( 'WPSEO_VERSION' ) || ! atelier_noir_force_og_default_image() ) {
			return;
		}

		$payload = atelier_noir_og_image_payload();
		if ( null === $payload ) {
			return;
		}

		printf(
			'<meta property="og:image" content="%s" />' . "\n" .
			'<meta property="og:image:width" content="%d" />' . "\n" .
			'<meta property="og:image:height" content="%d" />' . "\n" .
			'<meta name="twitter:image" content="%s" />' . "\n",
			esc_url( (string) $payload['url'] ),
			(int) $payload['width'],
			(int) $payload['height'],
			esc_url( (string) $payload['url'] )
		);
	},
	99
);

/**
 * All in One SEO — fallback si actif à la place de Yoast.
 */
add_filter(
	'aioseo_opengraph_image',
	static function ( $image ) {
		if ( ! atelier_noir_force_og_default_image() ) {
			return ! empty( $image ) ? $image : atelier_noir_resolve_og_image();
		}
		$default = atelier_noir_resolve_og_image();
		return '' !== $default ? $default : $image;
	},
	999
);

add_filter(
	'aioseo_twitter_image',
	static function ( $image ) {
		if ( ! atelier_noir_force_og_default_image() ) {
			return ! empty( $image ) ? $image : atelier_noir_resolve_og_image();
		}
		$default = atelier_noir_resolve_og_image();
		return '' !== $default ? $default : $image;
	},
	999
);

add_filter(
	'aioseo_facebook_tags',
	static function ( $tags ) {
		if ( ! is_array( $tags ) ) {
			return $tags;
		}
		$tags['og:title']       = atelier_noir_og_brand_title();
		$tags['og:description'] = '';
		$image                  = atelier_noir_resolve_og_image();
		if ( '' !== $image ) {
			$tags['og:image'] = $image;
		}
		return $tags;
	},
	999
);

/**
 * Fallback meta OG si aucun plugin SEO actif.
 */
add_action(
	'wp_head',
	static function () {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
			return;
		}
		$url = atelier_noir_resolve_og_image();
		if ( '' === $url ) {
			return;
		}
		printf(
			'<meta property="og:type" content="website">' . "\n" .
			'<meta property="og:title" content="%s">' . "\n" .
			'<meta property="og:image" content="%s">' . "\n" .
			'<meta property="og:image:width" content="1200">' . "\n" .
			'<meta property="og:image:height" content="630">' . "\n" .
			'<meta name="twitter:card" content="summary_large_image">' . "\n" .
			'<meta name="twitter:title" content="%s">' . "\n" .
			'<meta name="twitter:image" content="%s">' . "\n",
			esc_attr( atelier_noir_og_brand_title() ),
			esc_url( $url ),
			esc_attr( atelier_noir_og_brand_title() ),
			esc_url( $url )
		);
	},
	5
);

/**
 * Customizer : image OG par défaut (surcharge optionnelle).
 */
add_action(
	'customize_register',
	static function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_setting(
			'an_og_default_image_url',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'an_og_default_image_url',
				array(
					'section'     => 'title_tagline',
					'label'       => __( 'Image Open Graph par défaut', 'atelier-noir-core' ),
					'description' => __( 'Remplace og-default.jpg (1200×630). Laissez vide pour l’asset thème.', 'atelier-noir-core' ),
					'mime_type'   => 'image',
					'priority'    => 42,
				)
			)
		);
	},
	25
);
