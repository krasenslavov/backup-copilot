<?php

namespace BKPC\Backup_Copilot;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Create_Backup' ) ) {

	class BKPC_Create_Backup extends Backup_Copilot {
		public function __construct() {
			parent::__construct();

			// Core
			$this->fs       = new BKPC_FS;
			$this->db       = new BKPC_DB;
			$this->mu       = new BKPC_Multisite;
			$this->zip      = new BKPC_Zip;
			$this->utils    = new BKPC_Utils;
			// Events
			$this->delete   = new BKPC_Delete_Backup;
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
			wp_localize_script( 'backup_copilot', 'bkpc_create_backup', 
				array(
					'name' => $this->settings['db_name'],
					'url'  => esc_url( home_url( '/' ) ) . str_replace( ABSPATH, '', $this->settings['bkps_path'] ),
				)
			);
		}

		public function create_backup( $uuid = '', $ajax = true, $export = false ) {
			$uuid              = $uuid ?: sanitize_text_field( $_REQUEST['uuid'] );
			$notes             = sanitize_text_field( $_REQUEST['notes'] );
			$advanced_options  = $this->utils->sanitize_text_field_array( $_REQUEST['advanced_options'] );

			if ( is_multisite() ) {
				$advanced_options = array(
					'uploads',
					'content',
					'database',
				);
			}

			if ( $export ) {
				$find_text         = $this->utils->sanitize_text_field_array( $_REQUEST['find_text'] );
				$replace_with_text = $this->utils->sanitize_text_field_array( $_REQUEST['replace_with_text'] );
			}

			$wpc_dir            = $this->settings['wpc_path'];
			$backup_dir         = $this->settings['bkps_path'] . $uuid . DIRECTORY_SEPARATOR;
			$zip_filename       = $backup_dir . $this->settings['db_name'] . '.zip';
			$notes_filename     = $backup_dir . 'notes.txt';
			$progress_filename  = $backup_dir . 'progress.txt';

			$this->max_backup_size( $uuid, $wpc_dir );

			$this->fs->create_directory( $backup_dir );
			$this->fs->create_file( $progress_filename, 'Creating backup directory...', true );
			$this->fs->create_file( $progress_filename, '[Done]', true );

			if ( ! empty( $notes ) ) {
				$this->fs->create_file( $progress_filename, 'Adding notes...', true );
				$this->fs->create_file( $notes_filename, $notes );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			if ( in_array( 'htaccess', $advanced_options ) && ! $export ) {
				$this->fs->create_file( $progress_filename, 'Copying .htaccess file...', true );
				$this->fs->copy_file( ABSPATH . '.htaccess', $backup_dir . '.htaccess' );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			if ( in_array( 'wpconfig', $advanced_options ) && ! $export ) {
				$this->fs->create_file( $progress_filename, 'Copying wp-config.php file...', true );
				$this->fs->copy_file( ABSPATH . 'wp-config.php', $backup_dir . 'wp-config.php' );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			$this->mu->add_mu_option( $uuid );

			if ( in_array( 'database', $advanced_options ) ) {
				$this->fs->create_file( $progress_filename, 'Saving database...', true );

				if ( $export ) {
					$this->db->create_db_archive( $backup_dir, $advanced_options, $find_text, $replace_with_text );
				} else {
					$this->db->create_db_archive( $backup_dir, $advanced_options );
				}

				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			if ( in_array( 'content', $advanced_options ) ) {
				$this->fs->create_file( $progress_filename, 'Creating content archive... ', true );
				$this->zip->create_zip_archive( $wpc_dir, $zip_filename, $advanced_options );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			sleep( 1 );

			if ( ! $export ) {
				$this->fs->remove_file( $progress_filename );
			}
			
			$this->max_backup_size( $uuid, $backup_dir, true );

			if ($ajax) {
				echo wp_json_encode( 'New backup point was created successfully!' );
				exit;
			}
		}

		public function max_backup_size( $uuid, $path, $delete = false ) {
			// Maximum backup size: 500MB
			$backup_max_size       = 0.5 * ( 1024 * 1024 * 1024 ); 
			$backup_limit_exceeded = false;
			// With 60% average zip compression ratio 
			// wp-content directory size cannot exceed ~800MB
			$wpc_dir_max_size      = $backup_max_size + ( $backup_max_size * 0.6 ); 

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

				echo wp_json_encode( 'New backup point failed! You are over the maximum backup limit size!' );
				exit;
			}
		}
	}

	$bkpc = new BKPC_Create_Backup;
	$bkpc->init();
}
