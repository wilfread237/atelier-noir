<?php
/**
 * Import content: Elementor pages (EN), posts, WooCommerce products, menu.
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Génère un identifiant court type Elementor.
 *
 * @return string
 */
function atelier_noir_el_new_id() {
	return substr( md5( uniqid( (string) wp_rand(), true ) ), 0, 7 );
}

/**
 * Applique un document Elementor (tableau de sections).
 *
 * @param int   $post_id  ID publication.
 * @param array $elements Éléments racine (sections).
 * @return bool
 */
function atelier_noir_apply_elementor_document( $post_id, array $elements ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	$json = wp_json_encode( $elements, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	if ( ! is_string( $json ) ) {
		return false;
	}

	$ver = defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.18.0';

	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $post_id, '_elementor_data', wp_slash( $json ) );
	update_post_meta( $post_id, '_elementor_version', $ver );
	delete_post_meta( $post_id, '_elementor_css' );

	return true;
}

/**
 * Colonne Elementor avec widgets.
 *
 * @param int   $size Taille colonne (ex. 100, 50).
 * @param array $widgets Liste de widgets.
 * @return array
 */
function atelier_noir_el_column( $size, array $widgets ) {
	return array(
		'id'         => atelier_noir_el_new_id(),
		'elType'     => 'column',
		'isInner'    => false,
		'settings'   => array(
			'_column_size' => (int) $size,
			'width'        => (string) (int) $size,
		),
		'elements'   => $widgets,
	);
}

/**
 * Section Elementor.
 *
 * @param array $columns  Colonnes.
 * @param array $settings Réglages section.
 * @return array
 */
function atelier_noir_el_section( array $columns, array $settings = array() ) {
	$defaults = array(
		'layout' => 'full_width',
		'gap'    => 'default',
	);
	return array(
		'id'       => atelier_noir_el_new_id(),
		'elType'   => 'section',
		'isInner'  => false,
		'settings' => array_merge( $defaults, $settings ),
		'elements' => $columns,
	);
}

/**
 * @param string $title .
 * @param string $tag   h1–h6.
 * @param array  $more  Réglages additionnels.
 * @return array
 */
function atelier_noir_el_heading( $title, $tag = 'h1', $more = array() ) {
	return array(
		'id'         => atelier_noir_el_new_id(),
		'elType'     => 'widget',
		'widgetType' => 'heading',
		'isInner'    => false,
		'settings'   => array_merge(
			array(
				'title'       => $title,
				'header_size' => $tag,
				'align'       => 'left',
			),
			$more
		),
		'elements'   => array(),
	);
}

/**
 * @param string $html HTML.
 * @return array
 */
function atelier_noir_el_text_editor( $html ) {
	return array(
		'id'         => atelier_noir_el_new_id(),
		'elType'     => 'widget',
		'widgetType' => 'text-editor',
		'isInner'    => false,
		'settings'   => array(
			'editor' => $html,
		),
		'elements'   => array(),
	);
}

/**
 * @param string $label .
 * @param string $url   URL absolue ou relative.
 * @return array
 */
function atelier_noir_el_button( $label, $url ) {
	return array(
		'id'         => atelier_noir_el_new_id(),
		'elType'     => 'widget',
		'widgetType' => 'button',
		'isInner'    => false,
		'settings'   => array(
			'text' => $label,
			'link' => array(
				'url' => $url,
				'is_external'       => '',
				'nofollow'          => '',
				'custom_attributes' => '',
			),
			'align' => 'left',
		),
		'elements'   => array(),
	);
}

/**
 * @param string $url .
 * @return array
 */
function atelier_noir_el_image( $url ) {
	return array(
		'id'         => atelier_noir_el_new_id(),
		'elType'     => 'widget',
		'widgetType' => 'image',
		'isInner'    => false,
		'settings'   => array(
			'image'      => array(
				'url' => $url,
				'id'          => '',
				'size'        => '',
				'alt'         => '',
				'source'      => 'url',
				'placeholder' => 'https://via.placeholder.com/800x1000',
			),
			'image_size' => 'full',
		),
		'elements'   => array(),
	);
}

/**
 * @param string $html HTML brut.
 * @return array
 */
function atelier_noir_el_html( $html ) {
	return array(
		'id'         => atelier_noir_el_new_id(),
		'elType'     => 'widget',
		'widgetType' => 'html',
		'isInner'    => false,
		'settings'   => array(
			'html' => $html,
		),
		'elements'   => array(),
	);
}

/**
 * @param string $shortcode Shortcode WooCommerce / WordPress.
 * @return array
 */
function atelier_noir_el_shortcode( $shortcode ) {
	return array(
		'id'         => atelier_noir_el_new_id(),
		'elType'     => 'widget',
		'widgetType' => 'shortcode',
		'isInner'    => false,
		'settings'   => array(
			'shortcode' => $shortcode,
		),
		'elements'   => array(),
	);
}

/**
 * Video widget (YouTube or self-hosted).
 *
 * @param string $url Video URL.
 * @return array
 */
function atelier_noir_el_video( $url ) {
	$url = esc_url_raw( $url );
	$is_youtube = (bool) preg_match( '#(?:youtube\.com|youtu\.be)#i', $url );

	$settings = array(
		'autoplay'      => 'yes',
		'mute'          => 'yes',
		'loop'          => 'yes',
		'play_on_mobile'=> 'yes',
		'controls'      => '',
		'aspect_ratio'  => '169',
	);

	if ( $is_youtube ) {
		$settings['video_type']   = 'youtube';
		$settings['youtube_url']    = $url;
	} else {
		$settings['video_type']   = 'hosted';
		$settings['hosted_url']   = array( 'url' => $url );
	}

	return array(
		'id'         => atelier_noir_el_new_id(),
		'elType'     => 'widget',
		'widgetType' => 'video',
		'isInner'    => false,
		'settings'   => $settings,
		'elements'   => array(),
	);
}

/**
 * Crée ou met à jour une page.
 *
 * @param string               $slug  Slug.
 * @param string               $title Titre.
 * @param array<string, mixed> $args  postarr additionnel.
 * @return int ID page.
 */
function atelier_noir_import_get_or_create_page( $slug, $title, array $args = array() ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $page && ! is_wp_error( $page ) ) {
		$update = array_merge(
			array(
				'ID'          => $page->ID,
				'post_title'  => $title,
				'post_status' => 'publish',
			),
			$args
		);
		wp_update_post( $update );
		return (int) $page->ID;
	}

	$insert = array_merge(
		array(
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		),
		$args
	);

	$id = wp_insert_post( $insert, true );
	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * Données Elementor — Accueil.
 *
 * @return array
 */
