<?php
/**
 * Companion plugin (Atelier Noir Core): install / activate prompt.
 *
 * The release zip ships the plugin as bundled/atelier-noir-core.zip.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ATELIER_NOIR_CORE_PLUGIN = 'atelier-noir-core/atelier-noir-core.php';

/**
 * @return string Absolute path to the bundled plugin zip, or '' when absent.
 */
function atelier_noir_core_bundled_zip() {
	$zip = get_stylesheet_directory() . '/bundled/atelier-noir-core.zip';
	return file_exists( $zip ) ? $zip : '';
}

/**
 * @return bool
 */
function atelier_noir_core_is_installed() {
	return file_exists( WP_PLUGIN_DIR . '/' . ATELIER_NOIR_CORE_PLUGIN );
}

/**
 * @return bool
 */
function atelier_noir_core_is_active() {
	return function_exists( 'atelier_noir_core_boot' );
}

/**
 * Next action for the companion plugin: activate, install from the bundled zip, or download.
 *
 * @return array{url:string, label:string}
 */
function atelier_noir_core_action() {
	if ( atelier_noir_core_is_installed() ) {
		return array(
			'url'   => wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( ATELIER_NOIR_CORE_PLUGIN ) ), 'activate-plugin_' . ATELIER_NOIR_CORE_PLUGIN ),
			'label' => __( 'Activate Atelier Noir Core', 'atelier-noir' ),
		);
	}
	if ( atelier_noir_core_bundled_zip() ) {
		return array(
			'url'   => wp_nonce_url( admin_url( 'admin-post.php?action=atelier_noir_install_core' ), 'atelier_noir_install_core' ),
			'label' => __( 'Install and activate Atelier Noir Core', 'atelier-noir' ),
		);
	}
	return array(
		'url'   => ATELIER_NOIR_REPO_URL . '/releases/latest',
		'label' => __( 'Download Atelier Noir Core', 'atelier-noir' ),
	);
}

/**
 * Admin notice until the companion plugin is active.
 */
function atelier_noir_core_notice() {
	if ( ! current_user_can( 'install_plugins' ) || atelier_noir_core_is_active() ) {
		return;
	}
	if ( get_user_meta( get_current_user_id(), 'atelier_noir_core_notice_dismissed', true ) ) {
		return;
	}

	$action  = atelier_noir_core_action();
	$url     = $action['url'];
	$label   = $action['label'];

	$dismiss = wp_nonce_url( admin_url( 'admin-post.php?action=atelier_noir_dismiss_core_notice' ), 'atelier_noir_dismiss_core_notice' );
	?>
	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'Atelier Noir', 'atelier-noir' ); ?></strong> —
			<?php esc_html_e( 'The companion plugin Atelier Noir Core adds projects, SEO, the newsletter popup, transactional emails and the one-click demo import.', 'atelier-noir' ); ?>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
			<a class="button-link" href="<?php echo esc_url( $dismiss ); ?>"><?php esc_html_e( 'Dismiss', 'atelier-noir' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'atelier_noir_core_notice' );

/**
 * Install the bundled plugin zip, then activate it.
 */
function atelier_noir_install_core() {
	check_admin_referer( 'atelier_noir_install_core' );
	if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
		wp_die( esc_html__( 'Insufficient permissions.', 'atelier-noir' ) );
	}

	$zip = atelier_noir_core_bundled_zip();
	if ( '' !== $zip && ! atelier_noir_core_is_installed() ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$result   = $upgrader->install( $zip );
		if ( is_wp_error( $result ) || ! $result ) {
			wp_die( esc_html__( 'Atelier Noir Core could not be installed. Upload bundled/atelier-noir-core.zip from Plugins → Add New → Upload Plugin.', 'atelier-noir' ) );
		}
	}

	$activated = activate_plugin( ATELIER_NOIR_CORE_PLUGIN );
	if ( is_wp_error( $activated ) ) {
		wp_die( esc_html( $activated->get_error_message() ) );
	}

	wp_safe_redirect( admin_url( 'themes.php?page=atelier-noir-setup' ) );
	exit;
}
add_action( 'admin_post_atelier_noir_install_core', 'atelier_noir_install_core' );

/**
 * Dismiss the companion plugin notice for the current user.
 */
function atelier_noir_dismiss_core_notice() {
	check_admin_referer( 'atelier_noir_dismiss_core_notice' );
	update_user_meta( get_current_user_id(), 'atelier_noir_core_notice_dismissed', 1 );
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
	exit;
}
add_action( 'admin_post_atelier_noir_dismiss_core_notice', 'atelier_noir_dismiss_core_notice' );
