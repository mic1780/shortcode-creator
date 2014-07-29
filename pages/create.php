<?php

if (! defined('SCODE_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

global $nL;

scode_add_script('scode_general_settings', 'general_settings.js');

//glob the shortcode files and parse the contents.


?>

<div id="scode-admin">
	<!--Success handler include here-->
	<!--Error handler include here-->
	<h1>
		<?php echo get_admin_page_title(); ?>
	</h1>
	<hr>
	<h2>
		Current Shortcodes
	</h2>
</div>