function atelier_noir_import_elementor_home() {
	$shop = function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0
		? get_permalink( wc_get_page_id( 'shop' ) )
		: home_url( '/shop/' );

	$video_url  = atelier_noir_resolve_media_url( 'atelier_noir_eyes_video_url', 'atelier_noir_eyes_video_attachment' );
	$poster_url = atelier_noir_resolve_media_url( 'atelier_noir_eyes_poster_url', 'atelier_noir_eyes_poster_attachment' );
	$kicker = atelier_noir_home_mod( 'atelier_noir_eyes_kicker' );

	$founder_img = atelier_noir_resolve_media_url( 'atelier_noir_founder_image_url', 'atelier_noir_founder_image_attachment' );
	$label       = atelier_noir_home_mod( 'atelier_noir_studio_label' );
	$title       = atelier_noir_home_mod( 'atelier_noir_studio_title' );
	$bio         = atelier_noir_home_mod( 'atelier_noir_studio_text' );

	$full_height = array(
		'layout'        => 'full_width',
		'height'        => 'min-height',
		'custom_height' => array(
			'unit'  => 'vh',
			'size'  => 100,
			'sizes' => array(),
		),
	);

	$hero_widgets = array();
	if ( '' !== $kicker ) {
		$hero_widgets[] = atelier_noir_el_text_editor(
			'<p style="font-size:0.65rem;letter-spacing:0.28em;text-transform:uppercase;color:rgba(255,255,255,0.55)">' . esc_html( $kicker ) . '</p>'
		);
	}
	$hero_title_html = atelier_noir_render_eyes_hero_title_html();
	if ( '' !== $hero_title_html ) {
		$hero_widgets[] = atelier_noir_el_html( $hero_title_html );
	}

	$hero_pad = array(
		'unit'     => 'px',
		'top'      => '120',
		'bottom'   => '80',
		'left'     => '48',
		'right'    => '48',
		'isLinked' => false,
	);

	$hero_settings = array_merge(
		$full_height,
		array(
			'css_id'                => 'studio-hero',
			'background_background' => 'classic',
			'background_color'      => '#0A0A0A',
			'padding'               => $hero_pad,
			'content_position'      => 'bottom',
		)
	);

	if ( '' !== $video_url ) {
		$hero_settings['background_background']     = 'video';
		$hero_settings['background_video_link']     = esc_url_raw( $video_url );
		$hero_settings['background_play_once']      = '';
		$hero_settings['background_play_on_mobile'] = 'yes';
		if ( '' !== $poster_url ) {
			$hero_settings['background_video_fallback'] = array( 'url' => esc_url_raw( $poster_url ), 'id' => 0 );
			$hero_settings['background_image']          = array( 'url' => esc_url_raw( $poster_url ), 'id' => 0 );
		}
	}

	$s1 = atelier_noir_el_section(
		array(
			atelier_noir_el_column( 100, $hero_widgets ),
		),
		$hero_settings
	);

	$cta_l = atelier_noir_home_mod( 'atelier_noir_studio_cta_label' );
	$cta_u = atelier_noir_home_mod( 'atelier_noir_studio_cta_url' );
	if ( '' === $cta_u ) {
		$cta_u = home_url( '/services/' );
	}

	$founder_title = '' !== $title ? $title : 'THE ATELIER';
	$founder_lead  = '' !== $bio ? $bio : 'Boldness. Authenticity. Creative direction with a singular point of view.';

	$founder_widgets = array(
		atelier_noir_el_html(
			'<div class="studio-ceo-editorial__inner">'
			. '<div class="studio-ceo-editorial__copy">'
			. '<h2 class="studio-ceo-editorial__title">' . esc_html( $founder_title ) . '</h2>'
			. '<p class="studio-ceo-editorial__keywords">Boldness · Authenticity</p>'
			. '<p class="studio-ceo-editorial__lead">' . esc_html( $founder_lead ) . '</p>'
			. ( '' !== $cta_l
				? '<a href="' . esc_url( $cta_u ) . '" class="studio-ceo-editorial__cta">' . esc_html( $cta_l ) . '</a>'
				: '' )
			. '</div></div>'
		),
	);

	$founder_bg = '' !== $founder_img
		? $founder_img
		: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=1920&q=85';

	$s2 = atelier_noir_el_section(
		array(
			atelier_noir_el_column( 100, $founder_widgets ),
		),
		array_merge(
			$full_height,
			array(
				'css_id'                         => 'studio-founder',
				'background_background'          => 'classic',
				'background_color'               => '#0A0A0A',
				'background_image'               => array( 'url' => esc_url_raw( $founder_bg ), 'id' => 0 ),
				'background_position'            => 'center right',
				'background_size'                => 'cover',
				'background_overlay_background'  => 'classic',
				'background_overlay_color'       => 'rgba(0,0,0,0.42)',
				'content_position'               => 'middle',
				'padding'                        => array(
					'unit'     => 'px',
					'top'      => '0',
					'bottom'   => '0',
					'left'     => '48',
					'right'    => '48',
					'isLinked' => false,
				),
			)
		)
	);

	$shop_widgets = array(
		atelier_noir_el_shortcode( '[atelier_noir_home_merch_teaser]' ),
	);

	$s3 = atelier_noir_el_section(
		array(
			atelier_noir_el_column( 100, $shop_widgets ),
		),
		array_merge(
			$full_height,
			array(
				'css_id'           => 'studio-merch-preview',
				'background_color' => '#0A0A0A',
				'padding'          => array(
					'unit'     => 'px',
					'top'      => '80',
					'bottom'   => '80',
					'left'     => '48',
					'right'    => '48',
					'isLinked' => false,
				),
			)
		)
	);

	return array( $s1, $s2, $s3 );
}

/**
 * Restore home section CSS IDs + dark backgrounds when Elementor data lost them.
 *
 * @param int $home_id Front page ID.
 * @return bool
 */
