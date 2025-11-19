/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// License version check handler
BKPC.licenceVersionCheck = function (event) {
	event.preventDefault();

	const button = $(this);
	const status = $("#bkpc-version-status");

	button.prop("disabled", true);
	button.find(".dashicons").addClass("spin");
	status.text(bkpc.strings.processing || "Checking for updates...");

	$.ajax({
		url: bkpc.ajaxUrl,
		type: "POST",
		data: {
			action: "bkpc_check_latest_version",
			nonce: bkpc.ajaxNonce
		},
		success: function (response) {
			button.prop("disabled", false);
			button.find(".dashicons").removeClass("spin");

			if (response.status === "success") {
				status.html('<span class="text-color-green">' + response.message + "</span>");
			} else {
				status.html('<span class="text-color-red">' + response.message + "</span>");
			}

			setTimeout(function () {
				status.text("");
			}, 2000);
		},
		error: function () {
			button.prop("disabled", false);
			button.find(".dashicons").removeClass("spin");
			status.html('<span class="text-color-red">Error checking for updates.</span>');

			setTimeout(function () {
				status.text("");
			}, 2000);
		}
	});
};
