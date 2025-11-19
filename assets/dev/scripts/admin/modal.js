/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Modal functions
BKPC.showModal = function (options) {
	var defaults = {
		title: "Confirm Action",
		message: "Are you sure you want to proceed?",
		confirmText: "Confirm",
		cancelText: "Cancel",
		confirmClass: "button-primary",
		onConfirm: function () {},
		onCancel: function () {}
	};

	var settings = $.extend({}, defaults, options);

	// Remove any existing modal
	$("#bkpc-modal").remove();

	// Create modal HTML
	var $modal = $("<div/>")
		.attr("id", "bkpc-modal")
		.addClass("bkpc-modal")
		.html(
			'<div class="bkpc-modal-overlay"></div>' +
				'<div class="bkpc-modal-content">' +
				'<div class="bkpc-modal-header">' +
				"<h2>" +
				settings.title +
				"</h2>" +
				'<button type="button" class="bkpc-modal-close">&times;</button>' +
				"</div>" +
				'<div class="bkpc-modal-body">' +
				"<p>" +
				settings.message +
				"</p>" +
				"</div>" +
				'<div class="bkpc-modal-footer">' +
				'<button type="button" class="button bkpc-modal-cancel">' +
				settings.cancelText +
				"</button>" +
				'<button type="button" class="button ' +
				settings.confirmClass +
				' bkpc-modal-confirm">' +
				settings.confirmText +
				"</button>" +
				"</div>" +
				"</div>"
		);

	// Append to body
	$("body").append($modal);

	// Show modal with animation
	setTimeout(function () {
		$modal.addClass("bkpc-modal-show");
	}, 10);

	// Handle confirm
	$modal.find(".bkpc-modal-confirm").on("click", function () {
		settings.onConfirm();
		BKPC.closeModal();
	});

	// Handle cancel
	$modal.find(".bkpc-modal-cancel, .bkpc-modal-close").on("click", function () {
		settings.onCancel();
		BKPC.closeModal();
	});

	// Handle overlay click
	$modal.find(".bkpc-modal-overlay").on("click", function () {
		settings.onCancel();
		BKPC.closeModal();
	});

	// Handle ESC key
	$(document).on("keydown.bkpcModal", function (e) {
		if (e.keyCode === 27) {
			// ESC key
			settings.onCancel();
			BKPC.closeModal();
		}
	});
};

BKPC.closeModal = function () {
	var $modal = $("#bkpc-modal");

	if ($modal.length > 0) {
		$modal.removeClass("bkpc-modal-show");

		setTimeout(function () {
			$modal.remove();
			$(document).off("keydown.bkpcModal");
		}, 200);
	}
};
