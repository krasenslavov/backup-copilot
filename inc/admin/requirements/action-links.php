<?php
/**
 * Plugin action links and row meta modifications.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Add custom action links to the plugin on the Plugins page.
 */
function bkpc_add_action_links( $links, $file_path ) {
	if ( BKPC_PLUGIN_BASENAME === $file_path ) {
		$manage_backups_link = '<a href="' . esc_url( admin_url( 'admin.php?page=' . BKPC_MANAGE_BACKUPS_SLUG ) ) . '">' . esc_html__( 'Manage Backups', 'backup-copilot' ) . '</a>';
		array_unshift( $links, $manage_backups_link );
	}

	return $links;
}

add_action( 'plugin_action_links', __NAMESPACE__ . '\bkpc_add_action_links', 10, 2 );

/**
 * Add plugin row meta links.
 */
function bkpc_add_plugin_row_meta( $links, $file ) {
	if ( BKPC_PLUGIN_BASENAME === $file ) {
		// Add documentation link
		$links[] = '<a href="' . esc_url( BKPC_PLUGIN_DOCS_URL ) . '" target="_blank">' . esc_html__( 'Documentation', 'backup-copilot' ) . '</a>';
	}

	return $links;
}

add_filter( 'plugin_row_meta', __NAMESPACE__ . '\bkpc_add_plugin_row_meta', 10, 2 );
