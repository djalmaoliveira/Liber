<?php

/**
*   @package core.class
*/


/**
*   Class that manipulates extraction from databases.
*   Current suport: MySQL
*/
class SqlExtract {
    private $db_app_mode = '';
    private $_tb_status  = Array();
    private $db;
    
    function __construct($db_app_mode='PROD') {
        $this->db_app_mode = $db_app_mode;
        Liber::loadClass('BasicDb');
        $this->db = BasicDb::getInstance($this->db_app_mode);
        
        switch( Liber::$aDbConfig[$this->db_app_mode][4] ) {
            case 'mysql':
                $this->_tb_status = $this->db->query("show table status")->fetchAll(PDO::FETCH_GROUP);            
            break;
        }
    }


    /**
    *   Return the SQL code of table creation.
    *   Return Array of tables names with its SQL code.
    *   @param String | Array $tables
    *   @return Array
    */
    public function tableScheme($tables, $withoutKeys=true) {
        if ( !is_array($tables) ) {
            $tables = Array($tables);
        }
        $aList = Array();

        foreach( $tables as $tableName ) {
            if ( $this->isTable($tableName) ) {
                switch ( Liber::$aDbConfig[$this->db_app_mode][4] ) {
                    case 'mysql':
                        $sql = "SHOW CREATE TABLE ".$tableName;
                        $rs  = $this->db->query($sql)->fetch();
                        if ( $withoutKeys ) {
                            $lines = explode("\n", $rs[1]);
                            $keys  = (preg_grep('/[\s]*(CONSTRAINT|FOREIGN[\s]+KEY|PRIMARY[\s]+KEY)/', $lines));
                            $lines = array_diff_key($lines, $keys);
                            end($lines);
                            $line = prev($lines);
                            $lines[key($lines)] = ($line[strlen($line)-1]==','?substr($line, 0, strlen($line)-1):$line);
                            $rs[1] = implode("\n", $lines);
                        }
                        $aList[$tableName] = $rs[1];
                    break;
                }
            }
        }
        return $aList;
    }


    /**
    *   Return the SQL code of view creation specified.
    *   Return Array of view names with its SQL code.
    *   @param String | Array $views
    *   @return Array
    */
    public function viewScheme($views) {
        if ( !is_array($views) ) {
            $views = Array($views);
        }
        $aList = Array();

        foreach( $views as $viewName ) {
            if ( !$this->isTable($viewName) ) {
                switch ( Liber::$aDbConfig[$this->db_app_mode][4] ) {
                    case 'mysql':
                        $sql = "SHOW CREATE VIEW ".$viewName;
                        $rs  = $this->db->query($sql)->fetch();
                        $aList[$viewName] = $rs[1];
                    break;
                }
            }
        }
        return $aList;
    }


    
    /**
    *   Return the constraints(PRIMARY and FOREIGN KEYS) of $tables specified.
    *   @param String | Array $tables
    *   @return Array
    */
    public function tableConstraints($tables) {
        if ( !is_array($tables) ) {
            $tables = Array($tables);
        }
        $aList = Array();

        foreach( $tables as $tableName ) {
            $create = current($this->tableScheme($tableName, false));
            switch ( Liber::$aDbConfig[$this->db_app_mode][4] ) {
                case 'mysql':
                    $aKeys = preg_grep('/[\s]*(CONSTRAINT|FOREIGN[\s]+KEY|PRIMARY[\s]+KEY)/', explode("\n", $create));
                    foreach($aKeys as $key) {
                        $aList[$tableName][] = "ALTER TABLE $tableName ADD ".($key[(strlen($key)-1)]==','?substr($key, 0, strlen($key)-1):$key).' ;';
                    }
                break;
            }
        }
        return $aList;
    }


    /**
    *   Return an array of SQL Insert code of each data retrieved from $tables specified, by table name.
    *   @param String | Array $tables
    *   @return Array
    */
    public function tableData($tables) {
        if ( !is_array($tables) ) {
            $tables = Array($tables);
        }
        $aList = Array();
        $funcValue = create_function('$value','
            if ( is_null($value) ) {
                return "NULL";
            }
            $value = filter_var($value, FILTER_SANITIZE_MAGIC_QUOTES);
            return "\'$value\'";
        ');
        foreach( $tables as $tableName ) {
            $rs = $this->db->query("SELECT * FROM $tableName")->fetchAll(PDO::FETCH_ASSOC);
            if ( $rs ) {
                $fields = array_keys($rs[0]);
                switch ( Liber::$aDbConfig[$this->db_app_mode][4] ) {
                    case 'mysql':
                        
                        foreach( $rs as $row) {
                            $values = array_map($funcValue, array_values($row));
                            $aList[$tableName][] = "INSERT INTO $tableName (".implode(', ', $fields).") VALUES (".implode(', ', $values).") ;";
                        }
                    break;
                }
            }
        }
        return $aList;
    }
        
    
    /**
    *   Return if $table is table or not.
    *   The entity $table can be a view.
    *   @param String
    *   @return boolean
    */
    protected function isTable($table) {
        switch ( Liber::$aDbConfig[$this->db_app_mode][4] ) {
            case 'mysql':    
                return empty($this->_tb_status[$table][0]['Engine'])?false:true;
            break;
        }
        return false;
    }
    
        
    
}
?>
