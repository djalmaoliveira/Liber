<?php
    include_once '/opt/PHP/phpunit/PHPUnit/Framework/TestCase.php';

    include dirname(dirname(__FILE__)).'/demos/test_unit/config/config.php';
    include $aConfigs['configs']['BASE_PATH'].'Liber.php';

    // simulates on HOST
    $_SERVER['HTTP_HOST'] = 'liber';
    $_SERVER['SERVER_ADMIN'] = 'server_admin@localhost';

    Liber::loadConfig($aConfigs);

?>
