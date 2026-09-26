<?php
/**
 * One-shot brand content: transparent header logos, 3 services, legal overlay copy.
 *
 * @package atelier-noir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Privacy policy body for legal overlay.
 *
 * @return string
 */
function atelier_noir_default_legal_privacy_body() {
	$brand = esc_html( atelier_noir_brand_name() );
	$email = esc_html( atelier_noir_email_contact() );
	return '<h2>1. Data Controller</h2>
<p>' . $brand . ' is responsible for processing personal data collected through this website. Contact: <a href="mailto:' . $email . '">' . $email . '</a>.</p>
<h2>2. Data We Collect</h2>
<p>We may collect browsing data (IP address, browser type, pages viewed), information submitted via contact email or forms, and newsletter email addresses when you subscribe with consent.</p>
<h2>3. Purposes &amp; Legal Bases</h2>
<ul>
<li>Responding to enquiries — legitimate interest</li>
<li>Newsletter — consent</li>
<li>Site security and analytics — legitimate interest</li>
</ul>
<h2>4. Your Rights</h2>
<p>Under GDPR, you may request access, rectification, erasure, restriction, portability, or object to processing. Contact us at the address above. You may also lodge a complaint with your supervisory authority.</p>
<h2>5. Retention</h2>
<p>Contact data is retained only as long as necessary to handle your request. Newsletter data is kept until you unsubscribe.</p>';
}

/**
 * Cookie policy body for legal overlay.
 *
 * @return string
 */
function atelier_noir_default_legal_cookies_body() {
	return '<h2>1. What Are Cookies?</h2>
<p>Cookies are small text files stored on your device when you visit a website. They help the site function and may be used for analytics or preferences.</p>
<h2>2. Cookies We Use</h2>
<ul>
<li><strong>Essential cookies</strong> — required for site operation and security.</li>
<li><strong>Analytics cookies</strong> — anonymous usage statistics (if enabled).</li>
<li><strong>Preference cookies</strong> — remember choices such as language or display settings.</li>
</ul>
<h2>3. Managing Cookies</h2>
<p>You can configure your browser to refuse or delete cookies. Disabling essential cookies may affect site functionality.</p>
<h2>4. Third Parties</h2>
<p>Embedded content or social links (e.g. Instagram) may set their own cookies when you interact with them.</p>';
}

/**
 * Terms & conditions body for legal overlay.
 *
 * @return string
 */
