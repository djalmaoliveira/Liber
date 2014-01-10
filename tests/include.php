<?php

// include Liber framework
include_once realpath(dirname(__FILE__)."/../framework/")."/Liber.php";

// simulates on HOST
$_SERVER['SERVER_NAME']    = 'liber';
$_SERVER['SERVER_ADMIN']   = 'server_admin@localhost';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI']    = 'http://localhost';

// prepares enviroment to Liber application
Liber::loadApp( realpath(dirname(__FILE__).'/../examples/test_unit/').'/' );


ob_start();

?>
