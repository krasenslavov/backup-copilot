/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Download backup
BKPC.downloadBackup = function (event) {
	event.preventDefault();

	var $elem = $(event.target);
	var $form = $(event.target).closest("form");

	BKPC.setStatus($form.closest("table").find("td"), "wait");

	$.ajax({
		method: "post",
		url: bkpc.ajaxUrl,
		data: {
			action: "download_backup",
			nonce: bkpc.ajaxNonce,
			uuid: $form.find("input[name=uuid]").val()
		},
		beforeSend: function () {
			BKPC.ajaxBeforeSend($elem);
		},
		success: function (data, status, jqxhr) {
			BKPC.setStatus($form.closest("table").find("td"), "success");
			BKPC.ajaxSuccess($elem, data);
		},
		error: function (jqxhr, status, error) {
			BKPC.setStatus($form.closest("table").find("td"), "error");
			BKPC.ajaxError($elem, "Ajax error! Backup download failed.");
		}
	});
};
