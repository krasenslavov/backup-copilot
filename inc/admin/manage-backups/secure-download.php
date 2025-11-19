<?php
/**
 * Secure file download handler with authentication.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Initialize secure download handler.
 */
function bkpc_init_secure_download() {
	add_action( 'wp_ajax_secure_download', __NAMESPACE__ . '\bkpc_secure_download_handler' );
}

/**
 * Handle secure file downloads with authentication.
 */
function bkpc_secure_download_handler() {
	// Verify nonce.
	if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'bkpc_ajax_nonce' ) ) {
		wp_die( esc_html__( 'Security check failed!', 'backup-copilot' ), 403 );
	}

	// Check user capability.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Insufficient permissions!', 'backup-copilot' ), 403 );
	}

	// Get and validate file parameter.
	if ( ! isset( $_REQUEST['file'] ) ) {
		wp_die( esc_html__( 'File parameter missing!', 'backup-copilot' ), 400 );
	}

	$file_path = sanitize_text_field( wp_unslash( $_REQUEST['file'] ) );

	// Security: Ensure file is within backup directory.
	$backup_base    = realpath( BKPC_PLUGIN_BACKUP_DIR_PATH );
	$requested_file = realpath( $file_path );

	if ( ! $requested_file || strpos( $requested_file, $backup_base ) !== 0 ) {
		wp_die( esc_html__( 'Invalid file path!', 'backup-copilot' ), 403 );
	}

	// Check if file exists.
	if ( ! file_exists( $requested_file ) ) {
		wp_die( esc_html__( 'File not found!', 'backup-copilot' ), 404 );
	}

	// Determine MIME type.
	$finfo     = finfo_open( FILEINFO_MIME_TYPE );
	$mime_type = finfo_file( $finfo, $requested_file );
	finfo_close( $finfo );

	// Set headers for download.
	header( 'Content-Type: ' . $mime_type );
	header( 'Content-Disposition: attachment; filename="' . basename( $requested_file ) . '"' );
	header( 'Content-Length: ' . filesize( $requested_file ) );
	header( 'Cache-Control: no-cache, must-revalidate' );
	header( 'Pragma: no-cache' );
	header( 'Expires: 0' );

	// Clear output buffer.
	if ( ob_get_level() ) {
		ob_end_clean();
	}

	// Read and output file.
	readfile( $requested_file );
	exit;
}

// Register on 'plugins_loaded' to ensure AJAX handlers are available early.
add_action( 'plugins_loaded', __NAMESPACE__ . '\bkpc_init_secure_download' );
