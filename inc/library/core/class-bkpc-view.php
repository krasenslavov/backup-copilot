<?php
/**
 * Handles all view/template rendering for the plugin including
 * main admin page, backup lists, and configuration display.
 *
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      0.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_View' ) ) {

	class BKPC_View {
		private $utils;

		public function __construct() {
			$this->utils = new BKPC_Utils();
		}

		public function load_backup_copilot_main_page() {
			?>
				<div class="wrap">
					<div class="bkpc">
						<div class="bkpc-header">
							<header>
								<h1>
									<?php esc_html_e( 'Backup Copilot', 'backup-copilot' ); ?>
									<span class="bkpc-timer"></span>
								</h1>
								<p>
									<?php esc_html_e( 'Quickly and easily create backup points of your WordPress installation to restore, export, or transfer to another location.', 'backup-copilot' ); ?><br /><br />
									<?php esc_html_e( 'Maximum backup size:', 'backup-copilot' ); ?> <strong>500MB</strong>
								</p>
							</header>
						</div>
						<div class="bkpc-container">
							<main>
								<div class="bkpc-notice-container"></div>
								<div class="bkpc-inner-container">
									<table>
										<tr>
											<th valign="top">
												<h4>
													<i class="dashicons dashicons-admin-site-alt2"></i>
													<strong><?php esc_html_e( 'Create or export your backup', 'backup-copilot' ); ?></strong>
												</h4>
												<p>
													<em><?php esc_html_e( 'Add some notes to describe your backup. Use advanced options to create custom backups.', 'backup-copilot' ); ?></em>
												</p>
												<p>
													<small><?php esc_html_e( '* Backup Export will only generate backup for download and doesn\'t save any files on your server.', 'backup-copilot' ); ?></small>
												</p>
											</th>
											<td valign="top">
												<form id="create-export-backup" method="post" action="<?php echo esc_url( admin_url() ); ?>admin.php?page=create_backup">
													<?php wp_nonce_field( 'bkpc_create_backup', 'bkpc_create_backup_nonce' ); ?>
													<p>
														<input type="text" name="notes" placeholder="<?php esc_attr_e( 'Notes...', 'backup-copilot' ); ?>" class="regular-text" />
													</p>
													<?php if ( get_current_blog_id() === 1 ) : ?>
														<p>
															<a href="#" class="bkpc-advanced-options-toggle" title="<?php esc_attr_e( 'See advanced export options...', 'backup-copilot' ); ?>">
																<i class="dashicons dashicons-arrow-right"></i>
																<?php esc_html_e( 'Advanced Options...', 'backup-copilot' ); ?>
															</a>
														</p>
														<div class="bkpc-advanced-options" id="advanced-options">
															<label>
																<input type="checkbox" name="advanced-options" value="htaccess" />
																<?php esc_html_e( 'Save', 'backup-copilot' ); ?> <strong>.htaccess</strong> <?php esc_html_e( 'file', 'backup-copilot' ); ?>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="wpconfig" />
																<?php esc_html_e( 'Save', 'backup-copilot' ); ?> <strong>wp-config.php</strong> <?php esc_html_e( 'file', 'backup-copilot' ); ?>
															</label>
															<p>
																<small><?php esc_html_e( '.htaccess and wp-config.php files can be saved only for Create Export.', 'backup-copilot' ); ?></small>
															</p>
															<div class="find-and-replace-string">
																<input type="text" name="find-text" placeholder="<?php esc_attr_e( 'Find URL...', 'backup-copilot' ); ?>" />
																<input type="text" name="replace-with-text" placeholder="<?php esc_attr_e( 'Replace with URL...', 'backup-copilot' ); ?>" />
															</div>
															<p>
																<small><?php esc_html_e( 'Find and Replace URL is only used for Backup Export.', 'backup-copilot' ); ?></small>
															</p>
															<div class="find-and-replace-string">
																<input type="text" name="find-text" placeholder="<?php esc_attr_e( 'Find text...', 'backup-copilot' ); ?>" disabled />
																<input type="text" name="replace-with-text" placeholder="<?php esc_attr_e( 'Replace with text...', 'backup-copilot' ); ?>" disabled />
															</div>
															<p>
																<a href="#" class="bkpc-add-find-replace-row" title="<?php esc_attr_e( 'Add another find and replace text group...', 'backup-copilot' ); ?>">
																	<i class="dashicons dashicons-plus-alt"></i>
																	<?php esc_html_e( 'Add Row...', 'backup-copilot' ); ?>
																</a>
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
													<div class="button-group">
														<button type="submit" name="create-backup" class="button button-primary">
															<i class="dashicons dashicons-plus-alt"></i>
															&nbsp;<?php esc_html_e( 'Create', 'backup-copilot' ); ?>
														</button>
														<button type="submit" name="export-backup" class="button button-primary">
															<i class="dashicons dashicons-database-export"></i>
															&nbsp;<?php esc_html_e( 'Export', 'backup-copilot' ); ?>
														</button>
													</div>
												</form>
											</td>
										</tr>
										<tr>
											<th valign="top">
												<h4>
													<i class="dashicons dashicons-database-import"></i>
													<strong><?php esc_html_e( 'Import your backup', 'backup-copilot' ); ?></strong>
												</h4>
												<p>
													<em><?php esc_html_e( 'Upload your full backup and have it ready to be restored.', 'backup-copilot' ); ?></em>
												</p>
												<p>
													<small><?php esc_html_e( '* You can only restore from full backup files. (e.g. file name 1639558327.zip)', 'backup-copilot' ); ?></small>
												</p>
											</th>
											<td valign="top">
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
															&nbsp;<?php esc_html_e( 'Upload', 'backup-copilot' ); ?>
														</button>
													</p>
												</form>
											</td>
										</tr>
										<tr>
											<th valign="top">
												<h4>
													<i class="dashicons dashicons-backup"></i>
													<strong><?php esc_html_e( 'All backups', 'backup-copilot' ); ?></strong>
												</h4>
												<p>
													<em><?php esc_html_e( 'List with all available backups on your server.', 'backup-copilot' ); ?></em>
												</p>
												<p>
													<small><?php esc_html_e( '* Hold your mouse over each icon to view full description for the action.', 'backup-copilot' ); ?></small><br />
													<small><?php esc_html_e( '** Backups with delete and download full backup buttons are copies of your exports you can either delete them or they will be overwritten when/if you import the same backup.', 'backup-copilot' ); ?></small>
												</p>
											</th>
											<td valign="top">
												<div id="bkpc-all-backups-container" class="bkpc-all-backups-container">
													<table>
														<tr>
															<th><?php esc_html_e( 'Created', 'backup-copilot' ); ?></th>
															<th><?php esc_html_e( 'Size', 'backup-copilot' ); ?></th>
															<th><?php esc_html_e( 'Actions', 'backup-copilot' ); ?></th>
														</tr>
														<?php $this->display_all_backups( BKPC_PLUGIN_BACKUP_DIR_PATH ); ?>
													</table>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</main>
							<aside>
								<div class="bkpc-sidebar">
									<?php $this->display_sidebar(); ?>
								</div>
							</aside>
						</div>
						<div class="bkpc-footer">
							<?php $this->display_footer(); ?>
						</div>
					</div>
				</div>
			<?php
		}

		public function display_all_backups( $path ) {
			ob_start();

			$this->get_all_backups( $path );
			$content = ob_get_contents();

			ob_end_clean();

			echo ( ! $content ) ? '<p><em>' . esc_html__( 'No backups found!', 'backup-copilot' ) . '</em></p>' : $content;
		}

		private function get_all_backups( $path ) {
			if ( ! is_dir( $path ) ) {
				return false;
			}

			$files = scandir( $path );

			foreach ( $files as $file ) {
				if ( '.' !== $file && '..' !== $file ) {
					$absolute_path = trailingslashit( $path ) . $file;
					if ( is_dir( $absolute_path ) ) {
						// Skip special directories that are not backups
						if ( '.safety-backup' === $file || ! is_numeric( $file ) ) {
							continue;
						}

						// Multisite: Don't show backups that doesn't belong current Blog.
						if ( is_multisite() && ( get_option( $file ) !== get_current_blog_id() ) ) {
							continue;
						}

						$this->display_form_actions( $file );
						$this->get_all_backups( $absolute_path );
						?>
						<td>
							<form id="delete-backup" method="post" action="<?php echo esc_url( admin_url() ); ?>admin.php?page=delete_backup">
								<?php wp_nonce_field( 'bkpc_delete_backup_' . $file, 'bkpc_delete_backup_nonce' ); ?>
								<input type="hidden" name="uuid" value="<?php echo esc_attr( $file ); ?>" />
								<button type="submit" name="delete-backup" class="button button-primary button-red button-rounded" title="Delete Backup...">
									<i class="dashicons dashicons-trash"></i>
								</button>
							</form>
						</td>
					</tr>
						<?php
					} else {
						// Only display file links if we're inside a backup directory (not root .bkps).
						if ( rtrim( $path, '/\\' ) === rtrim( BKPC_PLUGIN_BACKUP_DIR_PATH, '/\\' ) ) {
							continue;
						}

						// Use secure download handler for all backup files
						$url = add_query_arg(
							array(
								'action' => 'secure_download',
								'file'   => urlencode( $absolute_path ),
								'nonce'  => wp_create_nonce( 'bkpc_ajax_nonce' ),
							),
							admin_url( 'admin-ajax.php' )
						);

						switch ( pathinfo( $absolute_path, PATHINFO_EXTENSION ) ) {
							case 'sql':
								$this->display_sql_link( $url );
								break;
							case 'zip':
								if ( basename( $path ) . '.zip' === $file ) {
									$this->display_wpcontent_link( $url );
								} else {
									$this->display_download_link( $url );
								}
								break;
							case 'txt':
								if ( strpos( $url, 'notes' ) !== false ) {
									$this->display_notes_link( $url );
								}
								break;
							case 'php':
								if ( 'wp-config.php' === $file ) {
									$this->display_wpconfig_link( $url );
								}
								break;
							case 'htaccess':
							case '':
								if ( '.htaccess' === $file ) {
									$this->display_htaccess_link( $url );
								}
								break;
							default:
								break;
						}
					}
				}
			}
		}

		private function display_form_actions( $uuid ) {
			?>
					<tr>
						<td>
							<abbr title="This backup is created on <?php echo esc_textarea( wp_date( 'Y-m-d', $uuid ) ); ?> at <?php echo esc_textarea( wp_date( 'H:i:s', $uuid ) ); ?>">
								<?php echo esc_textarea( $this->utils->get_time_elapsed( $uuid ) ); ?>
							</abbr>
						</td>
						<td>
							<strong>
								<abbr title="All files localted in the backup directory.">
									<?php echo esc_textarea( $this->utils->show_dir_size( BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid ) ); ?>
								</abbr>
							</strong>
						</td>
						<!-- Show restore and download buttons if either .sql or .zip files exist -->
						<?php
						$sql_file          = trailingslashit( BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid ) . $uuid . '.sql';
						$zip_file          = trailingslashit( BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid ) . $uuid . '.zip';
						$download_zip_file = trailingslashit( BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid ) . 'download-' . $uuid . '.zip';
						$has_backup_files  = file_exists( $sql_file ) || file_exists( $zip_file ) || file_exists( $download_zip_file );
						?>
						<?php if ( $has_backup_files ) : ?>
							<td>
								<form id="restore-backup" method="post" action="<?php echo esc_url( admin_url() ); ?>admin.php?page=restore_backup">
									<?php wp_nonce_field( 'bkpc_restore_backup_' . $uuid, 'bkpc_restore_backup_nonce' ); ?>
									<input type="hidden" name="uuid" value="<?php echo esc_attr( $uuid ); ?>" />
									<button type="submit" name="restore-backup" class="button button-primary button-rounded" title="Restore Backup...">
										<i class="dashicons dashicons-update-alt"></i>
									</button>
								</form>
							</td>
							<td>
								<form id="download-backup" method="post" action="<?php echo esc_url( admin_url() ); ?>admin.php?page=download_backup">
									<?php wp_nonce_field( 'bkpc_download_backup_' . $uuid, 'bkpc_download_backup_nonce' ); ?>
									<input type="hidden" name="uuid" value="<?php echo esc_attr( $uuid ); ?>" />
									<button type="submit" name="download-backup" class="button button-primary button-rounded" title="Generate Full Download...">
										<i class="dashicons dashicons-download"></i>
									</button>
								</form>
							</td>
						<?php endif; ?>
						<?php if ( is_multisite() ) : ?>
							<?php if ( ! file_exists( trailingslashit( BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid ) . $uuid . '.sql' ) || ! file_exists( trailingslashit( BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid ) . $uuid . '.zip' ) ) : ?>
								<td>
									<form id="restore-backup" method="post" action="<?php echo esc_url( admin_url() ); ?>admin.php?page=restore_backup">
										<?php wp_nonce_field( 'bkpc_restore_backup_' . $uuid, 'bkpc_restore_backup_nonce' ); ?>
										<input type="hidden" name="uuid" value="<?php echo esc_attr( $uuid ); ?>" />
										<input type="hidden" name="wp2wpmu" value="1" />
										<button type="submit" name="restore-backup" class="button button-primary button-rounded" title="Restore Multiste Backup... (WP -> WPMu)" disabled>
											<i class="dashicons dashicons-admin-multisite"></i>
										</button>
									</form>
								</td>
							<?php endif; ?>
						<?php endif; ?>
			<?php
		}

		private function display_sql_link( $url ) {
			?>
				<td>
					<a href="<?php echo esc_url( $url ); ?>" class="button button-primary button-black button-rounded" target="_blank" title="Download Database...">
						<i class="dashicons dashicons-database"></i>
					</a>
				</td>
			<?php
		}

		private function display_wpcontent_link( $url ) {
			?>
				<td>
					<a href="<?php echo esc_url( $url ); ?>" class="button button-primary button-black button-rounded" target="_blank" title="Download WP Content...">
						<i class="dashicons dashicons-media-archive"></i>
					</a>
				</td>
			<?php
		}

		private function display_download_link( $url ) {
			?>
				<td>
					<a href="<?php echo esc_url( $url ); ?>" class="button button-primary button-black button-rounded" target="_blank" title="Download Full Backup...">
						<i class="dashicons dashicons-archive"></i>
					</a>
				</td>
			<?php
		}

		private function display_notes_link( $url ) {
			?>
				<td>
					<a href="<?php echo esc_url( $url ); ?>" class="button button-primary button-black button-rounded" target="_blank" title="Open Notes...">
						<i class="dashicons dashicons-editor-textcolor"></i>
					</a>
				</td>
			<?php
		}

		private function display_wpconfig_link( $url ) {
			?>
				<td>
					<a href="<?php echo esc_url( $url ); ?>" class="button button-primary button-black button-rounded" target="_blank" title="Download wp-config.php...">
						<i class="dashicons dashicons-admin-settings"></i>
					</a>
				</td>
			<?php
		}

		private function display_htaccess_link( $url ) {
			?>
				<td>
					<a href="<?php echo esc_url( $url ); ?>" class="button button-primary button-black button-rounded" target="_blank" title="Download .htaccess...">
						<i class="dashicons dashicons-admin-generic"></i>
					</a>
				</td>
			<?php
		}

		public function display_sidebar() {
			?>
				<h3>
					<i class="dashicons dashicons-sos"></i>
					<?php esc_html_e( 'Support', 'backup-copilot' ); ?>
				</h3>
				<ol>
					<li>
						<?php
						printf(
							/* translators: %1$s: Site Health Info link, %2$s: button text */
							esc_html__( 'Go to your %1$s page and click the %2$s button.', 'backup-copilot' ),
							'<a href="' . esc_url( admin_url( 'site-health.php?tab=debug' ) ) . '">' . esc_html__( 'Site Health Info', 'backup-copilot' ) . '</a>',
							'<strong>' . esc_html__( 'Copy Site Info to Clipboard', 'backup-copilot' ) . '</strong>'
						);
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %1$s: Contact Us link */
							esc_html__( 'Go to the Copilot Plus %1$s page and send us a message with the Site Health Info contents included.', 'backup-copilot' ),
							'<a href="' . esc_url( BKPC_PLUGIN_SUPPORT_URL ) . '" target="_blank">' . esc_html__( 'Contact Us', 'backup-copilot' ) . '</a>'
						);
						?>
					</li>
					<li>
						<?php
						printf(
							/* translators: %1$s: Support Tab link */
							esc_html__( 'Or search and create a support ticket at the plugin %1$s page directly at WP.org.', 'backup-copilot' ),
							'<a href="' . esc_url( BKPC_PLUGIN_WPORG_SUPPORT_URL ) . '" target="_blank">' . esc_html__( 'Support Tab', 'backup-copilot' ) . '</a>'
						);
						?>
					</li>
				</ol>
				<p>
					<strong><?php esc_html_e( "DON'T POST YOUR SITE HEALTH INFO CONTENTS ONTO THE PUBLIC PLUGIN FORUMS!", 'backup-copilot' ); ?></strong>
				</p>
				<h3>
					<i class="dashicons dashicons-admin-settings"></i>
					<?php esc_html_e( 'Configuration', 'backup-copilot' ); ?>
				</h3>
				<div class="sysinfo">
					<?php
					echo ( class_exists( 'ZipArchive' ) ) ? '<strong class="text-color-green">[OK]</strong>' : '<strong class="text-color-red">[Failed]</strong>';
					echo ' ' . esc_html__( 'Create archive files.', 'backup-copilot' ) . '<br />';

					try {
						// Check if database constants are defined.
						if ( ! defined( 'DB_HOST' ) || ! defined( 'DB_NAME' ) || ! defined( 'DB_USER' ) || ! defined( 'DB_PASSWORD' ) ) {
							echo '<strong class="text-color-red">[Failed]</strong> - ' . esc_html__( 'Database constants not defined', 'backup-copilot' );
						} else {
							// Use WordPress database connection instead of PDO.
							global $wpdb;
							if ( $wpdb->check_connection( false ) ) {
								echo '<strong class="text-color-green">[OK]</strong>';
							} else {
								echo '<strong class="text-color-red">[Failed]</strong> - ' . esc_html__( 'Cannot connect to database', 'backup-copilot' );
							}
						}
					} catch ( \Exception $err ) {
						echo '<strong class="text-color-red">[Failed]</strong> - ' . esc_html( $err->getMessage() );
					}

					echo ' ' . esc_html__( 'Connect to database.', 'backup-copilot' ) . '<br />';

					echo ( is_writable( ABSPATH ) ) ? '<strong class="text-color-green">[OK]</strong>' : '<strong class="text-color-red">[Failed]</strong>';
					echo ' ' . esc_html__( 'Base path permissions.', 'backup-copilot' ) . '<br />';

					echo ( is_writable( BKPC_PLUGIN_WPCONTENT_DIR_PATH ) ) ? '<strong class="text-color-green">[OK]</strong>' : '<strong class="text-color-red">[Failed]</strong>';
					echo ' ' . esc_html__( 'Content path permissions.', 'backup-copilot' ) . '<br />';

					echo '&mdash;&mdash;&mdash;<br />';

					echo '<strong class="text-color-green">' . esc_html( ini_get( 'upload_max_filesize' ) ) . '</strong> ' . esc_html__( 'Maximum upload file size', 'backup-copilot' ) . '<br />';
					echo '<strong class="text-color-green">' . esc_html( ini_get( 'post_max_size' ) ) . '</strong> ' . esc_html__( 'Maximum post file size.', 'backup-copilot' ) . '<br />';
					echo '<strong class="text-color-green">' . esc_html( ini_get( 'memory_limit' ) ) . '</strong> ' . esc_html__( 'Memory limit.', 'backup-copilot' ) . '<br />';
					echo '<strong class="text-color-green">' . esc_html( ini_get( 'max_execution_time' ) ) . '</strong> ' . esc_html__( 'Maximum execution time.', 'backup-copilot' ) . '<br />';
					echo '<strong class="text-color-green">' . esc_html( ini_get( 'max_input_time' ) ) . '</strong> ' . esc_html__( 'Maximum input time.', 'backup-copilot' );
					?>
				</div>
			<?php
			require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/pro-table.php';
		}

		public function display_footer() {
			?>
				<p class="bg-color-orange" style="padding: 10px 15px; border-radius: 4px; margin: 0 10px 15px 10px;">
					<small>
						&bullet; <strong><?php esc_html_e( 'Maximum backup size: 500MB', 'backup-copilot' ); ?></strong><br />
						&bullet; <?php esc_html_e( 'Server timeout may occur on hosting providers with restrictions (e.g. WPEngine) and may cause corrupted backup files for sites with data over 1GB.', 'backup-copilot' ); ?>
					</small>
				</p>
			<?php
		}
	}
}
