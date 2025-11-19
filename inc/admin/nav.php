<?php
/**
 * Admin page navigation tabs.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

// Free version: Single page navigation
$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
?>

<div class="bkpc-admin">
	<h1>
		<?php esc_html_e( 'Backup Copilot', 'backup-copilot' ); ?>
		<span class="bkpc-timer"></span>
	</h1>
	<p>
		<?php esc_html_e( 'Simple and powerful WordPress backup and restore plugin. Backup your database and files, restore with one click, and migrate your site effortlessly.', 'backup-copilot' ); ?>
	</p>
	<nav class="bkpc-page-nav">
		<a 
			href="<?php echo esc_url( admin_url( 'admin.php?page=' . BKPC_MANAGE_BACKUPS_SLUG ) ); ?>"
			class="bkpc-manage-backups-tab <?php echo ( BKPC_MANAGE_BACKUPS_SLUG === $current_page ) ? 'current' : ''; ?>"
		>
			<?php echo esc_html__( 'Manage Backups', 'backup-copilot' ); ?>
		</a>
		<a 
			href="https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=pro_badge" 
			class="bkcp-manage-backups-tab"
			target="_blank"
		>
			<?php echo esc_html__( 'Cloud Storage', 'backup-copilot' ); ?>
			<span class="bkpc-status-badge bkpc-status-upgrade">
				<?php echo esc_html__( 'Pro', 'wp-media-recovery' ); ?>
			</span>
		</a>
		<a 
			href="https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=pro_badge" 
			target="_blank"
			class="bkcp-backup-scheduler-tab"
		>
			<?php echo esc_html__( 'Backup Scheduler', 'backup-copilot' ); ?>
			<span class="bkpc-status-badge bkpc-status-upgrade">
				<?php echo esc_html__( 'Pro', 'wp-media-recovery' ); ?>
			</span>
		</a>
		<a 
			href="https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=pro_badge" 
			class="bkcp-license-tab"
			target="_blank"
		>
			<?php echo esc_html__( 'License', 'backup-copilot' ); ?>
			<span class="bkpc-status-badge bkpc-status-upgrade">
				<?php echo esc_html__( 'Pro', 'wp-media-recovery' ); ?>
			</span>
		</a>
		<a 
			href="<?php echo esc_url( admin_url( 'admin.php?page=' . BKPC_SETTINGS_SLUG ) ); ?>"
			class="bkcp-settings-tab <?php echo ( BKPC_SETTINGS_SLUG === $current_page ) ? 'current' : ''; ?>"
		>
			<?php echo esc_html__( 'Settings', 'backup-copilot' ); ?>
		</a>
		<a
			href="https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=pro_badge" 
			class="bkpc-help-tab"
			target="_blank"
		>
			<?php echo esc_html__( 'Help', 'backup-copilot' ); ?>
			<span class="bkpc-status-badge bkpc-status-upgrade">
				<?php echo esc_html__( 'Pro', 'wp-media-recovery' ); ?>
			</span>
		</a>
	</nav>
</div>
