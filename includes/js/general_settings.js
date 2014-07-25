jQuery(function() {
	
	jQuery('[name="newCustom[ID]"]').on('change', function(e) {
		var thisVal =	jQuery(this).val();
		if ( jQuery('[name="ac_o[' + thisVal + '][0][ID]"]').length > 0 ) {
			jQuery('[name="newCustom[FilePath]"]').val( jQuery('[name="ac_o[' + thisVal + '][0][FilePath]"]').val() ).attr('readonly', true);
			jQuery('[name="newCustom[FileName]"]').val( jQuery('[name="ac_o[' + thisVal + '][0][FileName]"]').val() ).attr('readonly', true);
		} else {
			jQuery('[name="newCustom[FilePath]"]').val( '' ).attr('readonly', false);
			jQuery('[name="newCustom[FileName]"]').val( '' ).attr('readonly', false);
		}//END IF
	});
	
	jQuery('#newCustomReset').on('click', function(e) {
		//reset inputs with type=text
		jQuery('input[type="text"][name^="newCustom"]').each(function(index, el) {
			jQuery(this).val('');
		});
		//reset textareas
		jQuery('textarea[name^="newCustom"]').each(function(index, el) {
			jQuery(this).text('');
		});
		jQuery('select[name^="newCustom"]').val( jQuery('select[name^="newCustom"] option:last-child').val() )
		jQuery('select[name^="newCustom"]').trigger('change');
	});
	
	jQuery('[name="newCustom[NewCode]"], [name="newCustom[OldCode]"]').on('change', function(e) {
		var regPatt =	/<(\/?)(?!')([^\n\r>]*)>/g;
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