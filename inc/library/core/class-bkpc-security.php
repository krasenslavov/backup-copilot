<?php
/**
 * Handles security measures for backup directories including
 * preventing direct access via .htaccess and index.php files.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_Security' ) ) {
	class BKPC_Security {
		private $fs;
		private $backup_dir;

		public function __construct() {
			$this->fs         = new BKPC_FS();
			$this->backup_dir = BKPC_PLUGIN_BACKUP_DIR_PATH;
		}

		/**
		 * Secure the backup directory by creating protection files.
		 *
		 * Creates .htaccess to prevent direct access and index.php to prevent directory listing.
		 */
		public function secure_backup_directory() {
			// Ensure backup directory exists
			if ( ! is_dir( $this->backup_dir ) ) {
				$this->fs->create_directory( $this->backup_dir );
			}

			// Create .htaccess to prevent direct access
			$this->create_htaccess_protection();

			// Create index.php to prevent directory listing
			$this->create_index_protection();
		}

		/**
		 * Create .htaccess file in backup directory to deny all access.
		 */
		private function create_htaccess_protection() {
			$htaccess_file = trailingslashit( $this->backup_dir ) . '.htaccess';

			// Don't overwrite if it already exists
			if ( file_exists( $htaccess_file ) ) {
				return;
			}

			$htaccess_content  = "# Backup Copilot - Deny all direct access\n";
			$htaccess_content .= "Order deny,allow\n";
			$htaccess_content .= "Deny from all\n";

			$this->fs->create_file( $htaccess_file, $htaccess_content );
		}

		/**
		 * Create index.php file in backup directory to prevent directory listing.
		 */
		private function create_index_protection() {
			$index_file = trailingslashit( $this->backup_dir ) . 'index.php';

			// Don't overwrite if it already exists
			if ( file_exists( $index_file ) ) {
				return;
			}

			$index_content  = "<?php\n";
			$index_content .= "// Silence is golden.\n";

			$this->fs->create_file( $index_file, $index_content );
		}

		/**
		 * Generate a secure UUID for backup operations.
		 *
		 * Returns a Unix timestamp as the UUID, which serves as both a unique
		 * identifier and a timestamp for the backup. This allows the UUID to be
		 * used with wp_date() for displaying backup creation times.
		 *
		 * @return int Unix timestamp (seconds since epoch).
		 */
		public function generate_secure_uuid() {
			return time();
		}
	}
}
