<?php
/**
 * Dashboard widget displaying backup statistics.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Register dashboard widget.
 */
function bkpc_register_dashboard_widget() {
	$user_can = get_user_meta( get_current_user_id(), 'user_can_access_backup_copilot', true );

	if ( empty( $user_can ) ) {
		return;
	}

	wp_add_dashboard_widget(
		'bkpc_dashboard_widget',
		esc_html__( 'Backup Copilot', 'backup-copilot' ),
		__NAMESPACE__ . '\bkpc_display_dashboard_widget'
	);
}

add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\bkpc_register_dashboard_widget' );

/**
 * Display dashboard widget content.
 */
function bkpc_display_dashboard_widget() {
	$folder_size    = bkpc_get_backups_folder_size();
	$recent_backups = bkpc_get_recent_backups( 3 );
	?>
	<div class="bkpc-admin">
		<div class="bkpc-dashboard-widget">
			<p>
				<?php esc_html_e( 'Create, restore, and manage your WordPress database backups with ease.', 'backup-copilot' ); ?>
			</p>
			<div class="bkpc-widget-stats">
				<div class="bkpc-stat-item">
					<span class="dashicons dashicons-database"></span>
					<div>
						<strong><?php echo esc_html( $folder_size ); ?></strong>
						<small><?php esc_html_e( 'Total Backup Size', 'backup-copilot' ); ?></small>
					</div>
				</div>
				<div class="bkpc-stat-item">
					<span class="dashicons dashicons-backup"></span>
					<div>
						<strong><?php echo esc_html( count( $recent_backups ) ); ?></strong>
						<small><?php esc_html_e( 'Recent Full Backups', 'backup-copilot' ); ?></small>
					</div>
				</div>
			</div>
			<?php if ( ! empty( $recent_backups ) ) : ?>
				<div class="bkpc-widget-backups">
					<h4><?php esc_html_e( 'Latest Backups', 'backup-copilot' ); ?></h4>
					<ul>
						<?php foreach ( $recent_backups as $backup ) : ?>
							<li>
								<span class="dashicons dashicons-yes-alt"></span>
								<span><?php echo esc_html( $backup['name'] ); ?></span>
								<small><?php echo esc_html( $backup['date'] ); ?></small>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			<div class="bkpc-widget-actions button-group">
				<a 
					href="<?php echo esc_url( admin_url( 'admin.php?page=' . BKPC_MANAGE_BACKUPS_SLUG ) ); ?>" 
					class="button button-primary"
				>
					<span class="dashicons dashicons-admin-tools"></span>
					<?php esc_html_e( 'Manage Backups', 'backup-copilot' ); ?>
				</a>
				<a 
					href="<?php echo esc_url( BKPC_PLUGIN_SUPPORT_URL ); ?>" 
					class="button" 
					target="_blank"
				>
					<span class="dashicons dashicons-sos"></span>
					<?php esc_html_e( 'Support', 'backup-copilot' ); ?>
				</a>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Get backups folder size.
 */
function bkpc_get_backups_folder_size() {
	if ( ! is_dir( BKPC_PLUGIN_BACKUP_DIR_PATH ) ) {
		return '0 B';
	}

	$total_size = 0;
	$files      = new \RecursiveIteratorIterator(
		new \RecursiveDirectoryIterator( BKPC_PLUGIN_BACKUP_DIR_PATH, \RecursiveDirectoryIterator::SKIP_DOTS )
	);

	foreach ( $files as $file ) {
		if ( $file->isFile() ) {
			$total_size += $file->getSize();
		}
	}

	return bkpc_format_bytes( $total_size );
}

/**
 * Get recent backups.
 */
function bkpc_get_recent_backups( $limit = 3 ) {
	$backups = array();

	if ( ! is_dir( BKPC_PLUGIN_BACKUP_DIR_PATH ) ) {
		return $backups;
	}

	// Use scandir to get all directories.
	$items = scandir( BKPC_PLUGIN_BACKUP_DIR_PATH );
	if ( false === $items ) {
		return $backups;
	}

	$dirs = array();
	foreach ( $items as $item ) {
		// Skip current/parent directory markers.
		if ( '.' === $item || '..' === $item ) {
			continue;
		}

		// Skip common files (not directories).
		if ( in_array( $item, array( '.htaccess', 'index.html', 'index.php', 'web.config' ), true ) ) {
			continue;
		}

		$full_path = BKPC_PLUGIN_BACKUP_DIR_PATH . $item;

		if ( is_dir( $full_path ) ) {
			$dirs[] = $full_path;
		}
	}

	if ( empty( $dirs ) ) {
		return $backups;
	}

	// Sort by modification time (newest first).
	usort(
		$dirs,
		function ( $a, $b ) {
			return filemtime( $b ) - filemtime( $a );
		}
	);

	$dirs = array_slice( $dirs, 0, $limit );

	foreach ( $dirs as $dir ) {
		$uuid     = basename( $dir );
		$zip_file = trailingslashit( $dir ) . $uuid . '.zip';

		if ( file_exists( $zip_file ) ) {
			$backups[] = array(
				'name' => $uuid,
				'date' => date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), filemtime( $zip_file ) ),
				'size' => bkpc_format_bytes( filesize( $zip_file ) ),
			);
		}
	}

	return $backups;
}

/**
 * Format bytes to human-readable format.
 */
function bkpc_format_bytes( $bytes, $precision = 2 ) {
	$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

	$bytes = max( $bytes, 0 );
	$pow   = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
	$pow   = min( $pow, count( $units ) - 1 );

	$bytes /= pow( 1024, $pow );

	return round( $bytes, $precision ) . ' ' . $units[ $pow ];
}
