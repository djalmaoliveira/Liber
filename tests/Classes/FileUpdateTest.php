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
        $oFU->addFile(__FILE__);
        $aFiles = $oFU->files();
        
        $compare['data'] = file_get_contents(__FILE__);
        $compare['sha1'] = sha1($compare['data']);
        
        $this->assertEquals(current($aFiles['add']), $compare, "File cannot be added.'");
    }

    function testWriteUpdateAndLoadUpdate() {
        $oFU = Liber::loadClass('FileUpdate', true);
        $oFU->addFile(__FILE__);
        $serial = serialize($oFU);
        $oFU->writeUpdate('fileUpdate.test');
        
        $oFU->loadUpdate('fileUpdate.test');
        $compare = serialize($oFU);
        
        $this->assertEquals($serial, $compare, "File written has problems.'");
    }


    function testProcessUpdate() {
        $oFU = Liber::loadClass('FileUpdate', true);
        // source file        
        mkdir('/tmp/test/dir1/dir2', 0700, true);
        file_put_contents('/tmp/test/dir1/dir2/a.txt', 'test');
        
        // target file
        mkdir('/tmp/test2/dir1/dir2', 0700, true);
        
        // create a update and add some files
        $oFU->workingDir('/tmp/test/');
        $oFU->addFile('/tmp/test/dir1/dir2/a.txt');
        
        // setup target update
        $oFU->workingDir('/tmp/test2/');
        
        $this->assertEquals($oFU->processUpdate(), true, "Problem with processUpdate().'");
    }

    
}
?>
