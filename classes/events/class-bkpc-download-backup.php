<?php
/**
 * Backup Copilot - Download Backup Event
 *
 * Handles the backup download process including generating full
 * backup archives for download.
 *
 * @package    BKPC
 * @subpackage Backup_Copilot/Events
 * @author     Krasen Slavov <hello@krasenslavov.com>
 * @copyright  2025
 * @license    GPL-2.0-or-later
 * @link       https://krasenslavov.com/plugins/backup-copilot/
 * @since      0.1.0
 */

namespace BKPC;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Download_Backup' ) ) {

	class BKPC_Download_Backup extends Backup_Copilot {
		private $mu;
		private $fs;
		private $zip;

		public function __construct() {
			parent::__construct();

			// Core
			$this->mu  = new BKPC_Multisite();
			$this->fs  = new BKPC_FS();
			$this->zip = new BKPC_Zip();
		}

		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		public function on_loaded() {
			add_action( 'admin_menu', array( $this, 'add_admin_action' ) );
			add_action( 'wp_ajax_download_backup', array( $this, 'download_backup' ) );
		}

		public function add_admin_action() {
			add_submenu_page( null, '', '', 'manage_options', 'download_backup', 'download_backup', null );
		}

		public function download_backup( $uuid = '', $ajax = true ) {
			// Verify nonce and capabilities for AJAX requests.
			if ( $ajax ) {
				if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'bkpc_ajax_nonce' ) ) {
					echo wp_json_encode( 'Security check failed!' );
					exit;
				}

				if ( ! current_user_can( 'manage_options' ) ) {
					echo wp_json_encode( 'Insufficient permissions!' );
					exit;
				}
			}

			$uuid              = $uuid ? sanitize_text_field( $uuid ) : ( isset( $_REQUEST['uuid'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['uuid'] ) ) : '' );
			$backup_dir        = trailingslashit( $this->settings['bkps_path'] . $uuid );
			$progress_filename = $backup_dir . 'progress.txt';
			$zip_filepath      = $backup_dir . $uuid . '.zip';
			$download_url      = $this->mu->get_mu_download_url( $zip_filepath );

			if ( ! file_exists( $zip_filepath ) ) {
				$this->fs->create_file( $progress_filename, 'Generating full backup for download...', true );
				$this->zip->create_zip_archive( $backup_dir, $zip_filepath, array(), true );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			// sleep( 1 );

			$this->fs->remove_file( $progress_filename );

			if ( $ajax ) {
				echo wp_json_encode(
					'Backup full download was generated successfully! <strong><a href="'
					. esc_url( $download_url ) . '">Download Full Backup...</a></strong>'
				);
			} else {
				echo wp_json_encode(
					'Backup export was generated and is ready to be downloaded!<br /><form id="delete-backup" method="post" action="'
					. esc_url( admin_url( 'admin.php?page=delete_backup' ) ) . '"><input type="hidden" name="uuid" value="'
					. esc_attr( $uuid ) . '" /><input type="hidden" name="download_url" value="'
					. esc_url( $download_url ) . '" /><button type="submit" name="delete-backup" class="button button-primary" title="Download Backup Export..."><i class="dashicons dashicons-download"></i> Download Backup Export</button></form>'
				);
			}

			exit;
		}
	}

	$bkpc = new BKPC_Download_Backup();
	$bkpc->init();
}
