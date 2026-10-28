<?php
/**
 * Footer — minimal close when editorial layout renders inline footer.
 *
 * @package atelier-noir
 */

if ( function_exists( 'atelier_noir_has_inline_footer' ) && atelier_noir_has_inline_footer() ) :
	wp_footer();
	?>
</body>
</html>
	<?php
	return;
endif;
?>
</main>

<footer class="studio-footer" role="contentinfo">

	<!-- ══ Corps du footer ═══════════════════════════════════════════════════ -->
	<div class="studio-footer__body">

		<!-- Col 1 — Marque -->
		<div class="studio-footer__brand">
			<a class="studio-footer__logo" href="<?php echo esc_url( function_exists( 'atelier_noir_home_url' ) ? atelier_noir_home_url() : home_url( '/' ) ); ?>" rel="home">
				<?php atelier_noir_render_wordmark( 'an-wordmark an-wordmark--footer' ); ?>
				<span class="screen-reader-text"><?php echo esc_html( atelier_noir_brand_name() ); ?></span>
			</a>
			<p class="studio-footer__tagline">
				<?php echo esc_html( atelier_noir_footer_mod( 'atelier_noir_footer_tagline', __( 'Fashion — Creative identity — Art direction', 'atelier-noir' ) ) ); ?>
			</p>
			<div class="studio-footer__social">
				<?php get_template_part( 'template-parts/social', 'links' ); ?>
			</div>
		</div>

		<!-- Col 2 — Navigation site -->
		<div class="studio-footer__nav-group">
			<p class="studio-footer__nav-label"><?php esc_html_e( 'Navigation', 'atelier-noir' ); ?></p>
			<nav aria-label="<?php esc_attr_e( 'Footer navigation', 'atelier-noir' ); ?>">
				<ul class="studio-footer__nav-list">
					<li><a href="<?php echo esc_url( function_exists( 'atelier_noir_home_url' ) ? atelier_noir_home_url() : home_url( '/home/' ) ); ?>"><?php esc_html_e( 'Home', 'atelier-noir' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php esc_html_e( 'Services', 'atelier-noir' ); ?></a></li>
					<?php if ( function_exists( 'atelier_noir_is_commerce_enabled' ) && atelier_noir_is_commerce_enabled() ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ?: home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Shop', 'atelier-noir' ); ?></a></li>
					<?php endif; ?>
					<li><a href="<?php echo esc_url( home_url( '/projects/' ) ); ?>"><?php esc_html_e( 'Projects', 'atelier-noir' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'atelier-noir' ); ?></a></li>
				</ul>
			</nav>
		</div>

		<!-- Col 3 — Légal + contact -->
		<div class="studio-footer__nav-group">
			<p class="studio-footer__nav-label"><?php esc_html_e( 'Information', 'atelier-noir' ); ?></p>
			<ul class="studio-footer__nav-list">
				<li><a href="<?php echo esc_url( atelier_noir_legal_notice_url() ); ?>"><?php esc_html_e( 'Legal notice', 'atelier-noir' ); ?></a></li>
				<li><a href="<?php echo esc_url( atelier_noir_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy', 'atelier-noir' ); ?></a></li>
				<li><a href="<?php echo esc_url( atelier_noir_terms_url() ); ?>"><?php esc_html_e( 'Terms & conditions', 'atelier-noir' ); ?></a></li>
				<?php
				$mail_contact = atelier_noir_footer_mod( 'atelier_noir_footer_email_contact', atelier_noir_email_contact() );
				$mail_orders  = atelier_noir_footer_mod( 'atelier_noir_footer_email_orders', atelier_noir_email_orders() );
				?>
				<?php if ( $mail_contact ) : ?>
				<li><a href="mailto:<?php echo esc_attr( $mail_contact ); ?>"><?php echo esc_html( $mail_contact ); ?></a></li>
				<?php endif; ?>
				<?php if ( function_exists( 'atelier_noir_is_commerce_enabled' ) && atelier_noir_is_commerce_enabled() && $mail_orders ) : ?>
				<li><a href="mailto:<?php echo esc_attr( $mail_orders ); ?>"><?php echo esc_html( $mail_orders ); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>

		<div class="studio-footer__nav-group">
			<p class="studio-footer__nav-label"><?php esc_html_e( 'Theme author', 'atelier-noir' ); ?></p>
			<ul class="studio-footer__nav-list">
				<li><a href="<?php echo esc_url( 'https://www.linkedin.com/in/wilfreadessomba' ); ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a></li>
				<li><a href="<?php echo esc_url( 'mailto:wilfreadessomba@gmail.com' ); ?>"><?php echo esc_html( 'Contact Me' ); ?></a></li>
				<li><a href="<?php echo esc_url( 'https://www.instagram.com/wilfreadessomba' ); ?>" target="_blank" rel="noopener noreferrer">Instagram</a></li>
				<li><a href="<?php echo esc_url( 'https://www.tiktok.com/@with_ead' ); ?>" target="_blank" rel="noopener noreferrer">TikTok</a></li>
			</ul>
		</div>

		<?php if ( atelier_noir_newsletter_available() ) : ?>
		<!-- Col 4 — Newsletter mini -->
		<div class="studio-footer__newsletter">
			<p class="studio-footer__nav-label"><?php esc_html_e( 'Newsletter', 'atelier-noir' ); ?></p>
			<p class="studio-footer__newsletter-sub">
				<?php esc_html_e( 'Editorials, exclusives, and behind the scenes from the Studio.', 'atelier-noir' ); ?>
			</p>
			<form class="studio-footer__nl-form" id="studio-footer-nl-form" novalidate>
				<?php wp_nonce_field( 'atelier_noir_newsletter_subscribe', 'atelier_noir_nl_nonce' ); ?>
				<input type="text" name="atelier_noir_hp_nl" value="" tabindex="-1"
					style="position:absolute;left:-9999px;width:1px;height:1px;" aria-hidden="true">
				<div class="studio-footer__nl-field">
					<input
						type="email"
						name="email"
						id="studio-footer-nl-email"
						class="studio-footer__nl-input"
						placeholder="<?php esc_attr_e( 'Your email', 'atelier-noir' ); ?>"
						required
						autocomplete="email"
						aria-label="<?php esc_attr_e( 'Newsletter email address', 'atelier-noir' ); ?>"
					>
				</div>
				<label class="studio-footer__nl-consent">
					<input type="checkbox" name="consent" id="studio-footer-nl-consent" value="1" required>
					<span><?php echo esc_html( sprintf( /* translators: %s: brand name. */ __( 'I agree to the privacy policy and to receive marketing communications from %s.', 'atelier-noir' ), atelier_noir_brand_name() ) ); ?></span>
				</label>
				<button
					type="submit"
					class="studio-footer__nl-submit"
					id="studio-footer-nl-submit"
					hidden
				>
					<?php esc_html_e( 'Subscribe', 'atelier-noir' ); ?>
				</button>
				<p class="studio-footer__nl-success" aria-live="polite" hidden><?php esc_html_e( 'Thank you, you are subscribed.', 'atelier-noir' ); ?></p>
				<p class="studio-footer__nl-error" aria-live="polite" hidden><?php esc_html_e( 'Something went wrong. Please try again.', 'atelier-noir' ); ?></p>
			</form>
		</div>
		<?php endif; ?>

	</div>

	<div class="studio-footer__bar">
		<p class="studio-footer__copy">
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( atelier_noir_brand_name() ); ?>.
			<?php esc_html_e( 'All rights reserved.', 'atelier-noir' ); ?>
		</p>
	</div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
