<?php

$aConfigs = Array('configs'=>Array(), 'routes'=>Array());
$config = &$aConfigs['configs'];
$route = &$aConfigs['routes'];


$config['APP_PATH']   = realpath(dirname(__FILE__)."/../")."/";
$config['BASE_PATH']  = realpath(dirname(__FILE__)."/../../../")."/";
$config['CACHE_PATH'] = $config['APP_PATH']."cache/";

$config['APP_MODE']   = 'DEV';    //for production mode use 'prod'

$config['APP_URL']       = 'http://'.$_SERVER['HTTP_HOST'].$config['SUBFOLDER'];

$config['AUTOROUTE']     = TRUE;
$config['DEBUG_ENABLED'] = TRUE;


/**
 * Path to store logs/profiles when using with the logger tool. This is needed for writing log files and using the log viewer tool
 */
$config['LOG_PATH'] = $system_path."/logs/";



 $dbconfig['DEV']       = array('localhost', 'pizzaonline', 'root', 'root', 'mysql', true);
 $dbconfig['PROD']      = array('localhost', 'pizzaonline', 'root', 'root', 'mysql', true);
 $config['DB_CONFIG']   = $dbconfig;



 
$route['/']            = Array('Main', 'index');



?>
