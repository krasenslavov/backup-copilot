<?php
/**
 * Handle plugin activation/deactivation actions.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Handle actions upon activation of the plugin, such as
 * displaying notices or performing specific tasks.
 */
function bkpc_handle_activation( $plugin ) {
	if ( BKPC_PLUGIN_BASENAME === $plugin ) {
		// Grant access to current admin user.
		$admin_user_id = get_current_user_id();

		if ( user_can( $admin_user_id, 'manage_options' ) ) {
			update_user_meta( $admin_user_id, 'user_can_access_backup_copilot', 1 );
		}

		// Create backup directory.
		$bkps_path = trailingslashit( ABSPATH . '.bkps' );

		if ( ! file_exists( $bkps_path ) ) {
			wp_mkdir_p( $bkps_path );
		}

		// Copy security files.
		$config_path = BKPC_PLUGIN_DIR_PATH . 'config/';

		if ( file_exists( $config_path . 'htaccess.txt' ) ) {
			copy( $config_path . 'htaccess.txt', $bkps_path . '.htaccess' );
		}

		if ( file_exists( $config_path . 'index.php' ) ) {
			copy( $config_path . 'index.php', $bkps_path . 'index.php' );
		}

		// Set activation time.
		if ( ! get_option( 'bkpc_activation_time' ) ) {
			update_option( 'bkpc_activation_time', time() );
		}
	}
}

add_action( 'activated_plugin', __NAMESPACE__ . '\bkpc_handle_activation' );

/**
 * Handle actions upon deactivation of the plugin,
 * such as removing stored notices or performing cleanup tasks.
 */
function bkpc_handle_deactivation( $plugin ) {
	if ( BKPC_PLUGIN_BASENAME === $plugin ) {
		// Cleanup scheduled events.
		wp_clear_scheduled_hook( 'bkpc_scheduled_backup' );

		delete_option( 'bkpc_rating_notice' );
	}
}

add_action( 'deactivated_plugin', __NAMESPACE__ . '\bkpc_handle_deactivation' );
