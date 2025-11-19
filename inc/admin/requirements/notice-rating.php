<?php
/**
 * Display rating notice after plugin usage.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Display rating notice after 7 days of usage.
 */
function bkpc_display_rating_notice() {
	$bkpc_admin = new BKPC_Admin();

	if ( get_option( 'bkpc_rating_notice_dismissed', false ) ) {
		return;
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
				<h2>
					<?php echo esc_html( BKPC_PLUGIN_NAME ); ?>  🚀
				</h2>
				<p>
					<?php
					printf(
						wp_kses(
							/* translators: %1$s is replaced with "by giving it 5 stars rating" */
							__( '✨💪🔌 Could you kindly support the plugin by %1$s? Thank you in advance!', 'backup-copilot' ),
							json_decode( BKPC_PLUGIN_ALLOWED_HTML_ARR, true )
						),
						'<strong>' . esc_html__( 'by giving it 5 stars rating', 'backup-copilot' ) . '</strong>'
					);
					?>
				</p>
				<div class="button-group">
					<a href="<?php echo esc_url( BKPC_PLUGIN_WPORG_RATE_URL ); ?>" target="_blank" class="button button-primary">
						<?php echo esc_html__( 'Rate us @ WordPress.org', 'backup-copilot' ); ?>
						<i class="dashicons dashicons-external"></i>
					</a>
					<a href="<?php echo esc_url( admin_url( $bkpc_admin->admin_page . BKPC_MANAGE_BACKUPS_SLUG . '&_wpnonce=' . wp_create_nonce( 'bkpc_rating_notice_nonce' ) . '&action=bkpc_rating_notice_dismiss' ) ); ?>" class="button">
						<?php echo esc_html__( 'I already did', 'backup-copilot' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( $bkpc_admin->admin_page . BKPC_MANAGE_BACKUPS_SLUG . '&_wpnonce=' . wp_create_nonce( 'bkpc_rating_notice_nonce' ) . '&action=bkpc_rating_notice_dismiss' ) ); ?>" class="button">
						<?php echo esc_html__( "Don't show this notice again!", 'backup-copilot' ); ?>
					</a>
				</div>
			</div>
		</div>
	<?php
}

add_action( 'admin_notices', __NAMESPACE__ . '\bkpc_display_rating_notice' );
