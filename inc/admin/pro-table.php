<?php
/**
 * Pro version comparison table template.
 *
 * Displays feature comparison between free and PRO versions,
 * encouraging users to upgrade for advanced functionality.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

?>
<div class="bkpc-pro">
	<h4>
		<?php echo esc_html__( 'Get the PRO version today!', 'backup-copilot' ); ?>
	</h4>
	<p>
		<?php echo esc_html__( 'The PRO version offers advanced backup automation, cloud storage integration, and premium support.', 'backup-copilot' ); ?>
	</p>
	<table>
		<tr>
			<th><?php echo esc_html__( 'Feature', 'backup-copilot' ); ?></th>
			<th><?php echo esc_html__( 'Free', 'backup-copilot' ); ?></th>
			<th><?php echo esc_html__( 'PRO', 'backup-copilot' ); ?></th>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Manual Backups', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✓', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✓', 'backup-copilot' ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Database & Files Backup', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✓', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✓', 'backup-copilot' ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'One-Click Restore', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✓', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✓', 'backup-copilot' ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Download Backups', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( 'ZIP Export', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( 'Multiple formats', 'backup-copilot' ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Backup Scheduling', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✗', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( 'Daily, Weekly, Monthly', 'backup-copilot' ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Cloud Storage', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✗', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( 'Dropbox, Drive, OneDrive', 'backup-copilot' ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Email Alerts', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✗', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( 'Success & Failure', 'backup-copilot' ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Retention Management', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( 'Manual Only', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( 'Auto-Delete old backups', 'backup-copilot' ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Migration & Cloning', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✗', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( 'URL Find/Replace', 'backup-copilot' ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Emergency Rollback', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✗', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✓', 'backup-copilot' ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Priority Email Support', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( 'Community forums', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( '✓', 'backup-copilot' ); ?></td>
		</tr>
		<tr>
			<td><?php echo esc_html__( 'Plugin Updates & New Features', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( 'Delayed', 'backup-copilot' ); ?></td>
			<td><?php echo esc_html__( 'First access', 'backup-copilot' ); ?></td>
		</tr>
	</table>
	<p class="button-group">
		<a
			class="button button-primary button-pro"
			href="https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=pro_table_button"
			target="_blank"
		>
			<?php echo esc_html__( 'GET PRO VERSION', 'backup-copilot' ); ?>
		</a>
		<a
			class="button button-primary button-watch-video"
			href="https://www.youtube.com/watch?v=t6An3BgI6_k"
			target="_blank"
		>
			<?php echo esc_html__( 'Watch Video', 'backup-copilot' ); ?>
		</a>
	</p>
	<p>
		<?php
		printf(
			/* translators: %1$s is replaced with "Need automated backups with cloud storage integration?" */
			/* translators: %2$s is replaced with "Get the PRO version now" */
			wp_kses( '%1$s %2$s!', json_decode( BKPC_PLUGIN_ALLOWED_HTML_ARR, true ) ),
			'<em>' . esc_html__( 'Need automated backups with cloud storage integration and advanced features?', 'backup-copilot' ) . '</em>',
			'<a href="https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=pro_table_button" target="_blank"><strong>' . esc_html__( 'Get the PRO version now', 'backup-copilot' ) . '</strong></a>'
		);
		?>
	</p>
	<p>
		<iframe 
			width="100%"
			height="320" 
			src="https://www.youtube.com/embed/t6An3BgI6_k" 
			title="Backup Copilot" 
			frameborder="0" 
			allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
			allowfullscreen>
		</iframe>
	</p>
	<hr />
	<p>
		<?php
		printf(
			/* translators: %1$s is replaced with "Note" */
			wp_kses( '%1$s: Scheduled backups, cloud storage, email alerts, automatic retention, migration tools, and emergency rollback are available only in the PRO version!', json_decode( BKPC_PLUGIN_ALLOWED_HTML_ARR, true ) ),
			'<strong>' . esc_html__( 'Note', 'backup-copilot' ) . '</strong>'
		);
		?>
	</p>
</div>
