<?
/**
*   @package core.class
*/

/**
*   Basic class that manipulates action with a some database
*   Should be used as a model for future Database Layers
*/
class BasicDb {

    /**
    *   Return the instance
    *   @return object PDO
    */
    static function getInstance() {
        $config = Liber::$aDbConfig;
        if ( isset($config[Liber::conf('APP_MODE')]) ) {
            $config = $config[Liber::conf('APP_MODE')];
            $dsn    = $config[4].':dbname='.$config[1].';host='.$config[0];
            try {
                $o      = new PDO($dsn, $config[2], $config[3]);
                if ($o) { 
                    $o->exec("set names 'utf8'"); 
                } 
                return $o;                                 
            } catch(PDOException $e) {
                trigger_error("No database connection."); // Caution: Exception message show password on stack trace.
                return null;
            }
        } else {
            Liber::loadClass('Log',true)->add('Configure database settings.');
            return null;
        }
    }
    
}

?>
