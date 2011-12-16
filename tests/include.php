<?php

// include Liber framework
include_once realpath(dirname(__FILE__)."/../framework/")."/Liber.php";

// simulates on HOST
$_SERVER['HTTP_HOST']    = 'liber';
$_SERVER['SERVER_ADMIN'] = 'server_admin@localhost';

// prepares enviroment to Liber application
Liber::loadApp( realpath(dirname(__FILE__).'/../examples/test_unit/').'/' );


?>