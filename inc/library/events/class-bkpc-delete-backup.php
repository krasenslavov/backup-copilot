<?php
/**
 * Handles the backup deletion process including removal of all
 * associated files and multisite options.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      0.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_Delete_Backup' ) ) {
	class BKPC_Delete_Backup {
		private $mu;

		public function __construct() {
			$this->mu = new BKPC_Multisite();
		}

		/**
		 * Initialize WordPress hooks for backup deletion.
		 */
		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		/**
		 * Register admin menu and AJAX actions when WordPress is loaded.
		 */
		public function on_loaded() {
			add_action( 'admin_menu', array( $this, 'add_admin_action' ) );
			add_action( 'wp_ajax_delete_backup', array( $this, 'delete_backup' ) );
		}

		/**
		 * Add hidden admin submenu page for backup deletion.
		 */
		public function add_admin_action() {
			add_submenu_page( null, '', '', 'manage_options', 'delete_backup', 'delete_backup', null );
		}

		/**
		 * Delete a backup and all associated files.
		 */
		public function delete_backup( $uuid = '', $ajax = true ) {
			// Verify nonce and capabilities for AJAX requests.
			if ( $ajax ) {
				if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'bkpc_ajax_nonce' ) ) {
					echo wp_json_encode( esc_html__( 'Security check failed!', 'backup-copilot' ) );
					exit;
				}

				if ( ! current_user_can( 'manage_options' ) ) {
					echo wp_json_encode( esc_html__( 'Insufficient permissions!', 'backup-copilot' ) );
					exit;
				}
			}

			$uuid = $uuid ? sanitize_text_field( $uuid ) : ( isset( $_REQUEST['uuid'] )
				? sanitize_text_field( wp_unslash( $_REQUEST['uuid'] ) ) : '' );

			// Check if this is a safety backup by checking if it exists in .safety-backup directory.
			$safety_backup_dir  = BKPC_PLUGIN_BACKUP_DIR_PATH . '.safety-backup/' . $uuid;
			$regular_backup_dir = BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid;

			if ( is_dir( $safety_backup_dir ) ) {
				$backup_dir = $safety_backup_dir;
			} else {
				$backup_dir = $regular_backup_dir;
			}

			$this->delete_backup_files( $backup_dir );
			$this->mu->delete_mu_option( $uuid );

			if ( $ajax ) {
				echo wp_json_encode( esc_html__( 'Backup point was deleted successfully!', 'backup-copilot' ) );
				exit;
			}
		}

		/**
		 *
		 */
		private function delete_backup_files( $backup_dir ) {
			// Validate path is within backup directory.
			$real_backup_dir     = realpath( $backup_dir );
			$allowed_backup_base = realpath( BKPC_PLUGIN_BACKUP_DIR_PATH );

			if ( false === $real_backup_dir || false === $allowed_backup_base ) {
				return false;
			}

			if ( 0 !== strpos( $real_backup_dir, $allowed_backup_base ) ) {
				return false;
			}

			if ( ! is_dir( $backup_dir ) ) {
				return false;
			}

			$files = scandir( $backup_dir );

			foreach ( $files as $file ) {
				if ( '.' !== $file && '..' !== $file ) {
					$absolute_path = trailingslashit( $backup_dir ) . $file;
					if ( is_dir( $absolute_path ) && ! is_link( $absolute_path ) ) {
						$this->delete_backup_files( $absolute_path );
					} else {
						unlink( $absolute_path );
					}
				}
			}

			return rmdir( $backup_dir );
		}
	}

	$bkpc_delete_backup = new BKPC_Delete_Backup();
	$bkpc_delete_backup->init();
}
