<?php

include_once dirname(dirname(__FILE__)).'/include.php';

class LogTest extends PHPUnit_Framework_TestCase {



    function SetUp() {

    }

    function tearDown() {
    }

	function testNormalLog() {
		Liber::log()->add('Test log');
	}

	function testErrorLog() {
		Liber::loadController('Main', true)->errorLog();

	}

}
?>