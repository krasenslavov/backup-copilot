<?php

namespace BKPC\Backup_Copilot;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Zip' ) ) {

	class BKPC_Zip extends Backup_Copilot {
		function __construct() {
			parent::__construct();

			$this->mu = new BKPC_Multisite;
		}

		public function create_zip_archive( $wpc_dir, $zip_filename, $options = [], $backup_dir = false ) {
			if ( ! is_dir( $wpc_dir ) ) {
				return false;
			}

			if ( ! $backup_dir ) {
				$wpc_dir = $this->mu->archive_mu_uploads_dir( $wpc_dir, get_current_blog_id() );
			}

			// Create archive with PHP `ZipArchive` extension
			if ( class_exists( 'ZipArchive' ) ) {
				$zip = new \ZipArchive();

				if ( file_exists( $zip_filename ) ) {
					$zip->open( $zip_filename, \ZipArchive::FL_ENC_UTF_8 );
				} else {
					$zip->open( $zip_filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE | \ZipArchive::FL_ENC_UTF_8 );
				}

				$files = new \RecursiveIteratorIterator(
					new \RecursiveDirectoryIterator( $wpc_dir ),
					\RecursiveIteratorIterator::LEAVES_ONLY
				);

				foreach ( $files as $file ) {
					$abs_path      = $file->getRealPath();
					$relative_path = substr( $abs_path, strlen( $wpc_dir ) );

					// Don't ever include .htaccess and wp-config.php for export
					if ( $backup_dir ) {
						if ( strpos( $abs_path, '.htaccess' ) !== false || strpos( $abs_path, 'wp-config.php' ) !== false ) 
							continue;
					}

					if ( strpos( $abs_path, 'themes' ) !== false && ! in_array( 'themes', $options ) ) 
						continue;

					if ( strpos( $abs_path, 'plugins' ) !== false && ! in_array( 'plugins', $options ) ) 
						continue;

					if ( strpos( $abs_path, 'mu-plugins' ) !== false && ! in_array( 'mu-plugins', $options ) ) 
						continue;

					if ( strpos( $abs_path, 'uploads' ) !== false && ! in_array( 'uploads', $options ) ) 
						continue;

					if ( strpos( $abs_path, 'backups' ) !== false && ! in_array( 'backups', $options ) ) 
						continue;

					if ( strpos( $abs_path, 'cache' ) !== false && ! in_array( 'cache', $options ) ) 
						continue;

					if ( ! $file->isDir() ) {
						$zip->addFile( $abs_path, $relative_path );
					} else {
						if ( $relative_path !== false ) {
							$zip->addEmptyDir( $relative_path );
						}
					}
				}

				return $zip->close();
			}

			// Alt: Create archive under Unix with `zip` command
			exec( 'zip --help', $output );

			if ( $output ) {
				exec( 'zip -r ' . $zip_filename . ' ' . $wpc_dir, $output );

				if ( $output ) {
					return true;
				}
			}

			return false;
		}
	}
}
