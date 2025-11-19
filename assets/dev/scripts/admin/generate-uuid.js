/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

/**
 * Generate a cryptographically secure UUID via AJAX.
 *
 * @param {function} callback Function to call with the generated UUID
 */
BKPC.generateSecureUUID = function (callback) {
	$.ajax({
		method: "post",
		url: bkpc.ajaxUrl,
		data: {
			action: "generate_secure_uuid",
			nonce: bkpc.ajaxNonce
		},
		success: function (response) {
			if (response.success && response.data.uuid) {
				BKPC.uuid = response.data.uuid;
				if (typeof callback === "function") {
					callback(response.data.uuid);
				}
			} else {
				// Fallback to timestamp if AJAX fails
				BKPC.uuid = Math.floor(new Date().getTime() / 1000);
				if (typeof callback === "function") {
					callback(BKPC.uuid);
				}
			}
		},
		error: function () {
			// Fallback to timestamp if AJAX fails
			BKPC.uuid = Math.floor(new Date().getTime() / 1000);
			if (typeof callback === "function") {
				callback(BKPC.uuid);
			}
		}
	});
};
