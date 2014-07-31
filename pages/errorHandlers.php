<?php

if (! defined('SCODE_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

$status =	$this->status;
$error =		$this->error;

if ($status === 1)
	return;

$output =	'';

//first lets do an if statement to see if the status is 0 (maybe add different codes later on?)
if ($status === 0) {
	/*
	 *	Error List:
	 *		codeExists
	 *		createFileFailed
	 *		invalidName
	 *		missingActionFile
	 *		missingVariables
	 */

	switch (strtolower($error)) {
		case	'codeexists':
			$output =	"ERROR: A shortcode already exists for the name you provided. Please change the name you provided and try again.";
			break;
		case	'createfilefailed':
			$output =	"ERROR: Failed to create your shortcode file. Please try again.";
			break;
		case	'invalidname':
			$output =	"ERROR: Action failed because the provided name was invalid. Make sure the shortcode name is not 'index' or 'format' and try again.";
			break;
		case	'missingactionfile':
			$output =	"ERROR: Cannot perform actions because the actions file does not exist! Did you delete it or change where it is located?";
			break;
		case	'missingvariables':
			$output =	"ERROR: Some of the parameters required to complete your request are missing. Make sure you have everything you need and try again.";
			break;
		default:
			$output =	"An unknown error has occured. Sorry. Error was: " . $error;
			break;
	}//END SWITCH

}//END IF

if ($output === '') {
	return;
}//END IF

scode_add_stylesheet('scode_handler_styles', 'handler_styles.css');
scode_add_script('scode_handler_script', 'handler_script.js', array('jquery-core'));
?>
<div id="handlerContainer" class="dn">
	<div id="errorText"><?php echo $output; ?></div>
</div>