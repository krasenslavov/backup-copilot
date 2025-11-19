<?php
/**
 * Shows helpful pointer on plugin activation pointing to the plugin menu.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Enqueue pointer only if not dismissed.
 */
function bkpc_enqueue_wp_pointer( $hook_suffix ) {
	// Only load on your plugin settings page (optional).
	// if ( 'toplevel_page_bkpc_manage_backups' !== $hook_suffix ) {
	// 	return;
	// }

	// Check if dismissed.
	$dismissed = get_user_meta( get_current_user_id(), 'bkpc_admin_menu_pointer', true );

	if ( $dismissed ) {
		return;
	}

	// Load WP Pointer styles and scripts.
	wp_enqueue_style( 'wp-pointer' );
	wp_enqueue_script( 'wp-pointer' );

	wp_localize_script(
		'wp-pointer',
		'bkpc_admin_menu_pointer',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'bkpc_admin_menu_pointer_nonce' ),
		)
	);

	// Add inline JS in footer.
	add_action( 'admin_print_footer_scripts', __NAMESPACE__ . '\bkpc_wp_pointer_script' );
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\bkpc_enqueue_wp_pointer' );

/**
 * The actual pointer script.
 */
function bkpc_wp_pointer_script() {
	$pointer_content = sprintf(
		'<h3>%s</h3><p>%s</p>',
		esc_html__( 'Welcome to Backup Copilot!', 'backup-copilot' ),
		sprintf(
			/* translators: %1$s is replaced with "Quick Start" */
			esc_html__( '%1$s Click here to create your first database backup, restore previous backups, or manage all your backup files.', 'backup-copilot' ),
			'<strong>' . esc_html__( 'Quick Start:', 'backup-copilot' ) . '</strong>'
		)
	);
	?>
	<script type="text/javascript">
		jQuery(function($) {
			// Target the main plugin menu item
			var $target = $('#toplevel_page_bkpc_manage_backups');

			// Fallback to submenu under BKPC if compact mode is enabled
			if (!$target.length) {
				$target = $('#menu-tools').find('a[href*="bkpc_manage_backups"]').parent();
			}

			if ($target.length) {
				$target.pointer({
					content: '<?php echo wp_kses_post( addslashes( $pointer_content ) ); ?>',
					position: {
						edge: 'left',
						align: 'center'
					},
					close: function() {
						$.post(bkpc_admin_menu_pointer.ajaxurl, {
							action: 'dismiss_wp_pointer',
							pointer: 'bkpc_admin_menu_pointer',
							nonce: bkpc_admin_menu_pointer.nonce
						});
					}
				}).pointer('open');
			}
		});
	</script>
	<?php
}

/**
 * Register dismissal action.
 */
function bkpc_dismiss_wp_pointer() {
	// Verify nonce.
	check_ajax_referer( 'bkpc_admin_menu_pointer_nonce', 'nonce' );

	// Check user capability.
	if ( ! current_user_can( 'read' ) ) {
		wp_send_json_error( __( 'Insufficient permissions.', 'bkpc-copilot-pro' ) );
	}

	if ( isset( $_POST['pointer'] ) ) {
		update_user_meta(
			get_current_user_id(),
			sanitize_text_field( wp_unslash( $_POST['pointer'] ) ),
			true
		);
	}

	// Properly terminate the AJAX request.
	wp_send_json_success( __( 'Pointer dismissed successfully!', 'bkpc-copilot-pro' ) );
}

add_action( 'wp_ajax_dismiss_wp_pointer', __NAMESPACE__ . '\bkpc_dismiss_wp_pointer' );

/**
 * Reset WP Pointer dismissal on plugin deactivation.
 */
function bkpc_reset_pointer_on_deactivation() {
	// Get all users safely.
	if ( ! function_exists( 'get_users' ) ) {
		require_once ABSPATH . 'wp-includes/pluggable.php';
	}

	// Delete the dismissal flag for all users.
	$users = get_users( array( 'fields' => array( 'ID' ) ) );

	if ( empty( $users ) ) {
		return;
	}

	foreach ( $users as $user ) {
		delete_user_meta( $user->ID, 'bkpc_admin_menu_pointer' );
	}
}

add_action( 'deactivated_plugin', __NAMESPACE__ . '\bkpc_reset_pointer_on_deactivation' );
