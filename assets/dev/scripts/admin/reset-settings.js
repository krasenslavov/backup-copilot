/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Reset settings
BKPC.resetSettings = function (event) {
	event.preventDefault();

	const $elem = $(event.target);
	const $parentCont = $elem.closest(".bkpc-admin");
	const $output = $parentCont.find(".bkpc-notice-container");
	const $loadingBar = $parentCont.find(".bkpc-loading-bar");

	if (!window.confirm("Are you sure you want to RESET ALL options to their defaults?")) {
		return;
	}

	// Show loading bar
	$loadingBar.addClass("bkpc-loading-bar-active");

	// Clear previous output
	$output.removeClass("bkpc-notice-container-active bkpc-notice-success bkpc-notice-error");
	$output.html("");

	$.ajax({
		url: bkpc.ajaxUrl,
		type: "POST",
		data: {
			action: "bkpc_reset_settings",
			_wpnonce: bkpc.ajaxNonce
		},
		success: function (response) {
			$loadingBar.removeClass("bkpc-loading-bar-active");
			$output.addClass("bkpc-notice-container-active");

			// WordPress wp_send_json_success/error format: {success: true/false, data: {...}}
			if (response.success && response.data) {
				// Success
				$output.addClass("bkpc-notice-success");
				$output.html("<p><strong>" + response.data.message + "</strong></p>");

				// Scroll to top
				$("html, body").animate({ scrollTop: 0 }, "fast");

				// Reload page after 2s with hard reload
				setTimeout(function () {
					location.reload(true);
				}, 2000);
			} else {
				// Error
				$output.addClass("bkpc-notice-error");
				const errorMsg = response.data && response.data.message
					? response.data.message
					: "An error occurred while resetting settings.";
				$output.html("<p><strong>" + errorMsg + "</strong></p>");
			}
		},
		error: function () {
			$loadingBar.removeClass("bkpc-loading-bar-active");
			$output.addClass("bkpc-notice-container-active bkpc-notice-error");
			$output.html("<p><strong>An error occurred while resetting settings.</strong></p>");
		}
	});
};
