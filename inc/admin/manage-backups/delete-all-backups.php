<?php
/**
 * AJAX handler for deleting all backups and clearing the .bkps directory.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Initialize delete all backups action.
 */
function bkpc_init_delete_all_backups() {
	add_action( 'wp_ajax_delete_all_backups', __NAMESPACE__ . '\bkpc_delete_all_backups' );
}

/**
 * AJAX handler to delete all backups.
 */
function bkpc_delete_all_backups() {
	// Verify nonce.
	check_ajax_referer( 'bkpc_ajax_nonce', 'nonce' );

	// Check user capability.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Insufficient permissions.', 'backup-copilot' ),
			)
		);
	}

	$backup_dir = BKPC_PLUGIN_BACKUP_DIR_PATH;

	if ( ! is_dir( $backup_dir ) ) {
		wp_send_json_success(
			array(
				'message' => esc_html__( 'Backup directory does not exist.', 'backup-copilot' ),
			)
		);
	}

	// Delete all backup directories (for current site only in multisite).
	$deleted_count   = 0;
	$files           = scandir( $backup_dir );
	$current_blog_id = is_multisite() ? get_current_blog_id() : null;
	$mu              = new BKPC_Multisite();

	if ( $files ) {
		foreach ( $files as $file ) {
			if ( '.' === $file || '..' === $file ) {
				continue;
			}

			$absolute_path = trailingslashit( $backup_dir ) . $file;

			if ( is_dir( $absolute_path ) && ! is_link( $absolute_path ) ) {
				// Skip .safety-backup directory.
				if ( '.safety-backup' === $file ) {
					continue;
				}

				// Multisite: Only delete backups belonging to current site.
				if ( is_multisite() && is_numeric( $file ) ) {
					$backup_blog_id = $mu->get_mu_option( $file );

					// Skip if backup doesn't have blog ID set.
					if ( ! $backup_blog_id ) {
						continue;
					}

					// Skip if backup belongs to a different site.
					if ( $current_blog_id !== (int) $backup_blog_id ) {
						continue;
					}
				}

				// Use recursive delete function.
				if ( bkpc_recursive_delete_directory( $absolute_path ) ) {
					// Delete multisite option if it exists.
					$mu->delete_mu_option( $file );
					$deleted_count++;
				}
			} elseif ( is_file( $absolute_path ) ) {
				// Preserve security files, delete everything else.
				$security_files = array( '.htaccess', 'index.php', 'php.ini', '.user.ini', 'web.config' );
				if ( ! in_array( $file, $security_files, true ) ) {
					unlink( $absolute_path );
				}
			}
		}
	}

	// Prepare success message based on multisite context.
	if ( is_multisite() ) {
		$message = sprintf(
			/* translators: %d: number of backups deleted */
			esc_html__( 'Successfully deleted %d backup(s) for this site.', 'backup-copilot' ),
			$deleted_count
		);
	} else {
		$message = sprintf(
			/* translators: %d: number of backups deleted */
			esc_html__( 'Successfully deleted %d backup(s). The .bkps directory has been cleared.', 'backup-copilot' ),
			$deleted_count
		);
	}

	wp_send_json_success(
		array(
			'message' => $message,
		)
	);
}

/**
 * Recursively delete a directory and all its contents.
 *
 * @param string $dir Directory path to delete.
 * @return bool True on success, false on failure.
 */
function bkpc_recursive_delete_directory( $dir ) {
	if ( ! is_dir( $dir ) ) {
		return false;
	}

	$files = scandir( $dir );

	foreach ( $files as $file ) {
		if ( '.' !== $file && '..' !== $file ) {
			$absolute_path = trailingslashit( $dir ) . $file;

			if ( is_dir( $absolute_path ) && ! is_link( $absolute_path ) ) {
				bkpc_recursive_delete_directory( $absolute_path );
			} else {
				unlink( $absolute_path );
			}
		}
	}

	return rmdir( $dir );
}

// Register on 'plugins_loaded' to ensure AJAX handlers are available early.
add_action( 'plugins_loaded', __NAMESPACE__ . '\bkpc_init_delete_all_backups' );
