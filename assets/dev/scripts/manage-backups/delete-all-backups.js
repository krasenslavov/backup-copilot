/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Delete all backups
BKPC.deleteAllBackups = function (event) {
	event.preventDefault();

	var $elem = $(event.target);
	var $form = $(event.target).closest("form");

	BKPC.showModal({
		title: "Delete All Backups",
		message:
			"<strong>⚠️ EXTREME CAUTION:</strong> You are about to delete ALL backup files permanently.<br/><br/>" +
			"This action will:<br/>" +
			"• Delete all backup directories<br/>" +
			"• Remove all database exports (.sql files)<br/>" +
			"• Remove all content archives (.zip files)<br/>" +
			"• Empty the entire .bkps directory<br/><br/>" +
			"<strong>This action CANNOT be undone!</strong><br/><br/>" +
			"Are you absolutely sure you want to proceed?",
		confirmText: "Yes, Delete Everything",
		cancelText: "Cancel",
		confirmClass: "button-primary button-red",
		onConfirm: function () {
			// Proceed with deletion
			$.ajax({
				method: "post",
				url: bkpc.ajaxUrl,
				data: {
					action: "delete_all_backups",
					nonce: bkpc.ajaxNonce
				},
				beforeSend: function () {
					BKPC.ajaxBeforeSend($elem);
				},
				success: function (response) {
					if (response.success) {
						BKPC.showToast(response.data.message, "success");
						// Reload page after short delay
						setTimeout(function () {
							location.reload();
						}, 2000);
					} else {
						BKPC.ajaxError($elem, response.data.message || "Failed to delete backups.");
					}
				},
				error: function (jqxhr, status, error) {
					BKPC.ajaxError($elem, "Ajax error! Failed to delete all backups.");
				}
			});
		}
	});
};
