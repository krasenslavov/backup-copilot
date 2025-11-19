/**
 * Restore Preview Modal
 *
 * Handles validation and preview before backup restore.
 *
 * @format
 * @since 1.1
 */

( function ( $ ) {
	'use strict';

	var BKPC = window.BKPC || {};

	/**
	 * Show restore preview modal.
	 *
	 * @param {Event} event Click event.
	 */
	BKPC.showRestorePreview = function ( event ) {
		event.preventDefault();

		var $button = $( event.currentTarget );
		var uuid = $button.data( 'uuid' );
		var nonce = bkpc.ajaxNonce;
		var $icon = $button.find( 'i.dashicons' );

		// Show loading state - change icon to spinner
		$button.prop( 'disabled', true );
		$icon.removeClass( 'dashicons-update-alt' ).addClass( 'dashicons-update bkpc-spin' );

		$.ajax( {
			type: 'POST',
			dataType: 'json',
			url: bkpc.ajaxUrl,
			data: {
				action: 'restore_preview',
				nonce: nonce,
				uuid: uuid,
			},
			success: function ( response ) {
				// Restore original icon
				$button.prop( 'disabled', false );
				$icon.removeClass( 'dashicons-update bkpc-spin' ).addClass( 'dashicons-update-alt' );

				if ( response.success ) {
					BKPC.displayRestoreModal( uuid, response.data );
				} else {
					BKPC.displayValidationErrors( response.data );
				}
			},
			error: function ( xhr, status, error ) {
				// Restore original icon
				$button.prop( 'disabled', false );
				$icon.removeClass( 'dashicons-update bkpc-spin' ).addClass( 'dashicons-update-alt' );
				alert( 'Failed to load restore preview: ' + error );
			},
		} );
	};

	/**
	 * Display restore modal with preview data.
	 *
	 * @param {string} uuid Backup UUID.
	 * @param {Object} data Response data with validation and preview.
	 */
	BKPC.displayRestoreModal = function ( uuid, data ) {
		var validation = data.validation;
		var preview = data.preview;

		// Build modal HTML
		var modalHtml = '<div class="bkpc-restore-preview-modal">';
		modalHtml += '<div class="bkpc-modal-overlay"></div>';
		modalHtml += '<div class="bkpc-modal-content">';
		modalHtml += '<div class="bkpc-modal-header">';
		modalHtml += '<h2>Confirm Restore</h2>';
		modalHtml += '<button class="bkpc-modal-close">&times;</button>';
		modalHtml += '</div>';
		modalHtml += '<div class="bkpc-modal-body">';

		// Backup info
		if ( preview.backup_date ) {
			modalHtml += '<div class="bkpc-preview-section">';
			modalHtml += '<h3>Backup Information</h3>';
			modalHtml += '<p><strong>Created:</strong> ' + preview.backup_date + '</p>';
			modalHtml += '</div>';
		}

		// Notes
		if ( preview.notes ) {
			modalHtml += '<div class="bkpc-preview-section">';
			modalHtml += '<h3>Backup Notes</h3>';
			modalHtml += '<p>' + BKPC.escapeHtml( preview.notes ) + '</p>';
			modalHtml += '</div>';
		}

		// Validation info
		if ( validation.info && validation.info.length > 0 ) {
			modalHtml += '<div class="bkpc-preview-section">';
			modalHtml += '<h3>What will be restored</h3>';
			modalHtml += '<ul class="bkpc-info-list">';
			validation.info.forEach( function ( info ) {
				modalHtml += '<li>' + info + '</li>';
			} );
			modalHtml += '</ul>';
			modalHtml += '</div>';
		}

		// Warnings
		if ( validation.warnings && validation.warnings.length > 0 ) {
			modalHtml += '<div class="bkpc-preview-section bkpc-warning">';
			modalHtml += '<h3>Warnings</h3>';
			modalHtml += '<ul>';
			validation.warnings.forEach( function ( warning ) {
				modalHtml += '<li>' + warning + '</li>';
			} );
			modalHtml += '</ul>';
			modalHtml += '</div>';
		}

		// Database preview
		if ( preview.database ) {
			modalHtml += '<div class="bkpc-preview-section">';
			modalHtml += '<h3>Current Site Statistics</h3>';
			modalHtml += '<table class="bkpc-preview-table">';
			if ( preview.database.current_posts !== undefined ) {
				modalHtml += '<tr><td>Published Posts:</td><td>' + preview.database.current_posts + '</td></tr>';
			}
			if ( preview.database.current_comments !== undefined ) {
				modalHtml += '<tr><td>Comments:</td><td>' + preview.database.current_comments + '</td></tr>';
			}
			if ( preview.database.current_users !== undefined ) {
				modalHtml += '<tr><td>Users:</td><td>' + preview.database.current_users + '</td></tr>';
			}
			modalHtml += '</table>';
			modalHtml += '</div>';
		}

		// Safety warning
		modalHtml += '<div class="bkpc-preview-section bkpc-danger">';
		modalHtml += '<h3>Important</h3>';
		modalHtml += '<p><strong>This action will:</strong></p>';
		modalHtml += '<ul>';
		modalHtml += '<li>Create a safety backup of your current site</li>';
		modalHtml += '<li>Replace your current database and files with the backup</li>';
		modalHtml += '<li>You will be able to rollback if needed</li>';
		modalHtml += '</ul>';
		modalHtml += '<p class="bkpc-warning-text">Make sure you have a recent backup before proceeding.</p>';
		modalHtml += '</div>';

		modalHtml += '</div>';
		modalHtml += '<div class="bkpc-modal-footer">';
		modalHtml += '<button class="button bkpc-modal-cancel">Cancel</button>';
		modalHtml += '<button class="button button-primary bkpc-confirm-restore" data-uuid="' + uuid + '">Confirm Restore</button>';
		modalHtml += '</div>';
		modalHtml += '</div>';
		modalHtml += '</div>';

		// Add modal to page
		$( 'body' ).append( modalHtml );

		// Add event listeners
		$( '.bkpc-modal-close, .bkpc-modal-cancel, .bkpc-modal-overlay' ).on( 'click', BKPC.closeRestoreModal );
		$( '.bkpc-confirm-restore' ).on( 'click', BKPC.confirmRestore );
	};

	/**
	 * Display validation errors.
	 *
	 * @param {Object} data Error data.
	 */
	BKPC.displayValidationErrors = function ( data ) {
		var errorHtml = '<div class="bkpc-restore-preview-modal">';
		errorHtml += '<div class="bkpc-modal-overlay"></div>';
		errorHtml += '<div class="bkpc-modal-content">';
		errorHtml += '<div class="bkpc-modal-header">';
		errorHtml += '<h2>Validation Failed</h2>';
		errorHtml += '<button class="bkpc-modal-close">&times;</button>';
		errorHtml += '</div>';
		errorHtml += '<div class="bkpc-modal-body">';
		errorHtml += '<div class="bkpc-preview-section bkpc-danger">';
		errorHtml += '<h3>Errors</h3>';
		errorHtml += '<p>' + data.message + '</p>';

		if ( data.validation && data.validation.errors && data.validation.errors.length > 0 ) {
			errorHtml += '<ul>';
			data.validation.errors.forEach( function ( error ) {
				errorHtml += '<li>' + error + '</li>';
			} );
			errorHtml += '</ul>';
		}

		errorHtml += '</div>';
		errorHtml += '</div>';
		errorHtml += '<div class="bkpc-modal-footer">';
		errorHtml += '<button class="button bkpc-modal-close">Close</button>';
		errorHtml += '</div>';
		errorHtml += '</div>';
		errorHtml += '</div>';

		$( 'body' ).append( errorHtml );
		$( '.bkpc-modal-close, .bkpc-modal-overlay' ).on( 'click', BKPC.closeRestoreModal );
	};

	/**
	 * Close restore modal.
	 *
	 * @param {Event} event Click event.
	 */
	BKPC.closeRestoreModal = function ( event ) {
		event.preventDefault();
		$( '.bkpc-restore-preview-modal' ).remove();
	};

	/**
	 * Confirm and execute restore.
	 *
	 * @param {Event} event Click event.
	 */
	BKPC.confirmRestore = function ( event ) {
		event.preventDefault();

		var $button = $( event.currentTarget );
		var uuid = $button.data( 'uuid' );

		// Close modal
		$( '.bkpc-restore-preview-modal' ).remove();

		// Trigger the actual restore (from restore-backup.js)
		if ( typeof BKPC.restoreBackup === 'function' ) {
			// Find the restore button by looking for the form with this uuid
			var $form = $( 'form#restore-backup input[name="uuid"][value="' + uuid + '"]' ).closest( 'form' );
			var $restoreButton = $form.find( 'button[name="restore-backup"]' );

			if ( $restoreButton.length ) {
				BKPC.restoreBackup( { currentTarget: $restoreButton[ 0 ], preventDefault: function () {} } );
			}
		}
	};

	/**
	 * Escape HTML to prevent XSS.
	 *
	 * @param {string} text Text to escape.
	 * @return {string} Escaped text.
	 */
	BKPC.escapeHtml = function ( text ) {
		var map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;',
		};
		return text.replace( /[&<>"']/g, function ( m ) {
			return map[ m ];
		} );
	};

	window.BKPC = BKPC;
} )( jQuery );
