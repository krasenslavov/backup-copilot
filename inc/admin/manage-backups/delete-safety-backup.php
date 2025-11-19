<?php
/**
 * AJAX handler for deleting a safety backup.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Initialize delete safety backup action.
 */
function bkpc_init_delete_safety_backup() {
	add_action( 'wp_ajax_delete_safety_backup', __NAMESPACE__ . '\bkpc_delete_safety_backup' );
}

/**
 * AJAX handler to delete a safety backup.
 */
function bkpc_delete_safety_backup() {
	// Verify nonce.
	check_ajax_referer( 'bkpc_ajax_nonce', 'nonce' );

	// Check user capability.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Insufficient permissions.', 'backup-copilot' ),
			)
		);
	}

	$safety_uuid = isset( $_POST['safety_uuid'] ) ? sanitize_text_field( wp_unslash( $_POST['safety_uuid'] ) ) : '';

	if ( empty( $safety_uuid ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Invalid safety backup UUID.', 'backup-copilot' ),
			)
		);
	}

	$safety_backup = new BKPC_Safety_Backup();
	$result        = $safety_backup->delete_safety_backup( $safety_uuid );

	if ( $result ) {
		wp_send_json_success(
			array(
				'message' => esc_html__( 'Safety backup deleted successfully.', 'backup-copilot' ),
			)
		);
	} else {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Failed to delete safety backup.', 'backup-copilot' ),
			)
		);
	}
}

// Register on 'plugins_loaded' to ensure AJAX handlers are available early.
add_action( 'plugins_loaded', __NAMESPACE__ . '\bkpc_init_delete_safety_backup' );
