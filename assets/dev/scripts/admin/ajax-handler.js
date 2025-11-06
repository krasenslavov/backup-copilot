/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// AJAX before send handler
BKPC.ajaxBeforeSend = function ($elem) {
	$elem.prop("disabled", true);
	BKPC.startTimer($(".bkpc-timer"));
	BKPC.startProgressBar($("body"));
	BKPC.startProgressNotice();
};

// AJAX success handler
BKPC.ajaxSuccess = function ($elem, data, reload = true) {
	BKPC.stopTimer($(".bkpc-timer"));
	BKPC.stopProgressBar($("body"));
	BKPC.stopProgressNotice();
	BKPC.showNotice(JSON.parse(data));
	if (reload === true) {
		BKPC.reloadAllBackups($elem);
	}
};

// AJAX error handler
BKPC.ajaxError = function ($elem, message) {
	BKPC.stopTimer($(".bkpc-timer"));
	BKPC.stopProgressBar($("body"));
	BKPC.stopProgressNotice();
	BKPC.showNotice(message);
	BKPC.reloadAllBackups($elem);
};
