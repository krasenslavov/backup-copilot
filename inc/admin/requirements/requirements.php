<?php
/**
 * Requirements module loader for plugin initialization checks.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/check-requirements.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/enqueue-assets.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/action-links.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/notice-onboarding.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/notice-rating.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/notice-dismiss.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/dashboard-widget.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/pro-plugin.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/toggle-plugin.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/user-profile.php';
require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/requirements/wp-pointers.php';
