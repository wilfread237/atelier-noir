<?php
/**
 * Homepage loader — video, GIF, or Lottie via Customizer.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Default Lottie loader (LottieFiles — lightweight, fashion/editorial friendly). */
function atelier_noir_loader_default_media_url() {
	return 'https://assets10.lottiefiles.com/packages/lf20_touohxv0.json';
}

/**
 * Resolve loader media URL (attachment ID → URL field → default).
 *
 * @return string
 */
function atelier_noir_loader_resolved_media_url() {
	$att_id = absint( get_theme_mod( 'atelier_noir_loader_media_attachment', 0 ) );
	if ( $att_id ) {
		$url = wp_get_attachment_url( $att_id );
		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}
	}

	$url = (string) get_theme_mod( 'atelier_noir_loader_media_url', '' );
	if ( '' !== $url ) {
		return $url;
	}

	return atelier_noir_loader_default_media_url();
}

/**
 * Detect loader media type from Customizer or attachment mime.
 *
 * @return string text|video|gif|lottie
 */
function atelier_noir_loader_resolved_media_type() {
	$type = get_theme_mod( 'atelier_noir_loader_media_type', 'lottie' );

	$att_id = absint( get_theme_mod( 'atelier_noir_loader_media_attachment', 0 ) );
	if ( $att_id ) {
		$mime = get_post_mime_type( $att_id );
		if ( is_string( $mime ) ) {
			if ( str_starts_with( $mime, 'video/' ) ) {
				return 'video';
			}
			if ( 'image/gif' === $mime ) {
				return 'gif';
			}
			if ( str_contains( $mime, 'json' ) || 'application/json' === $mime ) {
				return 'lottie';
			}
			if ( str_starts_with( $mime, 'image/' ) ) {
				return 'gif';
			}
		}
	}

	return in_array( $type, array( 'text', 'video', 'gif', 'lottie' ), true ) ? $type : 'lottie';
}

/**
 * Video MIME sources for attachment (MP4, WebM, OGG).
 *
 * @param int $att_id Attachment ID.
 * @return array<int, array{type:string,src:string}>
 */
function atelier_noir_loader_video_sources( $att_id ) {
	$att_id = absint( $att_id );
	if ( ! $att_id ) {
		$url = atelier_noir_loader_resolved_media_url();
		return $url ? array( array( 'type' => 'video/mp4', 'src' => $url ) ) : array();
	}

	$mime = (string) get_post_mime_type( $att_id );
	$url  = (string) wp_get_attachment_url( $att_id );
	if ( '' === $url ) {
		return array();
	}

	$map = array(
		'video/mp4'  => 'video/mp4',
		'video/webm' => 'video/webm',
		'video/ogg'  => 'video/ogg',
	);

	if ( isset( $map[ $mime ] ) ) {
		return array( array( 'type' => $map[ $mime ], 'src' => $url ) );
	}

	return array( array( 'type' => 'video/mp4', 'src' => $url ) );
}

