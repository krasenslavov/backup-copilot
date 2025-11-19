<?php
/**
 * Prevents both free and PRO versions from running simultaneously
 * by deactivating one if the other is already active.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly

/**
 * Don't allow to have both Free and Pro active at the same time.
 */
function bkpc_check_pro_plugin() {
	// Deactitve the Pro version if active.
	if ( is_plugin_active( 'backup-copilot-pro/backup-copilot-pro.php' ) ) {
		deactivate_plugins( 'backup-copilot-pro/backup-copilot-pro.php', true );
	}
}

register_activation_hook( BKPC_PLUGIN_BASENAME, __NAMESPACE__ . '\bkpc_check_pro_plugin' );

/**
 * Display a promotion for the pro plugin.
 */
function bkpc_display_upgrade_notice() {
	$bkpc_admin = new BKPC_Admin();

	// Check if the notice has been dismissed.
	if ( get_option( 'bkpc_upgrade_notice' ) || get_transient( 'bkpc_upgrade_plugin' ) ) {
		// return;
	}

	$activation_time = get_option( 'bkpc_activation_time', 0 );

	if ( ! $activation_time || ( time() - $activation_time ) < ( 7 * DAY_IN_SECONDS ) ) {
		return;
	}

	$current_screen = get_current_screen();

	if ( ! $current_screen || ( 'dashboard' !== $current_screen->base
		&& false === strpos( $current_screen->base, 'bkpc_' ) ) ) {
		return;
	}
	?>
		<div class="bkpc-admin">
			<div class="bkpc-rating-notice">
				<!-- <p class="bkpc-upgrade-notice-discount"> -->
					<?php
					// printf(
					// 	wp_kses(
					// 		/* translators: %1$s is replaced with "BKPC10" */
					// 		/* translators: %2$s is replaced with "10% off" */
					// 		__( 'Use the %1$s promo code and get %2$s your purchase!', 'backup-copilot' ),
					// 		json_decode( BKPC_PLUGIN_ALLOWED_HTML_ARR, true )
					// 	),
					// 	'<code>' . esc_html__( 'BKPC10', 'backup-copilot' ) . '</code>',
					// 	'<strong>' . esc_html__( '10% off', 'backup-copilot' ) . '</strong>'
					// );
					?>
				<!-- </p> -->
				<h2>
					<?php echo esc_html__( 'Backup Copilot PRO 🚀', 'backup-copilot' ); ?>
				</h2>
				<p>
					<?php
					printf(
						wp_kses(
							/* translators: %1$s is replaced with "Found the free version helpful" */
							/* translators: %2$s is replaced with "Backup Copilot" */
							__( '✨🎉📢 %1$s? Discover the added benefits of upgrading to %2$s?', 'backup-copilot' ),
							json_decode( BKPC_PLUGIN_ALLOWED_HTML_ARR, true )
						),
						'<strong>' . esc_html__( 'Found the free version helpful', 'backup-copilot' ) . '</strong>',
						'<strong>' . esc_html__( 'Backup Copilot', 'backup-copilot' ) . '</strong>'
					);
					?>
				</p>
				<div class="button-group">
					<a href="https://backupcopilotplugin.com/?utm_source=bkpc&utm_medium=free_plugin&utm_campaign=admin_notice_button" target="_blank" class="button button-primary button-success">
						<?php echo esc_html__( 'Go Pro', 'backup-copilot' ); ?>
						<i class="dashicons dashicons-external"></i>
					</a>
					<a href="<?php echo esc_url( admin_url( $bkpc_admin->admin_page . BKPC_SETTINGS_SLUG . '&_wpnonce=' . wp_create_nonce( 'bkpc_upgrade_notice_nonce' ) . '&action=bkpc_dismiss_upgrade_notice' ) ); ?>" class="button">
						<?php echo esc_html__( 'I already did', 'backup-copilot' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( $bkpc_admin->admin_page . BKPC_SETTINGS_SLUG . '&_wpnonce=' . wp_create_nonce( 'bkpc_upgrade_notice_nonce' ) . '&action=bkpc_dismiss_upgrade_notice' ) ); ?>" class="button">
						<?php echo esc_html__( "Don't show this notice again!", 'backup-copilot' ); ?>
					</a>
				</div>
			</div>
		</div>
	<?php
	delete_option( 'bkpc_upgrade_notice' );

	// Set the transient to last for 30 days.
	set_transient( 'bkpc_upgrade_plugin', true, 30 * DAY_IN_SECONDS );
}

add_action( 'admin_notices', __NAMESPACE__ . '\bkpc_display_upgrade_notice' );
