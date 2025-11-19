/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

/**
 * Handle export download cleanup.
 * After downloading an exported backup, automatically delete it from .bkps directory.
 */
BKPC.exportDownloadCleanup = function (event) {
	var $link = $(event.currentTarget);
	var uuid = $link.data("uuid");

	if (!uuid) {
		return;
	}

	// Allow download to start, then clean up after a delay
	setTimeout(function () {
		$.ajax({
			method: "post",
			url: bkpc.ajaxUrl,
			data: {
				action: "delete_backup",
				nonce: bkpc.ajaxNonce,
				uuid: uuid
			},
			success: function (data, status, jqxhr) {
				// Show toast notification for cleanup
				BKPC.showToast(
					"Export backup cleaned up successfully! Temporary files have been removed.",
					"success"
				);

				// Reload page after short delay to refresh backup list
				setTimeout(function () {
					location.reload();
				}, 2000);
			},
			error: function (jqxhr, status, error) {
				BKPC.showToast(
					"Failed to clean up export backup. Please delete it manually.",
					"error"
				);
			}
		});
	}, 2000); // 2 second delay to allow download to start
};

/**
 * Show a toast notification.
 *
 * @param {string} message The message to display
 * @param {string} type The type of toast (success, error, info)
 */
BKPC.showToast = function (message, type) {
	type = type || "info";

	// Ensure toast container exists
	if ($(".bkpc-toast-container").length === 0) {
		$("body").append($("<div/>").addClass("bkpc-toast-container"));
	}

	// Create toast element
	var $toast = $('<div class="bkpc-toast bkpc-toast-' + type + '"><p>' + message + "</p></div>");

	// Append to container
	$(".bkpc-toast-container").append($toast);

	// Auto-dismiss after 5 seconds
	setTimeout(function () {
		$toast.addClass("bkpc-toast-hiding");
		setTimeout(function () {
			$toast.remove();
		}, 300);
	}, 5000);
};
