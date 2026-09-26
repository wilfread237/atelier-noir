<?php
/**
 * Template Name: Terms of Sale
 *
 * Compliant with the French Consumer Code.
 * Applicable to the online sale of physical products and service offerings.
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
		<h1 class="studio-legal__title">Terms of Sale</h1>
	</header>

	<div class="studio-legal__content">

		<p>
			These Terms of Sale (hereinafter the "Terms") govern any order placed on the website <strong><?php echo esc_html( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ); ?></strong> (hereinafter the "Site") with <?php echo esc_html( atelier_noir_brand_name() ); ?> (hereinafter the "Seller").
		</p>
		<p>
			Any order constitutes full and unreserved acceptance of these Terms by the purchaser (hereinafter the "Customer"). These Terms take precedence over any other general or specific terms and conditions of the Customer.
		</p>

		<h2>Article 1 — Seller Identification</h2>

		<div class="studio-legal__info-block">
			<p><strong><?php echo esc_html( atelier_noir_brand_name() ); ?></strong></p>
			<p><strong>Legal form:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_form', 'To be completed' ); ?></p>
			<p><strong>Registered office:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_registered_office', 'Full address' ); ?></p>
			<p><strong>SIRET:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_siret', 'Number' ); ?></p>
			<p><strong>EU VAT number:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_vat', 'FR XX XXX XXX XXX' ); ?></p>
			<p><strong>Orders email:</strong> <a href="mailto:<?php echo esc_attr( atelier_noir_email_orders() ); ?>"><?php echo esc_html( atelier_noir_email_orders() ); ?></a></p>
			<p><strong>Customer service email:</strong> <a href="mailto:<?php echo esc_attr( atelier_noir_email_contact() ); ?>"><?php echo esc_html( atelier_noir_email_contact() ); ?></a></p>
		</div>

		<h2>Article 2 — Products and Services Offered</h2>

		<h3>2.1 Physical Products (Shop)</h3>
		<p>
			<?php echo esc_html( atelier_noir_brand_name() ); ?> sells fashion items and accessories (clothing, headwear, collaborative art objects). Products are presented with descriptions and photographs that are as accurate as possible. Slight colour variations may occur depending on your screen display settings.
		</p>

		<h3>2.2 Services</h3>
		<p>
			The Studio offers image and fashion consulting services: Fashion Consulting, Personal Styling &amp; Branding, Art Direction, and Event support. The detailed terms of each service are set out in a personalised quote.
		</p>

		<h2>Article 3 — Prices</h2>

		<p>
			Prices are expressed in the currency selected by the Customer and are stated <strong>including all taxes (VAT included)</strong> for purchasers located in the European Union, in accordance with applicable VAT rates.
		</p>
		<p>
			For customers located outside the European Union, prices may be displayed excluding local taxes. Any applicable taxes and customs duties at destination remain the sole responsibility of the Customer.
		</p>
		<p>
			<?php echo esc_html( atelier_noir_brand_name() ); ?> reserves the right to modify its prices at any time. Products are invoiced on the basis of the prices in effect at the time the order is confirmed.
		</p>
		<p>
			Delivery charges are shown separately during checkout and are added to the price of the products.
		</p>

		<h2>Article 4 — Order</h2>

		<h3>4.1 Order Process</h3>
		<ol>
			<li>Selection of product(s) and addition to cart;</li>
			<li>Review of cart and quantities;</li>
			<li>Entry of delivery and billing information;</li>
			<li>Selection of payment method;</li>
			<li>Confirmation and payment;</li>
			<li>Order confirmation by email.</li>
		</ol>

		<h3>4.2 Order Confirmation</h3>
		<p>
			An order confirmation is sent by email to the address provided by the Customer. This confirmation constitutes the sales contract.
		</p>

		<h3>4.3 Stock Availability</h3>
		<p>
			Product offers and prices are valid for as long as they are visible on the Site. In the event of unavailability after order confirmation, the Customer will be informed as soon as possible and may choose an exchange, store credit, or full refund.
		</p>

		<h2>Article 5 — Payment</h2>

		<h3>5.1 Accepted Payment Methods</h3>
		<ul>
			<li><strong>Credit/debit card</strong> (Visa, Mastercard, CB) via Stripe — secure 3D Secure payment;</li>
			<li><strong>Apple Pay</strong> — via Stripe;</li>
			<li><strong>Google Pay</strong> — via Stripe;</li>
			<li><strong>PayPal</strong> — via PayPal Payments.</li>
		</ul>

		<h3>5.2 Payment Security</h3>
		<p>
			All transactions are secured by SSL encryption. The Customer's banking data does not pass through <?php echo esc_html( atelier_noir_brand_name() ); ?>'s servers and is not retained. Processing is handled by Stripe Inc. and PayPal, both PCI-DSS Level 1 certified.
		</p>

		<h3>5.3 Invoicing</h3>
		<p>
			An invoice is available in the Customer account area on the Site and is also attached to the confirmation email. Payment is due immediately upon ordering.
		</p>

		<h2>Article 6 — Delivery</h2>

		<h3>6.1 Delivery Area</h3>
		<p>
			<?php echo esc_html( atelier_noir_brand_name() ); ?> delivers to the following countries: mainland France, the European Union, and internationally depending on carrier availability.
			<?php
			$zones = atelier_noir_legal_mod( 'atelier_noir_legal_delivery_zones', '' );
			if ( '' !== trim( $zones ) ) {
				echo ' ' . esc_html( $zones );
			} else {
				echo ' <span class="studio-legal__todo">[Specify zones if necessary]</span>';
			}
			?>
		</p>

		<h3>6.2 Delivery Times</h3>
		<ul>
			<li><strong>Mainland France:</strong> 3 to 5 business days;</li>
			<li><strong>European Union:</strong> 5 to 10 business days;</li>
			<li><strong>International:</strong> 10 to 21 business days depending on destination.</li>
		</ul>
		<p>These timeframes are indicative and run from the date of dispatch confirmation.</p>

		<h3>6.3 Delivery Charges</h3>
		<p>
			Delivery charges are calculated based on weight, volume, and destination and are displayed during checkout before final confirmation.
		</p>

		<h3>6.4 Order Tracking</h3>
		<p>
			A tracking number is provided by email upon dispatch. The Customer may also view the status of their order in their account area.
		</p>

		<h3>6.5 Receipt and Inspection</h3>
		<p>
			Upon receipt, the Customer is invited to inspect the condition of the parcel and products in the presence of the carrier. Any anomaly (damage, missing items) must be reported by registered letter with acknowledgment of receipt to the carrier within <strong>3 business days</strong> of delivery, and to <?php echo esc_html( atelier_noir_brand_name() ); ?> at <a href="mailto:<?php echo esc_attr( atelier_noir_email_orders() ); ?>"><?php echo esc_html( atelier_noir_email_orders() ); ?></a>.
		</p>

		<h2>Article 7 — Right of Withdrawal</h2>

		<p>
			In accordance with Article L.221-18 of the French Consumer Code, the Customer has a <strong>14 calendar day right of withdrawal</strong> from receipt of the parcel, without having to provide reasons or pay any penalty.
		</p>

		<h3>7.1 Exercising the Right of Withdrawal</h3>
		<p>
			To exercise this right, the Customer must notify their decision by email to <a href="mailto:<?php echo esc_attr( atelier_noir_email_orders() ); ?>"><?php echo esc_html( atelier_noir_email_orders() ); ?></a> or via their account area before the expiry of the 14-day period. They may use the standard withdrawal form in the appendix.
		</p>

		<h3>7.2 Return of Products</h3>
		<p>
			The product must be returned in its original condition, complete, unused, in its original packaging, together with the invoice. Return shipping costs are borne by the Customer unless otherwise agreed.
		</p>
		<p>
			The return address is provided by email after notification of withdrawal.
		</p>

		<h3>7.3 Refund</h3>
		<p>
			The refund will be processed within <strong>14 days</strong> of receipt of the returned product, using the same payment method as that used for the order.
		</p>

		<h3>7.4 Exceptions to the Right of Withdrawal</h3>
		<p>
			In accordance with Article L.221-28 of the French Consumer Code, the right of withdrawal does not apply to:
		</p>
		<ul>
			<li>Products made to the Customer's specifications or clearly personalised;</li>
			<li>Services fully performed with the consumer's agreement before the expiry of the withdrawal period.</li>
		</ul>

		<h2>Article 8 — Legal Warranties</h2>

		<p>
			Regardless of any commercial warranty, the Seller is bound by the following legal warranties:
		</p>
		<ul>
			<li><strong>Legal conformity warranty</strong> (Art. L.217-4 et seq. of the French Consumer Code): 2 years from delivery of the goods;</li>
			<li><strong>Warranty against hidden defects</strong> (Art. 1641 et seq. of the French Civil Code).</li>
		</ul>
		<p>
			For any claim under legal warranties, contact: <a href="mailto:<?php echo esc_attr( atelier_noir_email_orders() ); ?>"><?php echo esc_html( atelier_noir_email_orders() ); ?></a>
		</p>

		<h2>Article 9 — Liability</h2>

		<p>
			<?php echo esc_html( atelier_noir_brand_name() ); ?> shall not be held liable for failure to perform the contract in the event of stock shortage, force majeure, disruption or strike affecting postal or carrier services, flood, or fire.
		</p>

		<h2>Article 10 — Personal Data Protection</h2>

		<p>
			The processing of personal data collected in connection with orders is described in our <a href="<?php echo esc_url( atelier_noir_privacy_policy_url() ); ?>">Privacy Policy</a>, available on the Site.
		</p>

		<h2>Article 11 — Intellectual Property</h2>

		<p>
			Purchase of a product does not confer any intellectual property rights over associated visuals, logos, or creations, which remain the exclusive property of <?php echo esc_html( atelier_noir_brand_name() ); ?> or its creative partners.
		</p>

		<h2>Article 12 — Governing Law and Disputes</h2>

		<p>
			These Terms are governed by French law. In the event of a dispute, an amicable solution will be sought as a priority. Failing that, the dispute shall be brought before the competent courts within the jurisdiction of the Seller's registered office.
		</p>
		<p>
			In accordance with Article L.612-1 of the French Consumer Code, in the event of an unresolved dispute, the Customer may avail themselves free of charge of consumer mediation.
		</p>
		<p>
			Designated mediator: <?php atelier_noir_legal_field_html( 'atelier_noir_legal_mediator', 'Name and contact details of the mediator' ); ?>
		</p>

		<h2>Appendix — Withdrawal Form</h2>

		<div class="studio-legal__info-block">
			<p><em>(Complete and return this form only if you wish to withdraw from the contract)</em></p>
			<p>To the attention of <?php echo esc_html( atelier_noir_brand_name() ); ?> — <a href="mailto:<?php echo esc_attr( atelier_noir_email_orders() ); ?>"><?php echo esc_html( atelier_noir_email_orders() ); ?></a></p>
			<p>I/We (*) hereby give notice of my/our (*) withdrawal from the contract for the sale of the goods (*) listed below:</p>
			<p>— Ordered on (*) / received on (*):</p>
			<p>— Name of consumer(s):</p>
			<p>— Address of consumer(s):</p>
			<p>— Signature of consumer(s) (only if this notice is submitted on paper):</p>
			<p>— Date:</p>
			<p><em>(*) Delete as appropriate.</em></p>
		</div>

	</div>

</div>
</main>
		<?php
	endif;
endwhile;

get_footer();
