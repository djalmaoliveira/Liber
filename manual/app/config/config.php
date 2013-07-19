<?php
error_reporting(E_ALL | E_STRICT);



$aConfigs['configs']['APP_MODE']    = 'DEV';
$aConfigs['routes']                 = Array();
$aConfigs['db']['default']          = Array();

$route      = &$aConfigs['routes'];
/*
    $route[URI][METHOD] = Array()
*/
$route['/']['*']                 = Array('MainController');
$route['/notfound']['*']         = Array('NotFoundController');

?>