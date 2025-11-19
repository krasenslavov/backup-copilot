<?php
/**
 * Settings page rendering and fields configuration.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit;  // Exit if accessed directly.

/**
 * Display settings page.
 */
function bkpc_display_settings_page() {
	$bkpc_admin = new BKPC_Admin();

	if ( ! $bkpc_admin->check_user_access() ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'backup-copilot' ) );
	}

	// Add settings section.
	add_settings_section(
		BKPC_SETTINGS_SLUG,
		'',
		'',
		BKPC_SETTINGS_SLUG
	);

	// Compact Mode
	add_settings_field(
		'bkpc_compact_mode',
		'<label for="bkpc-compact-mode">' . esc_html__( 'Compact Mode', 'backup-copilot' ) . '</label>',
		__NAMESPACE__ . '\bkpc_display_compact_mode',
		BKPC_SETTINGS_SLUG,
		BKPC_SETTINGS_SLUG
	);
	?>
		<div class="wrap">
			<?php require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/nav.php'; ?>
			<?php require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/settings/settings-main-page.php'; ?>
		</div>
	<?php
}
