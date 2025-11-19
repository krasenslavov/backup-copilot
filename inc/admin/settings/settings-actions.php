<?php
/**
 * Settings action handlers.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * [AJAX] Reset plugin settings to their default values
 * and provide a success message.
 */
function bkpc_reset_settings() {
	// Verify nonce for CSRF protection.
	if ( ! isset( $_POST['_wpnonce'] ) || ! check_ajax_referer( 'bkpc_ajax_nonce', '_wpnonce', false ) ) {
		wp_send_json_error(
			array(
				'status'  => 0,
				'message' => esc_html__( 'Security check failed!', 'backup-copilot' ),
			)
		);
	}

	// Check user capability.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error(
			array(
				'status'  => 0,
				'message' => esc_html__( 'Insufficient permissions!', 'backup-copilot' ),
			)
		);
	}

	// Delete all plugin options.
	delete_option( 'bkpc_compact_mode' );

	// Return success response.
	wp_send_json_success(
		array(
			'status'  => 1,
			'message' => esc_html__( 'All options have been reset to their default values.', 'backup-copilot' ),
		)
	);
}

add_action( 'wp_ajax_bkpc_reset_settings', __NAMESPACE__ . '\bkpc_reset_settings' );
