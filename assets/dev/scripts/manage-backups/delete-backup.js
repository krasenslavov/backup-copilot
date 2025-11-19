/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Delete backup
BKPC.deleteBackup = function (event) {
	event.preventDefault();

	var $elem = $(event.target);
	var $form = $(event.target).closest("form");

	// Show confirmation modal
	BKPC.showModal({
		title: "Delete Backup",
		message: "Are you sure you want to delete this backup? This action cannot be undone.",
		confirmText: "Delete",
		cancelText: "Cancel",
		confirmClass: "button-primary button-red",
		onConfirm: function () {
			// Proceed with deletion
			BKPC.setStatus($form.closest("table").find("td"), "wait");

			$.ajax({
				method: "post",
				url: bkpc.ajaxUrl,
				data: {
					action: "delete_backup",
					nonce: bkpc.ajaxNonce,
					uuid: $form.find("input[name=uuid]").val()
				},
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
		},
		onCancel: function () {
			// Do nothing, modal will close
		}
	});
};
