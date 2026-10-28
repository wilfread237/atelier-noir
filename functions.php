<?php
/**
 * Atelier Noir theme functions.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ATELIER_NOIR_REPO_URL', 'https://github.com/wilfread237/atelier-noir' );
define( 'ATELIER_NOIR_QUOTE_URL', 'https://www.linkedin.com/in/wilfreadessomba/' );

require_once get_stylesheet_directory() . '/inc/an-theme-mod.php';
require_once get_stylesheet_directory() . '/inc/site-structure.php';
require_once get_stylesheet_directory() . '/inc/customizer-loader-page.php';
require_once get_stylesheet_directory() . '/inc/menu-overlay.php';
require_once get_stylesheet_directory() . '/inc/editorial.php';
require_once get_stylesheet_directory() . '/inc/footer-editorial.php';
require_once get_stylesheet_directory() . '/inc/commerce.php';
require_once get_stylesheet_directory() . '/inc/media.php';
require_once get_stylesheet_directory() . '/inc/services.php';
require_once get_stylesheet_directory() . '/inc/emails.php';
require_once get_stylesheet_directory() . '/inc/brand.php';
require_once get_stylesheet_directory() . '/inc/brand-content-defaults.php';
require_once get_stylesheet_directory() . '/inc/import-defaults.php';
require_once get_stylesheet_directory() . '/inc/home-hero.php';
require_once get_stylesheet_directory() . '/inc/urls.php';
require_once get_stylesheet_directory() . '/inc/onboarding.php';
require_once get_stylesheet_directory() . '/inc/customizer-site.php';
require_once get_stylesheet_directory() . '/inc/customizer-services-repeater.php';
require_once get_stylesheet_directory() . '/inc/customizer-footer.php';
require_once get_stylesheet_directory() . '/inc/customizer-legal-contact.php';
require_once get_stylesheet_directory() . '/inc/loader.php';
require_once get_stylesheet_directory() . '/inc/theme-documentation.php';
require_once get_stylesheet_directory() . '/inc/companion-plugin.php';

/**
 * Chargement des traductions du thème enfant.
 */
function atelier_noir_setup() {
	load_child_theme_textdomain( 'atelier-noir', get_stylesheet_directory() . '/languages' );
	add_theme_support( 'elementor' );

	if ( function_exists( 'atelier_noir_is_commerce_enabled' ) && atelier_noir_is_commerce_enabled() ) {
		add_theme_support( 'woocommerce', [
			'thumbnail_image_width' => 800,
			'single_image_width'    => 1200,
			'product_grid'          => [
				'default_columns' => 3,
				'min_columns'     => 1,
				'max_columns'     => 4,
			],
		] );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action( 'after_setup_theme', 'atelier_noir_setup', 5 );

// Boutique WooCommerce → inc/woocommerce-shop.php

// atelier_noir_shop_search_icon → inc/woocommerce-shop.php

/**
 * Avis admin si Elementor n’est pas actif (thème prévu pour l’édition des pages via Elementor).
 */
function atelier_noir_elementor_missing_notice() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	if ( class_exists( '\Elementor\Plugin' ) ) {
		return;
	}
	$theme = wp_get_theme();
	if ( 'atelier-noir' !== $theme->get_stylesheet() ) {
		return;
	}

	$install_url = admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' );
	?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<strong><?php esc_html_e( 'Atelier Noir:', 'atelier-noir' ); ?></strong>
			<?php esc_html_e( 'Install and activate Elementor to edit page content in the visual builder.', 'atelier-noir' ); ?>
			<a href="<?php echo esc_url( $install_url ); ?>"><?php esc_html_e( 'Search for Elementor', 'atelier-noir' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'atelier_noir_elementor_missing_notice' );

/**
 * Détermine si la page d'accueil doit afficher le gabarit de secours (pas encore de contenu Elementor / éditeur).
 *
 * @param int $post_id ID de la page d'accueil statique.
 * @return bool
 */
function atelier_noir_should_show_home_fallback( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return true;
	}

	$mode = get_post_meta( $post_id, '_elementor_edit_mode', true );
	if ( 'builder' === $mode ) {
		$raw = get_post_meta( $post_id, '_elementor_data', true );
		if ( is_string( $raw ) && '' !== $raw && '[]' !== $raw ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) && count( $decoded ) > 0 ) {
				return false;
			}
		}
		if ( is_array( $raw ) && count( $raw ) > 0 ) {
			return false;
		}
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return true;
	}

	$text = trim( wp_strip_all_tags( $post->post_content ) );

	return '' === $text;
}

