/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

/**
 * Toggle drawer sections
 */
BKPC.toggleDrawer = function (event) {
	event.preventDefault();

	var $header = $(event.currentTarget);
	var $content = $header.next(".bkpc-drawer-content");
	var $icon = $header.find(".bkpc-drawer-icon");

	// Toggle classes
	$header.toggleClass("bkpc-drawer-active");
	$content.toggleClass("bkpc-drawer-open");

	// Toggle icon
	if ($header.hasClass("bkpc-drawer-active")) {
		$icon.removeClass("dashicons-arrow-right-alt2").addClass("dashicons-arrow-down-alt2");
	} else {
		$icon.removeClass("dashicons-arrow-down-alt2").addClass("dashicons-arrow-right-alt2");
	}
};
