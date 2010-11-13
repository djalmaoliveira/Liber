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


    function test_Fs_scan_recursive() {
        Liber::loadHelper('FS');
        if ( !file_exists('/tmp/test_fs') ) {
            mkdir('/tmp/test_fs/d1/d2/d3', 0777, true);
        }
        $func = create_function('$dir, $file', '
            return Array($dir."/".$file,);
        ');
        $out = fs_scan_(realpath('/tmp/test_fs'),$func, true);
        $this->assertEquals(count($out), 3, "Recursive scan seems have a problem.");
    }
    
}
?>
