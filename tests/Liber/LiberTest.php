<?php
include dirname(__FILE__).'../include.php';


class LiberTest extends PHPUnit_Framework_TestCase {

    function SetUp() {
    }

    function testLoad() {
        $this->assertTrue( Liber::load('MainController', Liber::conf('APP_PATH').'controller/' ) , "Didn't load MainController.");
    }


    function testLoadControllerOfApp() {
        $this->assertTrue( Liber::loadController('MainController'), "Didn't load MainController." );
    }

    function testLoadControllerOfAppWithReturningObject() {
        $o = Liber::loadController('MainController', true);
        $this->assertEquals( 'MainController', get_class($o), "Didn't instanciate MainController." );
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
/*
    function testRoutePassedArgsToControllerMethod() {
        $_SERVER['REQUEST_URI'] = '/slashArgs/a/b/c/d';
        Liber::processRoute();
    }
     */
}
?>
