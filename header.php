<?php
/**
 * En-tête du site (thème enfant).
 *
 * Surcharge de Hello Elementor : sur les écrans WooCommerce, le Theme Builder
 * peut enregistrer l’emplacement « header » sans rendu réel — le menu disparaît.
 * On affiche alors systématiquement le header du thème (dynamic-header / header).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$viewport_content = apply_filters( 'hello_elementor_viewport_content', 'width=device-width, initial-scale=1' );
$enable_skip_link = apply_filters( 'hello_elementor_enable_skip_link', true );
$skip_link_url    = apply_filters( 'hello_elementor_skip_link_url', '#content' );

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="<?php echo esc_attr( $viewport_content ); ?>">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php if ( $enable_skip_link ) { ?>
<a class="skip-link screen-reader-text" href="<?php echo esc_url( $skip_link_url ); ?>"><?php echo esc_html__( 'Skip to content', 'hello-elementor' ); ?></a>
<?php } ?>

<?php
if ( hello_elementor_display_header_footer() && ! ( function_exists( 'atelier_noir_is_loader_page' ) && atelier_noir_is_loader_page() ) ) {
	get_template_part( 'template-parts/dynamic-header' );
}
