/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// AJAX before send handler
BKPC.ajaxBeforeSend = function ($button) {
	$button.prop("disabled", true);

	BKPC.startTimer($(".bkpc-timer"));
	BKPC.startProgressBar($("body"));
	BKPC.startProgressNotice();
};

// AJAX success handler
BKPC.ajaxSuccess = function ($button, data, reload = true) {
	BKPC.stopTimer($(".bkpc-timer"));
	BKPC.stopProgressBar($("body"));
	BKPC.stopProgressNotice();
	// WordPress wp_send_json_success returns {success: true, data: "message"}
	// jQuery already parses JSON, so data is an object, not a string
	var message = typeof data === "string" ? data : data.data || data;
	BKPC.showToast(message, "success");

	if (reload === true) {
		BKPC.reloadAllBackups($button);
	}
};

// AJAX error handler
BKPC.ajaxError = function ($button, message) {
	BKPC.stopTimer($(".bkpc-timer"));
	BKPC.stopProgressBar($("body"));
	BKPC.stopProgressNotice();
	BKPC.showToast(message, "error");
	BKPC.reloadAllBackups($button);
};
