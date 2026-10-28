<?php
/**
 * Theme onboarding wizard (Astra-style): activation redirect + demo import.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return bool
 */
function atelier_noir_onboarding_complete() {
	return (bool) get_option( 'atelier_noir_onboarding_complete', false );
}

/**
 * Redirect to setup wizard after theme activation.
 */
function atelier_noir_onboarding_activation_redirect() {
	if ( get_stylesheet() !== 'atelier-noir' ) {
		return;
	}
	update_option( 'atelier_noir_activation_redirect', true, false );
}
add_action( 'after_switch_theme', 'atelier_noir_onboarding_activation_redirect' );

/**
 * Perform one-time redirect (not on AJAX / network admin).
 */
function atelier_noir_onboarding_maybe_redirect() {
	if ( ! get_option( 'atelier_noir_activation_redirect' ) ) {
		return;
	}
	if ( wp_doing_ajax() || is_network_admin() ) {
		return;
	}
	if ( isset( $_GET['page'] ) && 'atelier-noir-setup' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		delete_option( 'atelier_noir_activation_redirect' );
		return;
	}
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	delete_option( 'atelier_noir_activation_redirect' );
	wp_safe_redirect( admin_url( 'themes.php?page=atelier-noir-setup' ) );
	exit;
}
add_action( 'admin_init', 'atelier_noir_onboarding_maybe_redirect', 1 );

/**
 * Setup page under Appearance.
 */
function atelier_noir_onboarding_register_page() {
	add_submenu_page(
		'themes.php',
		__( 'Atelier Noir Setup', 'atelier-noir' ),
		__( 'Atelier Noir Setup', 'atelier-noir' ),
		'edit_theme_options',
		'atelier-noir-setup',
		'atelier_noir_onboarding_render_page'
	);
}
add_action( 'admin_menu', 'atelier_noir_onboarding_register_page' );

/**
 * Admin notice when setup is incomplete.
 */
function atelier_noir_onboarding_admin_notice() {
	if ( atelier_noir_onboarding_complete() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$url = admin_url( 'themes.php?page=atelier-noir-setup' );
	echo '<div class="notice notice-info is-dismissible"><p>';
	echo wp_kses_post(
		sprintf(
			/* translators: %s: setup wizard URL */
			__( '<strong>Atelier Noir</strong> — Complete the <a href="%s">setup wizard</a> to install demo content and configure your site.', 'atelier-noir' ),
			esc_url( $url )
		)
	);
	echo '</p></div>';
}
add_action( 'admin_notices', 'atelier_noir_onboarding_admin_notice' );

/**
 * Recommended plugins status.
 *
 * @return array<string, array{label:string, active:bool, slug:string}>
 */
function atelier_noir_onboarding_plugins_status() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$core = atelier_noir_core_action();

	$plugins = array(
		'atelier-noir-core' => array(
			'label'       => 'Atelier Noir Core',
			'slug'        => ATELIER_NOIR_CORE_PLUGIN,
			'install_url' => $core['url'],
		),
		'elementor' => array(
			'label' => 'Elementor',
			'slug'  => 'elementor/elementor.php',
		),
		'woocommerce' => array(
			'label' => 'WooCommerce',
			'slug'  => 'woocommerce/woocommerce.php',
		),
	);
	$out = array();
	foreach ( $plugins as $key => $row ) {
		$out[ $key ] = array(
			'label'       => $row['label'],
			'slug'        => $row['slug'],
			'active'      => is_plugin_active( $row['slug'] ),
			'install_url' => isset( $row['install_url'] )
				? $row['install_url']
				: admin_url( 'plugin-install.php?s=' . rawurlencode( $row['label'] ) . '&tab=search&type=term' ),
		);
	}
	return $out;
}

/**
 * Run full demo import (Customizer + pages + menu + Woo).
 *
 * @return array{ok:bool, messages:string[]}
 */
