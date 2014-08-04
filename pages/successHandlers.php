<?php
//successHandlers.php
//include this file when you want to handle some successful actions ($this->status will be 1 when no errors occur)

if (! defined('SCODE_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF


$status =	$this->status;
$pa =	(isset($_POST['pa']) ? $_POST['pa'] : (isset($_GET['pa']) ? $_GET['pa'] : ''));

//if we have an error, just dont bother with the rest of the script
if ($status !== 1 || $pa === '') {
	return;
}//END IF

$output =	'';

/*
 *	Action List:
 *		create
 *		edit
 */

switch (strtolower($pa)) {
	case	'create':
		$output =	"Successfully created shortcode: " . (isset($_POST['newCode']['Name']) ? $_POST['newCode']['Name'] : '(code unknown. potential attack!)') . ".";
		break;
	case	'edit':
		$output =	"All shortcodes were edited successfully.";
		break;
	default:
		$output =	"We dont know what just succeeded but it did. But should it have?";
		break;
}//END SWITCH

if ($output === '') {
	return;
}//END IF

scode_add_stylesheet('scode_handler_styles', 'handler_styles.css');
scode_add_script('scode_handler_script', 'handler_script.js');
?>
<div id="handlerContainer" class="dn">
	<div id="successText"><?php echo $output; ?></div>
</div>