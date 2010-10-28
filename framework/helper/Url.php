<?php

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
 *  Create relative url from APP_URL.
 *
 *
 * @access	public
 * @param $relative_url string
 * @param $return boolean
 * @return string
 */
function url_to_($relative_url = '', $return=false) {
    $url = Liber::redirect($relative_url, true);

    if ($return) {	
        return $url;
    } else {
        echo $url;
    }
}



/**
 * Current URL
 *
 * Returns the current URL requested.
 *
 * @access	public
 * @param $relative_url string
 * @param $return boolean
 * @return string
 */
function url_current_($return=false) 	{
    return (Liber::isSSL()?'https':'http').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}

/**
*   Returns a URL to app asset.
*   @param $relative_url string
*   @param $return boolean
*   @return string
*/  
function url_asset_( $relative_url='' , $return=false) {
    $url = Liber::conf('APP_URL').Liber::conf('ASSETS_DIR').'/app'.$relative_url;
            
    if ( $return ) {
        return $url;
    } else {
        echo $url;
    }
}


/**
*   Returns current module name that will be used by others helper functions that use it.
*   If specified a name, will set up with it.
*   @param $moduleName string
*   @return string
*/  
function url_module_name_asset_( $moduleName=null ) {
    static $modName = '';
    if ( $modName =='' ) {
        $modName = Liber::conf('ASSETS_DIR');
    }
    
    if ( $moduleName==null )  {
        return $modName;
    } else {
        $modName = $moduleName;
    }
}


/**
*   Returns a URL to module asset.
*   @param $relative_url string
*   @param $return boolean
*   @return string
*/  
function url_module_asset_($relative_url='', $return=false) {
    $url = Liber::conf('APP_URL').Liber::conf('ASSETS_DIR').'/'.url_module_name_asset_().$relative_url;

    if ( $return ) {
        return $url;
    } else {
        echo $url;
    }
}



/**
*   Return a clean specified URL.
*   Change spaces and others charactes to $separator by default '-'.
*   @param $url String
*   @param $separator String
*   @param $return boolean
*   @return String
*/
function url_clean_($url, $separator="-", $return=false) {
    if ( is_bool($separator) ) {
        $return = $separator;
        $separator = '-';
    }
    
    $url = strtr(utf8_decode($url), utf8_decode('ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ'),'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyRr');
    $url = urlencode( str_replace(' ', $separator, trim($url)) );            

    while ( ($pos = strpos($url, '%')) !== false ) {
        $part = substr($url, $pos,3);
        $url  = str_replace($part, $separator, $url);
    }

    if ( $return  ) {
        return $url;
    } else {
        echo $url;
    }
}

?>
