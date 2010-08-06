<?
/**
 *
 * @package core.helpers 
 * @author		djalmaoliveira@gmail.com
 * @copyright	djalmaoliveira@gmail.com
 * @license		
 * @link		
 * @since		Version 1.0
 */


/**
 *
 * Return a relative path from $dest_path to $source_path specified.
 * 
 * The path returned is relative a first parent directory from $dest_path.
 * Example: $dest_path = '/home/user/myfolder/dest_path';
 *          $source_path = '/home/user/oldfolder/source';
 * The path will consider that you are on '/home/user/myfolder' and will return '../oldfolder/source'.
 * @param	string $source_path 
 * @param   string $dest_path   
 * @return	string
 */
function fs_relative_path_($source_path, $dest_path) {
    $source_path = trim($source_path);
    $dest_path = trim($dest_path);
   
    
    $aS = explode('/', $source_path);
    $aD = explode('/', $dest_path);
    $i  = count(array_intersect_assoc($aS, $aD));
    $countBackDest = (count($aD)-$i)-1;
    $relativePath = implode('/', array_slice($aS, $i));

    $rel = str_repeat('../', $countBackDest).$relativePath;
    return $rel;
}
?>
