<?php
/**
 * Backup Copilot - Database Handler
 *
 * Handles all database operations including database backups,
 * exports, and find/replace functionality.
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

if ( ! class_exists( 'BKPC_DB' ) ) {

	class BKPC_DB extends Backup_Copilot {
		private $mu;

		public function __construct() {
			parent::__construct();

			$this->mu = new BKPC_Multisite();
		}

		public function create_db_archive( $backup_dir, $advanced_options = array(), $find = array(), $replace_with = array() ) {
			try {
				// Use WordPress constants directly for better compatibility.
				if ( ! defined( 'DB_HOST' ) || ! defined( 'DB_NAME' ) || ! defined( 'DB_USER' ) || ! defined( 'DB_PASSWORD' ) ) {
					throw new \Exception( esc_html__( 'Database credentials not loaded', 'backup-copilot' ) );
				}

				$url_regexp = '/^http:\/\/|(www\.)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/';

				$settings = array(
					'compress'           => \Ifsnop\Mysqldump\Mysqldump::NONE, // [GZIP, BZIP2, NONE]
					'no-data'            => false,
					'add-drop-table'     => true,
					'single-transaction' => true,
					'lock-tables'        => false,
					'add-locks'          => true,
					'extended-insert'    => true,
				);

				$settings = $this->mu->include_mu_site_tables( $settings, get_current_blog_id() );

				// Parse DB_HOST to handle LocalWP sockets and ports.
				$db_host = DB_HOST;
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
					$dsn = 'mysql:unix_socket=' . $socket . ';dbname=' . DB_NAME;
				} else {
					$dsn = 'mysql:host=' . $db_host;
					if ( $port ) {
						$dsn .= ';port=' . $port;
					}
					$dsn .= ';dbname=' . DB_NAME;
				}

				$dump = new \Ifsnop\Mysqldump\Mysqldump(
					$dsn,
					DB_USER,
					DB_PASSWORD,
					$settings
				);

				// Get UUID from backup directory path for consistent naming.
				$uuid          = basename( rtrim( $backup_dir, '/\\' ) );
				$sql_file_path = $backup_dir . $uuid . '.sql';

				$dump->start( $sql_file_path );

				// Verify the SQL file was created.
				if ( ! file_exists( $sql_file_path ) || filesize( $sql_file_path ) === 0 ) {
					throw new \Exception( esc_html__( 'SQL file was not created or is empty', 'backup-copilot' ) );
				}

				// Find and Replace URLs for backup export.
				if ( ! empty( $find ) ) {
					foreach ( $find as $id => $find_text ) {
						$replace_text = $replace_with[ $id ];
						if ( '' !== $find_text && '' !== $replace_text ) {
							if ( preg_match( $url_regexp, $find_text ) && preg_match( $url_regexp, $replace_text ) ) {
								file_put_contents( $sql_file_path, str_replace( $find_text, $replace_text, file_get_contents( $sql_file_path ) ) );
							}
						}
					}
				}
			} catch ( \Exception $error ) {
				$error_message = sprintf(
					/* translators: %s: Error message */
					esc_html__( 'Database backup failed: %s', 'backup-copilot' ),
					$error->getMessage()
				);
				wp_die( esc_html( $error_message ) );
			}
		}
	}
}
