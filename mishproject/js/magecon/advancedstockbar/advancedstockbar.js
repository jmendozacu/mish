document.addEventListener("DOMContentLoaded", function(event) {
	if (typeof mageconBarCurrentValue != 'undefined') {
		// Code to run since DOM is loaded and ready.
		var mageconBarValue = 0;
		var mageconBarМаx = document.getElementById("progressbar").getAttribute('max');
		var mageconBarTime = (1000 / mageconBarМаx) * 2;
		
		var mageconBarLoading = function() {
			if (mageconBarValue == mageconBarCurrentValue) {
				clearInterval(mageconBarAnimate);                    
			}
			
			mageconBarValue += 1;
			document.getElementById("progressbar").setAttribute('value', mageconBarValue);
		};
		
		var mageconBarAnimate = setInterval(function() {
			mageconBarLoading();
		}, mageconBarTime);
	}
});

