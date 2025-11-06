<?php

namespace BKPC\Backup_Copilot;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Upload_Backup' ) ) {

	class BKPC_Upload_Backup extends Backup_Copilot {
		public function __construct() {
			parent::__construct();

			// Core
			$this->fs     = new BKPC_FS;
			$this->mu     = new BKPC_Multisite;
			$this->utils  = new BKPC_Utils;
			// Events
			$this->delete = new BKPC_Delete_Backup;
		}

		public function init() 
		{
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		public function on_loaded() {
			add_action( 'admin_menu', array( $this, 'add_admin_action' ) );
			add_action( 'wp_ajax_upload_backup', array( $this, 'upload_backup' ) );
		}

		public function add_admin_action() {
			add_submenu_page( null, '', '', 'manage_options', 'upload_backup', 'upload_backup', null );
		}

		public function upload_backup() {
			$file_type  = $_FILES['file']['type'];
			$file_error = $_FILES['file']['error'];
			$file_name  = $_FILES['file']['name'];
			$file_path  = $_FILES['file']['tmp_name'];
			$file_size  = $_FILES['file']['size'];


			$accepted_file_types = array(
				'application/zip',
				'multipart/x-zip',
				'application/x-zip-compressed', 
				'application/octet-stream',
			);

			if ( $file_error === UPLOAD_ERR_OK && in_array( $file_type ,$accepted_file_types ) ) {
				$uuid              = str_replace( '.zip', '', $file_name );
				$backup_dir        = $this->settings['bkps_path'] . $uuid;
				$progress_filename = $backup_dir . 'progress.txt';

				if ( ceil( $file_size / ( 1024 * 1024 ) ) > 500 ) {
					echo wp_json_encode( 'Backup point upload failed! You are over the maximum backup limit size!' );
					exit;
				}

				if ( ! is_dir( $this->settings['bkps_path'] ) ) {
					$this->fs->create_directory( $this->settings['bkps_path'] );
				}

				if ( ! is_dir( $backup_dir ) ) {
					$this->fs->create_directory( $backup_dir );
				} else {
					// When we don't have have .sql and .zip files 
					// this means that we have a leftover backup after export.
					if ( ! file_exists( $backup_dir . DIRECTORY_SEPARATOR . $this->settings['db_name'] . '.sql' ) && ! file_exists( $backup_dir . DIRECTORY_SEPARATOR . $this->settings['db_name'] . '.zip' ) ) {
						// Quitely delete leftover backup directory
						$this->delete->delete_backup( $uuid, false ); 
					} else {
						echo wp_json_encode( 'Nothing is uploaded. Backup point already exists!' );
						exit;
					}
				}
				
				$this->fs->create_file( $progress_filename, 'Creating backup directory...', true );
				$this->fs->create_file( $progress_filename, '[Done]', true );

				if ( class_exists( 'ZipArchive' ) ) {
					// Extract archive with PHP `ZipArchive` extension
					$zip = new \ZipArchive;
					$res = $zip->open( $file_path );

					if ($res === true) {
						$this->fs->create_file( $progress_filename, 'Extracting archive content...', true );
						$zip->extractTo( $backup_dir );
						$zip->close();
						$this->fs->create_file( $progress_filename, '[Done]', true );
					} else {
						$this->delete->delete_backup( $uuid, false );
						echo wp_json_encode( 'Unzip failed! Double check if zip file is not corrupted!' );
						exit;
					}
				} else {
					// Create archive Unix `unzip` command
					exec( 'unzip --help', $output );
					if ( $output ) {
						exec( 'unzip ' . $file_path . ' -d ' . $backup_dir, $output );
					}
				}

				$this->mu->add_mu_option( $uuid );
				$this->fs->remove_file( $progress_filename );

				echo wp_json_encode( 'Backup point was uploaded successfully!' );
			} else {
				echo wp_json_encode( 'Backup point upload failed! Invalid file or archive type!' );
			}

			exit;
		}
	}

	$bkpc = new BKPC_Upload_Backup;
	$bkpc->init();
}
