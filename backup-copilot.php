<?php
/*
Plugin Name: Backup Copilot
Plugin URI: https://krasenslavov.com/plugins/backup-copilot/
Description: Create backup points of your WordPress installation to restore, export, or transfer to another location.
Author: Krasen Slavov
Version: 0.6.2
Author URI: https://krasenslavov.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: backup-copilot
Domain Path: /lang

Copyright 2021-2022 Krasen Slavov (email: hello@krasenslavov.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace BKPC\Backup_Copilot;

! defined( ABSPATH ) || exit;

// development
// ini_set( 'error_reporting', E_ALL | E_STRICT );
// ini_set( 'display_errors', 1 );

// system
// @ini_set( 'upload_max_filesize', '1024M' );
// @ini_set( 'post_max_size', '1025M' );
// @ini_set( 'memory_limit', '512M' );
// @ini_set( 'max_execution_time', 300 );
// @ini_set( 'max_input_time', 600 );

date_default_timezone_set( ini_get( 'date.timezone' ) );

if ( ! class_exists( 'Backup_Copilot' ) ) {

	class Backup_Copilot {
		const DEV_MODE         = false;
		const VERSION          = '0.6.2';
		const PHP_MIN_VERSION  = '7.2';
		const WP_MIN_VERSION   = '5.0';
		const UUID             = 'bkpc';
		const TEXTDOMAIN       = 'backup-copilot';
		const PLUGIN_NAME      = 'Backup Copliot';
		const PLUGIN_DOCURL    = 'https://krasenslavov.com/plugins/backup-copilot/'; 
		const PLUGIN_WPORGURL  = 'https://wordpress.org/support/plugin/backup-copilot/';    
		const PLUGIN_WPORGRATE = 'https://wordpress.org/support/plugin/backup-copilot/reviews/?filter=5';

		var $settings;

		public function __construct() {
			$this->settings = array(
				'dev_mode'         => self::DEV_MODE,
				'version'          => self::VERSION,
				'php_min_version'  => self::PHP_MIN_VERSION,
				'wp_min_version'   => self::WP_MIN_VERSION,
				'uuid'             => self::UUID,
				'textdomain'       => self::TEXTDOMAIN,
				'plugin_name'      => self::PLUGIN_NAME,
				'plugin_docurl'    => self::PLUGIN_DOCURL,
				'plugin_wporgurl'  => self::PLUGIN_WPORGURL,
				'plugin_wporgrate' => self::PLUGIN_WPORGRATE,
				'plugin_url'       => plugin_dir_url( __FILE__ ),
				'plugin_basename'  => plugin_basename( __FILE__ ),
				'plugin_path'      => plugin_dir_path( __FILE__ ),
				'bkps_path'        => ABSPATH . '.bkps' . DIRECTORY_SEPARATOR,
				'wpc_path'         => ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR,
			);

			if ( $this->check_dependencies() ) {
				add_action('admin_init', array( $this, 'load_blog_header' ) );
				load_plugin_textdomain( $this->settings['textdomain'], false, $this->settings['plugin_basename'] . 'lang' );
			}
		}

		public function rating_notice_display() {
			if ( ! get_option( 'bkpc_rating_notice' ) ) {
				?>
					<div class="notice notice-success is-dismissible">
						<h3>Backup Copilot</h3>
						<p>
							Could you please kindly help the plugin in your turn by giving it 5 stars rating? (Thank you in advance)
						</p>
						<p>
							<a href="<?php echo esc_url($this->settings['plugin_wporgrate']); ?>" target="_blank" class="button button-primary">Rate Us @ WordPress.org</a>
							<a href="?bkpc_rating_notice_dismiss" class="button"><strong>I already did</strong></a>
							<a href="?bkpc_rating_notice_dismiss" class="button"><strong>Don't show this notice again!</strong></a>
						</p>
						</p>
					</div>
				<?php
			}
		}

		public function rating_notice_dismiss() {
			if ( isset( $_GET['bkpc_rating_notice_dismiss'] ) ) {
				add_option( 'bkpc_rating_notice', 1 );
			}
		}

		public function load_blog_header() {
			require_once( ABSPATH . 'wp-blog-header.php' );

			if ( ! defined( 'WP_USE_THEMES' ) ) {
				define( 'WP_USE_THEMES', false );
			}

			$this->settings = array_merge(
				$this->settings,
				array(
					'db_hostname' => DB_HOST,
					'db_name'     => DB_NAME,
					'db_user'     => DB_USER,
					'db_password' => DB_PASSWORD
				)
			);
		}
	 
		public function check_dependencies() {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			if (version_compare( PHP_VERSION, $this->settings['php_min_version'] ) >= 0 && version_compare( $GLOBALS['wp_version'], $this->settings['wp_min_version'] ) >= 0 ) {
				$check = true;
			} else {
				$check = false;
				add_action( 'admin_notices', array( $this, 'display_min_requirements_notice' ) );
			}

			if ( $check ) {
				return true;
			}

			deactivate_plugins( $this->settings['plugin_basename'] );

			return false;
		}

		public function display_min_requirements_notice() {
			?>
				<div class="notice notice-error">
					<p>
						<strong><?php echo esc_html_e( $this->settings['plugin_name'] ); ?></strong> requires a minimum of <em>PHP <?php echo esc_html_e( $this->settings['php_min_version'] ); ?></em> and <em>WordPress <?php echo esc_html_e( $this->settings['wp_min_version'] ); ?></em>.
					</p>
					<p>
						You are currently running <strong>PHP <?php echo esc_html_e( PHP_VERSION ); ?></strong> and <strong>WordPress <?php echo esc_html_e( $GLOBALS['wp_version'] ); ?></strong>.
					</p>
				</div>
			<?php
		}
	}

	new Backup_Copilot; 
	
	// Core
	require_once( 'classes/core/class-bkpc-fs.php' );
	require_once( 'classes/core/class-bkpc-db.php' );
	require_once( 'classes/core/class-bkpc-utils.php' );
	require_once( 'classes/core/class-bkpc-view.php' );
	require_once( 'classes/core/class-bkpc-zip.php' );
	require_once( 'classes/core/class-bkpc-mu.php' );

	// 3rd Party Libraries
	require_once( 'classes/class-mysqldump.php' ); 

	// Init
	require_once( 'classes/class-bkpc-init.php' );

	// Events
	require_once( 'classes/events/class-bkpc-delete-backup.php' );
	require_once( 'classes/events/class-bkpc-download-backup.php' );
	require_once( 'classes/events/class-bkpc-create-backup.php' );
	require_once( 'classes/events/class-bkpc-export-backup.php' );
	require_once( 'classes/events/class-bkpc-restore-backup.php' );
	require_once( 'classes/events/class-bkpc-upload-backup.php' );
}
