<?php

namespace BKPC\Backup_Copilot;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Delete_Backup' ) ) {

	class BKPC_Delete_Backup extends Backup_Copilot {
		public function __construct() {
			parent::__construct();

			// Core
			$this->mu = new BKPC_Multisite;
		}

		public function init() {
			add_action( 'wp_loaded', array($this, 'on_loaded' ) );
		}

		public function on_loaded() {
			add_action( 'admin_menu', array($this, 'add_admin_action' ) );
			add_action( 'wp_ajax_delete_backup', array($this, 'delete_backup' ) );
		}

		public function add_admin_action() {
			add_submenu_page( null, '', '', 'manage_options', 'delete_backup', 'delete_backup', null );
		}

		public function delete_backup( $uuid = '', $ajax = true ) {
			$uuid              = $uuid ?: sanitize_text_field( $_REQUEST['uuid'] );
			$backup_dir        = $this->settings['bkps_path'] . $uuid;

			$this->delete_backup_files($backup_dir);
			$this->mu->delete_mu_option($uuid);   

			if ( $ajax ) {
				echo wp_json_encode( 'Backup point was deleted successfully!' );
				exit;
			}
		}

		private function delete_backup_files( $backup_dir ) {
			if ( ! is_dir( $backup_dir ) ) {
				return false;
			}

			$files = scandir( $backup_dir );

			foreach ( $files as $file ) {
				if ( $file !== '.' && $file !== '..' ) {
					$absolute_path = $backup_dir . DIRECTORY_SEPARATOR . $file;
					if ( is_dir( $absolute_path ) && ! is_link($absolute_path ) ) {
						$this->delete_backup_files( $absolute_path );
					} else {
						unlink( $absolute_path );
					}
				}
			}

			return rmdir( $backup_dir );
		}
	}

	$bkpc = new BKPC_Delete_Backup;
	$bkpc->init();
}
