<?php
//load the required classes
include("/home/megafinancas/app-globals.php");
require("classes/basecontroller.php");
require("classes/basemodel.php");
require("classes/view.php");
require("classes/viewmodel.php");
require("classes/loader.php");

/*
NAO ESTA USANDO MAIS PQ NAO PODE ALTERAR O PHP.INI
==================================================

// define as GLOBALS que tá no php.ini
function loadConfig( $vars = array() ) {
	foreach( $vars as $v ) {
		$_SERVER[$v] = get_cfg_var( "megafinan.$v");
	}
}

// Then call :
$cfg = array( 'BIRDS_HOME', 'BIRDS_HOME_URL', 'BIRDS_DBNAME', 'BIRDS_DBUSER', 'BIRDS_DBPASS', 'BIRDS_SYSTEM_MAIL_ADDR', 'BIRDS_SYSTEM_MAIL_PASS' );
loadConfig( $cfg );
*/
// ===================================

// set default timezone
setlocale(LC_ALL, NULL);
setlocale(LC_ALL, 'pt_BR');
date_default_timezone_set('America/Sao_Paulo');

$loader = new Loader(); //create the loader object
$controller = $loader->createController(); //creates the requested controller object based on the 'controller' URL value
$controller->executeAction(); //execute the requested controller's requested method based on the 'action' URL value. Controller methods output a View.
?>