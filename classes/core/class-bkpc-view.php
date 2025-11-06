<?php

namespace BKPC\Backup_Copilot;

! defined (ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_View' ) ) {

	class BKPC_View extends Backup_Copilot {
		public function __construct() {
			$this->utils = new BKPC_Utils;

			parent::__construct();
		}

		public function load_backup_copilot_main_page()
		{
			?>
				<div class="wrap">
					<div class="bkpc">
						<div class="bkpc-header">
							<header>
								<h1>
									Backup Copilot
									<span class="bkpc-timer"></span>
								</h1>
								<p>
									Quickly and easily create backup points of your WordPress installation to restore, export, or transfer to another location.<br /><br />
									Maxiumum backup size: <strong>500MB</strong>
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
													<strong>Create or export your backup</strong>
												</h4>
												<p>
													<em>Add some notes to describe your backup. Use advanced options to create custom backups.</em>
												</p>
												<p>
													<small>* Backup Export will only generate backup for download and doesn't save any files on your server.</small>
												</p>
											</th>
											<td valign="top">
												<form id="create-export-backup" method="post" action="<?php echo esc_url(admin_url()); ?>admin.php?page=create_backup">
													<p>
														<input type="text" name="notes" placeholder="Notes..." class="regular-text" />
													</p>
													<?php if ( get_current_blog_id() === 1 ) : ?>
														<p>
															<a href="#" class="bkpc-advanced-options-toggle" title="See advanced export options...">
																<i class="dashicons dashicons-arrow-right"></i>
																Advanced Options...
															</a>
														</p>
														<div class="bkpc-advanced-options" id="advanced-options">
															<label>
																<input type="checkbox" name="advanced-options" value="htaccess" />
																Save <strong>.htaccess</strong> file
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="wpconfig" />
																Save <strong>wp-config.php</strong> file
															</label>
															<p>
																<small><strong>.htaccess and wp-config.php</strong> files can be saved only for <strong>Create Export</strong>.</small>
															</p>
															<div class="find-and-replace-string">
																<input type="text" name="find-text" placeholder="Find URL..." />
																<input type="text" name="replace-with-text" placeholder="Replace with URL..." />
															</div>
															<p>
																<small><strong>Find and Replace URL</strong> is only used for <strong>Backup Export</strong>.</small>
															</p>
															<div class="find-and-replace-string">
																<input type="text" name="find-text" placeholder="Find text..." disabled />
																<input type="text" name="replace-with-text" placeholder="Replace with text..." disabled />
															</div>
															<p>
																<a href="#" class="bkpc-add-find-replace-row" title="Add another find and replace text group..">
																	<i class="dashicons dashicons-plus-alt"></i>
																	Add Row...
																</a>
															</p>
															<label>
																<input type="checkbox" name="advanced-options" value="spam-comments" checked readonly onclick="return false;" />
																Export <strong>spam comments</strong>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="post-revisions" checked readonly onclick="return false;" />
																Export <strong>post revisions</strong>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="uploads" checked />
																Export <strong>media library</strong>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="themes" checked />
																Export <strong>themes</strong>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="inactive-themes" checked  readonly onclick="return false;" />
																Export <strong>inactive themes</strong>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="mu-plugins" checked />
																Export <strong>must-use plugins</strong>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="plugins" checked />
																Export <strong>plugins</strong>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="inactive-plugins" checked readonly onclick="return false;" />
																Export <strong>inactive plugins</strong>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="cache" />
																Export <strong>cache</strong>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="backups" />
																Export <strong>3rd-party backups</strong>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="database" checked />
																Export <strong>database</strong>
															</label>
															<label>
																<input type="checkbox" name="advanced-options" value="content" checked />
																Export <strong>wp-content</strong>
															</label>
														</div>
													<?php endif; ?>
													<div class="button-group">
														<button type="submit" name="create-backup" class="button button-primary">
															<i class="dashicons dashicons-plus-alt"></i>
															&nbsp;Create
														</button>
														<button type="submit" name="export-backup" class="button button-primary">
															<i class="dashicons dashicons-database-export"></i>
															&nbsp;Export
														</button>
													</div>
												</form>
											</td>
										</tr>
										<tr>
											<th valign="top">
												<h4>
													<i class="dashicons dashicons-database-import"></i>
													<strong>Import your backup</strong>
												</h4>
												<p>
													<em>Upload your full backup and have it ready to be restored.</em>
												</p>
												<p>
													<small>* You can only restore from full backup files. (e.g. file name 1639558327.zip)</small>
												</p>
											</th>
											<td valign="top">
												<form id="import-backup" method="post" action="<?php echo esc_url(admin_url()); ?>admin.php?page=upload_backup">
													<p class="bkpc-custom-file-upload">
														<label>
															<input type="file" name="backup-file" class="regular-text" />
															<span>Choose Backup File...</span>
														</label>
													</p>
													<p>
														<button type="submit" name="upload-backup" class="button button-primary">
															<i class="dashicons dashicons-upload"></i>
															&nbsp;Upload
														</button>
													</p>
												</form>
											</td>
										</tr>
										<tr>
											<th valign="top">
												<h4>
													<i class="dashicons dashicons-backup"></i>
													<strong>All backups</strong>
												</h4>
												<p>
													<em>List with all avaialble backups on your server.</em>
												</p>
												<p>
													<small>* Hold your mouse over each icon to view full description for the action.</small><br />
													<small>** Backups with delete and download full backup buttons are copies of your exports you can either delete them or they will be overwritten when/if you import the same backup.</small>
												</p>
											</th>
											<td valign="top">
												<div id="bkpc-all-backups-container" class="bkpc-all-backups-container">
													<table>
														<tr>
															<th>Created</th>
															<th>Size</th>
															<th>Actions</th>
														</tr>
													</table>
													<?php $this->display_all_backups( $this->settings['bkps_path'] ); ?>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</main>
							<aside>
								<div class="bkpc-sidebar">
									<?php $this->display_sidebar();?>
								</div>
							</aside>
						</div>
						<div class="bkpc-footer">
							<?php $this->display_footer();?>
						</div>
					</div>
				</div>
			<?php
		}

		private function display_all_backups( $path ) {
			ob_start();

			$this->get_all_backups( $path );
			$content = ob_get_contents(); 

			ob_end_clean();

			echo ( ! $content ) ? '<p><em>No backups found!</em></p>' : $content;
		}

		private function get_all_backups( $path ) {
			if ( ! is_dir( $path ) ) {
				return false;
			}

			$files = scandir( $path );

			foreach ( $files as $file ) {
				if ($file !== '.' && $file !== '..') {
					$absolute_path = $path . DIRECTORY_SEPARATOR . $file;
					if ( is_dir( $absolute_path ) ) {
						// Multisite: Don't show backups that doesn't belong current Blog.
						if ( is_multisite() && ( get_option( $file ) !== get_current_blog_id() ) ) {
							continue;
						}

						$this->display_form_actions( $file );
						$this->get_all_backups( $absolute_path );
						?>
								</tr>
						</table>
						<?php
					} else {

						if ( is_multisite() ) {
							$url = esc_url( network_home_url( '/' ) ) . str_replace( ABSPATH, '', $absolute_path );
						} else {
							$url = esc_url( home_url( '/' ) ) . str_replace( ABSPATH, '', $absolute_path );
						}

						switch ( pathinfo($absolute_path, PATHINFO_EXTENSION ) ) {
							case 'sql':
								$this->display_sql_link( $url );
								break;
							case 'zip':
								if ( $file === $this->settings['db_name'] . '.zip' ) {
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
							default:
								break;
						}
					}
				}
			}
		}

		private function display_form_actions($uuid) {
			?>
				<table>
					<tr>
						<td>
							<abbr title="This backup is created on <?php echo esc_textarea( date( 'Y-m-d', $uuid ) ); ?> at <?php echo esc_textarea( date( 'H:i:s', $uuid ) ); ?>">
								<?php echo esc_textarea( $this->utils->get_time_elapsed( $uuid ) ); ?>
							</abbr>
						</td>
						<td>
							<strong>
								<abbr title="All files localted in the backup directory.">
									<?php echo esc_textarea( $this->utils->show_dir_size( $this->settings['bkps_path'] . $uuid ) ); ?>
								</abbr>
							</strong>
						</td>
						<!-- if .zip and .sql are missing & are not with same DB_NAME don't show restore and full backup forms -->
						<?php if ( file_exists($this->settings['bkps_path'] . DIRECTORY_SEPARATOR . $uuid . DIRECTORY_SEPARATOR . $this->settings['db_name'] . '.sql' ) && file_exists( $this->settings['bkps_path'] . DIRECTORY_SEPARATOR . $uuid . DIRECTORY_SEPARATOR . $this->settings['db_name'] . '.zip' ) ) : ?>
							<td>
								<form id="restore-backup" method="post" action="<?php echo esc_url( admin_url() ); ?>admin.php?page=restore_backup">
									<input type="hidden" name="uuid" value="<?php echo esc_attr( $uuid ); ?>" />
									<button type="submit" name="restore-backup" class="button button-primary button-rounded" title="Restore Backup...">
										<i class="dashicons dashicons-update-alt"></i>
									</button>
								</form>
							</td>
							<td>
								<form id="download-backup" method="post" action="<?php echo esc_url( admin_url() ); ?>admin.php?page=download_backup">
									<input type="hidden" name="uuid" value="<?php echo esc_attr( $uuid ); ?>" />
									<button type="submit" name="download-backup" class="button button-primary button-rounded" title="Generate Full Download...">
										<i class="dashicons dashicons-download"></i>
									</button>
								</form>
							</td>
						<?php endif; ?>
						<?php if ( is_multisite() ) : ?>
							<?php if( ! file_exists($this->settings['bkps_path'] . DIRECTORY_SEPARATOR . $uuid . DIRECTORY_SEPARATOR . $this->settings['db_name'] . '.sql' ) || ! file_exists( $this->settings['bkps_path'] . DIRECTORY_SEPARATOR . $uuid . DIRECTORY_SEPARATOR . $this->settings['db_name'] . '.zip' ) ) : ?>
								<td>
									<form id="restore-backup" method="post" action="<?php echo esc_url( admin_url() ); ?>admin.php?page=restore_backup">
										<input type="hidden" name="uuid" value="<?php echo esc_attr( $uuid ); ?>" />
										<input type="hidden" name="wp2wpmu" value="1" />
										<button type="submit" name="restore-backup" class="button button-primary button-rounded" title="Restore Multiste Backup... (WP -> WPMu)" disabled>
											<i class="dashicons dashicons-admin-multisite"></i>
										</button>
									</form>
								</td>
							<?php endif; ?>
						<?php endif; ?>
						<td>
							<form id="delete-backup" method="post" action="<?php echo esc_url( admin_url() ); ?>admin.php?page=delete_backup">
								<input type="hidden" name="uuid" value="<?php echo esc_attr( $uuid ); ?>" />
								<button type="submit" name="delete-backup" class="button button-primary button-red button-rounded" title="Delete Backup...">
									<i class="dashicons dashicons-trash"></i>
								</button>
							</form>
						</td>
			<?php
		}

		private function display_sql_link( $url ) {
			?>
				<td>
					<a href="<?php echo $url; ?>" class="button button-primary button-black button-rounded" target="_blank" title="Download Database...">
						<i class="dashicons dashicons-database"></i>
					</a>
				</td>
			<?php
		}

		private function display_wpcontent_link( $url ) {
			?>
				<td>
					<a href="<?php echo $url; ?>" class="button button-primary button-black button-rounded" target="_blank" title="Download WP Content...">
						<i class="dashicons dashicons-media-archive"></i>
					</a>
				</td>
			<?php
		}

		private function display_download_link( $url ) {
			?>
				<td>
					<a href="<?php echo $url; ?>" class="button button-primary button-black button-rounded" target="_blank" title="Download Full Backup...">
						<i class="dashicons dashicons-archive"></i>
					</a>
				</td>
			<?php
		}

		private function display_notes_link( $url ) {
			?>
				<td>
					<a href="<?php echo $url; ?>" class="button button-primary button-black button-rounded" target="_blank" title="Open Notes...">
						<i class="dashicons dashicons-editor-textcolor"></i>
					</a>
				</td>
			<?php
		}

		private function display_sidebar() {
			?>
				<h3>
					<i class="dashicons dashicons-sos"></i>
					Support
				</h3>
				<ol>
					<li>
						Go to your <a href="<?php esc_url( admin_url( '/' ) ); ?>/wp-admin/site-health.php?tab=debug">Site Health Info</a> page and click the <strong>Copy Site Info to Clipboard</strong> button.
					</li>
					<li>
						Go to the Copilot Plus <a href="<?php echo esc_url( $this->settings['plugin_docurl'] ); ?>" target="_blank">Contact Us</a> page and send us a message with the Site Health Info contens included.
					</li>
					<li>
						Or search and create a support ticket at the plugin <a href="<?php echo esc_url( $this->settings['plugin_wporgurl'] ); ?>" target="_blank">Support Tab</a> page directly at WP.org.
					</li>
				</ol>
				<p>
					<strong>DON'T POST YOUR SITE HEALTH INFO CONTENTS ONTO THE PUBLIC PLUGIN FORUMS!</strong>
				</p>
				<h3>
					<i class="dashicons dashicons-admin-settings"></i>  
					Configuration
				</h3>
				<div class="sysinfo">
					<?php
					echo ( class_exists( 'ZipArchive' ) ) ? '<strong class="text-success">[OK]</strong>' : '<strong class="text-fail">[Failed]</strong>'; 
					echo ' Create archive files.<br />';
				
					try {
						$db = new \PDO( 'mysql:host=' . $this->settings['db_hostname'] . ';dbname=' . $this->settings['db_name'], $this->settings['db_user'], $this->settings['db_password'] );
						echo '<strong class="text-success">[OK]</strong>';
					} catch( \PDOException $err ) {
						echo '<strong class="text-fail">[Failed]</strong>';
					}
					
					echo ' Connect to database.<br />';

					echo ( is_writable(ABSPATH ) ) ? '<strong class="text-success">[OK]</strong>' : '<strong class="text-fail">[Failed]</strong>'; 
					echo ' Base path permissions.<br />';
					
					echo ( is_writable( $this->settings['wpc_path'] ) ) ? '<strong class="text-success">[OK]</strong>': '<strong class="text-fail">[Failed]</strong>';
					echo ' Content path permissions.<br />';

					echo '&mdash;&mdash;&mdash;<br />';

					echo '<strong class="text-success">' . ini_get( 'upload_max_filesize' ) . '</strong> Maximum upload file size<br />';
					echo '<strong class="text-success">' . ini_get( 'post_max_size' ) . '</strong> Maximum post file size.<br />';
					echo '<strong class="text-success">' . ini_get( 'memory_limit' ) . '</strong> Memory limit.<br />';
					echo '<strong class="text-success">' . ini_get( 'max_execution_time' ). '</strong> Maximum execution time.<br />';
					echo '<strong class="text-success">' . ini_get( 'max_input_time' ) . '</strong> Maximum input time.';
					?>
				</div>
			<?php
		}

		private function display_footer() {
			?>
				<p>
					<small>* Server timeout may occur on hosting providers with restrictions (e.g. WPEngine) and may cause corrupted backup files for sites with data over 1GB.</small>
				</p>
			<?php
		}
	}
}
