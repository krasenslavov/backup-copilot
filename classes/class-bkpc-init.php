<?php

namespace BKPC\Backup_Copilot;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Init' ) ) {

	class BKPC_Init extends Backup_Copilot {
		public function __construct() {
			parent::__construct();

			$this->fs    = new BKPC_FS;
			$this->view  = new BKPC_View;

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
		}

		public function on_loaded() {
			$user_can = get_user_meta( get_current_user_id(), 'user_can_access_backup_copilot' );

			if ( ! empty( $user_can ) && $user_can[0] !== null ) {
				// Rating notices
				add_action( 'admin_notices', array( $this, 'rating_notice_display' ) );
				add_action( 'admin_init', array( $this, 'rating_notice_dismiss' ) );
				
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'localize_plugin_urls' ) );
				add_action( 'admin_menu', array( $this, 'add_main_admin_page' ) );
				add_action( 'admin_init', array( $this, 'add_plugin_links' ) );
				add_action( 'admin_init', array( $this, 'extend_user_profile' ) );
			}
		}
		
		public function activate_plugin($plugin) {
			if ( $this->settings['plugin_basename'] === $plugin ) {
				$this->activate_backup_copilot();
			} 
		}

		public function deactivate_plugin( $plugin ) {
			if ( $this->settings['plugin_basename'] === $plugin ) {
				$this->deactivate_backup_copilot();
			}
		}

		public function enqueue_admin_scripts(){
			$screen = get_current_screen();

			if ( 'toplevel_page_backup_copilot' === $screen->base && in_array( $_GET['page'], $this->pages ) ) {

				if ($this->settings['dev_mode'] == true) {
					wp_register_script( 'backup_copilot', $this->settings['plugin_url'] . 'assets/js/backup-copilot-init.js', array( 'jquery' ), '1.0', true );
					wp_register_style( 'backup_copilot', $this->settings['plugin_url'] . 'assets/css/backup-copilot.css', array(), '1.0', 'all' );
				} else {
					wp_register_script( 'backup_copilot', $this->settings['plugin_url'] . 'assets/build/js/backup-copilot.min.js', array( 'jquery' ), '1.0', true );
					wp_register_style( 'backup_copilot', $this->settings['plugin_url'] . 'assets/build/css/backup-copilot.min.css', array(), '1.0', 'all' );
				}

				wp_enqueue_script( 'backup_copilot' );
				wp_enqueue_style( 'backup_copilot' );
			}
		}

		public function add_main_admin_page() {
			$this->plugin_hook = add_menu_page( 'Backup Copilot', 'Backup Copilot', 'manage_options', 'backup_copilot', array( $this->view, 'load_backup_copilot_main_page' ), 'dashicons-backup', 75 );
		}

		public function localize_plugin_urls() {
			wp_localize_script( 'backup_copilot', 'bkpc', 
				array(
					'plugin_url' => $this->settings['plugin_url'],
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
				)
			);
		}

		public function add_plugin_links() {
			add_action( 'plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
			add_action( 'plugin_row_meta', array( $this, 'add_meta_links' ), 10, 2 );
		}

		public function add_action_links( $links, $file_path ) {
			if ( $this->settings['plugin_basename'] === $file_path ) {
				$links['settings'] = '<a href="' . sanitize_text_field(admin_url('admin.php?page=backup_copilot')) . '">' . __( 'Manage Backups', $this->settings['textdomain'] ) . '</a>';
				return array_reverse( $links );
			}
			
			return $links;
		}

		public function add_meta_links( $links, $file_path ) {
			if ( $this->settings['plugin_basename'] === $file_path ) {
				$links['docmentation'] = '<a href="' . sanitize_text_field( $this->settings['plugin_docurl'] ) . '" target="_blank">' . __( 'Documentation', $this->settings['textdomain'] ) . '</a>';
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
			$current_user_can = get_user_meta( $current_user_id, 'user_can_access_backup_copilot' )[0];
			$update_user_can  = get_user_meta( $user->ID, 'user_can_access_backup_copilot' )[0];

			if ( ! current_user_can( 'administrator', $current_user_id) || $current_user_can === null ) {
				return false;
			}
			?>
				<br />
				<h2>Backup Copilot</h2>
				<table class="form-table">
					<tr>
						<th>
							<label for="user_can_access_backup_copilot">User can have access<br /> to Backup Copilot?</label>
						</th>
						<td>
							<?php if ( $update_user_can !== null ) : ?>
								<input type="checkbox" class="regular-text" name="user_can_access_backup_copilot" value="1" id="user_can_access_backup_copilot" checked />
							<?php else : ?>
								<input type="checkbox" class="regular-text" name="user_can_access_backup_copilot" value="1" id="user_can_access_backup_copilot" />
							<?php endif; ?>
							<em>Useful when you want to restrict access to Backup Copilot  and have a single admin-only access.</em>
						</td>
					</tr>
				</table>
			<?php 
		}

		public function save_profile_fields( $update_user_id ) {
			$current_user_id  = get_current_user_id();
			$current_user_can = get_user_meta( $current_user_id, 'user_can_access_backup_copilot' )[0];

			if ( ! current_user_can( 'administrator', $current_user_id ) || $current_user_can === null ) {
				return false;
			}

			update_user_meta( $update_user_id, 'user_can_access_backup_copilot', $_POST['user_can_access_backup_copilot'] );
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
			if ( get_option( 'backup_copilot' ) === false ) {  
				add_option( 'backup_copilot', 1 );
				update_user_meta( get_current_user_id(), 'user_can_access_backup_copilot', 1 );
			}
		}

		public function deactivate_backup_copilot() {
			if ( is_dir($this->settings['bkps_path'] ) ) {
				$this->fs->deduct_file( $this->settings['plugin_path'] . 'config/.htaccess.txt', ABSPATH . '.htaccess' );
				$this->fs->deduct_file( $this->settings['plugin_path'] . 'config/.user.ini.txt', ABSPATH . '.user.ini' );
				$this->fs->deduct_file( $this->settings['plugin_path'] . 'config/php.ini.txt', ABSPATH . 'php.ini' );
			}
			delete_option( 'bkpc_rating_notice' );
		}
	}

	$bkpc = new BKPC_Init;
	$bkpc->init();
}
