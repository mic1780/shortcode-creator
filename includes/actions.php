<?php

if (! defined('SCODE_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

global $nL;

$pa =	(isset($_POST['pa']) ? $_POST['pa'] : (isset($_GET['pa']) ? $_GET['pa'] : ''));

switch (strtolower($pa)) {
	case	'create':
		$codeInfo =	$_POST['newCode'];
		
		if ($codeInfo['Name'] == '') {
			$this->status =	0;
			$this->error =		'missingVariables';
			break;
		}//END IF
		
		if ($codeInfo['Name'] == 'index' || $codeInfo['Name'] == 'format') {
			$this->status =	0;
			$this->error =		'invalidName';
			break;
		}//END IF
		
		if ( shortcode_exists($codeInfo['Name']) || file_exists( SCODE_PLUGIN_DIR . 'includes/shortcodes/' . $codeInfo['Name'] . '.php' ) ) {
			$this->status =	0;
			$this->error =		'codeExists';
			break;
		}//END IF
		
		//we know its a valid fileName and that the shortcode does not exist so we can create the file.
		
		//get our fotmat file
		$format =	file_get_contents( SCODE_PLUGIN_DIR . 'includes/shortcodes/format.php' );
		//change key words in the file
		$format =	str_replace('scode_name', $codeInfo['Name'], $format);
		$format =	str_replace('name_func', $codeInfo['Name'] . '_func', $format);
		
		//if we have declared attributes, make sure we have a matching number of defaults
		$numOfAttr =	substr_count($codeInfo['Attributes'], $nL);
		$numOfDefault =	substr_count($codeInfo['AttrDefaults'], $nL);
		if ( (strlen($codeInfo['Attributes']) > 0 && strlen($codeInfo['AttrDefaults']) > 0) && ($numOfAttr === $numOfDefault) ) {
			$replaceCode =	'shortcode_atts(array(' . $nL;
			$attrArray =	explode($nL, $codeInfo['Attributes']);
			$defArray =		explode($nL, $codeInfo['AttrDefaults']);
			foreach ($attrArray as $index => $attr) {
				$replaceCode .=	'' .
										"\t\t" . "'" . $attr . "'" . " => " . "'" . $defArray[$index] . "'" . "," . $nL .
										'';
			}//END FOREACH LOOP
			$replaceCode .=	"\t)";
			$format =	str_replace('shortcode_atts(array()', $replaceCode, $format);
			unset($replaceCode);
		}//END IF
		
		if (strlen($codeInfo['Deps']) > 0) {
			$replaceCode =	"\tdependencies here" . $nL;
			$depArray =	explode($nL, $codeInfo['Deps']);
			foreach ($depArray as $dependency) {
				if ( wp_script_is($dependency, 'registered') )
					$replaceCode .=	"\twp_enqueue_script('{$dependency}');" . $nL;
				if ($dependency == 'jquery-ui-dialog')
					$replaceCode .=	"\twp_enqueue_style('wp-jquery-ui-dialog');" . $nL;
			}//END FOREACH LOOP
			$format =	str_replace("\t//dependencies here", $replaceCode, $format);
			unset($replaceCode);
		}//END IF
		
		//next we add in our function code
		if (strlen($codeInfo['FunctionCode']) > 0) {
			$replaceCode =		"\t//function code here" . $nL;
			$replaceCode .=	scode_format_code($codeInfo['FunctionCode'], 'write');
			$format =	str_replace("\t//function code here", $replaceCode, $format);
		}//END IF
		
		//our new shortcode file is ready to go!
		$bytes =	file_put_contents( SCODE_PLUGIN_DIR . 'includes/shortcodes/' . $codeInfo['Name'] . '.php', $format, LOCK_EX );
		
		if ($bytes === false) {
			$this->status =	0;
			$this->error =		'createFileFailed';
			break;
		}//END IF
		
		break;
}//END SWITCH

?>