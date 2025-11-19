/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Dismiss notice
BKPC.dismissNotice = function (event) {
	event.preventDefault();

	$elem = $(event.target);

	$elem.closest(".bkpc-notice-dismiss.is-dismissible").remove();
};

// Show notice (inline, persistent until dismissed)
BKPC.showNotice = function (message, className = "notice-success") {
	$(".bkpc-notice-container").empty();

	$(".bkpc-notice-container").append(
		$("<div/>")
			.addClass("notice bkpc-notice-dismiss is-dismissible " + className)
			.html(
				'<button type="button" class="notice-dismiss bkpc-notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button><p>' +
					message +
					"</p>"
			)
	);
};

// Show toast (popup, auto-dismisses)
BKPC.showToast = function (message, type = "success", duration = 5000) {
	// Ensure toast container exists
	if ($(".bkpc-toast-container").length === 0) {
		$("body").append($("<div/>").addClass("bkpc-toast-container"));
	}

	var toastClass = "bkpc-toast-" + type;
	var $toast = $("<div/>")
		.addClass("bkpc-toast " + toastClass)
		.html(
			"<p>" +
				message +
				'</p><button type="button" class="bkpc-toast-dismiss"><span class="dashicons dashicons-no-alt"></span></button>'
		);

	$(".bkpc-toast-container").append($toast);

	// Manual dismiss on click
	$toast.find(".bkpc-toast-dismiss").on("click", function () {
		BKPC.dismissToast($toast);
	});
};

// Dismiss toast with animation
BKPC.dismissToast = function ($toast) {
	$toast.addClass("bkpc-toast-hiding");

	setTimeout(function () {
		$toast.remove();
	}, 200); // Match animation duration
};
