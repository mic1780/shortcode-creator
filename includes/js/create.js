jQuery(function() {
	
	jQuery('[name="newCode[Name]"]').on('change', function(e) {
		if (jQuery(this).val() == 'index' || jQuery(this).val() == 'format')
			alert('Name cannot be index or format.');
		
		jQuery(this).val( jQuery(this).val().replace(/[\s]+/, '_').toLowerCase() );
	});
	
	jQuery('[name="newCode[Attributes]"]').on('change', function(e) {
		jQuery(this).val( jQuery(this).val().toLowerCase().replace(/[^a-z\n\r]+/, '') );
	});
	
	jQuery('[name="newCode[FunctionCode]"]').on('change', function(e) {
		var tagsToChange = Array('script','noscript');
		var regexTagsToChange =	'(?=' + tagsToChange.join('|') + ')';
		var regPatt = new RegExp("<(\\/?)(?!')" + regexTagsToChange + "([^\\n\\r>]*)>", "g");
		var shouldReplace;
		
		if ( jQuery(this).val().search(regPatt) === -1 ) {
			shouldReplace = false;
		} else {
			shouldReplace = true;
		}//END IF
		
		if (shouldReplace) {
			jQuery(this).val( jQuery(this).val().replace(regPatt, "[$1$2]") );
		}//END IF
		
	});
});