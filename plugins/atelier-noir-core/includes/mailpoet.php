<?php
/**
 * MailPoet — transactional + marketing (newsletter popup, list subscription).
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return bool
 */
function atelier_noir_mailpoet_is_active() {
	return class_exists( '\MailPoet\DI\ContainerWrapper' );
}

/**
 * @return \MailPoet\API\MP\v1\API|null
 */
function atelier_noir_mailpoet_api() {
	if ( ! atelier_noir_mailpoet_is_active() ) {
		return null;
	}
	try {
		return \MailPoet\DI\ContainerWrapper::getInstance()->get( \MailPoet\API\MP\v1\API::class );
	} catch ( \Throwable $e ) {
		return null;
	}
}

/**
 * @return \MailPoet\Settings\SettingsController|null
 */
function atelier_noir_mailpoet_settings() {
	if ( ! atelier_noir_mailpoet_is_active() ) {
		return null;
	}
	try {
		return \MailPoet\DI\ContainerWrapper::getInstance()->get( \MailPoet\Settings\SettingsController::class );
	} catch ( \Throwable $e ) {
		return null;
	}
}

/**
 * Default MailPoet list for site newsletter (popup).
 *
 * @return int
 */
function atelier_noir_mailpoet_newsletter_list_id() {
	$cached = (int) get_option( 'atelier_noir_mailpoet_newsletter_list_id', 0 );
	if ( $cached > 0 ) {
		return $cached;
	}

	$api = atelier_noir_mailpoet_api();
	if ( ! $api ) {
		return 0;
	}

	try {
		foreach ( $api->getLists() as $list ) {
			if ( ! is_array( $list ) ) {
				continue;
			}
			$type = isset( $list['type'] ) ? (string) $list['type'] : '';
			if ( in_array( $type, array( 'wp_users', 'woocommerce_users' ), true ) ) {
				continue;
			}
			$id = isset( $list['id'] ) ? (int) $list['id'] : 0;
			if ( $id > 0 ) {
				update_option( 'atelier_noir_mailpoet_newsletter_list_id', $id, false );
				return $id;
			}
		}
	} catch ( \Throwable $e ) {
		return 0;
	}

	return 0;
}

/**
 * Apply Studio sender + transactional routing once MailPoet is ready.
 */
function atelier_noir_mailpoet_apply_studio_defaults() {
	$settings = atelier_noir_mailpoet_settings();
	if ( ! $settings ) {
		return;
	}

	$config_version = '2026-06-12';
	if ( get_option( 'atelier_noir_mailpoet_config_version', '' ) === $config_version ) {
		atelier_noir_mailpoet_newsletter_list_id();
		return;
	}

	if ( ! $settings->get( 'send_transactional_emails', false ) ) {
		$settings->set( 'send_transactional_emails', true );
	}

	$sender = $settings->get( 'sender', array() );
	if ( ! is_array( $sender ) ) {
		$sender = array();
	}

	$contact = atelier_noir_email_contact();
	$settings->set(
		'sender',
		array(
			'name'    => atelier_noir_brand_name(),
			'address' => $contact,
		)
	);

	$notify = $settings->get( 'subscriber_email_notification', array() );
	if ( ! is_array( $notify ) ) {
		$notify = array();
	}
	$notify['address'] = $contact;
	$settings->set( 'subscriber_email_notification', $notify );

	$stats = $settings->get( 'stats_notifications', array() );
	if ( ! is_array( $stats ) ) {
		$stats = array();
	}
	$stats['address'] = $contact;
	$settings->set( 'stats_notifications', $stats );

	atelier_noir_mailpoet_newsletter_list_id();
	update_option( 'atelier_noir_mailpoet_config_version', $config_version, false );
}
add_action( 'mailpoet_initialized', 'atelier_noir_mailpoet_apply_studio_defaults' );
add_action( 'init', 'atelier_noir_mailpoet_apply_studio_defaults', 20 );

/**
 * Subscribe an email to the marketing list via MailPoet API.
 *
 * @param string $email Subscriber email.
 * @return array{ok:bool,already?:bool,error?:string}
 */
