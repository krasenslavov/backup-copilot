<?php
/**
 * Backups - Menu Registration
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit;  // Exit if accessed directly.

/**
 * Add main backups menu page
 */
function bkpc_add_manage_backups_menu() {
	$bkpc_admin = new BKPC_Admin();

	if ( '' === $bkpc_admin->compact_mode ) {
		add_menu_page(
			esc_html__( 'Backup Copilot', 'backup-copilot' ),
			esc_html__( 'Backup Copilot', 'backup-copilot' ),
			'manage_options',
			BKPC_MANAGE_BACKUPS_SLUG,
			__NAMESPACE__ . '\bkpc_display_manage_backups_page',
			'dashicons-backup',
			75
		);
	} else {
		add_submenu_page(
			'tools.php',
			esc_html__( 'Backup Copilot', 'backup-copilot' ),
			esc_html__( 'Backup Copilot', 'backup-copilot' ),
			'manage_options',
			BKPC_MANAGE_BACKUPS_SLUG,
			__NAMESPACE__ . '\bkpc_display_manage_backups_page'
		);
	}
}

add_action( 'admin_menu', __NAMESPACE__ . '\bkpc_add_manage_backups_menu' );
