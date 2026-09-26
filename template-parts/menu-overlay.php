<?php
/**
 * Editorial menu overlay markup.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items            = atelier_noir_menu_overlay_items();
$watermark_desk   = atelier_noir_menu_watermark_desktop_url();
$watermark_mobile = atelier_noir_menu_watermark_mobile_url();
$pattern_url      = $watermark_desk ?: $watermark_mobile;
$overlay_classes  = array( 'studio-mobile-menu', 'an-menu-overlay' );
$overlay_attrs    = array();

if ( $pattern_url ) {
	$overlay_classes[] = 'an-menu-overlay--pattern';
	$mobile_url        = $watermark_mobile ?: $pattern_url;
	$overlay_attrs['style'] = sprintf(
		'--an-menu-pattern: url(%1$s); --an-menu-pattern-mobile: url(%2$s);',
		esc_url( $pattern_url ),
		esc_url( $mobile_url )
	);
}
?>
<div
	id="studio-mobile-menu"
	class="<?php echo esc_attr( implode( ' ', $overlay_classes ) ); ?>"
	aria-hidden="true"
	role="dialog"
	aria-modal="true"
	aria-label="<?php esc_attr_e( 'Site menu', 'atelier-noir' ); ?>"
	<?php
	foreach ( $overlay_attrs as $attr => $value ) {
		echo esc_attr( $attr ) . '="' . esc_attr( $value ) . '" ';
	}
	?>
>
	<?php if ( $pattern_url ) : ?>
		<div class="an-menu-overlay__pattern an-menu-overlay__pattern--base" aria-hidden="true"></div>
	<?php endif; ?>

	<button class="studio-mobile-menu__close an-menu-overlay__close" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'atelier-noir' ); ?>">
		<span></span>
		<span></span>
	</button>

	<nav class="an-menu-overlay__nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'atelier-noir' ); ?>">
		<ul class="an-menu-overlay__list">
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$classes = array( 'an-menu-overlay__item' );
				if ( ! empty( $item['lowercase'] ) ) {
					$classes[] = 'an-menu-overlay__item--lower';
				}
				$link_classes = array( 'an-menu-overlay__link' );
				if ( ! empty( $item['lowercase'] ) ) {
					$link_classes[] = 'an-menu-overlay__link--lower';
				}
				?>
				<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="--an-menu-i: <?php echo esc_attr( (string) $index ); ?>;">
					<a class="<?php echo esc_attr( implode( ' ', $link_classes ) ); ?>" href="<?php echo esc_url( $item['url'] ); ?>">
						<?php echo esc_html( $item['label'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<?php if ( $pattern_url ) : ?>
		<div class="an-menu-overlay__pattern an-menu-overlay__pattern--blend" aria-hidden="true"></div>
	<?php endif; ?>
</div>
