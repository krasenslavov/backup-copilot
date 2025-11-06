<?php

namespace BKPC\Backup_Copilot;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Export_Backup' ) ) {

	class BKPC_Export_Backup extends Backup_Copilot {
		public function __construct() {
			parent::__construct();

			// Core
			$this->fs       = new BKPC_FS;    
			// Events
			$this->create   = new BKPC_Create_Backup;
			$this->download = new BKPC_Download_Backup;
		}

		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		public function on_loaded() {
			add_action( 'admin_menu', array( $this, 'add_admin_action' ) );
			add_action( 'wp_ajax_export_backup', array( $this, 'export_backup' ) );
		}

		public function add_admin_action() {
			add_submenu_page( null, '', '', 'manage_options', 'export_backup', 'export_backup', null );
		}

		public function export_backup() {
			$uuid              = sanitize_text_field( $_REQUEST['uuid'] );
			$backup_dir        = $this->settings['bkps_path'] . $uuid . DIRECTORY_SEPARATOR;
			$progress_filename = $backup_dir . 'progress.txt';

			$this->create->create_backup( $uuid, false, true );
			$this->download->download_backup( $uuid, false );
			$this->fs->remove_file( $progress_filename );
			
			exit;
		}
	}

	$bkpc = new BKPC_Export_Backup;
	$bkpc->init();
}
