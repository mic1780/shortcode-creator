jQuery(function() {
	
	jQuery('[name="newCode[Name]"]').on('change', function(e) {
		if (jQuery(this).val() == 'index' || jQuery(this).val() == 'format')
			alert('Name cannot be index or format.');
		
		jQuery(this).val( jQuery(this).val().replace(/[\s]+/, '_').toLowerCase() );
	});
	
	jQuery('[name="newCode[Attributes]"]').on('change', function(e) {
		jQuery(this).val( jQuery(this).val().toLowerCase().replace(/[^a-z\n\r]+/, '') );
	});
});