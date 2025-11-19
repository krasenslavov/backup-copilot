<?php
/**
 * Settings submenu registration.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Add settings submenu page
 */
function bkpc_add_settings_menu() {
	$bkpc_admin = new BKPC_Admin();

	if ( '' === $bkpc_admin->compact_mode ) {
		add_submenu_page(
			BKPC_MANAGE_BACKUPS_SLUG,
			esc_html__( 'Settings', 'backup-copilot' ),
			null, // Hidden submenu.
			'manage_options',
			BKPC_SETTINGS_SLUG,
			__NAMESPACE__ . '\bkpc_display_settings_page'
		);
	} else {
		add_submenu_page(
			'tools.php',
			esc_html__( 'Settings', 'backup-copilot' ),
			null, // Hidden submenu.
			'manage_options',
			BKPC_SETTINGS_SLUG,
			__NAMESPACE__ . '\bkpc_display_settings_page'
		);
	}
}

add_action( 'admin_menu', __NAMESPACE__ . '\bkpc_add_settings_menu', 1000 );

/**
 * Add network admin settings submenu for multisite
 */
function bkpc_add_network_settings_menu() {
	if ( ! is_multisite() ) {
		return;
	}

	$bkpc_admin = new BKPC_Admin();

	if ( '' === $bkpc_admin->compact_mode ) {
		add_submenu_page(
			BKPC_MANAGE_BACKUPS_SLUG,
			esc_html__( 'Settings', 'backup-copilot' ),
			null, // Hidden submenu.
			'manage_network',
			BKPC_SETTINGS_SLUG,
			__NAMESPACE__ . '\bkpc_display_settings_page'
		);
	} else {
		add_submenu_page(
			'settings.php',
			esc_html__( 'Settings', 'backup-copilot' ),
			null, // Hidden submenu.
			'manage_network',
			BKPC_SETTINGS_SLUG,
			__NAMESPACE__ . '\bkpc_display_settings_page'
		);
	}
}

add_action( 'network_admin_menu', __NAMESPACE__ . '\bkpc_add_network_settings_menu', 1000 );
