<?php
/**
 * Customizer — unlimited services repeater (JSON theme_mod).
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize services JSON.
 *
 * @param string $value JSON.
 * @return string
 */
function atelier_noir_sanitize_services_json( $value ) {
	if ( ! is_string( $value ) || '' === $value ) {
		return '[]';
	}
	$decoded = json_decode( wp_unslash( $value ), true );
	if ( ! is_array( $decoded ) ) {
		return '[]';
	}
	$clean = array();
	foreach ( $decoded as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$title = sanitize_text_field( $row['title'] ?? '' );
		if ( '' === $title ) {
			continue;
		}
		$id = sanitize_title( $row['id'] ?? $title );
		$clean[] = array(
			'id'     => $id,
			'title'  => $title,
			'text'   => sanitize_textarea_field( $row['text'] ?? '' ),
			'video'  => esc_url_raw( $row['video'] ?? '' ),
			'image'  => esc_url_raw( $row['image'] ?? '' ),
			'poster' => esc_url_raw( $row['poster'] ?? '' ),
		);
	}
	return wp_json_encode( $clean, JSON_UNESCAPED_UNICODE );
}

if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Repeater control for services.
	 */
	class Studio_Services_Repeater_Control extends WP_Customize_Control {
		/** @var string */
		public $type = 'atelier_noir_services_repeater';

		/**
		 * Render control.
		 */
		public function render_content() {
			$items = json_decode( $this->value() ?: '[]', true );
			if ( ! is_array( $items ) ) {
				$items = array();
			}
			?>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if ( $this->description ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
			</label>
			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ?: '[]' ); ?>" class="studio-services-json" />
			<div class="studio-services-repeater" data-target="<?php echo esc_attr( $this->id ); ?>">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php $this->render_row( $item, (int) $index ); ?>
				<?php endforeach; ?>
			</div>
			<p><button type="button" class="button studio-services-add"><?php esc_html_e( 'Add service', 'atelier-noir' ); ?></button></p>
			<script type="text/template" class="studio-services-row-template">
				<?php $this->render_row( array(), '__INDEX__' ); ?>
			</script>
			<?php
		}

		/**
		 * @param array       $item  Row data.
		 * @param int|string  $index Row index.
		 */
		private function render_row( $item, $index ) {
			?>
			<div class="studio-services-row" data-index="<?php echo esc_attr( (string) $index ); ?>">
				<p>
					<label><?php esc_html_e( 'ID (slug)', 'atelier-noir' ); ?></label>
					<input type="text" class="studio-svc-id" value="<?php echo esc_attr( $item['id'] ?? '' ); ?>" placeholder="fashion-consulting" />
				</p>
				<p>
					<label><?php esc_html_e( 'Title', 'atelier-noir' ); ?></label>
					<input type="text" class="studio-svc-title" value="<?php echo esc_attr( $item['title'] ?? '' ); ?>" />
				</p>
				<p>
					<label><?php esc_html_e( 'Description', 'atelier-noir' ); ?></label>
					<textarea class="studio-svc-text" rows="3"><?php echo esc_textarea( $item['text'] ?? '' ); ?></textarea>
				</p>
				<p>
					<label><?php esc_html_e( 'Video URL (optional, direct MP4/WebM)', 'atelier-noir' ); ?></label>
					<input type="url" class="studio-svc-video" value="<?php echo esc_attr( $item['video'] ?? '' ); ?>" />
				</p>
				<p>
					<label><?php esc_html_e( 'Image / poster URL', 'atelier-noir' ); ?></label>
					<input type="url" class="studio-svc-image" value="<?php echo esc_attr( $item['image'] ?? '' ); ?>" />
				</p>
				<p><button type="button" class="button-link studio-services-remove" style="color:#b32d2e;"><?php esc_html_e( 'Remove', 'atelier-noir' ); ?></button></p>
				<hr>
			</div>
			<?php
		}
	}
}

/**
 * Register repeater + scripts.
 *
 * @param WP_Customize_Manager $wp_customize .
 */
function atelier_noir_register_services_repeater( $wp_customize ) {
	$wp_customize->add_setting(
		'atelier_noir_svc_cta_label',
		array(
			'default'           => 'Book a Session',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'atelier_noir_svc_cta_label',
		array(
			'label'    => __( 'CTA button label (all slides)', 'atelier-noir' ),
			'section'  => 'atelier_noir_services_panel',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	$wp_customize->add_setting(
		'atelier_noir_services_json',
		array(
			'default'           => wp_json_encode( atelier_noir_services_defaults(), JSON_UNESCAPED_UNICODE ),
			'sanitize_callback' => 'atelier_noir_sanitize_services_json',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		new Studio_Services_Repeater_Control(
			$wp_customize,
			'atelier_noir_services_json',
			array(
				'label'       => __( 'Services (unlimited)', 'atelier-noir' ),
				'description' => __( 'Add, edit, or remove full-screen service slides. Each service links to Contact via ?service=slug.', 'atelier-noir' ),
				'section'     => 'atelier_noir_services_panel',
				'priority'    => 5,
			)
		)
	);
}
add_action( 'customize_register', 'atelier_noir_register_services_repeater', 35 );

/**
 * Customizer controls JS/CSS for repeater.
 */
function atelier_noir_services_repeater_assets() {
	wp_enqueue_script(
		'atelier-noir-customizer-services',
		get_stylesheet_directory_uri() . '/assets/js/customizer-services-repeater.js',
		array( 'jquery', 'customize-controls' ),
		(string) filemtime( get_stylesheet_directory() . '/assets/js/customizer-services-repeater.js' ),
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'atelier_noir_services_repeater_assets' );
