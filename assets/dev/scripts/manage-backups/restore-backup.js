/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Restore backup with validation and preview
BKPC.initiateRestore = function (event) {
	event.preventDefault();

	var $button = $(event.currentTarget);
	var $form = $button.closest("form");
	var uuid = $form.find("input[name=uuid]").val();

	// Show validation and preview modal (from restore-preview.js)
	if (typeof BKPC.showRestorePreview === "function") {
		// Create event object with uuid in data attribute
		var fakeEvent = {
			preventDefault: function () {},
			currentTarget: $button[0]
		};
		// Temporarily add data-uuid attribute for the preview function
		$button.attr("data-uuid", uuid);
		BKPC.showRestorePreview(fakeEvent);
	} else {
		// Fallback to old confirmation modal if preview not loaded
		BKPC.restoreBackupFallback(event);
	}
};

// Actual restore execution (called after preview confirmation)
BKPC.restoreBackup = function (event) {
	event.preventDefault();

	var $button = $(event.currentTarget);
	var $form = $button.closest("form");

	// Proceed with restoration
	BKPC.setStatus($form.closest("table").find("td"), "wait");

	$.ajax({
		method: "post",
		url: bkpc.ajaxUrl,
		data: {
			action: "restore_backup",
			nonce: bkpc.ajaxNonce,
			uuid: $form.find("input[name=uuid]").val()
		},
		beforeSend: function () {
			BKPC.ajaxBeforeSend($button);
			BKPC.startProgressNotice();
		},
		success: function (data, status, jqxhr) {
			BKPC.setStatus($form.closest("table").find("td"), "success");
			BKPC.ajaxSuccess($button, data);
		},
		error: function (jqxhr, status, error) {
			BKPC.setStatus($form.closest("table").find("td"), "error");
			BKPC.ajaxError($button, "Ajax error! Backup restore failed. " + error);
		}
	});
};

// Fallback restore with old modal (if preview not available)
BKPC.restoreBackupFallback = function (event) {
	event.preventDefault();

	var $button = $(event.target);
	var $form = $(event.target).closest("form");

	// Show confirmation modal
	BKPC.showModal({
		title: "Restore Backup",
		message:
			"<strong>Warning:</strong> Restoring this backup will overwrite all current site content, including:<br/><br/>" +
			"• Database tables and data<br/>" +
			"• WordPress files and uploads<br/>" +
			"• Theme and plugin files<br/><br/>" +
			"This action cannot be undone. Are you sure you want to proceed?",
		confirmText: "Restore Backup",
		cancelText: "Cancel",
		confirmClass: "button-primary button-red",
		onConfirm: function () {
			BKPC.restoreBackup({ currentTarget: $button[0], preventDefault: function () {} });
		},
		onCancel: function () {
			// Do nothing, modal will close
		}
	});
};
