jQuery(function() {
	jQuery('#handlerContainer').slideToggle(500, function() {
		if (jQuery('#successText').length > 0) {
			setTimeout(function() {
				jQuery('#handlerContainer').slideToggle(500);
			}, 7500);
		}//END IF
	});
});