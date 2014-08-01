<?php

if (! defined('SCODE_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

global $nL, $excludeThese;
$nL =	'
';
$excludeThese =	array('index.php', 'format.php');

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

function scode_add_script( $hook = '', $name = '', $deps = array() ) {
	$script_path =	'includes/js/';
	
	//do nothing with empty arguments
	if ($hook == '' || $name == '') {
		return;
	}//END IF
	
	//if file does not exist, dont attempt to add it
	if (! file_exists(SCODE_PLUGIN_DIR . $script_path . $name) ) {
		return;
	}//END IF
	
	wp_register_script( $hook, SCODE_PLUGIN_URL . $script_path . $name, $deps, SCODE_VERSION, false );
	wp_enqueue_script( $hook );
}//END FUNCTION

if (! function_exists('echo_print_r')) {
	function echo_print_r($array = array(), $return = false) {
		$output =	'<pre>' . print_r($array, true) . '</pre>';
			
		if ($return === false) {
			echo	$output;
		} else {
			return	$output;
		}//END IF
		
	}//END FUNCTION
}//END IF

function scode_apply_all_codes() {
	global $excludeThese;
	$codePath =	'includes/shortcodes/';
	$allFiles =	glob(SCODE_PLUGIN_DIR . $codePath . '*.php');
	$fileName =	'';
	if (count($allFiles) > 0) {
		foreach ($allFiles as $key => $file) {
			$fileName =	end(explode('/', $file));
			if (in_array($fileName, $excludeThese))
				continue;
			else
				require_once($file);
		}//END FOREACH LOOP
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

function scode_read_code_files(&$scodeArray) {
	global $nL, $excludeThese;
	$codePath =	'includes/shortcodes/';
	$allFiles =	glob( SCODE_PLUGIN_DIR . $codePath . '*.php' );
	$allFiles =	json_decode(str_replace( SCODE_PLUGIN_DIR . $codePath, '', stripslashes(json_encode($allFiles)) ));
	
	$scodeFiles =	array();
	foreach($allFiles as $fileName) {
		if (in_array($fileName, $excludeThese))
			continue;
		$scodeFiles[] =	$fileName;
	}//END FOREACH LOOP
	unset($allFiles);
	
	//we have a list of all valid shortcode files. lets retrieve their data
	//the positions of our data we need vary depending on how the user created the shortcode.
	//so we need the positions of when attributes and function code start and end
	//lets get to it
	
	//needs some constants
	$addToAtt =		strlen('shortcode_atts(array(');
	$addToDep =		strlen("\t//dependencies here".$nL);
	$addToFunc =	strlen("\t//function code here".$nL);
	
	if (count($scodeFiles) > 0) {
		foreach ($scodeFiles as $key => $fileName) {
			$attributeArray =	array();
			$defaultsArray =	array();
			$depLines =			array();
			$funcLines =		array();
			$name =	rtrim($fileName, '.php');
			//get file
			$content =	file_get_contents( SCODE_PLUGIN_DIR . $codePath . $fileName );
			
			//parse the attributes
			$attStart =		strpos($content, 'shortcode_atts(array(') + $addToAtt;
			$attLen =		strpos($content, '), $atts);') - $attStart;
			
			if ($attLen > 0) {
				$attLines =	explode($nL, substr($content, $attStart, $attLen));
				$attCount =	count($attLines) - 2;
				for ($i=1; $i <= $attCount; $i++) {
					list($attribute, $value) =	explode(' => ', $attLines[$i]);
					$attributeArray[] =	trim(ltrim($attribute), "'");
					$defaultsArray[] =	substr($value, 1, strlen($value)-3);
				}//END FOR LOOP
			}//END IF
			
			$depStart =		strpos($content, "\t//dependencies here".$nL) + $addToDep;
			$depLen =		strpos($content, "\t//function code here".$nL) - $depStart;
			if ($depLen > 0) {
				$depLines =	explode($nL, substr($content, $depStart, $depLen));
				foreach ($depLines as $key => $dependency) {
					list($null, $depLines[$key], $null) =	explode("'", $dependency);
					if ($depLines[$key] == 'wp-jquery-ui-dialog')
						unset($depLines[$key]);
				}//END FOREACH LOOP
			}//END IF
			
			//parse for function code
			$funcStart =	strpos($content, "\t//function code here".$nL) + $addToFunc;
			$funcLen =		strpos($content, "\treturn 'Shortcode ".$name) - $funcStart;
			if ($funcLen > 0) {
				$funcLines =	explode($nL, substr($content, $funcStart, $funcLen));
			}//END IF
			
			//add a node to the array passed in
			$scodeArray[] =	array(
				'Name' => $name,
				'Attributes' => implode($nL, $attributeArray),
				'AttrDefaults' => implode($nL, $defaultsArray),
				'Deps' => rtrim(implode($nL, $depLines)),
				'FunctionCode' => rtrim(implode($nL, $funcLines))
			);
			
			unset($content, $attributeArray, $defaultsArray, $depLines, $funcLines);
		}//END FOREACH LOOP
	}//END IF
	
	
	return true;
}//END FUNCTION
?>