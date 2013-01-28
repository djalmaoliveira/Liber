<?php

$aConfigs['configs']['APP_MODE']    = 'PROD';
$aConfigs['routes']                 = Array();
$aConfigs['db']['default']          = Array('localhost','database_name','user','password', 'database_type');

$route      = &$aConfigs['routes'];
# $route[URI][METHOD] = Array('ControllerName', 'MethodName', 'ModuleName')

$route['/']['*']                 = Array('MainController');
$route['/notfound']['*']         = Array('NotFoundController');

?>