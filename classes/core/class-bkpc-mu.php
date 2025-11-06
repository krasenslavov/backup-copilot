<?php

namespace BKPC\Backup_Copilot;

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

			if ( $blog_id !== 1 ) {
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

			if ( $blog_id !== 1 ) {
				$wpc_dir .= 'uploads' . DIRECTORY_SEPARATOR . 'sites' . DIRECTORY_SEPARATOR . $blog_id . DIRECTORY_SEPARATOR;
			}

			return $wpc_dir;
		}

		public function get_mu_download_url( $zip_filepath ) {
			if ( ! is_multisite() )  {
				return esc_url( home_url( '/' ) ) . str_replace( ABSPATH, '', $zip_filepath );
			}
		
			return esc_url( network_site_url( '/' ) ) . str_replace( ABSPATH, '', $zip_filepath );
		}
	}
}
