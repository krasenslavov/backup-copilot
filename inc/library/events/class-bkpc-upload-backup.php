<?php
/**
 * Handles the backup upload/import process including file validation,
 * extraction, and multisite option management.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      0.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_Upload_Backup' ) ) {
	class BKPC_Upload_Backup {
		private $fs;
		private $mu;
		private $delete;
		private $progress;

		public function __construct() {
			$this->fs       = new BKPC_FS();
			$this->mu       = new BKPC_Multisite();
			$this->delete   = new BKPC_Delete_Backup();
			$this->progress = new BKPC_Progress();
		}

		/**
		 * Initializes the upload backup functionality by hooking into WordPress.
		 */
		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		/**
		 * Registers AJAX actions and admin menu items on WordPress load.
		 */
		public function on_loaded() {
			add_action( 'admin_menu', array( $this, 'add_admin_action' ) );
			add_action( 'wp_ajax_upload_backup', array( $this, 'upload_backup' ) );
		}

		/**
		 * Adds a hidden admin submenu page for upload functionality.
		 */
		public function add_admin_action() {
			add_submenu_page( null, '', '', 'manage_options', 'upload_backup', 'upload_backup', null );
		}

		/**
		 * Handles backup file upload and import via AJAX with validation and extraction.
		 */
		public function upload_backup() {
			// Verify nonce and capabilities.
			if ( ! isset( $_REQUEST['nonce'] )
				|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'bkpc_ajax_nonce' ) ) {
				echo wp_json_encode( esc_html__( 'Security check failed!', 'backup-copilot' ) );
				exit;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				echo wp_json_encode( esc_html__( 'Insufficient permissions!', 'backup-copilot' ) );
				exit;
			}

			// Validate file upload.
			if ( ! isset( $_FILES['file'] ) || ! is_array( $_FILES['file'] ) ) {
				echo wp_json_encode( esc_html__( 'No file uploaded!', 'backup-copilot' ) );
				exit;
			}

			$file_error = isset( $_FILES['file']['error'] ) ? absint( $_FILES['file']['error'] ) : UPLOAD_ERR_NO_FILE;
			$file_name  = isset( $_FILES['file']['name'] ) ? sanitize_file_name( $_FILES['file']['name'] ) : '';
			$file_path  = isset( $_FILES['file']['tmp_name'] ) ? sanitize_text_field( $_FILES['file']['tmp_name'] ) : '';
			$file_size  = isset( $_FILES['file']['size'] ) ? absint( $_FILES['file']['size'] ) : 0;

			// Use WordPress function to validate actual file type.
			$file_info = wp_check_filetype_and_ext( $file_path, $file_name, array( 'zip' => 'application/zip' ) );

			if ( ! $file_info['ext'] || ! $file_info['type'] ) {
				echo wp_json_encode( esc_html__( 'Invalid file type! Only ZIP files are allowed.', 'backup-copilot' ) );
				exit;
			}

			if ( UPLOAD_ERR_OK === $file_error ) {
				// Validate filename format: {uuid}.zip or download-{uuid}.zip
				// UUID can be either: 32-char secure hash (new) or numeric timestamp (legacy)
				$is_full_backup = false;
				$uuid           = '';

				if ( preg_match( '/^download-([a-f0-9]{32}|\d+)\.zip$/', $file_name, $matches ) ) {
					// Full backup format: download-{uuid}.zip
					$is_full_backup = true;
					$uuid           = $matches[1];
				} elseif ( preg_match( '/^([a-f0-9]{32}|\d+)\.zip$/', $file_name, $matches ) ) {
					// WP Content format: {uuid}.zip
					$is_full_backup = false;
					$uuid           = $matches[1];
				} else {
					echo wp_json_encode( esc_html__( 'Invalid backup filename format! Use {uuid}.zip or download-{uuid}.zip', 'backup-copilot' ) );
					exit;
				}

				$backup_dir = BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid;

				if ( ceil( $file_size / ( 1024 * 1024 ) ) > 500 ) {
					echo wp_json_encode( esc_html__( 'Backup point upload failed! You are over the maximum backup limit size!', 'backup-copilot' ) );
					exit;
				}

				// Initialize progress tracking
				$this->progress->init( $uuid );

				if ( ! is_dir( BKPC_PLUGIN_BACKUP_DIR_PATH ) ) {
					$this->fs->create_directory( BKPC_PLUGIN_BACKUP_DIR_PATH );
				}

				if ( ! is_dir( $backup_dir ) ) {
					$this->fs->create_directory( $backup_dir );
				} else {
					// When we don't have have .sql and .zip files
					// this means that we have a leftover backup after export.
					if ( ! file_exists( trailingslashit( $backup_dir ) . $uuid . '.sql' ) && ! file_exists( trailingslashit( $backup_dir ) . $uuid . '.zip' ) ) {
						// Quitely delete leftover backup directory
						$this->delete->delete_backup( $uuid, false );
					} else {
						echo wp_json_encode( esc_html__( 'Nothing is uploaded. Backup point already exists!', 'backup-copilot' ) );
						exit;
					}
				}

				$this->progress->add( $uuid, 'Creating backup directory...', false );

				// Store creation timestamp
				file_put_contents( trailingslashit( $backup_dir ) . '.timestamp', time() );

				$this->progress->add( $uuid, 'Creating backup directory...', true );

				if ( $is_full_backup ) {
					// Full backup: Extract all files from download-{uuid}.zip
					$this->progress->add( $uuid, 'Extracting full backup archive...', false );

					if ( class_exists( 'ZipArchive' ) ) {
						$zip = new \ZipArchive();
						$res = $zip->open( $file_path );

						if ( true === $res ) {
							$zip->extractTo( $backup_dir );
							$zip->close();
							$this->progress->add( $uuid, 'Extracting full backup archive...', true );
						} else {
							$this->delete->delete_backup( $uuid, false );
							echo wp_json_encode( esc_html__( 'Unzip failed! Double check if zip file is not corrupted!', 'backup-copilot' ) );
							exit;
						}
					} else {
						// Fallback to Unix unzip command
						exec( 'unzip --help', $output );
						if ( $output ) {
							exec( 'unzip ' . escapeshellarg( $file_path ) . ' -d ' . escapeshellarg( $backup_dir ), $output );
							$this->progress->add( $uuid, 'Extracting full backup archive...', true );
						}
					}
				} else {
					// WP Content backup: Just copy {uuid}.zip to backup directory
					$this->progress->add( $uuid, 'Copying wp-content backup...', false );
					$destination = trailingslashit( $backup_dir ) . $uuid . '.zip';

					if ( copy( $file_path, $destination ) ) {
						$this->progress->add( $uuid, 'Copying wp-content backup...', true );
					} else {
						$this->delete->delete_backup( $uuid, false );
						echo wp_json_encode( esc_html__( 'Failed to copy backup file!', 'backup-copilot' ) );
						exit;
					}
				}

				$this->mu->add_mu_option( $uuid );

				// Add final completion message
				$this->progress->add( $uuid, 'Upload completed successfully!', true );

				// Small delay to allow progress notice to display final message
				sleep( 2 );

				// Clear progress tracking
				$this->progress->clear( $uuid );

				echo wp_json_encode( esc_html__( 'Backup point was uploaded successfully!', 'backup-copilot' ) );
			} else {
				echo wp_json_encode( esc_html__( 'Backup point upload failed! Invalid file or archive type!', 'backup-copilot' ) );
			}

			exit;
		}
	}

	$bkpc_upload_backup = new BKPC_Upload_Backup();
	$bkpc_upload_backup->init();
}
