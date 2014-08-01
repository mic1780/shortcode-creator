<?php
//scode_name.php

function name_func( $atts ) {
	$a = shortcode_atts(array(), $atts);
	
	//dependencies here
	//function code here
	return 'Shortcode scode_name: No return value in function code.';
}
add_shortcode( 'scode_name', 'name_func' );
?>