function atelier_noir_run_demo_import() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return array(
			'ok'       => false,
			'messages' => array( __( 'Insufficient permissions.', 'atelier-noir' ) ),
		);
	}

	$messages = array();
	$ok       = false;

	if ( function_exists( 'atelier_noir_apply_en_usd_setup' ) ) {
		$result   = atelier_noir_apply_en_usd_setup();
		$messages = isset( $result['messages'] ) ? (array) $result['messages'] : array();
		$ok       = ! empty( $result['ok'] );
	} elseif ( function_exists( 'atelier_noir_import_full_site_content' ) ) {
		$result   = atelier_noir_import_full_site_content();
		$messages = isset( $result['messages'] ) ? (array) $result['messages'] : array();
		$ok       = ! empty( $result['ok'] );
	} else {
		$messages[] = __( 'Install and activate the Atelier Noir Core plugin to import demo content.', 'atelier-noir' );
	}

	if ( $ok ) {
		update_option( 'atelier_noir_onboarding_complete', true, false );
		update_option( 'atelier_noir_import_applied_at', time(), false );
	}

	return array(
		'ok'       => $ok,
		'messages' => $messages,
	);
}

/**
 * AJAX: demo import.
 */
function atelier_noir_onboarding_ajax_import() {
	check_ajax_referer( 'atelier_noir_onboarding', 'nonce' );

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'atelier-noir' ) ), 403 );
	}

	@set_time_limit( 300 );

	$result = atelier_noir_run_demo_import();

	if ( empty( $result['ok'] ) ) {
		wp_send_json_error(
			array(
				'messages' => isset( $result['messages'] ) ? $result['messages'] : array(),
			)
		);
	}

	wp_send_json_success(
		array(
			'messages' => isset( $result['messages'] ) ? $result['messages'] : array(),
		)
	);
}
add_action( 'wp_ajax_atelier_noir_run_demo_import', 'atelier_noir_onboarding_ajax_import' );

/**
 * Enqueue wizard assets.
 *
 * @param string $hook_suffix .
 */
