/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

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

// Progress notice functions (shows in toast)
BKPC.startProgressNotice = function () {
	// Poll progress via AJAX every second
	BKPC.progressNoticeInstance = setInterval(function () {
		$.ajax({
			url: bkpc.ajaxUrl,
			type: "POST",
			data: {
				action: "get_backup_progress",
				nonce: bkpc.ajaxNonce,
				uuid: BKPC.uuid
			},
			success: function (response) {
				if (response.success && response.data.progress && response.data.progress.length > 0) {
					var formattedData = "";

					// Format each progress item
					$.each(response.data.progress, function (index, item) {
						formattedData += item.message;

						if (item.done) {
							formattedData += ' <strong class="bkpc-progress-done">[Done]</strong>';
						}

						formattedData += "<br />";
					});

					// Only show toast if we have content
					if (formattedData) {
						// Ensure toast container exists
						if ($(".bkpc-toast-container").length === 0) {
							$("body").append($("<div/>").addClass("bkpc-toast-container"));
						}

						// Create or get progress toast
						var $progressToast = $("#bkpc-progress-toast");

						if ($progressToast.length === 0) {
							$progressToast = $("<div/>")
								.attr("id", "bkpc-progress-toast")
								.addClass("bkpc-toast bkpc-toast-progress")
								.html('<div class="bkpc-progress-content"></div>');

							$(".bkpc-toast-container").append($progressToast);
						}

						// Update the toast content
						$("#bkpc-progress-toast .bkpc-progress-content").html(formattedData);
					}
				}
			},
			error: function () {
				// Progress might not be available yet, that's OK
			}
		});
	}, 1000);
};

BKPC.stopProgressNotice = function () {
	clearInterval(BKPC.progressNoticeInstance);

	// Remove progress toast with animation
	var $progressToast = $("#bkpc-progress-toast");

	if ($progressToast.length > 0) {
		$progressToast.addClass("bkpc-toast-hiding");

		setTimeout(function () {
			$progressToast.remove();
		}, 200);
	}
};
