<?php
/**
 * Services list — Customizer JSON repeater (single source of truth).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default services used on first install / demo import.
 *
 * @return array<int, array<string, string>>
 */
function atelier_noir_services_defaults() {
	return array(
		array(
			'id'     => 'fashion-consulting',
			'title'  => 'Fashion Consulting',
			'text'   => '',
			'type'   => 'image',
			'image'  => '',
			'video'  => '',
			'poster' => '',
		),
		array(
			'id'    => 'styling-branding',
			'title' => 'Styling & Branding',
			'text'  => '',
			'type'  => 'image',
			'image' => '',
		),
		array(
			'id'    => 'events',
			'title' => 'Events',
			'text'  => '',
			'type'  => 'image',
			'image' => '',
		),
	);
}

/**
 * Parse services JSON into normalized rows (testable without theme mods).
 *
 * @param string $json JSON string.
 * @return array<int, array<string, mixed>>
 */
function atelier_noir_parse_services_json( $json ) {
	if ( ! is_string( $json ) || '' === $json || '[]' === $json ) {
		return array();
	}

	$decoded = json_decode( $json, true );
	if ( ! is_array( $decoded ) ) {
		return array();
	}

	$cta_label = atelier_noir_get_theme_mod( 'svc_cta_label', __( 'Book a Session', 'atelier-noir' ) );
	if ( ! is_string( $cta_label ) || '' === $cta_label ) {
		$cta_label = __( 'Book a Session', 'atelier-noir' );
	}
	$out       = array();
	$i         = 0;

	foreach ( $decoded as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$title = isset( $row['title'] ) ? trim( (string) $row['title'] ) : '';
		$slug  = sanitize_title( $row['id'] ?? '' );
		if ( '' === $slug && '' !== $title ) {
			$slug = sanitize_title( $title );
		}
		if ( '' === $slug ) {
			continue;
		}
		++$i;
		$video_url = isset( $row['video'] ) ? trim( (string) $row['video'] ) : '';
		$type      = '' !== $video_url ? 'video' : 'image';
		$out[]     = array(
			'id'     => $slug,
			'number' => sprintf( '%02d', $i ),
			'title'  => sanitize_text_field( $title ),
			'text'   => sanitize_textarea_field( $row['text'] ?? '' ),
			'type'   => $type,
			'video'  => esc_url_raw( $video_url ),
			'poster' => esc_url_raw( $row['poster'] ?? $row['image'] ?? '' ),
			'image'  => esc_url_raw( $row['image'] ?? '' ),
			'cta_en' => sanitize_text_field( $cta_label ),
		);
	}

	return $out;
}

/**
 * Services for template-services.php and contact form.
 *
 * @return array<int, array<string, mixed>>
 */
function atelier_noir_get_services_list() {
	$json = get_theme_mod( 'atelier_noir_services_json', '' );
	$out  = atelier_noir_parse_services_json( is_string( $json ) ? $json : '' );
	if ( $out ) {
		return $out;
	}

	return atelier_noir_parse_services_json(
		wp_json_encode( atelier_noir_services_defaults(), JSON_UNESCAPED_UNICODE )
	);
}

/**
 * Map slug => label for contact form.
 *
 * @return array<string, string>
 */
function atelier_noir_get_service_map() {
	$map = array();
	foreach ( atelier_noir_get_services_list() as $service ) {
		if ( ! empty( $service['id'] ) && ! empty( $service['title'] ) ) {
			$map[ $service['id'] ] = $service['title'];
		}
	}
	return $map;
}

/**
 * Seed JSON repeater from defaults (import / setup).
 */
function atelier_noir_seed_services_json() {
	$items = array();
	foreach ( atelier_noir_services_defaults() as $row ) {
		$items[] = array(
			'id'     => $row['id'],
			'title'  => $row['title'],
			'text'   => $row['text'],
			'video'  => $row['video'] ?? '',
			'image'  => $row['image'] ?? '',
			'poster' => $row['poster'] ?? $row['image'] ?? '',
		);
	}
	set_theme_mod( 'atelier_noir_services_json', wp_json_encode( $items, JSON_UNESCAPED_UNICODE ) );
}
