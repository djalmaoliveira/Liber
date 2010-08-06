<?
require_once('/opt/PHP/phpunit/PHPUnit/Framework/TestCase.php');
require_once(realpath(dirname(__FILE__).'/../').'/dooframework/Doo.php');

class CoreTest extends PHPUnit_Framework_TestCase {

    function SetUp() {
        include $config = realpath(dirname(__FILE__).'/../demos/test_unit/config/config.php');
        Doo::loadConfig($aConfigs);
    }

    function testLoad() {
        $this->assertTrue( Doo::load('Main', Doo::conf('APP_PATH').'controller/' ) , 'Não foi possível carregar Controller Main da aplicação.');
    }


    function testLoadControllerDaAplicacao() {
        $this->assertTrue( Doo::loadController('Main'), 'Não foi possível carregar Controller Main da aplicação.' );
    }

    function testLoadControllerDaAplicacaoComRetornoObjeto() {
        $o = Doo::loadController('Main', true);
        $this->assertEquals( 'Main', get_class($o), 'Não foi possível instanciar Controller Main da aplicação.' );
    }

    function testLoadControllerDoModulo() {
        $this->assertTrue( Doo::loadController('Module', 'Module1'), 'Não foi possível carregar Controller Module do módulo.' );
    }

    function testLoadControllerDoModuloComRetorno() {
        $o = Doo::loadController('Module', 'Module1', true);
        $this->assertEquals( 'Module', get_class($o), 'Não foi possível instanciar Controller Module do módulo Module1.' );
    }

 
    function testLoadModelDaAplicacao() {
        $this->assertTrue( Doo::loadModel('Model'), 'Não foi possível carregar Model da aplicação.' );
    }

    function testLoadModelDaAplicacaoComRetornoObjeto() {
        $o = Doo::loadModel('Model', true);
        $this->assertEquals( 'Model', get_class($o), 'Não foi possível instanciar Model da aplicação.' );
    }
    
     function testLoadModelDoModulo() {
        $this->assertTrue( Doo::loadModel('ModelModule', 'Module1'), 'Não foi possível carregar Model ModelModule do módulo.' );
    }

    function testLoadModelDoModuloComRetorno() {
        $o = Doo::loadModel('ModelModule', 'Module1', true);
        $this->assertEquals( 'ModelModule', get_class($o), 'Não foi possível instanciar Model ModelModule do módulo Module1.' );
    }

    
}
?>
