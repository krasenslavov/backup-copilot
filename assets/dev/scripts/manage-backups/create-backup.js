/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Create backup
BKPC.createBackup = function (event) {
	event.preventDefault();
	var $button = $(event.target);
	var $form = $(event.target).closest("form");

	BKPC.setStatus($form.closest("table").find("td"), "wait");

	// Generate a secure UUID before creating backup
	BKPC.generateSecureUUID(function (uuid) {
		$.ajax({
			method: "post",
			url: bkpc.ajaxUrl,
			data: {
				action: "create_backup",
				nonce: bkpc.ajaxNonce,
				uuid: uuid,
				notes: $form.find('textarea[name="notes"]').val(),
				advanced_options: BKPC.getTextFieldArray($("input[name='advanced-options']:checked")),
				find_text: BKPC.getTextFieldArray($("input[name='find-text']")),
				replace_with_text: BKPC.getTextFieldArray($("input[name='replace-with-text']"))
			},
			beforeSend: function () {
				BKPC.ajaxBeforeSend($button);
			},
			success: function (data, status, jqxhr) {
				BKPC.setStatus($form.closest("table").find("td"), "success");
				BKPC.ajaxSuccess($button, data);

				$form.find('textarea[name="notes"]').val("");
			},
			error: function (jqxhr, status, error) {
				BKPC.setStatus($form.closest("table").find("td"), "error");
				BKPC.ajaxError($button, "Server timeout! Created backup files could be corrupted." + error);
			}
		});
	});
};
