<?php

include_once dirname(dirname(__FILE__)).'/include.php'        ;

class DTTest extends PHPUnit_Framework_TestCase {

    function SetUp() {

    }


    function testDiffSince() {
        Liber::loadHelper('DT');
        $text = dt_diffsince_('2010-01-01 00:00:00', '2010-01-01 00:00:01', 6);
        $this->assertEquals('1 second', $text,'Wrong textual date/time.');
    }


    function testTimeSince() {
        Liber::loadHelper('DT');
        $text = dt_timesince_(time()-2, 6);
        $this->assertEquals('2 seconds', $text,'Wrong textual date/time.');
    }

}
?>