function atelier_noir_loader_customize_register( $wp_customize ) {
	$wp_customize->add_setting(
		'atelier_noir_loader_media_type',
		array(
			'default'           => 'lottie',
			'sanitize_callback' => function ( $v ) {
				return in_array( $v, array( 'text', 'video', 'gif', 'lottie' ), true ) ? $v : 'lottie';
			},
		)
	);
	$wp_customize->add_control(
		'atelier_noir_loader_media_type',
		array(
			'label'       => __( 'Loader media type', 'atelier-noir' ),
			'description' => __( 'Auto-detected when a file is uploaded via Media Library below.', 'atelier-noir' ),
			'section'     => 'atelier_noir_loader',
			'type'        => 'select',
			'choices'     => array(
				'text'   => __( 'Text only', 'atelier-noir' ),
				'video'  => __( 'Video (MP4 / WebM / OGG)', 'atelier-noir' ),
				'gif'    => __( 'Animated GIF', 'atelier-noir' ),
				'lottie' => __( 'Lottie JSON', 'atelier-noir' ),
			),
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_loader_media_attachment',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'atelier_noir_loader_media_attachment',
			array(
				'label'       => __( 'Loader media file', 'atelier-noir' ),
				'description' => __( 'Upload a short video (MP4, WebM), GIF or Lottie JSON. Overrides the URL field.', 'atelier-noir' ),
				'section'     => 'atelier_noir_loader',
				'mime_type'   => '',
			)
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_loader_media_url',
		array(
			'default'           => atelier_noir_loader_default_media_url(),
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'atelier_noir_loader_media_url',
		array(
			'label'       => __( 'Loader media URL (fallback)', 'atelier-noir' ),
			'description' => __( 'Lottie JSON or .lottie URL. Save “Happy Character Throwing Ball” to your LottieFiles workspace, enable Asset link, then paste the URL here.', 'atelier-noir' ),
			'section'     => 'atelier_noir_loader',
			'type'        => 'url',
		)
	);
}
add_action( 'customize_register', 'atelier_noir_loader_customize_register', 40 );

/**
 * Legacy overlay loader on homepage (disabled — loader is a standalone page at /).
 *
 * @return bool
 */
function atelier_noir_use_legacy_overlay_loader() {
	return (bool) apply_filters( 'atelier_noir_use_legacy_overlay_loader', false );
}

function atelier_noir_output_loader() {
	if ( ! atelier_noir_use_legacy_overlay_loader() ) {
		return;
	}
	if ( ! is_front_page() || is_customize_preview() ) {
		return;
	}
	if ( atelier_noir_get_theme_mod( 'an_loader_disable', false ) || get_theme_mod( 'atelier_noir_loader_disable', false ) ) {
		return;
	}

	$title     = get_theme_mod( 'atelier_noir_loader_title', atelier_noir_brand_name() );
	$sub       = get_theme_mod( 'atelier_noir_loader_sub', '' );
	$min       = (int) get_theme_mod( 'atelier_noir_loader_min_ms', 5500 );
	$type      = atelier_noir_loader_resolved_media_type();
	$media_url = atelier_noir_loader_resolved_media_url();
	$att_id    = absint( get_theme_mod( 'atelier_noir_loader_media_attachment', 0 ) );
	?>
	<div
		id="studio-site-loader"
		class="studio-site-loader"
		data-min-ms="<?php echo esc_attr( (string) max( 0, $min ) ); ?>"
		aria-hidden="true"
	>
		<div class="studio-site-loader__inner">
			<?php if ( $media_url && 'video' === $type ) : ?>
				<video class="studio-site-loader__media" autoplay muted loop playsinline>
					<?php foreach ( atelier_noir_loader_video_sources( $att_id ) as $source ) : ?>
						<source src="<?php echo esc_url( $source['src'] ); ?>" type="<?php echo esc_attr( $source['type'] ); ?>">
					<?php endforeach; ?>
				</video>
			<?php elseif ( $media_url && 'gif' === $type ) : ?>
				<img class="studio-site-loader__media" src="<?php echo esc_url( $media_url ); ?>" alt="">
			<?php elseif ( $media_url && 'lottie' === $type ) : ?>
				<div class="studio-site-loader__media studio-site-loader__lottie" data-lottie="<?php echo esc_url( $media_url ); ?>"></div>
			<?php else : ?>
				<p class="studio-site-loader__brand"><?php echo esc_html( $title ); ?></p>
				<?php if ( '' !== $sub ) : ?>
					<p class="studio-site-loader__sub"><?php echo esc_html( $sub ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

add_action( 'wp_body_open', 'atelier_noir_output_loader', 5 );

/**
 * Enqueue assets for standalone loader page template.
 */
function atelier_noir_enqueue_loader_page_assets() {
	if ( ! atelier_noir_is_loader_page() || is_customize_preview() ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();
	$ver = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'atelier-noir-loader-page',
		$uri . '/assets/css/loader-page.css',
		array(),
		file_exists( $dir . '/assets/css/loader-page.css' ) ? (string) filemtime( $dir . '/assets/css/loader-page.css' ) : $ver
	);

	wp_enqueue_script(
		'atelier-noir-loader-page',
		$uri . '/assets/js/loader-page.js',
		array(),
		file_exists( $dir . '/assets/js/loader-page.js' ) ? (string) filemtime( $dir . '/assets/js/loader-page.js' ) : $ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'atelier_noir_enqueue_loader_page_assets', 15 );

function atelier_noir_loader_assets() {
	if ( ! atelier_noir_use_legacy_overlay_loader() || ! is_front_page() ) {
		return;
	}
	if ( atelier_noir_get_theme_mod( 'an_loader_disable', false ) || get_theme_mod( 'atelier_noir_loader_disable', false ) ) {
		return;
	}
	if ( 'lottie' !== atelier_noir_loader_resolved_media_type() ) {
		return;
	}
	wp_enqueue_script(
		'dot-lottie-player',
		'https://unpkg.com/@dotlottie/player-component@2/dist/dotlottie-player.js',
		array(),
		'2',
		true
	);
	wp_add_inline_script(
		'dot-lottie-player',
		"document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('.studio-site-loader__lottie[data-lottie]').forEach(function(el){var src=el.getAttribute('data-lottie');if(!src)return;var p=document.createElement('dotlottie-player');p.setAttribute('src',src);p.setAttribute('autoplay','');p.setAttribute('loop','');p.style.width='min(280px,70vw)';p.style.height='auto';el.appendChild(p);});});"
	);
}
add_action( 'wp_enqueue_scripts', 'atelier_noir_loader_assets', 20 );
