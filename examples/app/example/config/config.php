<?php
// $path variable has absolute path to application

$aConfigs   = Array(

                // Configurations
                //
                'configs'   =>  Array(
                    'APP_MODE'      => 'PROD'
                ),

                'routes'    =>  Array(),

                'dbconfig'  =>  Array(
                    'DEV'  => Array('localhost','database_name','user','password', 'database_type'),
                    'PROD' => Array('localhost','database_name','user','password', 'database_type')
                )


                );

$route      = &$aConfigs['routes'];


# $route[URI][METHOD] = Array('ControllerName', 'MethodName', 'ModuleName')

$route['/']['*']                        = Array('MainController');
$route['/direct/route']['*']            = Array('MainController', 'direct');
$route['/process/named/:name:']['*']    = Array('Examples', 'named');
$route['/other/direct/route']['*']      = Array('Examples');

?>