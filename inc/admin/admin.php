<?php
/**
 * Admin Module Loader
 *
 * @package    BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit;  // Exit if accessed directly.

/**
 * Load admin modules
 */
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/requirements.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/manage-backups/manage-backups.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/settings/settings.php';
