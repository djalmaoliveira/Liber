<?php

$aConfigs['configs']['APP_MODE']    = 'PROD';
$aConfigs['routes']                 = Array();
$aConfigs['db']['default']          = Array('localhost','liber_panel','root','', 'mysql');



$route      = &$aConfigs['routes'];
# $route[URI][METHOD] = Array('ControllerName', 'MethodName', 'ModuleName')

$route['/']['*']                        = Array('MainController');
$route['/error']['*']                   = Array('SysErrorController', 'error');

?>