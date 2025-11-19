<?php
/**
 * Compact mode setting display and handler.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Display the setting.
 */
function bkpc_display_compact_mode() {
	$bkpc_admin = new BKPC_Admin();

	$compact_mode = get_option( 'bkpc_compact_mode', $bkpc_admin->compact_mode );

	// Compact mode option if empty or non-existent then No, otherwise Yes.
	if ( 'yes' === $compact_mode ) {
		$compact_mode = 'selected';
	}

	printf(
		'<select id="bkpc-compact-mode" name="bkpc_compact_mode">
			<option value="">No</option>
			<option value="yes" %1$s>Yes</option>
		</select>',
		esc_attr( $compact_mode )
	);
	?>
		<p class="description">
			<small>
				<?php echo esc_html__( 'Compact mode moves the plugin access link under Tools.', 'backup-copilot' ); ?>
			</small>
		</p>
	<?php
}

/**
 * Sanitize and update the setting.
 */
function bkpc_sanitize_compact_mode( $compact_mode ) {
	// Verify the nonce.
	$_wpnonce = ( isset( $_REQUEST['bkpc_wpnonce'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['bkpc_wpnonce'] ) ) : '';

	if ( empty( $_wpnonce ) || ! wp_verify_nonce( $_wpnonce, 'bkpc_settings_nonce' ) ) {
		return;
	}

	// Nothing selected.
	if ( empty( $compact_mode ) ) {
		return;
	}

	// Option changed and updated.
	if ( ! get_transient( 'bkpc_settings_compact_mode' )
		&& get_option( 'bkpc_compact_mode', '' ) !== $compact_mode ) {
		add_settings_error(
			'bkpc_settings_errors',
			'bkpc_settings_compact_mode',
			esc_html__( 'Compact mode option was updated successfully.', 'backup-copilot' ),
			'updated'
		);

		// Add transient to avoid double notice on initial Save when using settings_errors().
		set_transient( 'bkpc_settings_compact_mode', true, 5 );
	}

	return sanitize_text_field( wp_unslash( $compact_mode ) );
}
