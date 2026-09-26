<?php
/**
 * Theme mod helpers — an_* keys with legacy atelier_noir_* fallback.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize a theme mod key to an_ prefix (without atelier_noir_).
 *
 * @param string $key Key with or without prefix.
 * @return string an_* key.
 */
function atelier_noir_normalize_mod_key( $key ) {
	$key = (string) $key;
	if ( str_starts_with( $key, 'an_' ) ) {
		return $key;
	}
	if ( str_starts_with( $key, 'atelier_noir_' ) ) {
		return 'an_' . substr( $key, strlen( 'atelier_noir_' ) );
	}
	return 'an_' . $key;
}

/**
 * Legacy atelier_noir_* key for a an_* or bare key.
 *
 * @param string $key Key.
 * @return string
 */
function atelier_noir_legacy_mod_key( $key ) {
	$key = (string) $key;
	if ( str_starts_with( $key, 'atelier_noir_' ) ) {
		return $key;
	}
	$bare = str_starts_with( $key, 'an_' ) ? substr( $key, 3 ) : $key;
	return 'atelier_noir_' . $bare;
}

/**
 * Read theme mod (an_* first, then legacy atelier_noir_*).
 *
 * @param string $key     Mod key (an_*, atelier_noir_*, or bare).
 * @param mixed  $default Default value.
 * @return mixed
 */
function atelier_noir_get_theme_mod( $key, $default = '' ) {
	$an_key     = atelier_noir_normalize_mod_key( $key );
	$legacy_key = atelier_noir_legacy_mod_key( $key );

	$an_val = get_theme_mod( $an_key, null );
	if ( null !== $an_val && ! ( is_string( $an_val ) && '' === $an_val && '' !== $default ) ) {
		return $an_val;
	}

	$legacy_val = get_theme_mod( $legacy_key, null );
	if ( null !== $legacy_val ) {
		return $legacy_val;
	}

	return $default;
}

/**
 * Theme asset URI under assets/images/brand/.
 *
 * @param string $filename File name.
 * @return string
 */
function atelier_noir_brand_asset_uri( $filename ) {
	$dir = get_stylesheet_directory() . '/assets/images/brand/' . ltrim( $filename, '/' );
	$uri = get_stylesheet_directory_uri() . '/assets/images/brand/' . ltrim( $filename, '/' );
	return file_exists( $dir ) ? $uri : '';
}

/**
 * Resolve media URL from attachment ID theme mod or fallback URL / brand asset.
 *
 * @param string $attachment_key Theme mod key for attachment ID.
 * @param string $url_key        Theme mod key for URL fallback.
 * @param string $brand_fallback Brand asset filename.
 * @return string
 */
function atelier_noir_resolve_media_mod( $attachment_key, $url_key = '', $brand_fallback = '' ) {
	$att_id = absint( atelier_noir_get_theme_mod( $attachment_key, 0 ) );
	if ( $att_id ) {
		$url = wp_get_attachment_url( $att_id );
		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}
	}

	if ( '' !== $url_key ) {
		$url = (string) atelier_noir_get_theme_mod( $url_key, '' );
		if ( '' !== trim( $url ) ) {
			return esc_url( $url );
		}
	}

	if ( '' !== $brand_fallback ) {
		$asset = atelier_noir_brand_asset_uri( $brand_fallback );
		if ( '' !== $asset ) {
			return $asset;
		}
	}

	return '';
}
