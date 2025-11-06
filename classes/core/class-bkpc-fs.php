<?php
/**
 * Backup Copilot - File System Handler
 *
 * Handles all file system operations including file creation,
 * copying, removal, and directory management.
 *
 * @package    BKPC
 * @subpackage Backup_Copilot/Core
 * @author     Krasen Slavov <hello@krasenslavov.com>
 * @copyright  2025
 * @license    GPL-2.0-or-later
 * @link       https://krasenslavov.com/plugins/backup-copilot/
 * @since      0.1.0
 */

namespace BKPC;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_FS' ) ) {

	class BKPC_FS extends Backup_Copilot {
		public function __construct() {
			parent::__construct();
		}

		public function copy_file( $input_path, $output_path ) {
			if ( ! file_exists( $input_path ) || file_exists( $output_path ) ) {
				return false;
			}

			if ( copy( $input_path, $output_path ) ) {
				chmod( $output_path, 0644 );
				return true;
			}

			return false;
		}

		public function create_file( $path, $content, $flags = false ) {
			if ( file_exists( $path ) && 0 === $flags ) {
				return false;
			}

			if ( $flags ) {
				$flags = FILE_APPEND;
			}

			if ( file_put_contents( $path, $content, $flags ) ) {
				chmod( $path, 0644 );
				return true;
			}
		}

		public function remove_file( $path ) {
			if ( ! file_exists( $path ) ) {
				return false;
			}

			if ( unlink( $path ) ) {
				return true;
			}
		}

		public function append_file( $input_path, $output_path ) {
			if ( strpos( file_get_contents( $output_path ), 'BackupCopilot' ) !== false ) {
				return false;
			}

			file_put_contents( $output_path, file_get_contents( $input_path ), FILE_APPEND );

			return true;
		}

		public function deduct_file( $input_path, $output_path ) {
			if ( ! file_exists( $input_path ) || ! file_exists( $output_path ) ) {
				return false;
			}

			$input_data  = implode( PHP_EOL, file( $input_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) );
			$output_data = implode( PHP_EOL, file( $output_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) );

			file_put_contents( $output_path, str_replace( $input_data, '', $output_data ) );

			return true;
		}

		public function create_directory( $path ) {
			if ( is_dir( $path ) ) {
				return false;
			}

			if ( mkdir( $path, 0755 ) ) {
				return true;
			}

			return false;
		}
	}
}
