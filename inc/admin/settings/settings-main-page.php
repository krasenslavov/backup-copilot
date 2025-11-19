<?php
/**
 * Settings page main content template.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

$bkpc_admin = new BKPC_Admin();

$has_user_cap = $bkpc_admin->check_user_cap();

?>
<div class="bkpc-admin">
	<div class="bkpc-loading-bar"></div>
	<div id="bkpc-notice-container" class="bkpc-notice-container"></div>
	<div class="bkpc-container">
		<?php settings_errors( 'bkpc_settings_errors' ); ?>
		<p>
			<?php
			printf(
				/* translators: %1$s: Important, %2$s: Save */
				esc_html__( '%1$s: Make sure to click the "%2$s" button below after updating any options.', 'backup-copilot' ),
				'<strong>' . esc_html__( 'Important', 'backup-copilot' ) . '</strong>',
				'<strong>' . esc_html__( 'Save', 'backup-copilot' ) . '</strong>'
			);
			?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php wp_nonce_field( 'bkpc_settings_nonce', 'bkpc_wpnonce' ); ?>
			<?php
			settings_fields( BKPC_SETTINGS_SLUG );
			do_settings_sections( BKPC_SETTINGS_SLUG );
			?>
			<p class="submit button-group">
				<button
					type="submit"
					class="button button-primary"
					id="bkpc-save-settings"
					name="bkpc-save-settings"
					<?php if ( ! $has_user_cap ) : ?>
						disabled
					<?php endif; ?>
				>
					<?php esc_html_e( 'Save', 'backup-copilot' ); ?>
				</button>
				<button
					type="button"
					class="button"
					id="bkpc-reset-settings"
					name="bkpc-reset-settings"
					<?php if ( ! $has_user_cap ) : ?>
						disabled
					<?php endif; ?>
				>
					<?php esc_html_e( 'Reset', 'backup-copilot' ); ?>
				</button>
			</p>
		</form>
		<?php require_once BKPC_PLUGIN_DIR_PATH . 'inc/admin/pro-table.php'; ?>
		<br clear="all" />
		<hr />
		<div class="mlr-support-credits">
			<p>
				<?php
				printf(
					wp_kses(
						/* translators: %1$s is replaced with "Support Forum" */
						__( 'If something isn\'t clear, please open a ticket on the official plugin %1$s. We aim to address all tickets within a few working days.', 'backup-copilot' ),
						json_decode( BKPC_PLUGIN_ALLOWED_HTML_ARR, true )
					),
					'<a href="' . esc_url( BKPC_PLUGIN_WPORG_SUPPORT_URL ) . '" target="_blank">' . esc_html__( 'Support Forum', 'backup-copilot' ) . '</a>'
				);
				?>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Please rate us', 'backup-copilot' ); ?></strong>
				<a href="<?php echo esc_url( BKPC_PLUGIN_WPORG_RATE_URL ); ?>" target="_blank">
					<img src="<?php echo esc_url( BKPC_PLUGIN_DIR_URL ); ?>assets/dist/img/rate.png" alt="Rate us @ WordPress.org" />
				</a>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Having issues?', 'backup-copilot' ); ?></strong> 
				<a href="<?php echo esc_url( BKPC_PLUGIN_WPORG_SUPPORT_URL ); ?>" target="_blank">
					<?php echo esc_html__( 'Create a Support Ticket', 'backup-copilot' ); ?>
				</a>
			</p>
			<p>
				<strong><?php echo esc_html__( 'Developed by', 'backup-copilot' ); ?></strong>
				<a href="https://krasenslavov.com/" target="_blank">
					<?php echo esc_html__( 'Krasen Slavov @ Developry', 'backup-copilot' ); ?>
				</a>
			</p>
		</div>
		<hr />
		<p>
			<small>
				<?php
				printf(
					wp_kses(
						/* translators: %1$s is replaced with "help and support me on Patreon" */
						__( '* For the price of a cup of coffee per month, you can %1$s for the development and maintenance of all my free WordPress plugins. Every contribution helps and is deeply appreciated!', 'backup-copilot' ),
						json_decode( BKPC_PLUGIN_ALLOWED_HTML_ARR, true )
					),
					'<a href="https://patreon.com/krasenslavov" target="_blank">' . esc_html__( 'help and support me on Patreon', 'backup-copilot' ) . '</a>'
				);
				?>
			</small>
		</p>
	</div>
</div>
