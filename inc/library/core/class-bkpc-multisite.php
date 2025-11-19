<?php
/**
 * Handles WordPress Multisite-specific functionality including
 * site-specific backups, table filtering, and URL management.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      0.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Multisite' ) ) {
	class BKPC_Multisite {
		public function __construct() {}

		/**
		 * Add multisite option to track which blog a backup belongs to.
		 * Stores in the main site's options table so it's accessible from anywhere.
		 */
		public function add_mu_option( $uuid ) {
			if ( ! is_multisite() ) {
				return;
			}

			$current_blog_id = get_current_blog_id();

			// Switch to main site to store the option there.
			switch_to_blog( 1 );

			// Use a prefixed option name to avoid collisions with numeric UUIDs.
			$option_name = 'bkpc_backup_blog_' . $uuid;

			if ( ! get_option( $option_name ) ) {
				add_option( $option_name, $current_blog_id );
			}

			// Switch back to current site.
			restore_current_blog();
		}

		/**
		 * Get the blog ID for a backup.
		 * Reads from the main site's options table.
		 *
		 * @param string $uuid Backup UUID.
		 * @return int|false Blog ID if found, false otherwise.
		 */
		public function get_mu_option( $uuid ) {
			if ( ! is_multisite() ) {
				return false;
			}

			// Switch to main site to read the option from there.
			switch_to_blog( 1 );

			$option_name = 'bkpc_backup_blog_' . $uuid;
			$blog_id     = get_option( $option_name );

			// Backward compatibility: Check for old-style option (without prefix).
			if ( false === $blog_id ) {
				$blog_id = get_option( $uuid );
				// If found, migrate to new format.
				if ( false !== $blog_id ) {
					add_option( $option_name, $blog_id );
					delete_option( $uuid );
				}
			}

			// Switch back to current site.
			restore_current_blog();

			return $blog_id;
		}

		/**
		 * Delete multisite option for a backup.
		 * Removes from the main site's options table.
		 */
		public function delete_mu_option( $uuid ) {
			if ( ! is_multisite() ) {
				return;
			}

			// Switch to main site to delete the option from there.
			switch_to_blog( 1 );

			$option_name = 'bkpc_backup_blog_' . $uuid;

			// Delete new-style option.
			if ( get_option( $option_name ) ) {
				delete_option( $option_name );
			}

			// Backward compatibility: Also delete old-style option if it exists.
			if ( get_option( $uuid ) ) {
				delete_option( $uuid );
			}

			// Switch back to current site.
			restore_current_blog();
		}

		/**
		 * Include only site-specific tables for multisite backups.
		 */
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

		/**
		 * Adjust uploads directory path for multisite installations.
		 */
		public function archive_mu_uploads_dir( $wpc_dir, $blog_id ) {
			if ( ! is_multisite() ) {
				return $wpc_dir;
			}

			if ( 1 !== $blog_id ) {
				$wpc_dir .= 'uploads/sites/' . $blog_id . '/';
			}

			return $wpc_dir;
		}

		/**
		 * Generate secure download URL for multisite backup files.
		 */
		public function get_mu_download_url( $zip_filepath ) {
			// Use secure download handler instead of direct file access.
			$download_url = add_query_arg(
				array(
					'action' => 'secure_download',
					'file'   => urlencode( $zip_filepath ),
					'nonce'  => wp_create_nonce( 'bkpc_ajax_nonce' ),
				),
				admin_url( 'admin-ajax.php' )
			);

			return esc_url( $download_url );
		}
	}
}