function atelier_noir_repair_home_elementor_sections( $home_id = 0 ) {
	$home_id = $home_id > 0 ? $home_id : (int) get_option( 'page_on_front', 0 );
	if ( $home_id <= 0 || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	$raw      = get_post_meta( $home_id, '_elementor_data', true );
	$sections = is_string( $raw ) ? json_decode( wp_unslash( $raw ), true ) : $raw;
	if ( ! is_array( $sections ) || empty( $sections ) ) {
		return false;
	}

	$updated = false;

	foreach ( $sections as &$section ) {
		if ( ! is_array( $section ) || 'section' !== ( $section['elType'] ?? '' ) ) {
			continue;
		}

		$has_founder = atelier_noir_el_section_contains_class( $section, 'studio-ceo-editorial' )
			|| atelier_noir_el_section_contains_class( $section, 'studio-founder-eyebrow' );
		$has_merch   = atelier_noir_el_section_contains_class( $section, 'studio-merch-editorial' )
			|| atelier_noir_el_section_contains_class( $section, 'studio-merch-eyebrow' );

		if ( $has_founder ) {
			if ( empty( $section['settings']['css_id'] ) ) {
				$section['settings']['css_id'] = 'studio-founder';
				$updated                       = true;
			}
			if ( empty( $section['settings']['background_color'] ) ) {
				$section['settings']['background_background'] = 'classic';
				$section['settings']['background_color']      = '#0A0A0A';
				$updated                                      = true;
			}
		}

		if ( $has_merch ) {
			if ( empty( $section['settings']['css_id'] ) ) {
				$section['settings']['css_id'] = 'studio-merch-preview';
				$updated                       = true;
			}
			if ( empty( $section['settings']['background_color'] ) ) {
				$section['settings']['background_background'] = 'classic';
				$section['settings']['background_color']      = '#0A0A0A';
				$updated                                      = true;
			}
		}
	}
	unset( $section );

	if ( ! $updated ) {
		return false;
	}

	update_post_meta(
		$home_id,
		'_elementor_data',
		wp_slash( wp_json_encode( $sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) )
	);
	delete_post_meta( $home_id, '_elementor_css' );
	\Elementor\Plugin::$instance->files_manager->clear_cache();

	return true;
}

/**
 * Walk Elementor tree for a marker class in widget HTML.
 *
 * @param array  $node  Element.
 * @param string $class Class name.
 * @return bool
 */
function atelier_noir_el_section_contains_class( array $node, $class ) {
	if ( isset( $node['settings']['editor'] ) && is_string( $node['settings']['editor'] ) ) {
		if ( str_contains( $node['settings']['editor'], $class ) ) {
			return true;
		}
	}
	if ( isset( $node['settings']['html'] ) && is_string( $node['settings']['html'] ) ) {
		if ( str_contains( $node['settings']['html'], $class ) ) {
			return true;
		}
	}

	if ( ! empty( $node['elements'] ) && is_array( $node['elements'] ) ) {
		foreach ( $node['elements'] as $child ) {
			if ( is_array( $child ) && atelier_noir_el_section_contains_class( $child, $class ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * One-time repair after theme update (admin only).
 */
function atelier_noir_maybe_repair_home_elementor_sections() {
	if ( ! is_admin() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	if ( get_option( 'atelier_noir_home_sections_repaired_v4', false ) ) {
		return;
	}
	if ( atelier_noir_repair_home_elementor_sections() ) {
		update_option( 'atelier_noir_home_sections_repaired_v4', 1, false );
	}
}
add_action( 'admin_init', 'atelier_noir_maybe_repair_home_elementor_sections' );

/**
 * One-time home layout upgrade (editorial CEO + shop rail).
 */
function atelier_noir_maybe_upgrade_home_layout_v5() {
	if ( ! is_admin() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	if ( get_option( 'atelier_noir_home_layout_v5', false ) ) {
		return;
	}
	if ( ! class_exists( '\Elementor\Plugin' ) || ! function_exists( 'atelier_noir_apply_elementor_document' ) ) {
		return;
	}
	$home_id = atelier_noir_get_home_page_id();
	if ( $home_id <= 0 ) {
		return;
	}
	atelier_noir_apply_elementor_document( $home_id, atelier_noir_import_elementor_home() );
	if ( function_exists( 'atelier_noir_import_create_products' ) ) {
		atelier_noir_import_create_products();
	}
	\Elementor\Plugin::$instance->files_manager->clear_cache();
	update_option( 'atelier_noir_home_layout_v5', 1, false );
}
add_action( 'admin_init', 'atelier_noir_maybe_upgrade_home_layout_v5', 20 );

/**
 * Replace split merch widgets so products render inside .studio-merch-editorial__products.
 *
 * @param int $home_id Front page ID.
 * @return bool
 */
function atelier_noir_repair_home_merch_teaser_widget( $home_id = 0 ) {
	$home_id = $home_id > 0 ? $home_id : (int) get_option( 'page_on_front', 0 );
	if ( $home_id <= 0 || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	$raw      = get_post_meta( $home_id, '_elementor_data', true );
	$sections = is_string( $raw ) ? json_decode( wp_unslash( $raw ), true ) : $raw;
	if ( ! is_array( $sections ) || empty( $sections ) ) {
		return false;
	}

	$updated = false;

	foreach ( $sections as &$section ) {
		if ( ! is_array( $section ) || 'section' !== ( $section['elType'] ?? '' ) ) {
			continue;
		}

		$has_merch = atelier_noir_el_section_contains_class( $section, 'studio-merch-editorial' )
			|| atelier_noir_el_section_contains_class( $section, 'studio-merch-eyebrow' )
			|| ( isset( $section['settings']['css_id'] ) && 'studio-merch-preview' === $section['settings']['css_id'] );

		if ( ! $has_merch || empty( $section['elements'] ) || ! is_array( $section['elements'] ) ) {
			continue;
		}

		foreach ( $section['elements'] as &$column ) {
			if ( ! is_array( $column ) || 'column' !== ( $column['elType'] ?? '' ) ) {
				continue;
			}
			$column['elements'] = array(
				atelier_noir_el_shortcode( '[atelier_noir_home_merch_teaser]' ),
			);
			$updated            = true;
		}
		unset( $column );
	}
	unset( $section );

	if ( ! $updated ) {
		return false;
	}

	update_post_meta(
		$home_id,
		'_elementor_data',
		wp_slash( wp_json_encode( $sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) )
	);
	delete_post_meta( $home_id, '_elementor_css' );
	\Elementor\Plugin::$instance->files_manager->clear_cache();

	return true;
}

/**
 * One-time repair: merch teaser shortcode inside single Elementor widget.
 */
function atelier_noir_maybe_upgrade_home_layout_v6() {
	if ( ! is_admin() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	if ( get_option( 'atelier_noir_home_layout_v6', false ) ) {
		return;
	}
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return;
	}
	atelier_noir_repair_home_merch_teaser_widget();
	update_option( 'atelier_noir_home_layout_v6', 1, false );
}
add_action( 'admin_init', 'atelier_noir_maybe_upgrade_home_layout_v6', 21 );

/**
 * Sideload an external image into the Media library.
 *
 * @param string $url     Image URL.
 * @param int    $post_id Parent post ID.
 * @return int Attachment ID or 0.
 */
function atelier_noir_import_sideload_image( $url, $post_id = 0 ) {
	$url = esc_url_raw( (string) $url );
	if ( '' === $url ) {
		return 0;
	}

	if ( ! function_exists( 'media_sideload_image' ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$tmp = download_url( $url );
	if ( is_wp_error( $tmp ) ) {
		return 0;
	}

	$file_array = array(
		'name'     => basename( wp_parse_url( $url, PHP_URL_PATH ) ?: 'product.jpg' ),
		'tmp_name' => $tmp,
	);

	$att_id = media_handle_sideload( $file_array, $post_id );
	if ( is_wp_error( $att_id ) ) {
		@unlink( $tmp );
		return 0;
	}

	return (int) $att_id;
}

/**
 * Assign featured + gallery images to a product from external URLs.
 *
 * @param int   $product_id Product ID.
 * @param array $urls       Image URLs.
 */
function atelier_noir_import_product_set_images( $product_id, array $urls ) {
	$product_id = (int) $product_id;
	if ( $product_id <= 0 || empty( $urls ) ) {
		return;
	}

	$attachment_ids = array();
	foreach ( $urls as $url ) {
		$att_id = atelier_noir_import_sideload_image( $url, $product_id );
		if ( $att_id > 0 ) {
			$attachment_ids[] = $att_id;
		}
	}

	if ( empty( $attachment_ids ) ) {
		// Sideload may fail locally (network) — keep remote URLs for card rendering.
		$clean_urls = array_values( array_filter( array_map( 'esc_url_raw', $urls ) ) );
		if ( ! empty( $clean_urls ) ) {
			update_post_meta( $product_id, '_atelier_noir_fallback_image_urls', $clean_urls );
		}
		return;
	}

	delete_post_meta( $product_id, '_atelier_noir_fallback_image_urls' );

	set_post_thumbnail( $product_id, $attachment_ids[0] );
	if ( count( $attachment_ids ) > 1 ) {
		update_post_meta( $product_id, '_product_image_gallery', implode( ',', array_slice( $attachment_ids, 1 ) ) );
	}
}

/**
 * Données Elementor — Services (plein écran par bloc).
 *
 * @return array
 */
function atelier_noir_import_elementor_services() {
	$contact = home_url( '/contact/' );
	$blocks = array(
		array(
			'title' => 'Fashion consulting',
			'text'  => 'Image and collection strategy: positioning, editorial thread and creative decisions alongside the founder.',
			'img'   => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1920&q=80',
		),
		array(
			'title' => 'Personal styling & branding',
			'text'  => 'Wardrobe, silhouette and visual narrative — for a presence aligned with your standards.',
			'img'   => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1920&q=80',
		),
		array(
			'title' => 'Artistic direction',
			'text'  => 'Campaigns, lookbooks and digital: creative direction from brief to final image.',
			'img'   => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=1920&q=80',
		),
		array(
			'title' => 'Events',
			'text'  => 'Launches and brand experiences: scenography and direction for defining moments.',
			'img'   => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1920&q=80',
		),
	);

	$sections = array(
		atelier_noir_el_section(
			array(
				atelier_noir_el_column(
					100,
					array(
						atelier_noir_el_heading( 'Services', 'h1', array( 'title_color' => '#FFFFFF' ) ),
						atelier_noir_el_text_editor(
							'<p style="color:rgba(255,255,255,0.9)">Four areas of expertise at Atelier Noir. Refine visuals and copy here or via the PHP template « Atelier Noir — Services » and the Customizer.</p>'
						),
					)
				),
			),
			array(
				'layout'                => 'full_width',
				'background_background' => 'classic',
				'background_color'      => '#111111',
				'padding'               => array(
					'unit'     => 'px',
					'top'      => '100',
					'bottom'   => '48',
					'left'     => '40',
					'right'    => '40',
					'isLinked' => false,
				),
			)
		),
	);

	foreach ( $blocks as $b ) {
		$sections[] = atelier_noir_el_section(
			array(
				atelier_noir_el_column(
					100,
					array(
						atelier_noir_el_heading( $b['title'], 'h2', array( 'title_color' => '#FFFFFF' ) ),
						atelier_noir_el_text_editor(
							'<p style="color:rgba(255,255,255,0.95)">' . esc_html( $b['text'] ) . '</p>'
						),
						atelier_noir_el_button( 'Book a session', $contact ),
					)
				),
			),
			array(
				'layout'                       => 'full_width',
				'height'                       => 'min-height',
				'custom_height'                => array(
					'unit'  => 'vh',
					'size'  => 85,
					'sizes' => array(),
				),
				'background_background'        => 'classic',
				'background_image'             => array(
					'url'         => $b['img'],
					'id'          => '',
					'size'        => '',
					'alt'         => '',
					'source'      => 'url',
					'placeholder' => '',
				),
				'background_position'          => 'center center',
				'background_size'            => 'cover',
				'background_overlay_background' => 'classic',
				'background_overlay_color'     => 'rgba(0,0,0,0.55)',
				'padding' => array(
					'unit'     => 'px',
					'top'      => '120',
					'bottom'   => '80',
					'left'     => '40',
					'right'    => '40',
					'isLinked' => false,
				),
			)
		);
	}

	return $sections;
}

/**
 * Données Elementor — Contact.
 *
 * @return array
 */
function atelier_noir_import_elementor_contact() {
	$form_html = '<form class="atelier-noir-contact-sample" method="post" action="#" style="max-width:520px">'
		. '<p><label style="display:block;margin-bottom:0.35rem">Name</label><input type="text" name="nom" required style="width:100%;padding:0.5rem"></p>'
		. '<p><label style="display:block;margin-bottom:0.35rem">Email</label><input type="email" name="email" required style="width:100%;padding:0.5rem"></p>'
		. '<p><label style="display:block;margin-bottom:0.35rem">Message</label><textarea name="message" rows="5" style="width:100%;padding:0.5rem"></textarea></p>'
		. '<p><button type="submit" class="button" style="padding:0.65rem 1.5rem;border:0;cursor:pointer">' . esc_html__( 'Send', 'atelier-noir-core' ) . '</button></p>'
		. '</form>';

	$s1 = atelier_noir_el_section(
		array(
			atelier_noir_el_column(
				100,
				array(
					atelier_noir_el_heading( 'Contact', 'h1', array( 'title_color' => '#FAF7F2' ) ),
					atelier_noir_el_text_editor(
						'<p style="color:rgba(250,247,242,0.92)">A question, a project or a collaboration with Atelier Noir? Use this form or write to us directly by email. For service bookings, the theme page « Atelier Noir — Contact » handles email delivery.</p>'
					),
					atelier_noir_el_html( $form_html ),
					atelier_noir_el_text_editor(
						'<p style="color:rgba(250,247,242,0.75);font-size:0.9rem">' . esc_html( atelier_noir_brand_name() ) . ' · <a href="mailto:' . esc_attr( atelier_noir_email_contact() ) . '" style="color:inherit">' . esc_html( atelier_noir_email_contact() ) . '</a> · orders: <a href="mailto:' . esc_attr( atelier_noir_email_orders() ) . '" style="color:inherit">' . esc_html( atelier_noir_email_orders() ) . '</a></p>'
					),
				)
			),
		),
		array(
			'layout'                => 'full_width',
			'min_height'            => array(
				'unit'  => 'vh',
				'size'  => 70,
				'sizes' => array(),
			),
			'background_background' => 'classic',
			'background_color'      => '#3D2B1F',
			'padding'               => array(
				'unit'     => 'px',
				'top'      => '100',
				'bottom'   => '80',
				'left'     => '40',
				'right'    => '40',
				'isLinked' => false,
			),
		)
	);

	return array( $s1 );
}

/**
 * Intro blog (posts page).
 *
 * @return array
 */
function atelier_noir_import_elementor_project() {
	return array(
		atelier_noir_el_section(
			array(
				atelier_noir_el_column(
					100,
					array(
						atelier_noir_el_heading( 'Project', 'h1' ),
						atelier_noir_el_text_editor(
							'<p>Studio notes, behind the scenes and perspectives on fashion — the view from the founder and the house.</p>'
						),
					)
				),
			),
			array(
				'layout'                => 'full_width',
				'background_background' => 'classic',
				'background_color'      => '#FFFFFF',
				'padding'               => array(
					'unit'     => 'px',
					'top'      => '72',
					'bottom'   => '32',
					'left'     => '24',
					'right'    => '24',
					'isLinked' => false,
				),
			)
		),
	);
}

/** @deprecated Use atelier_noir_import_elementor_project() */
function atelier_noir_import_elementor_journal() {
	return atelier_noir_import_elementor_project();
}

/**
 * Boutique — page shop (contenu au-dessus du catalogue si le thème l’affiche ; sinon modifiable dans Elementor pour usage futur).
 *
 * @return array
 */
function atelier_noir_import_elementor_shop() {
	return array(
		atelier_noir_el_section(
			array(
				atelier_noir_el_column(
					100,
					array(
						atelier_noir_el_heading( 'Shop', 'h1' ),
						atelier_noir_el_text_editor(
							'<p>Atelier Noir selection: pieces and editions available online. Replace images, prices and descriptions when the final catalogue is ready.</p>'
						),
					)
				),
			),
			array(
				'layout'                => 'full_width',
				'padding'               => array(
					'unit'     => 'px',
					'top'      => '48',
					'bottom'   => '24',
					'left'     => '24',
					'right'    => '24',
					'isLinked' => false,
				),
			)
		),
	);
}

/**
 * Crée le menu principal (liens vers les pages importées).
 *
 * @param array<string, int> $page_ids slug => ID.
 */
function atelier_noir_import_rebuild_menu( array $page_ids ) {
	$menu_name = 'Studio menu principal';
	$menu_id   = 0;
	foreach ( wp_get_nav_menus() as $term ) {
		if ( isset( $term->name ) && $menu_name === $term->name ) {
			$menu_id = (int) $term->term_id;
			break;
		}
	}
	if ( ! $menu_id ) {
		$created = wp_create_nav_menu( $menu_name );
		if ( is_wp_error( $created ) ) {
			return;
		}
		$menu_id = (int) $created;
	}
	if ( ! $menu_id ) {
		return;
	}

	$items = wp_get_nav_menu_items( $menu_id );
	if ( $items ) {
		foreach ( $items as $item ) {
			if ( ! empty( $item->ID ) ) {
				wp_delete_post( (int) $item->ID, true );
			}
		}
	}

	$order = array( 'home', 'accueil', 'services', 'project' );
	if ( ! empty( $page_ids['boutique'] ) || ! empty( $page_ids['shop'] ) ) {
		$order[] = ! empty( $page_ids['shop'] ) ? 'shop' : 'boutique';
	}
	$order[] = 'contact';

	$pos = 0;
	foreach ( $order as $slug ) {
		if ( empty( $page_ids[ $slug ] ) ) {
			continue;
		}
		$titles = array(
			'home'     => 'Home',
			'accueil'  => 'Home',
			'services' => 'Services',
			'project'  => 'Project',
			'journal'  => 'Project',
			'boutique' => 'Shop',
			'shop'     => 'Shop',
			'contact'  => 'Contact',
		);
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => $titles[ $slug ] ?? get_the_title( $page_ids[ $slug ] ),
				'menu-item-object-id' => $page_ids[ $slug ],
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => ++$pos,
			)
		);
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! is_array( $locations ) ) {
		$locations = array();
	}
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Sample blog posts (English).
 */
function atelier_noir_import_create_posts() {
	$posts = array(
		array(
			'title'       => 'Behind the scenes: from moodboard to set',
			'slug'        => 'behind-the-scenes-campaign',
			'legacy_slug' => 'coulisses-campagne-printemps',
			'excerpt'     => 'How the Studio aligns references, casting and light before the shutter clicks.',
			'content'     => '<p>Every campaign starts with a clear intention: what the brand wants people to feel before the first frame. At Atelier Noir, we move from moodboard to silhouettes and light scouting so that on shoot day, the team moves in one direction.</p><p>Replace this note with a real client project when ready.</p>',
		),
		array(
			'title'       => 'Elegance as a brand language',
			'slug'        => 'elegance-as-brand-language',
			'legacy_slug' => 'elegance-comme-langage',
			'excerpt'     => 'Why a coherent image matters as much as the product.',
			'content'     => '<p>In contemporary fashion, elegance is not only about cut — it is a language of color, rhythm, silence and voice. We help brands define that language across campaigns, shop and social without contradiction.</p>',
		),
		array(
			'title'       => 'Editorial selection: texture and light',
			'slug'        => 'editorial-textures-light',
			'legacy_slug' => 'selection-editoriale-textures',
			'excerpt'     => 'Inspiration for a season where material tells the story.',
			'content'     => '<p>Texture, reflection and contrast: levers that give depth to a collection. Add your own visuals once final media is chosen with the client.</p>',
		),
		array(
			'title'   => 'Studio diary: shaping a collection narrative',
			'slug'    => 'studio-diary-collection-narrative',
			'excerpt' => 'Notes on building a coherent story from sketch to campaign.',
			'content' => '<p>From first sketches to final delivery, narrative consistency is what separates a lookbook from a world. This fourth Project entry completes the launch grid — replace with your own editorial when ready.</p>',
		),
	);

	foreach ( $posts as $p ) {
		$found = get_posts(
			array(
				'name'           => $p['slug'],
				'post_type'      => 'post',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( empty( $found[0] ) && ! empty( $p['legacy_slug'] ) ) {
			$found = get_posts(
				array(
					'name'           => $p['legacy_slug'],
					'post_type'      => 'post',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);
		}
		if ( ! empty( $found[0] ) ) {
			wp_update_post(
				array(
					'ID'           => (int) $found[0],
					'post_title'   => $p['title'],
					'post_name'    => $p['slug'],
					'post_content' => $p['content'],
					'post_excerpt' => $p['excerpt'],
					'post_status'  => 'publish',
				)
			);
			continue;
		}
		wp_insert_post(
			array(
				'post_title'   => $p['title'],
				'post_name'    => $p['slug'],
				'post_content' => $p['content'],
				'post_excerpt' => $p['excerpt'],
				'post_status'  => 'publish',
				'post_type'    => 'post',
			),
			true
		);
	}
}

/**
 * ID produit par SKU (compatibilité WooCommerce).
 *
 * @param string $sku SKU.
 * @return int
 */
function atelier_noir_import_wc_product_id_by_sku( $sku ) {
	if ( function_exists( 'wc_get_product_id_by_sku' ) ) {
		return (int) wc_get_product_id_by_sku( $sku );
	}
	global $wpdb;
	return (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value = %s LIMIT 1", $sku ) );
}

/**
 * Whether a product still needs demo images (no image or Woo placeholder).
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function atelier_noir_product_needs_demo_images( $product_id ) {
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return false;
	}

	$image_id = $product->get_image_id();
	if ( ! $image_id ) {
		return true;
	}

	$url = wp_get_attachment_url( $image_id );
	if ( ! is_string( $url ) || '' === $url ) {
		return true;
	}

	return str_contains( $url, 'woocommerce-placeholder' ) || str_contains( $url, 'placeholder' );
}

/**
 * Demo image URLs by product slug (when Media sideload is unavailable).
 *
 * @param string $slug Product slug.
 * @return string[]
 */
function atelier_noir_demo_product_image_urls_for_slug( $slug ) {
	$catalog = atelier_noir_get_demo_product_catalog();
	foreach ( $catalog as $row ) {
		$match_slugs = array( $row['slug'] );
		if ( ! empty( $row['legacy_slug'] ) ) {
			$match_slugs[] = $row['legacy_slug'];
		}
		if ( ! empty( $row['legacy_slugs'] ) && is_array( $row['legacy_slugs'] ) ) {
			$match_slugs = array_merge( $match_slugs, $row['legacy_slugs'] );
		}
		if ( in_array( $slug, $match_slugs, true ) ) {
			return isset( $row['images'] ) ? (array) $row['images'] : array();
		}
	}
	return array();
}

/**
 * @return array<int, array<string, mixed>>
 */
function atelier_noir_get_demo_product_catalog() {
	return array(
		array(
			'name'        => 'Leather tote — studio edition',
			'slug'        => 'leather-tote-studio-edition',
			'legacy_slug' => 'cabas-cuir-edition-studio',
			'price'       => '890',
			'desc'        => 'Structured tote, clean lines — signature piece for the Atelier Noir shop. Replace copy and photography when the real product and stock are available.',
			'short'       => 'Studio edition — full-grain leather tote.',
			'images'      => array(
				'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=900&q=80',
				'https://images.unsplash.com/photo-1590874103328-eac92a696e10?w=900&q=80',
			),
		),
		array(
			'name'        => 'Oversized poplin shirt',
			'slug'        => 'oversized-poplin-shirt',
			'legacy_slug' => 'chemise-oversize-popeline',
			'price'       => '320',
			'desc'        => 'Oversized poplin shirt with generous cut and refined interior finishes. Wardrobe essential for the Studio line — adjust per validated catalogue.',
			'short'       => 'Wardrobe essential — poplin.',
			'images'      => array(
				'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=900&q=80',
				'https://images.unsplash.com/photo-1624378515193-8e48fd8cd7d5?w=900&q=80',
				'https://images.unsplash.com/photo-1603252109303-2751441dd157?w=900&q=80',
			),
		),
		array(
			'name'        => 'Cashmere scarf',
			'slug'        => 'cashmere-scarf',
			'legacy_slug' => 'echarpe-cachemire-demo',
			'legacy_slugs'=> array( 'echarpe-cachemire-demo', 'echarpe-cachemire' ),
			'price'       => '185',
			'desc'        => 'Cashmere scarf in a neutral tone, soft hand-feel. Accessory from the online selection.',
			'short'       => 'Cashmere — sand tone.',
			'images'      => array(
				'https://images.unsplash.com/photo-1520903920243-fd6cee0a3a2a?w=900&q=80',
				'https://images.unsplash.com/photo-1576566588025-4b387fff3efd?w=900&q=80',
			),
		),
	);
}

/**
 * Produits WooCommerce fictifs.
 */
function atelier_noir_import_create_products() {
	if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Product_Simple' ) ) {
		return;
	}

	$catalog = atelier_noir_get_demo_product_catalog();

	foreach ( $catalog as $row ) {
		$sku         = 'spj-' . $row['slug'];
		$existing_id = atelier_noir_import_wc_product_id_by_sku( $sku );

		if ( ! $existing_id && ! empty( $row['legacy_slug'] ) ) {
			$legacy = get_page_by_path( $row['legacy_slug'], OBJECT, 'product' );
			if ( $legacy ) {
				$existing_id = (int) $legacy->ID;
			}
		}
		if ( ! $existing_id && ! empty( $row['legacy_slug'] ) ) {
			$legacy_sku = 'spj-' . $row['legacy_slug'];
			$existing_id = atelier_noir_import_wc_product_id_by_sku( $legacy_sku );
		}

		if ( $existing_id ) {
			$product = wc_get_product( $existing_id );
			if ( $product ) {
				$product->set_name( $row['name'] );
				$product->set_slug( $row['slug'] );
				$product->set_sku( $sku );
				$product->set_regular_price( $row['price'] );
				$product->set_description( $row['desc'] );
				$product->set_short_description( $row['short'] );
				$product->set_catalog_visibility( 'visible' );
				$product->save();
				if ( ! empty( $row['images'] ) && atelier_noir_product_needs_demo_images( $existing_id ) ) {
					atelier_noir_import_product_set_images( $existing_id, $row['images'] );
				}
				if ( ! empty( $row['images'] ) ) {
					$fallback = get_post_meta( $existing_id, '_atelier_noir_fallback_image_urls', true );
					if ( empty( $fallback ) && atelier_noir_product_needs_demo_images( $existing_id ) ) {
						update_post_meta( $existing_id, '_atelier_noir_fallback_image_urls', array_map( 'esc_url_raw', $row['images'] ) );
					}
				}
			}
			continue;
		}

		$product = new WC_Product_Simple();
		$product->set_name( $row['name'] );
		$product->set_slug( $row['slug'] );
		$product->set_regular_price( $row['price'] );
		$product->set_description( $row['desc'] );
		$product->set_short_description( $row['short'] );
		$product->set_catalog_visibility( 'visible' );
		$product->set_status( 'publish' );
		$product->set_sku( 'spj-' . $row['slug'] );
		$new_id = $product->save();
		if ( $new_id && ! empty( $row['images'] ) ) {
			atelier_noir_import_product_set_images( (int) $new_id, $row['images'] );
		}
	}
}

/**
 * Assure une page boutique WooCommerce.
 *
 * @return int ID page shop.
 */
function atelier_noir_import_ensure_shop_page() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return 0;
	}

	$shop_id = (int) wc_get_page_id( 'shop' );
	if ( $shop_id > 0 && get_post( $shop_id ) ) {
		return $shop_id;
	}

	$shop_id = atelier_noir_import_get_or_create_page(
		'shop',
		'Shop',
		array()
	);
	if ( $shop_id ) {
		update_option( 'woocommerce_shop_page_id', $shop_id );
	}

	return $shop_id;
}

/**
 * Migre ou crée une page avec slug anglais canonique.
 *
 * @param string[] $legacy_slugs   Anciens slugs acceptés.
 * @param string   $canonical_slug Slug cible.
 * @param string   $title          Titre.
 * @param string   $template       Template PHP optionnel.
 * @return int
 */
function atelier_noir_import_migrate_page( array $legacy_slugs, $canonical_slug, $title, $template = '' ) {
	$page_id = 0;
	foreach ( $legacy_slugs as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page && ! is_wp_error( $page ) ) {
			$page_id = (int) $page->ID;
			break;
		}
	}

	if ( $page_id ) {
		wp_update_post(
			array(
				'ID'          => $page_id,
				'post_title'  => $title,
				'post_name'   => $canonical_slug,
				'post_status' => 'publish',
			)
		);
	} else {
		$page_id = atelier_noir_import_get_or_create_page( $canonical_slug, $title, array() );
	}

	if ( $page_id && $template ) {
		update_post_meta( $page_id, '_wp_page_template', $template );
		delete_post_meta( $page_id, '_elementor_data' );
		delete_post_meta( $page_id, '_elementor_edit_mode' );
		delete_post_meta( $page_id, '_elementor_css' );
	}

	return (int) $page_id;
}

/**
 * Elementor starter — legal / info page.
 *
 * @param string $title Page title.
 * @return array
 */
function atelier_noir_import_elementor_legal( $title ) {
	return array(
		atelier_noir_el_section(
			array(
				atelier_noir_el_column(
					100,
					array(
						atelier_noir_el_heading( $title, 'h1' ),
						atelier_noir_el_text_editor(
							'<p>Edit this page with Elementor. Replace placeholder copy with your legal information.</p>'
						),
					)
				),
			),
			array(
				'layout'           => 'full_width',
				'background_color' => '#F5F3EF',
				'padding'          => array(
					'unit'     => 'px',
					'top'      => '100',
					'bottom'   => '80',
					'left'     => '48',
					'right'    => '48',
					'isLinked' => false,
				),
			)
		),
	);
}

/**
 * Pages légales (slugs anglais, éditables Elementor).
 *
 * @return array<string, int>
 */
function atelier_noir_import_legal_pages() {
	$map = array(
		'legal-notice'         => array(
			'legacy'   => array( 'mentions-legales' ),
			'title'    => 'Legal Notice',
			'template' => 'template-mentions-legales.php',
		),
		'privacy-policy'       => array(
			'legacy'   => array( 'politique-de-confidentialite' ),
			'title'    => 'Privacy Policy',
			'template' => 'template-politique-confidentialite.php',
		),
		'terms-and-conditions' => array(
			'legacy'   => array( 'cgv' ),
			'title'    => 'Terms of Sale',
			'template' => 'template-cgv.php',
		),
	);

	$ids = array();
	foreach ( $map as $slug => $row ) {
		$page_id = atelier_noir_import_migrate_page(
			array_merge( array( $slug ), $row['legacy'] ),
			$slug,
			$row['title'],
			''
		);
		if ( $page_id ) {
			update_post_meta( $page_id, '_wp_page_template', $row['template'] );
		}
		$ids[ $slug ] = $page_id;
	}
	return $ids;
}

/**
 * Réapplique le contenu Elementor accueil (anglais) une fois après mise à jour thème.
 */
function atelier_noir_maybe_refresh_home_elementor() {
	if ( wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}
	$version = 'home-en-9';
	if ( $version === get_option( 'atelier_noir_home_elementor_v', '' ) ) {
		return;
	}
	if ( ! class_exists( '\Elementor\Plugin' ) || ! function_exists( 'atelier_noir_apply_elementor_document' ) ) {
		return;
	}

	$home_id = atelier_noir_get_home_page_id();
	if ( $home_id <= 0 ) {
		return;
	}

	atelier_noir_apply_elementor_document( $home_id, atelier_noir_import_elementor_home() );
	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	update_option( 'atelier_noir_home_elementor_v', $version, false );
}
add_action( 'init', 'atelier_noir_maybe_refresh_home_elementor', 25 );

/**
 * Remove Elementor document meta from a page (restore PHP template rendering).
 *
 * @param int $page_id Page ID.
 */
function atelier_noir_clear_elementor_page( $page_id ) {
	$page_id = (int) $page_id;
	if ( $page_id <= 0 ) {
		return;
	}

	delete_post_meta( $page_id, '_elementor_data' );
	delete_post_meta( $page_id, '_elementor_edit_mode' );
	delete_post_meta( $page_id, '_elementor_css' );
	delete_post_meta( $page_id, '_elementor_version' );
	delete_post_meta( $page_id, '_elementor_page_settings' );
	delete_post_meta( $page_id, '_elementor_page_assets' );

	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}

/**
 * Restore PHP templates for Contact + legal pages (one-time after theme update).
 */
function atelier_noir_restore_php_content_pages() {
	$map = array(
		array(
			'slugs'    => array( 'contact' ),
			'template' => 'template-contact.php',
		),
		array(
			'slugs'    => array( 'legal-notice', 'mentions-legales' ),
			'template' => 'template-mentions-legales.php',
		),
		array(
			'slugs'    => array( 'privacy-policy', 'politique-de-confidentialite' ),
			'template' => 'template-politique-confidentialite.php',
		),
		array(
			'slugs'    => array( 'terms-and-conditions', 'cgv' ),
			'template' => 'template-cgv.php',
		),
	);

	foreach ( $map as $row ) {
		foreach ( $row['slugs'] as $slug ) {
			$page = get_page_by_path( $slug, OBJECT, 'page' );
			if ( ! $page ) {
				continue;
			}
			update_post_meta( $page->ID, '_wp_page_template', $row['template'] );
			atelier_noir_clear_elementor_page( (int) $page->ID );
		}
	}
}

/**
 * One-time: Contact + legal pages back to PHP templates (not Elementor stubs).
 */
function atelier_noir_maybe_restore_php_content_pages() {
	if ( 'php-pages-1' === get_option( 'atelier_noir_php_content_pages_v', '' ) ) {
		return;
	}
	atelier_noir_restore_php_content_pages();
	update_option( 'atelier_noir_php_content_pages_v', 'php-pages-1', false );
}
add_action( 'init', 'atelier_noir_maybe_restore_php_content_pages', 26 );

/**
 * Replace French CEO keywords in saved Elementor home data (one-time).
 */
function atelier_noir_repair_home_ceo_keywords_english() {
	$home_id = atelier_noir_get_home_page_id();
	if ( $home_id <= 0 || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	$raw = get_post_meta( $home_id, '_elementor_data', true );
	if ( ! is_string( $raw ) || '' === $raw ) {
		return false;
	}

	$replaced = str_replace(
		array( 'Audace · Authenticité', 'Audace. Authenticité.' ),
		array( 'Boldness · Authenticity', 'Boldness. Authenticity.' ),
		$raw
	);

	if ( $replaced === $raw ) {
		return false;
	}

	update_post_meta( $home_id, '_elementor_data', wp_slash( $replaced ) );
	delete_post_meta( $home_id, '_elementor_css' );
	\Elementor\Plugin::$instance->files_manager->clear_cache();

	return true;
}

function atelier_noir_maybe_repair_home_ceo_keywords_english() {
	if ( 'ceo-keywords-en-1' === get_option( 'atelier_noir_ceo_keywords_en_v', '' ) ) {
		return;
	}
	atelier_noir_repair_home_ceo_keywords_english();
	update_option( 'atelier_noir_ceo_keywords_en_v', 'ceo-keywords-en-1', false );
}
add_action( 'init', 'atelier_noir_maybe_repair_home_ceo_keywords_english', 27 );

/**
 * Re-sync demo product titles to English (one-time).
 */
function atelier_noir_maybe_resync_products_en() {
	if ( wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}
	if ( 'products-en-4' === get_option( 'atelier_noir_products_en_v', '' ) ) {
		return;
	}
	if ( ! function_exists( 'atelier_noir_import_create_products' ) ) {
		return;
	}
	atelier_noir_import_create_products();
	update_option( 'atelier_noir_products_en_v', 'products-en-4', false );
}
add_action( 'init', 'atelier_noir_maybe_resync_products_en', 27 );

/**
 * Import complet (appelé après application des theme_mod).
 *
 * @return array{ok:bool, messages:string[]}
 */
function atelier_noir_import_full_site_content() {
	$messages = array();
	$ok       = true;

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return array( 'ok' => false, 'messages' => array( 'Insufficient permissions.' ) );
	}

	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		$messages[] = 'Elementor is not active: pages were not updated. Activate Elementor and run setup again.';
		$ok         = false;
	}

	@set_time_limit( 180 );

	$page_ids = array();

	// Static pages.
	$home_id = atelier_noir_import_migrate_page( array( 'home', 'accueil' ), 'home', 'Home' );
	if ( $home_id ) {
		update_post_meta( $home_id, '_wp_page_template', 'default' );
		if ( class_exists( '\Elementor\Plugin' ) ) {
			atelier_noir_apply_elementor_document( $home_id, atelier_noir_import_elementor_home() );
			update_option( 'atelier_noir_home_elementor_v', 'home-en-9', false );
		}
		$page_ids['home'] = $home_id;
	}

	$services_id = atelier_noir_import_get_or_create_page( 'services', 'Services', array() );
	if ( $services_id ) {
		update_post_meta( $services_id, '_wp_page_template', 'template-services.php' );
		if ( class_exists( '\Elementor\Plugin' ) ) {
			atelier_noir_apply_elementor_document( $services_id, atelier_noir_import_elementor_services() );
		}
		$page_ids['services'] = $services_id;
	}

	$contact_id = atelier_noir_import_get_or_create_page( 'contact', 'Contact', array() );
	if ( $contact_id ) {
		update_post_meta( $contact_id, '_wp_page_template', 'template-contact.php' );
		$page_ids['contact'] = $contact_id;
	}

	$project_id = atelier_noir_import_get_or_create_page( 'project', 'Project', array() );
	if ( ! $project_id ) {
		$legacy = get_page_by_path( 'journal', OBJECT, 'page' );
		if ( $legacy ) {
			wp_update_post(
				array(
					'ID'        => (int) $legacy->ID,
					'post_name' => 'project',
					'post_title'=> 'Project',
				)
			);
			$project_id = (int) $legacy->ID;
		}
	}
	if ( $project_id ) {
		/* Posts page uses home.php — no Elementor document (breaks /project/page/N/). */
		update_post_meta( $project_id, '_wp_page_template', 'default' );
		delete_post_meta( $project_id, '_elementor_data' );
		delete_post_meta( $project_id, '_elementor_edit_mode' );
		delete_post_meta( $project_id, '_elementor_css' );
		$page_ids['project'] = $project_id;
	}

	$shop_id = 0;
	if ( class_exists( 'WooCommerce' ) ) {
		$shop_id = atelier_noir_import_ensure_shop_page();
		if ( $shop_id ) {
			update_post_meta( $shop_id, '_wp_page_template', 'default' );
			if ( class_exists( '\Elementor\Plugin' ) ) {
				atelier_noir_apply_elementor_document( $shop_id, atelier_noir_import_elementor_shop() );
			}
			$page_ids['shop'] = $shop_id;
		}
	}

	// Lecture.
	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}
	if ( ! empty( $project_id ) ) {
		update_option( 'page_for_posts', $project_id );
	}

	atelier_noir_import_legal_pages();

	atelier_noir_import_rebuild_menu( $page_ids );

	if ( function_exists( 'atelier_noir_seed_services_json' ) ) {
		atelier_noir_seed_services_json();
		$messages[] = 'Services list seeded (Customizer repeater).';
	}

	atelier_noir_import_create_posts();

	if ( class_exists( 'WooCommerce' ) ) {
		atelier_noir_import_create_products();
		$messages[] = 'WooCommerce sample products created or updated (USD).';
	}

	flush_rewrite_rules( false );

	update_option( 'atelier_noir_import_content_applied_at', time() );

	if ( class_exists( '\Elementor\Plugin' ) ) {
		$messages[] = 'Elementor pages (EN), menu and reading settings applied.';
	} elseif ( ! $ok ) {
		$messages[] = 'Partial setup: activate Elementor and run again to refresh page layouts.';
	}

	return array(
		'ok'       => $ok,
		'messages' => $messages,
	);
}
