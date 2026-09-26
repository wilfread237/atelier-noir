<?php
/**
 * Newsletter popup — Atelier Noir.
 *
 * Appears after 7s; dismiss cookie lasts 30 minutes (atelier_noir_nl_seen).
 * Design éditorial inspiré de Tom Ford : fond sombre, typographie imposante,
 * formulaire minimaliste.
 *
 * Intégration : abonnement via MailPoet (liste marketing + emails de confirmation).
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HTML du popup injecté juste après <body>.
 * Masqué par défaut, révélé par le JS après 7s.
 */
function atelier_noir_newsletter_popup_html() {
	if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
		return;
	}
	if ( ! empty( $_REQUEST['elementor-preview'] ) ) {
		return;
	}
	if ( function_exists( 'atelier_noir_get_theme_mod' ) && ! atelier_noir_get_theme_mod( 'an_newsletter_popup_enabled', false ) ) {
		return;
	}
	if ( function_exists( 'atelier_noir_is_loader_page' ) && atelier_noir_is_loader_page() ) {
		return;
	}
	?>
	<div
		id="studio-nl-popup"
		class="studio-nl-popup"
		role="dialog"
		aria-modal="true"
		aria-labelledby="studio-nl-title"
		hidden
	>
		<div class="studio-nl-popup__backdrop" id="studio-nl-backdrop"></div>

		<div class="studio-nl-popup__card">

			<!-- Bouton fermer -->
			<button
				type="button"
				class="studio-nl-popup__close"
				id="studio-nl-close"
				aria-label="<?php esc_attr_e( 'Close', 'atelier-noir-core' ); ?>"
			>
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
					stroke-width="1.2" stroke-linecap="round" aria-hidden="true">
					<line x1="18" y1="6" x2="6" y2="18"/>
					<line x1="6" y1="6" x2="18" y2="18"/>
				</svg>
			</button>

			<!-- Image éditoriale (gauche) -->
			<div class="studio-nl-popup__visual" aria-hidden="true">
				<div class="studio-nl-popup__visual-inner"></div>
			</div>

			<!-- Contenu (droite) -->
			<div class="studio-nl-popup__content">

				<p class="studio-nl-popup__eyebrow">
					<?php echo esc_html( get_theme_mod( 'atelier_noir_nl_eyebrow', atelier_noir_brand_name() ) ); ?>
				</p>

				<h2 class="studio-nl-popup__title" id="studio-nl-title">
					<?php echo esc_html( get_theme_mod( 'atelier_noir_nl_title_1', 'Be among' ) ); ?><br>
					<?php echo esc_html( get_theme_mod( 'atelier_noir_nl_title_2', 'the first to know.' ) ); ?>
				</h2>

				<p class="studio-nl-popup__sub">
					<?php echo esc_html( get_theme_mod( 'atelier_noir_nl_subtitle', 'Editorials, behind-the-scenes, exclusive previews.' ) ); ?>
				</p>

				<!-- Formulaire -->
				<form
					class="studio-nl-popup__form"
					id="studio-nl-form"
					novalidate
				>
					<?php wp_nonce_field( 'atelier_noir_newsletter_subscribe', 'atelier_noir_nl_nonce' ); ?>

					<div class="studio-nl-popup__field">
						<label for="studio-nl-email" class="studio-nl-popup__label sr-only">
							<?php esc_html_e( 'Email address', 'atelier-noir-core' ); ?>
						</label>
						<input
							type="email"
							id="studio-nl-email"
							name="email"
							class="studio-nl-popup__input"
							placeholder="<?php esc_attr_e( 'Your email address', 'atelier-noir-core' ); ?>"
							required
							autocomplete="email"
						>
					</div>

					<!-- Honeypot -->
					<input type="text" name="atelier_noir_hp_nl" value="" tabindex="-1"
						style="position:absolute;left:-9999px;width:1px;height:1px;" aria-hidden="true">

					<label class="studio-nl-popup__consent">
						<input type="checkbox" name="consent" id="studio-nl-consent" value="1" required>
						<span><?php echo esc_html( sprintf( /* translators: %s: brand name. */ __( 'I agree to the privacy policy and to receive marketing communications from %s.', 'atelier-noir-core' ), atelier_noir_brand_name() ) ); ?></span>
					</label>

					<button type="submit" class="studio-nl-popup__submit">
						<span class="studio-nl-popup__submit-text">
							<?php esc_html_e( 'Subscribe', 'atelier-noir-core' ); ?>
						</span>
						<span class="studio-nl-popup__submit-loader" aria-hidden="true">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none"
								stroke="currentColor" stroke-width="2" stroke-linecap="round">
								<path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke-dasharray="60" stroke-dashoffset="60">
									<animate attributeName="stroke-dashoffset" dur="1s" values="60;0" repeatCount="indefinite"/>
								</path>
							</svg>
						</span>
					</button>

					<p class="studio-nl-popup__success" aria-live="polite" hidden>
						<?php esc_html_e( 'Thank you — you are now part of the Studio circle.', 'atelier-noir-core' ); ?>
					</p>

					<p class="studio-nl-popup__error" aria-live="polite" hidden>
						<?php esc_html_e( 'Something went wrong. Please try again.', 'atelier-noir-core' ); ?>
					</p>
				</form>

				<p class="studio-nl-popup__privacy">
					<?php echo esc_html( get_theme_mod( 'atelier_noir_nl_privacy', 'Your data stays confidential. Unsubscribe anytime.' ) ); ?>
				</p>

			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_body_open', 'atelier_noir_newsletter_popup_html', 10 );

