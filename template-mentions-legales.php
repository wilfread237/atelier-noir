<?php
/**
 * Template Name: Legal Notice
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
		<h1 class="studio-legal__title">Legal Notice</h1>
	</header>

	<div class="studio-legal__content">

		<h2>Site Publisher</h2>

		<div class="studio-legal__info-block">
			<p><strong>Company name:</strong> <?php echo esc_html( atelier_noir_brand_name() ); ?></p>
			<p><strong>Legal form:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_form', 'To be completed: SASU / SARL / Sole proprietor…' ); ?></p>
			<p><strong>Share capital:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_share_capital', 'To be completed' ); ?></p>
			<p><strong>Registered office:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_registered_office', 'Full address' ); ?></p>
			<p><strong>SIRET:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_siret', 'SIRET number' ); ?></p>
			<p><strong>EU VAT number:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_vat', 'FR XX XXX XXX XXX' ); ?></p>
			<p><strong>Publication director:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_publication_director', 'Full name of the publication director' ); ?></p>
			<p><strong>Contact:</strong> <a href="mailto:<?php echo esc_attr( atelier_noir_email_contact() ); ?>"><?php echo esc_html( atelier_noir_email_contact() ); ?></a></p>
		</div>

		<h2>Hosting</h2>

		<div class="studio-legal__info-block">
			<p><strong>Host:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_host_name', 'Production hosting provider name' ); ?></p>
			<p><strong>Address:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_host_address', 'Hosting provider address' ); ?></p>
			<p><strong>Website:</strong> <?php atelier_noir_legal_field_html( 'atelier_noir_legal_host_website', 'Hosting provider URL' ); ?></p>
		</div>

		<h2>Intellectual Property</h2>

		<p>
			All content published on the website <strong><?php echo esc_html( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ); ?></strong> — text, photographs, illustrations, logos, videos, graphic compositions — is the exclusive property of <?php echo esc_html( atelier_noir_brand_name() ); ?> or is used under authorisation granted by the rights holders.
		</p>
		<p>
			Any reproduction, representation, modification, publication, adaptation, or exploitation, even partial, of any element of the site, by any means whatsoever, is strictly prohibited without the prior written consent of <?php echo esc_html( atelier_noir_brand_name() ); ?>.
		</p>
		<p>
			Any unauthorised use of the site or any of its elements shall be considered an infringement and prosecuted in accordance with the provisions of Articles L.335-2 et seq. of the French Intellectual Property Code.
		</p>

		<h2>Hyperlinks</h2>

		<p>
			The site may contain links to third-party websites. <?php echo esc_html( atelier_noir_brand_name() ); ?> exercises no control over these external sites and shall not be held liable for their content, data protection practices, or availability.
		</p>
		<p>
			Any link pointing to the <?php echo esc_html( atelier_noir_brand_name() ); ?> website must receive prior written authorisation.
		</p>

		<h2>Limitation of Liability</h2>

		<p>
			<?php echo esc_html( atelier_noir_brand_name() ); ?> endeavours to ensure the accuracy and currency of the information published on the site. However, it does not guarantee the completeness, accuracy, or suitability of such information for any particular purpose.
		</p>
		<p>
			<?php echo esc_html( atelier_noir_brand_name() ); ?> shall not be held liable for any direct or indirect damage resulting from access to the site or use of the information published thereon.
		</p>

		<h2>Governing Law and Jurisdiction</h2>

		<p>
			This legal notice is governed by French law. In the event of a dispute, and after an attempt at amicable resolution, the competent courts within the jurisdiction of <?php echo esc_html( atelier_noir_brand_name() ); ?>'s registered office shall have exclusive jurisdiction.
		</p>

		<h2>Consumer Mediation</h2>

		<p>
			In accordance with Article L.612-1 of the French Consumer Code, every consumer has the right to avail themselves free of charge of a consumer mediator for the amicable resolution of a dispute.
		</p>
		<p>
			Designated mediator: <?php atelier_noir_legal_field_html( 'atelier_noir_legal_mediator', 'Name and contact details of the mediator' ); ?><br>
			European Online Dispute Resolution platform: <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener">https://ec.europa.eu/consumers/odr</a>
		</p>

		<h2>Contact</h2>

		<p>
			For any questions regarding this website:<br>
			<a href="mailto:<?php echo esc_attr( atelier_noir_email_contact() ); ?>"><?php echo esc_html( atelier_noir_email_contact() ); ?></a>
		</p>

	</div>

</div>
</main>
		<?php
	endif;
endwhile;

get_footer();
