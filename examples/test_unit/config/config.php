<?php

$aConfigs['configs']['APP_MODE']    = 'PROD';
$aConfigs['routes']                 = Array();
$aConfigs['db']['default']          = Array('localhost','database_name','user','password', 'database_type');
$aConfigs['db']['sqlite']           = Array('localhost',':memory:','user','password', 'sqlite');
$aConfigs['db']['mysql']            = Array('localhost','tablemodel_unit_test','root','', 'mysql');
$aConfigs['db']['firebird']         = Array('localhost','localhost:'.realpath(dirname(__FILE__).'/../../../tests').'/tablemodel.fdb','SYSDBA','masterkey', 'firebird');

$route      = &$aConfigs['routes'];
# $route[URI][METHOD] = Array('ControllerName', 'MethodName', 'ModuleName')

$route['/']['*']                 = Array('MainController');
$route['/notfound']['*']         = Array('NotFoundController');

?>