/**
 * L’accueil doit-il utiliser le gabarit Personnaliseur (plutôt que la page / Elementor) ?
 *
 * @param int $page_id ID page d’accueil statique.
 * @return bool
 */
function atelier_noir_front_should_render_customizer_home( $page_id ) {
	$source = get_theme_mod( 'atelier_noir_home_source', 'auto' );
	if ( 'customizer' === $source ) {
		return true;
	}
	if ( 'page' === $source ) {
		return false;
	}
	return atelier_noir_should_show_home_fallback( $page_id );
}

/**
 * La page utilise un document Elementor enregistré (pas seulement l’éditeur vide).
 *
 * @param int $post_id ID de publication.
 * @return bool
 */
function atelier_noir_page_built_with_elementor( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	return (bool) \Elementor\Plugin::$instance->db->is_built_with_elementor( $post_id );
}

/**
 * Contexte éditeur ou aperçu Elementor : le gabarit doit appeler the_content().
 *
 * @return bool
 */
function atelier_noir_is_elementor_editor_or_preview() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	// Aperçu / iframe : selon versions, GET ou REQUEST.
	if ( ! empty( $_REQUEST['elementor-preview'] ) ) {
		return true;
	}
	$el = \Elementor\Plugin::$instance;
	if ( isset( $el->editor ) && is_object( $el->editor ) && method_exists( $el->editor, 'is_edit_mode' ) && $el->editor->is_edit_mode() ) {
		return true;
	}
	if ( isset( $el->preview ) && is_object( $el->preview ) && method_exists( $el->preview, 'is_preview_mode' ) && $el->preview->is_preview_mode() ) {
		return true;
	}
	return false;
}

/**
 * Afficher le header PHP du thème sur WooCommerce (sans passer par Elementor Theme Builder).
 * Évite un en-tête vide lorsque l’emplacement « header » est enregistré sans rendu.
 *
 * @return bool
 */
function atelier_noir_force_theme_header_on_wc() {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return false;
	}
	if ( is_woocommerce() || is_cart() || is_checkout() ) {
		return true;
	}
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return true;
	}
	return false;
}

/**
 * Gabarits légaux : the_content() uniquement dans l’iframe d’édition / aperçu Elementor.
 * Le design statique (texte juridique en PHP) reste affiché sur le site public.
 *
 * @return bool
 */
