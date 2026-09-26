<?php
/**
 * Project page (blog) helpers — rewrites, posts page, pagination.
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Redirect legacy slugs to English paths. */
add_action(
	'template_redirect',
	function () {
		if ( is_admin() ) {
			return;
		}
		$path = trim( (string) parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );

		$redirects = array(
			'journal'                      => 'project',
			'accueil'                      => '',
			'mentions-legales'             => 'legal-notice',
			'politique-de-confidentialite' => 'privacy-policy',
			'cgv'                          => 'terms-and-conditions',
		);

		if ( isset( $redirects[ $path ] ) ) {
			$target = $redirects[ $path ];
			wp_safe_redirect( home_url( $target ? '/' . $target . '/' : '/' ), 301 );
			exit;
		}

		foreach ( array( 'journal/', 'accueil/' ) as $prefix ) {
			if ( str_starts_with( $path, $prefix ) ) {
				$target = str_replace( 'journal', 'project', str_replace( 'accueil', '', $path ) );
				wp_safe_redirect( home_url( '/' . ltrim( $target, '/' ) ), 301 );
				exit;
			}
		}
	}
);

/**
 * Posts page must use home.php — strip Elementor data that breaks /project/page/N/ URLs.
 */
function atelier_noir_ensure_posts_page_is_blog_index() {
	$page_id = (int) get_option( 'page_for_posts', 0 );
	if ( $page_id <= 0 ) {
		return;
	}

	$changed = false;

	if ( 'default' !== get_page_template_slug( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'default' );
		$changed = true;
	}

	foreach ( array( '_elementor_data', '_elementor_edit_mode', '_elementor_css' ) as $meta_key ) {
		if ( get_post_meta( $page_id, $meta_key, true ) ) {
			delete_post_meta( $page_id, $meta_key );
			$changed = true;
		}
	}

	if ( $changed && class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}
add_action( 'init', 'atelier_noir_ensure_posts_page_is_blog_index', 25 );

/**
 * Explicit rewrite for posts-page pagination (fixes /project/page/2/ 404).
 */
function atelier_noir_register_posts_page_pagination_rewrite() {
	$page_id = (int) get_option( 'page_for_posts', 0 );
	if ( $page_id <= 0 ) {
		return;
	}

	$slug = get_post_field( 'post_name', $page_id );
	if ( ! is_string( $slug ) || '' === $slug ) {
		return;
	}

	add_rewrite_rule(
		'^' . preg_quote( $slug, '/' ) . '/page/([0-9]{1,})/?$',
		'index.php?pagename=' . $slug . '&paged=$matches[1]',
		'top'
	);
}
add_action( 'init', 'atelier_noir_register_posts_page_pagination_rewrite', 20 );

/**
 * Flush permalinks once after theme switch.
 */
function atelier_noir_flush_rewrites_on_switch() {
	atelier_noir_register_posts_page_pagination_rewrite();
	flush_rewrite_rules( false );
	$theme = wp_get_theme();
	update_option( 'atelier_noir_rewrites_flushed', $theme->get( 'Version' ), false );
}
add_action( 'after_switch_theme', 'atelier_noir_flush_rewrites_on_switch' );

/**
 * Blog home: 4 posts per page on every page (page 1 = 1 featured + 3 grid cards).
 */
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_home() ) {
			return;
		}
		$query->set( 'posts_per_page', 4 );
	}
);

/**
 * Total pages for Project layout (featured consumes one slot on page 1).
 *
 * @param int $total_posts Published post count.
 * @return int
 */
function atelier_noir_project_total_pages( $total_posts ) {
	$total_posts = max( 0, (int) $total_posts );
	if ( $total_posts <= 4 ) {
		return 1;
	}
	return 1 + (int) ceil( ( $total_posts - 4 ) / 4 );
}
