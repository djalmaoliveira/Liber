<?php

include_once dirname(dirname(__FILE__)).'/include.php'        ;

class FSTest extends PHPUnit_Framework_TestCase {

    function SetUp() {
    
    }

    function testRelativePath() {
    
        /*
            source - dest   => result expected
            /h/a   - /h/b   => a
            /h/a/b - /h/c   => a/b
            /h/a   - /a/b   => ../h/a
            /h/c   - /h/a/b => ../c
        */
    
        $this->assertTrue( true , 'texto.');
    }



    
}
?>
