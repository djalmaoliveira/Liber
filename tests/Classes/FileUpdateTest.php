<?php
    
include_once dirname(dirname(__FILE__)).'/include.php';

class FileUpdateTest extends PHPUnit_Framework_TestCase {

    var $fileTest = 'fileUpdate.test';

    function SetUp() {
    
    }

    function tearDown() {
        if (file_exists($this->fileTest)) {
            unlink($this->fileTest);
        }
    }

    function testLoadClass() {
        $this->assertTrue(Liber::loadClass('FileUpdate'), "Class didn't load.");
    }

    function testAddFiles() {
        $oFU = Liber::loadClass('FileUpdate', true);
        $oFU->add(__FILE__);
        $aFiles = $oFU->files();
        
        $compare['data'] = file_get_contents(__FILE__);
        $compare['sha1'] = sha1($compare['data']);
        
        $this->assertEquals(current($aFiles['add']), $compare, "File cannot be added.'");
    }

    function testAddRecursive() {
        $oFU = Liber::loadClass('FileUpdate', true);
        $p = '../';
        $oFU->add( realpath($p), true );
        $aFiles = $oFU->files();

        $this->assertGreaterThan(0 , count($aFiles['add']), "Path cannot be added recursively.");
    }

    function testDeleteRecursive() {
        $oFU = Liber::loadClass('FileUpdate', true);

        $oFU->workingDir('/tmp/test/');
        if ( !file_exists('/tmp/test/t1') ) {
            mkdir('/tmp/test/t1/t2/t3/t4/t4', 0777, true);
        }
        $oFU->delete('/tmp/test', true);
        $oFU->processUpdate();
        
        $this->assertTrue(!file_exists('/tmp/test'), "Delete recursively have a problem.");
    }



    function testWriteUpdateAndLoadUpdate() {
        $oFU = Liber::loadClass('FileUpdate', true);
        $oFU->add(__FILE__);
        $serial = serialize($oFU);
        $oFU->writeUpdate('fileUpdate.test');
        
        $oFU->loadUpdate('fileUpdate.test');
        $compare = serialize($oFU);
        
        $this->assertEquals($serial, $compare, "File written has problems.'");
    }


    function atestProcessUpdate() {
        $oFU = Liber::loadClass('FileUpdate', true);
        // source file        
        if (!is_dir('/tmp/test/dir1/dir2')) {
            mkdir('/tmp/test/dir1/dir2', 0700, true);
        }
        
        file_put_contents('/tmp/test/dir1/dir2/a.txt', 'test');
        
        // target file
        if (!is_dir('/tmp/test2/dir1/dir2')) {
            mkdir('/tmp/test2/dir1/dir2', 0700, true);
        }
        
        // create a update and add some files
        $oFU->workingDir('/tmp/test/');
        $oFU->add('/tmp/test/dir1/dir2/a.txt');
        
        // setup target update
        $oFU->workingDir('/tmp/test2/');
        
        $this->assertEquals($oFU->processUpdate(), true, "Problem with processUpdate().");
    }


    function testIgnoreFile() {
        $oFU = Liber::loadClass('FileUpdate', true);
        $oFU->ignore(__FILE__);
        $oFU->add(__FILE__);
        $aFiles = $oFU->files();
        
        $this->assertEquals($aFiles['add'], Array(), "File cannot be ignored.");
    }

    function testIgnorePath() {
        $oFU = Liber::loadClass('FileUpdate', true);
        $oFU->ignore(dirname(__FILE__));
        $oFU->add(__FILE__);
        $aFiles = $oFU->files();
        
        $this->assertEquals($aFiles['add'], Array(), "Path cannot be ignored.");
    }

    
}
?>
