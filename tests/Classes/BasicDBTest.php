<?php

include_once dirname(dirname(__FILE__)).'/include.php';

class BasucDBTest extends PHPUnit_Framework_TestCase {

    var $dbconf;

    function SetUp() {
        Liber::loadClass('Mailer');
        $this->dbconf = Array('/var/run/mysqld/mysqld.sock','test','root','root', 'mysql');
    }

    function tearDown() {
    }


    function testMysqlSocketConnection() {
        Liber::$aDbConfig['PROD'] = $this->dbconf;
        Liber::$aDbConfig['DEV'] = $this->dbconf;
        Liber::db();

        $this->assertInternalType('object',Liber::db(), 'No connection using socket');
    }

    function testMysqlConnection() {
        $this->dbconf[0] = 'localhost';
        Liber::$aDbConfig['PROD'] = $this->dbconf;
        Liber::$aDbConfig['DEV'] = $this->dbconf;
        Liber::db();

        $this->assertInternalType('object',Liber::db(), 'No connection using TCP/IP');
    }


}
?>
