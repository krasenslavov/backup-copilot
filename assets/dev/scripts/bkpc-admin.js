/** @format */

// Load core initialization
require("./admin/core.js");

// Load admin functionality
require("./admin/timer.js");
require("./admin/progressbar.js");
require("./admin/notifications.js");
require("./admin/modal.js");
require("./admin/drawer.js");
require("./admin/find-replace.js");
require("./admin/generate-uuid.js");
require("./admin/reset-settings.js");
require("./admin/utilities.js");

// Load AJAX handler
require("./common/ajax-handler.js");

// Load backup actions
require("./manage-backups/create-backup.js");
require("./manage-backups/delete-backup.js");
require("./manage-backups/delete-all-backups.js");
require("./manage-backups/download-backup.js");
require("./manage-backups/export-backup.js");
require("./manage-backups/export-download-cleanup.js");
require("./manage-backups/restore-preview.js");
require("./manage-backups/restore-backup.js");
require("./manage-backups/upload-backup.js");

// Event listeners
(function ($) {
	// Admin
	$(document).on("click", ".bkpc-notice-dismiss", BKPC.dismissNotice);
	$(document).on("click", ".bkpc-drawer-toggle", BKPC.toggleDrawer);
	$(document).on("click", ".bkpc-advanced-options-toggle", BKPC.toggleAdvancedOptions);
	$(document).on("click", ".bkpc-add-find-replace-row", BKPC.addFindReplaceRow);
	$(document).on("click", ".bkpc-remove-find-replace-row", BKPC.removeFindReplaceRow);
	$(document).on("click", "#bkpc-reset-settings", BKPC.resetSettings);
	$(document).on("change", '.bkpc-custom-file-upload input[name="backup-file"]', BKPC.showCustomFileUploadURL);

	// Manage backups
	$(document).on("click", 'button[name="create-backup"]', BKPC.createBackup);
	$(document).on("click", 'button[name="delete-backup"]', BKPC.deleteBackup);
	$(document).on("click", 'button[name="delete-all-backups"]', BKPC.deleteAllBackups);
	$(document).on("click", 'button[name="download-backup"]', BKPC.downloadBackup);
	$(document).on("click", 'button[name="export-backup"]', BKPC.exportBackup);
	$(document).on("click", ".bkpc-export-download", BKPC.exportDownloadCleanup);
	$(document).on("click", 'button[name="restore-backup"]', BKPC.initiateRestore);
	$(document).on("click", 'button[name="upload-backup"]', BKPC.uploadBackup);
})(jQuery);
