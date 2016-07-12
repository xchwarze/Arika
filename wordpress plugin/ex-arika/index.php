<?php
/**
* Arika Subtitle Engine
* by DSR! 
* https://github.com/xchwarze/Arika
*
* Todo esto fue programado de madrugada basado en una idea y unas pruebas que hice 
* en mis vacaciones del 2012. Mas precisamente fue en el micro llendo de buenos aires
* a cordoba. Recien en 2016 me hice tiempo para este proyecto, la idea es que sea facilmente
* portable a diferentes plugins de otros sistemas.
*/
//if (!defined('BASEFOLDER'))
//	define('BASEFOLDER', dirname(__FILE__));

$controller = (isset($_GET['page']) ? $_GET['page'] : 'index') . 'Controller';
$function = (isset($_GET['func']) ? $_GET['func'] : 'index') . 'Action';

//load section
$file = BASEFOLDER . "/controller/{$controller}.php";
if (!file_exists($file)) {
	$file = BASEFOLDER . '/controller/indexController.php';
}

require BASEFOLDER . '/autoload.php';
require $file;

//load function
$exec = new $controller;
if (!method_exists($exec, $function)) {
	$function = 'indexAction';
}

$exec->$function();