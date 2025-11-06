<?php
/**
 * Backup Copilot - Multisite Handler
 *
 * Handles WordPress Multisite-specific functionality including
 * site-specific backups, table filtering, and URL management.
 *
 * @package    BKPC
 * @subpackage Backup_Copilot/Core
 * @author     Krasen Slavov <hello@krasenslavov.com>
 * @copyright  2025
 * @license    GPL-2.0-or-later
 * @link       https://krasenslavov.com/plugins/backup-copilot/
 * @since      0.5.0
 */

namespace BKPC;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Multisite' ) ) {

	class BKPC_Multisite extends Backup_Copilot {
		public function __construct() {
			parent::__construct();
		}

		public function add_mu_option( $uuid ) {
			if ( is_multisite() && ! get_option( $uuid ) ) {
				add_option( $uuid, get_current_blog_id() );
			}
		}

		public function delete_mu_option( $uuid ) {
			if ( is_multisite() && get_option( $uuid ) ) {
				delete_option( $uuid );
			}
		}

		public function include_mu_site_tables( $settings, $blog_id ) {
			global $wpdb;

			$include_tables = array();

			if ( ! is_multisite() ) {
				return $settings;
			}

			if ( 1 !== $blog_id ) {
				foreach ( $wpdb->tables as $table_name ) {
					$include_tables[] = $wpdb->prefix . $table_name;
				}

				if ( empty( $include_tables ) ) {
					return $settings;
				}

				$settings['include-tables'] = $include_tables;
			}

			return $settings;
		}

		public function archive_mu_uploads_dir( $wpc_dir, $blog_id ) {
			if ( ! is_multisite() ) {
				return $wpc_dir;
			}

			if ( 1 !== $blog_id ) {
				$wpc_dir .= 'uploads/sites/' . $blog_id . '/';
			}

			return $wpc_dir;
		}

		public function get_mu_download_url( $zip_filepath ) {
			// Convert backslashes to forward slashes for URL compatibility.
			$relative_path = str_replace( ABSPATH, '', $zip_filepath );
			$relative_path = str_replace( '\\', '/', $relative_path );

			if ( ! is_multisite() ) {
				return esc_url( home_url( '/' . $relative_path ) );
			}

			return esc_url( network_site_url( '/' . $relative_path ) );
		}
	}
}
