<?php
/**
 * Plugin Name:       Atelier Noir Core
 * Plugin URI:        https://github.com/wilfread237/atelier-noir
 * Description:       Companion plugin for the Atelier Noir theme: projects, SEO & Open Graph, newsletter popup (MailPoet), transactional emails, order tracking, currency, permalinks and one-click demo import.
 * Version:           1.1.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Wilfread Essomba
 * Author URI:        https://www.linkedin.com/in/wilfreadessomba/
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       atelier-noir-core
 * Update URI:        https://github.com/wilfread237/atelier-noir
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ATELIER_NOIR_CORE_VERSION', '1.1.0' );
define( 'ATELIER_NOIR_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ATELIER_NOIR_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Modules rely on helpers defined by the Atelier Noir theme.
 *
 * @return bool
 */
function atelier_noir_core_theme_is_active() {
	return function_exists( 'atelier_noir_get_theme_mod' );
}

/**
 * Load modules once the theme's functions.php is available.
 */
function atelier_noir_core_boot() {
	load_plugin_textdomain( 'atelier-noir-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	if ( ! atelier_noir_core_theme_is_active() ) {
		add_action( 'admin_notices', 'atelier_noir_core_theme_missing_notice' );
		return;
	}

	$modules = array(
		'migration',
		'cpt-project',
		'project',
		'admin-permalinks',
		'seo',
		'mailpoet',
		'currency',
		'import-elementor',
		'site-en-setup',
	);

	if ( class_exists( '\MailPoet\DI\ContainerWrapper' ) ) {
		$modules[] = 'newsletter-popup';
	}

	if ( function_exists( 'atelier_noir_is_commerce_enabled' ) && atelier_noir_is_commerce_enabled() ) {
		$modules[] = 'woocommerce-emails';
		$modules[] = 'woocommerce-order-tracking';
	}

	foreach ( $modules as $module ) {
		require_once ATELIER_NOIR_CORE_DIR . 'includes/' . $module . '.php';
	}
}
add_action( 'after_setup_theme', 'atelier_noir_core_boot', 1 );

/**
 * Admin notice when the Atelier Noir theme is not active.
 */
function atelier_noir_core_theme_missing_notice() {
	if ( ! current_user_can( 'switch_themes' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'Atelier Noir Core requires the Atelier Noir theme. Activate the theme to enable its features.', 'atelier-noir-core' );
	echo '</p></div>';
}

/**
 * Flush rewrite rules on the first request after activation (project post type registered on init).
 */
function atelier_noir_core_activate() {
	update_option( 'atelier_noir_core_flush_rewrites', 1, false );
}
register_activation_hook( __FILE__, 'atelier_noir_core_activate' );

/**
 * @return void
 */
function atelier_noir_core_maybe_flush_rewrites() {
	if ( ! get_option( 'atelier_noir_core_flush_rewrites' ) ) {
		return;
	}
	delete_option( 'atelier_noir_core_flush_rewrites' );
	flush_rewrite_rules( false );
}
add_action( 'init', 'atelier_noir_core_maybe_flush_rewrites', 99 );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
