<?php
/**
 * Template Name: Privacy Policy
 *
 * Compliant with the GDPR (General Data Protection Regulation).
 *
 * @package atelier-noir
 */

get_header();

while ( have_posts() ) :
	the_post();

	if ( atelier_noir_legal_show_elementor_content_slot() ) :
		?>
		<main class="studio-legal studio-legal--elementor" id="main-content">
			<div class="studio-legal__container">
				<?php the_content(); ?>
			</div>
		</main>
		<?php
	else :
		?>
		<main class="studio-legal" id="main-content">
		<div class="studio-legal__container">

	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="studio-legal__back">← <?php esc_html_e( 'Back to home', 'atelier-noir' ); ?></a>

	<header class="studio-legal__header">
		<p class="studio-legal__eyebrow"><?php echo esc_html( atelier_noir_brand_name() ); ?></p>
		<h1 class="studio-legal__title">Privacy Policy</h1>
	</header>

	<div class="studio-legal__content">

		<p>
			<?php echo esc_html( atelier_noir_brand_name() ); ?> attaches particular importance to the protection of your personal data and respect for your privacy. This policy describes how we collect, use, and protect your data in accordance with the General Data Protection Regulation (GDPR — EU Regulation 2016/679) and the amended French Data Protection Act.
		</p>

		<h2>1. Data Controller</h2>

		<div class="studio-legal__info-block">
			<p><strong><?php echo esc_html( atelier_noir_brand_name() ); ?></strong></p>
			<p><strong>Address:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_registered_office', 'Full address' ); ?></p>
			<p><strong>Email:</strong> <a href="mailto:<?php echo esc_attr( atelier_noir_email_contact() ); ?>"><?php echo esc_html( atelier_noir_email_contact() ); ?></a></p>
		</div>

		<h2>2. Data Collected</h2>

		<h3>2.1 Browsing Data</h3>
		<p>When you visit the site, we automatically collect:</p>
		<ul>
			<li>Your device's IP address;</li>
			<li>Your browser type and version;</li>
			<li>Pages viewed and time spent on each page;</li>
			<li>Date and time of connection;</li>
			<li>Operating system used.</li>
		</ul>
		<p>This data is collected via technical cookies necessary for the proper functioning of the site.</p>

		<h3>2.2 Customer Account Data</h3>
		<p>When you create an account or make a purchase, we collect:</p>
		<ul>
			<li>First and last name;</li>
			<li>Email address;</li>
			<li>Phone number (optional);</li>
			<li>Delivery and billing address;</li>
			<li>Order history.</li>
		</ul>

		<h3>2.3 Payment Data</h3>
		<p>
			Payment information (card number, expiry date, security code) is processed directly by our secure payment providers — <strong>Stripe</strong> and <strong>PayPal</strong> — and is never stored on our servers. These providers are PCI-DSS certified.
		</p>

		<h3>2.4 Newsletter</h3>
		<p>
			If you subscribe to our newsletter, we retain only your email address, collected with your explicit consent.
		</p>

		<h3>2.5 Contact Form</h3>
		<p>
			Information submitted via our contact forms (name, email, subject, message) is used exclusively to respond to your enquiry.
		</p>

		<h2>3. Purposes and Legal Bases for Processing</h2>

		<div class="studio-legal__info-block">
			<p><strong>Order and customer account management</strong><br>Legal basis: Performance of a contract</p>
			<p><strong>Payment processing</strong><br>Legal basis: Performance of a contract</p>
			<p><strong>Transactional emails</strong> (confirmation, delivery)<br>Legal basis: Performance of a contract</p>
			<p><strong>Newsletter</strong><br>Legal basis: Consent</p>
			<p><strong>Response to contact enquiries</strong><br>Legal basis: Legitimate interest</p>
			<p><strong>Statistical analysis of browsing</strong><br>Legal basis: Legitimate interest (anonymised data)</p>
			<p><strong>Legal and accounting obligations</strong><br>Legal basis: Legal obligation (10-year retention)</p>
		</div>

		<h2>4. Retention Periods</h2>

		<ul>
			<li><strong>Customer account data:</strong> for the duration of the account, then 3 years after the last activity;</li>
			<li><strong>Order data:</strong> 10 years (accounting obligation);</li>
			<li><strong>Newsletter data:</strong> until unsubscription or 3 years of inactivity;</li>
			<li><strong>Technical cookies:</strong> 13 months maximum;</li>
			<li><strong>Connection logs:</strong> 1 year.</li>
		</ul>

		<h2>5. Data Recipients</h2>

		<p>Your data may be shared with:</p>
		<ul>
			<li><strong>Stripe Inc.</strong> — card payment, Apple Pay, Google Pay;</li>
			<li><strong>PayPal (Europe) S.à r.l. et Cie, S.C.A.</strong> — PayPal payment;</li>
			<li><strong>Hosting provider</strong> — <?php atelier_noir_legal_field_html( 'atelier_noir_legal_host_name', 'Name' ); ?>;</li>
			<li><strong>Logistics carriers</strong> — for order delivery;</li>
			<li><strong>Email service provider</strong> — <?php atelier_noir_legal_field_html( 'atelier_noir_legal_email_provider', 'Brevo / Postmark' ); ?> for transactional emails.</li>
		</ul>
		<p>
			These processors act in accordance with our instructions and GDPR requirements. No data is transferred to third parties for commercial purposes.
		</p>

		<h2>6. Transfers Outside the European Union</h2>

		<p>
			Some of our providers (Stripe, PayPal) may process data outside the European Union. These transfers are governed by appropriate safeguards (European Commission standard contractual clauses or Privacy Shield/DPF for the United States).
		</p>

		<h2>7. Cookies</h2>

		<p>The site uses the following cookies:</p>
		<ul>
			<li><strong>Strictly necessary cookies:</strong> WordPress session, WooCommerce cart, security (nonces). These cookies cannot be disabled.</li>
			<li><strong>Currency cookie</strong> (<code>atelier_noir_currency</code>): stores your currency preference (1 year).</li>
			<li><strong>Newsletter cookie</strong> (<code>atelier_noir_nl_seen</code>): prevents the popup from reappearing for 30 days.</li>
		</ul>
		<p>
			You may configure your browser to refuse cookies or to alert you when cookies are sent, but this may affect certain site functionality.
		</p>

		<h2>8. Your Rights</h2>

		<p>Under the GDPR, you have the following rights:</p>
		<ul>
			<li><strong>Right of access</strong> (Art. 15): obtain confirmation that data concerning you is being processed and receive a copy;</li>
			<li><strong>Right to rectification</strong> (Art. 16): have inaccurate or incomplete data corrected;</li>
			<li><strong>Right to erasure</strong> (Art. 17): have your data deleted, subject to legal conditions;</li>
			<li><strong>Right to data portability</strong> (Art. 20): receive your data in a structured format;</li>
			<li><strong>Right to object</strong> (Art. 21): object to processing based on legitimate interest;</li>
			<li><strong>Right to restriction</strong> (Art. 18): request temporary suspension of processing;</li>
			<li><strong>Withdrawal of consent</strong>: at any time for processing based on consent (newsletter).</li>
		</ul>
		<p>
			To exercise your rights, contact us at: <a href="mailto:<?php echo esc_attr( atelier_noir_email_contact() ); ?>"><?php echo esc_html( atelier_noir_email_contact() ); ?></a><br>
			We will respond within a maximum of <strong>30 days</strong>.
		</p>
		<p>
			You also have the right to lodge a complaint with the <strong>CNIL</strong> (French Data Protection Authority): <a href="https://www.cnil.fr" target="_blank" rel="noopener">www.cnil.fr</a>.
		</p>

		<h2>9. Security</h2>

		<p>
			<?php echo esc_html( atelier_noir_brand_name() ); ?> implements appropriate technical and organisational measures to protect your data against unauthorised access, disclosure, alteration, or destruction. The site uses the HTTPS protocol to secure data exchanges.
		</p>

		<h2>10. Changes to This Privacy Policy</h2>

		<p>
			This policy may be updated at any time to reflect changes in our practices or to comply with new legal obligations. The date of the last update is indicated at the top of this document. By continuing to use our site after an update, you accept the revised policy.
		</p>

		<h2>Contact</h2>

		<p>
			For any questions regarding the protection of your data:<br>
			<a href="mailto:<?php echo esc_attr( atelier_noir_email_contact() ); ?>"><?php echo esc_html( atelier_noir_email_contact() ); ?></a>
		</p>

	</div>

</div>
</main>
		<?php
	endif;
endwhile;

get_footer();
