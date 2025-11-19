<?php
/**
 * Backups Module Loader
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit;  // Exit if accessed directly.

define( __NAMESPACE__ . '\BKPC_MANAGE_BACKUPS_SLUG', 'bkpc_manage_backups' );

require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/manage-backups/manage-backups-menu.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/manage-backups/manage-backups-page.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/manage-backups/manage-backups-actions.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/manage-backups/manage-backups-progress.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/manage-backups/generate-uuid.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/manage-backups/secure-download.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/manage-backups/delete-all-backups.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/manage-backups/restore-preview.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/manage-backups/delete-safety-backup.php';
