/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Export backup
BKPC.exportBackup = function (event) {
	event.preventDefault();
	var $elem = $(event.target);
	var $form = $(event.target).closest("form");
	BKPC.uuid = Math.floor(new Date().getTime() / 1000);
	$.ajax({
		method: "post",
		url: bkpc.ajax_url,
		data: {
			action: "export_backup",
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
			BKPC.ajaxSuccess($elem, data, false);
			$elem.prop("disabled", false);
			$form.find('input[name="notes"]').val("");
		},
		error: function (jqxhr, status, error) {
			BKPC.ajaxError($elem, "Ajax error! Backup export failed.");
		}
	});
};

// Event listener
(function ($, undefined) {
	$(document).on("click", 'button[name="export-backup"]', BKPC.exportBackup);
})(jQuery);
