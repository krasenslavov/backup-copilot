/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Create backup
BKPC.createBackup = function (event) {
	event.preventDefault();
	var $elem = $(event.target);
	var $form = $(event.target).closest("form");

	// Generate a fresh UUID for each backup to prevent overwrites.
	BKPC.uuid = Math.floor(new Date().getTime() / 1000);

	BKPC.setStatus($form.closest("table").find("td"), "wait");
	$.ajax({
		method: "post",
		url: bkpc.ajax_url,
		data: {
			action: "create_backup",
			nonce: bkpc.nonce,
			uuid: BKPC.uuid,
			notes: $form.find('input[name="notes"]').val(),
			advanced_options: BKPC.getTextFieldArray($("input[name='advanced-options']:checked")),
			find_text: BKPC.getTextFieldArray($("input[name='find-text']")),
			replace_with_text: BKPC.getTextFieldArray($("input[name='replace-with-text']"))
		},
		beforeSend: function () {
			BKPC.ajaxBeforeSend($elem);
		},
		success: function (data, status, jqxhr) {
			BKPC.setStatus($form.closest("table").find("td"), "success");
			BKPC.ajaxSuccess($elem, data);
			$form.find('input[name="notes"]').val("");
		},
		error: function (jqxhr, status, error) {
			BKPC.setStatus($form.closest("table").find("td"), "error");
			BKPC.ajaxError($elem, "Server timeout! Created backup files could be corrupted.");
		}
	});
};

// Event listener
(function ($, undefined) {
	$(document).on("click", 'button[name="create-backup"]', BKPC.createBackup);
})(jQuery);
