/** @format */

var BKPC = window.BKPC || {};
var $ = jQuery;

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
