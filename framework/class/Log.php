<?

/**
*   @package core.class
*/


/**
*   Class that manipulate produced logs in general.
*/
class Log {
    
    static $aLogMsg = Array();
    static $aLogAll = Array();
    static $debug   = true;
    private static $instanced;
    
    function __construct() {
        if ( self::$instanced ) { 
            return ; 
        } else {
            self::$instanced = true;
            if ( Liber::conf('APP_MODE') == 'PROD' ) {
                register_shutdown_function ( Array($this, 'toFile') ) ;
            }
        }
    }
    
    
    /**
    *   Adds a message log.  
    *   @param String $msg 
    *   @param String $level - NameSpace to log like 'ERROR', 'URGENT', etc
    */
    function add($msg, $level='info') {
        if ( !self::$debug ) { return; }
        self::$aLogMsg[$level] = Array();
        $id = array_push(self::$aLogMsg[$level] , '['. date(DATE_RFC822).'] '.$msg."\n");
        $id--;
        self::$aLogAll[] = &self::$aLogMsg[$level][$id];
        if ( Liber::conf('APP_MODE') == 'DEV' ) {
            trigger_error("Framework Error.", E_USER_ERROR);
        }
    }
    
    
    /**
    *   Write current log to file.
    *   
    */
    function toFile() {
        if ( !self::$debug or count(self::$aLogMsg)==0 ) { return; }
        file_put_contents( Liber::conf('LOG_PATH').date('Ymd').'.log', implode("\n", self::$aLogAll), FILE_APPEND| LOCK_EX );
    }
    
    
    /**
    *   Return textual stored log.
    *   @return String
    */
    function toString() {
        return implode("\n", self::$aLogAll);
    }
    
    
    /**
    *   Generate a html content to show a popup with PHP error.
    *   Receive a Array $aData, can be from errorHandler or exceptionHandler.
    *   @param Array $aData 
    *   @return String
    */
    function toPopUp($aData) {
        $errno      = '';
        $errstr     = $aData[0];
        $errfile    = '';
        $errline    = '';
    
        if ( count($aData) > 1 ) {
            $errno      = $aData[0];
            $errstr     = $aData[1];
            $errfile    = $aData[2];
            $errline    = $aData[3];
        } 
    
        $html = "<html><body>";
        $html .= empty($errno)?'':" <div><b>Error</b>   : <i>#$errno</i></div>";
        $html .= empty($errstr)?'':"<div><b>Message</b> : <i>$errstr</i></div>";
        $html .= empty($errfile)?'':" <div><b>File</b>    : <i>$errfile (line $errline)</i></div>";
        $html .= $this->toString()==''?'':"<div><b>System</b>  : <i>".$this->toString()."</i></div>";

        ob_start();
        debug_print_backtrace();
        $html .= "<div><b>Trace:</b><pre>".ob_get_clean()."</pre></div>";

        $html .="</body></html>";
        
        
        $html = str_replace("'",'"', $html);
        $html = str_replace("\n",'<br>', $html);
        $html = "
        <script>
            var wError = window.open('about:blank', 'erro', 'top=10, left=200, width=700, height=200, resizable=yes');
            wError.document.write('$html');
        </script>";  
        return $html;
    }
}


?>