function atelier_noir_onboarding_assets( $hook_suffix ) {
	if ( 'appearance_page_atelier-noir-setup' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_style(
		'studio-onboarding',
		get_stylesheet_directory_uri() . '/assets/css/onboarding.css',
		array(),
		(string) filemtime( get_stylesheet_directory() . '/assets/css/onboarding.css' )
	);

	wp_enqueue_script(
		'studio-onboarding',
		get_stylesheet_directory_uri() . '/assets/js/onboarding.js',
		array(),
		(string) filemtime( get_stylesheet_directory() . '/assets/js/onboarding.js' ),
		true
	);

	wp_localize_script(
		'studio-onboarding',
		'studioOnboarding',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'atelier_noir_onboarding' ),
			'homeUrl' => home_url( '/' ),
			'i18n'    => array(
				'importing'      => __( 'Installing demo content…', 'atelier-noir' ),
				'success'        => __( 'Demo installed successfully.', 'atelier-noir' ),
				'successSummary' => __( 'Demo installed successfully. Your pages, menu and sample content are ready.', 'atelier-noir' ),
				'error'          => __( 'Import failed. Activate Atelier Noir Core, then try again.', 'atelier-noir' ),
				'viewDetails'    => __( 'View details', 'atelier-noir' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'atelier_noir_onboarding_assets' );

/**
 * Render setup wizard.
 */
function atelier_noir_onboarding_render_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Insufficient permissions.', 'atelier-noir' ) );
	}

	$plugins = atelier_noir_onboarding_plugins_status();
	$done    = atelier_noir_onboarding_complete();
	$step    = isset( $_GET['step'] ) ? max( 1, min( 4, (int) $_GET['step'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<div class="studio-setup">
		<header class="studio-setup__hero">
			<p class="studio-setup__brand">Atelier Noir</p>
			<h1><?php esc_html_e( 'Welcome — let’s set up your site', 'atelier-noir' ); ?></h1>
			<p class="studio-setup__lead">
				<?php esc_html_e( 'Install the pages, the menu and sample content. Visitors land on a black page: white type, one dark-orange line, Start project and Explore services.', 'atelier-noir' ); ?>
			</p>
		</header>

		<ol class="studio-setup__steps">
			<li class="<?php echo $step >= 1 ? 'is-active' : ''; ?>"><?php esc_html_e( 'Welcome', 'atelier-noir' ); ?></li>
			<li class="<?php echo $step >= 2 ? 'is-active' : ''; ?>"><?php esc_html_e( 'Plugins', 'atelier-noir' ); ?></li>
			<li class="<?php echo $step >= 3 ? 'is-active' : ''; ?>"><?php esc_html_e( 'Demo content', 'atelier-noir' ); ?></li>
			<li class="<?php echo $step >= 4 ? 'is-active' : ''; ?>"><?php esc_html_e( 'Done', 'atelier-noir' ); ?></li>
		</ol>

		<div class="studio-setup__panel">
			<?php if ( 1 === $step ) : ?>
				<h2><?php esc_html_e( 'What you get', 'atelier-noir' ); ?></h2>
				<ul class="studio-setup__list">
					<li><?php esc_html_e( 'A black homepage with centered white type, one dark-orange line, and the buttons Start project and Explore services', 'atelier-noir' ); ?></li>
					<li><?php esc_html_e( 'Services as full-screen slides, titles in lowercase, with a photograph behind each service', 'atelier-noir' ); ?></li>
					<li><?php esc_html_e( 'Projects as full-screen images, with an optional caption on each image', 'atelier-noir' ); ?></li>
					<li><?php esc_html_e( 'Contact, legal pages, and a footer column for the theme author', 'atelier-noir' ); ?></li>
					<li><?php esc_html_e( 'Outfit and Inter, a wordmark favicon, and an Open Graph preview for sharing the link', 'atelier-noir' ); ?></li>
				</ul>
				<p>
					<a class="button button-primary button-hero" href="<?php echo esc_url( admin_url( 'themes.php?page=atelier-noir-setup&step=2' ) ); ?>">
						<?php esc_html_e( 'Get started', 'atelier-noir' ); ?>
					</a>
				</p>
			<?php elseif ( 2 === $step ) : ?>
				<h2><?php esc_html_e( 'Recommended plugins', 'atelier-noir' ); ?></h2>
				<p><?php esc_html_e( 'Install and activate these plugins before importing demo content.', 'atelier-noir' ); ?></p>
				<ul class="studio-setup__plugins">
					<?php foreach ( $plugins as $plugin ) : ?>
						<li>
							<strong><?php echo esc_html( $plugin['label'] ); ?></strong>
							<?php if ( $plugin['active'] ) : ?>
								<span class="studio-setup__badge studio-setup__badge--ok"><?php esc_html_e( 'Active', 'atelier-noir' ); ?></span>
							<?php else : ?>
								<span class="studio-setup__badge"><?php esc_html_e( 'Not active', 'atelier-noir' ); ?></span>
								<a href="<?php echo esc_url( $plugin['install_url'] ); ?>">
									<?php esc_html_e( 'Install', 'atelier-noir' ); ?>
								</a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<p>
					<a class="button" href="<?php echo esc_url( admin_url( 'themes.php?page=atelier-noir-setup&step=1' ) ); ?>"><?php esc_html_e( 'Back', 'atelier-noir' ); ?></a>
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'themes.php?page=atelier-noir-setup&step=3' ) ); ?>">
						<?php esc_html_e( 'Continue', 'atelier-noir' ); ?>
					</a>
				</p>
			<?php elseif ( 3 === $step ) : ?>
				<h2><?php esc_html_e( 'Import demo content', 'atelier-noir' ); ?></h2>
				<p><?php esc_html_e( 'This creates or updates the pages and the menu. The front page visitors see stays the black landing.', 'atelier-noir' ); ?></p>
				<?php if ( $done ) : ?>
					<div class="notice notice-success inline"><p><?php esc_html_e( 'Demo content was already installed. You can run the import again to refresh defaults.', 'atelier-noir' ); ?></p></div>
				<?php endif; ?>
				<p>
					<button type="button" class="button button-primary button-hero" id="studio-setup-import">
						<?php esc_html_e( 'Install demo content', 'atelier-noir' ); ?>
					</button>
				</p>
				<div id="studio-setup-import-log" class="studio-setup__log" hidden>
					<p class="studio-setup__log-summary" id="studio-setup-import-summary"></p>
					<details class="studio-setup__log-details" id="studio-setup-import-details" hidden>
						<summary><?php esc_html_e( 'View details', 'atelier-noir' ); ?></summary>
						<ul class="studio-setup__log-list" id="studio-setup-import-list"></ul>
					</details>
				</div>
				<p class="studio-setup__nav-after-import" hidden>
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'themes.php?page=atelier-noir-setup&step=4' ) ); ?>">
						<?php esc_html_e( 'Continue', 'atelier-noir' ); ?>
					</a>
				</p>
			<?php else : ?>
				<h2><?php esc_html_e( 'You’re all set', 'atelier-noir' ); ?></h2>
				<p><?php esc_html_e( 'Your site is ready. The homepage is the black landing. Edit services, projects and contact from the admin, and colors from Appearance → Customize.', 'atelier-noir' ); ?></p>
				<p class="studio-setup__actions">
					<a class="button button-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener">
						<?php esc_html_e( 'View site', 'atelier-noir' ); ?>
					</a>
					<a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">
						<?php esc_html_e( 'Open Customizer', 'atelier-noir' ); ?>
					</a>
					<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>">
						<?php esc_html_e( 'Edit pages', 'atelier-noir' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>

		<aside class="studio-setup__quote">
			<p>
				<?php esc_html_e( 'Need a tailored version — your own layouts, a migration to Elementor Pro Theme Builder, or extra features?', 'atelier-noir' ); ?>
				<a href="<?php echo esc_url( ATELIER_NOIR_QUOTE_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Request a customization quote', 'atelier-noir' ); ?></a>
			</p>
		</aside>
	</div>
	<?php
}

/**
 * Flag the one-time welcome notice when the theme is activated.
 */
function atelier_noir_flag_welcome_notice() {
	update_option( 'atelier_noir_welcome_notice', 1, false );
}
add_action( 'after_switch_theme', 'atelier_noir_flag_welcome_notice' );

/**
 * One-time welcome notice (docs + customization quote), dismissible.
 */
function atelier_noir_welcome_notice() {
	if ( ! get_option( 'atelier_noir_welcome_notice' ) || ! atelier_noir_onboarding_complete() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$dismiss = wp_nonce_url( admin_url( 'admin-post.php?action=atelier_noir_dismiss_welcome' ), 'atelier_noir_dismiss_welcome' );
	?>
	<div class="notice notice-success">
		<p>
			<strong><?php esc_html_e( 'Thanks for choosing Atelier Noir.', 'atelier-noir' ); ?></strong>
			<?php esc_html_e( 'Documentation and updates live on GitHub. Need a tailored version? A customization quote is one click away.', 'atelier-noir' ); ?>
		</p>
		<p>
			<a class="button" href="<?php echo esc_url( ATELIER_NOIR_REPO_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Documentation', 'atelier-noir' ); ?></a>
			<a class="button" href="<?php echo esc_url( ATELIER_NOIR_QUOTE_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Request a quote', 'atelier-noir' ); ?></a>
			<a class="button-link" href="<?php echo esc_url( $dismiss ); ?>"><?php esc_html_e( 'Dismiss', 'atelier-noir' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'atelier_noir_welcome_notice' );

/**
 * Dismiss the welcome notice for good.
 */
function atelier_noir_dismiss_welcome() {
	check_admin_referer( 'atelier_noir_dismiss_welcome' );
	if ( current_user_can( 'edit_theme_options' ) ) {
		delete_option( 'atelier_noir_welcome_notice' );
	}
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
	exit;
}
add_action( 'admin_post_atelier_noir_dismiss_welcome', 'atelier_noir_dismiss_welcome' );
