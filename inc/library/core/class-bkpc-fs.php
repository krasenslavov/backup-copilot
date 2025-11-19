<?php
/**
 * Handles all file system operations including file creation,
 * copying, removal, and directory management.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      0.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_FS' ) ) {
	class BKPC_FS {
		public function __construct() {}

		/**
		 * Copy a file from input to output path with validation.
		 */
		public function copy_file( $input_path, $output_path ) {
			if ( ! $this->validate_path( $input_path ) || ! $this->validate_path( $output_path ) ) {
				return false;
			}

			if ( ! file_exists( $input_path ) || file_exists( $output_path ) ) {
				return false;
			}

			if ( copy( $input_path, $output_path ) ) {
				chmod( $output_path, 0644 );
				return true;
			}

			return false;
		}

		/**
		 * Create a file with specified content.
		 */
		public function create_file( $path, $content, $flags = false ) {
			if ( ! $this->validate_path( $path ) ) {
				return false;
			}

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

		/**
		 * Remove a file from the filesystem.
		 */
		public function remove_file( $path ) {
			if ( ! $this->validate_path( $path ) ) {
				return false;
			}

			if ( ! file_exists( $path ) ) {
				return false;
			}

			if ( unlink( $path ) ) {
				return true;
			}
		}

		/**
		 * Append content from input file to output file.
		 */
		public function append_file( $input_path, $output_path ) {
			if ( ! $this->validate_path( $input_path ) || ! $this->validate_path( $output_path ) ) {
				return false;
			}

			if ( strpos( file_get_contents( $output_path ), 'BackupCopilot' ) !== false ) {
				return false;
			}

			file_put_contents( $output_path, file_get_contents( $input_path ), FILE_APPEND );

			return true;
		}

		/**
		 * Remove content from output file that matches input file content.
		 */
		public function deduct_file( $input_path, $output_path ) {
			if ( ! $this->validate_path( $input_path ) || ! $this->validate_path( $output_path ) ) {
				return false;
			}

			if ( ! file_exists( $input_path ) || ! file_exists( $output_path ) ) {
				return false;
			}

			$input_data  = implode( PHP_EOL, file( $input_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) );
			$output_data = implode( PHP_EOL, file( $output_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) );

			file_put_contents( $output_path, str_replace( $input_data, '', $output_data ) );

			return true;
		}

		/**
		 * Create a directory with proper permissions.
		 */
		public function create_directory( $path ) {
			if ( ! $this->validate_path( $path ) ) {
				return false;
			}

			if ( is_dir( $path ) ) {
				return false;
			}

			if ( mkdir( $path, 0755 ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Validate that a path is within allowed directories (backup dir, wp-content, or ABSPATH).
		 */
		private function validate_path( $path ) {
			$real_path = realpath( $path );

			// If realpath returns false, the path doesn't exist yet - validate parent.
			if ( false === $real_path ) {
				$parent = dirname( $path );
				if ( file_exists( $parent ) ) {
					$real_path = realpath( $parent ) . DIRECTORY_SEPARATOR . basename( $path );
				} else {
					return false;
				}
			}

			// Define allowed base directories.
			$allowed_bases = array(
				realpath( BKPC_PLUGIN_BACKUP_DIR_PATH ),
				realpath( BKPC_PLUGIN_WPCONTENT_DIR_PATH ),
				realpath( ABSPATH ),
			);

			// Check if path starts with any allowed base.
			foreach ( $allowed_bases as $base ) {
				if ( false !== $base && 0 === strpos( $real_path, $base ) ) {
					return true;
				}
			}

			return false;
		}
	}
}
