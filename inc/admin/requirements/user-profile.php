<?php
/**
 * Extend user profile with plugin access control.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Add user profile fields.
 */
function bkpc_user_profile_fields( $user ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$user_can = get_user_meta( $user->ID, 'user_can_access_backup_copilot', true );
	?>
		<div class="bkpc-admin">
			<h3>
				<?php esc_html_e( 'Backup Copilot', 'backup-copilot' ); ?>
			</h3>
			<table>
				<tr>
					<th>
						<label for="user_can_access_backup_copilot">
							<?php esc_html_e( 'Access Control', 'backup-copilot' ); ?>
						</label>
					</th>
					<td>
						<input
							type="checkbox" 
							name="user_can_access_backup_copilot" 
							id="user_can_access_backup_copilot" 
							value="1" <?php checked( $user_can, 1 ); ?> 
						/>
						<label for="user_can_access_backup_copilot">
							<?php esc_html_e( 'Allow this user to access Backup Copilot', 'backup-copilot' ); ?>
						</label>
					</td>
				</tr>
			</table>
		</div>
	<?php
}

/**
 * Save user profile fields.
 */
function bkpc_save_user_profile_fields( $user_id ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$user_can = isset( $_POST['user_can_access_backup_copilot'] ) ? 1 : 0;
	update_user_meta( $user_id, 'user_can_access_backup_copilot', $user_can );
}

add_action( 'show_user_profile', __NAMESPACE__ . '\bkpc_user_profile_fields' );
add_action( 'edit_user_profile', __NAMESPACE__ . '\bkpc_user_profile_fields' );
add_action( 'personal_options_update', __NAMESPACE__ . '\bkpc_save_user_profile_fields' );
add_action( 'edit_user_profile_update', __NAMESPACE__ . '\bkpc_save_user_profile_fields' );
