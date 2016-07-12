<?php
/**
* Arika Subtitle Engine
* by DSR! 
* https://github.com/xchwarze/Arika
*/
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit;

define('BASEFOLDER', dirname(__FILE__));
require_once BASEFOLDER . '/classes/wp-integration.php';
require_once BASEFOLDER . '/classes/install.php';
Installer::uninstall();

//delete_option('ArikaOpts');
remove_shortcode('arika-integration');
