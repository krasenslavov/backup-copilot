<?php
/**
 * Handles progress tracking for backup operations using simple text files.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_Progress' ) ) {
	class BKPC_Progress {
		private $fs;
		private $progress_filename;

		public function __construct() {
			$this->fs = new BKPC_FS();
		}

		/**
		 * Initialize progress tracking for a backup operation.
		 *
		 * @param string $uuid Backup UUID.
		 */
		public function init( $uuid ) {
			$backup_dir              = trailingslashit( BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid );
			$this->progress_filename = $backup_dir . 'progress.txt';
			$this->fs->remove_file( $this->progress_filename );
		}

		/**
		 * Add a progress message to the tracking file.
		 *
		 * @param string $uuid Backup UUID (unused, kept for API compatibility).
		 * @param string $message Progress message to add.
		 * @param bool   $done Whether this step is completed (appends [Done] if true).
		 */
		public function add( $uuid, $message, $done = false ) {
			if ( $done ) {
				$message .= ' [Done]';
			}
			$this->fs->create_file( $this->progress_filename, $message, true );
		}

		/**
		 * Get current progress data.
		 *
		 * @param string $uuid Backup UUID.
		 * @return string Progress content or empty string if no progress file exists.
		 */
		public function get( $uuid ) {
			$backup_dir        = trailingslashit( BKPC_PLUGIN_BACKUP_DIR_PATH . $uuid );
			$progress_filename = $backup_dir . 'progress.txt';

			if ( ! file_exists( $progress_filename ) ) {
				return '';
			}

			return file_get_contents( $progress_filename );
		}

		/**
		 * Clear progress tracking file.
		 *
		 * @param string $uuid Backup UUID (unused, kept for API compatibility).
		 */
		public function clear( $uuid ) {
			$this->fs->remove_file( $this->progress_filename );
		}
	}
}
