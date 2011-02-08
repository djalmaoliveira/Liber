<?php

$project = realpath((dirname(__FILE__)).'/../../../').'/';
$aConfigs   = Array(
                'configs'=>Array(
                    //
                    // Configurations
                    //
                    'APP_PATH'      => $project.'app/example/',
                    'BASE_PATH'     => $project.'framework/',
                    'APP_MODE'      => 'PROD',
                ),

                'routes'=>Array(),

                'dbconfig'=>Array(
                    'DEV'  => Array('localhost','database_name','user','password', 'database_type'),
                    'PROD' => Array('localhost','database_name','user','password', 'database_type')
                )


                );

$route      = &$aConfigs['routes'];

/*
    $route[URI][METHOD] = Array()
*/

$route['/']['*']                 = Array('MainController', '*');
$route['/notfound']['*']         = Array('NotFoundController', 'index');

?>