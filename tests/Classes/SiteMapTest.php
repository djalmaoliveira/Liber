<?php

include_once dirname(dirname(__FILE__)).'/include.php';

class SiteMapTest extends PHPUnit_Framework_TestCase {



    function SetUp() {
        Liber::loadClass('SiteMap');
    }

    function tearDown() {
    }

    function testGetArrayUrl() {
        $s = new SiteMap;
		$s->url( Array('loc'=>'http://url.com.br') );
		$urls = $s->url();

        $this->assertTrue( count($urls) > 0, "Should have some url." );
    }

    function testXmlOutPut() {
        $s = new SiteMap;
		$s->url( Array('loc'=>'http://url.com.br', 'lastmod'=>'2010-11-01') );
		$xml = $s->xml();
        $this->assertTrue( !empty($xml), "Should have XML data." );
    }

}
?>
