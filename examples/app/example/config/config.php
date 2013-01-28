<?php

$aConfigs['configs']['APP_MODE']    = 'PROD';
$aConfigs['routes']                 = Array();
$aConfigs['db']['default']          = Array('localhost','database_name','user','password', 'database_type');



$route      = &$aConfigs['routes'];
# $route[URI][METHOD] = Array('ControllerName', 'MethodName', 'ModuleName')

$route['/']['*']                        = Array('MainController');
$route['/direct/route']['*']            = Array('MainController', 'direct');
$route['/process/named/:name:']['*']    = Array('Examples', 'named');
$route['/other/direct/route']['*']      = Array('Examples');

?>