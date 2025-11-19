<?php
/**
 * Backups action handlers initialization.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit;  // Exit if accessed directly.

/**
 * Initialize all backup action handlers.
 */
function bkpc_init_manage_backup_actions() {
	// Initialize Create Backup handler.
	$create_backup = new BKPC_Create_Backup();
	$create_backup->init();

	// Initialize Delete Backup handler.
	$delete_backup = new BKPC_Delete_Backup();
	$delete_backup->init();

	// Initialize Download Backup handler.
	$download_backup = new BKPC_Download_Backup();
	$download_backup->init();

	// Initialize Export Backup handler.
	$export_backup = new BKPC_Export_Backup();
	$export_backup->init();

	// Initialize Restore Backup handler.
	$restore_backup = new BKPC_Restore_Backup();
	$restore_backup->init();

	// Initialize Upload Backup handler.
	$upload_backup = new BKPC_Upload_Backup();
	$upload_backup->init();
}

// Register on 'plugins_loaded' instead of 'admin_init' so AJAX handlers are available.
add_action( 'plugins_loaded', __NAMESPACE__ . '\bkpc_init_manage_backup_actions' );