function atelier_noir_legal_has_elementor_content( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( $post_id <= 0 ) {
		return false;
	}
	return 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

function atelier_noir_legal_show_elementor_content_slot() {
	// Public site : toujours le gabarit PHP (texte juridique structuré).
	// Elementor : iframe éditeur / aperçu uniquement.
	return atelier_noir_is_elementor_editor_or_preview();
}

/**
 * Personnaliseur : couleurs globales + loader.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function atelier_noir_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'atelier_noir_theme_colors',
		array(
			'title'    => __( 'Atelier Noir — colors', 'atelier-noir' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_color_primary',
		array(
			'default'           => '#1a1a1a',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'atelier_noir_color_primary',
			array(
				'label'   => __( 'Primary color (text / accents)', 'atelier-noir' ),
				'section' => 'atelier_noir_theme_colors',
			)
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_color_bg',
		array(
			'default'           => '#f6f4f1',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'atelier_noir_color_bg',
			array(
				'label'   => __( 'Page background', 'atelier-noir' ),
				'section' => 'atelier_noir_theme_colors',
			)
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_color_accent',
		array(
			'default'           => '#8b7355',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'atelier_noir_color_accent',
			array(
				'label'   => __( 'Accent color (buttons, strong links)', 'atelier-noir' ),
				'section' => 'atelier_noir_theme_colors',
			)
		)
	);

	$wp_customize->add_section(
		'atelier_noir_loader',
		array(
			'title'    => __( 'Atelier Noir — homepage loader', 'atelier-noir' ),
			'priority' => 31,
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_loader_title',
		array(
			'default'           => atelier_noir_brand_name(),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'atelier_noir_loader_title',
		array(
			'label'   => __( 'Loader headline', 'atelier-noir' ),
			'section' => 'atelier_noir_loader',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_loader_sub',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'atelier_noir_loader_sub',
		array(
			'label'   => __( 'Subheadline (optional)', 'atelier-noir' ),
			'section' => 'atelier_noir_loader',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_loader_bg',
		array(
			'default'           => '#000000',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'atelier_noir_loader_bg',
			array(
				'label'   => __( 'Loader background', 'atelier-noir' ),
				'section' => 'atelier_noir_loader',
			)
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_loader_fg',
		array(
			'default'           => '#ffffff',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'atelier_noir_loader_fg',
			array(
				'label'   => __( 'Loader text color', 'atelier-noir' ),
				'section' => 'atelier_noir_loader',
			)
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_loader_min_ms',
		array(
			'default'           => 5500,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'atelier_noir_loader_min_ms',
		array(
			'label'       => __( 'Minimum loader duration (ms)', 'atelier-noir' ),
			'description' => __( 'Prevents a flash that is too brief on first visit.', 'atelier-noir' ),
			'section'     => 'atelier_noir_loader',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 8000,
				'step' => 100,
			),
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_loader_disable',
		array(
			'default'           => false,
			'sanitize_callback' => function ( $value ) {
				return (bool) $value;
			},
		)
	);
	$wp_customize->add_control(
		'atelier_noir_loader_disable',
		array(
			'label'   => __( 'Disable loader on homepage', 'atelier-noir' ),
			'section' => 'atelier_noir_loader',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'atelier_noir_customize_register' );

/**
 * Variables CSS depuis le Personnaliseur.
 */
function atelier_noir_customizer_css() {
	$primary = atelier_noir_get_theme_mod( 'color_primary', '#ffffff' );
	$bg      = atelier_noir_get_theme_mod( 'color_bg', '#000000' );
	$accent  = atelier_noir_get_theme_mod( 'color_accent', '#491F06' );
	$lbg     = atelier_noir_get_theme_mod( 'loader_bg', '#000000' );
	$lfg     = atelier_noir_get_theme_mod( 'loader_fg', '#ffffff' );

	if ( atelier_noir_get_theme_mod( 'site_black_background', true ) ) {
		$bg = '#000000';
	}

	if ( ! $primary || ! $bg || ! $accent || ! $lbg || ! $lfg ) {
		return;
	}

	$css = ':root {
		--color-text: ' . esc_attr( $primary ) . ';
		--color-bg: ' . esc_attr( $bg ) . ';
		--studio-accent: ' . esc_attr( $accent ) . ';
		--studio-loader-bg: ' . esc_attr( $lbg ) . ';
		--studio-loader-fg: ' . esc_attr( $lfg ) . ';
	}
	body { background-color: var(--color-bg) !important; color: var(--color-text); }
	.button:not(.studio-nl-popup__submit), .search-form__submit { background: var(--studio-accent) !important; border-color: var(--studio-accent); color: #fff; }
	.studio-nl-popup__submit, .studio-nl-popup__close { border: none !important; outline: none !important; box-shadow: none !important; }
	body[data-header-theme="dark"] a:hover:not(.an-menu-overlay__link):not(.an-legal-overlay__tab):not(.an-footer-editorial__link) { color: inherit; opacity: 0.72; }
	body:not([data-header-theme="dark"]) a:hover:not(.an-legal-overlay__tab):not(.an-footer-editorial__link):not(.an-menu-overlay__link) { color: var(--studio-accent); }
	.an-footer-editorial__link:hover,
	.an-footer-editorial__link:focus,
	.an-legal-overlay__body a:hover,
	.studio-header__toggle--vertical:hover { opacity: 1 !important; color: var(--studio-color-gray, #d9d9d9) !important; }
	.an-home-footer .an-footer-editorial__link:hover,
	.an-home-footer .an-footer-editorial__link:focus { color: var(--studio-color-off-white, #e8e4dc) !important; }
	.an-menu-overlay.is-open .an-menu-overlay__link,
	.an-menu-overlay__link:hover,
	.an-menu-overlay__link:focus { color: #ffffff !important; }
	.an-menu-overlay.is-open .an-menu-overlay__link { opacity: 1 !important; }
	.an-menu-overlay__link:hover,
	.an-menu-overlay__link:focus { opacity: 0.82 !important; }
	.an-legal-overlay__close,
	.an-legal-overlay__close:hover,
	.an-legal-overlay__close:focus {
		color: #ffffff !important;
		border: 0 !important;
		opacity: 1 !important;
		background: transparent !important;
	}
	.an-legal-overlay__close:hover,
	.an-legal-overlay__close:focus { opacity: 0.72 !important; }
	.an-legal-overlay__close-svg circle,
	.an-legal-overlay__close-svg path {
		fill: none !important;
		stroke: #ffffff !important;
		stroke-width: 1 !important;
	}
	.an-legal-overlay__tab:hover,
	.an-legal-overlay__tab:focus { color: rgba(255, 255, 255, 0.85) !important; }
	';

	wp_add_inline_style( 'atelier-noir', $css );
}
add_action( 'wp_enqueue_scripts', 'atelier_noir_customizer_css', 30 );

/**
 * Enqueue styles et scripts.
 */
function atelier_noir_enqueue_assets() {
	$theme          = wp_get_theme();
	$child_version  = $theme->get( 'Version' );
	$child_css_path = get_stylesheet_directory() . '/assets/css/child.css';
	$child_js_path  = get_stylesheet_directory() . '/assets/js/home-animations.js';
	$header_js_path = get_stylesheet_directory() . '/assets/js/header.js';
	$loader_css     = get_stylesheet_directory() . '/assets/css/loader.css';
	$loader_js      = get_stylesheet_directory() . '/assets/js/loader.js';
	$php_sections_css = get_stylesheet_directory() . '/assets/css/home-php-sections.css';

	wp_enqueue_style(
		'atelier-noir',
		get_stylesheet_directory_uri() . '/assets/css/child.css',
		array( 'hello-elementor' ),
		file_exists( $child_css_path ) ? (string) filemtime( $child_css_path ) : $child_version
	);

	// Header — skip on standalone loader page.
	if ( file_exists( $header_js_path ) && ! ( function_exists( 'atelier_noir_is_loader_page' ) && atelier_noir_is_loader_page() ) ) {
		wp_enqueue_script(
			'atelier-noir-header',
			get_stylesheet_directory_uri() . '/assets/js/header.js',
			array(),
			(string) filemtime( $header_js_path ),
			true
		);
	}

	if ( is_page_template( 'template-home.php' ) ) {
		$home_css = get_stylesheet_directory() . '/assets/css/home-editorial.css';
		if ( file_exists( $home_css ) ) {
			wp_enqueue_style(
				'atelier-noir-home-editorial',
				get_stylesheet_directory_uri() . '/assets/css/home-editorial.css',
				array( 'atelier-noir' ),
				(string) filemtime( $home_css )
			);
		}
	}

	$landing_css = get_stylesheet_directory() . '/assets/css/home-landing.css';
	if ( is_front_page() && file_exists( $landing_css ) && ! ( function_exists( 'atelier_noir_is_loader_page' ) && atelier_noir_is_loader_page() ) ) {
		wp_enqueue_style(
			'atelier-noir-home-landing',
			get_stylesheet_directory_uri() . '/assets/css/home-landing.css',
			array( 'atelier-noir' ),
			(string) filemtime( $landing_css )
		);
	}

	$use_php_sections_layout = is_page_template( 'template-contact.php' )
		|| is_page_template( 'template-services.php' )
		|| is_page_template( 'template-home-php-sections.php' );

	if ( $use_php_sections_layout && file_exists( $php_sections_css ) ) {
		wp_enqueue_style(
			'atelier-noir-home-php-sections',
			get_stylesheet_directory_uri() . '/assets/css/home-php-sections.css',
			array( 'atelier-noir' ),
			(string) filemtime( $php_sections_css )
		);
	}

	if ( is_page_template( 'template-loader.php' ) || ( function_exists( 'atelier_noir_is_loader_page' ) && atelier_noir_is_loader_page() ) ) {
		return;
	}

	// ── Assets WooCommerce (boutique, panier, checkout) — commerce activé uniquement ──
	$load_wc_theme_assets = function_exists( 'atelier_noir_is_commerce_enabled' )
		&& atelier_noir_is_commerce_enabled()
		&& function_exists( 'is_woocommerce' )
		&& ( is_woocommerce() || is_cart() || is_checkout() );
	if ( $load_wc_theme_assets ) {
		$wc_css = get_stylesheet_directory() . '/assets/css/woocommerce.css';
		$wc_js  = get_stylesheet_directory() . '/assets/js/woocommerce.js';

		wp_enqueue_style(
			'atelier-noir-woocommerce',
			get_stylesheet_directory_uri() . '/assets/css/woocommerce.css',
			array( 'atelier-noir' ),
			file_exists( $wc_css ) ? (string) filemtime( $wc_css ) : $child_version
		);

		wp_enqueue_script(
			'atelier-noir-woocommerce',
			get_stylesheet_directory_uri() . '/assets/js/woocommerce.js',
			array(),
			file_exists( $wc_js ) ? (string) filemtime( $wc_js ) : $child_version,
			true
		);
	}

	// ── Pages légales (ML, CGV, Confidentialité) ────────────────────────────
	$legal_templates = array(
		'template-mentions-legales.php',
		'template-politique-confidentialite.php',
		'template-cgv.php',
	);
	if ( is_page_template( $legal_templates ) ) {
		$legal_css = get_stylesheet_directory() . '/assets/css/legal.css';
		if ( file_exists( $legal_css ) ) {
			wp_enqueue_style(
				'atelier-noir-legal',
				get_stylesheet_directory_uri() . '/assets/css/legal.css',
				array( 'atelier-noir' ),
				(string) filemtime( $legal_css )
			);
		}
	}

	// ── Footer legacy (pages non éditoriales) ────────────────────────────────
	if ( ! ( function_exists( 'atelier_noir_has_inline_footer' ) && atelier_noir_has_inline_footer() ) ) {
		$footer_css = get_stylesheet_directory() . '/assets/css/footer.css';
		if ( file_exists( $footer_css ) ) {
			wp_enqueue_style(
				'atelier-noir-footer',
				get_stylesheet_directory_uri() . '/assets/css/footer.css',
				array( 'atelier-noir' ),
				(string) filemtime( $footer_css )
			);
		}

		$footer_nl_js = get_stylesheet_directory() . '/assets/js/footer-newsletter.js';
		if ( file_exists( $footer_nl_js ) && atelier_noir_newsletter_available() ) {
			wp_enqueue_script(
				'studio-footer-newsletter',
				get_stylesheet_directory_uri() . '/assets/js/footer-newsletter.js',
				array(),
				(string) filemtime( $footer_nl_js ),
				true
			);
			wp_localize_script( 'studio-footer-newsletter', 'atelierNoirFooterNL', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}
	}

	// ── Assets espace client (my-account + login) ────────────────────────────
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		$acc_css = get_stylesheet_directory() . '/assets/css/account.css';
		wp_enqueue_style(
			'atelier-noir-account',
			get_stylesheet_directory_uri() . '/assets/css/account.css',
			array( 'atelier-noir' ),
			file_exists( $acc_css ) ? (string) filemtime( $acc_css ) : $child_version
		);
		$status_css = get_stylesheet_directory() . '/assets/css/account-order-status.css';
		if ( file_exists( $status_css ) ) {
			wp_enqueue_style(
				'atelier-noir-account-order-status',
				get_stylesheet_directory_uri() . '/assets/css/account-order-status.css',
				array( 'atelier-noir-account' ),
				(string) filemtime( $status_css )
			);
		}
	}

	// ── Assets Journal & Single post ────────────────────────────────────────
	if ( is_home() || is_single() || is_category() || is_tag() || is_archive() ) {
		$journal_css = get_stylesheet_directory() . '/assets/css/journal.css';
		wp_enqueue_style(
			'atelier-noir-journal',
			get_stylesheet_directory_uri() . '/assets/css/journal.css',
			array( 'atelier-noir' ),
			file_exists( $journal_css ) ? (string) filemtime( $journal_css ) : $child_version
		);
	}

	// contact-page.css et projects.css → enqueues centralisés dans inc/editorial.php

	if ( is_page_template( 'template-services.php' ) ) {
		$services_css = get_stylesheet_directory() . '/assets/css/services.css';
		$services_js  = get_stylesheet_directory() . '/assets/js/services.js';

		wp_enqueue_style(
			'swiper',
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
			array(),
			'11'
		);

		wp_enqueue_style(
			'atelier-noir-services',
			get_stylesheet_directory_uri() . '/assets/css/services.css',
			array( 'atelier-noir', 'swiper' ),
			file_exists( $services_css ) ? (string) filemtime( $services_css ) : $child_version
		);

		wp_enqueue_script(
			'swiper',
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
			array(),
			'11',
			true
		);

		wp_enqueue_script(
			'atelier-noir-services',
			get_stylesheet_directory_uri() . '/assets/js/services.js',
			array( 'swiper' ),
			file_exists( $services_js ) ? (string) filemtime( $services_js ) : $child_version,
			true
		);
	}

	if ( atelier_noir_use_legacy_overlay_loader() && is_front_page() && ! is_customize_preview() && ! atelier_noir_get_theme_mod( 'an_loader_disable', false ) ) {
		wp_enqueue_style(
			'atelier-noir-loader',
			get_stylesheet_directory_uri() . '/assets/css/loader.css',
			array( 'atelier-noir' ),
			file_exists( $loader_css ) ? (string) filemtime( $loader_css ) : $child_version
		);

		wp_enqueue_script(
			'atelier-noir-loader',
			get_stylesheet_directory_uri() . '/assets/js/loader.js',
			array(),
			file_exists( $loader_js ) ? (string) filemtime( $loader_js ) : $child_version,
			true
		);
	}

	// GSAP + animations : chargés uniquement sur les pages qui en ont besoin.
	// Évite de charger ~70 KB CDN sur chaque page (journal, boutique, contact…).
	$needs_gsap = is_page_template( 'template-services-studio.php' )
		|| is_page_template( 'template-home-php-sections.php' );

	if ( $needs_gsap && get_theme_mod( 'atelier_noir_animations_on', true ) ) {
		wp_enqueue_script(
			'gsap',
			'https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js',
			array(),
			null,
			true
		);

		wp_enqueue_script(
			'gsap-scrolltrigger',
			'https://cdn.jsdelivr.net/npm/gsap@3/dist/ScrollTrigger.min.js',
			array( 'gsap' ),
			null,
			true
		);

		wp_enqueue_script(
			'atelier-noir-home-animations',
			get_stylesheet_directory_uri() . '/assets/js/home-animations.js',
			array( 'gsap', 'gsap-scrolltrigger' ),
			file_exists( $child_js_path ) ? (string) filemtime( $child_js_path ) : $child_version,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'atelier_noir_enqueue_assets', 20 );

// wp_get_attachment_url (URLs externes en _wp_attached_file) → inc/currency.php

/**
 * Injecter data-header-theme="light" sur <body> dès l'ouverture de la balise
 * pour les pages WooCommerce et le blog/journal (fond clair).
 * wp_body_open s'exécute juste après <body> → document.body existe déjà.
 */
function atelier_noir_body_header_theme() {
	if ( function_exists( 'atelier_noir_is_loader_page' ) && atelier_noir_is_loader_page() ) {
		return;
	}

	$theme = 'dark';

	if ( function_exists( 'atelier_noir_get_theme_mod' ) && ! atelier_noir_get_theme_mod( 'site_black_background', true ) ) {
		$theme = '';
	}

	if ( function_exists( 'is_woocommerce' ) &&
	     ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		$theme = 'light';
	} elseif ( is_page( get_option( 'woocommerce_myaccount_page_id' ) ) ) {
		$theme = 'light';
	} elseif ( ! atelier_noir_get_theme_mod( 'site_black_background', true ) && ( is_home() || is_archive() || is_search() ) ) {
		$theme = 'light';
	} elseif ( ! atelier_noir_get_theme_mod( 'site_black_background', true ) && is_singular( 'post' ) ) {
		$theme = 'light';
	}

	if ( $theme ) {
		echo '<script>document.body.setAttribute("data-header-theme","' . esc_js( $theme ) . '");</script>' . "\n";
	}
}
add_action( 'wp_body_open', 'atelier_noir_body_header_theme', 1 );

/**
 * Bloque le scroll pendant l’affichage du loader.
 */
function atelier_noir_loader_body_class( $classes ) {
	if ( atelier_noir_use_legacy_overlay_loader() && is_front_page() && ! is_customize_preview() && ! atelier_noir_get_theme_mod( 'an_loader_disable', false ) ) {
		$classes[] = 'has-studio-loader';
	}
	return $classes;
}
add_filter( 'body_class', 'atelier_noir_loader_body_class' );

/**
 * Synchronise les champs Customizer (Eyes catcher) vers la section héro Elementor.
 * Déclenché à chaque clic sur "Publier" dans le Personnaliseur.
 *
 * Champs lus :
 *   - atelier_noir_eyes_video_url  → background_video_link de la section #studio-hero
 *   - atelier_noir_eyes_poster_url → image de fallback / poster
 */
function atelier_noir_sync_eyes_to_elementor() {
	$video_url  = function_exists( 'atelier_noir_resolve_media_url' )
		? atelier_noir_resolve_media_url( 'atelier_noir_eyes_video_url', 'atelier_noir_eyes_video_attachment' )
		: get_theme_mod( 'atelier_noir_eyes_video_url', '' );
	$poster_url = function_exists( 'atelier_noir_resolve_media_url' )
		? atelier_noir_resolve_media_url( 'atelier_noir_eyes_poster_url', 'atelier_noir_eyes_poster_attachment' )
		: get_theme_mod( 'atelier_noir_eyes_poster_url', '' );

	if ( empty( $video_url ) && empty( $poster_url ) ) {
		return;
	}

	$home_id = (int) get_option( 'page_on_front', 0 );
	if ( $home_id <= 0 ) {
		return;
	}

	$raw      = get_post_meta( $home_id, '_elementor_data', true );
	$raw      = is_string( $raw ) ? $raw : '';
	$sections = json_decode( wp_unslash( $raw ), true );

	if ( ! is_array( $sections ) || empty( $sections ) ) {
		return;
	}

	$updated = false;
	foreach ( $sections as &$section ) {
		$css_id = isset( $section['settings']['css_id'] ) ? (string) $section['settings']['css_id'] : '';
		if ( 'studio-hero' !== $css_id ) {
			continue;
		}
		if ( ! empty( $video_url ) ) {
			$section['settings']['background_background']     = 'video';
			$section['settings']['background_video_link']     = esc_url_raw( $video_url );
			$section['settings']['background_play_on_mobile'] = 'yes';
		}
		if ( ! empty( $poster_url ) ) {
			$section['settings']['background_video_fallback'] = array( 'url' => esc_url_raw( $poster_url ), 'id' => 0 );
			$section['settings']['background_image']          = array( 'url' => esc_url_raw( $poster_url ), 'id' => 0 );
		}
		$updated = true;
		break;
	}
	unset( $section );

	if ( ! $updated ) {
		return;
	}

	update_post_meta( $home_id, '_elementor_data', wp_slash( wp_json_encode( $sections, JSON_UNESCAPED_UNICODE ) ) );

	global $wpdb;
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = '_elementor_css'", $home_id ) );
	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}
add_action( 'customize_save_after', 'atelier_noir_sync_eyes_to_elementor' );

// Home Elementor layout is applied via Setup wizard / one-time admin upgrade — not on every Customizer save (preserves Elementor edits).

// Devise → inc/currency.php
// Emails WooCommerce → inc/woocommerce-emails.php
