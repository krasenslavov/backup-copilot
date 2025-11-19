<?php
/**
 * Handle notice dismissal via AJAX and URL parameters.
 *
 * @package    DEVRY\BKPC
 * @copyright  Copyright (c) 2025, Developry Ltd.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU Public License
 * @since      1.1
 */

namespace DEVRY\BKPC;

! defined( ABSPATH ) || exit; // Exit if accessed directly.

/**
 * Dismiss the admin notice related to rating if the user chooses to do so.
 */
function bkpc_dismiss_rating_notice() {
	$action   = ( isset( $_REQUEST['action'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';
	$_wpnonce = ( isset( $_REQUEST['_wpnonce'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

	if ( empty( $action ) || empty( $_wpnonce ) ) {
		return;
	}

	if ( 'bkpc_rating_notice_dismiss' === $action ) {
		if ( wp_verify_nonce( $_wpnonce, 'bkpc_rating_notice_nonce' ) ) {
			add_option( 'bkpc_rating_notice_dismissed', true );
		}
	}
}

add_action( 'admin_init', __NAMESPACE__ . '\bkpc_dismiss_rating_notice' );

/**
 * Dismiss the upgrade notice, if the user chooses to do so.
 */
function bkpc_dismiss_upgrade_notice() {
	$action   = ( isset( $_REQUEST['action'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';
	$_wpnonce = ( isset( $_REQUEST['_wpnonce'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

	if ( empty( $action ) || empty( $_wpnonce ) ) {
		return;
	}

	if ( 'bkpc_dismiss_upgrade_notice' === $action ) {
		// Verify nonce (will exit if invalid)
		check_admin_referer( 'bkpc_upgrade_notice_nonce' );

		// Ensure only users with the right capability can dismiss
		if ( current_user_can( 'manage_options' ) ) {
			add_option( 'bkpc_upgrade_notice', true );
		}
	}
}

add_action( 'admin_init', __NAMESPACE__ . '\bkpc_dismiss_upgrade_notice' );

/**
 * Dismiss the admin notice related to onboarding if the user chooses to do so.
 */
function bkpc_dismiss_onboarding_notice() {
	$action   = ( isset( $_REQUEST['action'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';
	$_wpnonce = ( isset( $_REQUEST['_wpnonce'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

	if ( empty( $action ) || empty( $_wpnonce ) ) {
		return;
	}

	if ( 'bkpc_onboarding_notice_dismiss' === $action ) {
		if ( wp_verify_nonce( $_wpnonce, 'bkpc_onboarding_notice_nonce' ) ) {
			add_option( 'bkpc_onboarding_notice_dismissed', true );
		}
	}
}

add_action( 'admin_init', __NAMESPACE__ . '\bkpc_dismiss_onboarding_notice' );
