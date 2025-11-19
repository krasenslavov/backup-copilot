/** @format */

// Initialize BKPC namespace on window object
window.BKPC = window.BKPC || {};
var BKPC = window.BKPC;
var $ = jQuery;

// Core properties
BKPC.uuid = null; // Will be set by generateSecureUUID()
BKPC.timerInstance;
BKPC.progressBarInstance;
BKPC.progressNoticeInstance;
