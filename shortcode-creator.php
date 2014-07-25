<?php
/*
	Plugin Name: Shortcode Creator
	Plugin URI: https://github.com/mic1780/shortcode-creator
	Description: This is a custom plugin that lets you create your own shortcodes.
	Author: Michael Cummins
	Version: 0.0.1-dev
	Author URI: https://github.com/mic1780/
	Text Domain: 
 */
if( ! defined('ABSPATH') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

define( 'SCODE_DEBUG_MODE', false );
define( 'SCODE_PLUGIN_DEBUG_DIR', 'debug/' );

define( 'SCODE_VERSION', '0.0.1-dev' );
define( 'SCODE_PLUGIN_FILE', __FILE__ );
define( 'SCODE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SCODE_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

//Define file locations
define( 'SCODE_PLUGIN_ERROR_HANDLERS', SCODE_PLUGIN_DIR . 'pages/errorHandlers.php' );
define( 'SCODE_PLUGIN_SUCCESS_HANDLERS', SCODE_PLUGIN_DIR . 'pages/successHandlers.php' );

define( 'SCODE_PLUGIN_LIVE_ARRAY_FILE', SCODE_PLUGIN_DIR . 'includes/infoArray.php' );
define( 'SCODE_PLUGIN_LIVE_LOG_FILE', SCODE_PLUGIN_DIR . 'logs/log.txt' );

define( 'SCODE_PLUGIN_DEBUG_ARRAY_FILE', SCODE_PLUGIN_DIR . SCODE_PLUGIN_DEBUG_DIR . 'infoArray.php' );
define( 'SCODE_PLUGIN_DEBUG_LOG_FILE', SCODE_PLUGIN_DIR . SCODE_PLUGIN_DEBUG_DIR . 'log.txt' );

define( 'SCODE_PLUGIN_ARRAY_FILE', (SCODE_DEBUG_MODE ? SCODE_PLUGIN_DEBUG_ARRAY_FILE : SCODE_PLUGIN_LIVE_ARRAY_FILE) );
define( 'SCODE_PLUGIN_LOG_FILE', (SCODE_DEBUG_MODE ? SCODE_PLUGIN_DEBUG_LOG_FILE : SCODE_PLUGIN_LIVE_LOG_FILE) );

function shortcode_creator_plugin() {
	
	if (file_exists(SCODE_PLUGIN_DIR . 'GitHubPluginUpdater.php')) {
		require_once( SCODE_PLUGIN_DIR . 'GitHubPluginUpdater.php' );
		if ( is_admin() ) {
			new GitHubPluginUpdater( __FILE__, 'mic1780', "shortcode-creator" );
		}//END IF
	}//END IF
	
	/**
	if (! file_exists(SCODE_PLUGIN_DIR . 'includes/functions.php') ) {
		exit('ERROR: Could not find Plugin Customizer function file: includes/functions.php');
	}//END IF
	require_once (SCODE_PLUGIN_DIR . 'includes/functions.php');
	
	add_stylesheet('scode_stylesheet', 'style.css');
	
	// Only load the Admin class on admin requests, excluding AJAX.
	if( is_admin() && ( false === defined( 'DOING_AJAX' ) || false === DOING_AJAX ) ) {
		// Initialize Admin Class
		require_once SCODE_PLUGIN_DIR . 'includes/classes/admin.php';
		new SCODE_Admin();
	}//END IF
	/**/
	
	
}//END FUNCTION

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

?>