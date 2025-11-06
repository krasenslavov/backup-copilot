/** @format */

// Initialize BKPC namespace on window object
window.BKPC = window.BKPC || {};
var BKPC = window.BKPC;
var $ = jQuery;

// Core properties
BKPC.uuid = Math.floor(new Date().getTime() / 1000);
BKPC.timerInstance;
BKPC.progressBarInstance;
BKPC.progressNoticeInstance;

// Dismiss notice
BKPC.dismissNotice = function (event) {
	event.preventDefault();
	$elem = $(event.target);
	$elem.closest(".bkpc-notice-dimiss.is-dismissible").remove();
};

// Show custom file upload URL
BKPC.showCustomFileUploadURL = function (event) {
	event.preventDefault();
	$elem = $(event.target);
	$span = $(".bkpc-custom-file-upload span").empty();
	$elem.val() ? $span.text($elem[0].files[0].name) : $span.text("Choose Backup File...");
};

// Toggle advanced options
BKPC.toggleAdvancedOptions = function (event) {
	event.preventDefault();
	$elem = $(event.target);
	$elem.find("i.dashicons").toggleClass("dashicons-arrow-right").toggleClass("dashicons-arrow-down");
	$(".bkpc-advanced-options").fadeToggle();
};

// Timer functions
BKPC.startTimer = function ($container) {
	var timer = 0,
		minutes,
		seconds;
	BKPC.timerInstance = setInterval(function () {
		minutes = parseInt(timer / 60, 10);
		seconds = parseInt(timer % 60, 10);
		minutes = minutes < 10 ? "0" + minutes : minutes;
		seconds = seconds < 10 ? "0" + seconds : seconds;
		$container.text(minutes + ":" + seconds);
		timer++;
	}, 1000);
};

BKPC.stopTimer = function ($container) {
	clearInterval(BKPC.timerInstance);
	$container.empty();
};

// Progress bar functions
BKPC.startProgressBar = function ($container) {
	var width;
	$container.append($("<div/>").addClass("bkpc-progressbar"));
	BKPC.progressBarInstance = setInterval(function () {
		width = $(".bkpc-progressbar").width() + 10;
		if (width >= $(document).width() - width) {
			$(".bkpc-progressbar").css({ width: "0px" });
		} else {
			$(".bkpc-progressbar").css({ width: width + "px" });
		}
	}, 100);
};

BKPC.stopProgressBar = function ($container) {
	clearInterval(BKPC.progressBarInstance);
	$container.find(".bkpc-progressbar").remove();
};

BKPC.progressBar = function ($container) {
	$container.append($("<div/>").addClass("bkpc-progressbar"));
	BKPC.startProgressBar();
};

// Progress notice functions
BKPC.startProgressNotice = function ($container) {
	var backup_progress_url = bkpc_create_backup.url + BKPC.uuid + "/progress.txt";
	BKPC.progressNoticeInstance = setInterval(function () {
		$.ajax({
			method: "get",
			url: backup_progress_url,
			xhr: function () {
				var xhr = $.ajaxSettings.xhr();
				xhr.onprogress = function (event) {
					$.get(backup_progress_url, function (data) {
						var re = new RegExp(/\[Done\]/, "g");
						BKPC.showNotice(data.replace(re, "<strong>[Done]</strong><br />"), "notice-info bkpc-notice-progress");
					});
				};
				return xhr;
			}
		});
	}, 1000);
};

BKPC.stopProgressNotice = function () {
	clearInterval(BKPC.progressNoticeInstance);
};

// Show notice
BKPC.showNotice = function (message, className = "notice-success") {
	$(".bkpc-notice-container").empty();
	$(".bkpc-notice-container").append(
		$("<div/>")
			.addClass("notice bkpc-notice-dimiss is-dismissible " + className)
			.html(
				'<button type="button" class="notice-dismiss bkpc-notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button><p>' +
					message +
					"</p>"
			)
	);
};

// Reload all backups
BKPC.reloadAllBackups = function ($button) {
	$("#bkpc-all-backups-container").load(" #bkpc-all-backups-container > *");
	$button.prop("disabled", false);
};

// Get text field array
BKPC.getTextFieldArray = function ($elem) {
	var array = [];
	$elem.each(function () {
		array.push($(this).val());
	});
	return array;
};

// Human file size
BKPC.humanFileSize = function (bytes, si = false, dp = 1) {
	var thresh = si ? 1000 : 1024;
	if (Math.abs(bytes) < thresh) {
		return bytes + " B";
	}
	var units = si
		? ["kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"]
		: ["KiB", "MiB", "GiB", "TiB", "PiB", "EiB", "ZiB", "YiB"];
	let u = -1;
	var r = 10 ** dp;
	do {
		bytes /= thresh;
		++u;
	} while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);
	return bytes.toFixed(dp) + " " + units[u];
};

// Set status visual feedback
BKPC.setStatus = function ($form, status) {
	var $tableCell = $form.closest("table").find("td").css("transition", "background-color 600ms ease-in-out");
	var styles = {
		wait: { background: "transparent", opacity: "0.25" },
		success: { background: "#B8E6C1", opacity: "1" },
		error: { background: "#F8C4C4", opacity: "1" }
	};
	// Apply style based on status
	$tableCell.css(styles[status] || {});
	// Reset for success or error
	if (status === "success" || status === "error") {
		setTimeout(function () {
			$tableCell.css({ background: "transparent", opacity: "1" });
		}, 1000);
	}
};

// Document ready events
(function ($, undefined) {
	$(document).on("click", ".bkpc-notice-dismiss", BKPC.dismissNotice);
	$(document).on("click", ".bkpc-advanced-options-toggle", BKPC.toggleAdvancedOptions);
	$(document).on("change", '.bkpc-custom-file-upload input[name="backup-file"]', BKPC.showCustomFileUploadURL);
})(jQuery);
