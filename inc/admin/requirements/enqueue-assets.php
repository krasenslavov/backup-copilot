<?php
/**
 * Admin assets enqueuing for styles and scripts.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Enqueue admin assets (styles and scripts) for the plugin.
 */
function bkpc_enqueue_admin_assets() {
	if ( ! is_admin() ) {
		return;
	}

	$screen = get_current_screen();

	// Enqueue on dashboard for widget.
	if ( 'dashboard' === $screen->base ) {
		wp_enqueue_style(
			'bkpc-admin',
			BKPC_PLUGIN_DIR_URL . 'assets/dist/css/bkpc-admin.min.css',
			array(),
			BKPC_PLUGIN_VERSION,
			'all'
		);
	}

	// Enqueue on all plugin pages (those starting with bkpc_).
	if ( isset( $screen->id ) && strpos( $screen->id, 'bkpc_' ) !== false ) {
		// Enqueue styles.
		wp_enqueue_style(
			'bkpc-admin',
			BKPC_PLUGIN_DIR_URL . 'assets/dist/css/bkpc-admin.min.css',
			array(),
			BKPC_PLUGIN_VERSION,
			'all'
		);

		// Enqueue media library for backups page.
		if ( strpos( $screen->id, 'bkpc_manage_backups' ) !== false ) {
			wp_enqueue_media();
		}

		// Enqueue scripts.
		wp_enqueue_script(
			'bkpc-admin',
			BKPC_PLUGIN_DIR_URL . 'assets/dist/js/bkpc-admin.min.js',
			array( 'jquery' ),
			BKPC_PLUGIN_VERSION,
			true
		);

		// Localize script.
		wp_localize_script(
			'bkpc-admin',
			'bkpc',
			array(
				'pluginUrl' => BKPC_PLUGIN_DIR_URL,
				'ajaxUrl'   => esc_url( admin_url( 'admin-ajax.php' ) ),
				'ajaxNonce' => wp_create_nonce( 'bkpc_ajax_nonce' ),
				'backupUrl' => esc_url( home_url( '/' ) ) . str_replace( ABSPATH, '', BKPC_PLUGIN_BACKUP_DIR_PATH ),
				'dbName'    => BKPC_DB_NAME,
				'strings'   => array(
					'processing'        => esc_html__( 'Processing...', 'backup-copilot' ),
					'success'           => esc_html__( 'Success!', 'backup-copilot' ),
					'error'             => esc_html__( 'Error occurred', 'backup-copilot' ),
					'confirm'           => esc_html__( 'Are you sure?', 'backup-copilot' ),
					'deleteConfirm'     => esc_html__( 'Are you sure you want to delete this backup?', 'backup-copilot' ),
					'restoreConfirm'    => esc_html__( 'Are you sure you want to restore this backup?', 'backup-copilot' ),
					'creatingBackup'    => esc_html__( 'Creating backup...', 'backup-copilot' ),
					'downloadingBackup' => esc_html__( 'Downloading backup...', 'backup-copilot' ),
				),
			)
		);
	}
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\bkpc_enqueue_admin_assets' );
