<?php
/*
Plugin Name: Arika Subtitle Engine
Plugin URI: https://github.com/xchwarze/Arika
Description: Online colaborative subtitle creator and editor. For use create a page with [arika-integration] shortcode
Version: 1.0
Author: DSR!
Author URI: https://github.com/xchwarze/Arika
*/

/**
* Arika Subtitle Engine
* by DSR! 
* https://github.com/xchwarze/Arika
*/
class ArikaWPBridge
{
	public static function install() {
		define('BASEFOLDER', dirname(__FILE__));
		require_once BASEFOLDER . '/include/wp-integration.php';
		require_once BASEFOLDER . '/include/install.php';
		Installer::install();
	}

	public static function shortcode($params) {
		define('BASEFOLDER', dirname(__FILE__));
		include_once BASEFOLDER . '/index.php';
	}
}

register_activation_hook(__FILE__, array('ArikaWPBridge', 'install'));
add_shortcode('arika-integration', array('ArikaWPBridge', 'shortcode'));
