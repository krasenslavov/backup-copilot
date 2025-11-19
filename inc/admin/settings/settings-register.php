<?php
/**
 * Settings registration and sanitization handlers.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Register all settings.
 */
function bkpc_register_setting_fields() {
	register_setting( BKPC_SETTINGS_SLUG, 'bkpc_compact_mode', __NAMESPACE__ . '\bkpc_sanitize_compact_mode' );
}

add_action( 'admin_init', __NAMESPACE__ . '\bkpc_register_setting_fields' );
