<?php

namespace BKPC\Backup_Copilot;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_DB' ) ) {

	class BKPC_DB extends Backup_Copilot {
		public function __construct() {
			parent::__construct();

			$this->mu = new BKPC_Multisite;
		}

		public function create_db_archive( $backup_dir, $advanced_options = [], $find = [], $replace_with = [] ) {
			try {
				$url_regexp = '/^http:\/\/|(www\.)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/';

				$settings = array(
					'compress'            => \Ifsnop\Mysqldump\Mysqldump::NONE, // [GZIP, BZIP2, NONE]
					'no-data'             => false,
					'add-drop-table'      => true,
					'single-transaction'  => true,
					'lock-tables'         => false,
					'add-locks'           => true,
					'extended-insert'     => true
				);

				$settings = $this->mu->include_mu_site_tables( $settings, get_current_blog_id() );
				
				$dump = new \Ifsnop\Mysqldump\Mysqldump(
					'mysql:host=' . $this->settings['db_hostname'] . ';' .'dbname=' . $this->settings['db_name'], 
						$this->settings['db_user'], 
							$this->settings['db_password'], 
								$settings
				);

				$sql_file_path = $backup_dir . $this->settings['db_name'] . '.sql';

				$dump->start( $sql_file_path );

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
				echo esc_textarea( 'MySQLDump connection failed: ' . $error->getMessage() );
			}
		}
	}
}