function atelier_noir_mailpoet_subscribe_newsletter( $email ) {
	$api = atelier_noir_mailpoet_api();
	if ( ! $api ) {
		return array( 'ok' => false, 'error' => 'mailpoet_missing' );
	}

	$list_id = atelier_noir_mailpoet_newsletter_list_id();
	if ( $list_id <= 0 ) {
		return array( 'ok' => false, 'error' => 'list_missing' );
	}

	$options = array(
		'send_confirmation_email'      => true,
		'schedule_welcome_email'       => true,
		'skip_subscriber_notification' => false,
	);

	try {
		$api->addSubscriber(
			array( 'email' => $email ),
			array( $list_id ),
			$options
		);
		return array( 'ok' => true );
	} catch ( \MailPoet\API\MP\v1\APIException $e ) {
		if ( $e->getCode() !== \MailPoet\API\MP\v1\APIException::SUBSCRIBER_EXISTS ) {
			return array( 'ok' => false, 'error' => $e->getMessage() );
		}
	} catch ( \Throwable $e ) {
		return array( 'ok' => false, 'error' => $e->getMessage() );
	}

	try {
		$subscriber = $api->getSubscriber( $email );
		$subscriber_id = isset( $subscriber['id'] ) ? (int) $subscriber['id'] : 0;
		if ( $subscriber_id <= 0 ) {
			return array( 'ok' => false, 'error' => 'subscriber_not_found' );
		}
		$api->subscribeToLists( $subscriber_id, array( $list_id ), $options );
		return array( 'ok' => true, 'already' => true );
	} catch ( \Throwable $e ) {
		return array( 'ok' => false, 'error' => $e->getMessage() );
	}
}

/**
 * Default From for wp_mail (contact forms, etc.) when WooCommerce filters do not apply.
 */
function atelier_noir_wp_mail_from_address( $from_email ) {
	if ( atelier_noir_wp_mail_is_orders_context() ) {
		return atelier_noir_email_orders();
	}
	return atelier_noir_email_contact();
}

/**
 * @return bool
 */
function atelier_noir_wp_mail_is_orders_context() {
	if ( doing_action( 'woocommerce_email_header' ) || doing_action( 'woocommerce_email_footer' ) ) {
		return true;
	}
	return (bool) apply_filters( 'atelier_noir_wp_mail_orders_context', false );
}

add_filter( 'wp_mail_from', 'atelier_noir_wp_mail_from_address' );
add_filter( 'wp_mail_from_name', function () {
	return atelier_noir_brand_name();
} );

/**
 * Notice when FluentSMTP may conflict with MailPoet transactional sending.
 */
function atelier_noir_mailpoet_fluent_smtp_notice() {
	if ( ! current_user_can( 'manage_options' ) || get_user_meta( get_current_user_id(), 'atelier_noir_fluent_smtp_notice_dismissed', true ) ) {
		return;
	}
	if ( ! atelier_noir_mailpoet_is_active() || ! defined( 'FLUENTMAIL' ) ) {
		return;
	}
	$settings = atelier_noir_mailpoet_settings();
	if ( ! $settings || ! $settings->get( 'send_transactional_emails', false ) ) {
		return;
	}
	$dismiss_url = wp_nonce_url(
		add_query_arg( 'atelier_noir_dismiss_fluent_smtp', '1' ),
		'atelier_noir_dismiss_fluent_smtp'
	);
	echo '<div class="notice notice-warning is-dismissible"><p>';
	echo esc_html__( 'Atelier Noir: MailPoet sends transactional emails. Deactivate FluentSMTP (or disable its wp_mail hook) to avoid conflicts.', 'atelier-noir-core' );
	echo ' <a href="' . esc_url( $dismiss_url ) . '">' . esc_html__( 'Dismiss', 'atelier-noir-core' ) . '</a>';
	echo '</p></div>';
}
add_action( 'admin_notices', 'atelier_noir_mailpoet_fluent_smtp_notice' );

function atelier_noir_mailpoet_dismiss_fluent_smtp_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( empty( $_GET['atelier_noir_dismiss_fluent_smtp'] ) ) {
		return;
	}
	check_admin_referer( 'atelier_noir_dismiss_fluent_smtp' );
	update_user_meta( get_current_user_id(), 'atelier_noir_fluent_smtp_notice_dismissed', '1' );
	wp_safe_redirect( remove_query_arg( array( 'atelier_noir_dismiss_fluent_smtp', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'atelier_noir_mailpoet_dismiss_fluent_smtp_notice' );
