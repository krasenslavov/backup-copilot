<?php
/**
 * Backup Copilot - Initialization
 *
 * Handles plugin initialization, activation/deactivation, admin menu,
 * asset enqueuing, and user profile extensions.
 *
 * @package    BKPC
 * @subpackage Backup_Copilot/Includes
 * @author     Krasen Slavov <hello@krasenslavov.com>
 * @copyright  2025
 * @license    GPL-2.0-or-later
 * @link       https://krasenslavov.com/plugins/backup-copilot/
 * @since      0.1.0
 */

namespace BKPC;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Init' ) ) {

	class BKPC_Init extends Backup_Copilot {
		private $fs;
		private $view;
		private $pointers;
		private $pages;
		public $plugin_hook;
		public $settings;

		public function __construct() {
			parent::__construct();

			$this->fs       = new BKPC_FS();
			$this->view     = new BKPC_View();
			$this->pointers = new BKPC_Pointers();

			$this->pages = array(
				'backup_copilot',
				'create_backup',
				'delete_backup',
				'download_backup',
				'export_backup',
				'restore_backup',
				'upload_backup',
			);
		}

		public function init() {
			add_action( 'activated_plugin', array( $this, 'activate_plugin' ) );
			add_action( 'deactivated_plugin', array( $this, 'deactivate_plugin' ) );
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );

			// Initialize WP Pointers.
			$this->pointers->init();
		}

		public function on_loaded() {
			$user_can = get_user_meta( get_current_user_id(), 'user_can_access_backup_copilot', true );

			if ( ! empty( $user_can ) && null !== $user_can ) {
				// Onboarding notice.
				add_action( 'admin_notices', array( $this, 'onboarding_notice_display' ) );
				add_action( 'admin_init', array( $this, 'onboarding_notice_dismiss' ) );

				// Rating notices.
				add_action( 'admin_notices', array( $this, 'rating_notice_display' ) );
				add_action( 'admin_init', array( $this, 'rating_notice_dismiss' ) );

				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'localize_plugin_urls' ) );
				add_action( 'admin_menu', array( $this, 'add_main_admin_page' ) );
				add_action( 'admin_init', array( $this, 'add_plugin_links' ) );
				add_action( 'admin_init', array( $this, 'extend_user_profile' ) );
				add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
			}
		}

		public function activate_plugin( $plugin ) {
			if ( $this->settings['plugin_basename'] === $plugin ) {
				$this->activate_backup_copilot();
			}
		}

		public function deactivate_plugin( $plugin ) {
			if ( $this->settings['plugin_basename'] === $plugin ) {
				$this->deactivate_backup_copilot();
			}
		}

		public function enqueue_admin_scripts() {
			$screen = get_current_screen();

			// Enqueue styles on dashboard for widget.
			if ( 'dashboard' === $screen->base ) {
				wp_enqueue_style(
					'bkpc-admin',
					$this->settings['plugin_url'] . 'assets/dist/css/bkpc-admin.min.css',
					array(),
					'1.0',
					'all'
				);
			}

			// Enqueue scripts and styles on plugin pages.
			if ( 'toplevel_page_backup_copilot' === $screen->base
				&& isset( $_GET['page'] )
				&& in_array( sanitize_text_field( wp_unslash( $_GET['page'] ) ), $this->pages, true ) ) {
				wp_register_script(
					'bkpc-admin',
					$this->settings['plugin_url'] . 'assets/dist/js/bkpc-admin.min.js',
					array( 'jquery' ),
					'1.0',
					true
				);

				wp_register_style(
					'bkpc-admin',
					$this->settings['plugin_url'] . 'assets/dist/css/bkpc-admin.min.css',
					array(),
					'1.0',
					'all'
				);

				wp_enqueue_script( 'bkpc-admin' );
				wp_enqueue_style( 'bkpc-admin' );
			}
		}

		public function add_main_admin_page() {
			$this->plugin_hook = add_menu_page(
				'Backup Copilot',
				'Backup Copilot',
				'manage_options',
				'backup_copilot',
				array( $this->view, 'load_backup_copilot_main_page' ),
				'dashicons-backup',
				75
			);
		}

		public function localize_plugin_urls() {
			wp_localize_script(
				'bkpc-admin',
				'bkpc',
				array(
					'plugin_url' => $this->settings['plugin_url'],
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'nonce'      => wp_create_nonce( 'bkpc_ajax_nonce' ),
				)
			);
		}

		public function add_plugin_links() {
			add_action( 'plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
			add_action( 'plugin_row_meta', array( $this, 'add_meta_links' ), 10, 2 );
		}

		public function add_action_links( $links, $file_path ) {
			if ( $this->settings['plugin_basename'] === $file_path ) {
				$links['settings'] = '<a href="' . esc_url( admin_url( 'admin.php?page=backup_copilot' ) ) . '">'
					. __( 'Manage Backups', $this->settings['textdomain'] ) . '</a>'; // phpcs:ignore
				return array_reverse( $links );
			}

			return $links;
		}

		public function add_meta_links( $links, $file_path ) {
			if ( $this->settings['plugin_basename'] === $file_path ) {
				$links['docmentation'] = '<a href="' . esc_url( $this->settings['plugin_docurl'] ) . '" target="_blank">'
					. __( 'Documentation', $this->settings['textdomain'] ) . '</a>'; // phpcs:ignore
			}

			return $links;
		}

		public function extend_user_profile() {
			// Add field for user access to Backup Copilot.
			add_action( 'user_new_form', array( $this, 'register_profile_fields' ) );
			// add_action( 'show_user_profile', array( $this, 'register_profile_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'register_profile_fields' ) );
			// Save user access to Backup Copilot as usermeta.
			add_action( 'user_register', array( $this, 'save_profile_fields' ) );
			// add_action( 'personal_options_update', array( $this, 'save_profile_fields' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_profile_fields' ) );
		}

		public function register_profile_fields( $user ) {
			/**
			 * We want to restrict Administrators to change the BKPC access themselves.
			 *
			 * If Admin use is created without access to Backup Copilot then they won't
			 * be able to Add, Edit or Update others with this options.
			 */
			$current_user_id  = get_current_user_id();
			$current_user_can = get_user_meta( $current_user_id, 'user_can_access_backup_copilot', true );
			$update_user_can  = is_object( $user ) && isset( $user->ID )
				? get_user_meta( $user->ID, 'user_can_access_backup_copilot', true )
				: false;

			if ( ! current_user_can( 'administrator', $current_user_id ) || null === $current_user_can ) {
				return false;
			}
			?>
				<br />
				<h2><?php esc_html_e( 'Backup Copilot', 'backup-copilot' ); ?></h2>
				<table class="form-table">
					<tr>
						<th>
							<label for="user_can_access_backup_copilot"><?php esc_html_e( 'User can have access to Backup Copilot?', 'backup-copilot' ); ?></label>
						</th>
						<td>
							<?php if ( null !== $update_user_can ) : ?>
								<input type="checkbox" class="regular-text" name="user_can_access_backup_copilot" value="1" id="user_can_access_backup_copilot" checked />
							<?php else : ?>
								<input type="checkbox" class="regular-text" name="user_can_access_backup_copilot" value="1" id="user_can_access_backup_copilot" />
							<?php endif; ?>
							<em><?php esc_html_e( 'Useful when you want to restrict access to Backup Copilot and have a single admin-only access.', 'backup-copilot' ); ?></em>
						</td>
					</tr>
				</table>
			<?php
		}

		public function save_profile_fields( $update_user_id ) {
			// Verify nonce for security.
			if ( ! isset( $_POST['_wpnonce'] )
				|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-user_' . $update_user_id ) ) {
				return false;
			}

			$current_user_id  = get_current_user_id();
			$current_user_can = get_user_meta( $current_user_id, 'user_can_access_backup_copilot', true );

			if ( ! current_user_can( 'administrator', $current_user_id ) || null === $current_user_can ) {
				return false;
			}

			$access_value = isset( $_POST['user_can_access_backup_copilot'] )
				? absint( $_POST['user_can_access_backup_copilot'] )
				: 0;

			update_user_meta( $update_user_id, 'user_can_access_backup_copilot', $access_value );
		}

		public function activate_backup_copilot() {
			if ( $this->fs->create_directory( $this->settings['bkps_path'] ) ) {

				$this->fs->copy_file(
					$this->settings['plugin_path'] . 'config/.htaccess',
					$this->settings['bkps_path'] . '.htaccess'
				);

				$this->fs->copy_file(
					$this->settings['plugin_path'] . 'config/index.html',
					$this->settings['bkps_path'] . 'index.html'
				);

				$this->fs->copy_file(
					$this->settings['plugin_path'] . 'config/index.php',
					$this->settings['bkps_path'] . 'index.php'
				);

				$this->fs->copy_file(
					$this->settings['plugin_path'] . 'config/web.config',
					$this->settings['bkps_path'] . 'web.config'
				);
			}

			$this->fs->append_file(
				$this->settings['plugin_path'] . 'config/.htaccess.txt',
				ABSPATH . '.htaccess'
			);

			$this->fs->append_file(
				$this->settings['plugin_path'] . 'config/.user.ini.txt',
				ABSPATH . '.user.ini'
			);

			$this->fs->append_file(
				$this->settings['plugin_path'] . 'config/php.ini.txt',
				ABSPATH . 'php.ini'
			);

			// Activate plugin for the first time add option and add access to the primary admin user.
			if ( get_option( 'bkpc_backup_copilot' ) === false ) {
				add_option( 'bkpc_backup_copilot', 1 );
				add_option( 'bkpc_activation_time', time() );
				update_user_meta( get_current_user_id(), 'user_can_access_backup_copilot', 1 );
			}
		}

		public function deactivate_backup_copilot() {
			if ( is_dir( $this->settings['bkps_path'] ) ) {
				$this->fs->deduct_file( $this->settings['plugin_path'] . 'config/.htaccess.txt', ABSPATH . '.htaccess' );
				$this->fs->deduct_file( $this->settings['plugin_path'] . 'config/.user.ini.txt', ABSPATH . '.user.ini' );
				$this->fs->deduct_file( $this->settings['plugin_path'] . 'config/php.ini.txt', ABSPATH . 'php.ini' );
			}
			delete_option( 'bkpc_rating_notice' );

			// Reset WP Pointer on deactivation.
			$this->pointers->reset_pointer();
		}

		public function add_dashboard_widget() {
			wp_add_dashboard_widget(
				'bkpc_dashboard_widget',
				'Backup Copilot',
				array( $this, 'render_dashboard_widget' )
			);
		}

		public function render_dashboard_widget() {
			$folder_size    = $this->get_backups_folder_size();
			$recent_backups = $this->get_recent_backups( 3 );
			?>
			<div class="bkpc-dashboard-widget">
				<div class="bkpc-widget-summary">
					<p><?php esc_html_e( 'Create, restore, and manage your WordPress database backups with ease.', 'backup-copilot' ); ?></p>
				</div>

				<div class="bkpc-widget-stats">
					<div class="bkpc-stat-item">
						<span class="dashicons dashicons-database"></span>
						<div>
							<strong><?php echo esc_html( $folder_size ); ?></strong>
							<small><?php esc_html_e( 'Total Backup Size', 'backup-copilot' ); ?></small>
						</div>
					</div>
					<div class="bkpc-stat-item">
						<span class="dashicons dashicons-backup"></span>
						<div>
							<strong><?php echo esc_html( count( $recent_backups ) ); ?></strong>
							<small><?php esc_html_e( 'Recent Full Backups', 'backup-copilot' ); ?></small>
						</div>
					</div>
				</div>

				<?php if ( ! empty( $recent_backups ) ) : ?>
					<div class="bkpc-widget-backups">
						<h4><?php esc_html_e( 'Latest Backups', 'backup-copilot' ); ?></h4>
						<ul>
							<?php foreach ( $recent_backups as $backup ) : ?>
								<li>
									<span class="dashicons dashicons-yes-alt"></span>
									<span><?php echo esc_html( $backup['name'] ); ?></span>
									<small><?php echo esc_html( $backup['date'] ); ?></small>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<div class="bkpc-widget-actions">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=backup_copilot' ) ); ?>" class="button button-primary">
						<span class="dashicons dashicons-admin-tools"></span>
						<?php esc_html_e( 'Manage Backups', 'backup-copilot' ); ?>
					</a>
					<a href="<?php echo esc_url( $this->settings['plugin_docurl'] ); ?>" class="button" target="_blank">
						<span class="dashicons dashicons-sos"></span>
						<?php esc_html_e( 'Support', 'backup-copilot' ); ?>
					</a>
				</div>
			</div>
			<?php
		}

		private function get_backups_folder_size() {
			$bkps_path = $this->settings['bkps_path'];

			if ( ! is_dir( $bkps_path ) ) {
				return '0 B';
			}

			$total_size = 0;
			$files      = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator( $bkps_path, \RecursiveDirectoryIterator::SKIP_DOTS )
			);

			foreach ( $files as $file ) {
				if ( $file->isFile() ) {
					$total_size += $file->getSize();
				}
			}

			return $this->format_bytes( $total_size );
		}

		private function get_recent_backups( $limit = 3 ) {
			$bkps_path = $this->settings['bkps_path'];
			$backups   = array();

			// Normalize path to fix mixed slashes.
			$bkps_path = wp_normalize_path( $bkps_path );
			$bkps_path = trailingslashit( $bkps_path );

			if ( ! is_dir( $bkps_path ) ) {
				return $backups;
			}

			// Use scandir to get all directories.
			$items = scandir( $bkps_path );
			if ( false === $items ) {
				return $backups;
			}

			$dirs = array();
			foreach ( $items as $item ) {
				// Skip current/parent directory markers.
				if ( '.' === $item || '..' === $item ) {
					continue;
				}

				// Skip common files (not directories).
				if ( in_array( $item, array( '.htaccess', 'index.html', 'index.php', 'web.config' ), true ) ) {
					continue;
				}

				$full_path = $bkps_path . $item;

				if ( is_dir( $full_path ) ) {
					$dirs[] = $full_path;
				}
			}

			if ( empty( $dirs ) ) {
				return $backups;
			}

			// Sort by modification time (newest first).
			usort(
				$dirs,
				function ( $a, $b ) {
					return filemtime( $b ) - filemtime( $a );
				}
			);

			$dirs = array_slice( $dirs, 0, $limit );

			foreach ( $dirs as $dir ) {
				$uuid     = basename( $dir );
				$zip_file = trailingslashit( $dir ) . $uuid . '.zip';

				if ( file_exists( $zip_file ) ) {
					$backups[] = array(
						'name' => $uuid,
						'date' => date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), filemtime( $zip_file ) ),
						'size' => $this->format_bytes( filesize( $zip_file ) ),
					);
				}
			}

			return $backups;
		}

		private function format_bytes( $bytes, $precision = 2 ) {
			$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );

			$bytes = max( $bytes, 0 );
			$pow   = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
			$pow   = min( $pow, count( $units ) - 1 );

			$bytes /= pow( 1024, $pow );

			return round( $bytes, $precision ) . ' ' . $units[ $pow ];
		}
	}

	$bkpc = new BKPC_Init();
	$bkpc->init();
}
