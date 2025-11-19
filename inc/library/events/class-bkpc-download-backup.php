<?php
/**
 * Handles the backup download process including generating full
 * backup archives for download.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      0.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_Download_Backup' ) ) {
	class BKPC_Download_Backup {
		private $mu;
		private $fs;
		private $zip;
		private $progress;

		public function __construct() {
			$this->mu       = new BKPC_Multisite();
			$this->fs       = new BKPC_FS();
			$this->zip      = new BKPC_Zip();
			$this->progress = new BKPC_Progress();
		}

		/**
		 * Initialize WordPress hooks for backup downloads.
		 */
		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		/**
		 * Register admin menu and AJAX actions when WordPress is loaded.
		 */
		public function on_loaded() {
			add_action( 'admin_menu', array( $this, 'add_admin_action' ) );
			add_action( 'wp_ajax_download_backup', array( $this, 'download_backup' ) );
		}

		/**
		 * Add hidden admin submenu page for backup downloads.
		 */
		public function add_admin_action() {
			add_submenu_page( null, '', '', 'manage_options', 'download_backup', 'download_backup', null );
		}

		/**
		 * Generate and prepare a full backup archive for download.
		 */
		public function download_backup( $uuid = '', $ajax = true ) {
			// Verify nonce and capabilities for AJAX requests.
			if ( $ajax ) {
				if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'bkpc_ajax_nonce' ) ) {
					wp_send_json_error( 'Security check failed!' );
				}

				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( 'Insufficient permissions!' );
				}
			}

			$uuid = $uuid ? sanitize_text_field( $uuid ) : ( isset( $_REQUEST['uuid'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['uuid'] ) ) : '' );

			// Check if this is a safety backup
			$safety_backup_dir  = BKPC_PLUGIN_BACKUP_DIR_PATH . '.safety-backup/' . $uuid;
			$regular_backup_dir = BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid;
			$backup_dir         = is_dir( $safety_backup_dir ) ? trailingslashit( $safety_backup_dir ) : trailingslashit( $regular_backup_dir );

			$download_filename = 'download-' . $uuid . '.zip';
			$zip_filepath      = $backup_dir . $download_filename;
			$download_url      = $this->mu->get_mu_download_url( $zip_filepath );

			// Initialize progress tracking
			$this->progress->init( $uuid );

			if ( ! file_exists( $zip_filepath ) ) {
				$this->progress->add( $uuid, 'Generating full backup for download...', false );
				$this->zip->create_zip_archive( $backup_dir, $zip_filepath, array(), true );
				$this->progress->add( $uuid, 'Generating full backup for download...', true );
			}

			// Add final completion message
			$this->progress->add( $uuid, 'Download ready!', true );

			// Small delay to allow progress notice to display final message
			sleep( 2 );

			// Clear progress tracking
			$this->progress->clear( $uuid );

			if ( $ajax ) {
				wp_send_json_success(
					'Backup full download was generated successfully! <strong><a href="'
					. esc_url( $download_url ) . '">Download Full Backup...</a></strong>'
				);
			} else {
				wp_send_json_success(
					'Backup export was generated and is ready to be downloaded!<br /><a href="'
					. esc_url( $download_url ) . '" class="button button-primary bkpc-export-download" data-uuid="'
					. esc_attr( $uuid ) . '" title="Download Backup Export..." download><i class="dashicons dashicons-download"></i> Download Backup Export</a>'
				);
			}
		}
	}

	$bkpc_download_backup = new BKPC_Download_Backup();
	$bkpc_download_backup->init();
}
