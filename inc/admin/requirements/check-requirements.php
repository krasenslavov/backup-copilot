<?php
/**
 * System requirements checker for plugin dependencies.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Check the system requirements for the plugin and perform actions accordingly.
 */
function bkpc_check_requirements() {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	// Check PHP version.
	if ( version_compare( PHP_VERSION, BKPC_MIN_PHP_VERSION, '<' ) ) {
		if ( isset( $_REQUEST['activate'] ) ) {
			unset( $_REQUEST['activate'] );
		}

		$message = sprintf(
			/* translators: %1$s: Plugin name, %2$s: Required PHP version, %3$s: Current PHP version */
			esc_html__( '%1$s requires PHP version %2$s or greater. You are currently running version %3$s.', 'backup-copilot' ),
			'<strong>' . esc_html( BKPC_PLUGIN_NAME ) . '</strong>',
			'<strong>' . BKPC_MIN_PHP_VERSION . '</strong>',
			'<strong>' . PHP_VERSION . '</strong>'
		);

		printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );

		deactivate_plugins( BKPC_PLUGIN_BASENAME );
		return;
	}

	// Check WordPress version.
	if ( version_compare( $GLOBALS['wp_version'], BKPC_MIN_WP_VERSION, '<' ) ) {
		if ( isset( $_REQUEST['activate'] ) ) {
			unset( $_REQUEST['activate'] );
		}

		$message = sprintf(
			/* translators: %1$s: Plugin name, %2$s: Required WP version, %3$s: Current WP version */
			esc_html__( '%1$s requires WordPress version %2$s or greater. You are currently running version %3$s.', 'backup-copilot' ),
			'<strong>' . esc_html( BKPC_PLUGIN_NAME ) . '</strong>',
			'<strong>' . BKPC_MIN_WP_VERSION . '</strong>',
			'<strong>' . $GLOBALS['wp_version'] . '</strong>'
		);

		printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );

		deactivate_plugins( BKPC_PLUGIN_BASENAME );
		return;
	}

	// All requirements met - load textdomain and initialize hooks.
	load_plugin_textdomain( BKPC_PLUGIN_TEXTDOMAIN, false, dirname( BKPC_PLUGIN_BASENAME ) . '/languages' );
}

add_action( 'admin_init', __NAMESPACE__ . '\bkpc_check_requirements' );
