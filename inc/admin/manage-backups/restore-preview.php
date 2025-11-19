<?php
/**
 * AJAX handler for generating restore preview and validation.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Initialize restore preview action.
 */
function bkpc_init_restore_preview() {
	add_action( 'wp_ajax_restore_preview', __NAMESPACE__ . '\bkpc_restore_preview' );
}

/**
 * AJAX handler to generate restore preview.
 */
function bkpc_restore_preview() {
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

	$uuid = isset( $_POST['uuid'] ) ? sanitize_text_field( wp_unslash( $_POST['uuid'] ) ) : '';

	if ( empty( $uuid ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Invalid backup UUID.', 'backup-copilot' ),
			)
		);
	}

	// Validate backup.
	$validator  = new BKPC_Restore_Validator();
	$validation = $validator->validate_backup( $uuid );

	if ( ! $validation['valid'] ) {
		wp_send_json_error(
			array(
				'message'    => esc_html__( 'Backup validation failed.', 'backup-copilot' ),
				'validation' => $validation,
			)
		);
	}

	// Generate preview.
	$preview = $validator->generate_preview( $uuid );

	wp_send_json_success(
		array(
			'validation' => $validation,
			'preview'    => $preview,
		)
	);
}

// Register on 'plugins_loaded' to ensure AJAX handlers are available early.
add_action( 'plugins_loaded', __NAMESPACE__ . '\bkpc_init_restore_preview' );
