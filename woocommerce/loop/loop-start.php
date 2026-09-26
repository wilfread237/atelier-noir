<?php
/**
 * Product Loop Start — Studio Theme Child
 * Grille BEM studio-products-grid ; colonnes via wc_get_loop_prop (défaut 3).
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<ul class="products studio-products-grid columns-<?php echo esc_attr( wc_get_loop_prop( 'columns', 3 ) ); ?>">
