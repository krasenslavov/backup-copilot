<?php
/**
 * Backup Copilot - WP Pointers
 *
 * Displays WordPress admin pointers to help users get started with the plugin.
 *
 * @package    BKPC
 * @subpackage Backup_Copilot/Core
 * @author     Krasen Slavov <hello@krasenslavov.com>
 * @copyright  2025
 * @license    GPL-2.0-or-later
 * @link       https://krasenslavov.com/plugins/backup-copilot/
 * @since      1.0.0
 */

namespace BKPC;

! defined( ABSPATH ) || exit;

if ( ! class_exists( 'BKPC_Pointers' ) ) {

	class BKPC_Pointers extends Backup_Copilot {
		public function __construct() {
			parent::__construct();
		}

		public function init() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_wp_pointer' ) );
			add_action( 'wp_ajax_dismiss_bkpc_pointer', array( $this, 'dismiss_wp_pointer' ) );
		}

		/**
		 * Enqueue pointer only if not dismissed.
		 *
		 * @param string $hook_suffix Current admin page hook suffix.
		 */
		public function enqueue_wp_pointer( $hook_suffix ) {
			// Check if dismissed.
			$dismissed = get_user_meta( get_current_user_id(), 'bkpc_pointer', true );

			if ( $dismissed ) {
				return;
			}

			// Load WP Pointer styles and scripts.
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );

			wp_localize_script(
				'wp-pointer',
				'bkpc_pointer',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
				)
			);

			// Add inline JS in footer.
			add_action( 'admin_print_footer_scripts', array( $this, 'wp_pointer_script' ) );
		}

		/**
		 * Output the pointer JavaScript.
		 */
		public function wp_pointer_script() {
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
					var $target = $('#toplevel_page_backup_copilot');

					if ($target.length) {
						$target.pointer({
							content: '<?php echo wp_kses_post( addslashes( $pointer_content ) ); ?>',
							position: {
								edge: 'left',
								align: 'center'
							},
							close: function() {
								$.post(bkpc_pointer.ajaxurl, {
									action: 'dismiss_bkpc_pointer',
									pointer: 'bkpc_pointer',
									nonce: '<?php echo esc_js( wp_create_nonce( 'bkpc_pointer_nonce' ) ); ?>'
								});
							}
						}).pointer('open');
					}
				});
			</script>
			<?php
		}

		/**
		 * Register dismissal action via AJAX.
		 */
		public function dismiss_wp_pointer() {
			// Verify nonce.
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'bkpc_pointer_nonce' ) ) {
				wp_send_json_error( 'Invalid nonce' );
			}

			if ( isset( $_POST['pointer'] ) ) {
				update_user_meta(
					get_current_user_id(),
					sanitize_text_field( wp_unslash( $_POST['pointer'] ) ),
					true
				);
			}

			// Properly terminate the AJAX request.
			wp_send_json_success( 'Pointer dismissed successfully!' );
		}

		/**
		 * Reset WP Pointer dismissal for all users.
		 * Called on plugin deactivation.
		 */
		public function reset_pointer() {
			// Delete the dismissal flag for all users.
			$users = get_users( array( 'fields' => array( 'ID' ) ) );

			if ( empty( $users ) ) {
				return;
			}

			foreach ( $users as $user ) {
				delete_user_meta( $user->ID, 'bkpc_pointer' );
			}
		}
	}
}