function atelier_noir_default_legal_terms_body() {
	$brand = esc_html( atelier_noir_brand_name() );
	$email = esc_html( atelier_noir_email_contact() );
	$host  = esc_html( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	return '<h2>1. Website Use</h2>
<p>By accessing ' . $host . ', you agree to use this website lawfully and not to misuse its content or infrastructure.</p>
<h2>2. Intellectual Property</h2>
<p>All content — text, images, videos, logos, and designs — is owned by ' . $brand . ' or used under licence. Reproduction without written permission is prohibited.</p>
<h2>3. Services</h2>
<p>Fashion consulting, styling, branding, and event services are subject to separate agreements and quotes. Information on this site is indicative and not contractual.</p>
<h2>4. Liability</h2>
<p>' . $brand . ' strives for accuracy but does not guarantee uninterrupted access or error-free content. Liability is limited to the extent permitted by applicable law.</p>
<h2>5. Governing Law</h2>
<p>These terms are governed by French law. Any dispute shall be submitted to the competent courts, subject to mandatory consumer protections.</p>
<h2>6. Contact</h2>
<p>Questions: <a href="mailto:' . $email . '">' . $email . '</a></p>';
}

/**
 * Apply brand defaults once (logos, services, legal, hero fallbacks).
 */
function atelier_noir_apply_brand_content_defaults() {
	$remove = array(
		'atelier_noir_logo_desktop_attachment',
		'atelier_noir_logo_mobile_attachment',
		'an_logo_desktop_attachment',
		'an_logo_mobile_attachment',
		'an_logo_attachment',
		'atelier_noir_logo_desktop_url',
		'atelier_noir_logo_mobile_url',
		'an_logo_desktop_url',
		'an_logo_mobile_url',
		'an_home_hero_attachment',
		'an_watermark_desktop_attachment',
		'an_watermark_mobile_attachment',
		'an_menu_icon_attachment',
	);

	foreach ( $remove as $key ) {
		remove_theme_mod( $key );
	}

	$loader_desk = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'loader-desktop.png' )
		: '';
	$loader_mob = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'loader-mobile.png' )
		: '';
	$hero_url = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'home-ceo-spotlight.png' )
		: '';
	$logo_url = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'logo-header-desktop.png' )
		: '';

	if ( $loader_desk ) {
		set_theme_mod( 'an_loader_desktop_url', $loader_desk );
	}
	if ( $loader_mob ) {
		set_theme_mod( 'an_loader_mobile_url', $loader_mob );
	}
	if ( $hero_url ) {
		set_theme_mod( 'an_home_hero_url', $hero_url );
	}
	if ( $logo_url ) {
		set_theme_mod( 'an_logo_url', $logo_url );
	}

	$void_pattern = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'pattern-gris-translucide.png' )
		: '';
	if ( $void_pattern ) {
		set_theme_mod( 'an_home_void_desktop_url', $void_pattern );
		set_theme_mod( 'an_home_void_mobile_url', $void_pattern );
		set_theme_mod( 'an_watermark_desktop_url', $void_pattern );
		set_theme_mod( 'an_watermark_mobile_url', $void_pattern );
	}

	set_theme_mod(
		'atelier_noir_services_json',
		wp_json_encode( atelier_noir_services_defaults(), JSON_UNESCAPED_UNICODE )
	);
	set_theme_mod( 'an_legal_privacy_body', atelier_noir_default_legal_privacy_body() );
	set_theme_mod( 'an_legal_cookies_body', atelier_noir_default_legal_cookies_body() );
	set_theme_mod( 'an_legal_terms_body', atelier_noir_default_legal_terms_body() );
	set_theme_mod( 'an_contact_email', atelier_noir_email_contact() );
	set_theme_mod( 'an_contact_cities', 'PARIS · LONDON · NEW YORK' );

	update_option( 'an_brand_content_v1', time(), false );
}

/**
 * Point home void + overlay watermarks at the official pattern asset (once).
 */
function atelier_noir_maybe_update_brand_pattern_assets() {
	if ( get_option( 'an_brand_pattern_v2', false ) ) {
		return;
	}

	$pattern = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'pattern-gris-translucide.png' )
		: '';
	$hero = function_exists( 'atelier_noir_brand_asset_uri' )
		? atelier_noir_brand_asset_uri( 'home-ceo-spotlight.png' )
		: '';

	if ( $pattern ) {
		set_theme_mod( 'an_home_void_desktop_url', $pattern );
		set_theme_mod( 'an_home_void_mobile_url', $pattern );
		set_theme_mod( 'an_watermark_desktop_url', $pattern );
		set_theme_mod( 'an_watermark_mobile_url', $pattern );
	}
	if ( $hero ) {
		set_theme_mod( 'an_home_hero_url', $hero );
	}

	update_option( 'an_brand_pattern_v2', time(), false );
}
add_action( 'init', 'atelier_noir_maybe_update_brand_pattern_assets', 9 );

/**
 * Run once after theme update.
 */
function atelier_noir_maybe_apply_brand_content_defaults() {
	if ( get_option( 'an_brand_content_v1', false ) ) {
		return;
	}
	atelier_noir_apply_brand_content_defaults();
}
add_action( 'init', 'atelier_noir_maybe_apply_brand_content_defaults', 8 );

/**
 * Reset services titles (remove any leftover "demo" text).
 * Runs once per install via option guard.
 */
function atelier_noir_maybe_reset_services_v3() {
	if ( get_option( 'an_brand_services_v3', false ) ) {
		return;
	}
	if ( function_exists( 'atelier_noir_services_defaults' ) ) {
		set_theme_mod(
			'atelier_noir_services_json',
			wp_json_encode( atelier_noir_services_defaults(), JSON_UNESCAPED_UNICODE )
		);
	}
	update_option( 'an_brand_services_v3', time(), false );
}
add_action( 'init', 'atelier_noir_maybe_reset_services_v3', 10 );
