<?php
/**
 * Documentation du thème — écran d’administration.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Affiche la page de documentation (HTML sécurisé).
 */
function atelier_noir_documentation_render_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Droits insuffisants.', 'atelier-noir' ) );
	}

	$html_path = get_stylesheet_directory() . '/docs/documentation-admin.html';
	$raw_html  = is_readable( $html_path ) ? (string) file_get_contents( $html_path ) : '';

	?>
	<div class="wrap atelier-noir-doc">
		<h1><?php esc_html_e( 'Documentation — Studio Theme Child', 'atelier-noir' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Reference for editing the site: Customizer, page templates, WooCommerce and media.', 'atelier-noir' ); ?>
		</p>
		<div class="atelier-noir-doc__box" style="max-width:920px;background:#fff;border:1px solid #c3c4c7;padding:1.5rem 2rem;margin-top:1rem;">
			<div class="atelier-noir-doc__body" style="font-size:14px;line-height:1.65;">
			<?php
			if ( '' === $raw_html ) {
				echo '<p>' . esc_html__( 'Fichier docs/documentation-admin.html introuvable.', 'atelier-noir' ) . '</p>';
			} else {
				echo wp_kses_post( $raw_html );
			}
			?>
			</div>
		</div>
		<p style="margin-top:1.5rem;">
			<a href="<?php echo esc_url( admin_url( 'themes.php?page=atelier-noir-setup' ) ); ?>"><?php esc_html_e( '← Studio Theme Setup', 'atelier-noir' ); ?></a>
			&nbsp;|&nbsp;
			<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Open Customizer', 'atelier-noir' ); ?></a>
		</p>
	</div>
	<?php
}
