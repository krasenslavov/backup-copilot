<?php
/**
 * AJAX handler for generating secure UUIDs for backup operations.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Initialize UUID generation actions.
 */
function bkpc_init_uuid_generation() {
	add_action( 'wp_ajax_generate_secure_uuid', __NAMESPACE__ . '\bkpc_generate_secure_uuid' );
}

/**
 * AJAX handler to generate secure UUID.
 */
function bkpc_generate_secure_uuid() {
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

	// Generate secure UUID.
	$security = new BKPC_Security();
	$uuid     = $security->generate_secure_uuid();

	// Return UUID.
	wp_send_json_success(
		array(
			'uuid' => $uuid,
		)
	);
}

// Register on 'plugins_loaded' to ensure AJAX handlers are available early.
add_action( 'plugins_loaded', __NAMESPACE__ . '\bkpc_init_uuid_generation' );
