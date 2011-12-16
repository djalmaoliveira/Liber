<?php
error_reporting(E_ALL | E_STRICT);


$aConfigs   = Array(

                // Configurations
                //
                'configs'   =>  Array(
                    'APP_MODE'      => 'DEV'
                ),

                'routes'    =>  Array(),

                'dbconfig'  =>  Array()


                );

$route      = &$aConfigs['routes'];

/*
    $route[URI][METHOD] = Array()
*/
$route['/']['*']                 = Array('MainController', '*');
$route['/notfound']['*']         = Array('NotFoundController', 'index');

?>