<?php
/**
 * Backup Copilot - Restore Backup Event
 *
 * Handles the backup restoration process including database import,
 * file extraction, and content replacement.
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

if ( ! class_exists( 'BKPC_Restore_Backup' ) ) {

	class BKPC_Restore_Backup extends Backup_Copilot {
		private $fs;
		private $db;
		private $mu;
		private $zip;

		public function __construct() {
			parent::__construct();

			// Core
			$this->fs  = new BKPC_FS();
			$this->db  = new BKPC_DB();
			$this->mu  = new BKPC_Multisite();
			$this->zip = new BKPC_Zip();
		}

		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		public function on_loaded() {
			add_action( 'admin_menu', array( $this, 'add_admin_action' ) );
			add_action( 'wp_ajax_restore_backup', array( $this, 'restore_backup' ) );
		}

		public function add_admin_action() {
			add_submenu_page( null, '', '', 'manage_options', 'restore_backup', 'restore_backup', null );
		}

		public function restore_backup() {
			// Verify nonce and capabilities.
			if ( ! isset( $_REQUEST['nonce'] )
				|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'bkpc_ajax_nonce' ) ) {
				echo wp_json_encode( 'Security check failed!' );
				exit;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				echo wp_json_encode( 'Insufficient permissions!' );
				exit;
			}

			$uuid              = sanitize_text_field( wp_unslash( $_REQUEST['uuid'] ) );
			$wp2wpmu           = isset( $_REQUEST['wp2wpmu'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wp2wpmu'] ) ) : '';
			$backup_dir        = trailingslashit( $this->settings['bkps_path'] . $uuid );
			$progress_filename = $backup_dir . 'progress.txt';
			$zip_filepath      = $backup_dir . $uuid . '.zip';

			$this->fs->remove_file( $progress_filename );

			$this->settings['wpc_path'] = $this->mu->archive_mu_uploads_dir( $this->settings['wpc_path'], get_current_blog_id() );

			if ( is_dir( $this->settings['wpc_path'] ) ) {

				if ( ! is_multisite() ) {
					$this->fs->create_file( $progress_filename, 'Deleting wp-content directory...', true );
				} else {
					$this->fs->create_file( $progress_filename, 'Deleting site uploads directory...', true );
				}

				$this->delete_content_files( $this->settings['wpc_path'] );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			if ( ! is_dir( $this->settings['wpc_path'] ) ) {
				$this->fs->create_directory( $this->settings['wpc_path'] );
			}

			if ( file_exists( $backup_dir . '.htaccess' ) ) {
				$this->fs->create_file( $progress_filename, 'Restoring .htaccess file...', true );
				$this->fs->copy_file( $backup_dir . '.htaccess', ABSPATH . '.htaccess' );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			if ( file_exists( $backup_dir . 'wp-config.php' ) ) {
				$this->fs->create_file( $progress_filename, 'Restoring wp-config.php file...', true );
				$this->fs->copy_file( $backup_dir . 'wp-config.php', ABSPATH . 'wp-config.php' );
				$this->fs->create_file( $progress_filename, '[Done]', true );
			}

			try {
				$this->fs->create_file( $progress_filename, 'Restoring database...', true );

				// Parse DB_HOST to handle LocalWP sockets and ports.
				$db_host = $this->settings['db_hostname'];
				$port    = null;
				$socket  = null;

				// Check if host contains unix socket.
				if ( strpos( $db_host, ':' ) !== false && strpos( $db_host, '.sock' ) !== false ) {
					list( $db_host, $socket ) = explode( ':', $db_host, 2 );
				} elseif ( strpos( $db_host, ':' ) !== false ) {
					// Check if host contains port.
					list( $db_host, $port ) = explode( ':', $db_host, 2 );
				}

				// Build DSN based on connection type.
				if ( $socket ) {
					$dsn = 'mysql:unix_socket=' . $socket . ';dbname=' . $this->settings['db_name'];
				} else {
					$dsn = 'mysql:host=' . $db_host;
					if ( $port ) {
						$dsn .= ';port=' . $port;
					}
					$dsn .= ';dbname=' . $this->settings['db_name'];
				}

				$db = new \PDO( $dsn, $this->settings['db_user'], $this->settings['db_password'] ); // phpcs:ignore
				$db->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION ); // phpcs:ignore

				$sql = file_get_contents( $backup_dir . $uuid . '.sql' );

				$db->exec( $sql );

				$this->fs->create_file( $progress_filename, '[Done]', true );
			} catch ( \PDOException $err ) {
				echo wp_json_encode( 'MySQL Connection failed: ' . $err->getMessage() );
				exit;
			}

			if ( class_exists( 'ZipArchive' ) ) {
				// Extract archive with PHP `ZipArchive` extension
				$zip = new \ZipArchive();
				$res = $zip->open( $zip_filepath );

				if ( true === $res ) {
					if ( ! is_multisite() ) {
						$this->fs->create_file( $progress_filename, 'Restoring wp-content directory...', true );
					} else {
						$this->fs->create_file( $progress_filename, 'Restoring site uploads directory...', true );
					}

					$zip->extractTo( $this->settings['wpc_path'] );
					$zip->close();
					$this->fs->create_file( $progress_filename, '[Done]', true );
				}
			} else {
				// Alt: Create archive Unix `unzip` command.
				exec( 'unzip --help', $output );

				if ( $output ) {
					exec( 'unzip ' . escapeshellarg( $zip_filepath ) . ' -d ' . escapeshellarg( $this->settings['wpc_path'] ), $output );
				}
			}

			// sleep( 1 );

			$this->fs->remove_file( $progress_filename );

			echo wp_json_encode( 'Backup point was restored successfully!' );
			exit;
		}

		public function delete_content_files( $path ) {
			if ( ! is_dir( $path ) ) {
				return false;
			}

			$files = scandir( $path );

			foreach ( $files as $file ) {
				if ( '.' !== $file && '..' !== $file ) {
					$absolute_path = trailingslashit( $path ) . $file;
					if ( is_dir( $absolute_path ) && ! is_link( $absolute_path ) ) {
						$this->delete_content_files( $absolute_path );
					} else {
						unlink( $absolute_path );
					}
				}
			}

			rmdir( $path );
		}
	}

	$bkpc = new BKPC_Restore_Backup();
	$bkpc->init();
}
