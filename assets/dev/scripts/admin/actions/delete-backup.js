/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Delete backup
BKPC.deleteBackup = function (event) {
	event.preventDefault();
	var $elem = $(event.target);
	var $form = $(event.target).closest("form");
	BKPC.setStatus($form.closest("table").find("td"), "wait");
	$.ajax({
		method: "post",
		url: bkpc.ajax_url,
		data: { action: "delete_backup", nonce: bkpc.nonce, uuid: $form.find("input[name=uuid]").val() },
		beforeSend: function () {
			BKPC.ajaxBeforeSend($elem);
			if ($form.find("input[name=download_url]").length && $form.find("input[name=uuid]").length) {
				var url = $form.find("input[name=download_url]").val();
				var filename = $form.find("input[name=uuid]").val() + ".zip";
				var anchorElem = $("<a/>").css("display", "none").attr({ href: url, download: filename });
				$("body").append(anchorElem);
				anchorElem[0].click();
				anchorElem.remove();
			}
		},
		success: function (data, status, jqxhr) {
			BKPC.setStatus($form.closest("table").find("td"), "success");
			BKPC.ajaxSuccess($elem, data);
		},
		error: function (jqxhr, status, error) {
			BKPC.setStatus($form.closest("table").find("td"), "error");
			BKPC.ajaxError($elem, "Ajax error! Backup delete failed.");
		}
	});
};

// Event listener
(function ($, undefined) {
	$(document).on("click", 'button[name="delete-backup"]', BKPC.deleteBackup);
})(jQuery);
