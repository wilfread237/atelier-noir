<?php
/**
 * One-shot migration: atelier_noir_* theme mods → an_*.
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map legacy option names to an_* options.
 *
 * @return array<string, string>
 */
function atelier_noir_migration_option_map() {
	return array(
		'atelier_noir_onboarding_complete' => 'an_onboarding_complete',
		'atelier_noir_import_applied_at'   => 'an_import_applied_at',
	);
}

/**
 * Run theme mod + option migration once (admin or CLI).
 */
function atelier_noir_maybe_migrate_theme_mods() {
	if ( get_option( 'an_theme_mods_migrated_v1', false ) ) {
		return;
	}

	$mods = get_theme_mods();
	if ( is_array( $mods ) ) {
		foreach ( $mods as $key => $value ) {
			if ( ! is_string( $key ) || ! str_starts_with( $key, 'atelier_noir_' ) ) {
				continue;
			}
			$new_key = 'an_' . substr( $key, strlen( 'atelier_noir_' ) );
			if ( ! array_key_exists( $new_key, $mods ) ) {
				set_theme_mod( $new_key, $value );
			}
		}
	}

	// Loader delay: prefer explicit an_loader_delay_ms after migration.
	if ( null === get_theme_mod( 'an_loader_delay_ms', null ) ) {
		$legacy_ms = (int) get_theme_mod( 'an_loader_min_ms', 0 );
		if ( $legacy_ms <= 0 ) {
			$legacy_ms = (int) get_theme_mod( 'atelier_noir_loader_min_ms', 7000 );
		}
		set_theme_mod( 'an_loader_delay_ms', max( 0, $legacy_ms > 0 ? $legacy_ms : 7000 ) );
	}

	foreach ( atelier_noir_migration_option_map() as $old => $new ) {
		$val = get_option( $old, null );
		if ( null !== $val && false === get_option( $new, false ) ) {
			update_option( $new, $val, false );
		}
	}

	update_option( 'an_theme_mods_migrated_v1', time(), false );
}
add_action( 'init', 'atelier_noir_maybe_migrate_theme_mods', 1 );
