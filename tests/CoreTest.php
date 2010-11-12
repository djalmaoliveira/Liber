<?php
include 'include.php';


class CoreTest extends PHPUnit_Framework_TestCase {

    function SetUp() {
        include $config = realpath(dirname(__FILE__).'/../demos/test_unit/config/config.php');
        Liber::loadConfig($aConfigs);
    }

    function testLoad() {
        $this->assertTrue( Liber::load('Main', Liber::conf('APP_PATH').'controller/' ) , "Didn't load MainController.");
    }


    function testLoadControllerOfApp() {
        $this->assertTrue( Liber::loadController('Main'), "Didn't load MainController." );
    }

    function testLoadControllerOfAppWithReturningObject() {
        $o = Liber::loadController('Main', true);
        $this->assertEquals( 'Main', get_class($o), "Didn't instanciate MainController." );
    }

    function testLoadModuleController() {
        $this->assertTrue( Liber::loadController('Module', 'Module1'), "Didn't load Controller of Module1." );
    }

    function testLoadModuleControllerWithReturnObject() {
        $o = Liber::loadController('Module', 'Module1', true);
        $this->assertEquals( 'Module', get_class($o), "Didn't instanciate Controller of Module1." );
    }

 
    function testLoadAppModel() {
        $this->assertTrue( Liber::loadModel('Model'), "Didn't load App Model." );
    }

    function testLoadAppModelWithReturningObject() {
        $o = Liber::loadModel('Model', true);
        $this->assertEquals( 'Model', get_class($o), "Didn't instanciate App Model." );
    }
    
     function testLoadModuleModel() {
        $this->assertTrue( Liber::loadModel('ModelModule', 'Module1'), "Didn't load ModelModule of Module1." );
    }

    function testLoadModuleModelWithReturningObject() {
        $o = Liber::loadModel('ModelModule', 'Module1', true);
        $this->assertEquals( 'ModelModule', get_class($o), "Didn't load ModelModule of Module1." );
    }

    
}
?>
