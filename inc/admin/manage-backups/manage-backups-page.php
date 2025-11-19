<?php
/**
 * Backups page rendering.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit;  // Exit if accessed directly.

/**
 * Display backups page.
 */
function bkpc_display_manage_backups_page() {
	$bkpc_admin = new BKPC_Admin();

	if ( ! $bkpc_admin->check_user_access() ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'backup-copilot' ) );
	}
	?>
		<div class="wrap">
			<?php require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/nav.php'; ?>
			<?php require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/manage-backups/manage-backups-main-page.php'; ?>
		</div>
	<?php
}
