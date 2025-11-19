<?php
/**
 * Handles the backup creation process including database exports,
 * file archiving, and backup size validation.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      0.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_Create_Backup' ) ) {
	class BKPC_Create_Backup {
		private $fs;
		private $db;
		private $zip;
		private $mu;
		private $utils;
		private $delete;

		public function __construct() {

			// Core
			$this->fs    = new BKPC_FS();
			$this->db    = new BKPC_DB();
			$this->mu    = new BKPC_Multisite();
			$this->zip   = new BKPC_Zip();
			$this->utils = new BKPC_Utils();
			// Events
			$this->delete = new BKPC_Delete_Backup();
		}

		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		public function on_loaded() {
			add_action( 'admin_menu', array( $this, 'add_admin_action' ) );
			add_action( 'wp_ajax_create_backup', array( $this, 'create_backup' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'localize_progress_notice' ) );
		}

		public function add_admin_action() {
			add_submenu_page( null, '', '', 'manage_options', 'create_backup', 'create_backup', null );
		}

		public function localize_progress_notice() {
			wp_localize_script(
				'bkpc-admin',
				'bkpc_create_backup',
				array(
					'name' => BKPC_DB_NAME,
					'url'  => esc_url( home_url( '/' ) ) . str_replace( ABSPATH, '', BKPC_PLUGIN_BACKUP_DIR_PATH ),
				)
			);
		}

		public function create_backup( $uuid = '', $ajax = true, $export = false ) {
			// Verify nonce and capabilities for AJAX requests.
			if ( $ajax ) {
				if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'bkpc_ajax_nonce' ) ) {
					wp_send_json_error( esc_html__( 'Security check failed!', 'backup-copilot' ) );
				}

				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( esc_html__( 'Insufficient permissions!', 'backup-copilot' ) );
				}
			}

			$uuid = $uuid ? sanitize_text_field( $uuid ) : ( isset( $_REQUEST['uuid'] )
				? sanitize_text_field( wp_unslash( $_REQUEST['uuid'] ) ) : '' );

			$notes = isset( $_REQUEST['notes'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['notes'] ) ) : '';

			$advanced_options = isset( $_REQUEST['advanced_options'] )
				? $this->utils->sanitize_text_field_array( wp_unslash( $_REQUEST['advanced_options'] ) )
				: array();

			if ( is_multisite() ) {
				$advanced_options = array(
					'uploads',
					'content',
					'database',
				);
			}

			if ( $export ) {
				$find_text = isset( $_REQUEST['find_text'] )
					? $this->utils->sanitize_text_field_array( wp_unslash( $_REQUEST['find_text'] ) )
					: array();

				$replace_with_text = isset( $_REQUEST['replace_with_text'] )
					? $this->utils->sanitize_text_field_array( wp_unslash( $_REQUEST['replace_with_text'] ) )
					: array();
			}

			$wpc_dir           = BKPC_PLUGIN_WPCONTENT_DIR_PATH;
			$backup_dir        = trailingslashit( BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid );
			$zip_filename      = $backup_dir . $uuid . '.zip';
			$notes_filename    = $backup_dir . 'notes.txt';
			$progress_filename = $backup_dir . 'progress.txt';

			$this->max_backup_size( $uuid, $wpc_dir );

			$this->fs->create_directory( $backup_dir );
			$this->fs->create_file( $progress_filename, 'Creating backup directory...', true );
			$this->fs->create_file( $progress_filename, '[Done]', true );

			if ( ! empty( $notes ) ) {
				$this->fs->create_file( $progress_filename, 'Adding notes...', true );
				$this->fs->create_file( $notes_filename, $notes );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			if ( in_array( 'htaccess', $advanced_options, true ) && ! $export ) {
				$this->fs->create_file( $progress_filename, 'Copying .htaccess file...', true );
				$this->fs->copy_file( ABSPATH . '.htaccess', $backup_dir . '.htaccess' );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			if ( in_array( 'wpconfig', $advanced_options, true ) && ! $export ) {
				$this->fs->create_file( $progress_filename, 'Copying wp-config.php file...', true );
				$this->fs->copy_file( ABSPATH . 'wp-config.php', $backup_dir . 'wp-config.php' );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			$this->mu->add_mu_option( $uuid );

			if ( in_array( 'database', $advanced_options, true ) ) {
				$this->fs->create_file( $progress_filename, 'Saving database...', true );

				if ( $export ) {
					$this->db->create_db_archive( $backup_dir, $advanced_options, $find_text, $replace_with_text );
				} else {
					$this->db->create_db_archive( $backup_dir, $advanced_options );
				}

				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			// Create zip if ANY content-related option is selected.
			$content_options = array( 'content', 'themes', 'plugins', 'mu-plugins', 'uploads', 'cache', 'backups' );
			$has_content     = ! empty( array_intersect( $content_options, $advanced_options ) );

			if ( $has_content ) {
				$this->fs->create_file( $progress_filename, 'Creating content archive... ', true );
				$this->zip->create_zip_archive( $wpc_dir, $zip_filename, $advanced_options );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			// sleep( 1 );

			if ( ! $export ) {
				$this->fs->remove_file( $progress_filename );
			}

			$this->max_backup_size( $uuid, $backup_dir, true );

			if ( $ajax ) {
				wp_send_json_success( esc_html__( 'New backup point was created successfully!', 'backup-copilot' ) );
			}
		}

		public function max_backup_size( $uuid, $path, $delete = false ) {
			// Maximum backup size: 500MB (defined in main plugin file)
			$backup_max_size       = 500 * 1024 * 1024;
			$backup_limit_exceeded = false;
			// With 60% average zip compression ratio
			// wp-content directory size cannot exceed ~800MB
			$wpc_dir_max_size = $backup_max_size + ( $backup_max_size * 0.6 );

			if ( ! $uuid ) {
				return false;
			}

			if ( $this->utils->get_dir_size( $path, true ) > $wpc_dir_max_size ) {
				$backup_limit_exceeded = true;
			}

			if ( $backup_limit_exceeded ) {
				if ( $delete ) {
					$this->delete->delete_backup( $uuid );
				}

				wp_send_json_error( esc_html__( 'New backup point failed! You are over the maximum backup limit size!', 'backup-copilot' ) );
			}
		}
	}

	$bkpc_create_backup = new BKPC_Create_Backup();
	$bkpc_create_backup->init();
}
