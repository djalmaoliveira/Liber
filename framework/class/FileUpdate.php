<?php
/**
*   @package core.class
*/


/**
*   Class used for create a update file and to use it for updates remote an application.
*/
class FileUpdate {

    /**
    *   Store update data.
    *   @var $updateData
    */
    protected $updateData = Array(
                                'workingDir'    => '',
                                'add'           => Array(),
                                'ignore'        => Array(),
                                'delete'        => Array()
                            );


    /**
    *   Set or Get the working dir, it will be used to make relative path from files added.
    *   @param String $dirPath
    *   @return mixed
    */
    public function workingDir($dirPath=null) {
        if ( $dirPath === null ) {
            return $this->updateData['workingDir'];
        }

        $this->updateData['workingDir'] = trim($dirPath);
        if ( substr($this->updateData['workingDir'], -1, 1) == '/' ) {
            $this->updateData['workingDir'] = substr($this->updateData['workingDir'],0, strlen($this->updateData['workingDir'])-1);
        }

        return $this->updateData['workingDir'];
    }

    /**
    *   Add a file to update, returning the status of operation.
    *   @param String $filePath
    *   @return boolean
    */
    public function addFile($filePath) {
        if ( file_exists($filePath) ) {
            $file = str_replace($this->updateData['workingDir'], '', $filePath);
            $this->updateData['add'][$file]['data'] = file_get_contents($filePath);
            $this->updateData['add'][$file]['sha1'] = sha1($this->updateData['add'][$file]['data']);
            return (strlen($this->updateData['add'][$file]['data']) > 0);
        }
        return false;
    }

    /**
    *   Mark to ignore a file.
    *   @param String $filePath
    */
    public function ignoreFile($filePath) {
       $file = str_replace($this->updateData['workingDir'], '', $filePath);
       $this->updateData['ignore'][$file] = true;
    }

    /**
    *   Mark to delete a file.
    *   @param String $filePath
    */
    public function deleteFile($filePath) {
       $file = str_replace($this->updateData['workingDir'], '', $filePath);
       $this->updateData['delete'][$file] = true;
    }

    /**
    *   Load an update file in current instance, returning the state of operation.
    *   @param String $filePath
    *   @return boolean
    */
    public function loadUpdate($filePath) {
        if ( file_exists($filePath) ) {
            return ($this->updateData = unserialize(base64_decode(file_get_contents($filePath))));
        }
        return false;
    }

    /**
    *   Write the current update data to file, returning the state of operation.
    *   @param String $filePath
    *   @return boolean
    */
    public function writeUpdate($filePath) {
        $dir = dirname($filePath);
        if ( !file_exists($dir) ) {
            mkdir($dir, 0700, true);
        }
        return (file_put_contents($filePath, base64_encode(serialize($this->updateData))) !== false);
    }


    /**
    *   Execute the update using current update data, returning the state of operation.
    *   Return true if everything is ok, otherwise Array of files and its problems.
    *   @return mixed
    */
    public function processUpdate() {
        $errors = Array();
        static $msg;
        if ( !is_array($msg) ) {
            $lang = (Liber::conf('LANG')=='')?'en':strtolower( Liber::conf('LANG') );
            $msg = include Liber::conf('BASE_PATH').'i18n/class/FileUpdate.'.$lang.'.php';
        }


        // verify data integrity and target files
        foreach( $this->updateData['add'] as $file => $aFile ) {
            $filePath = $this->updateData['workingDir'].$file;
            if ( sha1($aFile['data']) != $aFile['sha1']) {
                $errors[$file] = $msg['CHECKSUM'];
                continue;
            }
            if ( file_exists($filePath) and !is_writeable($filePath)) {
                $errors[$file] = $msg['FILENOTWRITE'];
            }
        }

        // error found
        if ( count($errors) > 0 ) {
            return $errors;
        }

        // write files
        foreach( $this->updateData['add'] as $file => $aFile ) {
            $filePath = $this->updateData['workingDir'].$file;echo "\r\n".dirname($filePath)."\r\n";
            if ( !file_exists(dirname($filePath)) ) {
                try {
                    mkdir(dirname($filePath), 0700, true);
                } catch(Exception $e) {
                    $errors[dirname($filePath)] = $msg['DIRNOTWRITE'];
                    break;
                }
            }
            if ( file_put_contents($filePath, $aFile['data']) === false ) {
                $errors[$file] = $msg['NOTWRITED'];
            }
        }

        // error found
        if ( count($errors) > 0 ) {
            return $errors;
        }


        // delete files
        foreach( $this->updateData['delete'] as $file => $aFile ) {
            $filePath = $this->updateData['workingDir'].$file;
            if ( file_exists(($filePath)) ) {
                if ( !unlink($filePath) ) {
                    $errors[$file] = $msg['NOTDELETED'];
                }
            }
        }

        // error found
        if ( count($errors) > 0 ) {
            return $errors;
        }

        return true;
    }


    /**
    *   Return an array of current added, ignored and deleted files.
    *   @return Array
    */
    public function files() {
        return Array(   'add'       =>  $this->updateData['add'],
                        'ignore'    =>  $this->updateData['ignore'],
                        'delete'    =>  $this->updateData['delete']
        );
    }

}
?>
