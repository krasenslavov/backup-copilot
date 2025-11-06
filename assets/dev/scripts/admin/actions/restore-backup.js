/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Restore backup
BKPC.restoreBackup = function (event) {
	event.preventDefault();
	var $elem = $(event.target);
	var $form = $(event.target).closest("form");
	BKPC.setStatus($form.closest("table").find("td"), "wait");
	$.ajax({
		method: "post",
		url: bkpc.ajax_url,
		data: { action: "restore_backup", nonce: bkpc.nonce, uuid: $form.find("input[name=uuid]").val() },
		beforeSend: function () {
			BKPC.ajaxBeforeSend($elem);
		},
		success: function (data, status, jqxhr) {
			BKPC.setStatus($form.closest("table").find("td"), "success");
			BKPC.ajaxSuccess($elem, data);
		},
		error: function (jqxhr, status, error) {
			BKPC.setStatus($form.closest("table").find("td"), "error");
			BKPC.ajaxError($elem, "Ajax error! Backup restore failed.");
		}
	});
};

// Event listener
(function ($, undefined) {
	$(document).on("click", 'button[name="restore-backup"]', BKPC.restoreBackup);
})(jQuery);
