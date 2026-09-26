<?php
/**
 * Media URL helpers — direct video files only (no HTML page scraping).
 *
 * Accepted formats: direct .mp4 / .webm / .mov URLs, or YouTube / youtu.be links.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a URL points to a direct video file or supported embed host.
 *
 * @param string $url Raw URL.
 * @return bool
 */
function atelier_noir_is_allowed_video_url( $url ) {
	$url = is_string( $url ) ? trim( $url ) : '';
	if ( '' === $url ) {
		return false;
	}

	if ( preg_match( '#(?:youtube\.com|youtu\.be)#i', $url ) ) {
		return true;
	}

	if ( preg_match( '#\.(?:mp4|webm|mov)(\?.*)?$#i', $url ) ) {
		return true;
	}

	return false;
}

/**
 * Sanitize hero / background video URL from Customizer.
 * Rejects Pexels (or any) HTML page URLs — use direct MP4 links only.
 *
 * @param string $url Raw URL from Customizer.
 * @return string Sanitized URL or empty string.
 */
function atelier_noir_resolve_video_url( $url ) {
	$url = is_string( $url ) ? trim( $url ) : '';
	if ( '' === $url ) {
		return '';
	}

	if ( atelier_noir_is_youtube_url( $url ) ) {
		return esc_url_raw( $url );
	}

	// URL copiée depuis la médiathèque WordPress (wp-content/uploads/…).
	if ( preg_match( '#wp-content/uploads/#i', $url ) ) {
		$path = wp_parse_url( $url, PHP_URL_PATH );
		if ( is_string( $path ) && preg_match( '#\.(?:mp4|webm|mov|m4v|ogv)(\?.*)?$#i', $path ) ) {
			return esc_url_raw( $url );
		}
		$att_id = attachment_url_to_postid( $url );
		if ( $att_id ) {
			$mime = get_post_mime_type( $att_id );
			if ( is_string( $mime ) && str_starts_with( $mime, 'video/' ) ) {
				return esc_url_raw( $url );
			}
		}
	}

	if ( atelier_noir_is_allowed_video_url( $url ) ) {
		return esc_url_raw( $url );
	}

	return '';
}

/**
 * @param string $url Raw URL.
 * @return bool
 */
function atelier_noir_is_youtube_url( $url ) {
	return is_string( $url ) && (bool) preg_match( '#(?:youtube\.com|youtu\.be)#i', $url );
}

/**
 * @param string $url Watch or youtu.be URL.
 * @return string
 */
function atelier_noir_youtube_embed_url( $url ) {
	$url = is_string( $url ) ? trim( $url ) : '';
	if ( '' === $url ) {
		return '';
	}

	$video_id = '';
	if ( preg_match( '#youtu\.be/([a-zA-Z0-9_-]{6,})#i', $url, $m ) ) {
		$video_id = $m[1];
	} elseif ( preg_match( '#[?&]v=([a-zA-Z0-9_-]{6,})#i', $url, $m ) ) {
		$video_id = $m[1];
	}

	if ( '' === $video_id ) {
		return '';
	}

	return sprintf(
		'https://www.youtube-nocookie.com/embed/%s?autoplay=1&mute=1&loop=1&playlist=%s&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1',
		rawurlencode( $video_id ),
		rawurlencode( $video_id )
	);
}
