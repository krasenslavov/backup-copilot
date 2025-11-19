/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

// Export backup
BKPC.exportBackup = function (event) {
	event.preventDefault();

	var $button = $(event.target);
	var $form = $(event.target).closest("form");

	// Generate a secure UUID before exporting backup
	BKPC.generateSecureUUID(function (uuid) {
		$.ajax({
			method: "post",
			url: bkpc.ajaxUrl,
			data: {
				action: "export_backup",
				nonce: bkpc.ajaxNonce,
				uuid: uuid,
				notes: $form.find('textarea[name="notes"]').val(),
				advanced_options: BKPC.getTextFieldArray($("input[name='advanced-options']:checked")),
				find_text: BKPC.getTextFieldArray($("input[name='find-text']")),
				replace_with_text: BKPC.getTextFieldArray($("input[name='replace-with-text']"))
			},
			beforeSend: function () {
				BKPC.ajaxBeforeSend($button);
			},
			success: function (response, status, jqxhr) {
				// Parse the response
				var message = response.data;

				// Remove any previous export notifications
				$(".bkpc-export-notification").remove();

				// Create a persistent notification with the download link
				var $notification = $('<div class="bkpc-export-notification-notice"><p>' + message + "</p></div>");

				// Insert after the export form
				$form.after($notification);

				// Scroll to the notification
				$("html, body").animate({ scrollTop: $notification.offset().top - 100 }, 500);

				// Handle dismiss button
				$notification.find(".notice-dismiss").on("click", function () {
					$notification.fadeOut(300, function () {
						$(this).remove();
					});
				});

				BKPC.ajaxSuccess($button, message, false);
				$form.find('textarea[name="notes"]').val("");
			},
			error: function (jqxhr, status, error) {
				BKPC.ajaxError($button, "Ajax error! Backup export failed." + error);
			}
		});
	});
};
