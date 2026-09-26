<?php
/**
 * Core pages: Loader at /, Home at /home/.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return int Loader page ID.
 */
function atelier_noir_get_loader_page_id() {
	$stored = (int) get_option( 'an_loader_page_id', 0 );
	if ( $stored > 0 && 'publish' === get_post_status( $stored ) ) {
		return $stored;
	}

	foreach ( array( 'loader', 'accueil-loader' ) as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page && ! is_wp_error( $page ) ) {
			return (int) $page->ID;
		}
	}

	return (int) get_option( 'page_on_front', 0 );
}

/**
 * @return int Home content page ID (/home/).
 */
function atelier_noir_get_home_page_id() {
	$stored = (int) get_option( 'an_home_page_id', 0 );
	if ( $stored > 0 && 'publish' === get_post_status( $stored ) ) {
		return $stored;
	}

	foreach ( array( 'home', 'accueil' ) as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page && ! is_wp_error( $page ) ) {
			return (int) $page->ID;
		}
	}

	return 0;
}

/**
 * Whether current request is the standalone loader (site root).
 *
 * @return bool
 */
function atelier_noir_is_loader_page() {
	if ( is_admin() || wp_doing_ajax() ) {
		return false;
	}

	$loader_id = atelier_noir_get_loader_page_id();
	if ( $loader_id <= 0 ) {
		return is_front_page() && is_page_template( 'template-loader.php' );
	}

	return is_page( $loader_id ) || ( is_front_page() && (int) get_queried_object_id() === $loader_id );
}

/**
 * URL shown after loader redirect.
 *
 * @return string
 */
function atelier_noir_loader_redirect_url() {
	$custom = (string) atelier_noir_get_theme_mod( 'an_loader_redirect_path', '/home/' );
	if ( '' !== trim( $custom ) ) {
		return esc_url( home_url( '/' . ltrim( $custom, '/' ) ) );
	}

	$home_id = atelier_noir_get_home_page_id();
	if ( $home_id > 0 ) {
		return esc_url( get_permalink( $home_id ) );
	}

	return esc_url( home_url( '/home/' ) );
}

/**
 * Create/update Loader + Home pages and Reading settings (once).
 */
function atelier_noir_apply_site_structure() {
	$home_id = atelier_noir_get_home_page_id();

	if ( $home_id <= 0 ) {
		$existing = get_page_by_path( 'home', OBJECT, 'page' );
		if ( $existing && ! is_wp_error( $existing ) ) {
			$home_id = (int) $existing->ID;
		} else {
			$home_id = (int) wp_insert_post(
				array(
					'post_title'  => 'Home',
					'post_name'   => 'home',
					'post_status' => 'publish',
					'post_type'   => 'page',
				),
				true
			);
			if ( is_wp_error( $home_id ) ) {
				$home_id = 0;
			}
		}
	}

	if ( $home_id > 0 ) {
		update_option( 'an_home_page_id', $home_id, false );
		$current_tpl = get_page_template_slug( $home_id );
		if ( '' === $current_tpl || 'default' === $current_tpl ) {
			update_post_meta( $home_id, '_wp_page_template', 'template-home.php' );
		}
	}

	$loader_id = atelier_noir_get_loader_page_id();
	if ( $loader_id <= 0 || ! is_page( $loader_id ) ) {
		$loader_id = (int) wp_insert_post(
			array(
				'post_title'  => 'Loader',
				'post_name'   => 'loader',
				'post_status' => 'publish',
				'post_type'   => 'page',
			),
			true
		);
		if ( is_wp_error( $loader_id ) ) {
			return;
		}
	}

	update_post_meta( $loader_id, '_wp_page_template', 'template-loader.php' );
	update_option( 'an_loader_page_id', $loader_id, false );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $loader_id );

	update_option( 'an_site_structure_v2', time(), false );

	atelier_noir_ensure_editorial_pages();
}

/**
 * Create Services, Contact, Projects pages with correct templates.
 */
function atelier_noir_ensure_editorial_pages() {
	if ( get_option( 'an_site_structure_v3', false ) ) {
		return;
	}

	$pages = array(
		array(
			'slug'     => 'services',
			'title'    => 'Services',
			'template' => 'template-services.php',
		),
		array(
			'slug'     => 'contact',
			'title'    => 'Contact',
			'template' => 'template-contact.php',
		),
		array(
			'slug'     => 'projects',
			'title'    => 'Projects',
			'template' => 'template-projects.php',
		),
	);

	foreach ( $pages as $page_def ) {
		$page = get_page_by_path( $page_def['slug'], OBJECT, 'page' );
		if ( ! $page || is_wp_error( $page ) ) {
			$page_id = (int) wp_insert_post(
				array(
					'post_title'  => $page_def['title'],
					'post_name'   => $page_def['slug'],
					'post_status' => 'publish',
					'post_type'   => 'page',
				),
				true
			);
			if ( is_wp_error( $page_id ) || $page_id <= 0 ) {
				continue;
			}
		} else {
			$page_id = (int) $page->ID;
		}

		update_post_meta( $page_id, '_wp_page_template', $page_def['template'] );
	}

	update_option( 'an_site_structure_v3', time(), false );
	flush_rewrite_rules( false );
}

/**
 * Apply structure once after theme update.
 */
function atelier_noir_maybe_apply_site_structure() {
	if ( ! get_option( 'an_site_structure_v2', false ) ) {
		atelier_noir_apply_site_structure();
	} elseif ( ! get_option( 'an_site_structure_v3', false ) ) {
		atelier_noir_ensure_editorial_pages();
	}
}
add_action( 'init', 'atelier_noir_maybe_apply_site_structure', 5 );

/**
 * Admin notice if Loader is not the front page.
 */
function atelier_noir_site_structure_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( get_option( 'an_site_structure_v2', false ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'Atelier Noir: applying site structure (Loader at /, Home at /home/)…', 'atelier-noir' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'atelier_noir_site_structure_admin_notice' );
