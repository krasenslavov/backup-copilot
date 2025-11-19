<?php
/**
 * Handles pre-restore validation to ensure safe restoration.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_Restore_Validator' ) ) {
	class BKPC_Restore_Validator {
		/**
		 * Validate backup files before restoration.
		 */
		public function validate_backup( $uuid ) {
			$results = array(
				'valid'    => true,
				'errors'   => array(),
				'warnings' => array(),
				'info'     => array(),
			);

			$backup_dir = trailingslashit( BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid );
			$sql_file   = $backup_dir . $uuid . '.sql';
			$zip_file   = $backup_dir . $uuid . '.zip';

			// Validate SQL file.
			if ( file_exists( $sql_file ) ) {
				$sql_validation = $this->validate_sql_file( $sql_file );
				if ( ! $sql_validation['valid'] ) {
					$results['valid']    = false;
					$results['errors'][] = $sql_validation['message'];
				} else {
					$results['info'][] = sprintf(
						/* translators: %s: file size */
						esc_html__( 'Database backup: %s', 'backup-copilot' ),
						size_format( filesize( $sql_file ) )
					);
				}
			} else {
				$results['warnings'][] = esc_html__( 'No database backup found. Only files will be restored.', 'backup-copilot' );
			}

			// Validate ZIP file.
			if ( file_exists( $zip_file ) ) {
				$zip_validation = $this->validate_zip_file( $zip_file );
				if ( ! $zip_validation['valid'] ) {
					$results['valid']    = false;
					$results['errors'][] = $zip_validation['message'];
				} else {
					$results['info'][] = sprintf(
						/* translators: %s: file size */
						esc_html__( 'Files backup: %s', 'backup-copilot' ),
						size_format( filesize( $zip_file ) )
					);
				}
			} else {
				$results['warnings'][] = esc_html__( 'No files backup found. Only database will be restored.', 'backup-copilot' );
			}

			// Check if we have at least one valid file.
			if ( ! file_exists( $sql_file ) && ! file_exists( $zip_file ) ) {
				$results['valid']    = false;
				$results['errors'][] = esc_html__( 'No valid backup files found.', 'backup-copilot' );
			}

			// Validate disk space.
			$disk_space = $this->validate_disk_space( $sql_file, $zip_file );
			if ( ! $disk_space['valid'] ) {
				$results['valid']    = false;
				$results['errors'][] = $disk_space['message'];
			} else {
				$results['info'][] = $disk_space['message'];
			}

			// Validate file permissions.
			$permissions = $this->validate_permissions();
			if ( ! $permissions['valid'] ) {
				$results['valid']    = false;
				$results['errors'][] = $permissions['message'];
			}

			return $results;
		}

		/**
		 * Generate preview report comparing current site with backup.
		 */
		public function generate_preview( $uuid ) {
			global $wpdb;

			$preview = array(
				'database' => array(),
				'files'    => array(),
			);

			$backup_dir = trailingslashit( BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid );
			$sql_file   = $backup_dir . $uuid . '.sql';
			$zip_file   = $backup_dir . $uuid . '.zip';
			$notes_file = $backup_dir . 'notes.txt';

			// Get backup notes if available.
			if ( file_exists( $notes_file ) ) {
				$preview['notes'] = file_get_contents( $notes_file );
			}

			// Database comparison.
			$preview['database']['current_posts']    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish'" );
			$preview['database']['current_comments'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->comments}" );
			$preview['database']['current_users']    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users}" );

			// Get backup timestamp from directory creation time.
			if ( is_dir( $backup_dir ) ) {
				$preview['backup_date'] = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), filemtime( $backup_dir ) );
			}

			// File statistics.
			if ( file_exists( $zip_file ) ) {
				$preview['files']['backup_size'] = size_format( filesize( $zip_file ) );
			}

			if ( file_exists( $sql_file ) ) {
				$preview['database']['backup_size'] = size_format( filesize( $sql_file ) );
			}

			return $preview;
		}

		/**
		 * Validate SQL file syntax and integrity.
		 */
		private function validate_sql_file( $sql_file ) {
			$result = array(
				'valid'   => true,
				'message' => '',
			);

			// Check file size.
			$file_size = filesize( $sql_file );
			if ( 0 === $file_size ) {
				$result['valid']   = false;
				$result['message'] = esc_html__( 'SQL file is empty.', 'backup-copilot' );
				return $result;
			}

			// Read first few lines to verify SQL syntax.
			$handle = fopen( $sql_file, 'r' );
			if ( ! $handle ) {
				$result['valid']   = false;
				$result['message'] = esc_html__( 'Unable to read SQL file.', 'backup-copilot' );
				return $result;
			}

			$has_valid_sql = false;
			$line_count    = 0;
			while ( ( $line = fgets( $handle ) ) !== false && $line_count < 100 ) {
				$line = trim( $line );
				// Look for typical SQL patterns.
				if ( preg_match( '/^(CREATE|INSERT|DROP|USE|SET|\/\*)/i', $line ) ) {
					$has_valid_sql = true;
					break;
				}
				$line_count++;
			}
			fclose( $handle );

			if ( ! $has_valid_sql ) {
				$result['valid']   = false;
				$result['message'] = esc_html__( 'SQL file does not contain valid SQL statements.', 'backup-copilot' );
			}

			return $result;
		}

		/**
		 * Validate ZIP file integrity.
		 */
		private function validate_zip_file( $zip_file ) {
			$result = array(
				'valid'   => true,
				'message' => '',
			);

			if ( ! class_exists( 'ZipArchive' ) ) {
				$result['valid']   = false;
				$result['message'] = esc_html__( 'ZipArchive extension not available.', 'backup-copilot' );
				return $result;
			}

			$zip = new \ZipArchive();
			$res = $zip->open( $zip_file, \ZipArchive::CHECKCONS );

			if ( true !== $res ) {
				$result['valid'] = false;
				switch ( $res ) {
					case \ZipArchive::ER_NOZIP:
						$result['message'] = esc_html__( 'Not a valid ZIP archive.', 'backup-copilot' );
						break;
					case \ZipArchive::ER_INCONS:
						$result['message'] = esc_html__( 'ZIP archive is inconsistent or corrupted.', 'backup-copilot' );
						break;
					case \ZipArchive::ER_CRC:
						$result['message'] = esc_html__( 'ZIP archive CRC error.', 'backup-copilot' );
						break;
					default:
						$result['message'] = esc_html__( 'Unable to open ZIP file.', 'backup-copilot' );
				}
			} else {
				$zip->close();
			}

			return $result;
		}

		/**
		 * Validate available disk space.
		 */
		private function validate_disk_space( $sql_file, $zip_file ) {
			$result = array(
				'valid'   => true,
				'message' => '',
			);

			$required_space = 0;

			// Calculate required space for SQL file (add 20% buffer for extraction).
			if ( file_exists( $sql_file ) ) {
				$required_space += filesize( $sql_file ) * 1.2;
			}

			// Calculate required space for ZIP file (add 100% for extraction).
			if ( file_exists( $zip_file ) ) {
				$required_space += filesize( $zip_file ) * 2;
			}

			// Get available disk space.
			$available_space = @disk_free_space( ABSPATH );

			if ( false === $available_space ) {
				$result['message'] = esc_html__( 'Unable to determine available disk space.', 'backup-copilot' );
			} elseif ( $available_space < $required_space ) {
				$result['valid']   = false;
				$result['message'] = sprintf(
					/* translators: 1: required space, 2: available space */
					esc_html__( 'Insufficient disk space. Required: %1$s, Available: %2$s', 'backup-copilot' ),
					size_format( $required_space ),
					size_format( $available_space )
				);
			} else {
				$result['message'] = sprintf(
					/* translators: %s: available space */
					esc_html__( 'Available disk space: %s', 'backup-copilot' ),
					size_format( $available_space )
				);
			}

			return $result;
		}

		/**
		 * Validate file system permissions.
		 */
		private function validate_permissions() {
			$result = array(
				'valid'   => true,
				'message' => '',
			);

			$directories = array(
				BKPC_PLUGIN_WPCONTENT_DIR_PATH => 'wp-content',
				ABSPATH                        => 'WordPress root',
			);

			$unwritable = array();
			foreach ( $directories as $path => $name ) {
				if ( ! is_writable( $path ) ) {
					$unwritable[] = $name;
				}
			}

			if ( ! empty( $unwritable ) ) {
				$result['valid']   = false;
				$result['message'] = sprintf(
					/* translators: %s: list of unwritable directories */
					esc_html__( 'The following directories are not writable: %s', 'backup-copilot' ),
					implode( ', ', $unwritable )
				);
			}

			return $result;
		}
	}
}
