<?php
/**
*   @package core.class
*/


/**
*   Class that manage fucky cache files.
*   Fucky cache simply write a raw html content to a file to improve speed access.
*   This files used to be put on a public url path and auto-created when it's missing, using NotFoundController for example.
*/
class Fucky {

    /**
    *   Put a raw file data to a specified $path.
    *   Return a boolean value indicating if it did.
    *   @param String $path
    *   @param String $data
    *   @return boolean
    */
    function put($path, $data) {
        $aPath = pathinfo($path);
        if ( !file_exists($aPath['dirname']) ) {
            mkdir($aPath['dirname'], 0770, true);
        }
        return (file_put_contents($aPath['dirname'].'/'.$aPath['basename'], $data, LOCK_EX) !== false);
    }

    /**
    *   Clean a specified $path, recursively or not.
    *   @param String  $path
    *   @param boolean $recursive
    */
    function clean($path, $recursive=false) {
        if ( file_exists($path) ) {
            if ( is_file($path) ) {
                unlink($path);
            } else {
                Liber::loadHelper('FS');
                $f = create_function('$dir, $file','
                    unlink($dir.$file);
                    return $file;
                ');
                fs_scan_($path, $f, $recursive);
            }
        }
    }

}

?>