/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

/**
 * Add another find and replace row
 */
BKPC.addFindReplaceRow = function (event) {
	event.preventDefault();

	var $link = $(event.currentTarget);
	var $container = $link.closest(".bkpc-advanced-options");

	// Find the last find-and-replace-string div
	var $lastRow = $container.find(".find-and-replace-string:last");

	// Clone the row
	var $newRow = $lastRow.clone();

	// Clear the input values and enable them
	$newRow.find("input").val("").prop("disabled", false);

	// Insert the new row before the "Add Row" link's parent paragraph
	$link.parent().before($newRow);

	// Add a remove button if it doesn't exist
	if ($newRow.find(".bkpc-remove-find-replace-row").length === 0) {
		var $removeButton = $(
			'<a href="#" class="bkpc-remove-find-replace-row" title="Remove this row">' +
				'<i class="dashicons dashicons-no-alt"></i> Remove Row' +
				"</a>"
		);
		$newRow.append($removeButton);
	}

	// Focus on the first input of the new row
	$newRow.find("input:first").focus();
};

/**
 * Remove a find and replace row
 */
BKPC.removeFindReplaceRow = function (event) {
	event.preventDefault();

	var $link = $(event.currentTarget);
	var $row = $link.closest(".find-and-replace-string");
	var $container = $link.closest(".bkpc-advanced-options");

	// Only remove if there's more than one enabled row
	var $enabledRows = $container.find(".find-and-replace-string").filter(function () {
		return !$(this).find("input:first").prop("disabled");
	});

	if ($enabledRows.length > 1) {
		$row.fadeOut(300, function () {
			$(this).remove();
		});
	} else {
		alert("You must have at least one find and replace row.");
	}
};
