jQuery(function() {
	
	//delegated code events (should be close or exactly the newCode events)
	jQuery('#scodeEditForm').on('change', '[name^="code["][name$="][Attributes]"]', function(e) {
		jQuery(this).val( jQuery(this).val().toLowerCase().replace(/[^a-z\n\r]+/g, '') );
	});
	jQuery('#scodeEditForm').on('change', '[name^="code["][name$="][AttrDefaults]"]', function(e) {
		jQuery(this).val( jQuery(this).val().replace(/[^a-zA-Z0-9\ \-\n\r]+/g, '') );
	});
	jQuery('#scodeEditForm').on('change', '[name^="code["][name$="][Deps]"]', function(e) {
		jQuery(this).val( jQuery(this).val().toLowerCase().replace(/[^a-z0-9\-\n\r]+/g, '') );
	});
	
	//allowEdits buttons events
	jQuery('.allowEdits', '#scodeEditForm').on('click', function(e) {
		var editRow =	jQuery(this).val();
		var codeRow =	jQuery('#codeRow-' + editRow);
		//make elements
		var nameEl =	scodeCreateInput('hidden', 'code[' + editRow + '][Name]', codeRow.find('td:eq(0)').text());
		var attrEl =	scodeCreateTextarea('code[' + editRow + '][Attributes]', codeRow.find('td:eq(1)').text());
		var defsEl =	scodeCreateTextarea('code[' + editRow + '][AttrDefaults]', codeRow.find('td:eq(2)').text());
		var depsEl =	scodeCreateTextarea('code[' + editRow + '][Deps]', codeRow.find('td:eq(3)').text());
		//add elements
		codeRow.find('td:eq(0)').append( nameEl );
		codeRow.find('td:eq(1)').html( attrEl );
		codeRow.find('td:eq(2)').html( defsEl );
		codeRow.find('td:eq(3)').html( depsEl );
		//remove readonly property from textarea
		codeRow.find('td:eq(4) textarea').prop('readonly', false);
		//disable the clicked button
		jQuery(this).prop('disabled', true);
		jQuery(this).trigger('showmethebutton');
	});
	
	jQuery('#scodeEditForm').one('showmethebutton', '.allowEdits', function(e) {
		jQuery('#editTableFooter', '#scodeEditForm').show();
	});
	
	//newCode element events
	jQuery('[name="newCode[Name]"]').on('change', function(e) {
		if (jQuery(this).val() == 'index' || jQuery(this).val() == 'format')
			alert('Name cannot be index or format.');
		
		jQuery(this).val( jQuery(this).val().toLowerCase().replace(/[^a-z0-9\_\-]+/g, '_') );
	});
	
	jQuery('[name="newCode[Attributes]"]').on('change', function(e) {
		jQuery(this).val( jQuery(this).val().toLowerCase().replace(/[^a-z\n\r]+/g, '') );
	});
	
	jQuery('[name="newCode[AttrDefaults]"]').on('change', function(e) {
		jQuery(this).val( jQuery(this).val().replace(/[^a-zA-Z0-9\ \-\n\r]+/g, '') );
	});
	
	jQuery('[name="newCode[Deps]"]').on('change', function(e) {
		jQuery(this).val( jQuery(this).val().toLowerCase().replace(/[^a-z0-9\-\n\r]+/g, '') );
	});
	
	jQuery('[name$="[FunctionCode]"]', '#scode-admin').on('change', function(e) {//[name="newCode[FunctionCode]"]
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

function scodeCreateInput(elType, elName, elValue) {
	var el =	document.createElement('input');
	el.setAttribute('type', elType);
	el.setAttribute('name', elName);
	el.setAttribute('value', elValue);
	return el;
}//END FUNCTION

function scodeCreateTextarea(elName, elText) {
	var el =	document.createElement('textarea');
	el.setAttribute('name', elName);
	el.setAttribute('rows', 5);
	el.textContent = elText;
	return el;
}//END FUNCTION