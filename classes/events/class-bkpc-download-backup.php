<?php

namespace BKPC\Backup_Copilot;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Download_Backup' ) ) {

	class BKPC_Download_Backup extends Backup_Copilot {
		public function __construct() {
			parent::__construct();
			
			//Core
			$this->mu  = new BKPC_Multisite; 
			$this->fs  = new BKPC_FS;      
			$this->zip = new BKPC_Zip;
		}

		public function init() {
			add_action( 'wp_loaded', array($this, 'on_loaded' ) );
		}

		public function on_loaded() {
			add_action( 'admin_menu', array( $this, 'add_admin_action' ) );
			add_action( 'wp_ajax_download_backup', array( $this, 'download_backup' ) );
		}

		public function add_admin_action() {
			add_submenu_page( null, '', '', 'manage_options', 'download_backup', 'download_backup', null );
		}

		public function download_backup($uuid = '', $ajax = true) {
			$uuid              = $uuid ?: sanitize_text_field( $_REQUEST['uuid'] );
			$backup_dir        = $this->settings['bkps_path'] . $uuid . DIRECTORY_SEPARATOR;
			$progress_filename = $backup_dir . 'progress.txt';
			$zip_filepath      = $backup_dir . $uuid . '.zip';
			$download_url      = $this->mu->get_mu_download_url($zip_filepath);

			if ( ! file_exists( $zip_filepath ) ) {
				$this->fs->create_file( $progress_filename, 'Generating full backup for download...', true );
				$this->zip->create_zip_archive( $backup_dir, $zip_filepath, [], true );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			} 

			sleep(1);

			$this->fs->remove_file( $progress_filename );

			if ( $ajax ) {
				echo wp_json_encode( 'Backup full download was generated successfully! <strong><a href="' . $download_url . '">Download Full Backup...</a></strong>' );
			} else {
				echo wp_json_encode( 'Backup export was generated and is ready to be downloaded!<br /><form id="delete-backup" method="post" action="<?php echo esc_url(admin_url()); ?>admin.php?page=delete_backup"><input type="hidden" name="uuid" value="' . $uuid . '" /><input type="hidden" name="download_url" value="' . $download_url . '" /><button type="submit" name="delete-backup" class="button button-primary" title="Download Backup Export..."><i class="dashicons dashicons-download"></i> Download Backup Export</button></form>' );
			}

			exit;
		}
	}

	$bkpc = new BKPC_Download_Backup;
	$bkpc->init();
}
