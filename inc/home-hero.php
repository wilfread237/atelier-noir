<?php
/**
 * Home hero helpers: copy and media with import default fallbacks.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme mod with import default fallback.
 *
 * @param string $key Setting key.
 * @return string
 */
function atelier_noir_home_mod( $key ) {
	$defaults = function_exists( 'atelier_noir_get_import_defaults' )
		? atelier_noir_get_import_defaults()
		: array();
	$fallback = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return (string) get_theme_mod( $key, $fallback );
}

/**
 * Hero eyes catcher copy (two-line title + optional tagline).
 *
 * @return array{line1:string,line2:string,tagline:string}
 */
function atelier_noir_get_eyes_hero_copy() {
	$line1   = trim( atelier_noir_home_mod( 'atelier_noir_eyes_title_line1' ) );
	$line2   = trim( atelier_noir_home_mod( 'atelier_noir_eyes_title_line2' ) );
	$tagline = trim( atelier_noir_home_mod( 'atelier_noir_eyes_tagline' ) );

	if ( '' === $line1 && '' === $line2 ) {
		$legacy = trim( atelier_noir_home_mod( 'atelier_noir_eyes_overlay_title' ) );
		if ( preg_match( '/^studio\s+(.+)$/i', $legacy, $m ) ) {
			$line1 = 'Studio';
			$line2 = trim( $m[1] );
		} elseif ( '' !== $legacy ) {
			$line1 = $legacy;
		} else {
			$line1 = 'Atelier';
			$line2 = 'Noir';
		}
	}

	return array(
		'line1'   => $line1,
		'line2'   => $line2,
		'tagline' => $tagline,
	);
}

/**
 * Build hero title HTML for Elementor / PHP templates.
 *
 * @return string
 */
function atelier_noir_render_eyes_hero_title_html() {
	$copy = atelier_noir_get_eyes_hero_copy();
	$html = '';

	if ( '' !== $copy['line1'] || '' !== $copy['line2'] ) {
		$html .= '<h1 class="studio-hero-title" style="margin:0 0 0.75rem;font-family:var(--studio-font-heading,Cormorant Garamond,serif);font-weight:300;line-height:0.95;color:#fff">';
		if ( '' !== $copy['line1'] ) {
			$html .= '<span class="studio-hero-title__line" style="display:block">' . esc_html( $copy['line1'] ) . '</span>';
		}
		if ( '' !== $copy['line2'] ) {
			$html .= '<span class="studio-hero-title__line" style="display:block">' . esc_html( $copy['line2'] ) . '</span>';
		}
		$html .= '</h1>';
	}

	if ( '' !== $copy['tagline'] ) {
		$html .= '<p class="studio-hero-tagline" style="margin:0;max-width:36ch;font-size:0.95rem;line-height:1.55;color:rgba(255,255,255,0.72)">' . esc_html( $copy['tagline'] ) . '</p>';
	}

	return $html;
}

/**
 * Resolve uploaded attachment or external URL.
 *
 * @param string $url_key Theme mod URL key.
 * @param string $att_key Theme mod attachment ID key.
 * @return string
 */
function atelier_noir_resolve_media_url( $url_key, $att_key = '' ) {
	if ( $att_key ) {
		$att_id = absint( get_theme_mod( $att_key, 0 ) );
		if ( $att_id ) {
			$resolved = wp_get_attachment_url( $att_id );
			if ( is_string( $resolved ) && '' !== $resolved ) {
				return $resolved;
			}
		}
	}
	$url = atelier_noir_home_mod( $url_key );
	if ( $url_key === 'atelier_noir_eyes_video_url' && function_exists( 'atelier_noir_resolve_video_url' ) ) {
		$resolved = atelier_noir_resolve_video_url( $url );
		return '' !== $resolved ? $resolved : $url;
	}
	return $url;
}

