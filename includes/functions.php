<?php

if (! defined('SCODE_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

global $nL;
$nL =	'
';

function scode_get_tags_to_change() {
	return array('script');
}//END FUNCTION

function scode_add_stylesheet( $hook = '', $name = '' ) {
	$css_path =	'includes/css/';
	
	//do nothing with empty arguments
	if ($hook == '' || $name == '') {
		return;
	}//END IF
	
	//if file does not exist, dont attempt to add it
	if (! file_exists(SCODE_PLUGIN_DIR . $css_path . $name) ) {
		return;
	}//END IF
	
	wp_register_style( $hook, SCODE_PLUGIN_URL . $css_path . $name );
	wp_enqueue_style( $hook );
}//END FUNCTION

function scode_add_script( $hook = '', $name = '' ) {
	$script_path =	'includes/js/';
	
	//do nothing with empty arguments
	if ($hook == '' || $name == '') {
		return;
	}//END IF
	
	//if file does not exist, dont attempt to add it
	if (! file_exists(SCODE_PLUGIN_DIR . $script_path . $name) ) {
		return;
	}//END IF
	
	wp_register_script( $hook, SCODE_PLUGIN_URL . $script_path . $name );
	wp_enqueue_script( $hook );
}//END FUNCTION

if (! function_exists('echo_print_r'))
function echo_print_r($array = array(), $return = false) {
	$output =	'<pre>' . print_r($array, true) . '</pre>';
		
	if ($return === false) {
		echo	$output;
	} else {
		return	$output;
	}//END IF
	
}//END FUNCTION

/*
 * Code formatting function
 * @Description:	This function is used to get around apaches mod security when posting form data
 *						We can convert specific problem tags from html to '[' and ']' which bypasses security.
 * @Arguments
 * $code: The string containing code to be formatted.
 * $rw:
 *		'read':	Will change html braces to '[' and ']'. Use this when showing code on screen.
 *		'write':	Will change '[' and ']' from submitted code back to html braces to store code exactly how it should be.
 *
 *	'read' is used to read code to screen and 'write' when you are inserting into the array file.
 */
function scode_format_code($code, $rw) {
	global $nL;
	
	if ($rw === 'read') {
		$charOpen =			"<";
		$charClose =		">";
		$replaceOpen =		"[";
		$replaceClose =	"]";
	} else if ($rw === 'write') {
		$charOpen =			"\[";
		$charClose =		"\]";
		$replaceOpen =		"<";
		$replaceClose =	">";
	} else {
		return "Invalid value '$rw' for second argument of scode_format_code function. Code unchanged:" . $nL . $nL . $code;
	}//END IF
	
	$regexTagsToChange =	'(?=' . implode('|', scode_get_tags_to_change()) . ')';
	$code =	str_replace(array('\\"', "\\'"), array('"', "'"), $code);
	
	$output =	preg_replace("/".$charOpen."(\/?)(?!')".$regexTagsToChange."([^\n\r".$charClose."]*)".$charClose."/", $replaceOpen."$1$2".$replaceClose, $code);
	return	$output;
}//END FUNCTION
?>