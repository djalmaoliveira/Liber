<?php
error_reporting(E_ALL | E_STRICT);


$aConfigs   = Array(
                'configs'=>Array(
                    //
                    // Configurations
                    //
                    'APP_PATH'      => realpath(dirname(__FILE__).'/../').'/',
                    'BASE_PATH'     => realpath(dirname(__FILE__).'/../../../framework/').'/',
                    'APP_MODE'      => 'DEV'
                ), 
            
                'routes'=>Array(), 
                
                'dbconfig'=>Array()
                
                
                );

$route      = &$aConfigs['routes'];

/*
    $route[URI][METHOD] = Array()
*/
$route['/']['*']                 = Array('MainController', '*');
$route['/notfound']['*']         = Array('NotFoundController', 'index');

?>
