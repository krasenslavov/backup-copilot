<?php
/**
 * Display onboarding notice with quick start guide.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Display onboarding notice to help users get started.
 */
function bkpc_display_onboarding_notice() {
	$bkpc_admin = new BKPC_Admin();

	// Check if notice was dismissed.
	if ( get_option( 'bkpc_onboarding_notice_dismissed' ) ) {
		return;
	}

	// Only show on dashboard and plugin pages.
	$current_screen = get_current_screen();

	if ( ! $current_screen || ( 'dashboard' !== $current_screen->base
		&& false === strpos( $current_screen->base, 'bkpc_' ) ) ) {
		return;
	}
	?>
		<div class="bkpc-admin">
			<div class="bkpc-onboarding-notice">
				<div class="bkpc-onboarding-icon">
					<h2>
						<span class="dashicons dashicons-backup"></span>
						<?php esc_html_e( 'Welcome to Backup Copilot!', 'backup-copilot' ); ?>
					</h2>
				</div>
				<div class="bkpc-onboarding-text">
					<p>
						<?php esc_html_e( 'Thank you for installing Backup Copilot. Get started with these quick actions:', 'backup-copilot' ); ?>
					</p>
					<ul class="bkpc-onboarding-list">
						<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Create your first database backup', 'backup-copilot' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Export backups for safe storage', 'backup-copilot' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Restore backups with one click', 'backup-copilot' ); ?></li>
						<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Upload backups from other sites', 'backup-copilot' ); ?></li>
					</ul>
					<div class="bkpc-onboarding-actions button-group">
						<a
							href="<?php echo esc_url( admin_url( 'admin.php?page=' . BKPC_MANAGE_BACKUPS_SLUG ) ); ?>" 
							class="button button-primary"
						>
							<span class="dashicons dashicons-admin-tools"></span>
							<?php esc_html_e( 'Create First Backup', 'backup-copilot' ); ?>
						</a>
						<a 
							href="<?php echo esc_url( BKPC_PLUGIN_DOCS_URL ); ?>" 
							class="button" 
							target="_blank"
						>
							<span class="dashicons dashicons-book"></span>
							<?php esc_html_e( 'View Documentation', 'backup-copilot' ); ?>
						</a>
						<a 
							href="<?php echo esc_url( admin_url( $bkpc_admin->admin_page . BKPC_MANAGE_BACKUPS_SLUG . '&_wpnonce=' . wp_create_nonce( 'bkpc_onboarding_notice_nonce' ) . '&action=bkpc_onboarding_notice_dismiss' ) ); ?>" 
							class="button"
						>
							<span class="dashicons dashicons-dismiss"></span>
							<?php esc_html_e( 'Dismiss', 'backup-copilot' ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	<?php
}

add_action( 'admin_notices', __NAMESPACE__ . '\bkpc_display_onboarding_notice' );
