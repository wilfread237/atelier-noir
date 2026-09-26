<?php
/**
 * Valeurs par défaut du Personnaliseur (English, applied by Import / EN+USD setup).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, string|int|bool>
 */
function atelier_noir_get_import_defaults() {
	$contact  = home_url( '/contact/' );
	$services = home_url( '/services/' );

	return array(
		'atelier_noir_home_source'           => 'page',
		'atelier_noir_home_show_journal'     => true,
		'atelier_noir_home_show_shop_teaser' => true,
		'atelier_noir_animations_on'         => true,

		'atelier_noir_font_heading'          => 'Outfit',
		'atelier_noir_font_body'             => 'Inter',

		'atelier_noir_eyes_video_url'        => 'https://videos.pexels.com/video-files/3740510/3740510-hd_1920_1080_25fps.mp4',
		'atelier_noir_eyes_poster_url'       => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1920&q=80',
		'atelier_noir_eyes_kicker'           => 'Fashion · Creative Direction · Art Direction',
		'atelier_noir_eyes_title_line1'      => 'Atelier',
		'atelier_noir_eyes_title_line2'      => 'Noir',
		'atelier_noir_eyes_tagline'          => '',
		'atelier_noir_eyes_overlay_title'    => atelier_noir_brand_name(),

		'atelier_noir_founder_image_url'     => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=1920&q=85',
		'atelier_noir_studio_label'          => '',
		'atelier_noir_studio_title'          => 'THE ATELIER',
		'atelier_noir_studio_text'           => 'Boldness. Authenticity. Creative direction with a singular point of view.',
		'atelier_noir_studio_cta_label'      => 'Explore our services',
		'atelier_noir_studio_cta_url'        => $services,

		'atelier_noir_color_primary'         => '#0A0A0A',
		'atelier_noir_color_bg'              => '#FFFFFF',
		'atelier_noir_color_accent'          => '#491F06',
		'atelier_noir_loader_title'          => atelier_noir_brand_name(),
		'atelier_noir_loader_sub'            => '',
		'atelier_noir_loader_bg'             => '#0A0A0A',
		'atelier_noir_loader_fg'             => '#FFFFFF',
		'atelier_noir_loader_min_ms'         => 5500,
		'atelier_noir_loader_media_type'     => 'lottie',
		'atelier_noir_loader_media_url'      => 'https://assets10.lottiefiles.com/packages/lf20_touohxv0.json',
	);
}
