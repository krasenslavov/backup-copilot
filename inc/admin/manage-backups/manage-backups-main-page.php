<?php
/**
 * Backups page main content template.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit;  // Exit if accessed directly.

$bkpc_admin = new BKPC_Admin();
$bkpc_view  = new BKPC_View();

$has_user_cap = $bkpc_admin->check_user_cap();

?>
<div class="bkpc-admin">
	<div class="bkcp-loading-bar"></div>
	<div id="bkpc-notice-container" class="bkpc-notice-container"></div>
	<div class="bkpc-manage-backups-wrapper">
		<div class="bkpc-manage-backups-main">
			<!-- Create/Export Backup -->
			<h3 class="bkpc-drawer-toggle bkpc-drawer-active">
				<i class="dashicons dashicons-admin-site-alt2"></i>
				<strong><?php esc_html_e( 'Create or export your backup', 'backup-copilot' ); ?></strong>
				&mdash; <em><?php esc_html_e( 'Add some notes to describe your backup. Use advanced options to create custom backups.', 'backup-copilot' ); ?></em>
				<i class="dashicons dashicons-arrow-down-alt2 bkpc-drawer-icon"></i>
			</h3>
			<div class="bkpc-drawer-content bkpc-drawer-open">
				<form id="create-export-backup" method="post" action="<?php echo esc_url( admin_url() ); ?>admin.php?page=create_backup">
					<?php wp_nonce_field( 'bkpc', 'bkpc_nonce' ); ?>
					<p>
						<textarea name="notes" placeholder="<?php esc_attr_e( 'Notes...', 'backup-copilot' ); ?>" class="regular-text" /></textarea>
					</p>
					<?php if ( get_current_blog_id() === 1 ) : ?>
						<div>
							<a href="#" class="bkpc-advanced-options-toggle" title="<?php esc_attr_e( 'See advanced export options...', 'backup-copilot' ); ?>">
								<i class="dashicons dashicons-arrow-down"></i>
								<?php esc_html_e( 'Advanced Options...', 'backup-copilot' ); ?>
							</a>
						</div>
						<div id="bkpc-advanced-options" class="bkpc-advanced-options">
							<label>
								<input type="checkbox" name="advanced-options" value="htaccess" />
								<?php esc_html_e( 'Save', 'backup-copilot' ); ?> <strong>.htaccess</strong> <?php esc_html_e( 'file', 'backup-copilot' ); ?>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="wpconfig" />
								<?php esc_html_e( 'Save', 'backup-copilot' ); ?> <strong>wp-config.php</strong> <?php esc_html_e( 'file', 'backup-copilot' ); ?>
							</label>
							<p>
								<small class="bkpc-text-warning">
									<strong><?php esc_html_e( 'Security Warning:', 'backup-copilot' ); ?></strong>
									<?php esc_html_e( 'These files contain sensitive configuration data including database credentials. Only save them if absolutely necessary and ensure your backup directory is properly secured.', 'backup-copilot' ); ?>
								</small>
							</p>
							<p>
								<small><?php esc_html_e( '.htaccess and wp-config.php files can be saved only for Create Backup (not Export).', 'backup-copilot' ); ?></small>
							</p>
							<div class="find-and-replace-string">
								<input type="text" name="find-text" placeholder="<?php esc_attr_e( 'Find URL...', 'backup-copilot' ); ?>" />
								<input type="text" name="replace-with-text" placeholder="<?php esc_attr_e( 'Replace with URL...', 'backup-copilot' ); ?>" />
							</div>
							<p>
								<a href="#" class="bkpc-add-find-replace-row" title="<?php esc_attr_e( 'Add another find and replace URL row...', 'backup-copilot' ); ?>">
									<i class="dashicons dashicons-plus-alt"></i>
									<?php esc_html_e( 'Add Row...', 'backup-copilot' ); ?>
								</a>
							</p>
							<p>
								<small>&bullet; <?php esc_html_e( 'Find and Replace URL is only used for Backup Export.', 'backup-copilot' ); ?></small>
							</p>
							<label>
								<input type="checkbox" name="advanced-options" value="spam-comments" checked readonly onclick="return false;" />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( 'spam comments', 'backup-copilot' ); ?></strong>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="post-revisions" checked readonly onclick="return false;" />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( 'post revisions', 'backup-copilot' ); ?></strong>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="uploads" checked />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( 'media library', 'backup-copilot' ); ?></strong>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="themes" checked />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( 'themes', 'backup-copilot' ); ?></strong>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="inactive-themes" checked  readonly onclick="return false;" />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( 'inactive themes', 'backup-copilot' ); ?></strong>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="mu-plugins" checked />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( 'must-use plugins', 'backup-copilot' ); ?></strong>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="plugins" checked />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( 'plugins', 'backup-copilot' ); ?></strong>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="inactive-plugins" checked readonly onclick="return false;" />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( 'inactive plugins', 'backup-copilot' ); ?></strong>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="cache" />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( 'cache', 'backup-copilot' ); ?></strong>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="backups" />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( '3rd-party backups', 'backup-copilot' ); ?></strong>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="database" checked />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( 'database', 'backup-copilot' ); ?></strong>
							</label>
							<label>
								<input type="checkbox" name="advanced-options" value="content" checked />
								<?php esc_html_e( 'Export', 'backup-copilot' ); ?> <strong><?php esc_html_e( 'wp-content', 'backup-copilot' ); ?></strong>
							</label>
						</div>
					<?php endif; ?>
					<p class="button-group">
						<button
							type="submit" 
							name="create-backup" 
							class="button button-primary"
						>
							<i class="dashicons dashicons-plus-alt"></i>
							<?php esc_html_e( 'Create', 'backup-copilot' ); ?>
						</button>
						<button
							type="submit" 
							name="export-backup" 
							class="button button-primary"
						>
							<i class="dashicons dashicons-database-export"></i>
							<?php esc_html_e( 'Export', 'backup-copilot' ); ?>
						</button>
					</p>
				</form>
				<p>
					<small>&bullet; <?php esc_html_e( 'Backup Export will only generate backup for download and doesn\'t save any files on your server.', 'backup-copilot' ); ?></small>
				</p>
			</div>
			<!-- List Backups -->
			<h3 class="bkpc-drawer-toggle bkpc-drawer-active">
				<i class="dashicons dashicons-backup"></i>
				<strong><?php esc_html_e( 'All backups', 'backup-copilot' ); ?></strong>
				&mdash; <em><?php esc_html_e( 'List with all available backups on your server.', 'backup-copilot' ); ?></em>
				<i class="dashicons dashicons-arrow-down-alt2 bkpc-drawer-icon"></i>
			</h3>
			<div class="bkpc-drawer-content bkpc-drawer-open">
				<div id="bkpc-manage-backups-container" class="bkpc-manage-backups-container">
					<table cellspacing="0" cellpadding="0">
						<tr>
							<th><?php esc_html_e( 'Created On', 'backup-copilot' ); ?></th>
							<th><?php esc_html_e( 'Size', 'backup-copilot' ); ?></th>
							<th colspan="12"><?php esc_html_e( 'Actions', 'backup-copilot' ); ?></th>
						</tr>
						<?php $bkpc_view->display_all_backups( BKPC_PLUGIN_BACKUP_DIR_PATH ); ?>
					</table>
				</div>
				<p>
					<small>&bullet; <?php esc_html_e( 'Hold your mouse over each icon to view full description for the action.', 'backup-copilot' ); ?></small><br />
					<small>&bullet; <?php esc_html_e( 'Backups with delete and download full backup buttons are copies of your exports you can either delete them or they will be overwritten when/if you import the same backup.', 'backup-copilot' ); ?></small>
				</p>
			</div>
			<!-- Import Backup -->
			<h3 class="bkpc-drawer-toggle bkpc-drawer-active">
				<i class="dashicons dashicons-database-import"></i>
				<strong><?php esc_html_e( 'Import your backup', 'backup-copilot' ); ?></strong>
				&mdash; <em><?php esc_html_e( 'Upload your full backup and have it ready to be restored.', 'backup-copilot' ); ?></em>
				<i class="dashicons dashicons-arrow-down-alt2 bkpc-drawer-icon"></i>
			</h3>
			<div class="bkpc-drawer-content bkpc-drawer-open">
				<form id="import-backup" method="post" action="<?php echo esc_url( admin_url() ); ?>admin.php?page=upload_backup">
					<?php wp_nonce_field( 'bkpc_upload_backup', 'bkpc_upload_backup_nonce' ); ?>
					<p class="bkpc-custom-file-upload">
						<label>
							<input type="file" name="backup-file" class="regular-text" />
							<span><?php esc_html_e( 'Choose Backup File...', 'backup-copilot' ); ?></span>
						</label>
					</p>
					<p>
						<button type="submit" name="upload-backup" class="button button-primary">
							<i class="dashicons dashicons-upload"></i>
							<?php esc_html_e( 'Upload', 'backup-copilot' ); ?>
						</button>
					</p>
				</form>
				<p>
					<small>
						&bullet; 
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: %1$s: full backup example, %2$s: wp-content backup example */
								__( 'Upload formats: <strong>%1$s</strong> (full backup - extracts all files) or <strong>%2$s</strong> (wp-content only - no extraction).', 'backup-copilot' ),
								'download-1763468811.zip',
								'1763468811.zip'
							)
						);
						?>
					</small>
				</p>
			</div>
			<!-- Danger Zone -->
			<h3 class="bkpc-drawer-toggle bkpc-drawer-active">
				<i class="dashicons dashicons-warning"></i>
				<strong><?php esc_html_e( 'Danger Zone', 'backup-copilot' ); ?></strong>
				&mdash; <em><?php esc_html_e( 'Permanently delete all backup files and empty the .bkps directory. This action cannot be undone.', 'backup-copilot' ); ?></em>
				<i class="dashicons dashicons-arrow-down-alt2 bkpc-drawer-icon"></i>
			</h3>
			<div class="bkpc-drawer-content bkpc-drawer-open">
				<div class="bkpc-danger-zone">
					<form id="delete-all-backups" method="post">
						<?php wp_nonce_field( 'bkpc_delete_all_backups', 'bkpc_delete_all_backups_nonce' ); ?>
						<button type="submit" name="delete-all-backups" class="button button-red">
							<i class="dashicons dashicons-trash"></i>
							<?php esc_html_e( 'Delete All Backups', 'backup-copilot' ); ?>
						</button>
					</form>
					<?php if ( is_multisite() && 1 === get_current_blog_id() ) : ?>
						<p>
							<small>&bullet; <?php esc_html_e( 'Delete all button will only delete backups created on the main site.', 'backup-copilot' ); ?></small>
						</p>
					<?php endif; ?>
				</div>
			</div>
			<!-- Actions Legend -->
			<h3 class="bkpc-drawer-toggle bkpc-drawer-active">
				<i class="dashicons dashicons-info"></i>
				<strong><?php esc_html_e( 'Actions Legend', 'backup-copilot' ); ?></strong>
				&mdash; <em><?php esc_html_e( 'Explanation of all available action buttons.', 'backup-copilot' ); ?></em>
				<i class="dashicons dashicons-arrow-down-alt2 bkpc-drawer-icon"></i>
			</h3>
			<div class="bkpc-drawer-content bkpc-drawer-open">
				<div class="bkpc-actions-legend">
					<div class="bkpc-legend-item">
						<span class="bkpc-legend-icon">
							<button class="button button-primary button-rounded" disabled>
								<i class="dashicons dashicons-update-alt"></i>
							</button>
						</span>
						<span class="bkpc-legend-text">
							<strong><?php esc_html_e( 'Restore Backup', 'backup-copilot' ); ?></strong>
							&mdash; <?php esc_html_e( 'Restore your site from this backup point (creates safety backup first).', 'backup-copilot' ); ?>
						</span>
					</div>
					<div class="bkpc-legend-item">
						<span class="bkpc-legend-icon">
							<button class="button button-primary button-rounded" disabled>
								<i class="dashicons dashicons-download"></i>
							</button>
						</span>
						<span class="bkpc-legend-text">
							<strong><?php esc_html_e( 'Generate Full Download', 'backup-copilot' ); ?></strong>
							&mdash; <?php esc_html_e( 'Create a downloadable ZIP archive of the entire backup.', 'backup-copilot' ); ?>
						</span>
					</div>
					<div class="bkpc-legend-item">
						<span class="bkpc-legend-icon">
							<button class="button button-primary button-black button-rounded" disabled>
								<i class="dashicons dashicons-download"></i>
							</button>
						</span>
						<span class="bkpc-legend-text">
							<strong><?php esc_html_e( 'Download Full Backup', 'backup-copilot' ); ?></strong>
							&mdash; <?php esc_html_e( 'Download the generated full backup archive (available after generation).', 'backup-copilot' ); ?>
						</span>
					</div>
					<div class="bkpc-legend-item">
						<span class="bkpc-legend-icon">
							<button class="button button-primary button-black button-rounded" disabled>
								<i class="dashicons dashicons-database"></i>
							</button>
						</span>
						<span class="bkpc-legend-text">
							<strong><?php esc_html_e( 'Download Database', 'backup-copilot' ); ?></strong>
							&mdash; <?php esc_html_e( 'Download the SQL database file.', 'backup-copilot' ); ?>
						</span>
					</div>
					<div class="bkpc-legend-item">
						<span class="bkpc-legend-icon">
							<button class="button button-primary button-black button-rounded" disabled>
								<i class="dashicons dashicons-media-archive"></i>
							</button>
						</span>
						<span class="bkpc-legend-text">
							<strong><?php esc_html_e( 'Download WP Content', 'backup-copilot' ); ?></strong>
							&mdash; <?php esc_html_e( 'Download the wp-content directory archive.', 'backup-copilot' ); ?>
						</span>
					</div>
					<div class="bkpc-legend-item">
						<span class="bkpc-legend-icon">
							<button class="button button-primary button-black button-rounded" disabled>
								<i class="dashicons dashicons-editor-textcolor"></i>
							</button>
						</span>
						<span class="bkpc-legend-text">
							<strong><?php esc_html_e( 'Download Notes', 'backup-copilot' ); ?></strong>
							&mdash; <?php esc_html_e( 'View or download backup notes.', 'backup-copilot' ); ?>
						</span>
					</div>
					<div class="bkpc-legend-item">
						<span class="bkpc-legend-icon">
							<button class="button button-primary button-black button-rounded" disabled>
								<i class="dashicons dashicons-admin-settings"></i>
							</button>
						</span>
						<span class="bkpc-legend-text">
							<strong><?php esc_html_e( 'Download wp-config.php', 'backup-copilot' ); ?></strong>
							&mdash; <?php esc_html_e( 'Download WordPress configuration file.', 'backup-copilot' ); ?>
						</span>
					</div>
					<div class="bkpc-legend-item">
						<span class="bkpc-legend-icon">
							<button class="button button-primary button-black button-rounded" disabled>
								<i class="dashicons dashicons-admin-generic"></i>
							</button>
						</span>
						<span class="bkpc-legend-text">
							<strong><?php esc_html_e( 'Download .htaccess', 'backup-copilot' ); ?></strong>
							&mdash; <?php esc_html_e( 'Download Apache configuration file.', 'backup-copilot' ); ?>
						</span>
					</div>
					<div class="bkpc-legend-item">
						<span class="bkpc-legend-icon">
							<button class="button button-primary button-red button-rounded" disabled>
								<i class="dashicons dashicons-trash"></i>
							</button>
						</span>
						<span class="bkpc-legend-text">
							<strong><?php esc_html_e( 'Delete Backup', 'backup-copilot' ); ?></strong>
							&mdash; <?php esc_html_e( 'Permanently delete this backup.', 'backup-copilot' ); ?>
						</span>
					</div>
				</div>
			</div>
			<br />
			<?php $bkpc_view->display_footer(); ?>
			<hr />
			<div class="mlr-support-credits">
				<p>
					<?php
					printf(
						wp_kses(
							/* translators: %1$s is replaced with "Support Forum" */
							__( 'If something isn\'t clear, please open a ticket on the official plugin %1$s. We aim to address all tickets within a few working days.', 'backup-copilot' ),
							json_decode( BKPC_PLUGIN_ALLOWED_HTML_ARR, true )
						),
						'<a href="' . esc_url( BKPC_PLUGIN_WPORG_SUPPORT_URL ) . '" target="_blank">' . esc_html__( 'Support Forum', 'backup-copilot' ) . '</a>'
					);
					?>
				</p>
				<p>
					<strong><?php echo esc_html__( 'Please rate us', 'backup-copilot' ); ?></strong>
					<a href="<?php echo esc_url( BKPC_PLUGIN_WPORG_RATE_URL ); ?>" target="_blank">
						<img src="<?php echo esc_url( BKPC_PLUGIN_DIR_URL ); ?>assets/dist/img/rate.png" alt="Rate us @ WordPress.org" />
					</a>
				</p>
				<p>
					<strong><?php echo esc_html__( 'Having issues?', 'backup-copilot' ); ?></strong> 
					<a href="<?php echo esc_url( BKPC_PLUGIN_WPORG_SUPPORT_URL ); ?>" target="_blank">
						<?php echo esc_html__( 'Create a Support Ticket', 'backup-copilot' ); ?>
					</a>
				</p>
				<p>
					<strong><?php echo esc_html__( 'Developed by', 'backup-copilot' ); ?></strong>
					<a href="https://krasenslavov.com/" target="_blank">
						<?php echo esc_html__( 'Krasen Slavov @ Developry', 'backup-copilot' ); ?>
					</a>
				</p>
			</div>
			<hr />
			<p>
				<small>
					<?php
					printf(
						wp_kses(
							/* translators: %1$s is replaced with "help and support me on Patreon" */
							__( '* For the price of a cup of coffee per month, you can %1$s for the development and maintenance of all my free WordPress plugins. Every contribution helps and is deeply appreciated!', 'backup-copilot' ),
							json_decode( BKPC_PLUGIN_ALLOWED_HTML_ARR, true )
						),
						'<a href="https://patreon.com/krasenslavov" target="_blank">' . esc_html__( 'help and support me on Patreon', 'backup-copilot' ) . '</a>'
					);
					?>
				</small>
			</p>
		</div>
		<div class="bkpc-manage-backups-sidebar">
			<?php $bkpc_view->display_sidebar(); ?>
		</div>
	</div>
</div>
