<?php
/*
	Plugin Name: Shortcode Creator
	Plugin URI: https://github.com/mic1780/shortcode-creator
	Description: This is a custom plugin that lets you create your own shortcodes.
	Author: Michael Cummins
	Version: 1.0.2
	Author URI: https://github.com/mic1780/
	Text Domain: 
 */
/*
    Wordpress Shortcode Creator plugin
    Copyright (C) 2014  Michael Cummins

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program;
 */
if( ! defined('ABSPATH') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

define( 'SCODE_VERSION', '1.0.2' );
define( 'SCODE_PLUGIN_FILE', __FILE__ );
define( 'SCODE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SCODE_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

//Define file locations
//define( 'SCODE_PLUGIN_ERROR_HANDLERS', SCODE_PLUGIN_DIR . 'pages/errorHandlers.php' );
//define( 'SCODE_PLUGIN_SUCCESS_HANDLERS', SCODE_PLUGIN_DIR . 'pages/successHandlers.php' );

function shortcode_creator_plugin() {
	
	if (! file_exists(SCODE_PLUGIN_DIR . 'includes/functions.php') ) {
		exit('ERROR: Could not find Shortcode Creator function file: includes/functions.php');
	}//END IF
	require_once (SCODE_PLUGIN_DIR . 'includes/functions.php');
	
	// Only load the Admin class on admin requests, excluding AJAX.
	if( is_admin() && ( false === defined( 'DOING_AJAX' ) || false === DOING_AJAX ) ) {
		// Initialize Admin Class
		require_once(SCODE_PLUGIN_DIR . 'includes/classes/admin.php');
		new SCODE_Admin();
	}//END IF
	
	if ((! is_admin()) && ( false === defined( 'DOING_AJAX' ) || false === DOING_AJAX ) ) {
		scode_apply_all_codes();
	}//END IF
	
}//END FUNCTION

add_action('admin_init', 'scode_upgrade_check');
add_action('plugins_loaded', 'shortcode_creator_plugin', 10);

//register_activation_hook( __FILE__, 'scode_install_hook');
function scode_install_hook() {
	require_once(SCODE_PLUGIN_DIR . 'includes/install.php');
	if (function_exists('scode_activate_plugin')) {
		scode_activate_plugin();
	}//END IF
}//END FUNCTION

//register_deactivation_hook( __FILE__, 'scode_uninstall_hook');
function scode_uninstall_hook() {
	require_once(SCODE_PLUGIN_DIR . 'includes/install.php');
	if (function_exists('scode_deactivate_plugin')) {
		scode_deactivate_plugin();
	}//END IF
}//END FUNCTION

function scode_upgrade_check() {
	if (file_exists(SCODE_PLUGIN_DIR . 'includes/scodePluginUpdater.php')) {
		if ( is_admin() ) {
			require_once( SCODE_PLUGIN_DIR . 'includes/scodePluginUpdater.php' );
			new scodePluginUpdater( __FILE__, 'mic1780', "shortcode-creator" );
		}//END IF
	}//END IF
}//END FUNCTION
?>