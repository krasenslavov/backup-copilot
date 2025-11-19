<?php
/**
 * AJAX handler for fetching backup operation progress.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Initialize progress actions.
 */
function bkpc_init_progress_actions() {
	add_action( 'wp_ajax_get_backup_progress', __NAMESPACE__ . '\bkpc_get_backup_progress' );
}

/**
 * AJAX handler to get backup progress.
 */
function bkpc_get_backup_progress() {
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

	// Get UUID from request.
	$uuid = isset( $_REQUEST['uuid'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['uuid'] ) ) : '';

	if ( empty( $uuid ) ) {
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Invalid UUID.', 'backup-copilot' ),
			)
		);
	}

	// Get progress data.
	$progress_tracker = new BKPC_Progress();
	$progress_raw     = $progress_tracker->get( $uuid );

	// Parse progress text into structured array.
	$progress_data = array();
	if ( ! empty( $progress_raw ) ) {
		$lines = explode( "\n", trim( $progress_raw ) );
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( empty( $line ) ) {
				continue;
			}

			// Check if line ends with [Done]
			$done = false;
			if ( strpos( $line, '[Done]' ) !== false ) {
				$done    = true;
				$message = trim( str_replace( '[Done]', '', $line ) );
			} else {
				$message = $line;
			}

			$progress_data[] = array(
				'message' => $message,
				'done'    => $done,
			);
		}
	}

	// Return progress data.
	wp_send_json_success(
		array(
			'progress' => $progress_data,
			'uuid'     => $uuid,
		)
	);
}

// Register on 'plugins_loaded' to ensure AJAX handlers are available early.
add_action( 'plugins_loaded', __NAMESPACE__ . '\bkpc_init_progress_actions' );
