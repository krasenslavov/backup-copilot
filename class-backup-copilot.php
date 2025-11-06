<?php
/**
 * Backup Copilot
 *
 * @package    BKPC
 * @author     Krasen Slavov <hello@krasenslavov.com>
 * @copyright  2025
 * @license    GPL-2.0-or-later
 * @link       https://krasenslavov.com/plugins/backup-copilot/
 * @since      0.1.0
 *
 * @wordpress-plugin
 * Plugin Name:       Backup Copilot
 * Plugin URI:        https://krasenslavov.com/plugins/backup-copilot/
 * Description:       Create backup points of your WordPress installation to restore, export, or transfer to another location.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Krasen Slavov
 * Author URI:        https://krasenslavov.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       backup-copilot
 * Domain Path:       /languages
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

namespace BKPC;

! defined( ABSPATH ) || exit;

// Set timezone - use WordPress timezone if available, fallback to UTC.
if ( function_exists( 'wp_timezone' ) ) {
	$timezone = wp_timezone();
} else {
	$timezone_string = get_option( 'timezone_string' );
	$timezone        = new \DateTimeZone( ! empty( $timezone_string ) ? $timezone_string : 'UTC' );
}

if ( ! class_exists( 'Backup_Copilot' ) ) {
	/**
	 * Main Backup_Copilot Class.
	 *
	 * @since 0.1.0
	 */
	class Backup_Copilot {
		const DEV_MODE         = false;
		const VERSION          = '1.0.0';
		const PHP_MIN_VERSION  = '7.2';
		const WP_MIN_VERSION   = '5.0';
		const UUID             = 'bkpc';
		const TEXTDOMAIN       = 'backup-copilot';
		const PLUGIN_NAME      = 'Backup Copilot';
		const PLUGIN_DOCURL    = 'https://krasenslavov.com/plugins/backup-copilot/';
		const PLUGIN_WPORGURL  = 'https://wordpress.org/support/plugin/backup-copilot/';
		const PLUGIN_WPORGRATE = 'https://wordpress.org/support/plugin/backup-copilot/reviews/?filter=5';

		/**
		 * Plugin settings array.
		 *
		 * @var array
		 */
		public $settings;

		/**
		 * Constructor.
		 *
		 * @since 0.1.0
		 */
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
				'bkps_path'        => trailingslashit( ABSPATH . '.bkps' ),
				'wpc_path'         => trailingslashit( ABSPATH . 'wp-content' ),
			);

			if ( $this->check_dependencies() ) {
				add_action( 'admin_init', array( $this, 'load_blog_header' ) );
				load_plugin_textdomain( $this->settings['textdomain'], false, $this->settings['plugin_basename'] . 'lang' );
			}
		}

		/**
		 * Display rating notice in admin.
		 *
		 * @since 0.1.0
		 */
		public function rating_notice_display() {
			// Check if notice was dismissed.
			if ( get_option( 'bkpc_rating_notice' ) ) {
				return;
			}

			// Check if at least 7 days have passed since activation.
			$activation_time = get_option( 'bkpc_activation_time' );
			if ( ! $activation_time ) {
				return;
			}

			$days_since_activation = ( time() - $activation_time ) / DAY_IN_SECONDS;
			if ( $days_since_activation < 7 ) {
				return;
			}

			?>
				<div class="notice notice-success is-dismissible">
					<h3><?php echo esc_html( $this->settings['plugin_name'] ); ?></h3>
					<p>
						<?php esc_html_e( 'Could you please kindly help the plugin in your turn by giving it 5 stars rating? (Thank you in advance)', 'backup-copilot' ); ?>
					</p>
					<p class="button-group">
						<a href="<?php echo esc_url( $this->settings['plugin_wporgrate'] ); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Rate Us @ WordPress.org', 'backup-copilot' ); ?></a>
						<a href="<?php echo esc_url( wp_nonce_url( '?bkpc_rating_notice_dismiss', 'bkpc_rating_notice_dismiss' ) ); ?>" class="button"><strong><?php esc_html_e( 'I already did', 'backup-copilot' ); ?></strong></a>
						<a href="<?php echo esc_url( wp_nonce_url( '?bkpc_rating_notice_dismiss', 'bkpc_rating_notice_dismiss' ) ); ?>" class="button"><strong><?php esc_html_e( "Don't show this notice again!", 'backup-copilot' ); ?></strong></a>
					</p>
					</p>
				</div>
			<?php
		}

		/**
		 * Dismiss rating notice.
		 *
		 * @since 0.1.0
		 */
		public function rating_notice_dismiss() {
			if ( isset( $_GET['bkpc_rating_notice_dismiss'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'bkpc_rating_notice_dismiss' ) ) {
				add_option( 'bkpc_rating_notice', 1 );
			}
		}

		/**
		 * Display onboarding notice.
		 *
		 * @since 1.0.0
		 */
		public function onboarding_notice_display() {
			// Check if notice was dismissed.
			if ( get_option( 'bkpc_onboarding_notice' ) ) {
				return;
			}

			// Only show on dashboard and plugin pages.
			$screen = get_current_screen();
			if ( ! $screen || ( 'dashboard' !== $screen->base
				&& false === strpos( $screen->base, 'backup_copilot' ) ) ) {
				return;
			}
			?>
				<div class="notice notice-info bkpc-onboarding-notice is-dismissible">
					<div class="bkpc-onboarding-content">
						<div class="bkpc-onboarding-icon">
							<span class="dashicons dashicons-backup"></span>
							<h3><?php esc_html_e( 'Welcome to Backup Copilot!', 'backup-copilot' ); ?></h3>
						</div>
						<div class="bkpc-onboarding-text">
							<p><?php esc_html_e( 'Thank you for installing Backup Copilot. Get started with these quick actions:', 'backup-copilot' ); ?></p>
							<ul class="bkpc-onboarding-list">
								<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Create your first database backup', 'backup-copilot' ); ?></li>
								<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Restore backups with one click', 'backup-copilot' ); ?></li>
								<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Export backups for safe storage', 'backup-copilot' ); ?></li>
								<li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Upload backups from other sites', 'backup-copilot' ); ?></li>
							</ul>
							<div class="bkpc-onboarding-actions button-group">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=backup_copilot' ) ); ?>" class="button button-primary">
									<span class="dashicons dashicons-admin-tools"></span>
									<?php esc_html_e( 'Create First Backup', 'backup-copilot' ); ?>
								</a>
								<a href="<?php echo esc_url( $this->settings['plugin_docurl'] ); ?>" class="button" target="_blank">
									<span class="dashicons dashicons-book"></span>
									<?php esc_html_e( 'View Documentation', 'backup-copilot' ); ?>
								</a>
								<a href="<?php echo esc_url( wp_nonce_url( '?bkpc_onboarding_notice_dismiss', 'bkpc_onboarding_notice_dismiss' ) ); ?>" class="button">
									<span class="dashicons dashicons-dismiss"></span>
									<?php esc_html_e( 'Dismiss', 'backup-copilot' ); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			<?php
		}

		/**
		 * Dismiss onboarding notice.
		 *
		 * @since 1.0.0
		 */
		public function onboarding_notice_dismiss() {
			if ( isset( $_GET['bkpc_onboarding_notice_dismiss'] )
				&& isset( $_GET['_wpnonce'] )
				&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'bkpc_onboarding_notice_dismiss' ) ) {
				add_option( 'bkpc_onboarding_notice', 1 );
			}
		}

		/**
		 * Load WordPress blog header and database credentials.
		 *
		 * @since 0.1.0
		 */
		public function load_blog_header() {
			require_once ABSPATH . 'wp-blog-header.php';

			if ( ! defined( 'WP_USE_THEMES' ) ) {
				define( 'WP_USE_THEMES', false );
			}

			$this->settings = array_merge(
				$this->settings,
				array(
					'db_hostname' => DB_HOST,
					'db_name'     => DB_NAME,
					'db_user'     => DB_USER,
					'db_password' => DB_PASSWORD,
				)
			);
		}

		/**
		 * Check if plugin dependencies (PHP and WordPress versions) are met.
		 *
		 * @since 0.1.0
		 * @return bool True if dependencies are met, false otherwise.
		 */
		public function check_dependencies() {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			if ( version_compare( PHP_VERSION, $this->settings['php_min_version'] ) >= 0
				&& version_compare( $GLOBALS['wp_version'], $this->settings['wp_min_version'] ) >= 0 ) {
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

		/**
		 * Display minimum requirements notice when dependencies aren't met.
		 *
		 * @since 0.1.0
		 */
		public function display_min_requirements_notice() {
			?>
				<div class="notice notice-error">
					<p>
						<strong><?php echo esc_html( $this->settings['plugin_name'] ); ?></strong> requires a minimum of <em>PHP <?php echo esc_html( $this->settings['php_min_version'] ); ?></em> and <em>WordPress <?php echo esc_html( $this->settings['wp_min_version'] ); ?></em>.
					</p>
					<p>
						You are currently running <strong>PHP <?php echo esc_html( PHP_VERSION ); ?></strong> and <strong>WordPress <?php echo esc_html( $GLOBALS['wp_version'] ); ?></strong>.
					</p>
				</div>
			<?php
		}
	}

	new Backup_Copilot();

	// Core classes.
	require_once 'classes/core/class-bkpc-fs.php';
	require_once 'classes/core/class-bkpc-db.php';
	require_once 'classes/core/class-bkpc-utils.php';
	require_once 'classes/core/class-bkpc-view.php';
	require_once 'classes/core/class-bkpc-zip.php';
	require_once 'classes/core/class-bkpc-multisite.php';
	require_once 'classes/core/class-bkpc-pointers.php';

	// 3rd Party Libraries.
	require_once 'classes/class-mysqldump.php';

	// Initialization.
	require_once 'classes/class-bkpc-init.php';

	// Event handlers.
	require_once 'classes/events/class-bkpc-delete-backup.php';
	require_once 'classes/events/class-bkpc-download-backup.php';
	require_once 'classes/events/class-bkpc-create-backup.php';
	require_once 'classes/events/class-bkpc-export-backup.php';
	require_once 'classes/events/class-bkpc-restore-backup.php';
	require_once 'classes/events/class-bkpc-upload-backup.php';
}
