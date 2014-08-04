<?php

if (! defined('SCODE_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

global $nL, $excludeThese;

$pa =	(isset($_POST['pa']) ? $_POST['pa'] : (isset($_GET['pa']) ? $_GET['pa'] : ''));

switch (strtolower($pa)) {
	case	'edit':
		$code =	(isset($_POST['code']) ? $_POST['code'] : (isset($_GET['code']) ? $_GET['code'] : array()));
		if (count($code) > 0) {
			foreach ($code as $key => $row) {
				if ($row['Name'] == '') {
					$this->status =	0;
					$this->error =		'missingVariables';
					break;
				}//END IF
				if (in_array($row['Name'].'.php', $excludeThese)) {
					$this->status =	0;
					$this->error =		'invalidName';
					break;
				}//END IF
			}//END FOREACH LOOP
		}//END IF
		
		if ($this->status != 0) {
			if (count($code) > 0) {
				foreach ($code as $key => $row) {
					if (scode_write_code_file($row) === false) {
						$this->status =	0;
						$this->error =		'editFileFailed';
					}//END IF
				}//END FOREACH LOOP
			}//END IF
		}//END IF
			
		break;
	case	'create':
		$codeInfo =	$_POST['newCode'];
		
		if ($codeInfo['Name'] == '') {
			$this->status =	0;
			$this->error =		'missingVariables';
			break;
		}//END IF
		
		if (in_array($codeInfo['Name'].'.php', $excludeThese)) {
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
		
		$res =	scode_write_code_file($codeInfo);
		
		if ($res === false) {
			$this->status =	0;
			$this->error =		'createFileFailed';
			break;
		}//END IF
		
		break;
}//END SWITCH

?>