/**
 * Endpoint AJAX pour l'abonnement newsletter (MailPoet).
 */
function atelier_noir_newsletter_subscribe_ajax() {
	// Vérifications sécurité
	if ( empty( $_POST['atelier_noir_nl_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['atelier_noir_nl_nonce'] ) ), 'atelier_noir_newsletter_subscribe' ) ) {
		wp_send_json_error( array( 'message' => 'invalid_nonce' ), 400 );
	}

	// Honeypot
	if ( ! empty( $_POST['atelier_noir_hp_nl'] ) ) {
		wp_send_json_success(); // Silencieux pour ne pas alerter les bots
	}

	$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'invalid_email' ), 422 );
	}

	if ( empty( $_POST['consent'] ) ) {
		wp_send_json_error( array( 'message' => 'consent_required' ), 422 );
	}

	if ( ! function_exists( 'atelier_noir_mailpoet_subscribe_newsletter' ) ) {
		wp_send_json_error( array( 'message' => 'mailpoet_missing' ), 503 );
	}

	$result = atelier_noir_mailpoet_subscribe_newsletter( $email );
	if ( empty( $result['ok'] ) ) {
		wp_send_json_error(
			array( 'message' => isset( $result['error'] ) ? $result['error'] : 'subscribe_failed' ),
			500
		);
	}

	if ( ! empty( $result['already'] ) ) {
		wp_send_json_success( array( 'already' => true ) );
	}

	wp_send_json_success( array( 'subscribed' => true ) );
}
add_action( 'wp_ajax_atelier_noir_newsletter_subscribe',        'atelier_noir_newsletter_subscribe_ajax' );
add_action( 'wp_ajax_nopriv_atelier_noir_newsletter_subscribe', 'atelier_noir_newsletter_subscribe_ajax' );

/**
 * Enqueue CSS + JS du popup.
 */
function atelier_noir_newsletter_assets() {
	if ( function_exists( 'atelier_noir_get_theme_mod' ) && ! atelier_noir_get_theme_mod( 'an_newsletter_popup_enabled', false ) ) {
		return;
	}
	if ( is_admin() ) {
		return;
	}
	$dir = ATELIER_NOIR_CORE_DIR;
	$ver = file_exists( $dir . 'assets/js/newsletter.js' )
		? (string) filemtime( $dir . 'assets/js/newsletter.js' )
		: ATELIER_NOIR_CORE_VERSION;
	wp_enqueue_style(
		'studio-newsletter',
		ATELIER_NOIR_CORE_URL . 'assets/css/newsletter.css',
		array(),
		file_exists( $dir . 'assets/css/newsletter.css' ) ? (string) filemtime( $dir . 'assets/css/newsletter.css' ) : $ver
	);
	wp_enqueue_script(
		'studio-newsletter',
		ATELIER_NOIR_CORE_URL . 'assets/js/newsletter.js',
		array(),
		$ver,
		true
	);
	wp_localize_script( 'studio-newsletter', 'studioNL', array(
		'ajaxurl'      => admin_url( 'admin-ajax.php' ),
		'delay'        => 7000,
		'cookieMinutes' => 30,
	) );

	$img_url = '';
	$att_id  = absint( get_theme_mod( 'atelier_noir_nl_visual_attachment', 0 ) );
	if ( $att_id ) {
		$img_url = (string) wp_get_attachment_image_url( $att_id, 'large' );
	}
	if ( '' === $img_url ) {
		$img_url = (string) get_theme_mod(
			'atelier_noir_nl_visual_url',
			'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&q=80'
		);
	}
	if ( $img_url ) {
		$css = '.studio-nl-popup__visual-inner{background-image:linear-gradient(160deg, rgba(10,10,10,0.35) 0%, rgba(10,10,10,0.0) 60%), url(' . esc_url( $img_url ) . ');background-size:cover;background-position:center;}';
		wp_add_inline_style( 'studio-newsletter', $css );
	}
}
add_action( 'wp_enqueue_scripts', 'atelier_noir_newsletter_assets' );
