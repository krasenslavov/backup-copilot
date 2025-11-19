/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Upload backup
BKPC.uploadBackup = function (event) {
	event.preventDefault();

	var $elem = $(event.target);
	var $form = $(event.target).closest("form");
	var $form_data = new FormData();

	$form_data.append("action", "upload_backup");
	$form_data.append("nonce", bkpc.ajaxNonce);
	$form_data.append("file", $('input[name="backup-file"]')[0].files[0]);

	$.ajax({
		method: "post",
		url: bkpc.ajaxUrl,
		data: $form_data,
		cache: false,
		processData: false,
		contentType: false,
		beforeSend: function () {
			BKPC.ajaxBeforeSend($elem);
		},
		success: function (data, status, jqxhr) {
			BKPC.ajaxSuccess($elem, data, true);
			$form.find('input[name="backup-file"]+span').text("Choose Backup File...");
		},
		error: function (jqxhr, status, error) {
			BKPC.ajaxError($elem, "Ajax error! Backup upload failed.");
		}
	});
};
