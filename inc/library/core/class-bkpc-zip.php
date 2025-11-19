<?php
/**
 * Handles all ZIP archive operations including creating archives
 * from wp-content directory with selective file inclusion.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      0.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_Zip' ) ) {
	class BKPC_Zip {
		private $mu;

		function __construct() {
			$this->mu = new BKPC_Multisite();
		}

		/**
		 * Create a ZIP archive from the specified directory with optional file filtering.
		 */
		public function create_zip_archive( $wpc_dir, $zip_filename, $options = array(), $backup_dir = false ) {
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

					// Don't ever include .htaccess and wp-config.php for export.
					if ( $backup_dir ) {
						if ( false !== strpos( $abs_path, '.htaccess' )
							|| false !== strpos( $abs_path, 'wp-config.php' ) ) {
							continue;
						}
					}

					// If 'content' is selected, include all wp-content folders.
					// Otherwise, only include specifically selected folders.
					if ( ! in_array( 'content', $options, true ) ) {
						if ( false !== strpos( $abs_path, 'themes' )
							&& ! in_array( 'themes', $options, true ) ) {
							continue;
						}

						if ( false !== strpos( $abs_path, 'plugins' )
							&& ! in_array( 'plugins', $options, true ) ) {
							continue;
						}

						if ( false !== strpos( $abs_path, 'mu-plugins' )
							&& ! in_array( 'mu-plugins', $options, true ) ) {
							continue;
						}

						if ( false !== strpos( $abs_path, 'uploads' )
							&& ! in_array( 'uploads', $options, true ) ) {
							continue;
						}

						if ( false !== strpos( $abs_path, 'backups' )
							&& ! in_array( 'backups', $options, true ) ) {
							continue;
						}

						if ( false !== strpos( $abs_path, 'cache' )
							&& ! in_array( 'cache', $options, true ) ) {
							continue;
						}
					}

					if ( ! $file->isDir() ) {
						$zip->addFile( $abs_path, $relative_path );
					} else {
						if ( false !== $relative_path ) {
							$zip->addEmptyDir( $relative_path );
						}
					}
				}

				return $zip->close();
			}

			// Alt: Create archive under Unix with `zip` command.
			exec( 'zip --help', $output );

			if ( $output ) {
				exec( 'zip -r ' . escapeshellarg( $zip_filename ) . ' ' . escapeshellarg( $wpc_dir ), $output );

				if ( $output ) {
					return true;
				}
			}

			return false;
		}
	}
}
