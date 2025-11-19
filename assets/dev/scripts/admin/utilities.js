/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

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

// Reload all backups
BKPC.reloadAllBackups = function ($button) {
	$("#bkpc-manage-backups-container").load(" #bkpc-manage-backups-container > *");
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
		}, 2000);
	}
};
