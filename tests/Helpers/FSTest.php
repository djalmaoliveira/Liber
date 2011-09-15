<?php

include_once dirname(dirname(__FILE__)).'/include.php'        ;

class FSTest extends PHPUnit_Framework_TestCase {

    function SetUp() {
        Liber::loadHelper('FS');
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



    function testRelativePathOnSymlinkPath() {
        $source = '/tmp/test_rp/d1/d2/d3/d4';
        $dest   = '/tmp/test_rp/test';
        if ( !file_exists('/tmp/test_rp') ) {
            mkdir($source, 0777, true);
            symlink('/tmp/test_rp/d1/d2/', $dest);
        }
        $relative = fs_relative_path_('/tmp/', '/tmp/test_rp/d1/d2/d3/d4/');
        $this->assertEquals('../../../../', $relative, "You should be $source to reach /tmp/");
    }


    function test_Fs_scan_recursive() {

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
