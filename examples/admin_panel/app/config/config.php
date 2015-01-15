<?php

$aConfigs['configs']['APP_MODE']    = 'DEV';
$aConfigs['routes']                 = Array();
$aConfigs['db']['default']          = Array('localhost','liber_panel','root','', 'mysql');



$route      = &$aConfigs['routes'];
# $route[URI][METHOD] = Array('ControllerName', 'MethodName', 'ModuleName')

$route['/']['*']      = Array('MainController');
$route['/user']['*']  = Array('UserController', 'index', 'User');
$route['/error']['*'] = Array('SysErrorController', 'error');

?>