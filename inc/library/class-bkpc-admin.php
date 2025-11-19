<?php
/**
 * Admin helper class for common admin operations.
 *
 * @package    BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'BKPC_Admin' ) ) {
	class BKPC_Admin {
		/**
		 * Main menu admin page based on the compact mode.
		 */
		public $admin_page;

		public $compact_mode;

		/**
		 * Consturtor.
		 */
		public function __construct() {
			$this->admin_page   = ( ! get_option( 'bkpc_compact_mode', '' ) ) ? 'admin.php?page=' : 'tools.php?page=';
			$this->compact_mode = ( ! get_option( 'bkpc_compact_mode', '' ) ) ? '' : 'yes'; // No
		}

		/**
		 * Initializor.
		 */
		public function init() {
			add_action( 'wp_loaded', array( $this, 'on_loaded' ) );
		}

		/**
		 * Plugin loaded.
		 */
		public function on_loaded() {}

		/**
		 * Return a response message in JSON format and exit.
		 */
		public function print_json_message( $status, $message, $values_arr = array() ) {
			$response = array(
				'status'  => $status,
				'message' => vsprintf(
					wp_kses(
						$message,
						json_decode( BKPC_PLUGIN_ALLOWED_HTML_ARR, true )
					),
					$values_arr
				),
			);

			echo wp_json_encode(
				array(
					$response,
				),
			);
			exit;
		}

		/**
		 * Check the validity of the nonce token for the plugin's AJAX requests .
		 */
		public function check_nonce_token() {
			if ( ! check_ajax_referer( 'bkpc_ajax_nonce', '_wpnonce', false ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Check if the current user has the necessary capability, typically for
		 * administrative tasks in the plugin.
		 */
		public function check_user_cap() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Check user access control before loading admin modules.
		 */
		public function check_user_access() {
			// Allow access if user is admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			// Check user meta for plugin access.
			$user_can = get_user_meta( get_current_user_id(), 'user_can_access_backup_copilot', true );

			return ! empty( $user_can );
		}

		/**
		 * Check if the nonce token is invalid; if so, print an
		 * error message with a support email link.
		 */
		public function get_invalid_nonce_token() {
			/* translators: %1$s is replaced with "Invalid security token" */
			/* translators: %2$s is replaced with "contact@domain.com" */
			$message    = esc_html__( '%1$s! Contact us @ %2$s.', 'backup-copilot' );
			$values_arr = array(
				'<strong>' . __( 'Invalid security token', 'backup-copilot' ) . '</strong>',
				'<a href="' . esc_url( BKPC_PLUGIN_SUPPORT_URL ) . '">' . esc_html__( 'Support', 'backup-copilot' ) . '</a>',
			);

			if ( ! $this->check_nonce_token() ) {
				$this->print_json_message(
					0,
					$message,
					$values_arr
				);
			}
		}

		/**
		 * Check if the current user has the necessary capabilities;
		 * otherwise, print an error message.
		 */
		public function get_invalid_user_cap() {
			/* translators: %1$s is replaced with "Access denied" */
			$message    = esc_html__( '%1$s! The current user lacks the required capabilities to access this function.', 'backup-copilot' );
			$values_arr = array( '<strong>' . __( 'Access denied', 'backup-copilot' ) . '</strong>' );

			if ( ! $this->check_user_cap() ) {
				$this->print_json_message(
					0,
					$message,
					$values_arr
				);
			}
		}
	}

	$bkpc_admin = new BKPC_Admin();
	$bkpc_admin->init();
}
