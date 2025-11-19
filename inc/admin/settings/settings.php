<?php
/**
 * Settings Module Loader
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

define( __NAMESPACE__ . '\BKPC_SETTINGS_SLUG', 'bkpc_settings' );

require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/settings/settings-menu.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/settings/settings-page.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/settings/settings-actions.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/settings/settings-register.php';

// Individual setting files
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/settings/compact-mode.php';
